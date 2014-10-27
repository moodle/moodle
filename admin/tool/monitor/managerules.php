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

$courseid = optional_param('courseid', 0, PARAM_INT);
$ruleid = optional_param('ruleid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// Validate course id.
if (empty($courseid)) {
    require_login();
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
$a = new stdClass();
$a->coursename = $coursename;
$a->reportname = get_string('pluginname', 'tool_monitor');
$title = get_string('title', 'tool_monitor', $a);
$manageurl = new moodle_url("/admin/tool/monitor/managerules.php", array('courseid' => $courseid));

$PAGE->set_url($manageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Site level report.
if (empty($courseid)) {
    admin_externalpage_setup('toolmonitorrules', '', null, '', array('pagelayout' => 'report'));
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
    if ($rule->can_manage_rule()) {
        switch ($action) {
            case 'copy' :
                $rule->duplicate_rule($courseid);
                echo $OUTPUT->notification(get_string('rulecopysuccess', 'tool_monitor'), 'notifysuccess');
                break;
            case 'delete' :
                $confirmurl = new moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php',
                    array('ruleid' => $ruleid, 'courseid' => $courseid, 'action' => 'delete',
                        'confirm' => true, 'sesskey' => sesskey()));
                $cancelurl = new moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php',
                    array('courseid' => $courseid));
                if ($confirm) {
                    $rule->delete_rule();
                    echo $OUTPUT->notification(get_string('ruledeletesuccess', 'tool_monitor'), 'notifysuccess');
                } else {
                    echo $OUTPUT->confirm(get_string('ruleareyousure', 'tool_monitor', $rule->get_name($context)),
                        $confirmurl, $cancelurl);
                    echo $OUTPUT->footer();
                    exit();
                }
                break;
            default:
        }
    } else {
        // User doesn't have permissions. Should never happen for real users.
        throw new moodle_exception('rulenopermissions', 'tool_monitor', $manageurl, $action);
    }
} else {
    echo $OUTPUT->header();
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
