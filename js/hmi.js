
var UPDATE_RET = 0;
var SEARCH_ID_RET = 1;
var SEARCH_NAME_RET = 2;
var DELETE_PROD = 3;
var CHANGE_PRDINFO = 4;
var FIND_CUR_CNT = 12;
var STOCK_IN = 20;
var STOCK_OUT = 21;
var STOCK_EX = 22;


////////////////////////////Product////////////////////////
function savePartInfo(){
	
	//alert("0");
	
	if (!checkSerialName("id_f1_serialnr", "id_f1_name", "id_f1_supplier")) return;
	
	
	
	var j_update = {"action":"INSERT", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Code`, `Name`, `InPrice`, `OutPrice`, `Supplier`, `Info`, `Max`, `Min`, `GroupID`,`CreatedAt`,`Remark`";
	//j_update.COL_LIST = "`Code`, `Name`";
	
	//alert(j_update.COL_LIST);
	
	j_update.VALUES = "(" + returnValue(key_string, "id_f1_serialnr") + ",";   //serial nr
	//j_update.VALUES += returnValue(key_string, "id_f1_name") + ")";      		//name
	j_update.VALUES += returnValue(key_string, "id_f1_name") + ",";      		//name
	
	//alert("1");
	j_update.VALUES += returnRealPrice("id_inprice") + ",";      		//inprice
	j_update.VALUES += returnRealPrice("id_outprice") + ",";	 		//outprice
	
	j_update.VALUES += returnValue(key_string, "id_f1_supplier") + ",";  		//supplier 
	j_update.VALUES += returnValue(key_string, "id_others") + ",";    		//info
	j_update.VALUES += returnValue(key_number, "id_uppderlimit") + ",";   		//max
	j_update.VALUES += returnValue(key_number, "id_lowerlimit") + ",";    		//min
	j_update.VALUES += "'"+$( "#id_existinggroups option:selected" ).text() + "',"; //group id
	j_update.VALUES += returnValue(key_string, "id_createdate") + ",";    		//createdat
	j_update.VALUES += returnValue(key_string, "id_remark") + ")";    		//remark
		
	j_update.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr");
	//alert(j_update.VALUES);
	asynAjaxCall(j_update, UPDATE_RET);
	
}

function UpdatePartInfo(){
	
	if (!checkSerialName("id_f1_serialnr", "id_f1_name", "id_f1_supplier")) return;
	
	var j_update = {"action":"UPDATE", "SET_VAL_LIST":"", "CONDI":""};
	j_update.SET_VAL_LIST = "Name=" + returnValue(key_string, "id_f1_name") + ",";
	
	
	j_update.SET_VAL_LIST += "InPrice=" + returnRealPrice("id_inprice") + ",";      		//inprice
	j_update.SET_VAL_LIST += "OutPrice=" + returnRealPrice("id_outprice") + ",";	 		//outprice
	
	j_update.SET_VAL_LIST += "Supplier=" + returnValue(key_string, "id_f1_supplier") + ",";
	j_update.SET_VAL_LIST += "Info=" + returnValue(key_string, "id_others") + ",";
	j_update.SET_VAL_LIST += "Max=" + returnValue(key_number, "id_uppderlimit") + ",";
	j_update.SET_VAL_LIST += "Min=" + returnValue(key_number, "id_lowerlimit") + ",";
	j_update.SET_VAL_LIST += "GroupID='"+$("#id_existinggroups option:selected" ).text() + "',";
	j_update.SET_VAL_LIST += "CreatedAt=" + returnValue(key_string, "id_createddate") + ",";
	j_update.SET_VAL_LIST += "Remark=" + returnValue(key_string, "id_remark");
		
	j_update.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr");
	//alert(j_update.SET_VAL_LIST);
	asynAjaxCall(j_update, CHANGE_PRDINFO);
}

function deletePartInfo(){
	
	if (!checkSerialName("id_f1_serialnr","id_f1_name")) return;
	var j_delete = {"action":"DELETE", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_delete.CONDI="Code=" + returnValue(key_string, "id_f1_serialnr") + 
				   " AND " + "Name=" + returnValue(key_string, "id_f1_name");
				   
	//alert(j_delete.CONDI);
	asynAjaxCall(j_delete, DELETE_PROD);
	
	
}

function resetPartInfo(){
	
	//alert("abcdefg");
	$("#id_f1_serialnr").val("");
	$("#id_f1_name").val("");
	$("#id_f1_supplier").val("");
	$("#id_inprice").val("€0,00");
	$("#id_outprice").val("€0,00");
	$("#id_uppderlimit").val("");
	$("#id_lowerlimit").val("");
	$("#id_uppderlimit").val("");
	$("#id_remark").val("");
	$("#id_others").val("");
	
}

///////////////////////////stock in///////////////////////////////////////////
function saveInStock(){
	if (!checkSerialName("-", "id_prodname", "-", "id_incnt")) return;
	if ( !checkInID() ) return;
	
	var j_update = {"action":"INSERT_STOCKIN", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Code`, `Name`, `Quantity`, `Supplier`, `InDate`, `Info`, `Stockplace`";
	
	
	j_update.VALUES = "('" + global_stockin_id + "',";   //serial nr
	j_update.VALUES += returnValue(key_string, "id_prodname") + ",";      		//name
	j_update.VALUES += returnValue(key_number, "id_incnt") + ",";      		//quantity in
	j_update.VALUES += returnValue(key_string, "id_supplier") + ",";  		//supplier 
	j_update.VALUES += "'" + getTimestempel() + "',";	 		//in date
	j_update.VALUES += returnValue(key_string, "id_others") + ",";	 		//info
	j_update.VALUES += returnValue(key_string, "id_wheretoplace") + ")";    		//stockplace
	
		
	j_update.CONDI="Code='" + global_stockin_id +"'";
	//alert(j_update.CONDI);
	//alert(j_update.VALUES);
	asynAjaxCall(j_update, STOCK_IN);
}


/////////////////////////////////stock out////////////////////////////////////
function saveOutStock(){


	if (!checkSerialName("-", "id_prodname", "-", "-" , "id_outcnt")) return;
	if ( !checkOutID() ) return;
	
	var curcnt = parseInt($("#id_currentcnt").val());
	var outcnt = parseInt($("#id_outcnt").val());
	if (outcnt > curcnt ){
		useralert("max. Verfügbarkeit: " + curcnt , 2);
		return;
	}
	if (outcnt == curcnt ) useralert("Ausverkauft !" , 2);
	
	
	var j_update = {"action":"INSERT_STOCKOUT", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Code`, `Name`, `Quantity`, `Customer`, `OutDate`, `Info`, `Stockplace`";
	
	
	j_update.VALUES = "('" + global_stockout_id + "',";   //serial nr
	//j_update.VALUES += returnValue(key_string, "id_f1_name") + ")";      		//name
	j_update.VALUES += returnValue(key_string, "id_prodname") + ",";      		//name
	j_update.VALUES += returnValue(key_number, "id_outcnt") + ",";      		//quantity in
	j_update.VALUES += returnValue(key_string, "id_supplier") + ",";  		//supplier 
	j_update.VALUES += "'" + getTimestempel() + "',";	 		//out date
	j_update.VALUES += returnValue(key_string, "id_others") + ",";          //remark
	j_update.VALUES += returnValue(key_string, "id_wheretoplace") + ")";    		//stockplace
	
		
	j_update.CONDI="Code='" + global_stockout_id + "'";
	//alert(j_update.CONDI);
	//alert(j_update.VALUES);
	asynAjaxCall(j_update, STOCK_OUT);
	
}

function checkSerialName(serial, name, supplier, incnt, outcnt){
	
	var retval = true;
	
	//Serial, Name
	if ( serial != "-" && ($("#" + serial ).val().length === 0 || 
	       $("#" + name ).val().length === 0 )){
		useralert("ID und Bezeichnung sind MUST Felder !", 2);
		retval &= false;
	}

	//alert("supplier:" + supplier);
	//supplier
	if ( typeof supplier !== "undefined" && supplier !== "-"){
		if ($("#" + supplier).val().length === 0){
			useralert("Lieferant fehlt !", 2);
			retval &= false;
		}
	}
	
	//alert("in:" +incnt);
	//inStock
	if ( typeof incnt !== "undefined" && incnt !== "-"){
		if ($("#" + incnt).val().length === 0){
			useralert("keine Eingang Anzahl !", 2);
			retval &= false;
		}
	}
	
	//alert("out:" +outcnt);
	//outStock
	if ( typeof outcnt !== "undefined" && outcnt !== "-"){
		if ($("#" + outcnt).val().length === 0){
			useralert("keine Ausgang Anzahl !", 2);
			retval &= false;
		}
	}
	
	return retval;
}

function asynAjaxCall(jsondata, act_id){
	
	$.ajax({
			type: "POST",
			url: "./exe/maindata.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				
				//useralert(message, 1);
				//alert(message);
				//load the returned part information
				if (act_id == SEARCH_ID_RET || act_id == SEARCH_NAME_RET){
					loadPartInfoByIdandName(message, act_id);
				}
				
				//delete information
				if (act_id == DELETE_PROD){
					resetPartInfo();
					useralert(message, 1);
					
					//reset timer
					resetTimer();
				}
				
				//change product information
				if (act_id == UPDATE_RET || act_id == CHANGE_PRDINFO){
					if (message.indexOf("O.K") > -1 ) useralert(message, 1);
					else useralert(message, 2);
					//reset timer
					resetTimer();
					
				}
				if(act_id == FIND_CUR_CNT){
					if (message == 'false'){
						$("#id_stockinnew").show();
						simpleresetInStock();
					}
					else{
						var data = eval('('+message+')');
						$("#id_prodname").val(data[0]);
						$("#id_currentcnt").val(data[1]);
						
						$("#id_maxcnt").val(data[2]);
						$("#id_mincnt").val(data[3]);
						
					}
					useralert(message, 1);
				}
				
				//stock in
				if (act_id == STOCK_IN){
					
					if (message.indexOf("O.K.") > -1) {
						var a1 = parseInt($("#id_currentcnt").val());
						var a2 = parseInt($("#id_incnt").val());
						
						if (! isNaN(a1)) {
							$("#id_currentcnt").val((a1+a2));
							useralert(message, 1);
							
							//check max limit
							checkMaxLimit();
							
							//reset timer
							resetTimer();
						}
						else {
							$("#id_currentcnt").val(a2);
							useralert("falsce Zahl Stock In !", 2);
						}
						$("#id_incnt").val("")
						
					}
					else useralert(message, 2);
				}
				
				//stock out
				if (act_id == STOCK_OUT){
					if (message.indexOf("O.K.") > -1) {
						var a1 = parseInt($("#id_currentcnt").val());
						var a2 = parseInt($("#id_outcnt").val());
						
						if (! isNaN(a1)) {
							$("#id_currentcnt").val((a1-a2));
							useralert(message, 1);
							
							//check min limit
							checkMinLimit();
							
							//reset timer
							resetTimer();
							
						}
						else {
							useralert("falsce Zahl Stock Out !", 2);
						}
						$("#id_outcnt").val("");
					}
					else useralert(message, 2);
				}
				
				//exchange stock
				if (act_id == STOCK_EX){
					if (message.indexOf("O.K.") > -1) useralert(message, 1);
					else useralert(message, 2);
				}
				
			});
}

function searchPartInfoByName(){
	
	if ($("#id_f1_name").val().length == 0){
		useralert("keine Bezeichnung ！", 2);
		return;
	}
	
	var j_search = {"action":"SEARCH", "COL_LIST":"*", "VALUES":"", "CONDI":""};
	j_search.CONDI = "Name=" + returnValue(key_string, "id_f1_name");
	asynAjaxCall(j_search, SEARCH_NAME_RET);
	
}

function searchPartInfoById(){
	
	if ($("#id_f1_serialnr").val().length == 0){
		useralert("keine ID ！", 2);
		return;
	}
	
	var j_search = {"action":"SEARCH", "COL_LIST":"*", "VALUES":"", "CONDI":""};
	j_search.CONDI = "Code=" + returnValue(key_string, "id_f1_serialnr");
	asynAjaxCall(j_search, SEARCH_ID_RET);
}

function loadPartInfoByIdandName( json_data, act_id ){
	var data = eval('('+json_data+')');
	
	if (act_id == SEARCH_ID_RET) $("#id_f1_name").val(data[1]);
	if (act_id == SEARCH_NAME_RET) $("#id_f1_serialnr").val(data[0]);
	
	$("#id_existinggroups option").filter(function() {	
		return $(this).text() == data[8]; 
	}).prop('selected', true);
	
	$("#id_inprice").val("€" + data[2]);
	$("#id_outprice").val("€" + data[3]);
	$("#id_uppderlimit").val(data[6]);
	$("#id_lowerlimit").val(data[7]);
	$("#id_f1_supplier").val(data[4]);
	
	$("#id_others").val(data[5]); //info
	$("#id_createddate").val(data[9]); //create at
	$("#id_remark").val(data[10]);
	
}


function checkMaxLimit(){
	
	var max = $("#id_maxcnt").val();
	var cur = $("#id_currentcnt").val();
	//alert(max + "-" + cur + "-" + (cur>=max));
	if ( cur >= max) {
		$('#id_currentcnt').css('background-color','#f00');
		useralert("Max. Bestand Alarm: " + max, 2);
	}
	else $('#id_currentcnt').css('background-color','#c0c0c0');
	
}


function checkMinLimit(){
	
	var min = $("#id_mincnt").val();
	var cur = $("#id_currentcnt").val();
	
	//alert(min + "-" + cur + "=" + (cur - min));
	
	if ((cur - min) < 0){
		$('#id_currentcnt').css('background-color','#f00');
		useralert("Min. Bestand Alarm: " + min, 2);
	}
	else $('#id_currentcnt').css('background-color','#c0c0c0');
	
}
