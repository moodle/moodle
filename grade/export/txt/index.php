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

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/export/lib.php';
require_once 'grade_export_txt.php';

$id = required_param('id', PARAM_INT); // course id

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/txt:view', $context);

print_grade_page_head($COURSE->id, 'export', 'txt', get_string('exportto', 'grades') . ' ' . get_string('modulename', 'gradeexport_txt'));

if (!empty($CFG->gradepublishing)) {
    $CFG->gradepublishing = has_capability('gradeexport/txt:publish', $context);
}

$mform = new grade_export_form(null, array('includeseparator'=>true, 'publishing' => true));

$groupmode    = groups_get_course_groupmode($course);   // Groups are being used
$currentgroup = groups_get_course_group($course, true);
if ($groupmode == SEPARATEGROUPS and !$currentgroup and !has_capability('moodle/site:accessallgroups', $context)) {
    print_heading(get_string("notingroup"));
    print_footer($course);
    die;    
}

// process post information
if ($data = $mform->get_data()) {
    $export = new grade_export_txt($course, $currentgroup, '', false, false, $data->display, $data->decimals);

    // print the grades on screen for feedback

    $export->process_form($data);
    $export->print_continue();
    $export->display_preview();
    print_footer($course);
    exit;
}

groups_print_course_menu($course, 'index.php?id='.$id);
echo '<div class="clearer"></div>';

$mform->display();

print_footer();
?>
