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

    // to prevent empty <ul></ul> getting printed out, we have to interate twice.
    // once to make sure the <ul></ul> is needed at all. 
    // MDL-7861, <ul></ul> does not validate.
   
    $printul = false; 
    foreach ($wanted as $page) {
        $link = ewiki_link_regex_callback(array($page, $page));
        if (strstr($link, "?</a>")) {
            $printul = true;
        }
    }
    #-- print out
   
    if ($printul) {
        $o .= "<ul>";   
        foreach ($wanted as $page) {

            $link = ewiki_link_regex_callback(array($page, $page));

            if (strstr($link, "?</a>")) {
                $o .= "<li>" . $link . "</li>";
            }

        }
        $o .= "</ul>";
    }
    return($o);
}

?>
