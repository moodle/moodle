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
 * Log cleanup task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\task;

use core\task\scheduled_task;

/**
 * Log cleanup task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_log_task extends scheduled_task {
    public function get_name() {
        return get_string('logcleanuptask', 'tool_ally');
    }

    public function execute() {
        global $DB;
        $logdays = get_config('tool_ally', 'loglifetimedays');

        // Empty or less than 1 is disabled.
        if (empty($logdays) || $logdays < 1) {
            return;
        }

        // It would be odd if this doesn't exist, but checking in case.
        if (!$DB->get_manager()->table_exists('tool_ally_log')) {
            return;
        }

        $reftime = time() - ($logdays * DAYSECS);

        $records = $DB->delete_records_select('tool_ally_log', 'time < ?', [$reftime]);
    }

}
