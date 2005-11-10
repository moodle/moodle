<?php
/* 
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://adodb.sourceforge.net
*/

error_reporting(E_ALL);

$ADODB_FLUSH = true;

define('ADODB_ASSOC_CASE',0);


function getmicrotime()
{
	$t = microtime();
	$t = explode(' ',$t);
	return (float)$t[1]+ (float)$t[0];
}


if (PHP_VERSION < 5) include_once('../adodb-pear.inc.php');
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

	include_once('../session/adodb-session.php');
	if (defined('CHECKWSFAIL')){ echo " TESTING $conn ";flush();}
	$saved = $ADODB_EXTENSION;
	$db = ADONewConnection($conn);
	$ADODB_EXTENSION = $saved;
	if (headers_sent()) {
		print "<p><b>White space detected in adodb-$conn.inc.php or include file...</b></p>";
		//die();
	}
}

function do_strtolower(&$arr)
{
	foreach($arr as $k => $v) {
		if (is_object($v)) $arr[$k] = adodb_pr($v,true);
		else $arr[$k] = strtolower($v);
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
GLOBAL $ADODB_vers,$ADODB_CACHE_DIR,$ADODB_FETCH_MODE,$ADODB_COUNTRECS;

?>	<form method=GET>
	</p>
	<table width=100% ><tr><td bgcolor=beige>&nbsp;</td></tr></table>
	</p>
<?php  
	$create =false;
	/*$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
	$rs = $db->Execute('select lastname,firstname,lastname,id from adoxyz');
	$arr = $rs->GetAssoc();
	echo "<pre>";print_r($arr);
	die();*/
	
	GLOBAL $EXECS, $CACHED;
	
	$EXECS = 0;
	$CACHED = 0;
	//$db->Execute("drop table adodb_logsql");
	if ((rand()%3) == 0) @$db->Execute("delete from adodb_logsql");
	$db->debug=1;
	
	$db->fnExecute = 'CountExecs';
	$db->fnCacheExecute = 'CountCachedExecs';
	
	if (empty($_GET['nolog'])) {
		echo "<h3>SQL Logging enabled</h3>";
		$db->LogSQL();/*
		$sql =
"SELECT t1.sid, t1.sid, t1.title, t1.hometext, t1.notes, t1.aid, t1.informant, 
t2.url, t2.email, t1.catid, t3.title, t1.topic, t4.topicname, t4.topicimage, 
t4.topictext, t1.score, t1.ratings, t1.counter, t1.comments, t1.acomm 
FROM `nuke_stories` `t1`, `nuke_authors` `t2`, `nuke_stories_cat` `t3`, `nuke_topics` `t4` 
	WHERE ((t2.aid=t1.aid) AND (t3.catid=t1.catid) AND (t4.topicid=t1.topic) 
	AND ((t1.alanguage='german') OR (t1.alanguage='')) AND (t1.ihome='0')) 
	ORDER BY t1.time DESC";
		$db->SelectLimit($sql);
		echo $db->ErrorMsg();*/
	}
	$ADODB_CACHE_DIR = dirname(TempNam('/tmp','testadodb'));
	$db->debug = false;
	//print $db->UnixTimeStamp('2003-7-22 23:00:00');
	
	$phpv = phpversion();
	if (defined('ADODB_EXTENSION')) $ext = ' &nbsp; Extension '.ADODB_EXTENSION.' installed';
	else $ext = '';
	print "<h3>ADODB Version: $ADODB_vers Host: <i>$db->host</i> &nbsp; Database: <i>$db->database</i> &nbsp; PHP: $phpv $ext</h3>";
	flush();

	
	$arr = $db->ServerInfo();
	print_r($arr);
	echo "<br>";
	$e = error_reporting(E_ALL-E_WARNING);
	flush();
	
	$tt  = $db->Time(); 
	if ($tt == 0) echo '<br><b>$db->Time failed</b>';
	else echo "<br>db->Time: ".date('d-m-Y H:i:s',$tt);
	echo '<br>';
	
	echo "Date=",$db->UserDate('2002-04-07'),'<br>';
	print "<i>date1</i> (1969-02-20) = ".$db->DBDate('1969-2-20');
	print "<br><i>date1</i> (1999-02-20) = ".$db->DBDate('1999-2-20');
	print "<br><i>date1.1</i> 1999 = ".$db->DBDate("'1999'");
	print "<br><i>date2</i> (1970-1-2) = ".$db->DBDate(24*3600)."<p>";
	print "<i>ts1</i> (1999-02-20 13:40:50) = ".$db->DBTimeStamp('1999-2-20 1:40:50 pm');
	print "<br><i>ts1.1</i> (1999-02-20 13:40:00) = ".$db->DBTimeStamp('1999-2-20 13:40');
	print "<br><i>ts2</i> (1999-02-20) = ".$db->DBTimeStamp('1999-2-20');
	print "<br><i>ts3</i> (1970-1-2 +/- timezone) = ".$db->DBTimeStamp(24*3600);
	print "<br> Fractional TS (1999-2-20 13:40:50.91): ".$db->DBTimeStamp($db->UnixTimeStamp('1999-2-20 13:40:50.91+1'));
	 $dd = $db->UnixDate('1999-02-20');
	print "<br>unixdate</i> 1999-02-20 = ".date('Y-m-d',$dd)."<p>";
	print "<br><i>ts4</i> =".($db->UnixTimeStamp("19700101000101")+8*3600);
	print "<br><i>ts5</i> =".$db->DBTimeStamp($db->UnixTimeStamp("20040110092123"));
	print "<br><i>ts6</i> =".$db->UserTimeStamp("20040110092123");
	print "<br><i>ts7</i> =".$db->DBTimeStamp("20040110092123");
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
	if($rs === false) $create = true;
	else $rs->Close();
	
	//if ($db->databaseType !='vfp') $db->Execute("drop table ADOXYZ");
	
	if ($create) {
		if (false && $db->databaseType == 'ibase') {
			print "<b>Please create the following table for testing:</b></p>$createtab</p>";
			return;
		} else {
			$db->debug = 99;
			$e = error_reporting(E_ALL-E_WARNING);
			$db->Execute($createtab);
			error_reporting($e);
		}
	}
	
	echo "<p>Testing Metatypes</p>";
	$t = $db->MetaType('varchar');
	if ($t != 'C') Err("Bad Metatype for varchar");
	
	$rs = &$db->Execute("delete from ADOXYZ"); // some ODBC drivers will fail the drop so we delete
	if ($rs) {
		if(! $rs->EOF) print "<b>Error: </b>RecordSet returned by Execute('delete...') should show EOF</p>";
		$rs->Close();
	} else print "err=".$db->ErrorMsg();

	print "<p>Test select on empty table, FetchField when EOF, and GetInsertSQL</p>";
	$rs = &$db->Execute("select id,firstname from ADOXYZ where id=9999");
	if ($rs && !$rs->EOF) print "<b>Error: </b>RecordSet returned by Execute(select...') on empty table should show EOF</p>";
	if ($rs->EOF && (($ox = $rs->FetchField(0)) && !empty($ox->name))) {
		$record['id'] = 99;
		$record['firstname'] = 'John';
		$sql =  $db->GetInsertSQL($rs, $record);
		if (strtoupper($sql) != strtoupper("INSERT INTO ADOXYZ ( id, firstname ) VALUES ( 99, 'John' )")) Err("GetInsertSQL does not work on empty table: $sql");
	} else {
		Err("FetchField does not work on empty recordset, meaning GetInsertSQL will fail...");
	}
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
			print "Array of tables and views: "; 
			foreach($a as $v) print " ($v) ";
			print '</p>';
		}
		
		$a = $db->MetaTables('VIEW');
		if ($a===false) print "<b>MetaTables not supported (views)</b></p>";
		else {
			print "Array of views: "; 
			foreach($a as $v) print " ($v) ";
			print '</p>';
		}
		
		$a = $db->MetaTables(false,false,'aDo%');
		if ($a===false) print "<b>MetaTables not supported (mask)</b></p>";
		else {
			print "Array of ado%: "; 
			foreach($a as $v) print " ($v) ";
			print '</p>';
		}
		
		$a = $db->MetaTables('TABLE');
		if ($a===false) print "<b>MetaTables not supported</b></p>";
		else {
			print "Array of tables: "; 
			foreach($a as $v) print " ($v) ";
			print '</p>';
		}
		
		$db->debug=0;
		$rez = $db->MetaColumns("NOSUCHTABLEHERE");
		if ($rez !== false) {
			Err("MetaColumns error handling failed");
			var_dump($rez);
		}
		$db->debug=1;
		$a = $db->MetaColumns('ADOXYZ');
		if ($a===false) print "<b>MetaColumns not supported</b></p>";
		else {
			print "<p>Columns of ADOXYZ: <font size=1><br>";
			foreach($a as $v) {print_r($v); echo "<br>";}
			echo "</font>";
		}
		
		print "<p>Testing MetaIndexes</p>";
		
		$a = $db->MetaIndexes(('ADOXYZ'),true);
		if ($a===false) print "<b>MetaIndexes not supported</b></p>";
		else {
			print "<p>Indexes of ADOXYZ: <font size=1><br>";
			adodb_pr($a);
			echo "</font>";
		}
		print "<p>Testing MetaPrimaryKeys</p>";
		$a = $db->MetaPrimaryKeys('ADOXYZ');
		var_dump($a);
	}
	$rs = &$db->Execute('delete from ADOXYZ');
	if ($rs) $rs->Close();
	
	$db->debug = false;
	
	
	switch ($db->databaseType) {
	case 'vfp':
		
		if (0) {
			// memo test
			$rs = $db->Execute("select data from memo");
			rs2html($rs);
		}
		break;

	case 'postgres7':
	case 'postgres64':
	case 'postgres':
	case 'ibase':
		print "<p>Encode=".$db->BlobEncode("abc\0d\"'
ef")."</p>";//'

		print "<p>Testing Foreign Keys</p>";
		$arr = $db->MetaForeignKeys('adoxyz',false,true);
		print_r($arr);
		if (!$arr) Err("No MetaForeignKeys");
		break;
	
	case 'odbc_mssql':
	case 'mssqlpo':
		print "<p>Testing Foreign Keys</p>";
		$arr = $db->MetaForeignKeys('Orders',false,true);
		print_r($arr);
		if (!$arr) Err("Bad MetaForeignKeys");
		if ($db->databaseType == 'odbc_mssql') break;
	
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


CREATE PROCEDURE ADODBTestSP
@a nvarchar(25)
AS
SELECT GETDATE() AS T, @a AS A
GO
*/
		print "<h4>Testing Stored Procedures for mssql</h4>";
		$saved = $db->debug;
		$db->debug=true;
		
		$cmd = $db->PrepareSP('ADODBTestSP');
		$ss = "You should see me in the output.";
		$db->InParameter($cmd,$ss,'a');
		$rs = $db->Execute($cmd);
		echo $rs->fields['T']." --- ".$rs->fields['A']."---<br>";

		$cat = 'Dairy Products';
		$yr = '1998';
		
		$stmt = $db->PrepareSP('SalesByCategory');
		$db->InParameter($stmt,$cat,'CategoryName');
		$db->InParameter($stmt,$yr,'OrdYear');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		$cat = 'Grains/Cereals';
		$yr = 1998;
		
		$stmt = $db->PrepareSP('SalesByCategory');
		$db->InParameter($stmt,$cat,'CategoryName');
		$db->InParameter($stmt,$yr,'OrdYear');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		/*
		Test out params - works in PHP 4.2.3 and 4.3.3 and 4.3.8 but not 4.3.0:
		
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
		$db->debug=1;
		$stmt = $db->PrepareSP('at_date_interval');
		$days = 10;
		$begin_date = '';
		$end_date = '';
		$db->InParameter($stmt,$days,'days', 4, SQLINT4); 
		$db->OutParameter($stmt,$begin_date,'start', 20, SQLVARCHAR ); 
		$db->OutParameter($stmt,$end_date,'end', 20, SQLVARCHAR ); 
		$db->Execute($stmt);
		if (empty($begin_date) or empty($end_date) or $begin_date == $end_date) {
			Err("MSSQL SP Test for OUT Failed");
			print "begin=$begin_date end=$end_date<p>";
		} else print "(Today +10days) = (begin=$begin_date end=$end_date)<p>";
	
		$db->debug = $saved;
		break;
	case 'oci8': 
	case 'oci8po':
		
		if (0) {
		$t = getmicrotime();
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$arr = $db->GetArray('select * from abalone_tree');
		$arr = $db->GetArray('select * from abalone_tree');
		$arr = $db->GetArray('select * from abalone_tree');
		echo "<p>t = ",getmicrotime() - $t,"</p>";
		die();
		}
		
		# cleanup
		$db->Execute("delete from photos where id=99 or id=1");
		$db->Execute("insert into photos (id) values(1)");
		$db->Execute("update photos set photo=null,descclob=null where id=1");
		
		$saved = $db->debug;
		$db->debug=true;
		
		

		/*
		CREATE TABLE PHOTOS
		(
		  ID           NUMBER(16),
		  PHOTO        BLOB,
		  DESCRIPTION  VARCHAR2(4000 BYTE),
		  DESCCLOB     CLOB
		);
		
		INSERT INTO PHOTOS (ID) VALUES(1);
		*/
		$s = '';
		for ($i = 0; $i <= 500; $i++) {
			$s .= '1234567890';
		}
		
		$sql = "INSERT INTO photos ( ID, photo) ".
			"VALUES ( :id, empty_blob() )".
			" RETURNING photo INTO :xx";

		
		$blob_data = $s;
		$id = 99;
		
 		$stmt = $db->PrepareSP($sql);
		$db->InParameter($stmt, $id, 'id');
		$blob = $db->InParameter($stmt, $s, 'xx',-1, OCI_B_BLOB);
		$db->StartTrans();
		$result = $db->Execute($stmt);
		$db->CompleteTrans();
		
		$s2= $db->GetOne("select photo from photos where id=99");
		echo "<br>---$s2";
		if ($s !== $s2) Err("insert blob does not match");

		print "<h4>Testing Blob: size=".strlen($s)."</h4>";
		$ok = $db->Updateblob('photos','photo',$s,'id=1');
		if (!$ok) Err("Blob failed 1");
		else {
			$s2= $db->GetOne("select photo from photos where id=1");
			if ($s !== $s2) Err("updateblob does not match");
		}
		
		print "<h4>Testing Clob: size=".strlen($s)."</h4>";
		$ok = $db->UpdateClob('photos','descclob',$s,'id=1');
		if (!$ok) Err("Clob failed 1");
		else {
			$s2= $db->GetOne("select descclob from photos where id=1");
			if ($s !== $s2) Err("updateclob does not match");
		}
		
		
		$s = '';
		$s2 = '';
		print "<h4>Testing Foreign Keys</h4>";
		$arr = $db->MetaForeignKeys('emp','scott');
		print_r($arr);
		if (!$arr) Err("Bad MetaForeignKeys");
/*
-- TEST PACKAGE
-- "Set scan off" turns off substitution variables. 
Set scan off; 

CREATE OR REPLACE PACKAGE Adodb AS
TYPE TabType IS REF CURSOR RETURN TAB%ROWTYPE;
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames IN VARCHAR);
PROCEDURE open_tab2 (tabcursor IN OUT TabType,tablenames IN OUT VARCHAR) ;
PROCEDURE data_out(input IN VARCHAR, output OUT VARCHAR);
PROCEDURE data_in(input IN VARCHAR);
PROCEDURE myproc (p1 IN NUMBER, p2 OUT NUMBER);
END Adodb;
/


CREATE OR REPLACE PACKAGE BODY Adodb AS
PROCEDURE open_tab (tabcursor IN OUT TabType,tablenames IN VARCHAR) IS
	BEGIN
		OPEN tabcursor FOR SELECT * FROM TAB WHERE tname LIKE tablenames;
	END open_tab;

	PROCEDURE open_tab2 (tabcursor IN OUT TabType,tablenames IN OUT VARCHAR) IS
	BEGIN
		OPEN tabcursor FOR SELECT * FROM TAB WHERE tname LIKE tablenames;
		tablenames := 'TEST';
	END open_tab2;

PROCEDURE data_out(input IN VARCHAR, output OUT VARCHAR) IS
	BEGIN
		output := 'Cinta Hati '||input;
	END;
	
PROCEDURE data_in(input IN VARCHAR) IS
	ignore varchar(1000);
	BEGIN
		ignore := input;
	END;

PROCEDURE myproc (p1 IN NUMBER, p2 OUT NUMBER) AS
BEGIN
p2 := p1;
END;
END Adodb;
/

*/

		print "<h4>Testing Cursor Variables</h4>";
		$rs = $db->ExecuteCursor("BEGIN adodb.open_tab(:zz,'A%'); END;",'zz');
	
		if ($rs && !$rs->EOF) {
			$v = $db->GetOne("SELECT count(*) FROM tab where tname like 'A%'");
			if ($v == $rs->RecordCount()) print "Test 1 RowCount: OK<p>";
			else Err("Test 1 RowCount ".$rs->RecordCount().", actual = $v");
		} else {
			print "<b>Error in using Cursor Variables 1</b><p>";
		}
		$rs->Close();
		
		print "<h4>Testing Stored Procedures for oci8</h4>";
		
		$stmt = $db->PrepareSP("BEGIN adodb.data_out(:a1, :a2); END;");
		$a1 = 'Malaysia';
		//$a2 = ''; # a2 doesn't even need to be defined!
		$db->InParameter($stmt,$a1,'a1');
		$db->OutParameter($stmt,$a2,'a2');
		$rs = $db->Execute($stmt);
		if ($rs) {
			if ($a2 !== 'Cinta Hati Malaysia') print "<b>Stored Procedure Error: a2 = $a2</b><p>";
			else echo  "OK: a2=$a2<p>";
		} else {
			print "<b>Error in using Stored Procedure IN/Out Variables</b><p>";
		}
		
		$tname = 'A%';
		
		$stmt = $db->PrepareSP('select * from tab where tname like :tablename');
		$db->Parameter($stmt,$tname,'tablename');
		$rs = $db->Execute($stmt);
		rs2html($rs);
		
		$stmt = $db->PrepareSP("begin adodb.data_in(:a1); end;");
		$db->InParameter($stmt,$a1,'a1');
		$db->Execute($stmt);
		
		$db->debug = $saved;
		break;
	
	default:
		break;
	}
	$arr = array(
		array(1,'Caroline','Miranda'),
		array(2,'John','Lim'),
		array(3,'Wai Hun','See')
	);
	//$db->debug=1;
	print "<p>Testing Bulk Insert of 3 rows</p>";

	$sql = "insert into ADOXYZ (id,firstname,lastname) values (".$db->Param('0').",".$db->Param('1').",".$db->Param('2').")";
	$db->StartTrans();
	$db->Execute($sql,$arr);
	$db->CompleteTrans();
	$rs = $db->Execute('select * from ADOXYZ order by id');
	if (!$rs || $rs->RecordCount() != 3) Err("Bad bulk insert");
	
	rs2html($rs);
	
	$db->Execute('delete from ADOXYZ');
		
	print "<p>Inserting 50 rows</p>";

	for ($i = 0; $i < 5; $i++) {	

	$time = $db->DBDate(time());
	if (empty($_GET['hide'])) $db->debug = true;
	switch($db->databaseType){
	case 'mssqlpo':
	case 'mssql':
		$sqlt = "CREATE TABLE mytable (
  row1 INT  IDENTITY(1,1) NOT NULL,
  row2 varchar(16),
  PRIMARY KEY  (row1))";
  		//$db->debug=1;
  		if (!$db->Execute("delete from mytable")) 
			$db->Execute($sqlt);
			
		$ok = $db->Execute("insert into mytable (row2) values ('test')");
		$ins_id=$db->Insert_ID();
		echo "Insert ID=";var_dump($ins_id);
		if ($ins_id == 0) Err("Bad Insert_ID()");
		$ins_id2 = $db->GetOne("select row1 from mytable");
		if ($ins_id != $ins_id2) Err("Bad Insert_ID() 2");
		
		$arr = array(0=>'Caroline',1=>'Miranda');
		$sql = "insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,?,?,$time)";
		break;
	case 'mysqli':
	case 'mysqlt':
	case 'mysql':
		$sqlt = "CREATE TABLE `mytable` (
  `row1` int(11) NOT NULL auto_increment,
  `row2` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`row1`),
  KEY `myindex` (`row1`,`row2`)
) ";
		if (!$db->Execute("delete from mytable")) 
			$db->Execute($sqlt);
			
		$ok = $db->Execute("insert into mytable (row2) values ('test')");
		$ins_id=$db->Insert_ID();
		echo "Insert ID=";var_dump($ins_id);
		if ($ins_id == 0) Err("Bad Insert_ID()");
		$ins_id2 = $db->GetOne("select row1 from mytable");
		if ($ins_id != $ins_id2) Err("Bad Insert_ID() 2");
		
	default:
		$arr = array(0=>'Caroline',1=>'Miranda');
		$sql = "insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,?,?,$time)";
		break;
	
	case 'oci8':
	case 'oci805':
		$arr = array('first'=>'Caroline','last'=>'Miranda');
		$amt = rand() % 100;
		$sql = "insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,:first,:last,$time)";		
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
	/*$ins_id=$db->Insert_ID();
	echo "Insert ID=";var_dump($ins_id);*/
	if ($db->databaseType == 'mysql') if ($ins_id == 0) Err('Bad Insert_ID');
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
	$db->debug=1;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$cnt = $db->GetOne("select count(*) from ADOXYZ");
	$rs = $db->Execute('update ADOXYZ set id=id+1');	
	if (!is_object($rs)) {
		print_r($rs);
		err("Update should return object");
	} 
	if (!$rs) err("Update generated error");
	
	$nrows = $db->Affected_Rows();   
	if ($nrows === false) print "<p><b>Affected_Rows() not supported</b></p>";
	else if ($nrows != $cnt)  print "<p><b>Affected_Rows() Error: $nrows returned (should be 50) </b></p>";
	else print "<p>Affected_Rows() passed</p>";
	}
	
	/*if ($db->dataProvider == 'oci8') */ $array = array('zid'=>1,'zdate'=>date('Y-m-d',time()));
	/*else $array=array(1,date('Y-m-d',time()));*/
	
	$id = $db->GetOne("select id from ADOXYZ 
		where id=".$db->Param('zid')." and created>=".$db->Param('ZDATE')."",
		$array);
	if ($id != 1) Err("Bad bind; id=$id");
	else echo "<br>Bind date/integer passed";
	
	$db->debug = false;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
 //////////////////////////////////////////////////////////////////////////////////////////
	
	$rs = $db->Execute("select * from ADOXYZ where firstname = 'not known'");
	if (!$rs ||  !$rs->EOF) print "<p><b>Error on empty recordset</b></p>";
	else if ($rs->RecordCount() != 0) {
		print "<p><b>Error on RecordCount. Should be 0. Was ".$rs->RecordCount()."</b></p>"; 
		print_r($rs->fields);
	}
	if ($db->databaseType !== 'odbc') {
		$rs = &$db->Execute("select id,firstname,lastname,created,".$db->random." from ADOXYZ order by id");
		if ($rs) {
			if ($rs->RecordCount() != 50) {
				print "<p><b>RecordCount returns ".$rs->RecordCount().", should be 50</b></p>";
				adodb_pr($rs->GetArray());
				$poc = $rs->PO_RecordCount('ADOXYZ');
				if ($poc == 50) print "<p> &nbsp; &nbsp; PO_RecordCount passed</p>";
				else print "<p><b>PO_RecordCount returns wrong value: $poc</b></p>";
			} else print "<p>RecordCount() passed</p>";
			if (isset($rs->fields['firstname'])) print '<p>The fields columns can be indexed by column name.</p>';
			else {
				Err( '<p>The fields columns <i>cannot</i> be indexed by column name.</p>');
				print_r($rs->fields);
			}
			if (empty($_GET['hide'])) rs2html($rs);
		}
		else print "<p><b>Error in Execute of SELECT with random</b></p>";
	}
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
	$rs = $db->Execute('select * from ADOXYZ');
	if ($rs) {
		if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
		
		while ($o = $rs->FetchNextObject()) { // calls FetchObject internally
			if (!is_string($o->FIRSTNAME) || !is_string($o->LASTNAME)) {
				print_r($o);
				print "<p><b>Firstname is not string</b></p>";
				break;
			}
		}
	} else {
		print "<p><b>Failed rs</b></p>";
		die("<p>ADOXYZ table cannot be read - die()");
	}
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	print "<p>FetchObject/FetchNextObject Test 2</p>";
	#$db->debug=99;
	$rs = $db->Execute('select * from ADOXYZ');
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
		if (ADODB_ASSOC_CASE == 2) {
			$id = 'ID';
			$fname = 'FIRSTNAME';
		}else {
			$id = 'id';
			$fname = 'firstname';
		}
		if ($rs->fields[$id] != 1)  {Err("Error 1"); print_r($rs->fields);};
		if (trim($rs->fields[$fname]) != 'Caroline')  {Err("Error 2"); print_r($rs->fields);};
		$rs->MoveNext();
		if ($rs->fields[$id] != 2)  {Err("Error 3"); print_r($rs->fields);};
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
	
		
	echo "<p>Date Update Test</p>";
	$zdate = date('Y-m-d',time()+3600*24);
	$zdate = $db->DBDate($zdate);
	$db->Execute("update ADOXYZ set created=$zdate where id=1");
	$row = $db->GetRow("select created,firstname from ADOXYZ where id=1");
	print_r($row); echo "<br>";
	
	
	
	print "<p>SelectLimit Distinct Test 1: Should see Caroline, John and Mary</p>";
	$rs = &$db->SelectLimit('select distinct * from ADOXYZ order by id',3);
	
	
	if ($rs && !$rs->EOF) {
		if (trim($rs->fields[1]) != 'Caroline') Err("Error 1 (exp Caroline), ".$rs->fields[1]);
		$rs->MoveNext();
		
		if (trim($rs->fields[1]) != 'John') Err("Error 2 (exp John), ".$rs->fields[1]);
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Mary') Err("Error 3 (exp Mary),".$rs->fields[1]);
		$rs->MoveNext();
		if (! $rs->EOF) Err("Error EOF");
		//rs2html($rs);
	} else Err("Failed SelectLimit Test 1");
	
	print "<p>SelectLimit Test 2: Should see Mary, George and Mr. Alan</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',3,2);
	if ($rs && !$rs->EOF) {
		if (trim($rs->fields[1]) != 'Mary') Err("Error 1 - No Mary, instead: ".$rs->fields[1]);
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'George')Err("Error 2 - No George, instead: ".$rs->fields[1]);
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Mr. Alan') Err("Error 3 - No Mr. Alan, instead: ".$rs->fields[1]);
		$rs->MoveNext();
		if (! $rs->EOF) Err("Error EOF");
	//	rs2html($rs);
	}
	 else Err("Failed SelectLimit Test 2 ". ($rs ? 'EOF':'no RS'));
	
	print "<p>SelectLimit Test 3: Should see Wai Hun and Steven</p>";
	$db->debug=1;
	global $A; $A=1;
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',-1,48);
	$A=0;
	if ($rs && !$rs->EOF) {
		if (empty($rs->connection)) print "<b>Connection object missing from recordset</b></br>";
		if (trim($rs->fields[1]) != 'Wai Hun') Err("Error 1 ".$rs->fields[1]);
		$rs->MoveNext();
		if (trim($rs->fields[1]) != 'Steven') Err("Error 2 ".$rs->fields[1]);
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
		$sql = "select ".$db->Concat('cast(firstname as varchar)',$db->qstr(' '),'lastname')." as fullname,id,".$db->sysTimeStamp." as d from ADOXYZ";
		$rs = &$db->Execute($sql);
	} else {
		$sql = "select distinct ".$db->Concat('firstname',$db->qstr(' '),'lastname')." as fullname,id,".$db->sysTimeStamp." as d from ADOXYZ";
		$rs = &$db->Execute($sql);
	}
	if ($rs) {
		if (empty($_GET['hide'])) rs2html($rs);
	} else {
		Err( "Failed Concat:".$sql);
	}
	$ADODB_FETCH_MODE = $save;
	print "<hr>Testing GetArray() ";
	//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	
	$rs = &$db->Execute("select * from ADOXYZ order by id");
	if ($rs) {
		$arr = &$rs->GetArray(10);
		if (sizeof($arr) != 10 || trim($arr[1][1]) != 'John' || trim($arr[1][2]) != 'Lim') print $arr[1][1].' '.$arr[1][2]."<b> &nbsp; ERROR</b><br>";
		else print " OK<BR>";
	}
	
	$arr = $db->GetArray("select x from ADOXYZ");
	$e = $db->ErrorMsg(); $e2 = $db->ErrorNo();
	echo "Testing error handling, should see illegal column 'x' error=<i>$e ($e2) </i><br>";
	if (!$e || !$e2) Err("Error handling did not work");
	print "Testing FetchNextObject for 1 object ";
	$rs = &$db->Execute("select distinct lastname,firstname from ADOXYZ where firstname='Caroline'");
	$fcnt = 0;
	if ($rs)
	while ($o = $rs->FetchNextObject()) {
		$fcnt += 1;	
	}
	if ($fcnt == 1) print " OK<BR>";
	else print "<b>FAILED</b><BR>";
	
	$stmt = $db->Prepare("select * from ADOXYZ where id < 3");
	$rs = $db->Execute($stmt);
	if (!$rs) Err("Prepare failed");
	else {
		$arr = $rs->GetArray();
		if (!$arr) Err("Prepare failed 2");
		if (sizeof($arr) != 2) Err("Prepare failed 3");
	}
	print "Testing GetAssoc() ";
	$savecrecs = $ADODB_COUNTRECS;
	$ADODB_COUNTRECS = false;
	//$arr = $db->GetArray("select  lastname,firstname from ADOXYZ");
	//print_r($arr);
	print "<hr>";
	$rs =& $db->Execute("select distinct lastname,firstname,created from ADOXYZ");
	
	if ($rs) {
		$arr = $rs->GetAssoc();
		//print_r($arr);
		if (empty($arr['See']) || trim(reset($arr['See'])) != 'Wai Hun') print $arr['See']." &nbsp; <b>ERROR</b><br>";
		else print " OK 1";
	}
	
	$arr = &$db->GetAssoc("select distinct lastname,firstname from ADOXYZ");
	if ($arr) {
		//print_r($arr);
		if (empty($arr['See']) || trim($arr['See']) != 'Wai Hun') print $arr['See']." &nbsp; <b>ERROR</b><br>";
		else print " OK 2<BR>";
	}
	// Comment this out to test countrecs = false
	$ADODB_COUNTRECS = $savecrecs;
	$db->debug=1;
	$query = $db->Prepare("select count(*) from ADOXYZ");
	$rs = $db->CacheExecute(10,$query);
	if (reset($rs->fields) != 50) echo Err("$cnt wrong for Prepare/CacheGetOne");
	
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
	
	print "Testing GetMenu3()<br>";
	$rs = $db->Execute("select ".$db->Concat('firstname',"'-'",'id').",id, lastname from ADOXYZ order by lastname,id");
	if ($rs) print "Grouped Menu: ".$rs->GetMenu3('name');
	else Err('Grouped Menu GetMenu3()');
	print "<hr>";

	print "Testing GetMenu2() <BR>";
	$rs = &$db->CacheExecute(4,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) print 'With blanks, Steven selected:'. $rs->GetMenu2('menu',('Oey')).'<BR>'; 
	else print " Fail<BR>";
	$rs = &$db->CacheExecute(6,"select distinct firstname,lastname from ADOXYZ");
	if ($rs) print ' No blanks, Steven selected: '. $rs->GetMenu2('menu',('Oey'),false).'<BR>';
	else print " Fail<BR>";
	}
	echo "<h3>CacheEXecute</h3>";

	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	$rs = &$db->CacheExecute(6,"select distinct firstname,lastname from ADOXYZ");
	print_r($rs->fields); echo $rs->fetchMode;echo "<br>";
	echo $rs->Fields('firstname');
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = &$db->CacheExecute(6,"select distinct firstname,lastname from ADOXYZ");
	print_r($rs->fields);echo "<br>";
	echo $rs->Fields('firstname');
	$db->debug = false;
	
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	// phplens
	
	$sql = 'select * from ADOXYZ where 0=1';
	echo "<p>**Testing '$sql' (phplens compat 1)</p>";
	$rs = &$db->Execute($sql);
	if (!$rs) err( "<b>No recordset returned for '$sql'</b>");
	if (!$rs->FieldCount()) err( "<b>No fields returned for $sql</b>");
	if (!$rs->FetchField(1)) err( "<b>FetchField failed for $sql</b>");
	
	$sql = 'select * from ADOXYZ order by 1';
	echo "<p>**Testing '$sql' (phplens compat 2)</p>";
	$rs = &$db->Execute($sql);
	if (!$rs) err( "<b>No recordset returned for '$sql'<br>".$db->ErrorMsg()."</b>");
	
	
	$sql = 'select * from ADOXYZ order by 1,1';
	echo "<p>**Testing '$sql' (phplens compat 3)</p>";
	$rs = &$db->Execute($sql);
	if (!$rs) err( "<b>No recordset returned for '$sql'<br>".$db->ErrorMsg()."</b>");
	
	
	// Move
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
		echo  "($i: ",$val = $db->GenID($db->databaseType.'abcseq6' ,5), ") ";
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
	if ($ee = $db->ErrorMsg()) {
		Err("Error message=$ee");
	}
	if ($ee = $db->ErrorNo()) {
		Err("Error No = $ee");
	}
	print_r($rs->fields);
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
	$rs = $db->SelectLimit('select id,firstname,lastname,created,\'He, he\' he,\'"\' q  from adoxyz',10);	
	
	print "<pre>";
	print rs2csv($rs);
	print "</pre>";
	
	$rs = $db->SelectLimit('select id,firstname,lastname,created,\'The	"young man", he said\' from adoxyz',10);	
	
	if (PHP_VERSION < 5) {
		print "<pre>";
		rs2tabout($rs);
		print "</pre>";
	}
	//print " CacheFlush ";
	//$db->CacheFlush();
	$date = $db->SQLDate('d-m-M-Y-\QQ h:i:s A');
	$sql = "SELECT $date from ADOXYZ";
	print "<p>Test SQLDate: ".htmlspecialchars($sql)."</p>";
	$rs = $db->SelectLimit($sql,1);
	$d = date('d-m-M-Y-').'Q'.(ceil(date('m')/3.0)).date(' h:i:s A');
	if (!$rs) Err("SQLDate query returned no recordset");
	else if ($d != $rs->fields[0]) Err("SQLDate 1 failed expected: <br>act:$d <br>sql:".$rs->fields[0]);
	
	$date = $db->SQLDate('d-m-M-Y-\QQ h:i:s A',$db->DBDate("1974-02-25"));
	$sql = "SELECT $date from ADOXYZ";
	print "<p>Test SQLDate: ".htmlspecialchars($sql)."</p>";
	$rs = $db->SelectLimit($sql,1);
	$ts = ADOConnection::UnixDate('1974-02-25');
	$d = date('d-m-M-Y-',$ts).'Q'.(ceil(date('m',$ts)/3.0)).date(' h:i:s A',$ts);
	if (!$rs) {
		Err("SQLDate query returned no recordset");
		echo $db->ErrorMsg(),'<br>';
	} else if ($d != $rs->fields[0]) Err("SQLDate 2 failed expected: <br>act:$d <br>sql:".$rs->fields[0]);
	
	
	print "<p>Test Filter</p>";
	$db->debug = 1;
	
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
	
	$rs = $db->Execute('select * from adoxyz where id>= 1 order by id');
	$rs = $db->_rs2rs($rs);
	$rs->valueX = 'X';
	$rs->MoveNext();
	$rs = $db->_rs2rs($rs);
	if (!isset($rs->valueX)) err("rs2rs does not preserve array recordsets");
	if (reset($rs->fields) != 1) err("rs2rs does not move to first row: id=".reset($rs->fields));

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
		'ID', 			# sum
		'Sum ',			# label for sum
		'sum',			# aggregate function
		true
	);
	$rs = $db->Execute($sql);
	if ($rs) rs2html($rs);
	else Err("Pivot sql error");
	
	$pear = true; //true;
	$db->debug=false;
	
	if ($pear) {
	// PEAR TESTS BELOW
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
	include_once "PEAR.php";
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
	}
	if ($pear) print "<p>PEAR DB emulation passed.</p>";
	flush();

	
	$rs = $db->SelectLimit("select ".$db->sysDate." from adoxyz",1);
	$date = $rs->fields[0];
	if (!$date) Err("Bad sysDate");
	else {
		$ds = $db->UserDate($date,"d m Y");
		if ($ds != date("d m Y")) Err("Bad UserDate: ".$ds.' expected='.date("d m Y"));
		else echo "Passed UserDate: $ds<p>";
	}
	$db->debug=1;
	if ($db->dataProvider == 'oci8') 
		$rs = $db->SelectLimit("select to_char(".$db->sysTimeStamp.",'YYYY-MM-DD HH24:MI:SS') from adoxyz",1);
	else 
		$rs = $db->SelectLimit("select ".$db->sysTimeStamp." from adoxyz",1);
	$date = $rs->fields[0];
	if (!$date) Err("Bad sysTimeStamp");
	else {
		$ds = $db->UserTimeStamp($date,"H \\h\\r\\s-d m Y");
		if ($ds != date("H \\h\\r\\s-d m Y")) Err("Bad UserTimeStamp: ".$ds.", correct is ".date("H \\h\\r\\s-d m Y"));
		else echo "Passed UserTimeStamp: $ds<p>";
		
		$date = 100;
		$ds = $db->UserTimeStamp($date,"H \\h\\r\\s-d m Y");
		$ds2 = date("H \\h\\r\\s-d m Y",$date);
		if ($ds != $ds2) Err("Bad UserTimeStamp 2: $ds: $ds2");
		else echo "Passed UserTimeStamp 2: $ds<p>";
	}
	flush();
	
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
	flush();
	$saved = $db->debug;
	$db->debug=1;
	$cnt = _adodb_getcount($db, 'select * from ADOXYZ where firstname in (select firstname from ADOXYZ)');
	echo "<b>Count=</b> $cnt";
	$db->debug=$saved;
	
	global $TESTERRS;
	$debugerr = true;
	
	global $ADODB_LANG;$ADODB_LANG = 'fr';
	$db->debug = false;
	$TESTERRS = 0;
	$db->raiseErrorFn = 'adodb_test_err';
	global $ERRNO; // from adodb_test_err
	$db->Execute('select * from nowhere');
	$metae = $db->MetaError($ERRNO);
	if ($metae !== DB_ERROR_NOSUCHTABLE) print "<p><b>MetaError=".$metae." wrong</b>, should be ".DB_ERROR_NOSUCHTABLE."</p>";
	else print "<p>MetaError ok (".DB_ERROR_NOSUCHTABLE."): ".$db->MetaErrorMsg($metae)."</p>";
	if ($TESTERRS != 1) print "<b>raiseErrorFn select nowhere failed</b><br>";
	$rs = $db->Execute('select * from adoxyz');
	if ($debugerr) print " Move";
	$rs->Move(100);
	$rs->_queryID = false;
	if ($debugerr) print " MoveNext";
	$rs->MoveNext();
	if ($debugerr) print " $rs=false";
	$rs = false;

	flush();
	
	print "<p>SetFetchMode() tests</p>";
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	if (!isset($rs->fields['firstname'])) Err("BAD FETCH ASSOC");
	
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;	
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	//var_dump($rs->fields);
	if (!isset($rs->fields['firstname'])) Err("BAD FETCH ASSOC");
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;	
	$db->SetFetchMode(ADODB_FETCH_NUM);
	$rs = $db->SelectLimit('select firstname from adoxyz',1);
	if (!isset($rs->fields[0])) Err("BAD FETCH NUM");
	
	flush();
	
	print "<p>Test MetaTables again with SetFetchMode()</p>";
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	print_r($db->MetaTables());
	print "<p>";
	
	////////////////////////////////////////////////////////////////////
	
	print "<p>Testing Bad Connection</p>";
	flush();
	
	if (true || PHP_VERSION < 5)  {
		if ($db->dataProvider == 'odbtp') $db->databaseType = 'odbtp';
		$conn = NewADOConnection($db->databaseType);
		$conn->raiseErrorFn = 'adodb_test_err';
		if (1) $conn->PConnect('abc','baduser','badpassword');
		if ($TESTERRS == 2) print "raiseErrorFn tests passed<br>";
		else print "<b>raiseErrorFn tests failed ($TESTERRS)</b><br>";
		
		flush();
	}
	////////////////////////////////////////////////////////////////////
	
	global $nocountrecs;
	
	if (isset($nocountrecs) && $ADODB_COUNTRECS) err("Error: \$ADODB_COUNTRECS is set");
	if (empty($nocountrecs) && $ADODB_COUNTRECS==false) err("Error: \$ADODB_COUNTRECS is not set");

	flush();
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
	flush();
}

