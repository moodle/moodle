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

$courseid = optional_param('courseid', 1, PARAM_INT); // Course ID
$classid = required_param('classid', PARAM_INT);
$studentid = required_param('studentid', PARAM_INT);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);

if(!confirm_sesskey()) {
	print_error('badsessionkey', 'block_exabis_student_review');
}

if(count_records('block_exabstudreviteactoclas', 'teacherid', $USER->id, 'classid', $classid) == 0) {
	print_error('badclass', 'block_exabis_student_review');
}

if(count_records('block_exabstudrevistudtoclas', 'studentid', $studentid, 'classid', $classid) == 0) {
	print_error('badstudent', 'block_exabis_student_review');
}

$strstudentreview = get_string('reviewstudent', 'block_exabis_student_review');
$strclassreview = get_string('reviewclass', 'block_exabis_student_review');
$strreview = get_string('review', 'block_exabis_student_review');

$actPeriod = block_exabis_student_review_get_active_period();

$formdata = new stdClass();
if(!$reviewdata = get_record('block_exabstudrevirevi', 'teacher_id', $USER->id, 'periods_id', $actPeriod->id, 'student_id', $studentid)) {
	$formdata->courseid = $courseid;
	$formdata->studentid = $studentid;
	$formdata->classid = $classid;
	$formdata->team = 1;
	$formdata->resp = 1;
	$formdata->inde = 1;
	$formdata->review = '';
}
else {
	$formdata->courseid = $courseid;
	$formdata->studentid = $studentid;
	$formdata->classid = $classid;
	$formdata->team = $reviewdata->team;
	$formdata->resp = $reviewdata->resp;
	$formdata->inde = $reviewdata->inde;
	$formdata->review = $reviewdata->review;
}

$studentform = new student_edit_form();

if ($studentedit = $studentform->get_data()) {
	$newreview = new stdClass();
	$newreview->timemodified = time();
	$newreview->student_id = $studentid;
	$newreview->periods_id = $actPeriod->id;
	$newreview->teacher_id = $USER->id;
	$newreview->team = $studentedit->team;
	$newreview->resp = $studentedit->resp;
	$newreview->inde = $studentedit->inde;
	$newreview->review = $studentedit->review;
	
	trusttext_after_edit($newreview->review, $context);
	
	if(isset($reviewdata->id)) {
		$newreview->id = $reviewdata->id;
		if (!update_record('block_exabstudrevirevi', $newreview)) {
			print_error('errorupdatingstudent', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'edit', 'review_student.php?courseid=' . $courseid, $classid);
	}
	else {
		if (!($class->id = insert_record('block_exabstudrevirevi', $newreview))) {
			print_error('errorinsertingstudent', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'new', 'review_student.php?courseid=' . $courseid, '');
	}
	redirect($CFG->wwwroot . '/blocks/exabis_student_review/review_class.php?courseid=' . $courseid . '&amp;classid=' . $classid . '&amp;sesskey=' . sesskey());
}

block_exabis_student_review_print_header(array('review', 
		array('name' => $strclassreview, 'link' => $CFG->wwwroot.'/blocks/exabis_student_review/review_class.php?courseid='.$courseid.
			'&amp;classid='.$classid.'&amp;sesskey='.sesskey()),
	'='.$strstudentreview
), array('noheading'=>true));

$student = get_record('user', 'id', $studentid);
$studentdesc = print_user_picture($student->id, $courseid, $student->picture, 0, true, false) . ' ' . fullname($student, $student->id);

print_heading($studentdesc);

$studentform->set_data($formdata);
$studentform->display();

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/review_class.php',
					array('courseid' => $courseid, 'classid' => $classid, 'sesskey' => sesskey()),
					get_string('back', 'block_exabis_student_review'));

block_exabis_student_review_print_footer();
