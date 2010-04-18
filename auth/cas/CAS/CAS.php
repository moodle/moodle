<?php

// commented in 0.4.22-RC2 for Sylvain Derosiaux
// error_reporting(E_ALL ^ E_NOTICE);

//
// hack by Vangelis Haniotakis to handle the absence of $_SERVER['REQUEST_URI'] in IIS
//
if (!$_SERVER['REQUEST_URI']) {
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
}

//
// another one by Vangelis Haniotakis also to make phpCAS work with PHP5
//
if (version_compare(PHP_VERSION,'5','>=')) {
	require_once(dirname(__FILE__).'/CAS/domxml-php4-to-php5.php');
}

/**
 * @file CAS/CAS.php
 * Interface class of the phpCAS library
 *
 * @ingroup public
 */

// ########################################################################
//  CONSTANTS
// ########################################################################

// ------------------------------------------------------------------------
//  CAS VERSIONS
// ------------------------------------------------------------------------

/**
 * phpCAS version. accessible for the user by phpCAS::getVersion().
 */
define('PHPCAS_VERSION','1.1.0');

// ------------------------------------------------------------------------
//  CAS VERSIONS
// ------------------------------------------------------------------------
 /**
  * @addtogroup public
  * @{
  */

/**
 * CAS version 1.0
 */
define("CAS_VERSION_1_0",'1.0');
/*!
 * CAS version 2.0
 */
define("CAS_VERSION_2_0",'2.0');

// ------------------------------------------------------------------------
//  SAML defines
// ------------------------------------------------------------------------

/**
 * SAML protocol
 */
define("SAML_VERSION_1_1", 'S1');

/**
 * XML header for SAML POST
 */
define("SAML_XML_HEADER", '<?xml version="1.0" encoding="UTF-8"?>');

/**
 * SOAP envelope for SAML POST
 */
define ("SAML_SOAP_ENV", '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header/>');

/**
 * SOAP body for SAML POST
 */
define ("SAML_SOAP_BODY", '<SOAP-ENV:Body>');

/**
 * SAMLP request
 */
define ("SAMLP_REQUEST", '<samlp:Request xmlns:samlp="urn:oasis:names:tc:SAML:1.0:protocol"  MajorVersion="1" MinorVersion="1" RequestID="_192.168.16.51.1024506224022" IssueInstant="2002-06-19T17:03:44.022Z">');
define ("SAMLP_REQUEST_CLOSE", '</samlp:Request>');

/**
 * SAMLP artifact tag (for the ticket)
 */
define ("SAML_ASSERTION_ARTIFACT", '<samlp:AssertionArtifact>');

/**
 * SAMLP close
 */
define ("SAML_ASSERTION_ARTIFACT_CLOSE", '</samlp:AssertionArtifact>');

/**
 * SOAP body close
 */
define ("SAML_SOAP_BODY_CLOSE", '</SOAP-ENV:Body>');

/**
 * SOAP envelope close
 */
define ("SAML_SOAP_ENV_CLOSE", '</SOAP-ENV:Envelope>');

/**
 * SAML Attributes
 */
define("SAML_ATTRIBUTES", 'SAMLATTRIBS');



/** @} */
 /**
  * @addtogroup publicPGTStorage
  * @{
  */
// ------------------------------------------------------------------------
//  FILE PGT STORAGE
// ------------------------------------------------------------------------
 /**
  * Default path used when storing PGT's to file
  */
define("CAS_PGT_STORAGE_FILE_DEFAULT_PATH",'/tmp');
/**
 * phpCAS::setPGTStorageFile()'s 2nd parameter to write plain text files
 */
define("CAS_PGT_STORAGE_FILE_FORMAT_PLAIN",'plain');
/**
 * phpCAS::setPGTStorageFile()'s 2nd parameter to write xml files
 */
define("CAS_PGT_STORAGE_FILE_FORMAT_XML",'xml');
/**
 * Default format used when storing PGT's to file
 */
define("CAS_PGT_STORAGE_FILE_DEFAULT_FORMAT",CAS_PGT_STORAGE_FILE_FORMAT_PLAIN);
// ------------------------------------------------------------------------
//  DATABASE PGT STORAGE
// ------------------------------------------------------------------------
 /**
  * default database type when storing PGT's to database
  */
define("CAS_PGT_STORAGE_DB_DEFAULT_DATABASE_TYPE",'mysql');
/**
 * default host when storing PGT's to database
 */
define("CAS_PGT_STORAGE_DB_DEFAULT_HOSTNAME",'localhost');
/**
 * default port when storing PGT's to database
 */
define("CAS_PGT_STORAGE_DB_DEFAULT_PORT",'');
/**
 * default database when storing PGT's to database
 */
define("CAS_PGT_STORAGE_DB_DEFAULT_DATABASE",'phpCAS');
/**
 * default table when storing PGT's to database
 */
define("CAS_PGT_STORAGE_DB_DEFAULT_TABLE",'pgt');

/** @} */
// ------------------------------------------------------------------------
// SERVICE ACCESS ERRORS
// ------------------------------------------------------------------------
 /**
  * @addtogroup publicServices
  * @{
  */

/**
 * phpCAS::service() error code on success
 */
define("PHPCAS_SERVICE_OK",0);
/**
 * phpCAS::service() error code when the PT could not retrieve because
 * the CAS server did not respond.
 */
define("PHPCAS_SERVICE_PT_NO_SERVER_RESPONSE",1);
/**
 * phpCAS::service() error code when the PT could not retrieve because
 * the response of the CAS server was ill-formed.
 */
