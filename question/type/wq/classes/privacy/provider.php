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
 * Privacy class for Wiris Quizzes Common question type.
 *
 * @package    qtype_wq
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_wq\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\writer;

class provider implements
    // This plugin stores personal data.
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    // This trait must be included to provide the relevant polyfill for the metadata provider.
    // All required methods must start with an underscore.
    use \core_privacy\local\legacy_polyfill;


    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function _get_metadata(collection $items) { // @codingStandardsIgnoreLine
        $items->add_database_table(
            'qtype_wq',
            [
                'question' => 'privacy:metadata:qtype_wq:question',
                'xml' => 'privacy:metadata:qtype_wq:xml',
            ],
            'privacy:metadata:qtype_wq'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function _get_contexts_for_userid($userid) { // @codingStandardsIgnoreLine
        global $CFG;

        // Fetch all Wiris Quizzes question types.
        $sql = "SELECT c.id
                FROM {context} c
                INNER JOIN {question_categories} qc ON qc.contextid = c.id";

        if ($CFG->version >= 2022041900) {
            $sql .= " INNER JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
            INNER JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
            INNER JOIN {question} q ON q.id = qv.questionid";
        } else {
            $sql .= " INNER JOIN {question} q ON qc.id = q.category";
        }

        $sql .= " INNER JOIN {qtype_wq} wq ON q.id = wq.question
        WHERE q.createdby = :userid";

        $params = [
            'userid' => $userid
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
    public static function _export_user_data(approved_contextlist $contextlist) { // @codingStandardsIgnoreLine
        global $DB;
        global $CFG;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT c.instanceid instanceid,
                       c.contextlevel contextlevel,
                       wq.question AS question,
                       wq.xml AS xml
                FROM {context} c";

        if ($CFG->version >= 2022041900) {
            $sql .= " INNER JOIN {question_categories} qc ON qc.contextid = c.id
                 INNER JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
                 INNER JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                 INNER JOIN {question} q ON q.id = qv.questionid";
        } else {
            $sql .= " INNER JOIN {question_categories} qc ON qc.contextid = c.id
                     INNER JOIN {question} q ON qc.id = q.category";
        }

        $sql .= " INNER JOIN {qtype_wq} wq ON q.id = wq.question
                  WHERE c.id {$contextsql}
                  AND q.createdby = :userid";

        $params = ['userid' => $user->id] + $contextparams;

        $wirisquestions = $DB->get_recordset_sql($sql, $params);

        // Export user data using writer.
        $systemdata = [];
        $moduledata = [];
        $coursedata = [];

        foreach ($wirisquestions as $wirisquestion) {
            $wirisquestiondata = [
                'question' => $wirisquestion->question,
                'xml' => $wirisquestion->xml
            ];
            // We need to choose the appropiate context because questions exists at different context levels.
            if ($wirisquestion->contextlevel == CONTEXT_SYSTEM) {
                $context = \context_system::instance();
                array_push($systemdata, $wirisquestiondata);
            } else if ($wirisquestion->contextlevel == CONTEXT_MODULE) {
                $context = \context_module::instance($wirisquestion->instanceid);
                array_push($moduledata, $wirisquestiondata);
            } else if ($wirisquestion->contextlevel == CONTEXT_COURSE) {
                $context = \context_course::instance($wirisquestion->instanceid);
                array_push($coursedata, $wirisquestiondata);
            }
        }
        $wirisquestions->close();

        $contextdata = (object)array_merge($systemdata, $moduledata, $coursedata);
        if (!empty($contextdata)) {
            writer::with_context($context)->export_data([], $contextdata);
        }

    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function _delete_data_for_all_users_in_context(\context $context) { // @codingStandardsIgnoreLine
        global $DB;
        global $CFG;

        if (empty($context)) {
            return;
        }

        $sql = "SELECT wq.id
                FROM {question_categories} qc";

        if ($CFG->version >= 2022041900) {
            $sql .= " INNER JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
                      INNER JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                      INNER JOIN {question} q ON q.id = qv.questionid
                      INNER JOIN {qtype_wq} wq ON q.id = wq.question";
        } else {
            $sql .= " INNER JOIN {question} q ON qc.id = q.category
                      INNER JOIN {qtype_wq} wq ON q.id = wq.question";
        }

        $sql .= " WHERE qc.contextid = :contextid";

        $params = ['contextid' => $context->id];

        $records = $DB->get_recordset_sql($sql, $params);
        foreach ($records as $record) {
            $DB->delete_records('qtype_wq', ['id' => $record->id]);
        }

        $records->close();

    }


    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function _delete_data_for_user(approved_contextlist $contextlist) { // @codingStandardsIgnoreLine
        global $DB;
        global $CFG;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            $sql = "SELECT wq.id
                    FROM {question_categories} qc";

            if ($CFG->version >= 2022041900) {
                $sql .= " INNER JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
                          INNER JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                          INNER JOIN {question} q ON q.id = qv.questionid
                          INNER JOIN {qtype_wq} wq ON q.id = wq.question";
            } else {
                $sql .= " INNER JOIN {question} q ON qc.id = q.category
                          INNER JOIN {qtype_wq} wq ON q.id = wq.question";
            }

            $sql .= " WHERE qc.contextid = :contextid AND q.createdby = :userid";

            $params = ['contextid' => $context->id, 'userid' => $userid];

            $records = $DB->get_recordset_sql($sql, $params);
            foreach ($records as $record) {
                $DB->delete_records('qtype_wq', array('id' => $record->id));
            }
            $records->close();
        }
    }
}
