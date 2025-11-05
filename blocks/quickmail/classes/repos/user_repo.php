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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\repos;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\repos\repo;
use block_quickmail\repos\interfaces\user_repo_interface;
use context_course;
use block_quickmail\repos\role_repo;
use block_quickmail\repos\group_repo;
use block_quickmail_plugin;
use block_quickmail_config;
require_once($CFG->dirroot.'/user/profile/lib.php');

class user_repo extends repo implements user_repo_interface {

    public $defaultsort = 'id';

    public $defaultdir = 'asc';

    public $sortableattrs = [
        'id' => 'id',
    ];

    /**
     * Returns an array of all users that are allowed to be selected to message in the given course by the given user
     *
     * @param  object  $course
     * @param  object  $user
     * @param  object  $coursecontext  optional, if not given, will be resolved
     * @return array   keyed by user id
     */
    public static function get_course_user_selectable_users($course, $user, $coursecontext = null) {
        // If a context was not passed, pull one now.
        $coursecontext = $coursecontext ?: context_course::instance($course->id);

        // If user cannot access all groups in the course, and the course is set to be strict.
        if (!block_quickmail_plugin::user_has_capability('viewgroupusers', $user, $coursecontext)
            && block_quickmail_config::be_ferpa_strict_for_course($course)) {
            // Get all users with non-"group limited role"'s.
            $allaccessusers = get_enrolled_users($coursecontext, 'moodle/site:accessallgroups', 0, 'u.*', null, 0, 0, true);

            // Get the groups that this user is associated with.
            $groups = group_repo::get_course_user_groups($course, $user, $coursecontext);

            $groupids = array_keys($groups);

            // Get all users within any groups the user belongs to.
            $peerusers = self::get_course_group_users($coursecontext, $groupids, true, 'u.*');

            $users = array_merge($allaccessusers, $peerusers);

            // Be sure that we have unique users.
            $users = array_unique($users, SORT_REGULAR);
        } else {
            // Get all users in course.
            $users = self::get_course_users($coursecontext);
        }

        return $users;
    }

    /**
     * Get all users within a course
     *
     * @param  object  $coursecontext  must be a course context
     * @param  boolean $activeonly     whether or not to filter by active enrollment, defaults to true
     * @param  string  $userfields     comma-separated list of fields to include in results, must be prefixed with "u."
     * @param  integer $groupid        group id to filter by, should be left as default (0) for pulling all course users
     * @return array
     */
    public static function get_course_users($coursecontext, $activeonly = true, $userfields = null, $groupid = 0) {
        // Set fields to include.
        $userfields = ! empty($userfields) ? $userfields : 'u.id,u.firstname,u.lastname';

        $users = get_enrolled_users($coursecontext, '', $groupid, $userfields, null, 0, 0, $activeonly);

        return $users;
    }

    /**
     * Get siingle user
     *
     * @param  int     $userid
     * @return object  $user object
     */
    public static function get_user_in_course($userid = 0) {
        // If this is a single group, return all users for the group.
        global $DB;

        if ($user = $DB->get_record('user', array('id' => $userid))) {

            $temp = new \stdClass();
            $temp->id = $userid;
            $temp->firstname = $user->firstname;
            $temp->lastname = $user->lastname;
            return $temp;
        } else {
            return false;
        }
    }

    /**
     * Get all users within a course group
     *
     * @param  object  $coursecontext  must be a course context
     * @param  mixed   $groupid        a group id, or an array of group ids
     * @param  boolean $activeonly     whether or not to filter by active enrollment, defaults to true
     * @param  string  $userfields     comma-separated list of fields to include in results, must be prefixed with "u."
     * @return array   keyed by user id
     */
    public static function get_course_group_users($coursecontext, $groupid, $activeonly = true, $userfields = null) {
        // If this is a single group, return all users for the group.
        if (!is_array($groupid)) {
            $groupusers = self::get_course_users($coursecontext, $activeonly, $userfields, $groupid);

            $users = [];

            // Rekey the returned array by user id.
            foreach ($groupusers as $groupuser) {
                $users[$groupuser->id] = $groupuser;
            }

            // Otherwise, get the unique users within the given list of group ids.
        } else {
            $users = [];

            // For each given group id.
            foreach ($groupid as $gid) {
                // Pull the users within the group.
                $groupusers = self::get_course_users($coursecontext, $activeonly, $userfields, $gid);

                // Add each to the container.
                foreach ($groupusers as $groupuser) {
                    $users[$groupuser->id] = $groupuser;
                }

                // Be sure we have a unique list of users (still necessary?).
                $users = array_unique($users, SORT_REGULAR);
            }
        }

        return $users;
    }

