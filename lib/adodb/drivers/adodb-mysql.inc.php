<?php
/*
V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  MySQL code that does not support transactions. Use mysqlt if you need transactions.
  Requires mysql client. Works on Windows and Unix.
  
 28 Feb 2001: MetaColumns bug fix - suggested by  Freek Dijkstra (phpeverywhere@macfreek.com)
*/ 

if (! defined("_ADODB_MYSQL_LAYER")) {
 define("_ADODB_MYSQL_LAYER", 1 );

class ADODB_mysql extends ADOConnection {
	var $databaseType = 'mysql';
	var $dataProvider = 'mysql';
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
	var $dbxDriver = 1;
	var $substr = "substring";
	var $lastInsID = false;
	
	function ADODB_mysql() 
	{			
	}
	
	function ServerInfo()
	{
		$arr['description'] = $this->GetOne("select version()");
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}
	
	function IfNull( $field, $ifNull ) 
	{
		return " IFNULL($field, $ifNull) "; // if MySQL
	}
	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{	
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);
		
		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
	
	// if magic quotes disabled, use mysql_real_escape_string()
	function qstr($s,$magic_quotes=false)
	{
		if (!$magic_quotes) {
		
			if (ADODB_PHPVER >= 0x4300) {
				if (is_resource($this->_connectionID))
					return "'".mysql_real_escape_string($s,$this->_connectionID)."'";
			}
			if ($this->replaceQuote[0] == '\\'){
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
		return mysql_insert_id($this->_connectionID);
	}
	
	function GetOne($sql,$inputarr=false)
	{
		$rs =& $this->SelectLimit($sql,1,-1,$inputarr);
		if ($rs) {
			$rs->Close();
			if ($rs->EOF) return false;
			return reset($rs->fields);
		}
		
		return false;
	}
	
	function _affectedrows()
	{
			return mysql_affected_rows($this->_connectionID);
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
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			$u = strtoupper($seqname);
			$this->Execute(sprintf($this->_genSeqSQL,$seqname));
			$this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
			$rs = $this->Execute($getnext);
		}
		$this->genID = mysql_insert_id($this->_connectionID);
		
		if ($rs) $rs->Close();
		
		return $this->genID;
	}
	
  	function &MetaDatabases()
	{
		$qid = mysql_list_dbs($this->_connectionID);
		$arr = array();
		$i = 0;
		$max = mysql_num_rows($qid);
		while ($i < $max) {
			$db = mysql_tablename($qid,$i);
			if ($db != 'mysql') $arr[] = $db;
			$i += 1;
		}
		return $arr;
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
		$first = true;
		/*
		foreach($arr as $a) {
			if ($first) {
				$s = $a;
				$first = false;
			} else $s .= ','.$a;
		}*/
		
		// suggestion by andrew005@mnogo.ru
		$s = implode(',',$arr); 
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	}
	
	function OffsetDate($dayFraction,$date=false)
	{		
		if (!$date) $date = $this->sysDate;
		return "from_unixtime(unix_timestamp($date)+($dayFraction)*24*3600)";
	}
	
	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (ADODB_PHPVER >= 0x4300)
			$this->_connectionID = mysql_connect($argHostname,$argUsername,$argPassword,
												$this->forceNewConnect,$this->clientFlags);
		else if (ADODB_PHPVER >= 0x4200)
			$this->_connectionID = mysql_connect($argHostname,$argUsername,$argPassword,
												$this->forceNewConnect);
		else
			$this->_connectionID = mysql_connect($argHostname,$argUsername,$argPassword);
	
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (ADODB_PHPVER >= 0x4300)
			$this->_connectionID = mysql_pconnect($argHostname,$argUsername,$argPassword,$this->clientFlags);
		else
			$this->_connectionID = mysql_pconnect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($this->autoRollback) $this->RollbackTrans();
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->forceNewConnect = true;
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename);
	}
	
 	function &MetaColumns($table) 
	{
	
		if ($this->metaColumnsSQL) {
		global $ADODB_FETCH_MODE;
		
			$save = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
			if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
			
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));
			
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			
			if ($rs === false) return false;
			
			$retarr = array();
			while (!$rs->EOF){
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[0];
				$type = $rs->fields[1];
				
				// split type into type(length):
				if (preg_match("/^(.+)\((\d+)/", $type, $query_array)) {
					$fld->type = $query_array[1];
					$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
				} else {
					$fld->max_length = -1;
					$fld->type = $type;
				}
				$fld->not_null = ($rs->fields[2] != 'YES');
				$fld->primary_key = ($rs->fields[3] == 'PRI');
				$fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
				$fld->binary = (strpos($fld->type,'blob') !== false);
				if (!$fld->binary) {
					$d = $rs->fields[4];
					if ($d != "" && $d != "NULL") {
						$fld->has_default = true;
						$fld->default_value = $d;
					} else {
						$fld->has_default = false;
					}
				}
				if ($save == ADODB_FETCH_NUM) $retarr[] = $fld;	
				else $retarr[strtoupper($fld->name)] = $fld;
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;	
		}
		return false;
	}
		
	// returns true or false
	function SelectDB($dbName) 
	{
		$this->databaseName = $dbName;
		if ($this->_connectionID) {
			return @mysql_select_db($dbName,$this->_connectionID);		
		}
		else return false;	
	}
	
	// parameters use PostgreSQL convention, not MySQL
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs=0)
	{
		$offsetStr =($offset>=0) ? "$offset," : '';
		
		return ($secs) ? $this->CacheExecute($secs,$sql." LIMIT $offsetStr$nrows",$inputarr)
			: $this->Execute($sql." LIMIT $offsetStr$nrows",$inputarr);
		
	}
	
	
	// returns queryID or false
	function _query($sql,$inputarr)
	{
	//global $ADODB_COUNTRECS;
		//if($ADODB_COUNTRECS) 
		return mysql_query($sql,$this->_connectionID);
		//else return @mysql_unbuffered_query($sql,$this->_connectionID); // requires PHP >= 4.0.6
	}

	/*	Returns: the last error message from previous database operation	*/	
	function ErrorMsg() 
	{
	
		if ($this->_logsql) return $this->_errorMsg;
		if (empty($this->_connectionID)) $this->_errorMsg = @mysql_error();
		else $this->_errorMsg = @mysql_error($this->_connectionID);
		return $this->_errorMsg;
	}
	
	/*	Returns: the last error number from previous database operation	*/	
	function ErrorNo() 
	{
		if ($this->_logsql) return $this->_errorCode;
		if (empty($this->_connectionID))  return @mysql_errno();
		else return @mysql_errno($this->_connectionID);
	}
	

	
	// returns true or false
	function _close()
	{
		@mysql_close($this->_connectionID);
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

class ADORecordSet_mysql extends ADORecordSet{	
	
	var $databaseType = "mysql";
	var $canSeek = true;
	
	function ADORecordSet_mysql($queryID,$mode=false) 
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch ($mode)
		{
		case ADODB_FETCH_NUM: $this->fetchMode = MYSQL_NUM; break;
		case ADODB_FETCH_ASSOC:$this->fetchMode = MYSQL_ASSOC; break;
		default:
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:$this->fetchMode = MYSQL_BOTH; break;
		}
	
		$this->ADORecordSet($queryID);	
	}
	
	function _initrs()
	{
	//GLOBAL $ADODB_COUNTRECS;
	//	$this->_numOfRows = ($ADODB_COUNTRECS) ? @mysql_num_rows($this->_queryID):-1;
		$this->_numOfRows = @mysql_num_rows($this->_queryID);
		$this->_numOfFields = @mysql_num_fields($this->_queryID);
	}
	
	function &FetchField($fieldOffset = -1) 
	{	
	
		if ($fieldOffset != -1) {
			$o = @mysql_fetch_field($this->_queryID, $fieldOffset);
			$f = @mysql_field_flags($this->_queryID,$fieldOffset);
			$o->max_length = @mysql_field_len($this->_queryID,$fieldOffset); // suggested by: Jim Nicholson (jnich@att.com)
			//$o->max_length = -1; // mysql returns the max length less spaces -- so it is unrealiable
			$o->binary = (strpos($f,'binary')!== false);
		}
		else if ($fieldOffset == -1) {	/*	The $fieldOffset argument is not provided thus its -1 	*/
			$o = @mysql_fetch_field($this->_queryID);
			$o->max_length = @mysql_field_len($this->_queryID); // suggested by: Jim Nicholson (jnich@att.com)
			//$o->max_length = -1; // mysql returns the max length less spaces -- so it is unrealiable
		}
			
		return $o;
	}

	function &GetRowAssoc($upper=true)
	{
		if ($this->fetchMode == MYSQL_ASSOC && !$upper) return $this->fields;
		return ADORecordSet::GetRowAssoc($upper);
	}
	
	/* Use associative array to get fields array */
	function Fields($colname)
	{	
		// added @ by "Michael William Miller" <mille562@pilot.msu.edu>
		if ($this->fetchMode != MYSQL_NUM) return @$this->fields[$colname];
		
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}
	
	function _seek($row)
	{
		if ($this->_numOfRows == 0) return false;
		return @mysql_data_seek($this->_queryID,$row);
	}
	
	
	// 10% speedup to move MoveNext to child class
	function MoveNext() 
	{
	//global $ADODB_EXTENSION;if ($ADODB_EXTENSION) return adodb_movenext($this);
	
		if ($this->EOF) return false;
				
		$this->_currentRow++;
		$this->fields = @mysql_fetch_array($this->_queryID,$this->fetchMode);
		if (is_array($this->fields)) return true;
		
		$this->EOF = true;
		
		/* -- tested raising an error -- appears pointless
		$conn = $this->connection;
		if ($conn && $conn->raiseErrorFn && ($errno = $conn->ErrorNo())) {
			$fn = $conn->raiseErrorFn;
			$fn($conn->databaseType,'MOVENEXT',$errno,$conn->ErrorMsg().' ('.$this->sql.')',$conn->host,$conn->database);
		}
		*/
		return false;
	}	
	
	function _fetch()
	{
		$this->fields =  @mysql_fetch_array($this->_queryID,$this->fetchMode);
		return is_array($this->fields);
	}
	
	function _close() {
		@mysql_free_result($this->_queryID);	
		$this->_queryID = false;	
	}
	
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
		
		case 'INT': 
		case 'INTEGER':
		case 'BIGINT':
		case 'TINYINT':
		case 'MEDIUMINT':
		case 'SMALLINT': 
			
			if (!empty($fieldobj->primary_key)) return 'R';
			else return 'I';
		
		default: return 'N';
		}
	}

}
}
?>