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

    if ($eid) {
        $entry = get_record("glossary_entries", "id", $eid);
        $glossary = get_record('glossary','id',$entry->glossaryid);
        $entry->glossaryname = $glossary->name;
        $entries[] = $entry;

    } else if ($concept) {
        $entries = glossary_get_entries_search($concept, $courseid);
    }

    foreach ($entries as $key => $entry) {
        $entries[$key]->definition .= "<p align=\"right\">&raquo;&nbsp;<a target=\"_blank\" onClick=\"window.opener.location.href='$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid'\" href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">$entry->glossaryname</a></p>";
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
            print_header(strip_tags("$course->shortname: $strglossaries $strsearch"), "$course->fullname",
            "<a target=\"newwindow\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> $strglossaries -> $strsearch", "", "", true, "&nbsp;", "&nbsp;");
        } else {
            print_header(strip_tags("$course->shortname: $strglossaries $strsearch"), "$course->fullname",
            "$strglossaries -> $strsearch", "", "", true, "&nbsp;", "&nbsp;");
        }

    } else {
        print_header();    // Needs to be something here to allow linking back to the whole glossary
    }

    if ($entries) {
        glossary_print_dynaentry($courseid, $entries, $displayformat);
    }    
    
    close_window_button();

?>
