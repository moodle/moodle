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
 * Assign list report
 *
 * @package    block_personal
 * @subpackage assignlist
 * @copyright  2016 HsuanTang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__).'/../../config.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');
require_once($CFG->dirroot . '/blocks/personal/lib.php');

require_login();

$result = mod_assign_external::get_assignments();
$courses = $result['courses'];

$table = new html_table();
$table->head  = array('Course Full Name', 'Course Short Name', 'Assignment', 'Submitted');
$table->colclasses = array('leftalign coursefullname', 'leftalign courseshortname', 'leftalign assignmentname', 'leftalign submitted');
$table->attributes['class'] = 'admintable generaltable';
$table->data  = array();
foreach ($courses as $course) {
	foreach ($course['assignments'] as $assignment) {
		$row = array();
		$row[] = $course['fullname'];
		$row[] = $course['shortname'];
		$row[] = $assignment['name'];
		
		$status = get_assignment_status($assignment['id']);
		$row[] = ($status == 'submitted') ? 'submitted' : 'not yet';
		
		$table->data[] = $row;
	}
}

$PAGE->set_context(context_user::instance($USER->id));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('assignlist', 'block_personal'));

echo html_writer::table($table);

echo $OUTPUT->footer();
