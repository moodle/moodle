<?php
/* 
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://php.weblogs.com/
*/

error_reporting(E_ALL);


define('ADODB_ASSOC_CASE',0);

include_once('../adodb-pear.inc.php');
//--------------------------------------------------------------------------------------
//define('ADODB_ASSOC_CASE',1);
//
function Err($msg)
{
	print "<b>$msg</b><br>";
	flush();
}

function CheckWS($conn)
{
global $ADODB_EXTENSION;

	include_once('../adodb-session.php');
	
	$saved = $ADODB_EXTENSION;
	$db = ADONewConnection($conn);
	$ADODB_EXTENSION = $saved;
	if (headers_sent()) {
		print "<p><b>White space detected in adodb-$conn.inc.php or include file...</b></p>";
		die();
	}
}

function do_strtolower(&$arr)
{
	foreach($arr as $k => $v) {
		$arr[$k] = strtolower($v);
	}
}


function CountExecs($db, $sql, $inputarray)
{
global $EXECS;  $EXECS++;
}

function CountCachedExecs($db, $secs2cache, $sql, $inputarray)
{
global $CACHED; $CACHED++;
}

// the table creation code is specific to the database, so we allow the user 
// to define their own table creation stuff

function testdb(&$db,$createtab="create table ADOXYZ (id int, firstname char(24), lastname char(24), created date)")
{
GLOBAL $ADODB_vers,$ADODB_CACHE_DIR,$ADODB_FETCH_MODE, $HTTP_GET_VARS,$ADODB_COUNTRECS;
?>	<form method=GET>
	</p>
	<table width=100% ><tr><td bgcolor=beige>&nbsp;</td></tr></table>
	</p>
<?php  
	$create =false;
	
	GLOBAL $EXECS, $CACHED;
	
	$EXECS = 0;
	$CACHED = 0;
	
	$db->fnExecute = 'CountExecs';
	$db->fnCacheExecute = 'CountCachedExecs';
	
	$ADODB_CACHE_DIR = dirname(TempNam('/tmp','testadodb'));
	$db->debug = false;
	
	//print $db->UnixTimeStamp('2003-7-22 23:00:00');
	
	$phpv = phpversion();
	if (defined('ADODB_EXTENSION')) $ext = ' &nbsp; Extension '.ADODB_EXTENSION.' installed';
	else $ext = '';
	print "<h3>ADODB Version: $ADODB_vers Host: <i>$db->host</i> &nbsp; Database: <i>$db->database</i> &nbsp; PHP: $phpv $ext</h3>";
	
	$arr = $db->ServerInfo();
	print_r($arr);
	$e = error_reporting(E_ALL-E_WARNING);
	flush();
	print "<i>date1</i> (1969-02-20) = ".$db->DBDate('1969-2-20');
	print "<br><i>date1</i> (1999-02-20) = ".$db->DBDate('1999-2-20');
	print "<br><i>date2</i> (1970-1-2) = ".$db->DBDate(24*3600)."<p>";
	print "<i>ts1</i> (1999-02-20 3:40:50) = ".$db->DBTimeStamp('1999-2-20 13:40:50');
	print "<br><i>ts2</i> (1999-02-20) = ".$db->DBTimeStamp('1999-2-20');
	print "<br><i>ts3</i> (1970-1-2 +/- timezone) = ".$db->DBTimeStamp(24*3600);
	print "<br> Fractional TS (1999-2-20 13:40:50.91): ".$db->DBTimeStamp($db->UnixTimeStamp('1999-2-20 13:40:50.91+1'));
	 $dd = $db->UnixDate('1999-02-20');
	print "<br>unixdate</i> 1999-02-20 = ".date('Y-m-d',$dd)."<p>";
	flush();
	// mssql too slow in failing bad connection
	if (false && $db->databaseType != 'mssql') {
		print "<p>Testing bad connection. Ignore following error msgs:<br>";
		$db2 = ADONewConnection();
		$rez = $db2->Connect("bad connection");
		$err = $db2->ErrorMsg();
		print "<i>Error='$err'</i></p>";
		if ($rez) print "<b>Cannot check if connection failed.</b> The Connect() function returned true.</p>";
	}
	error_reporting($e);
	flush();
	
	//$ADODB_COUNTRECS=false;
	$rs=$db->Execute('select * from adoxyz order by id');

	//print_r($rs);
	//OCIFetchStatement($rs->_queryID,$rez,0,-1);//,OCI_ASSOC | OCI_FETCHSTATEMENT_BY_ROW);
	//print_r($rez);
	//die();
	if($rs === false) $create = true;
	else $rs->Close();
		
	//if ($db->databaseType !='vfp') $db->Execute("drop table ADOXYZ");
		
	if ($create) {
		if (false && $db->databaseType == 'ibase') {
			print "<b>Please create the following table for testing:</b></p>$createtab</p>";
			return;
		} else {
			$db->debug = 1;
			$e = error_reporting(E_ALL-E_WARNING);
			$db->Execute($createtab);
			error_reporting($e);
		}
	}
	$rs = &$db->Execute("delete from ADOXYZ"); // some ODBC drivers will fail the drop so we delete
	
	if ($rs) {
		if(! $rs->EOF) print "<b>Error: </b>RecordSet returned by Execute('delete...') should show EOF</p>";
		$rs->Close();
	} else print "err=".$db->ErrorMsg();

	print "<p>Test select on empty table</p>";
	$rs = &$db->Execute("select * from ADOXYZ where id=9999");
	if ($rs && !$rs->EOF) print "<b>Error: </b>RecordSet returned by Execute(select...') on empty table should show EOF</p>";
	if ($rs) $rs->Close();
	flush();
	//$db->debug=true;	
	print "<p>Testing Commit: ";
	$time = $db->DBDate(time());
	if (!$db->BeginTrans()) {
		print '<b>Transactions not supported</b></p>';
		if ($db->hasTransactions) Err("hasTransactions should be false");
	} else { /* COMMIT */
		if (!$db->hasTransactions) Err("hasTransactions should be true");
		if ($db->transCnt != 1) Err("Invalid transCnt = $db->transCnt (should be 1)");
		$rs = $db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values (99,'Should Not','Exist (Commit)',$time)");
		if ($rs && $db->CommitTrans()) {
			$rs->Close();
			$rs = &$db->Execute("select * from ADOXYZ where id=99");
			if ($rs === false || $rs->EOF) {
				print '<b>Data not saved</b></p>';
				$rs = &$db->Execute("select * from ADOXYZ where id=99");
				print_r($rs);
				die();
			} else print 'OK</p>';
			if ($rs) $rs->Close();
		} else {
			if (!$rs) {
				print "<b>Insert failed</b></p>";
				$db->RollbackTrans();
			} else print "<b>Commit failed</b></p>";
		}
		if ($db->transCnt != 0) Err("Invalid transCnt = $db->transCnt (should be 0)");
		
		/* ROLLBACK */	
		if (!$db->BeginTrans()) print "<p><b>Error in BeginTrans</b>()</p>";
		print "<p>Testing Rollback: ";
		$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values (100,'Should Not','Exist (Rollback)',$time)");
		if ($db->RollbackTrans()) {
			$rs = $db->Execute("select * from ADOXYZ where id=100");
			if ($rs && !$rs->EOF) print '<b>Fail: Data should rollback</b></p>';
			else print 'OK</p>';
			if ($rs) $rs->Close();
		} else
			print "<b>Commit failed</b></p>";
			
		$rs = &$db->Execute('delete from ADOXYZ where id>50');
		if ($rs) $rs->Close();
		
		if ($db->transCnt != 0) Err("Invalid transCnt = $db->transCnt (should be 0)");
	}
	
	if (1) {
		print "<p>Testing MetaDatabases()</p>";
		print_r( $db->MetaDatabases());
		
		print "<p>Testing MetaTables() and MetaColumns()</p>";
		$a = $db->MetaTables();
		if ($a===false) print "<b>MetaTables not supported</b></p>";
		else {
			print "Array of tables: "; 
			foreach($a as $v) print " ($v) ";
			print '</p>';
		}
		$db->debug=1;
		$a = $db->MetaColumns('ADOXYZ');
		if ($a===false) print "<b>MetaColumns not supported</b></p>";
		else {
			print "<p>Columns of ADOXYZ: ";
			foreach($a as $v) print " ($v->name $v->type $v->max_length) ";
		}
		print "<p>Testing MetaPrimaryKeys</p>";
		$a = $db->MetaPrimaryKeys('ADOXYZ');
		print_r($a);
	}
	$rs = &$db->Execute('delete from ADOXYZ');
	if ($rs) $rs->Close();
	
	$db->debug = false;
	
	
	switch ($db->databaseType) {
	case 'postgres7':
	case 'postgres64':
	case 'postgres':
	case 'ibase':
		print "<p>Encode=".$db->BlobEncode("abc\0d\"'
ef")."</p>";
		break;
	case 'mssql': 
/*
ASSUME Northwind available...

CREATE PROCEDURE SalesByCategory
	@CategoryName nvarchar(15), @OrdYear nvarchar(4) = '1998'
AS
IF @OrdYear != '1996' AND @OrdYear != '1997' AND @OrdYear != '1998' 
BEGIN
	SELECT @OrdYear = '1998'
END

SELECT ProductName,
	TotalPurchase=ROUND(SUM(CONVERT(decimal(14,2), OD.Quantity * (1-OD.Discount) * OD.UnitPrice)), 0)
FROM [Order Details] OD, Orders O, Products P, Categories C
WHERE OD.OrderID = O.OrderID 
	AND OD.ProductID = P.ProductID 
	AND P.CategoryID = C.CategoryID
	AND C.CategoryName = @CategoryName
	AND SUBSTRING(CONVERT(nvarchar(22), O.OrderDate, 111), 1, 4) = @OrdYear
GROUP BY ProductName
ORDER BY ProductName
GO
*/
		print "<h4>Testing Stored Procedures for mssql</h4>";
		$saved = $db->debug;
		$db->debug=true;
		
		$cat = 'Dairy Products';
		$yr = '1998';
		
		$stmt = $db->PrepareSP('SalesByCategory');
		$db->Parameter($stmt,$cat,'CategoryName');
		$db->Parameter($stmt,$yr,'OrdYear');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		$cat = 'Grains/Cereals';
		$yr = 1998;
		
		$stmt = $db->PrepareSP('SalesByCategory');
		$db->Parameter($stmt,$cat,'CategoryName');
		$db->Parameter($stmt,$yr,'OrdYear');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		/*
		Test out params - works in 4.2.3 but not 4.3.0???:
		
			CREATE PROCEDURE at_date_interval 
				@days INTEGER, 
				@start VARCHAR(20) OUT, 
				@end VARCHAR(20) OUT 	
			AS 
			BEGIN 
				set @start = CONVERT(VARCHAR(20), getdate(), 101) 
				set @end =CONVERT(VARCHAR(20), dateadd(day, @days, getdate()), 101 ) 
			END
			GO
		*/
		$stmt = $db->PrepareSP('at_date_interval');
		$days = 10;
		$begin_date = '';
		$end_date = '';
		$db->Parameter($stmt,$days,'days', false, 4, SQLINT4); 
		$db->Parameter($stmt,$begin_date,'start', 1, 20, SQLVARCHAR ); 
		$db->Parameter($stmt,$end_date,'end', 1, 20, SQLVARCHAR ); 
		$db->Execute($stmt);
		if (empty($begin_date) or empty($end_date)) {
			Err("MSSQL SP Test for OUT Failed");
			print "begin=$begin_date end=$end_date<p>";
		} else print "(Today +10days) = (begin=$begin_date end=$end_date)<p>";
		$db->debug = $saved;
		break;
	case 'oci8': 
	case 'oci8po':
		$saved = $db->debug;
		$db->debug=true;
		
		print "<h4>Testing Cursor Variables</h4>";
/*
-- TEST PACKAGE
CREATE OR REPLACE PACKAGE adodb AS
TYPE TabType IS REF CURSOR RETURN tab%ROWTYPE;
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames in varchar);
END adodb;
/

CREATE OR REPLACE PACKAGE BODY adodb AS
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames in varchar) IS
	BEGIN
		OPEN tabcursor FOR SELECT * FROM tab where tname like tablenames;
	END open_tab;
END adodb;

/
*/		
		$stmt = $db->Prepare("BEGIN adodb.open_tab(:RS,'A%'); END;");
		$db->Parameter($stmt, $cur, 'RS', false, -1, OCI_B_CURSOR);
		$rs = $db->Execute($stmt);
	
		if ($rs && !$rs->EOF) {
			print "Test 1 RowCount: ".$rs->RecordCount()."<p>";
		} else {
			print "<b>Error in using Cursor Variables 1</b><p>";
		}
		
		$rs = $db->ExecuteCursor("BEGIN adodb.open_tab(:RS2,:TAB); END;",'RS2',array('TAB'=>'A%'));
		if ($rs && !$rs->EOF) {
			print "Test 2 RowCount: ".$rs->RecordCount()."<p>";
		} else {
			print "<b>Error in using Cursor Variables 2</b><p>";
		}
		
		print "<h4>Testing Stored Procedures for oci8</h4>";

		
		$tname = 'A%';
		
		$stmt = $db->PrepareSP('select * from tab where tname like :tablename');
		$db->Parameter($stmt,$tname,'tablename');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		$db->debug = $saved;
		break;
	
	default:
		break;
	}
	print "<p>Inserting 50 rows</p>";

	for ($i = 0; $i < 5; $i++) {	

	$time = $db->DBDate(time());
	if (empty($HTTP_GET_VARS['hide'])) $db->debug = true;
	switch($db->databaseType){
	default:
		$arr = array(0=>'Caroline',1=>'Miranda');
		$sql = "insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,?,?,$time)";
		break;
		
	case 'oci8':
	case 'oci805':
		$arr = array('first'=>'Caroline','last'=>'Miranda');
		$amt = rand() % 100;
		$sql = "insert into ADOXYZ (id,firstname,lastname,created,amount) values ($i*10+0,:first,:last,$time,$amt)";		
		break;
	}
	if ($i & 1) {
		$sql = $db->Prepare($sql);
	}
	$rs = $db->Execute($sql,$arr);
		
	if ($rs === false) Err( 'Error inserting with parameters');
	else $rs->Close();
	$db->debug = false;
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+1,'John','Lim',$time)");
	echo "Insert ID=";var_dump($db->Insert_ID());
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+2,'Mary','Lamb',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+3,'George','Washington',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+4,'Mr. Alan','Tam',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+5,'Alan',".$db->quote("Turing'ton").",$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created)values ($i*10+6,'Serena','Williams',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+7,'Yat Sun','Sun',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+8,'Wai Hun','See',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+9,'Steven','Oey',$time )");
	} // for
	if (1) {
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	//$db->debug=1;
	$rs = $db->Execute('update ADOXYZ set id=id+1');	
	if (!is_object($rs)) {
		print_r($rs);
		err("Update should return object");
	}
	if (!$rs) err("Update generated error");
	
	$nrows = $db->Affected_Rows();   
	if ($nrows === false) print "<p><b>Affected_Rows() not supported</b></p>";
	else if ($nrows != 50)  print "<p><b>Affected_Rows() Error: $nrows returned (should be 50) </b></p>";
	else print "<p>Affected_Rows() passed</p>";
	}
	$db->debug = false;
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
 //////////////////////////////////////////////////////////////////////////////////////////
	
	$rs = $db->Execute("select * from ADOXYZ where firstname = 'not known'");
	if (!$rs ||  !$rs->EOF) print "<p><b>Error on empty recordset</b></p>";
	else if ($rs->RecordCount() != 0) {
		print "<p><b>Error on RecordCount. Should be 0. Was ".$rs->RecordCount()."</b></p>"; 
		print_r($rs->fields);
	}
	$rs = &$db->Execute("select id,firstname,lastname,created from ADOXYZ order by id");
	if ($rs) {
		if ($rs->RecordCount() != 50) {
			print "<p><b>RecordCount returns ".$rs->RecordCount()."</b></p>";
			$poc = $rs->PO_RecordCount('ADOXYZ');
			if ($poc == 50) print "<p> &nbsp; &nbsp; PO_RecordCount passed</p>";
			else print "<p><b>PO_RecordCount returns wrong value: $poc</b></p>";
		} else print "<p>RecordCount() passed</p>";
		if (isset($rs->fields['firstname'])) print '<p>The fields columns can be indexed by column name.</p>';
		else {
			Err( '<p>The fields columns <i>cannot</i> be indexed by column name.</p>');
			print_r($rs->fields);
		}
		if (empty($HTTP_GET_VARS['hide'])) rs2html($rs);
	}
	else print "<b>Error in Execute of SELECT</b></p>";
	
	$val = $db->GetOne("select count(*) from ADOXYZ");
	 if ($val == 50) print "<p>GetOne returns ok</p>";
	 else print "<p><b>Fail: GetOne returns $val</b></p>";

	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	$val = $db->GetRow("select count(*) from ADOXYZ");
	 if ($val[0] == 50 and sizeof($val) == 1) print "<p>GetRow returns ok</p>";
	 else {
	 	print_r($val);
	 	print "<p><b>Fail: GetRow returns {$val[0]}</b></p>";
	}

	print "<p>FetchObject/FetchNextObject Test</p>";
	$rs = &$db->Execute('select * from ADOXYZ');
	
	if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
	
	while ($o = $rs->FetchNextObject()) { // calls FetchObject internally
		if (!is_string($o->FIRSTNAME) || !is_string($o->LASTNAME)) {
			print_r($o);
			print "<p><b>Firstname is not string</b></p>";
			break;
		}
	}
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	print "<p>FetchObject/FetchNextObject Test 2</p>";
	
	$rs = &$db->Execute('select * from ADOXYZ');
	if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
	print_r($rs->fields);
	while ($o = $rs->FetchNextObject()) { // calls FetchObject internally
		if (!is_string($o->FIRSTNAME) || !is_string($o->LASTNAME)) {
			print_r($o);
			print "<p><b>Firstname is not string</b></p>";
			break;
		}
	}
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
	$savefetch = $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	
	print "<p>CacheSelectLimit  Test</p>";
	$db->debug=1;
	$rs = $db->CacheSelectLimit('  select  id, firstname from  ADOXYZ order by id',2);
	if ($rs && !$rs->EOF) {
		if (isset($rs->fields[0])) {
			Err("ASSOC has numeric fields");
			print_r($rs->fields);
		}
		if ($rs->fields['id'] != 1)  {Err("Error"); print_r($rs->fields);};
		if (trim($rs->fields['firstname']) != 'Caroline')  {print Err("Error 2"); print_r($rs->fields);};
		$rs->MoveNext();
		if ($rs->fields['id'] != 2)  {Err("Error 3"); print_r($rs->fields);};
		$rs->MoveNext();
		if (!$rs->EOF) {
			Err("Error EOF");
			print_r($rs);
		}
	}
	
	print "<p>FETCH_MODE = ASSOC: Should get 1, Caroline</p>";
	$rs = &$db->SelectLimit('select id,firstname from ADOXYZ order by id',2);
	if ($rs && !$rs->EOF) {
		if ($rs->fields['id'] != 1)  {Err("Error 1"); print_r($rs->fields);};
		if (trim($rs->fields['firstname']) != 'Caroline')  {Err("Error 2"); print_r($rs->fields);};
		$rs->MoveNext();
		if ($rs->fields['id'] != 2)  {Err("Error 3"); print_r($rs->fields);};
		$rs->MoveNext();
		if (!$rs->EOF) Err("Error EOF");
		else if (is_array($rs->fields) || $rs->fields) {
			Err("Error: ## fields should be set to false on EOF");
			print_r($rs->fields);
		}
	}
	
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	print "<p>FETCH_MODE = NUM: Should get 1, Caroline</p>";
	$rs = &$db->SelectLimit('select id,firstname from ADOXYZ order by id',1);
	if ($rs && !$rs->EOF) {
		if (isset($rs->fields['id'])) Err("FETCH_NUM has ASSOC fields");
		if ($rs->fields[0] != 1)  {Err("Error 1"); print_r($rs->fields);};
		if (trim($rs->fields[1]) != 'Caroline')  {Err("Error 2");print_r($rs->fields);};
		$rs->MoveNext();
		if (!$rs->EOF) Err("Error EOF");

	}
	$ADODB_FETCH_MODE = $savefetch;
	
	$db->debug = false;
	print "<p>GetRowAssoc Upper: Should get 1, Caroline</p>";
	$rs = &$db->SelectLimit('select id,firstname from ADOXYZ order by id',1);
	if ($rs && !$rs->EOF) {
		$arr = &$rs->GetRowAssoc();
		if ($arr['ID'] != 1) {Err("Error 1");print_r($arr);};
		if (trim($arr['FIRSTNAME']) != 'Caroline') {Err("Error 2"); print_r($arr);};
		$rs->MoveNext();
		if (!$rs->EOF) Err("Error EOF");

	}
	print "<p>GetRowAssoc Lower: Should get 1, Caroline</p>";
	$rs = &$db->SelectLimit('select id,firstname from ADOXYZ order by id',1);
	if ($rs && !$rs->EOF) {
		$arr = &$rs->GetRowAssoc(false);
		if ($arr['id'] != 1) {Err("Error 1"); print_r($arr);};
		if (trim($arr['firstname']) != 'Caroline') {Err("Error 2"); print_r($arr);};

	}
	
	print "<p>GetCol Test</p>";
	$col = $db->GetCol('select distinct firstname from adoxyz order by 1');
	if (!is_array($col)) Err("Col size is wrong");
	if (trim($col[0]) != 'Alan' or trim($col[9]) != 'Yat Sun') Err("Col elements wrong");

	$db->debug = true;
	print "<p>SelectLimit Distinct Test 1: Should see Caroline, John and Mary</p>";
	$rs = &$db->SelectLimit('select distinct * from ADOXYZ order by id',3);
	$db->debug=false;

	if ($rs && !$rs->EOF) {
		if (trim($rs->fields[1]) != 'Caroline') Err("Error 1");
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'John') Err("Error 2");
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Mary') Err("Error 3");
		$rs->MoveNext();
		if (! $rs->EOF) Err("Error EOF");
		//rs2html($rs);
	} else Err("Failed SelectLimit Test 1");
	
	print "<p>SelectLimit Test 2: Should see Mary, George and Mr. Alan</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',3,2);
	if ($rs && !$rs->EOF) {
		if (trim($rs->fields[1]) != 'Mary') Err("Error 1");
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'George')Err("Error 2");
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Mr. Alan') Err("Error 3");
		$rs->MoveNext();
		if (! $rs->EOF) Err("Error EOF");
	//	rs2html($rs);
	}
	 else Err("Failed SelectLimit Test 2");
	
	print "<p>SelectLimit Test 3: Should see Wai Hun and Steven</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',-1,48);
	if ($rs && !$rs->EOF) {
		if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
		if (trim($rs->fields[1]) != 'Wai Hun') Err("Error 1");
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Steven') Err("Error 2");
		$rs->MoveNext();
		if (! $rs->EOF) {
			Err("Error EOF");
		}
		//rs2html($rs);
	}
	 else Err("Failed SelectLimit Test 3");
		$db->debug = false;
	
	
	$rs = &$db->Execute("select * from ADOXYZ order by id");
	print "<p>Testing Move()</p>";	
	if (!$rs)Err( "Failed Move SELECT");
	else {
		if (!$rs->Move(2)) {
			if (!$rs->canSeek) print "<p>$db->databaseType: <b>Move(), MoveFirst() nor MoveLast() not supported.</b></p>";
			else print '<p><b>RecordSet->canSeek property should be set to false</b></p>';
		} else {
			$rs->MoveFirst();
			if (trim($rs->Fields("firstname")) != 'Caroline') {
				print "<p><b>$db->databaseType: MoveFirst failed -- probably cannot scroll backwards</b></p>";
			}
			else print "MoveFirst() OK<BR>";
						
						// Move(3) tests error handling -- MoveFirst should not move cursor
			$rs->Move(3);
			if (trim($rs->Fields("firstname")) != 'George') {
				print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(3) failed</b></p>";
			} else print "Move(3) OK<BR>";
						
			$rs->Move(7);
			if (trim($rs->Fields("firstname")) != 'Yat Sun') {
				print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(7) failed</b></p>";
				print_r($rs);
			} else print "Move(7) OK<BR>";
			if ($rs->EOF) Err("Move(7) is EOF already");
			$rs->MoveLast();
			if (trim($rs->Fields("firstname")) != 'Steven'){
				 print '<p>'.$rs->Fields("id")."<b>$db->databaseType: MoveLast() failed</b></p>";
				 print_r($rs);
			}else print "MoveLast() OK<BR>";
			$rs->MoveNext();
			if (!$rs->EOF) err("Bad MoveNext");
			if ($rs->canSeek) {
				$rs->Move(3);
				if (trim($rs->Fields("firstname")) != 'George') {
					print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(3) after MoveLast failed</b></p>";
					
				} else print "Move(3) after MoveLast() OK<BR>";
			}
			
			print "<p>Empty Move Test";
			$rs = $db->Execute("select * from ADOXYZ where id > 0 and id < 0");
			$rs->MoveFirst();
			if (!$rs->EOF || $rs->fields) Err("Error in empty move first");
		}
	}
	
	$rs = $db->Execute('select * from ADOXYZ where id = 2');
	if ($rs->EOF || !is_array($rs->fields)) Err("Error in select");
	$rs->MoveNext();
	if (!$rs->EOF) Err("Error in EOF (xx) ");
 //	$db->debug=true;
	print "<p>Testing ADODB_FETCH_ASSOC and concat: concat firstname and lastname</p>";

	$save = $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	if ($db->dataProvider == 'postgres') {
		$sql = "select ".$db->Concat('cast(firstname as varchar)',$db->qstr(' '),'lastname')." as fullname,id from ADOXYZ";
		$rs = &$db->Execute($sql);
	} else {
		$sql = "select distinct ".$db->Concat('firstname',$db->qstr(' '),'lastname')." as fullname,id from ADOXYZ";
		$rs = &$db->Execute($sql);
	}
	if ($rs) {
		if (empty($HTTP_GET_VARS['hide'])) rs2html($rs);
	} else {
		Err( "Failed Concat:".$sql);
	}
	$ADODB_FETCH_MODE = $save;
	print "<hr>Testing GetArray() ";
	$rs = &$db->Execute("select * from ADOXYZ order by id");
	if ($rs) {
		$arr = &$rs->GetArray(10);
		if (sizeof($arr) != 10 || trim($arr[1][1]) != 'John' || trim($arr[1][2]) != 'Lim') print $arr[1][1].' '.$arr[1][2]."<b> &nbsp; ERROR</b><br>";
		else print " OK<BR>";
	}
	
	print "Testing FetchNextObject for 1 object ";
	$rs = &$db->Execute("select distinct lastname,firstname from ADOXYZ where firstname='Caroline'");
	$fcnt = 0;
	if ($rs)
	while ($o = $rs->FetchNextObject()) {
		$fcnt += 1;	
	}
	if ($fcnt == 1) print " OK<BR>";
	else print "<b>FAILED</b><BR>";
	
	print "Testing GetAssoc() ";
	$savecrecs = $ADODB_COUNTRECS;
	$ADODB_COUNTRECS = false;
	$rs = &$db->Execute("select distinct lastname,firstname from ADOXYZ");
	if ($rs) {
		$arr = $rs->GetAssoc();
		//print_r($arr);
		if (trim($arr['See']) != 'Wai Hun') print $arr['See']." &nbsp; <b>ERROR</b><br>";
		else print " OK<BR>";
	}
	// Comment this out to test countrecs = false
	$ADODB_COUNTRECS = $savecrecs;
	
	for ($loop=0; $loop < 1; $loop++) {
	print "Testing GetMenu() and CacheExecute<BR>";
	$db->debug = true;
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	
	if ($rs) print 'With blanks, Steven selected:'. $rs->GetMenu('menu','Steven').'<BR>'; 
	else print " Fail<BR>";
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	
	if ($rs) print ' No blanks, Steven selected: '. $rs->GetMenu('menu','Steven',false).'<BR>';
	else print " Fail<BR>";
	
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) print ' Multiple, Alan selected: '. $rs->GetMenu('menu','Alan',false,true).'<BR>';
	else print " Fail<BR>";
	print '</p><hr>';
	
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) {
		print ' Multiple, Alan and George selected: '. $rs->GetMenu('menu',array('Alan','George'),false,true);
		if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
	} else print " Fail<BR>";
	print '</p><hr>';
	
	print "Testing GetMenu2() <BR>";
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) print 'With blanks, Steven selected:'. $rs->GetMenu2('menu',('Oey')).'<BR>'; 
	else print " Fail<BR>";
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) print ' No blanks, Steven selected: '. $rs->GetMenu2('menu',('Oey'),false).'<BR>';
	else print " Fail<BR>";
	}
	
	$db->debug = false;
	$rs1 = &$db->Execute("select id from ADOXYZ where id <= 2 order by 1");
	$rs2 = &$db->Execute("select id from ADOXYZ where id = 3 or id = 4 order by 1");

	if ($rs1) $rs1->MoveLast();
	if ($rs2) $rs2->MoveLast();
	
	if (empty($rs1) || empty($rs2) || $rs1->fields[0] != 2 || $rs2->fields[0] != 4) {
		$a = $rs1->fields[0];
		$b = $rs2->fields[0];
		print "<p><b>Error in multiple recordset test rs1=$a rs2=$b (should be rs1=2 rs2=4)</b></p>";
	} else
		print "<p>Testing multiple recordsets OK</p>";
		
	
	echo "<p> GenID test: ";
	for ($i=1; $i <= 10; $i++) 
		echo  "($i: ",$val = $db->GenID('abcseq6' ,5), ") ";
	if ($val == 0) Err("GenID not supported");
	
	if ($val) {
		$db->DropSequence('abc_seq2');
		$db->CreateSequence('abc_seq2');
		$val = $db->GenID('abc_seq2');
		$db->DropSequence('abc_seq2');
		$db->CreateSequence('abc_seq2');
		$val = $db->GenID('abc_seq2');
		if ($val != 1) Err("Drop and Create Sequence not supported ($val)");
	}
	echo "<p>";
	
	if (substr($db->dataProvider,0,3) != 'notused') { // used to crash ado
		$sql = "select firstnames from adoxyz";
		print "<p>Testing execution of illegal statement: <i>$sql</i></p>";
		if ($db->Execute($sql) === false) {
			print "<p>This returns the following ErrorMsg(): <i>".$db->ErrorMsg()."</i> and ErrorNo(): ".$db->ErrorNo().'</p>';
		} else 
			print "<p><b>Error in error handling -- Execute() should return false</b></p>";
	} else 
		print "<p><b>ADO skipped error handling of bad select statement</b></p>";
	
	print "<p>ASSOC TEST 2<br>";
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $db->query('select * from adoxyz order by id');
	for($i=0;$i<$rs->FieldCount();$i++) 
	{ 
		$fld=$rs->FetchField($i); 
		print "<br> Field name is ".$fld->name; 
		print " ".$rs->Fields($fld->name); 
	} 

		
	print "<p>BOTH TEST 2<br>";
	if ($db->dataProvider == 'ado') {
		print "<b>ADODB_FETCH_BOTH not supported</b> for dataProvider=".$db->dataProvider."<br>";
	} else {
		$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		$rs = $db->query('select * from adoxyz order by id');
		for($i=0;$i<$rs->FieldCount();$i++) 
		{ 
			$fld=$rs->FetchField($i); 
			print "<br> Field name is ".$fld->name; 
			print " ".$rs->Fields($fld->name); 
		} 
	}
	
	print "<p>NUM TEST 2<br>";
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	$rs = $db->query('select * from adoxyz order by id');
	for($i=0;$i<$rs->FieldCount();$i++) 
	{ 
		$fld=$rs->FetchField($i); 
		print "<br> Field name is ".$fld->name; 
		print " ".$rs->Fields($fld->name); 
	} 
	
	print "<p>ASSOC Test of SelectLimit<br>";
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $db->selectlimit('select * from adoxyz order by id',3,4);
	$cnt = 0;
	while ($rs && !$rs->EOF) {
		$cnt += 1;
		if (!isset($rs->fields['firstname'])) {
			print "<br><b>ASSOC returned numeric field</b></p>";
			break;
		}
		$rs->MoveNext();
	}
	if ($cnt != 3) print "<br><b>Count should be 3, instead it was $cnt</b></p>";
	
	
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	if ($db->sysDate) {
		$saved = $db->debug;
		$db->debug = 1;
		$rs = $db->Execute("select {$db->sysDate} from adoxyz where id=1");
		if (ADORecordSet::UnixDate(date('Y-m-d')) != $rs->UnixDate($rs->fields[0])) {
			print "<p><b>Invalid date {$rs->fields[0]}</b></p>";
		} else
			print "<p>Passed \$sysDate test ({$rs->fields[0]})</p>";
		
		print_r($rs->FetchField(0));
		print time();
		$db->debug=$saved;
	} else {
		print "<p><b>\$db->sysDate not defined</b></p>";
	}

	print "<p>Test CSV</p>";
	include_once('../toexport.inc.php');
	//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $db->SelectLimit('select id,firstname,lastname,created,\'The	"young man", he said\' from adoxyz',10);	
	
	print "<pre>";
	print rs2csv($rs);
	print "</pre>";
	
	$rs = $db->SelectLimit('select id,firstname,lastname,created,\'The	"young man", he said\' from adoxyz',10);	
	
	print "<pre>";
	rs2tabout($rs);
	print "</pre>";
	
	//print " CacheFlush ";
	//$db->CacheFlush();
	
	$date = $db->SQLDate('d-m-Y-\QQ');
	$sql = "SELECT $date from ADOXYZ";
	print "<p>Test SQLDate: ".htmlspecialchars($sql)."</p>";
	$rs = $db->SelectLimit($sql,1);
	$d = date('d-m-Y-').'Q'.(ceil(date('m')/3.0));
	if ($d != $rs->fields[0]) Err("SQLDate failed expected: $d, sql:".$rs->fields[0]);
	
	print "<p>Test Filter</p>";
	$rs = $db->SelectLimit('select * from ADOXYZ where id < 3 order by id');
	$rs = RSFilter($rs,'do_strtolower');
	if (trim($rs->fields[1]) != 'caroline'  && trim($rs->fields[2]) != 'miranda') {
		err('**** RSFilter failed');
		print_r($rs->fields);
	}
	rs2html($rs);
		
	$db->debug=1;
	
	
	print "<p>Test Replace</p>";
	
	$ret = $db->Replace('adoxyz', 
		array('id'=>1,'firstname'=>'Caroline','lastname'=>'Miranda'),
		array('id'),
		$autoq = true);
	if (!$ret) echo "<p>Error in replacing existing record</p>";
	else {
		$saved = $db->debug;
		$db->debug = 0;
		$savec = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = true;
		$rs = $db->Execute('select * FROM ADOXYZ where id=1');
		$db->debug = $saved;
		if ($rs->RecordCount() != 1) {
			$cnt = $rs->RecordCount();
			rs2html($rs);
			print "<b>Error - Replace failed, count=$cnt</b><p>";
		}
		$ADODB_COUNTRECS = $savec;
	}
	$ret = $db->Replace('adoxyz', 
		array('id'=>1000,'firstname'=>'Harun','lastname'=>'Al-Rashid'),
		array('id','firstname'),
		$autoq = true);
	if ($ret != 2) print "<b>Replace failed: </b>";
	print "test A return value=$ret (2 expected) <p>";
	
	$ret = $db->Replace('adoxyz', 
		array('id'=>1000,'firstname'=>'Sherazade','lastname'=>'Al-Rashid'),
		'id',
		$autoq = true);
	if ($ret != 1) 
		if ($db->dataProvider == 'ibase' && $ret == 2);
		else print "<b>Replace failed: </b>";
	print "test B return value=$ret (1 or if ibase then 2 expected) <p>";
	
	print "<h3>rs2rs Test</h3>";
	
	$rs = $db->Execute('select * from adoxyz order by id');
	$rs = $db->_rs2rs($rs);
	$rs->valueX = 'X';
	$rs->MoveNext();
	$rs = $db->_rs2rs($rs);
	if (!isset($rs->valueX)) err("rs2rs does not preserve array recordsets");
	if (reset($rs->fields) != 1) err("rs2rs does not move to first row");

	/////////////////////////////////////////////////////////////
	include_once('../pivottable.inc.php');
	print "<h3>Pivot Test</h3>";
	$db->debug=true;
 	$sql = PivotTableSQL(
 		$db,  			# adodb connection
 		'adoxyz',  		# tables
		'firstname',	# row fields
		'lastname',		# column fields 
		false,			# join
		'ID' 			# sum
	);
	$rs = $db->Execute($sql);
	if ($rs) rs2html($rs);
	else Err("Pivot sql error");
	
	$db->debug=false;
	include_once "PEAR.php";
	
	// PEAR TESTS BELOW
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	$pear = true;
	$rs = $db->query('select * from adoxyz where id>0 and id<10 order by id');
	
	$i = 0;
	if ($rs && !$rs->EOF) {
		while ($arr = $rs->fetchRow()) {
			$i++;
			//print "$i ";
			if ($arr[0] != $i) {
				print_r($arr);
				print "<p><b>PEAR DB emulation error 1.</b></p>";
				$pear = false;
				break;
			}
		}
		$rs->Close();
	}
	
	
	if ($i != $db->GetOne('select count(*) from adoxyz where id>0 and id<10')) {
		print "<p><b>PEAR DB emulation error 1.1 EOF ($i)</b></p>";
		$pear = false;
	}
	
	$rs = $db->limitQuery('select * from adoxyz where id>0 order by id',$i=3,$top=3);
	$i2 = $i;
	if ($rs && !$rs->EOF) {

		while (!is_object($rs->fetchInto($arr))) {
			$i2++;
			
	//			print_r($arr);
	//		print "$i ";print_r($arr);
			if ($arr[0] != $i2) {
				print "<p><b>PEAR DB emulation error 2.</b></p>";
				$pear = false;
				break;
			}
		}
		$rs->Close();
	}
	if ($i2 != $i+$top) {
		print "<p><b>PEAR DB emulation error 2.1 EOF (correct=$i+$top, actual=$i2)</b></p>";
		$pear = false;
	}
	
	if ($pear) print "<p>PEAR DB emulation passed.</p>";
	

	if ($db->hasTransactions) {
		//$db->debug=1;
		echo "<p>Testing StartTrans CompleteTrans</p>";
		$db->raiseErrorFn = false;
		$db->StartTrans();
		$rs = $db->Execute('select * from notable');
			$db->StartTrans();
				$db->BeginTrans();
			$db->Execute("update ADOXYZ set firstname='Carolx' where id=1");
				$db->CommitTrans();
			$db->CompleteTrans();
		$rez = $db->CompleteTrans();
		if ($rez !== false) {
			if (is_null($rez)) Err("Error: _transOK not modified");
			else Err("Error: CompleteTrans (1) should have failed");
		} else {
			$name = $db->GetOne("Select firstname from ADOXYZ where id=1");
			if ($name == "Carolx") Err("Error: CompleteTrans (2) should have failed");
			else echo "<p> -- Passed StartTrans test1 - rolling back</p>";
		}
		
		$db->StartTrans();
			$db->BeginTrans();
		$db->Execute("update ADOXYZ set firstname='Carolx' where id=1");
			$db->RollbackTrans();
		$rez = $db->CompleteTrans();
		if ($rez !== true) Err("Error: CompleteTrans (1) should have succeeded");
		else {
			$name = $db->GetOne("Select firstname from ADOXYZ where id=1");
			if (trim($name) != "Carolx") Err("Error: CompleteTrans (2) should have succeeded, returned name=$name");
			else echo "<p> -- Passed StartTrans test2 - commiting</p>";
		}
	}
	global $TESTERRS;
	$debugerr = true;
		
	$db->debug = false;
	$TESTERRS = 0;
	$db->raiseErrorFn = 'adodb_test_err';
	global $ERRNO; // from adodb_test_err
	$db->Execute('select * from nowhere');
	$metae = $db->MetaError($ERRNO);
	if ($metae !== DB_ERROR_NOSUCHTABLE) print "<p><b>MetaError=".$metae." wrong</b>, should be ".DB_ERROR_NOSUCHTABLE."</p>";
	else print "<p>MetaError ok (".DB_ERROR_NOSUCHTABLE.")</p>";
	if ($TESTERRS != 1) print "<b>raiseErrorFn select nowhere failed</b><br>";
	$rs = $db->Execute('select * from adoxyz');
	if ($debugerr) print " Move";
	$rs->Move(100);
	$rs->_queryID = false;
	if ($debugerr) print " MoveNext";
	$rs->MoveNext();
	if ($debugerr) print " $rs=false";
	$rs = false;

	print "<p>SetFetchMode() tests</p>";
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	//	var_dump($rs->fields);
	if (!isset($rs->fields['firstname'])) Err("BAD FETCH ASSOC");
	
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;	
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	//var_dump($rs->fields);
	if (!isset($rs->fields['firstname'])) Err("BAD FETCH ASSOC");
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;	
	$db->SetFetchMode(ADODB_FETCH_NUM);
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	if (!isset($rs->fields[0])) Err("BAD FETCH NUM");
	
	print "<p>Test MetaTables again with SetFetchMode()</p>";
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	print_r($db->MetaTables());
	print "<p>";
	////////////////////////////////////////////////////////////////////
	
	$conn = NewADOConnection($db->databaseType);
	$conn->raiseErrorFn = 'adodb_test_err';
	@$conn->Connect('abc');
	if ($TESTERRS == 2) print "raiseErrorFn tests passed<br>";
	else print "<b>raiseErrorFn tests failed ($TESTERRS)</b><br>";
	
	
	////////////////////////////////////////////////////////////////////
	
	global $nocountrecs;
	
	if (isset($nocountrecs) && $ADODB_COUNTRECS) err("Error: \$ADODB_COUNTRECS is set");
	if (empty($nocountrecs) && $ADODB_COUNTRECS==false) err("Error: \$ADODB_COUNTRECS is not set");

	
