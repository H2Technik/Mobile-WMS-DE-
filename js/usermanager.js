
var NEW_USER=1;
var LOAD_USR = 2;
var DELETE_USR = 3;
var CHECK_LOGIN=10;
var GET_LOGIN=11;
var SHOW_LOGIN=12;
var CHECK_PLARGT=13;
var LOG_OUT = 20;
var CLEAR_LOGIN = 30;

var global_userinfo = {};
var tmprights = new Array();

function clearLastLogin(){
	//alert("123");
	asynAjaxCallNewUser(CLEAR_LOGIN);
}


function logoutuser(){
	//alert("1");
	asynAjaxCallNewUser(LOG_OUT);

}

function doSecurityCheck(){
	
	$(".mainbtn").hide();
	asynAjaxCallNewUser(GET_LOGIN);
	
}

function loadinguserinfo(){
	asynAjaxCallNewUser(SHOW_LOGIN);
	
}

function findRightforPlace(){
	asynAjaxCallNewUser(CHECK_PLARGT);
}

function checkLogin(){
	
	if ($("#id_username").val().length == 0 || $("#id_password").val().length == 0){
		$("#id_username").val("");
		$("#id_password").val("");
		return;
	}
	
	asynAjaxCallNewUser(CHECK_LOGIN);
}

function createNewUser(){
	//alert("abcd");
	
	if ($("#id_newname").val().length == 0 ||
		$("#id_password1").val().length == 0 ||
     	$("#id_password2").val().length == 0){
			
		alert("Infomration fehlt !");
		return;
	
	}
	
	if ($("#id_password1").val() != $("#id_password2").val()){
		alert("Passwort stimmt nicht überein !");
		return;
	}
	
	tmprights = new Array();
	$("input[name='rights']:checked").each(function() {
		tmprights.push($(this).val());
		//alert($(this).val());
	});
	//alert(tmprights);
	
	if (tmprights.length == 0){
		alert("keine Berechtigungen ausgewählt !");
		return;
	}
	
	
	openmydialog(1, "<P>Wollen Sie den neuen Bentuzer erstellen ?</P>", "", callbackDialogNewUser);
}



function callbackDialogNewUser(what){
	
	if (what == 1){
		asynAjaxCallNewUser(NEW_USER);
	}

	
}


function deleteUser(){
	var tmp = $("#id_existingusers option:selected").text();
	if (tmp == "-") return;
	openmydialog(1, "<P>Wollen Sie den '" + tmp + "' löschen ?</P>", "", callbackDialogDelUser);
}

function callbackDialogDelUser(what){
	
	if (what == 1){
		asynAjaxCallNewUser(DELETE_USR);
	}

	
}




function loadcurusers(){
	//alert("locacurusers");
	asynAjaxCallNewUser(LOAD_USR);
}


function asynAjaxCallNewUser(act_id){
	
	//alert(act_id);
	var tmp = "";
	var tmpname = "";
	var tmppass = "";
	
	if (act_id == NEW_USER) {tmp = "NEW"; tmpname = $("#id_newname").val(); tmppass=$("#id_password1").val();}
	else if (act_id == LOAD_USR) tmp = "LOAD";
	else if (act_id == DELETE_USR) {tmp = "DELETE"; tmpname = $("#id_existingusers option:selected").text();}
	else if (act_id == CHECK_LOGIN) {tmp = "LOGIN";  tmpname = $("#id_username").val(); tmppass=$("#id_password").val(); }
	else if (act_id == CHECK_PLARGT) {tmp = "CHECK_PLACE_RIGHT";  tmpname = ""; tmppass=""; }
	else if (act_id == GET_LOGIN || act_id == SHOW_LOGIN) {tmp = "GETLOGIN";  tmpname = ""; tmppass=""; }
	else if (act_id == LOG_OUT) {tmp = "LOGOUT";  tmpname = ""; tmppass=""; }
	else if (act_id == CLEAR_LOGIN) {tmp = "CLEAR_LOGIN";  tmpname =$("#id_username").val(); tmppass=tmppass=$("#id_password").val(); }
	
	//alert(tmp);
	$.ajax({
			type: "POST",
			url: "./exe/usermanager.php",
			data: {Action:tmp, Name:tmpname, Pass:tmppass, Rights:tmprights }
			})
			.done(function( message ) {
				
				//alert(message);
				if (act_id == NEW_USER){
					
				}
				
				if (act_id == LOAD_USR){
					
					global_userinfo = eval('('+message+')');
					//alert(global_userinfo);
					fillOutCurInfo();
				}
				
				if (act_id == DELETE_USR){
					
				}
				
				//login
				if (act_id == CHECK_LOGIN || act_id == CLEAR_LOGIN){
					
					if (message.indexOf("eingeloggt O.K.")) window.location.href = "./in_menu.html";
					else{
						alert(message);
						{$("#id_username").val(""); $("#id_password").val("");}
					}
				}
				
				//got login
				if (act_id == GET_LOGIN){
					//alert(message);
					
					if (message == "-") window.location.href = "./in_logout.html"
					else{
						var tmps = message.split("|");
						$("#id_homeinfo").append(tmps[0] + " <a href='./in_logout.html' style='color: #f00'>Ausloggen<a>");
						//alert(tmps[2]);
						makeRightAction(tmps[2]);
					}
				}
				
				//show login
				if(act_id == SHOW_LOGIN){
					//alert(message);
					var tmps = message.split("|");
					$("#id_logininfo").text("Benutzer:   " + tmps[0]);
					$("#id_resttime").text("Ausloggon in: " + " 5 " + "Min");
					
				}
				
				//check Stockplace right
				if (act_id == CHECK_PLARGT){
				
					//watch stockplaces
					if (message & 256){
						$("#id_isonline").show();
						$(".actchange").hide();
						$("#id_changeact").val("Auswahl");
					}
					//change stockplaces
					if (message & 128){
						$(".actchange").show();
						$("#id_isonline").show();
						$("#id_changeact").val("Ändern");
					}
				}
				
				//logout
				if (act_id == LOG_OUT){
					alert(message);
				}
			});
	
}

function fillOutCurInfo(){
	
	//alert(global_userinfo.length);
	
	for (var i = 0 ; i< global_userinfo.length; i++){
			//alert(global_userinfo[i][0]);
			$("#id_existingusers").append("<option>" + global_userinfo[i][0] + "</option>");
	}
	
}
function loadcurright(){
	var cursel = $("#id_existingusers option:selected").text();
	for (var i = 0 ; i< global_userinfo.length; i++){
			if (cursel == global_userinfo[i][0]) {
				$("#id_currights").val(global_userinfo[i][1]);
				break;
			}
	}
}

function makeRightAction(right){
	
	//alert("123456");
	if ( right & 1 ) $("#id_newp").show();
	if ( right & 2) $("#id_chap").show();
	if (right & 4) $("#id_grop").show();
	if (right & 8) $("#id_moin").show();
	if (right & 16) $("#id_moou").show();
	if (right & 32) $("#id_repo").show();
	if (right & 64) $("#id_inve").show();
	if (right & 128 || right & 256) $("#id_plac").show();
	if (right & 512) $("#id_user").show();
	if (right & 1024) $("#id_chamove").show();
}

