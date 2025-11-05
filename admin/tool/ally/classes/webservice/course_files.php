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
 * Provide a list of course files to process for accessibility.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\local;
use tool_ally\local_course;
use tool_ally\local_file;

/**
 * Provide a list of course files to process for accessibility.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_files extends loggable_external_api {
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
                'id'           => new \external_value(PARAM_ALPHANUM, 'File path name SHA1 hash'),
                'courseid'     => new \external_value(PARAM_INT, 'Course ID of the file'),
                'name'         => new \external_value(PARAM_TEXT, 'File name'),
                'mimetype'     => new \external_value(PARAM_RAW, 'File mime type'),
                'contenthash'  => new \external_value(PARAM_ALPHANUM, 'File content SHA1 hash'),
                'timemodified' => new \external_value(PARAM_TEXT, 'Last modified time of the file'),
            ])
        );
    }

    /**
     * @param array $ids List of course IDs
     * @return array
     */
    public static function execute_service($ids) {
        $params      = self::validate_parameters(self::service_parameters(), ['ids' => $ids]);

        self::validate_context(\context_system::instance());
        require_capability('moodle/course:view', \context_system::instance());
        require_capability('moodle/course:viewhiddencourses', \context_system::instance());

        local::preload_course_contexts($params['ids']);

        $return = array();
        foreach ($params['ids'] as $id) {
            $context = \context_course::instance($id);
            $softdeltedcontexts = local_course::course_cm_soft_delete_contextids($id);
            $files = local_file::iterator();
            $files->in_context($context);

            foreach ($files as $file) {
                if (in_array($file->get_contextid(), $softdeltedcontexts)) {
                    // The module corresponding to this file has been soft-deleted.
                    continue;
                }
                $return[] = [
                    'id'           => $file->get_pathnamehash(),
                    'courseid'     => $context->instanceid,
                    'name'         => $file->get_filename(),
                    'mimetype'     => $file->get_mimetype(),
                    'contenthash'  => $file->get_contenthash(),
                    'timemodified' => local::iso_8601($file->get_timemodified()),
                ];
            }
        }

        return $return;
    }
}
