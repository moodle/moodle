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
$navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, 'export', 'ods');

$mform = new grade_export_form(null, array('publishing' => true));

// process post information
if ($data = $mform->get_data()) {
    $itemids = array();
    if ($data->itemids) {
        foreach ($data->itemids as $itemid=>$selected) {
            if ($selected) {
                $itemids[] = $itemid;
            }
        }
        $itemidsurl = implode(",", $itemids);
    } else {
        //error?
        $itemidsurl = '';
    }

    $export = new grade_export($id, $itemids, $data->export_letters);
    $export->display_grades($feedback, $data->previewrows);

    // this redirect should trigger a download prompt
    redirect('export.php?id='.$id.'&amp;itemids='.$itemidsurl.'&amp;export_letters='.$data->export_letters);
    exit;
}

$mform->display();

print_footer();
?>
