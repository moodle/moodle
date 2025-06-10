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
 * File updates web service class definition.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally\webservice;

use tool_ally\local;
use tool_ally\local_file;

/**
 * File updates web service class definition.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_updates extends loggable_external_api {

    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'since' => new \external_value(PARAM_TEXT, 'ISO 8601 timestamp from which to get updates since'),
        ]);
    }

    /**
     * @return \external_multiple_structure
     */
    public static function service_returns() {
        return new \external_multiple_structure(
            new \external_single_structure([
                'entity_id'    => new \external_value(PARAM_ALPHANUM, 'File path name SHA1 hash'),
                'context_id'   => new \external_value(PARAM_INT, 'ID of the context of the file'),
                'event_name'   => new \external_value(PARAM_ALPHAEXT, 'Name of the event'),
                'event_time'   => new \external_value(PARAM_TEXT, 'ISO8601 timestamp for the event'),
                'mime_type'    => new \external_value(PARAM_RAW, 'File mime type'),
                'content_hash' => new \external_value(PARAM_ALPHANUM, 'File content SHA1 hash'),
            ])
        );
    }

    /**
     * @param string $since
     * @return array
     */
    public static function execute_service($since) {

        $params = self::validate_parameters(self::service_parameters(), ['since' => $since]);

        self::validate_context(\context_system::instance());
        require_capability('moodle/course:view', \context_system::instance());
        require_capability('moodle/course:viewhiddencourses', \context_system::instance());

        // We are betting that most courses have files, so better to preload than to fetch one at a time.
        local::preload_course_contexts();

        $files  = local_file::iterator()->since(local::iso_8601_to_timestamp($params['since']));
        $return = [];
        foreach ($files as $file) {
            $return[] = local_file::to_crud($file);
        }

        return $return;
    }
}
