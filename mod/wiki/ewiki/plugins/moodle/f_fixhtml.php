<?php // $Id$

/*
   This filter plugin implements minimal html tag balancing, and can also
   convert ewiki_page() output into (hopefully) valid xhtml. It just works
   around some markup problems found in ewiki and that may arise from Wiki
   markup abuse; it however provides no fix for <ul> inside <ul> or even
   <h2> inside <p> problems (this should rather be fixed in the ewiki_format
   function).  So following code is not meant to fix any possible html file,
   and it certainly won't make valid html files out of random binary data. 
   So for full html spec conformance you should rather utilize w3c tidy (by
   using your Webservers "Filter" directive).
*/


define("EWIKI_XHTML", 1);
$ewiki_plugins["page_final"][] = "ewiki_html_tag_balancer";


function ewiki_html_tag_balancer(&$html) {

   #-- vars
   $html_standalone = array(
      "img", "br", "hr",
      "input", "meta", "link",
   );
   $html_tags = array(
      "a", "abbr", "acronym", "address", "applet", "area", "b", "base",
      "basefont", "bdo", "big", "blockquote", "body", "br", "button",
      "caption", "center", "cite", "code", "col", "colgroup", "dd", "del",
      "dfn", "dir", "div", "dl", "dt", "em", "fieldset", "font", "form",
      "h1", "h2", "h3", "h4", "h5", "h6", "head", "hr", "html", "i",
      "iframe", "img", "input", "ins", "kbd", "label", "legend", "li",
      "link", "map", "menu", "meta", "noframes", "noscript", "object", "ol",
      "optgroup", "option", "p", "param", "pre", "q", "s", "samp", "script",
      "select", "small", "span", "strike", "strong", "style", "sub", "sup",
      "table", "tbody", "td", "textarea", "tfoot", "th", "thead", "title",
      "tr", "tt", "u", "ul", "var",
      #-- H2.0  "nextid", "listing", "xmp", "plaintext",
      #-- H3.2  "frame", "frameset",
      #-- X1.1  "rb", "rbc", "rp", "rt", "rtc", "ruby",
   );
   $close_opened_when = array(
      "p", "div", "ul", "td", "table", "tr",
   );
   if (!EWIKI_XHTML) {
      $html_tags = array_merge(  (array) $html_tags, array(
         "bgsound", "embed", "layer", "multicol", "nobr", "noembed",
      ));
   }

   #-- walk through all tags
   $tree = array();
   $len = strlen($html);
   $done = "";
   $pos = 0;
   $loop = (int)$len / 3;
   while (($pos < $len) && $loop--) {

      #-- search next tag
      $l = strpos($html, "<", $pos);
      $r = strpos($html, ">", $l);
      if (($l===false) or ($r===false)) {
         # finish
         $done .= substr($html, $pos);
         break;
      }

      #-- copy plain text part
      if ($l >= $pos) {
         $done .= substr($html, $pos, $l-$pos);
         $pos = $l;
      }

      #-- analyze current html tag
      if ($r >= $pos) {
         $pos = $r + 1;
         $tag = substr($html, $l + 1, $r - $l - 1);

         #-- split into name and attributes
         $tname = strtolower(strtok($tag, " \t\n>"));     // LOWERCASING not needed here really
         ($tattr = strtok(">")) && ($tattr = " $tattr");

         // attribute checking could go here
         // (here we just assume good output from ewiki core)
         // ...

         #-- html comment
         if (substr($tname, 0, 3) == "!--") {
            $r = strpos($html, "-->", $l+4);
            $pos = $r + 3;
            $done .= substr($html, $l, $r-$l+3);
            continue;
         }

         #-- opening tag?
         elseif ($tname[0] != "/") {

            #-- cdata
            if($tname=='![cdata[') {
                $tname = strtoupper($tname); // Needs to be uppercase for XHTML compliance
                // LEAVE THE POOR THING ALONE!
            }
            #-- standalone tag
            else if (in_array($tname, $html_standalone)) {
               $tattr = rtrim(rtrim($tattr, "/"));
               if (EWIKI_XHTML) {
                  $tattr .= " /";
               }
            }
            #-- normal tag
            else {
               if (in_array($tname, $html_tags)) {
                  #-- ok
               }
               else {
                  #$tattr .= " class=\"$tname\"";
                  #$tname = "div";
               }
               array_push($tree, $tname);
            }

            $tag = "$tname$tattr";
         }
         #-- closing tag
         else {
            $tname = substr($tname, 1);

            if (!in_array($tname, $html_tags)) {
               $tname= "div";
            }

            #-- check if this is allowed
            if (!$tree) {
               continue;   // ignore closing tag
            }
            $last = array_pop($tree);
            if ($last != $tname) {

               #-- close until last opened block element
               if (in_array($tname, $close_opened_when)) {
                  do {
                     $done .= "</$last>";
                  }
                  while (($last = array_pop($tree)) && ($last!=$tname));
               }
               #-- close last, close current, reopen last
               else {
                  array_push($tree, $last);
                  $done .= "</$last></$tname><$last>";
                  continue;
               }
            }
            else {
               #-- all ok
            }

            #-- readd closing-slash to tag name
            $tag = "/$tname";
         }

         $done .= "<$tag>";
      }
   }

   #-- close still open tags
   while ($tree && ($last = array_pop($tree))) {
      $done .= "</$last>";
   }

   #-- copy back changes
   $html = $done;

}


?>
