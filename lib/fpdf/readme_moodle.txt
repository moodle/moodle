18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: font/makefont/makefont.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/fpdf/font/makefont/Attic/makefont.php,v
retrieving revision 1.2
diff -u -r1.2 makefont.php
--- font/makefont/makefont.php	16 May 2006 06:45:14 -0000	1.2
+++ font/makefont/makefont.php	18 Nov 2009 06:32:46 -0000
@@ -171,7 +171,7 @@
     //StemV
     if(isset($fm['StdVW']))
         $stemv=$fm['StdVW'];
-    elseif(isset($fm['Weight']) and eregi('(bold|black)',$fm['Weight']))
+    elseif(isset($fm['Weight']) and preg_match('/(bold|black)/i',$fm['Weight']))
         $stemv=120;
     else
         $stemv=70;
