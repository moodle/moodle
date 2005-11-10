<?php

/* 
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://adodb.sourceforge.net
*/

function NotifyExpire($ref,$key)
{
	print "<p><b>Notify Expiring=$ref, sessionkey=$key</b></p>";
}

//-------------------------------------------------------------------
	
error_reporting(E_ALL);

#### CONNECTION
if (0) {
	$ADODB_SESSION_DRIVER='oci8';
	$ADODB_SESSION_CONNECT='';
	$ADODB_SESSION_USER ='scott';
	$ADODB_SESSION_PWD ='natsoft';
	$ADODB_SESSION_DB ='';
} else {
	$ADODB_SESSION_DRIVER='mysql';
	$ADODB_SESSION_CONNECT='localhost';
	$ADODB_SESSION_USER ='root';
	$ADODB_SESSION_PWD ='';
	$ADODB_SESSION_DB ='xphplens_2';
}
	
### TURN DEBUGGING ON
	$ADODB_SESS_DEBUG = 99;

	
#### SETUP NOTIFICATION
	$USER = 'JLIM'.rand();
	$ADODB_SESSION_EXPIRE_NOTIFY = array('USER','NotifyExpire');

	
#### INIT
	ob_start();
	include('../session/adodb-cryptsession.php');
	session_start();

	adodb_session_regenerate_id();
	
### SETUP SESSION VARIABLES 
	$_SESSION['MONKEY'] = array('1','abc',44.41);
	if (!isset($_GET['nochange'])) @$_SESSION['AVAR'] += 1;

	
### START DISPLAY
	print "<h3>PHP ".PHP_VERSION."</h3>";
	print "<p><b>\$_SESSION['AVAR']={$_SESSION['AVAR']}</b></p>";
	
	print "<hr> <b>Cookies</b>: ";
	print_r($_COOKIE);
	
### RANDOMLY PERFORM Garbage Collection
### In real-production environment, this is done for you
### by php's session extension, which calls adodb_sess_gc()
### automatically for you. See php.ini's
### session.cookie_lifetime and session.gc_probability

	if (rand() % 5 == 0) {
	
		print "<hr><p><b>Garbage Collection</b></p>";
		adodb_sess_gc(10);
		
		if (rand() % 2 == 0) {
			print "<p>Random session destroy</p>";
			session_destroy();
		}
	}
?>