<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $eid = required_param('eid', PARAM_INT);    // Entry ID

    $mode = optional_param('mode', 'approval', PARAM_ALPHA);
    $hook = optional_param('hook', 'ALL', PARAM_CLEAN);

    if (!$entry = get_record('glossary_entries', 'id', $eid)) {
        error('Entry is incorrect');
    }
    if (!$glossary = get_record('glossary', 'id', $entry->glossaryid)) {
        error('Incorrect glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        error('Course Module ID was incorrect');
    }
    if (!$course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    require_login($course, false, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/glossary:approve', $context);

    if (!$entry->approved and confirm_sesskey()) {
        $newentry = new object();
        $newentry->id           = $entry->id;
        $newentry->approved     = 1;
        $newentry->timemodified = time(); // wee need this date here to speed up recent activity, TODO: use timestamp in approved field instead in 2.0
        if (update_record("glossary_entries", $newentry)) {
            add_to_log($course->id, "glossary", "approve entry", "showentry.php?id=$cm->id&amp;eid=$eid", "$eid", $cm->id);
        }
    }

    redirect("view.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook");
    die;
?>
