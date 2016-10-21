<?php 
/** Include path **/
include './PHPExcel-1.8/Classes/PHPExcel.php';
include ('./connection.php');
require 'message.php';


/** PHPExcel_Writer_Excel2007 */
include './PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php';

	class CreateExcelTable extends PHPExcel{
	
		private $shtindex = 10;
	    
		
		//open excel application / new xls file
		function prepareXls(){

			$this->getProperties()->setCreator("W.H.");
			$this->getProperties()->setLastModifiedBy("W.H.");
			$this->getProperties()->setTitle("Office 2007 XLSX");
			$this->getProperties()->setSubject("Office 2007 XLSX Test");
			$this->getProperties()->setDescription("Office 2007 XLSX, generated using PHP classes.");
			
		}
		
		//save excel 2007
		function SaveXls($name){
			$xls2007Writer = new PHPExcel_Writer_Excel2007($this);
			$xls2007Writer->save($name . ".xlsx");
		}
		
		function formatTitle($wksht){
			
			//type
			$wksht->mergeCells("C1:D1");
			$wksht->setCellValue( "C1" , "客户类型");
			$this->colorArea($wksht, "C1", "00FF00");
			
			//source
			$wksht->mergeCells("E1:G1");
			$wksht->setCellValue( "E1" , "信息来源");
			$this->colorArea($wksht, "E1", "00FFFF");
			
			//level
			$wksht->mergeCells("H1:K1");
			$wksht->setCellValue( "H1" , "信息等级");
			$this->colorArea($wksht, "H1", "00AABB");
			
			//type
			$wksht->mergeCells("L1:N1");
			$wksht->setCellValue( "L1" , "状态");
			$this->colorArea($wksht, "L1", "00AAFF");
		}
		
		function colorArea($sht, $cell, $color){
				$sht->getStyle($cell)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => $color)
						)
					)
				);
		}
	
	
		//add new sheet (set name), and add title, add values
		function transferValue($shtname, $titlearray, $valuesmatrix){
			
				//create sheet
				$tmpsht = $this->createSheet($this->shtindex++);
				$tmpsht->setTitle($shtname);
				
				//format title area
				//$this->formatTitle($tmpsht);
				
				//add title
				$col = 'A';
				$row = 1;
     			foreach($titlearray as $t){
					//echo $col . $row;
					
					$tmpsht->setCellValue( $col . $row , $t);
					$this->colorArea($tmpsht, $col . "1", "00FFFF");
					$col = chr(ord($col)+1);
				}
				
				//add values
				$col='A';
				$row = 2;
				
				$dicnt = count($valuesmatrix);
				for ($r = 0; $r < $dicnt; $r++) {
					$itcnt = count($valuesmatrix[$r]);
					for ($c = 0; $c < $itcnt; $c++) {
						//echo $col . $row;
						$tmpsht->setCellValue( $col . $row , $valuesmatrix[$r][$c]);
						$col = chr(ord($col)+1);
					}
					$col='A';
					++$row;
				}
				$BStyle = array(
					'borders' => array(
						'outline' => array(
							'style' => PHPExcel_Style_Border::BORDER_THICK
						)
					)
				);
				$tmpsht->getStyle('A1:N2')->applyFromArray($BStyle);
			}
	}
	

	define ("TAB_PRODUCT", "product", true);
	define ("TAB_MOVEIN", "movein", true);
	define ("TAB_MOVEOUT", "moveout", true);
	define ("TAB_BALANCED", "balanced", true);
	define ("TAB_GOODGROUP", "goodgroup", true);
	define ("TAB_INVENTUR", "inventur", true);
	
	//get json data
	$vals = json_decode(file_get_contents('php://input'), true );

	$xlscreator = new CreateExcelTable;
	$dbc = new createConnection(); 
	$dbc->connectToDatabase();
	$dbc->selectDatabase();

	$xlscreator->prepareXls();
	
	$sqltotal = "SELECT " . $vals["COL_LIST"] . " FROM " . $vals["TAB_LIST"] . " WHERE " . $vals["CONDI"];
	echo $sqltotal;
	CreateXlsTabele($sqltotal);
		
	$pathname = "../output/";
	$fname = date("Y-m-d_His") . "_user_report";
	$xlscreator->saveXls($pathname . $fname);
		
	echo $fname . " " . Message::MSG_OK_REPUSR . "&" . "<a href='./output/" . $fname . ".xlsx' download>" . $fname . "</a> .";
	
	
	function CreateXlsTabele($sql){  
   
			global $xlscreator;
			////////////////////////////////////////////////////////////
			//
			// create information list and return
			//
			//
			//table header
			$valuesm=array();
			
			//parse the passed user name , if it is "admin", list all items, if not return only the items
			//which are created by himself.
			
			
			$dbc = new createConnection(); 
			$dbc->connectToDatabase();
			$dbc->selectDatabase();
			
			//get column name of userQuery
			$title=array();
			$colcnt = 0;
			$result = $dbc->userQuery($sql);
			if (!$result ){
				echo Message::MSG_NO_REPDATA;
				exit;
			}
			
			$title = $dbc->getSQLColumns($sql);
			$colcnt = count($title); 
			
			
			//construct search condition
			$result = $dbc->userQuery($sql);
			foreach($result as $row) {
				
				//sub-value array
				$tmparray = array();
				for($i = 0; $i<$colcnt; ++$i) array_push($tmparray, $row[$i]);
				array_push($valuesm, $tmparray);
			}
			$dbc->closeConnection();
            $xlscreator->transferValue("user_report", $title, $valuesm);
			
	}

?>