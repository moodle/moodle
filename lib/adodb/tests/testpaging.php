<?php
/* 
V2.50 14 Nov 2002  (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://php.weblogs.com/
*/

error_reporting(E_ALL);


include_once('../adodb.inc.php');
include_once('../adodb-pager.inc.php');

$driver = 'mysql';
$sql = 'select ID, firstname as "First Name", lastname as "Last Name", created as "Date Created" from adoxyz  order  by  id';
//$sql = 'select count(unitsinstock),categoryid from products group by categoryid order by 1 ';

if ($driver == 'access') {
	$db = NewADOConnection('access');
	$db->PConnect("nwind", "", "", "");
}

if ($driver == 'ibase') {
	$db = NewADOConnection('ibase');
	$db->PConnect("localhost:e:\\interbase\\examples\\database\\employee.gdb", "sysdba", "masterkey", "");
	$sql = 'select ID, firstname , lastname , created  from adoxyz order by id';
}
if ($driver == 'mssql') {
	$db = NewADOConnection('mssql');
	$db->Connect('(local)\NetSDK','','','northwind');
}
if ($driver == 'oci8') {
	$db = NewADOConnection('oci8');
	$db->Connect('','scott','tiger');
}

if ($driver == 'access') {
	$db = NewADOConnection('access');
	$db->Connect('nwind');
}

if (empty($driver) or $driver == 'mysql') {
	$db = NewADOConnection('mysql');
	$db->Connect('localhost','root','','xphplens');
}

//$db->pageExecuteCountRows = false;

$db->debug = true;

if (0) {
$rs = &$db->Execute($sql);
include_once('../toexport.inc.php');
print "<pre>";
print rs2csv($rs); # return a string

print '<hr>';
$rs->MoveFirst(); # note, some databases do not support MoveFirst
print rs2tab($rs); # return a string

print '<hr>';
$rs->MoveFirst();
rs2tabout($rs); # send to stdout directly
print "</pre>";
}

$pager = new ADODB_Pager($db,$sql);
$pager->showPageLinks = true;
$pager->linksPerPage = 3;
//$pager->cache = 60;
$pager->Render($rows=7);
?>