function adodb_test_err($dbms, $fn, $errno, $errmsg, $p1=false, $p2=false)
{
global $TESTERRS,$ERRNO;

	$ERRNO = $errno;
	$TESTERRS += 1;
	print "<i>** $dbms ($fn): errno=$errno &nbsp; errmsg=$errmsg ($p1,$p2)</i><br>";
}

//--------------------------------------------------------------------------------------


@set_time_limit(240); // increase timeout

include("../tohtml.inc.php");
include("../adodb.inc.php");
include("../rsfilter.inc.php");

/* White Space Check */

if (isset($_SERVER['argv'][1])) {
	//print_r($_SERVER['argv']);
	$_GET[$_SERVER['argv'][1]] = 1;
}

if (@$_SERVER['COMPUTERNAME'] == 'TIGRESS') {
	CheckWS('mysqlt');
	CheckWS('postgres');
	CheckWS('oci8po');
	
	CheckWS('firebird');
	CheckWS('sybase');
	if (!ini_get('safe_mode')) CheckWS('informix');

	CheckWS('ado_mssql');
	CheckWS('ado_access');
	CheckWS('mssql');
	
	CheckWS('vfp');
	CheckWS('sqlanywhere');
	CheckWS('db2');
	CheckWS('access');
	CheckWS('odbc_mssql');
	CheckWS('firebird15');
	//
	CheckWS('oracle');
	CheckWS('proxy');
	CheckWS('fbsql');
	print "White Space Check complete<p>";
}
if (sizeof($_GET) == 0) $testmysql = true;


