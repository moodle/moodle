<?php

/**
  V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_oci8 extends ADODB_DataDict {
	
	var $databaseType = 'oci8';
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	var $dropTable = "DROP TABLE %s CASCADE CONSTRAINTS";
	
	function MetaType($t,$len=-1)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
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
		
		case 'DATE': 
			return 'T';
		
		case 'INT': 
		case 'SMALLINT':
		case 'INTEGER': 
			return 'I';
			
		default:
			return 'N';
		}
	}
	
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return 'VARCHAR(4000)';
		case 'XL': return 'CLOB';
		
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'NVARCHAR(2000)';
		
		case 'B': return 'BLOB';
			
		case 'D': 
		case 'T': return 'DATE';
		case 'L': return 'DECIMAL(1)';
		case 'I1': return 'DECIMAL(3)';
		case 'I2': return 'DECIMAL(5)';
		case 'I':
		case 'I4': return 'DECIMAL(10)';
		
		case 'I8': return 'DECIMAL(20)';
		case 'F': return 'DECIMAL';
		case 'N': return 'DECIMAL';
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
	
	function _DropAutoIncrement($t)
	{
		if (strpos($t,'.') !== false) {
			$tarr = explode('.',$t);
			return "drop sequence ".$tarr[0].".seq_".$tarr[1];
		}
		return "drop sequence seq_".$t;
	}
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
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
		$sql[] = "CREATE OR REPLACE TRIGGER $trigname BEFORE insert ON $tabname FOR EACH ROW BEGIN select $seqname.nextval into :new.$this->seqField from dual; END;";
		
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
?>