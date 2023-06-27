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

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/selector_form.php');
require_once($CFG->dirroot.'/report/trainingsessions/task_form.php');

$offset = optional_param('offset', 0, PARAM_INT);
$page = 20;

ini_set('memory_limit', '2048M');

$id = required_param('id', PARAM_INT); // The course id.

// For tasks.

$selform = new StdClass();
$selform->from = optional_param_array('from', null, PARAM_INT);

if (empty($selform->from) || @$selform->fromstart) {
    // Maybe we get it from parameters.
    $from = $course->startdate;
    $selform->from['day'] = date('d', $from);
    $selform->from['year'] = date('Y', $from);
    $selform->from['month'] = date('m', $from);
} else {
    $from = mktime(0, 0, 0, $selform->from['month'], $selform->from['day'], $selform->from['year']);
}

$startday = $selform->from['day']; // From (-1 is from course start).
$startmonth = $selform->from['month']; // From (-1 is from course start).
$startyear = $selform->from['year']; // From (-1 is from course start).

$selform->to = optional_param_array('to', null, PARAM_INT);
if (empty($selform->to) || @$selform->tonow) {
    // Maybe we get it from parameters.
    $to = time();
    $selform->to['day'] = date('d', $to);
    $selform->to['year'] = date('Y', $to);
    $selform->to['month'] = date('m', $to);
} else {
    $to = mktime(0, 0, 0, $selform->to['month'], $selform->to['day'], $selform->to['year']);
}

$endday = $selform->to['day']; // To (-1 is from course start).
$endmonth = $selform->to['month']; // To (-1 is from course start).
$endyear = $selform->to['year']; // To (-1 is from course start).

// Calculate start time.
$selform->groupid = optional_param('groupid', '', PARAM_INT);
$selform->fromstart = optional_param('fromstart', 0, PARAM_BOOL);
$selform->tonow = optional_param('tonow', 0, PARAM_BOOL);

$selformform = new SelectorForm($id, 'courseraw');

$context = context_course::instance($id);
$config = get_config('report_trainingsessions');

// Compute target group.

if (!empty($selform->groupid)) {
    $targetusers = groups_get_members($selform->groupid);
    $groupname = $DB->get_field('groups', 'name', array('id' => $selform->groupid));
} else {
    $targetusers = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);

    $hasgroups = $DB->count_records('groups', array('courseid' => $id));

    if ($hasgroups && count($targetusers) > 50 || !has_capability('moodle/site:accessallgroups', $context)) {
        // In that case we need force groupid to some value.
        $selform->groupid = groups_get_course_group($COURSE);
        $groupname = $DB->get_field('groups', 'name', array('id' => $selform->groupid));
        $targetusers = groups_get_members($selform->groupid);

        if (count($targetusers) > 50) {
            $OUTPUT->notification(get_string('errorcoursetoolarge', 'report_trainingsessions'));
        }
    } else {
        // We can compile for all course.
        $selform->groupid = 0;
        $groupname = '';
    }
}

// Filter out non compiling users.
report_trainingsessions_filter_unwanted_users($targetusers, $course);

// Print result.
echo $OUTPUT->header();
echo $OUTPUT->container_start();
echo $renderer->tabs($course, $view, $from, $to);
echo $OUTPUT->container_end();

echo $OUTPUT->box_start('block');
$selformform->set_data($selform);
$selformform->display();
echo $OUTPUT->box_end();

// Quick compile an XLS report if not too many users.
if (!empty($targetusers)) {

    if (count($targetusers) < 50) {
        include_once($CFG->dirroot.'/report/trainingsessions/renderers/csvrenderers.php');
        // This is a quick immediate compilation for small groups.
        echo get_string('quickgroupcompile', 'report_trainingsessions', count($targetusers));

        foreach ($targetusers as $u) {
            $logs = use_stats_extract_logs($from, $to, $u->id, $id);
            $aggregate[$u->id] = use_stats_aggregate_logs($logs, $from, $to);

            $weeklogs = use_stats_extract_logs($to - DAYSECS * 7, $to, $u->id, $id);
            $weekaggregate[$u->id] = use_stats_aggregate_logs($weeklogs, $to - DAYSECS * 7, $to);
        }

        $timestamp = time();
        report_trainingsessions_print_global_header($rawstr);
        $cols = report_trainingsessions_get_summary_cols();

        foreach ($targetusers as $userid => $auser) {
            report_trainingsessions_print_global_raw($id, $cols, $auser, $aggregate, $weekaggregate, $rawstr);
        }

        $fs = get_file_storage();

        // Prepare file record object.

        $fileinfo = array(
            'contextid' => $context->id, // ID of context (course context).
            'component' => 'report_trainingsessions',     // Usually = table name.
            'filearea' => 'rawreports',     // Usually = table name.
            'itemid' => $COURSE->id,               // Usually = ID of row in table.
            'filepath' => '/',           // Any path beginning and ending in /.
            'filename' => "raw_{$timestamp}.csv"); // Any filename.

        // Create file containing text.
        $fs->delete_area_files($context->id, 'report_trainingsessions', 'rawreports', $COURSE->id);
        $fs->create_file_from_string($fileinfo, $rawstr);

        $strupload = get_string('uploadresult', 'report_trainingsessions');
        $fileurl = moodle_url::make_pluginfile_url($context->id, 'report_trainingsessions', 'rawreports', $fileinfo['itemid'],
                                                   '/', 'raw_'.$timestamp.'.csv');
        $pix = '<img src="'.$OUTPUT->pix_url('f/spreadsheet').'" height="40" width="30" />';
        echo '<p><br/>'.$strupload.': <a href="'.$fileurl.'">'.$pix.'</a></p>';

    } else {
        echo $OUTPUT->box_start();
        echo $OUTPUT->notification(get_string('toobig', 'report_trainingsessions'));
        echo $OUTPUT->box_end();
    }
}

