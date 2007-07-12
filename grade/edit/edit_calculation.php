<?php  //$Id$
require_once '../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'edit_calculation_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
//require_capability() here!!

// default return url
//TODO: add proper return support
$returnurl = $CFG->wwwroot.'/grade/report.php?report=grader&amp;id='.$course->id;

if (!$grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$course->id))) {
    error('Incorect item id');
}

// module items and items without grade can not have calculation
if ($grade_item->is_normal_item() or ($grade_item->gradetype != GRADE_TYPE_VALUE and $grade_item->gradetype != GRADE_TYPE_SCALE)) {
    redirect($returnurl, get_string('erornocalculationallowed', 'grades')); //TODO: localize
}

$mform = new edit_calculation_form();

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if (!$mform->is_submitted()) {
    $calculation = grade_item::denormalize_formula($grade_item->calculation, $grade_item->courseid);
    $mform->set_data(array('courseid'=>$grade_item->courseid, 'calculation'=>$calculation, 'id'=>$grade_item->id, 'itemname'=>$grade_item->itemname));

} else if ($data = $mform->get_data()) {
    $grade_item->set_calculation($data->calculation);
    redirect($returnurl);
}

$strgrades          = get_string('grades');
$strgraderreport    = get_string('graderreport', 'grades');
$strcalculationedit = get_string('editcalculation', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strcalculationedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcalculationedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);