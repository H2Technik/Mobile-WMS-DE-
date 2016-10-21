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
define ("TAB_YARD", "yard", true);


//$dbc  = new createConnection();
 
$retmsg ;
$cur_balanced = 0;

//json for UPDATE : action, COL_LIST, VALUES, CONDI

$vals = json_decode(file_get_contents('php://input'), true );
//echo $vals["action"] . "/" . $vals["COL_LIST"]  . "/" . $vals["VALUES"];

switch ($vals["action"]) {
    case "INSERT":
        insertValues(TAB_PRODUCT, $vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
        break;
	case "INSERT_STOCKIN":
		insertStockInValues(TAB_MOVEIN, $vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
		break;
	case "INSERT_STOCKOUT":
		insertStockOutValues($vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
		break;
    case "UPDATE":
        updateValues($vals["SET_VAL_LIST"], $vals["CONDI"]);
        break;
    case "DELETE":
        deleteValues($vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
        break;
	case "SEARCH":
		searchValues($vals["COL_LIST"], $vals["CONDI"]);
		break;
	case "SEARCH_CNT";
		searchCntValues($vals["COL_LIST"], $vals["CONDI"]);
		break;
	case "STOCK_EX";
		exchangeStock($vals["VALUES"], $vals["CONDI"]);
		break;
}


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
		
		//insert into product
		if ( $dbc->execWriteStatement($sql)){
			
			//create item in balanced with current quantity 0
			$sql = "INSERT INTO " . TAB_BALANCED . " VALUES (" . trim(substr($tmps[0], 1, 30)) . "," . trim($tmps[1]) . ",0, '')";
			//echo $sql;
			if ( $dbc->execWriteStatement($sql)){
				Message::logEvent(Message::ACT_NEWPRD);
				echo Message::MSG_NEW_PROOK;
			}
			else Message::MSG_ER_ININEW;
		}
		else echo Message::MSG_NEW_PROER . "[" . $dbc->lastErrorMsg() . "]";
		
	}
	
	$dbc->closeConnection();
	
}


//insert values in stockin table
function insertStockInValues($tablename, $cols, $vals, $condition){
	
	global $dbc, $retmsg, $cur_balanced;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//on insert new part and son on, the condition 'Code=xxxx' must be checed
	if ( $dbc->CheckValueExistingCondition("product", $condition)  ){
		
		//check stockplace validation
		$tmps = explode(",", $vals);
		if ( !checkPlaceExisting($tmps[6]) ){
			echo  Message::MSG_ER_NOPLC;
			return;
		}
		
		$sql = "INSERT INTO `" . $tablename . "` (" . $cols .  ") VALUES " . $vals;
		//echo $sql;
		if ($dbc->execWriteStatement($sql)){
			
			//insert into in balanced table
			//get serialnr, name
			$tmps = explode(",", $vals);
			if (changeBalanced("UPDATE", trim(substr($tmps[0], 1, 30)), trim($tmps[1]), intval(trim($tmps[2])), $tmps[6])){
				echo Message::MSG_OK_INSIN . ">ok." ;
				Message::logEvent(Message::ACT_STOCKIN);
				return true;
			}
			else echo Message::MSG_ER_BALUP;
		} 
		else Message::MSG_ER_INSIN;
	} 
	else{
		echo Message::MSG_NO_PROID;
	}
	$dbc->closeConnection();
}



//insert values in stockout table
function insertStockOutValues($cols, $vals, $condition){
	
	global $dbc, $retmsg, $cur_balanced;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	//echo "-----------" . $tablename . "-----";
	
	//on insert new part and son on, the condition 'Code=xxxx' must be checed
	if ( $dbc->CheckValueExistingCondition("product", $condition)  ){
		
		//check stockplace validation
		$tmps = explode(",", $vals);
		if ( !checkPlaceExisting($tmps[6]) ){
			echo  Message::MSG_ER_NOPLC;
			return;
		}
		
		//check movein if the product is stored in this stocplace
		if ( !checkProductOnPlace(trim(substr($tmps[0], 1, 30)), $tmps[6]) ){
			echo Message::MSG_ER_PRONOTSTK;
			return;
		}
		
		
		
		$sql = "INSERT INTO `" . TAB_MOVEOUT . "` (" . $cols .  ") VALUES " . $vals;
		//echo $sql;
		if ($dbc->execWriteStatement($sql)){
			
			//insert into in balanced table
			//get serialnr, name
			$tmps = explode(",", $vals);
			if (changeBalanced("UPDATE", trim(substr($tmps[0], 1, 30)), trim($tmps[1]), -1*intval(trim($tmps[2])), $tmps[6])){
				echo Message::MSG_OK_INSOT . ">OK ."  ;
				Message::logEvent(Message::ACT_STOCKOU);
				return true;
			}
			else echo Message::MSG_ER_BALUP;
		} 
		else Message::MSG_ER_INSOT;
	} 
	else{
		echo Message::MSG_NO_PROID;
	}
	$dbc->closeConnection();
}


//insert values in tables with condition
function updateValues($setvals, $condition){
	global $dbc;
	
	echo $setvals;
	
	//$tmps1 = explode(",", $setvals);
	//$tmps2 = explode("=", $tmps1[8]);
	
	//echo $tmps2[1];
	//check stockplace validation
	/*if ( !checkPlaceExisting($tmps2[1]) ){
		echo  Message::MSG_ER_NOPLC;
		return;
	}*/
	
	$sql = "UPDATE " . TAB_PRODUCT . " SET " . $setvals . " WHERE " . $condition;
	//echo $sql;
	if ($dbc->execWriteStatement($sql)){
		Message::logEvent(Message::ACT_CHAPRD);
		echo Message::MSG_CHA_PROOK;
	}
	else echo Message::MSG_ER_PROCHA;
	
	
}

//insert values in tables with condition
function deleteValues($cols, $vals, $condition){
	global $dbc;
	
	//delete from product
	$sql = "DELETE FROM " . TAB_PRODUCT . " WHERE " . $condition;
	if ($dbc->execWriteStatement($sql)){
		$sql = "DELETE FROM " . TAB_BALANCED . " WHERE " . $condition;
		if ($dbc->execWriteStatement($sql)){
			Message::logEvent(Message::ACT_DELPRD);
			echo Message::MSG_DEL_PROOK;
		}
		else Message::MSG_ER_DELBAL;
	}
	else{
		echo Message::MSG_ER_DELPRO;
	}
	
	
}

//search values in tables with condition
function searchValues($cols, $condition){
	global $dbc;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "SELECT " . $cols .  " FROM "  . TAB_PRODUCT . " WHERE " . $condition;
	//echo $sql;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_NPPROID;
		exit;
	}
	echo json_encode($result[0]);
}

//search quantity in balacned with condition
function searchCntValues($cols, $condition){
	global $dbc;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "SELECT " . $cols .  " FROM "  . TAB_BALANCED . " WHERE " . $condition;
	//echo $sql;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_NPPROID;
		exit;
	}
	else{
		
		//get max and min of this product
		$sql1 = "SELECT Max, Min FROM " . TAB_PRODUCT . " WHERE " . $condition;
		$result1 = $dbc->userQuery($sql1); 
		$row1 = $result1[0];
		
		
		//combine the two arrays and return
		
		$row = $result[0]; //return indexed array
		//echo var_dump($row);
		
		$dbc->closeConnection();
		echo json_encode(array_merge($row, $row1));
	}
}


