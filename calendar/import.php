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
 * Moodle calendar import
 *
 * @package    core_calendar
 * @copyright  Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->libdir . '/bennu/bennu.inc.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/calendar/lib.php');

$courseid = optional_param('course', SITEID, PARAM_INT);
$groupcourseid  = optional_param('groupcourseid', 0, PARAM_INT);
$category  = optional_param('category', 0, PARAM_INT);
$data = [];
$pageurl = new moodle_url('/calendar/import.php');
$managesubscriptionsurl = new moodle_url('/calendar/managesubscriptions.php');

$headingstr = $calendarstr = get_string('calendar', 'calendar');

if (!empty($courseid) && $courseid != SITEID) {
    $course = get_course($courseid);
    $data['eventtype'] = 'course';
    $data['courseid'] = $course->id;
    $pageurl->param('course', $course->id);
    $managesubscriptionsurl->param('course', $course->id);
    $headingstr .= ": {$course->shortname}";
    navigation_node::override_active_url(new moodle_url('/course/view.php', ['id' => $course->id]));
    $PAGE->navbar->add(
        $calendarstr,
        new moodle_url('/calendar/view.php', ['view' => 'month', 'course' => $course->id])
    );
} else if (!empty($category)) {
    $course = get_site();
    $pageurl->param('category', $category);
    $managesubscriptionsurl->param('category', $category);
    $data['category'] = $category;
    $data['eventtype'] = 'category';
    navigation_node::override_active_url(new moodle_url('/course/index.php', ['categoryid' => $category]));
    $PAGE->set_category_by_id($category);
    $PAGE->navbar->add(
        $calendarstr,
        new moodle_url('/calendar/view.php', ['view' => 'month', 'category' => $category])
    );
} else {
    $course = get_site();
    $PAGE->navbar->add($calendarstr, new moodle_url('/calendar/view.php', ['view' => 'month']));
}

if (!empty($groupcourseid)) {
    $pageurl->param('groupcourseid', $groupcourseid);
}

require_login($course, false);
if (!calendar_user_can_add_event($course)) {
    throw new \moodle_exception('errorcannotimport', 'calendar');
}

$heading = get_string('importcalendar', 'calendar');
$pagetitle = $course->shortname . ': ' . $calendarstr . ': ' . $heading;

$PAGE->set_secondary_navigation(false);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($headingstr);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('managesubscriptions', 'calendar'), $managesubscriptionsurl);
$PAGE->navbar->add($heading, $pageurl);

// Populate the 'group' select box based on the given 'groupcourseid', if necessary.
$groups = [];
if (!empty($groupcourseid)) {
    require_once($CFG->libdir . '/grouplib.php');
    $groupcoursedata = groups_get_course_data($groupcourseid);
    if (!empty($groupcoursedata->groups)) {
        foreach ($groupcoursedata->groups as $groupid => $groupdata) {
            $groups[$groupid] = $groupdata->name;
        }
    }
    $data['groupcourseid'] = $groupcourseid;
    $data['eventtype'] = 'group';
}
if (!empty($category)) {
    $managesubscriptionsurl->param('category', $category);
    $data['category'] = $category;
    $data['eventtype'] = 'category';
}

$renderer = $PAGE->get_renderer('core_calendar');

$customdata = [
    'courseid' => $course->id,
    'groups' => $groups,
];

$form = new \core_calendar\local\event\forms\managesubscriptions(null, $customdata);
$form->set_data($data);

$formdata = $form->get_data();
if (!empty($formdata)) {
    require_sesskey();
    $subscriptionid = calendar_add_subscription($formdata);
    if ($formdata->importfrom == CALENDAR_IMPORT_FROM_FILE) {
        // Blank the URL if it's a file import.
        $formdata->url = '';
        $calendar = $form->get_file_content('importfile');
        $ical = new iCalendar();
        $ical->unserialize($calendar);
        $importresults = calendar_import_events_from_ical($ical, $subscriptionid);
    } else {
        try {
            $importresults = calendar_update_subscription_events($subscriptionid);
        } catch (\moodle_exception $e) {
            // Delete newly added subscription and show invalid url error.
            calendar_delete_subscription($subscriptionid);
            throw new \moodle_exception($e->errorcode, $e->module, $PAGE->url);
        }
    }
    if (!empty($formdata->courseid)) {
        $managesubscriptionsurl->param('course', $formdata->courseid);
    }
    if (!empty($formdata->categoryid)) {
        $managesubscriptionsurl->param('category', $formdata->categoryid);
    }
    redirect($managesubscriptionsurl, $renderer->render_import_result($importresults));
}

echo $OUTPUT->header();
echo $renderer->start_layout();
echo $OUTPUT->heading($heading);
$form->display();
echo $renderer->complete_layout();
echo $OUTPUT->footer();
