<?php
error_reporting(E_ALL);
include('../adodb.inc.php');



echo "New Connection\n";
$DB = NewADOConnection('pdo');
echo "Connect\n";
$pdo_connection_string = 'odbc:nwind';
$DB->Connect($pdo_connection_string,'','') || die("CONNECT FAILED");
echo "Execute\n";



//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$rs = $DB->Execute("select * from products where productid<3");
echo "e=".$DB->ErrorNo() . " ".($DB->ErrorMsg())."\n";


//print_r(get_class_methods($DB->_stmt));

if (!$rs) die("NO RS");
echo "FETCH\n";
$cnt = 0;
while (!$rs->EOF) {
	print_r($rs->fields);
	$rs->MoveNext();
	if ($cnt++ > 1000) break;
}

echo "<br>--------------------------------------------------------<br>\n\n\n";

$stmt = $DB->PrepareStmt("select * from products");
$rs = $stmt->Execute();
echo "e=".$stmt->ErrorNo() . " ".($stmt->ErrorMsg())."\n";
while ($arr = $rs->FetchRow()) {
	print_r($arr);
}
die("DONE\n");

?>