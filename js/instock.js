var global_stockin_id ="";
var global_stockin_cnt = 0;

function loadCurrentCntIn(){

	
	//alert($("#id_f1_serialnr").val());
	if ($("#id_f1_serialnr").val().length == 0){
		useralert("Keine ID !", 2);
		return;
	}
	else{
		//auto increase in cnt
		if (global_stockin_id.length == 0){
			global_stockin_id = $("#id_f1_serialnr").val();
			++global_stockin_cnt;
			
			var j_search = {"action":"SEARCH_CNT", "COL_LIST":"Name,Quantity", "VALUES":"", "CONDI":""};
			j_search.CONDI = "Code=" + returnValue(key_string, "id_f1_serialnr");
			//alert(j_search.CONDI);
			asynAjaxCall(j_search, FIND_CUR_CNT);
	
			
		}
		else if (global_stockin_id != $("#id_f1_serialnr").val()){
			useralert("anderer Produkt (ID) !", 2);
			return;
		}
		else ++global_stockin_cnt;
		$("#id_incnt").val(global_stockin_cnt);
		
		
		$("#id_f1_serialnr").focus();
		$("#id_f1_serialnr").val("");
		
	}
	
	
	
}

function changeBarcode(){
	
	//alert("1");
	
}
	
function resetInStock(){
	
	$("#id_f1_serialnr").val("");
	$("#id_prodname").val("");
	$("#id_currentcnt").val("");
	$("#id_supplier").val("");
	$("#id_incnt").val("");
	$("#id_others").val("");
	$("#id_wheretoplace").val("");
	global_stockin_cnt = 0 ;
	global_stockin_id="";
	
	
	
}

function simpleresetInStock(){
	
	$("#id_prodname").val("");
	$("#id_currentcnt").val("");
	$("#id_supplier").val("");
	$("#id_incnt").val("");
	$("#id_others").val("");
	$("#id_wheretoplace").val("");
	
	
}

function checkInID(){
	if (global_stockin_id.length ==0 ){
		useralert("ungültige Produkt ID !");
		return false;
	}
	return true;
}

