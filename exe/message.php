<?php

//include 'connection.php';

class Message{
	
	//global action ID
	const ACT_NEWPRD = 1;
	const ACT_CHAPRD = 2;
	const ACT_DELPRD = 3;
	const ACT_NEWGRP = 4;
	const ACT_DELGRP = 5;
	
	const ACT_STOCKIN = 110;
	const ACT_STOCKOU = 120;
	const ACT_STOCKCH = 130;
	const ACT_SAINVEN = 140;
	const ACT_PLACECH = 150;
	const ACT_PLADELE = 151;
	
	const ACT_NEWUSER = 200;
	const ACT_DELUSER = 201;
	
	const ACT_REPORT = 70;
	
	
	//products
	const MSG_DUP_PROID = "Duplikat von Produkt-ID !"; 
	const MSG_NEW_PROOK = "neuer Produkt eingefügt O.K."; 
	const MSG_NEW_PROER = "Einfügen neuer Produkt Fehler!"; 
	const MSG_ER_NPPROID = "Kein Produkt gefunden !"; 
	const MSG_ER_DELPRO = "Fehler beim Löschen Produkt !"; 
	const MSG_DEL_PROOK = "Produkt wurde gelöscht."; 
	const MSG_CHA_PROOK= "Produkt Info wurde geändert.";
	const MSG_ER_PROCHA= "Fehler beim Ändern Produkt Info !";
	
	//stock in
	const MSG_NO_PROID = "kein Product ID !"; 
	const MSG_ER_INSIN = "Fehler beim Einfügen StockIn !"; 
	const MSG_OK_INSIN = "Speichern StockIn O.K."; 
	
	//stock out
	const MSG_ER_INSOT = "Fehler beim Einfügen StockOut !"; 
	const MSG_OK_INSOT = "Speichern StockOut O.K."; 
	const MSG_ER_PRONOTSTK = "Produkt sollt nicht auf diesem Platz gelagert !"; 
	
	//balanced
	const MSG_ER_BALUP = "Fehler beim Update Balanced !";
	const MSG_ER_GETNR = "Fehler beim Lesen aktuellen Lagerbestand !";
	const MSG_ER_ININEW = "Fehler beim Initializieren Eintrag in Balanced !";
	const MSG_ER_DELBAL = "Fehler beim Löschen Produkt Info von Balanced Tabelle !"; 
	
	const MSG_ER_INVUPBA = "Fehler beim Inventur update Balanced";
	const MSG_OK_INVUPBA = "Inventur update Balanced O.K.";
	
	//inventur
	const MSG_NEW_INVOK ="neuer Inventur Eintrag eingefügt .";
	const MSG_NEW_INVER ="Fehler beim Einfügen neuees Inventur Eintrags !";
	
	//report
	const MSG_OK_REPORT = "Berichte wurde erfolgreich erstellt.";
	const MSG_OK_REPUSR ="Benutzer definierte Berichte wurde erfolgreich erstellt .";
	const MSG_NO_REPDATA = "keine Daten mit gegebenen Bedingungen .";
	
	//group
	const MSG_DUP_GRPNM = "Gruppe exisitiert bereit !";
	const MSG_NEW_GRPOK = "neue Gruppe ist erstellt O.K.";
	const MSG_ER_NEWGRP = "Fehler beim Erstellen neuer Gruppe !";
	const MSG_ER_DELGRP = "Fehler beim Löschen Gruppe !";
	const MSG_ER_GETGRP = "Fehler beim Lesen Gruppe !";
	const MSG_GRP_DELOK = "Gruppe ist gelöscht.";
	
	//stockplace
	const MSG_ER_FINDACT = "Fehler beim Lesen Lagerplatz Info !";
	const MSG_DUP_STKPLACE = "Duplikat von Lagerplatz !";
	const MSG_NEW_PLACEOK = "neuer Lagerplatz ist erstellt O.K.";
	const MSG_ER_NEWPLACE = "Fehler beim Erstellen Lagerplatz !";
	const MSG_PLC_DELOK = "Lagerplatz ist gelöscht O.K. ";
	const MSG_ER_PLCDEL = "Fehler beim Löschen Lagerplatz !";
	const MSG_ER_NOPLC = "Lagerplatz existiert nicht !";
	const MSG_ER_PLCCOL = "falsche Spalte in Lagerplatz ID !";
	const MSG_ER_PLCLEV = "falsche Ebene in Lagerplatz ID !";
	
	//USer
	const MSG_USR_DUPLI = "Duplikate Benutzer !";
	const MSG_USR_LOADALL = "Fehler beim Laden allen Benutzer !";
	const MSG_USR_OKNEW = "Bnutzer ist erstellt O.K.";
	const MSG_USR_ERNEW = "Fehler beim Erstellen Benutzer !";
	const MSG_USR_DELOK = "Benutzer wurde gelöscht .";
	const MSG_USR_ERDEL = "Fehler beim Löschen Benutzer !";
	
	const MSG_USER_LOGER = "Fehler beim LogBenutzer !";
	const MSG_USER_EINOK = "Benutzer ist eingeloggt O.K. ";
	const MSG_USER_AUSOK = "Benutzer ist ausgeloggt O.K. ";
	const MSG_USER_DUPLO = "Benutzer ist schon auf anderen Gerät eingeloggt !";
	
	
	public static function logEvent($actid, $name ='',$code='', $place='', $info='', $status=0){
		global $dbc;
		
		$sql = "INSERT INTO eventlog VALUES('" . Date("Y-m-d H:i:s") . "', '" . 
		                                        $name . "', " .
		                                        $actid ;
		
		if (isset($code)) $sql .= ", '" . $code . "' ";
		else $sql .= "''";
		
		if (isset($place)) $sql .= ", '" . $place . "' ";
		else $sql .= "''";
		
		if (isset($info)) $sql .= ", '" . $info . "' ";
		else $sql .= "''";
		
		if (isset($status)) $sql .= ", " . $status . "";
		else $sql .= "1";
		
		$sql .=")";
		
		echo $sql;
		$dbc->execWriteStatement($sql);
	}
	
}



?>