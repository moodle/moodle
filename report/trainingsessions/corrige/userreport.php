<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course trainingsessions report for a single user
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

ob_start();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');
echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>';


echo '<script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>';

// Selector form.

require_once($CFG->dirroot.'/report/trainingsessions/selector_form.php');
$selform = new SelectorForm($id, 'user');
if (!$data = $selform->get_data()) {
    $data = new StdClass;
    $data->from = optional_param('from', -1, PARAM_NUMBER);
    $data->to = optional_param('to', -1, PARAM_NUMBER);
    $data->userid = optional_param('userid', $USER->id, PARAM_INT);
    $data->fromstart = optional_param('fromstart', 0, PARAM_BOOL);
    $data->tonow = optional_param('tonow', 0, PARAM_BOOL);
}

report_trainingsessions_process_bounds($data, $course);

echo $OUTPUT->header();
echo $OUTPUT->container_start();
echo $renderer->tabs($course, $view, $data->from, $data->to);
echo $OUTPUT->container_end();

echo $OUTPUT->box_start('block');
$selform->set_data($data);
$selform->display();
echo $OUTPUT->box_end();

echo get_string('from', 'report_trainingsessions')." : ".userdate($data->from);
echo ' '.get_string('to', 'report_trainingsessions')." : ".userdate($data->to);

// Get data.

$logs = use_stats_extract_logs($data->from, $data->to, $data->userid, $course->id);
$aggregate = use_stats_aggregate_logs($logs, 'module', $data->from, $data->to);
$weekaggregate = use_stats_aggregate_logs($logs, 'module', $data->to - WEEKSECS, $data->to);

if (empty($aggregate['sessions'])) {
    $aggregate['sessions'] = array();
}

// Get course structure.

$coursestructure = report_trainingsessions_get_course_structure($course->id, $items);


// Time period form.

$str = '<div id="test"><table style="width: 100%;">';
$dataobject = report_trainingsessions_print_html($str, $coursestructure, $aggregate, $done);
$str .= "</table></div>";



if (empty($dataobject)) {
    $dataobject = new stdClass();
}
$dataobject->items = $items;
$dataobject->done = $done;

if ($dataobject->done > $items) {
    $dataobject->done = $items;
}

// In-activity.
// if (is_siteadmin()) echo '<pre>'.print_r($aggregate).'</pre>';
$dataobject->activityelapsed = @$aggregate['coursetotal'][$course->id]->elapsed;
$dataobject->activityevents = @$aggregate['activities'][$course->id]->events;
$dataobject->otherelapsed = @$aggregate['other'][$course->id]->elapsed;
$dataobject->otherevents = @$aggregate['other'][$course->id]->events;

$dataobject->course = new StdClass;

// Calculate in-course-out-activities.

$dataobject->course->elapsed = 0;
$dataobject->course->events = 0;

if (!empty($aggregate['course'])) {
    $dataobject->course->elapsed = 0 + @$aggregate['course'][$course->id]->elapsed;
    $dataobject->course->events = 0 + @$aggregate['course'][$course->id]->events;
}

// Calculate everything.

$dataobject->elapsed = $dataobject->activityelapsed + $dataobject->course->elapsed;
$dataobject->extelapsed = $dataobject->activityelapsed + $dataobject->otherelapsed + $dataobject->course->elapsed;
$dataobject->events = $dataobject->activityevents + $dataobject->otherevents + $dataobject->course->events;

if (array_key_exists('upload', $aggregate)) {
    $dataobject->elapsed += @$aggregate['upload'][0]->elapsed;
    $dataobject->upload = new StdClass;
    $dataobject->upload->elapsed = 0 + @$aggregate['upload'][0]->elapsed;
    $dataobject->upload->events = 0 + @$aggregate['upload'][0]->events;
}

// Get additional grade columns and add to passed dataobject for header.
report_trainingsessions_add_graded_data($gradecols, $data->userid, $aggregate);

$user = $DB->get_record('user', array('id' => $data->userid));
$cols = report_trainingsessions_get_summary_cols();
$headdata = report_trainingsessions_map_summary_cols($cols, $user, $aggregate, $weekaggregate, $course->id, true);
$headdata['gradecols'] = $gradecols;
echo report_trainingsessions_print_header_html($data->userid, $course->id, (object)$headdata);
$totalTimElapsed =  report_trainingsessions_print_header_elapsed($data->userid, $course->id, (object)$headdata);
$totalSessionTime =  report_trainingsessions_print_session_list_elapsed($aggregate['sessions'], $course->id, $data->userid);

// ---------------------------------- HADRIEN 02/02/19 --------------------------------------
echo report_trainingsessions_print_session_list($str, $aggregate['sessions'], $course->id, $data->userid);

echo $str;

if (is_siteadmin()) echo $renderer->xls_userexport_button($data);

// export pdf Franck 11/05/2020 

if($_POST['id'] || $_GET['id']){
    if($_POST['id'] == ''){
        $id = $_GET['id'];
    }else{
        $id = $_POST['id'];
    }

}

if(is_siteadmin()){
    echo '<form method="post" target="_blank" action="https://formassmat-moodle.fr/report/trainingsessions/tasks/userpdfreportperuser_batch_task.php" >';
    echo '<input type="hidden" name="id" value="'.$id.'" >';
    echo '<input type="hidden" name="view" value="'.$view.'" >';
    echo '<input type="hidden" name="userid" value="'.$data->userid.'" >';
    echo '<input type="hidden" name="from" value="'.$data->from.'" >';
    echo '<input type="hidden" name="to" value="'.$data->to.'" >';
    echo '<input type="hidden" name="totalTimElapsed" value="'.$totalTimElapsed.'" >';
    echo '<input type="hidden" name="totalSessionTime" value="'.$totalSessionTime.'" >';
    echo '<input type="hidden" name="sesskey" value="'.$_SESSION['sesskey'].'" >';
    echo '<button type="submit" class="btn btn-secondary">Générer en pdf</button>';
    echo '</form>';
}

if (report_trainingsessions_supports_feature('format/pdf')) {
    include_once($CFG->dirroot.'/report/trainingsessions/pro/renderer.php');
    $rendererext = new \report_trainingsessions\output\pro_renderer($PAGE, '');
    echo $rendererext->pdf_userexport_buttons($data);
}

echo '<br/>';



