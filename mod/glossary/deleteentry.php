<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // course module ID
    optional_variable($confirm);  // commit the operation?
    optional_variable($entry);  // entry id

    $prevmode = required_param('prevmode');
    $hook = optional_param('hook');

    $strglossary = get_string("modulename", "glossary");
    $strglossaries = get_string("modulenameplural", "glossary");
    $stredit = get_string("edit");
    $entrydeleted = get_string("entrydeleted","glossary");


    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $entry = get_record("glossary_entries","id", $entry)) {
        error("Entry ID was incorrect");
    }

    require_login($course->id, false, $cm);

    if (isguest()) {
        error("Guests are not allowed to edit or delete entries", $_SERVER["HTTP_REFERER"]);
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Glossary is incorrect");
    }

    if (!isteacher($course->id) and !$glossary->studentcanpost ) {
        error("You are not allowed to edit or delete entries");
    }

    $strareyousuredelete = get_string("areyousuredelete","glossary");

    print_header_simple(format_string($glossary->name), "",
                 "<a href=\"index.php?id=$course->id\">$strglossaries</a> -> ".format_string($glossary->name),
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary),
                  navmenu($course, $cm));


    if (($entry->userid != $USER->id) and !isteacher($course->id)) {
        error("You can't delete other people's entries!");
    }
    $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
    if (!$ineditperiod and !isteacher($course->id)) {
        error("You can't delete this. Time expired!");
    }

/// If data submitted, then process and store.

    if ($confirm) { // the operation was confirmed.
        // if it is an imported entry, just delete the relation

        if ( $entry->sourceglossaryid ) {
            $dbentry = new stdClass;
            $dbentry->id = $entry->id;
            $dbentry->glossaryid = $entry->sourceglossaryid;
            $dbentry->sourceglossaryid = 0;
            if (! update_record('glossary_entries', $dbentry)) {
                error("Could not update your glossary");
            }

        } else {
            if ( $entry->attachment ) {
                glossary_delete_old_attachments($entry);
            }
            delete_records("glossary_comments", "entryid",$entry->id);
            delete_records("glossary_alias", "entryid", $entry->id);
            delete_records("glossary_ratings", "entryid", $entry->id);
            delete_records("glossary_entries","id", $entry->id);
        }

        add_to_log($course->id, "glossary", "delete entry", "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook", $entry->id,$cm->id);
        redirect("view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook", $entrydeleted);

    } else {        // the operation has not been confirmed yet so ask the user to do so

        notice_yesno("<b>$entry->concept</b><p>$strareyousuredelete</p>",
                      "deleteentry.php?id=$cm->id&amp;mode=delete&amp;confirm=1&amp;entry=".s($entry->id)."&amp;prevmode=$prevmode&amp;hook=$hook",
                      "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook");

    }

    print_footer($course);

?>
