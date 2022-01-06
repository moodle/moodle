<?php

require_once('../../../../config.php');
require_once('lib.php');
require_once('locallib_admin.php');

global $PAGE, $OUTPUT, $USER;

$id = required_param('id', PARAM_INT);
$params = array('id' => $id);

$PAGE->set_url('/mod/assign/feedback/editpdfplus/view_admin.php?', $params); // Defined here to avoid notices on errors etc
// Prevent caching of this page to stop confusion when changing page after making AJAX changes
$PAGE->set_cacheable(false);

$context = context::instance_by_id($id);
$PAGE->set_context($context);

if ($context->contextlevel == CONTEXT_COURSE) {
    $course = $DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
    require_course_login($course, false);
    // Fix course format if it is no longer installed
    $course->format = course_get_format($course)->get_format();
    $PAGE->set_heading(get_string('admintitle', 'assignfeedback_editpdfplus') . " - " . $course->fullname);
} else {
    require_login();
    $PAGE->set_heading(get_string('admintitle', 'assignfeedback_editpdfplus'));
}

// Must set layout before gettting section info. See MDL-47555.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('admintitle', 'assignfeedback_editpdfplus'));

echo $OUTPUT->header();

require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

$editpdfplus = new assign_feedback_editpdfplus_admin($context);
echo $editpdfplus->view();

echo $OUTPUT->footer();
