<?php
/**
 * Data Dictionary for Oracle (oci8)
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

class ADODB2_oci8 extends ADODB_DataDict {

	var $databaseType = 'oci8';
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	var $dropTable = "DROP TABLE %s CASCADE CONSTRAINTS";
	var $trigPrefix = 'TRIG_';
	var $alterCol = ' MODIFY ';
	var $typeX = 'VARCHAR(4000)';
	var $typeXL = 'CLOB';
	
	/**
	 * Legacy compatibility for sequence names for emulated auto-increments.
	 *
	 * If set to true, creates sequences and triggers as TRIG_394545594
	 * instead of TRIG_possibly_too_long_tablename
	 *
	 * @var bool $useCompactAutoIncrements
	 */
	public $useCompactAutoIncrements = false;

	function metaType($t, $len=-1, $fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		
		$t = strtoupper($t);
		
		if (array_key_exists($t,$this->connection->customActualTypes))
			return  $this->connection->customActualTypes[$t];

		switch ($t) {
	 	case 'VARCHAR':
	 	case 'VARCHAR2':
		case 'CHAR':
		case 'VARBINARY':
		case 'BINARY':
			if (isset($this) && $len <= $this->blobSize) return 'C';
			return 'X';

		case 'NCHAR':
		case 'NVARCHAR2':
		case 'NVARCHAR':
			if (isset($this) && $len <= $this->blobSize) return 'C2';
			return 'X2';

		case 'NCLOB':
		case 'CLOB':
			return 'XL';

		case 'LONG RAW':
		case 'LONG VARBINARY':
		case 'BLOB':
			return 'B';

		case 'TIMESTAMP':
			return 'TS';

		case 'DATE':
			return 'T';

		case 'INT':
		case 'SMALLINT':
		case 'INTEGER':
			return 'I';

		default:
			return ADODB_DEFAULT_METATYPE;
		}
	}

 	function ActualType($meta)
	{
		$meta = strtoupper($meta);
		
		/*
		* Add support for custom meta types. We do this
		* first, that allows us to override existing types
		*/
		if (isset($this->connection->customMetaTypes[$meta]))
			return $this->connection->customMetaTypes[$meta]['actual'];
		
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return $this->typeX;
		case 'XL': return $this->typeXL;

		case 'C2': return 'NVARCHAR2';
		case 'X2': return 'NVARCHAR2(4000)';

		case 'B': return 'BLOB';

		case 'TS':
				return 'TIMESTAMP';

		case 'D':
		case 'T': return 'DATE';
		case 'L': return 'NUMBER(1)';
		case 'I1': return 'NUMBER(3)';
		case 'I2': return 'NUMBER(5)';
		case 'I':
		case 'I4': return 'NUMBER(10)';

		case 'I8': return 'NUMBER(20)';
		case 'F': return 'NUMBER';
		case 'N': return 'NUMBER';
		case 'R': return 'NUMBER(20)';
		default:
			return $meta;
		}
	}

	function CreateDatabase($dbname, $options=false)
	{
		$options = $this->_Options($options);
		$password = isset($options['PASSWORD']) ? $options['PASSWORD'] : 'tiger';
		$tablespace = isset($options["TABLESPACE"]) ? " DEFAULT TABLESPACE ".$options["TABLESPACE"] : '';
		$sql[] = "CREATE USER ".$dbname." IDENTIFIED BY ".$password.$tablespace;
		$sql[] = "GRANT CREATE SESSION, CREATE TABLE,UNLIMITED TABLESPACE,CREATE SEQUENCE TO $dbname";

		return $sql;
	}

	function AddColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName($tabname);
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname ADD (";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}

		$s .= implode(', ',$f).')';
		$sql[] = $s;
		return $sql;
	}

	function AlterColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		$tabname = $this->TableName($tabname);
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname MODIFY(";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}
		$s .= implode(', ',$f).')';
		$sql[] = $s;
		return $sql;
	}

	function DropColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		if (!is_array($flds)) $flds = explode(',',$flds);
		foreach ($flds as $k => $v) $flds[$k] = $this->NameQuote($v);

		$sql = array();
		$s = "ALTER TABLE $tabname DROP(";
		$s .= implode(', ',$flds).') CASCADE CONSTRAINTS';
		$sql[] = $s;
		return $sql;
	}

	function _DropAutoIncrement($t)
	{
		if (strpos($t,'.') !== false) {
			$tarr = explode('.',$t);
			return "drop sequence ".$tarr[0].".seq_".$tarr[1];
		}
		return "drop sequence seq_".$t;
	}

	// return string must begin with space
	function _CreateSuffix($fname,&$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';

		if ($fdefault == "''" && $fnotnull) {// this is null in oracle
			$fnotnull = false;
			if ($this->debug) ADOConnection::outp("NOT NULL and DEFAULT='' illegal in Oracle");
		}

		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';

		if ($fautoinc) $this->seqField = $fname;
		if ($fconstraint) $suffix .= ' '.$fconstraint;

		return $suffix;
	}

	/**
	 * Creates an insert trigger to emulate an auto-increment column
	 * in a table
	 *
	 * @param string   $tabname       The name of the table
	 * @param string[] $tableoptions  Optional configuration items
	 *
	 * @return string[] The SQL statements to create the trigger
	 */
	function _Triggers($tabname,$tableoptions)
	{
		
		if (!$this->seqField) return array();

		if ($this->schema) 
		{
			$t = strpos($tabname,'.');
			if ($t !== false) 
				$tab = substr($tabname,$t+1);
			else 
				$tab = $tabname;
			
			if ($this->connection->useCompactAutoIncrements)
				$id = sprintf('%u',crc32(strtolower($tab)));
			else
				$id = $tab;
			
			$seqname = $this->schema.'.'.$this->seqPrefix.$tab;
			$trigname = $this->schema.'.'.$this->trigPrefix.$this->seqPrefix.$tab;
			
		} 
		else 
		{
			if ($this->connection->useCompactAutoIncrements)
				$id = sprintf('%u',crc32(strtolower($tabname)));
			else
				$id = $tabname;
			
			$seqname = $this->seqPrefix.$id;
			$trigname = $this->trigPrefix.$id;
		}

		if (strlen($seqname) > 30) {
			$seqname = $this->seqPrefix.uniqid('');
		} // end if
		
		if (strlen($trigname) > 30) {
			$trigname = $this->trigPrefix.uniqid('');
		} // end if

		if (isset($tableoptions['REPLACE'])) $sql[] = "DROP SEQUENCE $seqname";
		$seqCache = '';
		if (isset($tableoptions['SEQUENCE_CACHE'])){$seqCache = $tableoptions['SEQUENCE_CACHE'];}
		$seqIncr = '';
		if (isset($tableoptions['SEQUENCE_INCREMENT'])){$seqIncr = ' INCREMENT BY '.$tableoptions['SEQUENCE_INCREMENT'];}
		$seqStart = '';
		if (isset($tableoptions['SEQUENCE_START'])){$seqStart = ' START WITH '.$tableoptions['SEQUENCE_START'];}
		$sql[] = "CREATE SEQUENCE $seqname MINVALUE 1 $seqStart $seqIncr $seqCache";
		$sql[] = "CREATE OR REPLACE TRIGGER $trigname BEFORE insert ON $tabname FOR EACH ROW WHEN (NEW.$this->seqField IS NULL OR NEW.$this->seqField = 0) BEGIN select $seqname.nextval into :new.$this->seqField from dual; END;";

		$this->seqField = false;
		return $sql;
	}

	/*
	CREATE [TEMPORARY] TABLE [IF NOT EXISTS] tbl_name [(create_definition,...)]
		[table_options] [select_statement]
		create_definition:
		col_name type [NOT NULL | NULL] [DEFAULT default_value] [AUTO_INCREMENT]
		[PRIMARY KEY] [reference_definition]
		or PRIMARY KEY (index_col_name,...)
		or KEY [index_name] (index_col_name,...)
		or INDEX [index_name] (index_col_name,...)
		or UNIQUE [INDEX] [index_name] (index_col_name,...)
		or FULLTEXT [INDEX] [index_name] (index_col_name,...)
		or [CONSTRAINT symbol] FOREIGN KEY [index_name] (index_col_name,...)
		[reference_definition]
		or CHECK (expr)
	*/



	function _IndexSQL($idxname, $tabname, $flds,$idxoptions)
	{
		$sql = array();

		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			$sql[] = sprintf ($this->dropIndex, $idxname, $tabname);
			if ( isset($idxoptions['DROP']) )
				return $sql;
		}

		if ( empty ($flds) ) {
			return $sql;
		}

		if (isset($idxoptions['BITMAP'])) {
			$unique = ' BITMAP';
		} elseif (isset($idxoptions['UNIQUE'])) {
			$unique = ' UNIQUE';
		} else {
			$unique = '';
		}

		if ( is_array($flds) )
			$flds = implode(', ',$flds);
		$s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' (' . $flds . ')';

		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];

		if (isset($idxoptions['oci8']))
			$s .= $idxoptions['oci8'];


		$sql[] = $s;

		return $sql;
	}

	function GetCommentSQL($table,$col)
	{
		$table = $this->connection->qstr($table);
		$col = $this->connection->qstr($col);
		return "select comments from USER_COL_COMMENTS where TABLE_NAME=$table and COLUMN_NAME=$col";
	}

	function SetCommentSQL($table,$col,$cmt)
	{
		$cmt = $this->connection->qstr($cmt);
		return  "COMMENT ON COLUMN $table.$col IS $cmt";
	}
}
