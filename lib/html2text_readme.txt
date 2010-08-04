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