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
require_once 'lib.php';
require_once $CFG->libdir.'/filelib.php';

$url       = required_param('url', PARAM_URL); // only real urls here
$id        = required_param('id', PARAM_INT); // course id
$feedback  = optional_param('feedback', 0, PARAM_BOOL);

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:import', $context);
require_capability('gradeimport/xml:view', $context);


// Large files are likely to take their time and memory. Let PHP know
// that we'll take longer, and that the process should be recycled soon
// to free up memory.
@set_time_limit(0);
@raise_memory_limit("256M");
if (function_exists('apache_child_terminate')) {
    @apache_child_terminate();
}

$text = download_file_content($url);
if ($text === false) {
    error('Can not read file');
}

$error = '';
$importcode = import_xml_grades($text, $course, $error);

if ($importcode !== false) {
    /// comit the code if we are up this far

    if (defined('USER_KEY_LOGIN')) {
        if (grade_import_commit($id, $importcode, $feedback, false)) {
            echo 'ok';
            die;
        } else {
            error('Grade import error'); //TODO: localize
        }

    } else {
        $strgrades = get_string('grades', 'grades');
        $actionstr = get_string('xml', 'grades');
        $navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

        print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
        print_grade_plugin_selector($id, 'import', 'xml');

        grade_import_commit($id, $importcode, $feedback, true);

        print_footer();
        die;
    }

} else {
    error($error);
}

?>
