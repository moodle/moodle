<?php
/* 
V2.00 13 May 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
    
  Latest version is available at http://php.weblogs.com/
*/

GLOBAL $HTTP_SESSION_VARS;

	$ADODB_SESS_DEBUG = true;
	include('adodb.inc.php');
	include('adodb-session.php');
	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	$AVAR += 1;
	
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";
	
?>