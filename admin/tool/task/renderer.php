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
 * Output rendering for the plugin.
 *
 * @package     tool_task
 * @copyright   2014 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;


/**
 * Implements the plugin renderer
 *
 * @copyright 2014 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_task_renderer extends plugin_renderer_base {
    /**
     * This function will render one beautiful table with all the scheduled tasks.
     *
     * @param \core\task\scheduled_task[] $tasks - list of all scheduled tasks.
     * @param string $lastchanged (optional) the last task edited. Gets highlighted in teh table.
     * @return string HTML to output.
     */
    public function scheduled_tasks_table($tasks, $lastchanged = '') {
        global $CFG;

        $showloglink = \core\task\logmanager::has_log_report();

        $table = new html_table();
        $table->caption = get_string('scheduledtasks', 'tool_task');
        $table->head = [
            get_string('name'),
            get_string('component', 'tool_task'),
            get_string('edit'),
            get_string('logs'),
            get_string('lastruntime', 'tool_task'),
            get_string('nextruntime', 'tool_task'),
            get_string('taskscheduleminute', 'tool_task'),
            get_string('taskschedulehour', 'tool_task'),
            get_string('taskscheduleday', 'tool_task'),
            get_string('taskscheduledayofweek', 'tool_task'),
            get_string('taskschedulemonth', 'tool_task'),
            get_string('faildelay', 'tool_task'),
            get_string('default', 'tool_task'),
        ];

        $table->attributes['class'] = 'admintable generaltable';
        $table->colclasses = [];

        if (!$showloglink) {
            // Hide the log links.
            $table->colclasses['3'] = 'hidden';
        }

        $data = [];
        $yes = get_string('yes');
        $no = get_string('no');
        $canruntasks = \core\task\manager::is_runnable();
        foreach ($tasks as $task) {
            $classname = get_class($task);
            $defaulttask = \core\task\manager::get_default_scheduled_task($classname, false);

            $customised = $task->is_customised() ? $no : $yes;
            if (empty($CFG->preventscheduledtaskchanges)) {
                $configureurl = new moodle_url('/admin/tool/task/scheduledtasks.php',
                        ['action' => 'edit', 'task' => $classname]);
                $editlink = $this->output->action_icon($configureurl, new pix_icon('t/edit',
                        get_string('edittaskschedule', 'tool_task', $task->get_name())));
            } else {
                $editlink = $this->render(new pix_icon('t/locked',
                        get_string('scheduledtaskchangesdisabled', 'tool_task')));
            }

            $loglink = '';
            if ($showloglink) {
                $loglink = $this->output->action_icon(
                    \core\task\logmanager::get_url_for_task_class($classname),
                    new pix_icon('e/file-text', get_string('viewlogs', 'tool_task', $task->get_name())
                ));
            }

            $namecell = new html_table_cell($task->get_name() . "\n" .
                    html_writer::span('\\' . $classname, 'task-class text-ltr'));
            $namecell->header = true;

            $plugininfo = core_plugin_manager::instance()->get_plugin_info($task->get_component());
            $plugindisabled = $plugininfo && $plugininfo->is_enabled() === false &&
                    !$task->get_run_if_component_disabled();
            $disabled = $plugindisabled || $task->get_disabled();

            $runnow = '';
            if (!$disabled && get_config('tool_task', 'enablerunnow') && $canruntasks ) {
                $runnow = html_writer::div(html_writer::link(
                        new moodle_url('/admin/tool/task/schedule_task.php',
                            ['task' => $classname]),
                        get_string('runnow', 'tool_task')), 'task-runnow');
            }

            $faildelaycell = new html_table_cell($task->get_fail_delay());
            if ($task->get_fail_delay()) {
                $faildelaycell->text .= html_writer::div(html_writer::link(
                        new moodle_url('/admin/tool/task/clear_fail_delay.php',
                                ['task' => $classname, 'sesskey' => sesskey()]),
                        get_string('clear')), 'task-clearfaildelay');
                $faildelaycell->attributes['class'] = 'table-danger';
            }

            $row = new html_table_row([
                        $namecell,
                        new html_table_cell($this->component_name($task->get_component())),
                        new html_table_cell($editlink),
                        new html_table_cell($loglink),
                        new html_table_cell($this->last_run_time($task) . $runnow),
                        new html_table_cell($this->next_run_time($task)),
                        $this->time_cell($task->get_minute(), $defaulttask->get_minute()),
                        $this->time_cell($task->get_hour(), $defaulttask->get_hour()),
                        $this->time_cell($task->get_day(), $defaulttask->get_day()),
                        $this->time_cell($task->get_day_of_week(), $defaulttask->get_day_of_week()),
                        $this->time_cell($task->get_month(), $defaulttask->get_month()),
                        $faildelaycell,
                        new html_table_cell($customised)]);

            $classes = [];
            if ($disabled) {
                $classes[] = 'disabled';
            }
            if (get_class($task) == $lastchanged) {
                $classes[] = 'table-primary';
            }
            $row->attributes['class'] = implode(' ', $classes);
            $data[] = $row;
        }
        $table->data = $data;
        if ($lastchanged) {
            // IE does not support this, and the ancient version of Firefox we use for Behat
            // has the method, but then errors on 'centre'. So, just try to scroll, and if it fails, don't care.
            $this->page->requires->js_init_code(
                    'try{document.querySelector("tr.table-primary").scrollIntoView({block: "center"});}catch(e){}');
        }
        return html_writer::table($table);
    }

    /**
     * Nicely display the name of a component, with its disabled status and internal name.
     *
     * @param string $component component name, e.g. 'core' or 'mod_forum'.
     * @return string HTML.
     */
    public function component_name(string $component): string {
        list($type) = core_component::normalize_component($component);
        if ($type === 'core') {
            return get_string('corecomponent', 'tool_task');
        }

        $plugininfo = core_plugin_manager::instance()->get_plugin_info($component);
        if (!$plugininfo) {
            return $component;
        }

        $plugininfo->init_display_name();

        $componentname = $plugininfo->displayname;
        if ($plugininfo->is_enabled() === false) {
            $componentname .= ' ' . html_writer::span(
                            get_string('disabled', 'tool_task'), 'badge badge-secondary');
        }
        $componentname .= "\n" . html_writer::span($plugininfo->component, 'task-class text-ltr');

        return $componentname;
    }

    /**
     * Standard display of a tasks last run time.
     *
     * @param scheduled_task $task
     * @return string HTML.
     */
    public function last_run_time(scheduled_task $task): string {
        if ($task->get_last_run_time()) {
            return userdate($task->get_last_run_time());
        } else {
            return get_string('never');
        }
    }

    /**
     * Standard display of a tasks next run time.
     *
     * @param scheduled_task $task
     * @return string HTML.
     */
    public function next_run_time(scheduled_task $task): string {
        $plugininfo = core_plugin_manager::instance()->get_plugin_info($task->get_component());

        $nextrun = $task->get_next_run_time();
        if ($plugininfo && $plugininfo->is_enabled() === false && !$task->get_run_if_component_disabled()) {
            $nextrun = get_string('plugindisabled', 'tool_task');
        } else if ($task->get_disabled()) {
            $nextrun = get_string('taskdisabled', 'tool_task');
        } else if ($nextrun > time()) {
            $nextrun = userdate($nextrun);
        } else {
            $nextrun = get_string('asap', 'tool_task');
        }

        return $nextrun;
    }

    /**
     * Get a table cell to show one time, comparing it to the default.
     *
     * @param string $current the current setting.
     * @param string $default the default setting from the db/tasks.php file.
     * @return html_table_cell for use in the table.
     */
    protected function time_cell(string $current, string $default): html_table_cell {
        $cell = new html_table_cell($current);
        // Cron-style values must always be LTR.
        $cell->attributes['class'] = 'text-ltr';

        // If the current value is default, that is all we want to do.
        if ($default === '*') {
            if ($current === '*') {
                return $cell;
            }
        } else if ($default === 'R' ) {
            if (is_numeric($current)) {
                return $cell;
            }
        } else {
            if ($default === $current) {
                return $cell;
            }
        }

        // Otherwise, highlight and show the default.
        $cell->attributes['class'] .= ' table-warning';
        $cell->text .= ' ' . html_writer::span(
                get_string('defaultx', 'tool_task', $default), 'task-class');
        return $cell;
    }

    /**
     * Renders a link back to the scheduled tasks page (used from the 'run now' screen).
     *
     * @param string $taskclassname if specified, the list of tasks will scroll to show this task.
     * @return string HTML code
     */
    public function link_back($taskclassname = '') {
        $url = new moodle_url('/admin/tool/task/scheduledtasks.php');
        if ($taskclassname) {
            $url->param('lastchanged', $taskclassname);
        }
        return $this->render_from_template('tool_task/link_back', ['url' => $url]);
    }
}
