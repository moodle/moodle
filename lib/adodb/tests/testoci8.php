<html>
<body>
<?php
/* 
V1.81 22 March 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
    
  Latest version is available at http://php.weblogs.com/
*/
error_reporting(63);
include("../adodb.inc.php");
include("../tohtml.inc.php");

if (1) {
	$db = ADONewConnection('oci8po');
	$db->PConnect('','scott','tiger','natsoftmts');
	$db->debug = true;

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
	$db->PConnect('','scott','tiger');
	$db->debug = true;
	$db->Execute("delete from emp where ename='John'");
	print $db->Affected_Rows().'<BR>';
	$stmt = &$db->Prepare('insert into emp (empno, ename) values (:empno, :ename)');
	$rs = $db->Execute($stmt,array('empno'=>4321,'ename'=>'John'));
	// prepare not quite ready for prime time
	//$rs = $db->Execute($stmt,array('empno'=>3775,'ename'=>'John'));
	if (!$rs) die("Empty RS");
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