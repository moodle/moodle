<?php

 /*

   this plugin introduces markup for footnotes, use it like:

      ...
      some very scientific sentence {{this is a footnote explaination}}
      ...

   this may be useful in some rare cases; usually one should create
   a WikiLink to explain a more complex task on another page;
   your decision

*/  



$ewiki_plugins["format_source"][] = "ewiki_format_source_footnotes";



function ewiki_format_source_footnotes (&$source) {

   $notenum = 0;

   $l = 0;
   while (
            ($l = strpos($source, "{{", $l))
        &&  ($r = strpos($source, "}}", $l))
         )
   {
      $l += 2;

      #-- skip "{{...\n...}}"
      if (strpos($source, "\n", $l) < $r) {
         continue;
      }

      $notenum++;

      #-- extract "footnote"
      $footnote = substr($source, $l, $r - $l);

      #-- strip "{{footnote}}"
      $source = substr($source, 0, $l - 2)
             . "<a href=\"#fn$notenum\">·$notenum</a>"
             . substr($source, $r + 2);

      #-- add "footnote" to the end of the wiki page source
      if ($notenum==1) {
         $source .= "\n----";
      }
      $source .= "\n" .
                 "<a name=\"fn$notenum\">·$notenum</a> ". $footnote . "\n<br />";
      
   }
}


?>