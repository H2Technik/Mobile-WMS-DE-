
var SUM_REPORT="MAIN_REP";
var IN_REPORT="IN_REP";
var OUT_REPORT="OUT_REP";
var report_index = 0;

function createreports(){
	
	alert(report_index);
	//1=product, 2=balanced, 4=inventur, 8=stockin, 16=stockout
	if ($("#id_reportprod").is(":checked")) report_index += 1;
	if ($("#id_reportbal").is(":checked")) report_index += 2;
	if ($("#id_reportinv").is(":checked")) report_index += 4;
	if ($("#id_reportstockin").is(":checked")) report_index += 8;
	if ($("#id_reportstockout").is(":checked")) report_index += 16;

	if (report_index == 0) {
		alert("Kein Bericht ausgewählt !");
		return;
	}
	asynAjaxCallReport(SUM_REPORT, report_index);
}


function asynAjaxCallReport(act_id, val1){
	
	var  urlstr="";
	if (act_id == SUM_REPORT) urlstr = "./exe/createxls.php";
	if (act_id == IN_REPORT) urlstr = "./exe/inreport.php";
	if (act_id == OUT_REPORT) urlstr = "./exe/outreport.php";
	
	$.ajax({
			type: "POST",
			url: urlstr,
			data: {"WHAT":act_id, "INDEX":val1}
			})
			.done(function( message ) {
				alert(message);	
				var tmps = message.split("&");
				if (tmps.length == 2) $("#id_filelink").append(tmps[1]);
				else alert(message);
			});
	
	
}

