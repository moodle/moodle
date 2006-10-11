<?php
/*
V4.93 10 Oct 2006  (c) 2000-2006 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	Made table name configurable - by David Johnson djohnson@inpro.net
	Encryption by Ari Kuorikoski <ari.kuorikoski@finebyte.com>
	
  Set tabs to 4 for best viewing.
  
  Latest version of ADODB is available at http://php.weblogs.com/adodb
  ======================================================================
  
 This file provides PHP4 session management using the ADODB database
wrapper library.
 
 Example
 =======
 
	include('adodb.inc.php');
	#---------------------------------#
	include('adodb-cryptsession.php'); 
	#---------------------------------#
	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	print "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>";

 
 Installation
 ============
 1. Create a new database in MySQL or Access "sessions" like
so:
 
  create table sessions (
	   SESSKEY char(32) not null,
	   EXPIRY int(11) unsigned not null,
	   EXPIREREF varchar(64),
	   DATA CLOB,
	  primary key (sesskey)
  );
  
  2. Then define the following parameters. You can either modify
     this file, or define them before this file is included:
	 
  	$ADODB_SESSION_DRIVER='database driver, eg. mysql or ibase';
	$ADODB_SESSION_CONNECT='server to connect to';
	$ADODB_SESSION_USER ='user';
	$ADODB_SESSION_PWD ='password';
	$ADODB_SESSION_DB ='database';
	$ADODB_SESSION_TBL = 'sessions'
	
  3. Recommended is PHP 4.0.2 or later. There are documented
session bugs in earlier versions of PHP.

*/


include_once('crypt.inc.php');

if (!defined('_ADODB_LAYER')) {
	include (dirname(__FILE__).'/adodb.inc.php');
}

 /* if database time and system time is difference is greater than this, then give warning */
 define('ADODB_SESSION_SYNCH_SECS',60); 

if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);
 
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_LIFE,
	$ADODB_SESS_DEBUG,
	$ADODB_SESS_INSERT,
	$ADODB_SESSION_EXPIRE_NOTIFY,
	$ADODB_SESSION_TBL; 

	//$ADODB_SESS_DEBUG = true;
	
	/* SET THE FOLLOWING PARAMETERS */
if (empty($ADODB_SESSION_DRIVER)) {
	$ADODB_SESSION_DRIVER='mysql';
	$ADODB_SESSION_CONNECT='localhost';
	$ADODB_SESSION_USER ='root';
	$ADODB_SESSION_PWD ='';
	$ADODB_SESSION_DB ='xphplens_2';
}

if (empty($ADODB_SESSION_TBL)){
	$ADODB_SESSION_TBL = 'sessions';
}

if (empty($ADODB_SESSION_EXPIRE_NOTIFY)) {
	$ADODB_SESSION_EXPIRE_NOTIFY = false;
}

function ADODB_Session_Key() 
{
$ADODB_CRYPT_KEY = 'CRYPTED ADODB SESSIONS ROCK!';

	/* USE THIS FUNCTION TO CREATE THE ENCRYPTION KEY FOR CRYPTED SESSIONS	*/
	/* Crypt the used key, $ADODB_CRYPT_KEY as key and session_ID as SALT	*/
	return crypt($ADODB_CRYPT_KEY, session_ID());
}

$ADODB_SESS_LIFE = ini_get('session.gc_maxlifetime');
if ($ADODB_SESS_LIFE <= 1) {
	// bug in PHP 4.0.3 pl 1  -- how about other versions?
	//print "<h3>Session Error: PHP.INI setting <i>session.gc_maxlifetime</i>not set: $ADODB_SESS_LIFE</h3>";
	$ADODB_SESS_LIFE=1440;
}

function adodb_sess_open($save_path, $session_name) 
{
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_DEBUG;
	
	$ADODB_SESS_INSERT = false;
	
	if (isset($ADODB_SESS_CONN)) return true;
	
	$ADODB_SESS_CONN = ADONewConnection($ADODB_SESSION_DRIVER);
	if (!empty($ADODB_SESS_DEBUG)) {
		$ADODB_SESS_CONN->debug = true;
		print" conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ";
	}
	return $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
}

