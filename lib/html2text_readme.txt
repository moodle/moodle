html2text.php is a modified copy of a file shipped with the RoundCube project:

  http://trac.roundcube.net/log/trunk/roundcubemail/program/lib/html2text.php


Modifications
--------------

1- fix for these warnings in cron:

  "html_entity_decode bug - cannot yet handle MBCS in html_entity_decode()!"

by using this code:

  $tl=textlib_get_instance();
  $text = $tl->entities_to_utf8($text, true);

instead of:

  $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');


2- fixed error in preg_replace_callback on php4

--- a/lib/html2text.php
+++ b/lib/html2text.php
@@ -468,7 +468,7 @@ class html2text
 
         // Run our defined search-and-replace
         $text = preg_replace($this->search, $this->replace, $text);
-        $text = preg_replace_callback($this->callback_search, array('html2text', '_preg_callback'), $text);
+        $text = preg_replace_callback($this->callback_search, array(&$this, '_preg_callback'), $text);
 
         // Replace known html entities
         $text = utf8_encode(html_entity_decode($text));


 -- Francois Marier <francois@catalyst.net.nz>  2009-05-22

 
2- Don't just strip images, replace them with their alt text.

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
