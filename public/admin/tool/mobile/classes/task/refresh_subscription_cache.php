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
 * Moodle Mobile tools refresh subscription cache task definitions.
 *
 * @package    tool_mobile
 * @copyright  2026 Daniel Urena
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mobile\task;

use tool_mobile\api;

/**
 * Scheduled task to refresh Moodle app subscription information cache.
 *
 * @package     tool_mobile
 * @copyright   2026
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_subscription_cache extends \core\task\scheduled_task {
    /**
     * Return the task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskrefreshsubscriptioncache', 'tool_mobile');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;

        mtrace('tool_mobile: Running scheduled subscription cache refresh task...');
        if (empty($CFG->enablemobilewebservice)) {
            mtrace('tool_mobile: task not running, mobile app is not enabled.');
            return;
        }

        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cachedata = $cache->get(0);
        if (is_array($cachedata) && isset($cachedata['subscription']['plan'])) {
            mtrace('tool_mobile: previous cache plan: ' . $cachedata['subscription']['plan'] . '.');
        } else {
            mtrace('tool_mobile: previous cache plan: unknown.');
        }

        $errormessage = '';
        $data = api::get_subscription_information(false, true, 10, $errormessage);
        if ($data === null) {
            mtrace('tool_mobile: subscription cache refresh failed.');
            if (!empty($errormessage)) {
                mtrace('tool_mobile: ' . $errormessage);
            }
        } else if (!empty($errormessage)) {
            mtrace('tool_mobile: ' . $errormessage);
            mtrace('tool_mobile: scheduled subscription cache refresh failed, serving previously cached data.');
        } else {
            mtrace(
                'tool_mobile: scheduled subscription cache refreshed. ' .
                'Plan: ' . ($data['subscription']['plan'] ?? 'unknown')
            );
            mtrace('tool_mobile: scheduled subscription cache refresh completed successfully.');
        }
    }
}
