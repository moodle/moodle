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
 * @package    core_grades
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_grades\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_course;
use context_system;
use grade_item;
use grade_grade;
use grade_scale;
use stdClass;
use core_grades\privacy\grade_grade_with_history;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

require_once($CFG->libdir . '/gradelib.php');

/**
 * Data provider class.
 *
 * @package    core_grades
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        // Tables without 'real' user information.
        $collection->add_database_table('grade_outcomes', [
            'timemodified' => 'privacy:metadata:outcomes:timemodified',
            'usermodified' => 'privacy:metadata:outcomes:usermodified',
        ], 'privacy:metadata:outcomes');

        $collection->add_database_table('grade_outcomes_history', [
            'timemodified' => 'privacy:metadata:history:timemodified',
            'loggeduser' => 'privacy:metadata:history:loggeduser',
        ], 'privacy:metadata:outcomeshistory');

        $collection->add_database_table('grade_categories_history', [
            'timemodified' => 'privacy:metadata:history:timemodified',
            'loggeduser' => 'privacy:metadata:history:loggeduser',
        ], 'privacy:metadata:categorieshistory');

        $collection->add_database_table('grade_items_history', [
            'timemodified' => 'privacy:metadata:history:timemodified',
            'loggeduser' => 'privacy:metadata:history:loggeduser',
        ], 'privacy:metadata:itemshistory');

        $collection->add_database_table('scale', [
            'userid' => 'privacy:metadata:scale:userid',
            'timemodified' => 'privacy:metadata:scale:timemodified',
        ], 'privacy:metadata:scale');

        $collection->add_database_table('scale_history', [
            'userid' => 'privacy:metadata:scale:userid',
            'timemodified' => 'privacy:metadata:history:timemodified',
            'loggeduser' => 'privacy:metadata:history:loggeduser',
        ], 'privacy:metadata:scalehistory');

        // Table with user information.
        $gradescommonfields = [
            'userid' => 'privacy:metadata:grades:userid',
            'usermodified' => 'privacy:metadata:grades:usermodified',
            'finalgrade' => 'privacy:metadata:grades:finalgrade',
            'feedback' => 'privacy:metadata:grades:feedback',
            'information' => 'privacy:metadata:grades:information',
        ];

        $collection->add_database_table('grade_grades', array_merge($gradescommonfields, [
            'timemodified' => 'privacy:metadata:grades:timemodified',
        ]), 'privacy:metadata:grades');

        $collection->add_database_table('grade_grades_history', array_merge($gradescommonfields, [
            'timemodified' => 'privacy:metadata:history:timemodified',
            'loggeduser' => 'privacy:metadata:history:loggeduser',
        ]), 'privacy:metadata:gradeshistory');

        // The following tables are reported but not exported/deleted because their data is temporary and only
        // used during an import. It's content is deleted after a successful, or failed, import.

        $collection->add_database_table('grade_import_newitem', [
            'itemname' => 'privacy:metadata:grade_import_newitem:itemname',
            'importcode' => 'privacy:metadata:grade_import_newitem:importcode',
            'importer' => 'privacy:metadata:grade_import_newitem:importer'
        ], 'privacy:metadata:grade_import_newitem');

        $collection->add_database_table('grade_import_values', [
            'userid' => 'privacy:metadata:grade_import_values:userid',
            'finalgrade' => 'privacy:metadata:grade_import_values:finalgrade',
            'feedback' => 'privacy:metadata:grade_import_values:feedback',
            'importcode' => 'privacy:metadata:grade_import_values:importcode',
            'importer' => 'privacy:metadata:grade_import_values:importer',
            'importonlyfeedback' => 'privacy:metadata:grade_import_values:importonlyfeedback'
        ], 'privacy:metadata:grade_import_values');

        $collection->link_subsystem('core_files', 'privacy:metadata:filepurpose');

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

        // Add where we modified outcomes.
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {grade_outcomes} go
              JOIN {context} ctx
                ON (go.courseid > 0 AND ctx.instanceid = go.courseid AND ctx.contextlevel = :courselevel)
                OR ((go.courseid IS NULL OR go.courseid < 1) AND ctx.id = :syscontextid)
             WHERE go.usermodified = :userid";
        $params = ['userid' => $userid, 'courselevel' => CONTEXT_COURSE, 'syscontextid' => SYSCONTEXTID];
        $contextlist->add_from_sql($sql, $params);

        // Add where we modified scales.
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {scale} s
              JOIN {context} ctx
                ON (s.courseid > 0 AND ctx.instanceid = s.courseid AND ctx.contextlevel = :courselevel)
                OR (s.courseid = 0 AND ctx.id = :syscontextid)
             WHERE s.userid = :userid";
        $params = ['userid' => $userid, 'courselevel' => CONTEXT_COURSE, 'syscontextid' => SYSCONTEXTID];
        $contextlist->add_from_sql($sql, $params);

        // Add where appear in the history of outcomes, categories, scales or items.
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {grade_outcomes_history} goh ON goh.loggeduser = :userid1 AND goh.courseid > 0
               AND goh.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel1";
        $params = [
            'courselevel1' => CONTEXT_COURSE,
            'userid1' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {grade_outcomes_history} goh ON goh.loggeduser = :userid1
               AND (goh.courseid IS NULL OR goh.courseid < 1) AND ctx.id = :syscontextid1";
        $params = [
            'syscontextid1' => SYSCONTEXTID,
            'courselevel1' => CONTEXT_COURSE,
            'userid1' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {grade_categories_history} gch ON gch.loggeduser = :userid2
               AND gch.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel2";
        $params = [
            'courselevel2' => CONTEXT_COURSE,
            'userid2' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {grade_items_history} gih ON gih.loggeduser = :userid3
               AND gih.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel3";
        $params = [
            'courselevel3' => CONTEXT_COURSE,
            'userid3' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {scale_history} sh ON sh.userid = :userid4
               AND sh.courseid > 0 AND sh.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel4";
        $params = [
            'courselevel4' => CONTEXT_COURSE,
            'userid4' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {scale_history} sh ON sh.loggeduser = :userid5
               AND sh.courseid > 0 AND sh.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel4";
        $params = [
            'courselevel4' => CONTEXT_COURSE,
            'userid5' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {scale_history} sh ON sh.userid = :userid4 AND sh.courseid = 0 AND ctx.id = :syscontextid2";
        $params = [
            'syscontextid2' => SYSCONTEXTID,
            'userid4' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {context} ctx
              JOIN {scale_history} sh ON sh.loggeduser = :userid5 AND sh.courseid = 0 AND ctx.id = :syscontextid2";
        $params = [
            'syscontextid2' => SYSCONTEXTID,
            'userid5' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Add where we were graded or modified grades, including in the history table.
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {grade_items} gi
              JOIN {context} ctx
                ON ctx.instanceid = gi.courseid
               AND ctx.contextlevel = :courselevel
              JOIN {grade_grades} gg
                ON gg.itemid = gi.id
             WHERE gg.userid = :userid1 OR gg.usermodified = :userid2";
        $params = [
            'courselevel' => CONTEXT_COURSE,
            'userid1' => $userid,
            'userid2' => $userid
        ];
        $contextlist->add_from_sql($sql, $params);

        $sql = "
            SELECT DISTINCT ctx.id
              FROM {grade_items} gi
              JOIN {context} ctx
                ON ctx.instanceid = gi.courseid
               AND ctx.contextlevel = :courselevel
              JOIN {grade_grades_history} ggh
                ON ggh.itemid = gi.id
             WHERE ggh.userid = :userid1
                OR ggh.loggeduser = :userid2
                OR ggh.usermodified = :userid3";
        $params = [
            'courselevel' => CONTEXT_COURSE,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid
        ];
        $contextlist->add_from_sql($sql, $params);

        // Historical grades can be made orphans when the corresponding itemid is deleted. When that happens
        // we cannot tie the historical grade to a course context, so we report the user context as a last resort.
        $sql = "
           SELECT DISTINCT ctx.id
             FROM {context} ctx
             JOIN {grade_grades_history} ggh
               ON ctx.contextlevel = :userlevel
              AND ggh.userid = ctx.instanceid
              AND (
                  ggh.userid = :userid1
               OR ggh.usermodified = :userid2
               OR ggh.loggeduser = :userid3
              )
        LEFT JOIN {grade_items} gi
               ON ggh.itemid = gi.id
            WHERE gi.id IS NULL";
        $params = [
            'userlevel' => CONTEXT_USER,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   \core_privacy\local\request\userlist    $userlist   The userlist containing the list of users who have data
     * in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel == CONTEXT_COURSE) {
            $params = ['contextinstanceid' => $context->instanceid];

            $sql = "SELECT usermodified
                      FROM {grade_outcomes}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('usermodified', $sql, $params);

            $sql = "SELECT loggeduser
                      FROM {grade_outcomes_history}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('loggeduser', $sql, $params);

            $sql = "SELECT userid
                      FROM {scale}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT loggeduser, userid
                      FROM {scale_history}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('loggeduser', $sql, $params);
            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT loggeduser
                      FROM {grade_items_history}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('loggeduser', $sql, $params);

            $sql = "SELECT ggh.userid
                      FROM {grade_grades_history} ggh
                      JOIN {grade_items} gi ON ggh.itemid = gi.id
                     WHERE gi.courseid = :contextinstanceid";
            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT gg.userid, gg.usermodified
                      FROM {grade_grades} gg
                      JOIN {grade_items} gi ON gg.itemid = gi.id
                     WHERE gi.courseid = :contextinstanceid";
            $userlist->add_from_sql('userid', $sql, $params);
            $userlist->add_from_sql('usermodified', $sql, $params);

            $sql = "SELECT loggeduser
                      FROM {grade_categories_history}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('loggeduser', $sql, $params);
        }

        // None of these are currently used (user deletion).
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $params = ['contextinstanceid' => 0];

            $sql = "SELECT usermodified
                      FROM {grade_outcomes}
                     WHERE (courseid IS NULL OR courseid < 1)";
            $userlist->add_from_sql('usermodified', $sql, []);

            $sql = "SELECT loggeduser
                      FROM {grade_outcomes_history}
                     WHERE (courseid IS NULL OR courseid < 1)";
            $userlist->add_from_sql('loggeduser', $sql, []);

            $sql = "SELECT userid
                      FROM {scale}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT loggeduser, userid
                      FROM {scale_history}
                     WHERE courseid = :contextinstanceid";
            $userlist->add_from_sql('loggeduser', $sql, $params);
            $userlist->add_from_sql('userid', $sql, $params);
        }

        if ($context->contextlevel == CONTEXT_USER) {
            // If the grade item has been removed and we have an orphan entry then we link to the
            // user context.
            $sql = "SELECT ggh.userid
                      FROM {grade_grades_history} ggh
                 LEFT JOIN {grade_items} gi ON ggh.itemid = gi.id
                     WHERE gi.id IS NULL
                       AND ggh.userid = :contextinstanceid";
            $userlist->add_from_sql('userid', $sql, ['contextinstanceid' => $context->instanceid]);
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
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) use ($userid) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                $carry[$context->contextlevel][] = $context;

            } else if ($context->contextlevel == CONTEXT_USER) {
                $carry[$context->contextlevel][] = $context;

            }

            return $carry;
        }, [
            CONTEXT_USER => [],
            CONTEXT_COURSE => []
        ]);

        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);

        // Export the outcomes.
        static::export_user_data_outcomes_in_contexts($contextlist);

        // Export the scales.
        static::export_user_data_scales_in_contexts($contextlist);

        // Export the historical grades which have become orphans (their grade items were deleted).
        // We place those in ther user context of the graded user.
        $userids = array_values(array_map(function($context) {
            return $context->instanceid;
        }, $contexts[CONTEXT_USER]));
        if (!empty($userids)) {

            // Export own historical grades and related ones.
            list($inuseridsql, $inuseridparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            list($inusermodifiedsql, $inusermodifiedparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            list($inloggedusersql, $inloggeduserparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $usercontext = $contexts[CONTEXT_USER];
            $gghfields = static::get_fields_sql('grade_grades_history', 'ggh', 'ggh_');
            $sql = "
                SELECT $gghfields, ctx.id as ctxid
                  FROM {grade_grades_history} ggh
                  JOIN {context} ctx
                    ON ctx.instanceid = ggh.userid
                   AND ctx.contextlevel = :userlevel
             LEFT JOIN {grade_items} gi
                    ON gi.id = ggh.itemid
                 WHERE gi.id IS NULL
                   AND (ggh.userid $inuseridsql
                    OR ggh.usermodified $inusermodifiedsql
                    OR ggh.loggeduser $inloggedusersql)
                   AND (ggh.userid = :userid1
                    OR ggh.usermodified = :userid2
                    OR ggh.loggeduser = :userid3)
              ORDER BY ggh.userid, ggh.timemodified, ggh.id";
            $params = array_merge($inuseridparams, $inusermodifiedparams, $inloggeduserparams,
                ['userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid, 'userlevel' => CONTEXT_USER]);

            $deletedstr = get_string('privacy:request:unknowndeletedgradeitem', 'core_grades');
            $recordset = $DB->get_recordset_sql($sql, $params);
            static::recordset_loop_and_export($recordset, 'ctxid', [], function($carry, $record) use ($deletedstr, $userid) {
                $context = context::instance_by_id($record->ctxid);
                $gghrecord = static::extract_record($record, 'ggh_');

                // Orphan grades do not have items, so we do not recreate a grade_grade item, and we do not format grades.
                $carry[] = [
                    'name' => $deletedstr,
                    'graded_user_was_you' => transform::yesno($userid == $gghrecord->userid),
                    'grade' => $gghrecord->finalgrade,
                    'feedback' => format_text($gghrecord->feedback, $gghrecord->feedbackformat, ['context' => $context]),
                    'information' => format_text($gghrecord->information, $gghrecord->informationformat, ['context' => $context]),
                    'timemodified' => transform::datetime($gghrecord->timemodified),
                    'logged_in_user_was_you' => transform::yesno($userid == $gghrecord->loggeduser),
                    'author_of_change_was_you' => transform::yesno($userid == $gghrecord->usermodified),
                    'action' => static::transform_history_action($gghrecord->action)
                ];

                return $carry;

            }, function($ctxid, $data) use ($rootpath) {
                $context = context::instance_by_id($ctxid);
                writer::with_context($context)->export_related_data($rootpath, 'history', (object) ['grades' => $data]);
            });
        }

        // Find out the course IDs.
        $courseids = array_values(array_map(function($context) {
            return $context->instanceid;
        }, $contexts[CONTEXT_COURSE]));
        if (empty($courseids)) {
            return;
        }
        list($incoursesql, $incourseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        // Ensure that the grades are final and do not need regrading.
        array_walk($courseids, function($courseid) {
            grade_regrade_final_grades($courseid);
        });

        // Export own grades.
        $ggfields = static::get_fields_sql('grade_grade', 'gg', 'gg_');
        $gifields = static::get_fields_sql('grade_item', 'gi', 'gi_');
        $scalefields = static::get_fields_sql('grade_scale', 'sc', 'sc_');
        $sql = "
            SELECT $ggfields, $gifields, $scalefields
              FROM {grade_grades} gg
              JOIN {grade_items} gi
                ON gi.id = gg.itemid
         LEFT JOIN {scale} sc
                ON sc.id = gi.scaleid
             WHERE gi.courseid $incoursesql
               AND gg.userid = :userid
          ORDER BY gi.courseid, gi.id, gg.id";
        $params = array_merge($incourseparams, ['userid' => $userid]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'gi_courseid', [], function($carry, $record) {
            $context = context_course::instance($record->gi_courseid);
            $gg = static::extract_grade_grade_from_record($record);
            $carry[] = static::transform_grade($gg, $context, false);

            return $carry;

        }, function($courseid, $data) use ($rootpath) {
            $context = context_course::instance($courseid);

            $pathtofiles = [
                get_string('grades', 'core_grades'),
                get_string('feedbackfiles', 'core_grades')
            ];
            foreach ($data as $key => $grades) {
                $gg = $grades['gradeobject'];
                writer::with_context($gg->get_context())->export_area_files($pathtofiles, GRADE_FILE_COMPONENT,
                    GRADE_FEEDBACK_FILEAREA, $gg->id);
                unset($data[$key]['gradeobject']); // Do not want to export this later.
            }

            writer::with_context($context)->export_data($rootpath, (object) ['grades' => $data]);
        });

        // Export own historical grades in courses.
        $gghfields = static::get_fields_sql('grade_grades_history', 'ggh', 'ggh_');
        $sql = "
            SELECT $gghfields, $gifields, $scalefields
              FROM {grade_grades_history} ggh
              JOIN {grade_items} gi
                ON gi.id = ggh.itemid
         LEFT JOIN {scale} sc
                ON sc.id = gi.scaleid
             WHERE gi.courseid $incoursesql
               AND ggh.userid = :userid
          ORDER BY gi.courseid, ggh.timemodified, ggh.id";
        $params = array_merge($incourseparams, ['userid' => $userid]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'gi_courseid', [], function($carry, $record) {
            $context = context_course::instance($record->gi_courseid);
            $gg = static::extract_grade_grade_from_record($record, true);
            $carry[] = array_merge(static::transform_grade($gg, $context, true), [
                'action' => static::transform_history_action($record->ggh_action)
            ]);
            return $carry;

        }, function($courseid, $data) use ($rootpath) {
            $context = context_course::instance($courseid);

            $pathtofiles = [
                get_string('grades', 'core_grades'),
                get_string('feedbackhistoryfiles', 'core_grades')
            ];
            foreach ($data as $key => $grades) {
                /** @var grade_grade_with_history */
                $gg = $grades['gradeobject'];
                writer::with_context($gg->get_context())->export_area_files($pathtofiles, GRADE_FILE_COMPONENT,
                    GRADE_HISTORY_FEEDBACK_FILEAREA, $gg->historyid);
                unset($data[$key]['gradeobject']); // Do not want to export this later.
            }

            writer::with_context($context)->export_related_data($rootpath, 'history', (object) ['grades' => $data]);
        });

        // Export edits of categories history.
        $sql = "
            SELECT gch.id, gch.courseid, gch.fullname, gch.timemodified, gch.action
              FROM {grade_categories_history} gch
             WHERE gch.courseid $incoursesql
               AND gch.loggeduser = :userid
          ORDER BY gch.courseid, gch.timemodified, gch.id";
        $params = array_merge($incourseparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) {
            $carry[] = [
                'name' => $record->fullname,
                'timemodified' => transform::datetime($record->timemodified),
                'logged_in_user_was_you' => transform::yesno(true),
                'action' => static::transform_history_action($record->action),
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = context_course::instance($courseid);
            writer::with_context($context)->export_related_data($relatedtomepath, 'categories_history',
                (object) ['modified_records' => $data]);
        });

        // Export edits of items history.
        $sql = "
            SELECT gih.id, gih.courseid, gih.itemname, gih.itemmodule, gih.iteminfo, gih.timemodified, gih.action
              FROM {grade_items_history} gih
             WHERE gih.courseid $incoursesql
               AND gih.loggeduser = :userid
          ORDER BY gih.courseid, gih.timemodified, gih.id";
        $params = array_merge($incourseparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) {
            $carry[] = [
                'name' => $record->itemname,
                'module' => $record->itemmodule,
                'info' => $record->iteminfo,
                'timemodified' => transform::datetime($record->timemodified),
                'logged_in_user_was_you' => transform::yesno(true),
                'action' => static::transform_history_action($record->action),
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = context_course::instance($courseid);
            writer::with_context($context)->export_related_data($relatedtomepath, 'items_history',
                (object) ['modified_records' => $data]);
        });

        // Export edits of grades in course.
        $sql = "
            SELECT $ggfields, $gifields, $scalefields
              FROM {grade_grades} gg
              JOIN {grade_items} gi
                ON gg.itemid = gi.id
         LEFT JOIN {scale} sc
                ON sc.id = gi.scaleid
             WHERE gi.courseid $incoursesql
               AND gg.userid <> :userid1    -- Our grades have already been exported.
               AND gg.usermodified = :userid2
          ORDER BY gi.courseid, gg.timemodified, gg.id";
        $params = array_merge($incourseparams, ['userid1' => $userid, 'userid2' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'gi_courseid', [], function($carry, $record) {
            $context = context_course::instance($record->gi_courseid);
            $gg = static::extract_grade_grade_from_record($record);
            $carry[] = array_merge(static::transform_grade($gg, $context, false), [
                'userid' => transform::user($gg->userid),
                'created_or_modified_by_you' => transform::yesno(true),
            ]);
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = context_course::instance($courseid);

            $pathtofiles = [
                get_string('grades', 'core_grades'),
                get_string('feedbackfiles', 'core_grades')
            ];
            foreach ($data as $key => $grades) {
                $gg = $grades['gradeobject'];
                writer::with_context($gg->get_context())->export_area_files($pathtofiles, GRADE_FILE_COMPONENT,
                    GRADE_FEEDBACK_FILEAREA, $gg->id);
                unset($data[$key]['gradeobject']); // Do not want to export this later.
            }

            writer::with_context($context)->export_related_data($relatedtomepath, 'grades', (object) ['grades' => $data]);
        });

        // Export edits of grades history in course.
        $sql = "
            SELECT $gghfields, $gifields, $scalefields, ggh.loggeduser AS loggeduser
              FROM {grade_grades_history} ggh
              JOIN {grade_items} gi
                ON ggh.itemid = gi.id
         LEFT JOIN {scale} sc
                ON sc.id = gi.scaleid
             WHERE gi.courseid $incoursesql
               AND ggh.userid <> :userid1   -- We've already exported our history.
               AND (ggh.loggeduser = :userid2
                OR ggh.usermodified = :userid3)
          ORDER BY gi.courseid, ggh.timemodified, ggh.id";
        $params = array_merge($incourseparams, ['userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'gi_courseid', [], function($carry, $record) use ($userid) {
            $context = context_course::instance($record->gi_courseid);
            $gg = static::extract_grade_grade_from_record($record, true);
            $carry[] = array_merge(static::transform_grade($gg, $context, true), [
                'userid' => transform::user($gg->userid),
                'logged_in_user_was_you' => transform::yesno($userid == $record->loggeduser),
                'author_of_change_was_you' => transform::yesno($userid == $gg->usermodified),
                'action' => static::transform_history_action($record->ggh_action),
            ]);
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = context_course::instance($courseid);

            $pathtofiles = [
                get_string('grades', 'core_grades'),
                get_string('feedbackhistoryfiles', 'core_grades')
            ];
            foreach ($data as $key => $grades) {
                /** @var grade_grade_with_history */
                $gg = $grades['gradeobject'];
                writer::with_context($gg->get_context())->export_area_files($pathtofiles, GRADE_FILE_COMPONENT,
                    GRADE_HISTORY_FEEDBACK_FILEAREA, $gg->historyid);
                unset($data[$key]['gradeobject']); // Do not want to export this later.
            }

            writer::with_context($context)->export_related_data($relatedtomepath, 'grades_history',
                (object) ['modified_records' => $data]);
        });
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;

        switch ($context->contextlevel) {
            case CONTEXT_USER:
                // The user context is only reported when there are orphan historical grades, so we only delete those.
                static::delete_orphan_historical_grades($context->instanceid);
                break;

            case CONTEXT_COURSE:
                // We must not change the structure of the course, so we only delete user content.
                $itemids = static::get_item_ids_from_course_ids([$context->instanceid]);
                if (empty($itemids)) {
                    return;
                }

                self::delete_files($itemids, true);
                self::delete_files($itemids, false);

                list($insql, $inparams) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
                $DB->delete_records_select('grade_grades', "itemid $insql", $inparams);
                $DB->delete_records_select('grade_grades_history', "itemid $insql", $inparams);
                break;
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

        $courseids = [];
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER && $userid == $context->instanceid) {
                // User attempts to delete data in their own context.
                static::delete_orphan_historical_grades($userid);

            } else if ($context->contextlevel == CONTEXT_COURSE) {
                // Log the list of course IDs.
                $courseids[] = $context->instanceid;
            }
        }

        $itemids = static::get_item_ids_from_course_ids($courseids);
        if (empty($itemids)) {
            // Our job here is done!
            return;
        }

        // Delete all the files.
        self::delete_files($itemids, true, [$userid]);
        self::delete_files($itemids, false, [$userid]);

        // Delete all the grades.
        list($insql, $inparams) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $userid]);

        $DB->delete_records_select('grade_grades', "itemid $insql AND userid = :userid", $params);
        $DB->delete_records_select('grade_grades_history', "itemid $insql AND userid = :userid", $params);
    }


    /**
     * Delete multiple users within a single context.
     *
     * @param   \core_privacy\local\request\approved_userlist $userlist The approved context and user information to
     * delete information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();
        if ($context->contextlevel == CONTEXT_USER) {
            if (array_search($context->instanceid, $userids) !== false) {
                static::delete_orphan_historical_grades($context->instanceid);
            }
            return;
        }

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $itemids = static::get_item_ids_from_course_ids([$context->instanceid]);
        if (empty($itemids)) {
            // Our job here is done!
            return;
        }

        // Delete all the files.
        self::delete_files($itemids, true, $userids);
        self::delete_files($itemids, false, $userids);

        // Delete all the grades.
        list($itemsql, $itemparams) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = array_merge($itemparams, $userparams);

        $DB->delete_records_select('grade_grades', "itemid $itemsql AND userid $usersql", $params);
        $DB->delete_records_select('grade_grades_history', "itemid $itemsql AND userid $usersql", $params);
    }

    /**
     * Delete orphan historical grades.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_orphan_historical_grades($userid) {
        global $DB;
        $sql = "
            SELECT ggh.id
              FROM {grade_grades_history} ggh
         LEFT JOIN {grade_items} gi
                ON ggh.itemid = gi.id
             WHERE gi.id IS NULL
               AND ggh.userid = :userid";
        $ids = $DB->get_fieldset_sql($sql, ['userid' => $userid]);
        if (empty($ids)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);

        // First, let's delete their files.
        $sql = "
            SELECT gi.id
              FROM {grade_grades_history} ggh
              JOIN {grade_items} gi
                ON gi.id = ggh.itemid
             WHERE ggh.userid = :userid";
        $params = ['userid' => $userid];
        $gradeitems = $DB->get_records_sql($sql, $params);
        if ($gradeitems) {
            $itemids = array_keys($gradeitems);
            self::delete_files($itemids, true, [$userid]);
        }

        $DB->delete_records_select('grade_grades_history', "id $insql", $inparams);
    }

    /**
     * Export the user data related to outcomes.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     * @return void
     */
    protected static function export_user_data_outcomes_in_contexts(approved_contextlist $contextlist) {
        global $DB;

        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);
        $userid = $contextlist->get_user()->id;

        // Reorganise the contexts.
        $reduced = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $carry['in_system'] = true;
            } else if ($context->contextlevel == CONTEXT_COURSE) {
                $carry['courseids'][] = $context->instanceid;
            }
            return $carry;
        }, [
            'in_system' => false,
            'courseids' => []
        ]);

        // Construct SQL.
        $sqltemplateparts = [];
        $templateparams = [];
        if ($reduced['in_system']) {
            $sqltemplateparts[] = '{prefix}.courseid IS NULL';
        }
        if (!empty($reduced['courseids'])) {
            list($insql, $inparams) = $DB->get_in_or_equal($reduced['courseids'], SQL_PARAMS_NAMED);
            $sqltemplateparts[] = "{prefix}.courseid $insql";
            $templateparams = array_merge($templateparams, $inparams);
        }
        if (empty($sqltemplateparts)) {
            return;
        }
        $sqltemplate = '(' . implode(' OR ', $sqltemplateparts) . ')';

        // Export edited outcomes.
        $sqlwhere = str_replace('{prefix}', 'go', $sqltemplate);
        $sql = "
            SELECT go.id, COALESCE(go.courseid, 0) AS courseid, go.shortname, go.fullname, go.timemodified
              FROM {grade_outcomes} go
             WHERE $sqlwhere
               AND go.usermodified = :userid
          ORDER BY go.courseid, go.timemodified, go.id";
        $params = array_merge($templateparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) {
            $carry[] = [
                'shortname' => $record->shortname,
                'fullname' => $record->fullname,
                'timemodified' => transform::datetime($record->timemodified),
                'created_or_modified_by_you' => transform::yesno(true)
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = $courseid ? context_course::instance($courseid) : context_system::instance();
            writer::with_context($context)->export_related_data($relatedtomepath, 'outcomes',
                (object) ['outcomes' => $data]);
        });

        // Export edits of outcomes history.
        $sqlwhere = str_replace('{prefix}', 'goh', $sqltemplate);
        $sql = "
            SELECT goh.id, COALESCE(goh.courseid, 0) AS courseid, goh.shortname, goh.fullname, goh.timemodified, goh.action
              FROM {grade_outcomes_history} goh
             WHERE $sqlwhere
               AND goh.loggeduser = :userid
          ORDER BY goh.courseid, goh.timemodified, goh.id";
        $params = array_merge($templateparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) {
            $carry[] = [
                'shortname' => $record->shortname,
                'fullname' => $record->fullname,
                'timemodified' => transform::datetime($record->timemodified),
                'logged_in_user_was_you' => transform::yesno(true),
                'action' => static::transform_history_action($record->action)
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = $courseid ? context_course::instance($courseid) : context_system::instance();
            writer::with_context($context)->export_related_data($relatedtomepath, 'outcomes_history',
                (object) ['modified_records' => $data]);
        });
    }

    /**
     * Export the user data related to scales.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     * @return void
     */
    protected static function export_user_data_scales_in_contexts(approved_contextlist $contextlist) {
        global $DB;

        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);
        $userid = $contextlist->get_user()->id;

        // Reorganise the contexts.
        $reduced = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $carry['in_system'] = true;
            } else if ($context->contextlevel == CONTEXT_COURSE) {
                $carry['courseids'][] = $context->instanceid;
            }
            return $carry;
        }, [
            'in_system' => false,
            'courseids' => []
        ]);

        // Construct SQL.
        $sqltemplateparts = [];
        $templateparams = [];
        if ($reduced['in_system']) {
            $sqltemplateparts[] = '{prefix}.courseid = 0';
        }
        if (!empty($reduced['courseids'])) {
            list($insql, $inparams) = $DB->get_in_or_equal($reduced['courseids'], SQL_PARAMS_NAMED);
            $sqltemplateparts[] = "{prefix}.courseid $insql";
            $templateparams = array_merge($templateparams, $inparams);
        }
        if (empty($sqltemplateparts)) {
            return;
        }
        $sqltemplate = '(' . implode(' OR ', $sqltemplateparts) . ')';

        // Export edited scales.
        $sqlwhere = str_replace('{prefix}', 's', $sqltemplate);
        $sql = "
            SELECT s.id, s.courseid, s.name, s.timemodified
              FROM {scale} s
             WHERE $sqlwhere
               AND s.userid = :userid
          ORDER BY s.courseid, s.timemodified, s.id";
        $params = array_merge($templateparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) {
            $carry[] = [
                'name' => $record->name,
                'timemodified' => transform::datetime($record->timemodified),
                'created_or_modified_by_you' => transform::yesno(true)
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = $courseid ? context_course::instance($courseid) : context_system::instance();
            writer::with_context($context)->export_related_data($relatedtomepath, 'scales',
                (object) ['scales' => $data]);
        });

        // Export edits of scales history.
        $sqlwhere = str_replace('{prefix}', 'sh', $sqltemplate);
        $sql = "
            SELECT sh.id, sh.courseid, sh.name, sh.userid, sh.timemodified, sh.action, sh.loggeduser
              FROM {scale_history} sh
             WHERE $sqlwhere
               AND sh.loggeduser = :userid1
                OR sh.userid = :userid2
          ORDER BY sh.courseid, sh.timemodified, sh.id";
        $params = array_merge($templateparams, ['userid1' => $userid, 'userid2' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'courseid', [], function($carry, $record) use ($userid) {
            $carry[] = [
                'name' => $record->name,
                'timemodified' => transform::datetime($record->timemodified),
                'author_of_change_was_you' => transform::yesno($record->userid == $userid),
                'author_of_action_was_you' => transform::yesno($record->loggeduser == $userid),
                'action' => static::transform_history_action($record->action)
            ];
            return $carry;

        }, function($courseid, $data) use ($relatedtomepath) {
            $context = $courseid ? context_course::instance($courseid) : context_system::instance();
            writer::with_context($context)->export_related_data($relatedtomepath, 'scales_history',
                (object) ['modified_records' => $data]);
        });
    }

    /**
     * Extract grade_grade from a record.
     *
     * @param stdClass $record The record.
     * @param bool $ishistory Whether we're extracting a historical grade.
     * @return grade_grade
     */
    protected static function extract_grade_grade_from_record(stdClass $record, $ishistory = false) {
        $prefix = $ishistory ? 'ggh_' : 'gg_';
        $ggrecord = static::extract_record($record, $prefix);
        if ($ishistory) {
            $gg = new grade_grade_with_history($ggrecord, false);
        } else {
            $gg = new grade_grade($ggrecord, false);
        }

        // There is a grade item in the record.
        if (!empty($record->gi_id)) {
            $gi = new grade_item(static::extract_record($record, 'gi_'), false);
            $gg->grade_item = $gi;  // This is a common hack throughout the grades API.
        }

        // Load the scale, when it still exists.
        if (!empty($gi->scaleid) && !empty($record->sc_id)) {
            $scalerec = static::extract_record($record, 'sc_');
            $gi->scale = new grade_scale($scalerec, false);
            $gi->scale->load_items();
        }

        return $gg;
    }

    /**
     * Extract a record from another one.
     *
     * @param object $record The record to extract from.
     * @param string $prefix The prefix used.
     * @return object
     */
    protected static function extract_record($record, $prefix) {
        $result = [];
        $prefixlength = strlen($prefix);
        foreach ($record as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $result[substr($key, $prefixlength)] = $value;
            }
        }
        return (object) $result;
    }

    /**
     * Get fields SQL for a grade related object.
     *
     * @param string $target The related object.
     * @param string $alias The table alias.
     * @param string $prefix A prefix.
     * @return string
     */
    protected static function get_fields_sql($target, $alias, $prefix) {
        switch ($target) {
            case 'grade_category':
            case 'grade_grade':
            case 'grade_item':
            case 'grade_outcome':
            case 'grade_scale':
                $obj = new $target([], false);
                $fields = array_merge(array_keys($obj->optional_fields), $obj->required_fields);
                break;

            case 'grade_grades_history':
                $fields = ['id', 'action', 'oldid', 'source', 'timemodified', 'loggeduser', 'itemid', 'userid', 'rawgrade',
                    'rawgrademax', 'rawgrademin', 'rawscaleid', 'usermodified', 'finalgrade', 'hidden', 'locked', 'locktime',
                    'exported', 'overridden', 'excluded', 'feedback', 'feedbackformat', 'information', 'informationformat'];
                break;

            default:
                throw new \coding_exception('Unrecognised target: ' . $target);
                break;
        }

        return implode(', ', array_map(function($field) use ($alias, $prefix) {
            return "{$alias}.{$field} AS {$prefix}{$field}";
        }, $fields));
    }

    /**
     * Get all the items IDs from course IDs.
     *
     * @param array $courseids The course IDs.
     * @return array
     */
    protected static function get_item_ids_from_course_ids($courseids) {
        global $DB;
        if (empty($courseids)) {
            return [];
        }
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        return $DB->get_fieldset_select('grade_items', 'id', "courseid $insql", $inparams);
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
            if ($lastid !== null && $record->{$splitkey} != $lastid) {
                $export($lastid, $data);
                $data = $initial;
            }
            $data = $reducer($data, $record);
            $lastid = $record->{$splitkey};
        }
        $recordset->close();

        if ($lastid !== null) {
            $export($lastid, $data);
        }
    }

    /**
     * Transform an history action.
     *
     * @param int $action The action.
     * @return string
     */
    protected static function transform_history_action($action) {
        switch ($action) {
            case GRADE_HISTORY_INSERT:
                return get_string('privacy:request:historyactioninsert', 'core_grades');
                break;
            case GRADE_HISTORY_UPDATE:
                return get_string('privacy:request:historyactionupdate', 'core_grades');
                break;
            case GRADE_HISTORY_DELETE:
                return get_string('privacy:request:historyactiondelete', 'core_grades');
                break;
        }

        return '?';
    }

    /**
     * Transform a grade.
     *
     * @param grade_grade $gg The grade object.
     * @param context $context The context.
     * @param bool $ishistory Whether we're extracting a historical grade.
     * @return array
     */
    protected static function transform_grade(grade_grade $gg, context $context, bool $ishistory) {
        $gi = $gg->load_grade_item();
        $timemodified = $gg->timemodified ? transform::datetime($gg->timemodified) : null;
        $timecreated = $gg->timecreated ? transform::datetime($gg->timecreated) : $timemodified; // When null we use timemodified.

        if ($gg instanceof grade_grade_with_history) {
            $filearea = GRADE_HISTORY_FEEDBACK_FILEAREA;
            $itemid = $gg->historyid;
            $subpath = get_string('feedbackhistoryfiles', 'core_grades');
        } else {
            $filearea = GRADE_FEEDBACK_FILEAREA;
            $itemid = $gg->id;
            $subpath = get_string('feedbackfiles', 'core_grades');
        }

        $pathtofiles = [
            get_string('grades', 'core_grades'),
            $subpath
        ];
        $gg->feedback = writer::with_context($gg->get_context())->rewrite_pluginfile_urls(
            $pathtofiles,
            GRADE_FILE_COMPONENT,
            $filearea,
            $itemid,
            $gg->feedback
        );

        return [
            'gradeobject' => $gg,
            'item' => $gi->get_name(),
            'grade' => $gg->finalgrade,
            'grade_formatted' => grade_format_gradevalue($gg->finalgrade, $gi),
            'feedback' => format_text($gg->feedback, $gg->feedbackformat, ['context' => $context]),
            'information' => format_text($gg->information, $gg->informationformat, ['context' => $context]),
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
        ];
    }

    /**
     * Handles deleting files for a given list of grade items.
     *
     * If an array of userids if given then it handles deleting files for those users.
     *
     * @param array $itemids
     * @param bool $ishistory
     * @param array|null $userids
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function delete_files(array $itemids, bool $ishistory, ?array $userids = null) {
        global $DB;

        list($iteminnsql, $params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        if (!is_null($userids)) {
            list($userinnsql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params = array_merge($params, $userparams);
        }

        if ($ishistory) {
            $gradefields = static::get_fields_sql('grade_grades_history', 'ggh', 'ggh_');
            $gradetable = 'grade_grades_history';
            $tableprefix = 'ggh';
            $filearea = GRADE_HISTORY_FEEDBACK_FILEAREA;
        } else {
            $gradefields = static::get_fields_sql('grade_grade', 'gg', 'gg_');
            $gradetable = 'grade_grades';
            $tableprefix = 'gg';
            $filearea = GRADE_FEEDBACK_FILEAREA;
        }

        $gifields = static::get_fields_sql('grade_item', 'gi', 'gi_');

        $fs = new \file_storage();
        $sql = "SELECT $gradefields, $gifields
                  FROM {{$gradetable}} $tableprefix
                  JOIN {grade_items} gi
                    ON gi.id = {$tableprefix}.itemid
                 WHERE gi.id $iteminnsql ";
        if (!is_null($userids)) {
            $sql .= "AND {$tableprefix}.userid $userinnsql";
        }

        $grades = $DB->get_recordset_sql($sql, $params);
        foreach ($grades as $grade) {
            $gg = static::extract_grade_grade_from_record($grade, $ishistory);
            if ($gg instanceof grade_grade_with_history) {
                $fileitemid = $gg->historyid;
            } else {
                $fileitemid = $gg->id;
            }
            $fs->delete_area_files($gg->get_context()->id, GRADE_FILE_COMPONENT, $filearea, $fileitemid);
        }
        $grades->close();
    }
}
