Description of PCLZIP v2.5 library import into Moodle

Changes:
 * removed verbose error output from PCLZIP_ERR_DIRECTORY_RESTRICTION error
 * removed PclZipUtilTranslateWinPath from line 1958 - see MDL-7828

skodak
$Id$


18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: pclzip.lib.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/pclzip/Attic/pclzip.lib.php,v
retrieving revision 1.15
diff -u -r1.15 pclzip.lib.php
--- pclzip.lib.php	28 Mar 2007 07:17:26 -0000	1.15
+++ pclzip.lib.php	18 Nov 2009 06:40:48 -0000
@@ -3245,7 +3245,9 @@
       }
 
       // ----- Look for extract by ereg rule
-      else if (   (isset($p_options[PCLZIP_OPT_BY_EREG]))
+      /*
+       * MDL-20821 ereg is now deprecated
+       else if (   (isset($p_options[PCLZIP_OPT_BY_EREG]))
                && ($p_options[PCLZIP_OPT_BY_EREG] != "")) {
           //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract by ereg '".$p_options[PCLZIP_OPT_BY_EREG]."'");
 
@@ -3253,7 +3255,7 @@
               //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Filename match the regular expression");
               $v_extract = true;
           }
-      }
+      }*/
 
       // ----- Look for extract by preg rule
       else if (   (isset($p_options[PCLZIP_OPT_BY_PREG]))
@@ -4684,6 +4686,8 @@
       }
 
       // ----- Look for extract by ereg rule
+      /*
+       * MDL-20821 ereg is now deprecated
       else if (   (isset($p_options[PCLZIP_OPT_BY_EREG]))
                && ($p_options[PCLZIP_OPT_BY_EREG] != "")) {
           //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract by ereg '".$p_options[PCLZIP_OPT_BY_EREG]."'");
@@ -4692,7 +4696,7 @@
               //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Filename match the regular expression");
               $v_found = true;
           }
-      }
+      }*/
 
       // ----- Look for extract by preg rule
       else if (   (isset($p_options[PCLZIP_OPT_BY_PREG]))
