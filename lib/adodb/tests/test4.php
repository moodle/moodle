<?php

/** 
 * @version V4.50 6 July 2004 (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
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
function testsql()
{

//define('ADODB_FORCE_NULLS',1);

include('../adodb.inc.php');
include('../tohtml.inc.php');

//==========================
// This code tests an insert

$sql = "
SELECT * 
FROM ADOXYZ WHERE id = -1"; 
// Select an empty record from the database 

$conn = &ADONewConnection("mysql");  // create a connection
$conn->PConnect("localhost", "root", "", "test"); // connect to MySQL, testdb

//$conn =& ADONewConnection('oci8');
//$conn->Connect('','scott','natsoft');
//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$conn->debug=1;
$conn->Execute("delete from adoxyz where lastname like 'Smith%'");

$rs = $conn->Execute($sql); // Execute the query and get the empty recordset
$record = array(); // Initialize an array to hold the record data to insert

// Set the values for the fields in the record
$record["firstname"] = 'null';
$record["lastname"] = "Smith\$@//";
$record["created"] = time();
//$record["id"] = -1;

// Pass the empty recordset and the array containing the data to insert
// into the GetInsertSQL function. The function will process the data and return
// a fully formatted insert sql statement.
$insertSQL = $conn->GetInsertSQL($rs, $record);

$conn->Execute($insertSQL); // Insert the record into the database

$insertSQL2 = $conn->GetInsertSQL($table='ADOXYZ', $record);
if ($insertSQL != $insertSQL2) echo "<p><b>Walt's new stuff failed</b>: $insertSQL2</p>";
//==========================
// This code tests an update

$sql = "
SELECT * 
FROM ADOXYZ WHERE lastname=".$conn->qstr($record['lastname']); 
// Select a record to update 

$rs = $conn->Execute($sql); // Execute the query and get the existing record to update
if (!$rs) print "<p><b>No record found!</b></p>";

$record = array(); // Initialize an array to hold the record data to update

// Set the values for the fields in the record
$record["firstName"] = "Caroline".rand();
$record["lasTname"] = "Smithy Jones"; // Update Caroline's lastname from Miranda to Smith
$record["creAted"] = '2002-12-'.(rand()%30+1);
$record['num'] = 3921;
// Pass the single record recordset and the array containing the data to update
// into the GetUpdateSQL function. The function will process the data and return
// a fully formatted update sql statement.
// If the data has not changed, no recordset is returned
$updateSQL = $conn->GetUpdateSQL($rs, $record);

$conn->Execute($updateSQL); // Update the record in the database
if ($conn->Affected_Rows() != 1)print "<p><b>Error</b>: Rows Affected=".$conn->Affected_Rows().", should be 1</p>";

$rs = $conn->Execute("select * from adoxyz where lastname like 'Smith%'");
adodb_pr($rs);
rs2html($rs);
}


testsql();
?>