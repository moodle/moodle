<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

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


$strgrades = get_string('grades', 'grades');
$actionstr = get_string('modulename', 'gradeexport_txt');
$navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, 'export', 'txt');

if (!empty($CFG->gradepublishing)) {
    $CFG->gradepublishing = has_capability('gradeexport/txt:publish', $context);
}

$mform = new grade_export_form(null, array('includeseparator'=>true, 'publishing' => true));

// process post information
if ($data = $mform->get_data()) {
    $export = new grade_export_txt($course, groups_get_course_group($course), '', false, false, $data->display, $data->decimals);

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
