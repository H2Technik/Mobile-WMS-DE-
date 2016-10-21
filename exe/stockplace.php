<?php

require 'connection.php';
require 'message.php';

session_start();

define ("TAB_PRODUCT", "product", true);
define ("TAB_MOVEIN", "movein", true);
define ("TAB_MOVEOUT", "moveout", true);
define ("TAB_BALANCED", "balanced", true);
define ("TAB_GOODGROUP", "goodgroup", true);
define ("TAB_INVENTUR", "inventur", true);
define ("TAB_PLACE", "yard", true);


//$dbc  = new createConnection();
 
$retmsg ;
$cur_balanced = 0;

$vals = json_decode(file_get_contents('php://input'), true );


$update_balanced_array=array();

switch ($vals["action"]) {
    case "SEARCH_ACT":
		//echo "searching ...";
        searchPlaceInfo($vals["COL_LIST"], $vals["CONDI"]);
        break;
	case "SAVE_PLACE":
		insertNewPlace($vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
		break;
	case "DELE_PLACE":
		deletePlace($vals["CONDI"]);
		break;
	case "OVER_VIEW":
		searchPlaceInfo($vals["COL_LIST"], $vals["CONDI"]);
		break;
	case "GET_OCCUPY":
		searchProdPlaceInfo($vals["COL_LIST"], $vals["CONDI"]);
		/*foreach ($update_balanced_array as $row) {
			//echo "--------" . $row[2] . "-----------";
			//
			//2016.07.28 follwing function call make MONITOR very heavy job.
			//
			//updateStockplaceinBalanced($row[0], $row[1], $row[2]);
		}*/
		break;
	
}

//===============================================================================

//search values in tables with condition
function searchPlaceInfo($cols, $condition){
	global $dbc;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	if (strlen($condition)>1) $sql = "SELECT " . $cols .  " FROM "  . TAB_PLACE . " WHERE " . $condition;
	else $sql = "SELECT " . $cols .  " FROM "  . TAB_PLACE;
	
	//echo $sql;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_FINDACT;
		exit;
	}
	
	echo json_encode($result);
}

//search values in tables with condition
function searchProdPlaceInfo($cols, $condition){
	global $dbc;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//1. get stock information for selected places (Location + Area)
	if (strlen($condition)>1) $sql = "SELECT " . $cols .  " FROM "  . TAB_MOVEIN . " WHERE " . $condition;
	else $sql = "SELECT " . $cols .  " FROM "  . TAB_MOVEIN;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_FINDACT;
		exit;
	}
	
	$tmparray = array();
	foreach ($result as $row) {
		//2. loop movein array in moveout, comparing with Code=Code, InData<OutDate, Stockplace=Stockplace
		if (!checkForMoveout($row)){
			array_push($tmparray, $row);
		}
	}
	echo json_encode($tmparray);
}



//insert values in tables group
function insertNewPlace($cols, $vals, $condition){
	global $dbc, $retmsg;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	 

	//insert new place, check Location+Area+RowId existing
	//if yes, it is duplicate
	
	$tmps = explode(",", $vals);
	$tmpcode = $tmps[1] . $tmps[2] . $tmps[3]; //
	$tmpcode = str_replace("'", "", $tmpcode);
	$tmpcode = str_replace("\"", "", $tmpcode);
	$tmpcode = "'" . $tmpcode . "'";
	if ( $dbc->CheckValueExisting(TAB_PLACE, "Code", $tmpcode)) {
		echo Message::MSG_DUP_STKPLACE;
	} 
	else{
		$vals = str_replace("%", $tmpcode, $vals);
		$sql = "INSERT INTO " . TAB_PLACE . " (" . $cols .  ") VALUES (" . $vals ;
		//echo $sql;
		
		//insert into product
		if ( $dbc->execWriteStatement($sql)){
			Message::logEvent(Message::ACT_PLACECH);
			echo Message::MSG_NEW_PLACEOK;
		}
		else echo Message::MSG_ER_NEWPLACE . "[" . $dbc->lastErrorMsg() . "]";
		
	}
	
	$dbc->closeConnection();
	
}



