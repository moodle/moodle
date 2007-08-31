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
require_once 'grade_export_xls.php';

$id = required_param('id', PARAM_INT); // course id

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/xls:view', $context);


$strgrades = get_string('grades', 'grades');
$actionstr = get_string('modulename', 'gradeexport_xls');
$navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, 'export', 'xls');

$mform = new grade_export_form(null, array('publishing' => true));

// process post information
if ($data = $mform->get_data()) {
    $export = new grade_export_xls($course, get_current_group($course->id));

    // print the grades on screen for feedbacks
    $export->process_form($data);
    $export->display_preview();
    $export->print_continue();
    die;
}

//TODO: add course group selector here

$mform->display();

print_footer();
?>
