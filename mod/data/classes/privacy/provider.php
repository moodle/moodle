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
 * Privacy Subsystem implementation for mod_data.
 *
 * @package    mod_data
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_data\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the database activity module.
 *
 * @package    mod_data
 * @copyright  2018 Marina Glancy
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
     * @param collection $collection a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'data_records',
            [
                'userid' => 'privacy:metadata:data_records:userid',
                'groupid' => 'privacy:metadata:data_records:groupid',
                'timecreated' => 'privacy:metadata:data_records:timecreated',
                'timemodified' => 'privacy:metadata:data_records:timemodified',
                'approved' => 'privacy:metadata:data_records:approved',
            ],
            'privacy:metadata:data_records'
        );
        $collection->add_database_table(
            'data_content',
            [
                'fieldid' => 'privacy:metadata:data_content:fieldid',
                'content' => 'privacy:metadata:data_content:content',
                'content1' => 'privacy:metadata:data_content:content1',
                'content2' => 'privacy:metadata:data_content:content2',
                'content3' => 'privacy:metadata:data_content:content3',
                'content4' => 'privacy:metadata:data_content:content4',
            ],
            'privacy:metadata:data_content'
        );

        // Link to subplugins.
        $collection->add_plugintype_link('datafield', [], 'privacy:metadata:datafieldnpluginsummary');

        // Subsystems used.
        $collection->link_subsystem('core_comment', 'privacy:metadata:commentpurpose');
        $collection->link_subsystem('core_files', 'privacy:metadata:filepurpose');
        $collection->link_subsystem('core_tag', 'privacy:metadata:tagpurpose');
        $collection->link_subsystem('core_rating', 'privacy:metadata:ratingpurpose');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // Fetch all data records.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {data} d ON d.id = cm.instance
            INNER JOIN {data_records} dr ON dr.dataid = d.id
             LEFT JOIN {comments} com ON com.commentarea=:commentarea and com.itemid = dr.id AND com.userid = :userid1
             LEFT JOIN {rating} r ON r.contextid = c.id AND r.itemid  = dr.id AND r.component = :moddata
                       AND r.ratingarea = :ratingarea AND r.userid = :userid2
                 WHERE dr.userid = :userid OR com.id IS NOT NULL OR r.id IS NOT NULL";

        $params = [
            'modname'       => 'data',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
            'userid1'       => $userid,
            'userid2'       => $userid,
            'commentarea'   => 'database_entry',
            'moddata'       => 'mod_data',
            'ratingarea'    => 'entry',
        ];
        $contextlist = new contextlist();
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

        // Find users with data records.
        $sql = "SELECT dr.userid
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {data} d ON d.id = cm.instance
                  JOIN {data_records} dr ON dr.dataid = d.id
                 WHERE c.id = :contextid";

        $params = [
            'modname'       => 'data',
            'contextid'     => $context->id,
            'contextlevel'  => CONTEXT_MODULE,
        ];

        $userlist->add_from_sql('userid', $sql, $params);

        // Find users with comments.
        \core_comment\privacy\provider::get_users_in_context_from_sql($userlist, 'com', 'mod_data', 'database_entry', $context->id);

        // Find users with ratings.
        $sql = "SELECT dr.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {data} d ON d.id = cm.instance
                  JOIN {data_records} dr ON dr.dataid = d.id
                 WHERE c.id = :contextid";

        $params = [
            'modname'       => 'data',
            'contextid'     => $context->id,
            'contextlevel'  => CONTEXT_MODULE,
        ];

        \core_rating\privacy\provider::get_users_in_context_from_sql($userlist, 'rat', 'mod_data', 'entry', $sql, $params);
    }

    /**
     * Creates an object from all fields in the $record where key starts with $prefix
     *
     * @param \stdClass $record
     * @param string $prefix
     * @param array $additionalfields
     * @return \stdClass
     */
    protected static function extract_object_from_record($record, $prefix, $additionalfields = []) {
        $object = new \stdClass();
        foreach ($record as $key => $value) {
            if (preg_match('/^'.preg_quote($prefix, '/').'(.*)/', $key, $matches)) {
                $object->{$matches[1]} = $value;
            }
        }
        if ($additionalfields) {
            foreach ($additionalfields as $key => $value) {
                $object->$key = $value;
            }
        }
        return $object;
    }

    /**
     * Export one field answer in a record in database activity module
     *
     * @param \context $context
     * @param \stdClass $recordobj record from DB table {data_records}
     * @param \stdClass $fieldobj record from DB table {data_fields}
     * @param \stdClass $contentobj record from DB table {data_content}
     */
    protected static function export_data_content($context, $recordobj, $fieldobj, $contentobj) {
        $value = (object)[
            'field' => [
                // Name and description are displayed in mod_data without applying format_string().
                'name' => $fieldobj->name,
                'description' => $fieldobj->description,
                'type' => $fieldobj->type,
                'required' => transform::yesno($fieldobj->required),
            ],
            'content' => $contentobj->content
        ];
        foreach (['content1', 'content2', 'content3', 'content4'] as $key) {
            if ($contentobj->$key !== null) {
                $value->$key = $contentobj->$key;
            }
        }
        $classname = manager::get_provider_classname_for_component('datafield_' . $fieldobj->type);
        if (class_exists($classname) && is_subclass_of($classname, datafield_provider::class)) {
            component_class_callback($classname, 'export_data_content',
                [$context, $recordobj, $fieldobj, $contentobj, $value]);
        } else {
            // Data field plugin does not implement datafield_provider, just export default value.
            writer::with_context($context)->export_data([$recordobj->id, $contentobj->id], $value);
        }
        writer::with_context($context)->export_area_files([$recordobj->id, $contentobj->id], 'mod_data',
            'content', $contentobj->id);
    }

    /**
     * SQL query that returns all fields from {data_content}, {data_fields} and {data_records} tables
     *
     * @return string
     */
    protected static function sql_fields() {
        return 'd.id AS dataid, dc.id AS contentid, dc.fieldid, df.type AS fieldtype, df.name AS fieldname,
                  df.description AS fielddescription, df.required AS fieldrequired,
                  df.param1 AS fieldparam1, df.param2 AS fieldparam2, df.param3 AS fieldparam3, df.param4 AS fieldparam4,
                  df.param5 AS fieldparam5, df.param6 AS fieldparam6, df.param7 AS fieldparam7, df.param8 AS fieldparam8,
                  df.param9 AS fieldparam9, df.param10 AS fieldparam10,
                  dc.content AS contentcontent, dc.content1 AS contentcontent1, dc.content2 AS contentcontent2,
                  dc.content3 AS contentcontent3, dc.content4 AS contentcontent4,
                  dc.recordid, dr.timecreated AS recordtimecreated, dr.timemodified AS recordtimemodified,
                  dr.approved AS recordapproved, dr.groupid AS recordgroupid, dr.userid AS recorduserid';
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id AS cmid, d.name AS dataname, cm.course AS courseid, " . self::sql_fields() . "
                FROM {context} ctx
                JOIN {course_modules} cm ON cm.id = ctx.instanceid
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {data} d ON d.id = cm.instance
                JOIN {data_records} dr ON dr.dataid = d.id
                JOIN {data_content} dc ON dc.recordid = dr.id
                JOIN {data_fields} df ON df.id = dc.fieldid
                WHERE ctx.id {$contextsql} AND ctx.contextlevel = :contextlevel
                AND dr.userid = :userid OR
                  EXISTS (SELECT 1 FROM {comments} com WHERE com.commentarea=:commentarea
                    AND com.itemid = dr.id AND com.userid = :userid1) OR
                  EXISTS (SELECT 1 FROM {rating} r WHERE r.contextid = ctx.id AND r.itemid  = dr.id AND r.component = :moddata
                    AND r.ratingarea = :ratingarea AND r.userid = :userid2)
                ORDER BY cm.id, dr.id, dc.fieldid";
        $rs = $DB->get_recordset_sql($sql, $contextparams + ['contextlevel' => CONTEXT_MODULE,
                'modname' => 'data', 'userid' => $user->id, 'userid1' => $user->id, 'commentarea' => 'database_entry',
                'userid2' => $user->id, 'ratingarea' => 'entry', 'moddata' => 'mod_data']);

        $context = null;
        $recordobj = null;
        foreach ($rs as $row) {
            if (!$context || $context->instanceid != $row->cmid) {
                // This row belongs to the different data module than the previous row.
                // Export the data for the previous module.
                self::export_data($context, $user);
                // Start new data module.
                $context = \context_module::instance($row->cmid);
            }

            if (!$recordobj || $row->recordid != $recordobj->id) {
                // Export previous data record.
                self::export_data_record($context, $user, $recordobj);
                // Prepare for exporting new data record.
                $recordobj = self::extract_object_from_record($row, 'record', ['dataid' => $row->dataid]);
            }
            $fieldobj = self::extract_object_from_record($row, 'field', ['dataid' => $row->dataid]);
            $contentobj = self::extract_object_from_record($row, 'content',
                ['fieldid' => $fieldobj->id, 'recordid' => $recordobj->id]);
            self::export_data_content($context, $recordobj, $fieldobj, $contentobj);
        }
        $rs->close();
        self::export_data_record($context, $user, $recordobj);
        self::export_data($context, $user);
    }

    /**
     * Export one entry in the database activity module (one record in {data_records} table)
     *
     * @param \context $context
     * @param \stdClass $user
     * @param \stdClass $recordobj
     */
    protected static function export_data_record($context, $user, $recordobj) {
        if (!$recordobj) {
            return;
        }
        $data = [
            'userid' => transform::user($user->id),
            'groupid' => $recordobj->groupid,
            'timecreated' => transform::datetime($recordobj->timecreated),
            'timemodified' => transform::datetime($recordobj->timemodified),
            'approved' => transform::yesno($recordobj->approved),
        ];
        // Data about the record.
        writer::with_context($context)->export_data([$recordobj->id], (object)$data);
        // Related tags.
        \core_tag\privacy\provider::export_item_tags($user->id, $context, [$recordobj->id],
            'mod_data', 'data_records', $recordobj->id);
        // Export comments. For records that were not made by this user export only this user's comments, for own records
        // export comments made by everybody.
        \core_comment\privacy\provider::export_comments($context, 'mod_data', 'database_entry', $recordobj->id,
            [$recordobj->id], $recordobj->userid != $user->id);
        // Export ratings. For records that were not made by this user export only this user's ratings, for own records
        // export ratings from everybody.
        \core_rating\privacy\provider::export_area_ratings($user->id, $context, [$recordobj->id], 'mod_data', 'entry',
            $recordobj->id, $recordobj->userid != $user->id);
    }

    /**
     * Export basic info about database activity module
     *
     * @param \context $context
     * @param \stdClass $user
     */
    protected static function export_data($context, $user) {
        if (!$context) {
            return;
        }
        $contextdata = helper::get_context_data($context, $user);
        helper::export_context_files($context, $user);
        writer::with_context($context)->export_data([], $contextdata);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }
        $recordstobedeleted = [];

        $sql = "SELECT " . self::sql_fields() . "
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {data} d ON d.id = cm.instance
                JOIN {data_records} dr ON dr.dataid = d.id
                LEFT JOIN {data_content} dc ON dc.recordid = dr.id
                LEFT JOIN {data_fields} df ON df.id = dc.fieldid
                WHERE cm.id = :cmid
                ORDER BY dr.id";
        $rs = $DB->get_recordset_sql($sql, ['cmid' => $context->instanceid, 'modname' => 'data']);
        foreach ($rs as $row) {
            self::mark_data_content_for_deletion($context, $row);
            $recordstobedeleted[$row->recordid] = $row->recordid;
        }
        $rs->close();

        self::delete_data_records($context, $recordstobedeleted);
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

        $user = $contextlist->get_user();
        $recordstobedeleted = [];

        foreach ($contextlist->get_contexts() as $context) {
            $sql = "SELECT " . self::sql_fields() . "
                FROM {context} ctx
                JOIN {course_modules} cm ON cm.id = ctx.instanceid
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {data} d ON d.id = cm.instance
                JOIN {data_records} dr ON dr.dataid = d.id AND dr.userid = :userid
                LEFT JOIN {data_content} dc ON dc.recordid = dr.id
                LEFT JOIN {data_fields} df ON df.id = dc.fieldid
                WHERE ctx.id = :ctxid AND ctx.contextlevel = :contextlevel
                ORDER BY dr.id";
            $rs = $DB->get_recordset_sql($sql, ['ctxid' => $context->id, 'contextlevel' => CONTEXT_MODULE,
                'modname' => 'data', 'userid' => $user->id]);
            foreach ($rs as $row) {
                self::mark_data_content_for_deletion($context, $row);
                $recordstobedeleted[$row->recordid] = $row->recordid;
            }
            $rs->close();
            self::delete_data_records($context, $recordstobedeleted);
        }

        // Additionally remove comments this user made on other entries.
        \core_comment\privacy\provider::delete_comments_for_user($contextlist, 'mod_data', 'database_entry');

        // We do not delete ratings made by this user on other records because it may change grades.
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist    $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $recordstobedeleted = [];
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        $sql = "SELECT " . self::sql_fields() . "
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {data} d ON d.id = cm.instance
                  JOIN {data_records} dr ON dr.dataid = d.id AND dr.userid {$userinsql}
             LEFT JOIN {data_content} dc ON dc.recordid = dr.id
             LEFT JOIN {data_fields} df ON df.id = dc.fieldid
                 WHERE ctx.id = :ctxid AND ctx.contextlevel = :contextlevel
              ORDER BY dr.id";

        $params = [
            'ctxid' => $context->id,
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'data',
        ];
        $params += $userinparams;

        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $row) {
            self::mark_data_content_for_deletion($context, $row);
            $recordstobedeleted[$row->recordid] = $row->recordid;
        }
        $rs->close();

        self::delete_data_records($context, $recordstobedeleted);

        // Additionally remove comments these users made on other entries.
        \core_comment\privacy\provider::delete_comments_for_users($userlist, 'mod_data', 'database_entry');

        // We do not delete ratings made by users on other records because it may change grades.
    }

    /**
     * Marks a data_record/data_content for deletion
     *
     * Also invokes callback from datafield plugin in case it stores additional data that needs to be deleted
     *
     * @param \context $context
     * @param \stdClass $row result of SQL query - tables data_content, data_record, data_fields join together
     */
    protected static function mark_data_content_for_deletion($context, $row) {
        $recordobj = self::extract_object_from_record($row, 'record', ['dataid' => $row->dataid]);
        if ($row->contentid && $row->fieldid) {
            $fieldobj = self::extract_object_from_record($row, 'field', ['dataid' => $row->dataid]);
            $contentobj = self::extract_object_from_record($row, 'content',
                ['fieldid' => $fieldobj->id, 'recordid' => $recordobj->id]);

            // Allow datafield plugin to implement their own deletion.
            $classname = manager::get_provider_classname_for_component('datafield_' . $fieldobj->type);
            if (class_exists($classname) && is_subclass_of($classname, datafield_provider::class)) {
                component_class_callback($classname, 'delete_data_content',
                    [$context, $recordobj, $fieldobj, $contentobj]);
            }
        }
    }

    /**
     * Deletes records marked for deletion and all associated data
     *
     * Should be executed after all records were marked by {@link mark_data_content_for_deletion()}
     *
     * Deletes records from data_content and data_records tables, associated files, tags, comments and ratings.
     *
     * @param \context $context
     * @param array $recordstobedeleted list of ids of the data records that need to be deleted
     */
    protected static function delete_data_records($context, $recordstobedeleted) {
        global $DB;
        if (empty($recordstobedeleted)) {
            return;
        }

        list($sql, $params) = $DB->get_in_or_equal($recordstobedeleted, SQL_PARAMS_NAMED);

        // Delete files.
        get_file_storage()->delete_area_files_select($context->id, 'mod_data', 'data_records',
            "IN (SELECT dc.id FROM {data_content} dc WHERE dc.recordid $sql)", $params);
        // Delete from data_content.
        $DB->delete_records_select('data_content', 'recordid ' . $sql, $params);
        // Delete from data_records.
        $DB->delete_records_select('data_records', 'id ' . $sql, $params);
        // Delete tags.
        \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_data', 'data_records', $sql, $params);
        // Delete comments.
        \core_comment\privacy\provider::delete_comments_for_all_users_select($context, 'mod_data', 'database_entry', $sql, $params);
        // Delete ratings.
        \core_rating\privacy\provider::delete_ratings_select($context, 'mod_data', 'entry', $sql, $params);
    }
}
