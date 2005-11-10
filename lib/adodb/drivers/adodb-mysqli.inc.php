<?php
/*
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  MySQL code that does not support transactions. Use mysqlt if you need transactions.
  Requires mysql client. Works on Windows and Unix.
 
21 October 2003: MySQLi extension implementation by Arjen de Rijke (a.de.rijke@xs4all.nl)
Based on adodb 3.40
*/ 

// security - hide paths
//if (!defined('ADODB_DIR')) die();

if (! defined("_ADODB_MYSQLI_LAYER")) {
 define("_ADODB_MYSQLI_LAYER", 1 );
 
 if (!defined('MYSQLI_READ_DEFAULT_GROUP')) define('MYSQLI_READ_DEFAULT_GROUP',1);

 // disable adodb extension - currently incompatible.
 global $ADODB_EXTENSION; $ADODB_EXTENSION = false;

class ADODB_mysqli extends ADOConnection {
	var $databaseType = 'mysqli';
	var $dataProvider = 'native';
	var $hasInsertID = true;
	var $hasAffectedRows = true;	
	var $metaTablesSQL = "SHOW TABLES";	
	var $metaColumnsSQL = "SHOW COLUMNS FROM %s";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasLimit = true;
	var $hasMoveFirst = true;
	var $hasGenID = true;
	var $isoDates = true; // accepts dates in ISO format
	var $sysDate = 'CURDATE()';
	var $sysTimeStamp = 'NOW()';
	var $hasTransactions = true;
	var $forceNewConnect = false;
	var $poorAffectedRows = true;
	var $clientFlags = 0;
	var $substr = "substring";
	var $port = false;
	var $socket = false;
	var $_bindInputArray = false;
	var $nameQuote = '`';		/// string to use to quote identifiers and names
	var $optionFlags = array(array(MYSQLI_READ_DEFAULT_GROUP,0));
	
	function ADODB_mysqli() 
	{			
	 // if(!extension_loaded("mysqli"))
	      ;//trigger_error("You must have the mysqli extension installed.", E_USER_ERROR);
	    
	}
	

	// returns true or false
	// To add: parameter int $port,
	//         parameter string $socket
	function _connect($argHostname = NULL, 
			  $argUsername = NULL, 
			  $argPassword = NULL, 
			  $argDatabasename = NULL, $persist=false)
	  {
	  	 if(!extension_loaded("mysqli")) {
			return null;
		 }
	    $this->_connectionID = @mysqli_init();
	    
	    if (is_null($this->_connectionID)) {
	      // mysqli_init only fails if insufficient memory
	      if ($this->debug) 
				ADOConnection::outp("mysqli_init() failed : "  . $this->ErrorMsg());
	      return false;
	    }
		/*
		I suggest a simple fix which would enable adodb and mysqli driver to
		read connection options from the standard mysql configuration file
		/etc/my.cnf - "Bastien Duclaux" <bduclaux#yahoo.com>
		*/
		foreach($this->optionFlags as $arr) {	
			mysqli_options($this->_connectionID,$arr[0],$arr[1]);
		}

		#if (!empty($this->port)) $argHostname .= ":".$this->port;
		$ok = mysqli_real_connect($this->_connectionID,
 				    $argHostname,
 				    $argUsername,
 				    $argPassword,
 				    $argDatabasename,
					$this->port,
					$this->socket,
					$this->clientFlags);
 	     
		if ($ok) {
	 		if ($argDatabasename)  return $this->SelectDB($argDatabasename);
 			return true;
 	   } else {
			if ($this->debug) 
		  		ADOConnection::outp("Could't connect : "  . $this->ErrorMsg());
			return false;
	   }
	}
	
	// returns true or false
	// How to force a persistent connection
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename, true);

	}
	
	// When is this used? Close old connection first?
	// In _connect(), check $this->forceNewConnect? 
	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	  {
	    $this->forceNewConnect = true;
	    return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename);
	  }
	
	function IfNull( $field, $ifNull ) 
	{
		return " IFNULL($field, $ifNull) "; // if MySQL
	}
	
	function ServerInfo()
	{
		$arr['description'] = $this->GetOne("select version()");
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}
	
	
	function BeginTrans()
	{	  
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->Execute('SET AUTOCOMMIT=0');
		$this->Execute('BEGIN');
		return true;
	}
	
	function CommitTrans($ok=true) 
	{
		if ($this->transOff) return true; 
		if (!$ok) return $this->RollbackTrans();
		
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('COMMIT');
		$this->Execute('SET AUTOCOMMIT=1');
		return true;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('ROLLBACK');
		$this->Execute('SET AUTOCOMMIT=1');
		return true;
	}
	
	// if magic quotes disabled, use mysql_real_escape_string()
	// From readme.htm:
	// Quotes a string to be sent to the database. The $magic_quotes_enabled
	// parameter may look funny, but the idea is if you are quoting a 
	// string extracted from a POST/GET variable, then 
	// pass get_magic_quotes_gpc() as the second parameter. This will 
	// ensure that the variable is not quoted twice, once by qstr and once 
	// by the magic_quotes_gpc.
	//
	//Eg. $s = $db->qstr(_GET['name'],get_magic_quotes_gpc());
	function qstr($s, $magic_quotes = false)
	{
		if (!$magic_quotes) {
	    	if (PHP_VERSION >= 5)
	      		return "'" . mysqli_real_escape_string($this->_connectionID, $s) . "'";   
	    
		if ($this->replaceQuote[0] == '\\')
			$s = adodb_str_replace(array('\\',"\0"),array('\\\\',"\\\0"),$s);
	    return  "'".str_replace("'",$this->replaceQuote,$s)."'"; 
	  }
	  // undo magic quotes for "
	  $s = str_replace('\\"','"',$s);
	  return "'$s'";
	}
	
	function _insertid()
	{
	  $result = @mysqli_insert_id($this->_connectionID);
	  if ($result == -1){
	      if ($this->debug) ADOConnection::outp("mysqli_insert_id() failed : "  . $this->ErrorMsg());
	  }
	  return $result;
	}
	
	// Only works for INSERT, UPDATE and DELETE query's
	function _affectedrows()
	{
	  $result =  @mysqli_affected_rows($this->_connectionID);
	  if ($result == -1) {
	      if ($this->debug) ADOConnection::outp("mysqli_affected_rows() failed : "  . $this->ErrorMsg());
	  }
	  return $result;
	}
  
 	// See http://www.mysql.com/doc/M/i/Miscellaneous_functions.html
	// Reference on Last_Insert_ID on the recommended way to simulate sequences
 	var $_genIDSQL = "update %s set id=LAST_INSERT_ID(id+1);";
	var $_genSeqSQL = "create table %s (id int not null)";
	var $_genSeq2SQL = "insert into %s values (%s)";
	var $_dropSeqSQL = "drop table %s";
	
	function CreateSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		$u = strtoupper($seqname);
		
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		return $this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
	}
	
	function GenID($seqname='adodbseq',$startID=1)
	{
		// post-nuke sets hasGenID to false
		if (!$this->hasGenID) return false;
		
		$getnext = sprintf($this->_genIDSQL,$seqname);
		$holdtransOK = $this->_transOK; // save the current status
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			if ($holdtransOK) $this->_transOK = true; //if the status was ok before reset
			$u = strtoupper($seqname);
			$this->Execute(sprintf($this->_genSeqSQL,$seqname));
			$this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
			$rs = $this->Execute($getnext);
		}
		$this->genID = mysqli_insert_id($this->_connectionID);
		
		if ($rs) $rs->Close();
		
		return $this->genID;
	}
	
  	function &MetaDatabases()
	{
		$query = "SHOW DATABASES";
		$ret =& $this->Execute($query);
		if ($ret && is_object($ret)){
		   $arr = array();
			while (!$ret->EOF){
				$db = $ret->Fields('Database');
				if ($db != 'mysql') $arr[] = $db;
				$ret->MoveNext();
			}
   		   return $arr;
		}
        return $ret;
	}

	  
	function &MetaIndexes ($table, $primary = FALSE)
	{
		// save old fetch mode
		global $ADODB_FETCH_MODE;
		
		$false = false;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== FALSE) {
		       $savem = $this->SetFetchMode(FALSE);
		}
		
		// get index details
		$rs = $this->Execute(sprintf('SHOW INDEXES FROM %s',$table));
		
		// restore fetchmode
		if (isset($savem)) {
		        $this->SetFetchMode($savem);
		}
		$ADODB_FETCH_MODE = $save;
		
		if (!is_object($rs)) {
		        return $false;
		}
		
		$indexes = array ();
		
		// parse index data into array
		while ($row = $rs->FetchRow()) {
		        if ($primary == FALSE AND $row[2] == 'PRIMARY') {
		                continue;
		        }
		        
		        if (!isset($indexes[$row[2]])) {
		                $indexes[$row[2]] = array(
		                        'unique' => ($row[1] == 0),
		                        'columns' => array()
		                );
		        }
		        
		        $indexes[$row[2]]['columns'][$row[3] - 1] = $row[4];
		}
		
		// sort columns by order in the index
		foreach ( array_keys ($indexes) as $index )
		{
		        ksort ($indexes[$index]['columns']);
		}
		
		return $indexes;
	}

	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysTimeStamp;
		$s = 'DATE_FORMAT('.$col.",'";
		$concat = false;
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= '%Y';
				break;
			case 'Q':
			case 'q':
				$s .= "'),Quarter($col)";
				
				if ($len > $i+1) $s .= ",DATE_FORMAT($col,'";
				else $s .= ",('";
				$concat = true;
				break;
			case 'M':
				$s .= '%b';
				break;
				
			case 'm':
				$s .= '%m';
				break;
			case 'D':
			case 'd':
				$s .= '%d';
				break;
			
			case 'H': 
				$s .= '%H';
				break;
				
			case 'h':
				$s .= '%I';
				break;
				
			case 'i':
				$s .= '%i';
				break;
				
			case 's':
				$s .= '%s';
				break;
				
			case 'a':
			case 'A':
				$s .= '%p';
				break;
			
			case 'w':
				$s .= '%w';
				break;
				
			case 'l':
				$s .= '%W';
				break;
				
			default:
				
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $ch;
				break;
			}
		}
		$s.="')";
		if ($concat) $s = "CONCAT($s)";
		return $s;
	}
	
	// returns concatenated string
	// much easier to run "mysqld --ansi" or "mysqld --sql-mode=PIPES_AS_CONCAT" and use || operator
	function Concat()
	{
		$s = "";
		$arr = func_get_args();
		
		// suggestion by andrew005@mnogo.ru
		$s = implode(',',$arr); 
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	}
	
	// dayFraction is a day in floating point
	function OffsetDate($dayFraction,$date=false)
	{		
		if (!$date) 
		  $date = $this->sysDate;
		return "from_unixtime(unix_timestamp($date)+($dayFraction)*24*3600)";
	}
	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{	
		$save = $this->metaTablesSQL;
		if ($showSchema && is_string($showSchema)) {
			$this->metaTablesSQL .= " from $showSchema";
		}
		
		if ($mask) {
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);
		
		$this->metaTablesSQL = $save;
		return $ret;
	}
	
	// "Innox - Juan Carlos Gonzalez" <jgonzalez#innox.com.mx>
	function MetaForeignKeys( $table, $owner = FALSE, $upper = FALSE, $asociative = FALSE )
	{
	    if ( !empty($owner) ) {
	       $table = "$owner.$table";
	    }
	    $a_create_table = $this->getRow(sprintf('SHOW CREATE TABLE %s', $table));
		if ($associative) $create_sql = $a_create_table["Create Table"];
	    else $create_sql  = $a_create_table[1];
	
	    $matches = array();
	
	    if (!preg_match_all("/FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)/", $create_sql, $matches)) return false;
	 	$foreign_keys = array();	 	 
	    $num_keys = count($matches[0]);
	    for ( $i = 0;  $i < $num_keys;  $i ++ ) {
	        $my_field  = explode('`, `', $matches[1][$i]);
	        $ref_table = $matches[2][$i];
	        $ref_field = explode('`, `', $matches[3][$i]);
	
	        if ( $upper ) {
	            $ref_table = strtoupper($ref_table);
	        }
	
	        $foreign_keys[$ref_table] = array();
	        $num_fields               = count($my_field);
	        for ( $j = 0;  $j < $num_fields;  $j ++ ) {
	            if ( $asociative ) {
	                $foreign_keys[$ref_table][$ref_field[$j]] = $my_field[$j];
	            } else {
	                $foreign_keys[$ref_table][] = "{$my_field[$j]}={$ref_field[$j]}";
	            }
	        }
	    }
	    
	    return  $foreign_keys;
	}
	
 	function &MetaColumns($table) 
	{
		$false = false;
		if (!$this->metaColumnsSQL)
			return $false;
		
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false)
			$savem = $this->SetFetchMode(false);
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));
		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!is_object($rs))
			return $false;
		
		$retarr = array();
		while (!$rs->EOF) {
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$type = $rs->fields[1];
			
			// split type into type(length):
			$fld->scale = null;
			if (preg_match("/^(.+)\((\d+),(\d+)/", $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
				$fld->scale = is_numeric($query_array[3]) ? $query_array[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
			} elseif (preg_match("/^(enum)\((.*)\)$/i", $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = max(array_map("strlen",explode(",",$query_array[2]))) - 2; // PHP >= 4.0.6
				$fld->max_length = ($fld->max_length == 0 ? 1 : $fld->max_length);
			} else {
				$fld->type = $type;
				$fld->max_length = -1;
			}
			$fld->not_null = ($rs->fields[2] != 'YES');
			$fld->primary_key = ($rs->fields[3] == 'PRI');
			$fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
			$fld->binary = (strpos($type,'blob') !== false);
			$fld->unsigned = (strpos($type,'unsigned') !== false);

			if (!$fld->binary) {
				$d = $rs->fields[4];
				if ($d != '' && $d != 'NULL') {
					$fld->has_default = true;
					$fld->default_value = $d;
				} else {
					$fld->has_default = false;
				}
			}
			
			if ($save == ADODB_FETCH_NUM) {
				$retarr[] = $fld;
			} else {
				$retarr[strtoupper($fld->name)] = $fld;
			}
			$rs->MoveNext();
		}
		
		$rs->Close();
		return $retarr;
	}
		
	// returns true or false
	function SelectDB($dbName) 
	{
//	    $this->_connectionID = $this->mysqli_resolve_link($this->_connectionID);
	    $this->database = $dbName;
		$this->databaseName = $dbName; # obsolete, retained for compat with older adodb versions
		
	    if ($this->_connectionID) {
        	$result = @mysqli_select_db($this->_connectionID, $dbName);
			if (!$result) {
		    	ADOConnection::outp("Select of database " . $dbName . " failed. " . $this->ErrorMsg());
			}
			return $result;		
		}
	    return false;	
	}
	
	// parameters use PostgreSQL convention, not MySQL
	function &SelectLimit($sql,
			      $nrows = -1,
			      $offset = -1,
			      $inputarr = false, 
			      $arg3 = false,
			      $secs = 0)
	{
		$offsetStr = ($offset >= 0) ? "$offset," : '';
		if ($nrows < 0) $nrows = '18446744073709551615';
		
		if ($secs)
			$rs =& $this->CacheExecute($secs, $sql . " LIMIT $offsetStr$nrows" , $inputarr , $arg3);
		else
			$rs =& $this->Execute($sql . " LIMIT $offsetStr$nrows" , $inputarr , $arg3);
			
		return $rs;
	}
	
	
	function Prepare($sql)
	{
		return $sql;
		
		$stmt = $this->_connectionID->prepare($sql);
		if (!$stmt) {
			echo $this->ErrorMsg();
			return $sql;
		}
		return array($sql,$stmt);
	}
	
	
	// returns queryID or false
	function _query($sql, $inputarr)
	{
	global $ADODB_COUNTRECS;
		
		if (is_array($sql)) {
			$stmt = $sql[1];
			$a = '';
			foreach($inputarr as $k => $v) {
				if (is_string($v)) $a .= 's';
				else if (is_integer($v)) $a .= 'i'; 
				else $a .= 'd';
			}
			
			$fnarr = array_merge( array($stmt,$a) , $inputarr);
			$ret = call_user_func_array('mysqli_stmt_bind_param',$fnarr);

			$ret = mysqli_stmt_execute($stmt);
			return $ret;
		}
		if (!$mysql_res =  mysqli_query($this->_connectionID, $sql, ($ADODB_COUNTRECS) ? MYSQLI_STORE_RESULT : MYSQLI_USE_RESULT)) {
		    if ($this->debug) ADOConnection::outp("Query: " . $sql . " failed. " . $this->ErrorMsg());
		    return false;
		}
		
		return $mysql_res;
	}

	/*	Returns: the last error message from previous database operation	*/	
	function ErrorMsg() 
	  {
	    if (empty($this->_connectionID)) 
	      $this->_errorMsg = @mysqli_connect_error();
	    else 
	      $this->_errorMsg = @mysqli_error($this->_connectionID);
	    return $this->_errorMsg;
	  }
	
	/*	Returns: the last error number from previous database operation	*/	
	function ErrorNo() 
	  {
	    if (empty($this->_connectionID))  
	      return @mysqli_connect_errno();
	    else 
	      return @mysqli_errno($this->_connectionID);
	  }
	
	// returns true or false
	function _close()
	  {
	    @mysqli_close($this->_connectionID);
	    $this->_connectionID = false;
	  }

	/*
	* Maximum size of C field
	*/
	function CharMax()
	{
		return 255; 
	}
	
	/*
	* Maximum size of X field
	*/
	function TextMax()
	{
	  return 4294967295; 
	}


}
 
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_mysqli extends ADORecordSet{	
	
	var $databaseType = "mysqli";
	var $canSeek = true;
	
	function ADORecordSet_mysqli($queryID, $mode = false) 
	{
	  if ($mode === false) 
	   { 
	      global $ADODB_FETCH_MODE;
	      $mode = $ADODB_FETCH_MODE;
	   }
	   
	  switch ($mode)
	    {
	    case ADODB_FETCH_NUM: 
	      $this->fetchMode = MYSQLI_NUM; 
	      break;
	    case ADODB_FETCH_ASSOC:
	      $this->fetchMode = MYSQLI_ASSOC; 
	      break;
	    case ADODB_FETCH_DEFAULT:
	    case ADODB_FETCH_BOTH:
	    default:
	      $this->fetchMode = MYSQLI_BOTH; 
	      break;
	    }
	  $this->adodbFetchMode = $mode;
	  $this->ADORecordSet($queryID);	
	}
	
	function _initrs()
	{
	global $ADODB_COUNTRECS;
	
		$this->_numOfRows = $ADODB_COUNTRECS ? @mysqli_num_rows($this->_queryID) : -1;
		$this->_numOfFields = @mysqli_num_fields($this->_queryID);
	}
	
	function &FetchField($fieldOffset = -1) 
	{	
	  $fieldnr = $fieldOffset;
	  if ($fieldOffset != -1) {
	    $fieldOffset = mysqli_field_seek($this->_queryID, $fieldnr);
	  }
	  $o = mysqli_fetch_field($this->_queryID);
	  return $o;
	}

	function &GetRowAssoc($upper = true)
	{
	  if ($this->fetchMode == MYSQLI_ASSOC && !$upper) 
	    return $this->fields;
	  $row =& ADORecordSet::GetRowAssoc($upper);
	  return $row;
	}
	
	/* Use associative array to get fields array */
	function Fields($colname)
	{	
	  if ($this->fetchMode != MYSQLI_NUM) 
	    return @$this->fields[$colname];
		
	  if (!$this->bind) {
	    $this->bind = array();
	    for ($i = 0; $i < $this->_numOfFields; $i++) {
	      $o = $this->FetchField($i);
	      $this->bind[strtoupper($o->name)] = $i;
	    }
	  }
	  return $this->fields[$this->bind[strtoupper($colname)]];
	}
	
	function _seek($row)
	{
	  if ($this->_numOfRows == 0) 
	    return false;

	  if ($row < 0)
	    return false;

	  mysqli_data_seek($this->_queryID, $row);
	  $this->EOF = false;
	  return true;
	}
		
	// 10% speedup to move MoveNext to child class
	// This is the only implementation that works now (23-10-2003).
	// Other functions return no or the wrong results.
	function MoveNext() 
	{
		if ($this->EOF) return false;
		$this->_currentRow++;
		$this->fields = @mysqli_fetch_array($this->_queryID,$this->fetchMode);
		
		if (is_array($this->fields)) return true;
		$this->EOF = true;
		return false;
	}	
	
	function _fetch()
	{
		$this->fields = mysqli_fetch_array($this->_queryID,$this->fetchMode);  
	  	return is_array($this->fields);
	}
	
	function _close() 
	{
		mysqli_free_result($this->_queryID); 
	  	$this->_queryID = false;	
	}
	
/*

0 = MYSQLI_TYPE_DECIMAL
1 = MYSQLI_TYPE_CHAR
1 = MYSQLI_TYPE_TINY
2 = MYSQLI_TYPE_SHORT
3 = MYSQLI_TYPE_LONG
4 = MYSQLI_TYPE_FLOAT
5 = MYSQLI_TYPE_DOUBLE
6 = MYSQLI_TYPE_NULL
7 = MYSQLI_TYPE_TIMESTAMP
8 = MYSQLI_TYPE_LONGLONG
9 = MYSQLI_TYPE_INT24
10 = MYSQLI_TYPE_DATE
11 = MYSQLI_TYPE_TIME
12 = MYSQLI_TYPE_DATETIME
13 = MYSQLI_TYPE_YEAR
14 = MYSQLI_TYPE_NEWDATE
247 = MYSQLI_TYPE_ENUM
248 = MYSQLI_TYPE_SET
249 = MYSQLI_TYPE_TINY_BLOB
250 = MYSQLI_TYPE_MEDIUM_BLOB
251 = MYSQLI_TYPE_LONG_BLOB
252 = MYSQLI_TYPE_BLOB
253 = MYSQLI_TYPE_VAR_STRING
254 = MYSQLI_TYPE_STRING
255 = MYSQLI_TYPE_GEOMETRY
*/

	function MetaType($t, $len = -1, $fieldobj = false)
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
		
		case MYSQLI_TYPE_TINY_BLOB :
		case MYSQLI_TYPE_CHAR :
		case MYSQLI_TYPE_STRING :
		case MYSQLI_TYPE_ENUM :
		case MYSQLI_TYPE_SET :
		case 253 :
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
		
		case MYSQLI_TYPE_BLOB :
		case MYSQLI_TYPE_LONG_BLOB :
		case MYSQLI_TYPE_MEDIUM_BLOB :
		
		   return !empty($fieldobj->binary) ? 'B' : 'X';
		case 'YEAR':
		case 'DATE': 
		case MYSQLI_TYPE_DATE :
		case MYSQLI_TYPE_YEAR :
		
		   return 'D';
		
		case 'TIME':
		case 'DATETIME':
		case 'TIMESTAMP':
		
		case MYSQLI_TYPE_DATETIME :
		case MYSQLI_TYPE_NEWDATE :
		case MYSQLI_TYPE_TIME :
		case MYSQLI_TYPE_TIMESTAMP :
		
			return 'T';
		
		case 'INT': 
		case 'INTEGER':
		case 'BIGINT':
		case 'TINYINT':
		case 'MEDIUMINT':
		case 'SMALLINT': 
		
		case MYSQLI_TYPE_INT24 :
		case MYSQLI_TYPE_LONG :
		case MYSQLI_TYPE_LONGLONG :
		case MYSQLI_TYPE_SHORT :
		case MYSQLI_TYPE_TINY :
		
		   if (!empty($fieldobj->primary_key)) return 'R';
		   
		   return 'I';
		
		
		   // Added floating-point types
		   // Maybe not necessery.
		 case 'FLOAT':
		 case 'DOUBLE':
		   //		case 'DOUBLE PRECISION':
		 case 'DECIMAL':
		 case 'DEC':
		 case 'FIXED':
		 default:
		 	//if (!is_numeric($t)) echo "<p>--- Error in type matching $t -----</p>"; 
		 	return 'N';
		}
	} // function
	

} // rs class
 
}

?>