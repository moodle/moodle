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

// Required use.
$courseid = optional_param('course', null, PARAM_INT);
$categoryid = optional_param('category', null, PARAM_INT);

$url = new moodle_url('/calendar/managesubscriptions.php');
if ($courseid != SITEID && !empty($courseid)) {
    $url->param('course', $courseid);
    navigation_node::override_active_url(new moodle_url('/course/view.php', ['id' => $courseid]));
    $PAGE->navbar->add(
        get_string('calendar', 'calendar'),
        new moodle_url('/calendar/view.php', ['view' => 'month', 'course' => $courseid])
    );
} else if ($categoryid) {
    $url->param('categoryid', $categoryid);
    navigation_node::override_active_url(new moodle_url('/course/index.php', ['categoryid' => $categoryid]));
    $PAGE->set_category_by_id($categoryid);
    $PAGE->navbar->add(
        get_string('calendar', 'calendar'),
        new moodle_url('/calendar/view.php', ['view' => 'month', 'category' => $categoryid])
    );
} else {
    $PAGE->navbar->add(get_string('calendar', 'calendar'), new moodle_url('/calendar/view.php', ['view' => 'month']));
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_secondary_navigation(false);

if ($courseid != SITEID && !empty($courseid)) {
    // Course ID must be valid and existing.
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $courses = array($course->id => $course);
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
}
require_login($course, false);

if (!calendar_user_can_add_event($course)) {
    print_error('errorcannotimport', 'calendar');
}
$PAGE->navbar->add(get_string('managesubscriptions', 'calendar'), $PAGE->url);

$types = calendar_get_allowed_event_types($courseid);

$searches = [];
$params = [];

$usedefaultfilters = true;

if (!empty($types['site'])) {
    $searches[] = "(eventtype = 'site')";
    $usedefaultfilters = false;
}

if (!empty($types['user'])) {
    $searches[] = "(eventtype = 'user' AND userid = :userid)";
    $params['userid'] = $USER->id;
    $usedefaultfilters = false;
}

if (!empty($courseid) && !empty($types['course'])) {
    $searches[] = "((eventtype = 'course' OR eventtype = 'group') AND courseid = :courseid)";
    $params += ['courseid' => $courseid];
    $usedefaultfilters = false;
}

if (!empty($types['category'])) {
    if (!empty($categoryid)) {
        $searches[] = "(eventtype = 'category' AND categoryid = :categoryid)";
        $params += ['categoryid' => $categoryid];
    } else {
        $searches[] = "(eventtype = 'category')";
    }

    $usedefaultfilters = false;
}

if ($usedefaultfilters) {
    $searches[] = "(eventtype = 'user' AND userid = :userid)";
    $params['userid'] = $USER->id;

    if (!empty($types['site'])) {
        $searches[] = "(eventtype = 'site' AND courseid  = :siteid)";
        $params += ['siteid' => SITEID];
    }

    if (!empty($types['course'])) {
        $courses = calendar_get_default_courses(null, 'id', true);
        if (!empty($courses)) {
            $courseids = array_map(function ($c) {
                return $c->id;
            }, $courses);

            list($courseinsql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $searches[] = "((eventtype = 'course' OR eventtype = 'group') AND courseid {$courseinsql})";
            $params += $courseparams;
        }
    }

    if (!empty($types['category'])) {
        list($categoryinsql, $categoryparams) = $DB->get_in_or_equal(
                array_keys(\core_course_category::make_categories_list('moodle/category:manage')), SQL_PARAMS_NAMED, 'category');
        $searches[] = "(eventtype = 'category' AND categoryid {$categoryinsql})";
        $params += $categoryparams;
    }
}

$sql = "SELECT * FROM {event_subscriptions} WHERE " . implode(' OR ', $searches);;
$subscriptions = $DB->get_records_sql($sql, $params);

// Print title and header.
$PAGE->set_title("$course->shortname: ".get_string('calendar', 'calendar').": ".get_string('subscriptions', 'calendar'));
$heading = get_string('calendar', 'core_calendar');
$heading = ($courseid != SITEID && !empty($courseid)) ? "{$heading}: {$COURSE->shortname}" : $heading;
$PAGE->set_heading($heading);

$renderer = $PAGE->get_renderer('core_calendar');

echo $OUTPUT->header();
echo $renderer->render_subscriptions_header();

// Filter subscriptions which user can't edit.
foreach($subscriptions as $subscription) {
    if (!calendar_can_edit_subscription($subscription)) {
        unset($subscriptions[$subscription->id]);
    }
}

// Display a table of subscriptions.
if (empty($subscription)) {
    echo $renderer->render_no_calendar_subscriptions();
} else {
    echo $renderer->subscription_details($courseid, $subscriptions);
}
echo $OUTPUT->footer();
