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
 * Privacy Subsystem implementation for core_backup.
 *
 * @package    core_backup
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Privacy Subsystem implementation for core_backup.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\subsystem\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->link_external_location(
            'Backup',
            [
                'detailsofarchive' => 'privacy:metadata:backup:detailsofarchive'
            ],
            'privacy:metadata:backup:externalpurpose'
        );

        $items->add_database_table(
            'backup_controllers',
            [
                'operation' => 'privacy:metadata:backup_controllers:operation',
                'type' => 'privacy:metadata:backup_controllers:type',
                'itemid' => 'privacy:metadata:backup_controllers:itemid',
                'timecreated' => 'privacy:metadata:backup_controllers:timecreated',
                'timemodified' => 'privacy:metadata:backup_controllers:timemodified'
            ],
            'privacy:metadata:backup_controllers'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                  FROM {backup_controllers} bc
                  JOIN {context} ctx
                        ON ctx.instanceid = bc.itemid
                       AND ctx.contextlevel = :contextlevel
                       AND bc.type = :type
                 WHERE bc.userid = :userid";
        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid,
            'type' => 'course',
        ];
        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {backup_controllers} bc
                  JOIN {course_sections} c
                        ON bc.itemid = c.id
                       AND bc.type = :type
                  JOIN {context} ctx
                        ON ctx.instanceid = c.course
                       AND ctx.contextlevel = :contextlevel
                 WHERE bc.userid = :userid";
        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid,
            'type' => 'section',
        ];
        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {backup_controllers} bc
                  JOIN {context} ctx
                        ON ctx.instanceid = bc.itemid
                       AND ctx.contextlevel = :contextlevel
                       AND bc.type = :type
                 WHERE bc.userid = :userid";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
            'type' => 'activity',
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_course) {
            $params = [
                'contextcourse' => CONTEXT_COURSE,
                'contextid' => $context->id,

            ];

            $sql = "SELECT bc.userid
                      FROM {backup_controllers} bc
                      JOIN {context} ctx
                           ON ctx.instanceid = bc.itemid
                           AND ctx.contextlevel = :contextcourse
                     WHERE ctx.id = :contextid
                           AND bc.type = :typecourse";

            $courseparams = ['typecourse' => 'course'] + $params;

            $userlist->add_from_sql('userid', $sql, $courseparams);

            $sql = "SELECT bc.userid
                      FROM {backup_controllers} bc
                      JOIN {course_sections} c
                           ON bc.itemid = c.id
                      JOIN {context} ctx
                           ON ctx.instanceid = c.course
                           AND ctx.contextlevel = :contextcourse
                     WHERE ctx.id = :contextid
                           AND bc.type = :typesection";

            $sectionparams = ['typesection' => 'section'] + $params;

            $userlist->add_from_sql('userid', $sql, $sectionparams);
        }

        if ($context instanceof \context_module) {
            $params = [
                'contextmodule' => CONTEXT_MODULE,
                'contextid' => $context->id,
                'typeactivity' => 'activity'
            ];

            $sql = "SELECT bc.userid
                      FROM {backup_controllers} bc
                      JOIN {context} ctx
                           ON ctx.instanceid = bc.itemid
                           AND ctx.contextlevel = :contextmodule
                     WHERE ctx.id = :contextid
                           AND bc.type = :typeactivity";

            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT bc.*
                  FROM {backup_controllers} bc
                  JOIN {context} ctx
                    ON ctx.instanceid = bc.itemid AND ctx.contextlevel = :contextlevel
                 WHERE ctx.id {$contextsql}
                   AND bc.userid = :userid
              ORDER BY bc.timecreated ASC";
        $params = ['contextlevel' => CONTEXT_COURSE, 'userid' => $user->id] + $contextparams;
        $backupcontrollers = $DB->get_recordset_sql($sql, $params);
        self::recordset_loop_and_export($backupcontrollers, 'itemid', [], function($carry, $record) {
            $carry[] = [
                'operation' => $record->operation,
                'type' => $record->type,
                'itemid' => $record->itemid,
                'timecreated' => transform::datetime($record->timecreated),
                'timemodified' => transform::datetime($record->timemodified),
            ];
            return $carry;
        }, function($courseid, $data) {
            $context = \context_course::instance($courseid);
            $finaldata = (object) $data;
            writer::with_context($context)->export_data([get_string('backup'), $courseid], $finaldata);
        });
    }

    /**
     * Delete all user data which matches the specified context.
     * Only dealing with the specific context - not it's child contexts.
     *
     * @param \context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context instanceof \context_course) {
            $sectionsql = "itemid IN (SELECT id FROM {course_sections} WHERE course = ?) AND type = ?";
            $DB->delete_records_select('backup_controllers', $sectionsql, [$context->instanceid, \backup::TYPE_1SECTION]);
            $DB->delete_records('backup_controllers', ['itemid' => $context->instanceid, 'type' => \backup::TYPE_1COURSE]);
        }
        if ($context instanceof \context_module) {
            $DB->delete_records('backup_controllers', ['itemid' => $context->instanceid, 'type' => \backup::TYPE_1ACTIVITY]);
        }
        return;
    }

    /**
     * Delete multiple users within a single context.
     * Only dealing with the specific context - not it's child contexts.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        if (empty($userlist->get_userids())) {
            return;
        }

        $context = $userlist->get_context();
        if ($context instanceof \context_course) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $select = "itemid = :itemid AND userid {$usersql} AND type = :type";
            $params = $userparams;
            $params['itemid'] = $context->instanceid;
            $params['type'] = \backup::TYPE_1COURSE;

            $DB->delete_records_select('backup_controllers', $select, $params);

            $params = $userparams;
            $params['course'] = $context->instanceid;
            $params['type'] = \backup::TYPE_1SECTION;
            $sectionsql = "itemid IN (SELECT id FROM {course_sections} WHERE course = :course)";
            $select = $sectionsql . " AND userid {$usersql} AND type = :type";
            $DB->delete_records_select('backup_controllers', $select, $params);
        }
        if ($context instanceof \context_module) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $select = "itemid = :itemid AND userid {$usersql} AND type = :type";
            $params = $userparams;
            $params['itemid'] = $context->instanceid;
            $params['type'] = \backup::TYPE_1ACTIVITY;

            // Delete activity backup data.
            $select = "itemid = :itemid AND type = :type AND userid {$usersql}";
            $params = ['itemid' => $context->instanceid, 'type' => 'activity'] + $userparams;
            $DB->delete_records_select('backup_controllers', $select, $params);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     * Only dealing with the specific context - not it's child contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof \context_course) {
                $select = "itemid = :itemid AND userid = :userid AND type = :type";
                $params = [
                    'userid' => $userid,
                    'itemid' => $context->instanceid,
                    'type' => \backup::TYPE_1COURSE
                ];

                $DB->delete_records_select('backup_controllers', $select, $params);

                $params = [
                    'userid' => $userid,
                    'course' => $context->instanceid,
                    'type' => \backup::TYPE_1SECTION
                ];
                $sectionsql = "itemid IN (SELECT id FROM {course_sections} WHERE course = :course)";
                $select = $sectionsql . " AND userid = :userid AND type = :type";
                $DB->delete_records_select('backup_controllers', $select, $params);
            }
            if ($context instanceof \context_module) {
                list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
                $select = "itemid = :itemid AND userid = :userid AND type = :type";
                $params = [
                    'itemid' => $context->instanceid,
                    'userid' => $userid,
                    'type' => \backup::TYPE_1ACTIVITY
                ];

                $DB->delete_records_select('backup_controllers', $select, $params);
            }

        }
    }

    /**
     * Loop and export from a recordset.
     *
     * @param \moodle_recordset $recordset The recordset.
     * @param string $splitkey The record key to determine when to export.
     * @param mixed $initial The initial data to reduce from.
     * @param callable $reducer The function to return the dataset, receives current dataset, and the current record.
     * @param callable $export The function to export the dataset, receives the last value from $splitkey and the dataset.
     * @return void
     */
    protected static function recordset_loop_and_export(\moodle_recordset $recordset, $splitkey, $initial,
            callable $reducer, callable $export) {
        $data = $initial;
        $lastid = null;

        foreach ($recordset as $record) {
            if ($lastid && $record->{$splitkey} != $lastid) {
                $export($lastid, $data);
                $data = $initial;
            }
            $data = $reducer($data, $record);
            $lastid = $record->{$splitkey};
        }
        $recordset->close();

        if (!empty($lastid)) {
            $export($lastid, $data);
        }
    }
}
