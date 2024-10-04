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

namespace core_reportbuilder\local\helpers;

use cache;
use context;
use context_system;
use core_collator;
use core_component;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\models\{audience as audience_model, schedule};
use invalid_parameter_exception;

/**
 * Class containing report audience helper methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience {

    /**
     * Return audience instances for a given report. Note that any records pointing to invalid audience types will be excluded
     *
     * @param int $reportid
     * @return base[]
     */
    public static function get_base_records(int $reportid): array {
        $records = audience_model::get_records(['reportid' => $reportid], 'id');

        $instances = array_map(static function(audience_model $audience): ?base {
            return base::instance(0, $audience->to_record());
        }, $records);

        // Filter and remove null elements (invalid audience types).
        return array_filter($instances);
    }

    /**
     * Returns list of report IDs that the specified user can access, based on audience configuration. This can be expensive if the
     * site has lots of reports, with lots of audiences, so we cache the result for the duration of the users session
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return int[]
     */
    public static function get_allowed_reports(?int $userid = null): array {
        global $USER, $DB;

        $userid = $userid ?: (int) $USER->id;

        // Prepare cache, if we previously stored the users allowed reports then return that.
        $cache = cache::make('core', 'reportbuilder_allowed_reports');
        $cachedreports = $cache->get($userid);
        if ($cachedreports !== false) {
            return $cachedreports;
        }

        $allowedreports = [];
        $reportaudiences = [];

        // Retrieve all audiences and group them by report for convenience.
        $audiences = audience_model::get_records();
        foreach ($audiences as $audience) {
            $reportaudiences[$audience->get('reportid')][] = $audience;
        }

        foreach ($reportaudiences as $reportid => $audiences) {

            // Generate audience SQL based on those for the current report.
            [$wheres, $params] = self::user_audience_sql($audiences);
            if (count($wheres) === 0) {
                continue;
            }

            $paramuserid = database::generate_param_name();
            $params[$paramuserid] = $userid;

            $sql = "SELECT DISTINCT(u.id)
                      FROM {user} u
                     WHERE (" . implode(' OR ', $wheres) . ")
                       AND u.deleted = 0
                       AND u.id = :{$paramuserid}";

            // If we have a matching record, user can view the report.
            if ($DB->record_exists_sql($sql, $params)) {
                $allowedreports[] = $reportid;
            }
        }

        // Store users allowed reports in cache.
        $cache->set($userid, $allowedreports);

        return $allowedreports;
    }

    /**
     * Purge the audience cache of allowed reports
     */
    public static function purge_caches(): void {
        cache::make('core', 'reportbuilder_allowed_reports')->purge();
    }

    /**
     * Generate SQL select clause and params for selecting reports specified user can access, based on audience configuration
     *
     * @param string $reporttablealias
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return array
     */
    public static function user_reports_list_sql(string $reporttablealias, ?int $userid = null): array {
        global $DB;

        $allowedreports = self::get_allowed_reports($userid);

        if (empty($allowedreports)) {
            return ['1=0', []];
        }

        // Get all sql audiences.
        [$select, $params] = $DB->get_in_or_equal($allowedreports, SQL_PARAMS_NAMED, database::generate_param_name('_'));
        $sql = "{$reporttablealias}.id {$select}";

        return [$sql, $params];
    }

    /**
     * Return list of report ID's specified user can access, based on audience configuration
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return int[]
     */
    public static function user_reports_list(?int $userid = null): array {
        global $DB;

        [$select, $params] = self::user_reports_list_sql('rb', $userid);
        $sql = "SELECT rb.id
                  FROM {reportbuilder_report} rb
                 WHERE {$select}";

        return $DB->get_fieldset_sql($sql, $params);
    }

    /**
     * Returns SQL to limit the list of reports to those that the given user has access to
     *
     * - A user with 'viewall/editall' capability will have access to all reports
     * - A user with 'edit' capability will have access to:
     *      - Those reports this user has created
     *      - Those reports this user is in audience of
     * - Otherwise:
     *      - Those reports this user is in audience of
     *
     * @param string $reporttablealias
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @return array
     */
    public static function user_reports_list_access_sql(
        string $reporttablealias,
        ?int $userid = null,
        ?context $context = null
    ): array {
        global $DB, $USER;

        if ($context === null) {
            $context = context_system::instance();
        }

        if (has_any_capability(['moodle/reportbuilder:editall', 'moodle/reportbuilder:viewall'], $context, $userid)) {
            return ['1=1', []];
        }

        // Limit the returned list to those reports the user can see, by selecting based on report audience.
        [$reportselect, $params] = $DB->get_in_or_equal(
            self::user_reports_list($userid),
            SQL_PARAMS_NAMED,
            database::generate_param_name('_'),
            true,
            null,
        );

        $where = "{$reporttablealias}.id {$reportselect}";

        // User can also see any reports that they can edit.
        if (has_capability('moodle/reportbuilder:edit', $context, $userid)) {
            $paramuserid = database::generate_param_name();
            $where = "({$reporttablealias}.usercreated = :{$paramuserid} OR {$where})";
            $params[$paramuserid] = $userid ?? $USER->id;
        }

        return [$where, $params];
    }

    /**
     * Return appropriate select clause and params for given audience
     *
     * @param audience_model $audience
     * @param string $userfieldsql
     * @return array [$select, $params]
     */
    public static function user_audience_single_sql(audience_model $audience, string $userfieldsql): array {
        $select = '';
        $params = [];

        if ($instance = base::instance(0, $audience->to_record())) {
            $innerusertablealias = database::generate_alias();
            [$join, $where, $params] = $instance->get_sql($innerusertablealias);

            $select = "{$userfieldsql} IN (
                SELECT {$innerusertablealias}.id
                  FROM {user} {$innerusertablealias}
                       {$join}
                 WHERE {$where}
            )";
        }

        return [$select, $params];
    }

    /**
     * Return appropriate list of select clauses and params for given audiences
     *
     * @param audience_model[] $audiences
     * @param string $usertablealias
     * @return array[] [$selects, $params]
     */
    public static function user_audience_sql(array $audiences, string $usertablealias = 'u'): array {
        $selects = $params = [];

        foreach ($audiences as $audience) {
            [$instanceselect, $instanceparams] = self::user_audience_single_sql($audience, "{$usertablealias}.id");
            if ($instanceselect !== '') {
                $selects[] = $instanceselect;
                $params += $instanceparams;
            }
        }

        return [$selects, $params];
    }

    /**
     * Return a list of audiences that are used by any schedule of the given report
     *
     * @param int $reportid
     * @return int[] Array of audience IDs
     */
    public static function get_audiences_for_report_schedules(int $reportid): array {
        global $DB;

        $audiences = $DB->get_fieldset_select(schedule::TABLE, 'audiences', 'reportid = ?', [$reportid]);

        // Reduce JSON encoded audience data of each schedule to an array of audience IDs.
        $audienceids = array_reduce($audiences, static function(array $carry, string $audience): array {
            return array_merge($carry, (array) json_decode($audience));
        }, []);

        return array_unique($audienceids, SORT_NUMERIC);
    }

    /**
     * Delete given audience from report
     *
     * @param int $reportid
     * @param int $audienceid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report_audience(int $reportid, int $audienceid): bool {
        $audience = audience_model::get_record(['id' => $audienceid, 'reportid' => $reportid]);
        if ($audience === false) {
            throw new invalid_parameter_exception('Invalid audience');
        }

        $instance = base::instance(0, $audience->to_record());
        if ($instance && $instance->user_can_edit()) {
            $persistent = $instance->get_persistent();
            $persistent->delete();
            return true;
        }

        return false;
    }

    /**
     * @deprecated since Moodle 4.1 - please do not use this function any more, {@see custom_report_audience_cards_exporter}
     */
    #[\core\attribute\deprecated('custom_report_audience_cards_exporter', since: '4.1', final: true)]
    public static function get_all_audiences_menu_types() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }
}
