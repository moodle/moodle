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
 * Handles external (web service) function calls related to search.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_user\external\user_summary_exporter;

/**
 * Handles external (web service) function calls related to search.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends \core_external\external_api {
    /**
     * Returns parameter types for get_relevant_users function.
     *
     * @return external_function_parameters Parameters
     */
    public static function get_relevant_users_parameters() {
        return new external_function_parameters([
            'query' => new external_value(
                PARAM_RAW,
                'Query string (full or partial user full name or other details)'
            ),
            'courseid' => new external_value(PARAM_INT, 'Course id (0 if none)'),
        ]);
    }

    /**
     * Returns result type for get_relevant_users function.
     *
     * @return external_description Result type
     */
    public static function get_relevant_users_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'User id'),
                'fullname' => new external_value(PARAM_RAW, 'Full name as text'),
                'profileimageurlsmall' => new external_value(PARAM_URL, 'URL to small profile image')
            ])
        );
    }

    /**
     * Searches for users given a query, taking into account the current user's permissions and
     * possibly a course to check within.
     *
     * @param string $query Query text
     * @param int $courseid Course id or 0 if no restriction
     * @return array Defined return structure
     */
    public static function get_relevant_users($query, $courseid) {
        global $CFG, $PAGE;

        // Validate parameter.
        [
            'query' => $query,
            'courseid' => $courseid,
        ] = self::validate_parameters(self::get_relevant_users_parameters(), [
            'query' => $query,
            'courseid' => $courseid,
        ]);

        // Validate the context (search page is always system context).
        $systemcontext = \context_system::instance();
        self::validate_context($systemcontext);

        // Get course object too.
        if ($courseid) {
            $coursecontext = \context_course::instance($courseid);
        } else {
            $coursecontext = null;
        }

        // If not logged in, can't see anyone when forceloginforprofiles is on.
        if (!empty($CFG->forceloginforprofiles)) {
            if (!isloggedin() || isguestuser()) {
                return [];
            }
        }

        $users = \core_user::search($query, $coursecontext);

        $result = [];
        foreach ($users as $user) {
            // Get a standard exported user object.
            $fulldetails = (new user_summary_exporter($user))->export($PAGE->get_renderer('core'));

            // To avoid leaking private data to students, only include the specific information we
            // are going to display (and not the email, idnumber, etc).
            $result[] = (object)['id' => $fulldetails->id, 'fullname' => $fulldetails->fullname,
                    'profileimageurlsmall' => $fulldetails->profileimageurlsmall];
        }
        return $result;
    }
}
