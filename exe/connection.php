<?php
/**
class createConnection extends SQLite3//create a class for make connection
{
    function connectToDatabase() // create a function for connect database
    {
    }
    function selectDatabase() // selecting the database.
    {
	}
    function closeConnection() // close the connection
    {
	}
	
	//check value existing with tablename, column name and value
	function CheckValueExisting($tname, $colname, $what)
	{
		$sql = "SELECT * FROM $ WHERE " . $colname . "=" . $what . "";
		$sql = str_replace("$", $tname, $sql);
		//echo $sql;Fatal error: Uncaught Error: Call to a member function fetchArray() on array in /customers/c/1/d/h-2technik.com/httpd.www/WMS/lager/exe/connection.php:62Stack trace:#0 /customers/c/1/d/h-2technik.com/httpd.www/WMS/lager/exe/maindata.php(105): createConnec
		$result = $this->query($sql);
	}
	}
		 }
	}
			}
			}
		}
		}
	}
}
?>