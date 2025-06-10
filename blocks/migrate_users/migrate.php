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
 * @package    block_migrate_users
 * @copyright  2019 onwards Louisiana State University
 * @copyright  2019 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/blocks/migrate_users/locallib.php');

global $CFG, $DB;

// Grab the urlparms for future use.
$page_params = [
    'userfrom' => required_param('userfrom', PARAM_TEXT),
    'userto' => required_param('userto', PARAM_TEXT),
    'courseid' => required_param('courseid', PARAM_INT)
];

$confirm = optional_param('confirm', 0, PARAM_INT);

// Require that the user is logged in and we know who they are.
require_login();

// Get the course from the course id.
$course = $DB->get_record('course', array('id' => $page_params['courseid']));

// Get the user who will serve as the data source.
$userfrom = migrate::get_user($page_params['userfrom']);

// Get the user who will serve as the data recipient.
$userto = migrate::get_user($page_params['userto']);

// Set up the page and nav links.
if ($page_params['courseid']) {
    $course_context = context_course::instance($course->id);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_context($course_context);
    $PAGE->set_url(new moodle_url('/blocks/migrate_users/migrate.php', $page_params));
    $PAGE->navbar->add($course->fullname, new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $course->id)));
    $PAGE->navbar->add(get_string('migrate_users', 'block_migrate_users'), null);
}

// Set up the handler array.
$handlers = migrate::get_handlers();

// Loop through the handler names and build out the strings.
foreach ($handlers as $handler) {
    $$handler = html_writer::div(
        get_string("prefix", 'block_migrate_users') .
        get_string("$handler", 'block_migrate_users') .
        get_string("found", 'block_migrate_users'), 'alert alert-info alert-block fade in ');
}

// Set up success and failure strings.
$success = html_writer::div(get_string('success', 'block_migrate_users'), "alert alert-success alert-block fade in ");

$failure = html_writer::div(get_string('securityviolation', 'block_migrate_users'), "alert alert-error alert-block fade in ");

$mistake = html_writer::div(get_string('missingboth', 'block_migrate_users'), "alert alert-info alert-block fade in ");

$tomistake = html_writer::div(get_string('missingto', 'block_migrate_users'), "alert alert-info alert-block fade in ");

$frommistake = html_writer::div(get_string('missingfrom', 'block_migrate_users'), "alert alert-info alert-block fade in ");

if(empty($userto) || empty($userfrom)) {
    echo $OUTPUT->header();

    if(empty($userto) && empty($userfrom)) {
        echo $mistake;

    } else {
    echo(empty($userto) ? $tomistake : $frommistake);

    }
    $mistakebutton = html_writer::link(
        new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $course->id), 'coursetools'),
        get_string('continue'), array('class' => 'btn btn-success'));

    echo $mistakebutton;
    echo $OUTPUT->footer();
    exit;
}

$confirmation = html_writer::div(get_string('alldata', 'block_migrate_users') . $userfrom->firstname . ' ' . $userfrom->lastname . ' (' . $userfrom->username . ') '
    . get_string('moveto', 'block_migrate_users') . $userto->firstname . ' ' . $userto->lastname . ' (' .$userto->username . ') '
    . get_string('deleted', 'block_migrate_users'), "alert alert-error alert-block fade in ");

// Build the continue button.
$continuebutton = html_writer::link(
    new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $course->id)),
    get_string('continue'), array('class' => 'btn btn-success'));

// Begin page output.
echo $OUTPUT->header();

// Again, check if the user can use the tool.
if (migrate::can_use()) {

    // Check to see if they've confirmed that they REALLY want to go ahead with it.
    if (!$confirm) {
        echo $confirmation;

        // Redirect them to their course.
        $optionsno = new moodle_url('/course/view.php', array('id' => $course->id));

        // Reload the page with the confirmation.
        $optionsyes = new moodle_url(
            '/blocks/migrate_users/migrate.php',
            array('userfrom' => $userfrom->username,
                'userto' => $userto->username,
                'courseid' => $course->id,
                'confirm' => 1,
                'sesskey' => sesskey()
            )
        );

        // Build the mini-form.
        echo $OUTPUT->confirm(
            get_string('continue', 'block_migrate_users', 'migrate.php'),
            $optionsyes,
            $optionsno
        );

    } else {

        // Now that they've confirmed, make sure they have the correct session key.
        if (confirm_sesskey()) {

            // Loop through the handlers again.
            foreach ($handlers as $handler) {
                // Best effort, baby.
                try {
                    // Call the appropriate handler method.
                    migrate::$handler($page_params['userfrom'],
                        $page_params['userto'],
                        $page_params['courseid']);

                    // Output the appropriate message string.
                    echo $$handler;

                // Something went wrong, report it.
                } catch (Exception $e) {
                    echo html_writer::div(
                        get_string('exception', 'block_migrate_users') .
                            ' ' .
                            $e->getMessage() .
                            ' in migrate::' . $handler . '.',
                        "alert alert-error alert-block fade in "
                    );
                }
            }

            // Let the user know we did something.
            echo $success;
            echo $continuebutton;
        } else {
            // Let the user know we did nothing.
            echo $failure;
            echo $continuebutton;
        }
    }
} else {
    echo $failure;
    echo $continuebutton;

}

echo $OUTPUT->footer();

