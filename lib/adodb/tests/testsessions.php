<?php

/* 
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://php.weblogs.com/
*/

function NotifyExpire($ref,$key)
{
	print "<p><b>Notify Expiring=$ref, sessionkey=$key</b></p>";
}

//-------------------------------------------------------------------
	
	
#### CONNECTION
	$ADODB_SESSION_DRIVER='oci8';
	$ADODB_SESSION_CONNECT='';
	$ADODB_SESSION_USER ='scott';
	$ADODB_SESSION_PWD ='natsoft';
	$ADODB_SESSION_DB ='';
	
	
### TURN DEBUGGING ON
	$ADODB_SESS_DEBUG = true;

	
#### SETUP NOTIFICATION
	$USER = 'JLIM'.rand();
	$ADODB_SESSION_EXPIRE_NOTIFY = array('USER','NotifyExpire');

	
#### INIT
	ob_start();
	error_reporting(E_ALL);
	include('../session/adodb-cryptsession.php');
	session_start();


### SETUP SESSION VARIABLES 
	$HTTP_SESSION_VARS['MONKEY'] = array('1','abc',44.41);
	if (!isset($HTTP_GET_VARS['nochange'])) @$HTTP_SESSION_VARS['AVAR'] += 1;

	
### START DISPLAY
	print "<h3>PHP ".PHP_VERSION."</h3>";
	print "<p><b>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</b></p>";
	
	print "<hr> <b>Cookies</b>: ";
	print_r($HTTP_COOKIE_VARS);
	
### RANDOMLY PERFORM Garbage Collection
	if (rand() % 10 == 0) {
	
		print "<hr><p><b>Garbage Collection</b></p>";
		adodb_sess_gc(10);
		
		print "<p>Random session destroy</p>";
		session_destroy();
	}
?>