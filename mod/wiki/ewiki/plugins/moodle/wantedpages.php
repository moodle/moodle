<?php // $Id$

# lists pages, which were referenced
# but not yet written


$ewiki_plugins["page"]["WantedPages"] = "ewiki_page_wantedpages";
#<off># $ewiki_plugins["page"]["DanglingSymlinks"] = "ewiki_page_wantedpages";


function ewiki_page_wantedpages($id, $data, $action) {
    $wanted=array();
    #-- collect referenced pages
    $result = ewiki_database("GETALL", array("refs"));
    while ($row = $result->get()) {
        if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($row["id"], $uu, "view")) {
            continue;
        }   
        $refs .= $row["refs"];
    }

   #-- build array
   $refs = array_unique(explode("\n", $refs));

   #-- strip existing pages from array
   $refs = ewiki_database("FIND", $refs);
    foreach ($refs as $id=>$exists) {
        if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($row["id"], $uu, "view")) {
            continue;
        }   
        if (!$exists && !strstr($id, "://") && strlen(trim($id))) {
            $wanted[] = $id;
        }
    }

   #-- print out
   $o .= "<ul>";   
   foreach ($wanted as $page) {

      $link = ewiki_link_regex_callback(array($page, $page));

      if (strstr($link, "?</a>")) {
         $o .= "<li>" . $link . "</li>";
      }

   }
   $o .= "<ul>";

   return($o);
}


?>
