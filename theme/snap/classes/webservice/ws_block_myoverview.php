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
 * Course Overview block service for Snap.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\webservice;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/externallib.php');

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_course_external;
use core_course\external\course_summary_exporter;

class ws_block_myoverview extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function service_parameters() {
        return new external_function_parameters(
            array(
                'classification' => new external_value(PARAM_ALPHA, 'future, inprogress, or past'),
                'limit' => new external_value(PARAM_INT, 'Result set limit', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Result set offset', VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_TEXT, 'Sort string', VALUE_DEFAULT, null),
                'customfieldname' => new external_value(PARAM_ALPHANUMEXT, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
                'customfieldvalue' => new external_value(PARAM_RAW, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
                'searchvalue' => new external_value(PARAM_RAW, 'The value a user wishes to search against',
                    VALUE_DEFAULT, null),
                'yeardata' => new external_value(PARAM_TEXT, 'The courses end date year',
                    VALUE_DEFAULT, null),
                'progress' => new external_value(PARAM_TEXT, 'The courses completion progress',
                    VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(course_summary_exporter::get_read_structure(), 'Course'),
                'nextoffset' => new external_value(PARAM_INT, 'Offset for the next request')
            )
        );
    }

    /**

     */
    public static function service(
        string $classification,
        int $limit = 0,
        int $offset = 0,
        string $sort = null,
        string $customfieldname = null,
        string $customfieldvalue = null,
        string $searchvalue = null,
        string $yeardata = null,
        string $progress = null
    ) {

        $params = self::validate_parameters(self::service_parameters(),
            array(
                'classification' => $classification,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort,
                'customfieldname' => $customfieldname,
                'customfieldvalue' => $customfieldvalue,
                'searchvalue' => $searchvalue,
                'yeardata' => $yeardata,
                'progress' => $progress
            )
        );
        $mainFiltersResult = \core_course_external::get_enrolled_courses_by_timeline_classification(
            $classification,
            $limit,
            $offset,
            $sort,
            $customfieldname,
            $customfieldvalue,
            $searchvalue
        );


        // BEGIN LSU - need to make the webservice reflect the renderer.                
        if ($params['yeardata'] != null && $params['yeardata'] == "All years") {
            $yd = 'all';
        } else {
            $yd = $params['yeardata'];
        }

        if ($yd != "all") {
            $filteredbyyearcourses = [];
            foreach ($mainFiltersResult["courses"] as $course) {
                if (!empty($course->enddate)) {
                    $endyear = userdate($course->enddate, '%Y');
                } else {
                    // Just need the year, so if end isn't enabled use start.
                    $endyear = userdate($course->startdate, '%Y');
                }
                if ($endyear == $yd) {
                    $filteredbyyearcourses[] = $course;
                }
            }
            $mainFiltersResult["courses"] = $filteredbyyearcourses;
        }
        $params['progress'] = trim(strtolower($params['progress']));
        if ($params['progress'] != "all") {
            $filteredbycompleted = [];
            $filteredbynotcompleted = [];
            foreach ($mainFiltersResult["courses"] as $course) {
                if ($course->progress == 100) {
                    $filteredbycompleted[] = $course;
                } else {
                    $filteredbynotcompleted[] = $course;
                }
            }

            if ($params['progress'] == 'completed') {
                $mainFiltersResult["courses"] = $filteredbycompleted;
            } elseif ($params['progress'] == 'notcompleted' || $params['progress'] == 'not completed') {
                $mainFiltersResult["courses"] = $filteredbynotcompleted;
            }
        }
        // END LSU - need to make the webservice reflect the renderer.                
        return $mainFiltersResult;
    }
}
