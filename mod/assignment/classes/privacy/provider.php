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
 * Privacy Subsystem implementation for mod_assignment.
 *
 * @package    mod_assignment
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assignment\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assignment/lib.php');

/**
 * Implementation of the privacy subsystem plugin provider for mod_assignment.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $collection a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table(
            'assignment_submissions',
            [
                'userid' => 'privacy:metadata:assignment_submissions:userid',
                'timecreated' => 'privacy:metadata:assignment_submissions:timecreated',
                'timemodified' => 'privacy:metadata:assignment_submissions:timemodified',
                'numfiles' => 'privacy:metadata:assignment_submissions:numfiles',
                'data1' => 'privacy:metadata:assignment_submissions:data1',
                'data2' => 'privacy:metadata:assignment_submissions:data2',
                'grade' => 'privacy:metadata:assignment_submissions:grade',
                'submissioncomment' => 'privacy:metadata:assignment_submissions:submissioncomment',
                'teacher' => 'privacy:metadata:assignment_submissions:teacher',
                'timemarked' => 'privacy:metadata:assignment_submissions:timemarked',
                'mailed' => 'privacy:metadata:assignment_submissions:mailed'
            ],
            'privacy:metadata:assignment_submissions'
        );

        // Legacy mod_assignment preferences from Moodle 2.X.
        $collection->add_user_preference('assignment_filter', 'privacy:metadata:assignmentfilter');
        $collection->add_user_preference('assignment_mailinfo', 'privacy:metadata:assignmentmailinfo');
        $collection->add_user_preference('assignment_perpage', 'privacy:metadata:assignmentperpage');
        $collection->add_user_preference('assignment_quickgrade', 'privacy:metadata:assignmentquickgrade');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        $sql = "SELECT DISTINCT
                       ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextmodule
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {assignment} a ON cm.instance = a.id
                  JOIN {assignment_submissions} s ON s.assignment = a.id
                 WHERE s.userid = :userid
                    OR s.teacher = :teacher";

        $params = [
            'contextmodule'  => CONTEXT_MODULE,
            'modulename'    => 'assignment',
            'userid'        => $userid,
            'teacher'       => $userid
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
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
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }

            // Cannot make use of helper::export_context_files(), need to manually export assignment details.
            $assignmentdata = self::get_assignment_by_context($context);

            // Get assignment details object for output.
            $assignment = self::get_assignment_output($assignmentdata);
            writer::with_context($context)->export_data([], $assignment);

            // Check if the user has marked any assignment's submissions to determine assignment submissions to export.
            $teacher = (self::has_marked_assignment_submissions($assignmentdata->id, $user->id) == true) ? true : false;

            // Get the assignment submissions submitted by & marked by the user for an assignment.
            $submissionsdata = self::get_assignment_submissions_by_assignment($assignmentdata->id, $user->id, $teacher);

            foreach ($submissionsdata as $submissiondata) {
                // Default subcontext path to export assignment submissions submitted by the user.
                $subcontexts = [
                    get_string('privacy:submissionpath', 'mod_assignment')
                ];

                if ($teacher == true) {
                    if ($submissiondata->teacher == $user->id) {
                        // Export assignment submissions that have been marked by the user.
                        $subcontexts = [
                            get_string('privacy:markedsubmissionspath', 'mod_assignment'),
                            transform::user($submissiondata->userid)
                        ];
                    }
                }

                // Get assignment submission details object for output.
                $submission = self::get_assignment_submission_output($submissiondata);
                $itemid = $submissiondata->id;

                writer::with_context($context)
                    ->export_data($subcontexts, $submission)
                    ->export_area_files($subcontexts, 'mod_assignment', 'submission', $itemid);
            }
        }
    }

    /**
     * Stores the user preferences related to mod_assign.
     *
     * @param  int $userid The user ID that we want the preferences for.
     */
    public static function export_user_preferences($userid) {
        $context = \context_system::instance();
        $assignmentpreferences = [
            'assignment_filter' => [
                'string' => get_string('privacy:metadata:assignmentfilter', 'mod_assignment'),
                'bool' => false
            ],
            'assignment_mailinfo' => [
                'string' => get_string('privacy:metadata:assignmentmailinfo', 'mod_assignment'),
                'bool' => false
            ],
            'assignment_perpage' => [
                'string' => get_string('privacy:metadata:assignmentperpage', 'mod_assignment'),
                'bool' => false
            ],
            'assignment_quickgrade' => [
                'string' => get_string('privacy:metadata:assignmentquickgrade', 'mod_assignment'),
                'bool' => false
            ],
        ];
        foreach ($assignmentpreferences as $key => $preference) {
            $value = get_user_preferences($key, null, $userid);
            if ($preference['bool']) {
                $value = transform::yesno($value);
            }
            if (isset($value)) {
                writer::with_context($context)
                    ->export_user_preference('mod_assignment', $key, $value, $preference['string']);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }

        if ($context->contextlevel == CONTEXT_MODULE) {
            // Delete all assignment submissions for the assignment associated with the context module.
            $assignment = self::get_assignment_by_context($context);
            if ($assignment != null) {
                $DB->delete_records('assignment_submissions', ['assignment' => $assignment->id]);

                // Delete all file uploads associated with the assignment submission for the specified context.
                $fs = get_file_storage();
                $fs->delete_area_files($context->id, 'mod_assignment', 'submission');
            }
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

        // Only retrieve assignment submissions submitted by the user for deletion.
        $assignmentsubmissionids = array_keys(self::get_assignment_submissions_by_contextlist($contextlist, $userid));
        $DB->delete_records_list('assignment_submissions', 'id', $assignmentsubmissionids);

        // Delete all file uploads associated with the assignment submission for the user's specified list of contexts.
        $fs = get_file_storage();
        foreach ($contextlist->get_contextids() as $contextid) {
            foreach ($assignmentsubmissionids as $submissionid) {
                $fs->delete_area_files($contextid, 'mod_assignment', 'submission', $submissionid);
            }
        }
    }

    // Start of helper functions.

    /**
     * Helper function to check if a user has marked assignment submissions for a given assignment.
     *
     * @param int $assignmentid The assignment ID to check if user has marked associated submissions.
     * @param int $userid       The user ID to check if user has marked associated submissions.
     * @return bool             If user has marked associated submissions returns true, otherwise false.
     * @throws \dml_exception
     */
    protected static function has_marked_assignment_submissions($assignmentid, $userid) {
        global $DB;

        $params = [
            'assignment' => $assignmentid,
            'teacher'    => $userid
        ];

        $sql = "SELECT count(s.id) as nomarked
                  FROM {assignment_submissions} s
                 WHERE s.assignment = :assignment
                   AND s.teacher = :teacher";

        $results = $DB->get_record_sql($sql, $params);

        return ($results->nomarked > 0) ? true : false;
    }

    /**
     * Helper function to return assignment for a context module.
     *
     * @param object $context   The context module object to return the assignment record by.
     * @return mixed            The assignment details or null record associated with the context module.
     * @throws \dml_exception
     */
    protected static function get_assignment_by_context($context) {
        global $DB;

        $params = [
            'modulename' => 'assignment',
            'contextmodule' => CONTEXT_MODULE,
            'contextid' => $context->id
        ];

        $sql = "SELECT a.id,
                       a.name,
                       a.intro,
                       a.assignmenttype,
                       a.grade,
                       a.timedue,
                       a.timeavailable,
                       a.timemodified
                  FROM {assignment} a
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextmodule
                 WHERE ctx.id = :contextid";

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Helper function to return assignment submissions submitted by / marked by a user and their contextlist.
     *
     * @param object $contextlist   Object with the contexts related to a userid to retrieve assignment submissions by.
     * @param int $userid           The user ID to find assignment submissions that were submitted by.
     * @param bool $teacher         The teacher status to determine if marked assignment submissions should be returned.
     * @return array                Array of assignment submission details.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_assignment_submissions_by_contextlist($contextlist, $userid, $teacher = false) {
        global $DB;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $params = [
            'contextmodule' => CONTEXT_MODULE,
            'modulename' => 'assignment',
            'userid' => $userid
        ];

        $sql = "SELECT s.id as id,
                       s.assignment as assignment,
                       s.numfiles as numfiles,
                       s.data1 as data1,
                       s.data2 as data2,
                       s.grade as grade,
                       s.submissioncomment as submissioncomment,
                       s.teacher as teacher,
                       s.timemarked as timemarked,
                       s.timecreated as timecreated,
                       s.timemodified as timemodified
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextmodule
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {assignment} a ON cm.instance = a.id
                  JOIN {assignment_submissions} s ON s.assignment = a.id
                 WHERE (s.userid = :userid";

        if ($teacher == true) {
            $sql .= " OR s.teacher = :teacher";
            $params['teacher'] = $userid;
        }

        $sql .= ")";

        $sql .= " AND ctx.id {$contextsql}";
        $params += $contextparams;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to retrieve assignment submissions submitted by / marked by a user for a specific assignment.
     *
     * @param int $assignmentid     The assignment ID to retrieve assignment submissions by.
     * @param int $userid           The user ID to retrieve assignment submissions submitted / marked by.
     * @param bool $teacher         The teacher status to determine if marked assignment submissions should be returned.
     * @return array                Array of assignment submissions details.
     * @throws \dml_exception
     */
    protected static function get_assignment_submissions_by_assignment($assignmentid, $userid, $teacher = false) {
        global $DB;

        $params = [
            'assignment' => $assignmentid,
            'userid' => $userid
        ];

        $sql = "SELECT s.id as id,
                       s.assignment as assignment,
                       s.numfiles as numfiles,
                       s.data1 as data1,
                       s.data2 as data2,
                       s.grade as grade,
                       s.submissioncomment as submissioncomment,
                       s.teacher as teacher,
                       s.timemarked as timemarked,
                       s.timecreated as timecreated,
                       s.timemodified as timemodified,
                       s.userid as userid
                  FROM {assignment_submissions} s
                 WHERE s.assignment = :assignment
                   AND (s.userid = :userid";

        if ($teacher == true) {
            $sql .= " OR s.teacher = :teacher";
            $params['teacher'] = $userid;
        }

        $sql .= ")";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function generate assignment output object for exporting.
     *
     * @param object $assignmentdata    Object containing assignment data.
     * @return object                   Formatted assignment output object for exporting.
     */
    protected static function get_assignment_output($assignmentdata) {
        $assignment = (object) [
            'name' => $assignmentdata->name,
            'intro' => $assignmentdata->intro,
            'assignmenttype' => $assignmentdata->assignmenttype,
            'grade' => $assignmentdata->grade,
            'timemodified' => transform::datetime($assignmentdata->timemodified)
        ];

        if ($assignmentdata->timeavailable != 0) {
            $assignment->timeavailable = transform::datetime($assignmentdata->timeavailable);
        }

        if ($assignmentdata->timedue != 0) {
            $assignment->timedue = transform::datetime($assignmentdata->timedue);
        }

        return $assignment;
    }

    /**
     * Helper function generate assignment submission output object for exporting.
     *
     * @param object $submissiondata    Object containing assignment submission data.
     * @return object                   Formatted assignment submission output for exporting.
     */
    protected static function get_assignment_submission_output($submissiondata) {
        $submission = (object) [
            'assignment' => $submissiondata->assignment,
            'numfiles' => $submissiondata->numfiles,
            'data1' => $submissiondata->data1,
            'data2' => $submissiondata->data2,
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
