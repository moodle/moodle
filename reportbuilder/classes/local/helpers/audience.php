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
use core_reportbuilder\local\models\audience as audience_model;

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
        $prefix = database::generate_param_name() . '_';
        [$select, $params] = $DB->get_in_or_equal($allowedreports, SQL_PARAMS_NAMED, $prefix);
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
     * - A user with 'editall' capability will have access to all reports
     * - A user with 'edit' capability will have access to:
     *      - Those reports this user has created
     *      - Those reports this user is in audience of
     * - A user with 'view' capability will have access to:
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

        // If user can't view all reports, limit the returned list to those reports they can see.
        if (!has_capability('moodle/reportbuilder:editall', $context, $userid)) {
            $reports = self::user_reports_list($userid);

            [$paramprefix, $paramuserid] = database::generate_param_names(2);
            [$reportselect, $params] = $DB->get_in_or_equal($reports, SQL_PARAMS_NAMED, "{$paramprefix}_", true, null);

            $where = "{$reporttablealias}.id {$reportselect}";

            // User can also see any reports that they can edit.
            if (has_capability('moodle/reportbuilder:edit', $context, $userid)) {
                $where = "({$reporttablealias}.usercreated = :{$paramuserid} OR {$where})";
                $params[$paramuserid] = $userid ?? $USER->id;
            }

            return [$where, $params];
        }

        return ['1=1', []];
    }

    /**
     * Return appropriate list of where clauses and params for given audiences
     *
     * @param audience_model[] $audiences
     * @param string $usertablealias
     * @return array[] [$wheres, $params]
     */
    public static function user_audience_sql(array $audiences, string $usertablealias = 'u'): array {
        $wheres = $params = [];

        foreach ($audiences as $audience) {
            if ($instance = base::instance(0, $audience->to_record())) {
                $instancetablealias = database::generate_alias();
                [$instancejoin, $instancewhere, $instanceparams] = $instance->get_sql($instancetablealias);

                $wheres[] = "{$usertablealias}.id IN (
                    SELECT {$instancetablealias}.id
                      FROM {user} {$instancetablealias}
                           {$instancejoin}
                     WHERE {$instancewhere}
                     )";
                $params += $instanceparams;
            }
        }

        return [$wheres, $params];
    }

    /**
     * Returns the list of audiences types in the system.
     *
     * @return array
     */
    private static function get_audience_types(): array {
        $sources = [];

        $audiences = core_component::get_component_classes_in_namespace(null, 'reportbuilder\\audience');
        foreach ($audiences as $class => $path) {
            $audienceclass = $class::instance();
            if (is_subclass_of($class, base::class) && $audienceclass->user_can_add()) {
                $componentname = $audienceclass->get_component_displayname();
                $sources[$componentname][$class] = $audienceclass->get_name();
            }
        }

        return $sources;
    }

    /**
     * Get all the audiences types the current user can add to, organised by categories.
     *
     * @return array
     *
     * @deprecated since Moodle 4.1 - please do not use this function any more, {@see custom_report_audience_cards_exporter}
     */
    public static function get_all_audiences_menu_types(): array {
        debugging('The function ' . __FUNCTION__ . '() is deprecated, please do not use it any more. ' .
            'See \'custom_report_audience_cards_exporter\' class for replacement', DEBUG_DEVELOPER);

        $menucardsarray = [];
        $notavailablestr = get_string('notavailable', 'moodle');

        $audiencetypes = self::get_audience_types();
        $audiencetypeindex = 0;
        foreach ($audiencetypes as $categoryname => $audience) {
            $menucards = [
                'name' => $categoryname,
                'key' => 'index' . ++$audiencetypeindex,
            ];

            foreach ($audience as $classname => $name) {
                $class = $classname::instance();
                $title = $class->is_available() ? get_string('addaudience', 'core_reportbuilder', $class->get_name()) :
                    $notavailablestr;
                $menucard['title'] = $title;
                $menucard['name'] = $class->get_name();
                $menucard['disabled'] = !$class->is_available();
                $menucard['identifier'] = get_class($class);
                $menucard['action'] = 'add-audience';
                $menucards['items'][] = $menucard;
            }

            // Order audience types on each category alphabetically.
            core_collator::asort_array_of_arrays_by_key($menucards['items'], 'name');
            $menucards['items'] = array_values($menucards['items']);

            $menucardsarray[] = $menucards;
        }

        return $menucardsarray;
    }
}
