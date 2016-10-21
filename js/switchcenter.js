
function switchpage(inx){

	//alert(inx);
	switch(inx){
		
		//from main menu
		case 0:window.location.href = "./in_newproduct.html";
			break;
		case 1:window.location.href = "./in_changeproduct.html";
			break;
		
		case 2:window.location.href = "./in_stockin.html";
			break;
		
		case 3:window.location.href = "./in_stockout.html";
			break;
		
		case 4:window.location.href = "./in_report.html";
			break;
		
		case 5:window.location.href = "./in_inventur.html";
			break;
		
		case 6:window.location.href = "./in_group.html";
			break;
		
		case 7:window.location.href = "./in_stockplace.html";
			break;
			
		case 8:window.location.href = "./in_usermanager.html";
			break;
		
		
		//from sub pages
		case 11: window.location.href = "./in_menu.html";
			break;
		
		//from sub pages
		case 9: window.location.href = "./in_stockex.html";
			break;
		
	}
	
}