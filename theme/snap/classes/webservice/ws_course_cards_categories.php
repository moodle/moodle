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
 * course cards service.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\webservice;

defined('MOODLE_INTERNAL') || die();

use context;
use context_course;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use theme_snap\renderables\course_card;
use theme_snap\services\course;
// BEGIN LSU Extra Course Tabs.
use theme_snap\renderables\remote_card;
// END LSU Extra Course Tabs.

/**
 * Feed service.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_course_cards_categories extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'page' => new external_value(PARAM_TEXT, 'Page', VALUE_DEFAULT, 0),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_multiple_structure
     */
    public static function service_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'category' => new external_value(PARAM_ALPHANUM, 'Category', VALUE_REQUIRED),
                'courses' => new external_value(PARAM_INT, 'Number of courses for the respective category', VALUE_REQUIRED),
            ])
        );
    }

    /**
     * @param null|int $page
     * @return array
     */
    public static function service() {
        global $DB, $USER, $CFG;

        $wheres = array("c.id <> :siteid");
        $params = array('siteid' => SITEID);

        if (isset($USER->loginascontext) && $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            $wheres[] = "courseid = :loginas";
            $params['loginas'] = $USER->loginascontext->instanceid;
        }

        $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_COURSE;
        $wheres = implode(" AND ", $wheres);

        $timeaccessjoin = "";

        if (!empty($courseids)) {
            list($courseidssql, $courseidsparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $wheres = sprintf("%s AND c.id %s", $wheres, $courseidssql);
            $params = array_merge($params, $courseidsparams);
        }

        if (!empty($excludecourses)) {
            list($courseidssql, $courseidsparams) = $DB->get_in_or_equal($excludecourses, SQL_PARAMS_NAMED, 'param', false);
            $wheres = sprintf("%s AND c.id %s", $wheres, $courseidssql);
            $params = array_merge($params, $courseidsparams);
        }

        $courseidsql = "";
        // Logged-in, non-guest users get their enrolled courses.
        if (!isguestuser() && isloggedin()) {
            $courseidsql .= "
                SELECT DISTINCT e.courseid
                FROM {enrol} e
                JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid1)
                WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1
                AND (ue.timeend = 0 OR ue.timeend > :now2)";
            $params['userid1'] = $USER->id;
            $params['active'] = ENROL_USER_ACTIVE;
            $params['enabled'] = ENROL_INSTANCE_ENABLED;
            $params['now1'] = round(time(), -2); // Improves db caching.
            $params['now2'] = $params['now1'];
        }

        if (is_siteadmin()) {
            // Site admins can access all courses.
            $courseidsql = "SELECT DISTINCT c2.id AS courseid FROM {course} c2";
        } else {
            // If we used the enrolment as well, then this will be UNIONed.
            if ($courseidsql) {
                $courseidsql .= " UNION ";
            }

            // Include courses with guest access and no password.
            $courseidsql .= "
                SELECT DISTINCT e.courseid
                FROM {enrol} e
                WHERE e.enrol = 'guest' AND e.password = :emptypass AND e.status = :enabled2";
            $params['emptypass'] = '';
            $params['enabled2'] = ENROL_INSTANCE_ENABLED;

            // Include courses where the current user is currently using guest access (may include
            // those which require a password).
            $courseids = [];
            $accessdata = get_user_accessdata($USER->id);
            foreach ($accessdata['ra'] as $contextpath => $roles) {
                if (array_key_exists($CFG->guestroleid, $roles)) {
                    // Work out the course id from context path.
                    $context = context::instance_by_id(preg_replace('~^.*/~', '', $contextpath));
                    if ($context instanceof context_course) {
                        $courseids[$context->instanceid] = true;
                    }
                }
            }

            // Include courses where the current user has moodle/course:view capability.
            $courses = get_user_capability_course('moodle/course:view', null, false);
            if (!$courses) {
                $courses = [];
            }
            foreach ($courses as $course) {
                $courseids[$course->id] = true;
            }

            // If there are any in either category, list them individually.
            if ($courseids) {
                list ($allowedsql, $allowedparams) = $DB->get_in_or_equal(
                    array_keys($courseids), SQL_PARAMS_NAMED);
                $courseidsql .= "
                    UNION
                   SELECT DISTINCT c3.id AS courseid
                    FROM {course} c3
                    WHERE c3.id $allowedsql";
                $params = array_merge($params, $allowedparams);
            }
        }
        if ($DB->get_dbfamily() === 'postgres') {
            $yearfield = 'EXTRACT(YEAR FROM TO_TIMESTAMP(c.enddate)) AS cat_year';
        } else {
            $yearfield = 'YEAR(FROM_UNIXTIME(c.enddate)) AS cat_year';
        }

        $sql = "SELECT DISTINCT $yearfield , COUNT(*) AS courses, MIN(c.enddate) AS stamp
              FROM {course} c
              JOIN ($courseidsql) en ON (en.courseid = c.id)
              $timeaccessjoin
              $ccjoin
              WHERE $wheres
              GROUP BY cat_year";

        $courses = $DB->get_records_sql($sql, $params);

        $categories = [];
        foreach ($courses as $course) {
            if ((int)$course->stamp != 0) {
                $categories[] = (object)[
                    'category' => (string)$course->cat_year,
                    'courses' => $course->courses,
                ];
            } else {
                $categories[] = (object)[
                    'category' => 'current',
                    'courses' => $course->courses,
                ];
            }
        }

        return $categories;
    }
}
