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
 * Privacy Subsystem implementation for mod_quiz.
 *
 * @package    mod_quiz
 * @category   privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\transform;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\manager;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Privacy Subsystem implementation for mod_quiz.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin_provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   collection  $items  The collection to add metadata to.
     * @return  collection  The array of metadata
     */
    public static function get_metadata(collection $items) : collection {
        // The table 'quiz' stores a record for each quiz.
        // It does not contain user personal data, but data is returned from it for contextual requirements.

        // The table 'quiz_attempts' stores a record of each quiz attempt.
        // It contains a userid which links to the user making the attempt and contains information about that attempt.
        $items->add_database_table('quiz_attempts', [
                'attempt'               => 'privacy:metadata:quiz_attempts:attempt',
                'currentpage'           => 'privacy:metadata:quiz_attempts:currentpage',
                'preview'               => 'privacy:metadata:quiz_attempts:preview',
                'state'                 => 'privacy:metadata:quiz_attempts:state',
                'timestart'             => 'privacy:metadata:quiz_attempts:timestart',
                'timefinish'            => 'privacy:metadata:quiz_attempts:timefinish',
                'timemodified'          => 'privacy:metadata:quiz_attempts:timemodified',
                'timemodifiedoffline'   => 'privacy:metadata:quiz_attempts:timemodifiedoffline',
                'timecheckstate'        => 'privacy:metadata:quiz_attempts:timecheckstate',
                'sumgrades'             => 'privacy:metadata:quiz_attempts:sumgrades',
            ], 'privacy:metadata:quiz_attempts');

        // The table 'quiz_feedback' contains the feedback responses which will be shown to users depending upon the
        // grade they achieve in the quiz.
        // It does not identify the user who wrote the feedback item so cannot be returned directly and is not
        // described, but relevant feedback items will be included with the quiz export for a user who has a grade.

        // The table 'quiz_grades' contains the current grade for each quiz/user combination.
        $items->add_database_table('quiz_grades', [
                'quiz'                  => 'privacy:metadata:quiz_grades:quiz',
                'userid'                => 'privacy:metadata:quiz_grades:userid',
                'grade'                 => 'privacy:metadata:quiz_grades:grade',
                'timemodified'          => 'privacy:metadata:quiz_grades:timemodified',
            ], 'privacy:metadata:quiz_grades');

        // The table 'quiz_overrides' contains any user or group overrides for users.
        // It should be included where data exists for a user.
        $items->add_database_table('quiz_overrides', [
                'quiz'                  => 'privacy:metadata:quiz_overrides:quiz',
                'userid'                => 'privacy:metadata:quiz_overrides:userid',
                'timeopen'              => 'privacy:metadata:quiz_overrides:timeopen',
                'timeclose'             => 'privacy:metadata:quiz_overrides:timeclose',
                'timelimit'             => 'privacy:metadata:quiz_overrides:timelimit',
            ], 'privacy:metadata:quiz_overrides');

        // These define the structure of the quiz.

        // The table 'quiz_sections' contains data about the structure of a quiz.
        // It does not contain any user identifying data and does not need a mapping.

        // The table 'quiz_slots' contains data about the structure of a quiz.
        // It does not contain any user identifying data and does not need a mapping.

        // The table 'quiz_reports' does not contain any user identifying data and does not need a mapping.

        // The table 'quiz_statistics' contains abstract statistics about question usage and cannot be mapped to any
        // specific user.
        // It does not contain any user identifying data and does not need a mapping.

        // The quiz links to the 'core_question' subsystem for all question functionality.
        $items->add_subsystem_link('core_question', [], 'privacy:metadata:core_question');

        // The quiz has two subplugins..
        $items->add_plugintype_link('quiz', [], 'privacy:metadata:quiz');
        $items->add_plugintype_link('quizaccess', [], 'privacy:metadata:quizaccess');

        // Although the quiz supports the core_completion API and defines custom completion items, these will be
        // noted by the manager as all activity modules are capable of supporting this functionality.

        return $items;
    }

    /**
     * Get the list of contexts where the specified user has attempted a quiz, or been involved with manual marking
     * and/or grading of a quiz.
     *
     * @param   int             $userid The user to search.
     * @return  contextlist     $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $resultset = new contextlist();

        // Users who attempted the quiz.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_attempts} qa ON qa.quiz = q.id
                 WHERE qa.userid = :userid AND qa.preview = 0";
        $params = ['contextlevel' => CONTEXT_MODULE, 'modname' => 'quiz', 'userid' => $userid];
        $resultset->add_from_sql($sql, $params);

        // Users with quiz overrides.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_overrides} qo ON qo.quiz = q.id
                 WHERE qo.userid = :userid";
        $params = ['contextlevel' => CONTEXT_MODULE, 'modname' => 'quiz', 'userid' => $userid];
        $resultset->add_from_sql($sql, $params);

        // Get the SQL used to link indirect question usages for the user.
        // This includes where a user is the manual marker on a question attempt.
        $qubaid = \core_question\privacy\provider::get_related_question_usages_for_user('rel', 'mod_quiz', 'qa.uniqueid', $userid);

        // Select the context of any quiz attempt where a user has an attempt, plus the related usages.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_attempts} qa ON qa.quiz = q.id
            " . $qubaid->from . "
            WHERE " . $qubaid->where() . " AND qa.preview = 0";
        $params = ['contextlevel' => CONTEXT_MODULE, 'modname' => 'quiz'] + $qubaid->from_where_params();
        $resultset->add_from_sql($sql, $params);

        return $resultset;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $params = [
            'cmid'    => $context->instanceid,
            'modname' => 'quiz',
        ];

        // Users who attempted the quiz.
        $sql = "SELECT qa.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_attempts} qa ON qa.quiz = q.id
                 WHERE cm.id = :cmid AND qa.preview = 0";
        $userlist->add_from_sql('userid', $sql, $params);

        // Users with quiz overrides.
        $sql = "SELECT qo.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_overrides} qo ON qo.quiz = q.id
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Question usages in context.
        // This includes where a user is the manual marker on a question attempt.
        $sql = "SELECT qa.uniqueid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_attempts} qa ON qa.quiz = q.id
                 WHERE cm.id = :cmid AND qa.preview = 0";
        \core_question\privacy\provider::get_users_in_context_from_sql($userlist, 'qn', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!count($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    q.*,
                    qg.id AS hasgrade,
                    qg.grade AS bestgrade,
                    qg.timemodified AS grademodified,
                    qo.id AS hasoverride,
                    qo.timeopen AS override_timeopen,
                    qo.timeclose AS override_timeclose,
                    qo.timelimit AS override_timelimit,
                    c.id AS contextid,
                    cm.id AS cmid
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {quiz} q ON q.id = cm.instance
             LEFT JOIN {quiz_overrides} qo ON qo.quiz = q.id AND qo.userid = :qouserid
             LEFT JOIN {quiz_grades} qg ON qg.quiz = q.id AND qg.userid = :qguserid
                 WHERE c.id {$contextsql}";

        $params = [
            'contextlevel'      => CONTEXT_MODULE,
            'modname'           => 'quiz',
            'qguserid'          => $userid,
            'qouserid'          => $userid,
        ];
        $params += $contextparams;

        // Fetch the individual quizzes.
        $quizzes = $DB->get_recordset_sql($sql, $params);
        foreach ($quizzes as $quiz) {
            list($course, $cm) = get_course_and_cm_from_cmid($quiz->cmid, 'quiz');
            $quizobj = new \quiz($quiz, $cm, $course);
            $context = $quizobj->get_context();

            $quizdata = \core_privacy\local\request\helper::get_context_data($context, $contextlist->get_user());
            \core_privacy\local\request\helper::export_context_files($context, $contextlist->get_user());

            if (!empty($quizdata->timeopen)) {
                $quizdata->timeopen = transform::datetime($quiz->timeopen);
            }
            if (!empty($quizdata->timeclose)) {
                $quizdata->timeclose = transform::datetime($quiz->timeclose);
            }
            if (!empty($quizdata->timelimit)) {
                $quizdata->timelimit = $quiz->timelimit;
            }

            if (!empty($quiz->hasoverride)) {
                $quizdata->override = (object) [];

                if (!empty($quizdata->override_override_timeopen)) {
                    $quizdata->override->timeopen = transform::datetime($quiz->override_timeopen);
                }
                if (!empty($quizdata->override_timeclose)) {
                    $quizdata->override->timeclose = transform::datetime($quiz->override_timeclose);
                }
                if (!empty($quizdata->override_timelimit)) {
                    $quizdata->override->timelimit = $quiz->override_timelimit;
                }
            }

            $quizdata->accessdata = (object) [];

            $components = \core_component::get_plugin_list('quizaccess');
            $exportparams = [
                    $quizobj,
                    $user,
                ];
            foreach (array_keys($components) as $component) {
                $classname = manager::get_provider_classname_for_component("quizaccess_$component");
                if (class_exists($classname) && is_subclass_of($classname, quizaccess_provider::class)) {
                    $result = component_class_callback($classname, 'export_quizaccess_user_data', $exportparams);
                    if (count((array) $result)) {
                        $quizdata->accessdata->$component = $result;
                    }
                }
            }

            if (empty((array) $quizdata->accessdata)) {
                unset($quizdata->accessdata);
            }

            writer::with_context($context)
                ->export_data([], $quizdata);
        }
        $quizzes->close();

        // Store all quiz attempt data.
        static::export_quiz_attempts($contextlist);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context->contextlevel != CONTEXT_MODULE) {
            // Only quiz module will be handled.
            return;
        }

        $cm = get_coursemodule_from_id('quiz', $context->instanceid);
        if (!$cm) {
            // Only quiz module will be handled.
            return;
        }

        $quizobj = \quiz::create($cm->instance);
        $quiz = $quizobj->get_quiz();

        // Handle the 'quizaccess' subplugin.
        manager::plugintype_class_callback(
                'quizaccess',
                quizaccess_provider::class,
                'delete_subplugin_data_for_all_users_in_context',
                [$quizobj]
            );

        // Delete all overrides - do not log.
        quiz_delete_all_overrides($quiz, false);

        // This will delete all question attempts, quiz attempts, and quiz grades for this quiz.
        quiz_delete_all_attempts($quiz);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
            // Only quiz module will be handled.
                continue;
            }

            $cm = get_coursemodule_from_id('quiz', $context->instanceid);
            if (!$cm) {
                // Only quiz module will be handled.
                continue;
            }

            // Fetch the details of the data to be removed.
            $quizobj = \quiz::create($cm->instance);
            $quiz = $quizobj->get_quiz();
            $user = $contextlist->get_user();

            // Handle the 'quizaccess' quizaccess.
            manager::plugintype_class_callback(
                    'quizaccess',
                    quizaccess_provider::class,
                    'delete_quizaccess_data_for_user',
                    [$quizobj, $user]
                );

            // Remove overrides for this user.
            $overrides = $DB->get_records('quiz_overrides' , [
                'quiz' => $quizobj->get_quizid(),
                'userid' => $user->id,
            ]);

            foreach ($overrides as $override) {
                quiz_delete_override($quiz, $override->id, false);
            }

            // This will delete all question attempts, quiz attempts, and quiz grades for this quiz.
            quiz_delete_user_attempts($quizobj, $user);
        }
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
            // Only quiz module will be handled.
            return;
        }

        $cm = get_coursemodule_from_id('quiz', $context->instanceid);
        if (!$cm) {
            // Only quiz module will be handled.
            return;
        }

        $quizobj = \quiz::create($cm->instance);
        $quiz = $quizobj->get_quiz();

        $userids = $userlist->get_userids();

        // Handle the 'quizaccess' quizaccess.
        manager::plugintype_class_callback(
                'quizaccess',
                quizaccess_user_provider::class,
                'delete_quizaccess_data_for_users',
                [$userlist]
        );

        foreach ($userids as $userid) {
            // Remove overrides for this user.
            $overrides = $DB->get_records('quiz_overrides' , [
                'quiz' => $quizobj->get_quizid(),
                'userid' => $userid,
            ]);

            foreach ($overrides as $override) {
                quiz_delete_override($quiz, $override->id, false);
            }

            // This will delete all question attempts, quiz attempts, and quiz grades for this user in the given quiz.
            quiz_delete_user_attempts($quizobj, (object)['id' => $userid]);
        }
    }

    /**
     * Store all quiz attempts for the contextlist.
     *
     * @param   approved_contextlist    $contextlist
     */
    protected static function export_quiz_attempts(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $qubaid = \core_question\privacy\provider::get_related_question_usages_for_user('rel', 'mod_quiz', 'qa.uniqueid', $userid);

        $sql = "SELECT
                    c.id AS contextid,
                    cm.id AS cmid,
                    qa.*
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz'
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_attempts} qa ON qa.quiz = q.id
            " . $qubaid->from. "
            WHERE (
                qa.userid = :qauserid OR
                " . $qubaid->where() . "
            ) AND qa.preview = 0
        ";

        $params = array_merge(
                [
                    'contextlevel'      => CONTEXT_MODULE,
                    'qauserid'          => $userid,
                ],
                $qubaid->from_where_params()
            );

        $attempts = $DB->get_recordset_sql($sql, $params);
        foreach ($attempts as $attempt) {
            $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz]);
            $context = \context_module::instance($attempt->cmid);
            $attemptsubcontext = helper::get_quiz_attempt_subcontext($attempt, $contextlist->get_user());
            $options = quiz_get_review_options($quiz, $attempt, $context);

            if ($attempt->userid == $userid) {
                // This attempt was made by the user.
                // They 'own' all data on it.
                // Store the question usage data.
                \core_question\privacy\provider::export_question_usage($userid,
                        $context,
                        $attemptsubcontext,
                        $attempt->uniqueid,
                        $options,
                        true
                    );

                // Store the quiz attempt data.
                $data = (object) [
                    'state' => \quiz_attempt::state_name($attempt->state),
                ];

                if (!empty($attempt->timestart)) {
                    $data->timestart = transform::datetime($attempt->timestart);
                }
                if (!empty($attempt->timefinish)) {
                    $data->timefinish = transform::datetime($attempt->timefinish);
                }
                if (!empty($attempt->timemodified)) {
                    $data->timemodified = transform::datetime($attempt->timemodified);
                }
                if (!empty($attempt->timemodifiedoffline)) {
                    $data->timemodifiedoffline = transform::datetime($attempt->timemodifiedoffline);
                }
                if (!empty($attempt->timecheckstate)) {
                    $data->timecheckstate = transform::datetime($attempt->timecheckstate);
                }

                if ($options->marks == \question_display_options::MARK_AND_MAX) {
                    $grade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);
                    $data->grade = (object) [
                            'grade' => quiz_format_grade($quiz, $grade),
                            'feedback' => quiz_feedback_for_grade($grade, $quiz, $context),
                        ];
                }

                writer::with_context($context)
                    ->export_data($attemptsubcontext, $data);
            } else {
                // This attempt was made by another user.
                // The current user may have marked part of the quiz attempt.
                \core_question\privacy\provider::export_question_usage(
                        $userid,
                        $context,
                        $attemptsubcontext,
                        $attempt->uniqueid,
                        $options,
                        false
                    );
            }
        }
        $attempts->close();
    }
}
