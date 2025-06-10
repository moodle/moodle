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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');

// Disables the time limit.
set_time_limit(0);

// Get the required Data Access Objects.
ues::require_daos();

// Ensure the user is logged in.
require_login();

// Ensure the user is the site administrator, if not, redirect to /my.
if (!is_siteadmin($USER->id)) {
    redirect('/my');
}

// Setup the options.
$errorids = optional_param_array('ids', null, PARAM_INT);
$reprocessall = optional_param('reprocess_all', null, PARAM_TEXT);
$deleteall = optional_param('delete_all', null, PARAM_TEXT);

// Build a simpler get_string.
$s = ues::gen_str();

// Set the base URL for the page.
$baseurl = new moodle_url('/admin/settings.php', array(
    'section' => 'enrolsettingsues'
));

// Set the block name.
$blockname = $s('pluginname');
$action = $s('reprocess_failures');

// Set up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_title($blockname. ': '. $action);
$PAGE->set_heading($blockname. ': '. $action);
$PAGE->set_url('/enrol/ues/cleanup.php');
$PAGE->set_pagetype('admin-settings-ues-semester-cleanup');
$PAGE->set_pagelayout('admin');

// Set up the navbar.
$PAGE->navbar->add($blockname, $baseurl);
$PAGE->navbar->add($action);

// Define the module array.
$module = array(
    'name' => 'ues',
    'fullpath' => '/enrol/ues/js/failure.js',
    'requires' => array('base', 'dom')
);

// Get the JS.
$PAGE->requires->js_init_call('M.ues.failures', null, false, $module);

// Build the page.
echo $OUTPUT->header();
echo $OUTPUT->heading($action);

// Do the work.
if ($reprocessall or $deleteall) {
    // Get all the errors.
    $posted = ues_error::get_all();
} else if ($errorids and is_array($errorids)) {
    // Get the errors the user wants to work with.
    $posted = ues_error::get_all(ues::where()->id->in($errorids));
} else {
    $posted = array();
}

// Make sure they selected something and actually want to do something.
if ($posted and $data = data_submitted()) {
    $reprocessing = ($reprocessall or isset($data->reprocess));

    if ($reprocessing) {
        // Reprocess the selected error ids.
        $handler = function($out) use ($posted) {
            $msg = ues::_s('reprocess_success');

            echo $out->notification($msg, 'notifysuccess');
            echo html_writer::start_tag('pre');
            ues::reprocess_errors($posted);
            echo html_writer::end_tag('pre');
        };
    } else {
        // Delete the selected error ids.
        $handler = function($out) use ($posted) {
            foreach ($posted as $error) {
                ues_error::delete($error->id);
            }

            $msg = ues::_s('delete_success');
            echo $out->notification($msg, 'notifysuccess');
        };
    }

    $url = new moodle_url('/enrol/ues/failures.php');

    output_box_and_die($url, $handler);
}

// Get whatever errors are left.
$errors = ues_error::get_all();

if (empty($errors)) {
    output_box_and_die($baseurl, function($out) use ($s) {
        echo $out->notification($s('no_errors'), 'notifysuccess');
    });
}

// Set up the table.
$table = new html_table();

// Build the table headings.
$table->head = array(
    get_string('name'), $s('error_params'), $s('error_when'),
    html_writer::checkbox('select_all', 1, false, get_string('select'))
);

// Set up the table data.
$table->data = array();

// Loop through the errors for use in the table.
foreach ($errors as $error) {
    $params = unserialize($error->params);

    // Populate the line.
    $line = array(
        $error->name,
        // TODO: Figure out a new way to deal with this.
        html_writer::tag('pre', print_r($params, true)),
        date('Y-m-d h:i:s a', $error->timestamp),
        html_writer::checkbox('ids[]', $error->id, false, '',  array('class' => 'ids'))
    );

    // Add the line to the data table.
    $table->data[] = new html_table_row($line);
}

// Count the errors.
echo $OUTPUT->heading($s('reprocess_count', count($errors)));

// Build the form.
echo html_writer::start_tag('form', array('method' => 'POST'));
echo html_writer::table($table);

// Reprocess all.
echo html_writer::empty_tag('input', array(
    'name' => 'reprocess_all',
    'type' => 'submit',
    'value' => $s('reprocess_all')
));

// Reprocess selected.
echo html_writer::empty_tag('input', array(
    'name' => 'reprocess',
    'type' => 'submit',
    'disabled' => 'disabled',
    'value' => $s('reprocess_selected')
));

// Delete all.
echo html_writer::empty_tag('input', array(
    'name' => 'delete_all',
    'type' => 'submit',
    'value' => $s('delete_all')
));

// Delete selected.
echo html_writer::empty_tag('input', array(
    'name' => 'delete',
    'type' => 'submit',
    'disabled' => 'disabled',
    'value' => $s('delete_selected')
));

// Finish the form.
echo html_writer::end_tag('form');

// Output the footer.
echo $OUTPUT->footer();

function output_box_and_die($baseurl, $middle) {
    global $OUTPUT;

    echo $OUTPUT->box_start();
    $middle($OUTPUT);
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button($baseurl);
    echo $OUTPUT->footer();
    die();
}
