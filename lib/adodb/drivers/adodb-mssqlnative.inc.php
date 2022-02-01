<?php
/**
 * Native MSSQL driver.
 *
 * Requires mssql client. Works on Windows.
 * https://docs.microsoft.com/sql/connect/php
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
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

class ADODB_mssqlnative extends ADOConnection {
	var $databaseType = "mssqlnative";
	var $dataProvider = "mssqlnative";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d\TH:i:s'";
	/**
	 * Enabling InsertID capability will cause execution of an extra query
	 * {@see $identitySQL} after each INSERT statement. To improve performance
	 * when inserting a large number of records, you should switch this off by
	 * calling {@see enableLastInsertID enableLastInsertID(false)}.
	 * @var bool $hasInsertID
	 */
	var $hasInsertID = true;
	var $substr = "substring";
	var $length = 'len';
	var $hasAffectedRows = true;
	var $poorAffectedRows = false;
	var $metaDatabasesSQL = "select name from sys.sysdatabases where name <> 'master'";
	var $metaTablesSQL="select name,case when type='U' then 'T' else 'V' end from sysobjects where (type='U' or type='V') and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE','dtproperties'))";
	var $metaColumnsSQL =
		"select c.name,
		t.name as type,
		c.length,
		c.xprec as precision,
		c.xscale as scale,
		c.isnullable as nullable,
		c.cdefault as default_value,
		c.xtype,
		t.length as type_length,
		sc.is_identity
		from syscolumns c
		join systypes t on t.xusertype=c.xusertype
		join sysobjects o on o.id=c.id
		join sys.tables st on st.name=o.name
		join sys.columns sc on sc.object_id = st.object_id and sc.name=c.name
		where o.name='%s'";
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

	var $connectionInfo    = array('ReturnDatesAsStrings'=>true);
	var $cachedSchemaFlush = false;

	var $sequences = false;
	var $mssql_version = '';

	function __construct()
	{
		if ($this->debug) {
			ADOConnection::outp("<pre>");
			sqlsrv_set_error_handling( SQLSRV_ERRORS_LOG_ALL );
			sqlsrv_log_set_severity( SQLSRV_LOG_SEVERITY_ALL );
			sqlsrv_log_set_subsystems(SQLSRV_LOG_SYSTEM_ALL);
			sqlsrv_configure('WarningsReturnAsErrors', 0);
		} else {
			sqlsrv_set_error_handling(0);
			sqlsrv_log_set_severity(0);
			sqlsrv_log_set_subsystems(SQLSRV_LOG_SYSTEM_ALL);
			sqlsrv_configure('WarningsReturnAsErrors', 0);
		}
	}

	/**
	 * Initializes the SQL Server version.
	 * Dies if connected to a non-supported version (2000 and older)
	 */
	function ServerVersion() {
		$data = $this->ServerInfo();
		preg_match('/^\d{2}/', $data['version'], $matches);
		$version = (int)reset($matches);

		// We only support SQL Server 2005 and up
		if($version < 9) {
			die("SQL SERVER VERSION {$data['version']} NOT SUPPORTED IN mssqlnative DRIVER");
		}

		$this->mssql_version = $version;
	}

	function ServerInfo() {
		global $ADODB_FETCH_MODE;
		static $arr = false;
		if (is_array($arr))
			return $arr;
		if ($this->fetchMode === false) {
			$savem = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		} elseif ($this->fetchMode >=0 && $this->fetchMode <=2) {
			$savem = $this->fetchMode;
		} else
			$savem = $this->SetFetchMode(ADODB_FETCH_NUM);

		$arrServerInfo = sqlsrv_server_info($this->_connectionID);
		$ADODB_FETCH_MODE = $savem;
		$arr['description'] = $arrServerInfo['SQLServerName'].' connected to '.$arrServerInfo['CurrentDatabase'];
		$arr['version'] = $arrServerInfo['SQLServerVersion'];//ADOConnection::_findvers($arr['description']);
		return $arr;
	}

	function IfNull( $field, $ifNull )
	{
		return " ISNULL($field, $ifNull) "; // if MS SQL Server
	}

	public function enableLastInsertID($enable = true) {
		$this->hasInsertID = $enable;
		$this->lastInsID = false;
	}

	/**
	 * Get the last value inserted into an IDENTITY column.
	 *
	 * The value will actually be set in {@see _query()} when executing an
	 * INSERT statement, but only if the connection's $hasInsertId property
	 * is true; this can be set with {@see enableLastInsertId()}.
	 *
	 * @inheritDoc
	 */
	protected function _insertID($table = '', $column = '')
	{
		return $this->lastInsID;
	}

	function _affectedrows()
	{
		if ($this->_queryID)
		return sqlsrv_rows_affected($this->_queryID);
	}

	function GenID($seq='adodbseq',$start=1) {
		switch($this->mssql_version){
		case 9:
		case 10:
			return $this->GenID2008($seq, $start);
			break;
		default:
			return $this->GenID2012($seq, $start);
			break;
		}
	}

	function CreateSequence($seq='adodbseq',$start=1)
	{
		switch($this->mssql_version){
		case 9:
		case 10:
			return $this->CreateSequence2008($seq, $start);
			break;
		default:
			return $this->CreateSequence2012($seq, $start);
			break;
		}
	}

	/**
	 * For Server 2005,2008, duplicate a sequence with an identity table
	 */
	function CreateSequence2008($seq='adodbseq',$start=1)
	{
		if($this->debug) ADOConnection::outp("<hr>CreateSequence($seq,$start)");
		sqlsrv_begin_transaction($this->_connectionID);
		$start -= 1;
		$this->Execute("create table $seq (id int)");//was float(53)
		$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
		if (!$ok) {
			if($this->debug) ADOConnection::outp("<hr>Error: ROLLBACK");
			sqlsrv_rollback($this->_connectionID);
			return false;
		}
		sqlsrv_commit($this->_connectionID);
		return true;
	}

	/**
	 * Proper Sequences Only available to Server 2012 and up
	 */
	function CreateSequence2012($seq='adodbseq',$start=1){
		if (!$this->sequences){
			$sql = "SELECT name FROM sys.sequences";
			$this->sequences = $this->GetCol($sql);
		}
		$ok = $this->Execute("CREATE SEQUENCE $seq START WITH $start INCREMENT BY 1");
		if (!$ok)
			die("CANNOT CREATE SEQUENCE" . print_r(sqlsrv_errors(),true));
		$this->sequences[] = $seq;
	}

	/**
	 * For Server 2005,2008, duplicate a sequence with an identity table
	 */
	function GenID2008($seq='adodbseq',$start=1)
	{
		if($this->debug) ADOConnection::outp("<hr>CreateSequence($seq,$start)");
		sqlsrv_begin_transaction($this->_connectionID);
		$ok = $this->Execute("update $seq with (tablock,holdlock) set id = id + 1");
		if (!$ok) {
			$start -= 1;
			$this->Execute("create table $seq (id int)");//was float(53)
			$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
			if (!$ok) {
				if($this->debug) ADOConnection::outp("<hr>Error: ROLLBACK");
				sqlsrv_rollback($this->_connectionID);
				return false;
			}
		}
		$num = $this->GetOne("select id from $seq");
		sqlsrv_commit($this->_connectionID);
		return $num;
	}
	/**
	 * Only available to Server 2012 and up
	 * Cannot do this the normal adodb way by trapping an error if the
	 * sequence does not exist because sql server will auto create a
	 * sequence with the starting number of -9223372036854775808
	 */
	function GenID2012($seq='adodbseq',$start=1)
	{

		/*
		 * First time in create an array of sequence names that we
		 * can use in later requests to see if the sequence exists
		 * the overhead is creating a list of sequences every time
		 * we need access to at least 1. If we really care about
		 * performance, we could maybe flag a 'nocheck' class variable
		 */
		if (!$this->sequences){
			$sql = "SELECT name FROM sys.sequences";
			$this->sequences = $this->GetCol($sql);
		}
		if (!is_array($this->sequences)
		|| is_array($this->sequences) && !in_array($seq,$this->sequences)){
			$this->CreateSequence2012($seq, $start);

		}
		$num = $this->GetOne("SELECT NEXT VALUE FOR $seq");
		return $num;
	}

	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{
		if (!$col) {
			$col = $this->sysTimeStamp;
		}
		$s = '';

		$ConvertableFmt=array(
			"m/d/Y"=>101,  "m/d/y"=>101 // US
			,"Y.m.d"=>102, "y.m.d"=>102 // ANSI
			,"d/m/Y"=>103, "d/m/y"=>103 // French /english
			,"d.m.Y"=>104, "d.m.y"=>104 // German
			,"d-m-Y"=>105, "d-m-y"=>105 // Italian
			,"m-d-Y"=>110, "m-d-y"=>110 // US Dash
			,"Y/m/d"=>111, "y/m/d"=>111 // Japan
			,"Ymd"=>112,   "ymd"=>112   // ISO
			,"H:i:s"=>108 // Time
		);
		if (key_exists($fmt,$ConvertableFmt)) {
			return "convert (varchar ,$col," . $ConvertableFmt[$fmt] . ")";
		}

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
			case 'l':
				$s .= "datename(dw,$col)";
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
		if ($this->debug) ADOConnection::outp('<hr>begin transaction');
		sqlsrv_begin_transaction($this->_connectionID);
		return true;
	}

	function CommitTrans($ok=true)
	{
		if ($this->transOff) return true;
		if ($this->debug) ADOConnection::outp('<hr>commit transaction');
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		sqlsrv_commit($this->_connectionID);
		return true;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->debug) ADOConnection::outp('<hr>rollback transaction');
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
	function RowLock($tables,$where,$col='1 as adodbignore')
	{
		if ($col == '1 as adodbignore') $col = 'top 1 null as ignore';
		if (!$this->transCnt) $this->BeginTrans();
		return $this->GetOne("select $col from $tables with (ROWLOCK,HOLDLOCK) where $where");
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
		}
		return $this->_errorMsg;
	}

	function ErrorNo()
	{
		$err = sqlsrv_errors(SQLSRV_ERR_ALL);
		if ($err && $err[0])
			return $err[0]['code'];
		else
			return 0;
	}

	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('sqlsrv_connect'))
		{
			if ($this->debug)
				ADOConnection::outp('Microsoft SQL Server native driver (mssqlnative) not installed');
			return null;
		}

		if (!empty($this->port))
			/*
			* Port uses a comma
			*/
			$argHostname .= ",".$this->port;

		$connectionInfo 			= $this->connectionInfo;
		$connectionInfo["Database"]	= $argDatabasename;
		if ((string)$argUsername != '' || (string)$argPassword != '')
		{
			/*
			* If they pass either a userid or password, we assume
			* SQL Server authentication
			*/
			$connectionInfo["UID"]		= $argUsername;
			$connectionInfo["PWD"]		= $argPassword;

			if ($this->debug)
				ADOConnection::outp('userid or password supplied, attempting connection with SQL Server Authentication');

		}
		else
		{
			/*
			* If they don't pass either value, we won't add them to the
			* connection parameters. This will then force an attempt
			* to use windows authentication
			*/
			if ($this->debug)

				ADOConnection::outp('No userid or password supplied, attempting connection with Windows Authentication');
		}


		/*
		* Now merge in the passed connection parameters setting
		*/
		foreach ($this->connectionParameters as $options)
		{
			foreach($options as $parameter=>$value)
				$connectionInfo[$parameter] = $value;
		}

		if ($this->debug) ADOConnection::outp("connecting to host: $argHostname params: ".var_export($connectionInfo,true));
		if(!($this->_connectionID = @sqlsrv_connect($argHostname,$connectionInfo)))
		{
			if ($this->debug)
				ADOConnection::outp( 'Connection Failed: '.print_r( sqlsrv_errors(), true));
			return false;
		}

		$this->ServerVersion();

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
		return $sql; // prepare does not work properly with bind parameters as bind parameters are managed by sqlsrv_prepare!
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

		array_walk(
			$arr,
			function(&$value, $key) {
				$value = "CAST(" . $value . " AS VARCHAR(255))";
			}
		);
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

	/**
	 * Execute a query.
	 *
	 * If executing an INSERT statement and $hasInsertId is true, will set
	 * $lastInsId.
	 *
	 * @param string $sql
	 * @param array $inputarr
	 * @return resource|false Query Id if successful, otherwise false
	 */
	function _query($sql, $inputarr = false)
	{
		$this->_errorMsg = false;

		if (is_array($sql)) {
			$sql = $sql[1];
		}

		// Handle native driver flaw for retrieving the last insert ID
		if ($this->hasInsertID) {
			// Check if it's an INSERT statement
			$retrieveLastInsertID = preg_match(
				'/^\W*insert[\s\w()[\]",.]+values\s*\((?:[^;\']|\'\'|(?:(?:\'\')*\'[^\']+\'(?:\'\')*))*;?$/i',
				$sql
			);
			if ($retrieveLastInsertID) {
				// Append the identity SQL, so it is executed in the same
				// scope as the insert query.
				$sql .= '; ' . $this->identitySQL;
			}
		} else {
			$retrieveLastInsertID = false;
		}

		if ($inputarr) {
			// Ensure that the input array is indexed numerically, as required
			// by sqlsrv_query(). If param() was used to create portable binds
			// then the array might be associative.
			$inputarr = array_values($inputarr);
			$rez = sqlsrv_query($this->_connectionID, $sql, $inputarr);
		} else {
			$rez = sqlsrv_query($this->_connectionID, $sql);
		}

		if ($this->debug) {
			ADOConnection::outp("<hr>running query: " . var_export($sql, true)
				. "<hr>input array: " . var_export($inputarr, true)
				. "<hr>result: " . var_export($rez, true)
			);
		}

		$this->lastInsID = false;
		if (!$rez) {
			$rez = false;
		} elseif ($retrieveLastInsertID) {
			// Get the inserted id from the last result
			// Note: loop is required as server may return more than one row,
			// e.g. if triggers are involved (see #41)
			while (sqlsrv_next_result($rez)) {
				sqlsrv_fetch($rez);
				$this->lastInsID = sqlsrv_get_field($rez, 0, SQLSRV_PHPTYPE_INT);
			}
		}
		return $rez;
	}

	// returns true or false
	function _close()
	{
		if ($this->transCnt) {
			$this->RollbackTrans();
		}
		if($this->_connectionID) {
			$rez = sqlsrv_close($this->_connectionID);
		}
		$this->_connectionID = false;
		return $rez;
	}


	function MetaIndexes($table,$primary=false, $owner = false)
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

		$constraints = $this->GetArray($sql);

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
				if (is_array($arr2[$a])) {	// a previous foreign key was define for this reference table, we merge the new one
					$arr2[$a] = array_merge($arr2[$a], $b);
				} else {
					$arr2[$a] = $b;
				}
			}
		}
		return $arr2;
	}

	//From: Fernando Moreira <FMoreira@imediata.pt>
	function MetaDatabases()
	{
		$this->SelectDB("master");
		$rs = $this->Execute($this->metaDatabasesSQL);
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
	function MetaPrimaryKeys($table, $owner=false)
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


	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(($mask));
			$this->metaTablesSQL .= " AND name like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype,$showSchema);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
	function MetaColumns($table, $upper=true, $schema=false){

		/*
		* A simple caching mechanism, to be replaced in ADOdb V6
		*/
		static $cached_columns = array();
		if ($this->cachedSchemaFlush)
			$cached_columns = array();

		if (array_key_exists($table,$cached_columns)){
			return $cached_columns[$table];
		}


		$this->_findschema($table,$schema);
		if ($schema) {
			$dbName = $this->database;
			$this->SelectDB($schema);
		}
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));

		if ($schema) {
			$this->SelectDB($dbName);
		}

		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!is_object($rs)) {
			$false = false;
			return $false;
		}

		$retarr = array();
		while (!$rs->EOF){

			$fld = new ADOFieldObject();
			if (array_key_exists(0,$rs->fields)) {
				$fld->name          = $rs->fields[0];
				$fld->type          = $rs->fields[1];
				$fld->max_length    = $rs->fields[2];
				$fld->precision     = $rs->fields[3];
				$fld->scale         = $rs->fields[4];
				$fld->not_null      =!$rs->fields[5];
				$fld->has_default   = $rs->fields[6];
				$fld->xtype         = $rs->fields[7];
				$fld->type_length   = $rs->fields[8];
				$fld->auto_increment= $rs->fields[9];
			} else {
				$fld->name          = $rs->fields['name'];
				$fld->type          = $rs->fields['type'];
				$fld->max_length    = $rs->fields['length'];
				$fld->precision     = $rs->fields['precision'];
				$fld->scale         = $rs->fields['scale'];
				$fld->not_null      =!$rs->fields['nullable'];
				$fld->has_default   = $rs->fields['default_value'];
				$fld->xtype         = $rs->fields['xtype'];
				$fld->type_length   = $rs->fields['type_length'];
				$fld->auto_increment= $rs->fields['is_identity'];
			}

			if ($save == ADODB_FETCH_NUM)
				$retarr[] = $fld;
			else
				$retarr[strtoupper($fld->name)] = $fld;

			$rs->MoveNext();

		}
		$rs->Close();
		$cached_columns[$table] = $retarr;

		return $retarr;
	}

	/**
	* Returns a substring of a varchar type field
	*
	* The SQL server version varies because the length is mandatory, so
	* we append a reasonable string length
	*
	* @param	string	$fld	The field to sub-string
	* @param	int		$start	The start point
	* @param	int		$length	An optional length
	*
	* @return	The SQL text
	*/
	function substr($fld,$start,$length=0)
	{
		if ($length == 0)
			/*
		     * The length available to varchar is 2GB, but that makes no
			 * sense in a substring, so I'm going to arbitrarily limit
			 * the length to 1K, but you could change it if you want
			 */
			$length = 1024;

		$text = "SUBSTRING($fld,$start,$length)";
		return $text;
	}

	/**
	* Returns the maximum size of a MetaType C field. Because of the
	* database design, SQL Server places no limits on the size of data inserted
	* Although the actual limit is 2^31-1 bytes.
	*
	* @return int
	*/
	function charMax()
	{
		return ADODB_STRINGMAX_NOLIMIT;
	}

	/**
	* Returns the maximum size of a MetaType X field. Because of the
	* database design, SQL Server places no limits on the size of data inserted
	* Although the actual limit is 2^31-1 bytes.
	*
	* @return int
	*/
	function textMax()
	{
		return ADODB_STRINGMAX_NOLIMIT;
	}
	/**
	 * Lists procedures, functions and methods in an array.
	 *
	 * @param	string $procedureNamePattern (optional)
	 * @param	string $catalog				 (optional)
	 * @param	string $schemaPattern		 (optional)

	 * @return array of stored objects in current database.
	 *
	 */
	public function metaProcedures($procedureNamePattern = null, $catalog  = null, $schemaPattern  = null)
	{
		$metaProcedures = array();
		$procedureSQL   = '';
		$catalogSQL     = '';
		$schemaSQL      = '';

		if ($procedureNamePattern)
			$procedureSQL = "AND ROUTINE_NAME LIKE " . strtoupper($this->qstr($procedureNamePattern));

		if ($catalog)
			$catalogSQL = "AND SPECIFIC_SCHEMA=" . strtoupper($this->qstr($catalog));

		if ($schemaPattern)
			$schemaSQL = "AND ROUTINE_SCHEMA LIKE {$this->qstr($schemaPattern)}";

		$fields = "	ROUTINE_NAME,ROUTINE_TYPE,ROUTINE_SCHEMA,ROUTINE_CATALOG";

		$SQL = "SELECT $fields
			FROM {$this->database}.information_schema.routines
			WHERE 1=1
				$procedureSQL
				$catalogSQL
				$schemaSQL
			ORDER BY ROUTINE_NAME
			";

		$result = $this->execute($SQL);

		if (!$result)
			return false;
		while ($r = $result->fetchRow()){
			if (!isset($r[0]))
				/*
				* Convert to numeric
				*/
				$r = array_values($r);

			$procedureName = $r[0];
			$schemaName    = $r[2];
			$routineCatalog= $r[3];
			$metaProcedures[$procedureName] = array('type'=> $r[1],
												   'catalog' => $routineCatalog,
												   'schema'  => $schemaName,
												   'remarks' => '',
												    );
		}

		return $metaProcedures;
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

	/**
	 * @var bool True if we have retrieved the fields metadata
	 */
	private $fieldObjectsRetrieved = false;

	/*
	* Cross-reference the objects by name for easy access
	*/
	private $fieldObjectsIndex = array();

	/*
	 * Cross references the dateTime objects for faster decoding
	 */
	private $dateTimeObjects = array();

	/*
	 * flags that we have dateTimeObjects to handle
	 */
	private $hasDateTimeObjects = false;

	/*
	 * This is cross reference between how the types are stored
	 * in SQL Server and their english-language description
	 * -154 is a time field, see #432
	 */
	private $_typeConversion = array(
			-155 => 'datetimeoffset',
			-154 => 'char',
			-152 => 'xml',
			-151 => 'udt',
			-11  => 'uniqueidentifier',
			-10  => 'ntext',
			-9   => 'nvarchar',
			-8   => 'nchar',
			-7   => 'bit',
			-6   => 'tinyint',
			-5   => 'bigint',
			-4   => 'image',
			-3   => 'varbinary',
			-2   => 'timestamp',
			-1   => 'text',
			 1   => 'char',
			 2   => 'numeric',
			 3   => 'decimal',
			 4   => 'int',
			 5   => 'smallint',
			 6   => 'float',
			 7   => 'real',
			 12  => 'varchar',
			 91  => 'date',
			 93  => 'datetime'
			);




	function __construct($id,$mode=false)
	{
		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;

		}
		$this->fetchMode = $mode;
		parent::__construct($id);
	}


	function _initrs()
	{
		$this->_numOfRows = -1;//not supported
		// Cache the metadata right now
		$this->_fetchField();

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
		if (!is_array($this->fields))
			/*
			* Too early
			*/
			return;
		if ($this->fetchMode != ADODB_FETCH_NUM)
			return $this->fields[$colname];

		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}

		return $this->fields[$this->bind[strtoupper($colname)]];
	}

	/**
	* Returns: an object containing field information.
	*
	* Get column information in the Recordset object. fetchField()
	* can be used in order to obtain information about fields in a
	* certain query result. If the field offset isn't specified,
	* the next field that wasn't yet retrieved by fetchField()
	* is retrieved.
	*
	* @param int $fieldOffset (optional default=-1 for all
	* @return mixed an ADOFieldObject, or array of objects
	*/
	private function _fetchField($fieldOffset = -1)
	{
		if ($this->fieldObjectsRetrieved) {
			if ($this->fieldObjectsCache) {
				// Already got the information
				if ($fieldOffset == -1) {
					return $this->fieldObjectsCache;
				} else {
					return $this->fieldObjectsCache[$fieldOffset];
				}
			} else {
				// No metadata available
				return false;
			}
		}

		$this->fieldObjectsRetrieved = true;
		/*
		 * Retrieve all metadata in one go. This is always returned as a
		 * numeric array.
		 */
		$fieldMetaData = sqlsrv_field_metadata($this->_queryID);

		if (!$fieldMetaData) {
			// Not a statement that gives us metaData
			return false;
		}

		$this->_numOfFields = count($fieldMetaData);
		foreach ($fieldMetaData as $key=>$value) {
			$fld = new ADOFieldObject;
			// Caution - keys are case-sensitive, must respect casing of values
			$fld->name          = $value['Name'];
			$fld->max_length    = $value['Size'];
			$fld->column_source = $value['Name'];
			$fld->type          = $this->_typeConversion[$value['Type']];

			$this->fieldObjectsCache[$key] = $fld;
			$this->fieldObjectsIndex[$fld->name] = $key;
		}
		if ($fieldOffset == -1) {
			return $this->fieldObjectsCache;
		}

		return $this->fieldObjectsCache[$fieldOffset];
	}

	/*
	 * Fetchfield copies the oracle method, it loads the field information
	 * into the _fieldobjs array once, to save multiple calls to the
	 * sqlsrv_field_metadata function
	 *
	 * @param int $fieldOffset	(optional)
	 *
	 * @return adoFieldObject
	 *
	 * @author 	KM Newnham
	 * @date 	02/20/2013
	 */
	function fetchField($fieldOffset = -1)
	{
		return $this->fieldObjectsCache[$fieldOffset];
	}

	function _seek($row)
	{
		return false;//There is no support for cursors in the driver at this time.  All data is returned via forward-only streams.
	}

	// speedup
	function MoveNext()
	{
		if ($this->EOF)
			return false;

		$this->_currentRow++;

		if ($this->_fetch())
			return true;
		$this->EOF = true;

		return false;
	}

	function _fetch($ignore_fields=false)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM)
				$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_BOTH);
			else
				$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_ASSOC);

			if (is_array($this->fields))
			{

				if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_LOWER)
					$this->fields = array_change_key_case($this->fields,CASE_LOWER);
				else if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER)
					$this->fields = array_change_key_case($this->fields,CASE_UPPER);

			}
		}
		else
			$this->fields = @sqlsrv_fetch_array($this->_queryID,SQLSRV_FETCH_NUMERIC);

		if (!$this->fields)
			return false;

		return $this->fields;
	}

	/**
	 * close() only needs to be called if you are worried about using too much
	 * memory while your script is running. All associated result memory for
	 * the specified result identifier will automatically be freed.
	 *
	 * @return bool tru if we succeeded in closing down
	 */
	function _close()
	{
		/*
		* If we are closing down a failed query, collect any
		* error messages. This is a hack fix to the "close too early"
		* problem so this might go away later
		*/
		$this->connection->errorMsg();
		if(is_resource($this->_queryID)) {
			$rez = sqlsrv_free_stmt($this->_queryID);
			$this->_queryID = false;
			return $rez;
		}

		return true;
	}

}


class ADORecordSet_array_mssqlnative extends ADORecordSet_array {}

/*
Code Example 1:

select	object_name(constid) as constraint_name,
		object_name(fkeyid) as table_name,
		col_name(fkeyid, fkey) as column_name,
	object_name(rkeyid) as referenced_table_name,
	col_name(rkeyid, rkey) as referenced_column_name
from sysforeignkeys
where object_name(fkeyid) = x
order by constraint_name, table_name, referenced_table_name,  keyno

Code Example 2:
select	constraint_name,
	column_name,
	ordinal_position
from information_schema.key_column_usage
where constraint_catalog = db_name()
and table_name = x
order by constraint_name, ordinal_position

http://www.databasejournal.com/scripts/article.php/1440551
*/
