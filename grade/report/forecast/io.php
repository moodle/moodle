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
 * The gradebook forecast report grade input post endpoint page
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/forecast/lib.php';

$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

require_login();

if ( ! $course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

$gpr = new grade_plugin_return(['type' => 'report', 'plugin' => 'forecast', 'courseid' => $courseid, 'userid' => $userid]);
$context = context_course::instance($courseid);
$PAGE->set_context($context);

// get report instance with injected grade item input
$report = new grade_report_forecast($courseid, $gpr, $context, $userid, null, $_POST);

// return the json encoded response
echo $report->getJsonResponse();