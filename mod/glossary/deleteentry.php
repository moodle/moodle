<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id      = required_param('id', PARAM_INT);          // course module ID
    $confirm = optional_param('confirm', 0, PARAM_INT);  // commit the operation?
    $entry   = optional_param('entry', 0, PARAM_INT);    // entry id

    $prevmode = required_param('prevmode');
    $hook = optional_param('hook', '', PARAM_CLEAN);

    $strglossary = get_string("modulename", "glossary");
    $strglossaries = get_string("modulenameplural", "glossary");
    $stredit = get_string("edit");
    $entrydeleted = get_string("entrydeleted","glossary");


    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
        print_error("invalidcoursemodule");
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    if (! $entry = $DB->get_record("glossary_entries", array("id"=>$entry))) {
        print_error('invalidentry');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $manageentries = has_capability('mod/glossary:manageentries', $context); 
    
    if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
        print_error('invalidid', 'glossary');
    }


    $strareyousuredelete = get_string("areyousuredelete","glossary");

    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($glossary->name), "", $navigation,
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary),
                  navmenu($course, $cm));

    if (($entry->userid != $USER->id) and !$manageentries) { // guest id is never matched, no need for special check here
        print_error('nopermissiontodelentry');
    }
    $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
    if (!$ineditperiod and !$manageentries) {
        print_error('errdeltimeexpired', 'glossary');
    }

/// If data submitted, then process and store.

    if ($confirm) { // the operation was confirmed.
        // if it is an imported entry, just delete the relation

        if ( $entry->sourceglossaryid ) {
            $dbentry = new stdClass;
            $dbentry->id = $entry->id;
            $dbentry->glossaryid = $entry->sourceglossaryid;
            $dbentry->sourceglossaryid = 0;
            if (! $DB->update_record('glossary_entries', $dbentry)) {
                print_error('cantupdateglossary', 'glossary');
            }

        } else {
            if ( $entry->attachment ) {
                glossary_delete_old_attachments($entry);
            }
            $DB->delete_records("glossary_comments", array("entryid"=>$entry->id));
            $DB->delete_records("glossary_alias", array("entryid"=>$entry->id));
            $DB->delete_records("glossary_ratings", array("entryid"=>$entry->id));
            $DB->delete_records("glossary_entries", array("id"=>$entry->id));
        }

        add_to_log($course->id, "glossary", "delete entry", "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook", $entry->id,$cm->id);
        redirect("view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook", $entrydeleted);

    } else {        // the operation has not been confirmed yet so ask the user to do so

        notice_yesno("<b>".format_string($entry->concept)."</b><p>$strareyousuredelete</p>",
                      "deleteentry.php?id=$cm->id&amp;mode=delete&amp;confirm=1&amp;entry=".s($entry->id)."&amp;prevmode=$prevmode&amp;hook=$hook",
                      "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook");

    }

    print_footer($course);

?>
