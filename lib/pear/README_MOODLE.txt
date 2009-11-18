MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================


Spreadsheet/Excel
=================

These functions:
    _calculateSharedStringsSizes()
    _storeSharedStringsTable()
have been inserted, replacing the original functions in order to make the function
work with 2-byte data.  The patch is discussed at this URL:
    http://pear.php.net/bugs/bug.php?id=1572
and documented for Moodle at:
    http://tracker.moodle.org/browse/MDL-9911

Such modifications should be carefuly each time the Excel PEAR package is updated
to a new release within Moodle.


PHP/CodeSniffer
===============

A whole Moodle coding standards definition sits in lib/pear/PHP/CodeSniffer/Standards/Moodle

To run the codesniffer, you can call the runsniffer script using your command-line php binary:

Example:  /usr/bin/php lib/pear/PHP/runsniffer mod/forum



18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: Spreadsheet/Excel/Writer/Parser.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/pear/Spreadsheet/Excel/Writer/Parser.php,v
retrieving revision 1.2
diff -u -r1.2 Parser.php
--- Spreadsheet/Excel/Writer/Parser.php	4 Jan 2006 08:23:42 -0000	1.2
+++ Spreadsheet/Excel/Writer/Parser.php	18 Nov 2009 04:11:09 -0000
@@ -1206,7 +1206,7 @@
             default:
                 // if it's a reference
                 if (preg_match('/^\$?[A-Ia-i]?[A-Za-z]\$?[0-9]+$/',$token) and
-                   !ereg("[0-9]",$this->_lookahead) and 
+                   !preg_match("/[0-9]/",$this->_lookahead) and
                    ($this->_lookahead != ':') and ($this->_lookahead != '.') and
                    ($this->_lookahead != '!'))
                 {
@@ -1214,39 +1214,39 @@
                 }
                 // If it's an external reference (Sheet1!A1 or Sheet1:Sheet2!A1)
                 elseif (preg_match("/^\w+(\:\w+)?\![A-Ia-i]?[A-Za-z][0-9]+$/u",$token) and
-                       !ereg("[0-9]",$this->_lookahead) and
+                       !preg("/[0-9]/",$this->_lookahead) and
                        ($this->_lookahead != ':') and ($this->_lookahead != '.'))
                 {
                     return $token;
                 }
                 // If it's an external reference ('Sheet1'!A1 or 'Sheet1:Sheet2'!A1)
                 elseif (preg_match("/^'[\w -]+(\:[\w -]+)?'\![A-Ia-i]?[A-Za-z][0-9]+$/u",$token) and
-                       !ereg("[0-9]",$this->_lookahead) and
+                       !preg("/[0-9]/",$this->_lookahead) and
                        ($this->_lookahead != ':') and ($this->_lookahead != '.'))
                 {
                     return $token;
                 }
                 // if it's a range (A1:A2)
                 elseif (preg_match("/^(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+:(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+$/",$token) and 
-                       !ereg("[0-9]",$this->_lookahead))
+                       !preg_match("/[0-9]/",$this->_lookahead))
                 {
                     return $token;
                 }
                 // if it's a range (A1..A2)
                 elseif (preg_match("/^(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+\.\.(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+$/",$token) and 
-                       !ereg("[0-9]",$this->_lookahead))
+                       !preg_match("/[0-9]/",$this->_lookahead))
                 {
                     return $token;
                 }
                 // If it's an external range like Sheet1!A1 or Sheet1:Sheet2!A1:B2
                 elseif (preg_match("/^\w+(\:\w+)?\!([A-Ia-i]?[A-Za-z])?[0-9]+:([A-Ia-i]?[A-Za-z])?[0-9]+$/u",$token) and
-                       !ereg("[0-9]",$this->_lookahead))
+                       !preg_match("/[0-9]/",$this->_lookahead))
                 {
                     return $token;
                 }
                 // If it's an external range like 'Sheet1'!A1 or 'Sheet1:Sheet2'!A1:B2
                 elseif (preg_match("/^'[\w -]+(\:[\w -]+)?'\!([A-Ia-i]?[A-Za-z])?[0-9]+:([A-Ia-i]?[A-Za-z])?[0-9]+$/u",$token) and
-                       !ereg("[0-9]",$this->_lookahead))
+                       !preg_match("/[0-9]/",$this->_lookahead))
                 {
                     return $token;
                 }
@@ -1258,12 +1258,12 @@
                     return $token;
                 }
                 // If it's a string (of maximum 255 characters)
-                elseif (ereg("^\"[^\"]{0,255}\"$",$token))
+                elseif (preg_match("/^\"[^\"]{0,255}\"$/",$token))
                 {
                     return $token;
                 }
                 // if it's a function call
-                elseif (eregi("^[A-Z0-9\xc0-\xdc\.]+$",$token) and ($this->_lookahead == "("))
+                elseif (preg_match("/^[A-Z0-9\xc0-\xdc\.]+$/i",$token) and ($this->_lookahead == "("))
                 {
                     return $token;
                 }
@@ -1363,7 +1363,7 @@
     function _expression()
     {
         // If it's a string return a string node
-        if (ereg("^\"[^\"]{0,255}\"$", $this->_current_token)) {
+        if (preg_match("/^\"[^\"]{0,255}\"$/", $this->_current_token)) {
             $result = $this->_createTree($this->_current_token, '', '');
             $this->_advance();
             return $result;
@@ -1521,7 +1521,7 @@
             return $result;
         }
         // if it's a function call
-        elseif (eregi("^[A-Z0-9\xc0-\xdc\.]+$",$this->_current_token))
+        elseif (preg_match("/^[A-Z0-9\xc0-\xdc\.]+$/",$this->_current_token))
         {
             $result = $this->_func();
             return $result;
Index: XML/Parser.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/pear/XML/Parser.php,v
retrieving revision 1.1
diff -u -r1.1 Parser.php
--- XML/Parser.php	22 Jun 2009 18:35:56 -0000	1.1
+++ XML/Parser.php	18 Nov 2009 04:11:10 -0000
@@ -417,7 +417,7 @@
         /**
          * check, if file is a remote file
          */
-        if (eregi('^(http|ftp)://', substr($file, 0, 10))) {
+        if (preg_match('#^(http|ftp)://#i', substr($file, 0, 10))) {
             if (!ini_get('allow_url_fopen')) {
                 return $this->
                 raiseError('Remote files cannot be parsed, as safe mode is enabled.',
@@ -474,7 +474,7 @@
         if (is_resource($fp)) {
             $this->fp = $fp;
             return true;
-        } elseif (eregi('^[a-z]+://', substr($fp, 0, 10))) {
+        } elseif (preg_match('#^[a-z]+://#i', substr($fp, 0, 10))) {
             // see if it's an absolute URL (has a scheme at the beginning)
             return $this->setInputFile($fp);
         } elseif (file_exists($fp)) {
