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

    /** @var int Counter to be used in {@link tree_node::unique_sql_parameter()}. */
    protected static $uniquesqlparametercounter = 1;

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
    abstract public function check_available($not,
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
    abstract public function is_available_for_all($not = false);

    /**
     * Saves tree data back to a structure object.
     *
     * @return \stdClass Structure object (ready to be made into JSON format)
     */
    abstract public function save();

    /**
     * Checks whether this node should be included after restore or not. The
     * node may be removed depending on restore settings, which you can get from
     * the $task object.
     *
     * By default nodes are still included after restore.
     *
     * @param string $restoreid Restore ID
     * @param int $courseid ID of target course
     * @param \base_logger $logger Logger for any warnings
     * @param string $name Name of this item (for use in warning messages)
     * @param \base_task $task Current restore task
     * @return bool True if there was any change
     */
    public function include_after_restore($restoreid, $courseid, \base_logger $logger, $name,
            \base_task $task) {
        return true;
    }

    /**
     * Updates this node after restore, returning true if anything changed.
     * The default behaviour is simply to return false. If there is a problem
     * with the update, $logger can be used to output a warning.
     *
     * Note: If you need information about the date offset, call
     * \core_availability\info::get_restore_date_offset($restoreid). For
     * information on the restoring task and its settings, call
     * \core_availability\info::get_restore_task($restoreid).
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
    abstract public function update_dependency_id($table, $oldid, $newid);

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
     * condition will be removed from the list, unless they have the ability
     * to view hidden activities/sections.
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
     * Conditions do not need to check the viewhiddenactivities or
     * viewhiddensections capabilities. These are handled by
     * core_availability\info::filter_user_list.
     *
     * @param array $users Array of userid => object
     * @param bool $not True if this condition is applying in negative mode
     * @param \core_availability\info $info Item we're checking
     * @param capability_checker $checker
     * @return array Filtered version of input array
     * @throws \coding_exception If called on a condition that doesn't apply to user lists
     */
    public function filter_user_list(array $users, $not,
            \core_availability\info $info, capability_checker $checker) {
        throw new \coding_exception('Not implemented (do not call unless '.
                'is_applied_to_user_lists is true)');
    }

    /**
     * Obtains SQL that returns a list of enrolled users that has been filtered
     * by the conditions applied in the availability API, similar to calling
     * get_enrolled_users and then filter_user_list. As for filter_user_list,
     * this ONLY filters out users with conditions that are marked as applying
     * to user lists. For example, group conditions are included but date
     * conditions are not included.
     *
     * The returned SQL is a query that returns a list of user IDs. It does not
     * include brackets, so you neeed to add these to make it into a subquery.
     * You would normally use it in an SQL phrase like "WHERE u.id IN ($sql)".
     *
     * The SQL will be complex and may be slow. It uses named parameters (sorry,
     * I know they are annoying, but it was unavoidable here).
     *
     * If there are no conditions, the returned result is array('', array()).
     *
     * Conditions do not need to check the viewhiddenactivities or
     * viewhiddensections capabilities. These are handled by
     * core_availability\info::get_user_list_sql.
     *
     * @param bool $not True if this condition is applying in negative mode
     * @param \core_availability\info $info Item we're checking
     * @param bool $onlyactive If true, only returns active enrolments
     * @return array Array with two elements: SQL subquery and parameters array
     * @throws \coding_exception If called on a condition that doesn't apply to user lists
     */
    public function get_user_list_sql($not, \core_availability\info $info, $onlyactive) {
        if (!$this->is_applied_to_user_lists()) {
            throw new \coding_exception('Not implemented (do not call unless '.
                    'is_applied_to_user_lists is true)');
        }

        // Handle situation where plugin does not implement this, by returning a
        // default (all enrolled users). This ensures compatibility with 2.7
        // plugins and behaviour. Plugins should be updated to support this
        // new function (if they return true to is_applied_to_user_lists).
        debugging('Availability plugins that return true to is_applied_to_user_lists ' .
                'should also now implement get_user_list_sql: ' . get_class($this),
                DEBUG_DEVELOPER);
        return get_enrolled_sql($info->get_context(), '', 0, $onlyactive);
    }

    /**
     * Utility function for generating SQL parameters (because we can't use ?
     * parameters because get_enrolled_sql has infected us with horrible named
     * parameters).
     *
     * @param array $params Params array (value will be added to this array)
     * @param string|int $value Value
     * @return SQL code for the parameter, e.g. ':pr1234'
     */
    protected static function unique_sql_parameter(array &$params, $value) {

        // Note we intentionally do not use self:: here.
        $count = tree_node::$uniquesqlparametercounter++;
        $unique = 'usp' . $count;
        $params[$unique] = $value;
        return ':' . $unique;
    }
}
