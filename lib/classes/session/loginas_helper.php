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

namespace core\session;

use core\context;
use core\context\course as context_course;
use core\context\system as context_system;
use core\session\manager as sessionmanager;
use stdClass;

/**
 * Helper functions for the 'login as' feature.
 *
 * @package core
 * @author Jason den Dulk <jasondendulk@catalyst-au.net>
 * @copyright 2025 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loginas_helper {
    /**
     * Determine which context a user can login as another user for, given the requested target user and course. If the
     * person cannot login at all, then null will be returned.
     *
     * @param stdClass $currentuser The current user. Defaults to $USER.
     * @param stdClass $loginasuser The user to be logged in as.
     * @param stdClass|null $course The course currently being looked at. Applies to those who can only login within a
     *                            course context. If null, then only system context will be considered.
     * @return context|null Returns the context that the user is able to login as, or null if no context can be found.
     */
    public static function get_context_user_can_login_as(
        stdClass $currentuser,
        stdClass $loginasuser,
        ?stdClass $course = null
    ): ?context {
        $systemcontext = context_system::instance();

        if (
            $loginasuser->deleted || // Can't login as a user that has been removed.
            $currentuser->id == $loginasuser->id || // Pointless for a user to login as himself.
            sessionmanager::is_loggedinas() // Already logged in as someone.
        ) {
            return null;
        }

        // Site admins can always login as someone else.
        if (is_siteadmin($currentuser)) {
            return $systemcontext;
        }

        // Non admins can never login as an admin.
        if (is_siteadmin($loginasuser)) {
            return null;
        }

        // Now, we accept anyone who can loginas at the site level, and they can do so at the system level.
        if (has_capability('moodle/user:loginas', $systemcontext, $currentuser)) {
            return $systemcontext;
        }

        if (!empty($course)) {
            $coursecontext = context_course::instance($course->id);

            if (
                // Reject all that do not have loginas capability.
                !has_capability('moodle/user:loginas', $coursecontext, $currentuser) ||
                // Reject if user is trying to login as someone with site level loginas powers.
                has_capability('moodle/user:loginas', $systemcontext, $loginasuser) ||
                // Reject if other is not enrolled.
                !is_enrolled($coursecontext, $loginasuser->id)
            ) {
                return null;
            }

            // Check if the users are in the same group.
            if (groups_get_course_groupmode($course) == SEPARATEGROUPS &&
                !has_capability('moodle/site:accessallgroups', $coursecontext, $currentuser)) {
                $samegroup = false;
                if ($groups = groups_get_all_groups($course->id, $currentuser->id)) {
                    foreach ($groups as $group) {
                        if (groups_is_member($group->id, $loginasuser->id)) {
                            $samegroup = true;
                            break;
                        }
                    }
                }
                if (!$samegroup) {
                    return null;
                }
            }

            // Passed all checks.
            return $coursecontext;
        }

        return null;
    }
}
