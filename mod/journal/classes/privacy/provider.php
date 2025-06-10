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
 * Data provider.
 *
 * @package    mod_journal
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_journal\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

require_once($CFG->dirroot . '/mod/journal/lib.php');

/**
 * The provider class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'journal_entries',
             [
                'userid' => 'privacy:metadata:journal_entries:userid',
                'modified' => 'privacy:metadata:journal_entries:modified',
                'text' => 'privacy:metadata:journal_entries:text',
                'rating' => 'privacy:metadata:journal_entries:rating',
                'entrycomment' => 'privacy:metadata:journal_entries:entrycomment',
                'teacher' => 'privacy:metadata:journal_entries:teacher',
             ],
            'privacy:metadata:journal_entries'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {

        $sql = "
            SELECT DISTINCT ctx.id
              FROM {journal} j
              JOIN {modules} m
                ON m.name = :journal
              JOIN {course_modules} cm
                ON cm.instance = j.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
         LEFT JOIN {journal_entries} je
                ON je.journal = j.id
               AND je.userid = :userid";

        $params = ['journal' => 'journal', 'modulelevel' => CONTEXT_MODULE, 'userid' => $userid];
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

        // Find users with journal entries.
        $sql = "
            SELECT j.userid
              FROM {journal} j
              JOIN {modules} m
                ON m.name = :journal
              JOIN {course_modules} cm
                ON cm.instance = j.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
             WHERE ctx.id = :contextid";
        $params = ['journal' => 'journal', 'modulelevel' => CONTEXT_MODULE, 'contextid' => $context->id];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();
        $userid = $user->id;
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);
        if (empty($cmids)) {
            return;
        }

        // If the context export was requested, then let's at least describe the journal.
        foreach ($cmids as $cmid) {
            $context = context_module::instance($cmid);
            $contextdata = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);
            writer::with_context($context)->export_data([], $contextdata);
        }

        // Find the journal IDs.
        $journalidstocmids = static::get_journal_ids_to_cmids_from_cmids($cmids);

        // Prepare the common SQL fragments.
        list($injournalsql, $injournalparams) = $DB->get_in_or_equal(array_keys($journalidstocmids), SQL_PARAMS_NAMED);
        $sqluserjournal = "userid = :userid AND journal $injournalsql";
        $paramsuserjournal = array_merge($injournalparams, ['userid' => $userid]);

        // Export the entries.
        $recordset = $DB->get_recordset_select('journal_entries', $sqluserjournal, $paramsuserjournal);
        static::recordset_loop_and_export($recordset, 'journal', null, function($carry, $record) {
            // We know that there is only one row per journal, so no need to use $carry.
            return (object) [
                'modified' => $record->modified !== null ? transform::datetime($record->modified) : null,
                'text' => $record->text,
                'rating' => $record->rating,
                'entrycomment' => $record->entrycomment,
                'teacher' => $record->teacher,
                'timemarked' => $record->timemarked !== null ? transform::datetime($record->timemarked) : null,
            ];
        }, function($journalid, $data) use ($journalidstocmids) {
            $context = context_module::instance($journalidstocmids[$journalid]);
            writer::with_context($context)->export_related_data([], 'entries', $data);
        });
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        if (!$journalid = static::get_journal_id_from_context($context)) {
            return;
        }

        $DB->delete_records('journal_entries', ['journal' => $journalid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);
        if (empty($cmids)) {
            return;
        }

        // Find the journal IDs.
        $journalidstocmids = static::get_journal_ids_to_cmids_from_cmids($cmids);
        $journalids = array_keys($journalidstocmids);
        if (empty($journalids)) {
            return;
        }

        // Prepare the SQL we'll need below.
        list($insql, $inparams) = $DB->get_in_or_equal($journalids, SQL_PARAMS_NAMED);
        $sql = "journal $insql AND userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        $DB->delete_records_select('journal_entries', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist    $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $journalid = static::get_journal_id_from_context($context);
        $userids = $userlist->get_userids();

        if (empty($journalid)) {
            return;
        }

        // Prepare the SQL we'll need below.
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "journal = :journalid AND userid {$insql}";
        $params = array_merge($inparams, ['journalid' => $journalid]);
        $DB->delete_records_select('journal_entries', $sql, $params);

    }

    /**
     * Get a journal ID from its context.
     *
     * @param context_module $context The module context.
     * @return int
     */
    protected static function get_journal_id_from_context(context_module $context) {
        $cm = get_coursemodule_from_id('journal', $context->instanceid);
        return $cm ? (int) $cm->instance : 0;
    }

    /**
     * Return a dict of journal IDs mapped to their course module ID.
     *
     * @param array $cmids The course module IDs.
     * @return array In the form of [$journalid => $cmid].
     */
    protected static function get_journal_ids_to_cmids_from_cmids(array $cmids) {
        global $DB;
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT j.id, cm.id AS cmid
              FROM {journal} j
              JOIN {modules} m
                ON m.name = :journal
              JOIN {course_modules} cm
                ON cm.instance = j.id
               AND cm.module = m.id
             WHERE cm.id $insql";
        $params = array_merge($inparams, ['journal' => 'journal']);
        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Loop and export from a recordset.
     *
     * @param moodle_recordset $recordset The recordset.
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
