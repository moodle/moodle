<?php
/*
  V4.93 10 Oct 2006  (c) 2000-2006 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.
  
  Latest version of ADODB is available at http://php.weblogs.com/adodb
  ======================================================================
  
 This file provides PHP4 session management using the ADODB database
 wrapper library, using Oracle CLOB's to store data. Contributed by achim.gosse@ddd.de.

 Example
 =======
 
	include('adodb.inc.php');
	include('adodb-session.php');
	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	print "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>";
	
To force non-persistent connections, call adodb_session_open first before session_start():

	include('adodb.inc.php');
	include('adodb-session.php');
	adodb_session_open(false,false,false);
	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	print "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>";

 
 Installation
 ============
 1. Create this table in your database (syntax might vary depending on your db):
 
  create table sessions (
	   SESSKEY char(32) not null,
	   EXPIRY int(11) unsigned not null,
	   EXPIREREF varchar(64),
	   DATA CLOB,
	  primary key (sesskey)
  );


  2. Then define the following parameters in this file:
  	$ADODB_SESSION_DRIVER='database driver, eg. mysql or ibase';
	$ADODB_SESSION_CONNECT='server to connect to';
	$ADODB_SESSION_USER ='user';
	$ADODB_SESSION_PWD ='password';
	$ADODB_SESSION_DB ='database';
	$ADODB_SESSION_TBL = 'sessions'
	$ADODB_SESSION_USE_LOBS = false; (or, if you wanna use CLOBS (= 'CLOB') or ( = 'BLOB')
	
  3. Recommended is PHP 4.1.0 or later. There are documented
	 session bugs in earlier versions of PHP.

  4. If you want to receive notifications when a session expires, then
  	 you can tag a session with an EXPIREREF, and before the session
	 record is deleted, we can call a function that will pass the EXPIREREF
	 as the first parameter, and the session key as the second parameter.
	 
	 To do this, define a notification function, say NotifyFn:
	 
	 	function NotifyFn($expireref, $sesskey)
	 	{
	 	}
	 
	 Then you need to define a global variable $ADODB_SESSION_EXPIRE_NOTIFY.
	 This is an array with 2 elements, the first being the name of the variable
	 you would like to store in the EXPIREREF field, and the 2nd is the 
	 notification function's name.
	 
	 In this example, we want to be notified when a user's session 
	 has expired, so we store the user id in the global variable $USERID, 
	 store this value in the EXPIREREF field:
	 
	 	$ADODB_SESSION_EXPIRE_NOTIFY = array('USERID','NotifyFn');
		
	Then when the NotifyFn is called, we are passed the $USERID as the first
	parameter, eg. NotifyFn($userid, $sesskey).
*/

if (!defined('_ADODB_LAYER')) {
	include (dirname(__FILE__).'/adodb.inc.php');
}

if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);
 
 /* if database time and system time is difference is greater than this, then give warning */
 define('ADODB_SESSION_SYNCH_SECS',60); 

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
	$ADODB_SESSION_EXPIRE_NOTIFY,
	$ADODB_SESSION_CRC,
	$ADODB_SESSION_USE_LOBS,
	$ADODB_SESSION_TBL;
	
	if (!isset($ADODB_SESSION_USE_LOBS)) $ADODB_SESSION_USE_LOBS = 'CLOB';
	
	$ADODB_SESS_LIFE = ini_get('session.gc_maxlifetime');
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
	
	if (empty($ADODB_SESSION_EXPIRE_NOTIFY)) {
		$ADODB_SESSION_EXPIRE_NOTIFY = false;
	}
	//  Made table name configurable - by David Johnson djohnson@inpro.net
	if (empty($ADODB_SESSION_TBL)){
		$ADODB_SESSION_TBL = 'sessions';
	}
	

	// defaulting $ADODB_SESSION_USE_LOBS
	if (!isset($ADODB_SESSION_USE_LOBS) || empty($ADODB_SESSION_USE_LOBS)) {
		$ADODB_SESSION_USE_LOBS = false;
	}

	/*
	$ADODB_SESS['driver'] = $ADODB_SESSION_DRIVER;
	$ADODB_SESS['connect'] = $ADODB_SESSION_CONNECT;
	$ADODB_SESS['user'] = $ADODB_SESSION_USER;
	$ADODB_SESS['pwd'] = $ADODB_SESSION_PWD;
	$ADODB_SESS['db'] = $ADODB_SESSION_DB;
	$ADODB_SESS['life'] = $ADODB_SESS_LIFE;
	$ADODB_SESS['debug'] = $ADODB_SESS_DEBUG;
	
	$ADODB_SESS['debug'] = $ADODB_SESS_DEBUG;
	$ADODB_SESS['table'] = $ADODB_SESS_TBL;
	*/
	
/****************************************************************************************\
	Create the connection to the database. 
	
	If $ADODB_SESS_CONN already exists, reuse that connection
\****************************************************************************************/
function adodb_sess_open($save_path, $session_name,$persist=true) 
{
GLOBAL $ADODB_SESS_CONN;
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
		ADOConnection::outp( " conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ");
	}
	if ($persist) $ok = $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	else $ok = $ADODB_SESS_CONN->Connect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
	if (!$ok) ADOConnection::outp( "
-- Session: connection failed</p>",false);
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
global $ADODB_SESS_CONN,$ADODB_SESSION_TBL,$ADODB_SESSION_CRC;

	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM $ADODB_SESSION_TBL WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$v = '';
		} else 
			$v = rawurldecode(reset($rs->fields));
			
		$rs->Close();
		
		// new optimization adodb 2.1
		$ADODB_SESSION_CRC = strlen($v).crc32($v);
		
		return $v;
	}
	
	return ''; // thx to Jorma Tuomainen, webmaster#wizactive.com
}

