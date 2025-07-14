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
 * This file contains the polyfill to allow a plugin to operate with Moodle 3.3 up.
 *
 * @package mod_assign
 * @copyright 2018 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\privacy;

use core_privacy\local\request\contextlist;

defined('MOODLE_INTERNAL') || die();

/**
 * The trait used to provide backwards compatability for third-party plugins.
 *
 * @copyright 2018 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait feedback_legacy_polyfill {

    /**
     * Retrieves the contextids associated with the provided userid for this subplugin.
     * NOTE if your subplugin must have an entry in the assign_grade table to work, then this
     * method can be empty.
     *
     * @param  int $userid The user ID to get context IDs for.
     * @param  \core_privacy\local\request\contextlist $contextlist Use add_from_sql with this object to add your context IDs.
     */
    public static function get_context_for_userid_within_feedback(int $userid, contextlist $contextlist) {
        return static::_get_context_for_userid_within_feedback($userid, $contextlist);
    }

    /**
     * Returns student user ids related to the provided teacher ID. If an entry must be present in the assign_grade table for
     * your plugin to work then there is no need to fill in this method. If you filled in get_context_for_userid_within_feedback()
     * then you probably have to fill this in as well.
     *
     * @param  useridlist $useridlist A list of user IDs of students graded by this user.
     */
    public static function get_student_user_ids(useridlist $useridlist) {
        return static::_get_student_user_ids($useridlist);
    }

    /**
     * Export feedback data with the available grade and userid information provided.
     * assign_plugin_request_data contains:
     * - context
     * - grade object
     * - current path (subcontext)
     * - user object
     *
     * @param  assign_plugin_request_data $exportdata Contains data to help export the user information.
     */
    public static function export_feedback_user_data(assign_plugin_request_data $exportdata) {
        return static::_export_feedback_user_data($exportdata);
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     * assign_plugin_request_data contains:
     * - context
     * - assign object
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_feedback_for_context(assign_plugin_request_data $requestdata) {
        return static::_delete_feedback_for_context($requestdata);
    }

    /**
     * Calling this function should delete all user data associated with this grade.
     * assign_plugin_request_data contains:
     * - context
     * - grade object
     * - user object
     * - assign object
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data.
     */
    public static function delete_feedback_for_grade(assign_plugin_request_data $requestdata) {
        return static::_delete_feedback_for_grade($requestdata);
    }
}
