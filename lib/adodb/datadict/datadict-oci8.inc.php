<?php

/**
  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_oci8 extends ADODB_DataDict {
	
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	var $dropTable = "DROP TABLE %s CASCADE CONSTRAINTS";
	
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return 'CLOB';
		
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'NCLOB';
		
		case 'B': return 'BLOB';
			
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
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname ADD (";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}
		
		$s .= implode(',',$f).')';
		$sql[] = $s;
		return $sql;
	}
	
	function AlterColumnSQL($tabname, $flds)
	{
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname MODIFY(";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}
		$s .= implode(',',$f).')';
		$sql[] = $s;
		return $sql;
	}
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported for Oracle");
		return array();
	}
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint)
	{
		$suffix = '';
		
		if ($fdefault == "''" && $fnotnull) {// this is null in oracle
			$fnotnull = false;
			if ($this->debug) ADOConnection::outp("NOT NULL and DEFAULT='' illegal in Oracle");
		}
				
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) {
			$suffix .= ' NOT NULL';
		}
		if ($fautoinc) $this->seqField = $fname;
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		
		return $suffix;
	}
	
/*
CREATE or replace TRIGGER jaddress_insert
before insert on jaddress
for each row
begin
select seqaddress.nextval into :new.A_ID from dual;
end;
*/
	function _Triggers($tabname,$tableoptions)
	{
		if (!$this->seqField) return array();
		
		if ($this->schema) {
			$t = strpos($tabname,'.');
			if ($t !== false) $tab = substr($tabname,$t+1);
			else $tab = $tabname;
			$seqname = $this->schema.'.'.$this->seqPrefix.$tab;
			$trigname = $this->schema.'.TRIG_'.$this->seqPrefix.$tab;
		} else {
			$seqname = $this->seqPrefix.$tabname;
			$trigname = "TRIG_$seqname";
		}
		if (isset($tableoptions['REPLACE'])) $sql[] = "DROP SEQUENCE $seqname";
		$sql[] = "CREATE SEQUENCE $seqname";
		$sql[] = "CREATE OR REPLACE TRIGGER $trigname BEFORE insert ON $tabname 
		FOR EACH ROW
		BEGIN
		  select $seqname.nextval into :new.$this->seqField from dual;
		END";
		
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
		if (isset($idxoptions['REPLACE'])) $sql[] = "DROP INDEX $idxname";
		if (isset($idxoptions['BITMAP'])) {
			$unique = ' BITMAP'; 
		} else if (isset($idxoptions['UNIQUE'])) 
			$unique = ' UNIQUE';
		else 
			$unique = '';
		
		if (is_array($flds)) $flds = implode(', ',$flds);
		$s = "CREATE$unique INDEX $idxname ON $tabname ($flds)";
		if (isset($idxoptions[$this->upperName])) $s .= $idxoptions[$this->upperName];
		$sql[] = $s;
		
		return $sql;
	}
}
?>