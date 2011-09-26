html2text.php is an unmodified copy of a file shipped with the RoundCube project:

  http://trac.roundcube.net/log/trunk/roundcubemail/program/lib/html2text.php

 -- Francois Marier <francois@catalyst.net.nz>  2009-05-22


Modifications
--------------

1- Don't just strip images, replace them with their alt text.

index b7e3e3e..96ef508 100644
--- a/lib/html2text.php
+++ b/lib/html2text.php
@@ -237,6 +237,7 @@ class html2text
         '/<(a) [^>]*href=("|\')([^"\']+)\2[^>]*>(.*?)<\/a>/i',
                                                    // <a href="">
         '/<(th)[^>]*>(.*?)<\/th>/i',               // <th> and </th>
+        '/<(img)[^>]*alt=\"([^>"]+)\"[^>]*>/i',    // <img> with alt
     );

    /**
@@ -574,6 +575,8 @@ class html2text
             return $this->_strtoupper("\n\n". $matches[2] ."\n\n");
         case 'a':
             return $this->_build_link_list($matches[3], $matches[4]);
+        case 'img':
+            return '[' . $matches[2] . ']';
         }
     }

-- Tim Hunt 2010-08-04

2- No strip slashes, we do not use magic quotes any more in Moodle 2.0 or later

3- Use textlib, not crappy functions that break UTF-8, in the _strtoupper method.

Index: lib/html2text.php
--- lib/html2text.php   2 Sep 2010 12:49:29 -0000   1.16
+++ lib/html2text.php   2 Nov 2010 19:57:09 -0000
@@ -580,9 +580,7 @@
      */
     function _strtoupper($str)
     {
-        if (function_exists('mb_strtoupper'))
-            return mb_strtoupper($str);
-        else
-            return strtoupper($str);
+        $tl = textlib_get_instance();
+        return $tl->strtoupper($str);
     }
 }

-- Tim Hunt 2010-11-02

4 - Make sure html2text does not destroy '0'.

index e2d0dff..9cc213d 100644
--- a/lib/html2text.php
+++ b/lib/html2text.php
@@ -335,7 +335,7 @@ class html2text
      */
     function html2text( $source = '', $from_file = false, $do_links = true, $wi     {
-        if ( !empty($source) ) {
+        if ($source !== '') {
             $this->set_html($source, $from_file);
         }

-- Tim Hunt 2011-09-21