?>
	</p>
	<table width=100% ><tr><td bgcolor=beige>&nbsp;</td></tr></table>
	</p></form>
<?php

	if ($rs1) $rs1->Close();
	if ($rs2) $rs2->Close();
	if ($rs) $rs->Close();
	$db->Close();
	
	if ($db->transCnt != 0) Err("Error in transCnt=$db->transCnt (should be 0)");
	
	
	printf("<p>Total queries=%d; total cached=%d</p>",$EXECS+$CACHED, $CACHED);
}

function adodb_test_err($dbms, $fn, $errno, $errmsg, $p1=false, $p2=false)
{
global $TESTERRS,$ERRNO;

	$ERRNO = $errno;
	$TESTERRS += 1;
	print "<i>** $dbms ($fn): errno=$errno &nbsp; errmsg=$errmsg ($p1,$p2)</i><br>";
	
}

//--------------------------------------------------------------------------------------


error_reporting(E_ALL);

set_time_limit(240); // increase timeout

include("../tohtml.inc.php");
include("../adodb.inc.php");
include("../rsfilter.inc.php");

/* White Space Check */
if (@$HTTP_SERVER_VARS['COMPUTERNAME'] == 'JAGUAR') {
	CheckWS('mysqlt');
	CheckWS('postgres');
	CheckWS('oci8po');
	CheckWS('firebird');
	CheckWS('sybase');
	CheckWS('informix');
	CheckWS('ado_mssql');
	CheckWS('ado_access');
	CheckWS('mssql');
	//
	CheckWS('vfp');
	CheckWS('sqlanywhere');
	CheckWS('db2');
	CheckWS('access');
	CheckWS('odbc_mssql');
	//
	CheckWS('oracle');
	CheckWS('proxy');
	CheckWS('fbsql');
	print "White Space Check complete<p>";
}
if (sizeof($HTTP_GET_VARS) == 0) $testmysql = true;


