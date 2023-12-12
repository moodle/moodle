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
 * Group visibility methods
 *
 * @package   core_group
 * @copyright 2022 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_group;

/**
 * Group visibility methods.
 */
class visibility {

    /**
     * Store the number groups with visibility other than ALL on the course.
     *
     * @param int $courseid Course ID to update the cache for.
     * @param \cache|null $cache Existing cache instance. If null, once will be created.
     * @return void
     * @throws \dml_exception
     */
    public static function update_hiddengroups_cache(int $courseid, ?\cache $cache = null): void {
        global $DB;
        if (!$cache) {
            $cache = \cache::make('core', 'coursehiddengroups');
        }
        $hiddengroups = $DB->count_records_select('groups', 'courseid = ? AND visibility != ?',
                [$courseid, GROUPS_VISIBILITY_ALL]);
        $cache->set($courseid, $hiddengroups);
    }

    /**
     * Return whether a course currently had hidden groups.
     *
     * This can be used as a shortcut to decide whether visibility restrictions need to be applied. If this returns false,
     * we may be able to use cached data, or do a much simpler query.
     *
     * @param int $courseid
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function course_has_hidden_groups(int $courseid): bool {
        $cache = \cache::make('core', 'coursehiddengroups');
        $hiddengroups = $cache->get($courseid);
        if ($hiddengroups === false) {
            self::update_hiddengroups_cache($courseid, $cache);
            $cache->get($courseid);
        }
        return $hiddengroups > 0;
    }

    /**
     * Can the current user view all the groups on the course?
     *
     * Returns true if there are no groups on the course with visibility != ALL,
     * or if the user has viewhiddengroups.
     *
     * This is useful for deciding whether we need to perform additional visibility checkes
     * such as the sql_* methods of this class.
     *
     * @param int $courseid
     * @return bool
     */
    public static function can_view_all_groups(int $courseid): bool {
        $viewhidden = has_capability('moodle/course:viewhiddengroups', \context_course::instance($courseid));
        $hashidden = self::course_has_hidden_groups($courseid);
        return $viewhidden || !$hashidden;
    }

    /**
     * Return SQL conditions for determining whether a user can see a group and its memberships.
     *
     * @param int $userid
     * @param string $groupsalias The SQL alias being used for the groups table.
     * @param string $groupsmembersalias The SQL alias being used for the groups_members table.
     * @return array [$where, $params]
     */
    public static function sql_group_visibility_where(int $userid,
            string $groupsalias = 'g', string $groupsmembersalias = 'gm'): array {
        global $USER;
        // Apply visibility restrictions.
        // Everyone can see who is in groups with ALL visibility.
        $where = "({$groupsalias}.visibility = :all";
        $params['all'] = GROUPS_VISIBILITY_ALL;
        if ($userid == $USER->id) {
            // If the user is looking at their own groups, they can see those with MEMBERS or OWN visibility.
            $where .= " OR {$groupsalias}.visibility IN (:members, :own)";
            $params['members'] = GROUPS_VISIBILITY_MEMBERS;
            $params['own'] = GROUPS_VISIBILITY_OWN;
        } else {
            list($memberssql, $membersparams) = self::sql_members_visibility_condition($groupsalias, $groupsmembersalias);
            // If someone else's groups, they can see those with MEMBERS visibilty, only if they are a member too.
            $where .= " OR ($memberssql)";
            $params = array_merge($params, $membersparams);
        }
        $where .= ")";
        return [$where, $params];
    }

    /**
     * Return SQL conditions for determining whether a user can see a group's members.
     *
     * @param string $groupsalias The SQL alias being used for the groups table.
     * @param string $groupsmembersalias The SQL alias being used for the groups_members table.
     * @param string $useralias The SQL alias being used for the user table.
     * @param string $paramprefix Prefix for the parameter names.
     * @return array [$where, $params]
     */
    public static function sql_member_visibility_where(
        string $groupsalias = 'g',
        string $groupsmembersalias = 'gm',
        string $useralias = 'u',
        string $paramprefix = '',
    ): array {
        global $USER;

        list($memberssql, $membersparams) = self::sql_members_visibility_condition($groupsalias, $groupsmembersalias, $paramprefix);

        $where = "(
            {$groupsalias}.visibility = :{$paramprefix}all
            OR ($memberssql)
            OR ({$groupsalias}.visibility = :{$paramprefix}own AND {$useralias}.id = :{$paramprefix}currentuser2)
        )";
        $params = [
            "{$paramprefix}all" => GROUPS_VISIBILITY_ALL,
            "{$paramprefix}own" => GROUPS_VISIBILITY_OWN,
            "{$paramprefix}currentuser2" => $USER->id,
        ];
        $params = array_merge($params, $membersparams);
        return [$where, $params];
    }

    /**
     * Return a condition to check if a user can view a group because it has MEMBERS visibility and they are a member.
     *
     * @param string $groupsalias The SQL alias being used for the groups table.
     * @param string $groupsmembersalias The SQL alias being used for the groups_members table.
     * @param string $paramprefix Prefix for the parameter names.
     * @return array [$sql, $params]
     */
    protected static function sql_members_visibility_condition(
        string $groupsalias = 'g',
        string $groupsmembersalias = 'gm',
        string $paramprefix = '',
    ): array {
        global $USER;
        $sql = "{$groupsalias}.visibility = :{$paramprefix}members
                    AND (
                        SELECT gm2.id
                          FROM {groups_members} gm2
                         WHERE gm2.groupid = {$groupsmembersalias}.groupid
                               AND gm2.userid = :{$paramprefix}currentuser
                    ) IS NOT NULL";
        $params = [
            "{$paramprefix}members" => GROUPS_VISIBILITY_MEMBERS,
            "{$paramprefix}currentuser" => $USER->id
        ];

        return [$sql, $params];
    }
}
