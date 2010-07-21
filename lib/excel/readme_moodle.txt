20 Jul 2010
MDL-20876 - replaced deprecated split() with explode() or str_split() where appropriate

diff --git a/lib/excel/Parser.php b/lib/excel/Parser.php
index f91cf98..06b9e23 100644
--- a/lib/excel/Parser.php
+++ b/lib/excel/Parser.php
@@ -538,10 +538,10 @@ class Parser
 
     // Split the range into 2 cell refs
     if(preg_match("/^([A-I]?[A-Z])(\d+)\:([A-I]?[A-Z])(\d+)$/",$range)) {
-        list($cell1, $cell2) = split(':', $range);
+        list($cell1, $cell2) = explode(':', $range);
         }
     elseif(preg_match("/^([A-I]?[A-Z])(\d+)\.\.([A-I]?[A-Z])(\d+)$/",$range)) {
-        list($cell1, $cell2) = split('\.\.', $range);
+        list($cell1, $cell2) = explode('..', $range);
         }
     else {
         die("Unknown range separator");
@@ -993,4 +993,4 @@ class Parser
     return $polish;
     }
   }
-?>
\ No newline at end of file
+?>
diff --git a/lib/excel/Worksheet.php b/lib/excel/Worksheet.php
index 1eb7682..d7c5cfc 100644
--- a/lib/excel/Worksheet.php
+++ b/lib/excel/Worksheet.php
@@ -909,7 +909,7 @@ class Worksheet extends BIFFwriter
         $row     = $match[2];
     
         // Convert base26 column string to number
-        $chars = split('', $col);
+        $chars = str_split($col);
         $expn  = 0;
         $col   = 0;
     
@@ -1530,13 +1530,13 @@ class Worksheet extends BIFFwriter
         // Determine if the link contains a sheet reference and change some of the
         // parameters accordingly.
         // Split the dir name and sheet name (if it exists)
-        list($dir_long , $sheet) = split('/\#/', $url);
+        list($dir_long , $sheet) = explode('/#/', $url);
         $link_type               = 0x01 | $absolute;
     
         if (isset($sheet)) {
             $link_type |= 0x08;
             $sheet_len  = pack("V", strlen($sheet) + 0x01);
-            $sheet      = join("\0", split('', $sheet));
+            $sheet      = join("\0", str_split($sheet));
             $sheet     .= "\0\0\0";
         }
         else {
@@ -1555,7 +1555,7 @@ class Worksheet extends BIFFwriter
         $dir_short   = preg_replace('/\.\.\\/', '', $dir_long) . "\0";
     
         // Store the long dir name as a wchar string (non-null terminated)
-        $dir_long       = join("\0", split('', $dir_long));
+        $dir_long       = join("\0", str_split($dir_long));
         $dir_long       = $dir_long . "\0";
     
         // Pack the lengths of the dir strings
@@ -1644,7 +1644,7 @@ class Worksheet extends BIFFwriter
         if (defined $sheet) {
             $link_type |= 0x08;
             $sheet_len  = pack("V", length($sheet) + 0x01);
-            $sheet      = join("\0", split('', $sheet));
+            $sheet      = join("\0", str_split($sheet));
             $sheet     .= "\0\0\0";
     }
         else {
@@ -1665,7 +1665,7 @@ class Worksheet extends BIFFwriter
     
     
         # Store the long dir name as a wchar string (non-null terminated)
-        $dir_long       = join("\0", split('', $dir_long));
+        $dir_long       = join("\0", str_split($dir_long));
         $dir_long       = $dir_long . "\0";
     
     
@@ -2832,4 +2832,4 @@ class Worksheet extends BIFFwriter
         $this->_append($header.$data);
     }
 }
-?>
\ No newline at end of file
+?>



18 Nov 2009
Description of WriteExcel modifications to remove functions deprecated as of php 5.3

