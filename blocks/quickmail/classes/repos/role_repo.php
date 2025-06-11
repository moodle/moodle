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
use block_quickmail\repos\interfaces\role_repo_interface;

class role_repo extends repo implements role_repo_interface {

    public $defaultsort = 'id';

    public $defaultdir = 'asc';

    public $sortableattrs = [
        'id' => 'id',
    ];

    /**
     * Returns an array of all roles that are allowed to be selected to message in the given course
     *
     * @param  object  $course
     * @param  object  $coursecontext  optional, if not given, will be resolved
     * @return array   keyed by role id
     */
    public static function get_course_selectable_roles($course, $coursecontext = null) {
        // If a context was not passed, pull one now.
        $coursecontext = $coursecontext ? $coursecontext : \context_course::instance($course->id);

        // Get configured, selectable role ids.
        $allowedroleids = \block_quickmail_config::get_role_selection_array($course);

        // If no roles configured, return no results.
        if (!$allowedroleids) {
            return [];
        }

        // Get all roles for context, keyed by id.
        $allcourseroles = get_roles_used_in_context($coursecontext);

        // Return an intesection of all roles, and those allowed by config.
        $roles = array_intersect_key($allcourseroles, array_flip($allowedroleids));
        return $roles;
    }

    /**
     * Returns an array of role selection values
     *
     * Note: if a course or course id is given, the returned array will limit to course-level configuration,
     * otherwise, defaults to block-level configuration.
     *
     * @param  mixed $courseorid
     * @return array  [role id => role name]
     */
    public static function get_alternate_email_role_selection_array($courseorid = null) {
        if (!$allowedroleids = \block_quickmail_config::get_role_selection_array($courseorid)) {
            return [];
        }

        global $DB;

        $roles = $DB->get_records('role', null, 'sortorder ASC');

        return array_reduce($roles, function ($carry, $role) use ($allowedroleids) {
            if (in_array($role->id, $allowedroleids)) {
                $carry[$role->id] = empty($role->name) ? $role->shortname : $role->name;
            }

            return $carry;
        }, []);
    }

    /**
     * Returns an array of role ids that are assigned to a given user id in a given course id
     *
     * @param  int             $userid
     * @param  int             $courseid
     * @param  context_course  $coursecontext   optional, will be fetched if not given
     * @return array
     */
    public static function get_user_roles_in_course($userid, $courseid, $coursecontext = null) {
        // If a context was not passed, pull one now.
        $coursecontext = $coursecontext ? $coursecontext : \context_course::instance($courseid);

        // Get the user's roles in the course context (no parents).
        $roles = get_user_roles($coursecontext, $userid, false);

        // Return as a simple array of role ids.
        return array_values(array_map(function($role) {
            return (int) $role->roleid;
        }, $roles));
    }

}
