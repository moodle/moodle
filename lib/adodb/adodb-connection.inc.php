<?php
/** 
 * @version V3.40 7 April 2003 (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
 * Whenever there is any discrepancy between the two licenses, 
 * the BSD license will take precedence. 
 *
 * Set tabs to 4 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 *
 */

	
	function ADODB_TransMonitor($dbms, $fn, $errno, $errmsg, $p1, $p2, &$thisConnection)
	{
		/* print "Errorno ($fn errno=$errno m=$errmsg) "; */
		
		$thisConnection->_transOK = false;
		if ($thisConnection->_oldRaiseFn) {
			$fn = $thisConnection->_oldRaiseFn;
			$fn($dbms, $fn, $errno, $errmsg, $p1, $p2,$thisConnection);
		}
	}
	
	
    /**
	 * Connection object. For connecting to databases, and executing queries.
	 */ 
	class ADOConnection {
	/*  */
	/*  PUBLIC VARS  */
	/*  */
	var $dataProvider = 'native';
	var $databaseType = '';		/* / RDBMS currently in use, eg. odbc, mysql, mssql					 */
	var $database = '';			/* / Name of database to be used.	 */
	var $host = ''; 			/* / The hostname of the database server	 */
	var $user = ''; 			/* / The username which is used to connect to the database server.  */
	var $password = ''; 		/* / Password for the username. For security, we no longer store it. */
	var $debug = false; 		/* / if set to true will output sql statements */
	var $maxblobsize = 256000; 	/* / maximum size of blobs or large text fields -- some databases die otherwise like foxpro */
	var $concat_operator = '+'; /* / default concat operator -- change to || for Oracle/Interbase	 */
	var $fmtDate = "'Y-m-d'";	/* / used by DBDate() as the default date format used by the database */
	var $fmtTimeStamp = "'Y-m-d, h:i:s A'"; /* / used by DBTimeStamp as the default timestamp fmt. */
	var $true = '1'; 			/* / string that represents TRUE for a database */
	var $false = '0'; 			/* / string that represents FALSE for a database */
	var $replaceQuote = "\\'"; 	/* / string to use to replace quotes */
	var $charSet=false; 		/* / character set to use - only for interbase */
	var $metaTablesSQL = '';
	/* -- */
	var $hasInsertID = false; 		/* / supports autoincrement ID? */
	var $hasAffectedRows = false; 	/* / supports affected rows for update/delete? */
	var $hasTop = false;			/* / support mssql/access SELECT TOP 10 * FROM TABLE */
	var $hasLimit = false;			/* / support pgsql/mysql SELECT * FROM TABLE LIMIT 10 */
	var $readOnly = false; 			/* / this is a readonly database - used by phpLens */
	var $hasMoveFirst = false;  /* / has ability to run MoveFirst(), scrolling backwards */
	var $hasGenID = false; 		/* / can generate sequences using GenID(); */
	var $hasTransactions = true; /* / has transactions */
	/* -- */
	var $genID = 0; 			/* / sequence id used by GenID(); */
	var $raiseErrorFn = false; 	/* / error function to call */
	var $upperCase = false; 	/* / uppercase function to call for searching/where */
	var $isoDates = false; /* / accepts dates in ISO format */
	var $cacheSecs = 3600; /* / cache for 1 hour */
	var $sysDate = false; /* / name of function that returns the current date */
	var $sysTimeStamp = false; /* / name of function that returns the current timestamp */
	var $arrayClass = 'ADORecordSet_array'; /* / name of class used to generate array recordsets, which are pre-downloaded recordsets */
	
	var $noNullStrings = false; /* / oracle specific stuff - if true ensures that '' is converted to ' ' */
	var $numCacheHits = 0; 
	var $numCacheMisses = 0;
	var $pageExecuteCountRows = true;
	var $uniqueSort = false; /* / indicates that all fields in order by must be unique */
	var $leftOuter = false; /* / operator to use for left outer join in WHERE clause */
	var $rightOuter = false; /* / operator to use for right outer join in WHERE clause */
	var $ansiOuter = false; /* / whether ansi outer join syntax supported */
	var $autoRollback = false; /*  autoRollback on PConnect(). */
	var $poorAffectedRows = false; /*  affectedRows not working or unreliable */
	
	var $fnExecute = false;
	var $fnCacheExecute = false;
	var $blobEncodeType = false; /*  false=not required, 'I'=encode to integer, 'C'=encode to char */
	var $dbxDriver = false;
	
	 /*  */
	 /*  PRIVATE VARS */
	 /*  */
	var $_oldRaiseFn =  false;
	var $_transOK = null;
	var $_connectionID	= false;	/* / The returned link identifier whenever a successful database connection is made.	 */
	var $_errorMsg = '';		/* / A variable which was used to keep the returned last error message.  The value will */
								/* / then returned by the errorMsg() function	 */
						
	var $_queryID = false;		/* / This variable keeps the last created result link identifier */
	
	var $_isPersistentConnection = false;	/* / A boolean variable to state whether its a persistent connection or normal connection.	*/ */
	var $_bindInputArray = false; /* / set to true if ADOConnection.Execute() permits binding of array parameters. */
	var $autoCommit = true; 	/* / do not modify this yourself - actually private */
	var $transOff = 0; 			/* / temporarily disable transactions */
	var $transCnt = 0; 			/* / count of nested transactions */
	
	var $fetchMode=false;
	
	/**
	 * Constructor
	 */
	function ADOConnection()			
	{
		die('Virtual Class -- cannot instantiate');
	}
	
	/**
		Get server version info...
		
		@returns An array with 2 elements: $arr['string'] is the description string, 
			and $arr[version] is the version (also a string).
	*/
	function ServerInfo()
	{
		return array('description' => '', 'version' => '');
	}
	
	function _findvers($str)
	{
		if (preg_match('/([0-9]+\.([0-9\.])+)/',$str, $arr)) return $arr[1];
		else return '';
	}
	
	/**
	* All error messages go through this bottleneck function.
	* You can define your own handler by defining the function name in ADODB_OUTP.
	*/
	function outp($msg,$newline=true)
	{
	global $HTTP_SERVER_VARS;
	
		if (defined('ADODB_OUTP')) {
			$fn = ADODB_OUTP;
			$fn($msg,$newline);
			return;
		}
		
		if ($newline) $msg .= "<br>\n";
		
		if (isset($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) echo $msg;
		else echo strip_tags($msg);
		flush();
	}
	
	/**
	 * Connect to database
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 * @param [forceNew]        force new connection
	 *
	 * @return true or false
	 */	  
	function Connect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "", $forceNew = false) 
	{
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword; /*  not stored for security reasons */
		if ($argDatabaseName != "") $this->database = $argDatabaseName;		
		
		$this->_isPersistentConnection = false;	
		if ($fn = $this->raiseErrorFn) {
			if ($forceNew) {
				if ($this->_nconnect($this->host, $this->user, $this->password, $this->database)) return true;
			} else {
				 if ($this->_connect($this->host, $this->user, $this->password, $this->database)) return true;
			}
			$err = $this->ErrorMsg();
			if (empty($err)) $err = "Connection error to server '$argHostname' with user '$argUsername'";
			$fn($this->databaseType,'CONNECT',$this->ErrorNo(),$err,$this->host,$this->database,$this);
		} else {
			if ($forceNew) {
				if ($this->_nconnect($this->host, $this->user, $this->password, $this->database)) return true;
			} else {
				if ($this->_connect($this->host, $this->user, $this->password, $this->database)) return true;
			}
		}
		if ($this->debug) ADOConnection::outp( $this->host.': '.$this->ErrorMsg());
		
		return false;
	}	
	
	 function _nconnect($argHostname, $argUsername, $argPassword, $argDatabaseName)
	 {
	 	return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabaseName);
	 }
	
	
	/**
	 * Always force a new connection to database - currently only works with oracle
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 *
	 * @return true or false
	 */	  
	function NConnect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "") 
	{
		return $this->Connect($argHostname, $argUsername, $argPassword, $argDatabaseName, true);
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
		if (defined('ADODB_NEVER_PERSIST')) 
			return $this->Connect($argHostname,$argUsername,$argPassword,$argDatabaseName);
		
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword;
		if ($argDatabaseName != "") $this->database = $argDatabaseName;		
			
		$this->_isPersistentConnection = true;	
		
		if ($fn = $this->raiseErrorFn) {
			if ($this->_pconnect($this->host, $this->user, $this->password, $this->database)) return true;
			$err = $this->ErrorMsg();
			if (empty($err)) $err = "Connection error to server '$argHostname' with user '$argUsername'";
			$fn($this->databaseType,'PCONNECT',$this->ErrorNo(),$err,$this->host,$this->database,$this);
		} else 
			if ($this->_pconnect($this->host, $this->user, $this->password, $this->database)) return true;

		if ($this->debug) ADOConnection::outp( $this->host.': '.$this->ErrorMsg());
		
		return false;
	}

	/*  Format date column in sql string given an input format that understands Y M D */
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysDate;
		return $col; /*  child class implement */
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
	* PEAR DB Compat
	*/
	function Quote($s)
	{
		return $this->qstr($s,false);
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
	*
	* @param mode	The fetchmode ADODB_FETCH_ASSOC or ADODB_FETCH_NUM
	* @returns		The previous fetch mode
	*/
	function SetFetchMode($mode)
	{	
		$old = $this->fetchMode;
		$this->fetchMode = $mode;
		
		if ($old === false) {
		global $ADODB_FETCH_MODE;
			return $ADODB_FETCH_MODE;
		}
		return $old;
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
		$rs = &$this->SelectLimit($sql, $count, $offset); /*  swap  */
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
		Improved method of initiating a transaction. Used together with CompleteTrans().
		Advantages include:
		
		a. StartTrans/CompleteTrans is nestable, unlike BeginTrans/CommitTrans/RollbackTrans.
		   Only the outermost block is treated as a transaction.<br>
		b. CompleteTrans auto-detects SQL errors, and will rollback on errors, commit otherwise.<br>
		c. All BeginTrans/CommitTrans/RollbackTrans inside a StartTrans/CompleteTrans block
		   are disabled, making it backward compatible.
	*/
	function StartTrans($errfn = 'ADODB_TransMonitor')
	{
		
		if ($this->transOff > 0) {
			$this->transOff += 1;
			return;
		}
		
		$this->_oldRaiseFn = $this->raiseErrorFn;
		$this->raiseErrorFn = $errfn;
		$this->_transOK = true;
		
		if ($this->debug && $this->transCnt > 0) ADOConnection::outp("Bad Transaction: StartTrans called within BeginTrans");
		$this->BeginTrans();
		$this->transOff = 1;
	}
	
	/**
		Used together with StartTrans() to end a transaction. Monitors connection
		for sql errors, and will commit or rollback as appropriate.
		
		@autoComplete if true, monitor sql errors and commit and rollback as appropriate, 
		and if set to false force rollback even if no SQL error detected.
		@returns true on commit, false on rollback.
	*/
	function CompleteTrans($autoComplete = true)
	{
		if ($this->transOff > 1) {
			$this->transOff -= 1;
			return true;
		}
		$this->raiseErrorFn = $this->_oldRaiseFn;
		
		$this->transOff = 0;
		if ($this->_transOK && $autoComplete) $this->CommitTrans();
		else $this->RollbackTrans();
		
		return $this->_transOK;
	}
	
	/*
		At the end of a StartTrans/CompleteTrans block, perform a rollback.
	*/
	function FailTrans()
	{
		if ($this->debug && $this->transOff == 0) {
			ADOConnection::outp("FailTrans outside StartTrans/CompleteTrans");
		}
		$this->_transOK = false;
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
		if ($this->fnExecute) {
			$fn = $this->fnExecute;
			$fn($this,$sql,$inputarr);
		}
		if (!$this->_bindInputArray && $inputarr) {
			$sqlarr = explode('?',$sql);
			$sql = '';
			$i = 0;
			foreach($inputarr as $v) {

				$sql .= $sqlarr[$i];
				/*  from Ron Baldwin <ron.baldwin@sourceprose.com> */
				/*  Only quote string types	 */
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
				ADOConnection::outp( "Input Array does not match ?: ".htmlspecialchars($sql));
			$inputarr = false;
		}
		/*  debug version of query */
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
			
			/*  check if running from browser or command-line */
			$inBrowser = isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']);
			
			if ($inBrowser)
				ADOConnection::outp( "<hr />\n($this->databaseType): ".htmlspecialchars($sqlTxt)." &nbsp; <code>$ss</code>\n<hr />\n",false);
			else
				ADOConnection::outp(  "=----\n($this->databaseType): ".($sqlTxt)." \n-----\n",false);
			flush();
			
			$this->_queryID = $this->_query($sql,$inputarr,$arg3);

			/* 
				Alexios Fakios notes that ErrorMsg() must be called before ErrorNo() for mssql
				because ErrorNo() calls Execute('SELECT @ERROR'), causing recure
			*/
			if ($this->databaseType == 'mssql') { 
			/*  ErrorNo is a slow function call in mssql, and not reliable */
			/*  in PHP 4.0.6 */
				if($emsg = $this->ErrorMsg()) {
					$err = $this->ErrorNo();
					if ($err) {
						ADOConnection::outp($err.': '.$emsg);
						flush();
					}
				}
			} else 
				if (!$this->_queryID) {
					$e = $this->ErrorNo();
					$m = $this->ErrorMsg();
					ADOConnection::outp($e .': '. $m );
					flush();
				}
		} else {
			/*  non-debug version of query */
			
			$this->_queryID =@$this->_query($sql,$inputarr,$arg3);
			
		}
		/*  error handling if query fails */
		if ($this->_queryID === false) {
			$fn = $this->raiseErrorFn;
			if ($fn) {
				$fn($this->databaseType,'EXECUTE',$this->ErrorNo(),$this->ErrorMsg(),$sql,$inputarr,$this);
			}
			return false;
		} else if ($this->_queryID === true) {
		/*  return simplified empty recordset for inserts/updates/deletes with lower overhead */
			$rs = new ADORecordSet_empty();
			return $rs;
		}
		
		/*  return real recordset from select statement */
		$rsclass = "ADORecordSet_".$this->databaseType;
		$rs = new $rsclass($this->_queryID,$this->fetchMode); /*  &new not supported by older PHP versions */
		$rs->connection = &$this; /*  Pablo suggestion */
		$rs->Init();
		if (is_array($sql)) $rs->sql = $sql[0];
		else $rs->sql = $sql;
		
		if ($rs->_numOfRows <= 0) {
		global $ADODB_COUNTRECS;
		
			if ($ADODB_COUNTRECS) {
				if (!$rs->EOF){ 
					$rs = &$this->_rs2rs($rs,-1,-1,!is_array($sql));
					$rs->_queryID = $this->_queryID;
				} else
					$rs->_numOfRows = 0;
			}
		}
		return $rs;
	}

	function CreateSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		return $this->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
	}
	
	function DropSequence($seqname)
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}

	/**
	 * Generates a sequence id and stores it in $this->genID;
	 * GenID is only available if $this->hasGenID = true;
	 *
	 * @param seqname		name of sequence to use
	 * @param startID		if sequence does not exist, start at this ID
	 * @return		0 if not supported, otherwise a sequence id
	 */

	function GenID($seqname='adodbseq',$startID=1)
	{
		if (!$this->hasGenID) {
			return 0; /*  formerly returns false pre 1.60 */
		}
		
		$getnext = sprintf($this->_genIDSQL,$seqname);
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			$createseq = $this->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
			$rs = $this->Execute($getnext);
		}
		if ($rs && !$rs->EOF) $this->genID = reset($rs->fields);
		else $this->genID = 0; /*  false */
	
		if ($rs) $rs->Close();

		return $this->genID;
	}	

	/**
	 * @return  the last inserted ID. Not all databases support this.
	 */ 
		function Insert_ID()
		{
				if ($this->hasInsertID) return $this->_insertid();
				if ($this->debug) ADOConnection::outp( '<p>Insert_ID error</p>');
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
				  
		  if ($this->debug) ADOConnection::outp( '<p>Affected_Rows error</p>',false);
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
	
	function MetaError($err=false)
	{
		include_once(ADODB_DIR."/adodb-error.inc.php");
		if ($err === false) $err = $this->ErrorNo();
		return adodb_error($this->dataProvider,$this->databaseType,$err);
	}
	
	function MetaErrorMsg($errno)
	{
		include_once(ADODB_DIR."/adodb-error.inc.php");
		return adodb_errormsg($errno);
	}
	
	/**
	 * @returns an array with the primary key columns in it.
	 */
	function MetaPrimaryKeys($table, $owner=false)
	{
	/*  owner not used in base class - see oci8 */
		$p = array();
		$objs = $this->MetaColumns($table);
		if ($objs) {
			foreach($objs as $v) {
				if (!empty($v->primary_key))
					$p[] = $v->name;
			}
		}
		if (sizeof($p)) return $p;
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
	* @param [nrows]		is the number of rows to get
	* @param [inputarr]	array of bind variables
	* @param [arg3]		is a private parameter only used by jlim
	* @param [secs2cache]		is a private parameter only used by jlim
	* @return		the recordset ($rs->databaseType == 'array')
 	*/
	function &SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$arg3=false,$secs2cache=0)
	{
		if ($this->hasTop && $nrows > 0) {
		/*  suggested by Reinhard Balling. Access requires top after distinct  */
		 /*  Informix requires first before distinct - F Riosa */
			$ismssql = (strpos($this->databaseType,'mssql') !== false);
			if ($ismssql) $isaccess = false;
			else $isaccess = (strpos($this->databaseType,'access') !== false);
			
			if ($offset <= 0) {
				
					/*  access includes ties in result */
					if ($isaccess) {
						$sql = preg_replace(
						'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop.' '.$nrows.' ',$sql);

						if ($secs2cache>0) return $this->CacheExecute($secs2cache, $sql,$inputarr,$arg3);
						else return $this->Execute($sql,$inputarr,$arg3);
					} else if ($ismssql){
						$sql = preg_replace(
						'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop.' '.$nrows.' ',$sql);
					} else {
						$sql = preg_replace(
						'/(^\s*select\s)/i','\\1 '.$this->hasTop.' '.$nrows.' ',$sql);
					}
			} else {
				$nn = $nrows + $offset;
				if ($isaccess || $ismssql) {
					$sql = preg_replace(
					'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop.' '.$nn.' ',$sql);
				} else {
					$sql = preg_replace(
					'/(^\s*select\s)/i','\\1 '.$this->hasTop.' '.$nn.' ',$sql);
				}
			}
		}
		
		/*  if $offset>0, we want to skip rows, and $ADODB_COUNTRECS is set, we buffer  rows */
		/*  0 to offset-1 which will be discarded anyway. So we disable $ADODB_COUNTRECS. */
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
		/* print_r($rs); */
		return $rs;
	}
	
	
	/**
	* Convert database recordset to an array recordset
	* input recordset's cursor should be at beginning, and
	* old $rs will be closed.
	*
	* @param rs			the recordset to copy
	* @param [nrows]  	number of rows to retrieve (optional)
	* @param [offset] 	offset by number of rows (optional)
	* @return 			the new recordset
	*/
	function &_rs2rs(&$rs,$nrows=-1,$offset=-1,$close=true)
	{
		if (! $rs) return false;
		
		$dbtype = $rs->databaseType;
		if (!$dbtype) {
			$rs = &$rs;  /*  required to prevent crashing in 4.2.1, but does not happen in 4.3.1 -- why ? */
			return $rs;
		}
		if (($dbtype == 'array' || $dbtype == 'csv') && $nrows == -1 && $offset == -1) {
			$rs->MoveFirst();
			$rs = &$rs; /*  required to prevent crashing in 4.2.1, but does not happen in 4.3.1-- why ? */
			return $rs;
		}
		
		for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++) {
			$flds[] = $rs->FetchField($i);
		}
		$arr = $rs->GetArrayLimit($nrows,$offset);
		/* print_r($arr); */
		if ($close) $rs->Close();
		
		$arrayClass = $this->arrayClass;
		
		$rs2 = new $arrayClass();
		$rs2->connection = &$this;
		$rs2->sql = $rs->sql;
		$rs2->dataProvider = $this->dataProvider;
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
	global $ADODB_COUNTRECS;
		$crecs = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = false;
		
		$ret = false;
		$rs = &$this->Execute($sql,$inputarr);
		if ($rs) {		
			if (!$rs->EOF) $ret = reset($rs->fields);
			$rs->Close();
		} 
		$ADODB_COUNTRECS = $crecs;
		return $ret;
	}
	
	function CacheGetOne($secs2cache,$sql=false,$inputarr=false)
	{
		$ret = false;
		$rs = &$this->CacheExecute($secs2cache,$sql,$inputarr);
		if ($rs) {		
			if (!$rs->EOF) $ret = reset($rs->fields);
			$rs->Close();
		} 
		
		return $ret;
	}
	
	function GetCol($sql, $inputarr = false, $trim = false)
	{
	  	$rv = false;
	  	$rs = &$this->Execute($sql, $inputarr);
	  	if ($rs) {
	   		if ($trim) {
				while (!$rs->EOF) {
					$rv[] = trim(reset($rs->fields));
					$rs->MoveNext();
		   		}
			} else {
				while (!$rs->EOF) {
					$rv[] = reset($rs->fields);
					$rs->MoveNext();
		   		}
			}
	   		$rs->Close();
	  	}
	  	return $rv;
	}
	
	function CacheGetCol($secs, $sql, $inputarr = false,$trim=false)
	{
	  	$rv = false;
	  	$rs = &$this->CacheExecute($secs, $sql, $inputarr);
	  	if ($rs) {
			if ($trim) {
				while (!$rs->EOF) {
					$rv[] = trim(reset($rs->fields));
					$rs->MoveNext();
		   		}
			} else {
				while (!$rs->EOF) {
					$rv[] = reset($rs->fields);
					$rs->MoveNext();
		   		}
			}
	   		$rs->Close();
	  	}
	  	return $rv;
	}
 
	/*
		Calculate the offset of a date for a particular database and generate
			appropriate SQL. Useful for calculating future/past dates and storing
			in a database.
			
		If dayFraction=1.5 means 1.5 days from now, 1.0/24 for 1 hour.
	*/
	function OffsetDate($dayFraction,$date=false)
	{		
		if (!$date) $date = $this->sysDate;
		return  '('.$date.'+'.$dayFraction.')';
	}
	
	
	/**
	* Return all rows. Compat with PEAR DB
	*
	* @param sql			SQL statement
	* @param [inputarr]		input bind array
	*/
	function GetAll($sql,$inputarr=false)
	{
	global $ADODB_COUNTRECS;
		
		$savec = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = false;
		$rs = $this->Execute($sql,$inputarr);
		$ADODB_COUNTRECS = $savec;
		
		if (!$rs) 
			if (defined('ADODB_PEAR')) return ADODB_PEAR_Error();
			else return false;
		$arr = $rs->GetArray();
		$rs->Close();
		return $arr;
	}
	
	function CacheGetAll($secs2cache,$sql=false,$inputarr=false)
	{
	global $ADODB_COUNTRECS;
		
		$savec = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = false;
		$rs = $this->CacheExecute($secs2cache,$sql,$inputarr);
		$ADODB_COUNTRECS = $savec;
		
		if (!$rs) 
			if (defined('ADODB_PEAR')) return ADODB_PEAR_Error();
			else return false;
		
		$arr = $rs->GetArray();
		$rs->Close();
		return $arr;
	}
	
	
	
	/**
	* Return one row of sql statement. Recordset is disposed for you.
	*
	* @param sql			SQL statement
	* @param [inputarr]		input bind array
	*/
	function GetRow($sql,$inputarr=false)
	{
	global $ADODB_COUNTRECS;
		$crecs = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = false;
		
		$rs = $this->Execute($sql,$inputarr);
		
		$ADODB_COUNTRECS = $crecs;
		if ($rs) {
			$arr = false;
			if (!$rs->EOF) $arr = $rs->fields;
			$rs->Close();
			return $arr;
		}
		
		return false;
	}
	
	function CacheGetRow($secs2cache,$sql=false,$inputarr=false)
	{
		$rs = $this->CacheExecute($secs2cache,$sql,$inputarr);
		if ($rs) {
			$arr = false;
			if (!$rs->EOF) $arr = $rs->fields;
			$rs->Close();
			return $arr;
		}
		return false;
	}
	
	/**
	* Insert or replace a single record. Note: this is not the same as MySQL's replace. 
	*  ADOdb's Replace() uses update-insert semantics, not insert-delete-duplicates of MySQL.
	*
	* $this->Replace('products', array('prodname' =>"'Nails'","price" => 3.99), 'prodname');
	*
	* $table		table name
	* $fieldArray	associative array of data (you must quote strings yourself).
	* $keyCol		the primary key field name or if compound key, array of field names
	* autoQuote		set to true to use a hueristic to quote strings. Works with nulls and numbers
	*					but does not work with dates nor SQL functions.
	* has_autoinc	the primary key is an auto-inc field, so skip in insert.
	*
	* Currently blob replace not supported
	*
	* returns 0 = fail, 1 = update, 2 = insert 
	*/
	
	function Replace($table, $fieldArray, $keyCol, $autoQuote=false, $has_autoinc=false)
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
			if (in_array($k,$keyCol)) continue; /*  skip UPDATE if is key */
			
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
			if ($rs) {
				if ($this->poorAffectedRows) {
				/*
				 The Select count(*) wipes out any errors that the update would have returned. 
				http://phplens.com/lens/lensforum/msgs.php?id=5696
				*/
					if ($this->ErrorNo()<>0) return 0;
					
				# affected_rows == 0 if update field values identical to old values
				# for mysql - which is silly. 
			
					$cnt = $this->GetOne("select count(*) from $table where $where");
					if ($cnt > 0) return 1; /*  record already exists */
				} else
					 if (($this->Affected_Rows()>0)) return 1;
			}
				
		}
	/* 	print "<p>Error=".$this->ErrorNo().'<p>'; */
		$first = true;
		foreach($fieldArray as $k => $v) {
			if ($has_autoinc && in_array($k,$keyCol)) continue; /*  skip autoinc col */
			
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
									  /*  sql,	nrows, offset,inputarr,arg3 */
			return $this->SelectLimit($secs2cache,$sql,$nrows,$offset,$inputarr,$this->cacheSecs);
		} else {
			if ($sql === false) ADOConnection::outp( "Warning: \$sql missing from CacheSelectLimit()");
			return $this->SelectLimit($sql,$nrows,$offset,$inputarr,$arg3,$secs2cache);
		}
	}
	
	/**
	* Flush cached recordsets that match a particular $sql statement. 
	* If $sql == false, then we purge all files in the cache.
 	*/
	function CacheFlush($sql=false,$inputarr=false)
	{
	global $ADODB_CACHE_DIR;
	
		if (strlen($ADODB_CACHE_DIR) > 1 && !$sql) {
			if (strpos(strtoupper(PHP_OS),'WIN') !== false) {
				$cmd = 'del /s '.str_replace('/','\\',$ADODB_CACHE_DIR).'\adodb_*.cache';
			} else {
				$cmd = 'rm -rf '.$ADODB_CACHE_DIR.'/??/adodb_*.cache'; 
				/*  old version 'rm -f `find '.$ADODB_CACHE_DIR.' -name adodb_*.cache`'; */
			}
			if ($this->debug) {
				ADOConnection::outp( "CacheFlush: $cmd<br><pre>\n", system($cmd),"</pre>");
			} else {
				exec($cmd);
			}
			return;
		} 
		$f = $this->_gencachename($sql.serialize($inputarr),false);
		adodb_write_file($f,''); /*  is adodb_write_file needed? */
		@unlink($f);
	}
	
	/**
	* Private function to generate filename for caching.
	* Filename is generated based on:
	*
	*  - sql statement
	*  - database type (oci8, ibase, ifx, etc)
	*  - database name
	*  - userid
	*
	* We create 256 sub-directories in the cache directory ($ADODB_CACHE_DIR). 
	* Assuming that we can have 50,000 files per directory with good performance, 
	* then we can scale to 12.8 million unique cached recordsets. Wow!
 	*/
	function _gencachename($sql,$createdir)
	{
	global $ADODB_CACHE_DIR;
		
		$m = md5($sql.$this->databaseType.$this->database.$this->user);
		$dir = $ADODB_CACHE_DIR.'/'.substr($m,0,2);
		if ($createdir && !file_exists($dir)) {
			$oldu = umask(0);
			if (!mkdir($dir,0771)) 
				if ($this->debug) ADOConnection::outp( "Unable to mkdir $dir for $sql");
			umask($oldu);
		}
		return $dir.'/adodb_'.$m.'.cache';
	}
	
	
	/**
	 * Execute SQL, caching recordsets.
	 *
	 * @param [secs2cache]	seconds to cache data, set to 0 to force query. 
	 *					  This is an optional parameter.
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
		
		$md5file = $this->_gencachename($sql.serialize($inputarr),true);
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
		/*  no cached rs found */
			if ($this->debug) {
				if (get_magic_quotes_runtime()) {
					ADOConnection::outp("Please disable magic_quotes_runtime - it corrupts cache files :(");
				}
				ADOConnection::outp( " $md5file cache failure: $err (see sql below)");
			}
			$rs = &$this->Execute($sql,$inputarr,$arg3);
			if ($rs) {
				$eof = $rs->EOF;
				$rs = &$this->_rs2rs($rs); /*  read entire recordset into memory immediately */
				$txt = _rs2serialize($rs,false,$sql); /*  serialize */
		
				if (!adodb_write_file($md5file,$txt,$this->debug)) {
					if ($fn = $this->raiseErrorFn) {
						$fn($this->databaseType,'CacheExecute',-32000,"Cache write error",$md5file,$sql,$this);
					}
					if ($this->debug) ADOConnection::outp( " Cache write error");
				}
				if ($rs->EOF && !$eof) {
					$rs->MoveFirst();
					/* $rs = &csv2rs($md5file,$err);		 */
					$rs->connection = &$this; /*  Pablo suggestion */
				}  
				
			} else
				@unlink($md5file);
		} else {
			if ($this->fnCacheExecute) {
				$fn = $this->fnCacheExecute;
				$fn($this, $secs2cache, $sql, $inputarr);
			}
		/*  ok, set cached object found */
			$rs->connection = &$this; /*  Pablo suggestion */
			if ($this->debug){ 
			global $HTTP_SERVER_VARS;
        			
				$inBrowser = isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']);
				$ttl = $rs->timeCreated + $secs2cache - time();
				$s = is_array($sql) ? $sql[0] : $sql;
				if ($inBrowser) $s = '<i>'.htmlspecialchars($s).'</i>';
				
				ADOConnection::outp( " $md5file reloaded, ttl=$ttl [ $s ]");
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
	 *	   that is run against a single table and sql should only 
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
	 *	   that is run against a single table.
  	 */
	function GetInsertSQL(&$rs, $arrFields,$magicq=false)
	{	
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		return _adodb_getinsertsql($this,$rs,$arrFields,$magicq);
	}
	

	/**
	* Update a blob column, given a where clause. There are more sophisticated
	* blob handling functions that we could have implemented, but all require
	* a very complex API. Instead we have chosen something that is extremely
	* simple to understand and use. 
	*
	* Note: $blobtype supports 'BLOB' and 'CLOB', default is BLOB of course.
	*
	* Usage to update a $blobvalue which has a primary key blob_id=1 into a 
	* field blobtable.blobcolumn:
	*
	*	UpdateBlob('blobtable', 'blobcolumn', $blobvalue, 'blob_id=1');
	*
	* Insert example:
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
	*	UpdateBlob('TABLE', 'COLUMN', '/path/to/file', 'ID=1');
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
	
	function BlobDecode($blob)
	{
		return $blob;
	}
	
	function BlobEncode($blob)
	{
		return $blob;
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
	 *  $meta	contains the desired type, which could be...
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
		return 255; /*  make it conservative if not defined */
	}
	
	
	/*
	* Maximum size of X field
	*/
	function TextMax()
	{
		return 4000; /*  make it conservative if not defined */
	}
	
	/**
	 * Close Connection
	 */
	function Close() 
	{
		return $this->_close();
		
		/*  "Simon Lee" <simon@mediaroad.com> reports that persistent connections need  */
		/*  to be closed too! */
		/* if ($this->_isPersistentConnection != true) return $this->_close(); */
		/* else return true;	 */
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
			/*  complicated state saving by the need for backward compat */
			$save = $ADODB_FETCH_MODE; 
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM; 
			
			if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
			
			$rs = $this->Execute($this->metaTablesSQL);
			if (isset($savem)) $this->SetFetchMode($savem);
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
	 * @param table	table name to query
	 * @param upper	uppercase table name (required by some databases)
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */ 
	function MetaColumns($table,$upper=true) 
	{
	global $ADODB_FETCH_MODE;
	
		if (!empty($this->metaColumnsSQL)) {
			$save = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
			if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,($upper)?strtoupper($table):$table));
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			if ($rs === false) return false;

			$retarr = array();
			while (!$rs->EOF) { /* print_r($rs->fields); */
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
	 * @param table	table name to query
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
	 * param s	variable number of string parameters
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
			
		return adodb_date($this->fmtDate,$d);
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
			
		return adodb_date($this->fmtTimeStamp,$ts);
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
		/*  h-m-s-MM-DD-YY */
		return @adodb_mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
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
			"|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,4}))?|", 
			($v), $rr)) return false;
		if ($rr[1] <= TIMESTAMP_FIRST_YEAR && $rr[2]<= 1) return 0;
	
		/*  h-m-s-MM-DD-YY */
		if (!isset($rr[5])) return  adodb_mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
		return  @adodb_mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
	}
	
	/**
	 * Also in ADORecordSet.
	 *
	 * Format database date based on user defined format.
	 *
	 * @param v  	is the character date in YYYY-MM-DD format, returned by database
	 * @param fmt 	is the format to apply to it, using date()
	 *
	 * @return a date formated as user desires
	 */
	 
	function UserDate($v,$fmt='Y-m-d')
	{
		$tt = $this->UnixDate($v);
		/*  $tt == -1 if pre TIMESTAMP_FIRST_YEAR */
		if (($tt === false || $tt == -1) && $v != false) return $v;
		else if ($tt == 0) return $this->emptyDate;
		else if ($tt == -1) { /*  pre-TIMESTAMP_FIRST_YEAR */
		}
		
		return adodb_date($fmt,$tt);
	
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
		if (!$magic_quotes) {
		
			if ($this->replaceQuote[0] == '\\'){
				/*  only since php 4.0.5 */
				$s = adodb_str_replace(array('\\',"\0"),array('\\\\',"\\\0"),$s);
				/* $s = str_replace("\0","\\\0", str_replace('\\','\\\\',$s)); */
			}
			return  "'".str_replace("'",$this->replaceQuote,$s)."'";
		}
		
		/*  undo magic quotes for " */
		$s = str_replace('\\"','"',$s);
		
		if ($this->replaceQuote == "\\'")  /*  ' already quoted, no need to change anything */
			return "'$s'";
		else {/*  change \' to '' for sybase/mssql */
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
	function &CachePageExecute($secs2cache, $sql, $nrows, $page,$inputarr=false, $arg3=false) 
	{
		/*switch($this->dataProvider) {
		case 'postgres':
		case 'mysql': 
			break;
		default: $secs2cache = 0; break;
		}*/
		return $this->PageExecute($sql,$nrows,$page,$inputarr,$arg3,$secs2cache);
	}

} /*  end class ADOConnection */

?>