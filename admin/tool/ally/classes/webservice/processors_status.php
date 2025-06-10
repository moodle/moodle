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
 * Gets the queue status.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\push_config;

/**
 * Gets the queue status.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processors_status extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'is_valid' => new \external_value(PARAM_BOOL, 'Is the configuration valid?'),
            'is_cli_only' => new \external_value(PARAM_BOOL, 'is the processor set to push only?'),
            'when_cli_only_on' => new \external_value(PARAM_INT, 'When the cli went On'),
            'when_cli_only_off' => new \external_value(PARAM_INT, 'when the cli went Off'),
            'content_events' => new \external_value(PARAM_INT, 'Amount of content events in the queue'),
            'oldest_content_event' => new \external_value(PARAM_INT, 'timestamp for oldest content event queued'),
            'course_events' => new \external_value(PARAM_INT, 'Amount of course events in the queue'),
            'oldest_course_event' => new \external_value(PARAM_INT, 'timestamp for oldest course event queued')
        ]);
    }

    /**
     * @param int $id Course id.
     * @return array
     */
    public static function execute_service() {
        global $DB;

        $config = new push_config();
        $contentquery = $DB->get_record('tool_ally_content_queue', [], 'count(id) as amount, min(eventtime) as oldest');
        $contentqueue = self::cast($contentquery);
        $deletedquery = $DB->get_record('tool_ally_deleted_content', [], 'count(id) as amount, min(timedeleted) as oldest');
        $deletedqueue = self::cast($deletedquery);
        $coursequery = $DB->get_record('tool_ally_course_event', [], 'count(id) as amount, min(time) as oldest');
        $coursequeue = self::cast($coursequery);
        $oldestcontent = null;
        if ($contentqueue->oldest != null && $deletedqueue->oldest != null) {
            $oldestcontent = $contentqueue->oldest < $deletedqueue->oldest ? $contentqueue->oldest : $deletedqueue->oldest;
        } else if ($contentqueue->oldest == null && $deletedqueue->oldest != null) {
            $oldestcontent = $deletedqueue->oldest;
        } else if ($contentqueue->oldest != null && $deletedqueue->oldest == null) {
            $oldestcontent = $contentqueue->oldest;
        }
        return (object)[
            'is_valid' => $config->is_valid(),
            'is_cli_only' => $config->is_cli_only(),
            'when_cli_only_on' => (int) get_config('tool_ally', 'push_cli_only_on'),
            'when_cli_only_off' => (int) get_config('tool_ally', 'push_cli_only_off'),
            'content_events' => $contentqueue->amount + $deletedqueue->amount,
            'oldest_content_event' => $oldestcontent,
            'course_events' => $coursequeue->amount,
            'oldest_course_event' => $coursequeue->oldest,
        ];
    }
    public static function cast($queue) {
        $queue->amount = (int) $queue->amount;
        $queue->oldest = (int) $queue->oldest;
        return $queue;
    }
}
