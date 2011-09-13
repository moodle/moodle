<?php
/* 
V5.14 8 Sept 2011  (c) 2000-2011 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Microsoft Visual FoxPro data driver. Requires ODBC. Works only on MS Windows.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();
include(ADODB_DIR."/drivers/adodb-db2.inc.php");


if (!defined('ADODB_DB2OCI')){
define('ADODB_DB2OCI',1);

/*
// regex code for smart remapping of :0, :1 bind vars to ? ?
function _colontrack($p)
{
global $_COLONARR,$_COLONSZ;
	$v = (integer) substr($p,1);
	if ($v > $_COLONSZ) return $p;
	$_COLONARR[] = $v;
	return '?';
}

// smart remapping of :0, :1 bind vars to ? ?
function _colonscope($sql,$arr)
{
global $_COLONARR,$_COLONSZ;

	$_COLONARR = array();
	$_COLONSZ = sizeof($arr);
	
	$sql2 = preg_replace("/(:[0-9]+)/e","_colontrack('\\1')",$sql);
	
	if (empty($_COLONARR)) return array($sql,$arr);
	
	foreach($_COLONARR as $k => $v) {
		$arr2[] = $arr[$v]; 
	}
	
	return array($sql2,$arr2);
}
*/

/*
	Smart remapping of :0, :1 bind vars to ? ?
	
	Handles colons in comments -- and / * * / and in quoted strings.
*/

function _colonparser($sql,$arr)
{
	$lensql = strlen($sql);
	$arrsize = sizeof($arr);
	$state = 'NORM';
	$at = 1;
	$ch = $sql[0]; 
	$ch2 = @$sql[1];
	$sql2 = '';
	$arr2 = array();
	$nprev = 0;
	
	
	while (strlen($ch)) {
	
		switch($ch) {
		case '/':
			if ($state == 'NORM' && $ch2 == '*') {
				$state = 'COMMENT';
				
				$at += 1;
				$ch = $ch2;
				$ch2 = $at < $lensql ? $sql[$at] : '';
			}
			break;
			
		case '*':
			if ($state == 'COMMENT' && $ch2 == '/') {
				$state = 'NORM';
				
				$at += 1;
				$ch = $ch2;
				$ch2 = $at < $lensql ? $sql[$at] : '';
			}
			break;
		
		case "\n":
		case "\r":
			if ($state == 'COMMENT2') $state = 'NORM';
			break;
		
		case "'":
			do {
				$at += 1;
				$ch = $ch2;
				$ch2 = $at < $lensql ? $sql[$at] : '';
			} while ($ch !== "'");
			break;
			
		case ':':
			if ($state == 'COMMENT' || $state == 'COMMENT2') break;
			
			//echo "$at=$ch $ch2, ";
			if ('0' <= $ch2 && $ch2 <= '9') {
				$n = '';
				$nat = $at;
				do {
					$at += 1;
					$ch = $ch2;
					$n .= $ch;
					$ch2 = $at < $lensql ? $sql[$at] : '';
				} while ('0' <= $ch && $ch <= '9');
				#echo "$n $arrsize ] ";
				$n = (integer) $n;
				if ($n < $arrsize) {
					$sql2 .= substr($sql,$nprev,$nat-$nprev-1).'?';
					$nprev = $at-1;
					$arr2[] = $arr[$n];
				}
			}
			break;
			
		case '-':
			if ($state == 'NORM') {
				if ($ch2 == '-') $state = 'COMMENT2';
				$at += 1;
				$ch = $ch2;
				$ch2 = $at < $lensql ? $sql[$at] : '';
			}
			break;
		}
		
		$at += 1;
		$ch = $ch2;
		$ch2 = $at < $lensql ? $sql[$at] : '';
	}
	
	if ($nprev == 0) {
		$sql2 = $sql;
	} else {
		$sql2 .= substr($sql,$nprev);
	}
	
	return array($sql2,$arr2);
}

class ADODB_db2oci extends ADODB_db2 {
	var $databaseType = "db2oci";	
	var $sysTimeStamp = 'sysdate';
	var $sysDate = 'trunc(sysdate)';
	var $_bindInputArray = true;
	
	function ADODB_db2oci()
	{
		parent::ADODB_db2();
	}
	
	function Param($name,$type=false)
	{
		return ':'.$name;
	}
	
	
	function MetaTables($ttype=false,$schema=false)
	{
	global $ADODB_FETCH_MODE;
	
		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = db2_tables($this->_connectionID);
		
		$rs = new ADORecordSet_db2($qid);
		
		$ADODB_FETCH_MODE = $savem;
		if (!$rs) {
			$false = false;
			return $false;
		}
		
		$arr = $rs->GetArray();
		$rs->Close();
		$arr2 = array();
	//	adodb_pr($arr);
		if ($ttype) {
			$isview = strncmp($ttype,'V',1) === 0;
		}
		for ($i=0; $i < sizeof($arr); $i++) {
			if (!$arr[$i][2]) continue;
			$type = $arr[$i][3];
			$schemaval = ($schema) ? $arr[$i][1].'.' : '';
			$name = $schemaval.$arr[$i][2];
			$owner = $arr[$i][1];
			if (substr($name,0,8) == 'EXPLAIN_') continue;
			if ($ttype) { 
				if ($isview) {
					if (strncmp($type,'V',1) === 0) $arr2[] = $name;
				} else if (strncmp($type,'T',1) === 0 && strncmp($owner,'SYS',3) !== 0) $arr2[] = $name;
			} else if (strncmp($type,'T',1) === 0 && strncmp($owner,'SYS',3) !== 0) $arr2[] = $name;
		}
		return $arr2;
	}
	
	function _Execute($sql, $inputarr=false	)
	{
		if ($inputarr) list($sql,$inputarr) = _colonparser($sql, $inputarr);
		return parent::_Execute($sql, $inputarr);
	}
};
 

class  ADORecordSet_db2oci extends ADORecordSet_db2 {	
	
	var $databaseType = "db2oci";		
	
	function ADORecordSet_db2oci($id,$mode=false)
	{
		return $this->ADORecordSet_db2($id,$mode);
	}
}

} //define
?>