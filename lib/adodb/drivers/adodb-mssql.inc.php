<?php
/* 
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Native mssql driver. Requires mssql client. Works on Windows. 
  To configure for Unix, see 
   	http://phpbuilder.com/columns/alberto20000919.php3
	
*/

//----------------------------------------------------------------
// MSSQL returns dates with the format Oct 13 2002 or 13 Oct 2002
// and this causes tons of problems because localized versions of 
// MSSQL will return the dates in dmy or  mdy order; and also the 
// month strings depends on what language has been configured. The 
// following two variables allow you to control the localization
// settings - Ugh.
//
// MORE LOCALIZATION INFO
// ----------------------
// To configure datetime, look for and modify sqlcommn.loc, 
//  	typically found in c:\mssql\install
// Also read :
//	 http://support.microsoft.com/default.aspx?scid=kb;EN-US;q220918
// Alternatively use:
// 	   CONVERT(char(12),datecol,120)
//----------------------------------------------------------------


// has datetime converstion to YYYY-MM-DD format, and also mssql_fetch_assoc
if (ADODB_PHPVER >= 0x4300) {
// docs say 4.2.0, but testing shows only since 4.3.0 does it work!
	ini_set('mssql.datetimeconvert',0); 
} else {
global $ADODB_mssql_mths;		// array, months must be upper-case


	$ADODB_mssql_date_order = 'mdy'; 
	$ADODB_mssql_mths = array(
		'JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MAY'=>5,'JUN'=>6,
		'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12);
}

//---------------------------------------------------------------------------
// Call this to autoset $ADODB_mssql_date_order at the beginning of your code,
// just after you connect to the database. Supports mdy and dmy only.
// Not required for PHP 4.2.0 and above.
function AutoDetect_MSSQL_Date_Order($conn)
{
global $ADODB_mssql_date_order;
	$adate = $conn->GetOne('select getdate()');
	if ($adate) {
		$anum = (int) $adate;
		if ($anum > 0) {
			if ($anum > 31) {
				//ADOConnection::outp( "MSSQL: YYYY-MM-DD date format not supported currently");
			} else
				$ADODB_mssql_date_order = 'dmy';
		} else
			$ADODB_mssql_date_order = 'mdy';
	}
}

class ADODB_mssql extends ADOConnection {
	var $databaseType = "mssql";	
	var $dataProvider = "mssql";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $hasInsertID = true;
	var $hasAffectedRows = true;
	var $metaTablesSQL="select name from sysobjects where type='U' or type='V' and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE'))";
	var $metaColumnsSQL = "select c.name,t.name,c.length from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $hasGenID = true;
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	var $_has_mssql_init;
	var $maxParameterLen = 4000;
	var $arrayClass = 'ADORecordSet_array_mssql';
	var $uniqueSort = true;
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	var $poorAffectedRows = true;
	var $identitySQL = 'select @@IDENTITY'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	
	
	function ADODB_mssql() 
	{		
		$this->_has_mssql_init = (strnatcmp(PHP_VERSION,'4.1.0')>=0);	
	}

	function ServerInfo()
	{
	global $ADODB_FETCH_MODE;
	
		$stmt = $this->PrepareSP('sp_server_info');
		$val = 2;
		if ($this->fetchMode === false) {
			$savem = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		} else 
			$savem = $this->SetFetchMode(ADODB_FETCH_NUM);
		
		
		$this->Parameter($stmt,$val,'attribute_id');
		$row = $this->GetRow($stmt);
		
		//$row = $this->GetRow("execute sp_server_info 2");
		
		if ($this->fetchMode === false) {
			$ADODB_FETCH_MODE = $savem;
		} else
			$this->SetFetchMode($savem);
		
		$arr['description'] = $row[2];
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}
	
	function _insertid()
	{
	// SCOPE_IDENTITY()
	// Returns the last IDENTITY value inserted into an IDENTITY column in 
	// the same scope. A scope is a module -- a stored procedure, trigger, 
	// function, or batch. Thus, two statements are in the same scope if 
	// they are in the same stored procedure, function, or batch.
			return $this->GetOne($this->identitySQL);
	}
	
	function _affectedrows()
	{
		return $this->GetOne('select @@rowcount');
	}
	
	var $_dropSeqSQL = "drop table %s";
	
	function CreateSequence($seq='adodbseq',$start=1)
	{
		$start -= 1;
		$this->Execute("create table $seq (id float(53))");
		$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
		if (!$ok) {
				$this->Execute('ROLLBACK TRANSACTION adodbseq');
				return false;
		}
		$this->Execute('COMMIT TRANSACTION adodbseq'); 
		return true;
	}
	
	function GenID($seq='adodbseq',$start=1)
	{	
		//$this->debug=1;
		$this->Execute('BEGIN TRANSACTION adodbseq');
		$ok = $this->Execute("update $seq with (tablock,holdlock) set id = id + 1");
		if (!$ok) {
			$this->Execute("create table $seq (id float(53))");
			$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
			if (!$ok) {
				$this->Execute('ROLLBACK TRANSACTION adodbseq');
				return false;
			}
			$this->Execute('COMMIT TRANSACTION adodbseq'); 
			return $start;
		}
		$num = $this->GetOne("select id from $seq");
		$this->Execute('COMMIT TRANSACTION adodbseq'); 
		return $num;
		
		// in old implementation, pre 1.90, we returned GUID...
		//return $this->GetOne("SELECT CONVERT(varchar(255), NEWID()) AS 'Char'");
	}
	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysDate;
		$s = '';
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '+';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "datename(yyyy,$col)";
				break;
			case 'M':
			case 'm':
				$s .= "replace(str(month($col),2),' ','0')";
				break;
			case 'Q':
			case 'q':
				$s .= "datename(quarter,$col)";
				break;
			case 'D':
			case 'd':
				$s .= "replace(str(day($col),2),' ','0')";
				break;
			default:
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $this->qstr($ch);
				break;
			}
		}
		return $s;
	}

	
	function BeginTrans()
	{
		if ($this->transOff) return true; 
		$this->transCnt += 1;
	   	$this->Execute('BEGIN TRAN');
	   	return true;
	}
		
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true; 
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('COMMIT TRAN');
		return true;
	}
	function RollbackTrans()
	{
		if ($this->transOff) return true; 
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('ROLLBACK TRAN');
		return true;
	}
	
	/*
		Usage:
		
		$this->BeginTrans();
		$this->RowLock('table1,table2','table1.id=33 and table2.id=table1.id'); # lock row 33 for both tables
		
		# some operation on both tables table1 and table2
		
		$this->CommitTrans();
		
		See http://www.swynk.com/friends/achigrik/SQL70Locks.asp
	*/
	function RowLock($tables,$where) 
	{
		if (!$this->transCnt) $this->BeginTrans();
		return $this->GetOne("select top 1 null as ignore from $tables with (ROWLOCK,HOLDLOCK) where $where");
	}
	
	//From: Fernando Moreira <FMoreira@imediata.pt>
	function MetaDatabases() 
	{ 
		if(@mssql_select_db("master")) { 
				 $qry="select name from sysdatabases where name <> 'master'"; 
				 if($rs=@mssql_query($qry)){ 
						 $tmpAr=$ar=array(); 
						 while($tmpAr=@mssql_fetch_row($rs)) 
								 $ar[]=$tmpAr[0]; 
						@mssql_select_db($this->databaseName); 
						 if(sizeof($ar)) 
								 return($ar); 
						 else 
								 return(false); 
				 } else { 
						 @mssql_select_db($this->databaseName); 
						 return(false); 
				 } 
		 } 
		 return(false); 
	} 

	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function MetaPrimaryKeys($table)
	{
		$sql = "select k.column_name from information_schema.key_column_usage k,
		information_schema.table_constraints tc 
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table'";
		
		$a = $this->GetCol($sql);
		if ($a && sizeof($a)>0) return $a;
		return false;	  
	}


 
	function SelectDB($dbName) 
	{
		$this->databaseName = $dbName;
		if ($this->_connectionID) {
			return @mssql_select_db($dbName);		
		}
		else return false;	
	}
	
	function ErrorMsg() 
	{
		if (empty($this->_errorMsg)){
			$this->_errorMsg = mssql_get_last_message();
		}
		return $this->_errorMsg;
	}
	
	function ErrorNo() 
	{
		if (empty($this->_errorMsg)) {
			$this->_errorMsg = mssql_get_last_message();
		}
		$id = @mssql_query("select @@ERROR",$this->_connectionID);
		if (!$id) return false;
		$arr = mssql_fetch_array($id);
		@mssql_free_result($id);
		if (is_array($arr)) return $arr[0];
	   else return -1;
	}
	
	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = mssql_connect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = mssql_pconnect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		
		// persistent connections can forget to rollback on crash, so we do it here.
		if ($this->autoRollback) {
			$cnt = $this->GetOne('select @@TRANCOUNT');
			while (--$cnt >= 0) $this->Execute('ROLLBACK TRAN'); 
		}
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	function Prepare($sql)
	{
		return $sql;
	}
	
	function PrepareSP($sql)
	{
		if (!$this->_has_mssql_init) {
			ADOConnection::outp( "PrepareSP: mssql_init only available since PHP 4.1.0");
			return $sql;
		}
		$stmt = mssql_init($sql,$this->_connectionID);
		if (!$stmt)  return $sql;
		return array($sql,$stmt);
	}
	
	/* 
	Usage:
		$stmt = $db->PrepareSP('SP_RUNSOMETHING'); -- takes 2 params, @myid and @group
		
		# note that the parameter does not have @ in front!
		$db->Parameter($stmt,$id,'myid');
		$db->Parameter($stmt,$group,'group',false,64);
		$db->Execute($stmt);
		
		@param $stmt Statement returned by Prepare() or PrepareSP().
		@param $var PHP variable to bind to. Can set to null (for isNull support).
		@param $name Name of stored procedure variable name to bind to.
		@param [$isOutput] Indicates direction of parameter 0/false=IN  1=OUT  2= IN/OUT. This is ignored in oci8.
		@param [$maxLen] Holds an maximum length of the variable.
		@param [$type] The data type of $var. Legal values depend on driver.
		
		See mssql_bind documentation at php.net.
	*/
	function Parameter(&$stmt, &$var, $name, $isOutput=false, $maxLen=4000, $type=false)
	{
		if (!$this->_has_mssql_init) {
			ADOConnection::outp( "Parameter: mssql_bind only available since PHP 4.1.0");
			return $sql;
		}

		$isNull = is_null($var); // php 4.0.4 and above...
			
		if ($type === false) 
			switch(gettype($var)) {
			default:
			case 'string': $type = SQLCHAR; break;
			case 'double': $type = SQLFLT8; break;
			case 'integer': $type = SQLINT4; break;
			case 'boolean': $type = SQLINT1; break; # SQLBIT not supported in 4.1.0
			}
		
		if  ($this->debug) {
			ADOConnection::outp( "Parameter(\$stmt, \$php_var='$var', \$name='$name'); (type=$type)");
		}
		return mssql_bind($stmt[1], '@'.$name, $var, $type, $isOutput, $isNull, $maxLen);
	}
	
	/* 
		Unfortunately, it appears that mssql cannot handle varbinary > 255 chars
		So all your blobs must be of type "image".
		
		Remember to set in php.ini the following...
		
		; Valid range 0 - 2147483647. Default = 4096. 
		mssql.textlimit = 0 ; zero to pass through 

		; Valid range 0 - 2147483647. Default = 4096. 
		mssql.textsize = 0 ; zero to pass through 
	*/
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		$sql = "UPDATE $table SET $column=0x".bin2hex($val)." WHERE $where";
		return $this->Execute($sql) != false;
	}
	
	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr)
	{
		$this->_errorMsg = false; 
		if (is_array($sql)) $rez = mssql_execute($sql[1]);
		else $rez = mssql_query($sql,$this->_connectionID);
		return $rez;
	}
	
	// returns true or false
	function _close()
	{ 
		if ($this->transCnt) $this->RollbackTrans();
		$rez = @mssql_close($this->_connectionID);
		$this->_connectionID = false;
		return $rez;
	}
	
	// mssql uses a default date like Dec 30 2000 12:00AM
	function UnixDate($v)
	{
		return ADORecordSet_array_mssql::UnixDate($v);
	}
	
	function UnixTimeStamp($v)
	{
		return ADORecordSet_array_mssql::UnixTimeStamp($v);
	}	
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_mssql extends ADORecordSet {	

	var $databaseType = "mssql";
	var $canSeek = true;
	// _mths works only in non-localised system
		
	function ADORecordset_mssql($id,$mode=false)
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		return $this->ADORecordSet($id,$mode);
	}
	
	
	function _initrs()
	{
	GLOBAL $ADODB_COUNTRECS;	
		$this->_numOfRows = ($ADODB_COUNTRECS)? @mssql_num_rows($this->_queryID):-1;
		$this->_numOfFields = @mssql_num_fields($this->_queryID);
	}
	

	//Contributed by "Sven Axelsson" <sven.axelsson@bokochwebb.se>
	// get next resultset - requires PHP 4.0.5 or later
	function NextRecordSet()
	{
		if (!mssql_next_result($this->_queryID)) return false;
		$this->_inited = false;
		$this->bind = false;
		$this->_currentRow = -1;
		$this->Init();
		return true;
	}

	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode != ADODB_FETCH_NUM) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}
	
	/*	Returns: an object containing field information. 
		Get column information in the Recordset object. fetchField() can be used in order to obtain information about
		fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
		fetchField() is retrieved.	*/

	function FetchField($fieldOffset = -1) 
	{
		if ($fieldOffset != -1) {
			return @mssql_fetch_field($this->_queryID, $fieldOffset);
		}
		else if ($fieldOffset == -1) {	/*	The $fieldOffset argument is not provided thus its -1 	*/
			return @mssql_fetch_field($this->_queryID);
		}
		return null;
	}
	
	function _seek($row) 
	{
		return @mssql_data_seek($this->_queryID, $row);
	}

	// speedup
	function MoveNext() 
	{
		if ($this->EOF) return false;
		
		$this->_currentRow++;
		
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM) {
				//ADODB_FETCH_BOTH mode
				$this->fields = @mssql_fetch_array($this->_queryID);
			}
			else {
				if (ADODB_PHPVER >= 0x4200) {// only for PHP 4.2.0 or later
					 $this->fields = @mssql_fetch_assoc($this->_queryID);
				} else {
					$flds = @mssql_fetch_array($this->_queryID);
					if (is_array($flds)) {
						$fassoc = array();
						foreach($flds as $k => $v) {
							if (is_numeric($k)) continue;
							$fassoc[$k] = $v;
						}
						$this->fields = $fassoc;
					}
				}
			}
			
			if (is_array($this->fields)) {
				if (ADODB_ASSOC_CASE == 0) {
					foreach($this->fields as $k=>$v) {
						$this->fields[strtolower($k)] = $v;
					}
				} else if (ADODB_ASSOC_CASE == 1) {
					foreach($this->fields as $k=>$v) {
						$this->fields[strtoupper($k)] = $v;
					}
				}
			}
		} else {
			$this->fields = @mssql_fetch_row($this->_queryID);
		}
		if ($this->fields) return true;
		$this->EOF = true;
		
		return false;
	}

	
	// INSERT UPDATE DELETE returns false even if no error occurs in 4.0.4
	// also the date format has been changed from YYYY-mm-dd to dd MMM YYYY in 4.0.4. Idiot!
	function _fetch($ignore_fields=false) 
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM) {
				//ADODB_FETCH_BOTH mode
				$this->fields = @mssql_fetch_array($this->_queryID);
			} else {
				if (ADODB_PHPVER >= 0x4200) // only for PHP 4.2.0 or later
					$this->fields = @mssql_fetch_assoc($this->_queryID);
				else {
					$this->fields = @mssql_fetch_array($this->_queryID);
					if (is_array($$this->fields)) {
						$fassoc = array();
						foreach($$this->fields as $k => $v) {
							if (is_integer($k)) continue;
							$fassoc[$k] = $v;
						}
						$this->fields = $fassoc;
					}
				}
			}
			
			if (!$this->fields) {
			} else if (ADODB_ASSOC_CASE == 0) {
				foreach($this->fields as $k=>$v) {
					$this->fields[strtolower($k)] = $v;
				}
			} else if (ADODB_ASSOC_CASE == 1) {
				foreach($this->fields as $k=>$v) {
					$this->fields[strtoupper($k)] = $v;
				}
			}
		} else {
			$this->fields = @mssql_fetch_row($this->_queryID);
		}
		return $this->fields;
	}
	
	/*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/

	function _close() 
	{
		$rez = mssql_free_result($this->_queryID);	
		$this->_queryID = false;
		return $rez;
	}
	// mssql uses a default date like Dec 30 2000 12:00AM
	function UnixDate($v)
	{
		return ADORecordSet_array_mssql::UnixDate($v);
	}
	
	function UnixTimeStamp($v)
	{
		return ADORecordSet_array_mssql::UnixTimeStamp($v);
	}
	
}


class ADORecordSet_array_mssql extends ADORecordSet_array {
	function ADORecordSet_array_mssql($id=-1,$mode=false) 
	{
		$this->ADORecordSet_array($id,$mode);
	}
	
		// mssql uses a default date like Dec 30 2000 12:00AM
	function UnixDate($v)
	{
	
		if (is_numeric(substr($v,0,1)) && ADODB_PHPVER >= 0x4200) return parent::UnixDate($v);
		
	global $ADODB_mssql_mths,$ADODB_mssql_date_order;
	
		//Dec 30 2000 12:00AM 
		if ($ADODB_mssql_date_order == 'dmy') {
			if (!preg_match( "|^([0-9]{1,2})[-/\. ]+([A-Za-z]{3})[-/\. ]+([0-9]{4})|" ,$v, $rr)) {
				return parent::UnixDate($v);
			}
			if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
			
			$theday = $rr[1];
			$themth =  substr(strtoupper($rr[2]),0,3);
		} else {
			if (!preg_match( "|^([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4})|" ,$v, $rr)) {
				return parent::UnixDate($v);
			}
			if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
			
			$theday = $rr[2];
			$themth = substr(strtoupper($rr[1]),0,3);
		}
		$themth = $ADODB_mssql_mths[$themth];
		if ($themth <= 0) return false;
		// h-m-s-MM-DD-YY
		return  mktime(0,0,0,$themth,$theday,$rr[3]);
	}
	
	function UnixTimeStamp($v)
	{
	
		if (is_numeric(substr($v,0,1)) && ADODB_PHPVER >= 0x4200) return parent::UnixTimeStamp($v);
		
	global $ADODB_mssql_mths,$ADODB_mssql_date_order;
	
		//Dec 30 2000 12:00AM
		 if ($ADODB_mssql_date_order == 'dmy') {
			 if (!preg_match( "|^([0-9]{1,2})[-/\. ]+([A-Za-z]{3})[-/\. ]+([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})|"
			,$v, $rr)) return parent::UnixTimeStamp($v);
			if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
		
			$theday = $rr[1];
			$themth =  substr(strtoupper($rr[2]),0,3);
		} else {
			if (!preg_match( "|^([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})|"
			,$v, $rr)) return parent::UnixTimeStamp($v);
			if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
		
			$theday = $rr[2];
			$themth = substr(strtoupper($rr[1]),0,3);
		}
		
		$themth = $ADODB_mssql_mths[$themth];
		if ($themth <= 0) return false;
		
		switch (strtoupper($rr[6])) {
		case 'P':
			if ($rr[4]<12) $rr[4] += 12;
			break;
		case 'A':
			if ($rr[4]==12) $rr[4] = 0;
			break;
		default:
			break;
		}
		// h-m-s-MM-DD-YY
		return  mktime($rr[4],$rr[5],0,$themth,$theday,$rr[3]);
	}
}

?>