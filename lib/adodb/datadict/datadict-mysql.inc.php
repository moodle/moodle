<?php

/**
  V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_mysql extends ADODB_DataDict {
	var $databaseType = 'mysql';
	var $alterCol = ' MODIFY COLUMN';
	var $quote = '`';
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		
		$len = -1; // mysql max_length is not accurate
		switch (strtoupper($t)) {
		case 'STRING': 
		case 'CHAR':
		case 'VARCHAR': 
		case 'TINYBLOB': 
		case 'TINYTEXT': 
		case 'ENUM': 
		case 'SET':
			if ($len <= $this->blobSize) return 'C';
			
		case 'TEXT':
		case 'LONGTEXT': 
		case 'MEDIUMTEXT':
			return 'X';
			
		// php_mysql extension always returns 'blob' even if 'text'
		// so we have to check whether binary...
		case 'IMAGE':
		case 'LONGBLOB': 
		case 'BLOB':
		case 'MEDIUMBLOB':
			return !empty($fieldobj->binary) ? 'B' : 'X';
			
		case 'YEAR':
		case 'DATE': return 'D';
		
		case 'TIME':
		case 'DATETIME':
		case 'TIMESTAMP': return 'T';
		
		case 'FLOAT':
		case 'DOUBLE':
			return 'F';
			
		case 'INT': 
		case 'INTEGER': return (!empty($fieldobj->primary_key)) ? 'R' : 'I';
		case 'TINYINT': return (!empty($fieldobj->primary_key)) ? 'R' : 'I1';
		case 'SMALLINT': return (!empty($fieldobj->primary_key)) ? 'R' : 'I2';
		case 'MEDIUMINT': return (!empty($fieldobj->primary_key)) ? 'R' : 'I4';
		case 'BIGINT':  return (!empty($fieldobj->primary_key)) ? 'R' : 'I8';
		default: return 'N';
		}
	}

	function ActualType($meta)
	{
		switch(strtoupper($meta)) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'LONGTEXT';
		
		case 'C2': return 'VARCHAR';
		case 'X2': return 'LONGTEXT';
		
		case 'B': return 'LONGBLOB';
			
		case 'D': return 'DATE';
		case 'T': return 'DATETIME';
		case 'L': return 'TINYINT';
		
		case 'I': return 'INTEGER';
		case 'I1': return 'TINYINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'MEDIUMINT';
		case 'I8': return 'BIGINT';
		
		case 'F': return 'DOUBLE';
		case 'N': return 'NUMERIC';
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
		if ($fautoinc) $suffix .= ' AUTO_INCREMENT';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
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
	
	/*
	CREATE [UNIQUE|FULLTEXT] INDEX index_name
		ON tbl_name (col_name[(length)],... )
	*/
	
	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		//if (isset($idxoptions['REPLACE'])) $sql[] = "DROP INDEX IF EXISTS $idxname";
		if (isset($idxoptions['REPLACE'])) $sql[] = "DROP INDEX $idxname ON $tabname";
		if (isset($idxoptions['FULLTEXT'])) $unique = ' FULLTEXT';
		else if (isset($idxoptions['UNIQUE'])) $unique = ' UNIQUE';
		else $unique = '';
		
		if (is_array($flds)) $flds = implode(', ',$flds);
		$s = "CREATE$unique INDEX $idxname ON $tabname ($flds)";
		if (isset($idxoptions[$this->upperName])) $s .= $idxoptions[$this->upperName];
		$sql[] = $s;
		
		return $sql;
	}
}
?>