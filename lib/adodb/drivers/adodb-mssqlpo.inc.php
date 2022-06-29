<?php
/**
 * Portable MSSQL Driver that supports || instead of +.
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

include_once(ADODB_DIR.'/drivers/adodb-mssql.inc.php');

class ADODB_mssqlpo extends ADODB_mssql {
	var $databaseType = "mssqlpo";
	var $concat_operator = '||';

	function PrepareSP($sql, $param = true)
	{
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		$stmt = mssql_init($sql,$this->_connectionID);
		if (!$stmt)  return $sql;
		return array($sql,$stmt);
	}

	function _query($sql,$inputarr=false)
	{
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		return ADODB_mssql::_query($sql,$inputarr);
	}
}

class ADORecordset_mssqlpo extends ADORecordset_mssql {
	var $databaseType = "mssqlpo";
}
