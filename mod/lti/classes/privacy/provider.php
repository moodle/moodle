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
 * Privacy Subsystem implementation for mod_lti.
 *
 * @package    mod_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for mod_lti.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_external_location_link(
            'lti_provider',
            [
                'userid' => 'privacy:metadata:userid',
                'username' => 'privacy:metadata:username',
                'useridnumber' => 'privacy:metadata:useridnumber',
                'firstname' => 'privacy:metadata:firstname',
                'lastname' => 'privacy:metadata:lastname',
                'fullname' => 'privacy:metadata:fullname',
                'email' => 'privacy:metadata:email',
                'role' => 'privacy:metadata:role',
                'courseid' => 'privacy:metadata:courseid',
                'courseidnumber' => 'privacy:metadata:courseidnumber',
                'courseshortname' => 'privacy:metadata:courseshortname',
                'coursefullname' => 'privacy:metadata:coursefullname',
            ],
            'privacy:metadata:externalpurpose'
        );

        $items->add_database_table(
            'lti_submission',
            [
                'userid' => 'privacy:metadata:lti_submission:userid',
                'datesubmitted' => 'privacy:metadata:lti_submission:datesubmitted',
                'dateupdated' => 'privacy:metadata:lti_submission:dateupdated',
                'gradepercent' => 'privacy:metadata:lti_submission:gradepercent',
                'originalgrade' => 'privacy:metadata:lti_submission:originalgrade',
            ],
            'privacy:metadata:lti_submission'
        );

        $items->add_database_table(
            'lti_tool_proxies',
            [
                'name' => 'privacy:metadata:lti_tool_proxies:name',
                'createdby' => 'privacy:metadata:createdby',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified'
            ],
            'privacy:metadata:lti_tool_proxies'
        );

        $items->add_database_table(
            'lti_types',
            [
                'name' => 'privacy:metadata:lti_types:name',
                'createdby' => 'privacy:metadata:createdby',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified'
            ],
            'privacy:metadata:lti_types'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        // Fetch all LTI submissions.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm
                    ON cm.id = c.instanceid
                   AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m
                    ON m.id = cm.module
                   AND m.name = :modname
            INNER JOIN {lti} lti
                    ON lti.id = cm.instance
            INNER JOIN {lti_submission} ltisub
                    ON ltisub.ltiid = lti.id
                 WHERE ltisub.userid = :userid";

        $params = [
            'modname' => 'lti',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        // Fetch all LTI types.
        $sql = "SELECT c.id
                 FROM {context} c
                 JOIN {course} course
                   ON c.contextlevel = :contextlevel
                  AND c.instanceid = course.id
                 JOIN {lti_types} ltit
                   ON ltit.course = course.id
                WHERE ltit.createdby = :userid";

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid
        ];
        $contextlist->add_from_sql($sql, $params);

        // The LTI tool proxies sit in the system context.
        $contextlist->add_system_context();

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        // Fetch all LTI submissions.
        $sql = "SELECT ltisub.userid
                  FROM {context} c
            INNER JOIN {course_modules} cm
                    ON cm.id = c.instanceid
                   AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m
                    ON m.id = cm.module
                   AND m.name = :modname
            INNER JOIN {lti} lti
                    ON lti.id = cm.instance
            INNER JOIN {lti_submission} ltisub
                    ON ltisub.ltiid = lti.id
                 WHERE c.id = :contextid";

        $params = [
            'modname' => 'lti',
            'contextlevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $userlist->add_from_sql('userid', $sql, $params);

        // Fetch all LTI types.
        $sql = "SELECT ltit.createdby AS userid
                 FROM {context} c
                 JOIN {course} course
                   ON c.contextlevel = :contextlevel
                  AND c.instanceid = course.id
                 JOIN {lti_types} ltit
                   ON ltit.course = course.id
                WHERE c.id = :contextid";

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'contextid' => $context->id,
        ];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        self::export_user_data_lti_submissions($contextlist);

        self::export_user_data_lti_types($contextlist);

        self::export_user_data_lti_tool_proxies($contextlist);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        if ($cm = get_coursemodule_from_id('lti', $context->instanceid)) {
            $DB->delete_records('lti_submission', ['ltiid' => $cm->instance]);
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
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
            $DB->delete_records('lti_submission', ['ltiid' => $instanceid, 'userid' => $userid]);
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

        if ($context instanceof \context_module) {
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);

            list($insql, $inparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $sql = "ltiid = :instanceid AND userid {$insql}";
            $params = array_merge(['instanceid' => $instanceid], $inparams);

            $DB->delete_records_select('lti_submission', $sql, $params);
        }
    }

    /**
     * Export personal data for the given approved_contextlist related to LTI submissions.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    protected static function export_user_data_lti_submissions(approved_contextlist $contextlist) {
        global $DB;

        // Filter out any contexts that are not related to modules.
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);

        if (empty($cmids)) {
            return;
        }

        $user = $contextlist->get_user();

        // Get all the LTI activities associated with the above course modules.
        $ltiidstocmids = self::get_lti_ids_to_cmids_from_cmids($cmids);
        $ltiids = array_keys($ltiidstocmids);

        list($insql, $inparams) = $DB->get_in_or_equal($ltiids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $user->id]);
        $recordset = $DB->get_recordset_select('lti_submission', "ltiid $insql AND userid = :userid", $params, 'dateupdated, id');
        self::recordset_loop_and_export($recordset, 'ltiid', [], function($carry, $record) use ($user, $ltiidstocmids) {
            $carry[] = [
                'gradepercent' => $record->gradepercent,
                'originalgrade' => $record->originalgrade,
                'datesubmitted' => transform::datetime($record->datesubmitted),
                'dateupdated' => transform::datetime($record->dateupdated)
            ];
            return $carry;
        }, function($ltiid, $data) use ($user, $ltiidstocmids) {
            $context = \context_module::instance($ltiidstocmids[$ltiid]);
            $contextdata = helper::get_context_data($context, $user);
            $finaldata = (object) array_merge((array) $contextdata, ['submissions' => $data]);
            helper::export_context_files($context, $user);
            writer::with_context($context)->export_data([], $finaldata);
        });
    }

    /**
     * Export personal data for the given approved_contextlist related to LTI types.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    protected static function export_user_data_lti_types(approved_contextlist $contextlist) {
        global $DB;

        // Filter out any contexts that are not related to courses.
        $courseids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);

        if (empty($courseids)) {
            return;
        }

        $user = $contextlist->get_user();

        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $user->id]);
        $ltitypes = $DB->get_recordset_select('lti_types', "course $insql AND createdby = :userid", $params, 'timecreated ASC');
        self::recordset_loop_and_export($ltitypes, 'course', [], function($carry, $record) {
            $context = \context_course::instance($record->course);
            $options = ['context' => $context];
            $carry[] = [
                'name' => format_string($record->name, true, $options),
                'createdby' => transform::user($record->createdby),
                'timecreated' => transform::datetime($record->timecreated),
                'timemodified' => transform::datetime($record->timemodified)
            ];
            return $carry;
        }, function($courseid, $data) {
            $context = \context_course::instance($courseid);
            $finaldata = (object) ['lti_types' => $data];
            writer::with_context($context)->export_data([], $finaldata);
        });
    }

    /**
     * Export personal data for the given approved_contextlist related to LTI tool proxies.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    protected static function export_user_data_lti_tool_proxies(approved_contextlist $contextlist) {
        global $DB;

        // Filter out any contexts that are not related to system context.
        $systemcontexts = array_filter($contextlist->get_contexts(), function($context) {
            return $context->contextlevel == CONTEXT_SYSTEM;
        });

        if (empty($systemcontexts)) {
            return;
        }

        $user = $contextlist->get_user();

        $systemcontext = \context_system::instance();

        $data = [];
        $ltiproxies = $DB->get_recordset('lti_tool_proxies', ['createdby' => $user->id], 'timecreated ASC');
        foreach ($ltiproxies as $ltiproxy) {
            $data[] = [
                'name' => format_string($ltiproxy->name, true, ['context' => $systemcontext]),
                'createdby' => transform::user($ltiproxy->createdby),
                'timecreated' => transform::datetime($ltiproxy->timecreated),
                'timemodified' => transform::datetime($ltiproxy->timemodified)
            ];
        }
        $ltiproxies->close();

        $finaldata = (object) ['lti_tool_proxies' => $data];
        writer::with_context($systemcontext)->export_data([], $finaldata);
    }

    /**
     * Return a dict of LTI IDs mapped to their course module ID.
     *
     * @param array $cmids The course module IDs.
     * @return array In the form of [$ltiid => $cmid].
     */
    protected static function get_lti_ids_to_cmids_from_cmids(array $cmids) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "SELECT lti.id, cm.id AS cmid
                 FROM {lti} lti
                 JOIN {modules} m
                   ON m.name = :lti
                 JOIN {course_modules} cm
                   ON cm.instance = lti.id
                  AND cm.module = m.id
                WHERE cm.id $insql";
        $params = array_merge($inparams, ['lti' => 'lti']);

        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Loop and export from a recordset.
     *
     * @param \moodle_recordset $recordset The recordset.
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
}
