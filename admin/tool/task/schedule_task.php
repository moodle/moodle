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
 * Web cron single task
 *
 * This script runs a single scheduled task from the web UI.
 *
 * @package tool_task
 * @copyright 2016 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');

require_once($CFG->libdir.'/cronlib.php');

// Allow execution of single task. This requires login and has different rules.
$taskname = required_param('task', PARAM_RAW_TRIMMED);

// Basic security checks.
require_admin();
$context = context_system::instance();

// Check input parameter against all existing tasks (this ensures it isn't possible to
// create some kind of security problem by specifying a class that isn't a task or whatever).
$task = \core\task\manager::get_scheduled_task($taskname);
if (!$task) {
    throw new moodle_exception('cannotfindinfo', 'error', new moodle_url('/admin/tool/task/scheduledtasks.php'), $taskname);
}

if (!\core\task\manager::is_runnable()) {
    $redirecturl = new \moodle_url('/admin/settings.php', ['section' => 'systempaths']);
    throw new moodle_exception('cannotfindthepathtothecli', 'tool_task', $redirecturl->out());
}

if (!get_config('tool_task', 'enablerunnow') || !$task->can_run()) {
    throw new moodle_exception('nopermissions', 'error', new moodle_url('/admin/tool/task/scheduledtasks.php'),
        get_string('runnow', 'tool_task'), $task->get_name());
}

// Start output.
$PAGE->set_url(new moodle_url('/admin/tool/task/schedule_task.php', ['task' => $taskname]));
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($task->get_name());

navigation_node::override_active_url(new moodle_url('/admin/tool/task/scheduledtasks.php'));
$PAGE->navbar->add(s($task->get_name()));

echo $OUTPUT->header();
echo $OUTPUT->heading($task->get_name());

// The initial request just shows the confirmation page; we don't do anything further unless
// they confirm.
if (!optional_param('confirm', 0, PARAM_INT)) {
    echo $OUTPUT->confirm(get_string('runnow_confirm', 'tool_task', $task->get_name()),
            new single_button(new moodle_url('/admin/tool/task/schedule_task.php',
                    ['task' => $taskname, 'confirm' => 1, 'sesskey' => sesskey()]),
            get_string('runnow', 'tool_task')),
            new single_button(new moodle_url('/admin/tool/task/scheduledtasks.php',
                    ['lastchanged' => get_class($task)]),
            get_string('cancel'), false));
    echo $OUTPUT->footer();
    exit;
}

// Action requires session key.
require_sesskey();

\core\session\manager::write_close();

// Prepare to handle output via mtrace.
echo html_writer::start_tag('pre');
require('lib.php');
$CFG->mtrace_wrapper = 'tool_task_mtrace_wrapper';

// Run the specified task (this will output an error if it doesn't exist).
\core\task\manager::run_from_cli($task);

echo html_writer::end_tag('pre');

$output = $PAGE->get_renderer('tool_task');

// Re-run the specified task (this will output an error if it doesn't exist).
echo $OUTPUT->single_button(new moodle_url('/admin/tool/task/schedule_task.php',
        array('task' => $taskname, 'confirm' => 1, 'sesskey' => sesskey())),
        get_string('runagain', 'tool_task'));
echo $output->link_back(get_class($task));

echo $OUTPUT->footer();
