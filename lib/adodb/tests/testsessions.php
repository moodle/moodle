<?php
/* 
V2.50 14 Nov 2002  (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
	
  Latest version is available at http://php.weblogs.com/
*/

GLOBAL $HTTP_SESSION_VARS;

	error_reporting(E_ALL);
	
	$ADODB_SESS_DEBUG = true;
	include('../adodb-session.php');
	session_start();
	
	print "<h3>PHP ".PHP_VERSION."</h3>";
	
	$HTTP_SESSION_VARS['MONKEY'] = array('1','abc',44.41);
	if (!isset($HTTP_GET_VARS['nochange'])) @$HTTP_SESSION_VARS['AVAR'] += 1;
	
	print "<p><b>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</b></p>";
	
	if (rand() % 10 == 0) {
		print "<p>Random session destroy</p>";
		session_destroy();
	}
?>