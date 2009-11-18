<?php

/**
 * @version V5.10 10 Nov 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
 * Latest version is available at http://php.weblogs.com
 *
 * Exception-handling code using PHP5 exceptions (try-catch-throw).
 */


if (!defined('ADODB_ERROR_HANDLER_TYPE')) define('ADODB_ERROR_HANDLER_TYPE',E_USER_ERROR); 
define('ADODB_ERROR_HANDLER','adodb_throw');

class ADODB_Exception extends Exception {
var $dbms;
var $fn;
var $sql = '';
var $params = '';
var $host = '';
var $database = '';
	
	function __construct($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection)
	{
		switch($fn) {
		case 'EXECUTE':
			$this->sql = $p1;
			$this->params = $p2;
			$s = "$dbms error: [$errno: $errmsg] in $fn(\"$p1\")\n";
			break;
	
		case 'PCONNECT':
		case 'CONNECT':
			$user = $thisConnection->user;
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, '$user', '****', $p2)\n";
			break;
		default:
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)\n";
			break;
		}
	
		$this->dbms = $dbms;
		if ($thisConnection) {
			$this->host = $thisConnection->host;
			$this->database = $thisConnection->database;
		}
		$this->fn = $fn;
		$this->msg = $errmsg;
				
		if (!is_numeric($errno)) $errno = -1;
		parent::__construct($s,$errno);
	}
}

/**
* Default Error Handler. This will be called with the following params
*
* @param $dbms		the RDBMS you are connecting to
* @param $fn		the name of the calling function (in uppercase)
* @param $errno		the native error number from the database
* @param $errmsg	the native error msg from the database
* @param $p1		$fn specific parameter - see below
* @param $P2		$fn specific parameter - see below
*/

function adodb_throw($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection)
{
global $ADODB_EXCEPTION;
	
	if (error_reporting() == 0) return; // obey @ protocol
	if (is_string($ADODB_EXCEPTION)) $errfn = $ADODB_EXCEPTION;
	else $errfn = 'ADODB_EXCEPTION';
	throw new $errfn($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection);
}


?>