<?php

#  this plugin appends the list of uploaded attachments at the bottom of
#  each page, the downloads / attachments plugin must be loaded too
#
#  you could alternatively define EWIKI_AUTOVIEW to 0, and call the
#  ewiki_attachments() wrapper function anywhere on yoursite.php


if (!defined("EWIKI_AUTOVIEW") || !EWIKI_AUTOVIEW) {
   $ewiki_plugins["view_append"][] = "ewiki_view_append_attachments";
}


$ewiki_t["en"]["ATTACHMENTS"] = "attachments";
$ewiki_t["de"]["ATTACHMENTS"] = "Anhänge";



function ewiki_view_append_attachments($id, $data, $action) {

   $o = '<hr /><h4><a href="' . ewiki_script(EWIKI_ACTION_ATTACHMENTS, $id) .
        '">' . ewiki_t("ATTACHMENTS") . '</a></h4>';

   $scan = 's:7:"section";' . serialize($id);
   $result = ewiki_database("SEARCH", array("meta" => $scan));

#### BEGIN MOODLE CHANGES - show attachments link only if there are attachments.
####                      - don't show the attachments on the content page.
    if (count($result->entries) <= 0) {
        $o = '';
    }
//   $ord = array();
//   while ($row = $result->get()) {
//      $ord[$row["id"]] = $row["created"];
//   }
//   arsort($ord);
//
//    foreach ($ord as $id => $uu) {    
//        $row = ewiki_database("GET", array("id"=>$id));
//        if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($row["id"], $row, "view")) {
//            continue;
//        }           
//        $o .= ewiki_entry_downloads($row, "*");    
//    }
#### END MOODLE CHANGES

   return($o);
}




function ewiki_attachments() {
   global $ewiki_title, $ewiki_id;
   return(ewiki_view_append_attachments($ewiki_title, array("id"=>$ewiki_id), "view"));
}



?>
