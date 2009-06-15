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
