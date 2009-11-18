18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: html2pdf.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/tcpdf/Attic/html2pdf.php,v
retrieving revision 1.1
diff -u -r1.1 html2pdf.php
--- html2pdf.php	2 Feb 2007 18:38:05 -0000	1.1
+++ html2pdf.php	18 Nov 2009 06:51:34 -0000
@@ -1,5 +1,5 @@
 <?php
-//HTML2PDF by Clément Lavoillotte
+//HTML2PDF by Clï¿œment Lavoillotte
 //ac.lavoillotte@noos.fr
 //webmaster@streetpc.tk
 //http://www.streetpc.tk
@@ -91,7 +91,7 @@
 				$tag=strtoupper(array_shift($a2));
 				$attr=array();
 				foreach($a2 as $v)
-					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
+					if(preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/',$v,$a3))
 						$attr[strtoupper($a3[1])]=$a3[2];
 				$this->OpenTag($tag,$attr);
 			}
