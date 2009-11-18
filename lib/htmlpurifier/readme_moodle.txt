Description of HTML Purifier v2.1.5 Lite library import into Moodle

Changes:
 * HMLTModule/Text.php - added  <nolink>, <tex>, <lang> and <algebra> tags
 * HMLTModule/XMLCommonAttributes.php - remove xml:lang - needed for multilang
 * AttrDef/Lang.php - relax lang check - needed for multilang

skodak

$Id$


18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: HTMLPurifier/AttrDef/Lang.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/htmlpurifier/HTMLPurifier/AttrDef/Lang.php,v
retrieving revision 1.3
diff -u -r1.3 Lang.php
--- HTMLPurifier/AttrDef/Lang.php	25 Sep 2007 14:34:13 -0000	1.3
+++ HTMLPurifier/AttrDef/Lang.php	18 Nov 2009 06:37:14 -0000
@@ -12,7 +12,7 @@
     function validate($string, $config, &$context) {
 
 // moodle change - we use special lang strings unfortunatelly
-        return ereg_replace('[^0-9a-zA-Z_-]', '', $string);
+        return preg_replace('/[^0-9a-zA-Z_-]/', '', $string);
 // moodle change end
         
         $string = trim($string);
