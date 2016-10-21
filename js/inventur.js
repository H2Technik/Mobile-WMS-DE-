var key_number = "number";
var key_string = "string";

var UPDATE_INV_INFO = 0;
var SEARCH_ID_RET = 1;
var SEARCH_SUP_RET = 3;
var SEARCH_NAME_RET = 2;

var global_max=0;
var global_min=0;

function loadbalancedvalue(){
	//check ID
	if (!checkSerialName("id_f1_serialnr", "-", "-")) return;
	
	//1. get current count
	var j_serach = {"action":"SEARCH_ID", "CONDI":""};
	j_serach.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr");
	//alert(j_serach.CONDI);
	asynAjaxCall(j_serach, SEARCH_ID_RET);
	
	//2. get supplier
	var j_serach2 = {"action":"SEARCH_SUPPLIER", "CONDI":""};
	j_serach2.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr");
	//alert(j_serach2.CONDI);
	asynAjaxCall(j_serach2, SEARCH_SUP_RET);
}

function saveinventoryinfo(){
	
	//check ID
	if (!checkSerialName("id_f1_serialnr", "id_curcnt", "id_actcnt")) return;
	
	var j_update = {"action":"UPDATE_INV_INFO", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Code`, `Must`, `Actual`, `Differ`, `Date`";
	
	j_update.VALUES = "(" + returnValue(key_string, "id_f1_serialnr") + ","; //code
	j_update.VALUES += returnValue(key_number, "id_curcnt") + ",";		//must
	j_update.VALUES += returnValue(key_number, "id_actcnt") + ",";		//actual
	j_update.VALUES += parseInt(returnValue(key_number, "id_actcnt")) - parseInt(returnValue(key_number, "id_curcnt")) + ",";		//differ
	j_update.VALUES += returnDate() + ")";
	
	j_update.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr");
	alert(j_update.VALUES);
	asynAjaxCall(j_update, UPDATE_INV_INFO);
}


function asynAjaxCall(jsondata, act_id){
	
	$.ajax({
			type: "POST",
			url: "./exe/inventur.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				useralert(message, 1);
				//load infor
				if (act_id == SEARCH_ID_RET){
					
					if (message.indexOf("-") >= 0) {
						alert("keine Treffer gefunden !");
						return;
					}
					else {
						var tmps = message.split("#");
						$("#id_curcnt").val(tmps[0]);
						if (tmps.length == 3){
							tmps[1] = tmps[1].replace(/\|/g, "<>");
							$("#id_actstockplaces").val(tmps[1]);
							$("#id_prodname").text(tmps[2]);
						}
					}
				}
				
				if (act_id == SEARCH_SUP_RET){
					
					if (message.indexOf("?") >= 0) {
						useralert("keine Lieferant Infor gefunden !");
						return;
					}
					else{
						var tmps = message.split("|");
						$("#id_supplier").val(tmps[0]);
						global_max = tmps[1];
						global_min = tmps[2];
						
						var cur = $("#id_curcnt").val();
						if ( (cur-global_min)<=0 || (cur - global_max)>=0)  {
							$("#id_curcnt").css('background-color','#f00');
						}
						
					} 
				}
				
				//save new inventur info
				if (act_id == UPDATE_INV_INFO){
					var tmps = message.split("|");
					if (tmps.length == 2){
						//alert(tmps[1]);
						$("#id_curcnt").val(tmps[1]);
						//alert(tmps[0]);
						
						//reset timer
						resetTimer();
					}
					else useralert(tmps[0], 2);
				}
				
			});
}

function checkSerialName(serial, must, actual){
	var retval = true;
	//ID
	//Serial, Name
	if ($("#" + serial ).val().length === 0 ){
			useralert("kein Produkt ID !", 2);
			retval &= false;
		}
	
	//must
	if ( typeof outcnt !== "undefined" && must !== "-"){
		if ($("#" + must).val().length === 0){
			useralert("keine SOLL Zahl !", 2);
			retval &= false;
		}
	}
	
	//acutal
	if ( typeof actual !== "undefined" && actual !== "-"){
		if ($("#" + actual).val().length === 0){
			useralert("keine IST Anzahl !", 2);
			retval &= false;
		}
	}

	return retval;
}

function returnValue( valtype, id ){
	
	//alert(valtype + "-" + id);
	
	if (valtype === key_number){
		if ($("#" + id).val().length > 0) return $("#" + id).val();
		else return "NULL";
	}
	
	if (valtype === key_string){
		if ($("#" + id).val().length > 0) return "\"" + $("#" + id).val() + "\"";
		else return "''";
	}
	
}


function returnDate(){
	
	var dateObj = new Date();
	var month = dateObj.getUTCMonth() + 1; //months from 1-12
	var day = dateObj.getUTCDate();
	var year = dateObj.getUTCFullYear();

	return "\"" + year + "-" + month + "-" + day + "\"";
	
}

function resetInventur(){
	
	$("#id_f1_serialnr").val("");
	$("#id_prodname").val("");
	$("#id_curcnt").val("");
	$("#id_actcnt").val("");
	$("#id_supplier").val("");
	$("#id_others").val("");
	
}