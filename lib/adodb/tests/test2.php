<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>
<?php
/*
  V2.00 13 May 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
 */
#
# test connecting to 2 MySQL databases simultaneously and ensure that each connection
# is independant.
#
include("../tohtml.inc.php");
include("../adodb.inc.php");	

ADOLoadCode('mysql');

$c1 = ADONewConnection('mysql');
$c2 = ADONewConnection('mysql');

if (!$c1->PConnect('flipper','','',"test")) 
	die("Cannot connect to flipper");
if (!$c2->PConnect('mangrove','root','',"northwind")) 
	die("Cannot connect to mangrove");

print "<h3>Flipper</h3>";
$t = $c1->MetaTables(); # list all tables in DB
print_r($t);
# select * from last table in DB
rs2html($c1->Execute("select * from ".$t[sizeof($t)-1])); 

print "<h3>Mangrove</h3>";
$t = $c2->MetaTables();
print_r($t);
rs2html($c2->Execute("select * from ".$t[sizeof($t)-1] ));

print "<h3>Flipper</h3>";
$t = $c1->MetaTables();
print_r($t);
rs2html($c1->Execute("select * from ".$t[sizeof($t)-1]));

?>


</body>
</html>
