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

namespace enrol_lti\local\ltiadvantage\utility;

/**
 * Utility class for LTI Advantage messages.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class message_helper {

    /**
     * Determine if the LTI roles in the launch contains any instructor or admin roles.
     *
     * @param array $jwtdata array formatted JWT data from the launch.
     * @return bool true if the roles contain a constructor role, false otherwise.
     */
    public static function is_instructor_launch(array $jwtdata): bool {
        return self::user_is_admin($jwtdata) || self::user_is_staff($jwtdata, true);
    }

    /**
     * Check whether the launch user is an instructor.
     *
     * @param array $jwtdata array formatted JWT data from the launch.
     * @param bool $includelegacyroles whether to also consider legacy simple names as valid roles.
     * @return bool true if the user is an instructor, false otherwise.
     */
    private static function user_is_staff(array $jwtdata, bool $includelegacyroles = false): bool {
        // See: http://www.imsglobal.org/spec/lti/v1p3/#role-vocabularies.
        // This method also provides support for (legacy, deprecated) simple names for context roles.
        // I.e. 'ContentDeveloper' may be supported.
        $launchroles = $jwtdata['https://purl.imsglobal.org/spec/lti/claim/roles'] ?? null;
        if ($launchroles) {
            $staffroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper',
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistant'
            ];

            if ($includelegacyroles) {
                $staffroles[] = 'ContentDeveloper';
                $staffroles[] = 'Instructor';
                $staffroles[] = 'Instructor#TeachingAssistant';
            }

            foreach ($staffroles as $validrole) {
                if (in_array($validrole, $launchroles)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check whether the launch user has an admin role.
     *
     * @param array $jwtdata array formatted JWT data from the launch.
     * @return bool true if the user is admin, false otherwise.
     */
    private static function user_is_admin(array $jwtdata): bool {
        // See: http://www.imsglobal.org/spec/lti/v1p3/#role-vocabularies.
        $launchroles = $jwtdata['https://purl.imsglobal.org/spec/lti/claim/roles'] ?? null;
        if ($launchroles) {
            $adminroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator',
                'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator'
            ];

            foreach ($adminroles as $validrole) {
                if (in_array($validrole, $launchroles)) {
                    return true;
                }
            }
        }
        return false;
    }
}
