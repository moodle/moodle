<?php
/**
 * Error Handler with PEAR support.
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

include_once('PEAR.php');

if (!defined('ADODB_ERROR_HANDLER')) define('ADODB_ERROR_HANDLER','ADODB_Error_PEAR');

/*
* Enabled the following if you want to terminate scripts when an error occurs
*/
//PEAR::setErrorHandling (PEAR_ERROR_DIE);

/*
* Name of the PEAR_Error derived class to call.
*/
if (!defined('ADODB_PEAR_ERROR_CLASS')) define('ADODB_PEAR_ERROR_CLASS','PEAR_Error');

/*
* Store the last PEAR_Error object here
*/
global $ADODB_Last_PEAR_Error; $ADODB_Last_PEAR_Error = false;

  /**
* Error Handler with PEAR support. This will be called with the following params
*
* @param $dbms		the RDBMS you are connecting to
* @param $fn		the name of the calling function (in uppercase)
* @param $errno		the native error number from the database
* @param $errmsg	the native error msg from the database
* @param $p1		$fn specific parameter - see below
* @param $P2		$fn specific parameter - see below
	*/
function ADODB_Error_PEAR($dbms, $fn, $errno, $errmsg, $p1=false, $p2=false)
{
global $ADODB_Last_PEAR_Error;

	if (error_reporting() == 0) return; // obey @ protocol
	switch($fn) {
	case 'EXECUTE':
		$sql = $p1;
		$inputparams = $p2;

		$s = "$dbms error: [$errno: $errmsg] in $fn(\"$sql\")";
		break;

	case 'PCONNECT':
	case 'CONNECT':
		$host = $p1;
		$database = $p2;

		$s = "$dbms error: [$errno: $errmsg] in $fn('$host', ?, ?, '$database')";
		break;

	default:
		$s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)";
		break;
	}

	$class = ADODB_PEAR_ERROR_CLASS;
	$ADODB_Last_PEAR_Error = new $class($s, $errno,
		$GLOBALS['_PEAR_default_error_mode'],
		$GLOBALS['_PEAR_default_error_options'],
		$errmsg);

	//print "<p>!$s</p>";
}

/**
* Returns last PEAR_Error object. This error might be for an error that
* occurred several sql statements ago.
*/
function ADODB_PEAR_Error()
{
global $ADODB_Last_PEAR_Error;

	return $ADODB_Last_PEAR_Error;
}