/****************************************************************************************\
	Write the serialized data to a database.
	
	If the data has not been modified since adodb_sess_read(), we do not write.
\****************************************************************************************/
function adodb_sess_write($key, $val) 
{
	global
		$ADODB_SESS_CONN, 
		$ADODB_SESS_LIFE, 
		$ADODB_SESSION_TBL,
		$ADODB_SESS_DEBUG, 
		$ADODB_SESSION_CRC,
		$ADODB_SESSION_EXPIRE_NOTIFY,
		$ADODB_SESSION_DRIVER,			// added
		$ADODB_SESSION_USE_LOBS;		// added

	$expiry = time() + $ADODB_SESS_LIFE;
	
	// crc32 optimization since adodb 2.1
	// now we only update expiry date, thx to sebastian thom in adodb 2.32
	if ($ADODB_SESSION_CRC !== false && $ADODB_SESSION_CRC == strlen($val).crc32($val)) {
		if ($ADODB_SESS_DEBUG) echo "
-- Session: Only updating date - crc32 not changed</p>";
		$qry = "UPDATE $ADODB_SESSION_TBL SET expiry=$expiry WHERE sesskey='$key' AND expiry >= " . time();
		$rs = $ADODB_SESS_CONN->Execute($qry);	
		return true;
	}
	$val = rawurlencode($val);
	
	$arr = array('sesskey' => $key, 'expiry' => $expiry, 'data' => $val);
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		$var = reset($ADODB_SESSION_EXPIRE_NOTIFY);
		global $$var;
		$arr['expireref'] = $$var;
	}

	
	if ($ADODB_SESSION_USE_LOBS === false) {	// no lobs, simply use replace()
		$rs = $ADODB_SESS_CONN->Replace($ADODB_SESSION_TBL,$arr, 'sesskey',$autoQuote = true);
		if (!$rs) {
			$err = $ADODB_SESS_CONN->ErrorMsg();
		}
	} else {
		// what value shall we insert/update for lob row?
		switch ($ADODB_SESSION_DRIVER) {
			// empty_clob or empty_lob for oracle dbs
			case "oracle":
			case "oci8":
			case "oci8po":
			case "oci805":
				$lob_value = sprintf("empty_%s()", strtolower($ADODB_SESSION_USE_LOBS));
				break;

			// null for all other
			default:
				$lob_value = "null";
				break;
		}

		// do we insert or update? => as for sesskey
		$res = $ADODB_SESS_CONN->Execute("select count(*) as cnt from $ADODB_SESSION_TBL where sesskey = '$key'");
		if ($res && reset($res->fields) > 0) {
			$qry = sprintf("update %s set expiry = %d, data = %s where sesskey = '%s'", $ADODB_SESSION_TBL, $expiry, $lob_value, $key);
		} else {
			// insert
			$qry = sprintf("insert into %s (sesskey, expiry, data) values ('%s', %d, %s)", $ADODB_SESSION_TBL, $key, $expiry, $lob_value);
		}

		$err = "";
		$rs1 = $ADODB_SESS_CONN->Execute($qry);
		if (!$rs1) {
			$err .= $ADODB_SESS_CONN->ErrorMsg()."\n";
		}
		$rs2 = $ADODB_SESS_CONN->UpdateBlob($ADODB_SESSION_TBL, 'data', $val, "sesskey='$key'", strtoupper($ADODB_SESSION_USE_LOBS));
		if (!$rs2) {
			$err .= $ADODB_SESS_CONN->ErrorMsg()."\n";
		}
		$rs = ($rs1 && $rs2) ? true : false;
	}

	if (!$rs) {
		ADOConnection::outp( '
-- Session Replace: '.nl2br($err).'</p>',false);
	}  else {
		// bug in access driver (could be odbc?) means that info is not commited
		// properly unless select statement executed in Win2000
		if ($ADODB_SESS_CONN->databaseType == 'access') 
			$rs = $ADODB_SESS_CONN->Execute("select sesskey from $ADODB_SESSION_TBL WHERE sesskey='$key'");
	}
	return !empty($rs);
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

function adodb_sess_gc($maxlifetime) 
{
	global $ADODB_SESS_DEBUG, $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
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
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$rs->Close();
			
			//$ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE expiry < $t");
			$ADODB_SESS_CONN->CommitTrans();
			
		}
	} else {
		$ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE expiry < " . time());
	
		if ($ADODB_SESS_DEBUG) ADOConnection::outp("
-- <b>Garbage Collection</b>: $qry</p>");
	}
	// suggested by Cameron, "GaM3R" <gamr@outworld.cx>
	if (defined('ADODB_SESSION_OPTIMIZE')) {
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
		if (!empty($opt_qry)) {
			$ADODB_SESS_CONN->Execute($opt_qry);
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

if (0) {

	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	ADOConnection::outp( "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>",false);
}

?>
