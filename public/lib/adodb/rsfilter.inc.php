<?php
/**
 * RecordSet Filter.
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

/*
	Filter all fields and all rows in a recordset and returns the
	processed recordset. We scroll to the beginning of the new recordset
	after processing.

	We pass a recordset and function name to RSFilter($rs,'rowfunc');
	and the function will be called multiple times, once
	for each row in the recordset. The function will be passed
	an array containing one row repeatedly.

	Example:

	// ucwords() every element in the recordset
	function do_ucwords(&$arr,$rs)
	{
		foreach($arr as $k => $v) {
			$arr[$k] = ucwords($v);
		}
	}
	$rs = RSFilter($rs,'do_ucwords');
 */
function RSFilter($rs,$fn)
{
	if ($rs->databaseType != 'array') {
		if (!$rs->connection) return false;

		$rs = $rs->connection->_rs2rs($rs);
	}
	$rows = $rs->RecordCount();
	for ($i=0; $i < $rows; $i++) {
		if (is_array ($fn)) {
        	$obj = $fn[0];
        	$method = $fn[1];
        	$obj->$method ($rs->_array[$i],$rs);
      } else {
			$fn($rs->_array[$i],$rs);
      }

	}
	if (!$rs->EOF) {
		$rs->_currentRow = 0;
		$rs->fields = $rs->_array[0];
	}

	return $rs;
}