foreach($HTTP_GET_VARS as $k=>$v)  {
	global $$k;
		
	$$k = $v;
}	


?>
<html>
<title>ADODB Testing</title>
<body bgcolor=white>
<H1>ADODB Test</H1>

This script tests the following databases: Interbase, Oracle, Visual FoxPro, Microsoft Access (ODBC and ADO), MySQL, MSSQL (ODBC, native, ADO). 
There is also support for Sybase, PostgreSQL.</p>
For the latest version of ADODB, visit <a href=http://php.weblogs.com/ADODB>php.weblogs.com</a>.</p>

<form method=get>
<input type=checkbox name="testaccess" value=1 <?php echo !empty($testaccess) ? 'checked' : '' ?>> <b>Access</b><br>
<input type=checkbox name="testibase" value=1 <?php echo !empty($testibase) ? 'checked' : '' ?>> <b>Interbase</b><br>
<input type=checkbox name="testmssql" value=1 <?php echo !empty($testmssql) ? 'checked' : '' ?>> <b>MSSQL</b><br>
 <input type=checkbox name="testmysql" value=1 <?php echo !empty($testmysql) ? 'checked' : '' ?>> <b>MySQL</b><br>
<input type=checkbox name="testmysqlodbc" value=1 <?php echo !empty($testmysqlodbc) ? 'checked' : '' ?>> <b>MySQL ODBC</b><br>
<input type=checkbox name="testproxy" value=1 <?php echo !empty($testproxy) ? 'checked' : '' ?>> <b>MySQL Proxy</b><br>
<input type=checkbox name="testoracle" value=1 <?php echo !empty($testoracle) ? 'checked' : '' ?>> <b>Oracle (oci8)</b> <br>
<input type=checkbox name="testpostgres" value=1 <?php echo !empty($testpostgres) ? 'checked' : '' ?>> <b>PostgreSQL</b><br>
<input type=checkbox name="testvfp" value=1 <?php echo !empty($testvfp) ? 'checked' : '' ?>> VFP<br>
<input type=checkbox name="testado" value=1 <?php echo !empty($testado) ? 'checked' : '' ?>> ADO (for mssql and access)<br>
<input type=checkbox name="nocountrecs" value=1 <?php echo !empty($nocountrecs) ? 'checked' : '' ?>> $ADODB_COUNTRECS=false<br>
<input type=submit>
</form>

Test <a href=test4.php>GetInsertSQL/GetUpdateSQL</a> &nbsp; 
	<a href=testsessions.php>Sessions</a> &nbsp;
	<a href=testpaging.php>Paging</a> &nbsp;
<?php

if ($ADODB_FETCH_MODE != ADODB_FETCH_DEFAULT) print "<h3>FETCH MODE IS NOT ADODB_FETCH_DEFAULT</h3>";

if (isset($nocountrecs)) $ADODB_COUNTRECS = false;
include('./testdatabases.inc.php');


include_once('../adodb-time.inc.php');
adodb_date_test();
?>
<p><i>ADODB Database Library  (c) 2000-2003 John Lim. All rights reserved. Released under BSD and LGPL.</i></p>
</body>
</html>
