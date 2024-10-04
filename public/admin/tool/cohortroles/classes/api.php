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
 * Class exposing the api for the cohortroles tool.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cohortroles;

use stdClass;
use context_system;
use core\invalid_persistent_exception;

/**
 * Class for doing things with cohort roles.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Create a cohort role assignment from a record containing all the data for the class.
     *
     * Requires moodle/role:manage capability at the system context.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return competency
     */
    public static function create_cohort_role_assignment(stdClass $record) {
        $cohortroleassignment = new cohort_role_assignment(0, $record);
        $context = context_system::instance();

        // First we do a permissions check.
        require_capability('moodle/role:manage', $context);

        // Validate before we check for existing records.
        if (!$cohortroleassignment->is_valid()) {
            throw new invalid_persistent_exception($cohortroleassignment->get_errors());
        }

        $existing = cohort_role_assignment::get_record((array) $record);
        if (!empty($existing)) {
            return false;
        } else {
            // OK - all set.
            $cohortroleassignment->create();
        }
        return $cohortroleassignment;
    }

    /**
     * Delete a cohort role assignment by id.
     *
     * Requires moodle/role:manage capability at the system context.
     *
     * @param int $id The record to delete. This will also remove this role from the user for all users in the system.
     * @return boolean
     */
    public static function delete_cohort_role_assignment($id) {
        $cohortroleassignment = new cohort_role_assignment($id);
        $context = context_system::instance();

        // First we do a permissions check.
        require_capability('moodle/role:manage', $context);

        // OK - all set.
        return $cohortroleassignment->delete();
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/role:manage capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @return array of cohort_role_assignment
     */
    public static function list_cohort_role_assignments($sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        $context = context_system::instance();

        // First we do a permissions check.
        require_capability('moodle/role:manage', $context);

        // OK - all set.
        return cohort_role_assignment::get_records(array(), $sort, $order, $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/role:manage capability at system context.
     *
     * @return int
     */
    public static function count_cohort_role_assignments() {
        $context = context_system::instance();

        // First we do a permissions check.
        require_capability('moodle/role:manage', $context);

        // OK - all set.
        return cohort_role_assignment::count_records();
    }

    /**
     * Sync all roles - adding and deleting role assignments as required.
     *
     * Slow. Should only be called from a background task.
     *
     * Requires moodle/role:manage capability at the system context.
     *
     * @return array('rolesadded' => array of (useridassignedto, useridassignedover, roleid),
     *               'rolesremoved' => array of (useridassignedto, useridassignedover, roleid))
     */
    public static function sync_all_cohort_roles() {
        global $DB;

        $context = context_system::instance();

        // First we do a permissions check.
        require_capability('moodle/role:manage', $context);

        // Ok ready to go.
        $rolesadded = array();
        $rolesremoved = array();

        // Remove any cohort role mappings for roles which have been deleted.
        // The role assignments are not a consideration because these will have been removed when the role was.
        $DB->delete_records_select('tool_cohortroles', "roleid NOT IN (SELECT id FROM {role})");

        // Get all cohort role assignments and group them by user and role.
        $all = cohort_role_assignment::get_records(array(), 'userid, roleid');
        // We build an better structure to loop on.
        $info = array();
        foreach ($all as $cra) {
            if (!isset($info[$cra->get('userid')])) {
                $info[$cra->get('userid')] = array();
            }
            if (!isset($info[$cra->get('userid')][$cra->get('roleid')])) {
                $info[$cra->get('userid')][$cra->get('roleid')] = array();
            }
            array_push($info[$cra->get('userid')][$cra->get('roleid')], $cra->get('cohortid'));
        }
        // Then for each user+role combo - find user context in the cohort without a role assigned.

        foreach ($info as $userid => $roles) {
            foreach ($roles as $roleid => $cohorts) {
                list($cohortsql, $params) = $DB->get_in_or_equal($cohorts, SQL_PARAMS_NAMED);

                $params['usercontext'] = CONTEXT_USER;
                $params['roleid'] = $roleid;
                $params['userid'] = $userid;

                $sql = 'SELECT DISTINCT u.id AS userid, ra.id, ctx.id AS contextid
                          FROM {user} u
                          JOIN {cohort_members} cm ON u.id = cm.userid
                          JOIN {context} ctx ON u.id = ctx.instanceid AND ctx.contextlevel = :usercontext
                          LEFT JOIN {role_assignments} ra ON ra.contextid = ctx.id
                           AND ra.roleid = :roleid
                           AND ra.userid = :userid
                         WHERE cm.cohortid ' . $cohortsql . '
                           AND u.deleted = 0
                           AND ra.id IS NULL';

                $toadd = $DB->get_records_sql($sql, $params);

                foreach ($toadd as $add) {
                    role_assign($roleid, $userid, $add->contextid, 'tool_cohortroles');
                    $rolesadded[] = array(
                        'useridassignedto' => $userid,
                        'useridassignedover' => $add->userid,
                        'roleid' => $roleid
                    );
                }
            }
        }

        // And for each user+role combo - find user context not in the cohort with a role assigned.
        // If the role was assigned by this component, unassign the role.
        foreach ($info as $userid => $roles) {
            foreach ($roles as $roleid => $cohorts) {
                // Now we are looking for entries NOT in the cohort.
                list($cohortsql, $params) = $DB->get_in_or_equal($cohorts, SQL_PARAMS_NAMED);

                $params['usercontext'] = CONTEXT_USER;
                $params['roleid'] = $roleid;
                $params['userid'] = $userid;
                $params['component'] = 'tool_cohortroles';

                $sql = 'SELECT u.id as userid, ra.id, ctx.id AS contextid
                          FROM {user} u
                          JOIN {context} ctx ON u.id = ctx.instanceid AND ctx.contextlevel = :usercontext
                          JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.roleid = :roleid AND ra.userid = :userid
                     LEFT JOIN {cohort_members} cm ON u.id = cm.userid
                           AND cm.cohortid ' . $cohortsql . '
                         WHERE ra.component = :component AND cm.cohortid IS NULL';

                $toremove = $DB->get_records_sql($sql, $params);
                foreach ($toremove as $remove) {
                    role_unassign($roleid, $userid, $remove->contextid, 'tool_cohortroles');
                    $rolesremoved[] = array(
                        'useridassignedto' => $userid,
                        'useridassignedover' => $remove->userid,
                        'roleid' => $roleid
                    );
                }
            }
        }

        // Clean the legacy role assignments which are stale.
        $paramsclean['usercontext'] = CONTEXT_USER;
        $paramsclean['component'] = 'tool_cohortroles';
        $sql = 'SELECT DISTINCT(ra.id), ra.roleid, ra.userid, ra.contextid, ctx.instanceid
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :usercontext
                  JOIN {cohort_members} cm ON cm.userid = ctx.instanceid
                  LEFT JOIN {tool_cohortroles} tc ON tc.cohortid = cm.cohortid
                    AND tc.userid = ra.userid
                    AND tc.roleid = ra.roleid
                 WHERE ra.component = :component
                   AND tc.id is null';
        if ($candidatelegacyassignments = $DB->get_records_sql($sql, $paramsclean)) {
            $sql = 'SELECT DISTINCT(ra.id)
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :usercontext
                  JOIN {cohort_members} cm ON cm.userid = ctx.instanceid
                  JOIN {tool_cohortroles} tc ON tc.cohortid = cm.cohortid AND tc.userid = ra.userid
                 WHERE ra.component = :component';
            if ($currentvalidroleassignments = $DB->get_records_sql($sql, $paramsclean)) {
                foreach ($candidatelegacyassignments as $candidate) {
                    if (!array_key_exists($candidate->id, $currentvalidroleassignments)) {
                        role_unassign($candidate->roleid, $candidate->userid, $candidate->contextid, 'tool_cohortroles');
                        $rolesremoved[] = array(
                            'useridassignedto' => $candidate->userid,
                            'useridassignedover' => $candidate->instanceid,
                            'roleid' => $candidate->roleid
                        );
                    }
                }
            } else {
                foreach ($candidatelegacyassignments as $candidate) {
                    role_unassign($candidate->roleid, $candidate->userid, $candidate->contextid, 'tool_cohortroles');
                    $rolesremoved[] = array(
                        'useridassignedto' => $candidate->userid,
                        'useridassignedover' => $candidate->instanceid,
                        'roleid' => $candidate->roleid
                    );
                }
            }
        }

        return array('rolesadded' => $rolesadded, 'rolesremoved' => $rolesremoved);
    }

}
