<?php

/** 
 * @version V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
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

include('../adodb.inc.php');
include('../tohtml.inc.php');

//==========================
// This code tests an insert

$sql = "SELECT * FROM ADOXYZ WHERE id = -1"; 
// Select an empty record from the database 

$conn = &ADONewConnection("mysql");  // create a connection
$conn->debug=1;
$conn->PConnect("localhost", "root", "", "test"); // connect to MySQL, testdb
$conn->Execute("delete from adoxyz where lastname like 'Smith%'");

$rs = $conn->Execute($sql); // Execute the query and get the empty recordset
$record = array(); // Initialize an array to hold the record data to insert

// Set the values for the fields in the record
$record["firstname"] = "Bob";
$record["lastname"] = "Smith\$@//";
$record["created"] = time();

// Pass the empty recordset and the array containing the data to insert
// into the GetInsertSQL function. The function will process the data and return
// a fully formatted insert sql statement.
$insertSQL = $conn->GetInsertSQL($rs, $record);

$conn->Execute($insertSQL); // Insert the record into the database

//==========================
// This code tests an update

$sql = "SELECT * FROM ADOXYZ WHERE lastname=".$conn->qstr($record['lastname']); 
// Select a record to update 

$rs = $conn->Execute($sql); // Execute the query and get the existing record to update
if (!$rs) print "<p>No record found!</p>";
$record = array(); // Initialize an array to hold the record data to update

// Set the values for the fields in the record
$record["firstname"] = "Caroline".rand();
$record["lastname"] = "Smithy"; // Update Caroline's lastname from Miranda to Smith
$record["created"] = '2002-12-'.(rand()%30+1);

// Pass the single record recordset and the array containing the data to update
// into the GetUpdateSQL function. The function will process the data and return
// a fully formatted update sql statement.
// If the data has not changed, no recordset is returned
$updateSQL = $conn->GetUpdateSQL($rs, $record);

$conn->Execute($updateSQL); // Update the record in the database
print "<p>Rows Affected=".$conn->Affected_Rows()."</p>";

rs2html($conn->Execute("select * from adoxyz where lastname like 'Smith%'"));
}


testsql();
?>