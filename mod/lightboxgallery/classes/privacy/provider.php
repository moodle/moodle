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
 * Privacy Subsystem implementation for mod_lightboxgallery.
 *
 * @package    mod_lightboxgallery
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lightboxgallery\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the lightboxgallery activity module.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'lightboxgallery_comments',
            [
                'gallery' => 'privacy:metadata:lightboxgallery_comments:gallery',
                'userid' => 'privacy:metadata:lightboxgallery_comments:userid',
                'commenttext' => 'privacy:metadata:lightboxgallery_comments:commenttext',
                'timemodified' => 'privacy:metadata:lightboxgallery_comments:timemodified',
            ],
            'privacy:metadata:lightboxgallery_comments'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // Fetch all lightboxgallery comments.
        $sql = "SELECT c.id
                FROM {context} c
                JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {lightboxgallery} lbg ON lbg.id = cm.instance
                JOIN {lightboxgallery_comments} lbgc ON lbgc.gallery = lbg.id
                WHERE lbgc.userid = :userid";

        $params = [
            'modname'       => 'lightboxgallery',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       lbgc.commenttext,
                       lbgc.timemodified
                FROM {context} c
                JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                JOIN {modules} m ON m.id = cm.module
                JOIN {lightboxgallery} lbg ON lbg.id = cm.instance
                JOIN {lightboxgallery_comments} lbgc ON lbgc.gallery = lbg.id
                WHERE c.id {$contextsql}
                  AND lbgc.userid = :userid
                ORDER BY cm.id";

        $params = ['userid' => $user->id, 'contextlevel' => CONTEXT_MODULE] + $contextparams;

        // Reference to the lightboxgallery activity seen in the last iteration
        // of the loop. By comparing this with the current record, and because
        // we know the results are ordered, we know when we've moved to the
        // answers for a new lightboxgallery activity and therefore when we can
        // export the complete data for the last activity.
        $lastcmid = null;

        $lbgcomments = $DB->get_recordset_sql($sql, $params);
        foreach ($lbgcomments as $comment) {
            if ($lastcmid != $comment->cmid) {
                if (!empty($commentdata)) {
                    $context = \context_module::instance($lastcmid);
                    self::export_lightboxgallery_data_for_user($commentdata, $context, $user);
                }
                $commentdata = [];
            }
            $commentdata['comments'][] = [
                'commenttext' => $comment->commenttext,
                'timemodified' => \core_privacy\local\request\transform::datetime($comment->timemodified),
            ];
            $lastcmid = $comment->cmid;
        }
        $lbgcomments->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($commentdata)) {
            $context = \context_module::instance($lastcmid);
            self::export_lightboxgallery_data_for_user($commentdata, $context, $user);
        }
    }

    /**
     * Export the supplied personal data for a single lightboxgallery activity, along with any generic data or area files.
     *
     * @param array $commentdata the personal data to export for the lightboxgallery.
     * @param \context_module $context the context of the lightboxgallery.
     * @param \stdClass $user the user record
     */
    protected static function export_lightboxgallery_data_for_user(array $commentdata, \context_module $context, \stdClass $user) {
        // Fetch the generic module data for the lightboxgallery.
        $contextdata = helper::get_context_data($context, $user);

        // Merge with lightboxgallery data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $commentdata);
        writer::with_context($context)->export_data([], $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     *
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $sql = "SELECT lbgc.userid
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {lightboxgallery} lbg ON lbg.id = cm.instance
                JOIN {lightboxgallery_comments} lbgc ON lbgc.gallery = lbg.id
                WHERE cm.id = :instanceid";

        $params = [
            'modname'       => 'lightboxgallery',
            'instanceid'    => $context->instanceid,
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }

        if (!$context instanceof \context_module) {
            return;
        }

        $instanceid = self::get_lightboxgallery_id_from_context($context);
        if (empty($instanceid)) {
            return;
        }

        $DB->delete_records('lightboxgallery_comments', ['gallery' => $instanceid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $instanceid = self::get_lightboxgallery_id_from_context($context);
        $userids = $userlist->get_userids();

        if (empty($instanceid)) {
            return;
        }

        // Prepare the SQL we'll need below.
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "gallery = :instanceid AND userid {$insql}";
        $params = array_merge($inparams, ['instanceid' => $instanceid]);

        $DB->delete_records_select('lightboxgallery_comments', $sql, $params);
    }

    protected static function get_lightboxgallery_id_from_context(\context_module $context) {
        $cm = get_coursemodule_from_id('lightboxgallery', $context->instanceid);
        return $cm ? (int) $cm->instance : 0;
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
            $DB->delete_records('lightboxgallery_comments', ['gallery' => $instanceid, 'userid' => $userid]);
        }
    }
}