define("PHPCAS_SERVICE_PT_BAD_SERVER_RESPONSE",2);
/**
 * phpCAS::service() error code when the PT could not retrieve because
 * the CAS server did not want to.
 */
define("PHPCAS_SERVICE_PT_FAILURE",3);
/**
 * phpCAS::service() error code when the service was not available.
 */
define("PHPCAS_SERVICE_NOT AVAILABLE",4);

/** @} */
// ------------------------------------------------------------------------
//  LANGUAGES
// ------------------------------------------------------------------------
 /**
  * @addtogroup publicLang
  * @{
  */

define("PHPCAS_LANG_ENGLISH",    'english');
define("PHPCAS_LANG_FRENCH",     'french');
define("PHPCAS_LANG_GREEK",      'greek');
define("PHPCAS_LANG_GERMAN",     'german');
define("PHPCAS_LANG_JAPANESE",   'japanese');
define("PHPCAS_LANG_SPANISH",    'spanish');
define("PHPCAS_LANG_CATALAN",    'catalan');

/** @} */

/**
 * @addtogroup internalLang
 * @{
 */

/**
 * phpCAS default language (when phpCAS::setLang() is not used)
 */
define("PHPCAS_LANG_DEFAULT", PHPCAS_LANG_ENGLISH);

/** @} */
// ------------------------------------------------------------------------
//  DEBUG
// ------------------------------------------------------------------------
 /**
  * @addtogroup publicDebug
  * @{
  */

/**
 * The default directory for the debug file under Unix.
 */
define('DEFAULT_DEBUG_DIR','/tmp/');

/** @} */
// ------------------------------------------------------------------------
//  MISC
// ------------------------------------------------------------------------
 /**
  * @addtogroup internalMisc
  * @{
  */

/**
 * This global variable is used by the interface class phpCAS.
 *
 * @hideinitializer
 */
$GLOBALS['PHPCAS_CLIENT']  = null;

/**
 * This global variable is used to store where the initializer is called from 
 * (to print a comprehensive error in case of multiple calls).
 *
 * @hideinitializer
 */
$GLOBALS['PHPCAS_INIT_CALL'] = array('done' => FALSE,
	'file' => '?',
	'line' => -1,
	'method' => '?');

/**
 * This global variable is used to store where the method checking
 * the authentication is called from (to print comprehensive errors)
 *
 * @hideinitializer
 */
$GLOBALS['PHPCAS_AUTH_CHECK_CALL'] = array('done' => FALSE,
	'file' => '?',
	'line' => -1,
	'method' => '?',
	'result' => FALSE);

/**
 * This global variable is used to store phpCAS debug mode.
 *
 * @hideinitializer
 */
$GLOBALS['PHPCAS_DEBUG']  = array('filename' => FALSE,
	'indent' => 0,
	'unique_id' => '');

/** @} */

// ########################################################################
//  CLIENT CLASS
// ########################################################################

// include client class
include_once(dirname(__FILE__).'/CAS/client.php');

// ########################################################################
//  INTERFACE CLASS
// ########################################################################

/**
 * @class phpCAS
 * The phpCAS class is a simple container for the phpCAS library. It provides CAS
 * authentication for web applications written in PHP.
 *
 * @ingroup public
 * @author Pascal Aubry <pascal.aubry at univ-rennes1.fr>
 *
 * \internal All its methods access the same object ($PHPCAS_CLIENT, declared 
 * at the end of CAS/client.php).
 */



class phpCAS
{
	
	// ########################################################################
	//  INITIALIZATION
	// ########################################################################
	
	/**
	 * @addtogroup publicInit
	 * @{
	 */
	