foreach($_GET as $k=>$v)  {
	//global $$k;
	$$k = $v;
}	
if (strpos(PHP_VERSION,'5') === 0) {
	//$testaccess=1;
	//$testmssql = 1;
	//$testsqlite=1;
}
?>
<html>
<title>ADODB Testing</title>
<body bgcolor=white>
<H1>ADODB Test</H1>

This script tests the following databases: Interbase, Oracle, Visual FoxPro, Microsoft Access (ODBC and ADO), MySQL, MSSQL (ODBC, native, ADO). 
There is also support for Sybase, PostgreSQL.</p>
For the latest version of ADODB, visit <a href=http://adodb.sourceforge.net/>adodb.sourceforge.net</a>.</p>

Test <a href=test4.php>GetInsertSQL/GetUpdateSQL</a> &nbsp; 
	<a href=testsessions.php>Sessions</a> &nbsp;
	<a href=testpaging.php>Paging</a> &nbsp;
	<a href=test-perf.php>Perf Monitor</a><p>
<?php
include('./testdatabases.inc.php');

echo "<br>vers=",ADOConnection::Version();


include_once('../adodb-time.inc.php');
if (isset($_GET['time'])) adodb_date_test();

?>
<p><i>ADODB Database Library  (c) 2000-2005 John Lim. All rights reserved. Released under BSD and LGPL, PHP <?php echo PHP_VERSION ?>.</i></p>
</body>
</html>
