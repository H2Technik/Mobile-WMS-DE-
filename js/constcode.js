var key_number = "number";
var key_string = "string";

var titlecode="<div class='submainbtn' onclick='switchpage(11)'>Home</div>" +
			  "<div class='submainbtn'><label id='id_logininfo'></label><br><label id='id_resttime'></label></div>"+
			  "<!--<div class='submainbtn'  onclick='switchpage(2)'>Einlager.</div>"+
			  "<div class='submainbtn'  onclick='switchpage(3)'>Auslager.</div>-->"+
			  "<div class='msgbox'><input type='text' style=' margin:5px; width:98%; ' id='id_msgbox' value=',' disabled></div>";
			  
 			  
var newstockplace="";
var Time_Delay = 5; //minutes
var timevar;
var cur_user="";

function loadtitlecode(pagename){
	
	titlecode = titlecode.replace(/,/g, pagename);
	$("#id_titlearea").empty();
	$("#id_titlearea").append(titlecode);
	
	//check if login valid
	doSecurityCheck();
	
	//load logged info
	loadinguserinfo();
	
	//window.clearTimeout(Timer1);
	startTimer();
}

function startTimer(){
	//alert("1");
	timevar = setTimeout(updateTimer, 60 * 1000 /*1 min*/)
}

function updateTimer(){
	//alert("3");
	Time_Delay -=1;
	if (Time_Delay == 0) window.location.href = "./in_logout.html";
	else{
		$("#id_resttime").text("Ausloggon in: " + Time_Delay + " Min");
		timevar = setTimeout(updateTimer, 60 * 1000 /*1 min*/)
	}
}

function stoptimer(){
	if (timevar) {
		clearTimeout(timevar);
		//alert("global timer stopped !");
	}
}

function resetTimer(){
	Time_Delay = 5;
	$("#id_resttime").text("Ausloggon in: " + Time_Delay + " Min");
}

function useralert(message, what){
	//green
	if (what == 1) {
		$("#id_msgbox").css({"color":'green'}); 
		$("#id_msgbox").val(message);
	}
	//red
	if (what == 2) {
		$("#id_msgbox").css({"color":'red'}); 
		$("#id_msgbox").val(message);
	}
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

function returnRealPrice(id){
	//alert("2");
	var  tmp = $("#" + id).val();
	var tmp1 = tmp.substring(1, tmp.length);
	var tmp2 = tmp1.replace(",", ".");
	return tmp2;
	
}

	
function paddingzero(val, howlong){
	var tmp = val.toString();
	while(tmp.length < howlong ) tmp ="0" + tmp;
	return tmp;
}

function getTimestempel(){
	
	var dateObj = new Date();
	var month = paddingzero(dateObj.getUTCMonth() + 1,2); //months from 1-12
	var day = paddingzero(dateObj.getUTCDate(),2);
	var year = paddingzero(dateObj.getUTCFullYear(),2);
	var hh = paddingzero(dateObj.getHours(),2);
	var mm = paddingzero(dateObj.getMinutes(),2);
	var ss = paddingzero(dateObj.getSeconds(),2);
	
	return year+"-"+month+"-"+day+ " " + hh + ":" + mm + ":" + ss;
	
}

