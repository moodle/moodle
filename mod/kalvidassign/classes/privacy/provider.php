<?php

namespace mod_kalvidassign\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection) : collection {

        $collection->add_subsystem_link('core_message', [], 'privacy:metadata:emailteachersexplanation');

        $collection->add_database_table(
            'kalvidassign_submission',
            [
                'userid' => 'privacy:metadata:kalvidassign_submission:userid',
                'entry_id' => 'privacy:metadata:kalvidassign_submission:entryid',
                'source' => 'privacy:metadata:kalvidassign_submission:source',
                'grade' => 'privacy:metadata:kalvidassign_submission:grade',
                'submissioncomment' => 'privacy:metadata:kalvidassign_submission:submissioncomment',
                'teacher' => 'privacy:metadata:kalvidassign_submission:teacher',
                'mailed' => 'privacy:metadata:kalvidassign_submission:mailed',
                'timemarked' => 'privacy:metadata:kalvidassign_submission:timemarked',
                'metadata' => 'privacy:metadata:kalvidassign_submission:metadata',
                'timecreated' => 'privacy:metadata:kalvidassign_submission:timecreated',
                'timemodified' => 'privacy:metadata:kalvidassign_submission:timemodified'
            ],
            'privacy:metadata:kalvidassign_submission'
        );

        $collection->add_user_preference('kalvidassign_filter', 'privacy:metadata:kalvidassignfilter');
        $collection->add_user_preference('kalvidassign_group_filter', 'privacy:metadata:kalvidassigngroupfilter');
        $collection->add_user_preference('kalvidassign_perpage', 'privacy:metadata:kalvidassignperpage');
        $collection->add_user_preference('kalvidassign_quickgrade', 'privacy:metadata:kalvidassignquickgrade');

