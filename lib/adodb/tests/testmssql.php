<?php

/** 
 * @version V3.40 7 April 2003 (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
 * Whenever there is any discrepancy between the two licenses, 
 * the BSD license will take precedence. 
 *
 * Set tabs to 4 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 *
 * Test GetUpdateSQL and GetInsertSQL.
 */
 
error_reporting(E_ALL);


include('../adodb.inc.php');
include('../tohtml.inc.php');

//==========================
// This code tests an insert



$conn = &ADONewConnection("odbc_mssql");  // create a connection
$conn->Connect('sqlserver','sa','natsoft');

//$conn = &ADONewConnection("mssql");
//$conn->Connect('mangrove','sa','natsoft','ai');

//$conn->Connect('mangrove','sa','natsoft','ai');
$conn->debug=1;
$conn->Execute('delete from blobtest');

$conn->Execute('insert into blobtest (id) values(1)');
$conn->UpdateBlobFile('blobtest','b1','../cute_icons_for_site/adodb.gif','id=1');
$rs = $conn->Execute('select b1 from blobtest where id=1');

$output = "c:\\temp\\test_out-".date('H-i-s').".gif"; 
print "Saving file <b>$output</b>, size=".strlen($rs->fields[0])."<p>";
$fd = fopen($output, "wb"); 
fwrite($fd, $rs->fields[0]); 
fclose($fd); 

print " <a href=file://$output>View Image</a>";
//$rs = $conn->Execute('SELECT id,SUBSTRING(b1, 1, 10) FROM blobtest');
//rs2html($rs);
?>