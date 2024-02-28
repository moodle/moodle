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
 * Privacy Subsystem implementation for mod_glossary.
 *
 * @package   mod_glossary
 * @copyright 2018 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_glossary\privacy;
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
 * Implementation of the privacy subsystem plugin provider for the glossary activity module.
 *
 * @copyright 2018 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin stores personal data.
    \core_privacy\local\metadata\provider,
    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,
    // This plugin is a core_user_data_provider.
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_database_table(
            'glossary_entries',
            [
                'glossaryid'    => 'privacy:metadata:glossary_entries:glossaryid',
                'userid'        => 'privacy:metadata:glossary_entries:userid',
                'concept'       => 'privacy:metadata:glossary_entries:concept',
                'definition'    => 'privacy:metadata:glossary_entries:definition',
                'attachment'    => 'privacy:metadata:glossary_entries:attachment',
                'timemodified'  => 'privacy:metadata:glossary_entries:timemodified',
            ],
            'privacy:metadata:glossary_entries'
        );

        $items->add_subsystem_link('core_files', [], 'privacy:metadata:core_files');
        $items->add_subsystem_link('core_comment', [], 'privacy:metadata:core_comments');
        $items->add_subsystem_link('core_tag', [], 'privacy:metadata:core_tag');
        $items->add_subsystem_link('core_rating', [], 'privacy:metadata:core_rating');
        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Glossary entries.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON ge.glossaryid = g.id
                 WHERE ge.userid = :glossaryentryuserid";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'glossary',
            'commentarea' => 'glossary_entry',
            'glossaryentryuserid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Where the user has rated something.
        $ratingquery = \core_rating\privacy\provider::get_sql_join('r', 'mod_glossary', 'entry', 'ge.id', $userid, true);
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON ge.glossaryid = g.id
            {$ratingquery->join}
                 WHERE {$ratingquery->userwhere}";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'glossary',
        ] + $ratingquery->params;
        $contextlist->add_from_sql($sql, $params);

        // Comments.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON ge.glossaryid = g.id
                  JOIN {comments} com ON com.commentarea =:commentarea AND com.itemid = ge.id
                 WHERE com.userid = :commentuserid";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'glossary',
            'commentarea' => 'glossary_entry',
            'commentuserid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     *
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        // Find users with glossary entries.
        $sql = "SELECT ge.userid
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON ge.glossaryid = g.id
                 WHERE c.id = :contextid";

        $params = [
            'contextid' => $context->id,
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'glossary',
        ];

        $userlist->add_from_sql('userid', $sql, $params);

        // Find users with glossary comments.
        \core_comment\privacy\provider::get_users_in_context_from_sql($userlist, 'com', 'mod_glossary', 'glossary_entry',
                $context->id);

        // Find users with glossary ratings.
        $sql = "SELECT ge.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON ge.glossaryid = g.id
                 WHERE c.id = :contextid";

        $params = [
            'contextid' => $context->id,
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'glossary',
        ];

        \core_rating\privacy\provider::get_users_in_context_from_sql($userlist, 'rat', 'mod_glossary', 'entry', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist.
     *
     * User and context information is contained within the contextlist.
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
        $ratingquery = \core_rating\privacy\provider::get_sql_join('r', 'mod_glossary', 'entry', 'ge.id', $user->id);

        $sql = "SELECT ge.id as entryid,
                       cm.id AS cmid,
                       ge.userid,
                       ge.concept,
                       ge.definition,
                       ge.definitionformat,
                       ge.attachment,
                       ge.timecreated,
                       ge.timemodified
                  FROM {glossary_entries} ge
                  JOIN {glossary} g ON ge.glossaryid = g.id
                  JOIN {course_modules} cm ON g.id = cm.instance
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {context} c ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
             LEFT JOIN {comments} com ON com.itemid = ge.id AND com.commentarea = :commentarea AND com.userid = :commentuserid
             {$ratingquery->join}
                 WHERE c.id {$contextsql}
                   AND (ge.userid = :userid OR com.id IS NOT NULL OR {$ratingquery->userwhere})
               ORDER BY ge.id, cm.id";
        $params = [
                'userid' => $user->id,
                'modulename' => 'glossary',
                'contextlevel' => CONTEXT_MODULE,
                'commentarea' => 'glossary_entry',
                'commentuserid' => $user->id
            ] + $contextparams;
        $params = array_merge($params, $ratingquery->params);
        $glossaryentries = $DB->get_recordset_sql($sql, $params);

        // Reference to the glossary activity seen in the last iteration of the loop. By comparing this with the
        // current record, and because we know the results are ordered, we know when we've moved to the entries
        // for a new glossary activity and therefore when we can export the complete data for the last activity.
        $lastcmid = null;

        $glossarydata = [];
        foreach ($glossaryentries as $record) {
            $concept = format_string($record->concept);
            $path = array_merge([get_string('entries', 'mod_glossary'), $concept . " ({$record->entryid})"]);

            // If we've moved to a new glossary, then write the last glossary data and reinit the glossary data array.
            if (!is_null($lastcmid)) {
                if ($lastcmid != $record->cmid) {
                    if (!empty($glossarydata)) {
                        $context = \context_module::instance($lastcmid);
                        self::export_glossary_data_for_user($glossarydata, $context, [], $user);
                        $glossarydata = [];
                    }
                }
            }
            $lastcmid = $record->cmid;
            $context = \context_module::instance($lastcmid);

            // Export files added on the glossary entry definition field.
            $definition = format_text(writer::with_context($context)->rewrite_pluginfile_urls($path, 'mod_glossary',
                'entry',  $record->entryid, $record->definition), $record->definitionformat);

            // Export just the files attached to this user entry.
            if ($record->userid == $user->id) {
                // Get all files attached to the glossary attachment.
                writer::with_context($context)->export_area_files($path, 'mod_glossary', 'entry', $record->entryid);

                // Get all files attached to the glossary attachment.
                writer::with_context($context)->export_area_files($path, 'mod_glossary', 'attachment', $record->entryid);
            }

            // Export associated comments.
            \core_comment\privacy\provider::export_comments($context, 'mod_glossary', 'glossary_entry',
                    $record->entryid, $path, $record->userid != $user->id);

            // Export associated tags.
            \core_tag\privacy\provider::export_item_tags($user->id, $context, $path, 'mod_glossary', 'glossary_entries',
                    $record->entryid, $record->userid != $user->id);

            // Export associated ratings.
            \core_rating\privacy\provider::export_area_ratings($user->id, $context, $path, 'mod_glossary', 'entry',
                    $record->entryid, $record->userid != $user->id);

            $glossarydata['entries'][] = [
                'concept'       => $record->concept,
                'definition'    => $definition,
                'timecreated'   => \core_privacy\local\request\transform::datetime($record->timecreated),
                'timemodified'  => \core_privacy\local\request\transform::datetime($record->timemodified)
            ];
        }
        $glossaryentries->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($glossarydata)) {
            $context = \context_module::instance($lastcmid);
            self::export_glossary_data_for_user($glossarydata, $context, [], $user);
        }
    }

    /**
     * Export the supplied personal data for a single glossary activity, along with any generic data or area files.
     *
     * @param array $glossarydata The personal data to export for the glossary.
     * @param \context_module $context The context of the glossary.
     * @param array $subcontext The location within the current context that this data belongs.
     * @param \stdClass $user the user record
     */
    protected static function export_glossary_data_for_user(array $glossarydata, \context_module $context,
                                                            array $subcontext, \stdClass $user) {
        // Fetch the generic module data for the glossary.
        $contextdata = helper::get_context_data($context, $user);
        // Merge with glossary data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $glossarydata);
        writer::with_context($context)->export_data($subcontext, $contextdata);
        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        if (!$cm = get_coursemodule_from_id('glossary', $context->instanceid)) {
            return;
        }

        $instanceid = $cm->instance;

        $entries = $DB->get_records('glossary_entries', ['glossaryid' => $instanceid], '', 'id');

        // Delete related entry aliases.
        $DB->delete_records_list('glossary_alias', 'entryid', array_keys($entries));

        // Delete related entry categories.
        $DB->delete_records_list('glossary_entries_categories', 'entryid', array_keys($entries));

        // Delete entry and attachment files.
        get_file_storage()->delete_area_files($context->id, 'mod_glossary', 'entry');
        get_file_storage()->delete_area_files($context->id, 'mod_glossary', 'attachment');

        // Delete related ratings.
        \core_rating\privacy\provider::delete_ratings($context, 'mod_glossary', 'entry');

        // Delete comments.
        \core_comment\privacy\provider::delete_comments_for_all_users($context, 'mod_glossary', 'glossary_entry');

        // Delete tags.
        \core_tag\privacy\provider::delete_item_tags($context, 'mod_glossary', 'glossary_entries');

        // Now delete all user related entries.
        $DB->delete_records('glossary_entries', ['glossaryid' => $instanceid]);
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
            if ($context->contextlevel == CONTEXT_MODULE) {

                $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);

                $entries = $DB->get_records('glossary_entries', ['glossaryid' => $instanceid, 'userid' => $userid],
                        '', 'id');

                if (!$entries) {
                    continue;
                }

                list($insql, $inparams) = $DB->get_in_or_equal(array_keys($entries), SQL_PARAMS_NAMED);
                // Delete related entry aliases.
                $DB->delete_records_list('glossary_alias', 'entryid', array_keys($entries));

                // Delete related entry categories.
                $DB->delete_records_list('glossary_entries_categories', 'entryid', array_keys($entries));

                // Delete related entry and attachment files.
                get_file_storage()->delete_area_files_select($context->id, 'mod_glossary', 'entry', $insql, $inparams);
                get_file_storage()->delete_area_files_select($context->id, 'mod_glossary', 'attachment', $insql, $inparams);

                // Delete user tags related to this glossary.
                \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_glossary', 'glossary_entries', $insql, $inparams);

                // Delete related ratings.
                \core_rating\privacy\provider::delete_ratings_select($context, 'mod_glossary', 'entry', $insql, $inparams);

                // Delete comments.
                \core_comment\privacy\provider::delete_comments_for_user($contextlist, 'mod_glossary', 'glossary_entry');

                // Now delete all user related entries.
                $DB->delete_records('glossary_entries', ['glossaryid' => $instanceid, 'userid' => $userid]);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist    $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();
        $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $glossaryentrieswhere = "glossaryid = :instanceid AND userid {$userinsql}";
        $userinstanceparams = $userinparams + ['instanceid' => $instanceid];

        $entriesobject = $DB->get_recordset_select('glossary_entries', $glossaryentrieswhere, $userinstanceparams, 'id', 'id');
        $entries = [];

        foreach ($entriesobject as $entry) {
            $entries[] = $entry->id;
        }

        $entriesobject->close();

        if (!$entries) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($entries, SQL_PARAMS_NAMED);

        // Delete related entry aliases.
        $DB->delete_records_list('glossary_alias', 'entryid', $entries);

        // Delete related entry categories.
        $DB->delete_records_list('glossary_entries_categories', 'entryid', $entries);

        // Delete related entry and attachment files.
        get_file_storage()->delete_area_files_select($context->id, 'mod_glossary', 'entry', $insql, $inparams);
        get_file_storage()->delete_area_files_select($context->id, 'mod_glossary', 'attachment', $insql, $inparams);

        // Delete user tags related to this glossary.
        \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_glossary', 'glossary_entries', $insql, $inparams);

        // Delete related ratings.
        \core_rating\privacy\provider::delete_ratings_select($context, 'mod_glossary', 'entry', $insql, $inparams);

        // Delete comments.
        \core_comment\privacy\provider::delete_comments_for_users($userlist, 'mod_glossary', 'glossary_entry');

        // Now delete all user related entries.
        $deletewhere = "glossaryid = :instanceid AND userid {$userinsql}";
        $DB->delete_records_select('glossary_entries', $deletewhere, $userinstanceparams);
    }
}
