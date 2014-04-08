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
 * Node (base class) used to construct a tree of availability conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Node (base class) used to construct a tree of availability conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tree_node {
    /**
     * Determines whether this particular item is currently available
     * according to the availability criteria.
     *
     * - This does not include the 'visible' setting (i.e. this might return
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * The $not option is potentially confusing. This option always indicates
     * the 'real' value of NOT. For example, a condition inside a 'NOT AND'
     * group will get this called with $not = true, but if you put another
     * 'NOT OR' group inside the first group, then a condition inside that will
     * be called with $not = false. We need to use the real values, rather than
     * the more natural use of the current value at this point inside the tree,
     * so that the information displayed to users makes sense.
     *
     * @param bool $not Set true if we are inverting the condition
     * @param \core_availability\info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return result Availability check result
     */
    public abstract function check_available($not,
            \core_availability\info $info, $grabthelot, $userid);

    /**
     * Checks whether this condition is actually going to be available for
     * all users under normal circumstances.
     *
     * Normally, if there are any conditions, then it may be hidden. However
     * in the case of date conditions there are some conditions which will
     * definitely not result in it being hidden for anyone.
     *
     * @param bool $not Set true if we are inverting the condition
     * @return bool True if condition will return available for everyone
     */
    public abstract function is_available_for_all($not = false);

    /**
     * Saves tree data back to a structure object.
     *
     * @return stdClass Structure object (ready to be made into JSON format)
     */
    public abstract function save();

    /**
     * Updates this node after restore, returning true if anything changed.
     * The default behaviour is simply to return false. If there is a problem
     * with the update, $logger can be used to output a warning.
     *
     * @param string $restoreid Restore ID
     * @param int $courseid ID of target course
     * @param \base_logger $logger Logger for any warnings
     * @param string $name Name of this item (for use in warning messages)
     * @return bool True if there was any change
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        return false;
    }

    /**
     * Updates this node if it contains any references (dependencies) to the
     * given table and id.
     *
     * @param string $table Table name e.g. 'course_modules'
     * @param int $oldid Previous ID
     * @param int $newid New ID
     * @return bool True if it changed, otherwise false
     */
    public abstract function update_dependency_id($table, $oldid, $newid);

    /**
     * Checks whether this condition applies to user lists. The default is
     * false (the condition is used to control access, but does not prevent
     * the student from appearing in lists).
     *
     * For example, group conditions apply to user lists: we do not want to
     * include a student in a list of users if they are prohibited from
     * accessing the activity because they don't belong to a relevant group.
     * However, date conditions do not apply - we still want to show users
     * in a list of people who might have submitted an assignment, even if they
     * are no longer able to access the assignment in question because there is
     * a date restriction.
     *
     * The general idea is that conditions which are likely to be permanent
     * (group membership, user profile) apply to user lists. Conditions which
     * are likely to be temporary (date, grade requirement) do not.
     *
     * Conditions which do apply to user lists must implement the
     * filter_user_list function.
     *
     * @return bool True if this condition applies to user lists
     */
    public function is_applied_to_user_lists() {
        return false;
    }

    /**
     * Tests this condition against a user list. Users who do not meet the
     * condition will be removed from the list.
     *
     * This function must be implemented if is_applied_to_user_lists returns
     * true. Otherwise it will not be called.
     *
     * The function must operate efficiently, e.g. by using a fixed number of
     * database queries regardless of how many users are in the list.
     *
     * Within this function, if you need to check capabilities, please use
     * the provided checker which caches results where possible.
     *
     * @param array $users Array of userid => object
     * @param bool $not True if this condition is applying in negative mode
     * @param \core_availability\info $info Item we're checking
     * @param capability_checker $checker
     * @return array Filtered version of input array
     * @throws coding_exception If called on a condition that doesn't apply to user lists
     */
    public function filter_user_list(array $users, $not,
            \core_availability\info $info, capability_checker $checker) {
        throw new coding_exception('Not implemented (do not call unless '.
                'is_applied_to_user_lists is true)');
    }
}
