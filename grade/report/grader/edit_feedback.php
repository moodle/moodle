<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'edit_feedback_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', 'view', PARAM_ALPHA);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/grader:manage', $context);

// default return url
$returnurl = $CFG->wwwroot.'/grade/report.php?report=grader&amp;id='.$course->id;

$mform = new edit_feedback_form();
if ($grade_text = get_record('grade_grades_text', 'gradeid', $id)) {
    $mform->set_data($grade_text);
} else {
    $mform->set_data(array('courseid'=>$course->id, 'id' => $id));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $data->gradeid = $data->id;
    unset($data->id);

    $grade_text = new grade_grades_text(array('gradeid'=>$id));
    grade_grades_text::set_properties($grade_text, $data);
    if (empty($grade_text->id)) {
        $grade_text->insert();

    } else {
        $grade_text->update();
    }

    redirect($returnurl, get_string('feedbacksaved', 'grades'), 1);
}

// Get extra data related to this feedback
$query = "SELECT a.id AS userid, a.firstname, a.lastname,
                 b.id AS itemid, b.itemname, b.grademin, b.grademax, b.iteminstance, b.itemmodule, b.scaleid,
                 c.finalgrade
            FROM {$CFG->prefix}user a,
                 {$CFG->prefix}grade_items b,
                 {$CFG->prefix}grade_grades c
           WHERE c.id = $id
             AND b.id = c.itemid
             AND a.id = c.userid";

$extra_info = get_record_sql($query) ;
$extra_info->grademin = round($extra_info->grademin);
$extra_info->grademax = round($extra_info->grademax);
$extra_info->finalgrade = round($extra_info->finalgrade);

if (!empty($extra_info->itemmodule) && !empty($extra_info->iteminstance)) {
    $extra_info->course_module = get_coursemodule_from_instance($extra_info->itemmodule, $extra_info->iteminstance, $courseid);
}

$stronascaleof   = get_string('onascaleof', 'grades', $extra_info);
$strgrades       = get_string('grades');
$strgrade        = get_string('grade');
$strgraderreport = get_string('graderreport', 'grades');
$strfeedback     = get_string('feedback', 'grades');
$strfeedbackedit = get_string('feedbackedit', 'grades');
$strfeedbackview = get_string('feedbackview', 'grades');
$strfeedbackadd  = get_string('feedbackadd', 'grades');
$strstudent      = get_string('student', 'grades');
$strgradeitem    = get_string('gradeitem', 'grades');

$feedback = null;
$heading = ${"strfeedback$action"};
if (!empty($action) && $action == 'view' && !empty($grade_text->feedback)) {
    $feedback = "<p><strong>$strfeedback</strong>:</p><p>$grade_text->feedback</p>";
}

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strgraderreport, 'link'=>$CFG->wwwroot.'/grade/report.php?id='.$courseid.'&amp;report=grader', 'type'=>'misc'),
             array('name'=>$heading, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);

/*********** BEGIN OUTPUT *************/

print_header_simple($strgrades . ': ' . $strgraderreport . ': ' . $heading,
    ': ' . $heading , $navigation, '', '', true, '', navmenu($course));

print_heading($heading);

print_simple_box_start("center");

// Student name and link
echo "<p><strong>$strstudent:</strong> <a href=\"" . $CFG->wwwroot . '/user/view.php?id='
     . $extra_info->userid . '">' . fullname($extra_info) . "</a></p>";

// Grade item name and link
if (!empty($extra_info->course_module) && !empty($extra_info->itemmodule)) {
    echo "<p><strong>$strgradeitem:</strong> <a href=\"" . $CFG->wwwroot . '/mod/' . $extra_info->itemmodule
         . '/view.php?id=' . $extra_info->course_module->id . "&amp;courseid=$courseid\">$extra_info->itemname</a></p>";
}

// Final grade and link to scale if applicable
if (!empty($extra_info->finalgrade)) {
    $openlink = '';
    $closelink = '';

    if (!empty($extra_info->scaleid)) {
        $openlink = '<a href="' . $CFG->wwwroot . '/course/scales.php?id=' . $courseid . '&amp;scaleid='
                  . $extra_info->scaleid . '">';
        $closelink = '</a>';
    }
    echo "<p><strong>$strgrade:</strong> " . $extra_info->finalgrade . "$openlink $stronascaleof $closelink</p>";
}

// Form if in edit or add modes
if ($action != 'view') {
    $mform->display();
} else { // Feedback string and Back button if in View mode
    echo $feedback;
    echo "<button onclick=\"window.location='" . $CFG->wwwroot . "/grade/report.php?report=grader&amp;id=$courseid';\">"
         . get_string('back') . '</button>';
}

print_simple_box_end();

print_footer($course);
die;
