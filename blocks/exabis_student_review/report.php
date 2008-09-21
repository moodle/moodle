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

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:head', $context);

if (!$class = get_record('block_exabstudreviclas', 'userid', $USER->id)) {
	print_error('noclassfound', 'block_exabis_student_review');
}

block_exabis_student_review_print_header('report');

$actPeriod = block_exabis_student_review_get_active_period();

if(!$classusers = get_records_sql('SELECT s.id, s.studentid FROM ' . $CFG->prefix . 'block_exabstudrevistudtoclas s WHERE s.classid=\'' . $class->id . '\' ')) {
	print_error('nostudentstoreview','block_exabis_student_review');
}

/* Print the Students */
$table = new stdClass();

$table->head =
  array( get_string('name'),
		 get_string('teamplayer', 'block_exabis_student_review'),
		 get_string('responsibility', 'block_exabis_student_review'),
		 get_string('selfreliance', 'block_exabis_student_review'), 
		 get_string('action'));
		 
$table->align = array('left', 'center', 'center', 'center', 'right');
$table->width = "90%";

foreach($classusers as $classuser) {
	$user = get_record('user', 'id', $classuser->studentid);

	if (!$user)
		continue;
	
	$userReport = block_exabis_student_review_get_report($user->id, $actPeriod->id);

	$link = '<a href="' . $CFG->wwwroot . '/blocks/exabis_student_review/printstudent.php?courseid=' . $courseid . '&amp;studentid=' . $user->id . '&amp;sesskey=' . sesskey() . '">';

	$icons = $link.'<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/print.gif" width="16" height="16" alt="' . get_string('printversion', 'block_exabis_student_review'). '" /></a>';
	
	$studentdesc = print_user_picture($user->id, $courseid, $user->picture, 0, true, false) . ' ' . $link.fullname($user, $user->id).'</a>';
	
	$table->data[] = array($studentdesc, $userReport->team, $userReport->resp, $userReport->inde, $icons);
}

print_table($table);

echo '<a href="' . $CFG->wwwroot . '/blocks/exabis_student_review/printclass.php?courseid=' . $courseid . '&amp;classid=' . $class->id . '&amp;sesskey=' . sesskey() . '"><img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/print.gif" width="16" height="16" alt="' . get_string('printall', 'block_exabis_student_review'). '" /></a>';

block_exabis_student_review_print_footer();
