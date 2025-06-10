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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Inlcude the Moodle config.
require_once('../../config.php');

// Grab these for later.
global $CFG, $USER;

// Inlcude the requisite helpers functionality.
require_once($CFG->dirroot . '/blocks/pu/validate_form.php');
require_once($CFG->dirroot . '/blocks/pu/classes/helpers.php');

// Make sure the user is logged in.
require_login();

// Set the context.
$context = \context_system::instance();

// Set the redirect url.
$returnurl = new moodle_url('/');
$pageurl = $CFG->wwwroot . '/blocks/pu/validate.php';

// Set up the page.
$PAGE->set_url('/blocks/pu/validate.php');
$PAGE->set_context($context);
$PAGE->set_pagetype('block-pu');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'block_pu') . ': ' . get_string('manage_invalids', 'block_pu'));
$PAGE->navbar->add(get_string('manage_invalids', 'block_pu'));
$PAGE->set_heading(get_string('pluginname', 'block_pu') . ': ' . get_string('manage_invalids', 'block_pu'));
$PAGE->requires->css(new moodle_url('/blocks/pu/styles.css'));

// Check to see if ths user in question can modify pu override settings.
if (!has_capability('block/pu:admin', $context)) {
    redirect($returnurl, get_string('nopermissions', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);
}

// Get the list of invalids.
$invalids = block_pu_helpers::get_invalids($perms = 'noperms');

// Build the form.
$form = new pu_validates_form(null, $invalids, $PAGE);

// Set default data (if any).
$form->set_data($invalids);

// Form processing and displaying.
if ($form->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present on form.
    redirect($returnurl, get_string('nothingtodo', 'block_pu'), null, \core\output\notification::NOTIFY_WARNING);

} else if ($fromform = $form->get_data()) {

    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $orcomplete = block_pu_helpers::pu_writevalidates($fromform, $userid = $USER->id);

    if ($orcomplete) {
        redirect($pageurl, get_string('validate_complete', 'block_pu'), 10, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $form->display();
    }
} else {
    // Output the page header.
    echo $OUTPUT->header();

    // Display the form.
    $form->display();
}

echo $OUTPUT->footer();
