<?php

/**
  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
 	DOCUMENTATION:
	
		See adodb/tests/test-datadict.php for docs and examples.
*/

class ADODB_DataDict {
	var $connection;
	var $debug = false;
	var $dropTable = "DROP TABLE %s";
	var $addCol = ' ADD';
	var $alterCol = ' ALTER COLUMN';
	var $dropCol = ' DROP COLUMN';
	var $schema = false;
	var $serverInfo = array();
	
	function MetaTables()
	{
		return $this->connection->MetaTables();
	}
	
	function MetaColumns($tab)
	{
		return $this->connection->MetaColumns($tab);
	}
	
	function MetaPrimaryKeys($tab,$owner=false,$intkey=false)
	{
		return $this->connection->MetaPrimaryKeys($tab.$owner,$intkey);
	}
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		return ADORecordSet::MetaType($t,$len,$fieldobj);
	}
	
	// Executes the sql array returned by GetTableSQL and GetIndexSQL
	function ExecuteSQLArray($sql, $continueOnError = true)
	{
		$rez = 2;
		$conn = &$this->connection;
		foreach($sql as $line) {
			$ok = $conn->Execute($line);
			if (!$ok) {
				if ($this->debug) ADOConnection::outp($conn->ErrorMsg());
				if (!$continueOnError) return 0;
				$rez = 1;
			}
		}
		return 2;
	}
	
	/*
	 	Returns the actual type given a character code.
		
		C:  varchar
		X:  CLOB (character large object) or largest varchar size if CLOB is not supported
		C2: Multibyte varchar
		X2: Multibyte CLOB
		
		B:  BLOB (binary large object)
		
		D:  Date
		T:  Date-time 
		L:  Integer field suitable for storing booleans (0 or 1)
		I:  Integer
		F:  Floating point number
		N:  Numeric or decimal number
	*/
	
	function ActualType($meta)
	{
		return $meta;
	}
	
	function CreateDatabase($dbname,$options=false)
	{
		$options = $this->_Options($options);
		$s = 'CREATE DATABASE '.$dbname;
		if (isset($options[$this->upperName])) $s .= ' '.$options[$this->upperName];
		$sql[] = $s;
		return $sql;
	}
	
	/*
	 Generates the SQL to create index. Returns an array of sql strings.
	*/
	function CreateIndexSQL($idxname, $tabname, $flds, $idxoptions = false)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		return $this->_IndexSQL($idxname, $tabname, $flds, $this->_Options($idxoptions));
	}
	
	function SetSchema($schema)
	{
		$this->schema = $schema;
	}
	
	function AddColumnSQL($tabname, $flds)
	{	
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		foreach($lines as $v) {
			$sql[] = "ALTER TABLE $tabname $this->addCol $v";
		}
		return $sql;
	}
	
	function AlterColumnSQL($tabname, $flds)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);

		foreach($lines as $v) {
			$sql[] = "ALTER TABLE $tabname $this->alterCol $v";
		}
		return $sql;
	}
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		if (!is_array($flds)) $flds = explode(',',$flds);
		$sql = array();
		foreach($flds as $v) {
			$sql[] = "ALTER TABLE $tabname $this->dropCol $v";
		}
		return $sql;
	}
	
	function DropTableSQL($tabname)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$sql[] = sprintf($this->dropTable,$tabname);
		return $sql;
	}
	
	/*
	 Generate the SQL to create table. Returns an array of sql strings.
	*/
	function CreateTableSQL($tabname, $flds, $tableoptions=false)
	{
		if (!$tableoptions) $tableoptions = array();
	
		list($lines,$pkey) = $this->_GenFields($flds);
		
		$taboptions = $this->_Options($tableoptions);
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$sql = $this->_TableSQL($tabname,$lines,$pkey,$taboptions);
		
		$tsql = $this->_Triggers($tabname,$taboptions);
		foreach($tsql as $s) $sql[] = $s;
		
		return $sql;
	}
	
	function _GenFields($flds)
	{
		$lines = array();
		$pkey = array();
		foreach($flds as $fld) {
			$fld = _array_change_key_case($fld);
		
			$fname = false;
			$fdefault = false;
			$fautoinc = false;
			$ftype = false;
			$fsize = false;
			$fprec = false;
			$fprimary = false;
			$fnoquote = false;
			$fdefts = false;
			$fdefdate = false;
			$fconstraint = false;
			$fnotnull = false;
			//-----------------
			// Parse attributes
			foreach($fld as $attr => $v) {
				if ($attr == 2 && is_numeric($v)) $attr = 'SIZE';
				else if (is_numeric($attr) && $attr > 1 && !is_numeric($v)) $attr = strtoupper($v);
				
				switch($attr) {
				case '0':
				case 'NAME': 	$fname = $v; break;
				case '1':
				case 'TYPE': 	$ty = $v; $ftype = $this->ActualType(strtoupper($v)); break;
				case 'SIZE': 	$dotat = strpos($v,'.');
								if ($dotat === false) $fsize = $v;
								else {
									$fsize = substr($v,0,$dotat);
									$fprec = substr($v,$dotat+1);
								}
								break;
				case 'AUTOINCREMENT':
				case 'AUTO':	$fautoinc = true; $fnotnull = true; break;
				case 'KEY':
				case 'PRIMARY':	$fprimary = $v; $fnotnull = true; break;
				case 'DEFAULT': $fdefault = $v; break;
				case 'NOTNULL': $fnotnull = $v; break;
				case 'NOQUOTE': $fnoquote = $v; break;
				case 'DEFDATE': $fdefdate = $v; break;
				case 'DEFTIMESTAMP': $fdefts = $v; break;
				case 'CONSTRAINT': $fconstraint = $v; break;
				} //switch
			} // foreach $fld
			
			//--------------------
			// VALIDATE FIELD INFO
			if (!strlen($fname)) {
				if ($this->debug) ADOConnection::outp("Undefined NAME");
				return false;
			}
			
			if (!strlen($ftype)) {
				if ($this->debug) ADOConnection::outp("Undefined TYPE for field '$fname'");
				return false;
			} else
				$ftype = strtoupper($ftype);
			
			$ftype = $this->_GetSize($ftype, $ty, $fsize, $fprec);
			
			if ($fprimary) $pkey[] = $fname;
			
			// some databases do not allow blobs to have defaults
			if ($ty == 'X') $fdefault = false;
			
			//--------------------
			// CONSTRUCT FIELD SQL
			if ($fdefts) {
				if (substr($this->connection->databaseType,0,5) == 'mysql') {
					$ftype = 'TIMESTAMP';
				} else {
					$fdefault = $this->connection->sysTimeStamp;
				}
			} else if ($fdefdate) {
				if (substr($this->connection->databaseType,0,5) == 'mysql') {
					$ftype = 'TIMESTAMP';
				} else {
					$fdefault = $this->connection->sysDate;
				}
			} else if (strlen($fdefault) && !$fnoquote)
				if ($ty == 'C' or $ty == 'X' or 
					( substr($fdefault,0,1) != "'" && !is_numeric($fdefault)))
					if (substr($fdefault,0,1) == ' ' && substr($fdefault,strlen($fdefault)-1) == ' ') 
						$fdefault = trim($fdefault);
					else
						$fdefault = $this->connection->qstr($fdefault);
			$suffix = $this->_CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint);
			
			$fname = str_pad($fname,16);
			$lines[] = "$fname $ftype$suffix";
			
		} // foreach $flds
		
		
		return array($lines,$pkey);
	}
	/*
		 GENERATE THE SIZE PART OF THE DATATYPE
			$ftype is the actual type
			$ty is the type defined originally in the DDL
	*/
	function _GetSize($ftype, $ty, $fsize, $fprec)
	{
		if (strlen($fsize) && $ty != 'X' && $ty != 'B') {
			$ftype .= "(".$fsize;
			if ($fprec) $ftype .= ",".$fprec;				
			$ftype .= ')';
		}
		return $ftype;
	}
	
	
	function _TableSQL($tabname,$lines,$pkey,$tableoptions)
	{
		$sql = array();
		
		if (isset($tableoptions['REPLACE'])) $sql[] = sprintf($this->dropTable,$tabname);
		$s = "CREATE TABLE $tabname (\n";
		$s .= implode(",\n", $lines);
		if (sizeof($pkey)>0) {
			$s .= ",\n                 PRIMARY KEY (";
			$s .= implode(", ",$pkey).")";
		}
		if (isset($tableoptions['CONSTRAINTS'])) 
			$s .= "\n".$tableoptions['CONSTRAINTS'];
		
		if (isset($tableoptions[$this->upperName.'_CONSTRAINTS'])) 
			$s .= "\n".$tableoptions[$this->upperName.'_CONSTRAINTS'];
		
		$s .= "\n)";
		if (isset($tableoptions[$this->upperName])) $s .= $tableoptions[$this->upperName];
		$sql[] = $s;
		
		return $sql;
	}
	
	/*
		GENERATE TRIGGERS IF NEEDED
		used when table has auto-incrementing field that is emulated using triggers
	*/
	function _Triggers($tabname,$taboptions)
	{
		return array();
	}
	
	/*
		Sanitize options, so that array elements with no keys are promoted to keys
	*/
	function _Options($opts)
	{
		if (!is_array($opts)) return array();
		$newopts = array();
		foreach($opts as $k => $v) {
			if (is_numeric($k)) $newopts[strtoupper($v)] = $v;
			else $newopts[strtoupper($k)] = $v;
		}
		return $newopts;
	}
}
?>