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

namespace tool_task\check;

use core\check\check;
use core\check\result;
use core\task\manager;

/**
 * Long running tasks check
 *
 * @package    tool_task
 * @author     Qihui Chan (qihuichan@catalyst-au.net)
 * @copyright  2022 Catalyst IT Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class longrunningtasks extends check {

    /**
     * Links to the running task list
     *
     * @return \action_link|null
     * @throws \coding_exception
     */
    public function get_action_link(): ?\action_link {
        $url = new \moodle_url('/admin/tool/task/runningtasks.php');
        return new \action_link($url, get_string('runningtasks', 'tool_task'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG;
        $status = result::OK;
        $slowtasks = 0;
        $details = '';
        $maxruntime = 0;
        $runtime = 0;

        $tasks = \core\task\manager::get_running_tasks();
        foreach ($tasks as $record) {
            $taskmethod = "{$record->type}_task_from_record";
            $task = manager::$taskmethod($record);
            $taskname = $task->get_name();

            $result = $task->get_runtime_result();
            $taskstatus = $result->get_status();
            $runtime = $task->get_runtime();
            $runtimedetails = get_string('taskrunningtime', 'tool_task', format_time($runtime));
            $maxruntime = ($runtime > $maxruntime) ? $runtime : $maxruntime;

            if ($taskstatus == result::OK) {
                continue;
            }

            $slowtasks++;
            $details .= strtoupper($taskstatus) . ": {$taskname}. {$runtimedetails} <br>";

            // The overall check status is the worst tasks status.
            if ($status !== result::ERROR) {
                $status = $taskstatus;
            }
        }

        $summary = get_string('checklongrunningtaskcount', 'tool_task', $slowtasks);
        $conclusion = get_string('taskdetails', 'tool_task', ['count' => $slowtasks,
            'time' => format_time($CFG->taskruntimewarn), 'maxtime' => format_time($maxruntime)]);

        $details = ($slowtasks ? $conclusion : $summary) . "<br>{$details}";

        return new result($status, $summary, $details);
    }
}
