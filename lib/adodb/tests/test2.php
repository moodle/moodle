<?php

// BASIC ADO test

	include_once('../adodb.inc.php');

	$db = &ADONewConnection("ado_access");
	$db->debug=1;
	$access = 'd:\inetpub\wwwroot\php\NWIND.MDB';
	$myDSN =  'PROVIDER=Microsoft.Jet.OLEDB.4.0;'
		. 'DATA SOURCE=' . $access . ';';
		
	echo "<p>PHP ",PHP_VERSION,"</p>";
	
	$db->Connect($myDSN) || die('fail');
	
	print_r($db->ServerInfo());
	
	try {
	$rs = $db->Execute("select $db->sysTimeStamp,* from adoxyz where id>02xx");
	print_r($rs->fields);
	} catch(exception $e) {
	print_r($e);
	echo "<p> Date m/d/Y =",$db->UserDate($rs->fields[4],'m/d/Y');
	}
?>