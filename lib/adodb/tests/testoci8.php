<html>
<body>
<?php
/* 
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://adodb.sourceforge.net
*/
error_reporting(63);
include("../adodb.inc.php");
include("../tohtml.inc.php");

if (0) {
	$db = ADONewConnection('oci8po');
	
	$db->PConnect('','scott','natsoft');
	if (!empty($testblob)) {
		$varHoldingBlob = 'ABC DEF GEF John TEST';
		$num = time()%10240;
		// create table atable (id integer, ablob blob);
		$db->Execute('insert into ATABLE (id,ablob) values('.$num.',empty_blob())');
		$db->UpdateBlob('ATABLE', 'ablob', $varHoldingBlob, 'id='.$num, 'BLOB');
		
		$rs = &$db->Execute('select * from atable');
		
		if (!$rs) die("Empty RS");
		if ($rs->EOF) die("EOF RS");
		rs2html($rs);
	}
	$stmt = $db->Prepare('select * from adoxyz where id=?');
	for ($i = 1; $i <= 10; $i++) {
	$rs = &$db->Execute(
		$stmt,
		array($i));
			
		if (!$rs) die("Empty RS");
		if ($rs->EOF) die("EOF RS");
		rs2html($rs);
	}
}
if (1) {
	$db = ADONewConnection('oci8');
	$db->PConnect('','scott','natsoft');
	$db->debug = true;
	$db->Execute("delete from emp where ename='John'");
	print $db->Affected_Rows().'<BR>';
	$stmt = &$db->Prepare('insert into emp (empno, ename) values (:empno, :ename)');
	$rs = $db->Execute($stmt,array('empno'=>4321,'ename'=>'John'));
	// prepare not quite ready for prime time
	//$rs = $db->Execute($stmt,array('empno'=>3775,'ename'=>'John'));
	if (!$rs) die("Empty RS");
	
	$db->setfetchmode(ADODB_FETCH_NUM);
	
	$vv = 'A%';
	$stmt = $db->PrepareSP("BEGIN adodb.open_tab2(:rs,:tt); END;",true);
	$db->OutParameter($stmt, $cur, 'rs', -1, OCI_B_CURSOR);
	$db->OutParameter($stmt, $vv, 'tt');
	$rs = $db->Execute($stmt);
	while (!$rs->EOF) {
		adodb_pr($rs->fields);
		$rs->MoveNext();
	}
	echo " val = $vv";

}

if (0) {
	$db = ADONewConnection('odbc_oracle');
	if (!$db->PConnect('local_oracle','scott','tiger')) die('fail connect');
	$db->debug = true;
	$rs = &$db->Execute(
		'select * from adoxyz where firstname=? and trim(lastname)=?',
		array('first'=>'Caroline','last'=>'Miranda'));
	if (!$rs) die("Empty RS");
	if ($rs->EOF) die("EOF RS");
	rs2html($rs);
}
?>