    /**
     * Get all users with a given role (or roles) within a given course
     *
     * @param  object  $coursecontext  must be a course context
     * @param  mixed   $roleid         a role id, or an array of role ids
     * @param  boolean $activeonly     whether or not to filter by active enrollment, defaults to true
     * @param  string  $userfields     comma-separated list of fields to include in results, must be prefixed with "u."
     * @return array   keyed by user id
     */
    public static function get_course_role_users($coursecontext, $roleid, $activeonly = true, $userfields = null) {
        // Set fields to include.
        $userfields = ! empty($userfields) ? $userfields : 'u.id,u.firstname,u.lastname';

        $orderby = 'u.firstname ASC';

        // If this is a single role, return all users for the role.
        if (!is_array($roleid)) {
            // Pull all.
            $roleusers = get_role_users($roleid, $coursecontext, false, $userfields, $orderby, ! $activeonly);

            $users = [];

            // Rekey the returned array by user id.
            foreach ($roleusers as $roleuser) {
                $users[$roleuser->id] = $roleuser;
            }

            // Otherwise, get the unique users within the given list of role ids.
        } else {
            $users = [];

            // For each given role id.
            foreach ($roleid as $rid) {
                // Pull the users within the role.
                $roleusers = get_role_users($rid, $coursecontext, false, $userfields, $orderby, ! $activeonly);

                // Add each to the container.
                foreach ($roleusers as $roleuser) {
                    $users[$roleuser->id] = $roleuser;
                }

                // Be sure we have a unique list of users (still necessary?).
                $users = array_unique($users, SORT_REGULAR);
            }
        }

        return $users;
    }

