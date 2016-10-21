

var cntselarea = 0;
var condition="<Select id='id_selcondition_,' onchange='changelogic(,)'> " +
				"<option>ID</option>" +
				"<option>Name</option>" +
				"<option>Lieferant</option>" +
				"<option>Kunden</option>" +
				"<option>Lagerbestand</option>" +
				"<option>Lagerbestand max. Alarm</option>" +
				"<option>Lagerbestand min. Alarm</option>" +
				"<option>Auslagerung Datum</option>" +
				"<option>Einlagerung Datum</option>" +
				"<option>Einkauf Preis</option>" +
				"<option>Verkauf Preis</option>" +
				"</Select>" + 
				"<Select id='id_logicalop_,'></Select>" +
				"<input type='text' id='id_conditionval_,'><br> AND <br>"; 
				
	
var logical_1 = "<option>></option><option><</option><option>>=</option><option><=</option><option>=</option>"
var logical_2 = "<option>=</option><option>like</option>"

function changelogic(sel){
	
	var tval = $("#id_selcondition_" + sel).val();
	
	if ( tval.indexOf("Lagerbestand") == 0 ||
	     tval.indexOf("Lagerbestand max. Alarm") == 0 ||
		 tval.indexOf("Lagerbestand min. Alarm") == 0 ||
		 tval.indexOf("Auslagerung Datum") == 0 ||
		 tval.indexOf("Einlagerung Datum") == 0 ||
		 tval.indexOf("Einkauf Preis") == 0 ||
		 tval.indexOf("Verkauf Preis") == 0 ){
	
		$("#id_logicalop_" + sel).empty().append(logical_1);
	}
	else $("#id_logicalop_" + sel).empty().append(logical_2);
	
	
}

function addnewselection(){

	++cntselarea;
	var tmp = condition.replace(/,/g, cntselarea);
	
	$("#id_conditionarea").append(tmp);
}


function createuserreport(){
	
	
	var j_userreport = {"action":"USER_REPORT", "COL_LIST":"", "TAB_LIST":"", "CONDI":""};
	
	//output column list
	j_userreport.COL_LIST = " * ";
	
	//find table list and construct condition
	
	for (var i=1; i <= cntselarea; i++){
		
		//check table existing
		
		$tmptname = findTableNameByCondition( $("#id_selcondition_" + i).val() );
		
		//alert("1" + $tmptname);
		
		if ( !checkTableExisting(j_userreport.TAB_LIST, $tmptname) ){
				j_userreport.TAB_LIST += $tmptname + ",";
		}
		
		var subcondi = constructSearchItem(i);
		if ( j_userreport.CONDI.length >0 && subcondi.length >0 )	j_userreport.CONDI += " AND " + subcondi;
		else j_userreport.CONDI += subcondi;
		
		alert(j_userreport.CONDI);
		
	}
	
	j_userreport.TAB_LIST = j_userreport.TAB_LIST.substring(0, j_userreport.TAB_LIST.length -1);
	alert(j_userreport.TAB_LIST);
	alert(j_userreport.CONDI);
	asynAjaxCallUserReport(0, j_userreport);
}

function asynAjaxCallUserReport(act_id, jsondata){
	
	$.ajax({
			type: "POST",
			url: "./exe/createuserxls.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				alert(message);
				alert(message);	
				var tmps = message.split("&");
				if (tmps.length == 2) $("#id_filelink").append(tmps[1]);
				else alert(message);
			});
}

function findTableNameByCondition(val){
	
	//alert( val.indexOf("ID")  );
	
	
	if (val.indexOf("ID") == 0 ) return "product";
	if (val.indexOf("Name") == 0 ) return "product";
	if (val.indexOf("Lieferant") == 0) return "product";
	if (val.indexOf("Kunden") == 0) return "moveout";
	
	if (val.indexOf("Lagerbestand max. Alarm") == 0) return "product, balanced";
	if (val.indexOf("Lagerbestand min. Alarm") == 0) return "product, balanced";
	
	if (val.indexOf("Lagerbestand") == 0) return "balanced";
	if (val.indexOf("Auslagerung Datum") == 0) return "moveout";
	if (val.indexOf("Einlagerung Datum") == 0) return "movein";
	if (val.indexOf("Einkauf Preis") == 0) return "product";
	if (val.indexOf("Verkauf Preis") == 0) return "product";
}

function findConditionColumn(val){
	
	if (val.indexOf("ID")>-1) return "product.Code,'%'";
	if (val.indexOf("Name")>-1) return "product.Name,'%'";
	if (val.indexOf("Lieferant")>-1) return "product.Supplier,'%'";
	if (val.indexOf("Kunden")>-1) return "moveout.Customer,'%'";
	
	
	if (val.indexOf("Lagerbestand max. Alarm")>-1) return "(product.Max < balanced.Quantity AND product.Code=balanced.Code) ";
	if (val.indexOf("Lagerbestand min. Alarm")>-1) return "(product.Min > balanced.Quantity AND product.Code=balanced.Code ) ";
	
	if (val.indexOf("Lagerbestand")>-1) return "balanced.Quantity,";
	
	if (val.indexOf("Auslagerung Datum") == 0) return "moveout.OutDate,'%'";
	if (val.indexOf("Einlagerung Datum") == 0) return "movein.InDate,'%'";
	
	if (val.indexOf("Einkauf Preis")>-1) return "product.InPrice,";
	if (val.indexOf("Verkauf Preis")>-1) return "product.OutPrice,";
}


function constructSearchItem(inx){
	
	//logical operator
	var logic = $("#id_logicalop_" + inx).val();
	var selval = $("#id_selcondition_" + inx).val();
	var val = $("#id_conditionval_" + inx).val();
	
	//alert( selval + logic + val);
	
	var tmps = findConditionColumn(selval);
	
	if ( val.length == 0 && 
		selval.indexOf("Lagerbestand max. Alarm") == -1 && 
		selval.indexOf("Lagerbestand min. Alarm") == -1 ) return "";
	else{
		
		//string
		if (tmps.indexOf(",") >-1 && tmps.indexOf("%") >-1){
				
				if (logic.indexOf("like") >-1){
					tmps = tmps.replace(/,/g, " " + logic + " ");
					tmps = tmps.replace(/%/g, "%" + val + "%");
				}
				else{
					tmps = tmps.replace(/,/g, " " + logic + " ");
					tmps = tmps.replace(/%/g, val);
				}
				
		}
		//number
		if (tmps.indexOf(",") >-1 && !tmps.indexOf("%") >-1){
			tmps = tmps.replace(/,/g, " " + logic + " ");
			tmps +=val;
		}
		
		
	}
	return tmps;
}

function checkTableExisting(tables, tname){
	return tables.indexOf(tname) > -1;
}