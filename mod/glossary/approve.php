<?php

require_once("../../config.php");
require_once("lib.php");

$id  = required_param('id', PARAM_INT);     // Course Module ID
$eid = optional_param('eid', 0,  PARAM_INT);    // Entry ID

$mode = optional_param('mode','approval', PARAM_ALPHA);
$hook = optional_param('hook','ALL', PARAM_CLEAN);

$url = new moodle_url($CFG->wwwroot.'/mod/glossary/approve.php', array('id'=>$id));
if ($eid !== 0) {
    $url->param('eid', $eid);
}
if ($mode !== 'approval') {
    $url->param('mode', $mode);
}
if ($hook !== 'ALL') {
    $url->param('hook', $hook);
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
require_capability('mod/glossary:approve', $context);

$newentry = new object();
$newentry->id = $eid;
$newentry->approved     = 1;
$newentry->timemodified = time(); // wee need this date here to speed up recent activity, TODO: use timestamp in approved field instead in 2.0

$DB->update_record("glossary_entries", $newentry);
add_to_log($course->id, "glossary", "approve entry", "showentry.php?id=$cm->id&amp;eid=$eid", "$eid",$cm->id);
redirect("view.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook",get_string("entryapproved","glossary"),1);
die;

