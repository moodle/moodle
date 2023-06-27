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
 * This script handles the report generation in batch task for a single group.
 * It may produce a group csv report.
 * groupid must be provided.
 * This script should be sheduled in a redirect bouncing process for maintaining
 * memory level available for huge batches.
 *
 * The global course final grade can be selected along with specified modules to get score from.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @version    moodle 2.x
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../../config.php');
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');

$maxbatchduration = 4 * HOURSECS;

$id = required_param('id', PARAM_INT); // The course id.
$from = optional_param('from', -1, PARAM_INT); // Alternate way of saying from when for XML generation.
$to = optional_param('to', -1, PARAM_INT); // Alternate way of saying from when for XML generation.
$groupid = optional_param('groupid', '', PARAM_INT); // Compiling for given group or all groups.
$outputdir = optional_param('outputdir', 'autoreports', PARAM_TEXT); // Where to put the file.
$reportlayout = optional_param('reportlayout', 'onefulluserpersheet', PARAM_TEXT); // Where to put the file.
$reportscope = optional_param('reportscope', 'onefulluserpersheet', PARAM_TEXT); // Allcourses or currentcourse.
$reportformat = 'csv';

if ($reportlayout == 'onefulluserpersheet') {
    print_error('unsupported', 'report_trainingsessions');
} else if ($reportlayout == 'oneuserperrow') {
    $reporttype = 'summary';
    $range = 'group';
} else {
    $reporttype = 'sessions';
    $range = 'user';
}

ini_set('memory_limit', '512M');

if (!$course = $DB->get_record('course', array('id' => $id))) {
    die ('invalidcourse');
}
$context = context_course::instance($course->id);

// Security.

report_trainingsessions_back_office_access($course);

// Calculate start time. Defaults ranges to all course range.

if ($from == -1) {
    // Maybe we get it from parameters.
    $from = $course->startdate;
}

if ($to == -1) {
    // Maybe we get it from parameters.
    $to = time();
}

// Compute target groups.
$groups = report_trainingsessions_compute_groups($id, $groupid, $range);

$timesession = time();
$sessionday = date('Ymd', $timesession);
$filenametimesession = date(get_string('filetimesuffixformat', 'report_trainingsessions'), $timesession);

$testmax = 5;
$i = 0;

$uri = new moodle_url('/report/trainingsessions/tasks/'.$range.$reportformat.'report'.$reporttype.'_batch_task.php');

foreach ($groups as $group) {

    $group = array_shift($groups);
    $i++;

    if ($range == 'user') {
        // Process all users in a target group individually to generate a report per user.

        $targetusers = $group->target;

        // Filters teachers out.
        report_trainingsessions_filter_unwanted_users($targetusers, $course);

        if (!empty($targetusers)) {

            $current = time();
            if ($current > $timesession + $maxbatchduration) {
                mtrace("Could not finish batch. Too long");
                return;
            }

            foreach ($targetusers as $user) {
                $filerec = new StdClass;
                $filerec->contextid = $context->id;
                $filerec->component = 'report_trainingsessions';
                $filerec->filearea = 'reports';
                $filerec->itemid = $course->id;
                $filerec->filepath = "/{$outputdir}/{$sessionday}/";
                $filerec->filename = "trainingsessions_user_{$user->username}_{$reporttype}_".$filenametimesession.".csv";

                report_trainingsessions_process_user_file($user, $id, $from, $to, $timesession, $uri, $filerec, $reportscope);
            }
        } else {
            mtrace('no more compilable users in this group: '.$group->name);
        }
    } else {
        // Process the group globally sending groupid to batch task.

        $filerec = new StdClass;
        $filerec->contextid = $context->id;
        $filerec->component = 'report_trainingsessions';
        $filerec->filearea = 'reports';
        $filerec->itemid = $course->id;
        $filerec->filepath = "/{$outputdir}/{$sessionday}/";
        $filerec->filename = "trainingsessions_group_{$group->name}_{$reporttype}_".$filenametimesession.".csv";

        report_trainingsessions_process_group_file($group, $id, $from, $to, $timesession, $uri, $filerec, $reportscope);
    }
}
