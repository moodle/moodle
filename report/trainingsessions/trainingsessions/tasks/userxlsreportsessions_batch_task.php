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
 * This script handles the session report generation in batch task for a single user.
 * It will produce a single PDF report that is pushed immediately to output
 * for downloading by a batch agent. No file is stored into the system.
 * userid must be provided.
 * This script should be sheduled in a CURL call stack or a multi_CURL parallel call.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');

ob_start();
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/xlsrenderers.php');
require_once($CFG->dirroot.'/report/trainingsessions/lib/excellib.php');

$id = required_param('id', PARAM_INT); // The course id.
$userid = required_param('userid', PARAM_INT); // The user id.

ini_set('memory_limit', '512M');

if (!$course = $DB->get_record('course', array('id' => $id))) {
    die ('Invalid course ID');
}
$context = context_course::instance($course->id);

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    // Do NOT print_error here as we are a document writer.
    die ('Invalid user ID');
}

$input = report_trainingsessions_batch_input($course);

// Security.
report_trainingsessions_back_office_access($course, $userid);

// Generate XLS.

$filename = "trainingsessions_user_{$userid}_report_".$input->filenametimesession.'.xls';

$workbook = new MoodleExcelWorkbookTS("-");
if (!$workbook) {
    die("Excel Librairies Failure");
}

// Sending HTTP headers.
ob_end_clean();
$workbook->send($filename);

$xlsformats = report_trainingsessions_xls_formats($workbook);
$startrow = report_trainingsessions_count_header_rows($course->id);

$worksheet = report_trainingsessions_init_worksheet($auser->id, $startrow, $xlsformats, $workbook, 'sessions');

report_trainingsessions_print_usersessions($worksheet, $userid, $startrow, $input->from, $input->to, $id, $xlsformats);

// Sending HTTP headers.
ob_end_clean();

$workbook->close();

exit(0);
