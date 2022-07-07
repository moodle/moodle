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
 * Script clears the fail delay for a task and reschedules its next execution.
 *
 * @package tool_task
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');

require_once($CFG->libdir.'/cronlib.php');

// Basic security checks.
require_admin();
$context = context_system::instance();

// Get task and check the parameter is valid.
$taskname = required_param('task', PARAM_RAW_TRIMMED);
$task = \core\task\manager::get_scheduled_task($taskname);
if (!$task) {
    print_error('cannotfindinfo', 'error', $taskname);
}

$returnurl = new moodle_url('/admin/tool/task/scheduledtasks.php',
        ['lastchanged' => get_class($task)]);

// If actually doing the clear, then carry out the task and redirect to the scheduled task page.
if (optional_param('confirm', 0, PARAM_INT)) {
    require_sesskey();

    \core\task\manager::clear_fail_delay($task);

    redirect($returnurl);
}

// Start output.
$PAGE->set_url(new moodle_url('/admin/tool/task/schedule_task.php'));
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('scheduledtasks', 'tool_task'), new moodle_url('/admin/tool/task/scheduledtasks.php'));
$PAGE->navbar->add(s($task->get_name()));
$PAGE->navbar->add(get_string('clear'));
echo $OUTPUT->header();

// The initial request just shows the confirmation page; we don't do anything further unless
// they confirm.
echo $OUTPUT->confirm(get_string('clearfaildelay_confirm', 'tool_task', $task->get_name()),
        new single_button(new moodle_url('/admin/tool/task/clear_fail_delay.php',
                ['task' => $taskname, 'confirm' => 1, 'sesskey' => sesskey()]),
                get_string('clear')),
        new single_button($returnurl, get_string('cancel'), false));

echo $OUTPUT->footer();
