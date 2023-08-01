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

namespace mod_lti\local;

use core\context\course;

/**
 * Helper class specifically dealing with LTI types (preconfigured tools).
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class types_helper {

    /**
     * Returns all LTI tool types (preconfigured tools) visible in the given course and for the given user.
     *
     * This list will contain both site level tools and course-level tools.
     *
     * @param int $courseid the id of the course.
     * @param int $userid the id of the user.
     * @param array $coursevisible options for 'coursevisible' field, which will default to
     *        [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER] if omitted.
     * @return \stdClass[] the array of tool type objects.
     */
    public static function get_lti_types_by_course(int $courseid, int $userid, array $coursevisible = []): array {
        global $DB, $SITE;

        if (!has_capability('mod/lti:addpreconfiguredinstance', course::instance($courseid), $userid)) {
            return [];
        }

        if (empty($coursevisible)) {
            $coursevisible = [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER];
        }
        list($coursevisiblesql, $coursevisparams) = $DB->get_in_or_equal($coursevisible, SQL_PARAMS_NAMED, 'coursevisible');

        $coursecond = implode(" OR ", ["t.course = :courseid", "t.course = :siteid"]);
        $coursecategory = $DB->get_field('course', 'category', ['id' => $courseid]);
        $query = "SELECT t.*
                FROM {lti_types} t
           LEFT JOIN {lti_types_categories} tc ON t.id = tc.typeid
               WHERE t.coursevisible $coursevisiblesql
                 AND ($coursecond)
                 AND t.state = :active
                 AND (tc.id IS NULL OR tc.categoryid = :categoryid)
            ORDER BY t.name ASC";

        return $DB->get_records_sql($query,
            [
                'siteid' => $SITE->id,
                'courseid' => $courseid,
                'active' => LTI_TOOL_STATE_CONFIGURED,
                'categoryid' => $coursecategory
            ] + $coursevisparams);
    }
}
