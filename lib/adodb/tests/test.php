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

//--------------------------------------------------------------------------------------

function Err($msg)
{
	print "<b>$msg</b><br>";
}

function CheckWS($conn)
{ 
	include_once('../adodb-session.php');
	$db = ADONewConnection($conn);
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
	$ADODB_CACHE_DIR = dirname(TempNam('/tmp','testadodb'));
		
	$db->debug = false;
	
	//print $db->UnixTimeStamp('2003-7-22 23:00:00');
	
	$phpv = phpversion();
	print "<h3>ADODB Version: $ADODB_vers Host: <i>$db->host</i> &nbsp; Database: <i>$db->database</i> &nbsp; PHP: $phpv</h3>";
	$e = error_reporting(E_ALL-E_WARNING);
	
	print "<i>date1</i> (1969-02-20) = ".$db->DBDate('1969-2-20');
	print "<br><i>date1</i> (1999-02-20) = ".$db->DBDate('1999-2-20');
	print "<br><i>date2</i> (1970-1-2) = ".$db->DBDate(24*3600)."<p>";
	print "<i>ts1</i> (1999-02-20 3:40:50) = ".$db->DBTimeStamp('1999-2-20 13:40:50');
	print "<br><i>ts2</i> (1999-02-20) = ".$db->DBTimeStamp('1999-2-20');
	print "<br><i>ts3</i> (1970-1-2 +/- timezone) = ".$db->DBTimeStamp(24*3600);
	print "<br> Fractional TS (1999-2-20 13:40:50.91): ".$db->DBTimeStamp($db->UnixTimeStamp('1999-2-20 13:40:50.91+1'));
	 $dd = $db->UnixDate('1999-02-20');
	print "<br>unixdate</i> 1999-02-20 = ".date('Y-m-d',$dd)."<p>";
	// mssql too slow in failing bad connection
	if ($db->databaseType != 'mssql') {
		print "<p>Testing bad connection. Ignore following error msgs:<br>";
		$db2 = ADONewConnection();
		$rez = $db2->Connect("bad connection");
		$err = $db2->ErrorMsg();
		print "<i>Error='$err'</i></p>";
		if ($rez) print "<b>Cannot check if connection failed.</b> The Connect() function returned true.</p>";
	}
	error_reporting($e);
	
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
		if ($db->databaseType == 'ibase') {
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
		if(! $rs->EOF)print "<b>Error: </b>RecordSet returned by Execute('delete...') should show EOF</p>";
		$rs->Close();
	} else print "err=".$db->ErrorMsg();
	
	print "<p>Test select on empty table</p>";
	$rs = &$db->Execute("select * from ADOXYZ where id=9999");
	if ($rs && !$rs->EOF) print "<b>Error: </b>RecordSet returned by Execute(select...') on empty table should show EOF</p>";
	if ($rs) $rs->Close();
	
	
	//$db->debug=true;	
	print "<p>Testing Commit: ";
	$time = $db->DBDate(time());
	if (!$db->BeginTrans()) print '<b>Transactions not supported</b></p>';
	else { /* COMMIT */
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
	
	if ($db->databaseType == 'mssql') {
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
		
		$db->debug = $saved;
	} else if (substr($db->databaseType,0,4) == 'oci8') {
		$saved = $db->debug;
		$db->debug=true;
		
		print "<h4>Testing Cursor Variables</h4>";
/*
-- TEST PACKAGE
CREATE or replace PACKAGE adodb AS
	TYPE TabType IS REF CURSOR RETURN tab%ROWTYPE;
	PROCEDURE open_tab (tab IN OUT TabType,param in varchar);
END adodb;
/

CREATE or replace PACKAGE BODY adodb AS
	PROCEDURE open_tab (tab IN OUT TabType,param in varchar) IS
		BEGIN
			OPEN tab FOR SELECT * FROM tab;
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
		
		$rs = $db->ExecuteCursor("BEGIN adodb.open_tab(:RS2,'A%'); END;",'RS2');
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
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+2,'Mary','Lamb',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+3,'George','Washington',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+4,'Mr. Alan','Tam',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+5,'Alan','Turing',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created)values ($i*10+6,'Serena','Williams',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+7,'Yat Sun','Sun',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+8,'Wai Hun','See',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+9,'Steven','Oey',$time )");
	} // for
	
	if (1) {
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	//$db->debug=1;
	$db->Execute('update ADOXYZ set id=id+1');	
   $nrows = $db->Affected_Rows();   
	if ($nrows === false) print "<p><b>Affected_Rows() not supported</b></p>";
	else if ($nrows != 50)  print "<p><b>Affected_Rows() Error: $nrows returned (should be 50) </b></p>";
	else print "<p>Affected_Rows() passed</p>";
	}
	$db->debug = false;

	$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
 //////////////////////////////////////////////////////////////////////////////////////////
	
	$rs = $db->Execute("select * from ADOXYZ where firstname = 'not known'");
	if (!$rs ||  !$rs->EOF) print "<p><b>Error on empty recordset</b></p>";
	if ($rs->RecordCount() != 0) print "<p><b>Error on RecordCount. Should be 0. Was ".$rs->RecordCount()."</b></p>"; 
	$rs = &$db->Execute("select id,firstname as TheFirstName,lastname,created from ADOXYZ order by id");
	if ($rs) {
		if ($rs->RecordCount() != 50) {
			print "<p><b>RecordCount returns -1</b></p>";
			if ($rs->PO_RecordCount('ADOXYZ') == 50) print "<p> &nbsp; &nbsp; PO_RecordCount passed</p>";
			else print "<p><b>PO_RecordCount returns wrong value</b></p>";
		} else print "<p>RecordCount() passed</p>";
		if (isset($rs->fields['firstname'])) print '<p>The fields columns can be indexed by column name.</p>';
		else print '<p>The fields columns <i>cannot</i> be indexed by column name.</p>';
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
		if (!$rs->EOF)Err("Error EOF");
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
				print_r($rs);
			} else print "Move(3) OK<BR>";
						
					$rs->Move(7);
			if (trim($rs->Fields("firstname")) != 'Yat Sun') {
				print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(7) failed</b></p>";
				print_r($rs);
			} else print "Move(7) OK<BR>";

			$rs->MoveLast();
			if (trim($rs->Fields("firstname")) != 'Steven'){
				 print '<p>'.$rs->Fields("id")."<b>$db->databaseType: MoveLast() failed</b></p>";
				 print_r($rs);
			}else print "MoveLast() OK<BR>";
		}
	}
	
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
	$rs = &$db->Execute("select distinct lastname,firstname from ADOXYZ");
	if ($rs) {
		$arr = $rs->GetAssoc();
		if (trim($arr['See']) != 'Wai Hun') print $arr['See']." &nbsp; <b>ERROR</b><br>";
		else print " OK<BR>";
	}
	
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
	$rs1 = &$db->Execute("select id from ADOXYZ where id = 2 or id = 1 order by 1");
	$rs2 = &$db->Execute("select id from ADOXYZ where id = 3 or id = 4 order by 1");
	
	if ($rs1) $rs1->MoveLast();
	if ($rs2) $rs2->MoveLast();
	
	if (empty($rs1) || empty($rs2) || $rs1->fields[0] != 2 || $rs2->fields[0] != 4) {
		$a = $rs1->fields[0];
		$b = $rs2->fields[0];
		print "<p><b>Error in multiple recordset test rs1=$a rs2=%b (should be rs1=2 rs2=4)</b></p>";
	} else
		print "<p>Testing multiple recordsets OK</p>";
		
	
	echo "<p> GenID test: ";
	for ($i=1; $i <= 10; $i++) 
		echo  "($i: ",$val = $db->GenID('abcseq5' ,5), ") ";
	if ($val == 0) echo " <p><b>GenID not supported</b>";
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
	$rs = $db->SelectLimit('select * from ADOXYZ where id < 3');
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
		$rs = $db->Execute('select * FROM ADOXYZ where id=1');
		$db->debug = $saved;
		if ($rs->RecordCount() != 1) print "<b>Error - update failed</b><p>";
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
	
	$db->Execute('select * from adoxyz');
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
	

	global $TESTERRS;
	$debugerr = true;
		
	$db->debug = false;
	$TESTERRS = 0;
	$db->raiseErrorFn = 'adodb_test_err';
	$db->Execute('select * from nowhere');
	if ($TESTERRS != 1) print "<b>raiseErrorFn select nowhere failed</b><br>";
	$rs = $db->Execute('select * from adoxyz');
	if ($debugerr) print " Move";
	$rs->Move(100);
	$rs->_queryID = false;
	if ($debugerr) print " MoveNext";
	$rs->MoveNext();
	
	if ($debugerr) print " $rs=false";
	$rs = false;
	$conn = NewADOConnection($db->databaseType);
	$conn->raiseErrorFn = 'adodb_test_err';
	@$conn->Connect('abc');
	if ($TESTERRS == 2) print "raiseErrorFn tests passed<br>";
	else print "<b>raiseErrorFn tests failed ($TESTERRS)</b><br>";
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
}

function adodb_test_err($dbms, $fn, $errno, $errmsg, $p1=false, $p2=false)
{
global $TESTERRS;

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
if (@$HTTP_SERVER_VARS['COMPUTERNAME'] == 'xJAGUAR') {
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
<input type=checkbox name="testaccess" value="1" <?php echo !empty($testaccess) ? 'checked' : '' ?>> Access<br>
<input type=checkbox name="testibase" value="1" <?php echo !empty($testibase) ? 'checked' : '' ?>> Interbase<br>
<input type=checkbox name="testmssql" value="1" <?php echo !empty($testmssql) ? 'checked' : '' ?>> MSSQL<br>
 <input type=checkbox name="testmysql" value="1" <?php echo !empty($testmysql) ? 'checked' : '' ?>> <b>MySQL</b><br>
<input type=checkbox name="testproxy" value=1 <?php echo !empty($testproxy) ? 'checked' : '' ?>> <b>MySQL Proxy</b><br>
<input type=checkbox name="testoracle" value="1" <?php echo !empty($testoracle) ? 'checked' : '' ?>> <b>Oracle (oci8)</b> <br>
<input type=checkbox name="testpostgres" value=1 <?php echo !empty($testpostgres) ? 'checked' : '' ?>> <b>PostgreSQL</b><br>
<input type=checkbox name="testvfp" value=1 <?php echo !empty($testvfp) ? 'checked' : '' ?>> VFP<br>
<input type=checkbox name="testado" value=1 <?php echo !empty($testado) ? 'checked' : '' ?>> ADO (for mssql and access)<br>
<input type=submit>
</form>

Test <a href=test4.php>GetInsertSQL/GetUpdateSQL</a> &nbsp; 
	<a href=testsessions.php>Sessions</a> &nbsp;
	<a href=testpaging.php>Paging</a> &nbsp;
<?php

if ($ADODB_FETCH_MODE != ADODB_FETCH_DEFAULT) print "<h3>FETCH MODE IS NOT ADODB_FETCH_DEFAULT</h3>";


include('./testdatabases.inc.php');
?>
<p><i>ADODB Database Library  (c) 2000-2002 John Lim. All rights reserved. Released under BSD and LGPL.</i></p>
</body>
</html>