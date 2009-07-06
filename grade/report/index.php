<?php //$Id$

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
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

require_once '../../config.php';

$courseid = required_param('id', PARAM_INT);

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

/// find all accessible reports
$reports = get_plugin_list('gradereport');     // Get all installed reports

foreach ($reports as $plugin => $plugindir) {                      // Remove ones we can't see
    if (!has_capability('gradereport/'.$plugin.':view', $context)) {
        unset($reports[$key]);
    }
}

if (empty($reports)) {
    print_error('noreports', 'debug', $CFG->wwwroot.'/course/view.php?id='.$course->id); // TODO: localize
}

if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}

if (!empty($USER->grade_last_report[$course->id])) {
    $last = $USER->grade_last_report[$course->id];
} else {
    $last = null;
}

if (!array_key_exists($last, $reports)) {
    $last = null;
}

if (empty($last)) {
    if (array_key_exists('grader', $reports)) {
        $last = 'grader';

    } else if (array_key_exists('user', $reports)) {
        $last = 'user';

    } else {
        $last = key(reset($reports));
    }
}

//redirect to last or guessed report
redirect($CFG->wwwroot.'/grade/report/'.$last.'/index.php?id='.$course->id);

