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


/// THIS SCRIPT IS CALLED WITH "require_once()" FROM index.php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);
$scope    = optional_param('scope', 'global', PARAM_ALPHA);

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (empty($CFG->enableoutcomes)) {
        redirect('../../index.php?id='.$courseid);
    }

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
    $context = get_context_instance(CONTEXT_SYSTEM);
}

require_capability('moodle/grade:manageoutcomes', $context);

$strgrades = get_string('grades');
$pagename  = get_string('outcomes', 'grades');

$navigation = grade_build_nav(__FILE__, $pagename, $courseid);

$strshortname        = get_string('shortname');
$strfullname         = get_string('fullname');
$strscale            = get_string('scale');
$strstandardoutcome  = get_string('outcomesstandard', 'grades');
$strcustomoutcomes   = get_string('outcomescustom', 'grades');
$strdelete           = get_string('delete');
$stredit             = get_string('edit');
$srtcreatenewoutcome = get_string('outcomecreate', 'grades');
$stritems            = get_string('items', 'grades');
$strcourses          = get_string('courses');
$stredit             = get_string('edit');
$strexport           = get_string('export', 'grades');

if (!confirm_sesskey()) {
    break;
}

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$caneditsystemscales = has_capability('moodle/course:managescales', $systemcontext);

if ($courseid) {

    print_grade_page_head($courseid, 'outcome', 'import', get_string('importoutcomes', 'grades'));

    $caneditcoursescales = has_capability('moodle/course:managescales', $context);

} else {
    admin_externalpage_print_header();
    $caneditcoursescales = $caneditsystemscales;
}

$imported_file = $upload_form->_upload_manager->files;

if ($imported_file['userfile']['size'] == 0) {
    redirect('index.php'. ($courseid ? "?id=$courseid" : ''), get_string('importfilemissing', 'grades'));
}

/// which scope are we importing the outcomes in?
if (isset($courseid) && ($scope  == 'local' || $scope == 'custom')) {
    // custom scale
    $local_scope = true;
} elseif (($scope == 'global') && has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
    // global scale
    $local_scope = false;
} else {
    // shouldn't happen .. user might be trying to access this script without the right permissions.
    redirect('index.php', get_string('importerror', 'grades'));
}

