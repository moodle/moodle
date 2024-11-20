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
     * This function will render a table with the summary of all adhoc tasks.
     *
     * @param array $summary
     * @return string HTML to output.
     */
    public function adhoc_tasks_summary_table(array $summary): string {
        $adhocurl = '/admin/tool/task/adhoctasks.php';
        $adhocrunurl = '/admin/tool/task/run_adhoctasks.php';

        // Main tasks table.
        $table = new html_table();
        $table->caption = get_string('adhoctasks', 'tool_task');
        $table->head = [
            get_string('component', 'tool_task') . ' / ' . get_string('classname', 'tool_task'),
            get_string('adhoctasksrunning', 'tool_task'),
            get_string('adhoctasksdue', 'tool_task'),
            get_string('adhoctasksfuture', 'tool_task'),
            get_string('adhoctasksfailed', 'tool_task'),
            get_string('nextruntime', 'tool_task'),
        ];

        $table->attributes['class'] = 'admintable generaltable';
        $table->colclasses = [];

        // For each task entry (row) show action buttons/logs link depending on the user permissions.
        $data = [];
        $canruntasks = \core\task\manager::is_runnable() && get_config('tool_task', 'enablerunnow');
        foreach ($summary as $component => $classes) {
            // Component cell.
            $componentcell = new html_table_cell($component);
            $componentcell->header = true;
            $componentcell->id = "tasks-$component";
            $componentcell->colspan = 6;

            $data[] = new html_table_row([$componentcell]);

            foreach ($classes as $classname => $stats) {
                // Task class cell.
                $classbits = explode('\\',  $classname);
                $classcontent = html_writer::link(
                    new moodle_url($adhocurl, ['classname' => $classname]),
                    end($classbits)
                );
                $classcell = new html_table_cell($classcontent);
                $classcell->header = true;
                $classcell->attributes['class'] = "task-class-summary text-ltr";

                $duecontent = $stats['due'];
                if ($canruntasks && ($stats['due'] > 0 || $stats['failed'] > 0)) {
                    $duecontent .= html_writer::div(
                        html_writer::link(
                            new moodle_url(
                                $adhocrunurl,
                                ['classname' => $classname]
                            ),
                            get_string('runclassname', 'tool_task')
                        ),
                        'task-runnow'
                    );
                }

                // Mark cell if has failed tasks.
                $failed = $stats['failed'];
                if ($canruntasks && $failed > 0) {
                    $failed .= html_writer::div(
                        html_writer::link(
                            new moodle_url(
                                $adhocrunurl,
                                ['classname' => $classname, 'failedonly' => 1]
                            ),
                            get_string('runclassnamefailedonly', 'tool_task')
                        ),
                        'task-runnow'
                    );
                }
                $failedcell = new html_table_cell($failed);
                if ($failed > 0) {
                    $failedcell->attributes['class'] = 'table-danger';
                }

                // Prepares the next run time cell contents.
                $nextrun = '';
                if ($stats['stop']) {
                    $nextrun = get_string('never', 'admin');
                } else if ($stats['due'] > 0) {
                    $nextrun = get_string('asap', 'tool_task');
                } else if ($stats['nextruntime']) {
                    $nextrun = userdate($stats['nextruntime']);
                }

                $data[] = new html_table_row([
                    $classcell,
                    new html_table_cell($stats['running']),
                    new html_table_cell($duecontent),
                    new html_table_cell($stats['count'] - $stats['running'] - $stats['due']),
                    $failedcell,
                    new html_table_cell($nextrun),
                ]);
            }
        }
        $table->data = $data;
        return html_writer::table($table);
    }

    /**
     * This function will render a table with all the adhoc tasks for the class.
     *
     * @param string $classname
     * @param array $tasks - list of all adhoc tasks.
     * @param array|null $params
     * @return string HTML to output.
     */
    public function adhoc_tasks_class_table(string $classname, array $tasks, ?array $params = []): string {
        $adhocurl = '/admin/tool/task/adhoctasks.php';
        $adhocrunurl = '/admin/tool/task/run_adhoctasks.php';
        $showloglink = \core\task\logmanager::has_log_report();
        $failedonly = !empty($params['failedonly']);
        $canruntasks = \core\task\manager::is_runnable() && get_config('tool_task', 'enablerunnow');

        // Depending on the currently set parameters, set up toggle buttons.
        $failedorall = html_writer::link(
            new moodle_url(
                $adhocurl,
                array_merge($params, ['classname' => $classname, 'failedonly' => !$failedonly])
            ),
            get_string($failedonly ? 'showall' : 'showfailedonly', 'tool_task')
        );

        // Main tasks table.
        $table = $this->generate_adhoc_tasks_simple_table($tasks, $canruntasks);

        $table->caption = s($classname) . " "
            . get_string($failedonly ? 'adhoctasksfailed' : 'adhoctasks', 'tool_task');
        $table->head[3] .= " $failedorall"; // Spice up faildelay heading.

        if ($showloglink) {
            // Insert logs as the second col.
            array_splice($table->head, 1, 0, [get_string('logs')]);
            array_walk($table->data, function ($row, $idx) use ($classname) {
                $loglink = '';
                $faildelaycell = $row->cells[3];
                if ($faildelaycell->attributes['class'] == 'table-danger') {
                    // Failed task.
                    $loglink = $this->output->action_icon(
                        \core\task\logmanager::get_url_for_task_class($classname),
                        new pix_icon('e/file-text', get_string('viewlogs', 'tool_task', $classname)
                    ));
                }

                array_splice($row->cells, 1, 0, [new html_table_cell($loglink)]);
            });
        }

        return html_writer::table($table)
            . html_writer::div(
                html_writer::link(
                    new moodle_url(
                        $adhocrunurl,
                        array_merge($params, ['classname' => $classname])
                    ),
                    get_string('runclassname', 'tool_task')
                ),
                'task-runnow'
            )
            . html_writer::div(
                html_writer::link(
                    new moodle_url(
                        $adhocurl
                    ),
                    get_string('showsummary', 'tool_task')
                ),
                'task-show-summary'
            );
    }

    /**
     * This function will render a plain adhoc tasks table.
     *
     * @param array $tasks - list of adhoc tasks.
     * @return string HTML to output.
     */
    public function adhoc_tasks_simple_table(array $tasks): string {
        $table = $this->generate_adhoc_tasks_simple_table($tasks);

        return html_writer::table($table);
    }

    /**
     * This function will render a plain adhoc tasks table.
     *
     * @param array $tasks - list of adhoc tasks.
     * @param bool $wantruntasks add 'Run now' link
     * @return html_table
     */
    private function generate_adhoc_tasks_simple_table(array $tasks, bool $wantruntasks = false): html_table {
        $adhocrunurl = '/admin/tool/task/run_adhoctasks.php';
        $now = time();
        $failedstr = get_string('failed', 'tool_task');

        // Main tasks table.
        $table = new html_table();
        $table->caption = get_string('adhoctasks', 'tool_task');
        $table->head = [
            get_string('taskid', 'tool_task'),
            get_string('nextruntime', 'tool_task'),
            get_string('payload', 'tool_task'),
            $failedstr
        ];

        $table->attributes['class'] = 'generaltable';
        $table->colclasses = [];

        // For each task entry (row) show action buttons/logs link depending on the user permissions.
        $data = [];
        foreach ($tasks as $task) {
            $taskid = $task->get_id();
            $started = $task->get_timestarted();

            // Task id cell.
            $taskidcellcontent = html_writer::span($taskid, 'task-id');
            $taskidcell = new html_table_cell($taskidcellcontent);
            $taskidcell->header = true;
            $taskidcell->id = "task-$taskid";

            // Mark cell if task has failed.
            $faildelay = $task->get_fail_delay();
            $faildelaycell = new html_table_cell($faildelay ? $failedstr : '');
            if ($faildelay) {
                $faildelaycell->attributes['class'] = 'table-danger';
            }

            // Prepares the next run time cell contents.
            $nextrun = get_string('started', 'tool_task');
            if (!$started) {
                $nextruntime = $task->get_next_run_time();
                $due = $nextruntime < $now;
                if ($task->get_attempts_available() > 0) {
                    $nextrun = $due ? userdate($nextruntime) : get_string('asap', 'tool_task');
                } else {
                    $nextrun = get_string('never', 'admin');
                }

                if ($wantruntasks && ($faildelay || $due)) {
                    $nextrun .= ' '.html_writer::div(
                        html_writer::link(
                            new moodle_url(
                                $adhocrunurl,
                                ['id' => $taskid]
                            ),
                            get_string('runnow', 'tool_task')
                        ),
                        'task-runnow'
                    );
                }
            }

            $data[] = new html_table_row([
                $taskidcell,
                new html_table_cell($nextrun),
                new html_table_cell($task->get_custom_data_as_string()),
                $faildelaycell,
            ]);
        }
        $table->data = $data;

        return $table;
    }

    /**
     * Displays a notification on ad hoc task run request.
     *
     * @return string HTML notification block for task initiated message
     */
    public function adhoc_task_run(): string {
        return $this->output->notification(get_string('adhoctaskrun', 'tool_task'), 'info');
    }

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
        $canruntasks = \core\task\manager::is_runnable() && get_config('tool_task', 'enablerunnow');
        foreach ($tasks as $task) {
            $classname = get_class($task);
            $defaulttask = \core\task\manager::get_default_scheduled_task($classname, false);

            $customised = $task->is_customised() ? $no : $yes;
            if (empty($CFG->preventscheduledtaskchanges) && !$task->is_overridden()) {
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

            $namecellcontent = $task->get_name() . "\n" .
                html_writer::span('\\' . $classname, 'task-class text-ltr');
            if ($task->is_overridden()) {
                // Let the user know the scheduled task is defined in config.
                $namecellcontent .= "\n" . html_writer::div(get_string('configoverride', 'admin'), 'alert-info');
            }
            $namecell = new html_table_cell($namecellcontent);
            $namecell->header = true;
            $namecell->id = scheduled_task::get_html_id($classname);

            $runnow = '';
            $canrunthistask = $canruntasks && $task->can_run();
            if ($canrunthistask) {
                $runnow = html_writer::div(html_writer::link(
                        new moodle_url('/admin/tool/task/schedule_task.php',
                            ['task' => $classname]),
                        get_string('runnow', 'tool_task')), 'task-runnow');
            }

            $faildelaycell = new html_table_cell($task->get_fail_delay());
            if ($task->get_fail_delay()) {
                $faildelaycell->text .= html_writer::div(
                    $this->output->single_button(
                        new moodle_url('/admin/tool/task/clear_fail_delay.php',
                                ['task' => $classname]),
                        get_string('clear')
                    ),
                    'task-runnow'
                );
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
            if (!$task->is_enabled()) {
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
                            get_string('disabled', 'tool_task'), 'badge bg-secondary text-dark');
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
        $nextrun = $task->get_next_run_time();

        if (!$task->is_component_enabled() && !$task->get_run_if_component_disabled()) {
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
     * Displays a warning on the page if cron is disabled.
     *
     * @return string HTML code for information about cron being disabled
     * @throws moodle_exception
     */
    public function cron_disabled(): string {
        return $this->output->notification(get_string('crondisabled', 'tool_task'), 'warning');
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
