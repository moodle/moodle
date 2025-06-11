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
 * Get a list of courses where the current user is a teacher.
 *
 * @package local_o365
 * @author 2011 Jerome Mouneyrac, modified 2016 James McQuillan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2011 Jerome Mouneyrac
 */

namespace local_o365\webservices;

use context_course;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

global $CFG;

require_once($CFG->dirroot . '/course/modlib.php');

/**
 * Get a list of courses where the current user is a teacher.
 */
class read_teachercourses extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function teachercourses_read_parameters() {
        return new external_function_parameters([
            'courseids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'course id, empty to retrieve all courses'),
                '0 or more course ids',
                VALUE_DEFAULT,
                []
            ),
        ]);
    }

    /**
     * Get list of courses user is enrolled in (only active enrolments are returned).
     * Please note the current user must be able to access the course, otherwise the course is not included.
     *
     * @param array $courseids
     * @return array of courses
     */
    public static function teachercourses_read($courseids = []) {
        global $USER;

        // Validate params.
        $params = self::validate_parameters(
            self::teachercourses_read_parameters(),
            [
                'courseids' => $courseids,
            ]
        );

        $courseids = (!empty($params['courseids']) && is_array($params['courseids'])) ? array_flip($params['courseids']) : [];

        // Get courses.
        $fields = 'id, shortname, fullname, idnumber, visible, format, showgrades, lang, enablecompletion';
        $courses = enrol_get_users_courses($USER->id, true, $fields);

        $result = [];

        foreach ($courses as $course) {
            if (!empty($courseids) && !isset($courseids[$course->id])) {
                continue;
            }

            $context = context_course::instance($course->id, IGNORE_MISSING);

            // Validate the user can execute functions in this course.
            try {
                static::validate_context($context);
            } catch (moodle_exception $e) {
                continue;
            }

            // We'll use the grade:edit capability to define "teacher".
            if (!has_capability('moodle/grade:edit', $context)) {
                continue;
            }

            $result[] = [
                'id' => $course->id,
                'shortname' => $course->shortname,
                'fullname' => $course->fullname,
                'idnumber' => $course->idnumber,
                'visible' => $course->visible,
                'format' => $course->format,
                'showgrades' => $course->showgrades,
                'lang' => $course->lang,
                'enablecompletion' => $course->enablecompletion,
            ];
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_multiple_structure
     */
    public static function teachercourses_read_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname' => new external_value(PARAM_RAW, 'long name of course'),
                    'idnumber' => new external_value(PARAM_RAW, 'id number of course'),
                    'visible' => new external_value(PARAM_INT, '1 means visible, 0 means hidden course'),
                    'format' => new external_value(PARAM_PLUGIN, 'course format: weeks, topics, social, site', VALUE_OPTIONAL),
                    'showgrades' => new external_value(PARAM_BOOL, 'true if grades are shown, otherwise false', VALUE_OPTIONAL),
                    'lang' => new external_value(PARAM_LANG, 'forced course language', VALUE_OPTIONAL),
                    'enablecompletion' => new external_value(PARAM_BOOL, 'true if completion is enabled, otherwise false',
                        VALUE_OPTIONAL),
                ]
            )
        );
    }
}
