<?php

/**
  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_postgres extends ADODB_DataDict {
	
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	
 	
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
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

	function AlterColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported for PostgreSQL");
		return array();
	}
	
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported for PostgreSQL");
		return array();
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
		if (isset($idxoptions['REPLACE'])) $sql[] = "DROP INDEX $idxname";
		if (isset($idxoptions['UNIQUE'])) $unique = ' UNIQUE';
		else $unique = '';
		
		if (is_array($flds)) $flds = implode(', ',$flds);
		$s = "CREATE$unique INDEX $idxname ON $tabname ";
		if (isset($idxoptions['HASH'])) $s .= 'USING HASH ';
		if (isset($idxoptions[$this->upperName])) $s .= $idxoptions[$this->upperName];
		$s .= "($flds)";
		$sql[] = $s;
		
		return $sql;
	}
}
?>