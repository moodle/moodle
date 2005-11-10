<?php
error_reporting(E_ALL);
include('../adodb.inc.php');

try {
	echo "New Connection\n";
	$DB = NewADOConnection('pdo');
	echo "Connect\n";
	
	$u = ''; $p = '';
	/*
	$connstr = 'odbc:nwind';
	
	$connstr = 'oci:';
	$u = 'scott';
	$p = 'natsoft';
	
	
	$connstr ="sqlite:d:\inetpub\adodb\sqlite.db";
	*/
	
	$connstr = "mysql:dbname=northwind";
	$u = 'root';
	
	$connstr = "pgsql:dbname=test";
	$u = 'tester';
	$p = 'test';
	
	$DB->Connect($connstr,$u,$p) || die("CONNECT FAILED");
	
	
	echo "$connstr: Execute\n";
	
	//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $DB->Execute("select * from ADOXYZ where id<3");
	echo "*** e=".$DB->ErrorNo() . " ".($DB->ErrorMsg())."\n";
	
	
	//print_r(get_class_methods($DB->_stmt));
	
	if (!$rs) die("NO RS");
	
	echo "Meta\n";
	for ($i=0; $i < $rs->NumCols(); $i++) {
		var_dump($rs->FetchField($i));
		echo "<br>";
	}
	
	echo "FETCH\n";
	$cnt = 0;
	while (!$rs->EOF) {
		adodb_pr($rs->fields); 
		$rs->MoveNext();
		if ($cnt++ > 1000) break;
	}
	
	echo "<br>--------------------------------------------------------<br>\n\n\n";
	
	$stmt = $DB->PrepareStmt("select * from ADOXYZ");
	
	$rs = $stmt->Execute();
	$cols = $stmt->NumCols(); // execute required
	
	echo "COLS = $cols";
	for($i=1;$i<=$cols;$i++) {
		$v = $stmt->_stmt->getColumnMeta($i);
		var_dump($v);
	}
	
	echo "e=".$stmt->ErrorNo() . " ".($stmt->ErrorMsg())."\n";
	while ($arr = $rs->FetchRow()) {
		adodb_pr($arr);
	}
	die("DONE\n");

} catch (exception $e) {
	echo "<pre>";
	echo $e;
	echo "</pre>";
}

?>