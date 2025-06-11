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
 * @package    mod_survey
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_survey\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_helper;
use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

require_once($CFG->dirroot . '/mod/survey/lib.php');

/**
 * Data provider class.
 *
 * @package    mod_survey
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
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
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('survey_answers', [
            'userid' => 'privacy:metadata:answers:userid',
            'question' => 'privacy:metadata:answers:question',
            'answer1' => 'privacy:metadata:answers:answer1',
            'answer2' => 'privacy:metadata:answers:answer2',
            'time' => 'privacy:metadata:answers:time',
        ], 'privacy:metadata:answers');

        $collection->add_database_table('survey_analysis', [
            'userid' => 'privacy:metadata:analysis:userid',
            'notes' => 'privacy:metadata:analysis:notes',
        ], 'privacy:metadata:analysis');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        // While we should not have an analysis without answers, it's safer to gather contexts by looking at both tables.
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {survey} s
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
         LEFT JOIN {survey_answers} sa
                ON sa.survey = s.id
               AND sa.userid = :userid1
         LEFT JOIN {survey_analysis} sy
                ON sy.survey = s.id
               AND sy.userid = :userid2
             WHERE s.template <> 0
               AND (sa.id IS NOT NULL
                OR sy.id IS NOT NULL)";

        $contextlist->add_from_sql($sql, [
            'survey' => 'survey',
            'modulelevel' => CONTEXT_MODULE,
            'userid1' => $userid,
            'userid2' => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $params = [
            'survey' => 'survey',
            'modulelevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $sql = "
            SELECT sa.userid
              FROM {survey} s
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
              JOIN {survey_answers} sa
                ON sa.survey = s.id
             WHERE ctx.id = :contextid
               AND s.template <> 0";

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "
            SELECT sy.userid
              FROM {survey} s
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
              JOIN {survey_analysis} sy
                ON sy.survey = s.id
             WHERE ctx.id = :contextid
               AND s.template <> 0";

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

        // Export the answers.
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT sa.*,
                   sq.id as qid,
                   sq.text as qtext,
                   sq.shorttext as qshorttext,
                   sq.intro as qintro,
                   sq.options as qoptions,
                   sq.type as qtype,
                   cm.id as cmid
              FROM {survey_answers} sa
              JOIN {survey_questions} sq
                ON sq.id = sa.question
              JOIN {survey} s
                ON s.id = sa.survey
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
             WHERE cm.id $insql
               AND sa.userid = :userid
          ORDER BY s.id, sq.id";
        $params = array_merge($inparams, ['survey' => 'survey', 'userid' => $userid]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'cmid', [], function($carry, $record) {
            $q = survey_translate_question((object) [
                'text' => $record->qtext,
                'shorttext' => $record->qshorttext,
                'intro' => $record->qintro,
                'options' => $record->qoptions
            ]);
            $qtype = $record->qtype;
            $options = explode(',', $q->options ?? '');

            $carry[] = [
                'question' => array_merge((array) $q, [
                    'options' => $qtype > 0 ? $options : '-'
                ]),
                'answer' => [
                    'actual' => $qtype > 0 && !empty($record->answer1) ? $options[$record->answer1 - 1] : $record->answer1,
                    'preferred' => $qtype > 0 && !empty($record->answer2) ? $options[$record->answer2 - 1] : $record->answer2,
                ],
                'time' => transform::datetime($record->time),
            ];
            return $carry;

        }, function($cmid, $data) use ($user) {
            $context = context_module::instance($cmid);
            $contextdata = helper::get_context_data($context, $user);
            $contextdata = (object) array_merge((array) $contextdata, ['answers' => $data]);
            helper::export_context_files($context, $user);
            writer::with_context($context)->export_data([], $contextdata);
        });

        // Export the analysis.
        $sql = "
            SELECT sy.*, cm.id as cmid
              FROM {survey_analysis} sy
              JOIN {survey} s
                ON s.id = sy.survey
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
             WHERE cm.id $insql
               AND sy.userid = :userid
          ORDER BY s.id";
        $params = array_merge($inparams, ['survey' => 'survey', 'userid' => $userid]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'cmid', null, function($carry, $record) {
            $carry = ['notes' => $record->notes];
            return $carry;
        }, function($cmid, $data) {
            $context = context_module::instance($cmid);
            writer::with_context($context)->export_related_data([], 'survey_analysis', (object) $data);
        });
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        if ($surveyid = static::get_survey_id_from_context($context)) {
            $DB->delete_records('survey_answers', ['survey' => $surveyid]);
            $DB->delete_records('survey_analysis', ['survey' => $surveyid]);
        }
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

        // Fetch the survey IDs.
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT s.id
              FROM {survey} s
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
             WHERE cm.id $insql";
        $params = array_merge($inparams, ['survey' => 'survey']);
        $surveyids = $DB->get_fieldset_sql($sql, $params);

        // Delete all the things.
        list($insql, $inparams) = $DB->get_in_or_equal($surveyids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $userid]);
        $DB->delete_records_select('survey_answers', "survey $insql AND userid = :userid", $params);
        $DB->delete_records_select('survey_analysis', "survey $insql AND userid = :userid", $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Fetch the survey ID.
        $sql = "
            SELECT s.id
              FROM {survey} s
              JOIN {modules} m
                ON m.name = :survey
              JOIN {course_modules} cm
                ON cm.instance = s.id
               AND cm.module = m.id
             WHERE cm.id = :cmid";
        $params = [
            'survey' => 'survey',
            'cmid' => $context->instanceid,
            ];
        $surveyid = $DB->get_field_sql($sql, $params);
        $userids = $userlist->get_userids();

        // Delete all the things.
        list($insql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['surveyid'] = $surveyid;

        $DB->delete_records_select('survey_answers', "survey = :surveyid AND userid {$insql}", $params);
        $DB->delete_records_select('survey_analysis', "survey = :surveyid AND userid {$insql}", $params);
    }

    /**
     * Get a survey ID from its context.
     *
     * @param context_module $context The module context.
     * @return int
     */
    protected static function get_survey_id_from_context(context_module $context) {
        $cm = get_coursemodule_from_id('survey', $context->instanceid);
        return $cm ? (int) $cm->instance : 0;
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
