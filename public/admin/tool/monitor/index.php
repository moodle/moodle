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
 * This page lets users to manage rules for a given course.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/monitor/lib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$cmid = optional_param('cmid', 0, PARAM_INT);
$ruleid = optional_param('ruleid', 0, PARAM_INT);
$subscriptionid = optional_param('subscriptionid', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$choose = false;
// Validate course id.
if (empty($courseid)) {
    require_login(null, false);
    $context = context_system::instance();
    // check system level capability.
    if (!has_capability('tool/monitor:subscribe', $context)) {
        // If not system level then check to see if they have access to any course level rules.
        if (tool_monitor_can_subscribe()) {
            // Make them choose a course.
            $choose = true;
        } else {
            // return error.
            throw new \moodle_exception('rulenopermission', 'tool_monitor');
        }
    }
} else {
    // They might want to see rules for this course.
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($course->id);
    // Check for caps.
    require_capability('tool/monitor:subscribe', $context);
}

if (!get_config('tool_monitor', 'enablemonitor')) {
    // This should never happen as the this page does not appear in navigation when the tool is disabled.
    throw new coding_exception('Event monitoring is disabled');
}

// Use the user context here so that the header shows user information.
$PAGE->set_context(context_user::instance($USER->id));

// Set up the page.
$indexurl = new moodle_url('/admin/tool/monitor/index.php', array('courseid' => $courseid));
$PAGE->set_url($indexurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('managesubscriptions', 'tool_monitor'));
$PAGE->set_heading(fullname($USER));
$settingsnode = $PAGE->settingsnav->find('monitor', null);
if ($settingsnode) {
    $settingsnode->make_active();
}

// Create/delete subscription if needed.
if (!empty($action)) {
    require_sesskey();
    switch ($action) {
        case 'subscribe' :
            $rule = \tool_monitor\rule_manager::get_rule($ruleid);
            $rule->subscribe_user($courseid, $cmid);
            echo $OUTPUT->header();
            echo $OUTPUT->notification(get_string('subcreatesuccess', 'tool_monitor'), 'notifysuccess');
            break;
        case 'unsubscribe' :
            // If the subscription does not exist, then redirect back as the subscription must have already been deleted.
            if (!$subscription = $DB->record_exists('tool_monitor_subscriptions', array('id' => $subscriptionid))) {
                redirect(new moodle_url('/admin/tool/monitor/index.php', array('courseid' => $courseid)));
            }

            // Set the URLs.
            $confirmurl = new moodle_url('/admin/tool/monitor/index.php', array('subscriptionid' => $subscriptionid,
                'courseid' => $courseid, 'action' => 'unsubscribe', 'confirm' => true,
                'sesskey' => sesskey()));
            $cancelurl = new moodle_url('/admin/tool/monitor/index.php', array('subscriptionid' => $subscriptionid,
                'courseid' => $courseid, 'sesskey' => sesskey()));
            if ($confirm) {
                \tool_monitor\subscription_manager::delete_subscription($subscriptionid);
                echo $OUTPUT->header();
                echo $OUTPUT->notification(get_string('subdeletesuccess', 'tool_monitor'), 'notifysuccess');
            } else {
                $subscription = \tool_monitor\subscription_manager::get_subscription($subscriptionid);
                echo $OUTPUT->header();
                echo $OUTPUT->confirm(get_string('subareyousure', 'tool_monitor', $subscription->get_name($context)),
                    $confirmurl, $cancelurl);
                echo $OUTPUT->footer();
                exit();
            }
            break;
        default:
    }
} else {
    echo $OUTPUT->header();
}

$renderer = $PAGE->get_renderer('tool_monitor', 'managesubs');

// Render the course selector.
$totalrules = \tool_monitor\rule_manager::count_rules_by_courseid($courseid);
$rules = new \tool_monitor\output\managesubs\rules('toolmonitorrules', $indexurl, $courseid);

$usercourses = $rules->get_user_courses_select($choose);
// There must be user courses otherwise we wouldn't make it this far.
echo $renderer->render($usercourses);

// Render the current subscriptions list.
$totalsubs = \tool_monitor\subscription_manager::count_user_subscriptions();
if (!empty($totalsubs) && !$choose) {
    // Show the subscriptions section only if there are subscriptions.
    $subs = new \tool_monitor\output\managesubs\subs('toolmonitorsubs', $indexurl, $courseid);
    echo $OUTPUT->heading(get_string('currentsubscriptions', 'tool_monitor'), 3);
    echo $renderer->render($subs);
}

// Render the potential rules list.
if (!$choose) {
    echo $OUTPUT->heading(get_string('rulescansubscribe', 'tool_monitor'), 3);
    echo $renderer->render($rules);
}

// Check if the user can manage the course rules we are viewing.
$canmanagerules = has_capability('tool/monitor:managerules', $context);

if (empty($totalrules)) {
    // No rules present. Show a link to manage rules page if permissions permit.
    echo html_writer::start_div();
    echo html_writer::tag('span', get_string('norules', 'tool_monitor'));
    if ($canmanagerules) {
        $manageurl = new moodle_url("/admin/tool/monitor/managerules.php", array('courseid' => $courseid));
        $a = html_writer::link($manageurl, get_string('managerules', 'tool_monitor'));
        $link = "&nbsp;";
        $link .= html_writer::tag('span', get_string('manageruleslink', 'tool_monitor', $a));
        echo $link;
    }
    echo html_writer::end_div();
} else if ($canmanagerules) {
    $manageurl = new moodle_url("/admin/tool/monitor/managerules.php", array('courseid' => $courseid));
    echo $renderer->render_rules_link($manageurl);
}
echo $OUTPUT->footer();
