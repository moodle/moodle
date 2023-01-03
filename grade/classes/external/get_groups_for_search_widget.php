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

namespace core_grades\external;

use coding_exception;
use context_course;
use external_api;
use external_description;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use invalid_parameter_exception;
use moodle_exception;
use restricted_context_exception;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/externallib.php');

/**
 * External group report API implementation
 *
 * @package    core_grades
 * @copyright  2022 Mathew May <mathew.solutions>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_groups_for_search_widget extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'actionbaseurl' => new external_value(PARAM_URL, 'The base URL for the group action', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a course ID find the existing user groups and map some fields to the returned array of group objects.
     *
     * @param int $courseid
     * @param string $actionbaseurl The base URL for the group action.
     * @return array Groups and warnings to pass back to the calling widget.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    protected static function execute(int $courseid, string $actionbaseurl): array {
        global $DB, $USER, $COURSE;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'actionbaseurl' => $actionbaseurl
            ]
        );

        $warnings = [];
        $context = context_course::instance($params['courseid']);
        parent::validate_context($context);

        $mappedgroups = [];
        $course = $DB->get_record('course', ['id' => $params['courseid']]);
        // Initialise the grade tracking object.
        if ($groupmode = $course->groupmode) {
            $aag = has_capability('moodle/site:accessallgroups', $context);

            $usergroups = [];
            $groupuserid = 0;
            if ($groupmode == VISIBLEGROUPS || $aag) {
                // Get user's own groups and put to the top.
                $usergroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
            } else {
                $groupuserid = $USER->id;
            }
            $allowedgroups = groups_get_all_groups($course->id, $groupuserid, $course->defaultgroupingid);

            $allgroups = array_merge($allowedgroups, $usergroups);
            // Filter out any duplicate groups.
            $groupsmenu = array_intersect_key($allgroups, array_unique(array_column($allgroups, 'name')));

            if (!$allowedgroups || $groupmode == VISIBLEGROUPS || $aag) {
                array_unshift($groupsmenu, (object) [
                    'id' => 0,
                    'name' => get_string('allparticipants'),
                ]);
            }

            $mappedgroups = array_map(function($group) use ($COURSE, $actionbaseurl, $context) {
                $url = new \moodle_url($actionbaseurl, [
                    'id' => $COURSE->id,
                    'group' => $group->id
                ]);
                return (object) [
                    'id' => $group->id,
                    'name' => format_string($group->name, true, ['context' => $context]),
                    'url' => $url->out(false),
                    'active' => false // @TODO MDL-76246
                ];
            }, $groupsmenu);
        }

        return [
            'groups' => $mappedgroups,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of what the group search for the widget should return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'groups' => new external_multiple_structure(self::group_description()),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Create group return value description.
     *
     * @return external_description
     */
    public static function group_description(): external_description {
        $groupfields = [
            'id' => new external_value(PARAM_ALPHANUM, 'An ID for the group', VALUE_REQUIRED),
            'url' => new external_value(PARAM_URL, 'The link that applies the group action', VALUE_REQUIRED),
            'name' => new external_value(PARAM_TEXT, 'The full name of the group', VALUE_REQUIRED),
            'active' => new external_value(PARAM_BOOL, 'Are we currently on this item?', VALUE_REQUIRED)
        ];
        return new external_single_structure($groupfields);
    }
}
