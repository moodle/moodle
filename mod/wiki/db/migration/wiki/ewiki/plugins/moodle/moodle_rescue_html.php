<?php

defined('MOODLE_INTERNAL') || die();
/*
     Can be used to allow preserving of certain "safe" HTML <tags>
     (as seen in [sfWiki | http://sfwiki.sf.net/].
     "Safe" tags include Q, S, PRE, TT, H1-H6, KBD, VAR, XMP, B, I
     but just see (or change) ewiki_format() for more. They are not
     accepted if written with mixed lowercase and uppercase letters,
     and they cannot contain any tag attributes.

     RESCUE_HTML was formerly part of the main rendering function, but
     has now been extracted into this plugin, so one only needs to
     include it to get simple html tags working.
*/


$ewiki_plugins["format_source"][] = "ewiki_moodle_rescue_html";


function ewiki_moodle_rescue_html(&$wiki_source) {
   $safe_html = EWIKI_RESCUE_HTML;
   $safe_html += 1;

   $rescue_html = array(
      "br", "tt", "b", "i", "strong", "em", "s", "kbd", "var", "xmp", "sup", "sub",
      "pre", "q", "h1", "h2", "h3", "h4", "h5", "h6", "cite", "code", "u",
   );



   #-- unescape allowed html
   if ($safe_html) {
    /*
      foreach ($rescue_html as $tag) {
         foreach(array($tag, "/$tag", ($tag=strtoupper($tag)), "/$tag") as $tag) {
            $wiki_source = str_replace('&lt;'.$tag.'&gt;', "<".$tag.">", $wiki_source);
      }  }
    */
      $regexp='#&lt;(/?('.implode("|",$rescue_html).'))( /)?&gt;#i';
      $wiki_source = preg_replace($regexp, '<$1>', $wiki_source);
   }

}

