<?php

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);  // Course Module ID.
$a  = optional_param('a', 0, PARAM_INT);   // Assignment ID.

require_login();
$PAGE->set_context(context_system::instance());

if (!$id && !$a) {
    print_error('invalidcoursemodule');
}

$mapping = null;
if ($id) {
    $mapping = $DB->get_record('assignment_upgrade', array('oldcmid' => $id), '*', IGNORE_MISSING);
} else {
    $mapping = $DB->get_record('assignment_upgrade', array('oldinstance' => $a), '*', IGNORE_MISSING);
}

if (!$mapping) {
    $url = '';
    if (has_capability('moodle/site:config', context_system::instance())) {
        $url = new moodle_url('/admin/tool/assignmentupgrade/listnotupgraded.php');
    }
    print_error('assignmentneedsupgrade', 'assignment', $url);
}

$url = new moodle_url('/mod/assign/view.php', array('id' => $mapping->newcmid));
redirect($url);
