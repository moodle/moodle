<?php
/**
 * IBM DB2 / Oracle compatibility driver.
 *
 * This driver re-maps ibm :0 bind variables to oracle compatible ? variables.
 *
 * @deprecated
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();
include_once(ADODB_DIR."/drivers/adodb-db2.inc.php");


if (!defined('ADODB_DB2OCI')){
define('ADODB_DB2OCI',1);

/**
 * Smart remapping of :0, :1 bind vars to ? ?
 * Handles colons in comments -- and / * * / and in quoted strings.
 * @param string $sql SQL statement
 * @param array  $arr parameters
 * @return array
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

	function Param($name,$type='C')
	{
		return ':'.$name;
	}


	function MetaTables($ttype = false, $schema = false, $mask = false)
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

}

} //define