	/**
	 * phpCAS client initializer.
	 * @note Only one of the phpCAS::client() and phpCAS::proxy functions should be
	 * called, only once, and before all other methods (except phpCAS::getVersion()
	 * and phpCAS::setDebug()).
	 *
	 * @param $server_version the version of the CAS server
	 * @param $server_hostname the hostname of the CAS server
	 * @param $server_port the port the CAS server is running on
	 * @param $server_uri the URI the CAS server is responding on
	 * @param $start_session Have phpCAS start PHP sessions (default true)
	 *
	 * @return a newly created CASClient object
	 */
	function client($server_version,
					$server_hostname,
					$server_port,
					$server_uri,
					$start_session = true)
		{
		global $PHPCAS_CLIENT, $PHPCAS_INIT_CALL;
		
		phpCAS::traceBegin();
		if ( is_object($PHPCAS_CLIENT) ) {
			phpCAS::error($PHPCAS_INIT_CALL['method'].'() has already been called (at '.$PHPCAS_INIT_CALL['file'].':'.$PHPCAS_INIT_CALL['line'].')');
		}
		if ( gettype($server_version) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_version (should be `string\')');
		}
		if ( gettype($server_hostname) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_hostname (should be `string\')');
		}
		if ( gettype($server_port) != 'integer' ) {
			phpCAS::error('type mismatched for parameter $server_port (should be `integer\')');
		}
		if ( gettype($server_uri) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_uri (should be `string\')');
		}
		
		// store where the initializer is called from
		$dbg = phpCAS::backtrace();
		$PHPCAS_INIT_CALL = array('done' => TRUE,
			'file' => $dbg[0]['file'],
			'line' => $dbg[0]['line'],
			'method' => __CLASS__.'::'.__FUNCTION__);
		
		// initialize the global object $PHPCAS_CLIENT
		$PHPCAS_CLIENT = new CASClient($server_version,FALSE/*proxy*/,$server_hostname,$server_port,$server_uri,$start_session);
		phpCAS::traceEnd();
		}
	
	/**
	 * phpCAS proxy initializer.
	 * @note Only one of the phpCAS::client() and phpCAS::proxy functions should be
	 * called, only once, and before all other methods (except phpCAS::getVersion()
	 * and phpCAS::setDebug()).
	 *
	 * @param $server_version the version of the CAS server
	 * @param $server_hostname the hostname of the CAS server
	 * @param $server_port the port the CAS server is running on
	 * @param $server_uri the URI the CAS server is responding on
	 * @param $start_session Have phpCAS start PHP sessions (default true)
	 *
	 * @return a newly created CASClient object
	 */
	function proxy($server_version,
				   $server_hostname,
				   $server_port,
				   $server_uri,
				   $start_session = true)
		{
		global $PHPCAS_CLIENT, $PHPCAS_INIT_CALL;
		
		phpCAS::traceBegin();
		if ( is_object($PHPCAS_CLIENT) ) {
			phpCAS::error($PHPCAS_INIT_CALL['method'].'() has already been called (at '.$PHPCAS_INIT_CALL['file'].':'.$PHPCAS_INIT_CALL['line'].')');
		}
		if ( gettype($server_version) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_version (should be `string\')');
		}
		if ( gettype($server_hostname) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_hostname (should be `string\')');
		}
		if ( gettype($server_port) != 'integer' ) {
			phpCAS::error('type mismatched for parameter $server_port (should be `integer\')');
		}
		if ( gettype($server_uri) != 'string' ) {
			phpCAS::error('type mismatched for parameter $server_uri (should be `string\')');
		}
		
		// store where the initialzer is called from
		$dbg = phpCAS::backtrace();
		$PHPCAS_INIT_CALL = array('done' => TRUE,
			'file' => $dbg[0]['file'],
			'line' => $dbg[0]['line'],
			'method' => __CLASS__.'::'.__FUNCTION__);
		
		// initialize the global object $PHPCAS_CLIENT
		$PHPCAS_CLIENT = new CASClient($server_version,TRUE/*proxy*/,$server_hostname,$server_port,$server_uri,$start_session);
		phpCAS::traceEnd();
		}
	
	/** @} */
	// ########################################################################
	//  DEBUGGING
	// ########################################################################
	
	/**
	 * @addtogroup publicDebug
	 * @{
	 */
	
	/**
	 * Set/unset debug mode
	 *
	 * @param $filename the name of the file used for logging, or FALSE to stop debugging.
	 */
	function setDebug($filename='')
		{
		global $PHPCAS_DEBUG;
		
		if ( $filename != FALSE && gettype($filename) != 'string' ) {
			phpCAS::error('type mismatched for parameter $dbg (should be FALSE or the name of the log file)');
		}
		
		if ( empty($filename) ) {
			if ( preg_match('/^Win.*/',getenv('OS')) ) {
				if ( isset($_ENV['TMP']) ) {
					$debugDir = $_ENV['TMP'].'/';
				} else if ( isset($_ENV['TEMP']) ) {
					$debugDir = $_ENV['TEMP'].'/';
				} else {
					$debugDir = '';
				}
			} else {
				$debugDir = DEFAULT_DEBUG_DIR;
			}
			$filename = $debugDir . 'phpCAS.log';
		}
		
		if ( empty($PHPCAS_DEBUG['unique_id']) ) {
			$PHPCAS_DEBUG['unique_id'] = substr(strtoupper(md5(uniqid(''))),0,4);
		}
		
		$PHPCAS_DEBUG['filename'] = $filename;
		
		phpCAS::trace('START ******************');
		}
	
	/** @} */
	/**
	 * @addtogroup internalDebug
	 * @{
	 */
	
	/**
	 * This method is a wrapper for debug_backtrace() that is not available 
	 * in all PHP versions (>= 4.3.0 only)
	 */
	function backtrace()
		{
		if ( function_exists('debug_backtrace') ) {
			return debug_backtrace();
		} else {
			// poor man's hack ... but it does work ...
			return array();
		}
		}
	
	/**
	 * Logs a string in debug mode.
	 *
	 * @param $str the string to write
	 *
	 * @private
	 */
	function log($str)
		{
		$indent_str = ".";
		global $PHPCAS_DEBUG;
		
		if ( $PHPCAS_DEBUG['filename'] ) {
			for ($i=0;$i<$PHPCAS_DEBUG['indent'];$i++) {
				$indent_str .= '|    ';
			}
			error_log($PHPCAS_DEBUG['unique_id'].' '.$indent_str.$str."\n",3,$PHPCAS_DEBUG['filename']);
		}
		
		}
	
	/**
	 * This method is used by interface methods to print an error and where the function
	 * was originally called from.
	 *
	 * @param $msg the message to print
	 *
	 * @private
	 */
	function error($msg)
		{
		$dbg = phpCAS::backtrace();
		$function = '?';
		$file = '?';
		$line = '?';
		if ( is_array($dbg) ) {
			for ( $i=1; $i<sizeof($dbg); $i++) {
				if ( is_array($dbg[$i]) ) {
					if ( $dbg[$i]['class'] == __CLASS__ ) {
						$function = $dbg[$i]['function'];
						$file = $dbg[$i]['file'];
						$line = $dbg[$i]['line'];
					}
				}
			}
		}
		echo "<br />\n<b>phpCAS error</b>: <font color=\"FF0000\"><b>".__CLASS__."::".$function.'(): '.htmlentities($msg)."</b></font> in <b>".$file."</b> on line <b>".$line."</b><br />\n";
		phpCAS::trace($msg);
		phpCAS::traceExit();
		exit();
		}
	
