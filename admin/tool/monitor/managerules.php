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

use core\report_helper;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$ruleid = optional_param('ruleid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$status = optional_param('status', 0, PARAM_BOOL);

// Validate course id.
if (empty($courseid)) {
    admin_externalpage_setup('toolmonitorrules', '', null, '', array('pagelayout' => 'report'));
    $context = context_system::instance();
    $coursename = format_string($SITE->fullname, true, array('context' => $context));
    $PAGE->set_context($context);
} else {
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($course->id);
    $coursename = format_string($course->fullname, true, array('context' => $context));
}

// Check for caps.
require_capability('tool/monitor:managerules', $context);

// Set up the page.
$manageurl = new moodle_url("/admin/tool/monitor/managerules.php", array('courseid' => $courseid));
$PAGE->set_url($manageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($coursename);
$PAGE->set_heading($coursename);


if (!empty($action) && $action == 'changestatus') {
    require_sesskey();
    require_capability('tool/monitor:managetool', context_system::instance());
    // Toggle status of the plugin.
    set_config('enablemonitor', $status, 'tool_monitor');
    redirect(new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => 0)));
}

// Copy/delete rule if needed.
if (!empty($action) && $ruleid) {
    require_sesskey();

    // If the rule does not exist, then redirect back as the rule must have already been deleted.
    if (!$rule = $DB->get_record('tool_monitor_rules', array('id' => $ruleid), '*', IGNORE_MISSING)) {
        redirect(new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => $courseid)));
    }

    echo $OUTPUT->header();
    $rule = \tool_monitor\rule_manager::get_rule($rule);
    switch ($action) {
        case 'copy':
            // No need to check for capability here as it is done at the start of the page.
            $rule->duplicate_rule($courseid);
            echo $OUTPUT->notification(get_string('rulecopysuccess', 'tool_monitor'), 'notifysuccess');
            break;
        case 'delete':
            if ($rule->can_manage_rule()) {
                $confirmurl = new moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php',
                    array('ruleid' => $ruleid, 'courseid' => $courseid, 'action' => 'delete',
                        'confirm' => true, 'sesskey' => sesskey()));
                $cancelurl = new moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php',
                    array('courseid' => $courseid));
                if ($confirm) {
                    $rule->delete_rule();
                    echo $OUTPUT->notification(get_string('ruledeletesuccess', 'tool_monitor'), 'notifysuccess');
                } else {
                    $strconfirm = get_string('ruleareyousure', 'tool_monitor', $rule->get_name($context));
                    if ($numberofsubs = $DB->count_records('tool_monitor_subscriptions', array('ruleid' => $ruleid))) {
                        $strconfirm .= '<br />';
                        $strconfirm .= get_string('ruleareyousureextra', 'tool_monitor', $numberofsubs);
                    }
                    echo $OUTPUT->confirm($strconfirm, $confirmurl, $cancelurl);
                    echo $OUTPUT->footer();
                    exit();
                }
            } else {
                // User doesn't have permissions. Should never happen for real users.
                throw new moodle_exception('rulenopermissions', 'tool_monitor', $manageurl, $action);
            }
            break;
        default:
    }
} else {
    echo $OUTPUT->header();
}

report_helper::save_selected_report($courseid, $manageurl);

// Print the selected dropdown.
$managerules = get_string('managerules', 'tool_monitor');
report_helper::print_report_selector($managerules);

echo $OUTPUT->heading(get_string('managerules', 'tool_monitor'));
$status = get_config('tool_monitor', 'enablemonitor');
$help = new help_icon('enablehelp', 'tool_monitor');

// Display option to enable/disable the plugin.
if ($status) {
    if (has_capability('tool/monitor:managetool', context_system::instance())) {
        // We don't need to show enabled status to everyone.
        echo get_string('monitorenabled', 'tool_monitor');
        $disableurl = new moodle_url("/admin/tool/monitor/managerules.php",
                array('courseid' => $courseid, 'action' => 'changestatus', 'status' => 0, 'sesskey' => sesskey()));
        echo ' ' . html_writer::link($disableurl, get_string('disable'));
        echo $OUTPUT->render($help);
    }
} else {
    echo get_string('monitordisabled', 'tool_monitor');
    if (has_capability('tool/monitor:managetool', context_system::instance())) {
        $enableurl = new moodle_url("/admin/tool/monitor/managerules.php",
                array('courseid' => $courseid, 'action' => 'changestatus', 'status' => 1, 'sesskey' => sesskey()));
        echo ' ' . html_writer::link($enableurl, get_string('enable'));
        echo $OUTPUT->render($help);
    } else {
        echo ' ' . get_string('contactadmin', 'tool_monitor');
    }
    echo $OUTPUT->footer(); // Do not render anything else.
    exit();
}

// Render the rule list.
$renderable = new \tool_monitor\output\managerules\renderable('toolmonitorrules', $manageurl, $courseid);
$renderer = $PAGE->get_renderer('tool_monitor', 'managerules');
echo $renderer->render($renderable);
if (has_capability('tool/monitor:subscribe', $context)) {
    $manageurl = new moodle_url("/admin/tool/monitor/index.php", array('courseid' => $courseid));
    echo $renderer->render_subscriptions_link($manageurl);
}
echo $OUTPUT->footer();
