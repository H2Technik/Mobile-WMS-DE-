var global_stockout_id ="";
var global_stockout_cnt = 0;

function loadCurrentCntOut(){

	
	//alert($("#id_f1_serialnr").val());
	if ($("#id_f1_serialnr").val().length == 0){
		useralert("Keine ID !", 2);
		return;
	}
	else{
		//auto increase in cnt
		if (global_stockout_id.length == 0){
			global_stockout_id = $("#id_f1_serialnr").val();
			++global_stockout_cnt;
			
			var j_search = {"action":"SEARCH_CNT", "COL_LIST":"Name,Quantity", "VALUES":"", "CONDI":""};
			j_search.CONDI = "Code=" + returnValue(key_string, "id_f1_serialnr");
			//alert(j_search.CONDI);
			asynAjaxCall(j_search, FIND_CUR_CNT);
		}
		else if (global_stockout_id != $("#id_f1_serialnr").val()){
			useralert("anderer Produkt (ID) !", 2);
			return;
		}
		else ++global_stockout_cnt;
		$("#id_outcnt").val(global_stockout_cnt);
		
		$("#id_f1_serialnr").focus();
		$("#id_f1_serialnr").val("");
	}
}

function autoIncreaseOutCnt(){
	
	if ($("#id_f1_serialnr").val().length == 0){
		useralert("Keine ID !", 2);
		return;
	}
}

function resetOutStock(){
	
	$("#id_f1_serialnr").val("");
	$("#id_prodname").val("");
	$("#id_currentcnt").val("");
	$("#id_outcnt").val("");
	$("#id_supplier").val("");
	$("#id_others").val("");
	$("#id_wheretoplace").val("");
	global_stockout_cnt = 0;
	global_stockout_id="";
	
}


function checkOutID(){
	if (global_stockout_id.length ==0 ){
		useralert("ungültige Produkt ID !");
		return false;
	}
	return true;
}







/*var key_number = "number";
var key_string = "string";

var UPDATE_OUTSTOCK_RET = 20;
var SAVE_OUTSTOCK = 21;
//var _NAME_RET = 2;


function DoSaveOutStock(){
	
	var j_update = {"action":"INSERT_STOCKOUT", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Parts_SerialNr`, `Count`, `ActualPrice`, `Date`, `ContractNr`, `OutTicketID`, `ChargePersonID`, `Rev1`, `Rev2`";
	
	//alert(j_update.COL_LIST);
	
	j_update.VALUES = "(" + returnValue(key_string, "id_f3_serialnr") + ",";   //serial nr
	j_update.VALUES += returnValue(key_number, "id_outcount") + ",";      	//count
	j_update.VALUES += "0,"; 												//price
	
	j_update.VALUES += "'" + $("#id_outdate").val() + "',";               	//date
	j_update.VALUES += "'" + $("#id_outcontract").val() + "',";   			//contractNr
	j_update.VALUES += "0,";    											//outTicketNr
	j_update.VALUES += returnValue(key_string, "id_outchargeperson") + ", "; //ChargePerson
	j_update.VALUES += returnValue(key_string, "id_f3_supplier") + ", "; //customer
	j_update.VALUES += "'" + $( "#id_selpurpose option:selected" ).text() + "')";//purpose

	
	j_update.CONDI="SerialNr=" + returnValue(key_string, "id_f3_serialnr");

	asynAjaxCallOutStock(j_update, SAVE_OUTSTOCK);
}


function saveOutStock(){
	if (!checkSerialNameOut("id_f3_serialnr", "-", "-", "-", "id_outcount", "id_outchargeperson")) return;
	DoSaveOutStock();
}

function resetOutStock(){
	
	$("#id_f3_serialnr").val("");
	$("#id_f3_name").val("");
	$("#id_outcount").val("");
	$("#id_outchargeperson").val("");
	//$("#id_incareperson").val("");
	//$("#id_indate").val("");
	$("#id_f3_currentstock").val("");
	$("#id_outcontract").val("");
	$("#id_f3_supplier").val("");
	
}



function checkSerialNameOut(serial, name, supplier, incnt, outcnt, charge){
	
	var retval = true;
	
	//alert(serial + "/" + name + "/" + supplier + "/" + incnt + "/" + outcnt);
	
	//Serial
	if ($("#" + serial ).val().length === 0 ){
			alert("请检查件号或者名称。");
			retval &= false;
	}

	//name
	if ( typeof name !== "undefined" && name !== "-"){
		if ($("#" + name).val().length === 0){
			alert("请检查配件名称。");
			retval &= false;
		}
	}
	
	//alert("supplier:" + supplier);
	//supplier
	if ( typeof supplier !== "undefined" && supplier !== "-"){
		if ($("#" + supplier).val().length === 0){
			alert("请检查供货商名称。");
			retval &= false;
		}
	}
	
	//alert("in:" +incnt);
	//inStock
	if ( typeof incnt !== "undefined" && incnt !== "-"){
		if ($("#" + incnt).val().length === 0){
			alert("请检查入库数量。");
			retval &= false;
		}
	}
	
	//alert("out:" +outcnt);
	//outStock
	if ( typeof outcnt !== "undefined" && outcnt !== "-"){
		if ($("#" + outcnt).val().length === 0){
			alert("请检查出库数量。");
			retval &= false;
		}
	}
	
	//charge, care person
	if ($("#" + charge ).val().length === 0 ){
			alert("请输入负责人的信息。");
			retval &= false;
		}
	
	
	return retval;
}




function asynAjaxCallOutStock(jsondata, act_id){
	
	$.ajax({
			type: "POST",
			url: "./exe/maindata.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				//alert(message);
				
				//load the returned part information
				if (act_id == SAVE_OUTSTOCK ){
					var tmps = message.split(">");
					alert(tmps[0]);
					if (tmps.length == 2) $("#id_f3_currentstock").val(tmps[1]);
				}
				
			});
	
	
}

function returnValueOutStock( valtype, id ){
	
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
*/