//update table balanced insert, update, delete
function changeBalanced($action, $serialnr, $name, $count, $newplace){
	
	global $dbc, $cur_balanced;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "";
	
	//check if ID existing, if not insert it
	if ( !$dbc->CheckValueExisting(TAB_BALANCED, "Code", $serialnr ) ) {
			$sql = "INSERT INTO balanced (`Code`,`Name`, `Quantity`) VALUES (" . $serialnr . "," . $name . ", " . $count . ")";
			//echo $sql;
			if ($dbc->execWriteStatement($sql)){
				return true;
			}
			else return false;
	}	
	
	//update
	/*UPDATE member_profile 
    SET points = points + 1
    WHERE user_id = '".$userid."'
	*/
	if ($action == "UPDATE"){
		$sql = "UPDATE balanced SET Quantity = Quantity +" . $count . " WHERE Code=" . $serialnr;
		//echo $sql;
		
		if ($dbc->execWriteStatement($sql)){
			//check if it is new place to store products
			updateStockPlace($serialnr, $name, $newplace);
			return true;
		}
		else return false;
	}
	
	//delete
	if ($action == "DELETE"){
		$sql = "DELETE FROM balanced WHERE Code=" . $serialnr;
		echo $sql;
		if ($dbc->execWriteStatement($sql)){
			return true;
		}
		else return false;
	}
	$dbc->closeConnection();
}


