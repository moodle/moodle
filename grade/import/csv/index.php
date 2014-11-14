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

require_once("../../../config.php");
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot. '/grade/import/grade_import_form.php');
require_once($CFG->dirroot.'/grade/import/lib.php');
require_once($CFG->libdir . '/csvlib.class.php');

$id            = required_param('id', PARAM_INT); // Course id.
$separator     = optional_param('separator', '', PARAM_ALPHA);
$verbosescales = optional_param('verbosescales', 1, PARAM_BOOL);
$iid           = optional_param('iid', null, PARAM_INT);
$importcode    = optional_param('importcode', '', PARAM_FILE);
$forceimport   = optional_param('forceimport', false, PARAM_BOOL);

$url = new moodle_url('/grade/import/csv/index.php', array('id'=>$id));
if ($separator !== '') {
    $url->param('separator', $separator);
}
if ($verbosescales !== 1) {
    $url->param('verbosescales', $verbosescales);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($id);
require_capability('moodle/grade:import', $context);
require_capability('gradeimport/csv:view', $context);

$separatemode = (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS and
        !has_capability('moodle/site:accessallgroups', $context));
$currentgroup = groups_get_course_group($course);

print_grade_page_head($course->id, 'import', 'csv', get_string('importcsv', 'grades'));

$renderer = $PAGE->get_renderer('gradeimport_csv');

// Get the grade items to be matched with the import mapping columns.
$gradeitems = gradeimport_csv_load_data::fetch_grade_items($course->id);

// If the csv file hasn't been imported yet then look for a form submission or
// show the initial submission form.
if (!$iid) {

    // Set up the import form.
    $mform = new grade_import_form(null, array('includeseparator' => true, 'verbosescales' => $verbosescales, 'acceptedtypes' =>
            array('.csv', '.txt')));

    // If the import form has been submitted.
    if ($formdata = $mform->get_data()) {
        $text = $mform->get_file_content('userfile');
        $csvimport = new gradeimport_csv_load_data();
        $csvimport->load_csv_content($text, $formdata->encoding, $separator, $formdata->previewrows);
        $csvimporterror = $csvimport->get_error();
        if (!empty($csvimporterror)) {
            echo $renderer->errors(array($csvimport->get_error()));
            echo $OUTPUT->footer();
            die();
        }
        $iid = $csvimport->get_iid();
        echo $renderer->import_preview_page($csvimport->get_headers(), $csvimport->get_previewdata());
    } else {
        // Display the standard upload file form.
        echo $renderer->standard_upload_file_form($course, $mform);
        echo $OUTPUT->footer();
        die();
    }
}

// Data has already been submitted so we can use the $iid to retrieve it.
$csvimport = new csv_import_reader($iid, 'grade');
$header = $csvimport->get_columns();
// Get a new import code for updating to the grade book.
if (empty($importcode)) {
    $importcode = get_new_importcode();
}

$mappingformdata = array(
    'gradeitems' => $gradeitems,
    'header' => $header,
    'iid' => $iid,
    'id' => $id,
    'importcode' => $importcode,
    'forceimport' => $forceimport,
    'verbosescales' => $verbosescales
);
// we create a form to handle mapping data from the file to the database.
$mform2 = new grade_import_mapping_form(null, $mappingformdata);

// Here, if we have data, we process the fields and enter the information into the database.
if ($formdata = $mform2->get_data()) {
    $gradeimport = new gradeimport_csv_load_data();
    $status = $gradeimport->prepare_import_grade_data($header, $formdata, $csvimport, $course->id, $separatemode,
            $currentgroup, $verbosescales);

    // At this stage if things are all ok, we commit the changes from temp table.
    if ($status) {
        grade_import_commit($course->id, $importcode);
    } else {
        $errors = $gradeimport->get_gradebookerrors();
        $errors[] = get_string('importfailed', 'grades');
        echo $renderer->errors($errors);
    }
    echo $OUTPUT->footer();
} else {
    // If data hasn't been submitted then display the data mapping form.
    $mform2->display();
    echo $OUTPUT->footer();
}
