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

/**
 * Import outcomes from a file
 *
 * @package   core_grades
 * @copyright 2008 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once('import_outcomes_form.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);
$scope    = optional_param('scope', 'global', PARAM_ALPHA);

$PAGE->set_url('/grade/edit/outcome/import.php', array('courseid' => $courseid));

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        print_error('nocourseid');
    }
    require_login($course);
    $context = context_course::instance($course->id);

    if (empty($CFG->enableoutcomes)) {
        redirect('../../index.php?id='.$courseid);
    }

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
    $context = context_system::instance();
}

require_capability('moodle/grade:manageoutcomes', $context);

$navigation = grade_build_nav(__FILE__, get_string('outcomes', 'grades'), $courseid);

$upload_form = new import_outcomes_form();

// display import form
if (!$upload_form->get_data()) {
    print_grade_page_head($courseid, 'outcome', 'import', get_string('importoutcomes', 'grades'));
    $upload_form->display();
    echo $OUTPUT->footer();
    die;
}
print_grade_page_head($courseid, 'outcome', 'import', get_string('importoutcomes', 'grades'));

$imported_file = $CFG->tempdir . '/outcomeimport/importedfile_'.time().'.csv';
make_temp_directory('outcomeimport');

// copying imported file
if (!$upload_form->save_file('userfile', $imported_file, true)) {
    redirect('import.php'. ($courseid ? "?courseid=$courseid" : ''), get_string('importfilemissing', 'grades'));
}

/// which scope are we importing the outcomes in?
if (isset($courseid) && ($scope  == 'custom')) {
    // custom scale
    $local_scope = true;
} elseif (($scope == 'global') && has_capability('moodle/grade:manage', context_system::instance())) {
    // global scale
    $local_scope = false;
} else {
    // shouldn't happen .. user might be trying to access this script without the right permissions.
    redirect('index.php', get_string('importerror', 'grades'));
}

// open the file, start importing data
if ($handle = fopen($imported_file, 'r')) {
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
                echo $OUTPUT->box_start('generalbox importoutcomenofile buttons');
                echo get_string('importoutcomenofile', 'grades', $line);
                echo $OUTPUT->single_button(new moodle_url('/grade/edit/outcome/import.php', array('courseid'=> $courseid)), get_string('back'), 'get');
                echo $OUTPUT->box_end();
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
            echo $OUTPUT->box_start('generalbox importoutcomenofile');
            echo get_string('importoutcomenofile', 'grades', $line);
            echo $OUTPUT->single_button(new moodle_url('/grade/edit/outcome/import.php', array('courseid'=> $courseid)), get_string('back'), 'get');
            echo $OUTPUT->box_end();
            $fatal_error = true;
            //echo $OUTPUT->box(var_export($csv_data, true) ."<br />". var_export($header, true));
            break;
        }

        // sanity check #3: all required fields must be present on the current line.
        foreach ($headers as $header => $position) {
            if ($csv_data[$imported_headers[$header]] == '') {
                echo $OUTPUT->box_start('generalbox importoutcomenofile');
                echo get_string('importoutcomenofile', 'grades', $line);
                echo $OUTPUT->single_button(new moodle_url('/grade/edit/outcome/import.php', array('courseid'=> $courseid)), get_string('back'), 'get');
                echo $OUTPUT->box_end();
                $fatal_error = true;
                break;
            }
        }

        // MDL-17273 errors in csv are not preventing import from happening. We break from the while loop here
        if ($fatal_error) {
            break;
        }
        $params = array($csv_data[$imported_headers['outcome_shortname']]);
        $wheresql = 'shortname = ? ';

        if ($local_scope) {
            $params[] = $courseid;
            $wheresql .= ' AND courseid = ?';
        } else {
            $wheresql .= ' AND courseid IS NULL';
        }

        $outcome = $DB->get_records_select('grade_outcomes', $wheresql, $params);

        if ($outcome) {
            // already exists, print a message and skip.
            echo $OUTPUT->box(get_string('importskippedoutcome', 'grades', $csv_data[$imported_headers['outcome_shortname']]));
            continue;
        }

        // new outcome will be added, search for compatible existing scale...
        $params = array($csv_data[$imported_headers['scale_name']], $csv_data[$imported_headers['scale_items']], $courseid);
        $wheresql = 'name = ? AND scale = ? AND (courseid = ? OR courseid = 0)';
        $scale = $DB->get_records_select('scale', $wheresql, $params);

        if ($scale) {
            // already exists in the right scope: use it.
            $scale_id = key($scale);
        } else {
            if (!has_capability('moodle/course:managescales', $context)) {
                echo $OUTPUT->box(get_string('importskippednomanagescale', 'grades', $csv_data[$imported_headers['outcome_shortname']]));
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
        echo $OUTPUT->box(get_string('importoutcomesuccess', 'grades', $outcome_success_strings));
    }
} else {
    echo $OUTPUT->box(get_string('importoutcomenofile', 'grades', 0));
}

// finish
fclose($handle);
// delete temp file
unlink($imported_file);

echo $OUTPUT->footer();
