<?php  //$Id$
require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'edit_item_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
//require_capability() here!!

// default return url
$returnurl = 'category.php?id='.$course->id;

$mform = new edit_item_form();
if ($item = get_record('grade_items', 'id', $id, 'courseid', $course->id)) {
    $item->calculation = grade_item::denormalize_formula($item->calculation, $course->id);
    $mform->set_data($item);

} else {
    $mform->set_data(array('courseid'=>$course->id, 'itemtype'=>'manual'));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if (array_key_exists('calculation', $data)) {
        $data->calculation = grade_item::normalize_formula($data->calculation, $course->id);
    }

    $grade_item = new grade_item(array('id'=>$id, 'courseid'=>$course->id));
    grade_item::set_properties($grade_item, $data);

    if (empty($grade_item->id)) {
        $grade_item->itemtype = 'manual'; // all new items to be manual only
        $grade_item->insert();

    } else {
        $grade_item->update();
    }

    redirect($returnurl);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$stritemsedit    = get_string('itemsedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strgraderreport, 'link'=>$CFG->wwwroot.'/grade/report.php?id='.$courseid.'&amp;report=grader', 'type'=>'misc'),
             array('name'=>$stritemsedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $stritemsedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);