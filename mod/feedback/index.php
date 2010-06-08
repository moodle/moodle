<?php

/**
 * prints the overview of all feedbacks included into the current course
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);

$url = new moodle_url('/mod/feedback/index.php', array('id'=>$id));

$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        print_error('badcontext');
}

require_login($course->id);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'feedback', 'view all', $url->out(false), $course->id);


/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->navbar->add($strfeedbacks);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(get_string('modulename', 'feedback').' '.get_string('activities'));
echo $OUTPUT->header();

/// Get all the appropriate data

if (! $feedbacks = get_all_instances_in_course("feedback", $course)) {
    notice(get_string('thereareno', 'moodle', $strfeedbacks), new moodle_url('/course/view.php', array('id'=>$course->id)));
    die;
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = get_all_sections($course->id);
}

/// Print the list of instances (your module will probably extend this)

$timenow = time();
$strname  = get_string("name");
$strsectionname = get_string('sectionname', 'format_'.$course->format);
$strresponses = get_string('responses', 'feedback');

$table = new html_table();

if ($usesections) {
    if(has_capability('mod/feedback:viewreports', $context)) {
        $table->head  = array ($strsectionname, $strname, $strresponses);
        $table->align = array ("center", "left", 'center');
    }else{
        $table->head  = array ($strsectionname, $strname);
        $table->align = array ("center", "left");
    }
} else {
    if(has_capability('mod/feedback:viewreports', $context)) {
        $table->head  = array ($strname, $strresponses);
        $table->align = array ("left", "center");
    }else{
        $table->head  = array ($strname);
        $table->align = array ("left");
    }
}


foreach ($feedbacks as $feedback) {
    //get the responses of each feedback
    $viewurl = new moodle_url('/mod/feedback/view.php', array('id'=>$feedback->coursemodule));

    if(has_capability('mod/feedback:viewreports', $context)) {
        $completedFeedbackCount = intval(feedback_get_completeds_group_count($feedback));
    }

    $dimmedclass = $feedback->visible ? '' : 'class="dimmed"';
    $link = '<a '.$dimmedclass.' href="'.$viewurl->out().'">'.$feedback->name.'</a>';

    if ($usesections) {
        $tabledata = array (get_section_name($course, $sections[$feedback->section]), $link);
    } else {
        $tabledata = array ($link);
    }
    if(has_capability('mod/feedback:viewreports', $context)) {
        $tabledata[] = $completedFeedbackCount;
    }

    $table->data[] = $tabledata;

}

echo "<br />";

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();

