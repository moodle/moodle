<?php

/**
  V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
 	DOCUMENTATION:
	
		See adodb/tests/test-datadict.php for docs and examples.
*/

/*
	Test script for parser
*/
function Lens_ParseTest()
{
$str = "`zcol ACOL` NUMBER(32,2) DEFAULT 'The \"cow\" (and Jim''s dog) jumps over the moon' PRIMARY, INTI INT AUTO DEFAULT 0";
print "<p>$str</p>";
$a= Lens_ParseArgs($str);
print "<pre>";
print_r($a);
print "</pre>";
}

if (!function_exists('ctype_alnum')) {
	function ctype_alnum($text) {
		return preg_match('/^[a-z0-9]*$/i', $text);
	}
}

//Lens_ParseTest();

/**
	Parse arguments, treat "text" (text) and 'text' as quotation marks.
	To escape, use "" or '' or ))
	
	Will read in "abc def" sans quotes, as: abc def
	Same with 'abc def'.
	However if `abc def`, then will read in as `abc def`
	
	@param endstmtchar    Character that indicates end of statement
	@param tokenchars     Include the following characters in tokens apart from A-Z and 0-9 
	@returns 2 dimensional array containing parsed tokens.
*/
function Lens_ParseArgs($args,$endstmtchar=',',$tokenchars='_.-')
{
	$pos = 0;
	$intoken = false;
	$stmtno = 0;
	$endquote = false;
	$tokens = array();
	$tokens[$stmtno] = array();
	$max = strlen($args);
	$quoted = false;
	
	while ($pos < $max) {
		$ch = substr($args,$pos,1);
		switch($ch) {
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			if (!$quoted) {
				if ($intoken) {
					$intoken = false;
					$tokens[$stmtno][] = implode('',$tokarr);
				}
				break;
			}
			
			$tokarr[] = $ch;
			break;
		
		case '`':
			if ($intoken) $tokarr[] = $ch;
		case '(':
		case ')':	
		case '"':
		case "'":
			
			if ($intoken) {
				if (empty($endquote)) {
					$tokens[$stmtno][] = implode('',$tokarr);
					if ($ch == '(') $endquote = ')';
					else $endquote = $ch;
					$quoted = true;
					$intoken = true;
					$tokarr = array();
				} else if ($endquote == $ch) {
					$ch2 = substr($args,$pos+1,1);
					if ($ch2 == $endquote) {
						$pos += 1;
						$tokarr[] = $ch2;
					} else {
						$quoted = false;
						$intoken = false;
						$tokens[$stmtno][] = implode('',$tokarr);
						$endquote = '';
					}
				} else
					$tokarr[] = $ch;
					
			}else {
			
				if ($ch == '(') $endquote = ')';
				else $endquote = $ch;
				$quoted = true;
				$intoken = true;
				$tokarr = array();
				if ($ch == '`') $tokarr[] = '`';
			}
			break;
			
		default:
			
			if (!$intoken) {
				if ($ch == $endstmtchar) {
					$stmtno += 1;
					$tokens[$stmtno] = array();
					break;
				}
			
				$intoken = true;
				$quoted = false;
				$endquote = false;
				$tokarr = array();
	
			}
			
			if ($quoted) $tokarr[] = $ch;
			else if (ctype_alnum($ch) || strpos($tokenchars,$ch) !== false) $tokarr[] = $ch;
			else {
				if ($ch == $endstmtchar) {			
					$tokens[$stmtno][] = implode('',$tokarr);
					$stmtno += 1;
					$tokens[$stmtno] = array();
					$intoken = false;
					$tokarr = array();
					break;
				}
				$tokens[$stmtno][] = implode('',$tokarr);
				$tokens[$stmtno][] = $ch;
				$intoken = false;
			}
		}
		$pos += 1;
	}
	
	return $tokens;
}


class ADODB_DataDict {
	var $connection;
	var $debug = false;
	var $dropTable = 'DROP TABLE %s';
	var $dropIndex = 'DROP INDEX %s';
	var $addCol = ' ADD';
	var $alterCol = ' ALTER COLUMN';
	var $dropCol = ' DROP COLUMN';
	var $nameRegex = '\w';
	var $schema = false;
	var $serverInfo = array();
	var $autoIncrement = false;
	var $dataProvider;
	var $blobSize = 100; 	/// any varchar/char field this size or greater is treated as a blob
							/// in other words, we use a text area for editting.
	
