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


//$dbc  = new createConnection();
 
$retmsg ;
$cur_balanced = 0;

//json for UPDATE : action, COL_LIST, VALUES, CONDI

$vals = json_decode(file_get_contents('php://input'), true );


switch ($vals["action"]) {
    case "SEARCH_ID":
        searchRetBalanced($vals["CONDI"]);
        break;
	 case "SEARCH_SUPPLIER":
        searchRetSupplier($vals["CONDI"]);
        break;
	case "UPDATE_INV_INFO":
		updateBalanced($vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
		break;
}

function searchRetBalanced($condition){
	global $dbc, $retmsg;
	
	$retmsg = "?";
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//
	//1. before loading, it must be calculated where this product are stocked 
	//
	$ret = searchProdPlaceInfo(" Code, Name, InDate, Quantity, Stockplace ", $condition);
	
	
	//
	//2. load information
	//
	$sql = "SELECT Quantity, Stockplaces, Name FROM " . TAB_BALANCED . " WHERE " . $condition;
	//echo $sql;
	$results = $dbc->userQuery($sql);
	foreach($results as $row){
	 //while ($row = $results->fetchArray()) {
		$retmsg = $row[0] . "#" . $ret . "#" . $row[2];
	}
	
	echo $retmsg;
}




function searchRetSupplier($condition){
	global $dbc, $retmsg;
	
	$retmsg = "-";
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "SELECT Supplier, Max, Min FROM " . TAB_PRODUCT . " WHERE " . $condition;
	//echo $sql;
	$results = $dbc->userQuery($sql);
	foreach($results as $row){
	//while ($row = $results->fetchArray()) {
		$retmsg = $row[0] . "|" . $row[1] . "|" . $row[2];
	}
	
	echo $retmsg;
}

/////////////////////////////////////////////////////////////////

//search values in tables with condition
function searchProdPlaceInfo($cols, $condition){
	global $dbc;
	
	
	//1. get stock information for product
	$sql = "SELECT " . $cols .  " FROM "  . TAB_MOVEIN . " WHERE " . $condition;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_FINDACT;
		exit;
	}
	
	$tmpret = "";
	foreach($result as $row){
	//while ($row = $result->fetchArray()) {
		//2. loop movein array in moveout, comparing with Code=Code, Stockplace=Stockplace
		$tmpret .= checkForMoveout($row) ;
	}
	return $tmpret;
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
	
	if ($tmpval_out < $tmpval_in){
		return $vals["Stockplace"] . "=" . ($tmpval_in -$tmpval_out) . "; ";
	}
	
	return "";
}



///////////////////////////////////////////////////////////////////////
function updateBalanced($cols, $vals,$condition){
	
	global $dbc, $retmsg;
	
	$retmsg = "-";
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//1. insert entry in table 
	$sql = "INSERT INTO " . TAB_INVENTUR . " (" . $cols .  ") VALUES " . $vals;
	//echo $sql;
		
	if ( $dbc->execWriteStatement($sql)){
		Message::logEvent(Message::ACT_SAINVEN);
		echo Message::MSG_NEW_INVOK ;
	}
	else echo Message::MSG_NEW_INVER . "[" . $dbc->lastErrorMsg() . "]";
	
	//2. update the quantity in balanced table
	//fetch 3rd value form vals list, it is the actual quantity, which must be written in balanced
	$tmps = explode(",", $vals);
	$sql = "UPDATE balanced SET Quantity = " . $tmps[2] . " WHERE " . $condition;	
	//echo $sql;
	if ($dbc->execWriteStatement($sql)){
		$retmsg = Message::MSG_OK_INVUPBA . "|" . $tmps[2];
	}
	else $retmsg = Message::MSG_ER_INVUPBA;
	
	echo $retmsg;
	
}

//--------------------------------------------------------------------------------
//insert values in tables with condition
function insertValues($tablename, $cols, $vals, $condition){
	global $dbc, $retmsg;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	 
	
	
	//on insert new part and son on, the condition 'serial=xxxx' must be checed
	//$dbc->CheckValueExistingWMS($tablename, $condition) == 1
	$tmps = explode(",", $vals);
	if ( $dbc->CheckValueExisting(TAB_PRODUCT, "Code", trim(substr($tmps[0], 1, 30))  )){
		echo Message::MSG_DUP_PROID;
	} 
	else{
		
		$sql = "INSERT INTO " . $tablename . " (" . $cols .  ") VALUES " . $vals;
		//echo $sql;
		
		if ( $dbc->execWriteStatement($sql)){
			echo Message::MSG_NEW_PROOK ;
		}
		else echo Message::MSG_NEW_PROER . "[" . $dbc->lastErrorMsg() . "]";
	}
	
	$dbc->closeConnection();
}


?>