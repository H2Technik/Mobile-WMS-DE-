
var LOAD_PLACE_1 = 11;
var LOAD_PLACE_2 = 12;
var LOAD_PLACE_3 = 13;


var SAVE_PLACE = 2;
var REMOVE_PLACE = 3;
var VIEW_PLACE = 4;
var PLACE_OCCUPY = 5;

var global_condi="";
var pro_place={};



//-------------------------TIMER-------------------------
var refresh_place_occupying = 10000;
var timer = null;
var cnt = 0;

function monitoring(){
	
	
	//online or offline
	if ( $('#id_isonline').is(":checked") ){
		startInterval();
		
		//alert("aaaaaaa");
		//global timer stop for auto logout
		stoptimer();
	}
	else{
		alert("stop");
		stopInterval();
		
		//global timer start for auto logout
		startTimer();
	}
	
	
}


function RefreshPlaceOccupying(){
	
    $("#id_refreshstatus").text(cnt++);
	overviewStockplace();

}

function reset()
{
	clearInterval(timer);
    
}
function startInterval()
{
	timer= setInterval("RefreshPlaceOccupying()", refresh_place_occupying);
}
function stopInterval()
{
    clearInterval(timer);
}
//--------------------------------------------------------------



function initactualplace(what){
	
	//hide sth
	//$(".actchange").hide();
	//$("#id_isonline").hide();
	//$("#id_changeact").val("Auswahl");
	
	//1. load location, area, rows information
	//alert(what);
	var j_search = {"action":"SEARCH_ACT", "COL_LIST":"", "VALUES":"", "CONDI":""};
	var act_id = 0;
	
	//get location
	if (what == 1) {
			j_search.COL_LIST = "distinct(Location)";
			act_id = 1;
			act_id = LOAD_PLACE_1;
	}
	
	//get area
	if (what == 2) {
		j_search.COL_LIST = "distinct(Area)";
		j_search.CONDI = "Location='" + $("#id_existingloc option:selected").text() + "'";
		act_id = LOAD_PLACE_2;
	}
	//get RowId
	if (what == 3) {
		j_search.COL_LIST = "RowId";
		j_search.CONDI = "Area='" + $("#id_existingarea option:selected").text() + "' AND " + 	
						"Location ='" + $("#id_existingloc option:selected").text() + "'";
		act_id = LOAD_PLACE_3;
	}
	//alert(j_search.CONDI);
	asynAjaxCallStockPlace(j_search, act_id);
		
	
	
	//2. refresh buffer for the occupying information in this location, area, rows
	//searchPlaceOccupy();
}

function searchPlaceOccupy(){
	
	//1. refresh buffer for the occupying information in this location, area, rows
	var j_search = {"action":"GET_OCCUPY", "COL_LIST":"Code, Name, InDate, Quantity, Stockplace", "VALUES":"", "CONDI":""};
	j_search.CONDI = "Stockplace LIKE '" + $("#id_existingloc option:selected").text() +  
										   $("#id_existingarea option:selected").text() + "%'" ;
	asynAjaxCallStockPlace(j_search, PLACE_OCCUPY);
	
}

function newStockplace(){
	$("#id_visualarea").hide();
	$("#id_isonline").prop("checked",false);
	
	if( $("#id_confignewplace").is(":visible")) $("#id_confignewplace").hide()
	else $("#id_confignewplace").show();
	
}