	function GetCommentSQL($table,$col)
	{
		return false;
	}
	
	function SetCommentSQL($table,$col,$cmt)
	{
		return false;
	}
	
	function &MetaTables()
	{
		return $this->connection->MetaTables();
	}
	
	function &MetaColumns($tab, $upper=true, $schema=false)
	{
		return $this->connection->MetaColumns($this->TableName($tab), $upper, $schema);
	}
	
	function &MetaPrimaryKeys($tab,$owner=false,$intkey=false)
	{
		return $this->connection->MetaPrimaryKeys($this->TableName($tab), $owner, $intkey);
	}
	
	function &MetaIndexes($table, $primary = false, $owner = false)
	{
		return $this->connection->MetaIndexes($this->TableName($table), $primary, $owner);
	}
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		return ADORecordSet::MetaType($t,$len,$fieldobj);
	}
	
	function NameQuote($name = NULL)
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
		
		return $name;
	}
	
	function TableName($name)
	{
		if ( $this->schema ) {
			return $this->NameQuote($this->schema) .'.'. $this->NameQuote($name);
		}
		return $this->NameQuote($name);
	}
	
	// Executes the sql array returned by GetTableSQL and GetIndexSQL
	function ExecuteSQLArray($sql, $continueOnError = true)
	{
		$rez = 2;
		$conn = &$this->connection;
		$saved = $conn->debug;
		foreach($sql as $line) {
			
			if ($this->debug) $conn->debug = true;
			$ok = $conn->Execute($line);
			$conn->debug = $saved;
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
		$sql = array();
		
		$s = 'CREATE DATABASE ' . $this->NameQuote($dbname);
		if (isset($options[$this->upperName]))
			$s .= ' '.$options[$this->upperName];
		
		$sql[] = $s;
		return $sql;
	}
	
	/*
	 Generates the SQL to create index. Returns an array of sql strings.
	*/
	function CreateIndexSQL($idxname, $tabname, $flds, $idxoptions = false)
	{
		if (!is_array($flds)) {
			$flds = explode(',',$flds);
		}
		
		foreach($flds as $key => $fld) {
			$flds[$key] = $this->NameQuote($fld);
		}
		
		return $this->_IndexSQL($this->NameQuote($idxname), $this->TableName($tabname), $flds, $this->_Options($idxoptions));
	}
	
	function DropIndexSQL ($idxname, $tabname = NULL)
	{
		return array(sprintf($this->dropIndex, $this->NameQuote($idxname), $this->TableName($tabname)));
	}
	
	function SetSchema($schema)
	{
		$this->schema = $schema;
	}
	
	function AddColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->addCol . ' ';
		foreach($lines as $v) {
			$sql[] = $alter . $v;
		}
		return $sql;
	}
	
	function AlterColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
		foreach($lines as $v) {
			$sql[] = $alter . $v;
		}
		return $sql;
	}
	
	function DropColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		if (!is_array($flds)) $flds = explode(',',$flds);
		$sql = array();
		$alter = 'ALTER TABLE ' . $tabname . $this->dropCol . ' ';
		foreach($flds as $v) {
			$sql[] = $alter . $this->NameQuote($v);
		}
		return $sql;
	}
	
	function DropTableSQL($tabname)
	{
		return array (sprintf($this->dropTable, $this->TableName($tabname)));
	}
	
	/*
	 Generate the SQL to create table. Returns an array of sql strings.
	*/
	function CreateTableSQL($tabname, $flds, $tableoptions=false)
	{
		if (!$tableoptions) $tableoptions = array();
		
		list($lines,$pkey) = $this->_GenFields($flds);
		
		$taboptions = $this->_Options($tableoptions);
		$tabname = $this->TableName ($tabname);
		$sql = $this->_TableSQL($tabname,$lines,$pkey,$taboptions);
		
		$tsql = $this->_Triggers($tabname,$taboptions);
		foreach($tsql as $s) $sql[] = $s;
		
		return $sql;
	}
	
	function _GenFields($flds)
	{
		if (is_string($flds)) {
			$padding = '     ';
			$txt = $flds.$padding;
			$flds = array();
			$flds0 = Lens_ParseArgs($txt,',');
			$hasparam = false;
			foreach($flds0 as $f0) {
				$f1 = array();
				foreach($f0 as $token) {
					switch (strtoupper($token)) {
					case 'CONSTRAINT':
					case 'DEFAULT': 
						$hasparam = $token;
						break;
					default:
						if ($hasparam) $f1[$hasparam] = $token;
						else $f1[] = $token;
						$hasparam = false;
						break;
					}
				}
				$flds[] = $f1;
				
			}
		}
		$this->autoIncrement = false;
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
			$funsigned = false;
			
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
				
				case 'SIZE': 	
								$dotat = strpos($v,'.'); if ($dotat === false) $dotat = strpos($v,',');
								if ($dotat === false) $fsize = $v;
								else {
									$fsize = substr($v,0,$dotat);
									$fprec = substr($v,$dotat+1);
								}
								break;
				case 'UNSIGNED': $funsigned = true; break;
				case 'AUTOINCREMENT':
				case 'AUTO':	$fautoinc = true; $fnotnull = true; break;
				case 'KEY':
				case 'PRIMARY':	$fprimary = $v; $fnotnull = true; break;
				case 'DEF':
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
			
			$fid = strtoupper(preg_replace('/^`(.+)`$/', '$1', $fname));
			$fname = $this->NameQuote($fname);
			
			if (!strlen($ftype)) {
				if ($this->debug) ADOConnection::outp("Undefined TYPE for field '$fname'");
				return false;
			} else {
				$ftype = strtoupper($ftype);
			}
			
			$ftype = $this->_GetSize($ftype, $ty, $fsize, $fprec);
			
			if ($ty == 'X' || $ty == 'X2' || $ty == 'B') $fnotnull = false; // some blob types do not accept nulls
			
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
					if (strlen($fdefault) != 1 && substr($fdefault,0,1) == ' ' && substr($fdefault,strlen($fdefault)-1) == ' ') 
						$fdefault = trim($fdefault);
					else if (strtolower($fdefault) != 'null')
						$fdefault = $this->connection->qstr($fdefault);
			$suffix = $this->_CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned);
			
			$fname = str_pad($fname,16);
			$lines[$fid] = $fname.' '.$ftype.$suffix;
			
			if ($fautoinc) $this->autoIncrement = true;
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
		if (strlen($fsize) && $ty != 'X' && $ty != 'B' && strpos($ftype,'(') === false) {
			$ftype .= "(".$fsize;
			if (strlen($fprec)) $ftype .= ",".$fprec;
			$ftype .= ')';
		}
		return $ftype;
	}
	
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint)
	{	
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}
	
	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();
		
		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			$sql[] = sprintf ($this->dropIndex, $idxname);
			if ( isset($idxoptions['DROP']) )
				return $sql;
		}
		
		if ( empty ($flds) ) {
			return $sql;
		}
		
		$unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';
	
		$s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' ';
		
		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];
		
		if ( is_array($flds) )
			$flds = implode(', ',$flds);
		$s .= '(' . $flds . ')';
		$sql[] = $s;
		
		return $sql;
	}
	
	function _DropAutoIncrement($tabname)
	{
		return false;
	}
	
	function _TableSQL($tabname,$lines,$pkey,$tableoptions)
	{
		$sql = array();
		
		if (isset($tableoptions['REPLACE']) || isset ($tableoptions['DROP'])) {
			$sql[] = sprintf($this->dropTable,$tabname);
			if ($this->autoIncrement) {
				$sInc = $this->_DropAutoIncrement($tabname);
				if ($sInc) $sql[] = $sInc;
			}
			if ( isset ($tableoptions['DROP']) ) {
				return $sql;
			}
		}
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
	
	/*
	"Florian Buzin [ easywe ]" <florian.buzin@easywe.de>
	
	This function changes/adds new fields to your table. You don't
	have to know if the col is new or not. It will check on its own.
	*/
	function ChangeTableSQL($tablename, $flds, $tableoptions = false)
	{
		if ( !is_array($cols = &$this->MetaColumns($tablename)) ) {
			return $this->CreateTableSQL($tablename, $flds, $tableoptions);
		}
		
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $this->TableName($tablename);
		$sql = array();
		
		foreach ( $lines as $id => $v ) {
			if ( isset($cols[$id]) && is_object($cols[$id]) ) {
				$sql[] = $alter . $this->alterCol . ' ' . $v;
			} else {
				$sql[] = $alter . $this->addCol . ' ' . $v;
			}
		}
		
		return $sql;
	}
} // class
?>