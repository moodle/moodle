18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: classes/XMLDBObject.class.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/xmldb/classes/Attic/XMLDBObject.class.php,v
retrieving revision 1.7.2.1
diff -u -r1.7.2.1 XMLDBObject.class.php
--- classes/XMLDBObject.class.php	15 Aug 2008 11:09:51 -0000	1.7.2.1
+++ classes/XMLDBObject.class.php	18 Nov 2009 06:55:49 -0000
@@ -164,7 +164,7 @@
     function checkName () {
         $result = true;
 
-        if ($this->name != eregi_replace('[^a-z0-9_ -]', '', $this->name)) {
+        if ($this->name != preg_replace('/[^a-z0-9_ -]/i', '', $this->name)) {
             $result = false;
         }
         return $result;
