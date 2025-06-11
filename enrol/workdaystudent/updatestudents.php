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
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Basic Moodle requirement.
require('../../config.php');

// We need adminlib and the WDS class lib.
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/enrol/workdaystudent/classes/workdaystudent.php');

// Define the page name.
admin_externalpage_setup('update_students');

// Set the context.
$context = context_system::instance();

// Make sure only admins can get here.
require_capability('moodle/site:config', $context);

// Set the url.
$url = new moodle_url('/enrol/workdaystudent/updatestudents.php');

// Set up the page.
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'enrol_workdaystudent'));
$PAGE->set_heading(get_string('wds:updatestudents', 'enrol_workdaystudent'));

// Make sure we only update stuff when we are sent the form data from submission.
if (optional_param('runupdate_students', 0, PARAM_BOOL)) {
    require_sesskey();

    // There is no do, only try.
    try {

        // Do the student nasty.
        $updated = workdaystudent::mass_mstudent_updates();

        // If it worked, redirect us with some happy platitudes.
        if ($updated) {
            redirect($url, get_string('wds:massupdate_ssuccess', 'enrol_workdaystudent'), null,
                core\output\notification::NOTIFY_SUCCESS);

        // It failed but without a DB error, redirect anyway, but with a failure notice.
        } else {
            redirect($url, get_string('wds:massupdate_sfail', 'enrol_workdaystudent'), null,
                core\output\notification::NOTIFY_ERROR);
        }
    // DB error, make sure we redirect and let the user know why. Probably a dupe. 
    } catch (dml_exception $e) {
        redirect($url, get_string('wds:massupdate_dberror', 'enrol_workdaystudent', $e->getMessage()), null,
            core\output\notification::NOTIFY_ERROR);
    }
}

// Make sure we only update stuff when we are sent the form data from submission.
if (optional_param('runupdate_teachers', 0, PARAM_BOOL)) {
    require_sesskey();

    // There is no do, only try.
    try {

        // Do the teacher nasty.
        $updated = workdaystudent::mass_mteacher_updates();

        // If it worked, redirect us with some happy platitudes.
        if ($updated) {
            redirect($url, get_string('wds:massupdate_fsuccess', 'enrol_workdaystudent'), null,
                core\output\notification::NOTIFY_SUCCESS);

        // It failed but without a DB error, redirect anyway, but with a failure notice.
        } else {
            redirect($url, get_string('wds:massupdate_ffail', 'enrol_workdaystudent'), null,
                core\output\notification::NOTIFY_ERROR);
        }

    // DB error, make sure we redirect and let the user know why. Probably a dupe.
    } catch (dml_exception $e) {
        redirect($url, get_string('wds:massupdate_dberror', 'enrol_workdaystudent', $e->getMessage()), null,
            core\output\notification::NOTIFY_ERROR);
    }
}

// Output the page header.
echo $OUTPUT->header();

// Output the heading.
echo $OUTPUT->heading(get_string('wds:updatestudents', 'enrol_workdaystudent'));

// Build out the URL with parms for the student button.
$runurlstudents = new moodle_url($url, ['runupdate_students' => 1, 'sesskey' => sesskey()]);

// output the Student button.
echo $OUTPUT->single_button($runurlstudents, get_string('wds:runstudentupdate', 'enrol_workdaystudent'));

// Give us an explanation of what we're doing and some space.
echo html_writer::tag('p', get_string('wds:updatestudents_desc', 'enrol_workdaystudent'));
echo html_writer::empty_tag('br');

// Output the heading.
echo $OUTPUT->heading(get_string('wds:updateteachers', 'enrol_workdaystudent'));

// Build out the URL with parms for the student button.
$runurlteachers = new moodle_url($url, ['runupdate_teachers' => 1, 'sesskey' => sesskey()]);

// output the Teacher button.
echo $OUTPUT->single_button($runurlteachers, get_string('wds:runteacherupdate', 'enrol_workdaystudent'));

// Give us an explanation of what we're doing.
echo html_writer::tag('p', get_string('wds:updateteachers_desc', 'enrol_workdaystudent'));

// Close out the page.
echo $OUTPUT->footer();
