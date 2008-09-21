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

$courseid = optional_param('courseid', 1, PARAM_INT); // Course ID
$classid = required_param('classid', PARAM_INT);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);

if(!confirm_sesskey()) {
	print_error("badsessionkey","block_exabis_student_review");
}

if(count_records('block_exabstudreviteactoclas', 'teacherid', $USER->id, 'classid', $classid) == 0) {
	print_error("badclass","block_exabis_student_review");
}

block_exabis_student_review_print_header(array('review', 'reviewclass'));

$actPeriod = block_exabis_student_review_get_active_period();

if(!$classusers = get_records('block_exabstudrevistudtoclas', 'classid', $classid)) {
	print_error('nostudentstoreview','block_exabis_student_review');
}


/* Print the Students */
$table = new stdClass();

$table->head =
  array( get_string('name'),
		 get_string('teamplayer', 'block_exabis_student_review'),
		 get_string('responsibility', 'block_exabis_student_review'),
		 get_string('selfreliance', 'block_exabis_student_review'), 
		 get_string('evaluation', 'block_exabis_student_review'),
		 get_string('action') );
		 
$table->align = array('left', 'center', 'center', 'center', 'left', 'right');
$table->width = "90%";

foreach($classusers as $classuser) {
	$user = get_record('user', 'id', $classuser->studentid);
	if (!$user)
		continue;
	
	$link = '<a href="' . $CFG->wwwroot . '/blocks/exabis_student_review/review_student.php?courseid=' . $courseid . '&amp;classid=' . $classid . '&amp;sesskey=' . sesskey() . '&amp;studentid=' . $user->id . '">';

	$icons = $link.'<img src="' . $CFG->wwwroot . '/pix/i/edit.gif" width="16" height="16" alt="' . get_string('edit'). '" /></a>';
	$userdesc = print_user_picture($user->id, $courseid, $user->picture, 0, true, false) . ' ' . $link . fullname($user, $user->id).'</a>';
	
	$report = get_record('block_exabstudrevirevi', 'teacher_id', $USER->id, 'periods_id', $actPeriod->id, 'student_id', $user->id);

	if ($report)
		$table->data[] = array ($userdesc, $report->team, $report->resp, $report->inde, format_text($report->review), $icons);
	else
		$table->data[] = array ($userdesc, '', '', '', '', $icons);
}

print_table($table);

block_exabis_student_review_print_footer();
