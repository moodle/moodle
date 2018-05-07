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
 * Privacy Subsystem implementation for core_question.
 *
 * @package    core_question
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/engine/datalib.php');

/**
 * Privacy Subsystem implementation for core_question.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This component has data.
    // We need to return all question information where the user is
    // listed in either the question.createdby or question.modifiedby fields.
    // We may also need to fetch this informtion from individual plugins in some cases.
    // e.g. to fetch the full and other question-specific meta-data.
    \core_privacy\local\metadata\provider,

    // This is a subsysytem which provides information to core.
    \core_privacy\local\request\subsystem\provider,

    // This is a subsysytem which provides information to plugins.
    \core_privacy\local\request\subsystem\plugin_provider
{

    /**
     * Describe the types of data stored by the question subsystem.
     *
     * @param   collection  $items  The collection to add metadata to.
     * @return  collection  The array of metadata
     */
    public static function get_metadata(collection $items) {
        // Other tables link against it.

        // The 'question_usages' table does not contain any user data.
        // The table links the but doesn't store itself.

        // The 'question_attempts' table contains data about question attempts.
        // It does not contain any user ids - these are stored by the caller.
        $items->add_database_table('question_attempts', [
            'flagged'           => 'privacy:metadata:database:question_attempts:flagged',
            'responsesummary'   => 'privacy:metadata:database:question_attempts:responsesummary',
            'timemodified'      => 'privacy:metadata:database:question_attempts:timemodified',
        ], 'privacy:metadata:database:question_attempts');;

        // The 'question_attempt_steps' table contains data about changes to the state of a question attempt.
        $items->add_database_table('question_attempt_steps', [
            'state'             => 'privacy:metadata:database:question_attempt_steps:state',
            'timecreated'       => 'privacy:metadata:database:question_attempt_steps:timecreated',
            'fraction'          => 'privacy:metadata:database:question_attempt_steps:fraction',
            'userid'            => 'privacy:metadata:database:question_attempt_steps:userid',
        ], 'privacy:metadata:database:question_attempt_steps');

        // The 'question_attempt_step_data' table contains specific all metadata for each state.
        $items->add_database_table('question_attempt_step_data', [
            'name'              => 'privacy:metadata:database:question_attempt_step_data:name',
            'value'             => 'privacy:metadata:database:question_attempt_step_data:value',
        ], 'privacy:metadata:database:question_attempt_step_data');

        // These are all part of the set of the question definition
        // The 'question' table is used to store instances of each question.
        // It contains a createdby and modifiedby which related to specific users.
        $items->add_database_table('question', [
            'name'              => 'privacy:metadata:database:question:name',
            'questiontext'      => 'privacy:metadata:database:question:questiontext',
            'generalfeedback'   => 'privacy:metadata:database:question:generalfeedback',
            'timecreated'       => 'privacy:metadata:database:question:timecreated',
            'timemodified'      => 'privacy:metadata:database:question:timemodified',
            'createdby'         => 'privacy:metadata:database:question:createdby',
            'modifiedby'        => 'privacy:metadata:database:question:modifiedby',
        ], 'privacy:metadata:database:question');

        // The 'question_answers' table is used to store the set of answers, with appropriate feedback for each question.
        // It does not contain user data.

        // The 'question_hints' table is used to store hints about the correct answer for a question.
        // It does not contain user data.

        // The 'question_categories' table contains structural information about how questions are presented in the UI.
        // It does not contain user data.

        // The 'question_statistics' table contains aggregated statistics about responses.
        // It does not contain any identifiable user data.

        // The question subsystem makes use of the qtype, qformat, and qbehaviour plugin types.
        $items->add_plugintype_link('qtype', [], 'privacy:metadata:link:qtype');
        $items->add_plugintype_link('qformat', [], 'privacy:metadata:link:qformat');
        $items->add_plugintype_link('qbehaviour', [], 'privacy:metadata:link:qbehaviour');

        return $items;
    }

    /**
     * Export the data for all question attempts on this question usage.
     *
     * Where a user is the owner of the usage, then the full detail of that usage will be included.
     * Where a user has been involved in the usage, but it is not their own usage, then only their specific
     * involvement will be exported.
     *
     * @param   int             $userid     The userid to export.
     * @param   \context        $context    The context that the question was used within.
     * @param   array           $usagecontext  The subcontext of this usage.
     * @param   int             $usage      The question usage ID.
     * @param   \question_display_options   $options    The display options used for formatting.
     * @param   bool            $isowner    Whether the user being exported is the user who used the question.
     */
    public static function export_question_usage(
            $userid,
            \context $context,
            array $usagecontext,
            $usage,
            \question_display_options $options,
            $isowner
        ) {
        // Determine the questions in this usage.
        $quba = \question_engine::load_questions_usage_by_activity($usage);

        $basepath = $usagecontext;
        $questionscontext = array_merge($usagecontext, [
            get_string('questions', 'core_question'),
        ]);

        foreach ($quba->get_attempt_iterator() as $qa) {
            $question = $qa->get_question();
            $slotno = $qa->get_slot();
            $questionnocontext = array_merge($questionscontext, [$slotno]);

            if ($isowner) {
                // This user is the overal owner of the question attempt and all data wil therefore be exported.
                //
                // Respect _some_ of the question_display_options to ensure that they don't have access to
                // generalfeedback and mark if the display options prevent this.
                // This is defensible because they can submit questions without completing a quiz and perform an SAR to
                // get prior access to the feedback and mark to improve upon it.
                // Export the response.
                $data = (object) [
                    'name' => $question->name,
                    'question' => $qa->get_question_summary(),
                    'answer' => $qa->get_response_summary(),
                    'timemodified' => transform::datetime($qa->timemodified),
                ];

                if ($options->marks >= \question_display_options::MARK_AND_MAX) {
                    $data->mark = $qa->format_mark($options->markdp);
                }

                if ($options->flags != \question_display_options::HIDDEN) {
                    $data->flagged = transform::yesno($qa->is_flagged());
                }

                if ($options->generalfeedback != \question_display_options::HIDDEN) {
                    $data->generalfeedback = $question->format_generalfeedback($qa);
                }

                if ($options->manualcomment != \question_display_options::HIDDEN) {
                    $behaviour = $qa->get_behaviour();
                    if ($qa->has_manual_comment()) {
                        // Note - the export of the step data will ensure that the files are exported.
                        // No need to do it again here.
                        list($comment, $commentformat) = $qa->get_manual_comment();
                        // Get the step data.
                        foreach ($qa->get_reverse_step_iterator() as $step) {
                            if ($step->has_behaviour_var('comment')) {
                                break;
                            }
                        }

                        $comment = writer::with_context($context)
                            ->rewrite_pluginfile_urls(
                                $questionnocontext,
                                'question',
                                'response_bf_comment',
                                $step->get_id(),
                                $comment
                            );
                        $data->comment = $behaviour->format_comment($comment, $commentformat);
                    }
                }

                writer::with_context($context)
                    ->export_data($questionnocontext, $data);

                // Export the step data.
                static::export_question_attempt_steps($userid, $context, $questionnocontext, $qa, $options, $isowner);
            }
        }
    }

    /**
     * Export the data for each step transition for each question in each question attempt.
     *
     * Where a user is the owner of the usage, then all steps in the question usage will be exported.
     * Where a user is not the owner, but has been involved in the usage, then only their specific
     * involvement will be exported.
     *
     * @param   int                 $userid     The user to export for
     * @param   \context            $context    The context that the question was used within.
     * @param   array               $questionnocontext  The subcontext of this question number.
     * @param   \question_attempt   $qa         The attempt being checked
     * @param   \question_display_options   $options    The display options used for formatting.
     * @param   bool                $isowner    Whether the user being exported is the user who used the question.
     */
    public static function export_question_attempt_steps(
            $userid,
            \context $context,
            array $questionnocontext,
            \question_attempt $qa,
            \question_display_options $options,
            $isowner
        ) {
        $attemptdata = (object) [
                'steps' => [],
            ];
        $stepno = 0;
        foreach ($qa->get_step_iterator() as $i => $step) {
            $stepno++;

            if ($isowner || ($step->get_user_id() != $userid)) {
                // The user is the owner, or the author of the step.

                $restrictedqa = new \question_attempt_with_restricted_history($qa, $i, null);
                $stepdata = (object) [
                    // Note: Do not include the user here.
                    'time' => transform::datetime($step->get_timecreated()),
                    'action' => $qa->summarise_action($step),
                ];

                if ($options->marks >= \question_display_options::MARK_AND_MAX) {
                    $stepdata->mark = $qa->format_fraction_as_mark($step->get_fraction(), $options->markdp);
                }

                if ($options->correctness != \question_display_options::HIDDEN) {
                    $stepdata->state = $restrictedqa->get_state_string($options->correctness);
                }

                if ($step->has_behaviour_var('comment')) {
                    $behaviour = $qa->get_behaviour();
                    $comment = $step->get_behaviour_var('comment');
                    $commentformat = $step->get_behaviour_var('commentformat');

                    if (empty(trim($comment))) {
                        // Skip empty comments.
                        continue;
                    }

                    // Format the comment.
                    $comment = writer::with_context($context)
                        ->rewrite_pluginfile_urls(
                            $questionnocontext,
                            'question',
                            'response_bf_comment',
                            $step->get_id(),
                            $comment
                        );

                    // Export any files associated with the comment files area.
                    writer::with_context($context)
                        ->export_area_files(
                            $questionnocontext,
                            'question',
                            "response_bf_comment",
                            $step->get_id()
                        );

                    $stepdata->comment = $behaviour->format_comment($comment, $commentformat);
                }

                // Export any response files associated with this step.
                foreach (\question_engine::get_all_response_file_areas() as $filearea) {
                    writer::with_context($context)
                        ->export_area_files(
                                $questionnocontext,
                                'question',
                                $filearea,
                                $step->get_id()
                            );
                }

                $attemptdata->steps[$stepno] = $stepdata;
            }
        }

        if (!empty($attemptdata->steps)) {
            writer::with_context($context)
                ->export_related_data($questionnocontext, 'steps', $attemptdata);
        }
    }

    /**
     * Get the list of contexts where the specified user has either created, or edited a question.
     *
     * To export usage of a question, please call {@link provider::export_question_usage()} from the module which
     * instantiated the usage of the question.
     *
     * @param   int             $userid The user to search.
     * @return  contextlist     $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        // A user may have created or updated a question.
        // Questions are linked against a question category, which has a contextid field.
        $sql = "SELECT cat.contextid
                  FROM {question} q
            INNER JOIN {question_categories} cat ON cat.id = q.category
                 WHERE
                    q.createdby = :useridcreated OR
                   q.modifiedby = :useridmodified";
        $params = [
            'useridcreated' => $userid,
            'useridmodified' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Determine related question usages for a user.
     *
     * @param   string          $prefix     A unique prefix to add to the table alias
     * @param   string          $component  The name of the component to fetch usages for.
     * @param   string          $joinfield  The SQL field name to use in the JOIN ON - e.g. q.usageid
     * @param   int             $userid     The user to search.
     * @return  \qubaid_join
     */
    public static function get_related_question_usages_for_user($prefix, $component, $joinfield, $userid) {
        return new \qubaid_join("
                JOIN {question_usages} {$prefix}_qu ON {$prefix}_qu.id = {$joinfield}
                 AND {$prefix}_qu.component = :{$prefix}_usagecomponent
                JOIN {question_attempts} {$prefix}_qa ON {$prefix}_qa.questionusageid = {$prefix}_qu.id
                JOIN {question_attempt_steps} {$prefix}_qas ON {$prefix}_qas.questionattemptid = {$prefix}_qa.id",
            "{$prefix}_qu.id",
            "{$prefix}_qas.userid = :{$prefix}_stepuserid",
            [
                "{$prefix}_stepuserid" => $userid,
                "{$prefix}_usagecomponent" => $component,
            ]);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $CFG, $DB, $SITE;
        if (empty($contextlist)) {
            return;
        }

        // Use the Moodle XML Data format.
        // It is the only lossless format that we support.
        $format = "xml";
        require_once($CFG->dirroot . "/question/format/{$format}/format.php");

        // THe export system needs questions in a particular format.
        // The easiest way to fetch these is with get_questions_category() which takes the details of a question
        // category.
        // We fetch the root question category for each context and the get_questions_category function recurses to
        // After fetching them, we filter out any not created or modified by the requestor.
        $user = $contextlist->get_user();
        $userid = $user->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $categories = $DB->get_records_select('question_categories', "contextid {$contextsql} AND parent = 0", $contextparams);

        $classname = "qformat_{$format}";
        foreach ($categories as $category) {
            $context = \context::instance_by_id($category->contextid);

            $questions = get_questions_category($category, true);
            $questions = array_filter($questions, function($question) use ($userid) {
                return ($question->createdby == $userid) || ($question->modifiedby == $userid);
            }, ARRAY_FILTER_USE_BOTH);

            if (empty($questions)) {
                continue;
            }

            $qformat = new $classname();
            $qformat->setQuestions($questions);

            $qformat->setContexts([$context]);
            $qformat->setContexttofile(true);

            // We do not know which course this belongs to, and it's not actually used except in error, so use Site.
            $qformat->setCourse($SITE);
            $content = '';
            if ($qformat->exportpreprocess()) {
                $content = $qformat->exportprocess(false);
            }

            $subcontext = [
                get_string('questionbank', 'core_question'),
            ];
            writer::with_context($context)->export_custom_file($subcontext, 'questions.xml', $content);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Questions are considered to be 'owned' by the institution, even if they were originally written by a specific
        // user. They are still exported in the list of a users data, but they are not removed.
        // The userid is instead anonymised.

        $DB->set_field_select('question', 'createdby', 0,
            'category IN (SELECT id FROM {question_categories} WHERE contextid = :contextid)',
            [
                'contextid' => $context->id,
            ]);

        $DB->set_field_select('question', 'modifiedby', 0,
            'category IN (SELECT id FROM {question_categories} WHERE contextid = :contextid)',
            [
                'contextid' => $context->id,
            ]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // Questions are considered to be 'owned' by the institution, even if they were originally written by a specific
        // user. They are still exported in the list of a users data, but they are not removed.
        // The userid is instead anonymised.

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['createdby'] = $contextlist->get_user()->id;
        $DB->set_field_select('question', 'createdby', 0, "
                category IN (SELECT id FROM {question_categories} WHERE contextid {$contextsql})
            AND createdby = :createdby", $contextparams);

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['modifiedby'] = $contextlist->get_user()->id;
        $DB->set_field_select('question', 'modifiedby', 0, "
                category IN (SELECT id FROM {question_categories} WHERE contextid {$contextsql})
            AND modifiedby = :modifiedby", $contextparams);
    }
}
