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
define ("TAB_USER", "user", true);
define ("TAB_USERLOG", "userlogbook", true);


class UserManager{
	
	//right
	const RIGHT_NEWPRD = 1; //newprd
	const RIGHT_CHAPRD = 2; //chaprd
	const RIGHT_CHACAT = 4; //chagrp
	const RIGHT_STOCKIN = 8; //movein
	const RIGHT_STOCKOUT = 16;  //moveou
	const RIGHT_REPORT = 32;  //report
	const RIGHT_INVENT= 64; //invent
	const RIGHT_CHAPLA= 128; //chaplc
	const RIGHT_WATPLA = 256; //watplc
	const RIGHT_MANUSER = 512; //userma
	const RIGHT_CHASTOCK = 1024; //change stock
	
	//db connection
	private $dbc;
	
	function __construct()
    {
		//echo "---crated---";
        $this->dbc  = new createConnection();
    }
	
	
	function checkUser($name){
		
		$condi = "Name='" . $name . "'";
		$ret = $this->dbc->CheckValueExistingCondition(TAB_USER, $condi);
		//$this->dbc->closeConnection();
		return $ret;
	}
	
	function checkLgoin($name, $pass){
		
		$condi = "Name='" . $name . "' AND Keyword='" . $pass . "'";
		//echo "++++" . $condi . "+++++";
		$ret = $this->dbc->CheckValueExistingCondition(TAB_USER, $condi);
		return $ret;
	}
	
	
	function insertNew($name, $pass, $right, $when, $info){
		
		if ($this->checkUser($name)){
			echo Message::MSG_USR_DUPLI;
			return;
		}
		
		$sql= "INSERT INTO " . TAB_USER . " VALUES('" .$name . "', '" .
              $pass . "', " . $right . ", '" . $when . "', '" . $info . "')";
		echo $sql;
		
		if ($this->dbc->execWriteStatement($sql)){
			Message::logEvent(Message::ACT_NEWUSER);
			echo Message::MSG_USR_OKNEW;
		}
		else echo Message::MSG_USR_ERNEW;
		
		//$this->dbc->closeConnection();
		
	}
	
	function deleteUser($name){
		$sql= "DELETE FROM " . TAB_USER . " WHERE Name='" . $name . "'";
		if ($this->dbc->execWriteStatement($sql)){
			Message::logEvent(Message::ACT_DELUSER);
			echo Message::MSG_USR_DELOK;
		}
		else echo Message::MSG_USR_ERDEL;
	}
	
	function getRights($name){
		$sql= "SELECT Rights FROM " . TAB_USER . " WHERE Name='" . $name . "'";
		
		
		$ret = $this->dbc->GetSingValue($sql);
		//$this->dbc->closeConnection();
		return $ret;
	}
	
	function checkRightIsOK($name, $right){
		$ret = $this->getRights($name) & $right;
		//$this->dbc->closeConnection();
		return $ret;
	}
	
	function loadAllUsers(){
		$sql = "SELECT Name, Rights FROM user";
		//echo $sql;
		
		$result = $this->dbc->userQuery($sql); 
		if (!$result) { // check for errors.
			echo  Message::MSG_ER_GETGRP;
			exit;
		}
	
		$tmparray = array();
		foreach ($result as $row) {
			
			//get right string
			$row[1] = $this->setRightStr($row[1]);
			
			//echo $row[0] . "-" . $row[1];
			array_push($tmparray, $row);
		}
	
		return $tmparray;
	
	}
	
	function setRightStr($rnr){
		$str ="";
		if ($rnr & UserManager::RIGHT_NEWPRD) $str .= "neu Prd/";
		if ($rnr & UserManager::RIGHT_CHAPRD) $str .= "ändern Prd/";
		if ($rnr & UserManager::RIGHT_CHACAT) $str .= "ändern Grp/";
		if ($rnr & UserManager::RIGHT_STOCKIN) $str .= "Einlager/";
		if ($rnr & UserManager::RIGHT_STOCKOUT) $str .= "Auslager/";
		if ($rnr & UserManager::RIGHT_REPORT) $str .= "Berichte/";
		if ($rnr & UserManager::RIGHT_INVENT) $str .= "Inventur/";
		if ($rnr & UserManager::RIGHT_CHAPLA) $str .= "ändern Lager/";
		if ($rnr & UserManager::RIGHT_WATPLA) $str .= "Über. Lager/";
		if ($rnr & UserManager::RIGHT_MANUSER) $str .= "Benutzer Verw/";
		
		return $str;
	}
	
