
function loadCurrentCntIn(){

	//alert("1");
	//alert($("#id_f1_serialnr").val());
	if ($("#id_f1_serialnr").val().length == 0){
		useralert("Keine ID !", 2);
		return;
	}
	else{
		var j_search = {"action":"SEARCH_CNT", "COL_LIST":"Name,Quantity", "VALUES":"", "CONDI":""};
		j_search.CONDI = "Code=" + returnValue(key_string, "id_f1_serialnr");
		//alert(j_search.CONDI);
		asynAjaxCall(j_search, FIND_CUR_CNT);
	
			
	}
}


function saveExStock(){
	
	if ($("#id_f1_serialnr").val().length == 0 || 
	    $("#id_actwheretoplace").val().length == 0 || 
		$("#id_newwheretoplace").val().length == 0 ||
		$("#id_currentcnt").val().length == 0 || 
		$("#id_currentcnt").val() < 0){
		useralert("Prüf Produkt ID, Lagerplatz oder Anzahl Info !", 2);
		return;
	}
	
	
	//alert("bbb");
	var j_update = {"action":"STOCK_EX", "COL_LIST":"", "VALUES":"", "CONDI":""};
	
	j_update.VALUES += $("#id_actwheretoplace").val() + ",";
	j_update.VALUES += $("#id_newwheretoplace").val() + ",";
	j_update.VALUES += $("#id_currentcnt").val() + "," ;
	j_update.VALUES += $("#id_prodname").val();
	
	j_update.CONDI = $("#id_f1_serialnr").val();
	//alert(j_update.VALUES);
	asynAjaxCall(j_update, STOCK_EX);
	
}
	
function resetExStock(){
	
	$("#id_f1_serialnr").val("");
	$("#id_prodname").val("");
	$("#id_currentcnt").val("");
	$("#id_actwheretoplace").val("");
	$("#id_newwheretoplace").val("");
}

