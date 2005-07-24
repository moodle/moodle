<?php // $Id$

/*
   CSS-highlights the terms used as search patterns. This is done
   by evaluating the REFERRER and using the QUERY_STRINGs "q="
   parameter (which is used by Google and ewikis` PowerSearch).

   Highlighting color should be controlled from CSS:

     em.highlight {
          color: red;
     }
   
     em.marker {
          background: yellow;
     }

   Using this plugin costs you nearly nothing (not slower), because
   there most often isn't a "?q=" from a search engine in the referer
   url.
*/



$ewiki_plugins["page_final"][] = "ewiki_moodle_highlight";


function ewiki_moodle_highlight(&$o, &$id, &$data, &$action) {

   if (strpos($_SERVER["HTTP_REFERER"], "q=")) {

      #-- PHP versions
      $stripos = function_exists("stripos") ? "stripos" : "strpos";

      #-- get ?q=...
      $uu = $_SERVER["HTTP_REFERER"];
      $uu = substr($uu, strpos($uu, "?"));
      parse_str($uu, $q);
      if ($q = $q["q"]) {

         #-- get words out of it
         $q = preg_replace('/[^-_\d'.EWIKI_CHARS_L.EWIKI_CHARS_U.']+/', " ", $q);
         $q = array_unique(explode(" ", $q));
         #-- walk through words            
         foreach ($q as $word) {

            if (empty($word)) {
               continue;
            }

            #-- search for word
            while ($l = $stripos(strtolower($o), strtolower($word), $l)) {

               #-- check for html-tags
               $t0 = strpos($o, "<", $l);
               $t1 = strpos($o, ">", $l);
               if ((!$t0) || ($t0 < $t1)) {

                  $repl = '<em class="highlight marker">' . $word . '</em>';
                  $o = substr($o, 0, $l)
                     . $repl
                     . substr($o, 1 + $l + strlen($word)-1);

                  $l += strlen($repl);
               }

               $l++;   // advance strpos
            }

         } // foreach(word)

      }

   } // if(q)

} // func


?>
