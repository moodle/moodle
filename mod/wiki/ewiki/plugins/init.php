<?php

/*
   This plugin is used as SetupWizard and initializes the database with
   the distributed default pages from the ./init-pages directory. It
   gives some configuration advice, when it thinks this is necessary.

   You need this plugin to run only once (when you first run the Wiki),
   afterwards you can and should comment out the include() directive which
   enabled it.   
*/


$ewiki_plugins["handler"][-125] = "ewiki_initialization_wizard";
$ewiki_plugins["page_init"][] = "ewiki_initialization_wizard2";


function ewiki_initialization_wizard2($id, &$data, $action) {
   global $ewiki_plugins;

   #-- disable the default handler
   unset($ewiki_plugins["handler"][-105]);
}

function ewiki_initialization_wizard($id, &$data, &$action) {

   global $ewiki_plugins;

   #-- proceed only if frontpage missing or explicetely requested
   if ((strtolower($id)=="wikisetupwizard") || ($id==EWIKI_PAGE_INDEX) && ($action=="edit") && empty($data["version"]) && !($_REQUEST["abort"])) {

      if ($_REQUEST["abort"]) {
      }

      #-- first print some what-would-we-do-stats
      elseif (empty($_REQUEST["init"])) {

         $o = "<h2>WikiSetupWizard</h2>\n";
         $o .= "You don't have any pages in your Wiki yet, so we should try to read-in the default ones from <tt>init-pages/</tt> now.<br /><br />";

         $o .= '<a href="'.ewiki_script("",$id,array("init"=>"now")).'">[InitializeWikiDatabase]</a>';
         $o .= " &nbsp; ";
         $o .= '<a href="'.ewiki_script("",$id,array("abort"=>"this")).'">[NoThanks]</a>';
         $o .= "<br /><br />";

         #-- analyze and print settings and misconfigurations
         $pf_db = $ewiki_plugins["database"][0];
         $db = substr($pf_db, strrpos($pf_db, "_") + 1);
         $o .= '<table border="0" width="90%" class="diagnosis">';
         $o .= '<tr><td>DatabaseBackend</td><td>';
         $o .= "<b>" . $db . "</b><br />";
         if ($db == "files") {
            $o .= "<small>_DBFILES_DIR='</small><tt>" . EWIKI_DBFILES_DIRECTORY . "'</tt>";
            if (strpos(EWIKI_DBFILES_DIRECTORY, "tmp")) {
               $o .= "<br /><b>Warning</b>: Storing your pages into a temporary directory is not what you want (there they would get deleted randomly), except for testing purposes of course. See the README.";
            }
         }
         else {
            $o .= "(looks ok)";
         }
         $o .= "</td></tr>";

         $o .= '<tr><td>WikiSoftware</td><td>ewiki '.EWIKI_VERSION."</td></tr>";
         $o .= "</table>";

         #-- more diagnosis 
         if (ini_get("magic_quotes")) {
            $o.= "<b>Warning</b>: Your PHP interpreter has enabled the ugly and outdated '<i>magic_quotes</i>'. This will lead to problems, so please ask your provider to correct it; or fix it yourself with .htaccess settings as documented in the README. Otherwise don't forget to include() the <tt>fragments/strip_wonderful_slashes.php</tt> (it's ok to proceed for the moment).<br /><br />";
         }
         if (ini_get("register_globals")) {
            $o.= "<b>Security warning</b>: The horrible '<i>register_globals</i>' setting is enabled. Without always using <tt>fragments/strike_register_globals.php</tt> or letting your provider fix that, you could get into trouble some day.<br /><br />";
         }

         return('<div class="wiki view WikiSetupWizard">' . $o . '</div>');
      }


      #-- actually initialize the database
      else {
         ewiki_database("INIT", array());
         if ($dh = @opendir($path=EWIKI_INIT_PAGES)) {
            while (false !== ($filename = readdir($dh))) {
               if (preg_match('/^(['.EWIKI_CHARS_U.']+['.EWIKI_CHARS_L.']+\w*)+/', $filename)) {
                  $found = ewiki_database("FIND", array($filename));
                  if (! $found[$filename]) {
                     $content = implode("", file("$path/$filename"));
                     ewiki_scan_wikiwords($content, $ewiki_links, "_STRIP_EMAIL=1");
                     $refs = "\n\n" . implode("\n", array_keys($ewiki_links)) . "\n\n";
                     $save = array(
                        "id" => "$filename",
                        "version" => "1",
                        "flags" => "1",
                        "content" => $content,
                        "author" => ewiki_author("ewiki_initialize"),
                        "refs" => $refs,
                        "lastmodified" => filemtime("$path/$filename"),
                        "created" => filectime("$path/$filename")   // (not exact)
                     );
                     ewiki_database("WRITE", $save);
                  }
               }
            }
            closedir($dh);
         }
         else {
            return("<b>ewiki error</b>: could not read from directory ". realpath($path) ."<br />\n");
         }

         #-- try to view/ that newly inserted page
         if ($data = ewiki_database("GET", array("id"=>$id))) {
            $action = "view";
         }

         #-- let ewiki_page() proceed as usual
         return("");
      }
   }

}


?>