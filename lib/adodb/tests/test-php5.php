<?php
/*
  V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
 */


error_reporting(E_ALL);

$path = dirname(__FILE__);

include("$path/../adodb-exceptions.inc.php");
include("$path/../adodb.inc.php");	

echo "<h3>PHP ".PHP_VERSION."</h3>\n";
try {

$dbt = 'mysql';

try {
switch($dbt) {
case 'oci8po':
	$db = NewADOConnection("oci8po");
	
	$db->Connect('','scott','natsoft');
	break;
default:
case 'mysql':
	$db = NewADOConnection("mysql");
	$db->Connect('localhost','roots','','northwind');
	break;
	
case 'mysqli':
	$db = NewADOConnection("mysqli://root:@localhost/northwind");
	//$db->Connect('localhost','root','','test');
	break;
}
} catch (exception $e){
	echo "Connect Failed";
	adodb_pr($e);
	die();
}

$db->debug=1;

$cnt = $db->GetOne("select count(*) from adoxyz where ?<id and id<?",array(10,20));
$stmt = $db->Prepare("select * from adoxyz where ?<id and id<?");
if (!$stmt) echo $db->ErrorMsg(),"\n";
$rs = $db->Execute($stmt,array(10,20));

echo  "<hr> Foreach Iterator Test (rand=".rand().")<hr>";
$i = 0;
foreach($rs as $v) {
	$i += 1;
	echo "rec $i: "; $s1 = adodb_pr($v,true); $s2 = adodb_pr($rs->fields,true);
	if ($s1 != $s2 && !empty($v)) {adodb_pr($s1); adodb_pr($s2);}
	else echo "passed<br>";
	flush();
}


if ($i != $cnt) die("actual cnt is $i, cnt should be $cnt\n");
else echo "Count $i is correct<br>";

$rs = $db->Execute("select bad from badder");

} catch (exception $e) {
	adodb_pr($e);
	echo "<h3>adodb_backtrace:</h3>\n";
	$e = adodb_backtrace($e->gettrace());
}

$rs = $db->Execute("select distinct id, firstname,lastname from adoxyz order by id");
echo "Result=\n",$rs;
?>