<?php

/**
  @version   v5.21.0  2021-02-27
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Set tabs to 4 for best viewing.

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB2_firebird extends ADODB_DataDict {

	var $databaseType = 'firebird';
	var $seqField = false;
	var $seqPrefix = 's_';
	var $blobSize = 40000;
	var $renameColumn = 'ALTER TABLE %s ALTER %s TO %s';
	var $alterCol = ' ALTER';
	var $dropCol = ' DROP';

	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'BLOB SUB_TYPE TEXT';

		case 'C2': return 'VARCHAR(32765)'; // up to 32K
		case 'X2': return 'VARCHAR(4096)';

		case 'V': return 'CHAR';
		case 'C1': return 'CHAR(1)';

		case 'B': return 'BLOB';

		case 'D': return 'DATE';
		case 'TS':
		case 'T': return 'TIMESTAMP';

		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'BIGINT';

		case 'F': return 'DOUBLE PRECISION';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}

	function NameQuote($name = NULL,$allowBrackets=false)
	{
		if (!is_string($name)) {
			return FALSE;
		}

		$name = trim($name);

		if ( !is_object($this->connection) ) {
			return $name;
		}

		$quote = $this->connection->nameQuote;

		// if name is of the form `name`, quote it
		if ( preg_match('/^`(.+)`$/', $name, $matches) ) {
			return $quote . $matches[1] . $quote;
		}

		// if name contains special characters, quote it
		if ( !preg_match('/^[' . $this->nameRegex . ']+$/', $name) ) {
			return $quote . $name . $quote;
		}

		return $quote . $name . $quote;
	}

	function CreateDatabase($dbname, $options=false)
	{
		$options = $this->_Options($options);
		$sql = array();

		$sql[] = "DECLARE EXTERNAL FUNCTION LOWER CSTRING(80) RETURNS CSTRING(80) FREE_IT ENTRY_POINT 'IB_UDF_lower' MODULE_NAME 'ib_udf'";

		return $sql;
	}

	function _DropAutoIncrement($t)
	{
		if (strpos($t,'.') !== false) {
			$tarr = explode('.',$t);
			return 'DROP GENERATOR '.$tarr[0].'."s_'.$tarr[1].'"';
		}
		return 'DROP GENERATOR s_'.$t;
	}


	function _CreateSuffix($fname,&$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';

		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fautoinc) $this->seqField = $fname;
		$fconstraint = preg_replace("/``/", "\"", $fconstraint);
		if ($fconstraint) $suffix .= ' '.$fconstraint;

		return $suffix;
	}

	/**
	 Generate the SQL to create table. Returns an array of sql strings.
	*/
	function CreateTableSQL($tabname, $flds, $tableoptions=array())
	{
		list($lines,$pkey,$idxs) = $this->_GenFields($flds, true);
		// genfields can return FALSE at times
		if ($lines == null) $lines = array();

		$taboptions = $this->_Options($tableoptions);
		$tabname = $this->TableName ($tabname);
		$sql = $this->_TableSQL($tabname,$lines,$pkey,$taboptions);

		if ($this->autoIncrement && !isset($taboptions['DROP']))
		{ $tsql = $this->_Triggers($tabname,$taboptions);
			foreach($tsql as $s) $sql[] = $s;
		}

		if (is_array($idxs)) {
			foreach($idxs as $idx => $idxdef) {
				$sql_idxs = $this->CreateIndexSql($idx, $tabname,  $idxdef['cols'], $idxdef['opts']);
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
	function _Triggers($tabname,$tableoptions)
	{
		if (!$this->seqField) return array();

		$tab1 = preg_replace( '/"/', '', $tabname );
		if ($this->schema) {
			$t = strpos($tab1,'.');
			if ($t !== false) $tab = substr($tab1,$t+1);
			else $tab = $tab1;
			$seqField = $this->seqField;
			$seqname = $this->schema.'.'.$this->seqPrefix.$tab;
			$trigname = $this->schema.'.t_'.$this->seqPrefix.$tab;
		} else {
			$seqField = $this->seqField;
			$seqname = $this->seqPrefix.$tab1;
			$trigname = 't_'.$seqname;
		}

		if (isset($tableoptions['DROP']))
		{ $sql[] = "DROP GENERATOR $seqname";
		}
		elseif (isset($tableoptions['REPLACE']))
		{ $sql[] = "DROP GENERATOR \"$seqname\"";
		  $sql[] = "CREATE GENERATOR \"$seqname\"";
		  $sql[] = "ALTER TRIGGER \"$trigname\" BEFORE INSERT OR UPDATE AS BEGIN IF ( NEW.$seqField IS NULL OR NEW.$seqField = 0 ) THEN NEW.$seqField = GEN_ID(\"$seqname\", 1); END";
		}
		else
		{ $sql[] = "CREATE GENERATOR $seqname";
		  $sql[] = "CREATE TRIGGER $trigname FOR $tabname BEFORE INSERT OR UPDATE AS BEGIN IF ( NEW.$seqField IS NULL OR NEW.$seqField = 0 ) THEN NEW.$seqField = GEN_ID($seqname, 1); END";
		}

		$this->seqField = false;
		return $sql;
	}

	/**
	 * Change the definition of one column
	 *
	 * As some DBM's can't do that on there own, you need to supply the complete definition of the new table,
	 * to allow, recreating the table and copying the content over to the new table
	 * @param string $tabname table-name
	 * @param string $flds column-name and type for the changed column
	 * @param string $tableflds='' complete definition of the new table, eg. for postgres, default ''
	 * @param array/string $tableoptions='' options for the new table see CreateTableSQL, default ''
	 * @return array with SQL strings
	 */
	function AlterColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey,$idxs) = $this->_GenFields($flds);
		// genfields can return FALSE at times
		if ($lines == null) $lines = array();
		$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
		foreach($lines as $v) {
			$sql[] = $alter . $v;
		}
		if (is_array($idxs)) {
			foreach($idxs as $idx => $idxdef) {
				$sql_idxs = $this->CreateIndexSql($idx, $tabname, $idxdef['cols'], $idxdef['opts']);
				$sql = array_merge($sql, $sql_idxs);
			}

		}
		return $sql;
	}

}
