<?php
/*
  V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
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

$dbt = 'oci8';

switch($dbt) {
case 'oci8':
	$db = NewADOConnection("oci8");
	$db->Connect('','scott','natsoft');
	break;
default:
case 'mysql':
	$db = NewADOConnection("mysql");
	$db->Connect('localhost','root','','test');
	break;
}

$db->debug=1;

$cnt = $db->GetOne("select count(*) from adoxyz");
$rs = $db->Execute("select * from adoxyz order by id");

$i = 0;
foreach($rs as  $v) {
	$i += 1;
	echo "$i: "; adodb_pr($v); adodb_pr($rs->fields);
	flush();
}

if ($i != $cnt) die("actual cnt is $i, cnt should be $cnt\n");


$rs = $db->Execute("select bad from badder");

} catch (exception $e) {
	adodb_pr($e);
	echo "<h3>adodb_backtrace:</h3>\n";
	$e = adodb_backtrace($e->gettrace());
}

?>