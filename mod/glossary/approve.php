<?php

require_once("../../config.php");
require_once("lib.php");

$eid = required_param('eid', PARAM_INT);    // Entry ID

$newstate = optional_param('newstate', 1, PARAM_BOOL);
$mode = optional_param('mode', 'approval', PARAM_ALPHA);
$hook = optional_param('hook', 'ALL', PARAM_CLEAN);

$url = new moodle_url('/mod/glossary/approve.php', array('eid' => $eid, 'mode' => $mode, 'hook' => $hook, 'newstate' => $newstate));
$PAGE->set_url($url);

$entry = $DB->get_record('glossary_entries', array('id'=> $eid), '*', MUST_EXIST);
$glossary = $DB->get_record('glossary', array('id'=> $entry->glossaryid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('glossary', $glossary->id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=> $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/glossary:approve', $context);

if (($newstate != $entry->approved) && confirm_sesskey()) {
    $newentry = new stdClass();
    $newentry->id           = $entry->id;
    $newentry->approved     = $newstate;
    $newentry->timemodified = time(); // wee need this date here to speed up recent activity, TODO: use timestamp in approved field instead in 2.0
    $DB->update_record("glossary_entries", $newentry);

    // Trigger event about entry approval/disapproval.
    $params = array(
        'context' => $context,
        'objectid' => $entry->id
    );
    if ($newstate) {
        $event = \mod_glossary\event\entry_approved::create($params);
    } else {
        $event = \mod_glossary\event\entry_disapproved::create($params);
    }
    $entry->approved = $newstate ? 1 : 0;
    $entry->timemodified = $newentry->timemodified;
    $event->add_record_snapshot('glossary_entries', $entry);
    $event->trigger();

    // Update completion state
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && $glossary->completionentries) {
        $completion->update_state($cm, COMPLETION_COMPLETE, $entry->userid);
    }

    // Reset caches.
    if ($entry->usedynalink) {
        \mod_glossary\local\concept_cache::reset_glossary($glossary);
    }
}

redirect("view.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook");
