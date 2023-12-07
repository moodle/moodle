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
 * Web run ad hoc task(s)
 *
 * This script runs a group or a single ad hoc task from the web UI.
 *
 * @package    tool_task
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('adhoctasks');

$runurl = '/admin/tool/task/run_adhoctasks.php';
$tasksurl = '/admin/tool/task/adhoctasks.php';

// Allow execution of single task. This requires login and has different rules.
$classname = optional_param('classname', null, PARAM_RAW);
$failedonly = optional_param('failedonly', false, PARAM_BOOL);
$taskid = optional_param('id', null, PARAM_INT);
$confirmed = optional_param('confirm', 0, PARAM_INT);

if (!\core\task\manager::is_runnable()) {
    $redirecturl = new \moodle_url('/admin/settings.php', ['section' => 'systempaths']);
    throw new moodle_exception('cannotfindthepathtothecli', 'tool_task', $redirecturl->out());
}

$params = ['classname' => $classname, 'failedonly' => $failedonly, 'id' => $taskid];

// Check input parameter id against all existing tasks.
if ($taskid) {
    $record = $DB->get_record('task_adhoc', ['id' => $taskid]);
    if (!$record) {
        throw new \moodle_exception('invalidtaskid');
    }
    $classname = $record->classname;
    $heading = "Run $classname task Id $taskid";
    $tasks = [core\task\manager::adhoc_task_from_record($record)];
} else {
    if (!$classname) {
        throw new \moodle_exception('noclassname', 'tool_task');
    }
    $heading = "Run " . s($classname) . " " . ($failedonly ? "failed" : "all")." tasks";
    $now = time();
    $tasks = array_filter(
        core\task\manager::get_adhoc_tasks($classname, $failedonly, true),
        function ($t) use ($now) {
            return $t->get_fail_delay() || $t->get_next_run_time() <= $now;
        }
    );
}

// Start output.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($classname);

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

if (!$tasks) {
    echo $OUTPUT->single_button($tasksurl,
            get_string('notasks', 'tool_task'),
            'get');
    echo $OUTPUT->footer();
    exit;
}

$renderer = $PAGE->get_renderer('tool_task');
if (!get_config('core', 'cron_enabled')) {
    echo $renderer->cron_disabled();
}
echo $renderer->adhoc_tasks_simple_table($tasks);

// The initial request just shows the confirmation page; we don't do anything further unless
// they confirm.
if (!$confirmed) {
    echo $OUTPUT->confirm(get_string('runadhoc_confirm', 'tool_task'),
            new single_button(new moodle_url($runurl, array_merge($params, ['confirm' => 1])),
            get_string('runadhoc', 'tool_task')),
            new single_button(new moodle_url($tasksurl, $params),
            get_string('cancel'), false));
    echo $OUTPUT->footer();
    exit;
}

// Action requires session key.
require_sesskey();

\core\session\manager::write_close();

// Prepare to handle output via mtrace.
require('lib.php');
$CFG->mtrace_wrapper = 'tool_task_mtrace_wrapper';

// Run the specified tasks.
if ($taskid) {
    $repeat = $DB->get_record('task_adhoc', ['id' => $taskid]);

    echo html_writer::start_tag('pre');
    \core\task\manager::run_adhoc_from_cli($taskid);
    echo html_writer::end_tag('pre');
} else {
    $repeat = core\task\manager::get_adhoc_tasks($classname, $failedonly, true);

    // Run failed first (if any). We have to run them separately anyway,
    // because faildelay is observed if failed flag is not true.
    echo html_writer::tag('p', get_string('runningfailedtasks', 'tool_task'), ['class' => 'lead']);
    echo html_writer::start_tag('pre');
    \core\task\manager::run_all_adhoc_from_cli(true, $classname);
    echo html_writer::end_tag('pre');

    if (!$failedonly) {
        echo html_writer::tag('p', get_string('runningalltasks', 'tool_task'), ['class' => 'lead']);
        echo html_writer::start_tag('pre');
        \core\task\manager::run_all_adhoc_from_cli(false, $classname);
        echo html_writer::end_tag('pre');
    }
}

if ($repeat) {
    echo html_writer::div(
        $OUTPUT->single_button(
            new moodle_url($runurl, array_merge($params, ['confirm' => 1])),
            get_string('runagain', 'tool_task')
        )
    );
}

echo html_writer::div(
    html_writer::link(
        new moodle_url($tasksurl, $taskid ? ['classname' => $classname] : []),
        get_string('backtoadhoctasks', 'tool_task')
    )
);

echo $OUTPUT->footer();
