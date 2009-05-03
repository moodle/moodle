<?php
/* 
V5.08 6 Apr 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Native mssql driver. Requires mssql client. Works on Windows. 
    http://www.microsoft.com/sql/technologies/php/default.mspx
  To configure for Unix, see 
   	http://phpbuilder.com/columns/alberto20000919.php3

    $stream = sqlsrv_get_field($stmt, $index, SQLSRV_SQLTYPE_STREAM(SQLSRV_ENC_BINARY));
    stream_filter_append($stream, "convert.iconv.ucs-2/utf-8"); // Voila, UTF-8 can be read directly from $stream

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!function_exists('sqlsrv_configure')) {
	die("mssqlnative extension not installed");
}

if (!function_exists('sqlsrv_set_error_handling')) {
	function sqlsrv_set_error_handling($constant) {
		sqlsrv_configure("WarningsReturnAsErrors", $constant);
	}
}
if (!function_exists('sqlsrv_log_set_severity')) {
	function sqlsrv_log_set_severity($constant) {
		sqlsrv_configure("LogSeverity", $constant);
	}
}
if (!function_exists('sqlsrv_log_set_subsystems')) {
	function sqlsrv_log_set_subsystems($constant) {
		sqlsrv_configure("LogSubsystems", $constant);
	}
}


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
//
// Also if your month is showing as month-1, 
//   e.g. Jan 13, 2002 is showing as 13/0/2002, then see
//     http://phplens.com/lens/lensforum/msgs.php?id=7048&x=1
//   it's a localisation problem.
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

class ADODB_mssqlnative extends ADOConnection {
	var $databaseType = "mssqlnative";	
	var $dataProvider = "mssqlnative";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasInsertID = true;
	var $substr = "substring";
	var $length = 'len';
	var $hasAffectedRows = true;
	var $poorAffectedRows = false;
	var $metaDatabasesSQL = "select name from sys.sysdatabases where name <> 'master'";
	var $metaTablesSQL="select name,case when type='U' then 'T' else 'V' end from sysobjects where (type='U' or type='V') and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE','dtproperties'))";
	var $metaColumnsSQL = # xtype==61 is datetime
        "select c.name,t.name,c.length,
	    (case when c.xusertype=61 then 0 else c.xprec end),
	    (case when c.xusertype=61 then 0 else c.xscale end) 
	    from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $hasGenID = true;
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	var $maxParameterLen = 4000;
	var $arrayClass = 'ADORecordSet_array_mssqlnative';
	var $uniqueSort = true;
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	var $identitySQL = 'select SCOPE_IDENTITY()'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	var $uniqueOrderBy = true;
	var $_bindInputArray = true;
	var $_dropSeqSQL = "drop table %s";
	
	function ADODB_mssqlnative() 
	{		
        if ($this->debug) {
            error_log("<pre>");
            sqlsrv_set_error_handling( SQLSRV_ERRORS_LOG_ALL );
            sqlsrv_log_set_severity( SQLSRV_LOG_SEVERITY_ALL );
            sqlsrv_log_set_subsystems(SQLSRV_LOG_SYSTEM_ALL);
            sqlsrv_configure('warnings_return_as_errors', 0);
        } else {
            sqlsrv_set_error_handling(0);
            sqlsrv_log_set_severity(0);
            sqlsrv_log_set_subsystems(SQLSRV_LOG_SYSTEM_ALL);
            sqlsrv_configure('warnings_return_as_errors', 0);
        }
	}

	function ServerInfo()
	{
    	global $ADODB_FETCH_MODE;
		if ($this->fetchMode === false) {
			$savem = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		} else 
			$savem = $this->SetFetchMode(ADODB_FETCH_NUM);
		$arrServerInfo = sqlsrv_server_info($this->_connectionID);
		$arr['description'] = $arrServerInfo['SQLServerName'].' connected to '.$arrServerInfo['CurrentDatabase'];
		$arr['version'] = $arrServerInfo['SQLServerVersion'];//ADOConnection::_findvers($arr['description']);
		return $arr;
	}
	
	function IfNull( $field, $ifNull ) 
	{
		return " ISNULL($field, $ifNull) "; // if MS SQL Server
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
        return sqlsrv_rows_affected($this->_queryID);
	}
	
	function CreateSequence($seq='adodbseq',$start=1)
	{
		if($this->debug) error_log("<hr>CreateSequence($seq,$start)");
        sqlsrv_begin_transaction($this->_connectionID);
		$start -= 1;
		$this->Execute("create table $seq (id int)");//was float(53)
		$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
		if (!$ok) {
            if($this->debug) error_log("<hr>Error: ROLLBACK");
            sqlsrv_rollback($this->_connectionID);
			return false;
		}
        sqlsrv_commit($this->_connectionID);
		return true;
	}

	function GenID($seq='adodbseq',$start=1)
	{
        if($this->debug) error_log("<hr>GenID($seq,$start)");
        sqlsrv_begin_transaction($this->_connectionID);
		$ok = $this->Execute("update $seq with (tablock,holdlock) set id = id + 1");
		if (!$ok) {
			$this->Execute("create table $seq (id int)");
			$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
			if (!$ok) {
                if($this->debug) error_log("<hr>Error: ROLLBACK");
                sqlsrv_rollback($this->_connectionID);
				return false;
			}
			sqlsrv_commit($this->_connectionID);
			return $start;
		}
		$num = $this->GetOne("select id from $seq");
        sqlsrv_commit($this->_connectionID);
        if($this->debug) error_log(" Returning: $num");
		return $num;
	}
	
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
				$s .= "datename(yyyy,$col)";
				break;
			case 'M':
				$s .= "convert(char(3),$col,0)";
				break;
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

	
	function BeginTrans()
	{
		if ($this->transOff) return true; 
		$this->transCnt += 1;
        if ($this->debug) error_log('<hr>begin transaction');
		sqlsrv_begin_transaction($this->_connectionID);
	   	return true;
	}
		
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true; 
        if ($this->debug) error_log('<hr>commit transaction');
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		sqlsrv_commit($this->_connectionID);
		return true;
	}
	function RollbackTrans()
	{
		if ($this->transOff) return true; 
        if ($this->debug) error_log('<hr>rollback transaction');
		if ($this->transCnt) $this->transCnt -= 1;
		sqlsrv_rollback($this->_connectionID);
		return true;
	}
	
	function SetTransactionMode( $transaction_mode ) 
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->Execute("SET TRANSACTION ".$transaction_mode);
	}
	
	/*
		Usage:
		
		$this->BeginTrans();
		$this->RowLock('table1,table2','table1.id=33 and table2.id=table1.id'); # lock row 33 for both tables
		
		# some operation on both tables table1 and table2
		
		$this->CommitTrans();
		
		See http://www.swynk.com/friends/achigrik/SQL70Locks.asp
	*/
	function RowLock($tables,$where,$flds='top 1 null as ignore') 
	{
		if (!$this->transCnt) $this->BeginTrans();
		return $this->GetOne("select $flds from $tables with (ROWLOCK,HOLDLOCK) where $where");
	}
	 
	function SelectDB($dbName) 
	{
		$this->database = $dbName;
		$this->databaseName = $dbName; # obsolete, retained for compat with older adodb versions
		if ($this->_connectionID) {
            $rs = $this->Execute('USE '.$dbName); 
            if($rs) {
                return true;
            } else return false;		
		}
		else return false;	
	}
	
	function ErrorMsg() 
	{
		$retErrors = sqlsrv_errors(SQLSRV_ERR_ALL);
		if($retErrors != null) {
			foreach($retErrors as $arrError) {
				$this->_errorMsg .= "SQLState: ".$arrError[ 'SQLSTATE']."\n";
				$this->_errorMsg .= "Error Code: ".$arrError[ 'code']."\n";
				$this->_errorMsg .= "Message: ".$arrError[ 'message']."\n";
			}
		} else {
			$this->_errorMsg = "No errors found";
		}
		return $this->_errorMsg;
	}
	
	function ErrorNo() 
	{
		if ($this->_logsql && $this->_errorCode !== false) return $this->_errorCode;
		$err = sqlsrv_errors(SQLSRV_ERR_ALL);
        if($err[0]) return $err[0]['code'];
        else return -1;
	}
	
	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('sqlsrv_connect')) return null;
        $connectionInfo = array("Database"=>$argDatabasename,'UID'=>$argUsername,'PWD'=>$argPassword);
        if ($this->debug) error_log("<hr>connecting... hostname: $argHostname params: ".var_export($connectionInfo,true));
        //if ($this->debug) error_log("<hr>_connectionID before: ".serialize($this->_connectionID));
        if(!($this->_connectionID = sqlsrv_connect($argHostname,$connectionInfo))) { 
            if ($this->debug) error_log( "<hr><b>errors</b>: ".print_r( sqlsrv_errors(), true));
            return false;
        }
        //if ($this->debug) error_log(" _connectionID after: ".serialize($this->_connectionID));
        //if ($this->debug) error_log("<hr>defined functions: <pre>".var_export(get_defined_functions(),true)."</pre>");
		return true;	
	}
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		//return null;//not implemented. NOTE: Persistent connections have no effect if PHP is used as a CGI program. (FastCGI!)
        return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename);
	}
	
	function Prepare($sql)
	{
		$stmt = sqlsrv_prepare( $this->_connectionID, $sql);
		if (!$stmt)  return $sql;
		return array($sql,$stmt);
	}
	
	// returns concatenated string
    // MSSQL requires integers to be cast as strings
    // automatically cast every datatype to VARCHAR(255)
    // @author David Rogers (introspectshun)
    function Concat()
    {
        $s = "";
        $arr = func_get_args();

        // Split single record on commas, if possible
        if (sizeof($arr) == 1) {
            foreach ($arr as $arg) {
                $args = explode(',', $arg);
            }
            $arr = $args;
        }

        array_walk($arr, create_function('&$v', '$v = "CAST(" . $v . " AS VARCHAR(255))";'));
        $s = implode('+',$arr);
        if (sizeof($arr) > 0) return "$s";
        
		return '';
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
	
		if (strtoupper($blobtype) == 'CLOB') {
			$sql = "UPDATE $table SET $column='" . $val . "' WHERE $where";
			return $this->Execute($sql) != false;
		}
		$sql = "UPDATE $table SET $column=0x".bin2hex($val)." WHERE $where";
		return $this->Execute($sql) != false;
	}
	
	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr=false)
	{
		$this->_errorMsg = false;
		if (is_array($inputarr)) {
            $rez = sqlsrv_query($this->_connectionID,$sql,$inputarr);
		} else if (is_array($sql)) {
            $rez = sqlsrv_query($this->_connectionID,$sql[1],$inputarr);
		} else {
			$rez = sqlsrv_query($this->_connectionID,$sql);
		}
        if ($this->debug) error_log("<hr>running query: ".var_export($sql,true)."<hr>input array: ".var_export($inputarr,true)."<hr>result: ".var_export($rez,true));//"<hr>connection: ".serialize($this->_connectionID)
        //fix for returning true on anything besides select statements
        if (is_array($sql)) $sql = $sql[1];
        $sql = ltrim($sql);
        if(stripos($sql, 'SELECT') !== 0 && $rez !== false) {
            if ($this->debug) error_log(" isn't a select query, returning boolean true");
            return true;
        }
        //end fix
        if(!$rez) $rez = false;
		return $rez;
	}
	
	// returns true or false
	function _close()
	{ 
		if ($this->transCnt) $this->RollbackTrans();
		$rez = @sqlsrv_close($this->_connectionID);
		$this->_connectionID = false;
		return $rez;
	}
	
	// mssql uses a default date like Dec 30 2000 12:00AM
	static function UnixDate($v)
	{
		return ADORecordSet_array_mssql::UnixDate($v);
	}
	
	static function UnixTimeStamp($v)
	{
		return ADORecordSet_array_mssql::UnixTimeStamp($v);
	}	

	function &MetaIndexes($table,$primary=false)
	{
		$table = $this->qstr($table);

		$sql = "SELECT i.name AS ind_name, C.name AS col_name, USER_NAME(O.uid) AS Owner, c.colid, k.Keyno, 
			CASE WHEN I.indid BETWEEN 1 AND 254 AND (I.status & 2048 = 2048 OR I.Status = 16402 AND O.XType = 'V') THEN 1 ELSE 0 END AS IsPK,
			CASE WHEN I.status & 2 = 2 THEN 1 ELSE 0 END AS IsUnique
			FROM dbo.sysobjects o INNER JOIN dbo.sysindexes I ON o.id = i.id 
			INNER JOIN dbo.sysindexkeys K ON I.id = K.id AND I.Indid = K.Indid 
			INNER JOIN dbo.syscolumns c ON K.id = C.id AND K.colid = C.Colid
			WHERE LEFT(i.name, 8) <> '_WA_Sys_' AND o.status >= 0 AND O.Name LIKE $table
			ORDER BY O.name, I.Name, K.keyno";

		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
        	$savem = $this->SetFetchMode(FALSE);
        }
        
        $rs = $this->Execute($sql);
        if (isset($savem)) {
        	$this->SetFetchMode($savem);
        }
        $ADODB_FETCH_MODE = $save;

        if (!is_object($rs)) {
        	return FALSE;
        }

		$indexes = array();
		while ($row = $rs->FetchRow()) {
			if (!$primary && $row[5]) continue;
			
            $indexes[$row[0]]['unique'] = $row[6];
            $indexes[$row[0]]['columns'][] = $row[1];
    	}
        return $indexes;
	}
	
	function MetaForeignKeys($table, $owner=false, $upper=false)
	{
    	global $ADODB_FETCH_MODE;
	
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$table = $this->qstr(strtoupper($table));
		
		$sql = 
            "select object_name(constid) as constraint_name,
	            col_name(fkeyid, fkey) as column_name,
	            object_name(rkeyid) as referenced_table_name,
   	            col_name(rkeyid, rkey) as referenced_column_name
            from sysforeignkeys
            where upper(object_name(fkeyid)) = $table
            order by constraint_name, referenced_table_name, keyno";
		
		$constraints =& $this->GetArray($sql);
		
		$ADODB_FETCH_MODE = $save;
		
		$arr = false;
		foreach($constraints as $constr) {
			//print_r($constr);
			$arr[$constr[0]][$constr[2]][] = $constr[1].'='.$constr[3]; 
		}
		if (!$arr) return false;
		
		$arr2 = false;
		
		foreach($arr as $k => $v) {
			foreach($v as $a => $b) {
				if ($upper) $a = strtoupper($a);
				$arr2[$a] = $b;
			}
		}
		return $arr2;
	}

	//From: Fernando Moreira <FMoreira@imediata.pt>
	function MetaDatabases() 
	{ 
	    $this->SelectDB("master");
        $rs =& $this->Execute($this->metaDatabasesSQL);
        $rows = $rs->GetRows();
        $ret = array();
        for($i=0;$i<count($rows);$i++) {
            $ret[] = $rows[$i][0];
        }
        $this->SelectDB($this->database);
        if($ret)
            return $ret;
        else 
            return false;
	} 

	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function &MetaPrimaryKeys($table)
	{
    	global $ADODB_FETCH_MODE;
	
		$schema = '';
		$this->_findschema($table,$schema);
		if (!$schema) $schema = $this->database;
		if ($schema) $schema = "and k.table_catalog like '$schema%'"; 

		$sql = "select distinct k.column_name,ordinal_position from information_schema.key_column_usage k,
		information_schema.table_constraints tc 
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table' $schema order by ordinal_position ";
		
		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$a = $this->GetCol($sql);
		$ADODB_FETCH_MODE = $savem;
		
		if ($a && sizeof($a)>0) return $a;
		$false = false;
		return $false;	  
	}

	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{
	    if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(($mask));
			$this->metaTablesSQL .= " AND name like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_mssqlnative extends ADORecordSet {	

	var $databaseType = "mssqlnative";
	var $canSeek = false;
	var $fieldOffset = 0;
	// _mths works only in non-localised system
	
	function ADORecordset_mssqlnative($id,$mode=false)
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
	    global $ADODB_COUNTRECS;	
        if ($this->connection->debug) error_log("(before) ADODB_COUNTRECS: {$ADODB_COUNTRECS} _numOfRows: {$this->_numOfRows} _numOfFields: {$this->_numOfFields}");
        /*$retRowsAff = sqlsrv_rows_affected($this->_queryID);//"If you need to determine the number of rows a query will return before retrieving the actual results, appending a SELECT COUNT ... query would let you get that information, and then a call to next_result would move you to the "real" results."
        error_log("rowsaff: ".serialize($retRowsAff));
		$this->_numOfRows = ($ADODB_COUNTRECS)? $retRowsAff:-1;*/
        $this->_numOfRows = -1;//not supported
        $fieldmeta = sqlsrv_field_metadata($this->_queryID);
        $this->_numOfFields = ($fieldmeta)? count($fieldmeta):-1;
        if ($this->connection->debug) error_log("(after) _numOfRows: {$this->_numOfRows} _numOfFields: {$this->_numOfFields}");
	}
	

	//Contributed by "Sven Axelsson" <sven.axelsson@bokochwebb.se>
	// get next resultset - requires PHP 4.0.5 or later
	function NextRecordSet()
	{
		if (!sqlsrv_next_result($this->_queryID)) return false;
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

	function &FetchField($fieldOffset = -1) 
	{
        if ($this->connection->debug) error_log("<hr>fetchfield: $fieldOffset, fetch array: <pre>".print_r($this->fields,true)."</pre> backtrace: ".adodb_backtrace(false));
		if ($fieldOffset != -1) $this->fieldOffset = $fieldOffset;
		$arrKeys = array_keys($this->fields);
		if(array_key_exists($this->fieldOffset,$arrKeys) && !array_key_exists($arrKeys[$this->fieldOffset],$this->fields)) {
			$f = false;
		} else {
			$f = $this->fields[ $arrKeys[$this->fieldOffset] ];
			if($fieldOffset == -1) $this->fieldOffset++;
		}

        if (empty($f)) {
            $f = false;//PHP Notice: Only variable references should be returned by reference
        }
		return $f;
	}
	
	function _seek($row) 
	{
		return false;//There is no support for cursors in the driver at this time.  All data is returned via forward-only streams.
	}

	// speedup
	function MoveNext() 
	{
        if ($this->connection->debug) error_log("movenext()");
        //if ($this->connection->debug) error_log("eof (beginning): ".$this->EOF);
		if ($this->EOF) return false;
		
		$this->_currentRow++;
        if ($this->connection->debug) error_log("_currentRow: ".$this->_currentRow);
		
		if ($this->_fetch()) return true;
		$this->EOF = true;
        //if ($this->connection->debug) error_log("eof (end): ".$this->EOF);
		
		return false;
	}

	
	// INSERT UPDATE DELETE returns false even if no error occurs in 4.0.4
	// also the date format has been changed from YYYY-mm-dd to dd MMM YYYY in 4.0.4. Idiot!
	function _fetch($ignore_fields=false) 
	{
        if ($this->connection->debug) error_log("_fetch()");
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM) {
                if ($this->connection->debug) error_log("fetch mode: both");
				$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_BOTH);
			} else {
                if ($this->connection->debug) error_log("fetch mode: assoc");
				$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_ASSOC);
			}
			
			if (ADODB_ASSOC_CASE == 0) {
				foreach($this->fields as $k=>$v) {
					$this->fields[strtolower($k)] = $v;
				}
			} else if (ADODB_ASSOC_CASE == 1) {
				foreach($this->fields as $k=>$v) {
					$this->fields[strtoupper($k)] = $v;
				}
			}
		} else {
            if ($this->connection->debug) error_log("fetch mode: num");
			$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_NUMERIC);
		}
        if(is_array($this->fields) && array_key_exists(1,$this->fields) && !array_key_exists(0,$this->fields)) {//fix fetch numeric keys since they're not 0 based 
            $arrFixed = array();
            foreach($this->fields as $key=>$value) {
                if(is_numeric($key)) {
                    $arrFixed[$key-1] = $value;
                } else {
                    $arrFixed[$key] = $value;
                }
            }
            //if($this->connection->debug) error_log("<hr>fixing non 0 based return array, old: ".print_r($this->fields,true)." new: ".print_r($arrFixed,true));
            $this->fields = $arrFixed;
        }
		if(is_array($this->fields)) {
			foreach($this->fields as $key=>$value) {
				if (is_object($value) && method_exists($value, 'format')) {//is DateTime object
					$this->fields[$key] = $value->format("Y-m-d\TH:i:s\Z");
				}
			}
		}
        if($this->fields === null) $this->fields = false;
        if ($this->connection->debug) error_log("<hr>after _fetch, fields: <pre>".print_r($this->fields,true)." backtrace: ".adodb_backtrace(false));
		return $this->fields;
	}
	
    /*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/
	function _close() 
	{
		$rez = sqlsrv_free_stmt($this->_queryID);	
		$this->_queryID = false;
		return $rez;
	}

	// mssql uses a default date like Dec 30 2000 12:00AM
	static function UnixDate($v)
	{
		return ADORecordSet_array_mssqlnative::UnixDate($v);
	}
	
	static function UnixTimeStamp($v)
	{
		return ADORecordSet_array_mssqlnative::UnixTimeStamp($v);
	}
}


class ADORecordSet_array_mssqlnative extends ADORecordSet_array {
	function ADORecordSet_array_mssqlnative($id=-1,$mode=false) 
	{
		$this->ADORecordSet_array($id,$mode);
	}
	
		// mssql uses a default date like Dec 30 2000 12:00AM
	static function UnixDate($v)
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
	
	static function UnixTimeStamp($v)
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

/*
Code Example 1:

select 	object_name(constid) as constraint_name,
       	object_name(fkeyid) as table_name, 
        col_name(fkeyid, fkey) as column_name,
	object_name(rkeyid) as referenced_table_name,
   	col_name(rkeyid, rkey) as referenced_column_name
from sysforeignkeys
where object_name(fkeyid) = x
order by constraint_name, table_name, referenced_table_name,  keyno

Code Example 2:
select 	constraint_name,
	column_name,
	ordinal_position
from information_schema.key_column_usage
where constraint_catalog = db_name()
and table_name = x
order by constraint_name, ordinal_position

http://www.databasejournal.com/scripts/article.php/1440551
*/

?>