	/**
	 * This method is used to log something in debug mode.
	 */
	function trace($str)
		{
		$dbg = phpCAS::backtrace();
		phpCAS::log($str.' ['.basename($dbg[1]['file']).':'.$dbg[1]['line'].']');
		}
	
	/**
	 * This method is used to indicate the start of the execution of a function in debug mode.
	 */
	function traceBegin()
		{
		global $PHPCAS_DEBUG;
		
		$dbg = phpCAS::backtrace();
		$str = '=> ';
		if ( !empty($dbg[2]['class']) ) {
			$str .= $dbg[2]['class'].'::';
		}
		$str .= $dbg[2]['function'].'(';      
		if ( is_array($dbg[2]['args']) ) {
			foreach ($dbg[2]['args'] as $index => $arg) {
				if ( $index != 0 ) {
					$str .= ', ';
				}
				$str .= str_replace("\n","",var_export($arg,TRUE));
			}
		}
		$str .= ') ['.basename($dbg[2]['file']).':'.$dbg[2]['line'].']';
		phpCAS::log($str);
		$PHPCAS_DEBUG['indent'] ++;
		}
	
	/**
	 * This method is used to indicate the end of the execution of a function in debug mode.
	 *
	 * @param $res the result of the function
	 */
	function traceEnd($res='')
		{
		global $PHPCAS_DEBUG;
		
		$PHPCAS_DEBUG['indent'] --;
		$dbg = phpCAS::backtrace();
		$str = '';
		$str .= '<= '.str_replace("\n","",var_export($res,TRUE));
		phpCAS::log($str);
		}
	
	/**
	 * This method is used to indicate the end of the execution of the program
	 */
	function traceExit()
		{
		global $PHPCAS_DEBUG;
		
		phpCAS::log('exit()');
		while ( $PHPCAS_DEBUG['indent'] > 0 ) {
			phpCAS::log('-');
			$PHPCAS_DEBUG['indent'] --;
		}
		}
	
	/** @} */
	// ########################################################################
	//  INTERNATIONALIZATION
	// ########################################################################
	/**
	 * @addtogroup publicLang
	 * @{
	 */
	
