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
 * Class intentshelper to store intents general functions to reduce code duplication.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

global $CFG;

define("INTENTDATEFORMAT", "d/m/Y"); // Date format used in bot messages.
define("INTENTTIMEFORMAT", "H:i"); // Time format used in bot messages.

require_once($CFG->libdir . '/accesslib.php');

/**
 * Class intentshelper to store intents general functions to reduce code duplication.
 */
class intentshelper {

    /**
     * Returns sql string and sql params to get students in courses (if no courses given then all users returned)
     * @param array $courses - array of courses ids which students to return
     * @param string $fields - fields of users table to return
     * @param string $where - custom where clause to add
     * @param array $whereparams - cutom where clause params
     * @param bool $limit - limit output to standard intents limit (true/false)
     * @return array - array containing sql string and sql params for DB manipulation functions
     */
    public function getcoursesstudentslistsql($courses = [], $fields = 'u.id', $where = '', $whereparams = [], $limit = false) {
        global $DB;
        $userssql = "SELECT $fields FROM {user} u ";
        $sqlparams = [];
        if (!empty($courses)) {
            list($coursessql, $coursesparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $userssql .= " JOIN {role_assignments} ra ON u.id = ra.userid
                           JOIN {role} r ON ra.roleid = r.id AND r.shortname = :studentstr
                           JOIN {context} c ON c.id = ra.contextid AND c.contextlevel = :contextcourse
                                AND c.instanceid $coursessql";
            $sqlparams['studentstr'] = 'student';
            $sqlparams['contextcourse'] = CONTEXT_COURSE;
            $sqlparams = array_merge($sqlparams, $coursesparams);
        }
        $userssql .= ' WHERE u.deleted = 0 AND u.suspended = 0';
        if (!empty($where)) {
            $userssql .= " AND $where";
            $sqlparams = array_merge($sqlparams, $whereparams);
        }
        $userssql .= ' ORDER BY u.lastaccess DESC';
        if ($limit) {
            $userssql .= ' LIMIT ' . \local_o365\bot\intents\intentinterface::DEFAULT_LIMIT_NUMBER;
        }
        return [$userssql, $sqlparams];
    }

    /**
     * Gets array of teacher courses.
     *
     * @param int $teacherid - teacher user id in Moodle
     * @return array - list of teacher courses ids
     */
    public static function getteachercourses($teacherid) {
        $courses = array_keys(enrol_get_users_courses($teacherid, true, 'id'));
        $teachercourses = [];
        foreach ($courses as $course) {
            $context = \context_course::instance($course, IGNORE_MISSING);
            if (!has_capability('moodle/grade:edit', $context)) {
                continue;
            }
            $teachercourses[] = $course;
        }
        return $teachercourses;
    }

    /**
     * Formats timestamp to default bot messages date format.
     *
     * @param int $timestamp - unix time stamp to be formatted
     * @param bool $time - should time be showed beside date (true/false)
     * @return false|string - formatted date
     */
    public function formatdate($timestamp, $time = false) {
        $format = ($time ? INTENTDATEFORMAT.' '.INTENTTIMEFORMAT : INTENTDATEFORMAT);
        return date($format, $timestamp);
    }
}
