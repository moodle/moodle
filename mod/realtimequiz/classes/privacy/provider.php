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
 * GDPR information
 *
 * @package   mod_realtimequiz
 * @copyright 2018 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_realtimequiz\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Class provider
 * @package mod_realtimequiz
 */
class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Get details of user data stored by this plugin
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'realtimequiz_submitted',
            [
                'questionid' => 'privacy:metadata:realtimequiz_submitted:questionid',
                'sessionid' => 'privacy:metadata:realtimequiz_submitted:sessionid',
                'userid' => 'privacy:metadata:realtimequiz_submitted:userid',
                'answerid' => 'privacy:metadata:realtimequiz_submitted:answerid',
            ],
            'privacy:metadata:realtimequiz_submitted'
        );
        return $collection;
    }

    /** @var int */
    private static $modid;

    /**
     * Get the id of the 'realtimequiz' module record.
     * @return false|mixed
     * @throws \dml_exception
     */
    private static function get_modid() {
        global $DB;
        if (self::$modid === null) {
            self::$modid = $DB->get_field('modules', 'id', ['name' => 'realtimequiz']);
        }
        return self::$modid;
    }

    /**
     * Get the contexts where the given user has realtimequiz data.
     * @param int $userid
     * @return contextlist
     * @throws \dml_exception
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $modid = self::get_modid();
        if (!$modid) {
            return $contextlist; // Realtime quiz module not installed.
        }

        $params = [
            'modid' => $modid,
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];

        // Quiz responses.
        $sql = '
           SELECT c.id
             FROM {context} c
             JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                                      AND cm.module = :modid
             JOIN {realtimequiz} q ON q.id = cm.instance
             JOIN {realtimequiz_question} qq ON qq.quizid = q.id
             JOIN {realtimequiz_submitted} qs ON qs.questionid = qq.id
            WHERE qs.userid = :userid
        ';
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export the realtimequiz user data for the given contexts.
     * @param approved_contextlist $contextlist
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $user = $contextlist->get_user();
        [$contextsql, $contextparams] = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       sess.name AS sessionname,
                       sess.timestamp AS sessiontimestamp,
                       qq.questiontext,
                       qa.answertext,
                       qa.correct

                 FROM {context} c
                 JOIN {course_modules} cm ON cm.id = c.instanceid
                 JOIN {realtimequiz} q ON q.id = cm.instance
                 JOIN {realtimequiz_question} qq ON qq.quizid = q.id
                 JOIN {realtimequiz_submitted} qs ON qs.questionid = qq.id
                 JOIN {realtimequiz_answer} qa ON qa.id = qs.answerid
                 JOIN {realtimequiz_session} sess ON sess.id = qs.sessionid

                WHERE c.id $contextsql
                  AND qs.userid = :userid

                ORDER BY cm.id, sess.timestamp, qq.questionnum
        ";
        $params = ['userid' => $user->id] + $contextparams;
        $lastcmid = null;
        $responsedata = [];

        $responses = $DB->get_recordset_sql($sql, $params);
        foreach ($responses as $response) {
            if ($lastcmid !== $response->cmid) {
                if ($responsedata) {
                    self::export_realtimequiz_data_for_user($responsedata, $lastcmid, $user);
                }
                $responsedata = [];
                $lastcmid = $response->cmid;
            }
            $responsedata[] = (object)[
                'session' => $response->sessionname,
                'sessiontime' => $response->sessiontimestamp ? transform::datetime($response->sessiontimestamp) : '',
                'questiontext' => $response->questiontext,
                'answertext' => $response->answertext,
                'correct' => $response->correct,
            ];
        }
        $responses->close();
        if ($responsedata) {
            self::export_realtimequiz_data_for_user($responsedata, $lastcmid, $user);
        }
    }

    /**
     * Export the supplied personal data for a single realtime quiz activity, along with any generic data or area files.
     *
     * @param array $responses the data for each of the items in the realtime quiz
     * @param int $cmid
     * @param \stdClass $user
     */
    protected static function export_realtimequiz_data_for_user(array $responses, int $cmid, \stdClass $user) {
        // Fetch the generic module data for the choice.
        $context = \context_module::instance($cmid);
        $contextdata = helper::get_context_data($context, $user);

        // Merge with realtime quiz data and write it.
        $contextdata = (object)array_merge((array)$contextdata, ['responses' => $responses]);
        writer::with_context($context)->export_data([], $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all realtimequiz data in the given context.
     * @param \context $context
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if (!$context) {
            return;
        }
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }
        if (!$cm = get_coursemodule_from_id('realtimequiz', $context->instanceid)) {
            return;
        }
        $questionids = $DB->get_fieldset_select('realtimequiz_question', 'id', 'quizid = ?', [$cm->instance]);
        if ($questionids) {
            $DB->delete_records_list('realtimequiz_submitted', 'questionid', $questionids);
        }
    }

    /**
     * Delete all realtimequiz data for the given user and contexts.
     * @param approved_contextlist $contextlist
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            if (!$cm = get_coursemodule_from_id('realtimequiz', $context->instanceid)) {
                continue;
            }
            $questionids = $DB->get_fieldset_select('realtimequiz_question', 'id', 'quizid = ?', [$cm->instance]);
            if ($questionids) {
                [$qsql, $params] = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED);
                $params['userid'] = $userid;
                $DB->delete_records_select('realtimequiz_submitted', "questionid $qsql AND userid = :userid", $params);
            }
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!is_a($context, \context_module::class)) {
            return;
        }
        $modid = self::get_modid();
        if (!$modid) {
            return; // Realtimequiz module not installed.
        }
        $params = [
            'modid' => $modid,
            'contextlevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        // Quiz responses.
        $sql = "
            SELECT qs.userid
              FROM {realtimequiz_submitted} qs
              JOIN {realtimequiz_question} qq ON qq.id = qs.questionid
              JOIN {realtimequiz} q ON q.id = qq.quizid
              JOIN {course_modules} cm ON cm.instance = q.id AND cm.module = :modid
              JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
             WHERE ctx.id = :contextid
        ";
        $userlist->add_from_sql('userid', $sql, $params);
    }


    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if (!is_a($context, \context_module::class)) {
            return;
        }
        $modid = self::get_modid();
        if (!$modid) {
            return; // Checklist module not installed.
        }

        // Prepare SQL to gather all completed IDs.
        $userids = $userlist->get_userids();
        [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        // Delete quiz responses.
        $DB->delete_records_select(
            'realtimequiz_submitted',
            "userid $insql",
            $inparams
        );
    }
}
