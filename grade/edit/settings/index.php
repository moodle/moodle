<?php // $Id$

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'form.php';

$courseid  = optional_param('id', SITEID, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('moodle/grade:manage', $context);

$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'settings', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('coursesettings', 'grades');

$navigation = grade_build_nav(__FILE__, $pagename, $courseid);

$returnurl = $CFG->wwwroot.'/grade/index.php?id='.$course->id;

$mform = new course_settings_form();

$data = new object;
$data->id                  = $course->id;
$data->displaytype         = grade_get_setting($course->id, 'displaytype', -1);
$data->decimalpoints       = grade_get_setting($course->id, 'decimalpoints',- 1);
$data->aggregationposition = grade_get_setting($course->id, 'aggregationposition', -1);

$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if ($data->displaytype == -1) {
        $data->displaytype = null;
    }
    grade_set_setting($course->id, 'displaytype', $data->displaytype);

    if ($data->decimalpoints == -1) {
        $data->decimalpoints = null;
    }
    grade_set_setting($course->id, 'decimalpoints', $data->decimalpoints);

    if ($data->aggregationposition == -1) {
        $data->aggregationposition = null;
    }
    grade_set_setting($course->id, 'aggregationposition', $data->aggregationposition);

    redirect($returnurl);
}

/// Print header
print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));
/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'settings');

$mform->display();

print_footer($course);

?>

