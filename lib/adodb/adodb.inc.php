<?php 

/*
 * Set tabs to 4 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 * 
 * This is the main include file for ADOdb.
 * Database specific drivers are stored in the adodb/drivers/adodb-*.inc.php
 *
 * The ADOdb files are formatted so that doxygen can be used to generate documentation.
 * Doxygen is a documentation generation tool and can be downloaded from http://doxygen.org/
 */

/**
	\mainpage 	
	
	 @version V3.40 7 April 2003 (c) 2000-2003 John Lim (jlim\@natsoft.com.my). All rights reserved.

	Released under both BSD license and Lesser GPL library license. 
 	Whenever there is any discrepancy between the two licenses, 
 	the BSD license will take precedence. 
	
	PHP's database access functions are not standardised. This creates a need for a database 
	class library to hide the differences between the different database API's (encapsulate 
	the differences) so we can easily switch databases.

	We currently support MySQL, Oracle, Microsoft SQL Server, Sybase, Sybase SQL Anywhere,
	Informix, PostgreSQL, FrontBase, Interbase (Firebird and Borland variants), Foxpro, Access,
	 ADO and ODBC. We have had successful reports of connecting to Progress and DB2 via ODBC. 
	 We hope more people will contribute drivers to support other databases.
	 
	 Latest Download at http://php.weblogs.com/adodb<br>
	 Manual is at http://php.weblogs.com/adodb_manual
	  
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
	
	/*
	Controls ADODB_FETCH_ASSOC field-name case. Default is 2, use native case-names.
	This currently works only with mssql, odbc, oci8po and ibase derived drivers.
	
 		0 = assoc lowercase field names. $rs->fields['orderid']
		1 = assoc uppercase field names. $rs->fields['ORDERID']
		2 = use native-case field names. $rs->fields['OrderID']
	*/
	if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE',2);
	
	// allow [ ] @ ` and . in table names
	define('ADODB_TABLE_REGEX','([]0-9a-z_\`\.\@\[-]*)');
	
	
	if (!defined('ADODB_PREFETCH_ROWS')) define('ADODB_PREFETCH_ROWS',10);

	/** 
	 * Set ADODB_DIR to the directory where this file resides...
	 * This constant was formerly called $ADODB_RootPath
	 */
	if (!defined('ADODB_DIR')) define('ADODB_DIR',dirname(__FILE__));
	
	define('TIMESTAMP_FIRST_YEAR',100);
	
	//==============================================================================================	
	// GLOBAL VARIABLES
	//==============================================================================================	

	GLOBAL 
		$ADODB_vers, 		// database version
		$ADODB_Database, 	// last database driver used
		$ADODB_COUNTRECS,	// count number of records returned - slows down query
		$ADODB_CACHE_DIR,	// directory to cache recordsets
		$ADODB_EXTENSION,   // ADODB extension installed
		$ADODB_COMPAT_PATCH, // If $ADODB_COUNTRECS and this is true, $rs->fields is available on EOF
	 	$ADODB_FETCH_MODE;	// DEFAULT, NUM, ASSOC or BOTH. Default follows native driver default...
	
	//==============================================================================================	
	// GLOBAL SETUP
	//==============================================================================================	
	
	if (strnatcmp(PHP_VERSION,'4.3.0')>=0) {
		define('ADODB_PHPVER',0x4300);
	} else if (strnatcmp(PHP_VERSION,'4.2.0')>=0) {
		define('ADODB_PHPVER',0x4200);
	} else if (strnatcmp(PHP_VERSION,'4.0.5')>=0) {
		define('ADODB_PHPVER',0x4050);
	} else {
		define('ADODB_PHPVER',0x4000);
	}
	$ADODB_EXTENSION = defined('ADODB_EXTENSION');
	//if (extension_loaded('dbx')) define('ADODB_DBX',1);
	
	/**
	 	Accepts $src and $dest arrays, replacing string $data
	*/
	function ADODB_str_replace($src, $dest, $data)
	{
		if (ADODB_PHPVER >= 0x4050) return str_replace($src,$dest,$data);
		
		$s = reset($src);
		$d = reset($dest);
		while ($s !== false) {
			$data = str_replace($s,$d,$data);
			$s = next($src);
			$d = next($dest);
		}
		return $data;
	}
	
	function ADODB_Setup()
	{
	GLOBAL 
		$ADODB_vers, 		// database version
		$ADODB_Database, 	// last database driver used
		$ADODB_COUNTRECS,	// count number of records returned - slows down query
		$ADODB_CACHE_DIR,	// directory to cache recordsets
	 	$ADODB_FETCH_MODE;
		
		$ADODB_FETCH_MODE = ADODB_FETCH_DEFAULT;
		
		if (!isset($ADODB_CACHE_DIR)) {
			$ADODB_CACHE_DIR = '/tmp';
		} else {
			// do not accept url based paths, eg. http:/ or ftp:/
			if (strpos($ADODB_CACHE_DIR,'://') !== false) 
				die("Illegal path http:// or ftp://");
		}
		
			
		// Initialize random number generator for randomizing cache flushes
		srand(((double)microtime())*1000000);
		
		/**
		 * Name of last database driver loaded into memory. Set by ADOLoadCode().
		 */
		$ADODB_Database = '';
		
		/**
		 * ADODB version as a string.
		 */
		$ADODB_vers = 'V3.40 7 April 2003 (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved. Released BSD & LGPL.';
	
		/**
		 * Determines whether recordset->RecordCount() is used. 
		 * Set to false for highest performance -- RecordCount() will always return -1 then
		 * for databases that provide "virtual" recordcounts...
		 */
		$ADODB_COUNTRECS = true; 
	}
	
	
	//==============================================================================================	
	// CHANGE NOTHING BELOW UNLESS YOU ARE CODING
	//==============================================================================================	
	
	ADODB_Setup();

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
	
	include_once(ADODB_DIR.'/adodb-connection.inc.php');
	
	
	
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
		var $databaseType = false;
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
	// DATE AND TIME FUNCTIONS
	//==============================================================================================	
	include_once(ADODB_DIR.'/adodb-time.inc.php');
	
	//==============================================================================================	
	// CLASS ADORecordSet
	//==============================================================================================	
	include_once(ADODB_DIR.'/adodb-recordset.inc.php');
	
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
		var $databaseType = 'array';

		var $_array; 	// holds the 2-dimensional data array
		var $_types;	// the array of types of each column (C B I L M)
		var $_colnames;	// names of each column in array
		var $_skiprow1;	// skip 1st row because it holds column names
		var $_fieldarr; // holds array of field objects
		var $canSeek = true;
		var $affectedrows = false;
		var $insertid = false;
		var $sql = '';
		var $compat = false;
		/**
		 * Constructor
		 *
		 */
		function ADORecordSet_array($fakeid=1)
		{
		global $ADODB_FETCH_MODE,$ADODB_COMPAT_FETCH;
		
			// fetch() on EOF does not delete $this->fields
			$this->compat = !empty($ADODB_COMPAT_FETCH);
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
		function InitArray($array,$typearr,$colnames=false)
		{
			$this->_array = $array;
			$this->_types = $typearr;	
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
		function InitArrayFields($array,$fieldarr)
		{
			$this->_array = $array;
			$this->_skiprow1= false;
			if ($fieldarr) {
				$this->_fieldobjects = $fieldarr;
			} 
			$this->Init();
		}
		
		function GetArray($nRows=-1)
		{
			if ($nRows == -1 && $this->_currentRow <= 0 && !$this->_skiprow1) {
				return $this->_array;
			} else {
				return ADORecordSet::GetArray($nRows);
			}
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
			if (sizeof($this->_array) && $row < $this->_numOfRows) {
				$this->fields = $this->_array[$row];
				return true;
			}
			return false;
		}
		
		function MoveNext() 
		{
			if (!$this->EOF) {		
				$this->_currentRow++;
				
				$pos = $this->_currentRow;
				if ($this->_skiprow1) $pos += 1;
				
				if ($this->_numOfRows <= $pos) {
					if (!$this->compat) $this->fields = false;
				} else {
					$this->fields = $this->_array[$pos];
					return true;
				}		
				$this->EOF = true;
			}
			
			return false;
		}	
	
		function _fetch()
		{
			$pos = $this->_currentRow;
			if ($this->_skiprow1) $pos += 1;
			
			if ($this->_numOfRows <= $pos) {
				if (!$this->compat) $this->fields = false;
				return false;
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
			case 'postgres':
			case 'pgsql': $ADODB_Database = 'postgres7'; break;
		}
		// Karsten Kraus <Karsten.Kraus@web.de> 
		return @include_once(ADODB_DIR."/drivers/adodb-".$ADODB_Database.".inc.php");		
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
		
		$rez = true;
		if ($db) {
			if ($ADODB_Database != $db) ADOLoadCode($db);
		} else { 
			if (!empty($ADODB_Database)) {
				ADOLoadCode($ADODB_Database);
			} else {
				 $rez = false;
			}
		}
		
		$errorfn = (defined('ADODB_ERROR_HANDLER')) ? ADODB_ERROR_HANDLER : false;
		if (!$rez) {
			 if ($errorfn) {
				// raise an error
				$errorfn('ADONewConnection', 'ADONewConnection', -998,
						 "could not load the database driver for '$db",
						 $dbtype);
			} else
				 ADOConnection::outp( "<p>ADONewConnection: Unable to load database driver '$db'</p>",false);
				
			return false;
		}
		
		$cls = 'ADODB_'.$ADODB_Database;
		$obj = new $cls();
		if ($errorfn) {
			$obj->raiseErrorFn = $errorfn;
		}
		return $obj;
	}
	
	function &NewDataDictionary(&$conn)
	{
		$provider = $conn->dataProvider;
		if ($provider !== 'native' && $provider != 'odbc' && $provider != 'ado') 
			$drivername = $conn->dataProvider;
		else {
			$drivername = $conn->databaseType;
			if (substr($drivername,0,5) == 'odbc_') $drivername = substr($drivername,5);
			else if (substr($drivername,0,4) == 'ado_') $drivername = substr($drivername,4);
			else if ($drivername == 'oracle') $drivername = 'oci8';
		}
		include_once(ADODB_DIR.'/adodb-lib.inc.php');
		include_once(ADODB_DIR.'/adodb-datadict.inc.php');
		$path = ADODB_DIR."/datadict/datadict-$drivername.inc.php";

		if (!file_exists($path)) {
			ADOConnection::outp("Database driver '$path' not available");
			return false;
		}
		include_once($path);
		$class = "ADODB2_$drivername";
		$dict = new $class();
		$dict->connection = &$conn;
		$dict->upperName = strtoupper($drivername);
		if (is_resource($conn->_connectionID))
			$dict->serverInfo = $conn->ServerInfo();
		
		return $dict;
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
				if ($debug) ADOConnection::outp( " Rename $tmpname ".($ok? 'ok' : 'failed'));
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
			if ($debug)ADOConnection::outp( " Failed acquiring lock for $filename<br>\n");
			$ok = false;
		}
	
		return $ok;
	}

	
	function adodb_backtrace($print=true)
	{
		$s = '';
		if (PHPVERSION() >= 4.3) {
		
			$MAXSTRLEN = 64;
		
			$s = '<pre align=left>';
			$traceArr = debug_backtrace();
			array_shift($traceArr);
			$tabs = sizeof($traceArr)-1;
			foreach ($traceArr as $arr) {
				for ($i=0; $i < $tabs; $i++) $s .= ' &nbsp; ';
				$tabs -= 1;
				$s .= '<font face="Courier New,Courier">';
				if (isset($arr['class'])) $s .= $arr['class'].'.';
				foreach($arr['args'] as $v) {
					if (is_null($v)) $args[] = 'null';
					else if (is_array($v)) $args[] = 'Array['.sizeof($v).']';
					else if (is_object($v)) $args[] = 'Object:'.get_class($v);
					else if (is_bool($v)) $args[] = $v ? 'true' : 'false';
					else { 
						$v = (string) @$v;
						$str = htmlspecialchars(substr($v,0,$MAXSTRLEN));
						if (strlen($v) > $MAXSTRLEN) $str .= '...';
						$args[] = $str;
					}
				}
				
				$s .= $arr['function'].'('.implode(', ',$args).')';
				$s .= sprintf("</font><font color=#808080 size=-1> # line %4d, file: <a href=\"file:/%s\">%s</a></font>",
					$arr['line'],$arr['file'],$arr['file']);
				$s .= "\n";
			}	
			$s .= '</pre>';
			if ($print) print $s;
		}
		return $s;
	}
	
} // defined
?>