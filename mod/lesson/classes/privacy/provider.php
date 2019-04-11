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
 * @package    mod_lesson
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lesson\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_helper;
use context_module;
use stdClass;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

require_once($CFG->dirroot . '/mod/lesson/locallib.php');
require_once($CFG->dirroot . '/mod/lesson/pagetypes/essay.php');
require_once($CFG->dirroot . '/mod/lesson/pagetypes/matching.php');
require_once($CFG->dirroot . '/mod/lesson/pagetypes/multichoice.php');

/**
 * Data provider class.
 *
 * @package    mod_lesson
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('lesson_attempts', [
            'userid' => 'privacy:metadata:attempts:userid',
            'pageid' => 'privacy:metadata:attempts:pageid',
            'answerid' => 'privacy:metadata:attempts:answerid',
            'retry' => 'privacy:metadata:attempts:retry',
            'correct' => 'privacy:metadata:attempts:correct',
            'useranswer' => 'privacy:metadata:attempts:useranswer',
            'timeseen' => 'privacy:metadata:attempts:timeseen',
        ], 'privacy:metadata:attempts');

        $collection->add_database_table('lesson_grades', [
            'userid' => 'privacy:metadata:grades:userid',
            'grade' => 'privacy:metadata:grades:grade',
            'completed' => 'privacy:metadata:grades:completed',
            // The column late is not used.
        ], 'privacy:metadata:grades');

        $collection->add_database_table('lesson_timer', [
            'userid' => 'privacy:metadata:timer:userid',
            'starttime' => 'privacy:metadata:timer:starttime',
            'lessontime' => 'privacy:metadata:timer:lessontime',
            'completed' => 'privacy:metadata:timer:completed',
            'timemodifiedoffline' => 'privacy:metadata:timer:timemodifiedoffline',
        ], 'privacy:metadata:timer');

        $collection->add_database_table('lesson_branch', [
            'userid' => 'privacy:metadata:branch:userid',
            'pageid' => 'privacy:metadata:branch:pageid',
            'retry' => 'privacy:metadata:branch:retry',
            'flag' => 'privacy:metadata:branch:flag',
            'timeseen' => 'privacy:metadata:branch:timeseen',
            'nextpageid' => 'privacy:metadata:branch:nextpageid',
        ], 'privacy:metadata:branch');

        $collection->add_database_table('lesson_overrides', [
            'userid' => 'privacy:metadata:overrides:userid',
            'available' => 'privacy:metadata:overrides:available',
            'deadline' => 'privacy:metadata:overrides:deadline',
            'timelimit' => 'privacy:metadata:overrides:timelimit',
            'review' => 'privacy:metadata:overrides:review',
            'maxattempts' => 'privacy:metadata:overrides:maxattempts',
            'retake' => 'privacy:metadata:overrides:retake',
            'password' => 'privacy:metadata:overrides:password',
        ], 'privacy:metadata:overrides');

        $collection->add_user_preference('lesson_view', 'privacy:metadata:userpref:lessonview');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "
            SELECT DISTINCT ctx.id
              FROM {lesson} l
              JOIN {modules} m
                ON m.name = :lesson
              JOIN {course_modules} cm
                ON cm.instance = l.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
         LEFT JOIN {lesson_attempts} la
                ON la.lessonid = l.id
               AND la.userid = :userid1
         LEFT JOIN {lesson_branch} lb
                ON lb.lessonid = l.id
               AND lb.userid = :userid2
         LEFT JOIN {lesson_grades} lg
                ON lg.lessonid = l.id
               AND lg.userid = :userid3
         LEFT JOIN {lesson_overrides} lo
                ON lo.lessonid = l.id
               AND lo.userid = :userid4
         LEFT JOIN {lesson_timer} lt
                ON lt.lessonid = l.id
               AND lt.userid = :userid5
             WHERE la.id IS NOT NULL
                OR lb.id IS NOT NULL
                OR lg.id IS NOT NULL
                OR lo.id IS NOT NULL
                OR lt.id IS NOT NULL";

        $params = [
            'lesson' => 'lesson',
            'modulelevel' => CONTEXT_MODULE,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
            'userid5' => $userid,
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

        $params = [
            'lesson' => 'lesson',
            'modulelevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        // Mapping of lesson tables which may contain user data.
        $joins = [
            'lesson_attempts',
            'lesson_branch',
            'lesson_grades',
            'lesson_overrides',
            'lesson_timer',
        ];

        foreach ($joins as $join) {
            $sql = "
                SELECT lx.userid
                  FROM {lesson} l
                  JOIN {modules} m
                    ON m.name = :lesson
                  JOIN {course_modules} cm
                    ON cm.instance = l.id
                   AND cm.module = m.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modulelevel
                  JOIN {{$join}} lx
                    ON lx.lessonid = l.id
                 WHERE ctx.id = :contextid";

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

        // If the context export was requested, then let's at least describe the lesson.
        foreach ($cmids as $cmid) {
            $context = context_module::instance($cmid);
            $contextdata = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);
            writer::with_context($context)->export_data([], $contextdata);
        }

        // Find the lesson IDs.
        $lessonidstocmids = static::get_lesson_ids_to_cmids_from_cmids($cmids);

        // Prepare the common SQL fragments.
        list($inlessonsql, $inlessonparams) = $DB->get_in_or_equal(array_keys($lessonidstocmids), SQL_PARAMS_NAMED);
        $sqluserlesson = "userid = :userid AND lessonid $inlessonsql";
        $paramsuserlesson = array_merge($inlessonparams, ['userid' => $userid]);

        // Export the overrides.
        $recordset = $DB->get_recordset_select('lesson_overrides', $sqluserlesson, $paramsuserlesson);
        static::recordset_loop_and_export($recordset, 'lessonid', null, function($carry, $record) {
            // We know that there is only one row per lesson, so no need to use $carry.
            return (object) [
                'available' => $record->available !== null ? transform::datetime($record->available) : null,
                'deadline' => $record->deadline !== null ? transform::datetime($record->deadline) : null,
                'timelimit' => $record->timelimit !== null ? format_time($record->timelimit) : null,
                'review' => $record->review !== null ? transform::yesno($record->review) : null,
                'maxattempts' => $record->maxattempts,
                'retake' => $record->retake !== null ? transform::yesno($record->retake) : null,
                'password' => $record->password,
            ];
        }, function($lessonid, $data) use ($lessonidstocmids) {
            $context = context_module::instance($lessonidstocmids[$lessonid]);
            writer::with_context($context)->export_related_data([], 'overrides', $data);
        });

        // Export the grades.
        $recordset = $DB->get_recordset_select('lesson_grades', $sqluserlesson, $paramsuserlesson, 'lessonid, completed');
        static::recordset_loop_and_export($recordset, 'lessonid', [], function($carry, $record) {
            $carry[] = (object) [
                'grade' => $record->grade,
                'completed' => transform::datetime($record->completed),
            ];
            return $carry;
        }, function($lessonid, $data) use ($lessonidstocmids) {
            $context = context_module::instance($lessonidstocmids[$lessonid]);
            writer::with_context($context)->export_related_data([], 'grades', (object) ['grades' => $data]);
        });

        // Export the timers.
        $recordset = $DB->get_recordset_select('lesson_timer', $sqluserlesson, $paramsuserlesson, 'lessonid, starttime');
        static::recordset_loop_and_export($recordset, 'lessonid', [], function($carry, $record) {
            $carry[] = (object) [
                'starttime' => transform::datetime($record->starttime),
                'lastactivity' => transform::datetime($record->lessontime),
                'completed' => transform::yesno($record->completed),
                'timemodifiedoffline' => $record->timemodifiedoffline ? transform::datetime($record->timemodifiedoffline) : null,
            ];
            return $carry;
        }, function($lessonid, $data) use ($lessonidstocmids) {
            $context = context_module::instance($lessonidstocmids[$lessonid]);
            writer::with_context($context)->export_related_data([], 'timers', (object) ['timers' => $data]);
        });

        // Export the attempts and branches.
        $sql = "
            SELECT " . $DB->sql_concat('lp.id', "':'", 'COALESCE(la.id, 0)', "':'", 'COALESCE(lb.id, 0)') . " AS uniqid,
                   lp.lessonid,

                   lp.id AS page_id,
                   lp.qtype AS page_qtype,
                   lp.qoption AS page_qoption,
                   lp.title AS page_title,
                   lp.contents AS page_contents,
                   lp.contentsformat AS page_contentsformat,

                   la.id AS attempt_id,
                   la.retry AS attempt_retry,
                   la.correct AS attempt_correct,
                   la.useranswer AS attempt_useranswer,
                   la.timeseen AS attempt_timeseen,

                   lb.id AS branch_id,
                   lb.retry AS branch_retry,
                   lb.timeseen AS branch_timeseen,

                   lpb.id AS nextpage_id,
                   lpb.title AS nextpage_title

              FROM {lesson_pages} lp
         LEFT JOIN {lesson_attempts} la
                ON la.pageid = lp.id
               AND la.userid = :userid1
         LEFT JOIN {lesson_branch} lb
                ON lb.pageid = lp.id
               AND lb.userid = :userid2
         LEFT JOIN {lesson_pages} lpb
                ON lpb.id = lb.nextpageid
             WHERE lp.lessonid $inlessonsql
               AND (la.id IS NOT NULL OR lb.id IS NOT NULL)
          ORDER BY lp.lessonid, lp.id, la.retry, lb.retry, la.id, lb.id";
        $params = array_merge($inlessonparams, ['userid1' => $userid, 'userid2' => $userid]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'lessonid', [], function($carry, $record) use ($lessonidstocmids) {
            $context = context_module::instance($lessonidstocmids[$record->lessonid]);
            $options = ['context' => $context];

            $take = isset($record->attempt_retry) ? $record->attempt_retry : $record->branch_retry;
            if (!isset($carry[$take])) {
                $carry[$take] = (object) [
                    'number' => $take + 1,
                    'answers' => [],
                    'jumps' => []
                ];
            }

            $pagefilespath = [get_string('privacy:path:pages', 'mod_lesson'), $record->page_id];
            writer::with_context($context)->export_area_files($pagefilespath, 'mod_lesson', 'page_contents', $record->page_id);
            $pagecontents = format_text(
                writer::with_context($context)->rewrite_pluginfile_urls(
                    $pagefilespath,
                    'mod_lesson',
                    'page_contents',
                    $record->page_id,
                    $record->page_contents
                ),
                $record->page_contentsformat,
                $options
            );

            $pagebase = [
                'id' => $record->page_id,
                'page' => $record->page_title,
                'contents' => $pagecontents,
                'contents_files_folder' => implode('/', $pagefilespath)
            ];

            if (isset($record->attempt_id)) {
                $carry[$take]->answers[] = array_merge($pagebase, static::transform_attempt($record, $context));

            } else if (isset($record->branch_id)) {
                if (!empty($record->nextpage_id)) {
                    $wentto = $record->nextpage_title . " (id: {$record->nextpage_id})";
                } else {
                    $wentto = get_string('endoflesson', 'mod_lesson');
                }
                $carry[$take]->jumps[] = array_merge($pagebase, [
                    'went_to' => $wentto,
                    'timeseen' => transform::datetime($record->attempt_timeseen)
                ]);
            }

            return $carry;

        }, function($lessonid, $data) use ($lessonidstocmids) {
            $context = context_module::instance($lessonidstocmids[$lessonid]);
            writer::with_context($context)->export_related_data([], 'attempts', (object) [
                'attempts' => array_values($data)
            ]);
        });
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $lessonview = get_user_preferences('lesson_view', null, $userid);
        if ($lessonview !== null) {
            $value = $lessonview;

            // The code seems to indicate that there also is the option 'simple', but it's not
            // described nor accessible from anywhere so we won't describe it more than being 'simple'.
            if ($lessonview == 'full') {
                $value = get_string('full', 'mod_lesson');
            } else if ($lessonview == 'collapsed') {
                $value = get_string('collapsed', 'mod_lesson');
            }

            writer::export_user_preference('mod_lesson', 'lesson_view', $lessonview,
                get_string('privacy:metadata:userpref:lessonview', 'mod_lesson'));
        }
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

        if (!$lessonid = static::get_lesson_id_from_context($context)) {
            return;
        }

        $DB->delete_records('lesson_attempts', ['lessonid' => $lessonid]);
        $DB->delete_records('lesson_branch', ['lessonid' => $lessonid]);
        $DB->delete_records('lesson_grades', ['lessonid' => $lessonid]);
        $DB->delete_records('lesson_timer', ['lessonid' => $lessonid]);
        $DB->delete_records_select('lesson_overrides', 'lessonid = :id AND userid IS NOT NULL', ['id' => $lessonid]);

        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_lesson', 'essay_responses');
        $fs->delete_area_files($context->id, 'mod_lesson', 'essay_answers');
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

        // Find the lesson IDs.
        $lessonidstocmids = static::get_lesson_ids_to_cmids_from_cmids($cmids);
        $lessonids = array_keys($lessonidstocmids);
        if (empty($lessonids)) {
            return;
        }

        // Prepare the SQL we'll need below.
        list($insql, $inparams) = $DB->get_in_or_equal($lessonids, SQL_PARAMS_NAMED);
        $sql = "lessonid $insql AND userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        // Delete the attempt files.
        $fs = get_file_storage();
        $recordset = $DB->get_recordset_select('lesson_attempts', $sql, $params, '', 'id, lessonid');
        foreach ($recordset as $record) {
            $cmid = $lessonidstocmids[$record->lessonid];
            $context = context_module::instance($cmid);
            $fs->delete_area_files($context->id, 'mod_lesson', 'essay_responses', $record->id);
            $fs->delete_area_files($context->id, 'mod_lesson', 'essay_answers', $record->id);
        }
        $recordset->close();

        // Delete all the things.
        $DB->delete_records_select('lesson_attempts', $sql, $params);
        $DB->delete_records_select('lesson_branch', $sql, $params);
        $DB->delete_records_select('lesson_grades', $sql, $params);
        $DB->delete_records_select('lesson_timer', $sql, $params);
        $DB->delete_records_select('lesson_overrides', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist    $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $lessonid = static::get_lesson_id_from_context($context);
        $userids = $userlist->get_userids();

        if (empty($lessonid)) {
            return;
        }

        // Prepare the SQL we'll need below.
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "lessonid = :lessonid AND userid {$insql}";
        $params = array_merge($inparams, ['lessonid' => $lessonid]);

        // Delete the attempt files.
        $fs = get_file_storage();
        $recordset = $DB->get_recordset_select('lesson_attempts', $sql, $params, '', 'id, lessonid');
        foreach ($recordset as $record) {
            $fs->delete_area_files($context->id, 'mod_lesson', 'essay_responses', $record->id);
            $fs->delete_area_files($context->id, 'mod_lesson', 'essay_answers', $record->id);
        }
        $recordset->close();

        // Delete all the things.
        $DB->delete_records_select('lesson_attempts', $sql, $params);
        $DB->delete_records_select('lesson_branch', $sql, $params);
        $DB->delete_records_select('lesson_grades', $sql, $params);
        $DB->delete_records_select('lesson_timer', $sql, $params);
        $DB->delete_records_select('lesson_overrides', $sql, $params);
    }

    /**
     * Get a survey ID from its context.
     *
     * @param context_module $context The module context.
     * @return int
     */
    protected static function get_lesson_id_from_context(context_module $context) {
        $cm = get_coursemodule_from_id('lesson', $context->instanceid);
        return $cm ? (int) $cm->instance : 0;
    }

    /**
     * Return a dict of lesson IDs mapped to their course module ID.
     *
     * @param array $cmids The course module IDs.
     * @return array In the form of [$lessonid => $cmid].
     */
    protected static function get_lesson_ids_to_cmids_from_cmids(array $cmids) {
        global $DB;
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT l.id, cm.id AS cmid
              FROM {lesson} l
              JOIN {modules} m
                ON m.name = :lesson
              JOIN {course_modules} cm
                ON cm.instance = l.id
               AND cm.module = m.id
             WHERE cm.id $insql";
        $params = array_merge($inparams, ['lesson' => 'lesson']);
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

    /**
     * Transform an attempt.
     *
     * @param stdClass $data Data from the database, as per the exporting method.
     * @param context_module $context The module context.
     * @return array
     */
    protected static function transform_attempt(stdClass $data, context_module $context) {
        global $DB;

        $options = ['context' => $context];
        $answer = $data->attempt_useranswer;
        $response = null;
        $responsefilesfolder = null;

        if ($answer !== null) {
            if ($data->page_qtype == LESSON_PAGE_ESSAY) {
                // Essay questions serialise data in the answer field.
                $info = \lesson_page_type_essay::extract_useranswer($answer);
                $answerfilespath = [get_string('privacy:path:essayanswers', 'mod_lesson'), $data->attempt_id];
                $answer = format_text(
                    writer::with_context($context)->rewrite_pluginfile_urls(
                        $answerfilespath,
                        'mod_lesson',
                        'essay_answers',
                        $data->attempt_id,
                        $info->answer
                    ),
                    $info->answerformat,
                    $options
                );
                writer::with_context($context)->export_area_files($answerfilespath, 'mod_lesson',
                    'essay_answers', $data->page_id);

                if ($info->response !== null) {
                    // We export the files in a subfolder to avoid conflicting files, and tell the user
                    // where those files were exported. That is because we are not using a subfolder for
                    // every single essay response.
                    $responsefilespath = [get_string('privacy:path:essayresponses', 'mod_lesson'), $data->attempt_id];
                    $responsefilesfolder = implode('/', $responsefilespath);
                    $response = format_text(
                        writer::with_context($context)->rewrite_pluginfile_urls(
                            $responsefilespath,
                            'mod_lesson',
                            'essay_responses',
                            $data->attempt_id,
                            $info->response
                        ),
                        $info->responseformat,
                        $options
                    );
                    writer::with_context($context)->export_area_files($responsefilespath, 'mod_lesson',
                        'essay_responses', $data->page_id);

                }

            } else if ($data->page_qtype == LESSON_PAGE_MULTICHOICE && $data->page_qoption) {
                // Multiple choice quesitons with multiple answers encode the answers.
                list($insql, $inparams) = $DB->get_in_or_equal(explode(',', $answer), SQL_PARAMS_NAMED);
                $orderby = 'id, ' . $DB->sql_order_by_text('answer') . ', answerformat';
                $records = $DB->get_records_select('lesson_answers', "id $insql", $inparams, $orderby);
                $answer = array_values(array_map(function($record) use ($options) {
                    return format_text($record->answer, $record->answerformat, $options);
                }, empty($records) ? [] : $records));

            } else if ($data->page_qtype == LESSON_PAGE_MATCHING) {
                // Matching questions need sorting.
                $chosen = explode(',', $answer);
                $answers = $DB->get_records_select('lesson_answers', 'pageid = :pageid', ['pageid' => $data->page_id],
                    'id', 'id, answer, answerformat', 2); // The two first entries are not options.
                $i = -1;
                $answer = array_values(array_map(function($record) use (&$i, $chosen, $options) {
                    $i++;
                    return [
                        'label' => format_text($record->answer, $record->answerformat, $options),
                        'matched_with' => array_key_exists($i, $chosen) ? $chosen[$i] : null
                    ];
                }, empty($answers) ? [] : $answers));
            }
        }

        $result = [
            'answer' => $answer,
            'correct' => transform::yesno($data->attempt_correct),
            'timeseen' => transform::datetime($data->attempt_timeseen),
        ];

        if ($response !== null) {
            $result['response'] = $response;
            $result['response_files_folder'] = $responsefilesfolder;
        }

        return $result;
    }

}
