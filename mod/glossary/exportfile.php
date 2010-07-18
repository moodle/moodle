<?php

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once("../../config.php");
require_once("$CFG->libdir/filelib.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);      // Course Module ID

$l   = optional_param('l','', PARAM_ALPHANUM);
$cat = optional_param('cat',0, PARAM_ALPHANUM);

$url = new moodle_url('/mod/glossary/exportfile.php', array('id'=>$id));
if ($l !== '') {
    $url->param('l', $l);
}
if ($cat !== 0) {
    $url->param('cat', $cat);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

require_login($course->id, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/glossary:export', $context);

$filename = clean_filename(strip_tags(format_string($glossary->name,true)).'.xml');
$content = glossary_generate_export_file($glossary,$l,$cat);

send_file($content, $filename, 0, 0, true, true);

