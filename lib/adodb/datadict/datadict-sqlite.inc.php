<?php
/**
 * Data Dictionary for SQLite.
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

class ADODB2_sqlite extends ADODB_DataDict {
	var $databaseType = 'sqlite';
	var $seqField = false;
	var $addCol=' ADD COLUMN';
	var $dropTable = 'DROP TABLE IF EXISTS %s';
	var $dropIndex = 'DROP INDEX IF EXISTS %s';
	var $renameTable = 'ALTER TABLE %s RENAME TO %s';

	public $blobAllowsDefaultValue = true;
	public $blobAllowsNotNull      = true;
    
	function ActualType($meta)
	{
		
		$meta = strtoupper($meta);
		
		/*
		* Add support for custom meta types. We do this
		* first, that allows us to override existing types
		*/
		if (isset($this->connection->customMetaTypes[$meta]))
			return $this->connection->customMetaTypes[$meta]['actual'];
		
		switch(strtoupper($meta)) {
		case 'C': return 'VARCHAR'; //  TEXT , TEXT affinity
		case 'XL':return 'LONGTEXT'; //  TEXT , TEXT affinity
		case 'X': return 'TEXT'; //  TEXT , TEXT affinity

		case 'C2': return 'VARCHAR'; //  TEXT , TEXT affinity
		case 'X2': return 'LONGTEXT'; //  TEXT , TEXT affinity

		case 'B': return 'LONGBLOB'; //  TEXT , NONE affinity , BLOB

		case 'D': return 'DATE'; // NUMERIC , NUMERIC affinity
		case 'T': return 'DATETIME'; // NUMERIC , NUMERIC affinity
		case 'L': return 'TINYINT'; // NUMERIC , INTEGER affinity

		case 'R':
		case 'I4':
		case 'I': return 'INTEGER'; // NUMERIC , INTEGER affinity
		case 'I1': return 'TINYINT'; // NUMERIC , INTEGER affinity
		case 'I2': return 'SMALLINT'; // NUMERIC , INTEGER affinity
		case 'I8': return 'BIGINT'; // NUMERIC , INTEGER affinity

		case 'F': return 'DOUBLE'; // NUMERIC , REAL affinity
		case 'N': return 'NUMERIC'; // NUMERIC , NUMERIC affinity
		default:
			return $meta;
		}
	}

	// return string must begin with space
	function _CreateSuffix($fname,&$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';
		if ($funsigned) $suffix .= ' UNSIGNED';
		if ($fnotnull) $suffix .= ' NOT NULL';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' AUTOINCREMENT';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

	function AlterColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported natively by SQLite");
		return array();
	}

	function DropColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported natively by SQLite");
		return array();
	}

	function RenameColumnSQL($tabname,$oldcolumn,$newcolumn,$flds='')
	{
		if ($this->debug) ADOConnection::outp("RenameColumnSQL not supported natively by SQLite");
		return array();
	}

}
