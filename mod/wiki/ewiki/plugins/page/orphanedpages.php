<?php

#  lists all pages, which are not referenced from others
#  (works rather unclean and dumb)


define("EWIKI_PAGE_ORPHANEDPAGES", "OrphanedPages");
$ewiki_plugins["page"][EWIKI_PAGE_ORPHANEDPAGES] = "ewiki_page_orphanedpages";


function ewiki_page_orphanedpages($id, $data, $action) {

   global $ewiki_links;

   $o = ewiki_make_title($id, ewiki_t($id), 2);

   $pages = array();
   $refs = array();
   $orphaned = array();

   #-- read database
   $db = ewiki_database("GETALL", array("refs", "flags"));
   $n=0;
   while ($row = $db->get()) {

      $p = $row["id"];

      #-- remove self-reference
      $row["refs"] = str_replace("\n$p\n", "\n", $row["refs"]);

      #-- add to list of referenced pages
      $rf = explode("\n", trim($row["refs"]));
      $refs = array_merge($refs, $rf);
      if ($n++ > 299) {
         $refs = array_unique($refs);
         $n=0;
      } // (clean-up only every 300th loop)

      #-- add page name
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {
         $pages[] = $row["id"];
      }
   }
   $refs = array_unique($refs);

    #-- check pages to be referenced from somewhere
    foreach ($pages as $p) {    
        if (!ewiki_in_array($p, $refs)) {
            if (!EWIKI_PROTECTED_MODE || EWIKI_PROTECTED_MODE_HIDING || ewiki_auth($p, $uu, "view")) {
                $orphaned[] = $p;    
            }  
        }
    }

   #-- output
   $o .= ewiki_list_pages($orphaned, 0);

   return($o);
}


?>
