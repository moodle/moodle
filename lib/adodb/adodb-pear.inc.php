<?php
/** 
 * @version V4.98 13 Feb 2008 (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
 * Whenever there is any discrepancy between the two licenses, 
 * the BSD license will take precedence. 
 *
 * Set tabs to 4 for best viewing.
 * 
 * PEAR DB Emulation Layer for ADODB.
 *
 * The following code is modelled on PEAR DB code by Stig Bakken <ssb@fast.no>								   |
 * and Tomas V.V.Cox <cox@idecnet.com>.	Portions (c)1997-2002 The PHP Group.
 */

 /*
 We support:
 
 DB_Common
 ---------
 	query - returns PEAR_Error on error
	limitQuery - return PEAR_Error on error
	prepare - does not return PEAR_Error on error
	execute - does not return PEAR_Error on error
	setFetchMode - supports ASSOC and ORDERED
	errorNative
	quote
	nextID
	disconnect
	
	getOne
	getAssoc
	getRow
	getCol
	getAll
	
 DB_Result
 ---------
 	numRows - returns -1 if not supported
	numCols
	fetchInto - does not support passing of fetchmode
	fetchRows - does not support passing of fetchmode
	free
 */
 
define('ADODB_PEAR',dirname(__FILE__));
include_once "PEAR.php";
include_once ADODB_PEAR."/adodb-errorpear.inc.php";
include_once ADODB_PEAR."/adodb.inc.php";

if (!defined('DB_OK')) {
define("DB_OK",	1);
define("DB_ERROR",-1);

// autoExecute constants
define('DB_AUTOQUERY_INSERT', 1);
define('DB_AUTOQUERY_UPDATE', 2);

/**
 * This is a special constant that tells DB the user hasn't specified
 * any particular get mode, so the default should be used.
 */

define('DB_FETCHMODE_DEFAULT', 0);

/**
 * Column data indexed by numbers, ordered from 0 and up
 */

define('DB_FETCHMODE_ORDERED', 1);

/**
 * Column data indexed by column names
 */

define('DB_FETCHMODE_ASSOC', 2);

/* for compatibility */

define('DB_GETMODE_ORDERED', DB_FETCHMODE_ORDERED);
define('DB_GETMODE_ASSOC',   DB_FETCHMODE_ASSOC);

/**
 * these are constants for the tableInfo-function
 * they are bitwised or'ed. so if there are more constants to be defined
 * in the future, adjust DB_TABLEINFO_FULL accordingly
 */

define('DB_TABLEINFO_ORDER', 1);
define('DB_TABLEINFO_ORDERTABLE', 2);
define('DB_TABLEINFO_FULL', 3);
}

/**
 * The main "DB" class is simply a container class with some static
 * methods for creating DB objects as well as some utility functions
 * common to all parts of DB.
 *
 */

class DB
{
	/**
	 * Create a new DB object for the specified database type
	 *
	 * @param $type string database type, for example "mysql"
	 *
	 * @return object a newly created DB object, or a DB error code on
	 * error
	 */

	function &factory($type)
	{
		include_once(ADODB_DIR."/drivers/adodb-$type.inc.php");
		$obj = &NewADOConnection($type);
		if (!is_object($obj)) $obj =& new PEAR_Error('Unknown Database Driver: '.$dsninfo['phptype'],-1);
		return $obj;
	}

	/**
	 * Create a new DB object and connect to the specified database
	 *
	 * @param $dsn mixed "data source name", see the DB::parseDSN
	 * method for a description of the dsn format.  Can also be
	 * specified as an array of the format returned by DB::parseDSN.
	 *
	 * @param $options mixed if boolean (or scalar), tells whether
	 * this connection should be persistent (for backends that support
	 * this).  This parameter can also be an array of options, see
	 * DB_common::setOption for more information on connection
	 * options.
	 *
	 * @return object a newly created DB connection object, or a DB
	 * error object on error
	 *
	 * @see DB::parseDSN
	 * @see DB::isError
	 */
	function &connect($dsn, $options = false)
	{
		if (is_array($dsn)) {
			$dsninfo = $dsn;
		} else {
			$dsninfo = DB::parseDSN($dsn);
		}
		switch ($dsninfo["phptype"]) {
			case 'pgsql': 	$type = 'postgres7'; break;
			case 'ifx':		$type = 'informix9'; break;
			default: 		$type = $dsninfo["phptype"]; break;
		}

		if (is_array($options) && isset($options["debug"]) &&
			$options["debug"] >= 2) {
			// expose php errors with sufficient debug level
			 @include_once("adodb-$type.inc.php");
		} else {
			 @include_once("adodb-$type.inc.php");
		}

		@$obj =& NewADOConnection($type);
		if (!is_object($obj)) {
			$obj =& new PEAR_Error('Unknown Database Driver: '.$dsninfo['phptype'],-1);
			return $obj;
		}
		if (is_array($options)) {
			foreach($options as $k => $v) {
				switch(strtolower($k)) {
				case 'persist':
				case 'persistent': 	$persist = $v; break;
				#ibase
				case 'dialect': 	$obj->dialect = $v; break;
				case 'charset':		$obj->charset = $v; break;
				case 'buffers':		$obj->buffers = $v; break;
				#ado
				case 'charpage':	$obj->charPage = $v; break;
				#mysql
				case 'clientflags': $obj->clientFlags = $v; break;
				}
			}
		} else {
		   	$persist = false;
		}

		if (isset($dsninfo['socket'])) $dsninfo['hostspec'] .= ':'.$dsninfo['socket'];
		else if (isset($dsninfo['port'])) $dsninfo['hostspec'] .= ':'.$dsninfo['port'];
		
		if($persist) $ok = $obj->PConnect($dsninfo['hostspec'], $dsninfo['username'],$dsninfo['password'],$dsninfo['database']);
		else  $ok = $obj->Connect($dsninfo['hostspec'], $dsninfo['username'],$dsninfo['password'],$dsninfo['database']);
		
		if (!$ok) $obj = ADODB_PEAR_Error();
		return $obj;
	}

