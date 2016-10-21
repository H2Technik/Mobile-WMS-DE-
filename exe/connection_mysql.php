<?php
/** * @Wei * @copyright 2016 connect MySQL DB using PDO_MySQL */ 
class createConnection 
{			var $host="127.0.0.1";    var $username="root";    // specify the sever details for mysql    Var $password="123456";    var $database="wms";    var $myconn;		/*    var $host="bdm12360424.my3w.com";    var $username="bdm12360424";    // specify the sever details for mysql    Var $password="db19930821";    var $database="bdm12360424_db";    var $myconn;	*/		function __construct()    {         //$conn= mysql_connect($this->host,$this->username,$this->password);		$conn = new PDO('mysql:host=' . $this->host . ";dbname=" . $this->database, $this->username,$this->password);        if( $this->myconn ) die ("Fehler bei Verbinden DB ！");        else{			$this->myconn = $conn;			//echo "DB ist verbunden .";
		}    }    function connectToDatabase() // create a function for connect database    {    }    function selectDatabase() // selecting the database.    {    }    function closeConnection() // close the connection    {        $this->myconn = null;		//mysql_close($this->myconn);        //echo "关闭数据库";    }	//---------------------------------------------------------
	
	//check value existing with tablename, column name and value
	function CheckValueExisting($tname, $colname, $what)
	{
		$sql = "SELECT * FROM $ WHERE " . $colname . "=" . $what . "";
		$sql = str_replace("$", $tname, $sql);
		//echo $sql;
		$result = $this->myconn->query($sql);		$cnt = count($result->fetchAll(PDO::FETCH_BOTH)); 				if ( $cnt >= 1) return true;		return false;
	}		//check value existing with condition	function CheckValueExistingCondition($tname, $condition)	{		$sql = "SELECT * FROM $ WHERE " . $condition;		$sql = str_replace("$", $tname, $sql);				//echo $sql;			$result = $this->myconn->query($sql);		$cnt = count($result->fetchAll(PDO::FETCH_BOTH)); 						if ( $cnt >= 1) {			//echo "#---" . print_r($this->myconn->errorInfo()) . "---#";			return true;
		}		return false;					}		function userQuery($sql){		$result = $this->myconn->query($sql);		return $result->fetchAll(PDO::FETCH_BOTH);
	}		function fetchArray($arr){		return $arr->fetch(PDO::FETCH_BOTH );
	}		function execWriteStatement($sql)	{		//echo $sql;		return $this->myconn->exec($sql);		//return $this->concurrentAvoider($sql, 2);
	}			function GetMaxIndex($tname, $colname){			$sql="SELECT MAX(" . $colname . ") as max_index FROM " . $tname ;		//echo $sql;		$result = $this->myconn->query($sql);		if (! $result ) return 0;				$arr = array();		$arr = $result->fetch();				return $arr[0];	}		function GetSingValue($sql){			//echo $sql;		$result = $this->myconn->query($sql);		if (! $result ) return 0;				$arr = $result->fetch(PDO::FETCH_BOTH);		return $arr[0];	}		function getTableColumns($tbname){		$sql = "DESCRIBE " . $tbname;		return $this->userQuery($sql);
	}		function getSQLColumns($sql){				$arr = array();		$result = $this->myconn->query($sql);				for ($i=0; $i<$result->columnCount(); $i++){				//echo "+++++" . $result->fetchColumn($i) . "++++";				$meta = $result->getColumnMeta($i);				echo "+++++" . $meta["name"] . "++++";				array_push($arr, $meta["name"]);
		}		return $arr;
	}		/*1=query(), 2=exec()*/	/*function concurrentAvoider($sql, $act){				//echo $sql;				//query (select)		if ($act == 1){			$ret = $this->query($sql);			$cnt = 0;			while( ++$cnt<3 && (!$ret)){				sleep(1);				$ret = $this->query($sql);
			}			return $ret;
		}				//exec (insert, update)		if ($act == 2){						$ret = $this->exec($sql);			$cnt = 0;			while( ++$cnt<3 && (!$ret)){				sleep(1);				$ret = $this->exec($sql);			}			return $ret;
		}		
	}*/
}$dbc  = new createConnection();
?>