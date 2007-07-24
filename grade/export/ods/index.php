<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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
require_once 'grade_export_ods.php';

$id       = required_param('id', PARAM_INT); // course id
$feedback = optional_param('feedback', '', PARAM_ALPHA);

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/ods:view', $context);


$strgrades = get_string('grades', 'grades');
$actionstr = get_string('modulename', 'gradeexport_ods');

$gradenav = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";
$gradenav .= " -> <a href=\"$CFG->wwwroot/grade/index.php?id=$course->id\">$strgrades</a>";
$gradenav .= " -> $actionstr";

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $gradenav);
// process post information
if (($data = data_submitted()) && confirm_sesskey()) {

    if (!is_array($data->itemids)) {
        $itemidsurl = $data->itemids;
    } else {
        $itemidsurl = implode(",",$data->itemids);
    }

    // print the grades on screen for feedbacks

    $export = new grade_export($id, $data->itemids);
    $export->display_grades($feedback);

    // this redirect should trigger a download prompt
    redirect('export.php?id='.$id.'&amp;itemids='.$itemidsurl);
    exit;
}

print_gradeitem_selections($id);
print_footer();
?>
