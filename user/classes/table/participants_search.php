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
 * Class used to fetch participants based on a filterset.
 *
 * @package    core_user
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\table;

use context;
use context_helper;
use core_table\local\filter\filterset;
use core_user;
use moodle_recordset;
use stdClass;
use user_picture;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class used to fetch participants based on a filterset.
 *
 * @package    core_user
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_search {

    /**
     * @var filterset $filterset The filterset describing which participants to include in the search.
     */
    protected $filterset;

    /**
     * @var stdClass $course The course being searched.
     */
    protected $course;

    /**
     * @var context_course $context The course context being searched.
     */
    protected $context;

    /**
     * @var string[] $userfields Names of any extra user fields to be shown when listing users.
     */
    protected $userfields;

    /**
     * Class constructor.
     *
     * @param stdClass $course The course being searched.
     * @param context $context The context of the search.
     * @param filterset $filterset The filterset used to filter the participants in a course.
     */
    public function __construct(stdClass $course, context $context, filterset $filterset) {
        $this->course = $course;
        $this->context = $context;
        $this->filterset = $filterset;

        $this->userfields = get_extra_user_fields($this->context);
    }

    /**
     * Fetch participants matching the filterset.
     *
     * @param string $additionalwhere Any additional SQL to add to where.
     * @param array $additionalparams The additional params used by $additionalwhere.
     * @param string $sort Optional SQL sort.
     * @param int $limitfrom Return a subset of records, starting at this point (optional).
     * @param int $limitnum Return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset
     */
    public function get_participants(string $additionalwhere = '', array $additionalparams = [], string $sort = '',
            int $limitfrom = 0, int $limitnum = 0): moodle_recordset {
        global $DB;

        [
            'subqueryalias' => $subqueryalias,
            'outerselect' => $outerselect,
            'innerselect' => $innerselect,
            'outerjoins' => $outerjoins,
            'innerjoins' => $innerjoins,
            'outerwhere' => $outerwhere,
            'innerwhere' => $innerwhere,
            'params' => $params,
        ] = $this->get_participants_sql($additionalwhere, $additionalparams);

        $sql = "{$outerselect}
                          FROM ({$innerselect}
                                          FROM {$innerjoins}
                                 {$innerwhere}
                               ) {$subqueryalias}
                 {$outerjoins}
                 {$outerwhere}
                       {$sort}";

        return $DB->get_recordset_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Returns the total number of participants for a given course.
     *
     * @param string $additionalwhere Any additional SQL to add to where.
     * @param array $additionalparams The additional params used by $additionalwhere.
     * @return int
     */
    public function get_total_participants_count(string $additionalwhere = '', array $additionalparams = []): int {
        global $DB;

        [
            'subqueryalias' => $subqueryalias,
            'innerselect' => $innerselect,
            'outerjoins' => $outerjoins,
            'innerjoins' => $innerjoins,
            'outerwhere' => $outerwhere,
            'innerwhere' => $innerwhere,
            'params' => $params,
        ] = $this->get_participants_sql($additionalwhere, $additionalparams);

        $sql = "SELECT COUNT(u.id)
                  FROM ({$innerselect}
                                  FROM {$innerjoins}
                         {$innerwhere}
                       ) {$subqueryalias}
         {$outerjoins}
         {$outerwhere}";

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Generate the SQL used to fetch filtered data for the participants table.
     *
     * @param string $additionalwhere Any additional SQL to add to where
     * @param array $additionalparams The additional params
     * @return array
     */
    protected function get_participants_sql(string $additionalwhere, array $additionalparams): array {
        $isfrontpage = ($this->course->id == SITEID);
        $accesssince = 0;
        // Whether to match on users who HAVE accessed since the given time (ie false is 'inactive for more than x').
        $matchaccesssince = false;

        // The alias for the subquery that fetches all distinct course users.
        $usersubqueryalias = 'targetusers';
        // The alias for {user} within the distinct user subquery.
        $inneruseralias = 'udistinct';
        // Inner query that selects distinct users in a course who are not deleted.
        // Note: This ensures the outer (filtering) query joins on distinct users, avoiding the need for GROUP BY.
        $innerselect = "SELECT DISTINCT {$inneruseralias}.id";
        $innerjoins = ["{user} {$inneruseralias}"];
        $innerwhere = "WHERE {$inneruseralias}.deleted = 0";

        $outerjoins = ["JOIN {user} u ON u.id = {$usersubqueryalias}.id"];
        $wheres = [];

        if ($this->filterset->has_filter('accesssince')) {
            $accesssince = $this->filterset->get_filter('accesssince')->current();

            // Last access filtering only supports matching or not matching, not any/all/none.
            $jointypenone = $this->filterset->get_filter('accesssince')::JOINTYPE_NONE;
            if ($this->filterset->get_filter('accesssince')->get_join_type() === $jointypenone) {
                $matchaccesssince = true;
            }
        }

        [
            // SQL that forms part of the filter.
            'sql' => $esql,
            // SQL for enrolment filtering that must always be applied (eg due to capability restrictions).
            'forcedsql' => $esqlforced,
            'params' => $params,
        ] = $this->get_enrolled_sql();

        $userfieldssql = user_picture::fields('u', $this->userfields);

        // Include any compulsory enrolment SQL (eg capability related filtering that must be applied).
        if (!empty($esqlforced)) {
            $outerjoins[] = "JOIN ({$esqlforced}) fef ON fef.id = u.id";
        }

        // Include any enrolment related filtering.
        if (!empty($esql)) {
            $outerjoins[] = "LEFT JOIN ({$esql}) ef ON ef.id = u.id";
            $wheres[] = 'ef.id IS NOT NULL';
        }

        if ($isfrontpage) {
            $outerselect = "SELECT {$userfieldssql}, u.lastaccess";
            if ($accesssince) {
                $wheres[] = user_get_user_lastaccess_sql($accesssince, 'u', $matchaccesssince);
            }
        } else {
            $outerselect = "SELECT {$userfieldssql}, COALESCE(ul.timeaccess, 0) AS lastaccess";
            // Not everybody has accessed the course yet.
            $outerjoins[] = 'LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = :courseid2)';
            $params['courseid2'] = $this->course->id;
            if ($accesssince) {
                $wheres[] = user_get_course_lastaccess_sql($accesssince, 'ul', $matchaccesssince);
            }

            // Make sure we only ever fetch users in the course (regardless of enrolment filters).
            $innerjoins[] = "JOIN {user_enrolments} ue ON ue.userid = {$inneruseralias}.id";
            $innerjoins[] = 'JOIN {enrol} e ON e.id = ue.enrolid
                                      AND e.courseid = :courseid1';
            $params['courseid1'] = $this->course->id;
        }

        // Performance hacks - we preload user contexts together with accounts.
        $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ccjoin = 'LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)';
        $params['contextlevel'] = CONTEXT_USER;
        $outerselect .= $ccselect;
        $outerjoins[] = $ccjoin;

        // Apply any role filtering.
        if ($this->filterset->has_filter('roles')) {
            [
                'where' => $roleswhere,
                'params' => $rolesparams,
            ] = $this->get_roles_sql();

            if (!empty($roleswhere)) {
                $wheres[] = "({$roleswhere})";
            }

            if (!empty($rolesparams)) {
                $params = array_merge($params, $rolesparams);
            }
        }

        // Apply any keyword text searches.
        if ($this->filterset->has_filter('keywords')) {
            [
                'where' => $keywordswhere,
                'params' => $keywordsparams,
            ] = $this->get_keywords_search_sql();

            if (!empty($keywordswhere)) {
                $wheres[] = $keywordswhere;
            }

            if (!empty($keywordsparams)) {
                $params = array_merge($params, $keywordsparams);
            }
        }

        // Add any supplied additional forced WHERE clauses.
        if (!empty($additionalwhere)) {
            $innerwhere .= " AND ({$additionalwhere})";
            $params = array_merge($params, $additionalparams);
        }

        // Prepare final values.
        $outerjoinsstring = implode("\n", $outerjoins);
        $innerjoinsstring = implode("\n", $innerjoins);
        if ($wheres) {
            switch ($this->filterset->get_join_type()) {
                case $this->filterset::JOINTYPE_ALL:
                    $wherenot = '';
                    $wheresjoin = ' AND ';
                    break;
                case $this->filterset::JOINTYPE_NONE:
                    $wherenot = ' NOT ';
                    $wheresjoin = ' AND NOT ';
                    break;
                default:
                    // Default to 'Any' jointype.
                    $wherenot = '';
                    $wheresjoin = ' OR ';
                    break;
            }

            $outerwhere = 'WHERE ' . $wherenot . implode($wheresjoin, $wheres);
        } else {
            $outerwhere = '';
        }

        return [
            'subqueryalias' => $usersubqueryalias,
            'outerselect' => $outerselect,
            'innerselect' => $innerselect,
            'outerjoins' => $outerjoinsstring,
            'innerjoins' => $innerjoinsstring,
            'outerwhere' => $outerwhere,
            'innerwhere' => $innerwhere,
            'params' => $params,
        ];
    }

    /**
     * Prepare SQL and associated parameters for users enrolled in the course.
     *
     * @return array SQL query data in the format ['sql' => '', 'forcedsql' => '', 'params' => []].
     */
    protected function get_enrolled_sql(): array {
        global $USER;

        $isfrontpage = ($this->context->instanceid == SITEID);
        $prefix = 'eu_';
        $filteruid = "{$prefix}u.id";
        $sql = '';
        $joins = [];
        $wheres = [];
        $params = [];
        // It is possible some statements must always be included (in addition to any filtering).
        $forcedprefix = "f{$prefix}";
        $forceduid = "{$forcedprefix}u.id";
        $forcedsql = '';
        $forcedjoins = [];
        $forcedwhere = "{$forcedprefix}u.deleted = 0";

        if (!$isfrontpage) {
            // Prepare any enrolment method filtering.
            [
                'joins' => $methodjoins,
                'where' => $wheres[],
                'params' => $methodparams,
            ] = $this->get_enrol_method_sql($filteruid);

            // Prepare any status filtering.
            [
                'joins' => $statusjoins,
                'where' => $statuswhere,
                'params' => $statusparams,
                'forcestatus' => $forcestatus,
            ] = $this->get_status_sql($filteruid, $forceduid, $forcedprefix);

            if ($forcestatus) {
                // Force filtering by active participants if user does not have capability to view suspended.
                $forcedjoins = array_merge($forcedjoins, $statusjoins);
                $statusjoins = [];
                $forcedwhere .= " AND ({$statuswhere})";
            } else {
                $wheres[] = $statuswhere;
            }

            $joins = array_merge($joins, $methodjoins, $statusjoins);
            $params = array_merge($params, $methodparams, $statusparams);
        }

        $groupids = [];

        if ($this->filterset->has_filter('groups')) {
            $groupids = $this->filterset->get_filter('groups')->get_filter_values();
        }

        // Force additional groups filtering if required due to lack of capabilities.
        // Note: This means results will always be limited to allowed groups, even if the user applies their own groups filtering.
        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $this->context);
        $forcegroups = ($this->course->groupmode == SEPARATEGROUPS && !$canaccessallgroups);

        if ($forcegroups) {
            $allowedgroupids = array_keys(groups_get_all_groups($this->course->id, $USER->id));

            // Users not in any group in a course with separate groups mode should not be able to access the participants filter.
            if (empty($allowedgroupids)) {
                // The UI does not support this, so it should not be reachable unless someone is trying to bypass the restriction.
                throw new \coding_exception('User must be part of a group to filter by participants.');
            }

            $forceduid = "{$forcedprefix}u.id";
            $forcedjointype = $this->get_groups_jointype(\core_table\local\filter\filter::JOINTYPE_ANY);
            $forcedgroupjoin = groups_get_members_join($allowedgroupids, $forceduid, $this->context, $forcedjointype);

            $forcedjoins[] = $forcedgroupjoin->joins;
            $forcedwhere .= "AND ({$forcedgroupjoin->wheres})";

            $params = array_merge($params, $forcedgroupjoin->params);

            // Remove any filtered groups the user does not have access to.
            $groupids = array_intersect($allowedgroupids, $groupids);
        }

        // Prepare any user defined groups filtering.
        if ($groupids) {
            $groupjoin = groups_get_members_join($groupids, $filteruid, $this->context, $this->get_groups_jointype());

            $joins[] = $groupjoin->joins;
            $params = array_merge($params, $groupjoin->params);
            if (!empty($groupjoin->wheres)) {
                $wheres[] = $groupjoin->wheres;
            }
        }

        // Combine the relevant filters and prepare the query.
        $joins = array_filter($joins);
        if (!empty($joins)) {
            $joinsql = implode("\n", $joins);

            $sql = "SELECT DISTINCT {$prefix}u.id
                               FROM {user} {$prefix}u
                                    {$joinsql}
                              WHERE {$prefix}u.deleted = 0";
        }

        $wheres = array_filter($wheres);
        if (!empty($wheres)) {
            if ($this->filterset->get_join_type() === $this->filterset::JOINTYPE_ALL) {
                $wheresql = '(' . implode(') AND (', $wheres) . ')';
            } else {
                $wheresql = '(' . implode(') OR (', $wheres) . ')';
            }

            $sql .= " AND ({$wheresql})";
        }

        // Prepare any SQL that must be applied.
        if (!empty($forcedjoins)) {
            $forcedjoinsql = implode("\n", $forcedjoins);
            $forcedsql = "SELECT DISTINCT {$forcedprefix}u.id
                                     FROM {user} {$forcedprefix}u
                                          {$forcedjoinsql}
                                    WHERE {$forcedwhere}";
        }

        return [
            'sql' => $sql,
            'forcedsql' => $forcedsql,
            'params' => $params,
        ];
    }

    /**
     * Prepare the enrolment methods filter SQL content.
     *
     * @param string $useridcolumn User ID column used in the calling query, e.g. u.id
     * @return array SQL query data in the format ['joins' => [], 'where' => '', 'params' => []].
     */
    protected function get_enrol_method_sql($useridcolumn): array {
        global $DB;

        $prefix = 'ejm_';
        $joins  = [];
        $where = '';
        $params = [];
        $enrolids = [];

        if ($this->filterset->has_filter('enrolments')) {
            $enrolids = $this->filterset->get_filter('enrolments')->get_filter_values();
        }

        if (!empty($enrolids)) {
            $jointype = $this->filterset->get_filter('enrolments')->get_join_type();

            // Handle 'All' join type.
            if ($jointype === $this->filterset->get_filter('enrolments')::JOINTYPE_ALL ||
                    $jointype === $this->filterset->get_filter('enrolments')::JOINTYPE_NONE) {
                $allwheres = [];

                foreach ($enrolids as $i => $enrolid) {
                    $thisprefix = "{$prefix}{$i}";
                    list($enrolidsql, $enrolidparam) = $DB->get_in_or_equal($enrolid, SQL_PARAMS_NAMED, $thisprefix);

                    $joins[] = "LEFT JOIN {enrol} {$thisprefix}e
                                       ON ({$thisprefix}e.id {$enrolidsql}
                                      AND {$thisprefix}e.courseid = :{$thisprefix}courseid)";
                    $joins[] = "LEFT JOIN {user_enrolments} {$thisprefix}ue
                                       ON {$thisprefix}ue.userid = {$useridcolumn}
                                      AND {$thisprefix}ue.enrolid = {$thisprefix}e.id";

                    if ($jointype === $this->filterset->get_filter('enrolments')::JOINTYPE_ALL) {
                        $allwheres[] = "{$thisprefix}ue.id IS NOT NULL";
                    } else {
                        // Ensure participants do not match any of the filtered methods when joining by 'None'.
                        $allwheres[] = "{$thisprefix}ue.id IS NULL";
                    }

                    $params["{$thisprefix}courseid"] = $this->course->id;
                    $params = array_merge($params, $enrolidparam);
                }

                if (!empty($allwheres)) {
                    $where = implode(' AND ', $allwheres);
                }
            } else {
                // Handle the 'Any'join type.

                list($enrolidssql, $enrolidsparams) = $DB->get_in_or_equal($enrolids, SQL_PARAMS_NAMED, $prefix);

                $joins[] = "LEFT JOIN {enrol} {$prefix}e
                                   ON ({$prefix}e.id {$enrolidssql}
                                  AND {$prefix}e.courseid = :{$prefix}courseid)";
                $joins[] = "LEFT JOIN {user_enrolments} {$prefix}ue ON {$prefix}ue.userid = {$useridcolumn}
                                                              AND {$prefix}ue.enrolid = {$prefix}e.id";
                $where = "{$prefix}ue.id IS NOT NULL";

                $params["{$prefix}courseid"] = $this->course->id;
                $params = array_merge($params, $enrolidsparams);
            }
        }

        return [
            'joins' => $joins,
            'where' => $where,
            'params' => $params,
        ];
    }

    /**
     * Prepare the status filter SQL content.
     * Note: Users who cannot view suspended users will always have their results filtered to only show active participants.
     *
     * @param string $filteruidcolumn User ID column used in the calling query, e.g. eu_u.id
     * @param string $forceduidcolumn User ID column used in any forced query, e.g. feu_u.id
     * @param string $forcedprefix The prefix to use if forced filtering is required
     * @return array SQL query data in the format ['joins' => [], 'where' => '', 'params' => [], 'forcestatus' => true]
     */
    protected function get_status_sql($filteruidcolumn, $forceduidcolumn, $forcedprefix): array {
        $prefix = $forcedprefix;
        $useridcolumn = $forceduidcolumn;
        $joins  = [];
        $where = '';
        $params = [];
        $forcestatus = true;

        // By default we filter to show users with active status only.
        $statusids = [ENROL_USER_ACTIVE];
        $statusjointype = $this->filterset::JOINTYPE_DEFAULT;

        // Allow optional status filtering if the user has relevant capabilities.
        if (has_capability('moodle/course:enrolreview', $this->context) &&
                (has_capability('moodle/course:viewsuspendedusers', $this->context))) {
            $forcestatus = false;
            $prefix = 'ejs_';
            $useridcolumn = $filteruidcolumn;

            // Default to no filtering if capabilities allow for it.
            $statusids = [];

            if ($this->filterset->has_filter('status')) {
                $statusjointype = $this->filterset->get_filter('status')->get_join_type();
                $statusfiltervalues = $this->filterset->get_filter('status')->get_filter_values();

                // If values are set for the status filter, use them.
                if (!empty($statusfiltervalues)) {
                    $statusids = $statusfiltervalues;
                }
            }
        }

        if (!empty($statusids)) {
            $enroljoin = 'JOIN {enrol} %1$se ON %1$se.id = %1$sue.enrolid
                                                  AND %1$se.courseid = :%1$scourseid';

            $whereactive = '(%1$sue.status = :%2$sactive
                          AND %1$se.status = :%2$senabled
                      AND %1$sue.timestart < :%2$snow1
                       AND (%1$sue.timeend = 0
                         OR %1$sue.timeend > :%2$snow2))';

            $wheresuspended = '(%1$sue.status = :%2$ssuspended
                             OR %1$se.status != :%2$senabled
                         OR %1$sue.timestart >= :%2$snow1
                           OR (%1$sue.timeend > 0
                          AND %1$sue.timeend <= :%2$snow2))';

            // Round 'now' time to help DB caching.
            $now = round(time(), -2);

            switch ($statusjointype) {
                case $this->filterset::JOINTYPE_ALL:
                    $joinwheres = [];

                    foreach ($statusids as $i => $statusid) {
                        $joinprefix = "{$prefix}{$i}";
                        $joins[] = "JOIN {user_enrolments} {$joinprefix}ue ON {$joinprefix}ue.userid = {$useridcolumn}";

                        if ($statusid === ENROL_USER_ACTIVE) {
                            // Conditions to be met if user filtering by active.
                            $joinwheres[] = sprintf($whereactive, $joinprefix, $joinprefix);

                            $activeparams = [
                                "{$joinprefix}active" => ENROL_USER_ACTIVE,
                                "{$joinprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$joinprefix}now1"   => $now,
                                "{$joinprefix}now2"   => $now,
                                "{$joinprefix}courseid"   => $this->course->id,
                            ];

                            $params = array_merge($params, $activeparams);
                        } else {
                            // Conditions to be met if filtering by suspended (currently the only other status).
                            $joinwheres[] = sprintf($wheresuspended, $joinprefix, $joinprefix);

                            $suspendedparams = [
                                "{$joinprefix}suspended" => ENROL_USER_SUSPENDED,
                                "{$joinprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$joinprefix}now1"   => $now,
                                "{$joinprefix}now2"   => $now,
                                "{$joinprefix}courseid"   => $this->course->id,
                            ];

                            $params = array_merge($params, $suspendedparams);
                        }

                        $joins[] = sprintf($enroljoin, $joinprefix);
                    }

                    $where = implode(' AND ', $joinwheres);
                    break;

                case $this->filterset::JOINTYPE_NONE:
                    // Should always be enrolled, just not in any of the filtered statuses.
                    $joins[] = "JOIN {user_enrolments} {$prefix}ue ON {$prefix}ue.userid = {$useridcolumn}";
                    $joins[] = sprintf($enroljoin, $prefix);
                    $joinwheres = [];
                    $params["{$prefix}courseid"] = $this->course->id;

                    foreach ($statusids as $i => $statusid) {
                        $paramprefix = "{$prefix}{$i}";

                        if ($statusid === ENROL_USER_ACTIVE) {
                            // Conditions to be met if user filtering by active.
                            $joinwheres[] = sprintf("NOT {$whereactive}", $prefix, $paramprefix);

                            $activeparams = [
                                "{$paramprefix}active" => ENROL_USER_ACTIVE,
                                "{$paramprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$paramprefix}now1"   => $now,
                                "{$paramprefix}now2"   => $now,
                            ];

                            $params = array_merge($params, $activeparams);
                        } else {
                            // Conditions to be met if filtering by suspended (currently the only other status).
                            $joinwheres[] = sprintf("NOT {$wheresuspended}", $prefix, $paramprefix);

                            $suspendedparams = [
                                "{$paramprefix}suspended" => ENROL_USER_SUSPENDED,
                                "{$paramprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$paramprefix}now1"   => $now,
                                "{$paramprefix}now2"   => $now,
                            ];

                            $params = array_merge($params, $suspendedparams);
                        }
                    }

                    $where = '(' . implode(' AND ', $joinwheres) . ')';
                    break;

                default:
                    // Handle the 'Any' join type.

                    $joins[] = "JOIN {user_enrolments} {$prefix}ue ON {$prefix}ue.userid = {$useridcolumn}";
                    $joins[] = sprintf($enroljoin, $prefix);
                    $joinwheres = [];
                    $params["{$prefix}courseid"] = $this->course->id;

                    foreach ($statusids as $i => $statusid) {
                        $paramprefix = "{$prefix}{$i}";

                        if ($statusid === ENROL_USER_ACTIVE) {
                            // Conditions to be met if user filtering by active.
                            $joinwheres[] = sprintf($whereactive, $prefix, $paramprefix);

                            $activeparams = [
                                "{$paramprefix}active" => ENROL_USER_ACTIVE,
                                "{$paramprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$paramprefix}now1"   => $now,
                                "{$paramprefix}now2"   => $now,
                            ];

                            $params = array_merge($params, $activeparams);
                        } else {
                            // Conditions to be met if filtering by suspended (currently the only other status).
                            $joinwheres[] = sprintf($wheresuspended, $prefix, $paramprefix);

                            $suspendedparams = [
                                "{$paramprefix}suspended" => ENROL_USER_SUSPENDED,
                                "{$paramprefix}enabled" => ENROL_INSTANCE_ENABLED,
                                "{$paramprefix}now1"   => $now,
                                "{$paramprefix}now2"   => $now,
                            ];

                            $params = array_merge($params, $suspendedparams);
                        }
                    }

                    $where = '(' . implode(' OR ', $joinwheres) . ')';
                    break;
            }
        }

        return [
            'joins' => $joins,
            'where' => $where,
            'params' => $params,
            'forcestatus' => $forcestatus,
        ];
    }

    /**
     * Fetch the groups filter's grouplib jointype, based on its filterset jointype.
     * This mapping is to ensure compatibility between the two, should their values ever differ.
     *
     * @param int|null $forcedjointype If set, specifies the join type to fetch mapping for (used when applying forced filtering).
     *                            If null, then user defined filter join type is used.
     * @return int
     */
    protected function get_groups_jointype(?int $forcedjointype = null): int {

        // If applying forced groups filter and no manual groups filtering is applied, add an empty filter so we can map the join.
        if (!is_null($forcedjointype) && !$this->filterset->has_filter('groups')) {
            $this->filterset->add_filter(new \core_table\local\filter\integer_filter('groups'));
        }

        $groupsfilter = $this->filterset->get_filter('groups');

        if (is_null($forcedjointype)) {
            // Fetch join type mapping for a user supplied groups filtering.
            $filterjointype = $groupsfilter->get_join_type();
        } else {
            // Fetch join type mapping for forced groups filtering.
            $filterjointype = $forcedjointype;
        }

        switch ($filterjointype) {
            case $groupsfilter::JOINTYPE_NONE:
                $groupsjoin = GROUPS_JOIN_NONE;
                break;
            case $groupsfilter::JOINTYPE_ALL:
                $groupsjoin = GROUPS_JOIN_ALL;
                break;
            default:
                // Default to ANY jointype.
                $groupsjoin = GROUPS_JOIN_ANY;
                break;
        }

        return $groupsjoin;
    }

    /**
     * Prepare SQL where clause and associated parameters for any roles filtering being performed.
     *
     * @return array SQL query data in the format ['where' => '', 'params' => []].
     */
    protected function get_roles_sql(): array {
        global $DB;

        $where = '';
        $params = [];

        // Limit list to users with some role only.
        if ($this->filterset->has_filter('roles')) {
            $rolesfilter = $this->filterset->get_filter('roles');

            $roleids = $rolesfilter->get_filter_values();
            $jointype = $rolesfilter->get_join_type();

            // Determine how to match values in the query.
            $matchinsql = 'IN';
            switch ($jointype) {
                case $rolesfilter::JOINTYPE_ALL:
                    $wherejoin = ' AND ';
                    break;
                case $rolesfilter::JOINTYPE_NONE:
                    $wherejoin = ' AND NOT ';
                    $matchinsql = 'NOT IN';
                    break;
                default:
                    // Default to 'Any' jointype.
                    $wherejoin = ' OR ';
                    break;
            }

            // We want to query both the current context and parent contexts.
            $rolecontextids = $this->context->get_parent_context_ids(true);

            // Get users without any role, if needed.
            if (($withoutkey = array_search(-1, $roleids)) !== false) {
                list($relatedctxsql1, $norolectxparams) = $DB->get_in_or_equal($rolecontextids, SQL_PARAMS_NAMED, 'relatedctx');

                if ($jointype === $rolesfilter::JOINTYPE_NONE) {
                    $where .= "(u.id IN (SELECT userid FROM {role_assignments} WHERE contextid {$relatedctxsql1}))";
                } else {
                    $where .= "(u.id NOT IN (SELECT userid FROM {role_assignments} WHERE contextid {$relatedctxsql1}))";
                }

                $params = array_merge($params, $norolectxparams);

                if ($withoutkey !== false) {
                    unset($roleids[$withoutkey]);
                }

                // Join if any roles will be included.
                if (!empty($roleids)) {
                    // The NOT case is replaced with AND to prevent a double negative.
                    $where .= $jointype === $rolesfilter::JOINTYPE_NONE ? ' AND ' : $wherejoin;
                }
            }

            // Get users with specified roles, if needed.
            if (!empty($roleids)) {
                // All case - need one WHERE per filtered role.
                if ($rolesfilter::JOINTYPE_ALL === $jointype) {
                    $numroles = count($roleids);
                    $rolecount = 1;

                    foreach ($roleids as $roleid) {
                        list($relatedctxsql, $relctxparams) = $DB->get_in_or_equal($rolecontextids, SQL_PARAMS_NAMED, 'relatedctx');
                        list($roleidssql, $roleidparams) = $DB->get_in_or_equal($roleid, SQL_PARAMS_NAMED, 'roleids');

                        $where .= "(u.id IN (
                                     SELECT userid
                                       FROM {role_assignments}
                                      WHERE roleid {$roleidssql}
                                        AND contextid {$relatedctxsql})
                                   )";

                        if ($rolecount < $numroles) {
                            $where .= $wherejoin;
                            $rolecount++;
                        }

                        $params = array_merge($params, $roleidparams, $relctxparams);
                    }

                } else {
                    // Any / None cases - need one WHERE to cover all filtered roles.
                    list($relatedctxsql, $relctxparams) = $DB->get_in_or_equal($rolecontextids, SQL_PARAMS_NAMED, 'relatedctx');
                    list($roleidssql, $roleidsparams) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED, 'roleids');

                    $where .= "(u.id {$matchinsql} (
                                 SELECT userid
                                   FROM {role_assignments}
                                  WHERE roleid {$roleidssql}
                                    AND contextid {$relatedctxsql})
                               )";

                    $params = array_merge($params, $roleidsparams, $relctxparams);
                }
            }
        }

        return [
            'where' => $where,
            'params' => $params,
        ];
    }

    /**
     * Prepare SQL where clause and associated parameters for any keyword searches being performed.
     *
     * @return array SQL query data in the format ['where' => '', 'params' => []].
     */
    protected function get_keywords_search_sql(): array {
        global $CFG, $DB, $USER;

        $keywords = [];
        $where = '';
        $params = [];
        $keywordsfilter = $this->filterset->get_filter('keywords');
        $jointype = $keywordsfilter->get_join_type();
        // None join types in both filter row and filterset require additional 'not null' handling for accurate keywords matches.
        $notjoin = false;

        // Determine how to match values in the query.
        switch ($jointype) {
            case $keywordsfilter::JOINTYPE_ALL:
                $wherejoin = ' AND ';
                break;
            case $keywordsfilter::JOINTYPE_NONE:
                $wherejoin = ' AND NOT ';
                $notjoin = true;
                break;
            default:
                // Default to 'Any' jointype.
                $wherejoin = ' OR ';
                break;
        }

        // Handle filterset None join type.
        if ($this->filterset->get_join_type() === $this->filterset::JOINTYPE_NONE) {
            $notjoin = true;
        }

        if ($this->filterset->has_filter('keywords')) {
            $keywords = $keywordsfilter->get_filter_values();
        }

        foreach ($keywords as $index => $keyword) {
            $searchkey1 = 'search' . $index . '1';
            $searchkey2 = 'search' . $index . '2';
            $searchkey3 = 'search' . $index . '3';
            $searchkey4 = 'search' . $index . '4';
            $searchkey5 = 'search' . $index . '5';
            $searchkey6 = 'search' . $index . '6';
            $searchkey7 = 'search' . $index . '7';

            $conditions = [];
            // Search by fullname.
            $fullname = $DB->sql_fullname('u.firstname', 'u.lastname');
            $conditions[] = $DB->sql_like($fullname, ':' . $searchkey1, false, false);

            // Search by email.
            $email = $DB->sql_like('email', ':' . $searchkey2, false, false);

            if ($notjoin) {
                $email = "(email IS NOT NULL AND {$email})";
            }

            if (!in_array('email', $this->userfields)) {
                $maildisplay = 'maildisplay' . $index;
                $userid1 = 'userid' . $index . '1';
                // Prevent users who hide their email address from being found by others
                // who aren't allowed to see hidden email addresses.
                $email = "(". $email ." AND (" .
                        "u.maildisplay <> :$maildisplay " .
                        "OR u.id = :$userid1". // Users can always find themselves.
                        "))";
                $params[$maildisplay] = core_user::MAILDISPLAY_HIDE;
                $params[$userid1] = $USER->id;
            }

            $conditions[] = $email;

            // Search by idnumber.
            $idnumber = $DB->sql_like('idnumber', ':' . $searchkey3, false, false);

            if ($notjoin) {
                $idnumber = "(idnumber IS NOT NULL AND  {$idnumber})";
            }

            if (!in_array('idnumber', $this->userfields)) {
                $userid2 = 'userid' . $index . '2';
                // Users who aren't allowed to see idnumbers should at most find themselves
                // when searching for an idnumber.
                $idnumber = "(". $idnumber . " AND u.id = :$userid2)";
                $params[$userid2] = $USER->id;
            }

            $conditions[] = $idnumber;

            if (!empty($CFG->showuseridentity)) {
                // Search all user identify fields.
                $extrasearchfields = explode(',', $CFG->showuseridentity);
                foreach ($extrasearchfields as $extrasearchfield) {
                    if (in_array($extrasearchfield, ['email', 'idnumber', 'country'])) {
                        // Already covered above. Search by country not supported.
                        continue;
                    }
                    $param = $searchkey3 . $extrasearchfield;
                    $condition = $DB->sql_like($extrasearchfield, ':' . $param, false, false);
                    $params[$param] = "%$keyword%";

                    if ($notjoin) {
                        $condition = "($extrasearchfield IS NOT NULL AND {$condition})";
                    }

                    if (!in_array($extrasearchfield, $this->userfields)) {
                        // User cannot see this field, but allow match if their own account.
                        $userid3 = 'userid' . $index . '3' . $extrasearchfield;
                        $condition = "(". $condition . " AND u.id = :$userid3)";
                        $params[$userid3] = $USER->id;
                    }
                    $conditions[] = $condition;
                }
            }

            // Search by middlename.
            $middlename = $DB->sql_like('middlename', ':' . $searchkey4, false, false);

            if ($notjoin) {
                $middlename = "(middlename IS NOT NULL AND {$middlename})";
            }

            $conditions[] = $middlename;

            // Search by alternatename.
            $alternatename = $DB->sql_like('alternatename', ':' . $searchkey5, false, false);

            if ($notjoin) {
                $alternatename = "(alternatename IS NOT NULL AND {$alternatename})";
            }

            $conditions[] = $alternatename;

            // Search by firstnamephonetic.
            $firstnamephonetic = $DB->sql_like('firstnamephonetic', ':' . $searchkey6, false, false);

            if ($notjoin) {
                $firstnamephonetic = "(firstnamephonetic IS NOT NULL AND {$firstnamephonetic})";
            }

            $conditions[] = $firstnamephonetic;

            // Search by lastnamephonetic.
            $lastnamephonetic = $DB->sql_like('lastnamephonetic', ':' . $searchkey7, false, false);

            if ($notjoin) {
                $lastnamephonetic = "(lastnamephonetic IS NOT NULL AND {$lastnamephonetic})";
            }

            $conditions[] = $lastnamephonetic;

            if (!empty($where)) {
                $where .= $wherejoin;
            } else if ($jointype === $keywordsfilter::JOINTYPE_NONE) {
                // Join type 'None' requires the WHERE to begin with NOT.
                $where .= ' NOT ';
            }

            $where .= "(". implode(" OR ", $conditions) .") ";
            $params[$searchkey1] = "%$keyword%";
            $params[$searchkey2] = "%$keyword%";
            $params[$searchkey3] = "%$keyword%";
            $params[$searchkey4] = "%$keyword%";
            $params[$searchkey5] = "%$keyword%";
            $params[$searchkey6] = "%$keyword%";
            $params[$searchkey7] = "%$keyword%";
        }

        return [
            'where' => $where,
            'params' => $params,
        ];
    }
}
