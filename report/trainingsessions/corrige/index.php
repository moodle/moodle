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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
// exit(var_dump($CFG->libdir,$CFG->dirroot ));
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/lib/statslib.php');

// echo $CFG->libdir => /var/www/html/lib;

$id = required_param('id', PARAM_INT); // Course id.
$view = optional_param('view', 'user', PARAM_ALPHA);
$report = optional_param('report', STATS_REPORT_ACTIVE_COURSES, PARAM_INT);
$time = optional_param('time', 0, PARAM_INT);

// Form bounce somewhere ?
$view = (empty($view)) ? 'user' : $view;

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourse');
}


// Security.
require_course_login($course);
$context = context_course::instance($course->id);
require_capability('report/trainingsessions:view', $context);

$renderer = $PAGE->get_renderer('report_trainingsessions');

//----------------------  Hadrien 02/02/19 ---------------------------------
// if (is_siteadmin()) $strreports = get_string('reports');
$strcourseoverview = get_string('trainingsessions', 'report_trainingsessions');

@ini_set('max_execution_time', '3000');
raise_memory_limit('250M');

// Defer printing header in report views after potential redirections.

if (file_exists($CFG->dirroot."/report/trainingsessions/{$view}report.php")) {
    include_once($CFG->dirroot."/report/trainingsessions/{$view}report.php");
} else {
    print_error('errorbadviewid', 'report_trainingsessions');
}

// echo $CFG->dirroot."/report/trainingsessions/{$view}report.php";

echo $OUTPUT->footer();

