<?php
/**
 * Error handling using Exceptions.
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

if (!defined('ADODB_ERROR_HANDLER_TYPE')) define('ADODB_ERROR_HANDLER_TYPE',E_USER_ERROR);
define('ADODB_ERROR_HANDLER','adodb_throw');

class ADODB_Exception extends Exception {
var $dbms;
var $fn;
var $sql = '';
var $params = '';
var $host = '';
var $database = '';

	/** @var string A message text. */
	var $msg = '';

	function __construct($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection)
	{
		switch($fn) {
		case 'EXECUTE':
			$this->sql = is_array($p1) ? $p1[0] : $p1;
			$this->params = $p2;
			$s = "$dbms error: [$errno: $errmsg] in $fn(\"$this->sql\")";
			break;

		case 'PCONNECT':
		case 'CONNECT':
			$user = $thisConnection->user;
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, '$user', '****', $p2)";
			break;
		default:
			//Prevent PHP warning if $p1 or $p2 are arrays.
			$p1 = ( is_array($p1) ) ? 'Array' : $p1;
			$p2 = ( is_array($p2) ) ? 'Array' : $p2;
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)";
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
* Default Error Handler.
*
* @param string $dbms    the RDBMS you are connecting to
* @param string $fn      the name of the calling function (in uppercase)
* @param int    $errno   the native error number from the database
* @param string $errmsg  the native error msg from the database
* @param mixed  $p1      $fn specific parameter - see below
* @param mixed  $p2      $fn specific parameter - see below
*/

function adodb_throw($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection)
{
global $ADODB_EXCEPTION;

	if (error_reporting() == 0) return; // obey @ protocol
	if (is_string($ADODB_EXCEPTION)) $errfn = $ADODB_EXCEPTION;
	else $errfn = 'ADODB_EXCEPTION';
	throw new $errfn($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection);
}
