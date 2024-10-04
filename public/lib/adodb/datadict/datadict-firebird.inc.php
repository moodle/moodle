<?php
/**
 * Data Dictionary for Firebird.
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

class ADODB2_firebird extends ADODB_DataDict
{
	var $databaseType = 'firebird';
	var $seqField = false;
	var $seqPrefix = 's_';
	var $blobSize = 40000;
	var $renameColumn = 'ALTER TABLE %s ALTER %s TO %s';
	var $alterCol = ' ALTER';
	var $dropCol = ' DROP';

	function actualType($meta)
	{

		$meta = strtoupper($meta);

		// Add support for custom meta types.
		// We do this first, that allows us to override existing types
		if (isset($this->connection->customMetaTypes[$meta])) {
			return $this->connection->customMetaTypes[$meta]['actual'];
		}

		switch($meta) {
			case 'C':
				return 'VARCHAR';
			case 'XL':
				return 'BLOB SUB_TYPE BINARY';
			case 'X':
				return 'BLOB SUB_TYPE TEXT';

			case 'C2':
				return 'VARCHAR(32765)'; // up to 32K
			case 'X2':
				return 'VARCHAR(4096)';

			case 'V':
				return 'CHAR';
			case 'C1':
				return 'CHAR(1)';

			case 'B':
				return 'BLOB';

			case 'D':
				return 'DATE';
			case 'TS':
			case 'T':
				return 'TIMESTAMP';

			case 'L':
			case 'I1':
			case 'I2':
				return 'SMALLINT';
			case 'I':
			case 'I4':
				return 'INTEGER';
			case 'I8':
				return 'BIGINT';

			case 'F':
				return 'DOUBLE PRECISION';
			case 'N':
				return 'DECIMAL';
			default:
				return $meta;
		}
	}

	function nameQuote($name = null, $allowBrackets = false)
	{
		if (!is_string($name)) {
			return false;
		}

		$name = trim($name);

		if (!is_object($this->connection)) {
			return $name;
		}

		$quote = $this->connection->nameQuote;

		// if name is of the form `name`, quote it
		if (preg_match('/^`(.+)`$/', $name, $matches)) {
			return $quote . $matches[1] . $quote;
		}

		// if name contains special characters, quote it
		if (!preg_match('/^[' . $this->nameRegex . ']+$/', $name)) {
			return $quote . $name . $quote;
		}

		return $quote . $name . $quote;
	}

	function createDatabase($dbname, $options = false)
	{
		$sql = array();

		$sql[] = "DECLARE EXTERNAL FUNCTION LOWER CSTRING(80) RETURNS CSTRING(80) FREE_IT ENTRY_POINT 'IB_UDF_lower' MODULE_NAME 'ib_udf'";

		return $sql;
	}

	function _dropAutoIncrement($tabname)
	{
		if (strpos($tabname, '.') !== false) {
			$tarr = explode('.', $tabname);
			return 'DROP SEQUENCE ' . $tarr[0] . '."s_' . $tarr[1] . '"';
		}
		return 'DROP SEQUENCE s_' . $tabname;
	}


	function _createSuffix($fname, &$ftype, $fnotnull, $fdefault, $fautoinc, $fconstraint, $funsigned, $fprimary, &$pkey)
	{
		$suffix = '';

		if (strlen($fdefault)) {
			$suffix .= " DEFAULT $fdefault";
		}
		if ($fnotnull) {
			$suffix .= ' NOT NULL';
		}
		if ($fautoinc) {
			$this->seqField = $fname;
		}
		$fconstraint = preg_replace("/``/", "\"", $fconstraint);
		if ($fconstraint) {
			$suffix .= ' ' . $fconstraint;
		}

		return $suffix;
	}

	/**
	 * Generate the SQL to create table. Returns an array of sql strings.
	 */
	function createTableSQL($tabname, $flds, $tableoptions = array())
	{
		list($lines, $pkey, $idxs) = $this->_GenFields($flds, true);
		// genfields can return FALSE at times
		if ($lines == null) {
			$lines = array();
		}

		$taboptions = $this->_Options($tableoptions);
		$tabname = $this->TableName($tabname);
		$sql = $this->_TableSQL($tabname, $lines, $pkey, $taboptions);

		if ($this->autoIncrement && !isset($taboptions['DROP'])) {
			$tsql = $this->_Triggers($tabname, $taboptions);
			foreach ($tsql as $s) {
				$sql[] = $s;
			}
		}

		if (is_array($idxs)) {
			foreach ($idxs as $idx => $idxdef) {
				$sql_idxs = $this->CreateIndexSql($idx, $tabname, $idxdef['cols'], $idxdef['opts']);
				$sql = array_merge($sql, $sql_idxs);
			}
		}

		return $sql;
	}


	/*
	CREATE or replace TRIGGER jaddress_insert
	before insert on jaddress
	for each row
	begin
	IF ( NEW."seqField" IS NULL OR NEW."seqField" = 0 ) THEN
	  NEW."seqField" = GEN_ID("GEN_tabname", 1);
	end;
	*/
	function _triggers($tabname, $taboptions)
	{
		if (!$this->seqField) {
			return array();
		}

		$tab1 = preg_replace('/"/', '', $tabname);
		if ($this->schema) {
			$t = strpos($tab1, '.');
			if ($t !== false) {
				$tab = substr($tab1, $t + 1);
			} else {
				$tab = $tab1;
			}
			$seqField = $this->seqField;
			$seqname = $this->schema . '.' . $this->seqPrefix . $tab;
			$trigname = $this->schema . '.t_' . $this->seqPrefix . $tab;
		} else {
			$seqField = $this->seqField;
			$seqname = $this->seqPrefix . $tab1;
			$trigname = 't_' . $seqname;
		}

		if (isset($taboptions['DROP'])) {
			$sql[] = "DROP SEQUENCE $seqname";
		} elseif (isset($taboptions['REPLACE'])) {
			$sql[] = "DROP SEQUENCE \"$seqname\"";
			$sql[] = "CREATE SEQUENCE \"$seqname\"";
			$sql[] = "ALTER TRIGGER \"$trigname\" BEFORE INSERT OR UPDATE AS BEGIN IF ( NEW.$seqField IS NULL OR NEW.$seqField = 0 ) THEN NEW.$seqField = GEN_ID(\"$seqname\", 1); END";
		} else {
			$sql[] = "CREATE SEQUENCE $seqname";
			$sql[] = "CREATE TRIGGER $trigname FOR $tabname BEFORE INSERT OR UPDATE AS BEGIN IF ( NEW.$seqField IS NULL OR NEW.$seqField = 0 ) THEN NEW.$seqField = GEN_ID($seqname, 1); END";
		}

		$this->seqField = false;
		return $sql;
	}

	/**
	 * Change the definition of one column
	 *
	 * @param string $tabname table-name
	 * @param string $flds column-name and type for the changed column
	 * @param string $tableflds Unused
	 * @param array|string $tableoptions Unused
	 *
	 * @return array with SQL strings
	 */
	public function alterColumnSQL($tabname, $flds, $tableflds = '', $tableoptions = '')
	{
		$tabname = $this->TableName($tabname);
		$sql = array();
		list($lines, , $idxs) = $this->_GenFields($flds);
		// genfields can return FALSE at times

		if ($lines == null) {
			$lines = array();
		}

		$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';

		foreach ($lines as $v) {
			/*
			* The type must be preceded by the keyword 'TYPE'
			*/
			$vExplode = explode(' ', $v);
			$vExplode = array_filter($vExplode);
			array_splice($vExplode, 1, 0, array('TYPE'));
			$v = implode(' ', $vExplode);
			$sql[] = $alter . $v;
		}

		if (is_array($idxs)) {
			foreach ($idxs as $idx => $idxdef) {
				$sql_idxs = $this->CreateIndexSql($idx, $tabname, $idxdef['cols'], $idxdef['opts']);
				$sql = array_merge($sql, $sql_idxs);
			}

		}
		return $sql;
	}

}
