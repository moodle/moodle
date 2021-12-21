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
 * Scheduled task admin pages.
 *
 * @package    tool_task
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('scheduledtasks');

$action = optional_param('action', '', PARAM_ALPHAEXT);
$taskname = optional_param('task', '', PARAM_RAW);
$lastchanged = optional_param('lastchanged', '', PARAM_RAW);

$task = null;
$mform = null;

if ($taskname) {
    $task = \core\task\manager::get_scheduled_task($taskname);
    if (!$task) {
        print_error('invaliddata');
    }
}

if ($action == 'edit') {
    $PAGE->navbar->add(get_string('edittaskschedule', 'tool_task', $task->get_name()));
}

if ($task) {
    $mform = new tool_task_edit_scheduled_task_form(null, $task);
    $nexturl = new moodle_url($PAGE->url, ['lastchanged' => $taskname]);
}

$renderer = $PAGE->get_renderer('tool_task');

if ($mform && ($mform->is_cancelled() || !empty($CFG->preventscheduledtaskchanges) || $task->is_overridden())) {
    redirect($nexturl);
} else if ($action == 'edit' && empty($CFG->preventscheduledtaskchanges)) {

    if ($data = $mform->get_data()) {
        if ($data->resettodefaults) {
            $defaulttask = \core\task\manager::get_default_scheduled_task($taskname);
            $task->set_minute($defaulttask->get_minute());
            $task->set_hour($defaulttask->get_hour());
            $task->set_month($defaulttask->get_month());
            $task->set_day_of_week($defaulttask->get_day_of_week());
            $task->set_day($defaulttask->get_day());
            $task->set_disabled($defaulttask->get_disabled());
            $task->set_customised(false);
        } else {
            $task->set_minute($data->minute);
            $task->set_hour($data->hour);
            $task->set_month($data->month);
            $task->set_day_of_week($data->dayofweek);
            $task->set_day($data->day);
            $task->set_disabled($data->disabled);
            $task->set_customised(true);
        }

        try {
            \core\task\manager::configure_scheduled_task($task);
            redirect($nexturl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
        } catch (Exception $e) {
            redirect($nexturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('edittaskschedule', 'tool_task', $task->get_name()));
        echo html_writer::div('\\' . get_class($task), 'task-class text-ltr');
        echo html_writer::div(get_string('fromcomponent', 'tool_task',
                $renderer->component_name($task->get_component())));
        $mform->display();
        echo $OUTPUT->footer();
    }

} else {
    echo $OUTPUT->header();
    if (!get_config('core', 'cron_enabled')) {
        echo $renderer->cron_disabled();
    }
    $tasks = core\task\manager::get_all_scheduled_tasks();
    echo $renderer->scheduled_tasks_table($tasks, $lastchanged);
    echo $OUTPUT->footer();
}
