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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/link_logins/locallib.php');
global $CFG, $DB;

// Make sure we can use this.
if (!link::can_use()) {

    // Redirect to home.
    redirect($CFG->wwwroot);
}

// Grab the urlparms for future use.
$page_params = [
    'existingusername' => required_param('existingusername', PARAM_TEXT),
    'prospectiveemail' => required_param('prospectiveemail', PARAM_TEXT)
];

// Set this for future use.
$confirm = optional_param('confirm', 0, PARAM_INT);

// Require that the user is logged in and we know who they are.
require_login();

// Get the user who will serve as the existing Moodle user.
$existinguser = link::get_user_from_username($page_params['existingusername']);

// Check and see if the prospective user exists as this would be bad.
$prospectiveusers = link::get_user_from_email($page_params['prospectiveemail']);

// We need this for later.
$prospectiveusername = link::generate_username_from_email($page_params['prospectiveemail']);

// Check for any existing duplicate links.
$dupe = link::check_dupes($prospectiveusername, $existinguser);

// Move the name around for future use.
$existinguserdupe = $dupe->existingusername;

// Make sure we have all the creator data before jumping to conclusions.
if (isset($existinguserdupe->id) && isset($dupe->creator->firstname) && isset($dupe->creator->lastname)) {
    $existinguserdupe->creatorfirstname = $dupe->creator->firstname;
    $existinguserdupe->creatorlastname = $dupe->creator->lastname;
} else if (isset($existinguserdupe->id)) {
    $existinguserdupe->creatorfirstname = "OAuth 2";
    $existinguserdupe->creatorlastname = "Services";
}

// Set the system context.
$system_context = context_system::instance();

// Set the page parms.
$PAGE->set_pagelayout('standard');
$PAGE->set_context($system_context);
$PAGE->set_url(new moodle_url('/blocks/link_logins/link.php', $page_params));
$PAGE->navbar->add(get_string('link_logins', 'block_link_logins'), null);

// Set up success and failure strings.
$success = html_writer::div(get_string('success', 'block_link_logins'), "alert alert-success alert-block fade in ");
$failure = html_writer::div(get_string('securityviolation', 'block_link_logins'), "alert alert-warning alert-block fade in ");
$dupemistake = html_writer::div(get_string('dupemistake', 'block_link_logins', $existinguserdupe), "alert alert-warning alert-block fade in ");
$mistake = html_writer::div(get_string('mistake','block_link_logins'), "alert alert-warning alert-block fade in ");
$multimistake = html_writer::div(get_string('multimistake','block_link_logins'), "alert alert-warning alert-block fade in ");
$pumistake = html_writer::div(get_string('existingprospective', 'block_link_logins'), "alert alert-warning alert-block fade in ");
$mumistake = html_writer::div(get_string('missingusername', 'block_link_logins'), "alert alert-info alert-block fade in ");
$mistakebutton = html_writer::link(new moodle_url($CFG->wwwroot), get_string('continue'), array('class' => 'btn btn-success'));

// output the header.
echo $OUTPUT->header();

// Check to see if we have existing linked logins matching the input data.
if (isset($existinguserdupe->id)) {
    echo $dupemistake;
    echo $mistakebutton;
    exit;
}

// If we have more than one prospective users.
if (count($prospectiveusers) > 1) {
    echo $multimistake;
    echo $mistakebutton;
    exit;

// Otherwise if we only have 1 record.
} else if (count($prospectiveusers) == 1) {
    $prospectiveuser = reset($prospectiveusers);

// We have no records.
} else {
    $prospectiveuser = new stdClass();
}

// If we have a prospective user without an existing user.
if (isset($prospectiveuser->id) && !isset($existinguser->id)) {
    echo $mistake;
    echo $mistakebutton;
    exit;
}

// If we have a prospective user.
if (isset($prospectiveuser->id)) {
    echo $pumistake;
    echo $mistakebutton;
    exit;
}

// Build the confirmation text.
$confirmation = html_writer::div(get_string('confirm1', 'block_link_logins') . $prospectiveusername . get_string('confirm2', 'block_link_logins') . $existinguser->firstname . ' ' . $existinguser->lastname . ' (' . $existinguser->username . ')?', "alert alert-info alert-block fade in ");

// Build the continue button.
$continuebutton = html_writer::link(new moodle_url('/'), get_string('continue'), array('class' => 'btn btn-success'));

// Again, check if the user can use the tool.
if (link::can_use()) {

    // Check to see if they've confirmed that they REALLY want to go ahead with it.
    if (!$confirm) {

        // Print the confirmation text.
        echo $confirmation;

        // Redirect them home.
        $optionsno = new moodle_url('/');

        // Reload the page with the confirmation.
        $optionsyes = new moodle_url('/blocks/link_logins/link.php', array('existingusername' => $existinguser->username, 'prospectiveemail' => $page_params['prospectiveemail'], 'confirm' => 1, 'sesskey' => sesskey()));

        // Build the mini-form.
        echo $OUTPUT->confirm(get_string('continue', 'block_link_logins', 'link.php'), $optionsyes, $optionsno);
    } else {

        // Now that they've confirmed, make sure they have the correct session key.
        if (confirm_sesskey()) {
            try {
                // Get the linked login linkid.
                $linkid = link::handle_creating_link($page_params['prospectiveemail'], $prospectiveusername, $existinguser->id);

                // Get the data from the id above.
                $createdlink = link::get_link($linkid);

                // Build the text.
                $successfullink = html_writer::div(get_string('successfullink', 'block_link_logins', $createdlink), "alert alert-success alert-block fade in ");

                // Let the user know everything worked.
                echo $successfullink;

            // Something went wrong.
            } catch (Exception $e) {
                echo html_writer::div(get_string('exception', 'block_link_logins') . ' ' . $e->getMessage() . ' in link::handle_creating_link.', "alert alert-error alert-block fade in ");;
            }

            // Display a continue button.
            echo $continuebutton;

        // You are not allowed to see this.
        } else {
            echo $failure;
            echo $continuebutton;
        }
    }

// You are not allowed to use this.
} else {
    echo $failure;
    echo $continuebutton;
}

// Output the footer.
echo $OUTPUT->footer();
