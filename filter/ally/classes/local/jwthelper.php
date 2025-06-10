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
 * JSON Web Token helpers.
 *
 * @package   filter_ally
 * @copyright Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_ally\local;

/**
 * Class jwthelper
 * @package filter_ally
 */
class jwthelper {
    const ALGO    = 'HS256';

    /**
     * Returns generated JSON Web Token according to the RFC 7519 (https://tools.ietf.org/html/rfc7519)
     * Gets the required settings from the tool_ally lti setup.
     *
     * @param mixed    $user      User object or user id
     * @param int      $courseid  The course id of the current course
     * @return bool|string
     */
    public static function get_token($user, $courseid) {
        global $CFG;

        $secret = get_config('tool_ally', 'secret');
        $token = false;

        if (!empty($secret)) {
            $payload = [];

            // Issued at <time>.
            $payload['iat'] = (int)time();

            $payload['course_id'] = (int)$courseid;

            $payload['roles'] = self::lti_get_ims_role($user, $courseid);

            if (!isloggedin() || isguestuser($user)) {
                $payload['user_id'] = null;
            } else {
                $payload['user_id'] = (int)$user->id;
            }

            $payload['locale'] = current_language();

            $payload['return_url'] = $CFG->wwwroot;

            if (!class_exists('\Firebase\JWT\JWT')) {
                /* @noinspection PhpIncludeInspection */
                require_once($CFG->dirroot.'/filter/ally/vendor/autoload.php');
            }

            try {
                /* @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                $token = \Firebase\JWT\JWT::encode($payload, $secret, self::ALGO);
            } catch (\Exception $e) {
                debugging('Cannot encode JWT: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        return $token;
    }

    /**
     * Gets the IMS role string for the specified user and course
     *
     * @param mixed    $user      User object or user id
     * @param int      $courseid  The course id of the LTI activity
     *
     * @return string A role string suitable for passing with an LTI launch
     */
    private static function lti_get_ims_role($user, $courseid) {
        $roles = array();

        $coursecontext = \context_course::instance($courseid);

        if (!isloggedin() || isguestuser($user)) {
            array_push($roles, 'urn:lti:sysrole:ims/lis/None');
        } else if (has_capability('moodle/course:manageactivities', $coursecontext, $user)) {
            array_push($roles, 'urn:lti:role:ims/lis/Instructor');
        } else {
            array_push($roles, 'urn:lti:role:ims/lis/Learner');
        }

        if (is_siteadmin($user)) {
            array_push($roles, 'urn:lti:role:ims/lis/Administrator');
        }

        return join(',', $roles);
    }
}
