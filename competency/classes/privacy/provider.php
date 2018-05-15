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
 * @package    core_competency
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_course;
use context_helper;
use context_module;
use context_system;
use context_user;
use moodle_recordset;
use core_competency\api;
use core_competency\competency;
use core_competency\competency_framework;
use core_competency\course_competency;
use core_competency\course_competency_settings;
use core_competency\course_module_competency;
use core_competency\evidence;
use core_competency\plan;
use core_competency\plan_competency;
use core_competency\related_competency;
use core_competency\template;
use core_competency\template_cohort;
use core_competency\template_competency;
use core_competency\user_competency;
use core_competency\user_competency_course;
use core_competency\user_competency_plan;
use core_competency\user_evidence;
use core_competency\user_evidence_competency;
use core_competency\external\performance_helper;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Data provider class.
 *
 * @package    core_competency
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        // Tables not related to users aside from the editing information.
        $collection->add_database_table('competency', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency');

        $collection->add_database_table('competency_coursecompsetting', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_coursecompsetting');

        $collection->add_database_table('competency_framework', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_framework');

        $collection->add_database_table('competency_coursecomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_coursecomp');

        $collection->add_database_table('competency_template', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_template');

        $collection->add_database_table('competency_templatecomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_templatecomp');

        $collection->add_database_table('competency_templatecohort', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_templatecohort');

        $collection->add_database_table('competency_relatedcomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_relatedcomp');

        $collection->add_database_table('competency_modulecomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_modulecomp');

        // Tables containing user data.
        $collection->add_database_table('competency_plan', [
            'name' => 'privacy:metadata:plan:name',
            'description' => 'privacy:metadata:plan:description',
            'userid' => 'privacy:metadata:plan:userid',
            'status' => 'privacy:metadata:plan:status',
            'duedate' => 'privacy:metadata:plan:duedate',
            'reviewerid' => 'privacy:metadata:plan:reviewerid',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_plan');

        $collection->add_database_table('competency_usercomp', [
            'userid' => 'privacy:metadata:usercomp:userid',
            'status' => 'privacy:metadata:usercomp:status',
            'reviewerid' => 'privacy:metadata:usercomp:reviewerid',
            'proficiency' => 'privacy:metadata:usercomp:proficiency',
            'grade' => 'privacy:metadata:usercomp:grade',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_usercomp');

        $collection->add_database_table('competency_usercompcourse', [
            'userid' => 'privacy:metadata:usercomp:userid',
            'proficiency' => 'privacy:metadata:usercomp:proficiency',
            'grade' => 'privacy:metadata:usercomp:grade',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_usercompcourse');

        $collection->add_database_table('competency_usercompplan', [
            'userid' => 'privacy:metadata:usercomp:userid',
            'proficiency' => 'privacy:metadata:usercomp:proficiency',
            'grade' => 'privacy:metadata:usercomp:grade',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_usercompplan');

        $collection->add_database_table('competency_plancomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_plancomp');

        $collection->add_database_table('competency_evidence', [
            'action' => 'privacy:metadata:evidence:action',
            'actionuserid' => 'privacy:metadata:evidence:actionuserid',
            'descidentifier' => 'privacy:metadata:evidence:descidentifier',
            'desccomponent' => 'privacy:metadata:evidence:desccomponent',
            'desca' => 'privacy:metadata:evidence:desca',
            'url' => 'privacy:metadata:evidence:url',
            'grade' => 'privacy:metadata:evidence:grade',
            'note' => 'privacy:metadata:evidence:note',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_evidence');

        $collection->add_database_table('competency_userevidence', [
            'name' => 'privacy:metadata:userevidence:name',
            'description' => 'privacy:metadata:userevidence:description',
            'url' => 'privacy:metadata:userevidence:url',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_userevidence');

        $collection->add_database_table('competency_userevidencecomp', [
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'usermodified' => 'privacy:metadata:usermodified',
        ], 'privacy:metadata:competency_userevidencecomp');

        // Comments can be left on learning plans and competencies.
        $collection->link_subsystem('core_comment', 'privacy:metadata:core_comments');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        global $DB;
        $contextlist = new \core_privacy\local\request\contextlist();

        // Find the contexts of the frameworks, and related data, modified by the user.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {context} ctx
               JOIN {" . competency_framework::TABLE . "} cf
                 ON cf.contextid = ctx.id
          LEFT JOIN {" . competency::TABLE . "} c
                 ON c.competencyframeworkid = cf.id
          LEFT JOIN {" . related_competency::TABLE . "} cr
                 ON cr.competencyid = c.id
              WHERE cf.usermodified = :userid1
                 OR c.usermodified = :userid2
                 OR cr.usermodified = :userid3";
        $params = ['userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid];
        $contextlist->add_from_sql($sql, $params);

        // Find the contexts of the templates, and related data, modified by the user.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {context} ctx
               JOIN {" . template::TABLE . "} tpl
                 ON tpl.contextid = ctx.id
          LEFT JOIN {" . template_cohort::TABLE . "} tch
                 ON tch.templateid = tpl.id
                AND tch.usermodified = :userid2
          LEFT JOIN {" . template_competency::TABLE . "} tc
                 ON tc.templateid = tpl.id
                AND tc.usermodified = :userid3
              WHERE tpl.usermodified = :userid1
                 OR tch.id IS NOT NULL
                 OR tc.id IS NOT NULL";
        $params = ['userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid];
        $contextlist->add_from_sql($sql, $params);

        // Find the possible course contexts.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {" . course_competency::TABLE . "} cc
               JOIN {context} ctx
                 ON ctx.instanceid = cc.courseid
                AND ctx.contextlevel = :courselevel
              WHERE cc.usermodified = :userid";
        $params = ['courselevel' => CONTEXT_COURSE, 'userid' => $userid];
        $contextlist->add_from_sql($sql, $params);

        $sql = "
             SELECT DISTINCT ctx.id
               FROM {" . course_competency_settings::TABLE . "} ccs
               JOIN {context} ctx
                 ON ctx.instanceid = ccs.courseid
                AND ctx.contextlevel = :courselevel
              WHERE ccs.usermodified = :userid";
        $params = ['courselevel' => CONTEXT_COURSE, 'userid' => $userid];
        $contextlist->add_from_sql($sql, $params);

        $sql = "
             SELECT DISTINCT ctx.id
               FROM {" . user_competency_course::TABLE . "} ucc
               JOIN {context} ctx
                 ON ctx.instanceid = ucc.courseid
                AND ctx.contextlevel = :courselevel
              WHERE ucc.usermodified = :userid";
        $params = ['courselevel' => CONTEXT_COURSE, 'userid' => $userid];
        $contextlist->add_from_sql($sql, $params);

        // Find the possible module contexts.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {" . course_module_competency::TABLE . "} cmc
               JOIN {context} ctx
                 ON ctx.instanceid = cmc.cmid
                AND ctx.contextlevel = :modulelevel
              WHERE cmc.usermodified = :userid";
        $params = ['modulelevel' => CONTEXT_MODULE, 'userid' => $userid];
        $contextlist->add_from_sql($sql, $params);

        // Add user contexts through usermodified/reviewing of plan related data.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {" . plan::TABLE . "} p
               JOIN {context} ctx
                 ON ctx.instanceid = p.userid
                AND ctx.contextlevel = :userlevel
          LEFT JOIN {" . plan_competency::TABLE . "} pc
                 ON pc.planid = p.id
                AND pc.usermodified = :userid3
          LEFT JOIN {" . user_competency_plan::TABLE . "} upc
                 ON upc.planid = p.id
                AND upc.usermodified = :userid4
              WHERE p.usermodified = :userid1
                 OR p.reviewerid = :userid2
                 OR pc.id IS NOT NULL
                 OR upc.id IS NOT NULL";
        $params = [
            'userlevel' => CONTEXT_USER,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Add user contexts through usermodified/reviewing of competency data.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {context} ctx
          LEFT JOIN {" . user_competency::TABLE . "} uc
                 ON uc.userid = ctx.instanceid
                AND ctx.contextlevel = :userlevel1
          LEFT JOIN {" . evidence::TABLE . "} e
                 ON e.usercompetencyid = uc.id
                AND (e.usermodified = :userid3 OR e.actionuserid = :userid4)
          LEFT JOIN {" . user_evidence::TABLE . "} ue
                 ON ue.userid = ctx.instanceid
                AND ctx.contextlevel = :userlevel2
                AND ue.usermodified = :userid5
          LEFT JOIN {" . user_evidence_competency::TABLE . "} uec
                 ON uec.userevidenceid = ue.id
                AND uec.usermodified = :userid6
              WHERE uc.usermodified = :userid1
                 OR uc.reviewerid = :userid2
                 OR e.id IS NOT NULL
                 OR ue.id IS NOT NULL
                 OR uec.id IS NOT NULL";
        $params = [
            'userlevel1' => CONTEXT_USER,
            'userlevel2' => CONTEXT_USER,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
            'userid5' => $userid,
            'userid6' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Now, the easy part, we fetch the user context for user plans and competencies.
        // We also fetch the course context for the state of competencies for the user in courses.
        $sql = "
             SELECT DISTINCT ctx.id
               FROM {context} ctx
          LEFT JOIN {" . plan::TABLE . "} p
                 ON p.userid = ctx.instanceid
                AND ctx.contextlevel = :userlevel1
          LEFT JOIN {" . user_competency::TABLE . "} uc
                 ON uc.userid = ctx.instanceid
                AND ctx.contextlevel = :userlevel2
                AND uc.userid = :userid2
          LEFT JOIN {" . user_evidence::TABLE . "} ue
                 ON ue.userid = ctx.instanceid
                AND ctx.contextlevel = :userlevel3
                AND ue.userid = :userid3
          LEFT JOIN {" . user_competency_course::TABLE . "} ucc
                 ON ucc.courseid = ctx.instanceid
                AND ctx.contextlevel = :courselevel
                AND ucc.userid = :userid4
              WHERE p.userid = :userid1
                 OR uc.id IS NOT NULL
                 OR ue.id IS NOT NULL
                 OR ucc.id IS NOT NULL";
        $params = [
            'userlevel1' => CONTEXT_USER,
            'userlevel2' => CONTEXT_USER,
            'userlevel3' => CONTEXT_USER,
            'courselevel' => CONTEXT_COURSE,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Include the user contexts in which the user commented.
        $sql = "
            SELECT ctx.id
              FROM {context} ctx
              JOIN {comments} c
                ON c.contextid = ctx.id
             WHERE c.component = :component
               AND c.commentarea IN (:planarea, :usercomparea)
               AND c.userid = :userid";
        $params = [
            'component' => 'competency',    // Must not be core_competency.
            'planarea' => 'plan',
            'usercomparea' => 'user_competency',
            'userid' => $userid
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $user = $contextlist->get_user();
        $userid = $user->id;

        // Re-arrange the contexts by context level.
        $groupedcontexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            $contextlevel = $context->contextlevel;
            if (!in_array($contextlevel, [CONTEXT_USER, CONTEXT_COURSE, CONTEXT_MODULE, CONTEXT_SYSTEM, CONTEXT_COURSECAT])) {
                return $carry;
            }
            $carry[$contextlevel][] = $context;
            return $carry;
        }, [
            CONTEXT_COURSE => [],
            CONTEXT_COURSECAT => [],
            CONTEXT_MODULE => [],
            CONTEXT_SYSTEM => [],
            CONTEXT_USER => [],
        ]);

        // Process module contexts.
        static::export_user_data_in_module_contexts($userid, $groupedcontexts[CONTEXT_MODULE]);

        // Process course contexts.
        static::export_user_data_in_course_contexts($userid, $groupedcontexts[CONTEXT_COURSE]);

        // Process course categories context.
        static::export_user_data_in_category_contexts($userid, $groupedcontexts[CONTEXT_COURSECAT]);

        // Process system context.
        if (!empty($groupedcontexts[CONTEXT_SYSTEM])) {
            static::export_user_data_in_system_context($userid);
        }

        // Process user contexts.
        static::export_user_data_in_user_contexts($userid, $groupedcontexts[CONTEXT_USER]);
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
                $userid = $context->instanceid;
                static::delete_user_evidence_of_prior_learning($userid);
                static::delete_user_plans($userid);
                static::delete_user_competencies($userid);
                break;

            case CONTEXT_COURSE:
                $courseid = $context->instanceid;
                $DB->delete_records(user_competency_course::TABLE, ['courseid' => $courseid]);
                break;
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * Here we only delete the private data of user, not whether they modified, are reviewing,
     * or are associated with the record on at a second level. Only data directly linked to the
     * user will be affected.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $user = $contextlist->get_user();
        $userid = $user->id;

        foreach ($contextlist as $context) {
            switch ($context->contextlevel) {
                case CONTEXT_USER:
                    if ($context->instanceid != $userid) {
                        // Only delete the user's information when they requested their context to be deleted. We
                        // do not take any action on other user's contexts because we don't own the data there.
                        continue;
                    }
                    static::delete_user_evidence_of_prior_learning($userid);
                    static::delete_user_plans($userid);
                    static::delete_user_competencies($userid);
                    break;

                case CONTEXT_COURSE:
                    static::delete_user_competencies_in_course($userid, $context->instanceid);
                    break;
            }
        }
    }

    /**
     * Delete evidence of prior learning.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_evidence_of_prior_learning($userid) {
        global $DB;

        $usercontext = context_user::instance($userid);
        $ueids = $DB->get_fieldset_select(user_evidence::TABLE, 'id', 'userid = :userid', ['userid' => $userid]);
        if (empty($ueids)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($ueids, SQL_PARAMS_NAMED);

        // Delete competencies associated with user evidence.
        $DB->delete_records_select(user_evidence_competency::TABLE, "userevidenceid $insql", $inparams);

        // Delete the user evidence.
        $DB->delete_records_select(user_evidence::TABLE, "id $insql", $inparams);

        // Delete the user evidence files.
        $fs = get_file_storage();
        $fs->delete_area_files($usercontext->id, 'core_competency', 'userevidence');
    }

    /**
     * User plans.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_plans($userid) {
        global $DB;
        $usercontext = context_user::instance($userid);

        // Remove all the comments made on plans.
        \core_comment\privacy\provider::delete_comments_for_all_users($usercontext, 'competency', 'plan');

        // Find the user plan IDs.
        $planids = $DB->get_fieldset_select(plan::TABLE, 'id', 'userid = :userid', ['userid' => $userid]);
        if (empty($planids)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($planids, SQL_PARAMS_NAMED);

        // Delete all the competencies proficiency in the plans.
        $DB->delete_records_select(user_competency_plan::TABLE, "planid $insql", $inparams);

        // Delete all the competencies in the plans.
        $DB->delete_records_select(plan_competency::TABLE, "planid $insql", $inparams);

        // Delete all the plans.
        $DB->delete_records_select(plan::TABLE, "id $insql", $inparams);
    }

    /**
     * Delete user competency data.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_competencies($userid) {
        global $DB;
        $usercontext = context_user::instance($userid);

        // Remove all the comments made on user competencies.
        \core_comment\privacy\provider::delete_comments_for_all_users($usercontext, 'competency', 'user_competency');

        // Find the user competency IDs.
        $ucids = $DB->get_fieldset_select(user_competency::TABLE, 'id', 'userid = :userid', ['userid' => $userid]);
        if (empty($ucids)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($ucids, SQL_PARAMS_NAMED);

        // Delete all the evidence associated with competencies.
        $DB->delete_records_select(evidence::TABLE, "usercompetencyid $insql", $inparams);

        // Delete all the record of competency.
        $DB->delete_records_select(user_competency::TABLE, "id $insql", $inparams);
    }

    /**
     * Delete the record of competencies for a user in a course.
     *
     * @param int $userid The user ID.
     * @param int $courseid The course ID.
     * @return void
     */
    protected static function delete_user_competencies_in_course($userid, $courseid) {
        global $DB;
        $DB->delete_records(user_competency_course::TABLE, ['userid' => $userid, 'courseid' => $courseid]);
    }

    /**
     * Export the user data in user context.
     *
     * @param int $userid The user ID.
     * @param array $contexts The contexts.
     * @return void
     */
    protected static function export_user_data_in_user_contexts($userid, array $contexts) {
        global $DB;

        $mycontext = context_user::instance($userid);
        $contextids = array_map(function($context) {
            return $context->id;
        }, $contexts);
        $exportowncontext = in_array($mycontext->id, $contextids);
        $othercontexts = array_filter($contextids, function($contextid) use ($mycontext) {
            return $contextid != $mycontext->id;
        });

        if ($exportowncontext) {
            static::export_user_data_learning_plans($mycontext);
            static::export_user_data_competencies($mycontext);
            static::export_user_data_user_evidence($mycontext);
        }

        foreach ($othercontexts as $contextid) {
            static::export_user_data_learning_plans_related_to_me($userid, context::instance_by_id($contextid));
            static::export_user_data_competencies_related_to_me($userid, context::instance_by_id($contextid));
            static::export_user_data_user_evidence_related_to_me($userid, context::instance_by_id($contextid));
        }
    }

    /**
     * Export the user data in systen context.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function export_user_data_in_system_context($userid) {
        static::export_user_data_frameworks_in_context($userid, context_system::instance());
        static::export_user_data_templates_in_context($userid, context_system::instance());
    }

    /**
     * Export the user data in category contexts.
     *
     * @param int $userid The user ID.
     * @param array $contexts The contexts.
     * @return void
     */
    protected static function export_user_data_in_category_contexts($userid, array $contexts) {
        $contexts = array_filter($contexts, function($context) {
            return $context->contextlevel == CONTEXT_COURSECAT;
        });
        if (empty($contexts)) {
            return;
        }

        foreach ($contexts as $context) {
            static::export_user_data_frameworks_in_context($userid, $context);
            static::export_user_data_templates_in_context($userid, $context);
        }
    }

    /**
     * Export the user data in course contexts.
     *
     * @param int $userid The user whose data we're exporting.
     * @param array $contexts A list of contexts.
     * @return void
     */
    protected static function export_user_data_in_course_contexts($userid, array $contexts) {
        global $DB;

        $contexts = array_filter($contexts, function($context) {
            return $context->contextlevel == CONTEXT_COURSE;
        });
        if (empty($contexts)) {
            return;
        }

        $helper = new performance_helper();
        $path = [get_string('competencies', 'core_competency')];
        $courseids = array_map(function($context) {
            return $context->instanceid;
        }, $contexts);

        // Fetch all the records of competency proficiency in the course.
        $ffields = competency_framework::get_sql_fields('f', 'f_');
        $compfields = competency::get_sql_fields('c', 'c_');
        $uccfields = user_competency_course::get_sql_fields('ucc', 'ucc_');
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT $ffields, $compfields, $uccfields, $ctxfields
              FROM {" . user_competency_course::TABLE . "} ucc
              JOIN {" . competency::TABLE . "} c
                ON c.id = ucc.competencyid
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
              JOIN {context} ctx
                ON ctx.id = f.contextid
             WHERE ucc.userid = :userid
               AND ucc.courseid $insql
          ORDER BY ucc.courseid, c.id";
        $params = array_merge($inparams, ['userid' => $userid]);

        // Export data.
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'ucc_courseid', [], function($carry, $record) use ($helper) {
            context_helper::preload_from_record($record);
            $framework = new competency_framework(null, competency_framework::extract_record($record, 'f_'));
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $ucc = new user_competency_course(null, user_competency_course::extract_record($record, 'ucc_'));
            $helper->ingest_framework($framework);

            $carry[] = array_merge(static::transform_competency_brief($competency), [
                'rating' => [
                    'rating' => static::transform_competency_grade($competency, $ucc->get('grade'), $helper),
                    'proficient' => static::transform_proficiency($ucc->get('proficiency')),
                    'timecreated' => transform::datetime($ucc->get('timecreated')),
                    'timemodified' => transform::datetime($ucc->get('timemodified')),
                ]
            ]);
            return $carry;

        }, function($courseid, $data) use ($path) {
            $context = context_course::instance($courseid);
            writer::with_context($context)->export_data($path, (object) ['ratings' => $data]);
        });

        // Export usermodified data.
        static::export_user_data_in_course_contexts_associations($userid, $courseids, $path);
        static::export_user_data_in_course_contexts_settings($userid, $courseids, $path);
        static::export_user_data_in_course_contexts_rated_by_me($userid, $courseids, $path, $helper);
    }

    /**
     * Export the ratings given in a course.
     *
     * @param int $userid The user ID.
     * @param array $courseids The course IDs.
     * @param array $path The root path.
     * @param performance_helper $helper The performance helper.
     * @return void
     */
    protected static function export_user_data_in_course_contexts_rated_by_me($userid, $courseids, $path,
            performance_helper $helper) {
        global $DB;

        // Fetch all the records of competency proficiency in the course.
        $ffields = competency_framework::get_sql_fields('f', 'f_');
        $compfields = competency::get_sql_fields('c', 'c_');
        $uccfields = user_competency_course::get_sql_fields('ucc', 'ucc_');
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT $ffields, $compfields, $uccfields, $ctxfields
              FROM {" . user_competency_course::TABLE . "} ucc
              JOIN {" . competency::TABLE . "} c
                ON c.id = ucc.competencyid
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
              JOIN {context} ctx
                ON ctx.id = f.contextid
             WHERE ucc.usermodified = :userid
               AND ucc.courseid $insql
          ORDER BY ucc.courseid, ucc.id";
        $params = array_merge($inparams, ['userid' => $userid]);

        // Export the data.
        static::recordset_loop_and_export($DB->get_recordset_sql($sql, $params), 'ucc_courseid', [],
            function($carry, $record) use ($helper) {
                context_helper::preload_from_record($record);

                $framework = new competency_framework(null, competency_framework::extract_record($record, 'f_'));
                $competency = new competency(null, competency::extract_record($record, 'c_'));
                $ucc = new user_competency_course(null, user_competency_course::extract_record($record, 'ucc_'));
                $helper->ingest_framework($framework);

                $carry[] = array_merge(static::transform_competency_brief($competency), [
                    'rating' => [
                        'userid' => transform::user($ucc->get('userid')),
                        'rating' => static::transform_competency_grade($competency, $ucc->get('grade'), $helper),
                        'proficient' => static::transform_proficiency($ucc->get('proficiency')),
                        'timemodified' => transform::datetime($ucc->get('timemodified')),
                    ]
                ]);
                return $carry;

            }, function($courseid, $data) use ($path) {
                $context = context_course::instance($courseid);
                writer::with_context($context)->export_related_data($path, 'rated_by_me', (object) [
                    'ratings' => $data
                ]);
            }
        );
    }

    /**
     * Export user data in course contexts related to linked competencies.
     *
     * @param int $userid The user ID.
     * @param array $courseids The course IDs.
     * @param array $path The root path to export at.
     * @return void
     */
    protected static function export_user_data_in_course_contexts_associations($userid, $courseids, $path) {
        global $DB;

        // Fetch all the courses with associations we created or modified.
        $compfields = competency::get_sql_fields('c', 'c_');
        $ccfields = course_competency::get_sql_fields('cc', 'cc_');
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT $compfields, $ccfields, $ctxfields
              FROM {" . course_competency::TABLE . "} cc
              JOIN {" . competency::TABLE . "} c
                ON c.id = cc.competencyid
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
              JOIN {context} ctx
                ON ctx.id = f.contextid
             WHERE cc.usermodified = :userid
               AND cc.courseid $insql
          ORDER BY cc.courseid, c.id";
        $params = array_merge($inparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);

        // Export the data.
        static::recordset_loop_and_export($recordset, 'cc_courseid', [], function($carry, $record) {
            context_helper::preload_from_record($record);
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $cc = new course_competency(null, course_competency::extract_record($record, 'cc_'));
            $carry[] = array_merge(static::transform_competency_brief($competency), [
                'timemodified' => transform::datetime($cc->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno(true)
            ]);
            return $carry;

        }, function($courseid, $data) use ($path, $userid, $DB) {
            $context = context_course::instance($courseid);
            writer::with_context($context)->export_related_data($path, 'associations', (object) ['competencies' => $data]);
        });
    }

    /**
     * Export user data in course contexts related to course settings.
     *
     * @param int $userid The user ID.
     * @param array $courseids The course IDs.
     * @param array $path The root path to export at.
     * @return void
     */
    protected static function export_user_data_in_course_contexts_settings($userid, $courseids, $path) {
        global $DB;

        // Fetch all the courses with associations we created or modified.
        $ccsfields = course_competency_settings::get_sql_fields('ccs', 'ccs_');
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT $ccsfields
              FROM {" . course_competency_settings::TABLE . "} ccs
             WHERE ccs.usermodified = :userid
               AND ccs.courseid $insql
          ORDER BY ccs.courseid";
        $params = array_merge($inparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_sql($sql, $params);

        // Export the data.
        static::recordset_loop_and_export($recordset, 'ccs_courseid', [], function($carry, $record) {
            $ccs = new course_competency_settings(null, course_competency_settings::extract_record($record, 'ccs_'));
            return [
                'timemodified' => transform::datetime($ccs->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno(true)
            ];
        }, function($courseid, $data) use ($path, $userid, $DB) {
            $context = context_course::instance($courseid);
            writer::with_context($context)->export_related_data($path, 'settings', (object) $data);
        });
    }

    /**
     * Export the user data in module contexts.
     *
     * @param int $userid The user whose data we're exporting.
     * @param array $contexts A list of contexts.
     * @return void
     */
    protected static function export_user_data_in_module_contexts($userid, array $contexts) {
        global $DB;

        $contexts = array_filter($contexts, function($context) {
            return $context->contextlevel == CONTEXT_MODULE;
        });
        if (empty($contexts)) {
            return;
        }

        $path = [get_string('competencies', 'core_competency')];
        $cmids = array_map(function($context) {
            return $context->instanceid;
        }, $contexts);

        // Fetch all the modules with associations we created or modified.
        $compfields = competency::get_sql_fields('c', 'c_');
        $cmcfields = course_module_competency::get_sql_fields('cmc', 'cmc_');
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT $compfields, $cmcfields, $ctxfields
              FROM {" . course_module_competency::TABLE . "} cmc
              JOIN {" . competency::TABLE . "} c
                ON c.id = cmc.competencyid
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
              JOIN {context} ctx
                ON ctx.id = f.contextid
             WHERE cmc.usermodified = :userid
               AND cmc.cmid $insql
          ORDER BY cmc.cmid";
        $params = array_merge($inparams, ['userid' => $userid]);

        // Export the data.
        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'cmc_cmid', [], function($carry, $record) {
            context_helper::preload_from_record($record);
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $cmc = new course_module_competency(null, course_module_competency::extract_record($record, 'cmc_'));
            $carry[] = array_merge(static::transform_competency_brief($competency), [
                'timecreated' => transform::datetime($cmc->get('timecreated')),
                'timemodified' => transform::datetime($cmc->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno(true)
            ]);
            return $carry;

        }, function($cmid, $data) use ($path) {
            $context = context_module::instance($cmid);
            writer::with_context($context)->export_data($path, (object) ['associations' => $data]);
        });
    }

    /**
     * Export a user's competencies.
     *
     * @param context_user $context The context of the user requesting the export.
     * @return void
     */
    protected static function export_user_data_competencies(context_user $context) {
        global $DB;

        $userid = $context->instanceid;
        $path = [get_string('competencies', 'core_competency'), get_string('competencies', 'core_competency')];
        $helper = new performance_helper();
        $cfields = competency::get_sql_fields('c', 'c_');
        $ucfields = user_competency::get_sql_fields('uc', 'uc_');
        $efields = evidence::get_sql_fields('e', 'e_');

        $makecomppath = function($competencyid, $data) use ($path) {
            return array_merge($path, [$data['name'] . ' (' . $competencyid . ')']);
        };

        $sql = "
            SELECT $cfields, $ucfields, $efields
              FROM {" . user_competency::TABLE . "} uc
              JOIN {" . competency::TABLE . "} c
                ON c.id = uc.competencyid
         LEFT JOIN {" . evidence::TABLE . "} e
                ON uc.id = e.usercompetencyid
             WHERE uc.userid = :userid
          ORDER BY c.id, e.timecreated DESC, e.id DESC";
        $params = ['userid' => $userid];

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'c_id', null, function($carry, $record)
                use ($context, $userid, $helper, $makecomppath) {

            $competency = new competency(null, competency::extract_record($record, 'c_'));

            if ($carry === null) {
                $uc = new user_competency(null, user_competency::extract_record($record, 'uc_'));
                $carry = array_merge(static::transform_competency_brief($competency), [
                    'rating' => static::transform_user_competency($userid, $uc, $competency, $helper),
                    'evidence' => []
                ]);
                \core_comment\privacy\provider::export_comments($context, 'competency', 'user_competency',
                    $uc->get('id'), $makecomppath($competency->get('id'), $carry), false);
            }

            // There is an evidence in this record.
            if (!empty($record->e_id)) {
                $evidence = new evidence(null, evidence::extract_record($record, 'e_'));
                $carry['evidence'][] = static::transform_evidence($userid, $evidence, $competency, $helper);
            }

            return $carry;

        }, function($competencyid, $data) use ($makecomppath, $context) {
            writer::with_context($context)->export_data($makecomppath($competencyid, $data), (object) $data);
        });
    }

    /**
     * Export a user's learning plans.
     *
     * @param context_user $context The context of the user requesting the export.
     * @return void
     */
    protected static function export_user_data_learning_plans(context_user $context) {
        global $DB;

        $userid = $context->instanceid;
        $path = [get_string('competencies', 'core_competency'), get_string('privacy:path:plans', 'core_competency')];
        $helper = new performance_helper();
        $pfields = plan::get_sql_fields('p', 'p_');
        $pcfields = plan_competency::get_sql_fields('pc', 'pc_');
        $cfields = competency::get_sql_fields('c', 'c_');
        $ucfields = user_competency::get_sql_fields('uc', 'uc_');
        $ucpfields = user_competency_plan::get_sql_fields('ucp', 'ucp_');

        // The user's learning plans.
        $sql = "
            SELECT $pfields, $pcfields, $cfields, $ucfields, $ucpfields
              FROM {" . plan::TABLE . "} p
         LEFT JOIN {" . plan_competency::TABLE . "} pc
                ON p.id = pc.planid
               AND p.templateid IS NULL
               AND p.status != :complete1
         LEFT JOIN {" . template_competency::TABLE . "} tc
                ON tc.templateid = p.templateid
               AND p.templateid IS NOT NULL
               AND p.status != :complete2
         LEFT JOIN {" . user_competency_plan::TABLE . "} ucp
                ON ucp.planid = p.id
               AND p.status = :complete3
         LEFT JOIN {" . competency::TABLE . "} c
                ON c.id = pc.competencyid
                OR c.id = tc.competencyid
                OR c.id = ucp.competencyid
         LEFT JOIN {" . user_competency::TABLE . "} uc
                ON uc.userid = p.userid
               AND (uc.competencyid = pc.competencyid OR uc.competencyid = tc.competencyid)
             WHERE p.userid = :userid
          ORDER BY p.id, c.id";
        $params = [
            'userid' => $userid,
            'complete1' => plan::STATUS_COMPLETE,
            'complete2' => plan::STATUS_COMPLETE,
            'complete3' => plan::STATUS_COMPLETE,
        ];

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'p_id', null, function($carry, $record) use ($userid, $helper, $context) {
            $iscomplete = $record->p_status == plan::STATUS_COMPLETE;

            if ($carry === null) {
                $plan = new plan(null, plan::extract_record($record, 'p_'));
                $options = ['context' => $context];
                $carry = [
                    'name' => format_string($plan->get('name'), true, $options),
                    'description' => format_text($plan->get('description'), $plan->get('descriptionformat'), $options),
                    'status' => $plan->get_statusname(),
                    'duedate' => $plan->get('duedate') ? transform::datetime($plan->get('duedate')) : '-',
                    'reviewerid' => $plan->get('reviewerid') ? transform::user($plan->get('reviewerid')) : '-',
                    'timecreated' => transform::datetime($plan->get('timecreated')),
                    'timemodified' => transform::datetime($plan->get('timemodified')),
                    'competencies' => [],
                ];
            }

            // The plan is empty.
            if (empty($record->c_id)) {
                return $carry;
            }

            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $rating = null;

            if ($iscomplete) {
                // When the plan is complete, we should always found the user_competency_plan.
                $ucp = new user_competency_plan(null, user_competency_plan::extract_record($record, 'ucp_'));
                $rating = static::transform_user_competency($userid, $ucp, $competency, $helper);

            } else if (!empty($record->uc_id)) {
                // When the plan is complete, there are still records of user_competency but we do not
                // export them here, we export them as part of the competencies structure. The reason why
                // we try to get the user_competency when the plan is not complete is to give the most accurate
                // representation of the plan as possible.
                $uc = new user_competency(null, user_competency::extract_record($record, 'uc_'));
                $rating = static::transform_user_competency($userid, $uc, $competency, $helper);
            }

            $carry['competencies'][] = array_merge(static::transform_competency_brief($competency), ['rating' => $rating]);
            return $carry;

        }, function($planid, $data) use ($context, $path) {
            $planpath = array_merge($path, [$data['name'] . ' (' . $planid . ')']);
            \core_comment\privacy\provider::export_comments($context, 'competency', 'plan', $planid, $planpath, false);
            writer::with_context($context)->export_data($planpath, (object) $data);
        });
    }

    /**
     * Export a user's data related to learning plans.
     *
     * @param int $userid The user ID we're exporting for.
     * @param context_user $context The context of the user in which we're gathering data.
     * @return void
     */
    protected static function export_user_data_learning_plans_related_to_me($userid, context_user $context) {
        global $DB;

        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('privacy:path:plans', 'core_competency'),
        ];
        $plans = [];
        $helper = new performance_helper();
        $pfields = plan::get_sql_fields('p', 'p_');
        $pcfields = plan_competency::get_sql_fields('pc', 'pc_');
        $cfields = competency::get_sql_fields('c', 'c_');
        $ucpfields = user_competency_plan::get_sql_fields('ucp', 'ucp_');

        // Function to initialise a plan record.
        $initplan = function($record) use ($context, $userid, &$plans) {
            $plan = new plan(null, plan::extract_record($record, 'p_'));
            $options = ['context' => $context];
            $plans[$plan->get('id')] = [
                'name' => format_string($plan->get('name'), true, $options),
                'reviewer_is_you' => transform::yesno($plan->get('reviewerid') == $userid),
                'timecreated' => transform::datetime($plan->get('timecreated')),
                'timemodified' => transform::datetime($plan->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno($plan->get('usermodified') == $userid),
                'competencies' => [],
            ];
        };

        $initcompetency = function($record, $planid) use (&$plans) {
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $plans[$planid]['competencies'][$competency->get('id')] = static::transform_competency_brief($competency);
        };

        // Look for associations that were created.
        $sql = "
            SELECT $pfields, $pcfields, $cfields
              FROM {" . plan_competency::TABLE . "} pc
              JOIN {" . plan::TABLE . "} p
                ON p.id = pc.planid
              JOIN {" . competency::TABLE . "} c
                ON c.id = pc.competencyid
             WHERE p.userid = :targetuserid
               AND pc.usermodified = :userid
          ORDER BY p.id, c.id";
        $params = [
            'targetuserid' => $context->instanceid,
            'userid' => $userid,
        ];

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $planid = $record->p_id;
            if (!isset($plans[$planid])) {
                $initplan($record);
            }

            $initcompetency($record, $planid);
            $pc = new plan_competency(null, plan_competency::extract_record($record, 'pc_'));
            $plans[$planid]['competencies'][$pc->get('competencyid')] = array_merge(
                $plans[$planid]['competencies'][$pc->get('competencyid')], [
                    'timemodified' => $pc->get('timemodified') ? transform::datetime($pc->get('timemodified')) : '-',
                    'timecreated' => $pc->get('timecreated') ? transform::datetime($pc->get('timecreated')) : '-',
                    'created_or_modified_by_you' => transform::yesno($pc->get('usermodified') == $userid),
                ]
            );
        }
        $recordset->close();

        // Look for final grades that were given.
        $sql = "
            SELECT $pfields, $ucpfields, $cfields
              FROM {" . user_competency_plan::TABLE . "} ucp
              JOIN {" . plan::TABLE . "} p
                ON p.id = ucp.planid
              JOIN {" . competency::TABLE . "} c
                ON c.id = ucp.competencyid
             WHERE p.userid = :targetuserid
               AND ucp.usermodified = :userid
          ORDER BY p.id, c.id";
        $params = [
            'targetuserid' => $context->instanceid,
            'userid' => $userid,
        ];

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $planid = $record->p_id;
            $competencyid = $record->c_id;

            if (!isset($plans[$planid])) {
                $initplan($record);
            }

            if (!isset($plans[$planid]['competencies'][$competencyid])) {
                $initcompetency($record, $planid);
            }

            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $ucp = new user_competency_plan(null, user_competency_plan::extract_record($record, 'ucp_'));
            $plans[$planid]['competencies'][$competencyid]['rating'] = static::transform_user_competency($userid, $ucp,
                $competency, $helper);
        }
        $recordset->close();

        // Find the plans that were modified or reviewed.
        $insql = " > 0";
        $inparams = [];
        if (!empty($plans)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($plans), SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $pfields
              FROM {" . plan::TABLE . "} p
         LEFT JOIN {comments} c
                ON c.contextid = :contextid
               AND c.commentarea = :planarea
               AND c.component = :competency
               AND c.itemid = p.id
             WHERE p.userid = :targetuserid
               AND (p.usermodified = :userid1
                OR p.reviewerid = :userid2
                OR c.userid = :userid3)
               AND p.id $insql
          ORDER BY p.id";
        $params = array_merge($inparams, [
            'targetuserid' => $context->instanceid,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'contextid' => $context->id,
            'planarea' => 'plan',
            'competency' => 'competency'
        ]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $planid = $record->p_id;
            if (!isset($plans[$planid])) {
                $initplan($record);
            }
        }
        $recordset->close();

        // Export each plan on its own.
        foreach ($plans as $planid => $plan) {
            $planpath = array_merge($path, ["{$plan['name']} ({$planid})"]);
            $plan['competencies'] = array_values($plan['competencies']);    // Drop the keys.

            writer::with_context($context)->export_data($planpath, (object) $plan);
            \core_comment\privacy\provider::export_comments($context, 'competency', 'plan', $planid, $planpath, true);
        }
    }

    /**
     * Export a user's data related to competencies.
     *
     * @param int $userid The user ID we're exporting for.
     * @param context_user $context The context of the user in which we're gathering data.
     * @return void
     */
    protected static function export_user_data_competencies_related_to_me($userid, context_user $context) {
        global $DB;

        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('competencies', 'core_competency'),
        ];
        $competencies = [];
        $helper = new performance_helper();
        $cfields = competency::get_sql_fields('c', 'c_');
        $ucfields = user_competency::get_sql_fields('uc', 'uc_');
        $efields = evidence::get_sql_fields('e', 'e_');

        $initcompetency = function($record) use (&$competencies) {
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $competencies[$competency->get('id')] = array_merge(static::transform_competency_brief($competency), [
                'evidence' => []
            ]);
        };

        $initusercomp = function($competency, $record) use (&$competencies, $userid, $helper) {
            $competencyid = $competency->get('id');
            $uc = new user_competency(null, user_competency::extract_record($record, 'uc_'));
            $competencies[$competencyid]['uc_id'] = $uc->get('id');
            $competencies[$competencyid]['rating'] = static::transform_user_competency($userid, $uc, $competency, $helper);
        };

        // Look for evidence.
        $sql = "
            SELECT $efields, $ucfields, $cfields
              FROM {" . evidence::TABLE . "} e
              JOIN {" . user_competency::TABLE . "} uc
                ON uc.id = e.usercompetencyid
              JOIN {" . competency::TABLE . "} c
                ON c.id = uc.competencyid
             WHERE uc.userid = :targetuserid
               AND (e.usermodified = :userid1
                OR e.actionuserid = :userid2)
          ORDER BY c.id, e.id";
        $params = [
            'targetuserid' => $context->instanceid,
            'userid1' => $userid,
            'userid2' => $userid,
        ];
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $competencyid = $record->c_id;
            $competency = new competency(null, competency::extract_record($record, 'c_'));

            if (!isset($competencies[$competencyid])) {
                $initcompetency($record);
            }

            if (!array_key_exists('rating', $competencies[$competencyid])) {
                $competencies[$competencyid]['rating'] = null;
                if ($record->uc_reviewerid == $userid || $record->uc_usermodified == $userid) {
                    $initusercomp($competency, $record);
                }
            }

            $evidence = new evidence(null, evidence::extract_record($record, 'e_'));
            $competencies[$competencyid]['evidence'][] = static::transform_evidence($userid, $evidence, $competency, $helper);
        }
        $recordset->close();

        // Look for user competency we modified and didn't catch.
        $insql = ' > 0';
        $inparams = [];
        if (!empty($competencies)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($competencies), SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $ucfields, $cfields
              FROM {" . user_competency::TABLE . "} uc
              JOIN {" . competency::TABLE . "} c
                ON c.id = uc.competencyid
         LEFT JOIN {comments} cmt
                ON cmt.contextid = :contextid
               AND cmt.commentarea = :ucarea
               AND cmt.component = :competency
               AND cmt.itemid = uc.id
             WHERE uc.userid = :targetuserid
               AND (uc.usermodified = :userid1
                OR uc.reviewerid = :userid2
                OR cmt.userid = :userid3)
               AND uc.competencyid $insql
          ORDER BY c.id, uc.id";
        $params = array_merge($inparams, [
            'targetuserid' => $context->instanceid,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'contextid' => $context->id,
            'ucarea' => 'user_competency',
            'competency' => 'competency',
        ]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            if (!isset($competencies[$competency->get('id')])) {
                $initcompetency($record);
                $initusercomp($competency, $record);
            }
        }
        $recordset->close();

        // Export each competency on its own.
        foreach ($competencies as $competencyid => $competency) {
            $comppath = array_merge($path, ["{$competency['name']} ({$competencyid})"]);
            $ucid = isset($competency['uc_id']) ? $competency['uc_id'] : null;
            unset($competency['uc_id']);

            // Send to writer.
            writer::with_context($context)->export_data($comppath, (object) $competency);
            if ($ucid) {
                \core_comment\privacy\provider::export_comments($context, 'competency', 'user_competency', $ucid, $comppath, true);
            }
        }
    }

    /**
     * Export a user's data related to evidence of prior learning.
     *
     * @param int $userid The user ID we're exporting for.
     * @param context_user $context The context of the user in which we're gathering data.
     * @return void
     */
    protected static function export_user_data_user_evidence_related_to_me($userid, context_user $context) {
        global $DB;

        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('privacy:path:userevidence', 'core_competency'),
        ];
        $evidence = [];
        $helper = new performance_helper();
        $cfields = competency::get_sql_fields('c', 'c_');
        $uecfields = user_evidence_competency::get_sql_fields('uec', 'uec_');
        $uefields = user_evidence::get_sql_fields('ue', 'ue_');

        $initevidence = function($record) use (&$evidence, $userid) {
            $ue = new user_evidence(null, user_evidence::extract_record($record, 'ue_'));
            $evidence[$ue->get('id')] = static::transform_user_evidence($userid, $ue);
        };

        // Look for evidence.
        $sql = "
            SELECT $uefields, $uecfields, $cfields
              FROM {" . user_evidence_competency::TABLE . "} uec
              JOIN {" . user_evidence::TABLE . "} ue
                ON ue.id = uec.userevidenceid
              JOIN {" . competency::TABLE . "} c
                ON c.id = uec.competencyid
             WHERE ue.userid = :targetuserid
               AND uec.usermodified = :userid
          ORDER BY ue.id, c.id";
        $params = [
            'targetuserid' => $context->instanceid,
            'userid' => $userid,
        ];
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $ueid = $record->ue_id;
            if (!isset($evidence[$ueid])) {
                $initevidence($record);
            }

            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $uec = new user_evidence_competency(null, user_evidence_competency::extract_record($record, 'uec_'));
            $evidence[$ueid]['competencies'][] = array_merge(static::transform_competency_brief($competency), [
                'timemodified' => $uec->get('timemodified') ? transform::datetime($uec->get('timemodified')) : '-',
                'timecreated' => $uec->get('timecreated') ? transform::datetime($uec->get('timecreated')) : '-',
                'created_or_modified_by_you' => transform::yesno($uec->get('usermodified'))
            ]);
        }
        $recordset->close();

        // Look for user evidence we modified or reviewed and didn't catch.
        $insql = ' > 0';
        $inparams = [];
        if (!empty($evidence)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($evidence), SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $uefields
              FROM {" . user_evidence::TABLE . "} ue
             WHERE ue.userid = :targetuserid
               AND ue.usermodified = :userid
               AND ue.id $insql
          ORDER BY ue.id";
        $params = array_merge($inparams, [
            'targetuserid' => $context->instanceid,
            'userid' => $userid,
        ]);

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $initevidence($record);
        }
        $recordset->close();

        // Export files, then content.
        foreach ($evidence as $ueid => $data) {
            $uepath = array_merge($path, ["{$data['name']} ({$ueid})"]);
            writer::with_context($context)->export_area_files($uepath, 'core_competency', 'userevidence', $ueid);
            writer::with_context($context)->export_data($uepath, (object) $data);
        }
    }

    /**
     * Export the evidence of prior learning of a user.
     *
     * @param context_user $context The context of the user we're exporting for.
     * @return void
     */
    protected static function export_user_data_user_evidence(context_user $context) {
        global $DB;

        $userid = $context->instanceid;
        $path = [get_string('competencies', 'core_competency'), get_string('privacy:path:userevidence', 'core_competency')];
        $uefields = user_evidence::get_sql_fields('ue', 'ue_');
        $cfields = competency::get_sql_fields('c', 'c_');

        $sql = "
            SELECT $uefields, $cfields
              FROM {" . user_evidence::TABLE . "} ue
         LEFT JOIN {" . user_evidence_competency::TABLE . "} uec
                ON uec.userevidenceid = ue.id
         LEFT JOIN {" . competency::TABLE . "} c
                ON c.id = uec.competencyid
             WHERE ue.userid = :userid
          ORDER BY ue.id";
        $params = ['userid' => $userid];

        $recordset = $DB->get_recordset_sql($sql, $params);
        static::recordset_loop_and_export($recordset, 'ue_id', null, function($carry, $record) use ($userid, $context){
            if ($carry === null) {
                $ue = new user_evidence(null, user_evidence::extract_record($record, 'ue_'));
                $carry = static::transform_user_evidence($userid, $ue);
            }

            if (!empty($record->c_id)) {
                $competency = new competency(null, competency::extract_record($record, 'c_'));
                $carry['competencies'][] = static::transform_competency_brief($competency);
            }

            return $carry;
        }, function($ueid, $data) use ($context, $path) {
            $finalpath = array_merge($path, [$data['name'] . ' (' . $ueid . ')']);
            writer::with_context($context)->export_area_files($finalpath, 'core_competency', 'userevidence', $ueid);
            writer::with_context($context)->export_data($finalpath, (object) $data);
        });
    }

    /**
     * Export the user data related to frameworks in context.
     *
     * @param int $userid The user ID.
     * @param context $context The context.
     * @return void
     */
    protected static function export_user_data_frameworks_in_context($userid, context $context) {
        global $DB;

        $ffields = competency_framework::get_sql_fields('f', 'f_');
        $cfields = competency::get_sql_fields('c', 'c_');
        $c2fields = competency::get_sql_fields('c2', 'c2_');
        $rcfields = related_competency::get_sql_fields('rc', 'rc_');

        $frameworks = [];
        $initframework = function($record) use (&$frameworks, $userid) {
            $framework = new competency_framework(null, competency_framework::extract_record($record, 'f_'));
            $frameworks[$framework->get('id')] = array_merge(static::transform_framework_brief($framework), [
                'timemodified' => transform::datetime($framework->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno($framework->get('usermodified') == $userid),
                'competencies' => []
            ]);
        };
        $initcompetency = function($record, $prefix) use (&$frameworks, $userid) {
            $competency = new competency(null, competency::extract_record($record, $prefix));
            $frameworks[$competency->get('competencyframeworkid')]['competencies'][$competency->get('id')] = array_merge(
                static::transform_competency_brief($competency),
                [
                    'timemodified' => transform::datetime($competency->get('timemodified')),
                    'created_or_modified_by_you' => transform::yesno($competency->get('usermodified') == $userid),
                    'related_competencies' => []
                ]
            );
        };

        // Start by looking for related competencies.
        $sql = "
            SELECT $ffields, $cfields, $c2fields, $rcfields
              FROM {" . related_competency::TABLE . "} rc
              JOIN {" . competency::TABLE . "} c
                ON c.id = rc.competencyid
              JOIN {" . competency::TABLE . "} c2
                ON c2.id = rc.relatedcompetencyid
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
             WHERE rc.usermodified = :userid
               AND f.contextid = :contextid
          ORDER BY rc.id, c.id";
        $params = ['userid' => $userid, 'contextid' => $context->id];

        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $frameworkid = $record->f_id;
            $comp1id = $record->c_id;
            $comp2id = $record->c2_id;

            if (!isset($frameworks[$frameworkid])) {
                $initframework($record);
            }

            foreach (['c_', 'c2_'] as $key) {
                $competencyid = $record->{$key . 'id'};
                if (!isset($frameworks[$frameworkid]['competencies'][$competencyid])) {
                    $initcompetency($record, $key);
                }
            }

            $relcomp = new related_competency(null, related_competency::extract_record($record, 'rc_'));
            foreach (['c_' => 'c2_', 'c2_' => 'c_'] as $key => $relatedkey) {
                $competencyid = $record->{$key . 'id'};
                $competency = new competency(null, competency::extract_record($record, $relatedkey));
                $frameworks[$frameworkid]['competencies'][$competencyid]['related_competencies'][] = [
                    'name' => $competency->get('shortname'),
                    'idnumber' => $competency->get('idnumber'),
                    'timemodified' => transform::datetime($relcomp->get('timemodified')),
                    'created_or_modified_by_you' => transform::yesno($relcomp->get('usermodified') == $userid),
                ];
            }
        }
        $recordset->close();

        // Now look for competencies, but skip the ones we've already seen.
        $competencyids = array_reduce($frameworks, function($carry, $framework) {
            return array_merge($carry, array_keys($framework['competencies']));
        }, []);
        $insql = ' IS NOT NULL';
        $inparams = [];
        if (!empty($competencyids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $ffields, $cfields
              FROM {" . competency::TABLE . "} c
              JOIN {" . competency_framework::TABLE . "} f
                ON f.id = c.competencyframeworkid
             WHERE c.usermodified = :userid
               AND f.contextid = :contextid
               AND c.id $insql
          ORDER BY c.id";
        $params = array_merge($inparams, ['userid' => $userid, 'contextid' => $context->id]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $frameworkid = $record->f_id;
            if (!isset($frameworks[$frameworkid])) {
                $initframework($record);
            }
            $initcompetency($record, 'c_');
        }
        $recordset->close();

        // Now look for frameworks, but skip the ones we've already seen.
        $frameworkids = array_keys($frameworks);
        $insql = ' IS NOT NULL';
        $inparams = [];
        if (!empty($frameworkids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($frameworkids, SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $ffields
              FROM {" . competency_framework::TABLE . "} f
             WHERE f.usermodified = :userid
               AND f.contextid = :contextid
               AND f.id $insql
          ORDER BY f.id";
        $params = array_merge($inparams, ['userid' => $userid, 'contextid' => $context->id]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            context_helper::preload_from_record($record);
            $initframework($record);
        }
        $recordset->close();

        // Export all the things!
        writer::with_context($context)->export_related_data(
            [get_string('competencies', 'core_competency')],
            'frameworks',
            (object) [
                // Drop the temporary IDs.
                'frameworks' => array_reduce($frameworks, function($carry, $item) {
                    $item['competencies'] = array_values($item['competencies']);
                    $carry[] = $item;
                    return $carry;
                }, [])
            ]
        );
    }

    /**
     * Export the user data related to templates in contexts.
     *
     * @param int $userid The user ID.
     * @param context $context The context.
     * @return void
     */
    protected static function export_user_data_templates_in_context($userid, context $context) {
        global $DB;

        $tfields = template::get_sql_fields('t', 't_');
        $cfields = competency::get_sql_fields('c', 'c_');
        $tcfields = template_competency::get_sql_fields('tc', 'tc_');
        $tchfields = template_cohort::get_sql_fields('tch', 'tch_');

        $templates = [];
        $inittemplate = function($record) use (&$templates, $userid) {
            $template = new template(null, template::extract_record($record, 't_'));
            $templates[$template->get('id')] = array_merge(static::transform_template_brief($template), [
                'timemodified' => transform::datetime($template->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno($template->get('usermodified') == $userid),
                'competencies' => [],
                'cohorts' => []
            ]);
        };

        // Find the template competencies.
        $sql = "
            SELECT $tfields, $cfields, $tcfields
              FROM {" . template_competency::TABLE . "} tc
              JOIN {" . template::TABLE . "} t
                ON t.id = tc.templateid
              JOIN {" . competency::TABLE . "} c
                ON c.id = tc.competencyid
             WHERE t.contextid = :contextid
               AND tc.usermodified = :userid
          ORDER BY t.id, tc.id";
        $params = ['userid' => $userid, 'contextid' => $context->id];
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $templateid = $record->t_id;
            if (!isset($templates[$templateid])) {
                $inittemplate($record);
            }
            $tplcomp = new template_competency(null, template_competency::extract_record($record, 'tc_'));
            $competency = new competency(null, competency::extract_record($record, 'c_'));
            $templates[$templateid]['competencies'][] = array_merge(
                static::transform_competency_brief($competency),
                [
                    'timemodified' => transform::datetime($tplcomp->get('timemodified')),
                    'created_or_modified_by_you' => transform::yesno($tplcomp->get('usermodified') == $userid)
                ]
            );
        }
        $recordset->close();

        // Find the template cohorts.
        $sql = "
            SELECT $tfields, $tchfields, c.name AS cohortname
              FROM {" . template_cohort::TABLE . "} tch
              JOIN {" . template::TABLE . "} t
                ON t.id = tch.templateid
              JOIN {cohort} c
                ON c.id = tch.cohortid
             WHERE t.contextid = :contextid
               AND tch.usermodified = :userid
          ORDER BY t.id, tch.id";
        $params = ['userid' => $userid, 'contextid' => $context->id];
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $templateid = $record->t_id;
            if (!isset($templates[$templateid])) {
                $inittemplate($record);
            }
            $tplcohort = new template_cohort(null, template_cohort::extract_record($record, 'tch_'));
            $templates[$templateid]['cohorts'][] = [
                'name' => $record->cohortname,
                'timemodified' => transform::datetime($tplcohort->get('timemodified')),
                'created_or_modified_by_you' => transform::yesno($tplcohort->get('usermodified') == $userid)
            ];
        }
        $recordset->close();

        // Find the modified templates which we haven't been found yet.
        $templateids = array_keys($templates);
        $insql = "IS NOT NULL";
        $inparams = [];
        if (!empty($templateids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($templateids, SQL_PARAMS_NAMED, 'param', false);
        }
        $sql = "
            SELECT $tfields
              FROM {" . template::TABLE . "} t
             WHERE t.contextid = :contextid
               AND t.usermodified = :userid
               AND t.id $insql
          ORDER BY t.id";
        $params = array_merge($inparams, ['userid' => $userid, 'contextid' => $context->id]);
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $inittemplate($record);
        }
        $recordset->close();

        // Export all the things!
        writer::with_context($context)->export_related_data([get_string('competencies', 'core_competency')],
            'templates', (object) ['templates' => array_values($templates)]);
    }

    /**
     * Transform a competency into a brief description.
     *
     * @param competency $competency The competency.
     * @return array
     */
    protected static function transform_competency_brief(competency $competency) {
        global $OUTPUT;
        $exporter = new \core_competency\external\competency_exporter($competency, ['context' => $competency->get_context()]);
        $data = $exporter->export($OUTPUT);
        return [
            'idnumber' => $data->idnumber,
            'name' => $data->shortname,
            'description' => $data->description
        ];
    }

    /**
     * Transform a competency rating.
     *
     * @param competency $competency The competency.
     * @param int $grade The grade.
     * @param performance_helper $helper The performance helper.
     * @return string
     */
    protected static function transform_competency_grade(competency $competency, $grade, performance_helper $helper) {
        if ($grade === null) {
            return '-';
        }
        $scale = $helper->get_scale_from_competency($competency);
        return $scale->scale_items[$grade - 1];
    }

    /**
     * Transform an evidence.
     *
     * @param int $userid The user ID we are exporting for.
     * @param evidence $evidence The evidence.
     * @param competency $competency The associated competency.
     * @param performance_helper $helper The performance helper.
     * @return array
     */
    protected static function transform_evidence($userid, evidence $evidence, competency $competency, performance_helper $helper) {
        $action = $evidence->get('action');
        $actiontxt = '?';
        if ($action == evidence::ACTION_LOG) {
            $actiontxt = get_string('privacy:evidence:action:log', 'core_competency');
        } else if ($action == evidence::ACTION_COMPLETE) {
            $actiontxt = get_string('privacy:evidence:action:complete', 'core_competency');
        } else if ($action == evidence::ACTION_OVERRIDE) {
            $actiontxt = get_string('privacy:evidence:action:override', 'core_competency');
        }

        $actionuserid = $evidence->get('actionuserid');

        return [
            'action' => $actiontxt,
            'actionuserid' => $actionuserid ? transform::user($actionuserid) : '-',
            'acting_user_is_you' => transform::yesno($userid == $actionuserid),
            'description' => (string) $evidence->get_description(),
            'url' => $evidence->get('url'),
            'grade' => static::transform_competency_grade($competency, $evidence->get('grade'), $helper),
            'note' => $evidence->get('note'),
            'timecreated' => transform::datetime($evidence->get('timecreated')),
            'timemodified' => transform::datetime($evidence->get('timemodified')),
            'created_or_modified_by_you' => transform::yesno($userid == $evidence->get('usermodified'))
        ];
    }

    /**
     * Transform a framework into a brief description.
     *
     * @param competency_framework $framework The framework.
     * @return array
     */
    protected static function transform_framework_brief(competency_framework $framework) {
        global $OUTPUT;
        $exporter = new \core_competency\external\competency_framework_exporter($framework);
        $data = $exporter->export($OUTPUT);
        return [
            'name' => $data->shortname,
            'idnumber' => $data->idnumber,
            'description' => $data->description
        ];
    }

    /**
     * Transform a template into a brief description.
     *
     * @param template $template The Template.
     * @return array
     */
    protected static function transform_template_brief(template $template) {
        global $OUTPUT;
        $exporter = new \core_competency\external\template_exporter($template);
        $data = $exporter->export($OUTPUT);
        return [
            'name' => $data->shortname,
            'description' => $data->description
        ];
    }

    /**
     * Transform proficiency.
     *
     * @param null|bool $proficiency The proficiency.
     * @return string
     */
    protected static function transform_proficiency($proficiency) {
        return $proficiency !== null ? transform::yesno($proficiency) : '-';
    }

    /**
     * Transform user competency.
     *
     * @param int $userid The user ID we are exporting for.
     * @param user_competency|user_competency_plan|user_competency_course $uc The user competency.
     * @param competency $competency The associated competency.
     * @param performance_helper $helper The performance helper.
     * @return array
     */
    protected static function transform_user_competency($userid, $uc, competency $competency, performance_helper $helper) {
        $data = [
            'proficient' => static::transform_proficiency($uc->get('proficiency')),
            'rating' => static::transform_competency_grade($competency, $uc->get('grade'), $helper),
            'timemodified' => $uc->get('timemodified') ? transform::datetime($uc->get('timemodified')) : '-',
            'timecreated' => $uc->get('timecreated') ? transform::datetime($uc->get('timecreated')) : '-',
            'created_or_modified_by_you' => transform::yesno($uc->get('usermodified') == $userid),
        ];

        if ($uc instanceof user_competency) {
            $reviewer = $uc->get('reviewerid');
            $data['status'] = (string) user_competency::get_status_name($uc->get('status'));
            $data['reviewerid'] = $reviewer ? transform::user($reviewer) : '-';
            $data['reviewer_is_you'] = transform::yesno($reviewer == $userid);
        }

        return $data;
    }

    /**
     * Transform a user evidence.
     *
     * @param int $userid The user we are exporting for.
     * @param user_evidence $ue The evidence of prior learning.
     * @return array
     */
    protected static function transform_user_evidence($userid, user_evidence $ue) {
        $options = ['context' => $ue->get_context()];
        return [
            'name' => format_string($ue->get('name'), true, $options),
            'description' => format_text($ue->get('description'), $ue->get('descriptionformat'), $options),
            'url' => $ue->get('url'),
            'timecreated' => $ue->get('timecreated') ? transform::datetime($ue->get('timecreated')) : '-',
            'timemodified' => $ue->get('timemodified') ? transform::datetime($ue->get('timemodified')) : '-',
            'created_or_modified_by_you' => transform::yesno($ue->get('usermodified') == $userid),
            'competencies' => []
        ];
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
    protected static function recordset_loop_and_export(moodle_recordset $recordset, $splitkey, $initial,
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