// open the file, start importing data
if ($handle = fopen($imported_file['userfile']['tmp_name'], 'r')) {
    $line = 0; // will keep track of current line, to give better error messages.
    $file_headers = '';

    // $csv_data needs to have at least these columns, the value is the default position in the data file.
    $headers = array('outcome_name' => 0, 'outcome_shortname' => 1, 'scale_name' => 3, 'scale_items' => 4);
    $optional_headers = array('outcome_description'=>2, 'scale_description' => 5);
    $imported_headers = array(); // will later be initialized with the values found in the file

    $fatal_error = false;

    // data should be separated by a ';'.  *NOT* by a comma!  TODO: version 2.0
    // or whenever we can depend on PHP5, set the second parameter (8192) to 0 (unlimited line length) : the database can store over 128k per line.
    while ( $csv_data = fgetcsv($handle, 8192, ';', '"')) { // if the line is over 8k, it won't work...
        $line++;

        // be tolerant on input, as fgetcsv returns "an array comprising a single null field" on blank lines
        if ($csv_data == array(null)) {
            continue;
        }

        // on first run, grab and analyse the header
        if ($file_headers == '') {

            $file_headers = array_flip($csv_data); // save the header line ... TODO: use the header line to let import work with columns in arbitrary order

            $error = false;
            foreach($headers as $key => $value) {
                // sanity check #1: make sure the file contains all the mandatory headers
                if (!array_key_exists($key, $file_headers)) {
                    $error = true;
                    break;
                }
            }
            if ($error) {
                print_box_start('generalbox importoutcomenofile');
                echo get_string('importoutcomenofile', 'grades', $line);
                echo print_single_button($CFG->wwwroot.'/grade/edit/outcome/index.php', array('id'=> $courseid), get_string('back'), 'get', '_self', true);
                print_box_end();
                $fatal_error = true;
                break;
            }

            foreach(array_merge($headers, $optional_headers) as $header => $position) {
                // match given columns to expected columns *into* $headers
                $imported_headers[$header] = $file_headers[$header];
            }

            continue; // we don't import headers
        }

        // sanity check #2: every line must have the same number of columns as there are
        // headers.  If not, processing stops.
        if ( count($csv_data) != count($file_headers) ) {
            print_box_start('generalbox importoutcomenofile');
            echo get_string('importoutcomenofile', 'grades', $line);
            echo print_single_button($CFG->wwwroot.'/grade/edit/outcome/index.php', array('id'=> $courseid), get_string('back'), 'get', '_self', true);
            print_box_end();
            $fatal_error = true;
            //print_box(var_export($csv_data, true) ."<br />". var_export($header, true));
            break;
        }

        // sanity check #3: all required fields must be present on the current line.
        foreach ($headers as $header => $position) {
            if ($csv_data[$imported_headers[$header]] == '') {
                print_box_start('generalbox importoutcomenofile');
                echo get_string('importoutcomenofile', 'grades', $line);
                echo print_single_button($CFG->wwwroot.'/grade/edit/outcome/index.php', array('id'=> $courseid), get_string('back'), 'get', '_self', true);
                print_box_end();
                $fatal_error = true;
                break;
            }
        }

        //var_dump($csv_data);
        //$db->debug = 3498723498237; // .. very large randomly-typed random value

        // MDL-17273 errors in csv are not preventing import from happening. We break from the while loop here
        if ($fatal_error) {
            break;
        }

        if ($local_scope) {
            $outcome = get_records_select('grade_outcomes', 'shortname = \''. addslashes($csv_data[$imported_headers['outcome_shortname']]) .'\' and courseid = '. $courseid );
        } else {
            $outcome = get_records_select('grade_outcomes', 'shortname = \''. addslashes($csv_data[$imported_headers['outcome_shortname']]) .'\' and courseid is null');
        }
        //var_export($outcome);

        if ($outcome) {
            // already exists, print a message and skip.
            print_box(get_string('importskippedoutcome', 'grades', $csv_data[$imported_headers['outcome_shortname']]));
            continue;
        }
        // new outcome will be added, search for compatible existing scale...
        $scale = get_records_select('scale', 'name =\''. addslashes($csv_data[$imported_headers['scale_name']]) .'\' and scale =\''. addslashes($csv_data[$imported_headers['scale_items']]) .'\' and (courseid = '. $courseid .' or courseid = 0)');

        if ($scale) {
            // already exists in the right scope: use it.
            $scale_id = key($scale);
        } else {
            if (!has_capability('moodle/course:managescales', $context)) {
                print_box(get_string('importskippednomanagescale', 'grades', $csv_data[$imported_headers['outcome_shortname']]));
                continue;
            } else {
                // scale doesn't exists : create it.
                $scale_data = array('name' => $csv_data[$imported_headers['scale_name']],
                        'scale' => $csv_data[$imported_headers['scale_items']],
                        'description' => $csv_data[$imported_headers['scale_description']],
                        'userid' => $USER->id);

                if ($local_scope) {
                    $scale_data['courseid'] = $courseid;
                } else {
                    $scale_data['courseid'] = 0; // 'global' : scale use '0', outcomes use null
                }
                $scale = new grade_scale($scale_data);
                $scale_id = $scale->insert();
            }
        }

        // add outcome
        $outcome_data = array('shortname' => $csv_data[$imported_headers['outcome_shortname']],
                'fullname' => $csv_data[$imported_headers['outcome_name']],
                'scaleid' => $scale_id,
                'description' => $csv_data[$imported_headers['outcome_description']],
                'usermodified' => $USER->id);

        if ($local_scope) {
            $outcome_data['courseid'] = $courseid;
        } else {
            $outcome_data['courseid'] = null; // 'global' : scale use '0', outcomes use null
        }
        $outcome = new grade_outcome($outcome_data);
        $outcome_id = $outcome->insert();

        $outcome_success_strings = new StdClass();
        $outcome_success_strings->name = $outcome_data['fullname'];
        $outcome_success_strings->id = $outcome_id;
        print_box(get_string('importoutcomesuccess', 'grades', $outcome_success_strings));
    }
} else {
    print_box(get_string('importoutcomenofile', 'grades', 0));
}

// finish
fclose($handle);

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}

?>
