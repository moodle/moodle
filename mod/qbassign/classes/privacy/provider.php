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
 * Privacy class for requesting user data.
 *
 * @package    mod_qbassign
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\helper;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\manager;

/**
 * Privacy class for requesting user data.
 *
 * @package    mod_qbassign
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\user_preference_provider,
        \core_privacy\local\request\core_userlist_provider {

    /** Interface for all qbassign submission sub-plugins. */
    const qbassignSUBMISSION_INTERFACE = 'mod_qbassign\privacy\qbassignsubmission_provider';

    /** Interface for all qbassign submission sub-plugins. This allows for deletion of users with a context. */
    const qbassignSUBMISSION_USER_INTERFACE = 'mod_qbassign\privacy\qbassignsubmission_user_provider';

    /** Interface for all qbassign feedback sub-plugins. This allows for deletion of users with a context. */
    const qbassignFEEDBACK_USER_INTERFACE = 'mod_qbassign\privacy\qbassignfeedback_user_provider';

    /** Interface for all qbassign feedback sub-plugins. */
    const qbassignFEEDBACK_INTERFACE = 'mod_qbassign\privacy\qbassignfeedback_provider';

    /**
     * Provides meta data that is stored about a user with mod_qbassign
     *
     * @param  collection $collection A collection of meta data items to be added to.
     * @return  collection Returns the collection of metadata.
     */
    public static function get_metadata(collection $collection) : collection {
        $qbassigngrades = [
                'userid' => 'privacy:metadata:userid',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'timemodified',
                'grader' => 'privacy:metadata:grader',
                'grade' => 'privacy:metadata:grade',
                'attemptnumber' => 'attemptnumber'
        ];
        $qbassignoverrides = [
                'groupid' => 'privacy:metadata:groupid',
                'userid' => 'privacy:metadata:userid',
                'allowsubmissionsfromdate' => 'allowsubmissionsfromdate',
                'duedate' => 'duedate',
                'cutoffdate' => 'cutoffdate'
        ];
        $qbassignsubmission = [
                'userid' => 'privacy:metadata:userid',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'timemodified',
                'timestarted' => 'privacy:metadata:timestarted',
                'status' => 'gradingstatus',
                'groupid' => 'privacy:metadata:groupid',
                'attemptnumber' => 'attemptnumber',
                'latest' => 'privacy:metadata:latest'
        ];
        $qbassignuserflags = [
                'userid' => 'privacy:metadata:userid',
                'qbassignment' => 'privacy:metadata:qbassignmentid',
                'locked' => 'locksubmissions',
                'mailed' => 'privacy:metadata:mailed',
                'extensionduedate' => 'extensionduedate',
                'workflowstate' => 'markingworkflowstate',
                'allocatedmarker' => 'allocatedmarker'
        ];
        $qbassignusermapping = [
                'qbassignment' => 'privacy:metadata:qbassignmentid',
                'userid' => 'privacy:metadata:userid'
        ];
        $collection->add_database_table('qbassign_grades', $qbassigngrades, 'privacy:metadata:qbassigngrades');
        $collection->add_database_table('qbassign_overrides', $qbassignoverrides, 'privacy:metadata:qbassignoverrides');
        $collection->add_database_table('qbassign_submission', $qbassignsubmission, 'privacy:metadata:qbassignsubmissiondetail');
        $collection->add_database_table('qbassign_user_flags', $qbassignuserflags, 'privacy:metadata:qbassignuserflags');
        $collection->add_database_table('qbassign_user_mapping', $qbassignusermapping, 'privacy:metadata:qbassignusermapping');
        $collection->add_user_preference('qbassign_perpage', 'privacy:metadata:qbassignperpage');
        $collection->add_user_preference('qbassign_filter', 'privacy:metadata:qbassignfilter');
        $collection->add_user_preference('qbassign_markerfilter', 'privacy:metadata:qbassignmarkerfilter');
        $collection->add_user_preference('qbassign_workflowfilter', 'privacy:metadata:qbassignworkflowfilter');
        $collection->add_user_preference('qbassign_quickgrading', 'privacy:metadata:qbassignquickgrading');
        $collection->add_user_preference('qbassign_downloadasfolders', 'privacy:metadata:qbassigndownloadasfolders');

        // Link to subplugins.
        $collection->add_plugintype_link('qbassignsubmission', [],'privacy:metadata:qbassignsubmissionpluginsummary');
        $collection->add_plugintype_link('qbassignfeedback', [], 'privacy:metadata:qbassignfeedbackpluginsummary');
        $collection->add_subsystem_link('core_message', [], 'privacy:metadata:qbassignmessageexplanation');

        return $collection;
    }

    /**
     * Returns all of the contexts that has information relating to the userid.
     *
     * @param  int $userid The user ID.
     * @return contextlist an object with the contexts related to a userid.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $params = ['modulename' => 'qbassign',
                   'contextlevel' => CONTEXT_MODULE,
                   'userid' => $userid,
                   'graderid' => $userid,
                   'aouserid' => $userid,
                   'asnuserid' => $userid,
                   'aufuserid' => $userid,
                   'aumuserid' => $userid];

        $sql = "SELECT ctx.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {qbassign} a ON cm.instance = a.id
                  JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {qbassign_grades} ag ON a.id = ag.qbassignment AND (ag.userid = :userid OR ag.grader = :graderid)";

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {qbassign} a ON cm.instance = a.id
                  JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {qbassign_overrides} ao ON a.id = ao.qbassignid
                 WHERE ao.userid = :aouserid";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {qbassign} a ON cm.instance = a.id
                  JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {qbassign_submission} asn ON a.id = asn.qbassignment
                 WHERE asn.userid = :asnuserid";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {qbassign} a ON cm.instance = a.id
                  JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {qbassign_user_flags} auf ON a.id = auf.qbassignment
                 WHERE auf.userid = :aufuserid";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
                  JOIN {qbassign} a ON cm.instance = a.id
                  JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {qbassign_user_mapping} aum ON a.id = aum.qbassignment
                 WHERE aum.userid = :aumuserid";

        $contextlist->add_from_sql($sql, $params);

        manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_INTERFACE,
                'get_context_for_userid_within_feedback', [$userid, $contextlist]);
        manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_INTERFACE,
                'get_context_for_userid_within_submission', [$userid, $contextlist]);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {

        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $params = [
            'modulename' => 'qbassign',
            'contextid' => $context->id,
            'contextlevel' => CONTEXT_MODULE
        ];

        $sql = "SELECT g.userid, g.grader
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {qbassign} a ON a.id = cm.instance
                  JOIN {qbassign_grades} g ON a.id = g.qbassignment
                 WHERE ctx.id = :contextid AND ctx.contextlevel = :contextlevel";
        $userlist->add_from_sql('userid', $sql, $params);
        $userlist->add_from_sql('grader', $sql, $params);

        $sql = "SELECT o.userid
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {qbassign} a ON a.id = cm.instance
                  JOIN {qbassign_overrides} o ON a.id = o.qbassignid
                 WHERE ctx.id = :contextid AND ctx.contextlevel = :contextlevel";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT s.userid
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {qbassign} a ON a.id = cm.instance
                  JOIN {qbassign_submission} s ON a.id = s.qbassignment
                 WHERE ctx.id = :contextid AND ctx.contextlevel = :contextlevel";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT uf.userid
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {qbassign} a ON a.id = cm.instance
                  JOIN {qbassign_user_flags} uf ON a.id = uf.qbassignment
                 WHERE ctx.id = :contextid AND ctx.contextlevel = :contextlevel";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT um.userid
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {qbassign} a ON a.id = cm.instance
                  JOIN {qbassign_user_mapping} um ON a.id = um.qbassignment
                 WHERE ctx.id = :contextid AND ctx.contextlevel = :contextlevel";
        $userlist->add_from_sql('userid', $sql, $params);

        manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_USER_INTERFACE,
                'get_userids_from_context', [$userlist]);
        manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_USER_INTERFACE,
                'get_userids_from_context', [$userlist]);
    }

    /**
     * Write out the user data filtered by contexts.
     *
     * @param approved_contextlist $contextlist contexts that we are writing data out from.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        foreach ($contextlist->get_contexts() as $context) {
            // Check that the context is a module context.
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            $user = $contextlist->get_user();
            $qbassigndata = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);

            writer::with_context($context)->export_data([], $qbassigndata);
            $qbassign = new \qbassign($context, null, null);

            // I need to find out if I'm a student or a teacher.
            if ($userids = self::get_graded_users($user->id, $qbassign)) {
                // Return teacher info.
                $currentpath = [get_string('privacy:studentpath', 'mod_qbassign')];
                foreach ($userids as $studentuserid) {
                    $studentpath = array_merge($currentpath, [$studentuserid->id]);
                    static::export_submission($qbassign, $studentuserid, $context, $studentpath, true);
                }
            }

            static::export_overrides($context, $qbassign, $user);
            static::export_submission($qbassign, $user, $context, []);
            // Meta data.
            self::store_qbassign_user_flags($context, $qbassign, $user->id);
            if ($qbassign->is_blind_marking()) {
                $uniqueid = $qbassign->get_uniqueid_for_user_static($qbassign->get_instance()->id, $contextlist->get_user()->id);
                if ($uniqueid) {
                    writer::with_context($context)
                            ->export_metadata([get_string('blindmarking', 'mod_qbassign')], 'blindmarkingid', $uniqueid,
                                    get_string('privacy:blindmarkingidentifier', 'mod_qbassign'));
                }
            }
        }
    }

    /**
     * Delete all use data which matches the specified context.
     *
     * @param \context $context The module context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id('qbassign', $context->instanceid);
            if ($cm) {
                // Get the qbassignment related to this context.
                $qbassign = new \qbassign($context, null, null);
                // What to do first... Get sub plugins to delete their stuff.
                $requestdata = new qbassign_plugin_request_data($context, $qbassign);
                manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_INTERFACE,
                    'delete_submission_for_context', [$requestdata]);
                $requestdata = new qbassign_plugin_request_data($context, $qbassign);
                manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_INTERFACE,
                    'delete_feedback_for_context', [$requestdata]);
                $DB->delete_records('qbassign_grades', ['qbassignment' => $qbassign->get_instance()->id]);

                // Delete advanced grading information.
                $gradingmanager = get_grading_manager($context, 'mod_qbassign', 'submissions');
                $controller = $gradingmanager->get_active_controller();
                if (isset($controller)) {
                    \core_grading\privacy\provider::delete_instance_data($context);
                }

                // Time to roll my own method for deleting overrides.
                static::delete_overrides_for_users($qbassign);
                $DB->delete_records('qbassign_submission', ['qbassignment' => $qbassign->get_instance()->id]);
                $DB->delete_records('qbassign_user_flags', ['qbassignment' => $qbassign->get_instance()->id]);
                $DB->delete_records('qbassign_user_mapping', ['qbassignment' => $qbassign->get_instance()->id]);
            }
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        foreach ($contextlist as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            // Get the qbassign object.
            $qbassign = new \qbassign($context, null, null);
            $qbassignid = $qbassign->get_instance()->id;

            $submissions = $DB->get_records('qbassign_submission', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
            foreach ($submissions as $submission) {
                $requestdata = new qbassign_plugin_request_data($context, $qbassign, $submission, [], $user);
                manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_INTERFACE,
                        'delete_submission_for_userid', [$requestdata]);
            }

            $grades = $DB->get_records('qbassign_grades', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
            $gradingmanager = get_grading_manager($context, 'mod_qbassign', 'submissions');
            $controller = $gradingmanager->get_active_controller();
            foreach ($grades as $grade) {
                $requestdata = new qbassign_plugin_request_data($context, $qbassign, $grade, [], $user);
                manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_INTERFACE,
                        'delete_feedback_for_grade', [$requestdata]);
                // Delete advanced grading information.
                if (isset($controller)) {
                    \core_grading\privacy\provider::delete_instance_data($context, $grade->id);
                }
            }

            static::delete_overrides_for_users($qbassign, [$user->id]);
            $DB->delete_records('qbassign_user_flags', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
            $DB->delete_records('qbassign_user_mapping', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
            $DB->delete_records('qbassign_grades', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
            $DB->delete_records('qbassign_submission', ['qbassignment' => $qbassignid, 'userid' => $user->id]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param  approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $userids = $userlist->get_userids();

        $qbassign = new \qbassign($context, null, null);
        $qbassignid = $qbassign->get_instance()->id;
        $requestdata = new qbassign_plugin_request_data($context, $qbassign);
        $requestdata->set_userids($userids);
        $requestdata->populate_submissions_and_grades();
        manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_USER_INTERFACE, 'delete_submissions',
                [$requestdata]);
        manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_USER_INTERFACE, 'delete_feedback_for_grades',
                [$requestdata]);

        // Update this function to delete advanced grading information.
        $gradingmanager = get_grading_manager($context, 'mod_qbassign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (isset($controller)) {
            $gradeids = $requestdata->get_gradeids();
            // Careful here, if no gradeids are provided then all data is deleted for the context.
            if (!empty($gradeids)) {
                \core_grading\privacy\provider::delete_data_for_instances($context, $gradeids);
            }
        }

        static::delete_overrides_for_users($qbassign, $userids);
        list($sql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['qbassignment'] = $qbassignid;
        $DB->delete_records_select('qbassign_user_flags', "qbassignment = :qbassignment AND userid $sql", $params);
        $DB->delete_records_select('qbassign_user_mapping', "qbassignment = :qbassignment AND userid $sql", $params);
        $DB->delete_records_select('qbassign_grades', "qbassignment = :qbassignment AND userid $sql", $params);
        $DB->delete_records_select('qbassign_submission', "qbassignment = :qbassignment AND userid $sql", $params);
    }

    /**
     * Deletes qbassignment overrides in bulk
     *
     * @param  \qbassign $qbassign  The qbassignment object
     * @param  array   $userids An array of user IDs
     */
    protected static function delete_overrides_for_users(\qbassign $qbassign, array $userids = []) {
        global $DB;
        $qbassignid = $qbassign->get_instance()->id;

        $usersql = '';
        $params = ['qbassignid' => $qbassignid];
        if (!empty($userids)) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params = array_merge($params, $userparams);
            $overrides = $DB->get_records_select('qbassign_overrides', "qbassignid = :qbassignid AND userid $usersql", $params);
        } else {
            $overrides = $DB->get_records('qbassign_overrides', $params);
        }
        if (!empty($overrides)) {
            $params = ['modulename' => 'qbassign', 'instance' => $qbassignid];
            if (!empty($userids)) {
                $params = array_merge($params, $userparams);
                $DB->delete_records_select('event', "modulename = :modulename AND instance = :instance AND userid $usersql",
                        $params);
                // Setting up for the next query.
                $params = $userparams;
                $usersql = "AND userid $usersql";
            } else {
                $DB->delete_records('event', $params);
                // Setting up for the next query.
                $params = [];
            }
            list($overridesql, $overrideparams) = $DB->get_in_or_equal(array_keys($overrides), SQL_PARAMS_NAMED);
            $params = array_merge($params, $overrideparams);
            $DB->delete_records_select('qbassign_overrides', "id $overridesql $usersql", $params);
        }
    }

    /**
     * Find out if this user has graded any users.
     *
     * @param  int $userid The user ID (potential teacher).
     * @param  qbassign $qbassign The qbassignment object.
     * @return array If successful an array of objects with userids that this user graded, otherwise false.
     */
    protected static function get_graded_users(int $userid, \qbassign $qbassign) {
        $params = ['grader' => $userid, 'qbassignid' => $qbassign->get_instance()->id];

        $sql = "SELECT DISTINCT userid AS id
                  FROM {qbassign_grades}
                 WHERE grader = :grader AND qbassignment = :qbassignid";

        $useridlist = new useridlist($userid, $qbassign->get_instance()->id);
        $useridlist->add_from_sql($sql, $params);

        // Call sub-plugins to see if they have information not already collected.
        manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_INTERFACE, 'get_student_user_ids',
                [$useridlist]);
        manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_INTERFACE, 'get_student_user_ids', [$useridlist]);

        $userids = $useridlist->get_userids();
        return ($userids) ? $userids : false;
    }

    /**
     * Writes out various user meta data about the qbassignment.
     *
     * @param  \context $context The context of this qbassignment.
     * @param  \qbassign $qbassign The qbassignment object.
     * @param  int $userid The user ID
     */
    protected static function store_qbassign_user_flags(\context $context, \qbassign $qbassign, int $userid) {
        $datatypes = ['locked' => get_string('locksubmissions', 'mod_qbassign'),
                      'mailed' => get_string('privacy:metadata:mailed', 'mod_qbassign'),
                      'extensionduedate' => get_string('extensionduedate', 'mod_qbassign'),
                      'workflowstate' => get_string('markingworkflowstate', 'mod_qbassign'),
                      'allocatedmarker' => get_string('allocatedmarker_help', 'mod_qbassign')];
        $userflags = (array)$qbassign->get_user_flags($userid, false);

        foreach ($datatypes as $key => $description) {
            if (isset($userflags[$key]) && !empty($userflags[$key])) {
                $value = $userflags[$key];
                if ($key == 'locked' || $key == 'mailed') {
                    $value = transform::yesno($value);
                } else if ($key == 'extensionduedate') {
                    $value = transform::datetime($value);
                }
                writer::with_context($context)->export_metadata([], $key, $value, $description);
            }
        }
    }

    /**
     * Formats and then exports the user's grade data.
     *
     * @param  \stdClass $grade The qbassign grade object
     * @param  \context $context The context object
     * @param  array $currentpath Current directory path that we are exporting to.
     */
    protected static function export_grade_data(\stdClass $grade, \context $context, array $currentpath) {
        $gradedata = (object)[
            'timecreated' => transform::datetime($grade->timecreated),
            'timemodified' => transform::datetime($grade->timemodified),
            'grader' => transform::user($grade->grader),
            'grade' => $grade->grade,
            'attemptnumber' => ($grade->attemptnumber + 1)
        ];
        writer::with_context($context)
                ->export_data(array_merge($currentpath, [get_string('privacy:gradepath', 'mod_qbassign')]), $gradedata);
    }

    /**
     * Formats and then exports the user's submission data.
     *
     * @param  \stdClass $submission The qbassign submission object
     * @param  \context $context The context object
     * @param  array $currentpath Current directory path that we are exporting to.
     */
    protected static function export_submission_data(\stdClass $submission, \context $context, array $currentpath) {
        $submissiondata = (object)[
            'timecreated' => transform::datetime($submission->timecreated),
            'timemodified' => transform::datetime($submission->timemodified),
            'timestarted' => transform::datetime($submission->timestarted),
            'status' => get_string('submissionstatus_' . $submission->status, 'mod_qbassign'),
            'groupid' => $submission->groupid,
            'attemptnumber' => ($submission->attemptnumber + 1),
            'latest' => transform::yesno($submission->latest)
        ];
        writer::with_context($context)
                ->export_data(array_merge($currentpath, [get_string('privacy:submissionpath', 'mod_qbassign')]), $submissiondata);
    }

    /**
     * Stores the user preferences related to mod_qbassign.
     *
     * @param  int $userid The user ID that we want the preferences for.
     */
    public static function export_user_preferences(int $userid) {
        $context = \context_system::instance();
        $qbassignpreferences = [
            'qbassign_perpage' => ['string' => get_string('privacy:metadata:qbassignperpage', 'mod_qbassign'), 'bool' => false],
            'qbassign_filter' => ['string' => get_string('privacy:metadata:qbassignfilter', 'mod_qbassign'), 'bool' => false],
            'qbassign_markerfilter' => ['string' => get_string('privacy:metadata:qbassignmarkerfilter', 'mod_qbassign'), 'bool' => true],
            'qbassign_workflowfilter' => ['string' => get_string('privacy:metadata:qbassignworkflowfilter', 'mod_qbassign'),
                    'bool' => true],
            'qbassign_quickgrading' => ['string' => get_string('privacy:metadata:qbassignquickgrading', 'mod_qbassign'), 'bool' => true],
            'qbassign_downloadasfolders' => ['string' => get_string('privacy:metadata:qbassigndownloadasfolders', 'mod_qbassign'),
                    'bool' => true]
        ];
        foreach ($qbassignpreferences as $key => $preference) {
            $value = get_user_preferences($key, null, $userid);
            if ($preference['bool']) {
                $value = transform::yesno($value);
            }
            if (isset($value)) {
                writer::with_context($context)->export_user_preference('mod_qbassign', $key, $value, $preference['string']);
            }
        }
    }

    /**
     * Export overrides for this qbassignment.
     *
     * @param  \context $context Context
     * @param  \qbassign $qbassign The qbassign object.
     * @param  \stdClass $user The user object.
     */
    public static function export_overrides(\context $context, \qbassign $qbassign, \stdClass $user) {

        $overrides = $qbassign->override_exists($user->id);
        // Overrides returns an array with data in it, but an override with actual data will have the qbassign ID set.
        if (isset($overrides->qbassignid)) {
            $data = new \stdClass();
            if (!empty($overrides->duedate)) {
                $data->duedate = transform::datetime($overrides->duedate);
            }
            if (!empty($overrides->cutoffdate)) {
                $data->cutoffdate = transform::datetime($overrides->cutoffdate);
            }
            if (!empty($overrides->allowsubmissionsfromdate)) {
                $data->allowsubmissionsfromdate = transform::datetime($overrides->allowsubmissionsfromdate);
            }
            if (!empty($data)) {
                writer::with_context($context)->export_data([get_string('overrides', 'mod_qbassign')], $data);
            }
        }
    }

    /**
     * Exports qbassignment submission data for a user.
     *
     * @param  \qbassign         $qbassign           The qbassignment object
     * @param  \stdClass        $user             The user object
     * @param  \context_module $context          The context
     * @param  array           $path             The path for exporting data
     * @param  bool|boolean    $exportforteacher A flag for if this is exporting data as a teacher.
     */
    protected static function export_submission(\qbassign $qbassign, \stdClass $user, \context_module $context, array $path,
            bool $exportforteacher = false) {
        $submissions = $qbassign->get_all_submissions($user->id);
        $teacher = ($exportforteacher) ? $user : null;
        $gradingmanager = get_grading_manager($context, 'mod_qbassign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        foreach ($submissions as $submission) {
            // Attempt numbers start at zero, which is fine for programming, but doesn't make as much sense
            // for users.
            $submissionpath = array_merge($path,
                    [get_string('privacy:attemptpath', 'mod_qbassign', ($submission->attemptnumber + 1))]);

            $params = new qbassign_plugin_request_data($context, $qbassign, $submission, $submissionpath ,$teacher);
            manager::plugintype_class_callback('qbassignsubmission', self::qbassignSUBMISSION_INTERFACE,
                    'export_submission_user_data', [$params]);
            if (!isset($teacher)) {
                self::export_submission_data($submission, $context, $submissionpath);
            }
            $grade = $qbassign->get_user_grade($user->id, false, $submission->attemptnumber);
            if ($grade) {
                $params = new qbassign_plugin_request_data($context, $qbassign, $grade, $submissionpath, $teacher);
                manager::plugintype_class_callback('qbassignfeedback', self::qbassignFEEDBACK_INTERFACE, 'export_feedback_user_data',
                        [$params]);

                self::export_grade_data($grade, $context, $submissionpath);
                // Check for advanced grading and retrieve that information.
                if (isset($controller)) {
                    \core_grading\privacy\provider::export_item_data($context, $grade->id, $submissionpath);
                }
            }
        }
    }
}
