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
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../../config.php');
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/csvrenderers.php');

$id = required_param('id', PARAM_INT); // The course id.
$groupid = required_param('groupid', PARAM_INT); // Group id.

ini_set('memory_limit', '512M');

if (!$course = $DB->get_record('course', array('id' => $id))) {
    // Do NOT print_error here as we are a document writer.
    die('Invalid course ID');
}
$context = context_course::instance($course->id);

$input = report_trainingsessions_batch_input($course);

// Security.
report_trainingsessions_back_office_access($course);

// Compute target group.

$group = $DB->get_record('groups', array('id' => $groupid));

if ($groupid) {
    $targetusers = groups_get_members($groupid);
    $filename = "trainingsessions_group_{$groupid}_report_".$input->filenametimesession.".csv";
} else {
    $targetusers = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
    $filename = "trainingsessions_course_{$course->id}_report_".$input->filenametimesession.".csv";
}

// Filter out non compiling users.
report_trainingsessions_filter_unwanted_users($targetusers, $course);

// Print result.

$csvbuffer = '';
report_trainingsessions_print_global_header($csvbuffer);

if (!empty($targetusers)) {
    // generate CSV.

    foreach ($targetusers as $auser) {

        $logusers = $auser->id;
        $logs = use_stats_extract_logs($input->from, $input->to, $auser->id, $course->id);
        $aggregate = use_stats_aggregate_logs($logs, $input->from, $input->to);
        $weekaggregate = use_stats_aggregate_logs($logs, $input->from, $input->from - WEEKSECS);

        $cols = report_trainingsessions_get_summary_cols();
        report_trainingsessions_print_global_raw($course->id, $cols, $auser, $aggregate, $weekaggregate, $csvbuffer);
    }

}

// Sending HTTP headers.
ob_end_clean();
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment filename=\"$filename\";");
header("Content-Transfer-Encoding: binary");
echo $csvbuffer;

// echo '200';