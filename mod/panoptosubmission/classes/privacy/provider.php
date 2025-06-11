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

namespace mod_panoptosubmission\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;

/**
 * This class defines the privacy information for the panopto submission module
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     *
     * This function defines and returns the metadata that is stored by this module
     *
     * @param collection $collection the object used to store and return the privacy definitions
     * @return returns the collection that includes the new privacy definitions
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_external_location_link(
            'panoptosubmission_submission',
            [
                'userid' => 'privacy:metadata:panoptosubmission_submission:userid',
                'username' => 'privacy:metadata:panoptosubmission_submission:username',
                'email' => 'privacy:metadata:panoptosubmission_submission:email',
            ],
            'privacy:metadata:panoptosubmission_submission'
        );

        $collection->add_subsystem_link('core_message', [], 'privacy:metadata:emailteachersexplanation');

        $collection->add_database_table(
            'panoptosubmission_submission',
            [
                'userid' => 'privacy:metadata:panoptosubmission_submission:userid',
                'source' => 'privacy:metadata:panoptosubmission_submission:source',
                'grade' => 'privacy:metadata:panoptosubmission_submission:grade',
                'submissioncomment' => 'privacy:metadata:panoptosubmission_submission:submissioncomment',
                'teacher' => 'privacy:metadata:panoptosubmission_submission:teacher',
                'mailed' => 'privacy:metadata:panoptosubmission_submission:mailed',
                'timemarked' => 'privacy:metadata:panoptosubmission_submission:timemarked',
                'timecreated' => 'privacy:metadata:panoptosubmission_submission:timecreated',
                'timemodified' => 'privacy:metadata:panoptosubmission_submission:timemodified',
            ],
            'privacy:metadata:panoptosubmission_submission'
        );

        $collection->add_user_preference('panoptosubmission_filter',
            'privacy:metadata:panoptosubmissionfilter'
        );
        $collection->add_user_preference('panoptosubmission_group_filter',
            'privacy:metadata:panoptosubmissiongroupfilter'
        );
        $collection->add_user_preference('panoptosubmission_perpage',
            'privacy:metadata:panoptosubmissionperpage'
        );
        $collection->add_user_preference('panoptosubmission_quickgrade',
            'privacy:metadata:panoptosubmissionquickgrade'
        );

        return $collection;
    }

    /**
     * Stores the user preferences related to mod_panoptosubmission.
     *
     * @param  int $userid The user ID that we want the preferences for.
     */
    public static function export_user_preferences(int $userid) {
        $context = \context_system::instance();
        $assignmentpreferences = [
            'panoptosubmission_filter' => get_string('privacy:metadata:panoptosubmissionfilter', 'mod_panoptosubmission'),
            'panoptosubmission_group_filter' => get_string(
                'privacy:metadata:panoptosubmissiongroupfilter', 'mod_panoptosubmission'),
            'panoptosubmission_perpage' => get_string('privacy:metadata:panoptosubmissionperpage', 'mod_panoptosubmission'),
            'panoptosubmission_quickgrade' => get_string('privacy:metadata:panoptosubmissionquickgrade', 'mod_panoptosubmission'),
        ];

        foreach ($assignmentpreferences as $key => $preferencestring) {
            $value = get_user_preferences($key, null, $userid);

            if (isset($value)) {
                writer::with_context($context)
                    ->export_user_preference('mod_panoptosubmission', $key, $value, $preferencestring);
            }
        }
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT DISTINCT ctx.id FROM {context} ctx " .
                  "JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel " .
                  "JOIN {modules} m ON cm.module = m.id AND m.name = :modulename " .
                  "JOIN {panoptosubmission} a ON cm.instance = a.id " .
                  "JOIN {panoptosubmission_submission} s ON s.panactivityid = a.id " .
                 "WHERE s.userid = :userid " .
                    "OR s.teacher = :teacher";

        $params = [
            'modulename' => 'panoptosubmission',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
            'teacher' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        $params = [
            'modulename' => 'panoptosubmission',
            'contextlevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $sql = "SELECT s.userid FROM {panoptosubmission_submission} s " .
                  "JOIN {panoptosubmission} a ON s.panactivityid = a.id " .
                  "JOIN {modules} m ON m.name = :modulename " .
                  "JOIN {course_modules} cm ON a.id = cm.instance AND cm.module = m.id " .
                  "JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel " .
                 "WHERE ctx.id = :contextid";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT s.teacher FROM {panoptosubmission_submission} s " .
                  "JOIN {panoptosubmission} a ON s.panactivityid = a.id " .
                  "JOIN {modules} m ON m.name = :modulename " .
                  "JOIN {course_modules} cm ON a.id = cm.instance AND cm.module = m.id " .
                  "JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel " .
                 "WHERE ctx.id = :contextid";
        $userlist->add_from_sql('teacher', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist.
     * User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }

            // Cannot make use of helper::export_context_files(), need to manually export panoptosubmission details.
            $panoptosubmissiondata = self::get_panoptosubmission_by_context($context);

            // Get panoptosubmission details object for output.
            $panoptosubmission = self::get_panoptosubmission_output($panoptosubmissiondata);
            writer::with_context($context)->export_data([], $panoptosubmission);

            // Check if the user has marked any panoptosubmission's submissions to determine submissions to export.
            $teacher = (self::has_marked_panoptosubmission_submissions(
                $panoptosubmissiondata->id, $user->id) == true) ? true : false;

            // Get the panoptosubmission submissions submitted by & marked by the user for an panoptosubmission.
            $submissionsdata = self::get_panoptosubmission_submissions_by_panoptosubmission(
                $panoptosubmissiondata->id,
                $user->id,
                $teacher
            );

            $gradingmanager = get_grading_manager($context, 'mod_assign', 'submissions');
            $controller = $gradingmanager->get_active_controller();
            foreach ($submissionsdata as $submissiondata) {
                // Default subcontext path to export assignment submissions submitted by the user.
                $subcontexts = [get_string('privacy:submissionpath', 'mod_panoptosubmission')];

                if ($teacher == true) {
                    if ($submissiondata->teacher == $user->id) {
                        // Export panoptosubmission submissions that have been marked by the user.
                        $subcontexts = [
                            get_string('privacy:markedsubmissionspath', 'mod_panoptosubmission'),
                            transform::user($submissiondata->userid),
                        ];
                    }
                }

                // Get panoptosubmission submission details object for output.
                $submission = self::get_panoptosubmission_submission_output($submissiondata);

                writer::with_context($context)->export_data($subcontexts, $submission);

                // Check for advanced grading and retrieve that information.
                if (isset($controller)) {
                    \core_grading\privacy\provider::export_item_data($context, $submissiondata->id, get_string(
                        'privacy:submissionpath', 'mod_panoptosubmission'));
                }
            }
        }
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        // Delete all panoptosubmission submissions for the panoptosubmission associated with the context module.
        $panoptosubmission = self::get_panoptosubmission_by_context($context);

        if ($panoptosubmission != null) {
            $DB->delete_records('panoptosubmission_submission', ['panactivityid' => $panoptosubmission->id]);
        }

        // Delete advanced grading information.
        $gradingmanager = get_grading_manager($context, 'mod_panoptosubmission', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (isset($controller)) {
            \core_grading\privacy\provider::delete_instance_data($context);
        }

        // Delete all panoptosubmission files.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, STUDENTSUBMISSION_FILE_COMPONENT, STUDENTSUBMISSION_FILE_FILEAREA);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        // Only retrieve panoptosubmission submissions submitted by the user for deletion.
        $panoptosubmissionsubmissionids = array_keys(
            self::get_panoptosubmission_submissions_by_contextlist($contextlist, $userid)
        );

        $gradingmanager = get_grading_manager($context, 'mod_panoptosubmission', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (isset($controller)) {
            // Careful here, if no submissionids are provided then all data is deleted for the context.
            foreach ($panoptosubmissionsubmissionids as $submissionid) {
                \core_grading\privacy\provider::delete_instance_data($context, $submissionid);
            }
        }

        $DB->delete_records_list('panoptosubmission_submission', 'id', $panoptosubmissionsubmissionids);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        // If the context isn't for a module then return early.
        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        // Fetch the panoptosubmission.
        $panoptosubmission = self::get_panoptosubmission_by_context($context);
        $userids = $userlist->get_userids();

        list($inorequalsql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['videoassignid'] = $panoptosubmission->id;

        // Get panoptosubmission submissions ids.
        $sql = "SELECT s.id " .
            "FROM {panoptosubmission_submission} s " .
            "JOIN {panoptosubmission} a ON s.panactivityid = a.id " .
            "WHERE a.id = :videoassignid " .
            "AND s.userid $inorequalsql";

        $submissionids = $DB->get_records_sql($sql, $params);

        $gradingmanager = get_grading_manager($context, 'mod_panoptosubmission', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        // Careful here, if no submissionids are provided then all data is deleted for the context.
        if (isset($controller) && !empty($submissionids)) {
            \core_grading\privacy\provider::delete_instance_data($context, $submissionids);
        }

        // Delete related tables.
        $DB->delete_records_list('assignment_submissions', 'id', array_keys($submissionids));
    }

    /**
     * Helper function to return panoptosubmission submissions submitted by / marked by a user and their contextlist.
     *
     * @param object $contextlist   Object with the contexts related to a userid to retrieve panoptosubmission submissions by.
     * @param int $userid           The user ID to find panoptosubmission submissions that were submitted by.
     * @return array                Array of panoptosubmission submission details.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_panoptosubmission_submissions_by_contextlist($contextlist, $userid) {
        global $DB;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modulename' => 'panoptosubmission',
            'userid' => $userid,
        ];

        $sql = "SELECT s.id as id, " .
                       "s.panactivityid as panactivityid, " .
                       "s.source as source, " .
                       "s.grade as grade, " .
                       "s.submissioncomment as submissioncomment, " .
                       "s.teacher as teacher, " .
                       "s.timemarked as timemarked, " .
                       "s.timecreated as timecreated, " .
                       "s.timemodified as timemodified " .
                  "FROM {context} ctx " .
                  "JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel " .
                  "JOIN {modules} m ON cm.module = m.id AND m.name = :modulename " .
                  "JOIN {panoptosubmission} a ON cm.instance = a.id " .
                  "JOIN {panoptosubmission_submission} s ON s.panactivityid = a.id " .
                 "WHERE (s.userid = :userid)";

        $sql .= " AND ctx.id {$contextsql}";
        $params += $contextparams;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to return panoptosubmission for a context module.
     *
     * @param object $context   The context module object to return the panoptosubmission record by.
     * @return mixed            The panoptosubmission details or null record associated with the context module.
     * @throws \dml_exception
     */
    protected static function get_panoptosubmission_by_context($context) {
        global $DB;

        $params = [
            'modulename' => 'panoptosubmission',
            'contextmodule' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $sql = "SELECT a.id, " .
                       "a.name, " .
                       "a.intro, " .
                       "a.grade, " .
                       "a.timedue, " .
                       "a.timeavailable, " .
                       "a.cutofftime, " .
                       "a.timemodified " .
                  "FROM {panoptosubmission} a " .
                  "JOIN {course_modules} cm ON a.id = cm.instance " .
                  "JOIN {modules} m ON m.id = cm.module AND m.name = :modulename " .
                  "JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextmodule " .
                 "WHERE ctx.id = :contextid";

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Helper function generate panoptosubmission output object for exporting.
     *
     * @param object $panoptosubmissiondata  Object containing panoptosubmission data.
     * @return object                   Formatted panoptosubmission output object for exporting.
     */
    protected static function get_panoptosubmission_output($panoptosubmissiondata) {
        $panoptosubmission = (object) [
            'name' => $panoptosubmissiondata->name,
            'intro' => $panoptosubmissiondata->intro,
            'grade' => $panoptosubmissiondata->grade,
            'timemodified' => transform::datetime($panoptosubmissiondata->timemodified),
        ];

        if ($panoptosubmissiondata->timeavailable != 0) {
            $panoptosubmission->timeavailable = transform::datetime($panoptosubmissiondata->timeavailable);
        }

        if ($panoptosubmissiondata->timedue != 0) {
            $panoptosubmission->timedue = transform::datetime($panoptosubmissiondata->timedue);
        }

        if ($panoptosubmissiondata->cutofftime != 0) {
            $panoptosubmission->cutofftime = transform::datetime($panoptosubmissiondata->cutofftime);
        }

        return $panoptosubmission;
    }

    /**
     * Helper function to check if a user has marked panoptosubmission submissions for a given panoptosubmission.
     *
     * @param int $panoptosubmissionid The panoptosubmission ID to check if user has marked associated submissions.
     * @param int $userid         The user ID to check if user has marked associated submissions.
     * @return bool               If user has marked associated submissions returns true, otherwise false.
     * @throws \dml_exception
     */
    protected static function has_marked_panoptosubmission_submissions($panoptosubmissionid, $userid) {
        global $DB;

        $params = [
            'panactivityid' => $panoptosubmissionid,
            'teacher' => $userid,
        ];

        $sql = "SELECT count(s.id) as nomarked " .
                  "FROM {panoptosubmission_submission} s " .
                 "WHERE s.panactivityid = :panactivityid " .
                   "AND s.teacher = :teacher";

        $results = $DB->get_record_sql($sql, $params);

        return ($results->nomarked > 0) ? true : false;
    }

    /**
     * Helper function to retrieve panoptosubmission submissions submitted by / marked by a user for a specific panoptosubmission.
     *
     * @param int $panoptosubmissionid   The panoptosubmission ID to retrieve panoptosubmission submissions by.
     * @param int $userid           The user ID to retrieve panoptosubmission submissions submitted / marked by.
     * @param bool $teacher         The teacher status to determine if marked panoptosubmission submissions should be returned.
     * @return array                Array of panoptosubmission submissions details.
     * @throws \dml_exception
     */
    protected static function get_panoptosubmission_submissions_by_panoptosubmission(
        $panoptosubmissionid, $userid, $teacher = false) {
        global $DB;

        $params = [
            'panactivityid' => $panoptosubmissionid,
            'userid' => $userid,
        ];

        $sql = "SELECT s.id as id, " .
                       "s.panactivityid as panactivityid, " .
                       "s.source as source, " .
                       "s.grade as grade, " .
                       "s.submissioncomment as submissioncomment, " .
                       "s.teacher as teacher, " .
                       "s.timemarked as timemarked, " .
                       "s.timecreated as timecreated, " .
                       "s.timemodified as timemodified, " .
                       "s.userid as userid " .
                  "FROM {panoptosubmission_submission} s " .
                 "WHERE s.panactivityid = :panactivityid " .
                   "AND (s.userid = :userid";

        if ($teacher == true) {
            $sql .= " OR s.teacher = :teacher";
            $params['teacher'] = $userid;
        }

        $sql .= ")";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function generate panoptosubmission submission output object for exporting.
     *
     * @param object $submissiondata    Object containing panoptosubmission submission data.
     * @return object                   Formatted panoptosubmission submission output for exporting.
     */
    protected static function get_panoptosubmission_submission_output($submissiondata) {
        $submission = (object) [
            'panactivityid' => $submissiondata->panactivityid,
            'source' => $submissiondata->source,
            'grade' => $submissiondata->grade,
            'submissioncomment' => $submissiondata->submissioncomment,
            'teacher' => transform::user($submissiondata->teacher),
        ];

        if ($submissiondata->timecreated != 0) {
            $submission->timecreated = transform::datetime($submissiondata->timecreated);
        }

        if ($submissiondata->timemarked != 0) {
            $submission->timemarked = transform::datetime($submissiondata->timemarked);
        }

        if ($submissiondata->timemodified != 0) {
            $submission->timemodified = transform::datetime($submissiondata->timemodified);
        }

        return $submission;
    }
}