//check stockplace if existing
function checkPlaceExisting($place){
	global $dbc;
	
	if (strlen($place) == 0) return true;
	if (strlen($place) < 16) return false;
	
	$tmpplace = str_replace('"', "", $place);
	//$tmpplace = str_replace("'", "", $place);
	
	//echo $tmpplace;
	//Code
	$tmpcode = substr(trim($tmpplace), 0, 7);
	//column
	$col = intval(substr(trim($tmpplace), 7, 3));
	//level
	$lev = intval(substr(trim($tmpplace), 10, 3));
	//echo "-" . $col . "-" . $lev ."-";
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "SELECT ColumnCnt, LevelCnt" .  " FROM "  . TAB_YARD . " WHERE Code='" . $tmpcode . "'";
	//echo $sql;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_NOPLC;
		exit;
	}
	
	$row = array();
	$row = $result[0]; //return indexed array
	if ($row[0] <= $col) {
		echo Message::MSG_ER_PLCCOL;
		return false;
	}
	if ($row[1] <= $lev) {
		echo Message::MSG_ER_PLCLEV;
		return false;
	}
	return true;
}

function updateStockPlace($code, $name, $place){
	
	global $dbc;
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$tmpplace = str_replace('"', "", $place); //remove single quote
	$tmpplace = str_replace(')', "", $tmpplace); //remove the last clamma
	
	$sql = "SELECT Stockplaces FROM " . TAB_BALANCED . " WHERE Code=" . $code . " AND Name=" . $name ;
	
	//echo 	$sql;
	$curplaces = $dbc->GetSingValue($sql);
	$newplaces = "";
	if (empty($curplaces)) $newplaces = $tmpplace;
	else{
		if (strpos($curplaces, $tmpplace) !== false)	$newplaces = $curplaces;
		else $newplaces = $curplaces . "|" . $tmpplace;
	} 
	
	echo $newplaces;
	//rewrite the stockplace into balanced table
	$sql = "UPDATE " . TAB_BALANCED . " SET Stockplaces='" . $newplaces . "' WHERE Code=" . $code . " AND " .
	       " Name=" . $name;
	
	echo $sql;
	if ($dbc->execWriteStatement($sql)){
			return true;
	}
	else return false;	   
}

function checkProductOnPlace($code, $place){
	
	global $dbc;
	
	//echo "..........." . strpos($place, ")") . "...............";
	if (strpos($place, ")") > 0 ) $condi = "Code=" . $code . " AND Stockplace=" . str_replace(")", "", $place);
	else $condi = "Code=" . $code . " AND Stockplace=" . $place;
	
	//echo "/////" . $condi . "//////////";
	return $dbc->CheckValueExistingCondition(TAB_MOVEIN, $condi);
}

function exchangeStock($vals, $condi){
	//tmps[0] = actual place, tmps[1]=new place, tmps[2]=moved cnt, $condi is code 
	$tmps = explode(",", $vals); 
	
	//insert move out
	$cols = "Code,  Name, Quantity, Customer, OutDate, Info, Stockplace ";
	$tmpvals = "('" . $condi . "','".  $tmps[3] . "', " . $tmps[2] . ", '','" . Date("Y-m-d H:i:s") . "', '', \"" . $tmps[0] . "\")";
	$tmpcondi = "Code='" . $condi . "'";
	insertStockOutValues($cols, $tmpvals, $tmpcondi);
	//echo $cols . $vals .$condi . "---";
	
	//insert move in
	$cols = "Code,  Name, Quantity, Supplier, InDate, Info, Stockplace ";
	$tmpvals = "('" . $condi . "','". $tmps[3] ."', " . $tmps[2] . ", '','" . Date("Y-m-d H:i:s") . "', '', \"" . $tmps[1] . "\")";
	$tmpcondi = "Code='" . $condi . "'";
	insertStockInValues(TAB_MOVEIN, $cols, $tmpvals, $tmpcondi);
	
	Message::logEvent(Message::ACT_STOCKCH);
	
}

?>