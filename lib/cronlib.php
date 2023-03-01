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
 * Cron functions.
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute cron tasks
 *
 * @param int|null $keepalive The keepalive time for this cron run.
 * @deprecated since 4.2 Use \core\cron::run_main_process() instead.
 */
function cron_run(?int $keepalive = null): void {
    debugging(
        'The cron_run() function is deprecated. Please use \core\cron::run_main_process() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_main_process($keepalive);
}

/**
 * Execute all queued scheduled tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @deprecated since 4.2 Use \core\cron::run_scheduled_tasks() instead.
 */
function cron_run_scheduled_tasks(int $timenow) {
    debugging(
        'The cron_run_scheduled_tasks() function is deprecated. Please use \core\cron::run_scheduled_tasks() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_scheduled_tasks($timenow);
}

/**
 * Execute all queued adhoc tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @param   int     $keepalive Keep this function alive for N seconds and poll for new adhoc tasks.
 * @param   bool    $checklimits Should we check limits?
 * @deprecated since 4.2 Use \core\cron::run_adhoc_tasks() instead.
 */
function cron_run_adhoc_tasks(int $timenow, $keepalive = 0, $checklimits = true) {
    debugging(
        'The cron_run_adhoc_tasks() function is deprecated. Please use \core\cron::run_adhoc_tasks() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_adhoc_tasks($timenow, $keepalive, $checklimits);
}

/**
 * Shared code that handles running of a single scheduled task within the cron.
 *
 * Not intended for calling directly outside of this library!
 *
 * @param \core\task\task_base $task
 * @deprecated since 4.2 Use \core\cron::run_inner_scheduled_task() instead.
 */
function cron_run_inner_scheduled_task(\core\task\task_base $task) {
    debugging(
        'The cron_run_inner_scheduled_task() function is deprecated. Please use \core\cron::run_inner_scheduled_task() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_inner_scheduled_task($task);
}

/**
 * Shared code that handles running of a single adhoc task within the cron.
 *
 * @param \core\task\adhoc_task $task
 * @deprecated since 4.2 Use \core\cron::run_inner_adhoc_task() instead.
 */
function cron_run_inner_adhoc_task(\core\task\adhoc_task $task) {
    debugging(
        'The cron_run_inner_adhoc_task() function is deprecated. Please use \core\cron::run_inner_adhoc_task() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_inner_adhoc_task($task);
}

/**
 * Sets the process title
 *
 * This makes it very easy for a sysadmin to immediately see what task
 * a cron process is running at any given moment.
 *
 * @param string $title process status title
 * @deprecated since 4.2 Use \core\cron::set_process_title() instead.
 */
function cron_set_process_title(string $title) {
    debugging(
        'The cron_set_process_title() function is deprecated. Please use \core\cron::set_process_title() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::set_process_title($title);
}

/**
 * Output some standard information during cron runs. Specifically current time
 * and memory usage. This method also does gc_collect_cycles() (before displaying
 * memory usage) to try to help PHP manage memory better.
 *
 * @deprecated since 4.2 Use \core\cron::trace_time_and_memory() instead.
 */
function cron_trace_time_and_memory() {
    debugging(
        'The cron_trace_time_and_memory() function is deprecated. Please use \core\cron::trace_time_and_memory() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::trace_time_and_memory();
}

/**
 * Prepare the output renderer for the cron run.
 *
 * This involves creating a new $PAGE, and $OUTPUT fresh for each task and prevents any one task from influencing
 * any other.
 *
 * @param   bool    $restore Whether to restore the original PAGE and OUTPUT
 * @deprecated since 4.2 Use \core\cron::prepare_core_renderer() instead.
 */
function cron_prepare_core_renderer($restore = false) {
    debugging(
        'The cron_prepare_core_renderer() function is deprecated. Please use \core\cron::prepare_core_renderer() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::prepare_core_renderer($restore);
}
