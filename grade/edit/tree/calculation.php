<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/mathslib.php';
require_once 'calculation_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/report.php?id='.$course->id);

if (!$grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$course->id))) {
    error('Incorect item id');
}

// module items and items without grade can not have calculation
if (($grade_item->is_normal_item() and !$grade_item->is_outcome_item())
  or ($grade_item->gradetype != GRADE_TYPE_VALUE and $grade_item->gradetype != GRADE_TYPE_SCALE)) {
    redirect($returnurl, get_string('erornocalculationallowed', 'grades')); //TODO: localize
}

$mform = new edit_calculation_form(null, array('gpr'=>$gpr));

if ($mform->is_cancelled()) {
    redirect($returnurl);

}

$calculation = calc_formula::localize($grade_item->calculation);
$calculation = grade_item::denormalize_formula($calculation, $grade_item->courseid);
$mform->set_data(array('courseid'=>$grade_item->courseid, 'calculation'=>$calculation, 'id'=>$grade_item->id, 'itemname'=>$grade_item->itemname));

if ($data = $mform->get_data(false)) {
    $calculation = calc_formula::unlocalize($data->calculation);
    $grade_item->set_calculation($calculation);
    redirect($returnurl);
}

$strgrades          = get_string('grades');
$strgraderreport    = get_string('graderreport', 'grades');
$strcalculationedit = get_string('editcalculation', 'grades');

$navigation = grade_build_nav(__FILE__, $strcalculationedit, array('courseid' => $courseid));

print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcalculationedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);
