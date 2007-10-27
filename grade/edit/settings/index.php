<?php // $Id$

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
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'form.php';

$courseid  = optional_param('id', SITEID, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('moodle/grade:manage', $context);

$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'settings', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('coursesettings', 'grades');

$navigation = grade_build_nav(__FILE__, $pagename, $courseid);

$returnurl = $CFG->wwwroot.'/grade/index.php?id='.$course->id;

$mform = new course_settings_form();

$settings = grade_get_settings($course->id);

$mform->set_data($settings);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $data = (array)$data;
    $general = array('displaytype', 'decimalpoints', 'aggregationposition');
    foreach ($data as $key=>$value) {
        if (!in_array($key, $general) and strpos($key, 'report_') !== 0
                                      and strpos($key, 'import_') !== 0
                                      and strpos($key, 'export_') !== 0) {
            continue;
        }
        if ($value == -1) {
            $value = null;
        }
        grade_set_setting($course->id, $key, $value);
    }

    redirect($returnurl);
}

/// Print header
print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));
/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'settings');

$mform->display();

print_footer($course);

?>
