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

use action_link;
use core\check\check;
use core\check\result;
use moodle_url;

/**
 * Ad hoc queue checks
 *
 * @package    tool_task
 * @copyright  2020 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhocqueue extends check {

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $DB, $CFG;

        $stats = $DB->get_record_sql('
            SELECT count(*) cnt,
                   MAX(? - nextruntime) age
              FROM {task_adhoc}', [time()]);

        $status = result::OK;
        $summary = get_string('adhocempty', 'tool_task');
        $details = '';

        if ($stats->cnt > 0) {
            // A large queue size by itself is not an issue, only when tasks
            // are not being processed in a timely fashion is it an issue.
            $status = result::INFO;
            $summary = get_string('adhocqueuesize', 'tool_task', $stats->cnt);
        }

        $max = $CFG->adhoctaskagewarn ?? 10 * MINSECS;
        if ($stats->age > $max) {
            $status = result::WARNING;
            $summary = get_string('adhocqueueold', 'tool_task', [
                'age' => format_time($stats->age),
                'max' => format_time($max),
            ]);
        }

        $max = $CFG->adhoctaskageerror ?? 4 * HOURSECS;
        if ($stats->age > $max) {
            $status = result::ERROR;
            $summary = get_string('adhocqueueold', 'tool_task', [
                'age' => format_time($stats->age),
                'max' => format_time($max),
            ]);
        }

        return new result($status, $summary, $details);
    }

    /**
     * Link to the Ad hoc tasks report
     *
     * @return action_link|null
     */
    public function get_action_link(): ?action_link {
        return new action_link(
            new moodle_url('/admin/tool/task/adhoctasks.php'),
            get_string('adhoctasks', 'tool_task'),
        );
    }
}
