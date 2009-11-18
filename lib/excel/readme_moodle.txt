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
