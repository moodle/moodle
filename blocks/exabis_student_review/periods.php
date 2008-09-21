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
require_capability('block/exabis_student_review:editperiods', $context);

$strperiods = get_string('periods', 'block_exabis_student_review');


block_exabis_student_review_print_header('periods');

block_exabis_student_review_check_periods(true);

if (!$periods = get_records('block_exabstudreviperi')) {
	redirect('configuration_period.php?courseid=' . $courseid, get_string('redirectingtoperiodsinput', 'block_exabis_student_review'));
}

/* Print the periods */
$table = new stdClass();

$table->head = array(
	get_string('perioddescription', 'block_exabis_student_review'), 
	get_string('starttime', 'block_exabis_student_review'), 
	get_string('endtime', 'block_exabis_student_review'),
	get_string('action')
);

$table->align = array("left", "left", "left", "right");
$table->width = "90%";

foreach($periods as $period) {

	$link = '<a href="' . $CFG->wwwroot . '/blocks/exabis_student_review/configuration_period.php?courseid=' . $courseid . '&amp;periodid=' . $period->id . '&amp;sesskey=' . sesskey() . '&amp;action=edit">';

	$icons = $link.'<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/edit.gif" width="16" height="16" alt="' . get_string('edit'). '" /></a>
			  <a href="' . $CFG->wwwroot . '/blocks/exabis_student_review/configuration_period.php?courseid=' . $courseid . '&amp;periodid=' . $period->id . '&amp;sesskey=' . sesskey() . '&amp;action=delete"><img src="' . $CFG->wwwroot . '/pix/t/delete.gif" width="11" height="11" alt="' . get_string('delete'). '" /></a> ';

	$starttime = date('d. M. Y - H:i', $period->starttime);
	$endtime = date('d. M. Y - H:i', $period->endtime);
	
	$table->data[] = array ($link.$period->description.'</a>', $starttime, $endtime, $icons);
}

print_table($table);

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration_period.php',
					array('courseid' => $courseid, 'sesskey' => sesskey(), 'action' => 'new'),
					get_string('newperiod', 'block_exabis_student_review'));

block_exabis_student_review_print_footer();
