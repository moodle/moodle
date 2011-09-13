<?php

/**
 * print the form to map courses for global feedbacks
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->libdir/tablelib.php");

$id = required_param('id', PARAM_INT); // Course Module ID, or
$searchcourse = optional_param('searchcourse', '', PARAM_NOTAGS);
$coursefilter = optional_param('coursefilter', '', PARAM_INT);
$courseid = optional_param('courseid', false, PARAM_INT);

$url = new moodle_url('/mod/feedback/mapcourse.php', array('id'=>$id));
if ($searchcourse !== '') {
    $url->param('searchcourse', $searchcourse);
}
if ($coursefilter !== '') {
    $url->param('coursefilter', $coursefilter);
}
if ($courseid !== false) {
    $url->param('courseid', $courseid);
}
$PAGE->set_url($url);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

// $SESSION->feedback->current_tab = 'mapcourse';
$current_tab = 'mapcourse';

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

require_capability('mod/feedback:mapcourse', $context);

if ($coursefilter) {
    $map->feedbackid = $feedback->id;
    $map->courseid = $coursefilter;
    // insert a map only if it does exists yet
    $sql = "SELECT id, feedbackid
              FROM {feedback_sitecourse_map}
             WHERE feedbackid = ? AND courseid = ?";
    if (!$DB->get_records_sql($sql, array($map->feedbackid, $map->courseid))) {
        $DB->insert_record('feedback_sitecourse_map', $map);
    }
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

include('tabs.php');

echo $OUTPUT->box(get_string('mapcourseinfo', 'feedback'), 'generalbox boxaligncenter boxwidthwide');
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
echo '<form method="post">';
echo '<input type="hidden" name="id" value="'.$id.'" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

$sql = "select c.id, c.shortname
          from {course} c
         where ".$DB->sql_like('c.shortname', '?', false)."
               OR ".$DB->sql_like('c.fullname', '?', false);
$params = array("%{$searchcourse}%", "%{$searchcourse}%");

if (($courses = $DB->get_records_sql_menu($sql, $params)) && !empty($searchcourse)) {
    echo ' ' . get_string('courses') . ': ';
    echo html_writer::select($courses, 'coursefilter', $coursefilter);
    echo '<input type="submit" value="'.get_string('mapcourse', 'feedback').'"/>';
    echo $OUTPUT->help_icon('mapcourses', 'feedback');
    echo '<input type="button" value="'.get_string('searchagain').'" onclick="document.location=\'mapcourse.php?id='.$id.'\'"/>';
    echo '<input type="hidden" name="searchcourse" value="'.$searchcourse.'"/>';
    echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'"/>';
    echo $OUTPUT->help_icon('searchcourses', 'feedback');
} else {
    echo '<input type="text" name="searchcourse" value="'.$searchcourse.'"/> <input type="submit" value="'.get_string('searchcourses').'"/>';
    echo $OUTPUT->help_icon('searchcourses', 'feedback');
}

echo '</form>';

if($coursemap = feedback_get_courses_from_sitecourse_map($feedback->id)) {
    $table = new flexible_table('coursemaps');
    $table->define_columns( array('course'));
    $table->define_headers( array(get_string('mappedcourses', 'feedback')));

    $table->setup();

    $unmapurl = new moodle_url('/mod/feedback/unmapcourse.php');
    foreach ($coursemap as $cmap) {
        $cmapcontext = get_context_instance(CONTEXT_COURSE, $cmap->id);
        $cmapshortname = format_string($cmap->shortname, true, array('context' => $cmapcontext));
        $cmapfullname = format_string($cmap->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $cmap->courseid)));
        $unmapurl->params(array('id'=>$id, 'cmapid'=>$cmap->id));
        $table->add_data(array('<a href="'.$unmapurl->out().'"><img src="'.$OUTPUT->pix_url('t/delete') . '" alt="Delete" /></a> ('.$cmapshortname.') '.$cmapfullname));
    }

    $table->print_html();
} else {
    echo '<h3>'.get_string('mapcoursenone', 'feedback').'</h3>';
}


echo $OUTPUT->box_end();

echo $OUTPUT->footer();

