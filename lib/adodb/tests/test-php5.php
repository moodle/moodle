<?php
/*
  V4.51 29 July 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
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

$dbt = 'oci8po';

switch($dbt) {
case 'oci8po':
	$db = NewADOConnection("oci8po");
	$db->Connect('','scott','natsoft');
	break;
default:
case 'mysql':
	$db = NewADOConnection("mysql");
	$db->Connect('localhost','root','','test');
	break;
	
case 'mysqli':
	$db = NewADOConnection("mysqli://root:@localhost/test");
	//$db->Connect('localhost','root','','test');
	break;
}

$db->debug=1;

$cnt = $db->GetOne("select count(*) from adoxyz where ?<id and id<?",array(10,20));
$stmt = $db->Prepare("select * from adoxyz where ?<id and id<?");
if (!$stmt) echo $db->ErrorMsg(),"\n";
$rs = $db->Execute($stmt,array(10,20));

$i = 0;
foreach($rs as  $v) {
	$i += 1;
	echo "rec $i: "; adodb_pr($v); adodb_pr($rs->fields);
	flush();
}


if ($i != $cnt) die("actual cnt is $i, cnt should be $cnt\n");


$rs = $db->Execute("select bad from badder");

} catch (exception $e) {
	adodb_pr($e);
	echo "<h3>adodb_backtrace:</h3>\n";
	$e = adodb_backtrace($e->gettrace());
}

$rs = $db->Execute("select distinct id, firstname,lastname from adoxyz order by id");
echo "Result=\n",$rs;
?>