function adodb_sess_close() 
{
global $ADODB_SESS_CONN;

	if ($ADODB_SESS_CONN) $ADODB_SESS_CONN->Close();
	return true;
}

function adodb_sess_read($key) 
{
$Crypt = new MD5Crypt;
global $ADODB_SESS_CONN,$ADODB_SESS_INSERT,$ADODB_SESSION_TBL;
	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM $ADODB_SESSION_TBL WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$ADODB_SESS_INSERT = true;
			$v = '';
		} else {
			// Decrypt session data
			$v = rawurldecode($Crypt->Decrypt(reset($rs->fields), ADODB_Session_Key()));
		}
		$rs->Close();
		return $v;
	}
	else $ADODB_SESS_INSERT = true;
	
	return '';
}

function adodb_sess_write($key, $val) 
{
$Crypt = new MD5Crypt;
	global $ADODB_SESS_INSERT,$ADODB_SESS_CONN, $ADODB_SESS_LIFE, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;

	$expiry = time() + $ADODB_SESS_LIFE;

	// encrypt session data..	
	$val = $Crypt->Encrypt(rawurlencode($val), ADODB_Session_Key());
	
	$arr = array('sesskey' => $key, 'expiry' => $expiry, 'data' => $val);
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		$var = reset($ADODB_SESSION_EXPIRE_NOTIFY);
		global $$var;
		$arr['expireref'] = $$var;
	}
	$rs = $ADODB_SESS_CONN->Replace($ADODB_SESSION_TBL,
	    $arr,
    	'sesskey',$autoQuote = true);

	if (!$rs) {
		ADOConnection::outp( '
-- Session Replace: '.$ADODB_SESS_CONN->ErrorMsg().'</p>',false);
	} else {
		// bug in access driver (could be odbc?) means that info is not commited
		// properly unless select statement executed in Win2000
	
	if ($ADODB_SESS_CONN->databaseType == 'access') $rs = $ADODB_SESS_CONN->Execute("select sesskey from $ADODB_SESSION_TBL WHERE sesskey='$key'");
	}
	return isset($rs);
}

function adodb_sess_destroy($key) 
{
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$ADODB_SESS_CONN->CommitTrans();
		}
	} else {
		$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE sesskey = '$key'";
		$rs = $ADODB_SESS_CONN->Execute($qry);
	}
	return $rs ? true : false;
}


function adodb_sess_gc($maxlifetime) {
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY,$ADODB_SESS_DEBUG;

	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$t = time();
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE expiry < $t");
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				//$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$rs->Close();
			
			$ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE expiry < $t");
			$ADODB_SESS_CONN->CommitTrans();
		}
	} else {
		$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE expiry < " . time();
		$ADODB_SESS_CONN->Execute($qry);
	}
	
	// suggested by Cameron, "GaM3R" <gamr@outworld.cx>
	if (defined('ADODB_SESSION_OPTIMIZE'))
	{
	global $ADODB_SESSION_DRIVER;
	
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
	
	if ($ADODB_SESS_CONN->dataProvider === 'oci8') $sql = 'select  TO_CHAR('.($ADODB_SESS_CONN->sysTimeStamp).', \'RRRR-MM-DD HH24:MI:SS\') from '. $ADODB_SESSION_TBL;
	else $sql = 'select '.$ADODB_SESS_CONN->sysTimeStamp.' from '. $ADODB_SESSION_TBL;
	
	$rs =& $ADODB_SESS_CONN->SelectLimit($sql,1);
	if ($rs && !$rs->EOF) {
	
		$dbts = reset($rs->fields);
		$rs->Close();
		$dbt = $ADODB_SESS_CONN->UnixTimeStamp($dbts);
		$t = time();
		if (abs($dbt - $t) >= ADODB_SESSION_SYNCH_SECS) {
			$msg = 
			__FILE__.": Server time for webserver {$_SERVER['HTTP_HOST']} not in synch with database: database=$dbt ($dbts), webserver=$t (diff=".(abs($dbt-$t)/3600)." hrs)";
			error_log($msg);
			if ($ADODB_SESS_DEBUG) ADOConnection::outp("
-- $msg</p>");
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
/*
if (0) {

	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	print "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>";
}
*/
?>