	/**
	 * This method is used to set the language used by phpCAS. 
	 * @note Can be called only once.
	 *
	 * @param $lang a string representing the language.
	 *
	 * @sa PHPCAS_LANG_FRENCH, PHPCAS_LANG_ENGLISH
	 */
	function setLang($lang)
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( gettype($lang) != 'string' ) {
			phpCAS::error('type mismatched for parameter $lang (should be `string\')');
		}
		$PHPCAS_CLIENT->setLang($lang);
		}
	
	/** @} */
	// ########################################################################
	//  VERSION
	// ########################################################################
	/**
	 * @addtogroup public
	 * @{
	 */
	
	/**
	 * This method returns the phpCAS version.
	 *
	 * @return the phpCAS version.
	 */
	function getVersion()
		{
		return PHPCAS_VERSION;
		}
	
	/** @} */
	// ########################################################################
	//  HTML OUTPUT
	// ########################################################################
	/**
	 * @addtogroup publicOutput
	 * @{
	 */
	
	/**
	 * This method sets the HTML header used for all outputs.
	 *
	 * @param $header the HTML header.
	 */
	function setHTMLHeader($header)
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( gettype($header) != 'string' ) {
			phpCAS::error('type mismatched for parameter $header (should be `string\')');
		}
		$PHPCAS_CLIENT->setHTMLHeader($header);
		}
	
	/**
	 * This method sets the HTML footer used for all outputs.
	 *
	 * @param $footer the HTML footer.
	 */
	function setHTMLFooter($footer)
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( gettype($footer) != 'string' ) {
			phpCAS::error('type mismatched for parameter $footer (should be `string\')');
		}
		$PHPCAS_CLIENT->setHTMLFooter($footer);
		}
	
	/** @} */
	// ########################################################################
	//  PGT STORAGE
	// ########################################################################
	/**
	 * @addtogroup publicPGTStorage
	 * @{
	 */
	
	/**
	 * This method is used to tell phpCAS to store the response of the
	 * CAS server to PGT requests onto the filesystem. 
	 *
	 * @param $format the format used to store the PGT's (`plain' and `xml' allowed)
	 * @param $path the path where the PGT's should be stored
	 */
	function setPGTStorageFile($format='',
		$path='')
		{
		global $PHPCAS_CLIENT,$PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_CLIENT->isProxy() ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( $PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called before '.$PHPCAS_AUTH_CHECK_CALL['method'].'() (called at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].')');
		}
		if ( gettype($format) != 'string' ) {
			phpCAS::error('type mismatched for parameter $format (should be `string\')');
		}
		if ( gettype($path) != 'string' ) {
			phpCAS::error('type mismatched for parameter $format (should be `string\')');
		}
		$PHPCAS_CLIENT->setPGTStorageFile($format,$path);
		phpCAS::traceEnd();
		}
	
	/**
	 * This method is used to tell phpCAS to store the response of the
	 * CAS server to PGT requests into a database. 
	 * @note The connection to the database is done only when needed. 
	 * As a consequence, bad parameters are detected only when 
	 * initializing PGT storage, except in debug mode.
	 *
	 * @param $user the user to access the data with
	 * @param $password the user's password
	 * @param $database_type the type of the database hosting the data
	 * @param $hostname the server hosting the database
	 * @param $port the port the server is listening on
	 * @param $database the name of the database
	 * @param $table the name of the table storing the data
	 */
	function setPGTStorageDB($user,
							 $password,
							 $database_type='',
								 $hostname='',
									 $port=0,
										 $database='',
											 $table='')
		{
		global $PHPCAS_CLIENT,$PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_CLIENT->isProxy() ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( $PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called before '.$PHPCAS_AUTH_CHECK_CALL['method'].'() (called at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].')');
		}
		if ( gettype($user) != 'string' ) {
			phpCAS::error('type mismatched for parameter $user (should be `string\')');
		}
		if ( gettype($password) != 'string' ) {
			phpCAS::error('type mismatched for parameter $password (should be `string\')');
		}
		if ( gettype($database_type) != 'string' ) {
			phpCAS::error('type mismatched for parameter $database_type (should be `string\')');
		}
		if ( gettype($hostname) != 'string' ) {
			phpCAS::error('type mismatched for parameter $hostname (should be `string\')');
		}
		if ( gettype($port) != 'integer' ) {
			phpCAS::error('type mismatched for parameter $port (should be `integer\')');
		}
		if ( gettype($database) != 'string' ) {
			phpCAS::error('type mismatched for parameter $database (should be `string\')');
		}
		if ( gettype($table) != 'string' ) {
			phpCAS::error('type mismatched for parameter $table (should be `string\')');
		}
		$PHPCAS_CLIENT->setPGTStorageDB($user,$password,$database_type,$hostname,$port,$database,$table);
		phpCAS::traceEnd();
		}
	
	/** @} */
	// ########################################################################
	// ACCESS TO EXTERNAL SERVICES
	// ########################################################################
	/**
	 * @addtogroup publicServices
	 * @{
	 */
	
	/**
	 * This method is used to access an HTTP[S] service.
	 * 
	 * @param $url the service to access.
	 * @param $err_code an error code Possible values are PHPCAS_SERVICE_OK (on
	 * success), PHPCAS_SERVICE_PT_NO_SERVER_RESPONSE, PHPCAS_SERVICE_PT_BAD_SERVER_RESPONSE,
	 * PHPCAS_SERVICE_PT_FAILURE, PHPCAS_SERVICE_NOT AVAILABLE.
	 * @param $output the output of the service (also used to give an error
	 * message on failure).
	 *
	 * @return TRUE on success, FALSE otherwise (in this later case, $err_code
	 * gives the reason why it failed and $output contains an error message).
	 */
	function serviceWeb($url,&$err_code,&$output)
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_CLIENT->isProxy() ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called after the programmer is sure the user has been authenticated (by calling '.__CLASS__.'::checkAuthentication() or '.__CLASS__.'::forceAuthentication()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['result'] ) {
			phpCAS::error('authentication was checked (by '.$PHPCAS_AUTH_CHECK_CALL['method'].'() at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].') but the method returned FALSE');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		
		$res = $PHPCAS_CLIENT->serviceWeb($url,$err_code,$output);
		
		phpCAS::traceEnd($res);
		return $res;
		}
	
	/**
	 * This method is used to access an IMAP/POP3/NNTP service.
	 * 
	 * @param $url a string giving the URL of the service, including the mailing box
	 * for IMAP URLs, as accepted by imap_open().
	 * @param $service a string giving for CAS retrieve Proxy ticket
	 * @param $flags options given to imap_open().
	 * @param $err_code an error code Possible values are PHPCAS_SERVICE_OK (on
	 * success), PHPCAS_SERVICE_PT_NO_SERVER_RESPONSE, PHPCAS_SERVICE_PT_BAD_SERVER_RESPONSE,
	 * PHPCAS_SERVICE_PT_FAILURE, PHPCAS_SERVICE_NOT AVAILABLE.
	 * @param $err_msg an error message on failure
	 * @param $pt the Proxy Ticket (PT) retrieved from the CAS server to access the URL
	 * on success, FALSE on error).
	 *
	 * @return an IMAP stream on success, FALSE otherwise (in this later case, $err_code
	 * gives the reason why it failed and $err_msg contains an error message).
	 */
	function serviceMail($url,$service,$flags,&$err_code,&$err_msg,&$pt)
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_CLIENT->isProxy() ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called after the programmer is sure the user has been authenticated (by calling '.__CLASS__.'::checkAuthentication() or '.__CLASS__.'::forceAuthentication()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['result'] ) {
			phpCAS::error('authentication was checked (by '.$PHPCAS_AUTH_CHECK_CALL['method'].'() at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].') but the method returned FALSE');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		
		if ( gettype($flags) != 'integer' ) {
			phpCAS::error('type mismatched for parameter $flags (should be `integer\')');
		}
		
		$res = $PHPCAS_CLIENT->serviceMail($url,$service,$flags,$err_code,$err_msg,$pt);
		
		phpCAS::traceEnd($res);
		return $res;
		}
	
	/** @} */
	// ########################################################################
	//  AUTHENTICATION
	// ########################################################################
	/**
	 * @addtogroup publicAuth
	 * @{
	 */
	
	/**
	 * Set the times authentication will be cached before really accessing the CAS server in gateway mode: 
	 * - -1: check only once, and then never again (until you pree login)
	 * - 0: always check
	 * - n: check every "n" time
	 *
	 * @param $n an integer.
	 */
	function setCacheTimesForAuthRecheck($n)
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( gettype($n) != 'integer' ) {
			phpCAS::error('type mismatched for parameter $header (should be `string\')');
		}
		$PHPCAS_CLIENT->setCacheTimesForAuthRecheck($n);
		}
	
	/**
	 * This method is called to check if the user is authenticated (use the gateway feature).
	 * @return TRUE when the user is authenticated; otherwise FALSE.
	 */
	function checkAuthentication()
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		
		$auth = $PHPCAS_CLIENT->checkAuthentication();
		
		// store where the authentication has been checked and the result
		$dbg = phpCAS::backtrace();
		$PHPCAS_AUTH_CHECK_CALL = array('done' => TRUE,
			'file' => $dbg[0]['file'],
			'line' => $dbg[0]['line'],
			'method' => __CLASS__.'::'.__FUNCTION__,
			'result' => $auth );
		phpCAS::traceEnd($auth);
		return $auth; 
		}

	/**
	 * This method is called to force authentication if the user was not already 
	 * authenticated. If the user is not authenticated, halt by redirecting to 
	 * the CAS server.
	 */
	function forceAuthentication()
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		
		$auth = $PHPCAS_CLIENT->forceAuthentication();
		
		// store where the authentication has been checked and the result
		$dbg = phpCAS::backtrace();
		$PHPCAS_AUTH_CHECK_CALL = array('done' => TRUE,
			'file' => $dbg[0]['file'],
			'line' => $dbg[0]['line'],
			'method' => __CLASS__.'::'.__FUNCTION__,
			'result' => $auth );
		
		if ( !$auth ) {
			phpCAS::trace('user is not authenticated, redirecting to the CAS server');
			$PHPCAS_CLIENT->forceAuthentication();
		} else {
			phpCAS::trace('no need to authenticate (user `'.phpCAS::getUser().'\' is already authenticated)');
		}
		
		phpCAS::traceEnd();
		return $auth; 
		}
	
	/**
	 * This method is called to renew the authentication.
	 **/
	function renewAuthentication() {
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before'.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		
		// store where the authentication has been checked and the result
		$dbg = phpCAS::backtrace();
		$PHPCAS_AUTH_CHECK_CALL = array('done' => TRUE, 'file' => $dbg[0]['file'], 'line' => $dbg[0]['line'], 'method' => __CLASS__.'::'.__FUNCTION__, 'result' => $auth );
		
		$PHPCAS_CLIENT->renewAuthentication();
		phpCAS::traceEnd();
	}

	/**
	 * This method has been left from version 0.4.1 for compatibility reasons.
	 */
	function authenticate()
		{
		phpCAS::error('this method is deprecated. You should use '.__CLASS__.'::forceAuthentication() instead');
		}
	
	/**
	 * This method is called to check if the user is authenticated (previously or by
	 * tickets given in the URL).
	 *
	 * @return TRUE when the user is authenticated.
	 */
	function isAuthenticated()
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		
		// call the isAuthenticated method of the global $PHPCAS_CLIENT object
		$auth = $PHPCAS_CLIENT->isAuthenticated();
		
		// store where the authentication has been checked and the result
		$dbg = phpCAS::backtrace();
		$PHPCAS_AUTH_CHECK_CALL = array('done' => TRUE,
			'file' => $dbg[0]['file'],
			'line' => $dbg[0]['line'],
			'method' => __CLASS__.'::'.__FUNCTION__,
			'result' => $auth );
		phpCAS::traceEnd($auth);
		return $auth;
		}
	
	/**
	 * Checks whether authenticated based on $_SESSION. Useful to avoid
	 * server calls.
	 * @return true if authenticated, false otherwise.
	 * @since 0.4.22 by Brendan Arnold
	 */
	function isSessionAuthenticated ()
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		return($PHPCAS_CLIENT->isSessionAuthenticated());
		}
	
	/**
	 * This method returns the CAS user's login name.
	 * @warning should not be called only after phpCAS::forceAuthentication()
	 * or phpCAS::checkAuthentication().
	 *
	 * @return the login name of the authenticated user
	 */
	function getUser()
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::forceAuthentication() or '.__CLASS__.'::isAuthenticated()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['result'] ) {
			phpCAS::error('authentication was checked (by '.$PHPCAS_AUTH_CHECK_CALL['method'].'() at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].') but the method returned FALSE');
		}
		return $PHPCAS_CLIENT->getUser();
		}
	
	/**
	 * This method returns the CAS user's login name.
	 * @warning should not be called only after phpCAS::forceAuthentication()
	 * or phpCAS::checkAuthentication().
	 *
	 * @return the login name of the authenticated user
	 */
	function getAttributes()
		{
		global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['done'] ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::forceAuthentication() or '.__CLASS__.'::isAuthenticated()');
		}
		if ( !$PHPCAS_AUTH_CHECK_CALL['result'] ) {
			phpCAS::error('authentication was checked (by '.$PHPCAS_AUTH_CHECK_CALL['method'].'() at '.$PHPCAS_AUTH_CHECK_CALL['file'].':'.$PHPCAS_AUTH_CHECK_CALL['line'].') but the method returned FALSE');
		}
		return $PHPCAS_CLIENT->getAttributes();
		}
    /**
     * Handle logout requests.
     */
    function handleLogoutRequests($check_client=true, $allowed_clients=false)
        {
            global $PHPCAS_CLIENT;
            if ( !is_object($PHPCAS_CLIENT) ) {
                phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
            }
            return($PHPCAS_CLIENT->handleLogoutRequests($check_client, $allowed_clients));
        }
   
	/**
	 * This method returns the URL to be used to login.
	 * or phpCAS::isAuthenticated().
	 *
	 * @return the login name of the authenticated user
	 */
	function getServerLoginURL()
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		return $PHPCAS_CLIENT->getServerLoginURL();
		}
	
	/**
	 * Set the login URL of the CAS server.
	 * @param $url the login URL
	 * @since 0.4.21 by Wyman Chan
	 */
	function setServerLoginURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after
				'.__CLASS__.'::client()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be
			`string\')');
		}
		$PHPCAS_CLIENT->setServerLoginURL($url);
		phpCAS::traceEnd();
		}
		
		
	/**
	 * Set the serviceValidate URL of the CAS server.
	 * Used only in CAS 1.0 validations
	 * @param $url the serviceValidate URL
	 * @since 1.1.0 by Joachim Fritschi
	 */
	function setServerServiceValidateURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after
				'.__CLASS__.'::client()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be
			`string\')');
		}
		$PHPCAS_CLIENT->setServerServiceValidateURL($url);
		phpCAS::traceEnd();
		}
		
		
	 /**
	 * Set the proxyValidate URL of the CAS server.
	 * Used for all CAS 2.0 validations
	 * @param $url the proxyValidate URL
	 * @since 1.1.0 by Joachim Fritschi
	 */
	function setServerProxyValidateURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after
				'.__CLASS__.'::client()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be
			`string\')');
		}
		$PHPCAS_CLIENT->setServerProxyValidateURL($url);
		phpCAS::traceEnd();
		}
		
     /**
	 * Set the samlValidate URL of the CAS server.
	 * @param $url the samlValidate URL
	 * @since 1.1.0 by Joachim Fritschi
	 */
	function setServerSamlValidateURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after
				'.__CLASS__.'::client()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be
			`string\')');
		}
		$PHPCAS_CLIENT->setServerSamlValidateURL($url);
		phpCAS::traceEnd();
		}			
	
	/**
	 * This method returns the URL to be used to login.
	 * or phpCAS::isAuthenticated().
	 *
	 * @return the login name of the authenticated user
	 */
	function getServerLogoutURL()
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should not be called before '.__CLASS__.'::client() or '.__CLASS__.'::proxy()');
		}
		return $PHPCAS_CLIENT->getServerLogoutURL();
		}
	
	/**
	 * Set the logout URL of the CAS server.
	 * @param $url the logout URL
	 * @since 0.4.21 by Wyman Chan
	 */
	function setServerLogoutURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after
				'.__CLASS__.'::client()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be
			`string\')');
		}
		$PHPCAS_CLIENT->setServerLogoutURL($url);
		phpCAS::traceEnd();
		}
	
	/**
	 * This method is used to logout from CAS.
	 * @params $params an array that contains the optional url and service parameters that will be passed to the CAS server
	 * @public
	 */
	function logout($params = "") {
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if (!is_object($PHPCAS_CLIENT)) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}
		$parsedParams = array();
		if ($params != "") {
			if (is_string($params)) {
				phpCAS::error('method `phpCAS::logout($url)\' is now deprecated, use `phpCAS::logoutWithUrl($url)\' instead');
			}
			if (!is_array($params)) {
				phpCAS::error('type mismatched for parameter $params (should be `array\')');
			}
			foreach ($params as $key => $value) {
				if ($key != "service" && $key != "url") {
					phpCAS::error('only `url\' and `service\' parameters are allowed for method `phpCAS::logout($params)\'');
				}
				$parsedParams[$key] = $value;
			}
		}
		$PHPCAS_CLIENT->logout($parsedParams);
		// never reached
		phpCAS::traceEnd();
	}
	
	/**
	 * This method is used to logout from CAS. Halts by redirecting to the CAS server.
	 * @param $service a URL that will be transmitted to the CAS server
	 */
	function logoutWithRedirectService($service) {
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}
		if (!is_string($service)) {
			phpCAS::error('type mismatched for parameter $service (should be `string\')');
		}
		$PHPCAS_CLIENT->logout(array("service" => $service));
		// never reached
		phpCAS::traceEnd();
	}
	
	/**
	 * This method is used to logout from CAS. Halts by redirecting to the CAS server.
	 * @param $url a URL that will be transmitted to the CAS server
	 */
	function logoutWithUrl($url) {
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}
		if (!is_string($url)) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		$PHPCAS_CLIENT->logout(array("url" => $url));
		// never reached
		phpCAS::traceEnd();
	}
	
	/**
	 * This method is used to logout from CAS. Halts by redirecting to the CAS server.
	 * @param $service a URL that will be transmitted to the CAS server
	 * @param $url a URL that will be transmitted to the CAS server
	 */
	function logoutWithRedirectServiceAndUrl($service, $url) {
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}
		if (!is_string($service)) {
			phpCAS::error('type mismatched for parameter $service (should be `string\')');
		}
		if (!is_string($url)) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		$PHPCAS_CLIENT->logout(array("service" => $service, "url" => $url));
		// never reached
		phpCAS::traceEnd();
	}
	
	/**
	 * Set the fixed URL that will be used by the CAS server to transmit the PGT.
	 * When this method is not called, a phpCAS script uses its own URL for the callback.
	 *
	 * @param $url the URL
	 */
	function setFixedCallbackURL($url='')
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( !$PHPCAS_CLIENT->isProxy() ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		$PHPCAS_CLIENT->setCallbackURL($url);
		phpCAS::traceEnd();
		}
	
	/**
	 * Set the fixed URL that will be set as the CAS service parameter. When this
	 * method is not called, a phpCAS script uses its own URL.
	 *
	 * @param $url the URL
	 */
	function setFixedServiceURL($url)
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}  
		if ( gettype($url) != 'string' ) {
			phpCAS::error('type mismatched for parameter $url (should be `string\')');
		}
		$PHPCAS_CLIENT->setURL($url);
		phpCAS::traceEnd();
		}
	
	/**
	 * Get the URL that is set as the CAS service parameter.
	 */
	function getServiceURL()
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}  
		return($PHPCAS_CLIENT->getURL());
		}
	
	/**
	 * Retrieve a Proxy Ticket from the CAS server.
	 */
	function retrievePT($target_service,&$err_code,&$err_msg)
		{
		global $PHPCAS_CLIENT;
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::proxy()');
		}  
		if ( gettype($target_service) != 'string' ) {
			phpCAS::error('type mismatched for parameter $target_service(should be `string\')');
		}
		return($PHPCAS_CLIENT->retrievePT($target_service,$err_code,$err_msg));
		}
	
	/**
	 * Set the certificate of the CAS server.
	 *
	 * @param $cert the PEM certificate
	 */
	function setCasServerCert($cert)
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}  
		if ( gettype($cert) != 'string' ) {
			phpCAS::error('type mismatched for parameter $cert (should be `string\')');
		}
		$PHPCAS_CLIENT->setCasServerCert($cert);
		phpCAS::traceEnd();
		}
	
	/**
	 * Set the certificate of the CAS server CA.
	 *
	 * @param $cert the CA certificate
	 */
	function setCasServerCACert($cert)
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}  
		if ( gettype($cert) != 'string' ) {
			phpCAS::error('type mismatched for parameter $cert (should be `string\')');
		}
		$PHPCAS_CLIENT->setCasServerCACert($cert);
		phpCAS::traceEnd();
		}
	
	/**
	 * Set no SSL validation for the CAS server.
	 */
	function setNoCasServerValidation()
		{
		global $PHPCAS_CLIENT;
		phpCAS::traceBegin();
		if ( !is_object($PHPCAS_CLIENT) ) {
			phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		}  
		$PHPCAS_CLIENT->setNoCasServerValidation();
		phpCAS::traceEnd();
		}
	
	/** @} */
	
  /**
   * Change CURL options.
   * CURL is used to connect through HTTPS to CAS server
   * @param $key the option key
   * @param $value the value to set
   */
   function setExtraCurlOption($key, $value)
		{
		  global $PHPCAS_CLIENT;
		  phpCAS::traceBegin();
		  if ( !is_object($PHPCAS_CLIENT) ) {
		  	phpCAS::error('this method should only be called after '.__CLASS__.'::client() or'.__CLASS__.'::proxy()');
		  }  
		  $PHPCAS_CLIENT->setExtraCurlOption($key, $value);
		  phpCAS::traceEnd();
		}

}

