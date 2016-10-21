<?php
/** * @Wei * @copyright 2016 connect sqlite3 database */ 
class createConnection extends SQLite3//create a class for make connection
{	private $db_path = "../DB/08012016.db";		function __construct()    {        $this->open($this->db_path);    }		
    function connectToDatabase() // create a function for connect database
    {
    }
    function selectDatabase() // selecting the database.
    {		
	}
    function closeConnection() // close the connection
    {		$this->close();
	}
	
	//check value existing with tablename, column name and value
	function CheckValueExisting($tname, $colname, $what)
	{
		$sql = "SELECT * FROM $ WHERE " . $colname . "=" . $what . "";
		$sql = str_replace("$", $tname, $sql);
		//echo $sql;Fatal error: Uncaught Error: Call to a member function fetchArray() on array in /customers/c/1/d/h-2technik.com/httpd.www/WMS/lager/exe/connection.php:62Stack trace:#0 /customers/c/1/d/h-2technik.com/httpd.www/WMS/lager/exe/maindata.php(105): createConnec
		$result = $this->query($sql);		if (!$result) return false;				$cnt = count($result->fetchArray()); 				if ( $cnt > 1) return true;		return false;
	}		//check value existing with condition	function CheckValueExistingCondition($tname, $condition)	{		$sql = "SELECT * FROM $ WHERE " . $condition;		$sql = str_replace("$", $tname, $sql);		//echo $sql;			//$result = $this->query($sql);		$result = $this->query($sql);		if (!$result) return false;				$cnt = count($result->fetchArray()); 				if ( $cnt > 1) return true;		return false;	}		function userQuery($sql){		return $this->concurrentAvoider($sql, 1);
	}		/*function userQueryArray($sql){				$arr = array();				$result = $this->query($sql);		 while ($row = $result->fetchArray()){			 array_push($arr, $row);
		 }		 return $arr;	}*/		function execWriteStatement($sql)	{		//echo $sql;		//return $this->exec($sql);		return $this->concurrentAvoider($sql, 2);
	}			function GetMaxIndex($tname, $colname){			$sql="SELECT MAX(" . $colname . ") as max_index FROM " . $tname ;		//echo $sql;		$result = $this->query($sql);		if (! $result ) return 0;				$arr = array();		$arr = $result->fetchArray();				return $arr[0];	}		function GetSingValue($sql){			//echo $sql;		$result = $this->query($sql);		if (! $result ) return 0;				$arr = array();		$arr = $result->fetchArray();				return $arr[0];	}		function getTableColumns($tbname){				$sql = "PRAGMA table_info(" . $tbname . ");";		return $this->userQuery($sql);			}		function getSQLColumns($sql){				$arr = array();		$result = $this->query($sql);			$colcnt = $result->numColumns(); 				while ($row = $result->fetchArray()) {				for ($i = 0; $i < $colcnt; $i++) {					array_push($arr, $result->columnName($i));				} 				//only one loop enough for getting column names				break;		}		return $arr;	}			/*1=query(), 2=exec()*/	function concurrentAvoider($sql, $act){				//echo $sql;		$arr = array();						//query (select)		if ($act == 1){			$ret = $this->query($sql);			$cnt = 0;			while( ++$cnt<3 && (!$ret)){				sleep(1);				$ret = $this->query($sql);
			}						if ($ret) {				while ($row = $ret->fetchArray()){					array_push($arr, $row);				}				return $arr;
			}			else return $ret;
		}				//exec (insert, update)		if ($act == 2){						$ret = $this->exec($sql);			$cnt = 0;			while( ++$cnt<3 && (!$ret)){				sleep(1);				$ret = $this->exec($sql);			}			return $ret;
		}		
	}
}$dbc  = new createConnection();
?>