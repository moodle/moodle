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
 * List of grade letters.
 *
 * @package   moodlecore
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid  = optional_param('id', SITEID, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url('/grade/edit/letter/index.php', array('id' => $courseid));

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:manageletters', $context)) {
    print_error('nopermissiontoviewletergrade');
}

$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'letter', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('letters', 'grades');

print_grade_page_head($courseid, 'letter', 'view', get_string('gradeletters', 'grades'));

$letters = grade_get_letters($context);

$data = array();

$max = 100;
foreach($letters as $boundary=>$letter) {
    $line = array();
    $line[] = format_float($max,2).' %';
    $line[] = format_float($boundary,2).' %';
    $line[] = format_string($letter);
    $data[] = $line;
    $max = $boundary - 0.01;
}

$table = new html_table();
$table->head  = array(get_string('max', 'grades'), get_string('min', 'grades'), get_string('letter', 'grades'));
$table->size  = array('30%', '30%', '40%');
$table->align = array('left', 'left', 'left');
$table->width = '30%';
$table->data  = $data;
$table->tablealign  = 'center';
echo html_writer::table($table);

echo $OUTPUT->footer();
