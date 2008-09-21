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
require_once($CFG->dirroot . '/blocks/exabis_student_review/lib/edit_form.php');

$courseid       = optional_param('courseid', 1, PARAM_INT); // Course ID
$showall        = optional_param('showall', 0, PARAM_BOOL);
$searchtext     = optional_param('searchtext', '', PARAM_ALPHANUM); // search string

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:head', $context);

if (!$class = get_record('block_exabstudreviclas', 'userid', $USER->id)) {
	$class = new stdClass();
	$class->courseid = $courseid;
	$class->class = '';
}

$classform = new class_edit_form();
if ($classedit = $classform->get_data()) {
	if(!confirm_sesskey()) {
		print_error("badsessionkey","block_exabis_student_review");
	}
	
	$newclass = new stdClass();
	$newclass->timemodified = time();
	$newclass->userid = $USER->id;
	$newclass->class = $classedit->class;
	
	// das ist glaub ich falsch, weil $class noch nicht definiert ist!
	if(isset($class->id)) {
		$newclass->id = $class->id;
		if (!update_record('block_exabstudreviclas', $newclass)) {
			print_error('errorupdatingclass', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'edit', 'configuration.php?courseid=' . $courseid, $class->id);
	}
	else {
		if (!($class->id = insert_record('block_exabstudreviclas', $newclass))) {
			print_error('errorinsertingclass', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'new', 'configuration.php?courseid=' . $courseid, '');
	}
	redirect('configuration.php?courseid=' . $courseid);
}


block_exabis_student_review_print_header(array('configuration', 'editclassname'));

print_heading($class->class);

$classform->set_data($class);
$classform->display();

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration.php',
					array('courseid' => $courseid),
					get_string('back', 'block_exabis_student_review'));
					
block_exabis_student_review_print_footer();
