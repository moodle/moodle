<?php

/**
  V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_postgres extends ADODB_DataDict {
	
	var $databaseType = 'postgres';
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	var $addCol = ' ADD COLUMN';
	var $quote = '"';
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
			case 'INTERVAL':
			case 'CHAR':
			case 'CHARACTER':
			case 'VARCHAR':
			case 'NAME':
	   		case 'BPCHAR':
				if ($len <= $this->blobSize) return 'C';
			
			case 'TEXT':
				return 'X';
	
			case 'IMAGE': // user defined type
			case 'BLOB': // user defined type
			case 'BIT':	// This is a bit string, not a single bit, so don't return 'L'
			case 'VARBIT':
			case 'BYTEA':
				return 'B';
			
			case 'BOOL':
			case 'BOOLEAN':
				return 'L';
			
			case 'DATE':
				return 'D';
			
			case 'TIME':
			case 'DATETIME':
			case 'TIMESTAMP':
			case 'TIMESTAMPTZ':
				return 'T';
			
			case 'INTEGER': return (empty($fieldobj->primary_key) && empty($fieldobj->unique))? 'I' : 'R';
			case 'SMALLINT': 
			case 'INT2': return (empty($fieldobj->primary_key) && empty($fieldobj->unique))? 'I2' : 'R';
			case 'INT4': return (empty($fieldobj->primary_key) && empty($fieldobj->unique))? 'I4' : 'R';
			case 'BIGINT': 
			case 'INT8': return (empty($fieldobj->primary_key) && empty($fieldobj->unique))? 'I8' : 'R';
				
			case 'OID':
			case 'SERIAL':
				return 'R';
			
			case 'FLOAT4':
			case 'FLOAT8':
			case 'DOUBLE PRECISION':
			case 'REAL':
				return 'F';
				
			 default:
			 	return 'N';
		}
	}
 	
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'TEXT';
		
		case 'C2': return 'VARCHAR';
		case 'X2': return 'TEXT';
		
		case 'B': return 'BYTEA';
			
		case 'D': return 'DATE';
		case 'T': return 'TIMESTAMP';
		
		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'INT2';
		case 'I4': return 'INT4';
		case 'I8': return 'INT8';
		
		case 'F': return 'FLOAT8';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}
	
	/* The following does not work in Pg 6.0 - does anyone want to contribute code? 
	
	//"ALTER TABLE table ALTER COLUMN column SET DEFAULT mydef" and
	//"ALTER TABLE table ALTER COLUMN column DROP DEFAULT mydef"
	//"ALTER TABLE table ALTER COLUMN column SET NOT NULL" and
	//"ALTER TABLE table ALTER COLUMN column DROP NOT NULL"*/
	function AlterColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported for PostgreSQL");
		return array();
	}
	
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL only works with PostgreSQL 7.3+");
		return ADODB_DataDict::DropColumnSQL($tabname, $flds)."/* only works for PostgreSQL 7.3+ */";
	}

	// return string must begin with space
	function _CreateSuffix($fname, &$ftype, $fnotnull,$fdefault,$fautoinc,$fconstraint)
	{
		if ($fautoinc) {
			$ftype = 'SERIAL';
			return '';
		}
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}
	
	function _DropAutoIncrement($t)
	{
		return "drop sequence ".$t."_m_id_seq";
	}
	
	/*
	CREATE [ [ LOCAL ] { TEMPORARY | TEMP } ] TABLE table_name (
	{ column_name data_type [ DEFAULT default_expr ] [ column_constraint [, ... ] ]
	| table_constraint } [, ... ]
	)
	[ INHERITS ( parent_table [, ... ] ) ]
	[ WITH OIDS | WITHOUT OIDS ]
	where column_constraint is:
	[ CONSTRAINT constraint_name ]
	{ NOT NULL | NULL | UNIQUE | PRIMARY KEY |
	CHECK (expression) |
	REFERENCES reftable [ ( refcolumn ) ] [ MATCH FULL | MATCH PARTIAL ]
	[ ON DELETE action ] [ ON UPDATE action ] }
	[ DEFERRABLE | NOT DEFERRABLE ] [ INITIALLY DEFERRED | INITIALLY IMMEDIATE ]
	and table_constraint is:
	[ CONSTRAINT constraint_name ]
	{ UNIQUE ( column_name [, ... ] ) |
	PRIMARY KEY ( column_name [, ... ] ) |
	CHECK ( expression ) |
	FOREIGN KEY ( column_name [, ... ] ) REFERENCES reftable [ ( refcolumn [, ... ] ) ]
	[ MATCH FULL | MATCH PARTIAL ] [ ON DELETE action ] [ ON UPDATE action ] }
	[ DEFERRABLE | NOT DEFERRABLE ] [ INITIALLY DEFERRED | INITIALLY IMMEDIATE ]
	*/
	
	
	/*
	CREATE [ UNIQUE ] INDEX index_name ON table
[ USING acc_method ] ( column [ ops_name ] [, ...] )
[ WHERE predicate ]
CREATE [ UNIQUE ] INDEX index_name ON table
[ USING acc_method ] ( func_name( column [, ... ]) [ ops_name ] )
[ WHERE predicate ]
	*/
	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
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
		
		$unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';
		
		$s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' ';
		
		if (isset($idxoptions['HASH']))
			$s .= 'USING HASH ';
		
		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];
		
		if ( is_array($flds) )
			$flds = implode(', ',$flds);
		$s .= '(' . $flds . ')';
		$sql[] = $s;
		
		return $sql;
	}
}
?>