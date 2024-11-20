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

namespace mod_h5pactivity\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use stdClass;

/**
 * Privacy API implementation for the H5P activity plugin.
 *
 * @package    mod_h5pactivity
 * @category   privacy
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('h5pactivity_attempts', [
                'userid' => 'privacy:metadata:userid',
                'attempt' => 'privacy:metadata:attempt',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified',
                'rawscore' => 'privacy:metadata:rawscore',
            ], 'privacy:metadata:xapi_track');

        $collection->add_database_table('h5pactivity_attempts_results', [
                'attempt' => 'privacy:metadata:attempt',
                'timecreated' => 'privacy:metadata:timecreated',
                'rawscore' => 'privacy:metadata:rawscore',
            ], 'privacy:metadata:xapi_track_results');

        $collection->add_subsystem_link('core_xapi', [], 'privacy:metadata:xapisummary');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {h5pactivity_attempts} ss
                  JOIN {modules} m
                    ON m.name = :activityname
                  JOIN {course_modules} cm
                    ON cm.instance = ss.h5pactivityid
                   AND cm.module = m.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modlevel
                 WHERE ss.userid = :userid";

        $params = ['activityname' => 'h5pactivity', 'modlevel' => CONTEXT_MODULE, 'userid' => $userid];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        \core_xapi\privacy\provider::add_contexts_for_userid($contextlist, $userid, 'mod_h5pactivity');

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $sql = "SELECT ss.userid
                  FROM {h5pactivity_attempts} ss
                  JOIN {modules} m
                    ON m.name = 'h5pactivity'
                  JOIN {course_modules} cm
                    ON cm.instance = ss.h5pactivityid
                   AND cm.module = m.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modlevel
                 WHERE ctx.id = :contextid";

        $params = ['modlevel' => CONTEXT_MODULE, 'contextid' => $context->id];

        $userlist->add_from_sql('userid', $sql, $params);

        \core_xapi\privacy\provider::add_userids_for_context($userlist);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from CONTEXT_MODULE.
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contexts)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;
        // Get H5P attempts data.
        foreach ($contexts as $contextid) {
            $context = \context::instance_by_id($contextid);
            $data = helper::get_context_data($context, $user);
            writer::with_context($context)->export_data([], $data);
            helper::export_context_files($context, $user);

            // Get user's xAPI state data for the particular context.
            $state = \core_xapi\privacy\provider::get_xapi_states_for_user($contextlist->get_user()->id,
                    'mod_h5pactivity', $context->instanceid);
            if ($state) {
                // If the activity has xAPI state data by the user, include it in the export.
                writer::with_context($context)->export_data(
                        [get_string('privacy:xapistate', 'core_xapi')], (object) $state);
            }

        }

        // Get attempts track data.
        list($insql, $inparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $sql = "SELECT har.id,
                       ha.attempt,
                       har.description,
                       har.interactiontype,
                       har.response,
                       har.additionals,
                       har.rawscore,
                       har.maxscore,
                       har.duration,
                       har.timecreated,
                       ctx.id as contextid
                  FROM {h5pactivity_attempts_results} har
                  JOIN {h5pactivity_attempts} ha
                    ON har.attemptid = ha.id
                  JOIN {course_modules} cm
                    ON cm.instance = ha.h5pactivityid
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                 WHERE ctx.id $insql
                   AND ha.userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        $alldata = [];
        $attemptsdata = $DB->get_recordset_sql($sql, $params);
        foreach ($attemptsdata as $track) {
            $alldata[$track->contextid][$track->attempt][] = (object)[
                    'description' => $track->description,
                    'response' => $track->response,
                    'interactiontype' => $track->interactiontype,
                    'additionals' => $track->additionals,
                    'rawscore' => $track->rawscore,
                    'maxscore' => $track->maxscore,
                    'duration' => $track->duration,
                    'timecreated' => transform::datetime($track->timecreated),
                ];
        }
        $attemptsdata->close();

        // The result data is organised in:
        // {Course name}/{H5P activity name}/{My attempts}/{Attempt X}/data.json
        // where X is the attempt number.
        array_walk($alldata, function($attemptsdata, $contextid) {
            $context = \context::instance_by_id($contextid);
            array_walk($attemptsdata, function($data, $attempt) use ($context) {
                $subcontext = [
                    get_string('myattempts', 'mod_h5pactivity'),
                    get_string('attempt', 'mod_h5pactivity'). " $attempt"
                ];
                writer::with_context($context)->export_data(
                    $subcontext,
                    (object)['results' => $data]
                );
            });
        });
    }

    /**
     * Delete all user data which matches the specified context.
     *
     * @param \context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // This should not happen, but just in case.
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $cm = get_coursemodule_from_id('h5pactivity', $context->instanceid);
        if (!$cm) {
            // Only h5pactivity module will be handled.
            return;
        }

        self::delete_all_attempts($cm);

        // Delete xAPI state data.
        \core_xapi\privacy\provider::delete_states_for_all_users($context, 'mod_h5pactivity');

    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {

        foreach ($contextlist as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }

            $cm = get_coursemodule_from_id('h5pactivity', $context->instanceid);
            if (!$cm) {
                // Only h5pactivity module will be handled.
                continue;
            }

            $user = $contextlist->get_user();

            self::delete_all_attempts($cm, $user);

            // Delete xAPI state data.
            \core_xapi\privacy\provider::delete_states_for_user($contextlist, 'mod_h5pactivity');
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {

        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $cm = get_coursemodule_from_id('h5pactivity', $context->instanceid);
        if (!$cm) {
            // Only h5pactivity module will be handled.
            return;
        }

        $userids = $userlist->get_userids();

        foreach ($userids as $userid) {
            self::delete_all_attempts ($cm, (object)['id' => $userid]);
        }

        // Delete xAPI states data.
        \core_xapi\privacy\provider::delete_states_for_userlist($userlist);

    }

    /**
     * Wipe all attempt data for specific course_module and an optional user.
     *
     * @param stdClass $cm a course_module record
     * @param stdClass $user a user record
     */
    private static function delete_all_attempts(stdClass $cm, ?stdClass $user = null): void {
        global $DB;

        $where = 'a.h5pactivityid = :h5pactivityid';
        $conditions = ['h5pactivityid' => $cm->instance];
        if (!empty($user)) {
            $where .= ' AND a.userid = :userid';
            $conditions['userid'] = $user->id;
        }

        $DB->delete_records_select('h5pactivity_attempts_results', "attemptid IN (
                SELECT a.id
                FROM {h5pactivity_attempts} a
                WHERE $where)", $conditions);

        $DB->delete_records('h5pactivity_attempts', $conditions);
    }
}
