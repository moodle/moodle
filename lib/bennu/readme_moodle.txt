18 Nov 2009
Description of Bennu modifications to remove functions deprecated as of php 5.3

Index: iCalendar_rfc2445.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/bennu/iCalendar_rfc2445.php,v
retrieving revision 1.5
diff -u -r1.5 iCalendar_rfc2445.php
--- iCalendar_rfc2445.php	4 Nov 2009 20:06:40 -0000	1.5
+++ iCalendar_rfc2445.php	18 Nov 2009 03:50:16 -0000
@@ -138,13 +138,13 @@
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
