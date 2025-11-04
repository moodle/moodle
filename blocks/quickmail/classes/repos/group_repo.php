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
use block_quickmail\repos\interfaces\group_repo_interface;

class group_repo extends repo implements group_repo_interface {

    public $defaultsort = 'id';

    public $defaultdir = 'asc';

    public $sortableattrs = [
        'id' => 'id',
    ];

    /**
     * Returns an array of all groups that are allowed to be selected to message in the given course by the given user
     *
     * @param  object  $course
     * @param  object  $user
     * @param  bool    $includegroupusers    if true, group users will be return in the results
     * @param  object  $coursecontext  optional, if not given, will be resolved
     * @return array   keyed by group id
     */
    public static function get_course_user_selectable_groups($course, $user, $includegroupusers = false, $coursecontext = null) {
        // If a context was not passed, pull one now.
        $coursecontext = $coursecontext ?: \context_course::instance($course->id);

        // If user cannot access all groups in the course, and the course is set to be strict.
        if (!\block_quickmail_plugin::user_has_capability('viewgroupusers', $user, $coursecontext)
            && \block_quickmail_config::be_ferpa_strict_for_course($course)) {
            // Get this user's group associations, by groupings.
            $groupingarray = groups_get_user_groups($course->id, $user->id);

            // Transform this array to an array of groups.
            $groups = self::transform_grouping_array_to_groups($groupingarray);

            // Add a "members" property to the result objects to be consistent.
            $groups = array_map(function($group) {
                $g = $group;
                $g->members = [];
                return $g;
            }, $groups);
        } else {
            $groups = array_map(function($group) use ($includegroupusers) {
                $g = $group;
                $g->members = $includegroupusers
                    ? array_keys(groups_get_members($group->id, 'u.id'))
                    : [];
                return $g;
            }, groups_get_all_groups($course->id));
        }

        return $groups;
    }

    /**
     * Returns an array of all groups that the given user is associated with in the given course
     *
     * @param  object  $course
     * @param  object  $user
     * @param  object  $coursecontext  optional, if not given, will be resolved
     * @return array   keyed by group id
     */
    public static function get_course_user_groups($course, $user, $coursecontext = null) {

        // Scheduled task could pass in user id as user, check.
        if (gettype($user) == "string") {
            $userid = $user;
        } else {
            $userid = $user->id;
        }
        // Get this user's group associations, by groupings.
        $groupingarray = groups_get_user_groups($course->id, $userid);

        // Transform this array to an array of groups.
        $groups = self::transform_grouping_array_to_groups($groupingarray);

        return $groups;
    }

    /**
     * Returns an array of groups given an array of groupings with nested groups
     *
     * @param  array  $groupingarray
     * @return array  keyed by group id
     */
    private static function transform_grouping_array_to_groups($groupingarray) {
        if (!$groupingarray) {
            return [];
        }

        $groupids = [];

        // Iterate through each grouping.
        foreach ($groupingarray as $groupinggrouparray) {
            // Extract only group ids.
            $groupids = array_map(function($groupid) {
                return $groupid;
            }, $groupinggrouparray);
        }

        // Reduce list down to unique group ids.
        $groupids = array_unique($groupids);

        $groups = [];

        // Iterate through each group id.
        foreach ($groupids as $groupid) {
            // Pull the group object, adding it to the container.
            $groups[$groupid] = groups_get_group($groupid);
        }

        return $groups;
    }

}
