<?php
/*
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.
  
  Latest version of ADODB is available at http://php.weblogs.com/adodb
  ======================================================================
  
 This file provides PHP4 session management using the ADODB database
wrapper library.
 
 Example
 =======
 
 	GLOBAL $HTTP_SESSION_VARS;
	include('adodb.inc.php');
	include('adodb-session.php');
	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";

 
 Installation
 ============
 1. Create this table in your database (syntax might vary depending on your db):
 
  create table sessions (
       SESSKEY char(32) not null,
       EXPIRY int(11) unsigned not null,
       DATA text not null,
      primary key (sesskey)
  );


  2. Then define the following parameters in this file:
  	$ADODB_SESSION_DRIVER='database driver, eg. mysql or ibase';
	$ADODB_SESSION_CONNECT='server to connect to';
	$ADODB_SESSION_USER ='user';
	$ADODB_SESSION_PWD ='password';
	$ADODB_SESSION_DB ='database';
	$ADODB_SESSION_TBL = 'sessions'
	
  3. Recommended is PHP 4.0.6 or later. There are documented
     session bugs in earlier versions of PHP.

*/

if (!defined('_ADODB_LAYER')) {
	include (dirname(__FILE__).'/adodb.inc.php');
}

if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);

/****************************************************************************************\
	Global definitions
\****************************************************************************************/
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_LIFE,
	$ADODB_SESS_DEBUG,
	$ADODB_SESS_INSERT, 
	$ADODB_SESSION_CRC;
	
	$ADODB_SESS_LIFE = get_cfg_var('session.gc_maxlifetime');
	if ($ADODB_SESS_LIFE <= 1) {
	 // bug in PHP 4.0.3 pl 1  -- how about other versions?
	 //print "<h3>Session Error: PHP.INI setting <i>session.gc_maxlifetime</i>not set: $ADODB_SESS_LIFE</h3>";
	 	$ADODB_SESS_LIFE=1440;
	}
	$ADODB_SESSION_CRC = false;
	//$ADODB_SESS_DEBUG = true;
	
	//////////////////////////////////
	/* SET THE FOLLOWING PARAMETERS */
	//////////////////////////////////
	
	if (empty($ADODB_SESSION_DRIVER)) {
		$ADODB_SESSION_DRIVER='mysql';
		$ADODB_SESSION_CONNECT='localhost';
		$ADODB_SESSION_USER ='root';
		$ADODB_SESSION_PWD ='';
		$ADODB_SESSION_DB ='xphplens_2';
	}
	
	//  Made table name configurable - by David Johnson djohnson@inpro.net
	if (empty($ADODB_SESSION_TBL)){
		$ADODB_SESSION_TBL = 'sessions';
	}

/****************************************************************************************\
	Create the connection to the database. 
	
	If $ADODB_SESS_CONN already exists, reuse that connection
\****************************************************************************************/
function adodb_sess_open($save_path, $session_name,$persist=true) 
{
GLOBAL $ADODB_SESS_CONN;
	//if( $persist) print "PERSIST ";
	if (isset($ADODB_SESS_CONN)) return true;
	
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_DEBUG;
	
	// cannot use & below - do not know why...
	$ADODB_SESS_CONN = ADONewConnection($ADODB_SESSION_DRIVER);
	if (!empty($ADODB_SESS_DEBUG)) {
		$ADODB_SESS_CONN->debug = true;
		print " conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ";
	}
	if ($persist) $ok = $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	else $ok = $ADODB_SESS_CONN->Connect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
	if (!$ok) print "<p>Session: connection failed</p>";
}

/****************************************************************************************\
	Close the connection
\****************************************************************************************/
function adodb_sess_close() 
{
global $ADODB_SESS_CONN;

	if ($ADODB_SESS_CONN) $ADODB_SESS_CONN->Close();
	return true;
}

