<?PHP  // $Id$
    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($concept);
    optional_variable($courseid,0);
    optional_variable($eid,0);
    optional_variable($displayformat,-1);

    if ($CFG->forcelogin) {
        require_login();
    }

    if (!empty($courseid)) {
        $course = get_record("course", "id", $courseid);
        if ($course->category) {
            require_login($courseid);
        }

        $strglossaries = get_string("modulenameplural", "glossary");
        $strsearch = get_string("search");

        $CFG->framename = "newwindow";
        if ($course->category) {
            print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
            "<a target=\"newwindow\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> $strglossaries -> $strsearch", "", "", true, "&nbsp;", "&nbsp;");
        } else {
            print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
            "$strglossaries -> $strsearch", "", "", true, "&nbsp;", "&nbsp;");
        }


    } else {
        print_header();    // Needs to be something here to allow linking back to the whole glossary
    }


    if ($eid) {
        $entries[] = get_record("glossary_entries", "id", $eid);

    } else if ($concept) {
        $entries = get_records_sql("select e.* from {$CFG->prefix}glossary_entries e, {$CFG->prefix}glossary g".
                                  " where e.glossaryid = g.id and".
                                      " (e.casesensitive != 0 and ucase(concept) = '" . strtoupper(trim($concept)). "' or".
                                      " e.casesensitive = 0 and concept = '$concept') and".
                                      " (g.course = $courseid or g.globalglossary) and".
                                      " e.usedynalink != 0 and g.usedynalink != 0");
    } 

    if ($entries) {
        glossary_print_dynaentry($courseid, $entries, $displayformat);
    }    
    
    close_window_button();

?>
