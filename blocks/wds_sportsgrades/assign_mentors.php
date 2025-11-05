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
 * Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/blocks/wds_sportsgrades/classes/forms/assign_mentor_form.php');

// Set the context.
$context = context_system::instance();

// Page setup.
$PAGE->set_url(new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('assignmentors', 'block_wds_sportsgrades'));
$PAGE->set_heading(get_string('assignmentors', 'block_wds_sportsgrades'));
$PAGE->set_pagelayout('standard');

require_login();
require_capability('block/wds_sportsgrades:assignmentors', $context);

// Get filter params.
$firstname = optional_param('firstname', '', PARAM_ALPHA);
$lastname = optional_param('lastname', '', PARAM_ALPHA);

// The action url for the form.
$formurl = new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php', ['firstname' => $firstname, 'lastname' => $lastname]);

// Instantiate the form.
$mform = new assign_mentor_form($formurl, ['firstname' => $firstname, 'lastname' => $lastname]);

// Check if we're adding and the form has been submitted.
if ($mform->is_submitted() && !$mform->is_cancelled() && $mform->is_validated()) {

    // Get the form data.
    $formdata = $mform->get_data();

    // Process the form data.
    if (!empty($formdata->studentadd)) {

        // Loop through the students.
        foreach ($formdata->studentadd as $userid) {

            // Build the new record.
            $record = new stdClass();
            $record->mentorid = $formdata->mentorid;
            $record->userid = $userid;
            $record->timecreated = time();
            $record->timemodified = time();
            $record->createdby = $USER->id;
            $record->modifiedby = $USER->id;

            // Check to see if the record exists prior to adding it.
            if (!$DB->record_exists('block_wds_sportsgrades_mentor', ['mentorid' => $formdata->mentorid, 'userid' => $userid])) {

                // Add the record.
                $DB->insert_record('block_wds_sportsgrades_mentor', $record);
            }
        }

        // We've added the records, redirect.
        redirect(new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php'));
    }
}

// Check to see if we're deleting records.
if (optional_param('userremove', 0, PARAM_INT) && confirm_sesskey()) {

    // Get the url parm.
    $userremoveid = required_param('userremove', PARAM_INT);

    // Delete the record.
    $DB->delete_records('block_wds_sportsgrades_mentor', ['id' => $userremoveid]);

    // Verify deletion was successful.
    if (!$DB->record_exists('block_wds_sportsgrades_mentor', ['id' => $userremoveid])) {

        // Redirect back to the form.
        redirect(new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php'));
    }
}

// Output the header.
echo $OUTPUT->header();

// Display the form.
$mform->display();

// Output the footer.
echo $OUTPUT->footer();
