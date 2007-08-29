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
require_once 'grade_export_xml.php';

$id       = required_param('id', PARAM_INT); // course id
$feedback = optional_param('feedback', '', PARAM_ALPHA);

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/xml:view', $context);


$strgrades = get_string('grades', 'grades');
$actionstr = get_string('modulename', 'gradeexport_xml');
$navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, 'export', 'xml');

$mform = new grade_export_form(null, array('idnumberrequired'=>true, 'publishing'=>true));

// process post information
if ($data = $mform->get_data()) {
    if ($data->itemids) {
        $items = array();
        foreach ($data->itemids as $itemid=>$selected) {
            if ($selected) {
                $items[] = $itemid;
            }
        }
        $itemidsurl = implode(",", $items);
    } else {
        //error?
        $itemidsurl = '';
    }

    // print the grades on screen for feedbacks

    $export = new grade_export($id, $data->itemids, $data->export_letters, !empty($data->key));

    $export->display_grades($feedback, $data->previewrows);

    // this redirect should trigger a download prompt
    if (empty($data->key)) {
        print_continue('export.php?id='.$id.'&amp;itemids='.$itemidsurl.'&amp;export_letters='.$data->export_letters);

    } else {
        if ($data->key == 1) {
            $data->key = create_user_key('grade/export', $USER->id, $COURSE->id, $data->iprestriction, $data->validuntil);
        }
        $link = $CFG->wwwroot.'/grade/export/xml/dump.php?id='.$id.'&amp;itemids='.$itemidsurl.'&amp;export_letters='.$data->export_letters.'&amp;key='.$data->key;
        echo "<a href=\"$link\">$link</a>";
    }
    exit;
}

$mform->display();

print_footer();
?>
