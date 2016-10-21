
var LOAD_GRP = 1;
var SAVE_GRP = 2;
var REMOVE_GRP = 3;

function loadexistinggroups(){
	
	$("#id_existinggroups").empty();
	//alert("dfddddd");
	var j_search = {"action":"SEARCH", "COL_LIST":"Name", "VALUES":"", "CONDI":""};
	asynAjaxCallGroup(j_search, LOAD_GRP);
}


function saveNewGroup(){
	
	//1. create new product if it is new
	
	if ($("#id_new_group").val().length == 0){
			useralert("Kategorie Name ist leer !", 2);
			return;
	} 
	var j_update = {"action":"INSERT_GROUP", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_update.COL_LIST = "`Code`, `Name`, `Info`";
		
	j_update.VALUES = "(%,";   													//% is placeholder for max id, which has to be gotten in php						
	j_update.VALUES += returnValue(key_string, "id_new_group") + ",";      		//name	
	j_update.VALUES += returnValue(key_string, "id_newgrp_info") + ")";    		//info
	//alert(j_update.VALUES);
	asynAjaxCallGroup(j_update, SAVE_GRP);

}

function deleteGroup(){
	
	var selgrp =$("#id_existinggroups option:selected").text();
	if (selgrp == '-' || selgrp == 'default') {
		alert("Diese Gruppe ist nicht löschbar !");
		return;
	}
	
	var j_search = {"action":"DELETE_GROUP", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_search.CONDI = selgrp;
	asynAjaxCallGroup(j_search, REMOVE_GRP);
	
}


function asynAjaxCallGroup(jsondata, act_id){
	
	$.ajax({
			type: "POST",
			url: "./exe/group.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				
				//alert(message);
				
				if (act_id == LOAD_GRP){
					if (alert == 'false') {
						useralert("keine Gruppe Namen !", 2);
						return;
					}
					else loadgroupnames(message);
					
				}
				if (act_id == SAVE_GRP){
					if (message.indexOf("O.K.")>-1){ 
						loadexistinggroups();
						useralert(message, 1);
					}
					else useralert(message, 2);
				}
				if (act_id == REMOVE_GRP){
					$("#id_existinggroups").empty();
					loadexistinggroups();
				}
				
			});
	
	
}


function loadgroupnames(json_data){
	//alert(json_data);
	var data = eval('('+json_data+')');
	
	var tmphtml="";
	for (var i=0; i<data.length; i++) tmphtml +="<option>" + data[i][0] + "</option>"
	
	$("#id_existinggroups").append(tmphtml);
	
}

