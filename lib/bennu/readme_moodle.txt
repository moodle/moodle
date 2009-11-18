18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: iCalendar_rfc2445.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/bennu/iCalendar_rfc2445.php,v
retrieving revision 1.2.10.1
diff -u -r1.2.10.1 iCalendar_rfc2445.php
--- iCalendar_rfc2445.php	12 Oct 2009 05:08:02 -0000	1.2.10.1
+++ iCalendar_rfc2445.php	18 Nov 2009 06:19:31 -0000
@@ -139,13 +139,13 @@
             }
         
             if($scheme === 'mailto') {
-                $regexp = '^[a-zA-Z0-9]+[_a-zA-Z0-9\-]*(\.[_a-z0-9\-]+)*@(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})$';
+                $regexp = '#^[a-zA-Z0-9]+[_a-zA-Z0-9\-]*(\.[_a-z0-9\-]+)*@(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})$#';
             }
             else {
-                $regexp = '^//(.+(:.*)?@)?(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})(:[0-9]{1,5})?(/.*)?$';
+                $regexp = '#^//(.+(:.*)?@)?(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})(:[0-9]{1,5})?(/.*)?$#';
             }
         
-            return ereg($regexp, $remain);
+            return preg_match($regexp, $remain);
         break;
 
         case RFC2445_TYPE_BINARY:
