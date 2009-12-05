<?php  // $Id$
    require_once("../../config.php");
    require_once("lib.php");

    $concept  = optional_param('concept', '', PARAM_CLEAN);
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $eid      = optional_param('eid', 0, PARAM_INT); // glossary entry id
    $displayformat = optional_param('displayformat',-1, PARAM_SAFEDIR);

    if ($CFG->forcelogin) {
        require_login();
    }

    if ($eid) {
        if (!$entry = get_record("glossary_entries", "id", $eid)) {
            error('Invalid entry id');
        }
        if (!$glossary = get_record('glossary','id',$entry->glossaryid)) {
            error('Invalid glossary id');
        }
        if (!$cm = get_coursemodule_from_instance("glossary", $glossary->id)) {
            error("Could not determine which course module this belonged to!");
        }
        if (!$course = get_record("course", "id", $cm->course)) {
            error('Invalid course id');
        }
        require_course_login($course, true, $cm);
        $entry->glossaryname = $glossary->name;
        $entry->cmid = $cm->id;
        $entry->courseid = $cm->course;
        $entries = array($entry);

    } else if ($concept) {
        if (!$course = get_record("course", "id", $courseid)) {
            error('Invalid course id');
        }
        require_course_login($course);
        $entries = glossary_get_entries_search($concept, $courseid);

    } else {
        error('No valid entry specified');
    }

    if ($entries) {
        foreach ($entries as $key => $entry) {
            // Need to get the course where the entry is,
            // in order to check for visibility/approve permissions there
            if (!$entrycourse = get_record("course", "id", $entry->courseid)) {
                error('Invalid entry course id');
            }
            $modinfo = get_fast_modinfo($entrycourse);
            // make sure the entry is visible
            if (empty($modinfo->cms[$entry->cmid]->uservisible)) {
                unset($entries[$key]);
                continue;
            }
            // make sure the entry is approved (or approvable by current user)
            if (!$entry->approved and ($USER->id != $entry->userid)) {
                $context = get_context_instance(CONTEXT_MODULE, $entry->cmid);
                if (!has_capability('mod/glossary:approve', $context)) {
                    unset($entries[$key]);
                    continue;
                }
            }
            $entries[$key]->footer = "<p style=\"text-align:right\">&raquo;&nbsp;<a href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">".format_string($entry->glossaryname,true)."</a></p>";
            add_to_log($entry->courseid, "glossary", "view entry", "showentry.php?eid=$entry->id", $entry->id, $entry->cmid);
        }
    }

    if (!empty($courseid)) {
        $strglossaries = get_string("modulenameplural", "glossary");
        $strsearch = get_string("search");

        $CFG->framename = "newwindow";
        $navlinks = array();
        $navlinks[] = array('name' => $strglossaries, 'link' => '', 'type' => 'activity');
        $navlinks[] = array('name' => $strsearch, 'link' => '', 'type' => 'title');

        $navigation = build_navigation($navlinks);

        print_header(strip_tags("$course->shortname: $strglossaries $strsearch"), $course->fullname, $navigation, "", "", true, "&nbsp;", "&nbsp;");

    } else {
        print_header();    // Needs to be something here to allow linking back to the whole glossary
    }

    if ($entries) {
        glossary_print_dynaentry($courseid, $entries, $displayformat);
    }

    close_window_button();

/// Show one reduced footer
    print_footer('none');

?>
