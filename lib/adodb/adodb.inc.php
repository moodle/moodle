<?php 

/** 
 * @version V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
 * Whenever there is any discrepancy between the two licenses, 
 * the BSD license will take precedence. 
 *
 * Set tabs to 4 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 * 
 * This is the main include file for ADODB.
 * It has all the generic functionality of ADODB. 
 * Database specific drivers are stored in the adodb-*.inc.php files.
 *
 * Requires PHP4.01pl2 or later because it uses include_once
*/

 if (!defined('_ADODB_LAYER')) {
 	define('_ADODB_LAYER',1);
	
	//==============================================================================================	
	// CONSTANT DEFINITIONS
	//==============================================================================================	

	define('ADODB_BAD_RS','<p>Bad $rs in %s. Connection or SQL invalid. Try using $connection->debug=true;</p>');
	
	define('ADODB_FETCH_DEFAULT',0);
	define('ADODB_FETCH_NUM',1);
	define('ADODB_FETCH_ASSOC',2);
	define('ADODB_FETCH_BOTH',3);
	
	// allow [ ] @ and . in table names
	define('ADODB_TABLE_REGEX','([]0-9a-z_\.\@\[-]*)');
	if (!defined('MAX_BLOB_SIZE')) define('MAX_BLOB_SIZE',999999); // 900K
	
	if (!defined('ADODB_PREFETCH_ROWS')) define('ADODB_PREFETCH_ROWS',10);

	/** 
	 * Set ADODB_DIR to the directory where this file resides...
	 * This constant was formerly called $ADODB_RootPath
	 */
	if (!defined('ADODB_DIR')) define('ADODB_DIR',dirname(__FILE__));
	
	if (strpos(strtoupper(PHP_OS),'WIN') !== false) {
	// windows, negative timestamps are illegal as of php 4.2.0
		define('TIMESTAMP_FIRST_YEAR',1970);
	} else
		define('TIMESTAMP_FIRST_YEAR',1904);
	
	//==============================================================================================	
	// GLOBAL VARIABLES
	//==============================================================================================	

	GLOBAL 
		$ADODB_vers, 		// database version
		$ADODB_Database, 	// last database driver used
		$ADODB_COUNTRECS,	// count number of records returned - slows down query
		$ADODB_CACHE_DIR,	// directory to cache recordsets
	 	$ADODB_FETCH_MODE;	// DEFAULT, NUM, ASSOC or BOTH. Default follows native driver default...
	
	//==============================================================================================	
	// GLOBAL SETUP
	//==============================================================================================	
	
	$ADODB_FETCH_MODE = ADODB_FETCH_DEFAULT;
	
	if (!isset($ADODB_CACHE_DIR)) {
		$ADODB_CACHE_DIR = '/tmp';
	} else {
		// do not accept url based paths, eg. http:/ or ftp:/
		if (strpos($ADODB_CACHE_DIR,'://') !== false) 
			die("Illegal path http:// or ftp://");
	}
	
	//==============================================================================================	
	// CHANGE NOTHING BELOW UNLESS YOU ARE CODING
	//==============================================================================================	

	
	// Initialize random number generator for randomizing cache flushes
	srand(((double)microtime())*1000000);
	
	/**
	 * Name of last database driver loaded into memory. Set by ADOLoadCode().
	 */
	$ADODB_Database = '';
	
	/**
	 * ADODB version as a string.
	 */
	$ADODB_vers = 'V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved. Released BSD & LGPL.';

	/**
	 * Determines whether recordset->RecordCount() is used. 
	 * Set to false for highest performance -- RecordCount() will always return -1 then
	 * for databases that provide "virtual" recordcounts...
	 */
	$ADODB_COUNTRECS = true; 

	//==============================================================================================	
	// CLASS ADOFieldObject
	//==============================================================================================	

	/**
	 * Helper class for FetchFields -- holds info on a column
	 */
	class ADOFieldObject { 
		var $name = '';
		var $max_length=0;
		var $type="";

		// additional fields by dannym... (danny_milo@yahoo.com)
		var $not_null = false; 
		// actually, this has already been built-in in the postgres, fbsql AND mysql module? ^-^
		// so we can as well make not_null standard (leaving it at "false" does not harm anyways)

		var $has_default = false; // this one I have done only in mysql and postgres for now ... 
			// others to come (dannym)
		var $default_value; // default, if any, and supported. Check has_default first.
	}
	
	
	//==============================================================================================	
	// CLASS ADOConnection
	//==============================================================================================	
	
	/**
	 * Connection object. For connecting to databases, and executing queries.
	 */ 
	class ADOConnection {
	/*
	 * PUBLIC VARS 
	 */
	var $dataProvider = 'native';
	var $databaseType = '';		// RDBMS currently in use, eg. odbc, mysql, mssql					
	var $database = '';			// Name of database to be used.	
	var $host = ''; 			// The hostname of the database server	
	var $user = ''; 			// The username which is used to connect to the database server. 
	var $password = ''; 		// Password for the username
	var $debug = false; 		// if set to true will output sql statements
	var $maxblobsize = 64000; 	// maximum size of blobs or large text fields -- some databases die otherwise like foxpro
	var $concat_operator = '+'; // default concat operator -- change to || for Oracle/Interbase	
	var $fmtDate = "'Y-m-d'";	// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "'Y-m-d, h:i:s A'"; // used by DBTimeStamp as the default timestamp fmt.
	var $true = '1'; 			// string that represents TRUE for a database
	var $false = '0'; 			// string that represents FALSE for a database
	var $replaceQuote = "\\'"; 	// string to use to replace quotes
    var $hasInsertID = false; 	// supports autoincrement ID?
    var $hasAffectedRows = false; 	// supports affected rows for update/delete?
    var $charSet=false; 		// character set to use - only for interbase
	var $metaTablesSQL = '';
	var $hasTop = false;		// support mssql/access SELECT TOP 10 * FROM TABLE
	var $hasLimit = false;		// support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	var $readOnly = false; 		// this is a readonly database - used by phpLens
	var $hasMoveFirst = false;  // has ability to run MoveFirst(), scrolling backwards
	var $hasGenID = false; 		// can generate sequences using GenID();
	var $genID = 0; 			// sequence id used by GenID();
	var $raiseErrorFn = false; 	// error function to call
	var $upperCase = false; 	// uppercase function to call for searching/where
	var $isoDates = false; // accepts dates in ISO format
	var $cacheSecs = 3600; // cache for 1 hour
	var $sysDate = false; // name of function that returns the current date
	var $sysTimeStamp = false; // name of function that returns the current timestamp
	var $arrayClass = 'ADORecordSet_array';
	// oracle specific stuff
	var $noNullStrings = false;
	var $numCacheHits = 0;
	var $numCacheMisses = 0;
	var $pageExecuteCountRows = true;
	var $uniqueSort = false; // indicates that all fields in order by must be unique
	
	/*
	 * PRIVATE VARS
	 */
	var $_connectionID	= false;	// The returned link identifier whenever a successful database connection is made.	*/
		
	var $_errorMsg = '';		// A variable which was used to keep the returned last error message.  The value will
					//then returned by the errorMsg() function	
						
	var $_queryID = false;		// This variable keeps the last created result link identifier.		*/
	
	var $_isPersistentConnection = false;	// A boolean variable to state whether its a persistent connection or normal connection.	*/
	
	var $_bindInputArray = false; // set to true if ADOConnection.Execute() permits binding of array parameters.
	
	 var $autoCommit = true; 	// do not modify this yourself - actually private
	
	/**
	 * Constructor
	 */
	function ADOConnection()			
	{
		die('Virtual Class -- cannot instantiate');
	}
	

	/**
	 * Connect to database
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 *
	 * @return true or false
	 */	  
	function Connect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "") 
	{
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword; // not stored for security reasons
		if ($argDatabaseName != "") $this->database = $argDatabaseName;		
		
		$this->_isPersistentConnection = false;	
		if ($fn = $this->raiseErrorFn) {
			if ($this->_connect($this->host, $this->user, $this->password, $this->database)) return true;
			$err = $this->ErrorMsg();
			if (empty($err)) $err = "Connection error to server '$argHostname' with user '$argUsername'";
			$fn($this->databaseType,'CONNECT',$this->ErrorNo(),$err,$this->host,$this->database);
		} else 
			if ($this->_connect($this->host, $this->user, $this->password, $this->database)) return true;

		if ($this->debug) print $this->host.': '.$this->ErrorMsg()."<br />\n";
		
		return false;
	}	
	

	/**
	 * Establish persistent connect to database
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 *
	 * @return return true or false
	 */	
	function PConnect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "")
	{
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword;
		if ($argDatabaseName != "") $this->database = $argDatabaseName;		
			
		$this->_isPersistentConnection = true;	
		
		if ($fn = $this->raiseErrorFn) {
			if ($this->_pconnect($this->host, $this->user, $this->password, $this->database)) return true;
			$err = $this->ErrorMsg();
			if (empty($err)) $err = "Connection error to server '$argHostname' with user '$argUsername'";
			$fn($this->databaseType,'PCONNECT',$this->ErrorNo(),$err,$this->host,$this->database);
		} else 
			if ($this->_pconnect($this->host, $this->user, $this->password, $this->database)) return true;

		if ($this->debug) print $this->host.': '.$this->ErrorMsg()."<br />\n";
		
		return false;
	}

	function UnixDate($d)
	{
		return ADORecordSet::UnixDate($d);
	}
	
	/**
	 * Should prepare the sql statement and return the stmt resource.
	 * For databases that do not support this, we return the $sql. To ensure
	 * compatibility with databases that do not support prepare:
	 *
	 *   $stmt = $db->Prepare("insert into table (id, name) values (?,?)");
	 *   $db->Execute($stmt,array(1,'Jill')) or die('insert failed');
	 *   $db->Execute($stmt,array(2,'Joe')) or die('insert failed');
	 *
	 * @param sql	SQL to send to database
	 *
	 * @return return FALSE, or the prepared statement, or the original sql if
	 * 			if the database does not support prepare.
	 *
	 */	
	function Prepare($sql)
	{
		return $sql;
	}

	/**
	 * Some databases, eg. mssql require a different function for preparing
	 * stored procedures. So we cannot use Prepare().
	 *
	 * Should prepare the stored procedure  and return the stmt resource.
	 * For databases that do not support this, we return the $sql. To ensure
	 * compatibility with databases that do not support prepare:
	 *
	 * @param sql	SQL to send to database
	 *
	 * @return return FALSE, or the prepared statement, or the original sql if
	 * 			if the database does not support prepare.
	 *
	 */	
	function PrepareSP($sql)
	{
		return $this->Prepare($sql);
	}
	
	/**
	* PEAR DB Compat - Quote with auto-checking of magic-quotes-gpc.
	*/
	function Quote($s)
	{
		return $this->qstr($s,get_magic_quotes_gpc());
	}

	
	/**
	* PEAR DB Compat - do not use internally. 
	*/
	function ErrorNative()
    {
        return $this->ErrorNo();
    }

	
   /**
	* PEAR DB Compat - do not use internally. 
	*/
    function nextId($seq_name)
	{
		return $this->GenID($seq_name);
	}

	/**
	*	 Lock a row, will escalate and lock the table if row locking not supported
	*	will normally free the lock at the end of the transaction
	*
	*  @param $table	name of table to lock
	*  @param $where	where clause to use, eg: "WHERE row=12". If left empty, will escalate to table lock
	*/
	function RowLock($table,$where)
	{
		return false;
	}
	
	function CommitLock($table)
	{
		return $this->CommitTrans();
	}
	
	function RollbackLock($table)
	{
		return $this->RollbackTrans();
	}
	
	/**
	* PEAR DB Compat - do not use internally. 
	*
	* The fetch modes for NUMERIC and ASSOC for PEAR DB and ADODB are identical
	* 	for easy porting :-)
	*/
	function SetFetchMode($mode)
	{
	global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = $mode;
	}
	

	/**
	* PEAR DB Compat - do not use internally. 
	*/
	function &Query($sql, $inputarr=false)
	{
		$rs = &$this->Execute($sql, $inputarr);
		if (!$rs && defined('ADODB_PEAR')) return ADODB_PEAR_Error();
		return $rs;
	}

	
	/**
	* PEAR DB Compat - do not use internally
	*/
	function &LimitQuery($sql, $offset, $count)
	{
		$rs = &$this->SelectLimit($sql, $count, $offset); // swap 
		if (!$rs && defined('ADODB_PEAR')) return ADODB_PEAR_Error();
		return $rs;
	}

	
	/**
	* PEAR DB Compat - do not use internally
	*/
	function Disconnect()
	{
		return $this->Close();
	}

	/* 
	Usage in oracle
		$stmt = $db->Prepare('select * from table where id =:myid and group=:group');
		$db->Parameter($stmt,$id,'myid');
		$db->Parameter($stmt,$group,'group',64);
		$db->Execute();
		
		@param $stmt Statement returned by Prepare() or PrepareSP().
		@param $var PHP variable to bind to
		@param $name Name of stored procedure variable name to bind to.
		@param [$isOutput] Indicates direction of parameter 0/false=IN  1=OUT  2= IN/OUT. This is ignored in oci8.
		@param [$maxLen] Holds an maximum length of the variable.
		@param [$type] The data type of $var. Legal values depend on driver.

	*/
	function Parameter(&$stmt,&$var,$name,$isOutput=false,$maxLen=4000,$type=false)
	{
		return false;
	}
	
	/**
	 * Execute SQL 
	 *
	 * @param sql		SQL statement to execute, or possibly an array holding prepared statement ($sql[0] will hold sql text)
	 * @param [inputarr]	holds the input data to bind to. Null elements will be set to null.
	 * @param [arg3]	reserved for john lim for future use
	 * @return 		RecordSet or false
	 */
	function &Execute($sql,$inputarr=false,$arg3=false) 
	{
		if (!$this->_bindInputArray && $inputarr) {
			$sqlarr = explode('?',$sql);
			$sql = '';
			$i = 0;
			foreach($inputarr as $v) {

				$sql .= $sqlarr[$i];
				// from Ron Baldwin <ron.baldwin@sourceprose.com>
				// Only quote string types	
				if (gettype($v) == 'string')
					$sql .= $this->qstr($v);
				else if ($v === null)
					$sql .= 'NULL';
				else
					$sql .= $v;
				$i += 1;
	
			}
			$sql .= $sqlarr[$i];
			if ($i+1 != sizeof($sqlarr))	
				print "Input Array does not match ?: ".htmlspecialchars($sql);
			$inputarr = false;
		}
		
		// debug version of query
		if ($this->debug) {
		global $HTTP_SERVER_VARS;
		
			$ss = '';
			if ($inputarr) {
				foreach ($inputarr as $kk => $vv)  {
					if (is_string($vv) && strlen($vv)>64) $vv = substr($vv,0,64).'...';
					$ss .= "($kk=>'$vv') ";
				}
				$ss = "[ $ss ]";
			}
			if (is_array($sql)) $sqlTxt = $sql[0];
			else $sqlTxt = $sql;
			
			// check if running from browser or command-line
			$inBrowser = isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']);
			
			if ($inBrowser)
				print "<hr />\n($this->databaseType): ".htmlspecialchars($sqlTxt)." &nbsp; <code>$ss</code>\n<hr />\n";
			else
				print "=----\n($this->databaseType): ".($sqlTxt)." \n-----\n";
			flush();
			
			$this->_queryID = $this->_query($sql,$inputarr,$arg3);

			/* 
				Alexios Fakios notes that ErrorMsg() must be called before ErrorNo() for mssql
				because ErrorNo() calls Execute('SELECT @ERROR'), causing recure
			*/
			if ($this->databaseType == 'mssql') { 
			// ErrorNo is a slow function call in mssql, and not reliable
			// in PHP 4.0.6
				if($emsg = $this->ErrorMsg()) {
					$err = $this->ErrorNo();
					if ($err) {
						print $err.': '.$emsg.(($inBrowser) ? "<br />\n" : "\n");
						flush();
					}
				}
			} else 
				if (!$this->_queryID) {
					print $this->ErrorNo().': '.$this->ErrorMsg() .(($inBrowser) ? "<br />\n" : "\n");
					flush();
				}
		} else 
			// non-debug version of query
			$this->_queryID =@$this->_query($sql,$inputarr,$arg3);
		
		// error handling if query fails
		if ($this->_queryID === false) {
			if ($fn = $this->raiseErrorFn) {
				$fn($this->databaseType,'EXECUTE',$this->ErrorNo(),$this->ErrorMsg(),$sql,$inputarr);
			}
			return false;
		} else if ($this->_queryID === true){
		// return simplified empty recordset for inserts/updates/deletes with lower overhead
			$rs = new ADORecordSet_empty();
			return $rs;
		}
		
		// return real recordset from select statement
		$rsclass = "ADORecordSet_".$this->databaseType;
		$rs = new $rsclass($this->_queryID); // &new not supported by older PHP versions
		$rs->connection = &$this; // Pablo suggestion
		$rs->Init();

		if (is_array($sql)) $rs->sql = $sql[0];
		else $rs->sql = $sql;
		
		global $ADODB_COUNTRECS;
		if ($rs->_numOfRows <= 0 && !$rs->EOF && $ADODB_COUNTRECS) { 
			$rs = &$this->_rs2rs($rs);
			$rs->_queryID = $this->_queryID;
		}
		return $rs;
	}


	/**
	 * Generates a sequence id and stores it in $this->genID;
	 * GenID is only available if $this->hasGenID = true;
	 *
	 * @seqname		name of sequence to use
	 * @startID		if sequence does not exist, start at this ID
	 * @return		0 if not supported, otherwise a sequence id
	 */

	function GenID($seqname='adodbseq',$startID=1)
	{
		if (!$this->hasGenID) {
			return 0; // formerly returns false pre 1.60
		}
		
		$getnext = sprintf($this->_genIDSQL,$seqname);
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			$u = strtoupper($seqname);
			$createseq = $this->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
			$rs = $this->Execute($getnext);
		}
		if ($rs && !$rs->EOF) $this->genID = (integer) reset($rs->fields);
		else $this->genID = 0; // false
	
		if ($rs) $rs->Close();

		return $this->genID;
	}	

	/**
	 * @return  the last inserted ID. Not all databases support this.
	 */ 
        function Insert_ID()
        {
                if ($this->hasInsertID) return $this->_insertid();
                if ($this->debug) print '<p>Insert_ID error</p>';
                return false;
        }
    
	
    /**
	 * Portable Insert ID. Pablo Roca <pabloroca@mvps.org>
	 *
	 * @return  the last inserted ID. All databases support this. But aware possible
	 * problems in multiuser environments. Heavy test this before deploying.
	 */ 
        function PO_Insert_ID($table="", $id="") 
		{
           if ($this->hasInsertID){
               return $this->Insert_ID();
           } else {
               return $this->GetOne("SELECT MAX($id) FROM $table");
           }
        }	
	
		
     /**
	 * @return  # rows affected by UPDATE/DELETE
	 */ 
     function Affected_Rows()
     {
          if ($this->hasAffectedRows) {
                 $val = $this->_affectedrows();
                 return ($val < 0) ? false : $val;
          }
                  
          if ($this->debug) print '<p>Affected_Rows error</p>';
          return false;
     }
	
	
    /**
	 * @return  the last error message
	 */
	function ErrorMsg()
	{
		return '!! '.strtoupper($this->dataProvider.' '.$this->databaseType).': '.$this->_errorMsg;
	}
	
	
	/**
	 * @return the last error number. Normally 0 means no error.
	 */
	function ErrorNo() 
	{
		return ($this->_errorMsg) ? -1 : 0;
	}
	
	
	/**
	 * @returns an array with the primary key columns in it.
	 */
	function MetaPrimaryKeys($table)
	{
		return false;
	}
	
	
	/**
	 * Choose a database to connect to. Many databases do not support this.
	 *
	 * @param dbName 	is the name of the database to select
	 * @return 		true or false
	 */
	function SelectDB($dbName) 
	{return false;}
	
	
	/**
	* Will select, getting rows from $offset (1-based), for $nrows. 
	* This simulates the MySQL "select * from table limit $offset,$nrows" , and
	* the PostgreSQL "select * from table limit $nrows offset $offset". Note that
	* MySQL and PostgreSQL parameter ordering is the opposite of the other.
	* eg. 
	*  SelectLimit('select * from table',3); will return rows 1 to 3 (1-based)
	*  SelectLimit('select * from table',3,2); will return rows 3 to 5 (1-based)
	*
	* Uses SELECT TOP for Microsoft databases (when $this->hasTop is set)
	* BUG: Currently SelectLimit fails with $sql with LIMIT or TOP clause already set
	*
	* @param sql
	* @param [offset]	is the row to start calculations from (1-based)
	* @param [rows]		is the number of rows to get
	* @param [inputarr]	array of bind variables
	* @param [arg3]		is a private parameter only used by jlim
	* @param [secs2cache]		is a private parameter only used by jlim
	* @return		the recordset ($rs->databaseType == 'array')
 	*/
	function &SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$arg3=false,$secs2cache=0)
	{
		if ($this->hasTop && $nrows > 0) {
		// suggested by Reinhard Balling. Access requires top after distinct 
		
			if ($offset <= 0) {
				$sql = preg_replace(
				'/(^[\\t\\n ]*select[\\t\\n ]*(distinctrow|distinct)?)/i','\\1 '.$this->hasTop.' '.$nrows.' ',$sql);
				
					if ($secs2cache>0) return $this->CacheExecute($secs2cache, $sql,$inputarr,$arg3);
					else return $this->Execute($sql,$inputarr,$arg3);
			} else {
				$nrows += $offset;
				$sql = preg_replace(
				'/(^[\\t\\n ]*select[\\t\\n ]*(distinctrow|distinct)?)/i','\\1 '.$this->hasTop.' '.$nrows.' ',$sql);
				$nrows = -1;
			}
	 
		}
		
		// if $offset>0, we want to skip rows, and $ADODB_COUNTRECS is set, we buffer  rows
		// 0 to offset-1 which will be discarded anyway. So we disable $ADODB_COUNTRECS.
		global $ADODB_COUNTRECS;
		
		$savec = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = false;
			
		if ($offset>0){
			if ($secs2cache>0) $rs = &$this->CacheExecute($secs2cache,$sql,$inputarr,$arg3);
			else $rs = &$this->Execute($sql,$inputarr,$arg3);
		} else {
			if ($secs2cache>0) $rs = &$this->CacheExecute($secs2cache,$sql,$inputarr,$arg3);
			else $rs = &$this->Execute($sql,$inputarr,$arg3);
		}
		$ADODB_COUNTRECS = $savec;
		
		if ($rs && !$rs->EOF) {
			return $this->_rs2rs($rs,$nrows,$offset);
		}
		//print_r($rs);
		return $rs;
	}
	
	
	/**
	* Convert recordset to an array recordset
	* input recordset's cursor should be at beginning, and
	* old $rs will be closed.
	*
	* @param rs			the recordset to copy
	* @param [nrows]  	number of rows to retrieve (optional)
	* @param [offset] 	offset by number of rows (optional)
	* @return 			the new recordset
	*/
	function &_rs2rs(&$rs,$nrows=-1,$offset=-1)
	{
		if (! $rs) return false;
		$arr = &$rs->GetArrayLimit($nrows,$offset);
		$flds = array();
		for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
			$flds[] = &$rs->FetchField($i);
		$rs->Close();
		
		$arrayClass = $this->arrayClass;
		
		$rs2 = new $arrayClass();
		$rs2->connection = &$this;
		$rs2->sql = $rs->sql;
		$rs2->InitArrayFields($arr,$flds);
		return $rs2;
	}
	
	
	/**
	* Return first element of first row of sql statement. Recordset is disposed
	* for you.
	*
	* @param sql			SQL statement
	* @param [inputarr]		input bind array
	*/
	function GetOne($sql,$inputarr=false)
	{
		$ret = false;
		$rs = &$this->Execute($sql,$inputarr);
		if ($rs) {		
			if (!$rs->EOF) $ret = reset($rs->fields);
			$rs->Close();
		} 
		
		return $ret;
	}
	
	
	/**
	* Return all rows. Compat with PEAR DB
	*
	* @param sql			SQL statement
	* @param [inputarr]		input bind array
	*/
	function &GetAll($sql,$inputarr=false)
	{
		$rs = $this->Execute($sql,$inputarr);
		if (!$rs) 
			if (defined('ADODB_PEAR')) return ADODB_PEAR_Error();
			else return false;
		return $rs->GetArray();
	}
	
	
	/**
	* Return one row of sql statement. Recordset is disposed for you.
	*
	* @param sql			SQL statement
	* @param [inputarr]		input bind array
	*/
	function GetRow($sql,$inputarr=false)
	{
		$rs = $this->Execute($sql,$inputarr);
		if ($rs) {
			$arr = false;
			if (!$rs->EOF) $arr = $rs->fields;
			$rs->Close();
			return $arr;
		}
		return false;
	}
	/**
	* Insert or replace a single record
	*
	* $this->Replace('products', array('prodname' =>"'Nails'","price" => 3.99), 'prodname');
	*
	* $table		table name
	* $fieldArray	associative array of data (you must quote strings yourself).
	* $keyCol		the primary key field name or if compound key, array of field names
	* autoQuote		set to true to use a hueristic to quote strings. Works with nulls and numbers
	*					but does not work with dates nor SQL functions.
	*
	* Currently blob replace not supported
	*
	* returns 0 = fail, 1 = update, 2 = insert 
	*/
	
	function Replace($table, $fieldArray, $keyCol,$autoQuote=false)
	{
		if (count($fieldArray) == 0) return 0;
		$first = true;
		$uSet = '';
		
		if (!is_array($keyCol)) {
			$keyCol = array($keyCol);
		}
		foreach($fieldArray as $k => $v) {
			if ($autoQuote && !is_numeric($v) and $v[0] != "'" and strcasecmp($v,'null')!=0) {
				$v = $this->qstr($v);
				$fieldArray[$k] = $v;
			}
			if (in_array($k,$keyCol)) continue; // skip UPDATE if is key
			
			if ($first) {
				$first = false;			
				$uSet = "$k=$v";
			} else
				$uSet .= ",$k=$v";
		}
		 
		$first = true;
		foreach ($keyCol as $v) {
			if ($first) {
				$first = false;
				$where = "$v=$fieldArray[$v]";
			} else {
				$where .= " and $v=$fieldArray[$v]";
			}
		}
		
		if ($uSet) {
			$update = "UPDATE $table SET $uSet WHERE $where";
		
			$rs = $this->Execute($update);
			if ($rs and $this->Affected_Rows()>0) return 1;
		}
		$first = true;
		foreach($fieldArray as $k => $v) {
			if ($first) {
				$first = false;			
				$iCols = "$k";
				$iVals = "$v";
			} else {
				$iCols .= ",$k";
				$iVals .= ",$v";
			}				
		}
		$insert = "INSERT INTO $table ($iCols) VALUES ($iVals)"; 
		$rs = $this->Execute($insert);
		return ($rs) ? 2 : 0;
	}
	
	
	/**
	* Will select, getting rows from $offset (1-based), for $nrows. 
	* This simulates the MySQL "select * from table limit $offset,$nrows" , and
	* the PostgreSQL "select * from table limit $nrows offset $offset". Note that
	* MySQL and PostgreSQL parameter ordering is the opposite of the other.
	* eg. 
	*  CacheSelectLimit(15,'select * from table',3); will return rows 1 to 3 (1-based)
	*  CacheSelectLimit(15,'select * from table',3,2); will return rows 3 to 5 (1-based)
	*
	* BUG: Currently CacheSelectLimit fails with $sql with LIMIT or TOP clause already set
	*
	* @param [secs2cache]	seconds to cache data, set to 0 to force query. This is optional
	* @param sql
	* @param [offset]	is the row to start calculations from (1-based)
	* @param [nrows]	is the number of rows to get
	* @param [inputarr]	array of bind variables
	* @param [arg3]		is a private parameter only used by jlim
	* @return		the recordset ($rs->databaseType == 'array')
 	*/
	function &CacheSelectLimit($secs2cache,$sql,$nrows=-1,$offset=-1,$inputarr=false, $arg3=false)
    {	
		if (!is_numeric($secs2cache)) {
			if ($sql === false) $sql = -1;
			if ($offset == -1) $offset = false;
			                          // sql,    nrows, offset,inputarr,arg3
			return $this->SelectLimit($secs2cache,$sql,$nrows,$offset,$inputarr,$this->cacheSecs);
		}
		if ($sql === false) echo "Warning: \$sql missing from CacheSelectLimit()<br />\n";
		return $this->SelectLimit($sql,$nrows,$offset,$inputarr,$arg3,$secs2cache);
	}
	
	
	function CacheFlush($sql)
	{
		$f = $this->_gencachename($sql,false);
		adodb_write_file($f,''); // is adodb_write_file needed?
		@unlink($f);
	}
	
	
	function _gencachename($sql,$createdir)
	{
	global $ADODB_CACHE_DIR;
		
		$m = md5($sql.$this->databaseType.$this->database.$this->user);
		$dir = $ADODB_CACHE_DIR.'/'.substr($m,0,2);
		if ($createdir)
			if(!file_exists($dir) && !mkdir($dir,0771)) 
				if ($this->debug) print "Unable to mkdir $dir for $sql<br>\n";
		return $dir.'/adodb_'.$m.'.cache';
	}
	
	
	/**
	 * Execute SQL, caching recordsets.
	 *
	 * @param [secs2cache]	seconds to cache data, set to 0 to force query. 
	 *                      This is an optional parameter.
	 * @param sql		SQL statement to execute
	 * @param [inputarr]	holds the input data  to bind to
	 * @param [arg3]	reserved for john lim for future use
	 * @return 		RecordSet or false
	 */
	function &CacheExecute($secs2cache,$sql=false,$inputarr=false,$arg3=false)
	{
		if (!is_numeric($secs2cache)) {
			$arg3 = $inputarr;
			$inputarr = $sql;
			$sql = $secs2cache;
			$secs2cache = $this->cacheSecs;
		}
		include_once(ADODB_DIR.'/adodb-csvlib.inc.php');
		// cannot cache if $inputarr set
		if ($inputarr) return $this->Execute($sql, $inputarr, $arg3); 
		
		$md5file = $this->_gencachename($sql,true);
		$err = '';
		
		if ($secs2cache > 0){
			$rs = &csv2rs($md5file,$err,$secs2cache);
			$this->numCacheHits += 1;
		} else {
			$err='Timeout 1';
			$rs = false;
			$this->numCacheMisses += 1;
		}
		
		if (!$rs) {
		// no cached rs found
			if ($this->debug) print " $md5file cache failure: $err<br>\n";
			$rs = &$this->Execute($sql,$inputarr,$arg3);
			if ($rs) {
				$eof = $rs->EOF;
				$rs = &$this->_rs2rs($rs); // read entire recordset into memory immediately
				$txt = _rs2serialize($rs,false,$sql); // serialize
		
				if (!adodb_write_file($md5file,$txt,$this->debug)) {
					if ($fn = $this->raiseErrorFn) {
						$fn($this->databaseType,'CacheExecute',-32000,"Cache write error",$md5file,$sql);
					}
					if ($this->debug) print " Cache write error<br>\n";
				}
				if ($rs->EOF && !$eof) {
					$rs->MoveFirst();
					//$rs = &csv2rs($md5file,$err);		
					$rs->connection = &$this; // Pablo suggestion
				}  
				
			} else
				@unlink($md5file);
		} else { 
		// ok, set cached object found
			$rs->connection = &$this; // Pablo suggestion
			if ($this->debug){ 
				$ttl = $rs->timeCreated + $secs2cache - time();
				print " $md5file reloaded, ttl=$ttl<br>\n";
			}
		}
		return $rs;
	}
	
	
    /**
	 * Generates an Update Query based on an existing recordset.
	 * $arrFields is an associative array of fields with the value
	 * that should be assigned.
	 *
	 * Note: This function should only be used on a recordset
	 *       that is run against a single table and sql should only 
	 *		 be a simple select stmt with no groupby/orderby/limit
	 *
	 * "Jonathan Younger" <jyounger@unilab.com>
  	 */
	function GetUpdateSQL(&$rs, $arrFields,$forceUpdate=false,$magicq=false)
	{
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		return _adodb_getupdatesql($this,$rs,$arrFields,$forceUpdate,$magicq);
	}


	/**
	 * Generates an Insert Query based on an existing recordset.
	 * $arrFields is an associative array of fields with the value
	 * that should be assigned.
	 *
	 * Note: This function should only be used on a recordset
	 *       that is run against a single table.
  	 */
	function GetInsertSQL(&$rs, $arrFields,$magicq=false)
	{	
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		return _adodb_getinsertsql($this,$rs,$arrFields,$magicq);
	}
	

	/**
	* Usage:
	*	UpdateBlob('TABLE', 'COLUMN', $var, 'ID=1', 'BLOB');
	*	
	*	$blobtype supports 'BLOB' and 'CLOB'
	*
	*	$conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
	*	$conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
	*/
	
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		return $this->Execute("UPDATE $table SET $column=? WHERE $where",array($val)) != false;
	}

		/**
	* Usage:
	*	UpdateBlob('TABLE', 'COLUMN', '/path/to/file', 'ID=1', 'BLOB');
	*	
	*	$blobtype supports 'BLOB' and 'CLOB'
	*
	*	$conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
	*	$conn->UpdateBlob('blobtable','blobcol',$blobpath,'id=1');
	*/
	function UpdateBlobFile($table,$column,$path,$where,$blobtype='BLOB')
	{
		$fd = fopen($path,'rb');
		if ($fd === false) return false;
		$val = fread($fd,filesize($path));
		fclose($fd);
		return $this->UpdateBlob($table,$column,$val,$where,$blobtype);
	}
	
	/**
	* Usage:
	*	UpdateClob('TABLE', 'COLUMN', $var, 'ID=1', 'CLOB');
	*
	*	$conn->Execute('INSERT INTO clobtable (id, clobcol) VALUES (1, null)');
	*	$conn->UpdateClob('clobtable','clobcol',$clob,'id=1');
	*/
	function UpdateClob($table,$column,$val,$where)
	{
		return $this->UpdateBlob($table,$column,$val,$where,'CLOB');
	}
	
	
	/**
	 *  @meta	contains the desired type, which could be...
	 *	C for character. You will have to define the precision yourself.
	 *	X for teXt. For unlimited character lengths.
	 *	B for Binary
	 *  F for floating point, with no need to define scale and precision
	 * 	N for decimal numbers, you will have to define the (scale, precision) yourself
	 *	D for date
	 *	T for timestamp
	 * 	L for logical/Boolean
	 *	I for integer
	 *	R for autoincrement counter/integer
	 *  and if you want to use double-byte, add a 2 to the end, like C2 or X2.
	 * 
	 *
	 * @return the actual type of the data or false if no such type available
	*/
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C':
		case 'X':
			return 'VARCHAR';
		case 'B':
			
		case 'D':
		case 'T':
		case 'L':
		
		case 'R':
			
		case 'I':
		case 'N':
			return false;
		}
	}
	
	
	/*
	* Maximum size of C field
	*/
	function CharMax()
	{
		return 255; // make it conservative if not defined
	}
	
	
	/*
	* Maximum size of X field
	*/
	function TextMax()
	{
		return 4000; // make it conservative if not defined
	}
	
	
	/**
	 * Close Connection
	 */
	function Close() 
	{
		return $this->_close();
		
		// "Simon Lee" <simon@mediaroad.com> reports that persistent connections need 
		// to be closed too!
		//if ($this->_isPersistentConnection != true) return $this->_close();
		//else return true;	
	}
	
	
	/**
	 * Begin a Transaction. Must be followed by CommitTrans() or RollbackTrans().
	 *
	 * @return true if succeeded or false if database does not support transactions
	 */
	function BeginTrans() {return false;}
	
	
	/**
	 * If database does not support transactions, always return true as data always commited
	 *
	 * @param $ok  set to false to rollback transaction, true to commit
	 *
	 * @return true/false.
	 */
	function CommitTrans($ok=true) 
	{ return true;}
	
	
	/**
	 * If database does not support transactions, rollbacks always fail, so return false
	 *
	 * @return true/false.
	 */
	function RollbackTrans() 
	{ return false;}


    /**
	 * return the databases that the driver can connect to. 
	 * Some databases will return an empty array.
	 *
	 * @return an array of database names.
	 */
        function MetaDatabases() 
		{return false;}
        
	/**
	 * @return  array of tables for current database.
	 */ 
    function MetaTables() 
	{
	global $ADODB_FETCH_MODE;
	
		if ($this->metaTablesSQL) {
			$save = $ADODB_FETCH_MODE; 
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM; 
			$rs = $this->Execute($this->metaTablesSQL);
			$ADODB_FETCH_MODE = $save; 
			
			if ($rs === false) return false;
			$arr = $rs->GetArray();
			$arr2 = array();
			for ($i=0; $i < sizeof($arr); $i++) {
				$arr2[] = $arr[$i][0];
			}
			$rs->Close();
			return $arr2;
		}
		return false;
	}
	
	
	/**
	 * List columns in a database as an array of ADOFieldObjects. 
	 * See top of file for definition of object.
	 *
	 * @params table	table name to query
	 * @params upper	uppercase table name (required by some databases)
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */ 
    function MetaColumns($table,$upper=true) 
	{
	global $ADODB_FETCH_MODE;
	
		if (!empty($this->metaColumnsSQL)) {
			$save = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,($upper)?strtoupper($table):$table));
			$ADODB_FETCH_MODE = $save;
			if ($rs === false) return false;

			$retarr = array();
			while (!$rs->EOF) { //print_r($rs->fields);
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[0];
				$fld->type = $rs->fields[1];
				$fld->max_length = $rs->fields[2];
				$retarr[strtoupper($fld->name)] = $fld;	
				
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;	
		}
		return false;
	}
    
	/**
	 * List columns names in a table as an array. 
	 * @params table	table name to query
	 *
	 * @return  array of column names for current table.
	 */ 
    function MetaColumnNames($table) 
	{
		$objarr = $this->MetaColumns($table);
		if (!is_array($objarr)) return false;
		
		$arr = array();
		foreach($objarr as $v) {
			$arr[] = $v->name;
		}
		return $arr;
	}
        	
	/**
	 * Different SQL databases used different methods to combine strings together.
	 * This function provides a wrapper. 
	 * 
	 * @param s	variable number of string parameters
	 *
	 * Usage: $db->Concat($str1,$str2);
	 * 
	 * @return concatenated string
	 */ 	 
	function Concat()
	{	
		$arr = func_get_args();
		return implode($this->concat_operator, $arr);
	}
	
	
	/**
	 * Converts a date "d" to a string that the database can understand.
	 *
	 * @param d	a date in Unix date time format.
	 *
	 * @return  date string in database date format
	 */
	function DBDate($d)
	{
	
		if (empty($d) && $d !== 0) return 'null';

		if (is_string($d) && !is_numeric($d)) 
			if ($this->isoDates) return "'$d'";
			else $d = ADOConnection::UnixDate($d);
		
		return date($this->fmtDate,$d);
	}
	
	
	/**
	 * Converts a timestamp "ts" to a string that the database can understand.
	 *
	 * @param ts	a timestamp in Unix date time format.
	 *
	 * @return  timestamp string in database timestamp format
	 */
	function DBTimeStamp($ts)
	{
		if (empty($ts) && $ts !== 0) return 'null';

		if (is_string($ts) && !is_numeric($ts)) 
			if ($this->isoDates) return "'$ts'";
			else $ts = ADOConnection::UnixTimeStamp($ts);
		return date($this->fmtTimeStamp,$ts);
	}
	
	/**
	 * Also in ADORecordSet.
	 * @param $v is a date string in YYYY-MM-DD format
	 *
	 * @return date in unix timestamp format, or 0 if before TIMESTAMP_FIRST_YEAR, or false if invalid date format
	 */
	function UnixDate($v)
	{
		if (!preg_match( "|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})|", 
			($v), $rr)) return false;

		if ($rr[1] <= TIMESTAMP_FIRST_YEAR) return 0;
		// h-m-s-MM-DD-YY
		return mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
	}
	

	/**
	 * Also in ADORecordSet.
	 * @param $v is a timestamp string in YYYY-MM-DD HH-NN-SS format
	 *
	 * @return date in unix timestamp format, or 0 if before TIMESTAMP_FIRST_YEAR, or false if invalid date format
	 */
	function UnixTimeStamp($v)
	{
		if (!preg_match( 
			"|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9]{1,2}))?$|", 
			($v), $rr)) return false;
		if ($rr[1] <= TIMESTAMP_FIRST_YEAR && $rr[2]<= 1) return 0;
	
		// h-m-s-MM-DD-YY
		return  @mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
	}
	
	/**
	 * Correctly quotes a string so that all strings are escaped. We prefix and append
	 * to the string single-quotes.
	 * An example is  $db->qstr("Don't bother",magic_quotes_runtime());
	 * 
	 * @param s			the string to quote
	 * @param [magic_quotes]	if $s is GET/POST var, set to get_magic_quotes_gpc().
	 *				This undoes the stupidity of magic quotes for GPC.
	 *
	 * @return  quoted string to be sent back to database
	 */
	function qstr($s,$magic_quotes=false)
	{	
	$nofixquotes=false;
		if (!$magic_quotes) {
		
			if ($this->replaceQuote[0] == '\\'){
				$s = str_replace('\\','\\\\',$s);
			}
			return  "'".str_replace("'",$this->replaceQuote,$s)."'";
		}
		
		// undo magic quotes for "
		$s = str_replace('\\"','"',$s);
		
		if ($this->replaceQuote == "\\'")  // ' already quoted, no need to change anything
			return "'$s'";
		else {// change \' to '' for sybase/mssql
			$s = str_replace('\\\\','\\',$s);
			return "'".str_replace("\\'",$this->replaceQuote,$s)."'";
		}
	}
	
	
	/**
	* Will select the supplied $page number from a recordset, given that it is paginated in pages of 
	* $nrows rows per page. It also saves two boolean values saying if the given page is the first 
	* and/or last one of the recordset. Added by Iván Oliva to provide recordset pagination.
	*
	* See readme.htm#ex8 for an example of usage.
	*
	* @param sql
	* @param nrows		is the number of rows per page to get
	* @param page		is the page number to get (1-based)
	* @param [inputarr]	array of bind variables
	* @param [arg3]		is a private parameter only used by jlim
	* @param [secs2cache]		is a private parameter only used by jlim
	* @return		the recordset ($rs->databaseType == 'array')
	*
	* NOTE: phpLens uses a different algorithm and does not use PageExecute().
	*
	*/
	function &PageExecute($sql, $nrows, $page, $inputarr=false, $arg3=false, $secs2cache=0) 
	{
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		if ($this->pageExecuteCountRows) return _adodb_pageexecute_all_rows($this, $sql, $nrows, $page, $inputarr, $arg3, $secs2cache);
		return _adodb_pageexecute_no_last_page($this, $sql, $nrows, $page, $inputarr, $arg3, $secs2cache);

	}
	
		
	/**
	* Will select the supplied $page number from a recordset, given that it is paginated in pages of 
	* $nrows rows per page. It also saves two boolean values saying if the given page is the first 
	* and/or last one of the recordset. Added by Iván Oliva to provide recordset pagination.
	*
	* @param secs2cache	seconds to cache data, set to 0 to force query
	* @param sql
	* @param nrows		is the number of rows per page to get
	* @param page		is the page number to get (1-based)
	* @param [inputarr]	array of bind variables
	* @param [arg3]		is a private parameter only used by jlim
	* @return		the recordset ($rs->databaseType == 'array')
	*/
	function &CachePageExecute($secs2cache, $sql, $nrows, $page,$inputarr=false, $arg3=false) {
		return $this->PageExecute($sql,$nrows,$page,$inputarr,$arg3,$secs2cache);
	}

} // end class ADOConnection
	
	
	
	//==============================================================================================	
	// CLASS ADOFetchObj
	//==============================================================================================	
		
	/**
	* Internal placeholder for record objects. Used by ADORecordSet->FetchObj().
	*/
	class ADOFetchObj {
	};
	
	//==============================================================================================	
	// CLASS ADORecordSet_empty
	//==============================================================================================	
	
	/**
	* Lightweight recordset when there are no records to be returned
	*/
	class ADORecordSet_empty
	{
		var $dataProvider = 'empty';
		var $EOF = true;
		var $_numOfRows = 0;
		var $fields = false;
		var $connection = false;
		function RowCount() {return 0;}
		function RecordCount() {return 0;}
		function PO_RecordCount(){return 0;}
		function Close(){return true;}
		function FetchRow() {return false;}
		function FieldCount(){ return 0;}
	}
	
	//==============================================================================================	
	// CLASS ADORecordSet
	//==============================================================================================	
	
	/**
	 * RecordSet class that represents the dataset returned by the database.
	 * To keep memory overhead low, this class holds only the current row in memory.
	 * No prefetching of data is done, so the RecordCount() can return -1 ( which
	 * means recordcount not known).
	 */
	class ADORecordSet {
	/*
	 * public variables	
	 */
	var $dataProvider = "native";
	var $fields = false; 	// holds the current row data
	var $blobSize = 64; 	// any varchar/char field this size or greater is treated as a blob
							// in other words, we use a text area for editting.
	var $canSeek = false; 	// indicates that seek is supported
	var $sql; 				// sql text
	var $EOF = false;		/* Indicates that the current record position is after the last record in a Recordset object. */
	
	var $emptyTimeStamp = '&nbsp;'; // what to display when $time==0
	var $emptyDate = '&nbsp;'; // what to display when $time==0
	var $debug = false;
	var $timeCreated=0; 	// datetime in Unix format rs created -- for cached recordsets

	var $bind = false; 		// used by Fields() to hold array - should be private?
	var $fetchMode;			// default fetch mode
	var $connection = false; // the parent connection
	/*
	 *	private variables	
	 */
	var $_numOfRows = -1;	/* number of rows, or -1 */
	var $_numOfFields = -1;	/* number of fields in recordset */
	var $_queryID = -1;		/* This variable keeps the result link identifier.	*/
	var $_currentRow = -1;	/* This variable keeps the current row in the Recordset.	*/
	var $_closed = false; 	/* has recordset been closed */
	var $_inited = false; 	/* Init() should only be called once */
	var $_obj; 				/* Used by FetchObj */
	var $_names;			/* Used by FetchObj */
	
	var $_currentPage = -1;	/* Added by Iván Oliva to implement recordset pagination */
	var $_atFirstPage = false;	/* Added by Iván Oliva to implement recordset pagination */
	var $_atLastPage = false;	/* Added by Iván Oliva to implement recordset pagination */
	var $_lastPageNo = -1; 
	var $_maxRecordCount = 0;
	/**
	 * Constructor
	 *
	 * @param queryID  	this is the queryID returned by ADOConnection->_query()
	 *
	 */
	function ADORecordSet($queryID) 
	{
		$this->_queryID = $queryID;
	}
	
	
	
	function Init()
	{
		if ($this->_inited) return;
		$this->_inited = true;
		
		if ($this->_queryID) @$this->_initrs();
		else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
		}
		if ($this->_numOfRows != 0 && $this->_numOfFields && $this->_currentRow == -1) {
			$this->_currentRow = 0;
			$this->EOF = ($this->_fetch() === false);
		} else 
			$this->EOF = true;
	}
	
	
	/**
	 * Generate a <SELECT> string from a recordset, and return the string.
	 * If the recordset has 2 cols, we treat the 1st col as the containing 
	 * the text to display to the user, and 2nd col as the return value. Default
	 * strings are compared with the FIRST column.
	 *
	 * @param name  		name of <SELECT>
	 * @param [defstr]		the value to hilite. Use an array for multiple hilites for listbox.
	 * @param [blank1stItem]	true to leave the 1st item in list empty
	 * @param [multiple]		true for listbox, false for popup
	 * @param [size]		#rows to show for listbox. not used by popup
	 * @param [selectAttr]		additional attributes to defined for <SELECT>.
	 *				useful for holding javascript onChange='...' handlers.
	 & @param [compareFields0]	when we have 2 cols in recordset, we compare the defstr with 
	 *				column 0 (1st col) if this is true. This is not documented.
	 *
	 * @return HTML
	 *
	 * changes by glen.davies@cce.ac.nz to support multiple hilited items
	 */
	function GetMenu($name,$defstr='',$blank1stItem=true,$multiple=false,
			$size=0, $selectAttr='',$compareFields0=true)
	{
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		return _adodb_getmenu($this, $name,$defstr,$blank1stItem,$multiple,
			$size, $selectAttr,$compareFields0);
	}
	
	/**
	 * Generate a <SELECT> string from a recordset, and return the string.
	 * If the recordset has 2 cols, we treat the 1st col as the containing 
	 * the text to display to the user, and 2nd col as the return value. Default
	 * strings are compared with the SECOND column.
	 *
	 */
	function GetMenu2($name,$defstr='',$blank1stItem=true,$multiple=false,$size=0, $selectAttr='')	
	{
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		return _adodb_getmenu($this,$name,$defstr,$blank1stItem,$multiple,
			$size, $selectAttr,false);
	}


	/**
	 * return recordset as a 2-dimensional array.
	 *
	 * @param [nRows]  is the number of rows to return. -1 means every row.
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function GetArray($nRows = -1) 
	{
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nRows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}
	
	/*
	* Some databases allow multiple recordsets to be returned. This function
	* will return true if there is a next recordset, or false if no more.
	*/
	function NextRecordSet()
	{
		return false;
	}
	
	/**
	 * return recordset as a 2-dimensional array. 
	 * Helper function for ADOConnection->SelectLimit()
	 *
	 * @param offset	is the row to start calculations from (1-based)
	 * @param [nrows]	is the number of rows to return
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= 0) return $this->GetArray($nrows);
		$this->Move($offset);
		
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}
	
	
	/**
	 * Synonym for GetArray() for compatibility with ADO.
	 *
	 * @param [nRows]  is the number of rows to return. -1 means every row.
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function GetRows($nRows = -1) 
	{
		return $this->GetArray($nRows);
	}
	
	/**
	 * return whole recordset as a 2-dimensional associative array if there are more than 2 columns. 
	 * The first column is treated as the key and is not included in the array. 
	 * If there is only 2 columns, it will return a 1 dimensional array of key-value pairs unless
	 * $force_array == true.
	 *
	 * @param [force_array] has only meaning if we have 2 data columns. If false, a 1 dimensional
	 * 	array is returned, otherwise a 2 dimensional array is returned. If this sounds confusing,
	 * 	read the source.
	 *
	 * @return an associative array indexed by the first column of the array, 
	 * 	or false if the  data has less than 2 cols.
	 */
	function GetAssoc($force_array = false) {
		$cols = $this->_numOfFields;
		if ($cols < 2) {
			return false;
		}
		$numIndex = isset($this->fields[0]);
		$results = array();
		if ($cols > 2 || $force_array) {
			if ($numIndex) {
				while (!$this->EOF) {
				$results[trim($this->fields[0])] = array_slice($this->fields, 1);
				$this->MoveNext();
				}
			} else {
				while (!$this->EOF) {
					$results[trim(reset($this->fields))] = array_slice($this->fields, 1);
					$this->MoveNext();
				}
			}
		} else {
			// return scalar values
			if ($numIndex) {
				while (!$this->EOF) {
				// some bug in mssql PHP 4.02 -- doesn't handle references properly so we FORCE creating a new string
					$results[trim(($this->fields[0]))] = $this->fields[1];
					$this->MoveNext();
				}
			} else {
				while (!$this->EOF) {
				// some bug in mssql PHP 4.02 -- doesn't handle references properly so we FORCE creating a new string
					$v1 = trim(reset($this->fields));
					$v2 = ''.next($this->fields); 
					$results[$v1] = $v2;
					$this->MoveNext();
				}
			}
		}
		return $results; 
	}
	
	
	/**
	 *
	 * @param v  	is the character timestamp in YYYY-MM-DD hh:mm:ss format
	 * @param fmt 	is the format to apply to it, using date()
	 *
	 * @return a timestamp formated as user desires
	 */
	function UserTimeStamp($v,$fmt='Y-m-d H:i:s')
	{
		$tt = $this->UnixTimeStamp($v);
		// $tt == -1 if pre TIMESTAMP_FIRST_YEAR
		if (($tt === false || $tt == -1) && $v != false) return $v;
		if ($tt == 0) return $this->emptyTimeStamp;
		
		return date($fmt,$tt);
	}
	
	
    /**
	 * @param v  	is the character date in YYYY-MM-DD format
	 * @param fmt 	is the format to apply to it, using date()
	 *
	 * @return a date formated as user desires
	 */
	function UserDate($v,$fmt='Y-m-d')
	{
		$tt = $this->UnixDate($v);
		// $tt == -1 if pre TIMESTAMP_FIRST_YEAR
		if (($tt === false || $tt == -1) && $v != false) return $v;
		else if ($tt == 0) return $this->emptyDate;
		else if ($tt == -1) { // pre-TIMESTAMP_FIRST_YEAR
		}
		return date($fmt,$tt);
	
	}
	
	
	/**
	 * @param $v is a date string in YYYY-MM-DD format
	 *
	 * @return date in unix timestamp format, or 0 if before TIMESTAMP_FIRST_YEAR, or false if invalid date format
	 */
	function UnixDate($v)
	{
		if (!preg_match( "|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})|", 
			($v), $rr)) return false;
			
		if ($rr[1] <= 1903) return 0;
		// h-m-s-MM-DD-YY
		return mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
	}
	

	/**
	 * @param $v is a timestamp string in YYYY-MM-DD HH-NN-SS format
	 *
	 * @return date in unix timestamp format, or 0 if before TIMESTAMP_FIRST_YEAR, or false if invalid date format
	 */
	function UnixTimeStamp($v)
	{
		if (!preg_match( 
			"|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9]{1,2}))?$|", 
			($v), $rr)) return false;
		if ($rr[1] <= 1903 && $rr[2]<= 1) return 0;
	
		// h-m-s-MM-DD-YY
		return  @mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
	}
	
	
	/**
	* PEAR DB Compat - do not use internally
	*/
	function Free()
	{
		return $this->Close();
	}
	
	
	/**
	* PEAR DB compat, number of rows
	*/
	function NumRows()
	{
		return $this->_numOfRows;
	}
	
	
	/**
	* PEAR DB compat, number of cols
	*/
	function NumCols()
	{
		return $this->_numOfCols;
	}
	
	/**
	* Fetch a row, returning false if no more rows. 
	* This is PEAR DB compat mode.
	*
	* @return false or array containing the current record
	*/
	function FetchRow()
	{
		if ($this->EOF) return false;
		$arr = $this->fields;
		$this->_currentRow++;
		if (!$this->_fetch()) $this->EOF = true;
		return $arr;
	}
	
	
	/**
	* Fetch a row, returning PEAR_Error if no more rows. 
	* This is PEAR DB compat mode.
	*
	* @return DB_OK or error object
	*/
	function FetchInto(&$arr)
	{
		if ($this->EOF) return (defined('PEAR_ERROR_RETURN')) ? new PEAR_Error('EOF',-1): false;
		$arr = $this->fields;
		$this->MoveNext();
		return 1; // DB_OK
	}
	
	
	/**
	 * Move to the first row in the recordset. Many databases do NOT support this.
	 *
	 * @return true or false
	 */
	function MoveFirst() 
	{
		if ($this->_currentRow == 0) return true;
		return $this->Move(0);			
	}			

	
	/**
	 * Move to the last row in the recordset. 
	 *
	 * @return true or false
	 */
	function MoveLast() 
	{
		if ($this->_numOfRows >= 0) return $this->Move($this->_numOfRows-1);
                while (!$this->EOF) $this->MoveNext();
		return true;
	}
	
	
	/**
	 * Move to next record in the recordset.
	 *
	 * @return true if there still rows available, or false if there are no more rows (EOF).
	 */
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_fetch()) return true;
		}
		$this->EOF = true;
		/* -- tested error handling when scrolling cursor -- seems useless.
		$conn = $this->connection;
		if ($conn && $conn->raiseErrorFn && ($errno = $conn->ErrorNo())) {
			$fn = $conn->raiseErrorFn;
			$fn($conn->databaseType,'MOVENEXT',$errno,$conn->ErrorMsg().' ('.$this->sql.')',$conn->host,$conn->database);
		}
		*/
		return false;
	}	
	
	/**
	 * Random access to a specific row in the recordset. Some databases do not support
	 * access to previous rows in the databases (no scrolling backwards).
	 *
	 * @param rowNumber is the row to move to (0-based)
	 *
	 * @return true if there still rows available, or false if there are no more rows (EOF).
	 */
	function Move($rowNumber = 0) 
	{
		if ($rowNumber == $this->_currentRow) return true;
		if ($rowNumber > $this->_numOfRows)
       		if ($this->_numOfRows != -1) $rowNumber = $this->_numOfRows-1;
   
        if ($this->canSeek) {
        	if ($this->_seek($rowNumber)) {
				$this->_currentRow = $rowNumber;
				if ($this->_fetch()) {
					$this->EOF = false;	
                                   //  $this->_currentRow += 1;			
					return true;
				}
			} else 
				return false;
        } else {
            if ($rowNumber < $this->_currentRow) return false;
            while (! $this->EOF && $this->_currentRow < $rowNumber) {
				$this->_currentRow++;
                if (!$this->_fetch()) $this->EOF = true;
			}
            return !($this->EOF);
        }
		
		$this->fields = null;	
		$this->EOF = true;
		return false;
	}
	
		
	/**
	 * Get the value of a field in the current row by column name.
	 * Will not work if ADODB_FETCH_MODE is set to ADODB_FETCH_NUM.
	 * 
	 * @param colname  is the field to access
	 *
	 * @return the value of $colname column
	 */
	function Fields($colname)
	{
		return $this->fields[$colname];
	}
	
	
  /**
   * Use associative array to get fields array for databases that do not support
   * associative arrays. Submitted by Paolo S. Asioli paolo.asioli@libero.it
   *
   * If you don't want uppercase cols, set $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC
   * before you execute your SQL statement, and access $rs->fields['col'] directly.
   */
	function &GetRowAssoc($upper=true)
	{
	 
	   	if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[($upper) ? strtoupper($o->name) : strtolower($o->name)] = $i;
			}
		}
		
		$record = array();
		foreach($this->bind as $k => $v) {
            $record[$k] = $this->fields[$v];
        }

        return $record;
    }
	
	
	/**
	 * Clean up recordset
	 *
	 * @return true or false
	 */
	function Close() 
	{
		// free connection object - this seems to globally free the object
		// and not merely the reference, so don't do this...
		// $this->connection = false; 
		if (!$this->_closed) {
			$this->_closed = true;
			return $this->_close();		
		} else
			return true;
	}
	
	/**
	 * synonyms RecordCount and RowCount	
	 *
	 * @return the number of rows or -1 if this is not supported
	 */
	function RecordCount() {return $this->_numOfRows;}
	
	
	/*
	* If we are using PageExecute(), this will return the maximum possible rows
	* that can be returned when paging a recordset.
	*/
	function MaxRecordCount()
	{
		return ($this->_maxRecordCount) ? $this->_maxRecordCount : $this->RecordCount();
	}
	
	/**
	 * synonyms RecordCount and RowCount	
	 *
	 * @return the number of rows or -1 if this is not supported
	 */
	function RowCount() {return $this->_numOfRows;} 
	

	 /**
	 * Portable RecordCount. Pablo Roca <pabloroca@mvps.org>
	 *
     * @return  the number of records from a previous SELECT. All databases support this.
	 *
	 * But aware possible problems in multiuser environments. For better speed the table
	 * must be indexed by the condition. Heavy test this before deploying.
     */ 
    function PO_RecordCount($table="", $condition="") {
        
        $lnumrows = $this->_numOfRows;
    	// the database doesn't support native recordcount, so we do a workaround
        if ($lnumrows == -1 && $this->connection) {
            IF ($table) {
                if ($condition) $condition = " WHERE " . $condition; 
                $resultrows = &$this->connection->Execute("SELECT COUNT(*) FROM $table $condition");
                if ($resultrows) $lnumrows = reset($resultrows->fields);
            }
        }
        return $lnumrows;
    }
	
	/**
	 * @return the current row in the recordset. If at EOF, will return the last row. 0-based.
	 */
	function CurrentRow() {return $this->_currentRow;}
	
	/**
	 * synonym for CurrentRow -- for ADO compat
	 *
	 * @return the current row in the recordset. If at EOF, will return the last row. 0-based.
	 */
	function AbsolutePosition() {return $this->_currentRow;}
	
	/**
	 * @return the number of columns in the recordset. Some databases will set this to 0
	 * if no records are returned, others will return the number of columns in the query.
	 */
	function FieldCount() {return $this->_numOfFields;}   


	/**
	 * Get the ADOFieldObject of a specific column.
	 *
	 * @param fieldoffset	is the column position to access(0-based).
	 *
	 * @return the ADOFieldObject for that column, or false.
	 */
	function &FetchField($fieldoffset) 
	{
		// must be defined by child class
	}	
	
	/**
	 * Get the ADOFieldObjects of all columns in an array.
	 *
	 */
	function FieldTypesArray()
	{
		$arr = array();
		for ($i=0, $max=$this->_numOfFields; $i < $max; $i++) 
			$arr[] = $this->FetchField($i);
		return $arr;
	}
	
	/**
	* Return the fields array of the current row as an object for convenience.
	* 
	* @param $isupper to set the object property names to uppercase
	*
	* @return the object with the properties set to the fields of the current row
	*/
	function &FetchObject($isupper=true)
	{
		if (empty($this->_obj)) {
			$this->_obj = new ADOFetchObj();
			$this->_names = array();
			for ($i=0; $i <$this->_numOfFields; $i++) {
				$f = $this->FetchField($i);
				$this->_names[] = $f->name;
			}
		}
		$i = 0;
		$o = &$this->_obj;
		for ($i=0; $i <$this->_numOfFields; $i++) {
			$name = $this->_names[$i];
			if ($isupper) $n = strtoupper($name);
			else $n = $name;
			
			$o->$n = $this->Fields($name);
		}
		return $o;
	}
	/**
	* Return the fields array of the current row as an object for convenience.
	* 
	* @param $isupper to set the object property names to uppercase
	*
	* @return the object with the properties set to the fields of the current row,
	* 	or false if EOF
	*
	* Fixed bug reported by tim@orotech.net
	*/
	function &FetchNextObject($isupper=true)
	{
		$o = false;
		if ($this->_numOfRows != 0 && !$this->EOF) {
			$o = $this->FetchObject($isupper);	
			$this->_currentRow++;
			if ($this->_fetch()) return $o;
		}
		$this->EOF = true;
		return $o;
	}
	
	/**
	 * Get the metatype of the column. This is used for formatting. This is because
	 * many databases use different names for the same type, so we transform the original
	 * type to our standardised version which uses 1 character codes:
	 *
	 * @param t  is the type passed in. Normally is ADOFieldObject->type.
	 * @param len is the maximum length of that field. This is because we treat character
	 * 	fields bigger than a certain size as a 'B' (blob).
	 * @param fieldobj is the field object returned by the database driver. Can hold
	 *	additional info (eg. primary_key for mysql).
	 * 
	 * @return the general type of the data: 
	 *	C for character < 200 chars
	 *	X for teXt (>= 200 chars)
	 *	B for Binary
	 * 	N for numeric floating point
	 *	D for date
	 *	T for timestamp
	 * 	L for logical/Boolean
	 *	I for integer
	 *	R for autoincrement counter/integer
	 * 
	 *
	*/
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		switch (strtoupper($t)) {
		case 'VARCHAR':
		case 'VARCHAR2':
		case 'CHAR':
		case 'STRING':
		case 'C':
		case 'NCHAR':
		case 'NVARCHAR':
		case 'VARYING':
		case 'BPCHAR':
		case 'CHARACTER':
			if (!empty($this)) if ($len <= $this->blobSize) return 'C';
			else if ($len <= 250) return 'C';
		
		case 'LONGCHAR':
		case 'TEXT':
		case 'M':
		case 'X':
		case 'CLOB':
		case 'NCLOB':
		case 'LONG':
			return 'X';
		
		case 'BLOB':
		case 'NTEXT':
		case 'BINARY':
		case 'VARBINARY':
		case 'LONGBINARY':
		case 'B':
			return 'B';
			
		case 'DATE':
		case 'D':
			return 'D';
		
		
		case 'TIME':
		case 'TIMESTAMP':
		case 'DATETIME':
		case 'T':
			return 'T';
		
		case 'BOOLEAN': 
		case 'BIT':
		case 'L':
			return 'L';
			
		case 'COUNTER':
		case 'R':
		case 'SERIAL': /* ifx */
			return 'R';
			
		case 'INT':
		case 'INTEGER':
		case 'SHORT':
		case 'TINYINT':
		case 'SMALLINT':
		case 'I':
			if (!empty($fieldobj->primary_key)) return 'R';
			return 'I';
			
		default: return 'N';
		}
	}
	
	function _close() {}
	
	/**
	 * set/returns the current recordset page when paginating
	 */
	function AbsolutePage($page=-1)
	{
		if ($page != -1) $this->_currentPage = $page;
		return $this->_currentPage;
	}
	
	/**
	 * set/returns the status of the atFirstPage flag when paginating
	 */
	function AtFirstPage($status=false)
	{
		if ($status != false) $this->_atFirstPage = $status;
		return $this->_atFirstPage;
	}
	
	function LastPageNo($page = false)
	{
		if ($page != false) $this->_lastPageNo = $page;
		return $this->_lastPageNo;
	}
	
	/**
	 * set/returns the status of the atLastPage flag when paginating
	 */
	function AtLastPage($status=false)
	{
		if ($status != false) $this->_atLastPage = $status;
		return $this->_atLastPage;
	}
} // end class ADORecordSet
	
	//==============================================================================================	
	// CLASS ADORecordSet_array
	//==============================================================================================	
	
	/**
	 * This class encapsulates the concept of a recordset created in memory
	 * as an array. This is useful for the creation of cached recordsets.
	 * 
	 * Note that the constructor is different from the standard ADORecordSet
	 */
	
	class ADORecordSet_array extends ADORecordSet
	{
		var $databaseType = "array";
	
		var $_array; 	// holds the 2-dimensional data array
		var $_types;	// the array of types of each column (C B I L M)
		var $_colnames;	// names of each column in array
		var $_skiprow1;	// skip 1st row because it holds column names
		var $_fieldarr; // holds array of field objects
		var $canSeek = true;
		var $affectedrows = false;
		var $insertid = false;
		var $sql = '';
		/**
		 * Constructor
		 *
		 */
		function ADORecordSet_array($fakeid=1)
		{
		global $ADODB_FETCH_MODE;
		
			$this->ADORecordSet($fakeid); // fake queryID		
			$this->fetchMode = $ADODB_FETCH_MODE;
		}
		
		
		/**
		 * Setup the Array. Later we will have XML-Data and CSV handlers
		 *
		 * @param array		is a 2-dimensional array holding the data.
		 *			The first row should hold the column names 
		 *			unless paramter $colnames is used.
		 * @param typearr	holds an array of types. These are the same types 
		 *			used in MetaTypes (C,B,L,I,N).
		 * @param [colnames]	array of column names. If set, then the first row of
		 *			$array should not hold the column names.
		 */
		function InitArray(&$array,$typearr,$colnames=false)
		{
			$this->_array = $array;
			$this->_types = &$typearr;	
			if ($colnames) {
				$this->_skiprow1 = false;
				$this->_colnames = $colnames;
			} else $this->_colnames = $array[0];
			
			$this->Init();
		}
		/**
		 * Setup the Array and datatype file objects
		 *
		 * @param array		is a 2-dimensional array holding the data.
		 *			The first row should hold the column names 
		 *			unless paramter $colnames is used.
		 * @param fieldarr	holds an array of ADOFieldObject's.
		 */
		function InitArrayFields(&$array,&$fieldarr)
		{
			$this->_array = &$array;
			$this->_skiprow1= false;
			if ($fieldarr) {
				$this->_fieldobjects = &$fieldarr;
			} 
			
			$this->Init();
		}
		
		function _initrs()
		{
			$this->_numOfRows =  sizeof($this->_array);
			if ($this->_skiprow1) $this->_numOfRows -= 1;
		
			$this->_numOfFields =(isset($this->_fieldobjects)) ?
				 sizeof($this->_fieldobjects):sizeof($this->_types);
		}
		
		/* Use associative array to get fields array */
		function Fields($colname)
		{
			if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];
	
			if (!$this->bind) {
				$this->bind = array();
				for ($i=0; $i < $this->_numOfFields; $i++) {
					$o = $this->FetchField($i);
					$this->bind[strtoupper($o->name)] = $i;
				}
			}
			return $this->fields[$this->bind[strtoupper($colname)]];
		}
		
		function &FetchField($fieldOffset = -1) 
		{
			if (isset($this->_fieldobjects)) {
				return $this->_fieldobjects[$fieldOffset];
			}
			$o =  new ADOFieldObject();
			$o->name = $this->_colnames[$fieldOffset];
			$o->type =  $this->_types[$fieldOffset];
			$o->max_length = -1; // length not known
			
			return $o;
		}
			
		function _seek($row)
		{
			return true;
		}
		
		function _fetch()
		{
			$pos = $this->_currentRow;
			
			if ($this->_skiprow1) {
				if ($this->_numOfRows <= $pos-1) return false;
				$pos += 1;
			} else {
				if ($this->_numOfRows <= $pos) return false;
			}
			
			$this->fields = $this->_array[$pos];
			return true;
		}
		
		function _close() 
		{
			return true;	
		}
	
	} // ADORecordSet_array

	//==============================================================================================	
	// HELPER FUNCTIONS
	//==============================================================================================			
	
    /**
	 * Synonym for ADOLoadCode.
	 *
	 * @deprecated
	 */
	function ADOLoadDB($dbType) 
	{ 
		return ADOLoadCode($dbType);
	}
        
    /**
	 * Load the code for a specific database driver
	 */
    function ADOLoadCode($dbType) 
	{
	GLOBAL $ADODB_Database;
	
		if (!$dbType) return false;
		$ADODB_Database = strtolower($dbType);
		switch ($ADODB_Database) {
			case 'maxsql': $ADODB_Database = 'mysqlt'; break;
			case 'pgsql': $ADODB_Database = 'postgres7'; break;
		}
		include_once(ADODB_DIR."/drivers/adodb-$ADODB_Database.inc.php");		
		return true;		
	}

	/**
	 * synonym for ADONewConnection for people like me who cannot remember the correct name
	 */
	function &NewADOConnection($db='')
	{
		return ADONewConnection($db);
	}
	
	/**
	 * Instantiate a new Connection class for a specific database driver.
	 *
	 * @param [db]  is the database Connection object to create. If undefined,
	 * 	use the last database driver that was loaded by ADOLoadCode().
	 *
	 * @return the freshly created instance of the Connection class.
	 */
	function &ADONewConnection($db='')
	{
	GLOBAL $ADODB_Database;
	
		if ($db) {
			if ($ADODB_Database != $db) ADOLoadCode($db);
		} else { 
			if (!empty($ADODB_Database)) ADOLoadCode($ADODB_Database);
			else print "<p>ADONewConnection: No database driver defined</p>";
		}
		
		$cls = 'ADODB_'.$ADODB_Database;
		$obj = new $cls();
		if (defined('ADODB_ERROR_HANDLER')) {
			$obj->raiseErrorFn = ADODB_ERROR_HANDLER;
		}
		return $obj;
	}
	
	/**
	* Save a file $filename and its $contents (normally for caching) with file locking
	*/
	function adodb_write_file($filename, $contents,$debug=false)
	{ 
	# http://www.php.net/bugs.php?id=9203 Bug that flock fails on Windows
	# So to simulate locking, we assume that rename is an atomic operation.
	# First we delete $filename, then we create a $tempfile write to it and 
	# rename to the desired $filename. If the rename works, then we successfully 
	# modified the file exclusively.
	# What a stupid need - having to simulate locking.
	# Risks:
	# 1. $tempfile name is not unique -- very very low
	# 2. unlink($filename) fails -- ok, rename will fail
	# 3. adodb reads stale file because unlink fails -- ok, $rs timeout occurs
	# 4. another process creates $filename between unlink() and rename() -- ok, rename() fails and  cache updated
		if (strpos(strtoupper(PHP_OS),'WIN') !== false) {
			// skip the decimal place
			$mtime = substr(str_replace(' ','_',microtime()),2); 
			// unlink will let some latencies develop, so uniqid() is more random
			@unlink($filename);
			// getmypid() actually returns 0 on Win98 - never mind!
			$tmpname = $filename.uniqid($mtime).getmypid();
			if (!($fd = fopen($tmpname,'a'))) return false;
			$ok = ftruncate($fd,0);			
			if (!fwrite($fd,$contents)) $ok = false;
			fclose($fd);
			chmod($tmpname,0644);
			if (!@rename($tmpname,$filename)) {
				unlink($tmpname);
				$ok = false;
			}
			if (!$ok) {
				if ($debug) print " Rename $tmpname ".($ok? 'ok' : 'failed')." <br />\n";
			}
			return $ok;
		}
		if (!($fd = fopen($filename, 'a'))) return false;
		if (flock($fd, LOCK_EX) && ftruncate($fd, 0)) {
			$ok = fwrite( $fd, $contents );
			fclose($fd);
			chmod($filename,0644);
		}else {
			fclose($fd);
			if ($debug)print " Failed acquiring lock for $filename<br>\n";
			$ok = false;
		}
	
		return $ok;
    }

} // defined
?>