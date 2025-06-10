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
 * Get list of rich content items.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\local;
use tool_ally\local_content;

/**
 * Get list of rich content items.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_content extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'ids' => new \external_multiple_structure(new \external_value(PARAM_INT, 'Course id'), 'List of course IDs')
        ]);
    }

    /**
     * @return \external_multiple_structure
     */
    public static function service_returns() {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id'           => new \external_value(PARAM_INT, 'Component id'),
                'component'    => new \external_value(PARAM_ALPHANUMEXT, 'Component name'),
                'title'        => new \external_value(PARAM_TEXT, 'Title'),
                'table'        => new \external_value(PARAM_ALPHANUMEXT,
                        'Where content not in main component table - e.g: forum_discussions, forum_posts, etc'),
                'field'        => new \external_value(PARAM_ALPHANUMEXT,
                        'Table field for storing content - e.g: description, message, etc'),
                'courseid'     => new \external_value(PARAM_INT, 'Course ID of course housing content'),
                'timemodified' => new \external_value(PARAM_TEXT, 'Last modified time of the content')
            ])
        );
    }

    /**
     * @param array $ids List of course IDs
     * @return array
     */
    public static function execute_service($ids) {
        $params = self::validate_parameters(self::service_parameters(), ['ids' => $ids]);

        self::validate_context(\context_system::instance());
        require_capability('moodle/course:view', \context_system::instance());
        require_capability('moodle/course:viewhiddencourses', \context_system::instance());

        // We are betting that most courses have content, so better to preload than to fetch one at a time.
        local::preload_course_contexts($ids);

        $return = array();

        $components = local_content::list_html_content_supported_components();

        foreach ($components as $component) {
            foreach ($params['ids'] as $id) {
                $return = array_merge($return, local_content::get_course_html_content_items($component, $id));
            }
        }

        return $return;
    }
}