// ########################################################################
// DOCUMENTATION
// ########################################################################

// ########################################################################
//  MAIN PAGE

/**
 * @mainpage
 *
 * The following pages only show the source documentation.
 *
 */

// ########################################################################
//  MODULES DEFINITION

/** @defgroup public User interface */

/** @defgroup publicInit Initialization
 *  @ingroup public */

/** @defgroup publicAuth Authentication
 *  @ingroup public */

/** @defgroup publicServices Access to external services
 *  @ingroup public */

/** @defgroup publicConfig Configuration
 *  @ingroup public */

/** @defgroup publicLang Internationalization
 *  @ingroup publicConfig */

/** @defgroup publicOutput HTML output
 *  @ingroup publicConfig */

/** @defgroup publicPGTStorage PGT storage
 *  @ingroup publicConfig */

/** @defgroup publicDebug Debugging
 *  @ingroup public */


/** @defgroup internal Implementation */

/** @defgroup internalAuthentication Authentication
 *  @ingroup internal */

/** @defgroup internalBasic CAS Basic client features (CAS 1.0, Service Tickets)
 *  @ingroup internal */

/** @defgroup internalProxy CAS Proxy features (CAS 2.0, Proxy Granting Tickets)
 *  @ingroup internal */

/** @defgroup internalPGTStorage PGT storage
 *  @ingroup internalProxy */

