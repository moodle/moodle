<?php
/*
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  MySQL code that does not support transactions. Use mysqlt if you need transactions.
  Requires mysql client. Works on Windows and Unix.
 
21 October 2003: MySQLi extension implementation by Arjen de Rijke (a.de.rijke@xs4all.nl)
Based on adodb 3.40
*/ 
if (! defined("_ADODB_MYSQL_LAYER")) {
 define("_ADODB_MYSQL_LAYER", 1 );
 
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
	var $upperCase = 'upper';
	var $isoDates = true; // accepts dates in ISO format
	var $sysDate = 'CURDATE()';
	var $sysTimeStamp = 'NOW()';
	var $hasTransactions = false;
	var $forceNewConnect = false;
	var $poorAffectedRows = true;
	var $clientFlags = 0;
	var $executeOnly = true;
	var $substr = "substring";
	var $nameQuote = '`';		/// string to use to quote identifiers and names
	//var $_bindInputArray = true;
	
	function ADODB_mysqli() 
	{			
	  if(!extension_loaded("mysqli"))
	    {
	      trigger_error("You must have the MySQLi extension.", E_USER_ERROR);
	    }
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
	//Eg. $s = $db->qstr(HTTP_GET_VARS['name'],get_magic_quotes_gpc());
	function qstr($s, $magic_quotes = false)
	{
	  if (!$magic_quotes) {
	    if (ADODB_PHPVER >= 0x5000) {
	    //  $this->_connectionID = $this->mysqli_resolve_link($this->_connectionID);
	      return "'" . mysqli_real_escape_string($this->_connectionID, $s) . "'";
	    }
	    else
	      {
		trigger_error("phpver < 5 not implemented", E_USER_ERROR);
	      }
	    
	    if ($this->replaceQuote[0] == '\\')
	      {
		$s = adodb_str_replace(array('\\',"\0"),array('\\\\',"\\\0"),$s);
	      }
	    return  "'".str_replace("'",$this->replaceQuote,$s)."'"; 
	  }
	  // undo magic quotes for "
	  $s = str_replace('\\"','"',$s);
	  return "'$s'";
	}
	
	function _insertid()
	{
//	  $this->_connectionID = $this->mysqli_resolve_link($this->_connectionID);
	  $result = @mysqli_insert_id($this->_connectionID);
	  if ($result == -1){
	      if ($this->debug) ADOConnection::outp("mysqli_insert_id() failed : "  . $this->ErrorMsg());
	  }
	  return $result;
	}
	
	// Only works for INSERT, UPDATE and DELETE query's
	function _affectedrows()
	{
	//  $this->_connectionID = $this->mysqli_resolve_link($this->_connectionID);
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
		return $ret;
	  }

	  
	function &MetaIndexes ($table, $primary = FALSE)
	{
	        // save old fetch mode
	        global $ADODB_FETCH_MODE;
	        
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
	                return FALSE;
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
	
	// returns true or false
	// To add: parameter int $port,
	//         parameter string $socket
	function _connect($argHostname = NULL, 
			  $argUsername = NULL, 
			  $argPassword = NULL, 
			  $argDatabasename = NULL)
	  {
	    // @ means: error surpression on
	    $this->_connectionID = @mysqli_init();
	    
	    if (is_null($this->_connectionID))
	    {
	      // mysqli_init only fails if insufficient memory
	      if ($this->debug) 
		ADOConnection::outp("mysqli_init() failed : "  . $this->ErrorMsg());
	      return false;
	    }
	    // Set connection options
	    // Not implemented now
	    // mysqli_options($this->_connection,,);
 	    if (mysqli_real_connect($this->_connectionID,
 				    $argHostname,
 				    $argUsername,
 				    $argPassword,
 				    $argDatabasename))
 	      {
 		if ($argDatabasename) 
		  {
		    return $this->SelectDB($argDatabasename);
		  }
		
 		return true;
 	      }
 	    else
	      {
		if ($this->debug) 
		  ADOConnection::outp("Could't connect : "  . $this->ErrorMsg());
		return false;
	      }
	  }
	
	// returns true or false
	// How to force a persistent connection
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	  {
	    // not implemented in mysqli (yet)?
	    $this->_connectionID = mysqli_connect($argHostname,
						  $argUsername,
						  $argPassword,
						  $argDatabasename);
	    if ($this->_connectionID === false) return false;
	    //	    if ($this->autoRollback) $this->RollbackTrans();
	    if ($argDatabasename) return $this->SelectDB($argDatabasename);
	    return true;	
	  }
	
	// When is this used? Close old connection first?
	// In _connect(), check $this->forceNewConnect? 
	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	  {
	    $this->forceNewConnect = true;
	    $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename);
	  }
	
 	function &MetaColumns($table) 
	{
	  if ($this->metaColumnsSQL) {
	    global $ADODB_FETCH_MODE;
	    $save = $ADODB_FETCH_MODE;
	    $rs = false;
	    switch($ADODB_FETCH_MODE)
	      {
	      case ADODB_FETCH_NUM:
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,
					     $table));
		
		$ADODB_FETCH_MODE = $save;
		if ($rs === false) break;
		$retarr = array();
		while (!$rs->EOF){
		  $fld = new ADOFieldObject();
		  $fld->name = $rs->fields[0];
		  $fld->type = $rs->fields[1];
		  // split type into type(length):
		  if (preg_match("/^(.+)\((\d+)\)$/", $fld->type, $query_array))
		    {
		      $fld->type = $query_array[1];
		      $fld->max_length = $query_array[2];
		    }
		  else
		    {
		      $fld->max_length = -1;
		    }
		  $fld->not_null = ($rs->fields[2] != 'YES');
		  $fld->primary_key = ($rs->fields[3] == 'PRI');
		  $fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
		  $fld->binary = (strpos($fld->type,'blob') !== false);
		  if (!$fld->binary) 
		    {
		      $d = $rs->fields[4];
		      $d = $rs->fields['Default'];
		      if ($d != "" && $d != "NULL")
			{
			  $fld->has_default = true;
			  $fld->default_value = $d;
			} 
		      else 
			{
			  $fld->has_default = false;
			}
		    }
		  $retarr[strtoupper($fld->name)] = $fld;	
		  $rs->MoveNext();
		}
		break;
	      case ADODB_FETCH_ASSOC:
	      case ADODB_FETCH_DEFAULT:
	      case ADODB_FETCH_BOTH:
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,
					     $table));
		$ADODB_FETCH_MODE = $save;
		if ($rs === false) break;
		$retarr = array();
		while (!$rs->EOF){
		  $fld = new ADOFieldObject();
		  $fld->name = $rs->fields['Field'];
		  $fld->type = $rs->fields['Type'];
				
		  // split type into type(length):
		  if (preg_match("/^(.+)\((\d+)\)$/", $fld->type, $query_array))
		    {
		      $fld->type = $query_array[1];
		      $fld->max_length = $query_array[2];
		    }
		  else
		    {
		      $fld->max_length = -1;
		    }
		  $fld->not_null = ($rs->fields['Null'] != 'YES');
		  $fld->primary_key = ($rs->fields['Key'] == 'PRI');
		  $fld->auto_increment = (strpos($rs->fields['Extra'], 'auto_increment') !== false);
		  $fld->binary = (strpos($fld->type,'blob') !== false);
		  if (!$fld->binary) 
		    {
		      $d = $rs->fields['Default'];
		      if ($d != "" && $d != "NULL")
			{
			  $fld->has_default = true;
			  $fld->default_value = $d;
			} 
		      else 
			{
			  $fld->has_default = false;
			}
		    }
		  $retarr[strtoupper($fld->name)] = $fld;	
		  $rs->MoveNext();
		}
		break;
	      default:
	      }
	    
	    if ($rs === false) return false;
	    $rs->Close();
	    return $retarr;	
	  }
	  return false;
	}
		
	// returns true or false
	function SelectDB($dbName) 
	{
//	    $this->_connectionID = $this->mysqli_resolve_link($this->_connectionID);
	    $this->databaseName = $dbName;
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
		
		if ($secs)
			$rs =& $this->CacheExecute($secs, $sql . " LIMIT $offsetStr$nrows" , $inputarr , $arg3);
		else
			$rs =& $this->Execute($sql . " LIMIT $offsetStr$nrows" , $inputarr , $arg3);
			
		return $rs;
	}
	
	
	function Prepare($sql)
	{
		return $sql;
		
		$stmt = mysqli_prepare($this->_connectionID,$sql);
		if (!$stmt) return false;
		return array($sql,$stmt);
	}
	
	
	// returns queryID or false
	function _query($sql, $inputarr)
	{
	global $ADODB_COUNTRECS;
	
		if (is_array($sql)) {
			$stmt = $sql[1];
			foreach($inputarr as $k => $v) {
				if (is_string($v)) $a[] = MYSQLI_BIND_STRING;
				else if (is_integer($v)) $a[] = MYSQLI_BIND_INT; 
				else $a[] = MYSQLI_BIND_DOUBLE;
				
				$fnarr =& array_merge( array($stmt,$a) , $inputarr);
				$ret = call_user_func_array('mysqli_bind_param',$fnarr);
			}
			$ret = mysqli_execute($stmt);
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
	      $this->_errorMsg = @mysqli_error();
	    else 
	      $this->_errorMsg = @mysqli_error($this->_connectionID);
	    return $this->_errorMsg;
	  }
	
	/*	Returns: the last error number from previous database operation	*/	
	function ErrorNo() 
	  {
	    if (empty($this->_connectionID))  
	      return @mysqli_errno();
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
	      $this->fetchMode = MYSQLI_ASSOC; 
	      break;
	    }
	  $this->ADORecordSet($queryID);	
	}
	
	function _initrs()
	{
	    // mysqli_num_rows only return correct number, depens
	    // on the use of mysql_store_result and mysql_use_result
	    if (!$this->Connection->executeOnly) {
			$this->_numOfRows = @mysqli_num_rows($this->_queryID);
			$this->_numOfFields = @mysqli_num_fields($this->_queryID);
	    }
	    else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
	    }
	}
	
	function &FetchField($fieldOffset = -1) 
	{	
	  $fieldnr = $fieldOffset;
	  if ($fieldOffset != -1) {
	    $fieldOffset = mysqi_field_seek($this->_queryID, $fieldnr);
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
	  if ($this->EOF) 
	    return false;
	  $this->_currentRow++;
	  switch($this->fetchMode)
	    {
	    case MYSQLI_NUM:
	      $this->fields = mysqli_fetch_array($this->_queryID);
	      break;
	    case MYSQLI_ASSOC:
	    case MYSQLI_BOTH:
	      $this->fields = mysqli_fetch_assoc($this->_queryID);
	      break;
	    default:
	    }
	  if (is_array($this->fields)) 
	    return true;
	  $this->EOF = true;
	  return false;
	}	
	
	function _fetch()
	{
	  // mysqli_fetch_array($this->_queryID, MYSQLI_NUM) does not
	  // work (22-10-2003). But mysqli_fetch_array($this->_queryID) gives
	  // int resulttype should default to MYSQLI_BOTH,but give MYSQLI_NUM.

	  //	  $this->fields =  mysqli_fetch_fields($this->_queryID);
	  //	  $this->fields =  mysqli_fetch_array($this->_queryID); //, $this->fetchMode);
		  
	  $this->fields =  mysqli_fetch_assoc($this->_queryID); // $this->fetchMode);
	  return is_array($this->fields);
	}
	
	function _close() 
	{
	  mysqli_free_result($this->_queryID); 
	  $this->_queryID = false;	
	}
	
	function MetaType($t, $len = -1, $fieldobj = false)
	{
	  if (is_object($t)) 
	    {
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
	  case 'DATE': 
	    return 'D';
		
	  case 'TIME':
	  case 'DATETIME':
	  case 'TIMESTAMP': return 'T';
		
	  case 'INT': 
	  case 'INTEGER':
	  case 'BIGINT':
	  case 'TINYINT':
	  case 'MEDIUMINT':
	  case 'SMALLINT': 
			
	    if (!empty($fieldobj->primary_key)) return 'R';
	    else return 'I';
	    // Added floating-point types
	    // Maybe not necessery.
	  case 'FLOAT':
	  case 'DOUBLE':
	    //		case 'DOUBLE PRECISION':
	  case 'DECIMAL':
	  case 'DEC':
	  case 'FIXED':
	  default: 
	    return 'N';
	  }
	}
	

}
 
}

?>