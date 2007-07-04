<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'edit_feedback_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
//require_capability() here!!

// default return url
$returnurl = 'index.php?id='.$course->id;


$mform = new edit_feedback_form();
if ($grade_text = get_record('grade_grades_text', 'gradeid', $id)) {
    $mform->set_data($grade_text);
} else {
    $mform->set_data(array('courseid'=>$course->id));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $grade_text = new grade_grades_text(array('gradeid'=>$id));
    grade_grades_text::set_properties($grade_text, $data);

    if (empty($grade_text->id)) {
        $grade_text->insert();

    } else {
        $grade_text->update();
    }

    redirect($returnurl);
}

// Get name of student and gradeitem name
$query = "SELECT a.firstname, a.lastname, b.itemname, c.finalgrade, b.grademin, b.grademax
            FROM {$CFG->prefix}user AS a, 
                 {$CFG->prefix}grade_items AS b, 
                 {$CFG->prefix}grade_grades AS c
           WHERE c.id = $id
             AND b.id = c.itemid
             AND a.id = c.userid";

$extra_info = get_record_sql($query) ;
$extra_info->grademin = round($extra_info->grademin);
$extra_info->grademax = round($extra_info->grademax);
$extra_info->finalgrade = round($extra_info->finalgrade);

$stronascaleof   = get_string('onascaleof', 'grades', $extra_info);
$strgrades       = get_string('grades');
$strgrade        = get_string('grade');
$strgraderreport = get_string('graderreport', 'grades');
$strfeedbackedit = get_string('feedbackedit', 'grades');
$strstudent      = get_string('student', 'grades');
$strgradeitem    = get_string('gradeitem', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strgraderreport, 'link'=>$CFG->wwwroot.'/grade/report.php?id='.$courseid.'&amp;report=grader', 'type'=>'misc'),
             array('name'=>$strfeedbackedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strfeedbackedit, $navigation, '', '', true, '', navmenu($course));

print_heading(get_string('feedbackedit', 'grades'));
print_box_start('gradefeedbackbox generalbox');
echo "<p>$strstudent: " . fullname($extra_info) . "</p>";
echo "<p>$strgradeitem: " . $extra_info->itemname . "</p>";
if (!empty($extra_info->finalgrade)) {
    echo "<p>$strgrade: " . $extra_info->finalgrade . "$stronascaleof</p>";
}

$mform->display();

print_box_end();

print_footer($course);
die;
