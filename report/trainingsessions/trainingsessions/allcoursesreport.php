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
 * Course trainingsessions report. Gives a transversal view of all courses for a user.
 * this script is used as inclusion of the index.php file.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/*
 * direct log construction implementation
 *
 */
ob_start();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/selector_form.php');

$id = required_param('id', PARAM_INT); // The course id.

// Calculate start time.
$selform = new SelectorForm($id, 'allcourses');

if (!$data = $selform->get_data()) {
    $data = new StdClass();
    $data->from = optional_param('from', -1, PARAM_NUMBER);
    $data->to = optional_param('to', -1, PARAM_NUMBER);
    $data->userid = optional_param('userid', $USER->id, PARAM_INT);
    $data->fromstart = optional_param('fromstart', 0, PARAM_BOOL);
    $data->tonow = optional_param('tonow', 0, PARAM_BOOL);
    $data->output = optional_param('output', 'html', PARAM_ALPHA);
}

$context = context_course::instance($id);
$canseeothers = has_capability('report/trainingsessions:viewother', $context);

if (!$canseeothers) {
    // Restrict to view yourself only.
    $userid = $USER->id;
} else {
    $userid = $data->userid;
}

report_trainingsessions_process_bounds($data, $course);

if ($data->output == 'html') {
    echo $OUTPUT->header();
    echo $OUTPUT->container_start();
    echo $renderer->tabs($course, $view, $data->from, $data->to);
    echo $OUTPUT->container_end();

    echo $OUTPUT->box_start('block');
    $selform->set_data($data);
    $selform->display();
    echo $OUTPUT->box_end();

    echo get_string('from', 'report_trainingsessions')." : ".userdate($data->from);
    echo ' '.get_string('to', 'report_trainingsessions')."  : ".userdate($data->to);
}

// Get log data.
$logs = use_stats_extract_logs($data->from, $data->to, $userid, null);
$aggregate = use_stats_aggregate_logs($logs, $data->from, $data->to);

if (empty($aggregate['sessions'])) {
    $aggregate['sessions'] = array();
}

// Print result.

if ($data->output == 'html') {
    // Time period form.

    include_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

    echo '<br/>';

    $str = '';
    $dataobject = report_trainingsessions_print_allcourses_html($str, $aggregate);

    $dataobject->activityelapsed = @$aggregate['activities'][$COURSE->id]->elapsed;
    $dataobject->activityevents = @$aggregate['activities'][$COURSE->id]->events;
    $dataobject->otherelapsed = @$aggregate['other'][$COURSE->id]->elapsed;
    $dataobject->otherevents = @$aggregate['other'][$COURSE->id]->events;

    $dataobject->course = new StdClass();
    $dataobject->course->elapsed = 0;
    $dataobject->course->events = 0;

    if (!empty($aggregate['course'])) {
        $dataobject->course->elapsed = 0 + @$aggregate['course'][$course->id]->elapsed;
        $dataobject->course->events = 0 + @$aggregate['course'][$course->id]->events;
    }

    // Calculate everything.

    $dataobject->elapsed = $dataobject->activityelapsed + $dataobject->otherelapsed + $dataobject->course->elapsed;
    $dataobject->events = $dataobject->activityevents + $dataobject->otherevents + $dataobject->course->events;

    $dataobject->sessions = 0;
    if (!empty($aggregate['sessions'])) {
        $dataobject->sessions = report_trainingsessions_count_sessions_in_course($aggregate['sessions'], 0);
    }

    if (array_key_exists('upload', $aggregate)) {
        $dataobject->elapsed += @$aggregate['upload'][0]->elapsed;
        $dataobject->upload = new StdClass;
        $dataobject->upload->elapsed = 0 + @$aggregate['upload'][0]->elapsed;
        $dataobject->upload->events = 0 + @$aggregate['upload'][0]->events;
    }

    report_trainingsessions_print_header_html($userid, $course->id, $dataobject, true, false, false);

    echo $OUTPUT->heading(get_string('incourses', 'report_trainingsessions'));
    echo $str;

    report_trainingsessions_print_session_list($str2, @$aggregate['sessions'], 0, $userid);
    echo $str2;

    $params = array('id' => $course->id,
                    'userid' => $userid,
                    'from' => $data->from,
                    'to' => $data->to);
    echo '<br/><center>';

    $url = new moodle_url('/report/trainingsessions/tasks/userxlsreportallcourses_batch_task.php', $params);
    echo $OUTPUT->single_button($url, get_string('generatexls', 'report_trainingsessions'), 'get');

    if (report_trainingsessions_supports_feature('format/pdf')) {
        include_once($CFG->dirroot.'/report/trainingsessions/pro/renderer.php');
        $rendererext = new \report_trainingsessions\output\pro_renderer($PAGE, '');
        echo $rendererext->user_session_reports_buttons($data->userid, 'allcourses');
    }

    echo '</center>';
    echo '<br/>';

} else {

    require_once($CFG->dirroot.'/report/trainingsessions/renderers/xlsrenderers.php');

    require_once($CFG->libdir.'/excellib.class.php');

    $filename = 'allcourses_sessions_report_'.date('d-M-Y', time()).'.xls';
    $workbook = new MoodleExcelWorkbook('-');

    // Sending HTTP headers.
    ob_end_clean();

    $workbook->send($filename);

    // Preparing some formats.
    $xlsformats = report_trainingsessions_xls_formats($workbook);
    $startrow = 15;
    $worksheet = report_trainingsessions_init_worksheet($userid, $startrow, $xlsformats, $workbook, 'allcourses');
    $overall = report_trainingsessions_print_allcourses_xls($worksheet, $aggregate, $startrow, $xlsformats);
    $data->elapsed = $overall->elapsed;
    $data->events = $overall->events;
    report_trainingsessions_print_header_xls($worksheet, $userid, $course->id, $data, $xlsformats);

    $worksheet = report_trainingsessions_init_worksheet($userid, $startrow, $xlsformats, $workbook, 'sessions');
    report_trainingsessions_print_sessions_xls($worksheet, 15, @$aggregate['sessions'], null, $xlsformats);
    report_trainingsessions_print_header_xls($worksheet, $userid, $course->id, $data, $xlsformats);

    $workbook->close();
}
