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
 * Raw event retrieval strategy.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\strategies;

defined('MOODLE_INTERNAL') || die();

/**
 * Raw event retrieval strategy.
 *
 * This strategy is based on what used to be the calendar API's get_events function.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class raw_event_retrieval_strategy implements raw_event_retrieval_strategy_interface {

    public function get_raw_events(
        ?array $usersfilter = null,
        ?array $groupsfilter = null,
        ?array $coursesfilter = null,
        ?array $categoriesfilter = null,
        ?array $whereconditions = null,
        ?array $whereparams = null,
        $ordersql = null,
        $offset = null,
        $limitnum = null,
        $ignorehidden = true
    ) {
        return $this->get_raw_events_legacy_implementation(
            !is_null($usersfilter) ? $usersfilter : true, // True means no filter in old implementation.
            !is_null($groupsfilter) ? $groupsfilter : true,
            !is_null($coursesfilter) ? $coursesfilter : true,
            !is_null($categoriesfilter) ? $categoriesfilter : true,
            $whereconditions,
            $whereparams,
            $ordersql,
            $offset,
            $limitnum,
            $ignorehidden
        );
    }

    /**
     * The legacy implementation with minor tweaks.
     *
     * @param array|int|boolean $users array of users, user id or boolean for all/no user events
     * @param array|int|boolean $groups array of groups, group id or boolean for all/no group events
     * @param array|int|boolean $courses array of courses, course id or boolean for all/no course events
     * @param array $whereconditions The conditions in the WHERE clause.
     * @param array $whereparams The parameters for the WHERE clause.
     * @param string $ordersql The ORDER BY clause.
     * @param int $offset Offset.
     * @param int $limitnum Limit.
     * @param boolean $ignorehidden whether to select only visible events or all events
     * @return array $events of selected events or an empty array if there aren't any (or there was an error)
     */
    protected function get_raw_events_legacy_implementation(
        $users,
        $groups,
        $courses,
        $categories,
        $whereconditions,
        $whereparams,
        $ordersql,
        $offset,
        $limitnum,
        $ignorehidden
    ) {
        global $DB;

        $params = array();
        // Quick test.
        if (empty($users) && empty($groups) && empty($courses) && empty($categories)) {
            return array();
        }

        if (is_numeric($users)) {
            $users = array($users);
        }
        if (is_numeric($groups)) {
            $groups = array($groups);
        }
        if (is_numeric($courses)) {
            $courses = array($courses);
        }
        if (is_numeric($categories)) {
            $categories = array($categories);
        }

        // Array of filter conditions. To be concatenated by the OR operator.
        $filters = [];

        // User filter.
        if (is_array($users) && !empty($users)) {
            // Events from a number of users.
            list($insqlusers, $inparamsusers) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
            $filters[] = "(e.userid $insqlusers AND e.courseid = 0 AND e.groupid = 0 AND e.categoryid = 0)";
            $params = array_merge($params, $inparamsusers);
        } else if ($users === true) {
            // Events from ALL users.
            $filters[] = "(e.userid != 0 AND e.courseid = 0 AND e.groupid = 0 AND e.categoryid = 0)";
        }
        // Boolean false (no users at all): We don't need to do anything.

        // Group filter.
        if (is_array($groups) && !empty($groups)) {
            // Events from a number of groups.
            list($insqlgroups, $inparamsgroups) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);
            $filters[] = "e.groupid $insqlgroups";
            $params = array_merge($params, $inparamsgroups);
        } else if ($groups === true) {
            // Events from ALL groups.
            $filters[] = "e.groupid != 0";
        }
        // Boolean false (no groups at all): We don't need to do anything.

        // Course filter.
        if (is_array($courses) && !empty($courses)) {
            list($insqlcourses, $inparamscourses) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $filters[] = "(e.groupid = 0 AND e.courseid $insqlcourses)";
            $params = array_merge($params, $inparamscourses);
        } else if ($courses === true) {
            // Events from ALL courses.
            $filters[] = "(e.groupid = 0 AND e.courseid != 0)";
        }

        // Category filter.
        if (is_array($categories) && !empty($categories)) {
            list($insqlcategories, $inparamscategories) = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED);
            $filters[] = "(e.groupid = 0 AND e.courseid = 0 AND e.categoryid $insqlcategories)";
            $params = array_merge($params, $inparamscategories);
        } else if ($categories === true) {
            // Events from ALL categories.
            $filters[] = "(e.groupid = 0 AND e.courseid = 0 AND e.categoryid != 0)";
        }

        // Security check: if, by now, we have NOTHING in $whereclause, then it means
        // that NO event-selecting clauses were defined. Thus, we won't be returning ANY
        // events no matter what. Allowing the code to proceed might return a completely
        // valid query with only time constraints, thus selecting ALL events in that time frame!
        if (empty($filters)) {
            return array();
        }

        // Build our clause for the filters.
        $filterclause = implode(' OR ', $filters);

        // Array of where conditions for our query. To be concatenated by the AND operator.
        $whereconditions[] = "($filterclause)";

        // Show visible only.
        if ($ignorehidden) {
            $whereconditions[] = "(e.visible = 1)";
        }

        // Build the main query's WHERE clause.
        $whereclause = implode(' AND ', $whereconditions);

        // Build SQL subquery and conditions for filtered events based on priorities.
        $subquerytimeconditions = array_filter($whereconditions, function($condition) {
            return (strpos($condition, 'time') !== false);
        });
        $subquerywhere = '';
        $subqueryconditions = [];
        $subqueryparams = [];
        $allusercourses = [];

        if (is_array($users) && !empty($users)) {
            $userrecords = $DB->get_records_sql("SELECT * FROM {user} WHERE id $insqlusers", $inparamsusers);
            foreach ($userrecords as $userrecord) {
                // Get the user's courses. Otherwise, get the default courses being shown by the calendar.
                $usercourses = calendar_get_default_courses(null, 'id, category, groupmode, groupmodeforce',
                        false, $userrecord->id);

                // Set calendar filters.
                list($usercourses, $usergroups, $user) = calendar_set_filters($usercourses, true, $userrecord);

                $filteredcourses = is_array($courses) ? $courses : [$courses];
                $filteredcourses = array_filter($usercourses, function($course) use ($filteredcourses) {
                    return in_array($course, $filteredcourses);
                });

                $allusercourses = array_merge($allusercourses, $filteredcourses);

                // Flag to indicate whether the query needs to exclude group overrides.
                $viewgroupsonly = false;

                if ($user) {
                    // Set filter condition for the user's events.
                    // Even though $user is a single scalar, we still use get_in_or_equal() because we are inside a loop.
                    list($inusers, $inuserparams) = $DB->get_in_or_equal($user, SQL_PARAMS_NAMED);
                    $condition = "(ev.userid $inusers AND ev.courseid = 0 AND ev.groupid = 0 AND ev.categoryid = 0)";
                    $subqueryconditions[] = $condition;
                    $subqueryparams = array_merge($subqueryparams, $inuserparams);

                    foreach ($usercourses as $courseid) {
                        if (has_capability('moodle/site:accessallgroups', \context_course::instance($courseid), $userrecord)) {
                            $usergroupmembership = groups_get_all_groups($courseid, $user, 0, 'g.id');
                            if (count($usergroupmembership) == 0) {
                                $viewgroupsonly = true;
                                break;
                            }
                        }
                    }
                }

                // Set filter condition for the user's group events.
                if ($usergroups === true || $viewgroupsonly) {
                    // Fetch group events, but not group overrides.
                    $groupconditions = "(ev.groupid != 0 AND ev.eventtype = 'group')";
                } else if (!empty($usergroups)) {
                    // Fetch group events and group overrides.
                    list($inusergroups, $inusergroupparams) = $DB->get_in_or_equal($usergroups, SQL_PARAMS_NAMED);
                    $groupconditions = "(ev.groupid $inusergroups)";
                    $subqueryparams = array_merge($subqueryparams, $inusergroupparams);
                }
            }
        } else if ($users === true) {
            // Events from ALL users.
            $subqueryconditions[] = "(ev.userid != 0 AND ev.courseid = 0 AND ev.groupid = 0 AND ev.categoryid = 0)";

            if (is_array($groups)) {
                // Events from a number of groups.
                list($insqlgroups, $inparamsgroups) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);
                $subqueryconditions[] = "ev.groupid $insqlgroups";
                $subqueryparams = array_merge($subqueryparams, $inparamsgroups);
            } else if ($groups === true) {
                // Events from ALL groups.
                $subqueryconditions[] = "ev.groupid != 0";
            }

            if ($courses === true) {
                // ALL course events. It's not needed to worry about users' access as $users = true.
                $subqueryconditions[] = "(ev.groupid = 0 AND ev.courseid != 0 AND ev.categoryid = 0)";
            }
        }

        // Get courses to be used for the subquery.
        $subquerycourses = [];
        if (is_array($courses)) {
            $subquerycourses = $courses;
        }
        // Merge with user courses, if necessary.
        if (!empty($allusercourses)) {
            $subquerycourses = array_merge($subquerycourses, $allusercourses);
            // Make sure we remove duplicate values.
            $subquerycourses = array_unique($subquerycourses);
        }

        // Set subquery filter condition for the courses.
        if (!empty($subquerycourses)) {
            list($incourses, $incoursesparams) = $DB->get_in_or_equal($subquerycourses, SQL_PARAMS_NAMED);
            if (isset($groupconditions)) {
                $groupconditions = $groupconditions." OR ";
            } else {
                $groupconditions = '';
            }
            $condition = "($groupconditions(ev.groupid = 0 AND ev.courseid $incourses AND ev.categoryid = 0))";
            $subtimesparams = [];
            if (!empty($subquerytimeconditions)) {
                $subtimes = $this->subquerytimeconditions("courses", $subquerytimeconditions, $whereparams);
                $condition .= $subtimes['where'];
                $subtimesparams = $subtimes['params'];
            }
            $subqueryconditions[] = $condition;
            $subqueryparams = array_merge($subqueryparams, $incoursesparams, $subtimesparams);
        }

        // Set subquery filter condition for the categories.
        if ($categories === true) {
            $subqueryconditions[] = "(ev.categoryid != 0 AND ev.eventtype = 'category')";
        } else if (!empty($categories)) {
            list($incategories, $incategoriesparams) = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED);
            $condition = "(ev.groupid = 0 AND ev.courseid = 0 AND ev.categoryid $incategories)";
            $subtimesparams = [];
            if (!empty($subquerytimeconditions)) {
                $subtimes = $this->subquerytimeconditions("cats", $subquerytimeconditions, $whereparams);
                $condition .= $subtimes['where'];
                $subtimesparams = $subtimes['params'];
            }
            $subqueryconditions[] = $condition;
            $subqueryparams = array_merge($subqueryparams, $incategoriesparams, $subtimesparams);
        }

        // Build the WHERE condition for the sub-query.
        if (!empty($subqueryconditions)) {
            $unionstartquery = "SELECT modulename, instance, eventtype, priority
                                  FROM {event} ev
                                 WHERE ";
            $subqueryunion = '('.$unionstartquery . implode(" UNION $unionstartquery ", $subqueryconditions).')';
        } else {
            $subqueryunion = '{event}';
        }

        // Merge subquery parameters to the parameters of the main query.
        if (!empty($subqueryparams)) {
            $params = array_merge($params, $subqueryparams);
        }

        // Sub-query that fetches the list of unique events that were filtered based on priority.
        $subquery = "SELECT ev.modulename,
                            ev.instance,
                            ev.eventtype,
                            MIN(ev.priority) as priority
                       FROM $subqueryunion ev
                   GROUP BY ev.modulename, ev.instance, ev.eventtype";

        // Build the main query.
        $sql = "SELECT e.*, c.fullname AS coursefullname, c.shortname AS courseshortname
                  FROM {event} e
            INNER JOIN ($subquery) fe
                    ON e.modulename = fe.modulename
                       AND e.instance = fe.instance
                       AND e.eventtype = fe.eventtype
                       AND (e.priority = fe.priority OR (e.priority IS NULL AND fe.priority IS NULL))
             LEFT JOIN {modules} m
                    ON e.modulename = m.name
             LEFT JOIN {course} c
                    ON c.id = e.courseid
                 WHERE (m.visible = 1 OR m.visible IS NULL) AND $whereclause
              ORDER BY " . ($ordersql ? $ordersql : "e.timestart");

        if (!empty($whereparams)) {
            $params = array_merge($params, $whereparams);
        }

        $events = $DB->get_records_sql($sql, $params, $offset, $limitnum);

        return  $events === false ? [] : $events;
    }

    /**
     * Returns a query fragment and params, with time constraints applied
     *
     * @param  string $prefix
     * @param  array $conditions
     * @param  array $params
     * @return array [<where>, <params>]
     */
    protected function subquerytimeconditions(string $prefix, array $conditions, array $params): array {
        $outwhere = '';
        $outparams = [];
        // Most specific to least specific.
        $timeparams = ['timefromid', 'timefrom3', 'timefrom2', 'timefrom1', 'timefrom', 'timetoid', 'timeto2', 'timeto1', 'timeto'];
        $whereconditions = [];
        foreach ($conditions as $condition) {
            $where = $condition;
            // This query has been borrowed from the main WHERE clause, so the alias needs to be renamed to match the union.
            $where = str_replace('e.id', 'ev.id', $where);
            foreach ($timeparams as $timeparam) {
                if (isset($params[$timeparam])) {
                    $where = str_replace(":{$timeparam}", ":{$prefix}{$timeparam}", $where);
                    $outparams["{$prefix}{$timeparam}"] = $params[$timeparam];
                }
            }
            $whereconditions[] = $where;
        }
        if (count($whereconditions) > 0) {
            $outwhere = ' AND ' . implode(' AND ', $whereconditions);
        }
        return ['where' => $outwhere, 'params' => $outparams];
    }
}
