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
require_once $CFG->libdir.'/filelib.php';
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/gradelib.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/import/lib.php';

$url = required_param('url', PARAM_URL); // only real urls here
$id  = required_param('id', PARAM_INT); // course id

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:import', $context);
require_capability('gradeimport/xmlurl:view', $context);


// Large files are likely to take their time and memory. Let PHP know
// that we'll take longer, and that the process should be recycled soon
// to free up memory.
@set_time_limit(0);
@raise_memory_limit("192M");
if (function_exists('apache_child_terminate')) {
    @apache_child_terminate();
}

$text = download_file_content($url);
if ($text === false) {
    error('Can not read file');
}

$status = true;
$error = '';

$importcode = time();
$newgrades = array();

$content = xmlize($text);

if ($results = $content['results']['#']['result']) {

    foreach ($results as $i => $result) {
        if (!$grade_items = grade_item::fetch_all(array('idnumber'=>$result['#']['assignment'][0]['#'], 'courseid'=>$course->id))) {
            // gradeitem does not exist
            // no data in temp table so far, abort
            $status = false;
            $error  = 'incorrect grade item idnumber'; //TODO: localize
            break;
        } else if (count($grade_items) != 1) {
            $status = false;
            $error  = 'duplicate grade item idnumber'; //TODO: localize
            break;
        } else {
            $grade_item = reset($grade_items);
        }

        // grade item locked, abort
        if ($grade_item->locked) {
            $status = false;
            $error  = get_string('gradeitemlocked', 'grades');
            break;
        }

        // check if grade_grade is locked and if so, abort
        if ($grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$result['#']['student'][0]['#']))) {
            if ($grade_grade->locked) {
                // individual grade locked, abort
                $status = false;
                $error  = get_string('gradegradeslocked', 'grades');
                break;
            }
        }

        if (isset($result['#']['score'][0]['#'])) {
            $newgrade = new object();
            $newgrade->itemid = $grade_item->id;
            $newgrade->grade  = $result['#']['score'][0]['#'];
            $newgrade->userid = $result['#']['student'][0]['#'];
            $newgrades[] = $newgrade;
        }
    }

    // loop through info collected so far
    if ($status && !empty($newgrades)) {
        foreach ($newgrades as $newgrade) {

            // check if user exist
            if (!$user = get_record('user', 'id', addslashes($newgrade->userid))) {
                // no user found, abort
                $status = false;
                $error = get_string('baduserid', 'grades');
                break;
            }

            // check grade value is a numeric grade
            if (!is_numeric($newgrade->grade)) {
                $status = false;
                $error = get_string('badgrade', 'grades');
                break;
            }

            // insert this grade into a temp table
            $newgrade->import_code = $importcode;
            if (!insert_record('grade_import_values', addslashes_recursive($newgrade))) {
                $status = false;
                // could not insert into temp table
                $error = get_string('importfailed', 'grades');
                break;
            }
        }
    }
} else {
    // no results section found in xml,
    // assuming bad format, abort import
    $status = false;
    $error = get_string('badxmlformat', 'grade');
}

if ($status) {
    /// comit the code if we are up this far

    if (defined('USER_KEY_LOGIN')) {
        if (grade_import_commit($id, $importcode, false)) {
            echo 'ok';
            die;
        } else {
            error('Grade import error'); //TODO: localize
        }

    } else {
        $strgrades = get_string('grades', 'grades');
        $actionstr = get_string('xmlurl', 'grades');
        $navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

        print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
        print_grade_plugin_selector($id, 'import', 'xmlurl');

        grade_import_commit($id, $importcode);

        print_footer();
        die;
    }

} else {
    import_cleanup($importcode);
    error($error);
}

?>