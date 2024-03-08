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
 * Privacy Subsystem implementation for report_stats.
 *
 * @package    report_stats
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_stats\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy Subsystem for report_stats implementing provider.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\subsystem\provider{

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection): collection {
        $statsuserdaily = [
            'courseid' => 'privacy:metadata:courseid',
            'userid' => 'privacy:metadata:userid',
            'roleid' => 'privacy:metadata:roleid',
            'timeend' => 'privacy:metadata:timeend',
            'statsreads' => 'privacy:metadata:statsreads',
            'statswrites' => 'privacy:metadata:statswrites',
            'stattype' => 'privacy:metadata:stattype'
        ];

        $statsuserweekly = [
            'courseid' => 'privacy:metadata:courseid',
            'userid' => 'privacy:metadata:userid',
            'roleid' => 'privacy:metadata:roleid',
            'timeend' => 'privacy:metadata:timeend',
            'statsreads' => 'privacy:metadata:statsreads',
            'statswrites' => 'privacy:metadata:statswrites',
            'stattype' => 'privacy:metadata:stattype'
        ];

        $statsusermonthly = [
            'courseid' => 'privacy:metadata:courseid',
            'userid' => 'privacy:metadata:userid',
            'roleid' => 'privacy:metadata:roleid',
            'timeend' => 'privacy:metadata:timeend',
            'statsreads' => 'privacy:metadata:statsreads',
            'statswrites' => 'privacy:metadata:statswrites',
            'stattype' => 'privacy:metadata:stattype'
        ];
        $collection->add_database_table('stats_user_daily', $statsuserdaily, 'privacy:metadata:statssummary');
        $collection->add_database_table('stats_user_weekly', $statsuserweekly, 'privacy:metadata:statssummary');
        $collection->add_database_table('stats_user_monthly', $statsusermonthly, 'privacy:metadata:statssummary');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $params = ['userid' => $userid, 'contextcourse' => CONTEXT_COURSE];
        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {stats_user_daily} sud ON sud.courseid = ctx.instanceid AND sud.userid = :userid
                WHERE ctx.contextlevel = :contextcourse";

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {stats_user_weekly} suw ON suw.courseid = ctx.instanceid AND suw.userid = :userid
                WHERE ctx.contextlevel = :contextcourse";
        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {stats_user_monthly} sum ON sum.courseid = ctx.instanceid AND sum.userid = :userid
                WHERE ctx.contextlevel = :contextcourse";
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        $params = ['courseid' => $context->instanceid];

        $sql = "SELECT userid FROM {stats_user_daily} WHERE courseid = :courseid";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT userid FROM {stats_user_weekly} WHERE courseid = :courseid";
        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT userid FROM {stats_user_monthly} WHERE courseid = :courseid";
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Some sneeky person might have sent us the wrong context list. We should check.
        if ($contextlist->get_component() != 'report_stats') {
            return;
        }

        // Got to check that someone hasn't foolishly added a context between creating the context list and then filtering down
        // to an approved context.
        $contexts = array_filter($contextlist->get_contexts(), function($context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                return $context;
            }
        });

        $tables = [
            'stats_user_daily' => get_string('privacy:dailypath', 'report_stats'),
            'stats_user_weekly' => get_string('privacy:weeklypath', 'report_stats'),
            'stats_user_monthly' => get_string('privacy:monthlypath', 'report_stats')
        ];

        $courseids = array_map(function($context) {
            return $context->instanceid;
        }, $contexts);

        foreach ($tables as $table => $path) {

            list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $sql = "SELECT s.id, c.fullname, s.roleid, s.timeend, s.statsreads, s.statswrites, s.stattype, c.id as courseid
                      FROM {" . $table . "} s
                      JOIN {course} c ON s.courseid = c.id
                     WHERE s.userid = :userid AND c.id $insql
                     ORDER BY c.id ASC";
            $params['userid'] = $contextlist->get_user()->id;
            $records = $DB->get_records_sql($sql, $params);

            $statsrecords = [];
            foreach ($records as $record) {
                $context = \context_course::instance($record->courseid);
                if (!isset($statsrecords[$record->courseid])) {
                    $statsrecords[$record->courseid] = new \stdClass();
                    $statsrecords[$record->courseid]->context = $context;
                }
                $statsrecords[$record->courseid]->entries[] = [
                    'course' => format_string($record->fullname, true, ['context' => $context]),
                    'roleid' => $record->roleid,
                    'timeend' => \core_privacy\local\request\transform::datetime($record->timeend),
                    'statsreads' => $record->statsreads,
                    'statswrites' => $record->statswrites,
                    'stattype' => $record->stattype
                ];
            }
            foreach ($statsrecords as $coursestats) {
                \core_privacy\local\request\writer::with_context($coursestats->context)->export_data([$path],
                        (object) $coursestats->entries);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Check that this context is a course context.
        if ($context->contextlevel == CONTEXT_COURSE) {
            static::delete_stats($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if ($contextlist->get_component() != 'report_stats') {
            return;
        }
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                static::delete_stats($context->instanceid, $contextlist->get_user()->id);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_course) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $select = "courseid = :courseid AND userid {$usersql}";
            $params = ['courseid' => $context->instanceid] + $userparams;

            $DB->delete_records_select('stats_user_daily', $select, $params);
            $DB->delete_records_select('stats_user_weekly', $select, $params);
            $DB->delete_records_select('stats_user_monthly', $select, $params);
        }
    }

    /**
     * Deletes stats for a given course.
     *
     * @param int $courseid The course ID to delete the stats for.
     * @param int $userid Optionally a user id to delete records with.
     */
    protected static function delete_stats(int $courseid, int $userid = null) {
        global $DB;
        $params = (isset($userid)) ? ['courseid' => $courseid, 'userid' => $userid] : ['courseid' => $courseid];
        $DB->delete_records('stats_user_daily', $params);
        $DB->delete_records('stats_user_weekly', $params);
        $DB->delete_records('stats_user_monthly', $params);
    }
}
