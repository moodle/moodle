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
        //$entries[$key]->footer = "<p align=\"right\">&raquo;&nbsp;<a onClick=\"if (window.opener) {window.opener.location.href='$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid'; return false;} else {openpopup('/mod/glossary/view.php?g=$entry->glossaryid', 'glossary', 'menubar=1,location=1,toolbar=1,scrollbars=1,directories=1,status=1,resizable=1', 0); return false;}\" href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\" target=\"_blank\">$entry->glossaryname</a></p>";  // Could not get this to work satisfactorily in all cases  - Martin
        $entries[$key]->footer = "<p align=\"right\">&raquo;&nbsp;<a target=\"_blank\" href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">$entry->glossaryname</a></p>";
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
