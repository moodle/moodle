<?php

/** 
 * @version V4.66 28 Sept 2005 (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
 */
 
/* Documentation on usage is at http://php.weblogs.com/adodb_csv
 *
 * Legal query string parameters:
 * 
 * sql = holds sql string
 * nrows = number of rows to return 
 * offset = skip offset rows of data
 * fetch = $ADODB_FETCH_MODE
 * 
 * example:
 *
 * http://localhost/php/server.php?select+*+from+table&nrows=10&offset=2
 */


/* 
 * Define the IP address you want to accept requests from 
 * as a security measure. If blank we accept anyone promisciously!
 */
$ACCEPTIP = '';

/*
 * Connection parameters
 */
$driver = 'mysql';
$host = 'localhost'; // DSN for odbc
$uid = 'root';
$pwd = '';
$database = 'test';

/*============================ DO NOT MODIFY BELOW HERE =================================*/
// $sep must match csv2rs() in adodb.inc.php
$sep = ' :::: ';

include('./adodb.inc.php');
include_once(ADODB_DIR.'/adodb-csvlib.inc.php');

function err($s)
{
	die('**** '.$s.' ');
}

// undo stupid magic quotes
function undomq(&$m) 
{
	if (get_magic_quotes_gpc()) {
		// undo the damage
		$m = str_replace('\\\\','\\',$m);
		$m = str_replace('\"','"',$m);
		$m = str_replace('\\\'','\'',$m);
		
	}
	return $m;
}

///////////////////////////////////////// DEFINITIONS


$remote = $_SERVER["REMOTE_ADDR"]; 
 
if (empty($_REQUEST['sql'])) err('No SQL');

if (!empty($ACCEPTIP))
 if ($remote != '127.0.0.1' && $remote != $ACCEPTIP) 
 	err("Unauthorised client: '$remote'");


$conn = &ADONewConnection($driver);

if (!$conn->Connect($host,$uid,$pwd,$database)) err($conn->ErrorNo(). $sep . $conn->ErrorMsg());
$sql = undomq($_REQUEST['sql']);

if (isset($_REQUEST['fetch']))
	$ADODB_FETCH_MODE = $_REQUEST['fetch'];
	
if (isset($_REQUEST['nrows'])) {
	$nrows = $_REQUEST['nrows'];
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : -1;
	$rs = $conn->SelectLimit($sql,$nrows,$offset);
} else 
	$rs = $conn->Execute($sql);
if ($rs){ 
	//$rs->timeToLive = 1;
	echo _rs2serialize($rs,$conn,$sql);
	$rs->Close();
} else
	err($conn->ErrorNo(). $sep .$conn->ErrorMsg());

?>