        return $collection;
    }

    /**
     * Stores the user preferences related to mod_kalvidassign.
     *
     * @param  int $userid The user ID that we want the preferences for.
     */
    public static function export_user_preferences(int $userid) {
        $context = \context_system::instance();
        $assignmentpreferences = [
            'kalvidassign_filter' => get_string('privacy:metadata:kalvidassignfilter', 'mod_kalvidassign'),
            'kalvidassign_group_filter' => get_string('privacy:metadata:kalvidassigngroupfilter', 'mod_kalvidassign'),
            'kalvidassign_perpage' => get_string('privacy:metadata:kalvidassignperpage', 'mod_kalvidassign'),
            'kalvidassign_quickgrade' => get_string('privacy:metadata:kalvidassignquickgrade', 'mod_kalvidassign')
        ];

        foreach ($assignmentpreferences as $key => $preferencestring) {
            $value = get_user_preferences($key, null, $userid);

            if (isset($value)) {
                writer::with_context($context)
                    ->export_user_preference('mod_kalvidassign', $key, $value, $preferencestring);
            }
        }
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT DISTINCT
                       ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {kalvidassign} a ON cm.instance = a.id
                  JOIN {kalvidassign_submission} s ON s.vidassignid = a.id
                 WHERE s.userid = :userid
                    OR s.teacher = :teacher";

        $params = [
            'modulename' => 'kalvidassign',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
            'teacher' => $userid
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
            'modulename' => 'kalvidassign',
            'contextlevel' => CONTEXT_MODULE,
            'contextid' => $context->id
        ];

        $sql = "SELECT s.userid
                  FROM {kalvidassign_submission} s
                  JOIN {kalvidassign} a ON s.vidassignid = a.id
                  JOIN {modules} m ON m.name = :modulename
                  JOIN {course_modules} cm ON a.id = cm.instance AND cm.module = m.id
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
                 WHERE ctx.id = :contextid";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT s.teacher
                  FROM {kalvidassign_submission} s
                  JOIN {kalvidassign} a ON s.vidassignid = a.id
                  JOIN {modules} m ON m.name = :modulename
                  JOIN {course_modules} cm ON a.id = cm.instance AND cm.module = m.id
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
                 WHERE ctx.id = :contextid";
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

            // Cannot make use of helper::export_context_files(), need to manually export kalvidassign details
            $kalvidassigndata = self::get_kalvidassign_by_context($context);

            // Get kalvidassign details object for output
            $kalvidassign = self::get_kalvidassign_output($kalvidassigndata);
            writer::with_context($context)->export_data([], $kalvidassign);

            // Check if the user has marked any kalvidassign's submissions to determine kalvidassign submissions to export
            $teacher = (self::has_marked_kalvidassign_submissions($kalvidassigndata->id, $user->id) == true) ? true : false;

            // Get the kalvidassign submissions submitted by & marked by the user for an kalvidassign
            $submissionsdata = self::get_kalvidassign_submissions_by_kalvidassign($kalvidassigndata->id, $user->id, $teacher);

            foreach ($submissionsdata as $submissiondata) {
                // Default subcontext path to export assignment submissions submitted by the user.
                $subcontexts = [
                    get_string('privacy:submissionpath', 'mod_kalvidassign')
                ];

                if ($teacher == true) {
                    if ($submissiondata->teacher == $user->id) {
                        // Export kalvidassign submissions that have been marked by the user
                        $subcontexts = [
                            get_string('privacy:markedsubmissionspath', 'mod_kalvidassign'),
                            transform::user($submissiondata->userid)
                        ];
                    }
                }

                // Get kalvidassign submission details object for output
                $submission = self::get_kalvidassign_submission_output($submissiondata);

                writer::with_context($context)->export_data($subcontexts, $submission);
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

        // Delete all kalvidassign submissions for the kalvidassign associated with the context module.
        $kalvidassign = self::get_kalvidassign_by_context($context);

        if ($kalvidassign != null) {
            $DB->delete_records('kalvidassign_submission', ['vidassignid' => $kalvidassign->id]);
        }
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

        // Only retrieve kalvidassign submissions submitted by the user for deletion.
        $kalvidassignsubmissionids = array_keys(self::get_kalvidassign_submissions_by_contextlist($contextlist, $userid));
        $DB->delete_records_list('kalvidassign_submission', 'id', $kalvidassignsubmissionids);
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

        // Fetch the kalvidassign
        $kalvidassign = self::get_kalvidassign_by_context($context);
        $userids = $userlist->get_userids();

        list($inorequalsql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['videoassignid'] = $kalvidassign->id;

        // Get kalvidassign submissions ids
        $sql = "
            SELECT s.id
            FROM {kalvidassign_submission} s
            JOIN {kalvidassign} a ON s.vidassignid = a.id
            WHERE a.id = :videoassignid
            AND s.userid $inorequalsql";

        $submissionids = $DB->get_records_sql($sql, $params);

        // Delete related tables.
        $DB->delete_records_list('assignment_submissions', 'id', array_keys($submissionids));
    }

    /**
     * Helper function to return kalvidassign submissions submitted by / marked by a user and their contextlist.
     *
     * @param object $contextlist   Object with the contexts related to a userid to retrieve kalvidassign submissions by.
     * @param int $userid           The user ID to find kalvidassign submissions that were submitted by.
     * @return array                Array of kalvidassign submission details.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_kalvidassign_submissions_by_contextlist($contextlist, $userid) {
        global $DB;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modulename' => 'kalvidassign',
            'userid' => $userid
        ];

        $sql = "SELECT s.id as id,
                       s.vidassignid as vidassignid,
                       s.entry_id as entryid,
                       s.source as source,
                       s.grade as grade,
                       s.submissioncomment as submissioncomment,
                       s.teacher as teacher,
                       s.timemarked as timemarked,
                       s.timecreated as timecreated,
                       s.timemodified as timemodified
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {kalvidassign} a ON cm.instance = a.id
                  JOIN {kalvidassign_submission} s ON s.vidassignid = a.id
                 WHERE (s.userid = :userid)";

        $sql .= " AND ctx.id {$contextsql}";
        $params += $contextparams;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to return kalvidassign for a context module.
     *
     * @param object $context   The context module object to return the kalvidassign record by.
     * @return mixed            The kalvidassign details or null record associated with the context module.
     * @throws \dml_exception
     */
    protected static function get_kalvidassign_by_context($context) {
        global $DB;

        $params = [
            'modulename' => 'kalvidassign',
            'contextmodule' => CONTEXT_MODULE,
            'contextid' => $context->id
        ];

        $sql = "SELECT a.id,
                       a.name,
                       a.intro,
                       a.grade,
                       a.timedue,
                       a.timeavailable,
                       a.timemodified
                  FROM {kalvidassign} a
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextmodule
                 WHERE ctx.id = :contextid";

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Helper function generate kalvidassign output object for exporting.
     *
     * @param object $kalvidassigndata  Object containing kalvidassign data.
     * @return object                   Formatted kalvidassign output object for exporting.
     */
    protected static function get_kalvidassign_output($kalvidassigndata) {
        $kalvidassign = (object) [
            'name' => $kalvidassigndata->name,
            'intro' => $kalvidassigndata->intro,
            'grade' => $kalvidassigndata->grade,
            'timemodified' => transform::datetime($kalvidassigndata->timemodified)
        ];

        if ($kalvidassigndata->timeavailable != 0) {
            $kalvidassign->timeavailable = transform::datetime($kalvidassigndata->timeavailable);
        }

        if ($kalvidassigndata->timedue != 0) {
            $kalvidassign->timedue = transform::datetime($kalvidassigndata->timedue);
        }

        return $kalvidassign;
    }

    /**
     * Helper function to check if a user has marked kalvidassign submissions for a given kalvidassign.
     *
     * @param int $kalvidassignid The kalvidassign ID to check if user has marked associated submissions.
     * @param int $userid         The user ID to check if user has marked associated submissions.
     * @return bool               If user has marked associated submissions returns true, otherwise false.
     * @throws \dml_exception
     */
    protected static function has_marked_kalvidassign_submissions($kalvidassignid, $userid) {
        global $DB;

        $params = [
            'vidassignid' => $kalvidassignid,
            'teacher'    => $userid
        ];

        $sql = "SELECT count(s.id) as nomarked
                  FROM {kalvidassign_submission} s
                 WHERE s.vidassignid = :vidassignid
                   AND s.teacher = :teacher";

        $results = $DB->get_record_sql($sql, $params);

        return ($results->nomarked > 0) ? true : false;
    }

    /**
     * Helper function to retrieve kalvidassign submissions submitted by / marked by a user for a specific kalvidassign.
     *
     * @param int $kalvidassignid   The kalvidassign ID to retrieve kalvidassign submissions by.
     * @param int $userid           The user ID to retrieve kalvidassign submissions submitted / marked by.
     * @param bool $teacher         The teacher status to determine if marked kalvidassign submissions should be returned.
     * @return array                Array of kalvidassign submissions details.
     * @throws \dml_exception
     */
    protected static function get_kalvidassign_submissions_by_kalvidassign($kalvidassignid, $userid, $teacher = false) {
        global $DB;

        $params = [
            'vidassignid' => $kalvidassignid,
            'userid' => $userid
        ];

        $sql = "SELECT s.id as id,
                       s.vidassignid as vidassignid,
                       s.entry_id as entryid,
                       s.source as source,
                       s.grade as grade,
                       s.submissioncomment as submissioncomment,
                       s.teacher as teacher,
                       s.timemarked as timemarked,
                       s.timecreated as timecreated,
                       s.timemodified as timemodified,
                       s.userid as userid
                  FROM {kalvidassign_submission} s
                 WHERE s.vidassignid = :vidassignid
                   AND (s.userid = :userid";

        if ($teacher == true) {
            $sql .= " OR s.teacher = :teacher";
            $params['teacher'] = $userid;
        }

        $sql .= ")";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function generate kalvidassign submission output object for exporting.
     *
     * @param object $submissiondata    Object containing kalvidassign submission data.
     * @return object                   Formatted kalvidassign submission output for exporting.
     */
    protected static function get_kalvidassign_submission_output($submissiondata) {
        $submission = (object) [
            'vidassignid' => $submissiondata->vidassignid,
            'entry_id' => $submissiondata->entryid,
            'source' => $submissiondata->source,
            'grade' => $submissiondata->grade,
            'submissioncomment' => $submissiondata->submissioncomment,
            'teacher' => transform::user($submissiondata->teacher)
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