	/**
	 * Return the DB API version
	 *
	 * @return int the DB API version number
	 */
	function apiVersion()
	{
		return 2;
	}

	/**
	 * Tell whether a result code from a DB method is an error
	 *
	 * @param $value int result code
	 *
	 * @return bool whether $value is an error
	 */
	function isError($value)
	{
		if (!is_object($value)) return false;
		$class = get_class($value);
		return $class == 'pear_error' || is_subclass_of($value, 'pear_error') || 
				$class == 'db_error' || is_subclass_of($value, 'db_error');
	}


	/**
	 * Tell whether a result code from a DB method is a warning.
	 * Warnings differ from errors in that they are generated by DB,
	 * and are not fatal.
	 *
	 * @param $value mixed result value
	 *
	 * @return bool whether $value is a warning
	 */
	function isWarning($value)
	{
		return false;
		/*
		return is_object($value) &&
			(get_class( $value ) == "db_warning" ||
			 is_subclass_of($value, "db_warning"));*/
	}

	/**
	 * Parse a data source name
	 *
	 * @param $dsn string Data Source Name to be parsed
	 *
	 * @return array an associative array with the following keys:
	 *
	 *  phptype: Database backend used in PHP (mysql, odbc etc.)
	 *  dbsyntax: Database used with regards to SQL syntax etc.
	 *  protocol: Communication protocol to use (tcp, unix etc.)
	 *  hostspec: Host specification (hostname[:port])
	 *  database: Database to use on the DBMS server
	 *  username: User name for login
	 *  password: Password for login
	 *
	 * The format of the supplied DSN is in its fullest form:
	 *
	 *  phptype(dbsyntax)://username:password@protocol+hostspec/database
	 *
	 * Most variations are allowed:
	 *
	 *  phptype://username:password@protocol+hostspec:110//usr/db_file.db
	 *  phptype://username:password@hostspec/database_name
	 *  phptype://username:password@hostspec
	 *  phptype://username@hostspec
	 *  phptype://hostspec/database
	 *  phptype://hostspec
	 *  phptype(dbsyntax)
	 *  phptype
	 *
	 * @author Tomas V.V.Cox <cox@idecnet.com>
	 */
	function parseDSN($dsn)
	{
		if (is_array($dsn)) {
			return $dsn;
		}

		$parsed = array(
			'phptype'  => false,
			'dbsyntax' => false,
			'protocol' => false,
			'hostspec' => false,
			'database' => false,
			'username' => false,
			'password' => false
		);

		// Find phptype and dbsyntax
		if (($pos = strpos($dsn, '://')) !== false) {
			$str = substr($dsn, 0, $pos);
			$dsn = substr($dsn, $pos + 3);
		} else {
			$str = $dsn;
			$dsn = NULL;
		}

		// Get phptype and dbsyntax
		// $str => phptype(dbsyntax)
		if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
			$parsed['phptype'] = $arr[1];
			$parsed['dbsyntax'] = (empty($arr[2])) ? $arr[1] : $arr[2];
		} else {
			$parsed['phptype'] = $str;
			$parsed['dbsyntax'] = $str;
		}

		if (empty($dsn)) {
			return $parsed;
		}

		// Get (if found): username and password
		// $dsn => username:password@protocol+hostspec/database
		if (($at = strpos($dsn,'@')) !== false) {
			$str = substr($dsn, 0, $at);
			$dsn = substr($dsn, $at + 1);
			if (($pos = strpos($str, ':')) !== false) {
				$parsed['username'] = urldecode(substr($str, 0, $pos));
				$parsed['password'] = urldecode(substr($str, $pos + 1));
			} else {
				$parsed['username'] = urldecode($str);
			}
		}

		// Find protocol and hostspec
		// $dsn => protocol+hostspec/database
		if (($pos=strpos($dsn, '/')) !== false) {
			$str = substr($dsn, 0, $pos);
			$dsn = substr($dsn, $pos + 1);
		} else {
			$str = $dsn;
			$dsn = NULL;
		}

		// Get protocol + hostspec
		// $str => protocol+hostspec
		if (($pos=strpos($str, '+')) !== false) {
			$parsed['protocol'] = substr($str, 0, $pos);
			$parsed['hostspec'] = urldecode(substr($str, $pos + 1));
		} else {
			$parsed['hostspec'] = urldecode($str);
		}

		// Get dabase if any
		// $dsn => database
		if (!empty($dsn)) {
			$parsed['database'] = $dsn;
		}

		return $parsed;
	}

	/**
	 * Load a PHP database extension if it is not loaded already.
	 *
	 * @access public
	 *
	 * @param $name the base name of the extension (without the .so or
	 * .dll suffix)
	 *
	 * @return bool true if the extension was already or successfully
	 * loaded, false if it could not be loaded
	 */
	function assertExtension($name)
	{
		if (!extension_loaded($name)) {
			$dlext = (strncmp(PHP_OS,'WIN',3) === 0) ? '.dll' : '.so';
			@dl($name . $dlext);
		}
		if (!extension_loaded($name)) {
			return false;
		}
		return true;
	}
}

?>