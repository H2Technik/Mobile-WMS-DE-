// JavaScript Document



/******************************
parameter declaration
inx: type of dialog (buttons)
htmlstr: content in html format in dialog
valid: the element where the value must be returned
callbackfunc: the callback function
******************************/
var dialog_retval="";
var dialog_retval2="";
var dialog_retval3="";

var bret = false;
function openmydialog(inx, htmlstr, valid, cbfunc){
	
	preparedialog(htmlstr);	
	
	switch (inx)
	{
		case 1:

			dialog_new(valid, cbfunc);
			break;
		
		case 2:
			
			dialog_nobutton(cbfunc);
			break;	
		
		case 3:

			dialog_delete(cbfunc);
			break;

			
		default:
	}
	$("#id_mydialog").dialog("open");
	
	
}

function openmydialogForWS(htmlstr, id_name, id_type, id_resolution, cbfunc){
	preparedialog(htmlstr);	
	dialog_newWS(id_name, id_type, id_resolution, cbfunc);
}



function preparedialog(htmlstr){
	
	//remove id_mydialog
	$("#id_mydialog").remove();
	dialog_retval = "";
	bret = false;
	$("body").append("<div id='id_mydialog' style='display:none;' title='Message'>"+htmlstr+"</div>");
	
	
}

function dialog_nobutton(cbfunc){
	
		$('.ui-dialog-buttonpane button:contains("O.K.")').button().hide();
		$('.ui-dialog-buttonpane button:contains("Cancel")').button().hide();
	
		$("#id_mydialog").dialog({
				close: function(event, ui) {  cbfunc(2); },
				autoOpen:false,
				modal:true,
				height:200,
				buttons:{
					"Close": function() {
				$( this ).dialog( "close" );
				}
				}
			} ).prev(".ui-dialog-titlebar").css("background","#6CF");
			
}
function dialog_new(id, cbfunc){
	
		$( "#id_mydialog" ).dialog({
			//close: function(event, ui) {  $(this).html("") },
			resizable: false,
			height:240,
			modal: true,
			buttons: {
			"O.K.": function() {
				dialog_retval = $("#"+id).val();
				$( this ).dialog( "close" );
				cbfunc(1);
			},
			"Cancel": function() {
				$( this ).dialog( "close" );
			}
		}
		} ).prev(".ui-dialog-titlebar").css("background","#6CF");
}

function dialog_newWS(id_n, id_t, id_r, cbfunc){
		
		$( "#id_mydialog" ).dialog({
			//close: function(event, ui) {  $(this).html("") },
			resizable: false,
			height:300,
			modal: true,
			buttons: {
			"O.K.": function() {
				var x = 1;
				if (id_n.length > 0){
					 dialog_retval = $("#"+id_n).val();
				}
				else{
					x = 2;
				}
				dialog_retval2 = $("#"+id_t).val();
				dialog_retval3 = $("#"+id_r).val();
				$( this ).dialog( "close" );
				cbfunc(x);
			},
			"Cancel": function() {
				$( this ).dialog( "close" );
			}
		}
		} ).prev(".ui-dialog-titlebar").css("background","#6CF");
}

function dialog_delete(cbfunc){
	
		$( "#id_mydialog" ).dialog({
			//close: function(event, ui) {  $(this).html("") },
			resizable: false,
			height:240,
			modal: true,
			buttons: {
			"Yes": function() {
				bret = true;
				$( this ).dialog( "close" );
				cbfunc(3);
			},
			"No": function() {
				$( this ).dialog( "close" );
			}
		}
		} ).prev(".ui-dialog-titlebar").css("background","#6CF");
}



/* $(function() {
$( "#id_save_page" ).dialog({
resizable: false,
height:240,
modal: true,
buttons: {
"Delete all items": function() {
$( this ).dialog( "close" );
},
Cancel: function() {
$( this ).dialog( "close" );
}
}
});
});
*/

/*
$(function(){
		
		$("#id_save_page").dialog({autoOpen:false,modal:true
			} );
		
		$("#opener").click( function(){
				//alert("234234");
				$("#id_save_page").dialog("open");  
   				$(".ui-widget-overlay").css("background", "white");
			} );	
	});
*/