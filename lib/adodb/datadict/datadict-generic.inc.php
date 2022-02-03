<?php
/**
 * Generic Data Dictionary.
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

class ADODB2_generic extends ADODB_DataDict {

	var $databaseType = 'generic';
	var $seqField = false;


 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'VARCHAR(250)';

		case 'C2': return 'VARCHAR';
		case 'X2': return 'VARCHAR(250)';

		case 'B': return 'VARCHAR';

		case 'D': return 'DATE';
		case 'TS':
		case 'T': return 'DATE';

		case 'L': return 'DECIMAL(1)';
		case 'I': return 'DECIMAL(10)';
		case 'I1': return 'DECIMAL(3)';
		case 'I2': return 'DECIMAL(5)';
		case 'I4': return 'DECIMAL(10)';
		case 'I8': return 'DECIMAL(20)';

		case 'F': return 'DECIMAL(32,8)';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}

	function AlterColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported");
		return array();
	}


	function DropColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported");
		return array();
	}

}

/*
//db2
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return 'VARCHAR';

		case 'C2': return 'VARCHAR'; // up to 32K
		case 'X2': return 'VARCHAR';

		case 'B': return 'BLOB';

		case 'D': return 'DATE';
		case 'T': return 'TIMESTAMP';

		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'BIGINT';

		case 'F': return 'DOUBLE';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}

// ifx
function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';// 255
		case 'X': return 'TEXT';

		case 'C2': return 'NVARCHAR';
		case 'X2': return 'TEXT';

		case 'B': return 'BLOB';

		case 'D': return 'DATE';
		case 'T': return 'DATETIME';

		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'DECIMAL(20)';

		case 'F': return 'FLOAT';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}
*/
