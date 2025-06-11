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

namespace core_courseformat;

use core_group\hook\after_group_membership_added;
use core_group\hook\after_group_membership_removed;

/**
 * Hook listener for course format
 *
 * @package    core_courseformat
 * @copyright  2024 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Add members to group room when a new member is added to the group.
     *
     * @param after_group_membership_added $hook The group membership added hook.
     */
    public static function add_members_to_group(
        after_group_membership_added $hook,
    ): void {
        $group = $hook->groupinstance;
        $course = get_course($group->courseid);
        base::invalidate_all_session_caches_for_course($course);
    }

    /**
     * Remove members from the room when a member is removed from group room.
     *
     * @param after_group_membership_removed $hook The group membership removed hook.
     */
    public static function remove_members_from_group(
        after_group_membership_removed $hook,
    ): void {
        $group = $hook->groupinstance;
        $course = get_course($group->courseid);
        base::invalidate_all_session_caches_for_course($course);
    }
}
