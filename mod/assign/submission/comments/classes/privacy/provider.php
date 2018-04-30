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
 * @package    assignsubmission_comments
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_comments\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\metadata\provider as metadataprovider;
use \core_comment\privacy\provider as comments_provider;
use \core_privacy\local\request\contextlist;
use \mod_assign\privacy\assign_plugin_request_data;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignsubmission_comments
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadataprovider, \mod_assign\privacy\assignsubmission_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) {
        $collection->link_subsystem('core_comment', 'privacy:metadata:commentpurpose');
        return $collection;
    }

    /**
     * It is possible to make a comment as a teacher without creating an entry in the submission table, so this is required
     * to find those entries.
     *
     * @param  int $userid The user ID that we are finding contexts for.
     * @param  contextlist $contextlist A context list to add sql and params to for contexts.
     */
    public static function get_context_for_userid_within_submission($userid, contextlist $contextlist) {
        $sql = "SELECT contextid
                  FROM {comments}
                 WHERE component = :component
                       AND commentarea = :commentarea
                       AND userid = :userid";
        $params = ['userid' => $userid, 'component' => 'assignsubmission_comments', 'commentarea' => 'submission_comments'];
        $contextlist->add_from_sql($sql, $params);
    }

    /**
     * Due to the fact that we can't rely on the queries in the mod_assign provider we have to add some additional sql.
     *
     * @param  \mod_assign\privacy\useridlist $useridlist An object for obtaining user IDs of students.
     */
    public static function get_student_user_ids(\mod_assign\privacy\useridlist $useridlist) {
        $params = ['assignid' => $useridlist->get_assignid(), 'commentuserid' => $useridlist->get_teacherid(),
                'commentuserid2' => $useridlist->get_teacherid()];
        $sql = "SELECT DISTINCT c.userid AS id
                  FROM {comments} c
                  JOIN (SELECT c.itemid
                          FROM {comments} c
                          JOIN {assign_submission} s ON s.id = c.itemid AND s.assignment = :assignid
                         WHERE c.userid = :commentuserid) aa ON aa.itemid = c.itemid
                 WHERE c.userid NOT IN (:commentuserid2)";
        $useridlist->add_from_sql($sql, $params);
    }

    /**
     * Export all user data for this plugin.
     *
     * @param  assign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_submission_user_data(assign_plugin_request_data $exportdata) {
        $component = 'assignsubmission_comments';
        $commentarea = 'submission_comments';

        $userid = ($exportdata->get_user() != null);
        $submission = $exportdata->get_pluginobject();

        // For the moment we are only showing the comments made by this user.
        comments_provider::export_comments($exportdata->get_context(), $component, $commentarea, $submission->id,
                $exportdata->get_subcontext(), $userid);
    }

    /**
     * Delete all the comments made for this context.
     *
     * @param  assign_plugin_request_data $requestdata Data to fulfill the deletion request.
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        comments_provider::delete_comments_for_all_users($requestdata->get_context(), 'assignsubmission_comments',
                'submission_comments');
    }

    /**
     * A call to this method should delete user data (where practical) using the userid and submission.
     *
     * @param  assign_plugin_request_data $exportdata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $exportdata) {
        // Create an approved context list to delete the comments.
        $contextlist = new \core_privacy\local\request\approved_contextlist($exportdata->get_user(), 'assignsubmission_comments',
            [$exportdata->get_context()->id]);
        comments_provider::delete_comments_for_user($contextlist, 'assignsubmission_comments', 'submission_comments');
    }
}
