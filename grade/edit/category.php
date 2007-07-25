<?php  //$Id$

require_once '../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'category_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('tree.php?id='.$course->id);


$mform = new edit_category_form(null, array('gpr'=>$gpr));
if ($category = get_record('grade_categories', 'id', $id, 'courseid', $course->id)) {
    // Get Category preferences
    $category->pref_aggregationview = grade_report::get_pref('aggregationview', $id);

    $mform->set_data($category);
} else {
    $mform->set_data(array('courseid'=>$course->id));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $grade_category = new grade_category(array('id'=>$id, 'courseid'=>$course->id));
    grade_category::set_properties($grade_category, $data);

    if (empty($grade_category->id)) {
        $grade_category->insert();

    } else {
        $grade_category->update();
    }

    // Handle user preferences
    if (isset($data->pref_aggregationview)) {
        if (!grade_report::set_pref('aggregationview', $data->pref_aggregationview, $grade_category->id)) {
            error("Could not set preference aggregationview to $value for this grade category");
        }
    }

    redirect($returnurl);
}


$strgrades         = get_string('grades');
$strgraderreport   = get_string('graderreport', 'grades');
$strcategoriesedit = get_string('categoriesedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strcategoriesedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);
die;
