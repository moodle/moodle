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
 * Contains class mod_questionnaire\privacy\provider
 *
 * @package    mod_questionnaire
 * @copyright  2018 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_questionnaire\privacy;

defined('MOODLE_INTERNAL') || die();

class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin_provider interface.
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $items The collection to add metadata to.
     * @return  collection  The array of metadata
     */
    public static function get_metadata(\core_privacy\local\metadata\collection $collection): \core_privacy\local\metadata\collection {

        // Add all of the relevant tables and fields to the collection.
        $collection->add_database_table('questionnaire_attempts', [
            'userid' => 'privacy:metadata:questionnaire_attempts:userid',
            'rid' => 'privacy:metadata:questionnaire_attempts:rid',
            'qid' => 'privacy:metadata:questionnaire_attempts:qid',
            'timemodified' => 'privacy:metadata:questionnaire_attempts:timemodified',
        ], 'privacy:metadata:questionnaire_attempts');

        $collection->add_database_table('questionnaire_response', [
            'userid' => 'privacy:metadata:questionnaire_response:userid',
            'survey_id' => 'privacy:metadata:questionnaire_response:survey_id',
            'complete' => 'privacy:metadata:questionnaire_response:complete',
            'grade' => 'privacy:metadata:questionnaire_response:grade',
            'submitted' => 'privacy:metadata:questionnaire_response:submitted',
        ], 'privacy:metadata:questionnaire_response');

        $collection->add_database_table('questionnaire_response_bool', [
            'response_id' => 'privacy:metadata:questionnaire_response_bool:response_id',
            'question_id' => 'privacy:metadata:questionnaire_response_bool:question_id',
            'choice_id' => 'privacy:metadata:questionnaire_response_bool:choice_id',
        ], 'privacy:metadata:questionnaire_response_bool');

        $collection->add_database_table('questionnaire_response_date', [
            'response_id' => 'privacy:metadata:questionnaire_response_date:response_id',
            'question_id' => 'privacy:metadata:questionnaire_response_date:question_id',
            'response' => 'privacy:metadata:questionnaire_response_date:response',
        ], 'privacy:metadata:questionnaire_response_date');

        $collection->add_database_table('questionnaire_response_other', [
            'response_id' => 'privacy:metadata:questionnaire_response_other:response_id',
            'question_id' => 'privacy:metadata:questionnaire_response_other:question_id',
            'choice_id' => 'privacy:metadata:questionnaire_response_other:choice_id',
            'response' => 'privacy:metadata:questionnaire_response_other:response',
        ], 'privacy:metadata:questionnaire_response_other');

        $collection->add_database_table('questionnaire_response_rank', [
            'response_id' => 'privacy:metadata:questionnaire_response_rank:response_id',
            'question_id' => 'privacy:metadata:questionnaire_response_rank:question_id',
            'choice_id' => 'privacy:metadata:questionnaire_response_rank:choice_id',
            'rank' => 'privacy:metadata:questionnaire_response_rank:rank',
        ], 'privacy:metadata:questionnaire_response_rank');

        $collection->add_database_table('questionnaire_response_text', [
            'response_id' => 'privacy:metadata:questionnaire_response_text:response_id',
            'question_id' => 'privacy:metadata:questionnaire_response_text:question_id',
            'response' => 'privacy:metadata:questionnaire_response_text:response',
        ], 'privacy:metadata:questionnaire_response_text');

        $collection->add_database_table('questionnaire_resp_multiple', [
            'response_id' => 'privacy:metadata:questionnaire_resp_multiple:response_id',
            'question_id' => 'privacy:metadata:questionnaire_resp_multiple:question_id',
            'choice_id' => 'privacy:metadata:questionnaire_resp_multiple:choice_id',
        ], 'privacy:metadata:questionnaire_resp_multiple');

        $collection->add_database_table('questionnaire_resp_single', [
            'response_id' => 'privacy:metadata:questionnaire_resp_single:response_id',
            'question_id' => 'privacy:metadata:questionnaire_resp_single:question_id',
            'choice_id' => 'privacy:metadata:questionnaire_resp_single:choice_id',
        ], 'privacy:metadata:questionnaire_resp_single');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT c.id
                 FROM {context} c
           INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
           INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
           INNER JOIN {questionnaire} q ON q.id = cm.instance
           INNER JOIN {questionnaire_response} qr ON qr.survey_id = q.sid
                WHERE qr.userid = :attemptuserid
        ";

        $params = [
            'modname' => 'questionnaire',
            'contextlevel' => CONTEXT_MODULE,
            'attemptuserid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       q.id AS qid, q.course AS qcourse,
                       qr.id AS responseid, qr.submitted AS lastsaved, qr.complete AS complete
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {questionnaire} q ON q.id = cm.instance
            INNER JOIN {questionnaire_response} qr ON qr.survey_id = q.sid
            LEFT JOIN {questionnaire_attempts} qa ON qa.rid = qr.id
                 WHERE c.id {$contextsql}
                       AND qr.userid = :userid
              ORDER BY cm.id, qr.id ASC";

        $params = ['modname' => 'questionnaire', 'contextlevel' => CONTEXT_MODULE, 'userid' => $user->id] + $contextparams;

        // There can be more than one attempt per instance, so we'll gather them by cmid.
        $lastcmid = 0;
        $responsedata = [];
        $responses = $DB->get_recordset_sql($sql, $params);
        foreach ($responses as $response) {
            // If we've moved to a new choice, then write the last choice data and reinit the choice data array.
            if ($lastcmid != $response->cmid) {
                if (!empty($responsedata)) {
                    $context = \context_module::instance($lastcmid);
                    // Fetch the generic module data for the questionnaire.
                    $contextdata = \core_privacy\local\request\helper::get_context_data($context, $user);
                    // Merge with attempt data and write it.
                    $contextdata = (object)array_merge((array)$contextdata, $responsedata);
                    \core_privacy\local\request\writer::with_context($context)->export_data([], $contextdata);
                }
                $responsedata = [];
                $lastcmid = $response->cmid;
                $course = $DB->get_record("course", ["id" => $response->qcourse]);
                $cm = get_coursemodule_from_instance("questionnaire", $response->qid, $course->id);
                $questionnaire = new \questionnaire($response->qid, null, $course, $cm);
            }
            $responsedata['responses'][] = [
                'complete' => (($response->complete == 'y') ? get_string('yes') : get_string('no')),
                'lastsaved' => \core_privacy\local\request\transform::datetime($response->lastsaved),
                'questions' => $questionnaire->get_structured_response($response->responseid),
            ];
        }
        $responses->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($responsedata)) {
            $context = \context_module::instance($lastcmid);
            // Fetch the generic module data for the questionnaire.
            $contextdata = \core_privacy\local\request\helper::get_context_data($context, $user);
            // Merge with attempt data and write it.
            $contextdata = (object)array_merge((array)$contextdata, $responsedata);
            \core_privacy\local\request\writer::with_context($context)->export_data([], $contextdata);
        }
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!($context instanceof \context_module)) {
            return;
        }

        if (!$cm = get_coursemodule_from_id('questionnaire', $context->instanceid)) {
            return;
        }

        if (!($questionnaire = $DB->get_record('questionnaire', ['id' => $cm->instance]))) {
            return;
        }

        if ($responses = $DB->get_recordset('questionnaire_response', ['survey_id' => $questionnaire->sid])) {
            self::delete_responses($responses);
        }
        $responses->close();
        $DB->delete_records('questionnaire_response', ['survey_id' => $questionnaire->sid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!($context instanceof \context_module)) {
                continue;
            }
            if (!$cm = get_coursemodule_from_id('questionnaire', $context->instanceid)) {
                continue;
            }

            if (!($questionnaire = $DB->get_record('questionnaire', ['id' => $cm->instance]))) {
                continue;
            }

            if ($responses = $DB->get_recordset('questionnaire_response',
                ['survey_id' => $questionnaire->sid, 'userid' => $userid])) {
                self::delete_responses($responses);
            }
            $responses->close();
            $DB->delete_records('questionnaire_response', ['survey_id' => $questionnaire->sid, 'userid' => $userid]);
        }
    }

    /**
     * Helper function to delete all the response records for a recordset array of responses.
     *
     * @param   recordset    $responses    The list of response records to delete for.
     */
    private static function delete_responses($responses) {
        global $DB;

        foreach ($responses as $response) {
            $DB->delete_records('questionnaire_response_bool', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_response_date', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_resp_multiple', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_response_other', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_response_rank', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_resp_single', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_response_text', ['response_id' => $response->id]);
            $DB->delete_records('questionnaire_attempts', ['rid' => $response->id]);
        }
    }
}