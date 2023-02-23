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

declare(strict_types=1);

namespace core_reportbuilder\privacy;

use context;
use stdClass;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\user_preference_provider;
use core_privacy\local\request\writer;
use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\local\helpers\schedule as schedule_helper;
use core_reportbuilder\local\models\audience;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\schedule;

/**
 * Privacy Subsystem for core_reportbuilder
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    core_userlist_provider,
    user_preference_provider {

    /**
     * Returns metadata about the component
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(report::TABLE, [
            'name' => 'privacy:metadata:report:name',
            'source' => 'privacy:metadata:report:source',
            'conditiondata' => 'privacy:metadata:report:conditiondata',
            'settingsdata' => 'privacy:metadata:report:settingsdata',
            'uniquerows' => 'privacy:metadata:report:uniquerows',
            'usercreated' => 'privacy:metadata:report:usercreated',
            'usermodified' => 'privacy:metadata:report:usermodified',
            'timecreated' => 'privacy:metadata:report:timecreated',
            'timemodified' => 'privacy:metadata:report:timemodified',
        ], 'privacy:metadata:report');

        $collection->add_database_table(column::TABLE, [
            'uniqueidentifier' => 'privacy:metadata:column:uniqueidentifier',
            'usercreated' => 'privacy:metadata:column:usercreated',
            'usermodified' => 'privacy:metadata:column:usermodified',
        ], 'privacy:metadata:column');

        $collection->add_database_table(filter::TABLE, [
            'uniqueidentifier' => 'privacy:metadata:filter:uniqueidentifier',
            'usercreated' => 'privacy:metadata:filter:usercreated',
            'usermodified' => 'privacy:metadata:filter:usermodified',
        ], 'privacy:metadata:filter');

        $collection->add_database_table(audience::TABLE, [
            'classname' => 'privacy:metadata:audience:classname',
            'configdata' => 'privacy:metadata:audience:configdata',
            'heading' => 'privacy:metadata:audience:heading',
            'usercreated' => 'privacy:metadata:audience:usercreated',
            'usermodified' => 'privacy:metadata:audience:usermodified',
            'timecreated' => 'privacy:metadata:audience:timecreated',
            'timemodified' => 'privacy:metadata:audience:timemodified',
        ], 'privacy:metadata:audience');

        $collection->add_database_table(schedule::TABLE, [
            'name' => 'privacy:metadata:schedule:name',
            'enabled' => 'privacy:metadata:schedule:enabled',
            'audiences' => 'privacy:metadata:schedule:audiences',
            'format' => 'privacy:metadata:schedule:format',
            'subject' => 'privacy:metadata:schedule:subject',
            'message' => 'privacy:metadata:schedule:message',
            'userviewas' => 'privacy:metadata:schedule:userviewas',
            'timescheduled' => 'privacy:metadata:schedule:timescheduled',
            'recurrence' => 'privacy:metadata:schedule:recurrence',
            'reportempty' => 'privacy:metadata:schedule:reportempty',
            'usercreated' => 'privacy:metadata:schedule:usercreated',
            'usermodified' => 'privacy:metadata:schedule:usermodified',
            'timecreated' => 'privacy:metadata:schedule:timecreated',
            'timemodified' => 'privacy:metadata:schedule:timemodified',
        ], 'privacy:metadata:schedule');

        $collection->add_user_preference('core_reportbuilder', 'privacy:metadata:preference:reportfilter');

        return $collection;
    }

    /**
     * Export all user preferences for the component
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid): void {
        $preferencestring = get_string('privacy:metadata:preference:reportfilter', 'core_reportbuilder');

        $filters = user_filter_manager::get_all_for_user($userid);
        foreach ($filters as $key => $filter) {
            writer::export_user_preference('core_reportbuilder',
                $key,
                json_encode($filter, JSON_PRETTY_PRINT),
                $preferencestring
            );
        }
    }

    /**
     * Get export sub context for a report
     *
     * @param report $report
     * @return array
     */
    public static function get_export_subcontext(report $report): array {
        $reportnode = implode('-', [
            $report->get('id'),
            clean_filename($report->get_formatted_name()),
        ]);

        return [get_string('reportbuilder', 'core_reportbuilder'), $reportnode];
    }

    /**
     * Get the list of contexts that contain user information for the specified user
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Locate all contexts for reports the user has created, or reports they have created audience/schedules for.
        $sql = '
            SELECT r.contextid
              FROM {' . report::TABLE . '} r
             WHERE r.type = 0
               AND (r.usercreated = ?
                 OR r.usermodified = ?
                 OR r.id IN (
                    SELECT a.reportid
                      FROM {' . audience::TABLE . '} a
                     WHERE a.usercreated = ? OR a.usermodified = ?
                     UNION
                    SELECT s.reportid
                      FROM {' . schedule::TABLE . '} s
                     WHERE s.usercreated = ? OR s.usermodified = ?
                    )
                   )';

        return $contextlist->add_from_sql($sql, array_fill(0, 6, $userid));
    }

    /**
     * Get users in context
     *
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist): void {
        $select = 'r.type = :type AND r.contextid = :contextid';
        $params = ['type' => 0, 'contextid' => $userlist->get_context()->id];

        // Users who have created reports.
        $sql = 'SELECT r.usercreated, r.usermodified
                 FROM {' . report::TABLE . '} r
                WHERE ' . $select;
        $userlist->add_from_sql('usercreated', $sql, $params);
        $userlist->add_from_sql('usermodified', $sql, $params);

        // Users who have created audiences.
        $sql = 'SELECT a.usercreated, a.usermodified
                  FROM {' . audience::TABLE . '} a
                  JOIN {' . report::TABLE . '} r ON r.id = a.reportid
                WHERE ' . $select;
        $userlist->add_from_sql('usercreated', $sql, $params);
        $userlist->add_from_sql('usermodified', $sql, $params);

        // Users who have created schedules.
        $sql = 'SELECT s.usercreated, s.usermodified
                  FROM {' . schedule::TABLE . '} s
                  JOIN {' . report::TABLE . '} r ON r.id = s.reportid
                 WHERE ' . $select;
        $userlist->add_from_sql('usercreated', $sql, $params);
        $userlist->add_from_sql('usermodified', $sql, $params);
    }

    /**
     * Export all user data for the specified user in the specified contexts
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        // We need to get all reports that the user has created, or reports they have created audience/schedules for.
        $select = 'type = 0 AND (usercreated = ? OR usermodified = ? OR id IN (
            SELECT a.reportid
              FROM {' . audience::TABLE . '} a
             WHERE a.usercreated = ? OR a.usermodified = ?
             UNION
            SELECT s.reportid
              FROM {' . schedule::TABLE . '} s
             WHERE s.usercreated = ? OR s.usermodified = ?
        ))';
        $params = array_fill(0, 6, $user->id);

        foreach (report::get_records_select($select, $params) as $report) {
            $subcontext = static::get_export_subcontext($report);

            self::export_report($subcontext, $report);

            $select = 'reportid = ? AND (usercreated = ? OR usermodified = ?)';
            $params = [$report->get('id'), $user->id, $user->id];

            // Audiences.
            if ($audiences = audience::get_records_select($select, $params)) {
                static::export_audiences($report->get_context(), $subcontext, $audiences);
            }

            // Schedules.
            if ($schedules = schedule::get_records_select($select, $params)) {
                static::export_schedules($report->get_context(), $subcontext, $schedules);
            }
        }
    }

    /**
     * Delete data for all users in context
     *
     * @param context $context
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        // We don't perform any deletion of user data.
    }

    /**
     * Delete data for user
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        // We don't perform any deletion of user data.
    }

    /**
     * Delete data for users
     *
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        // We don't perform any deletion of user data.
    }

    /**
     * Export given report in context
     *
     * @param array $subcontext
     * @param report $report
     */
    protected static function export_report(array $subcontext, report $report): void {
        // Show the source name, if it exists.
        $source = $report->get('source');
        if (manager::report_source_exists($source)) {
            $source = call_user_func([$source, 'get_name']);
        }

        $reportdata = (object) [
            'name' => $report->get_formatted_name(),
            'source' => $source,
            'conditiondata' => $report->get('conditiondata'),
            'settingsdata' => $report->get('settingsdata'),
            'uniquerows' => transform::yesno($report->get('uniquerows')),
            'usercreated' => transform::user($report->get('usercreated')),
            'usermodified' => transform::user($report->get('usermodified')),
            'timecreated' => transform::datetime($report->get('timecreated')),
            'timemodified' => transform::datetime($report->get('timemodified')),
        ];

        writer::with_context($report->get_context())->export_data($subcontext, $reportdata);
    }

    /**
     * Export given audiences in context
     *
     * @param context $context
     * @param array $subcontext
     * @param audience[] $audiences
     */
    protected static function export_audiences(context $context, array $subcontext, array $audiences): void {
        $audiencedata = array_map(static function(audience $audience) use ($context): stdClass {
            // Show the audience name, if it exists.
            $classname = $audience->get('classname');
            if (class_exists($classname)) {
                $classname = $classname::instance()->get_name();
            }

            return (object) [
                'classname' => $classname,
                'heading' => $audience->get_formatted_heading($context),
                'configdata' => $audience->get('configdata'),
                'usercreated' => transform::user($audience->get('usercreated')),
                'usermodified' => transform::user($audience->get('usermodified')),
                'timecreated' => transform::datetime($audience->get('timecreated')),
                'timemodified' => transform::datetime($audience->get('timemodified')),
            ];
        }, $audiences);

        writer::with_context($context)->export_related_data($subcontext, 'audiences', (object) ['data' => $audiencedata]);
    }

    /**
     * Export given schedules in context
     *
     * @param context $context
     * @param array $subcontext
     * @param schedule[] $schedules
     */
    protected static function export_schedules(context $context, array $subcontext, array $schedules): void {
        $formatoptions = schedule_helper::get_format_options();
        $recurrenceoptions = schedule_helper::get_recurrence_options();
        $viewasoptions = schedule_helper::get_viewas_options();
        $reportemptyoptions = schedule_helper::get_report_empty_options();

        $scheduledata = array_map(static function(schedule $schedule) use (
                $context, $formatoptions, $recurrenceoptions, $viewasoptions, $reportemptyoptions): stdClass {

            // The "User view as" property will be either creator, recipient or a specific userid.
            $userviewas = $schedule->get('userviewas');

            return (object) [
                'name' => $schedule->get_formatted_name($context),
                'enabled' => transform::yesno($schedule->get('enabled')),
                'format' => $formatoptions[$schedule->get('format')],
                'timescheduled' => transform::datetime($schedule->get('timescheduled')),
                'recurrence' => $recurrenceoptions[$schedule->get('recurrence')],
                'userviewas' => $viewasoptions[$userviewas] ?? transform::user($userviewas),
                'audiences' => $schedule->get('audiences'),
                'subject' => $schedule->get('subject'),
                'message' => format_text($schedule->get('message'), $schedule->get('messageformat'), ['context' => $context]),
                'reportempty' => $reportemptyoptions[$schedule->get('reportempty')],
                'usercreated' => transform::user($schedule->get('usercreated')),
                'usermodified' => transform::user($schedule->get('usermodified')),
                'timecreated' => transform::datetime($schedule->get('timecreated')),
                'timemodified' => transform::datetime($schedule->get('timemodified')),
            ];
        }, $schedules);

        writer::with_context($context)->export_related_data($subcontext, 'schedules', (object) ['data' => $scheduledata]);
    }
}
