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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Required stuffs.
require_once('../../config.php');
require_once('cv_edit_form.php');

// Require login to use this.
require_login();

// We need this stuff.
global $PAGE, $OUTPUT, $USER;

// Get the context.
$context = context_system::instance();

// Set the context and other page stuff.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/wdsprefs/courseview.php'));
$PAGE->set_title(get_string('wdsprefs:course', 'block_wdsprefs'));
$PAGE->set_heading(get_string('wdsprefs:course', 'block_wdsprefs'));

// Add breadcrumbs.
$PAGE->navbar->add(
    get_string('home'),
    new moodle_url('/')
);
$PAGE->navbar->add(
    get_string('wdsprefs:course', 'block_wdsprefs'),
    new moodle_url('/blocks/wdsprefs/courseview.php')
);

// Set page layout.
$PAGE->set_pagelayout('base');

// Get the userid from the USER object.
$userid = $USER->id;

// Define the form.
$mform = new wdsprefs_cps_edit_form();

// If form is cancelled.
if ($mform->is_cancelled()) {

    // Redirect on cancel.
    redirect(
        new moodle_url('/'),
        get_string('wdsprefs:cancel', 'block_wdsprefs'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );

// If form is submitted and validated.
} elseif ($data = $mform->get_data()) {

    // Set the preferences as needed.
    set_user_preference('wdspref_createprior', $data->wdspref_createprior, $userid);
    set_user_preference('wdspref_enrollprior', $data->wdspref_enrollprior, $userid);
    set_user_preference('wdspref_courselimit', $data->wdspref_courselimit, $userid);
    set_user_preference('wdspref_format', $data->wdspref_format, $userid);

    // Redirect on submit.
    redirect(
        new moodle_url('/blocks/wdsprefs/courseview.php'),
        get_string('wdsprefs:success', 'block_wdsprefs'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Set form defaults from user preferences.
$mform->set_data_from_preferences($userid);

// Output the rest of the required Moodle stuff.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

