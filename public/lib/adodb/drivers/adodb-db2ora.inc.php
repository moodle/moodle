<?php
/**
 * IBM DB2 / Oracle compatibility driver.
 *
 * This driver provides undocumented bind variable mapping from ibm to oracle.
 * The functionality appears to overlap the db2_oci driver.
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
 * Callback function for preg_replace in _colonscope()
 * @param array $p matched patterns
 * return string '?' if parameter replaced, :N if not
 */
function _colontrack($p)
{
	global $_COLONARR, $_COLONSZ;
	$v = (integer) substr($p[1], 1);
	if ($v > $_COLONSZ) return $p[1];
	$_COLONARR[] = $v;
	return '?';
}

/**
 * smart remapping of :0, :1 bind vars to ? ?
 * @param string $sql SQL statement
 * @param array  $arr parameters
 * @return array
 */
function _colonscope($sql,$arr)
{
global $_COLONARR,$_COLONSZ;

	$_COLONARR = array();
	$_COLONSZ = sizeof($arr);

	$sql2 = preg_replace_callback('/(:[0-9]+)/', '_colontrack', $sql);

	if (empty($_COLONARR)) return array($sql,$arr);

	foreach($_COLONARR as $k => $v) {
		$arr2[] = $arr[$v];
	}

	return array($sql2,$arr2);
}

class ADODB_db2oci extends ADODB_db2 {
	var $databaseType = "db2oci";
	var $sysTimeStamp = 'sysdate';
	var $sysDate = 'trunc(sysdate)';

	function _Execute($sql, $inputarr = false)
	{
		if ($inputarr) list($sql,$inputarr) = _colonscope($sql, $inputarr);
		return parent::_Execute($sql, $inputarr);
	}
};


class  ADORecordSet_db2oci extends ADORecordSet_odbc {

	var $databaseType = "db2oci";

}

} //define
