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
 * Privacy class for requesting user data.
 *
 * @package    core_course
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\context_aware_provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_subsystem_link('core_completion', [], 'privacy:metadata:completionsummary');
        $collection->add_subsystem_link('core_favourites', [], 'privacy:metadata:favouritessummary');
        $collection->add_subsystem_link('core_favourites', [], 'privacy:metadata:activityfavouritessummary');
        $collection->add_user_preference('coursecat_management_perpage', 'privacy:perpage');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        list($join, $where, $params) = \core_completion\privacy\provider::get_course_completion_join_sql($userid, 'cc', 'c.id');
        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {course} c ON ctx.instanceid = c.id AND ctx.contextlevel = :contextcourse
                {$join}
                WHERE {$where}";
        $params['contextcourse'] = CONTEXT_COURSE;
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $userid, 'core_course', 'courses');

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        \core_completion\privacy\provider::add_course_completion_users_to_userlist($userlist);
        \core_favourites\privacy\provider::add_userids_for_context($userlist, 'courses');
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Get the course.
        list($select, $params) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params['contextcourse'] = CONTEXT_COURSE;

        $sql = "SELECT c.*
                FROM {course} c
                JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                WHERE ctx.id $select";

        $courses = $DB->get_recordset_sql($sql, $params);

        foreach ($courses as $course) {
            $coursecompletion = \core_completion\privacy\provider::get_course_completion_info($contextlist->get_user(), $course);
            writer::with_context(\context_course::instance($course->id))->export_data(
                    [get_string('privacy:completionpath', 'course')], (object) $coursecompletion);
            // Get user's favourites information for the particular course.
            $coursefavourite = \core_favourites\privacy\provider::get_favourites_info_for_user($contextlist->get_user()->id,
                    \context_course::instance($course->id), 'core_course', 'courses', $course->id);
            if ($coursefavourite) { // If the course has been favourited by the user, include it in the export.
                writer::with_context(\context_course::instance($course->id))->export_data(
                        [get_string('privacy:favouritespath', 'course')], (object) $coursefavourite);
            }
        }
        $courses->close();
    }

    /**
     * Give the component a chance to include any contextual information deemed relevant to any child contexts which are
     * exporting personal data.
     *
     * By giving the component access to the full list of contexts being exported across all components, it can determine whether a
     * descendant context is being exported, and decide whether to add relevant contextual information about itself. Having access
     * to the full list of contexts being exported is what makes this component a context aware provider.
     *
     * E.g.
     * If, during the core export process, a course module is included in the contextlist_collection but the course containing the
     * module is not (perhaps there's no longer a user enrolment), then the course should include general contextual information in
     * the export so we know basic details about which course the module belongs to. This method allows the course to make that
     * decision, based on the existence of any decendant module contexts in the collection.
     *
     * @param \core_privacy\local\request\contextlist_collection $contextlistcollection
     */
    public static function export_context_data(\core_privacy\local\request\contextlist_collection $contextlistcollection) {
        global $DB;

        $coursecontextids = $DB->get_records_menu('context', ['contextlevel' => CONTEXT_COURSE], '', 'id, instanceid');
        $courseids = [];
        foreach ($contextlistcollection as $component) {
            foreach ($component->get_contexts() as $context) {
                // All course contexts have been accounted for, so skip all checks.
                if (empty($coursecontextids)) {
                    break;
                }
                // Only course, module, and block contexts are checked.
                if (in_array($context->contextlevel, [CONTEXT_USER, CONTEXT_SYSTEM, CONTEXT_COURSECAT])) {
                    continue;
                }
                // If the context is a course, then we just add it without the need to check context path.
                if ($context->contextlevel == CONTEXT_COURSE) {
                    $courseids[$context->id] = $context->instanceid;
                    unset($coursecontextids[$context->id]);
                    continue;
                }
                // Otherwise, we need to check all the course context paths, to see if this context is a descendant.
                foreach ($coursecontextids as $contextid => $instanceid) {
                    if (stripos($context->path, '/' . $contextid . '/') !== false) {
                        $courseids[$contextid] = $instanceid;
                        unset($coursecontextids[$contextid]);
                    }
                }
            }
        }
        if (empty($courseids)) {
            return;
        }

        // Export general data for these contexts.
        list($sql, $params) = $DB->get_in_or_equal($courseids);
        $sql = 'id ' . $sql;
        $coursedata = $DB->get_records_select('course', $sql, $params);

        foreach ($coursedata as $course) {
            $context = \context_course::instance($course->id);
            $courseformat = $course->format !== 'site' ? get_string('pluginname', 'format_' . $course->format) : get_string('site');
            $data = (object) [
                'fullname' => format_string($course->fullname, true, ['context' => $context]),
                'shortname' => $course->shortname,
                'idnumber' => $course->idnumber,
                'summary' => format_text(
                    writer::with_context($context)->rewrite_pluginfile_urls(
                        [],
                        'course',
                        'summary',
                        0,
                        $course->summary
                    ), $course->summaryformat, ['context' => $context]),
                'format' => $courseformat,
                'startdate' => transform::datetime($course->startdate),
                'enddate' => transform::datetime($course->enddate)
            ];
            writer::with_context($context)
                    ->export_area_files([], 'course', 'summary', 0)
                    ->export_area_files([], 'course', 'overviewfiles', 0)
                    ->export_data([], $data);
        }
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $perpage = get_user_preferences('coursecat_management_perpage', null, $userid);
        if (isset($perpage)) {
            writer::export_user_preference('core_course',
                'coursecat_management_perpage',
                $perpage,
                get_string('privacy:perpage', 'course')
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Check what context we've been delivered.
        if (!$context instanceof \context_course) {
            return;
        }
        // Delete course completion data.
        \core_completion\privacy\provider::delete_completion(null, $context->instanceid);
        // Delete course favourite data.
        \core_favourites\privacy\provider::delete_favourites_for_all_users($context, 'core_course',
                'courses');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        foreach ($contextlist as $context) {
            // Check what context we've been delivered.
            if ($context instanceof \context_course) {
                // Delete course completion data.
                \core_completion\privacy\provider::delete_completion($contextlist->get_user(), $context->instanceid);
                // Delete course favourite data.
                \core_favourites\privacy\provider::delete_favourites_for_user($contextlist, 'core_course',
                    'courses');
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        // Check what context we've been delivered.
        if (!$context instanceof \context_course) {
            return;
        }
        // Delete course completion data.
        \core_completion\privacy\provider::delete_completion_by_approved_userlist($userlist, $context->instanceid);
        // Delete course favourite data.
        \core_favourites\privacy\provider::delete_favourites_for_userlist($userlist, 'courses');
    }
}
