Description of ADODB v4.98 library import into Moodle

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * pear/
 * tests/
 * server.php

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * adodb-lib.inc.php - added support for "F" and "L" types in  _adodb_column_sql()
 * adodb-lib.inc.php - modify some debug output to be correct XHTML. MDL-12378.
       Reported to ADOdb at: http://phplens.com/lens/lensforum/msgs.php?id=17133
       Once fixed by adodb guys, we'll return to their official distro.
 * drivers/adodb-mysqli.inc.php - fixed problem with driver not detecting enums
       in the MetaColumns() function. MDL-14215.
       Reported to ADOdb at: http://phplens.com/lens/lensforum/msgs.php?id=17383
       Once fixed by adodb guys, we'll return to their official distro.
 * drivers/adodb-mssql.inc.php, drivers/adodb-oci8.inc.php (qstr) and
       adodb.inc.php (addq and qstr) - fixed wrong "undo magic quotes" that was
       ignoring "magic_quotes_sybase" and leading to wrongly escaped contents. MDL-19452
       Reported privately to John Lim, will be added to upstream soon. Once fixed
       we'll return to their official distro.

skodak, iarenaza, moodler, stronk7

$Id$


18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: drivers/adodb-sybase.inc.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/adodb/drivers/adodb-sybase.inc.php,v
retrieving revision 1.21.6.2
diff -u -r1.21.6.2 adodb-sybase.inc.php
--- drivers/adodb-sybase.inc.php	15 Feb 2008 06:04:06 -0000	1.21.6.2
+++ drivers/adodb-sybase.inc.php	18 Nov 2009 06:15:43 -0000
@@ -376,7 +376,7 @@
 	global $ADODB_sybase_mths;
 	
 		//Dec 30 2000 12:00AM
-		if (!ereg( "([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4})"
+		if (!preg_match( "#([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4})#"
 			,$v, $rr)) return parent::UnixDate($v);
 			
 		if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
@@ -393,7 +393,7 @@
 	global $ADODB_sybase_mths;
 		//11.02.2001 Toni Tunkkari toni.tunkkari@finebyte.com
 		//Changed [0-9] to [0-9 ] in day conversion
-		if (!ereg( "([A-Za-z]{3})[-/\. ]([0-9 ]{1,2})[-/\. ]([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})"
+		if (!preg_match( "#([A-Za-z]{3})[-/\. ]([0-9 ]{1,2})[-/\. ]([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})#"
 			,$v, $rr)) return parent::UnixTimeStamp($v);
 		if ($rr[3] <= TIMESTAMP_FIRST_YEAR) return 0;
 		
Index: session/old/adodb-session-clob.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/adodb/session/old/Attic/adodb-session-clob.php,v
retrieving revision 1.5.6.1
diff -u -r1.5.6.1 adodb-session-clob.php
--- session/old/adodb-session-clob.php	15 Feb 2008 06:04:08 -0000	1.5.6.1
+++ session/old/adodb-session-clob.php	18 Nov 2009 06:15:43 -0000
@@ -439,7 +439,7 @@
 if (0) {
 
 	session_start();
-	session_register('AVAR');
+	session_register('AVAR');//this is deprecated in php 5.3
 	$_SESSION['AVAR'] += 1;
 	ADOConnection::outp( "
 -- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>",false);
