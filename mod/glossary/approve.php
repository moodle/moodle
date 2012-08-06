<?php

require_once("../../config.php");
require_once("lib.php");

$eid = required_param('eid', PARAM_INT);    // Entry ID

$mode = optional_param('mode', 'approval', PARAM_ALPHA);
$hook = optional_param('hook', 'ALL', PARAM_CLEAN);

$url = new moodle_url('/mod/glossary/approve.php', array('eid'=>$eid,'mode'=>$mode, 'hook'=>$hook));
$PAGE->set_url($url);

$entry = $DB->get_record('glossary_entries', array('id'=> $eid), '*', MUST_EXIST);
$glossary = $DB->get_record('glossary', array('id'=> $entry->glossaryid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('glossary', $glossary->id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=> $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/glossary:approve', $context);

if (!$entry->approved and confirm_sesskey()) {
    $newentry = new stdClass();
    $newentry->id           = $entry->id;
    $newentry->approved     = 1;
    $newentry->timemodified = time(); // wee need this date here to speed up recent activity, TODO: use timestamp in approved field instead in 2.0
    $DB->update_record("glossary_entries", $newentry);

    // Update completion state
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && $glossary->completionentries) {
        $completion->update_state($cm, COMPLETION_COMPLETE, $entry->userid);
    }

    add_to_log($course->id, "glossary", "approve entry", "showentry.php?id=$cm->id&amp;eid=$eid", "$eid", $cm->id);
}

redirect("view.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook");