function saveStockplace(){
	
    var msg = checkempty(1);
	//alert(msg);
	if (msg.indexOf("O.K.") == -1){
		useralert(msg, 2);
		return;
	}
	var j_insert = {"action":"SAVE_PLACE", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_insert.COL_LIST = "Code, Location, Area, RowId, ColumnCnt, LevelCnt, Position, Info";
	
	j_insert.VALUES = "%,";
	
	if ($("#id_newloc").val().length > 0) j_insert.VALUES += returnValue(key_string, "id_newloc") +",";
	else j_insert.VALUES +="'" + $("#id_existingloc option:selected").text() +"',";
	
	
	if ($("#id_newarea").val().length > 0) j_insert.VALUES +=returnValue(key_string, "id_newarea") +",";
	else j_insert.VALUES += "'" + $("#id_existingarea option:selected").text() +"',";
	
	if ($("#id_newcol").val().length > 0) j_insert.VALUES +=returnValue(key_string, "id_newcol") +",";
	else j_insert.VALUES += "'" + $("#id_existingcol option:selected").text() +"',";
	
	j_insert.VALUES +=returnValue(key_number, "id_colcnt") + ",";
	j_insert.VALUES +=returnValue(key_number, "id_levelcnt") + ",";
	j_insert.VALUES +=returnValue(key_number, "id_poscnt") + ",";
	j_insert.VALUES +=returnValue(key_string, "id_info") + ")";
	
	//alert(j_insert.VALUES);
	asynAjaxCallStockPlace(j_insert, SAVE_PLACE);
	
}

function clearStockplace(){
	
	$("#id_newloc").val("");
	$("#id_newarea").val("");
	$("#id_newcol").val("");
	$("#id_colcnt").val("");
	$("#id_levelcnt").val("");
	$("#id_poscnt").val("");
	$("#id_info").val("");
	
}

function deleteStockplace(id){
	
	global_condi = "";
	//delete by Location+Area+RowId
	if (isNaN(id)){
		str = "Ort, Bereich und Reihe (" + id + ")" ;
		global_condi = "Code='" + id + "'";
	}
	//delete by single location, area or rowid
	else{
		//alert(id);
		var str = "";
		switch(id){
			case 1:	//1=location
				str = "Ort (" + $("#id_existingloc option:selected").text() + ")" ;
				global_condi = "Location='" + $("#id_existingloc option:selected").text() +"'";
				break; 
			case 2: //2=area
				str = " Bereich (" + $("#id_existingarea option:selected").text() + ")" ;
				global_condi = "Location='" + $("#id_existingloc option:selected").text() +"' AND " + 
								"Area='" +$("#id_existingarea option:selected").text()+"'";
				break; 
			case 3:	//3=row
				str = " Reihe (" + $("#id_existingcol option:selected").text() + ")" ;
				global_condi = "Location='" + $("#id_existingloc option:selected").text() +"' AND " + 
								"Area='" +$("#id_existingarea option:selected").text()+"' AND " + 
								"RowId='"+$("#id_existingcol option:selected").text()+"'";
				break; 
		}
	}
	
	if (global_condi.length > 0) openmydialog(3, "<P>Wollen Sie " + str + " löschen ?</P>", "", callbackDialog);
	
}

function callbackDialog(what){
	
	var j_delete = {"action":"DELE_PLACE", "COL_LIST":"", "VALUES":"", "CONDI":""};
	j_delete.CONDI= global_condi;
	alert(global_condi);
	asynAjaxCallStockPlace(j_delete, REMOVE_PLACE);
}


function asynAjaxCallStockPlace(jsondata, act_id){
	$.ajax({
			type: "POST",
			url: "./exe/stockplace.php",
			data: JSON.stringify(jsondata),
			contentType: "application/json",
			})
			.done(function( message ) {
				
				//alert(message);
				
				if (act_id >= LOAD_PLACE_1){
					if (alert == 'false') {
						useralert("Fehler beim Laden aktuellen Orten Information !", 2);
						return;
					}
					else loadactualplace(message, act_id - 10);
					
				}
				if (act_id == SAVE_PLACE){
					if (message.indexOf("O.K.")>-1){ 
						useralert(message, 1);
					}
					else useralert(message, 2);
					clearStockplace();
				}
				if (act_id == REMOVE_PLACE){
					$("#id_existinggroups").empty();
					useralert(message);
					initactualplace(1);
					
				}
				
				if (act_id == VIEW_PLACE){
					//alert(message);
					$("#id_overviewstockplace").empty();
					$("#id_visualarea").show();
					createtable(message);
				}
				
				if (act_id == PLACE_OCCUPY){
					
					pro_place = eval('('+message+')');
					//alert("----" + message + "----");
					//alert("aaaaaaaaaaaaaaaaaaaaaaaaaaa");
					//alert(pro_place.length);
				}
			});
	
	
}


function checkempty(what){
	
	if ($("#id_existingloc option:selected").text().length == 0 &&
	    $("#id_newloc").val().length == 0) return "keine Orte Information !";
	
	if ($("#id_existingarea option:selected").text().length == 0 &&
	    $("#id_newarea").val().length == 0) return "keine Bereiche Information !";
	
	if ($("#id_existingcol option:selected").text().length == 0 &&
	    $("#id_newcol").val().length == 0) return "keine Reihen Information !";
	
	if (what == 2) return "O.K.";
	
	if ($("#id_colcnt").val().length == 0 ) return "keine Spalten Anzahl !";
	if ($("#id_levelcnt").val().length == 0 ) return "keine Ebene Anzahl !";
	if ($("#id_poscnt").val().length == 0 ) return "keine Position Anzahl !";
	
	return "O.K.";
	
	
}

//what = 1 location
//what = 2 area
//what = 3 rowid
function loadactualplace(json_data, what){
	
	
	var data = eval('('+json_data+')');
	
	var tmplocs="";
	var tmpareas="";
	var tmpcols="";
	
	for (var i=0; i<data.length; i++){
		if (what == 1) tmplocs +="<option>" + data[i][0] + "</option>"
		if (what == 2) tmpareas +="<option>" + data[i][0] + "</option>"
		if (what == 3) tmpcols +="<option>" + data[i][0] + "</option>"
	}
	if (what == 1){
		$("#id_existingloc").empty();
		$("#id_existingloc").append(tmplocs);
		$("#id_existingarea").empty();
		$("#id_existingcol").empty();
		initactualplace(2);
	}
	
	if (what == 2){
		$("#id_existingarea").empty();
		$("#id_existingarea").append(tmpareas);
		$("#id_existingcol").empty();
		initactualplace(3);
	}
	
	if (what == 3){
		$("#id_existingcol").empty();
		$("#id_existingcol").append(tmpcols);
	}
	
	$("#id_overviewstockplace").empty();
}

//------------------------------------------------------------------
//------------------------------------------------------------------
function overviewStockplace(){
	
	$("#id_confignewplace").hide()
	
	 var msg = checkempty(2);
	//alert(msg);
	if (msg.indexOf("O.K.") == -1){
		useralert(msg, 2);
		return;
	}

	//1.get refreshed occupy information
	searchPlaceOccupy();
	
	//2. visual place
	var j_search = {"action":"OVER_VIEW", "COL_LIST":"Code, ColumnCnt, LevelCnt, Position", "VALUES":"", "CONDI":""};
	j_search.CONDI = "Location='" + $("#id_existingloc option:selected").text() + "' AND " + 
					 "Area='" + $("#id_existingarea option:selected").text() + "'" ;
	
	//alert(j_search.CONDI);
	asynAjaxCallStockPlace(j_search, VIEW_PLACE);
	
}

function createtable(json_data){
 
	var data = eval('('+json_data+')');
	 //1. get row count
	 var rowcount = data.length; 
	 if (rowcount == 0){
		  useralert("keine Lagerplatz Information !", 2);
		  return;
	  }
		  
	  //2. show row and column number
	  //alert("Row:" + rowcount + "  Column:" + colcount + "   Ebene:" + ebenecount);

	  //3. create table
	  //var colStart = num_cols / num_super;
	  var langbody="";
	  var tbody="";
	  var placeid="";
		  
	  //3.1 loop rowcount
	  for(var o=0; o<rowcount; o++){
			var rowid = data[o][0];     //Code
			var colcount = data[o][1];  //ColumnCnt
			var ebenecount= data[o][2]; //LevelCnt
		
			 var theader = "<table border='1' class='overviewarea' >\n";
			 
			 //an empty row for row id and delete button
			 tbody +="<tr><td align='center' colspan='" + colcount+1 + "'>" + 
					  rowid + 
					  //"<input type='button' id='id_del_"+rowid + "' value='Löschen' onclick='deleteStockplace(\"" + rowid +"\")'></td><tr>";
					  "</td><tr>";
			 
			//3.2 loop colcount
			for(var u=0; u<colcount; u++){
			   if ( u%2 == 0) tbody +="<tr style='background-color: #c0c0c0'>";
					   else{
							isfree = true;
							tbody += "<tr style='background-color: #c0c0c0'>";
						}
					   
						//3.3 loop ebencount			;
						for( var j=0; j<ebenecount; j++){
							placeid =rowid + paddingzero(u, 3) + paddingzero(j, 3) +"000";
							
							tbody += "<td";
							//here the placeid 16 long must be searched in db for occupying!
							var st = findOccupySituation(placeid);
							if (st.length > 0 ){
									tbody +=" title='" + st + "'>X";
									//alert(tbody);
							}
							else{
								tbody +=">-";
							}
							tbody += "</td>"
						}
					  tbody += "</tr>\n";
					  placeid ="";
			}

			var tfooter = "</table>";
					
					
			langbody = langbody + (theader + tbody + tfooter);
			//alert(langbody);
			tbody ="";	

	}
	$("#id_overviewstockplace").append(langbody);
}

function findOccupySituation(placeid){
	
	//alert(placeid);
	for (var i=0 ; i<pro_place.length; i++){
		
		//alert(pro_place[i][4] + "----" + placeid);
		
		if (pro_place[i][4].indexOf(placeid) > -1){
			return pro_place[i][0] + "(" +pro_place[i][1] + ")";
		}
	}
	return "";
}