//insert values in tables with condition
function deletePlace($condition){
	global $dbc;
	
	//delete from product
	$sql = "DELETE FROM " . TAB_PLACE . " WHERE " . $condition ."";
	echo $sql;
	if ($dbc->execWriteStatement($sql)){
		//for all goods with the same group, must be set to default group
		
		//$sql="UPDATE " . TAB_PRODUCT . " SET Stockplace='' WHERE Stockplace='" . $condition ."'";
		
		$dbc->execWriteStatement($sql);
		Message::logEvent(Message::ACT_PLADELE);
		echo Message::MSG_PLC_DELOK;
	}
	else{
		echo Message::MSG_ER_PLCDEL;
	}
	
	$dbc->closeConnection();
}

function checkForMoveout($vals){
	
	//$vals["Code"] $vals["Name"] $vals["InDate"] $vals["Quantity"] $vals["Stockplace"]
	global $dbc, $update_balanced_array;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//1.it is possible, many of same products in same place by different movein
	//therefore the sum of movein for this place must be caculated !
	$sql = "SELECT SUM(Quantity) FROM " . TAB_MOVEIN . " WHERE " .
		   "Code='" . $vals["Code"] . "'" . " AND " .
		   "Name='" . $vals["Name"] . "'" . " AND " .
	       "Stockplace='" . $vals["Stockplace"] . "'" ;
	
	//echo $sql;
	$tmpval_in = $dbc->GetSingValue($sql);
	
	//2.it is possible, many of same products in same place by different moveout
	//therefore the sum of moveout for this place must be caculated !
	$sql = "SELECT SUM(Quantity) FROM " . TAB_MOVEOUT . " WHERE " .
		   "Code='" . $vals["Code"] . "'" . " AND " .
		   "Name='" . $vals["Name"] . "'" . " AND " .
	       "Stockplace='" . $vals["Stockplace"] . "'" ;
	
	//echo $sql;
	$tmpval_out = $dbc->GetSingValue($sql);
	
	
	
	if (empty($tmpval_in)) $tmpval_in = 0;
	if (empty($tmpval_out)) $tmpval_out = 0;
	
	//3. if there are no more such product, the corresponding balanced "stockplaces"
	//have to be updated
	
	if ($tmpval_out >= $tmpval_in){
		array_push($update_balanced_array, array($vals["Code"], $vals["Name"], $vals["Stockplace"]));
	}
	//echo "/" . count($update_balanced_array) . "/" . var_dump($update_balanced_array) . "/";
	return $tmpval_out >= $tmpval_in;
}

//2016.07.28 not used 
function updateStockplaceinBalanced($code, $name, $place){
	
	global $dbc;
	$localdbc = $dbc;
	//$localdbc = new createConnection();
	//$localdbc->connectToDatabase();
	//$localdbc->selectDatabase();
	
	$sql = "SELECT Stockplaces FROM " . TAB_BALANCED . " WHERE Code='" . $code . "' AND Name='" . $name . "'";
	
	//echo 	$sql;
	$curplaces = $localdbc->GetSingValue($sql);
	$newplaces = "";
	
	
	//echo "##################";
	
	if (strpos($curplaces, $place . "|") !== false){ $newplaces = str_replace($place . "|", "", $curplaces); /*echo "-1-";*/}
	else if (strpos($curplaces, "|" . $place ) !== false) { $newplaces = str_replace("|" . $place, "", $curplaces); /*echo "-2-";*/}
	else if (strpos($curplaces, $place) !== false) { $newplaces = str_replace($place, "", $curplaces); /*echo "-3-";*/}
	else { $newplaces = $curplaces; /*echo "-4-";*/}
	
	//echo $newplaces;
	
	//echo $newplaces;
	//rewrite the stockplace into balanced table
	$sql = "UPDATE " . TAB_BALANCED . " SET Stockplaces='" . $newplaces . "' WHERE Code='" . $code . "' AND " .
	       " Name='" . $name . "'";
	
	//echo $sql;
	if ($localdbc->execWriteStatement($sql)){
			//$localdbc->closeConnection();
			return true;
	}
	else {
		//$localdbc->closeConnection();
		return false;	   
	}
	
}

?>