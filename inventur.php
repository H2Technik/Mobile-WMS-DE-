<?php 
 
	$var = "No";
	if( isset($_POST["json"]) ) {
		 $data = json_decode($_POST["json"]);
		 $var = $data->msg;
		 $data->msg = strrev($data->msg);
		
		 $arr = array('msg' => "123456");
		
		 echo json_encode($arr);
		 $var=$arr->msg;
	}

	//reload page
	header("Refresh:5; url=inventur.php");
	
?>
