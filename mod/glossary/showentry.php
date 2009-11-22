<?php

require_once('../../config.php');
require_once('lib.php');

$concept  = optional_param('concept', '', PARAM_CLEAN);
$courseid = optional_param('courseid', 0, PARAM_INT);
$eid      = optional_param('eid', 0, PARAM_INT); // glossary entry id
$displayformat = optional_param('displayformat',-1, PARAM_SAFEDIR);

$url = new moodle_url($CFG->wwwroot.'/mod/glossary/showentry.php');
$url->param('concept', $concept);
$url->param('courseid', $courseid);
$url->param('eid', $eid);
$url->param('displayformat', $displayformat);
$PAGE->set_url($url);

if ($CFG->forcelogin) {
    require_login();
}

if ($eid) {
    $entry = $DB->get_record('glossary_entries', array('id'=>$eid), '*', MUST_EXIST);
    $glossary = $DB->get_record('glossary', 'id', array($entry->glossaryid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('glossary', $glossary->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    require_course_login($course, true, $cm);
    $entry->glossaryname = $glossary->name;
    $entry->cmid = $cm->id;
    $entry->courseid = $cm->course;
    $entries = array($entry);

} else if ($concept) {
    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    require_course_login($course);
    $entries = glossary_get_entries_search($concept, $courseid);

} else {
    error('No valid entry specified');
}

if ($entries) {
    $modinfo = get_fast_modinfo($course);
    foreach ($entries as $key => $entry) {
        // make sure the entry is visible
        if (empty($modinfo->cms[$entry->cmid]->uservisible)) {
            unset($entries[$key]);
            continue;
        }
        if (!$entry->approved and ($USER->id != $entry->userid)) {
            $context = get_context_instance(CONTEXT_MODULE, $entry->cmid);
            if (!has_capability('mod/glossary:approve', $context)) {
                unset($entries[$key]);
                continue;
            }
        }
        //$entries[$key]->footer = "<p align=\"right\">&raquo;&nbsp;<a onClick=\"if (window.opener) {window.opener.location.href='$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid'; return false;} else {openpopup('/mod/glossary/view.php?g=$entry->glossaryid', 'glossary', 'menubar=1,location=1,toolbar=1,scrollbars=1,directories=1,status=1,resizable=1', 0); return false;}\" href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\" target=\"_blank\">".format_string($entry->glossaryname,true)."</a></p>";  // Could not get this to work satisfactorily in all cases  - Martin
        $entries[$key]->footer = "<p style=\"text-align:right\">&raquo;&nbsp;<a href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">".format_string($entry->glossaryname,true)."</a></p>";
        add_to_log($entry->courseid, 'glossary', 'view entry', "showentry.php?eid=$entry->id", $entry->id, $entry->cmid);
    }
}

if (!empty($courseid)) {
    $strglossaries = get_string('modulenameplural', 'glossary');
    $strsearch = get_string('search');

    $CFG->framename = 'newwindow';

    $PAGE->navbar->add($strglossaries);
    $PAGE->navbar->add($strsearch);
    $PAGE->set_title(strip_tags("$course->shortname: $strglossaries $strsearch"));
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
} else {
    echo $OUTPUT->header();    // Needs to be something here to allow linking back to the whole glossary
}

if ($entries) {
    glossary_print_dynaentry($courseid, $entries, $displayformat);
}

echo $OUTPUT->close_window_button();

/// Show one reduced footer
echo $OUTPUT->footer();