    /**
     * Returns an array of unique user ids, "selectable" by the given user, given arrays of included and excluded "entity ids"
     *
     * @param  object  $course
     * @param  object  $user
     * @param  array   $includedentityids   [role_(role id), group_(group id), user_(user id)]
     * @param  array   $excludedentityids   [role_(role id), group_(group id), user_(user id)]
     * @return array
     */
    public static function get_unique_course_user_ids_from_selected_entities(
            $course,
            $user,
            $includedentityids = [],
            $excludedentityids = []) {
        $resultuserids = [];

        // If none included, return no results.
        if (empty($includedentityids)) {
            return $resultuserids;
        }

        // Make sure there are no duplicates in the incoming arrays.
        $includedentityids = array_unique($includedentityids);
        $excludedentityids = array_unique($excludedentityids);

        // Determine whether or not we're sending to all.
        $sendingtoall = in_array('all', $includedentityids);

        // Ignore "exclude all".
        if (($key = array_search('all', $excludedentityids)) !== false) {
            unset($excludedentityids[$key]);
        }

        // Create a container for included/excluded role/group IDs.
        $filteredentityids = [
            'included' => [
                'role' => [],
                'group' => [],
            ],
            'excluded' => [
                'role' => [],
                'group' => [],
            ]
        ];

        // Extrct table IDs for roles/groups, adding them to the container.
        // Iterate through (included, excluded).
        foreach ($filteredentityids as $type => $entity) {
            // If we're sending to all, do not worry about determining included ids.
            if ($sendingtoall && $type == 'included') {
                continue;
            }

            // Iterate through each entity name within this type (role, group).
            foreach ($entity as $name => $keys) {
                $typekey = $type . 'entityids';

                // Get entity keys for this included/excluded role/group.
                $entitykeys = array_filter($$typekey, function($key) use ($name) {
                    return strpos($key, $name . '_') === 0;
                });

                // Remove entity name prefix and add to filtered results.
                $filteredentityids[$type][$name] = array_map(function($key) use ($name) {
                    return str_replace($name . '_', '', $key);
                }, $entitykeys);
            }
        }

        // Remove any excluded role/group IDs from existing included role/group IDs in the container.
        // Iterate through excluded entity names (role, group).
        foreach ($filteredentityids['excluded'] as $name => $entitykeys) {
            // Iterate through each value in this excluded role/group.
            foreach ($entitykeys as $keykey => $keyvalue) {
                // If this excluded role/group value appears in the included container.
                if (in_array($keyvalue, $filteredentityids['included'][$name])) {
                    // Get the array key within the included container.
                    $includedkey = array_search($keyvalue, $filteredentityids['included'][$name]);
                    // Remove this value from both the includes and excludes.
                    unset($filteredentityids['included'][$name][$includedkey]);
                }
            }
        }

        // Get course context for use in upcoming queries.
        $coursecontext = context_course::instance($course->id);

        // Create two new containers for final output of included/excluded user ids.
        $includeduserids = [];
        $excludeduserids = [];

        // Pull any users for each included/excluded role/groups, adding them to the new containers.
        // If not sending to all, pull all selectable roles for the auth user if we're going to be including roles.
        $selectableroleids = ! empty($filteredentityids['included']['role']) && ! $sendingtoall
            ? array_keys(role_repo::get_course_selectable_roles($course, $coursecontext))
            : [];

        // If not sending to all, pull all selectable groups for the auth user if we're going to be including groups.
        $selectablegroupids = ! empty($filteredentityids['included']['group']) && ! $sendingtoall
            ? array_keys(group_repo::get_course_user_selectable_groups($course, $user, false, $coursecontext))
            : [];

        // Iterate through initial container of included/excluded role/group.
        foreach (['included', 'excluded'] as $type) {
            // If we're sending to all, do not worry about determining included roles/groups.
            if ($sendingtoall && $type == 'included') {
                continue;
            }

            foreach (['role', 'group'] as $name) {
                foreach ($filteredentityids[$type][$name] as $nameid) {
                    // For inclusions, check that the role or group is selectable by the user.
                    if ($type == 'included') {
                        // If this is a role but NOT selectable by this user.
                        if ($name == 'role' && ! in_array($nameid, $selectableroleids)) {
                            continue;

                            // Otherwise, if this is a group but NOT selectable by this user.
                        } else if ($name == 'group' && ! in_array($nameid, $selectablegroupids)) {
                            continue;
                        }
                    }

                    // Get all user for this included/excluded role/group, scoped to this course.
                    $users = $name == 'role'
                        ? self::get_course_role_users($coursecontext, $nameid)
                        : self::get_course_group_users($coursecontext, $nameid);

                    // Get appropriate name for the container to place these user ids within.
                    $typecontainer = $type . 'userids';

                    // Push these new user ids into the appropriate container.
                    $$typecontainer = array_merge($$typecontainer, array_map(function($user) {
                        return $user->id;
                    }, $users));
                }
            }
        }

        // Pull all course users for later use.
        $courseusers = self::get_course_user_selectable_users($course, $user, $coursecontext);

        // Convert these users to an array of ids.
        $courseuserids = array_map(function($user) {
            return $user->id;
        }, $courseusers);

        // If sending to all, add all course user ids to include user ids.
        if ($sendingtoall) {
            $includeduserids = $courseuserids;
        }

        // Add in each explicitly included/excluded user to the appropriate container.

        foreach (['included', 'excluded'] as $type) {
            // If we're sending to all, do not worry about determining included users.
            if ($sendingtoall && $type == 'included') {
                continue;
            }

            // Get name of appropriate (initial) container.
            $typekey = $type . 'entityids';

            // Extract only the user ids from the container..
            $users = array_filter($$typekey, function($key) {
                return strpos($key, 'user_') === 0;
            });

            // Filter out any explicitly included users that do not belong to this course.
            if ($type == 'included') {
                $users = array_filter($users, function($user) use ($courseuserids) {
                    return in_array(str_replace('user_', '', $user), $courseuserids);
                });
            }

            // Get name of appropriate output container.
            $typecontainer = $type . 'userids';

            // Push these user ids into the appropriate container.
            $$typecontainer = array_merge($$typecontainer, array_map(function($user) {
                return str_replace('user_', '', $user);
            }, $users));
        }

        // Remove any excluded user IDs from the included user IDs, creating a new container.
        $resultuserids = array_filter($includeduserids, function($id) use ($excludeduserids) {
            return ! in_array($id, $excludeduserids);
        });

        // Finally, remove any user IDs that this user may not message.
        return array_unique(array_intersect(array_map(function ($user) {
            return $user->id;
        }, $courseusers), $resultuserids));
    }

    /**
     * Returns an array of mentor users that are assigned to the given "mentee" user
     *
     * @param  object  $user
     * @return array  keyed by user ids
     */
    public static function get_mentors_of_user($user) {
        global $DB;
        $sql = 'SELECT ra.userid as mentor_user_id
                    FROM {context} c JOIN {role_assignments} ra on c.id = ra.contextid
                WHERE contextlevel = 30 AND instanceid = ?';
        $result = $DB->get_records_sql($sql, [$user->id]);

        if (!$result) {
            return [];
        }

        return $DB->get_records_list('user', 'id', array_keys($result));
    }

}
