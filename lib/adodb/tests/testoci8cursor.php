<?php
/* 
V3.60 16 June 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://php.weblogs.com/
*/

/*
	Test for Oracle Variable Cursors, which are treated as ADOdb recordsets.
	
	We have 2 examples. The first shows us using the Parameter statement. 
	The second shows us using the new ExecuteCursor($sql, $cursorName)
	function.
	
------------------------------------------------------------------
-- TEST PACKAGE YOU NEED TO INSTALL ON ORACLE - run from sql*plus
------------------------------------------------------------------

CREATE or replace PACKAGE adodb AS
TYPE TabType IS REF CURSOR RETURN tab%ROWTYPE;
  -- list all tables that match tablenames in current schema
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames in varchar);
END adodb;
/
CREATE or replace PACKAGE BODY adodb AS
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames in varchar) IS
	BEGIN
		OPEN tabcursor FOR SELECT * FROM tab where tname like tablenames;
	END open_tab;
END adodb;
/

------------------------------------------------------------------
-- END PACKAGE
------------------------------------------------------------------

*/

include('../adodb.inc.php');
include('../tohtml.inc.php');

	error_reporting(E_ALL);
	$db = ADONewConnection('oci8');
	$db->PConnect('','scott','tiger');
	$db->debug = true;



	#---------------------------------------------------------------
	# EXAMPLE 1
	# explicitly use Parameter function
	#---------------------------------------------------------------
	$stmt = $db->Prepare("BEGIN adodb.open_tab(:RS,'%'); END;");
	$db->Parameter($stmt, $cur, 'RS', false, -1, OCI_B_CURSOR);
	$rs = $db->Execute($stmt);
	
	if ($rs && !$rs->EOF) {
		print "Test 1 RowCount: ".$rs->RecordCount()."<p>";
	} else {
		print "<b>Error in using Cursor Variables 1</b><p>";
	}
	
	#---------------------------------------------------------------
	# EXAMPLE 2
	# Equivalent of above example 1 using ExecuteCursor($sql,$rsname)
	#---------------------------------------------------------------
	$rs = $db->ExecuteCursor(
		"BEGIN adodb.open_tab(:RS,'%'); END;", # pl/sql script
		'RS');                                 # cursor name
		
	if ($rs && !$rs->EOF) {
		print "Test 2 RowCount: ".$rs->RecordCount()."<p>";
		rs2html($rs);
	} else {
		print "<b>Error in using Cursor Variables 2</b><p>";
	}
		
?>