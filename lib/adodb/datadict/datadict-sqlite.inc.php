<?php

/**
  @version   v5.20.9  21-Dec-2016
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Set tabs to 4 for best viewing.

	SQLite datadict Andrei Besleaga

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



	function ActualType($meta)
	{
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
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
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
