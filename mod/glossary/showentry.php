<?php

require_once("../../config.php");
require_once("lib.php");

$concept  = optional_param('concept', '', PARAM_CLEAN);
$courseid = optional_param('courseid', 0, PARAM_INT);
$eid      = optional_param('eid', 0, PARAM_INT); // glossary entry id
$displayformat = optional_param('displayformat',-1, PARAM_SAFEDIR);

$url = new moodle_url($CFG->wwwroot.'/mod/glossary/showentry.php');
if ($concept !== '') {
    $url->param('concept', $concept);
}
if ($courseid !== 0) {
    $url->param('courseid', $courseid);
}
if ($eid !== 0) {
    $url->param('eid', $eid);
}
if ($displayformat !== -1) {
    $url->param('displayformat', $displayformat);
}
$PAGE->set_url($url);

if ($CFG->forcelogin) {
    require_login();
}

if ($eid) {
    $entry = $DB->get_record("glossary_entries", array("id"=>$eid));
    $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid));
    $entry->glossaryname = format_string($glossary->name,true);
    if (!$cm = get_coursemodule_from_instance("glossary", $glossary->id)) {
        print_error("invalidcoursemodule");
    }
    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect($CFG->wwwroot.'/course/view.php?id='.$cm->course, get_string('activityiscurrentlyhidden'));
    }
    $entry->cmid = $cm->id;
    $entry->courseid = $cm->course;
    $entries[] = $entry;
} else if ($concept) {
    $entries = glossary_get_entries_search($concept, $courseid);
} else {
    print_error('invalidentry');
}

if ($entries) {
    foreach ($entries as $key => $entry) {
        //$entries[$key]->footer = "<p align=\"right\">&raquo;&nbsp;<a onClick=\"if (window.opener) {window.opener.location.href='$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid'; return false;} else {openpopup('/mod/glossary/view.php?g=$entry->glossaryid', 'glossary', 'menubar=1,location=1,toolbar=1,scrollbars=1,directories=1,status=1,resizable=1', 0); return false;}\" href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\" target=\"_blank\">".format_string($entry->glossaryname,true)."</a></p>";  // Could not get this to work satisfactorily in all cases  - Martin
        $entries[$key]->footer = "<p style=\"text-align:right\">&raquo;&nbsp;<a href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">".format_string($entry->glossaryname,true)."</a></p>";
        add_to_log($entry->courseid, "glossary", "view entry", "showentry.php?eid=$entry->id", $entry->id, $entry->cmid);
    }
}

if (!empty($courseid)) {
    $course = $DB->get_record("course", array("id"=>$courseid));
    if ($course->id != SITEID) {
        require_login($courseid);
    }

    $strglossaries = get_string("modulenameplural", "glossary");
    $strsearch = get_string("search");

    $CFG->framename = "newwindow";

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

