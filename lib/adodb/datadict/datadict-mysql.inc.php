<?php

/**
  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_mysql extends ADODB_DataDict {
	
	var $alterCol = ' MODIFY COLUMN';
	
	function ActualType($meta)
	{
		switch(strtoupper($meta)) {
		case 'C': return 'VARCHAR';
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
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint)
	{	
		$suffix = '';
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