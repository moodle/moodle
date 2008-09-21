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

require('inc.php');

$courseid = optional_param('courseid', 1, PARAM_INT); // Course ID

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:head', $context);

if (!$class = get_record('block_exabstudreviclas', 'userid', $USER->id)) {
	print_error('noclassfound', 'block_exabis_student_review');
}

$actPeriod = block_exabis_student_review_get_active_period();

if(!$mystudents = get_records_sql('SELECT s.id, s.studentid, r.team, r.resp, r.inde, r.review FROM ' . $CFG->prefix . 'block_exabstudrevistudtoclas s LEFT JOIN ' . $CFG->prefix . 'block_exabstudrevirevi r ON s.studentid=r.student_id WHERE s.classid=\'' . $class->id . '\'')) {
	print_error('studentsnotfound','block_exabis_student_review');
}

block_exabis_student_review_print_student_report_header();
foreach($mystudents as $mystudent) {
	block_exabis_student_review_print_student_report($mystudent->studentid, $actPeriod->id, $class->class);
	echo '<p style=\'page-break-before: always;\'>&nbsp;</p>';
}

block_exabis_student_review_print_student_report_footer();
