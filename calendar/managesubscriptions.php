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
 * Allows the user to manage calendar subscriptions.
 *
 * @copyright 2012 Jonathan Harker
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

require_once('../config.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/calendar/managesubscriptions_form.php');

// Required use.
$courseid = optional_param('course', SITEID, PARAM_INT);
// Used for processing subscription actions.
$subscriptionid = optional_param('id', 0, PARAM_INT);
$pollinterval  = optional_param('pollinterval', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_INT);

$url = new moodle_url('/calendar/managesubscriptions.php');
if ($courseid != SITEID) {
    $url->param('course', $courseid);
}
navigation_node::override_active_url(new moodle_url('/calendar/view.php', array('view' => 'month')));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('managesubscriptions', 'calendar'));

if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid));
    $courses = array($course->id => $course);
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
}
require_course_login($course);
if (!calendar_user_can_add_event($course)) {
    print_error('errorcannotimport', 'calendar');
}

$form = new calendar_addsubscription_form(null);
$form->set_data(array(
    'course' => $course->id
));

$importresults = '';

$formdata = $form->get_data();
if (!empty($formdata)) {
    require_sesskey(); // Must have sesskey for all actions.
    $subscriptionid = calendar_add_subscription($formdata);
    if ($formdata->importfrom == CALENDAR_IMPORT_FROM_FILE) {
        // Blank the URL if it's a file import.
        $formdata->url = '';
        $calendar = $form->get_file_content('importfile');
        $ical = new iCalendar();
        $ical->unserialize($calendar);
        $importresults = calendar_import_icalendar_events($ical, $courseid, $subscriptionid);
    } else {
        try {
            $importresults = calendar_update_subscription_events($subscriptionid);
        } catch (moodle_exception $e) {
            // Delete newly added subscription and show invalid url error.
            calendar_delete_subscription($subscriptionid);
            print_error($e->errorcode, $e->module, $PAGE->url);
        }
    }
    // Redirect to prevent refresh issues.
    redirect($PAGE->url, $importresults);
} else if (!empty($subscriptionid)) {
    // The user is wanting to perform an action upon an existing subscription.
    require_sesskey(); // Must have sesskey for all actions.
    if (calendar_can_edit_subscription($subscriptionid)) {
        try {
            $importresults = calendar_process_subscription_row($subscriptionid, $pollinterval, $action);
        } catch (moodle_exception $e) {
            // If exception caught, then user should be redirected to page where he/she came from.
            print_error($e->errorcode, $e->module, $PAGE->url);
        }
    } else {
        print_error('nopermissions', 'error', $PAGE->url, get_string('managesubscriptions', 'calendar'));
    }
}

$sql = 'SELECT *
          FROM {event_subscriptions}
         WHERE courseid = :courseid
            OR (courseid = 0 AND userid = :userid)';
$params = array('courseid' => $courseid, 'userid' => $USER->id);
$subscriptions = $DB->get_records_sql($sql, $params);

// Print title and header.
$PAGE->set_title("$course->shortname: ".get_string('calendar', 'calendar').": ".get_string('subscriptions', 'calendar'));
$PAGE->set_heading($course->fullname);

$renderer = $PAGE->get_renderer('core_calendar');

echo $OUTPUT->header();

// Filter subscriptions which user can't edit.
foreach($subscriptions as $subscription) {
    if (!calendar_can_edit_subscription($subscription)) {
        unset($subscriptions[$subscription->id]);
    }
}

// Display a table of subscriptions.
echo $renderer->subscription_details($courseid, $subscriptions, $importresults);
// Display the add subscription form.
$form->display();
echo $OUTPUT->footer();
