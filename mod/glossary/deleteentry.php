<?php

require_once("../../config.php");
require_once("lib.php");

$id       = required_param('id', PARAM_INT);          // course module ID
$confirm  = optional_param('confirm', 0, PARAM_INT);  // commit the operation?
$entry    = optional_param('entry', 0, PARAM_INT);    // entry id
$prevmode = required_param('prevmode', PARAM_ALPHA);
$hook     = optional_param('hook', '', PARAM_CLEAN);

$url = new moodle_url('/mod/glossary/deleteentry.php', array('id'=>$id,'prevmode'=>$prevmode));
if ($confirm !== 0) {
    $url->param('confirm', $confirm);
}
if ($entry !== 0) {
    $url->param('entry', $entry);
}
if ($hook !== '') {
    $url->param('hook', $hook);
}
$PAGE->set_url($url);

$strglossary   = get_string("modulename", "glossary");
$strglossaries = get_string("modulenameplural", "glossary");
$stredit       = get_string("edit");
$entrydeleted  = get_string("entrydeleted","glossary");


if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error("invalidcoursemodule");
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $entry = $DB->get_record("glossary_entries", array("id"=>$entry))) {
    print_error('invalidentry');
}

// Permission checks are based on the course module instance so make sure it is correct.
if ($cm->instance != $entry->glossaryid) {
    print_error('invalidentry');
}

require_login($course, false, $cm);
$context = context_module::instance($cm->id);

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

// Throws an exception if the user cannot delete the entry.
mod_glossary_can_delete_entry($entry, $glossary, $context, false);

/// If data submitted, then process and store.

if ($confirm and confirm_sesskey()) { // the operation was confirmed.

    mod_glossary_delete_entry($entry, $glossary, $cm, $context, $course, $hook, $prevmode);
    redirect("view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=$hook");

} else {        // the operation has not been confirmed yet so ask the user to do so
    $strareyousuredelete = get_string("areyousuredelete", "glossary");
    $PAGE->navbar->add(get_string('delete'));
    $PAGE->set_title($glossary->name);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    $areyousure = "<b>".format_string($entry->concept)."</b><p>$strareyousuredelete</p>";
    $linkyes    = 'deleteentry.php';
    $linkno     = 'view.php';
    $optionsyes = array('id'=>$cm->id, 'entry'=>$entry->id, 'confirm'=>1, 'sesskey'=>sesskey(), 'prevmode'=>$prevmode, 'hook'=>$hook);
    $optionsno  = array('id'=>$cm->id, 'mode'=>$prevmode, 'hook'=>$hook);

    echo $OUTPUT->confirm($areyousure, new moodle_url($linkyes, $optionsyes), new moodle_url($linkno, $optionsno));

    echo $OUTPUT->footer();
}
