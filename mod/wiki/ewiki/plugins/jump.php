<?php

/*
   This plugin adds a page redirection feature. ewiki instantly switches
   to another page, when one of the following markup snippets is found:

      [jump:AnotherPage]
      [goto:SwitchToHere]
   or
      [jump:WardsWiki:WelcomeVisitors]
      [jump:Google:ErfurtWiki:MarioSalzer]
      [jump:http://www.heise.de/]

   One can also use [redirect:] or [location:]. Page switching only occours
   with the "view" action. Sending a HTTP redirect is the default, but in
   place redirects are also possible.
   There exists a loop protection, which limits redirects to 5 (for browsers
   that cannot detect this themselfes).
*/

#-- config 
define("EWIKI_JUMP_HTTP", 1);       #-- issue a HTTP redirect, or jump in place
define("EWIKI_UP_REDIRECT_COUNT", "redir");

#-- text
$ewiki_t["en"]["REDIRECTION_LOOP"] = "<h2>Redirection loop detected<h2>\nOperation stopped, because we're traped in an infinite redirection loop with page \$id.";

#-- plugin glue 
$ewiki_plugins["handler"][] = "ewiki_handler_jump";
$ewiki_config["interwiki"]["jump"] = "";
$ewiki_config["interwiki"]["goto"] = "";


function ewiki_handler_jump(&$id, &$data, &$action) {

   global $ewiki_config;

   static $redirect_count = 5;
   $jump_markup = array("jump", "goto", "redirect", "location");

   #-- we only care about "view" action
   if ($action != "view") {
      return;
   }

   #-- escape from loop
   if (isset($_REQUEST["EWIKI_UP_REDIRECT_COUNT"])) {
      $redirect_count = $_REQUEST["EWIKI_UP_REDIRECT_COUNT"];
   }
   if ($redirect_count-- <= 0) {
      return(ewiki_t("REDIRECTION_LOOP", array("id"=>$id)));
   }

   #-- search for [jump:...]
   if ($links = explode("\n", trim($data["refs"])))
   foreach ($links as $link) {

      if (strlen($link) && strpos($link, ":")
      && in_array(strtolower(strtok($link, ":")), $jump_markup)
      && ($dest = trim(strtok("\n"))) )
      {
         $url = "";
         if (strpos($dest, "://")) {
            $url = $dest;
         }
         else {
            $url = ewiki_interwiki($dest);
         }

         #-- Location:
         if (EWIKI_JUMP_HTTP && EWIKI_HTTP_HEADERS && !headers_sent()) {

            if (empty($url)) {
               $url = ewiki_script("", $dest,
                  array(EWIKI_UP_REDIRECT_COUNT=>$redirect_count),
                  0, 0, ewiki_script_url()
               );
            }
            header("Location: $url");
            die();

         }
         #-- show page as usual, what will reveal dest URL
         elseif ($url) {
            return("");
            # the rendering kernel will just show up the [jump:]!
            # (without the jump: of course)
         }
         #-- it's simply about another WikiPage
         else {

            #-- we'll just restart ewiki
            $data = array();
            $id = $dest;
            return(ewiki_page("view/".$id));
         }
      }
   }#-search
}


?>