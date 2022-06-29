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
 * Task fail delay check
 *
 * @package    tool_task
 * @copyright  2020 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_task\check;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Task fail delay check
 *
 * @package    tool_task
 * @copyright  2020 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maxfaildelay extends check {

    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        $this->id = 'cronfaildelay';
        $this->name = get_string('checkmaxfaildelay', 'tool_task');
        $this->actionlink = new \action_link(
            new \moodle_url('/admin/tool/task/scheduledtasks.php'),
            get_string('scheduledtasks', 'tool_task'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result() : result {
        global $CFG;

        $status = result::OK;
        $summary = get_string('tasknofailures', 'tool_task');
        $details = '';
        $failures = 0;
        $maxdelay = 0;

        $tasks = \core\task\manager::get_all_scheduled_tasks();
        foreach ($tasks as $task) {
            if ($task->get_disabled()) {
                continue;
            }
            $faildelay = $task->get_fail_delay();
            if ($faildelay > $maxdelay) {
                $maxdelay = $faildelay;
            }
            if ($faildelay > 0) {
                $failures++;
                $details .= get_string('faildelay', 'tool_task') . ': ' . format_time($faildelay);
                $details .= ' - ' . $task->get_name() . ' (' .get_class($task) . ")<br>";
            }
        }

        $tasks = \core\task\manager::get_failed_adhoc_tasks();
        foreach ($tasks as $task) {
            $faildelay = $task->get_fail_delay();
            if ($faildelay > $maxdelay) {
                $maxdelay = $faildelay;
            }
            if ($faildelay > 0) {
                $failures++;
                $details .= get_string('faildelay', 'tool_task') . ': ' . format_time($faildelay);
                $details .= ' - ' .get_class($task) . " ID = " . $task->get_id() ."<br>";
            }
        }

        if ($failures > 0) {
            // Intermittent failures are not yet a warning.
            $status = result::INFO;
            $summary = get_string('taskfailures', 'tool_task', $failures);
        }
        if ($maxdelay > 5 * MINSECS) {
            $status = result::WARNING;
        }
        if ($maxdelay > 4 * HOURSECS) {
            $status = result::ERROR;
        }

        return new result($status, $summary, $details);
    }
}