Index: Parser.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/excel/Parser.php,v
retrieving revision 1.1
diff -u -r1.1 Parser.php
--- Parser.php	26 Sep 2003 04:18:02 -0000	1.1
+++ Parser.php	18 Nov 2009 03:58:49 -0000
@@ -466,7 +466,7 @@
         {
         return(pack("C", $this->ptg[$token]));
         }
-    elseif(preg_match("/[A-Z0-9À-Ü\.]+/",$token))
+    elseif(preg_match("/[A-Z0-9ï¿œ-ï¿œ\.]+/",$token))
         {
         return($this->_convert_function($token,$this->_func_args));
         }
@@ -723,21 +723,21 @@
             break;
         default:
 	    // if it's a reference
-            if(eregi("^[A-I]?[A-Z][0-9]+$",$token) and 
-	       !ereg("[0-9]",$this->_lookahead) and 
+            if(preg_match("/^[A-I]?[A-Z][0-9]+$/i",$token) and
+	       !preg_match("/[0-9]/",$this->_lookahead) and
                ($this->_lookahead != ':') and ($this->_lookahead != '.'))
                 {
                 return($token);
                 }
             // if it's a range (A1:A2)
-            elseif(eregi("^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$",$token) and 
-	           !ereg("[0-9]",$this->_lookahead))
+            elseif(preg_match("/^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$/i",$token) and
+	           !preg_match("/[0-9]/",$this->_lookahead))
 	        {
 		return($token);
 		}
             // if it's a range (A1..A2)
-            elseif(eregi("^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$",$token) and 
-	           !ereg("[0-9]",$this->_lookahead))
+            elseif(preg_match("/^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$/i",$token) and
+	           !preg_match("/[0-9]/",$this->_lookahead))
 	        {
 		return($token);
 		}
@@ -746,7 +746,7 @@
                 return($token);
                 }
             // if it's a function call
-            elseif(eregi("^[A-Z0-9À-Ü\.]+$",$token) and ($this->_lookahead == "("))
+            elseif(preg_match("/^[A-Z0-9ï¿œ-ï¿œ\.]+$/i",$token) and ($this->_lookahead == "("))
 
 	        {
 		return($token);
@@ -857,15 +857,15 @@
         return($result);
         }
     // if it's a reference
-    if (eregi("^[A-I]?[A-Z][0-9]+$",$this->_current_token))
+    if (preg_match("/^[A-I]?[A-Z][0-9]+$/i",$this->_current_token))
         {
         $result = $this->_create_tree($this->_current_token, '', '');
         $this->_advance();
         return($result);
         }
     // if it's a range
-    elseif (eregi("^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$",$this->_current_token) or 
-            eregi("^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$",$this->_current_token)) 
+    elseif (preg_match("/^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$/i",$this->_current_token) or
+            preg_match("/^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$/i",$this->_current_token))
         {
         $result = $this->_current_token;
         $this->_advance();
@@ -878,7 +878,7 @@
         return($result);
         }
     // if it's a function call
-    elseif (eregi("^[A-Z0-9À-Ü\.]+$",$this->_current_token))
+    elseif (preg_match("/^[A-Z0-9ï¿œ-ï¿œ\.]+$/i",$this->_current_token))
         {
         $result = $this->_func();
         return($result);
Index: Worksheet.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/excel/Worksheet.php,v
retrieving revision 1.1
diff -u -r1.1 Worksheet.php
--- Worksheet.php	26 Sep 2003 04:18:02 -0000	1.1
+++ Worksheet.php	18 Nov 2009 03:58:50 -0000
@@ -1264,10 +1264,10 @@
         }
     
         // Strip the '=' or '@' sign at the beginning of the formula string
-        if (ereg("^=",$formula)) {
+        if (preg_match("/^=/",$formula)) {
             $formula = preg_replace("/(^=)/","",$formula);
         }
-        elseif(ereg("^@",$formula)) {
+        elseif(preg_match("/^@/",$formula)) {
             $formula = preg_replace("/(^@)/","",$formula);
         }
         else {