/****************************************************************************************\
	Slurp in the session variables and return the serialized string
\****************************************************************************************/
function adodb_sess_read($key) 
{
global $ADODB_SESS_CONN,$ADODB_SESS_INSERT,$ADODB_SESSION_TBL,$ADODB_SESSION_CRC;

	$ADODB_SESS_INSERT = false;	
	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM $ADODB_SESSION_TBL WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$ADODB_SESS_INSERT = true;
			$v = '';
		} else 
			$v = rawurldecode(reset($rs->fields));
			
		$rs->Close();
		
		// new optimization adodb 2.1
		$ADODB_SESSION_CRC = crc32($v);
		
		return $v;
	}
	else $ADODB_SESS_INSERT = true;
	
	return ''; // thx to Jorma Tuomainen, webmaster#wizactive.com
}

/****************************************************************************************\
	Write the serialized data to a database.
	
	If the data has not been modified since adodb_sess_read(), we do not write.
\****************************************************************************************/
function adodb_sess_write($key, $val) 
{
	global $ADODB_SESS_INSERT,
		$ADODB_SESS_CONN, 
		$ADODB_SESS_LIFE, 
		$ADODB_SESSION_TBL,
		$ADODB_SESS_DEBUG, 
		$ADODB_SESSION_CRC;

	$expiry = time() + $ADODB_SESS_LIFE;
	
	// new optimization adodb 2.1
	if ($ADODB_SESSION_CRC !== false && $ADODB_SESSION_CRC == crc32($val)) {
		if ($ADODB_SESS_DEBUG) echo "<p>Session: No need to update - crc32 not changed</p>";
		return true;
	}
	$val = rawurlencode($val);
	$qry = "UPDATE $ADODB_SESSION_TBL SET expiry=$expiry,data='$val' WHERE sesskey='$key'";
	$rs = $ADODB_SESS_CONN->Execute($qry);
	if ($rs) $rs->Close();
	else print '<p>Session Update: '.$ADODB_SESS_CONN->ErrorMsg().'</p>';
	
	if ($ADODB_SESS_INSERT || $rs === false) {
		$qry = "INSERT INTO $ADODB_SESSION_TBL(sesskey,expiry,data) VALUES ('$key',$expiry,'$val')";
		$rs = $ADODB_SESS_CONN->Execute($qry);
		if ($rs) $rs->Close();
		else print '<p>Session Insert: '.$ADODB_SESS_CONN->ErrorMsg().'</p>';
	}
	// bug in access driver (could be odbc?) means that info is not commited
	// properly unless select statement executed in Win2000
	if ($ADODB_SESS_CONN->databaseType == 'access') $rs = $ADODB_SESS_CONN->Execute("select sesskey from $ADODB_SESSION_TBL WHERE sesskey='$key'");

	return isset($rs);
}

function adodb_sess_destroy($key) 
{
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL;

	$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE sesskey = '$key'";
	$rs = $ADODB_SESS_CONN->Execute($qry);
	return $rs ? true : false;
}

function adodb_sess_gc($maxlifetime) 
{
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_DRIVER;

	$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE expiry < " . time();
	$rs = $ADODB_SESS_CONN->Execute($qry);
	if ($rs) $rs->Close();
	
	// suggested by Cameron, "GaM3R" <gamr@outworld.cx>
	if (defined('ADODB_SESSION_OPTIMIZE'))
	{
		switch( $ADODB_SESSION_DRIVER ) {
			case 'mysql':
			case 'mysqlt':
				$opt_qry = 'OPTIMIZE TABLE '.$ADODB_SESSION_TBL;
				break;
			case 'postgresql':
			case 'postgresql7':
				$opt_qry = 'VACUUM '.$ADODB_SESSION_TBL;	
				break;
		}
	}
	
	return true;
}

session_module_name('user'); 
session_set_save_handler(
	"adodb_sess_open",
	"adodb_sess_close",
	"adodb_sess_read",
	"adodb_sess_write",
	"adodb_sess_destroy",
	"adodb_sess_gc");
}

/*  TEST SCRIPT -- UNCOMMENT */

if (0) {
GLOBAL $HTTP_SESSION_VARS;

	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";
}

?>
