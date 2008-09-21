<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 exabis internet solutions <info@exabis.at>
*  All rights reserved
*
*  You can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This module is based on the Collaborative Moodle Modules from
*  NCSA Education Division (http://www.ncsa.uiuc.edu)
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require("inc.php");

$courseid       = optional_param('courseid', 1, PARAM_INT); // Course ID
$showall        = optional_param('showall', 0, PARAM_BOOL);
$searchtext     = optional_param('searchtext', '', PARAM_ALPHANUM); // search string

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:head', $context);



block_exabis_student_review_print_header('configuration');

if (!$class = get_record('block_exabstudreviclas', 'userid', $USER->id)) {
	redirect('configuration_class.php?courseid=' . $courseid, get_string('redirectingtoclassinput', 'block_exabis_student_review'));
}

print_heading($class->class);

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration_class.php',
					array('courseid' => $courseid, 'sesskey' => sesskey()),
					get_string('editclassname', 'block_exabis_student_review'));

/* Print the Students */
$table = new stdClass();

$table->head = array (get_string('firstname'), get_string('lastname'), get_string('email'));
$table->align = array ("left", "left", "left");
$table->width = "90%";

$usertoclasses = get_records('block_exabstudrevistudtoclas', 'classid', $class->id, 'studentid');

$classusers = array();
foreach($usertoclasses as $usertoclass) {
	$user = get_record('user', 'id', $usertoclass->studentid);
	$table->data[] = array ($user->firstname, $user->lastname, $user->email);
}

print_table($table);

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration_classmembers.php',
					array('courseid' => $courseid, 'sesskey' => sesskey()),
					get_string('editclassmemberlist', 'block_exabis_student_review'));

/* Print the Classes */
$table = new stdClass();

$table->head = array (get_string('firstname'), get_string('lastname'), get_string('email'));
$table->align = array ("left", "left", "left");
$table->width = "90%";

$usertoclasses = get_records('block_exabstudreviteactoclas', 'classid', $class->id, 'teacherid');

$classusers = array();
foreach($usertoclasses as $usertoclass) {
	$user = get_record('user', 'id', $usertoclass->teacherid);
	$table->data[] = array ($user->firstname, $user->lastname, $user->email);
}

print_table($table);

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration_classteachers.php',
					array('courseid' => $courseid, 'sesskey' => sesskey()),
					get_string('editclassteacherlist', 'block_exabis_student_review'));

block_exabis_student_review_print_footer();
