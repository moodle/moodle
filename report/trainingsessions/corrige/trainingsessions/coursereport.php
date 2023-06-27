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
 * Course trainingsessions report
 *
 * @package    report_trainingsessions
 * @category   report
 * @version    moodle 2.x
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

ob_start();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/selector_form.php');

$id = required_param('id', PARAM_INT); // The course id.

// Calculate start time.

$selform = new SelectorForm($id, 'course');
if (!$data = $selform->get_data()) {
    $data = new StdClass;
    $data->from = optional_param('from', -1, PARAM_NUMBER);
    $data->to = optional_param('to', -1, PARAM_NUMBER);
    $data->userid = optional_param('userid', $USER->id, PARAM_INT);
    $data->fromstart = optional_param('fromstart', 0, PARAM_BOOL);
    $data->tonow = optional_param('tonow', 0, PARAM_BOOL);
    $data->output = optional_param('output', 'html', PARAM_ALPHA);
    $data->groupid = optional_param('group', '0', PARAM_ALPHA);
}

$context = context_course::instance($id);
$config = get_config('report_trainingsessions');

// Calculate start time.

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

// Compute target group.

$allgroupsaccess = has_capability('moodle/site:accessallgroups', $context);

if (!$allgroupsaccess) {
    $mygroups = groups_get_my_groups();

    $allowedgroupids = array();
    if ($mygroups) {
        foreach ($mygroups as $g) {
            $allowedgroupids[] = $g->id;
        }
        if (empty($data->groupid) || !in_array($data->groupid, $allowedgroupids)) {
            $data->groupid = $allowedgroupids[0];
        }
    } else {
        echo $OUTPUT->notification(get_string('errornotingroups', 'report_trainingsessions'));
        echo $OUTPUT->footer($course);
        die;
    }
} else {
    if ($allowedgroups = groups_get_all_groups($COURSE->id, $USER->id, 0, 'g.id,g.name')) {
        $allowedgroupids = array_keys($allowedgroups);
    }
}

if ($data->groupid) {
    $targetusers = get_enrolled_users($context, '', $data->groupid, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
} else {
    $targetusers = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
    if (count($targetusers) > 100) {
        if (!empty($allowedgroupids)) {
            $OUTPUT->notification(get_string('errorcoursetoolarge', 'report_trainingsessions'));
            $data->groupid = $allowedgroupids[0];
            // Refetch again after eventual group correction.
            $targetusers = get_enrolled_users($context, '', $data->groupid, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
        } else {
            // DO NOT COMPILE.
            echo $OUTPUT->notification('coursetoolargenotice', 'report_trainingsessions');
            echo $OUTPUT->footer($course);
            die;
        }
    }
}

// Filter out non compiling users.
report_trainingsessions_filter_unwanted_users($targetusers, $course);

// Get course structure.
$coursestructure = report_trainingsessions_get_course_structure($course->id, $items);

// Print result.

require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

echo '<link rel="stylesheet" href="reports.css" type="text/css" />';

if (!empty($targetusers)) {
    foreach ($targetusers as $auser) {

        $logusers = $auser->id;
        $logs = use_stats_extract_logs($data->from, $data->to, $auser->id, $course);
        $aggregate = use_stats_aggregate_logs($logs, $data->from, $data->to);

        if (empty($aggregate['sessions'])) {
            $aggregate['sessions'] = array();
        }

        $data->items = $items;

        $data->activityelapsed = @$aggregate['activities'][$course->id]->elapsed;
        $data->activityevents = @$aggregate['activities'][$course->id]->events;
        $data->otherelapsed = @$aggregate['other'][$course->id]->elapsed;
        $data->otherevents = @$aggregate['other'][$course->id]->events;
        $data->done = 0;

        if (!empty($aggregate)) {

            $data->course = new StdClass();
            $data->course->elapsed = 0;
            $data->course->events = 0;

            if (!empty($aggregate['course'])) {
                $data->course->elapsed = 0 + @$aggregate['course'][$course->id]->elapsed;
                $data->course->events = 0 + @$aggregate['course'][$course->id]->events;
            }

            // Calculate everything.

            $data->elapsed = $data->activityelapsed + $data->otherelapsed + $data->course->elapsed;
            $data->events = $data->activityevents + $data->otherevents + $data->course->events;

            $sesscount = report_trainingsessions_count_sessions_in_course($aggregate['sessions'], $course->id);
            $data->sessions = (!empty($aggregate['sessions'])) ? $sesscount : 0;

            foreach (array_keys($aggregate) as $module) {
                /*
                 * Exclude from calculation some pseudo-modules that are not part of
                 * a course structure.
                 */
                if (preg_match('/course|user|upload|sessions|system|activities|other/', $module)) {
                    continue;
                }
                $data->done += count($aggregate[$module]);
            }
        } else {
            $data->sessions = 0;
        }
        if ($data->done > $items) {
            $data->done = $items;
        }

        $data->linktousersheet = 1;
        echo report_trainingsessions_print_header_html($auser->id, $course->id, $data, true);
    }
} else {
    echo $OUTPUT->notification(get_string('nothing', 'report_trainingsessions'));
}

$options['id'] = $course->id;
$options['groupid'] = $data->groupid;
$options['from'] = $data->from; // Alternate way.
$options['to'] = $data->to; // Alternate way.
$options['output'] = 'xls'; // Ask for XLS.
$options['asxls'] = 'xls'; // Force XLS for index.php.
$options['view'] = 'course'; // Force course view.

echo '<br/><center>';

echo '<div class="report-buttons">';
echo '<div class="table-row">';
echo '<div class="tr-summary table-cell">';
$params = array('id' => $course->id,
                'from' => $data->from,
                'to' => $data->to,
                'timesession' => time(),
                'groupid' => $data->groupid);
$csvurl = new moodle_url('/report/trainingsessions/tasks/groupcsvreportonerow_batch_task.php', $params);
echo $OUTPUT->single_button($csvurl, get_string('generatecsv', 'report_trainingsessions'), 'get');
echo '</div>';

echo '<div class="tr-detailed table-cell">';
$params = array('id' => $course->id,
                'view' => 'course',
                'groupid' => $data->groupid,
                'from' => $data->from,
                'to' => $data->to,
                'output' => 'xls');
$url = new moodle_url('/report/trainingsessions/tasks/groupxlsreportperuser_batch_task.php', $params);
echo $OUTPUT->single_button($url, get_string('generatexls', 'report_trainingsessions'), 'get');

if (report_trainingsessions_supports_feature('format/pdf')) {
    $params = array('id' => $course->id,
                    'view' => 'course',
                    'groupid' => $data->groupid,
                    'from' => $data->from,
                    'to' => $data->to);
    $url = new moodle_url('/report/trainingsessions/pro/tasks/grouppdfreportperuser_batch_task.php', $params);
    echo $OUTPUT->single_button($url, get_string('generatepdf', 'report_trainingsessions'), 'get');
}
echo '</div>';
echo '</div>';
echo '</div>';

echo '</center>';
echo '<br/>';
