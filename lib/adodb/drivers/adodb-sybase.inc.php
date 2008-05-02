<?php
/* 
V5.04a 25 Mar 2008   (c) 2000-2008 John Lim. All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Sybase driver contributed by Toni (toni.tunkkari@finebyte.com)
  
  - MSSQL date patch applied.
  
  Date patch by Toni 15 Feb 2002
*/
 
 // security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB_sybase extends ADOConnection {
	var $databaseType = "sybase";	
	var $dataProvider = 'sybase';
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasInsertID = true;
	var $hasAffectedRows = true;
  	var $metaTablesSQL="select name from sysobjects where type='U' or type='V'";
	// see http://sybooks.sybase.com/onlinebooks/group-aw/awg0800e/dbrfen8/@ebt-link;pt=5981;uf=0?target=0;window=new;showtoc=true;book=dbrfen8
	var $metaColumnsSQL = "SELECT c.column_name, c.column_type, c.width FROM syscolumn c, systable t WHERE t.table_name='%s' AND c.table_id=t.table_id AND t.table_type='BASE'";
	/*
	"select c.name,t.name,c.length from 
	syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id 
	where o.name='%s'";
	*/
	var $concat_operator = '+'; 
	var $arrayClass = 'ADORecordSet_array_sybase';
	var $sysDate = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	
	function ADODB_sybase() 
	{			
	}
 
	// might require begintrans -- committrans
	function _insertid()
	{
		return $this->GetOne('select @@identity');
	}
	  // might require begintrans -- committrans
	function _affectedrows()
	{
	   return $this->GetOne('select @@rowcount');
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
	
		$this->transCnt -= 1;
		$this->Execute('COMMIT TRAN');
		return true;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt -= 1;
		$this->Execute('ROLLBACK TRAN');
		return true;
	}
	
	// http://www.isug.com/Sybase_FAQ/ASE/section6.1.html#6.1.4
	function RowLock($tables,$where,$flds='top 1 null as ignore') 
	{
		if (!$this->_hastrans) $this->BeginTrans();
		$tables = str_replace(',',' HOLDLOCK,',$tables);
		return $this->GetOne("select $flds from $tables HOLDLOCK where $where");
		
	}	
		
	function SelectDB($dbName) 
	{
		$this->database = $dbName;
		$this->databaseName = $dbName; # obsolete, retained for compat with older adodb versions
		if ($this->_connectionID) {
			return @sybase_select_db($dbName);		
		}
		else return false;	
	}

	/*	Returns: the last error message from previous database operation
		Note: This function is NOT available for Microsoft SQL Server.	*/	

	
	function ErrorMsg()
	{
		if ($this->_logsql) return $this->_errorMsg;
		if (function_exists('sybase_get_last_message'))
			$this->_errorMsg = sybase_get_last_message();
		else
			$this->_errorMsg = isset($php_errormsg) ? $php_errormsg : 'SYBASE error messages not supported on this platform';
		return $this->_errorMsg;
	}

	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('sybase_connect')) return null;
		
		$this->_connectionID = sybase_connect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('sybase_connect')) return null;
		
		$this->_connectionID = sybase_pconnect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr)
	{
	global $ADODB_COUNTRECS;
	
		if ($ADODB_COUNTRECS == false && ADODB_PHPVER >= 0x4300)
			return sybase_unbuffered_query($sql,$this->_connectionID);
		else
			return sybase_query($sql,$this->_connectionID);
	}
	
	// See http://www.isug.com/Sybase_FAQ/ASE/section6.2.html#6.2.12
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	{
		if ($secs2cache > 0) {// we do not cache rowcount, so we have to load entire recordset
			$rs = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
			return $rs;
		}
		
		$nrows = (integer) $nrows;
		$offset = (integer) $offset;
		
		$cnt = ($nrows >= 0) ? $nrows : 999999999;
		if ($offset > 0 && $cnt) $cnt += $offset;
		
		$this->Execute("set rowcount $cnt"); 
		$rs = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,0);
		$this->Execute("set rowcount 0");
		
		return $rs;
	}

	// returns true or false
	function _close()
	{ 
		return @sybase_close($this->_connectionID);
	}
	
	function UnixDate($v)
	{
		return ADORecordSet_array_sybase::UnixDate($v);
	}
	
	function UnixTimeStamp($v)
	{
		return ADORecordSet_array_sybase::UnixTimeStamp($v);
	}	
	
	

	# Added 2003-10-05 by Chris Phillipson
    # Used ASA SQL Reference Manual -- http://sybooks.sybase.com/onlinebooks/group-aw/awg0800e/dbrfen8/@ebt-link;pt=16756?target=%25N%15_12018_START_RESTART_N%25
    # to convert similar Microsoft SQL*Server (mssql) API into Sybase compatible version
    // Format date column in sql string given an input format that understands Y M D
    function SQLDate($fmt, $col=false)
    {
        if (!$col) $col = $this->sysTimeStamp;
        $s = '';

        $len = strlen($fmt);
        for ($i=0; $i < $len; $i++) {
            if ($s) $s .= '+';
            $ch = $fmt[$i];
            switch($ch) {
            case 'Y':
            case 'y':
                $s .= "datename(yy,$col)";
                break;
            case 'M':
                $s .= "convert(char(3),$col,0)";
                break;
            case 'm':
                $s .= "replace(str(month($col),2),' ','0')";
                break;
            case 'Q':
            case 'q':
                $s .= "datename(qq,$col)";
                break;
            case 'D':
            case 'd':
                $s .= "replace(str(datepart(dd,$col),2),' ','0')";
                break;
            case 'h':
                $s .= "substring(convert(char(14),$col,0),13,2)";
                break;

            case 'H':
                $s .= "replace(str(datepart(hh,$col),2),' ','0')";
                break;

            case 'i':
                $s .= "replace(str(datepart(mi,$col),2),' ','0')";
                break;
            case 's':
                $s .= "replace(str(datepart(ss,$col),2),' ','0')";
                break;
            case 'a':
            case 'A':
                $s .= "substring(convert(char(19),$col,0),18,2)";
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
	
	# Added 2003-10-07 by Chris Phillipson
    # Used ASA SQL Reference Manual -- http://sybooks.sybase.com/onlinebooks/group-aw/awg0800e/dbrfen8/@ebt-link;pt=5981;uf=0?target=0;window=new;showtoc=true;book=dbrfen8
    # to convert similar Microsoft SQL*Server (mssql) API into Sybase compatible version
    function MetaPrimaryKeys($table)
    {
        $sql = "SELECT c.column_name " .
               "FROM syscolumn c, systable t " .
               "WHERE t.table_name='$table' AND c.table_id=t.table_id " .
               "AND t.table_type='BASE' " .
               "AND c.pkey = 'Y' " .
               "ORDER BY c.column_id";

        $a = $this->GetCol($sql);
        if ($a && sizeof($a)>0) return $a;
        return false;
    }
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/
global $ADODB_sybase_mths;
$ADODB_sybase_mths = array(
	'JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MAY'=>5,'JUN'=>6,
	'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12);

class ADORecordset_sybase extends ADORecordSet {	

	var $databaseType = "sybase";
	var $canSeek = true;
	// _mths works only in non-localised system
	var  $_mths = array('JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MAY'=>5,'JUN'=>6,'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12);	

	function ADORecordset_sybase($id,$mode=false)
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		if (!$mode) $this->fetchMode = ADODB_FETCH_ASSOC;
		else $this->fetchMode = $mode;
		$this->ADORecordSet($id,$mode);
	}
	
	/*	Returns: an object containing field information. 
		Get column information in the Recordset object. fetchField() can be used in order to obtain information about
		fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
		fetchField() is retrieved.	*/
	function FetchField($fieldOffset = -1) 
	{
		if ($fieldOffset != -1) {
			$o = @sybase_fetch_field($this->_queryID, $fieldOffset);
		}
		else if ($fieldOffset == -1) {	/*	The $fieldOffset argument is not provided thus its -1 	*/
			$o = @sybase_fetch_field($this->_queryID);
		}
		// older versions of PHP did not support type, only numeric
		if ($o && !isset($o->type)) $o->type = ($o->numeric) ? 'float' : 'varchar';
		return $o;
	}
	
	function _initrs()
	{
	global $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS)? @sybase_num_rows($this->_queryID):-1;
		$this->_numOfFields = @sybase_num_fields($this->_queryID);
	}
	
	function _seek($row) 
	{
		return @sybase_data_seek($this->_queryID, $row);
	}		

	function _fetch($ignore_fields=false) 
	{
		if ($this->fetchMode == ADODB_FETCH_NUM) {
			$this->fields = @sybase_fetch_row($this->_queryID);
		} else if ($this->fetchMode == ADODB_FETCH_ASSOC) {
			$this->fields = @sybase_fetch_row($this->_queryID);
			if (is_array($this->fields)) {
				$this->fields = $this->GetRowAssoc(ADODB_ASSOC_CASE);
				return true;
			}
			return false;
		}  else {
			$this->fields = @sybase_fetch_array($this->_queryID);
		}
		if ( is_array($this->fields)) {
			return true;
		}

		return false;
	}
	
	/*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/
	function _close() {
		return @sybase_free_result($this->_queryID);		
	}
	
	// sybase/mssql uses a default date like Dec 30 2000 12:00AM
	function UnixDate($v)
	{
		return ADORecordSet_array_sybase::UnixDate($v);
	}
	
	function UnixTimeStamp($v)
	{
		return ADORecordSet_array_sybase::UnixTimeStamp($v);
	}
}

class ADORecordSet_array_sybase extends ADORecordSet_array {
	function ADORecordSet_array_sybase($id=-1)
	{
		$this->ADORecordSet_array($id);
	}
	
		// sybase/mssql uses a default date like Dec 30 2000 12:00AM
	function UnixDate($v)
	{
	global $ADODB_sybase_mths;
	
		//Dec 30 2000 12:00AM
		if (!ereg( "([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4})"
			,$v, $rr)) return parent::UnixDate($v);
			
		if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
		
		$themth = substr(strtoupper($rr[1]),0,3);
		$themth = $ADODB_sybase_mths[$themth];
		if ($themth <= 0) return false;
		// h-m-s-MM-DD-YY
		return  mktime(0,0,0,$themth,$rr[2],$rr[3]);
	}
	
	function UnixTimeStamp($v)
	{
	global $ADODB_sybase_mths;
		//11.02.2001 Toni Tunkkari toni.tunkkari@finebyte.com
		//Changed [0-9] to [0-9 ] in day conversion
		if (!ereg( "([A-Za-z]{3})[-/\. ]([0-9 ]{1,2})[-/\. ]([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})"
			,$v, $rr)) return parent::UnixTimeStamp($v);
		if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
		
		$themth = substr(strtoupper($rr[1]),0,3);
		$themth = $ADODB_sybase_mths[$themth];
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
		return  mktime($rr[4],$rr[5],0,$themth,$rr[2],$rr[3]);
	}
}
?>