// Print batch list.

$maxtaskid = 0;
if (!empty($CFG->trainingreporttasks)) {
    $tasks = unserialize($CFG->trainingreporttasks);
    if (!empty($tasks)) {
        foreach ($tasks as $tid => $t) {
            $maxtaskid = max($maxtaskid, $tid);
        }
    }
}
$maxtaskid++;

$currentcontext = array(
    'groupname' => $groupname,
    'startyear' => $startyear,
    'startmonth' => $startmonth,
    'startday' => $startday,
    'endyear' => $endyear,
    'endmonth' => $endmonth,
    'endday' => $endday,
);

$form = new Task_Form(new moodle_url('/report/trainingsessions/courseraw.task_receiver.php'), $currentcontext);

// Quick written controller for deletion.
if ($delete = optional_param('delete', '', PARAM_INT)) {
    unset($tasks[$delete]);
    set_config('trainingreporttasks', serialize($tasks));
}

if (!empty($CFG->trainingreporttasks)) {
    echo $OUTPUT->heading(get_string('scheduledbatches', 'report_trainingsessions'));

    $taskstr = get_string('taskname', 'report_trainingsessions');
    $dirstr = get_string('outputdir', 'report_trainingsessions');
    $datestr = get_string('batchdate', 'report_trainingsessions');
    $coursestr = get_string('course');
    $replaystr = get_string('replay', 'report_trainingsessions');
    $reportlayoutstr = get_string('reportlayout', 'report_trainingsessions');
    $reportformatstr = get_string('reportformat', 'report_trainingsessions');
    $groupstr = get_string('group');
    $table = new html_table();
    $table->head = array("<b>$taskstr</b>",
                         "<b>$coursestr</b>",
                         "<b>$datestr</b>",
                         "<b>$dirstr</b>",
                         "<b>$reportlayoutstr</b>",
                         "<b>$reportformatstr</b>",
                         "<b>$replaystr</b>",
                         '');
    $table->align = array('left', 'left', 'left', 'left', 'center', 'center', 'center', 'center');
    $table->width = '100%';
    $table->size = array('30%', '15%', '10%', '10%', '10%', '10%', '10%', '5%');

    if (!empty($tasks)) {
        foreach ($tasks as $task) {
            if ($group = groups_get_group($task->groupid)) {
                $groupname = $group->name;
            } else {
                $groupname = get_string('course');
            }
            if ($task->startday != -1) {
                $task->from = mktime (0, 0, 0, $task->startmonth, $task->startday, $task->startyear);
            } else {
                $task->from = $DB->get_field('course', 'startdate', array('id' => $task->courseid));
            }
            if ($task->endday != -1) {
                $task->to = mktime (0, 0, 0, $task->endmonth, $task->endday, $task->endyear);
            } else {
                $task->to = time();
            }

            if (@$task->reportscope == 'allcourses') {
                $courseshort = $DB->get_field('course', 'shortname', array('id' => $task->courseid));
                $params = array('id' => $task->courseid,
                                'view' => 'courseraw',
                                'from' => $from,
                                'to' => $to,
                                'groupid' => $selform->groupid);
                $reportcontexturl = new moodle_url('/report/trainingsessions/index.php', $params);
                $scope = '<a href="'.$reportcontexturl.'">'.$groupname.'@*</a>';
            } else {
                $courseshort = $DB->get_field('course', 'shortname', array('id' => $task->courseid));
                $params = array('id' => $task->courseid,
                                'view' => 'courseraw',
                                'from' => $from,
                                'to' => $to,
                                'groupid' => $selform->groupid);
                $reportcontexturl = new moodle_url('/report/trainingsessions/index.php', $params);
                $scope = '<a href="'.$reportcontexturl.'">'.$groupname.'@'.$courseshort.'</a>';
            }

            switch($task->reportlayout) {
                case 'onefulluserpersheet':
                    $layoutimg = 'usersheets';
                    break;
                case 'oneuserperrow' :
                    $layoutimg = 'userlist';
                    break;
                default:
                    $layoutimg = 'sessions';
            }
            $attrs = array('src' => $OUTPUT->pix_url($layoutimg, 'report_trainingsessions'),
                           'title' => get_string($layoutimg, 'report_trainingsessions'));
            $layout = html_writer::tag('img', null, $attrs);

            if (empty($task->reportformat)) {
                $task->reportformat = 'csv';
            }
            $icons = array('pdf' => 'pdf', 'csv' => 'writer', 'xls' => 'spreadsheet');
            $attrs = array('src' => $OUTPUT->pix_url('f/'.$icons[$task->reportformat].'-32'),
                           'title' => get_string($task->reportformat, 'report_trainingsessions'));
            $format = html_writer::tag('img', null, $attrs);

            $params = array('id' => $id, 'view' => 'courseraw', 'delete' => $task->id);
            $deleteurl = new moodle_url('/report/trainingsessions/index.php', $params);
            $attrs = array('src' => $OUTPUT->pix_url('/t/delete'), 'title' => get_string('delete'));
            $deleteimg = html_writer::tag('img', null, $attrs);

            $commands = '<a href="'.$deleteurl.'">'.$deleteimg.'</a>';

            $params = array('id' => $id,
                            'from' => $task->from,
                            'to' => $task->to,
                            'outputdir' => urlencode($task->outputdir),
                            'reportlayout' => $task->reportlayout,
                            'reportscope' => @$task->reportscope,
                            'runmode' => 'url');
            if ($task->groupid) {
                $params['groupid'] = $task->groupid;
            }
            $dist = report_trainingsessions_supports_feature('format/'.$task->reportformat);
            $distpath = ($dist == 'pro') ? 'pro/' : '';
            $batchloc = '/report/trainingsessions/'.$distpath.'batchs/group'.$task->reportformat.'report_batch.php';
            $batchurl = new moodle_url($batchloc, $params);
            $attrs = array('href' => $batchurl, 'target' => '_blank',
                           'title' => get_string('interactivetitle', 'report_trainingsessions'));
            $commands .= '&nbsp;'.html_writer::tag('a', get_string('interactive', 'report_trainingsessions'), $attrs);

            switch ($task->replay) {
                case TASK_REPLAY: {
                    $attrs = array('src' => $OUTPUT->pix_url('replay', 'report_trainingsessions'));
                    $replayimg = html_writer::tag('img', '', $attrs);
                    break;
                }

                case TASK_SHIFT: {
                    $attrs = array('src' => $OUTPUT->pix_url('periodshift', 'report_trainingsessions'));
                    $replayimg = html_writer::tag('img', '', $attrs);
                    break;
                }

                case TASK_SHIFT_TO: {
                    $attrs = array('src' => $OUTPUT->pix_url('endshift', 'report_trainingsessions'));
                    $replayimg = html_writer::tag('img', '', $attrs);
                    break;
                }
            }

            $table->rowclasses[] = ($id == $task->courseid) ? 'trainingsessions-green' : '';
            $table->data[] = array($task->taskname,
                                   $scope,
                                   userdate($task->batchdate),
                                   $task->outputdir,
                                   $layout,
                                   $format,
                                   ($task->replay) ? format_time($task->replaydelay * 60).' s<br/>'.$replayimg : '-',
                                   $commands);
        }
    }

    echo html_writer::table($table);
}

if (!empty($targetusers)) {
    $formdata = new StdClass;
    $formdata->id = $id;
    $formdata->view = $view;
    $formdata->startday = $startday;
    $formdata->startmonth = $startmonth;
    $formdata->startyear = $startyear;
    $formdata->endday = $endday;
    $formdata->endmonth = $endmonth;
    $formdata->endyear = $endyear;
    $formdata->taskid = $maxtaskid;
    $formdata->groupid = $selform->groupid;
    $form->set_data($formdata);
    $form->display();
} else {
    echo $OUTPUT->box(get_string('nothing', 'report_trainingsessions'), 'report-trainingsession userinfobox');
}

echo $OUTPUT->heading(get_string('reports', 'report_trainingsessions'));

$reportsfileurl = new moodle_url('/report/trainingsessions/filearea.php', array('id' => $id, 'view' => $view));
echo html_writer::tag('a', get_string('reportfilemanager', 'report_trainingsessions'), array('href' => $reportsfileurl));