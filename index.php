<?php

session_start();
 
 //echo $_SESSION["username"];
 
 if (isset($_SESSION["username"]) && 
     isset($_SESSION["password"]))
	 {

		header("location: in_menu.html");
	 }
 else{
	 
	 //header("location: login.html");
	 //echo "nobody";
	 header("location: in_login.html");
 }

?>
