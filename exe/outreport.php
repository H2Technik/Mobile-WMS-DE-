<?php

require('fpdf17/chinese.php');
include('connection.php');


class PDF extends PDF_Chinese
{
	// Page header
	function Header()
	{
		// Logo
		//$this->Image('./imgs/companylogo.png',10,6,20);
		// Arial bold 15
		//$this->AddGBFont('simsun','ËÎÌå');
		$this->SetFont('Arial','',20);
		// Move to the right
		$this->Cell(80);
		// Title
		//$this->Cell(0,10,iconv('utf-8','GB2312','北京鑫晨星工程机械服务技术服务有限公司'),0,0,'C');
		// Line break
		$this->Ln(20);
	}

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		
		$this->SetFont('Arial','',10);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

//$globalpah= "./WMS/";



if ($_POST['WHAT'] == "OUT_REP"){
	  
	  
		global $itemval, $items;
	  
		$dbc = new createConnection(); 
		$dbc->connectToDatabase();
		$dbc->selectDatabase();

		// Instanciation of inherited class
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage("L");
		$pdf->AddGBFont('simsun','ËÎÌå');
		$pdf->SetFont('simsun','',10);
		$pdf->Image('../imgs/companylogo.png',20,25,20);
		$pdf->Cell(0,10,iconv('utf-8','GB2312','北京鑫晨星工程机械技术服务有限公司'),0,1,'C');
		$pdf->Ln(5);
		$pdf->SetFont('simsun','',20);
		$pdf->Cell(0,10,iconv('utf-8','GB2312','出库表'),0,1, 'C');

		$pdf->SetFont('simsun','',10);
		//$pdf->Cell(190,5,iconv('utf-8','GB2312','报价单号 QN:'). $_POST['txtplannr'],0,1,'C');
		$pdf->Cell(190,5,iconv('utf-8','GB2312','日期 Date:'). date("Y-m-d"),0,1,'C');
		$pdf->Ln(3);
		
		//check if contract Nr. check 
		if (strpos($_POST['CONDITION'], 'ContractNr') !== false) {
			$tmps1 = explode("ContractNr", $_POST['CONDITION']);
			$tmps2 = explode("LIKE", $tmps1[1]);
			$pdf->Cell(190, 5, iconv('utf-8','GB2312','销售合同:') . iconv('utf-8','GB2312', $tmps2[1]),0,0,'C');
		}
		
		/*$tmp1 = GetEquipInfo($_POST['defectnr'], "CautionNr", $dbc);
		$pdf->Cell(100,5,iconv('utf-8','GB2312','设备编号 Code:').iconv('utf-8','GB2312',$tmp1),0,0,'L');
		$tmp2 = GetEquipInfo($_POST['defectnr'], "SerialNr", $dbc);
		$pdf->Cell(50,5,iconv('utf-8','GB2312','设备序号:'). iconv('utf-8','GB2312',$tmp2),0,1,'R');
		
		$tmp3 = GetEquipInfo($_POST['defectnr'], "EquName", $dbc);
		$pdf->Cell(100,5,iconv('utf-8','GB2312','设备名称 Name:'). iconv('utf-8','GB2312',$tmp3),0,0,'L');
		$tmp4 = GetEquipInfo($_POST['defectnr'], "EquType", $dbc);
		$pdf->Cell(50,5,iconv('utf-8','GB2312','设备类型 Mode:').iconv('utf-8','GB2312',$tmp4),0,1,'R');
		*/
		
		//draw seperate line
		$offsettoup = 75;
		Drawline();
		
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','序号'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','日期'),0, 0);
		$pdf->cell(10);
		$pdf->cell(20, 10, iconv('utf-8','GB2312','件号'),0, 0);
		$pdf->cell(10);
		$pdf->cell(30, 10, iconv('utf-8','GB2312','名称'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','客户'),0, 0);
		$pdf->cell(10);
		$pdf->cell(5, 10, iconv('utf-8','GB2312','单位'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','出库量'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','出库单价'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','总金额'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','备注'),0, 0);
		
		$pdf->Ln(3);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Nr.'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Date'),0, 0);
		$pdf->cell(10);
		$pdf->cell(20, 10, iconv('utf-8','GB2312','Equ. ID'),0, 0);
		$pdf->cell(10);
		$pdf->cell(30, 10, iconv('utf-8','GB2312','Name'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Customer'),0, 0);
		$pdf->cell(10);
		$pdf->cell(5, 10, iconv('utf-8','GB2312','Unit'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Quan.'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Price'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Sum'),0, 0);
		$pdf->cell(10);
		$pdf->cell(10, 10, iconv('utf-8','GB2312','Remark'),0, 0);
		$pdf->Ln(8);

		$offsettoup +=31;
	
		//07.10.2015
		//search table : wms_balanced, wms_parts
		$totalprice = 0.0;
		$inx =1;
		
		$sql =  "SELECT wms_stockout.Date, wms_stockout.Parts_SerialNr, wms_parts.Name, wms_stockout.Rev1, " ;
		$sql .= "wms_stockout.Count, wms_balanced.StockCount, wms_parts.InPrice, wms_stockout.Rev2 ";
		$sql .= "FROM wms_parts, wms_balanced, wms_stockout WHERE " . $_POST['CONDITION'] ;
				
		echo $sql;
		
		$result = mysql_query($sql, $dbc->myconn);
		$cnt = mysql_num_rows($result);
		if ($cnt >= 1){
			while ($row = mysql_fetch_assoc($result)) {
				//$ret = $row[$what];
				//echo "-" . $row['Name'] . "-" . $row['SerialNr'] . "-";
				FormatContent($inx, $row);
				
				
				++$inx;
				$totalprice += floatval($row["InPrice"]) * floatval($row["StockCount"]);
			}
		}
		
		//output total price
		$pdf->Ln(10);
		$pdf->SetFont('simsun','',20);
		$pdf->cell(140, 10, iconv('utf-8','GB2312', '合计:    ') . $totalprice,0, 1, 'R');
		$pdf->SetFont('simsun','',10);

		//DrawUnderline();
		$offsettoup += $inx *10 + 50;
		Drawline();

		//signature area
		//DrawSignature( );

		//save pdf in specialdefect or generaldefect folder
		//before saving , if there has been already on, delete it
		
		$fname = "out_" . date("Ymd") . ".pdf";
		makesingleoffer($fname);
		$savereport = "../output/" . $fname;
		
	
		$pdf->Output($savereport, "F");
		echo "成功生成出库表 ！" . ">" .  "output/" . $fname;
		
		
		$dbc->closeConnection();
  }
  else{
	  
	  echo "数据不完整, 无法生成报告！";
  }
	

	
//----------------assistant functions ---------------
//
//----------------------------------------------------
function FormatContent($i, $si){
	
	global $pdf;
	

	//echo var_dump($si);
	//echo $si['desc'] . $si['price'] . $si['quty'] . $si['other'];
	$pdf->cell(10);
	$pdf->cell(10, 10, $i,0, 0);
	$pdf->cell(10);
	$pdf->cell(10, 10, $si['Date'],0, 0);
	$pdf->cell(10);
	$pdf->cell(20, 10, iconv('utf-8','GB2312',$si['Parts_SerialNr']),0, 0);
	$pdf->cell(10);
	$pdf->cell(30, 10, iconv('utf-8','GB2312', $si['Name']),0, 0);
	$pdf->cell(10);
	$pdf->cell(10, 10, iconv('utf-8','GB2312', $si['Rev1']) ,0, 0);
	$pdf->cell(10);
	$pdf->cell(5, 10, iconv('utf-8','GB2312', '-'),0, 0 ,0, 0);
	$pdf->cell(10);
	$pdf->cell(10, 10, $si['Count'],0, 0);
	$pdf->cell(10);
	$pdf->cell(10, 10, $si['InPrice'],0, 0);
	$pdf->cell(10);
	$pdf->cell(10, 10, floatval($si['Count']) * floatval($si['InPrice']),0, 0 );
	$pdf->cell(10);
	$pdf->cell(10, 10, iconv('utf-8','GB2312', $si['Rev2']),0, 0);
	$pdf->Ln(5);
	
}


function retTypeCondition($t){
	
	$ret = "";
	
	switch ($t) {
    case 1:
        $ret = "";
        break;
    case 2:
         $ret = " AND wms_parts.MachineMode='整机'";;
        break;
    case 3:
         $ret = " AND wms_parts.MachineMode='配件'";;
        break;
	case 4:
         $ret = " AND wms_parts.MachineMode='其他耗材'";;
        break;
	}
	return $ret;
}


function Drawline(){
	
	global $pdf, $offsettoup;
	
	// Linienfarbe auf Blau einstellen 
	$pdf->SetDrawColor(0, 0, 255);
	// Linienbreite einstellen, 0.5 mm
	$pdf->SetLineWidth(0.5);
	// Linie zeichnen
	$pdf->Line(20, $offsettoup, 255, $offsettoup);
	// Linienbreite einstellen, 1 mm
	$pdf->SetLineWidth(1); 
	
	//$pdf->cell(10,10, $offsettoup, 0,1);
	
}

function DrawUnderline(){
	
	global $offsettoup, $pdf;
	$pdf->cell(10);
	$pdf->Cell(0,10, "", 0,1, 'C');
	//$pdf->cell(10,10, $offsettoup, 0,1);
}


function DrawSignature(){
	
	global $pdf, $offsettoup, $cnt;
	
	$pdf->cell(10, 10, iconv('utf-8','GB2312','鑫晨星负责人 Representativ of MS:'),0, 0);
	$pdf->cell(90);
	$pdf->cell(10, 10, iconv('utf-8','GB2312','利氏负责人 Representativ of RBA:'),0, 0);
	$pdf->cell(10);
	
	$pdf->Ln(10);
		
	$pdf->cell(10, 10, iconv('utf-8','GB2312','签字 Signature:'),0, 0);
	//$pdf->Image('../company_docs/sign_jiazhiming.png',70, $offsettoup, 20);
	$pdf->cell(90);
	$pdf->cell(10, 10, iconv('utf-8','GB2312','签字 Signature:'),0, 0);
	$pdf->cell(10);
	$pdf->ln(10);
	$pdf->Image('../company_docs/sign_jiazhiming.png', null, null, 20);
}


function makesingleoffer($defnr){
	
	//global $isgeneral;
	
	//if ($isgeneral == "true") $searchpath = "../Inspection/General_offer";
	//else
	$searchpath = "../output/";
	
	$fs = scandir($searchpath);
	foreach($fs as $f){
		if ( strpos($f, $defnr) !== false && strpos($f, ".pdf") !== false ){
			unlink($searchpath . "/" . $f);
		}
	}
}

function GetEquipInfo($defnr, $what, $db){
	
	global $isgeneral;
	
	$ret="";
	//search 
	if ($isgeneral == "true"){
		
		if ($what == "CautionNr") return "";
		
		$sql= "SELECT " . $what .  " FROM generaldefect WHERE ID=\"" . $defnr . "\"";
		$result = mysql_query($sql, $db->myconn);
		$cnt = mysql_num_rows($result);
		if ($cnt >= 1){
			while ($row = mysql_fetch_assoc($result)) {
				$ret = $row[$what];
			}
		}
	}
	else{
		
		if ($what == "SerialNr") return "";
		
		$sql= "SELECT " . $what .  " FROM specialdefect WHERE ID=\"" . $defnr . "\"";
		$result = mysql_query($sql, $db->myconn);
		$cnt = mysql_num_rows($result);
		if ($cnt >= 1){
			while ($row = mysql_fetch_assoc($result)) {
				$ret = $row[$what];
			}
		}
	}
	return $ret;
}

function RefreshStatus($defnr, $plannr, $statusnr, $db){
	
	//get status name
	$sql1= "SELECT Name FROM statustable WHERE ID='" . $statusnr ."'";
	$result = mysql_query($sql1, $db->myconn);
	
	$cnt = mysql_num_rows($result);
	$statusname ="";
	if ($cnt >= 1){
		while ($row = mysql_fetch_assoc($result)) {
			 $statusname = $row["Name"];
		}
	}
	
	//update status for repairplan
	$sql2 = "UPDATE `repairplan` SET PlanStatus=\"" . $statusname . "\"" .
				   " WHERE DefectID=" . "\"" . $defnr . "\"" . " AND PlanID=\"" . $plannr . "\"";
	
	//echo $sql2;
	if( !mysql_query($sql2, $db->myconn) ) echo " 更新状态错误 ！";
}

?>