	function userLogInfo($name, $in_or_out, $check){
		
		//check if user already logged in
		if ($check){
			//get user login 
			$sql = "SELECT Timestamp FROM " . TAB_USERLOG . " WHERE User='" . $name. "' AND Status='IN' "  . " ORDER BY Timestamp DESC Limit 1";
			$ret = $this->dbc->GetSingValue($sql);
			//echo ",,,,,," . empty($ret)  . ",,,,,,,";
			return empty($ret);
		}
		
		
		//Log in 
		if ($in_or_out == "IN"){
			$sql = "INSERT INTO " . TAB_USERLOG . " VALUES('" . date("Y-m-d H:i:s") . "', '" . 
			        $name . "', '" . $in_or_out . "','')";
			if ($this->dbc->execWriteStatement($sql)) echo Message::MSG_USER_EINOK;
			else echo Message::MSG_USER_LOGER;
			//echo "&&&&&&&&&" . "----"  . "&&&&";
		}
		
		//Log out
		if ($in_or_out == "OUT"){
			
			//get user login 
			$sql = "SELECT Timestamp FROM " . TAB_USERLOG . " WHERE User='" . $name. "' AND Status='IN' "  . " ORDER BY Timestamp DESC Limit 1";
			$ret = $this->dbc->GetSingValue($sql);
			
			//echo "-----" . $ret . "-------------";
			
			$sql = "UPDATE " . TAB_USERLOG . " SET Status='OUT' WHERE User='" . $name . "' AND Timestamp='" . $ret . "'";
			if ($this->dbc->execWriteStatement($sql)){
				echo Message::MSG_USER_AUSOK;
			}
			else echo Message::MSG_USER_LOGER;
		}
	}
	
}

//--------------------------------------------------------
//echo "123";
if (! isset($_POST["Action"])) return;

$user = new UserManager();


switch ($_POST["Action"]) {
	case "LOAD":
		loadusers();
		break;
    case "NEW":
        newUser($_POST["Name"], $_POST["Pass"], $_POST["Rights"]);
        break;
	case "DELETE":
		deleteUser($_POST["Name"]);
		break;
	case "LOGIN":
		doCheckLogin($_POST["Name"], $_POST["Pass"]);
		break;
	case "GETLOGIN":
	
		if (isset($_SESSION["user"]) && isset($_SESSION["logintime"]) && isset($_SESSION["Right"])){
			echo $_SESSION["user"] . "|" . $_SESSION["logintime"] . "|" . $_SESSION["Right"];
		}
		else echo "-";
		break;
	case "CHECK_PLACE_RIGHT":
		echo $user->getRights($_SESSION["user"]);
		break;
		
	case "LOGOUT":
		if (isset($_SESSION["user"])) logUserOut($_SESSION["user"]);
		else echo Message::MSG_USER_DUPLO;;
		break;
	
	case "CLEAR_LOGIN":
		clearLastLogin($_POST["Name"], $_POST["Pass"]);
		break;
}


function newUser($name, $pass, $right_array){
		
		global $user;
		$tmpright = 0;
		foreach($right_array as $r ){
			//echo $r;
			switch ($r){
				case "newprd":
					//echo "--------------";
					$tmpright += UserManager::RIGHT_NEWPRD; break;
				case "chaprd":
					$tmpright += UserManager::RIGHT_CHAPRD; break;
				case "chagrp":
					$tmpright += UserManager::RIGHT_CHACAT; break;
				case "movein":
					$tmpright += UserManager::RIGHT_STOCKIN; break;
				case "moveou":
					$tmpright += UserManager::RIGHT_STOCKOUT; break;
				case "chamov":
					$tmpright += UserManager::RIGHT_CHASTOCK; break;
				case "report":
					$tmpright += UserManager::RIGHT_REPORT; break;
				case "invent":
					$tmpright += UserManager::RIGHT_INVENT; break;
				case "chaplc":
					$tmpright += UserManager::RIGHT_CHAPLA; break;
				case "watplc":
					$tmpright += UserManager::RIGHT_WATPLA; break;
				case "userma":
					$tmpright += UserManager::RIGHT_MANUSER; break;
			}
		}
		//echo $tmpright;
		$user->insertNew($name, $pass, $tmpright, date("Y-m-d H:i:s") ,'');
	
}

function loadusers(){
	global $user;
	//echo "...loading....";
	$tmparray = array();
	$tmparray = $user->loadAllUsers();
	//echo var_dump($tmparray);
	echo json_encode($tmparray);
}

function deleteUser($name){
	global $user;
	$user->deleteUser($name);
}

function doCheckLogin($name, $pass){
	global $user;
	
	//1. check if the user is alaready logged in
	if ( !$user->userLogInfo($name, "IN", true) ){
		echo Message::MSG_USER_DUPLO;
		return;
	}
	else{
		//2. log the this loggin information
		$user->userLogInfo($name, "IN", false);
	}
	
	if ($user->checkLgoin($name, $pass)){
		$_SESSION["user"] = $name;
		$_SESSION["logintime"] = time();
		$_SESSION["Right"] = $user->getRights($name);
		echo Message::MSG_USER_EINOK;
		//echo $_SESSION["user"] . " --" . $_SESSION["logintime"] . "----" . $_SESSION["Right"];
	}
	else {
		echo Message::MSG_USER_LOGER;
	} 
	
}

function clearLastLogin($name, $pass){
	global $user;
	
	//1. check if the user is alaready logged in
	if ( !$user->userLogInfo($name, "IN", true) ){
		
		//1. forcely remove the last IN 
		$user->userLogInfo($name, "OUT", false);
		
		//2. log the this loggin information
		$user->userLogInfo($name, "IN", false);
		
		//3. re login 
		$_SESSION["user"] = $name;
		$_SESSION["logintime"] = time();
		$_SESSION["Right"] = $user->getRights($name);
		echo Message::MSG_USER_EINOK;
	}
}



function logUserOut($name){
	global $user;
	$user->userLogInfo($name, "OUT", false);
	session_destroy();
	
}

?>