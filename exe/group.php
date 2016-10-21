<?php

require 'connection.php';
require 'message.php';


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
//echo $vals["action"] . "/" . $vals["COL_LIST"]  . "/" . $vals["VALUES"];

switch ($vals["action"]) {
    case "SEARCH":
		//echo "searching ...";
        searchGrpNames($vals["COL_LIST"]);
        break;
	case "INSERT_GROUP":
		insertNewGroup($vals["COL_LIST"], $vals["VALUES"], $vals["CONDI"]);
		break;
	case "DELETE_GROUP":
        deleteGroup($vals["CONDI"]);
        break;
}



//search values in tables with condition
function searchGrpNames($cols){
	global $dbc;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	
	$sql = "SELECT " . $cols .  " FROM "  . TAB_GOODGROUP ;
	//echo $sql;
	
	$result = $dbc->userQuery($sql); 
	if (!$result) { // check for errors.
		echo  Message::MSG_ER_GETGRP;
		exit;
	}

	echo json_encode($result);
}


//insert values in tables group
function insertNewGroup($cols, $vals, $condition){
	global $dbc, $retmsg;
	
	$dbc->connectToDatabase();
	$dbc->selectDatabase();
	 

	//on insert new group, the Code(ID) must be fetched at first.
	//$dbc->CheckValueExistingWMS($tablename, $condition) == 1
	$tmps = explode(",", $vals);
	if ( $dbc->CheckValueExisting(TAB_GOODGROUP, "Name", $tmps[1])) {
		echo Message::MSG_DUP_GRPNM;
	} 
	else{
		$newindex = $dbc->GetMaxIndex(TAB_GOODGROUP, "Code");
		$tmpnew = intval($newindex) + 1;
		
		$vals = str_replace("%", "'" . $tmpnew . "'"  ,$vals);
		$sql = "INSERT INTO " . TAB_GOODGROUP . " (" . $cols .  ") VALUES " . $vals;
		//echo $sql;
		
		//insert into product
		if ( $dbc->execWriteStatement($sql)){
			echo Message::MSG_NEW_GRPOK;
		}
		else echo Message::MSG_ER_NEWGRP . "[" . $dbc->lastErrorMsg() . "]";
		
	}
	
	$dbc->closeConnection();
	
}



//insert values in tables with condition
function deleteGroup($condition){
	global $dbc;
	
	//delete from product
	$sql = "DELETE FROM " . TAB_GOODGROUP . " WHERE Name='" . $condition ."'";
	echo $sql;
	if ($dbc->execWriteStatement($sql)){
		//for all goods with the same group, must be set to default group
		$sql="UPDATE " . TAB_PRODUCT . " SET GroupId='default' WHERE GroupId='" . $condition ."'";
		$dbc->execWriteStatement($sql);
		echo Message::MSG_GRP_DELOK;
	}
	else{
		echo Message::MSG_ER_DELGRP;
	}
	
	
}

?>