/** @defgroup internalPGTStorageDB PGT storage in a database
 *  @ingroup internalPGTStorage */

/** @defgroup internalPGTStorageFile PGT storage on the filesystem
 *  @ingroup internalPGTStorage */

/** @defgroup internalCallback Callback from the CAS server
 *  @ingroup internalProxy */

/** @defgroup internalProxied CAS proxied client features (CAS 2.0, Proxy Tickets)
 *  @ingroup internal */

/** @defgroup internalConfig Configuration
 *  @ingroup internal */

/** @defgroup internalOutput HTML output
 *  @ingroup internalConfig */

/** @defgroup internalLang Internationalization
 *  @ingroup internalConfig
 *
 * To add a new language:
 * - 1. define a new constant PHPCAS_LANG_XXXXXX in CAS/CAS.php
 * - 2. copy any file from CAS/languages to CAS/languages/XXXXXX.php
 * - 3. Make the translations
 */

/** @defgroup internalDebug Debugging
 *  @ingroup internal */

/** @defgroup internalMisc Miscellaneous
 *  @ingroup internal */

// ########################################################################
//  EXAMPLES

/**
 * @example example_simple.php
 */
 /**
  * @example example_proxy.php
  */
  /**
   * @example example_proxy2.php
   */
   /**
    * @example example_lang.php
    */
    /**
     * @example example_html.php
     */
     /**
      * @example example_file.php
      */
      /**
       * @example example_db.php
       */
       /**
        * @example example_service.php
        */
        /**
         * @example example_session_proxy.php
         */
         /**
          * @example example_session_service.php
          */
          /**
           * @example example_gateway.php
           */



?>
