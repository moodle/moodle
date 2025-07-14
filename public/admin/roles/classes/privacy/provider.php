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
 * Privacy Subsystem implementation for core_role.
 *
 * @package    core_role
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_role\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy provider for core_role.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\subsystem\plugin_provider,
    \core_privacy\local\request\user_preference_provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection): collection {
        $rolecapabilities = [
            'roleid' => 'privacy:metadata:role_capabilities:roleid',
            'capability' => 'privacy:metadata:role_capabilities:capability',
            'permission' => 'privacy:metadata:role_capabilities:permission',
            'timemodified' => 'privacy:metadata:role_capabilities:timemodified',
            'modifierid' => 'privacy:metadata:role_capabilities:modifierid'
        ];
        $roleassignments = [
            'roleid' => 'privacy:metadata:role_assignments:roleid',
            'userid' => 'privacy:metadata:role_assignments:userid',
            'timemodified' => 'privacy:metadata:role_assignments:timemodified',
            'modifierid' => 'privacy:metadata:role_assignments:modifierid',
            'component' => 'privacy:metadata:role_assignments:component',
            'itemid' => 'privacy:metadata:role_assignments:itemid'
        ];
        $collection->add_database_table('role_capabilities', $rolecapabilities,
            'privacy:metadata:role_capabilities:tableexplanation');
        $collection->add_database_table('role_assignments', $roleassignments,
            'privacy:metadata:role_assignments:tableexplanation');

        $collection->add_user_preference('definerole_showadvanced',
            'privacy:metadata:preference:showadvanced');

        return $collection;
    }
    /**
     * Export all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $showadvanced = get_user_preferences('definerole_showadvanced', null, $userid);
        if ($showadvanced !== null) {
            writer::export_user_preference('core_role',
                'definerole_showadvanced',
                transform::yesno($showadvanced),
                get_string('privacy:metadata:preference:showadvanced', 'core_role')
            );
        }
    }
    /**
     * Return all contexts for this userid.
     *
     * @param  int $userid The user ID.
     * @return contextlist The list of context IDs.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();

        // The role_capabilities table contains user data.
        $contexts = [
            CONTEXT_SYSTEM,
            CONTEXT_USER,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
            CONTEXT_MODULE,
            CONTEXT_BLOCK
        ];
        list($insql, $inparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {role_capabilities} rc
                    ON rc.contextid = ctx.id
                   AND ((ctx.contextlevel {$insql} AND rc.modifierid = :modifierid)
                    OR (ctx.contextlevel = :contextlevel AND ctx.instanceid = :userid))";
        $params = [
            'modifierid' => $userid,
            'contextlevel' => CONTEXT_USER,
            'userid' => $userid
         ];
        $params += $inparams;

        $contextlist->add_from_sql($sql, $params);

        // The role_assignments table contains user data.
        $contexts = [
            CONTEXT_SYSTEM,
            CONTEXT_USER,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
            CONTEXT_MODULE,
            CONTEXT_BLOCK
        ];
        list($insql, $inparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $params = [
            'userid' => $userid,
            'modifierid' => $userid
         ];
        $params += $inparams;
        $sql = "SELECT ctx.id
                  FROM {role_assignments} ra
                  JOIN {context} ctx
                    ON ctx.id = ra.contextid
                   AND ctx.contextlevel {$insql}
                 WHERE (ra.userid = :userid
                    OR ra.modifierid = :modifierid)
                   AND ra.component != 'tool_cohortroles'";
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {

        if (empty($userlist)) {
            return;
        }

        $context = $userlist->get_context();

        // Include users who created or modified role capabilities.
        $sql = "SELECT modifierid as userid
                  FROM {role_capabilities}
                 WHERE contextid = :contextid";

        $params = [
            'contextid' => $context->id
        ];

        $userlist->add_from_sql('userid', $sql, $params);

        // Include users that have a role assigned to them.
        $sql = "SELECT userid
                  FROM {role_assignments}
                 WHERE contextid = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);

        // Include users who created or modified the role assignment.
        // Differentiate and exclude special cases where tool_cohortroles adds records through the
        // "Assign user roles to cohort" feature into the role_assignments table.
        // These records should be separately processed in tool_cohortroles.
        $sql = "SELECT modifierid as userid
                  FROM {role_assignments}
                 WHERE contextid = :contextid
                       AND component != 'tool_cohortroles'";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist)) {
             return;
        }

        $rolesnames = self::get_roles_name();
        $userid = $contextlist->get_user()->id;
        $ctxfields = \context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Role Assignments export data.
        $contexts = [
            CONTEXT_SYSTEM,
            CONTEXT_USER,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
            CONTEXT_MODULE,
            CONTEXT_BLOCK
        ];
        list($inctxsql, $ctxparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $sql = "SELECT ra.id, ra.contextid, ra.roleid, ra.userid, ra.timemodified, ra.modifierid, $ctxfields
                  FROM {role_assignments} ra
                  JOIN {context} ctx
                    ON ctx.id = ra.contextid
                   AND ctx.contextlevel {$inctxsql}
                   AND (ra.userid = :userid OR ra.modifierid = :modifierid)
                   AND ra.component != 'tool_cohortroles'
                  JOIN {role} r
                    ON r.id = ra.roleid
                 WHERE ctx.id {$insql}";
        $params = ['userid' => $userid, 'modifierid' => $userid];
        $params += $inparams;
        $params += $ctxparams;
        $assignments = $DB->get_recordset_sql($sql, $params);
        foreach ($assignments as $assignment) {
            \context_helper::preload_from_record($assignment);
            $alldata[$assignment->contextid][$rolesnames[$assignment->roleid]][] = (object)[
                'timemodified' => transform::datetime($assignment->timemodified),
                'userid' => transform::user($assignment->userid),
                'modifierid' => transform::user($assignment->modifierid)
            ];
        }
        $assignments->close();
        if (!empty($alldata)) {
            array_walk($alldata, function($roledata, $contextid) {
                $context = \context::instance_by_id($contextid);
                array_walk($roledata, function($data, $rolename) use ($context) {
                    writer::with_context($context)->export_data(
                            [get_string('privacy:metadata:role_assignments', 'core_role'), $rolename],
                            (object)$data);
                });
            });
            unset($alldata);
        }

        // Role Capabilities export data.
        $strpermissions = self::get_permissions_name();
        $contexts = [
            CONTEXT_SYSTEM,
            CONTEXT_USER,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
            CONTEXT_MODULE,
            CONTEXT_BLOCK
        ];
        list($inctxsql, $ctxparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $sql = "SELECT rc.id, rc.contextid, rc.capability, rc.permission, rc.timemodified, rc.roleid, $ctxfields
                  FROM {context} ctx
                  JOIN {role_capabilities} rc
                    ON rc.contextid = ctx.id
                   AND ((ctx.contextlevel {$inctxsql} AND rc.modifierid = :modifierid)
                    OR (ctx.contextlevel = :contextlevel AND ctx.instanceid = :userid))
                 WHERE ctx.id {$insql}";
        $params = [
            'modifierid' => $userid,
            'contextlevel' => CONTEXT_USER,
            'userid' => $userid
         ];
        $params += $inparams;
        $params += $ctxparams;
        $capabilities = $DB->get_recordset_sql($sql, $params);
        foreach ($capabilities as $capability) {
            \context_helper::preload_from_record($capability);
            $alldata[$capability->contextid][$rolesnames[$capability->roleid]][] = (object)[
                'timemodified' => transform::datetime($capability->timemodified),
                'capability' => $capability->capability,
                'permission' => $strpermissions[$capability->permission]
            ];
        }
        $capabilities->close();
        if (!empty($alldata)) {
            array_walk($alldata, function($capdata, $contextid) {
                $context = \context::instance_by_id($contextid);
                array_walk($capdata, function($data, $rolename) use ($context) {
                    writer::with_context($context)->export_data(
                            [get_string('privacy:metadata:role_capabilities', 'core_role'), $rolename],
                            (object)$data);
                });
            });
        }
    }
    /**
     * Exports the data relating to tool_cohortroles component on role assignments by
     * Assign user roles to cohort feature.
     *
     * @param  int $userid The user ID.
     */
    public static function export_user_role_to_cohort(int $userid) {
        global $DB;

        $rolesnames = self::get_roles_name();
        $sql = "SELECT ra.id, ra.contextid, ra.roleid, ra.userid, ra.timemodified, ra.modifierid, r.id as roleid
                  FROM {role_assignments} ra
                  JOIN {context} ctx
                    ON ctx.id = ra.contextid
                   AND ctx.contextlevel = :contextlevel
                   AND ra.component = 'tool_cohortroles'
                  JOIN {role} r
                    ON r.id = ra.roleid
                 WHERE ctx.instanceid = :instanceid
                    OR ra.userid = :userid";
        $params = ['userid' => $userid, 'instanceid' => $userid, 'contextlevel' => CONTEXT_USER];
        $assignments = $DB->get_recordset_sql($sql, $params);
        foreach ($assignments as $assignment) {
            $alldata[$assignment->contextid][$rolesnames[$assignment->roleid]][] = (object)[
                'timemodified' => transform::datetime($assignment->timemodified),
                'userid' => transform::user($assignment->userid),
                'modifierid' => transform::user($assignment->modifierid)
            ];
        }
        $assignments->close();
        if (!empty($alldata)) {
            array_walk($alldata, function($roledata, $contextid) {
                $context = \context::instance_by_id($contextid);
                array_walk($roledata, function($data, $rolename) use ($context) {
                    writer::with_context($context)->export_related_data(
                            [get_string('privacy:metadata:role_cohortroles', 'core_role'), $rolename], 'cohortroles',
                            (object)$data);
                });
            });
        }
    }
    /**
     * Delete all user data for this context.
     *
     * @param  \context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Don't remove data from role_capabilities.
        // Because this data affects the whole Moodle, there are override capabilities.
        // Don't belong to the modifier user.

        // Remove data from role_assignments.
        $DB->delete_records('role_assignments', ['contextid' => $context->id]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        // Don't remove data from role_capabilities.
        // Because this data affects the whole Moodle, there are override capabilities.
        // Don't belong to the modifier user.
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = ['contextid' => $context->id] + $userparams;

        // Remove data from role_assignments.
        $DB->delete_records_select('role_assignments',
            "contextid = :contextid AND userid {$usersql}", $params);
    }

    /**
     * Delete all user data for this user only.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // Don't remove data from role_capabilities.
        // Because this data affects the whole Moodle, there are override capabilities.
        // Don't belong to the modifier user.

        // Remove data from role_assignments.
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $contextids = $contextlist->get_contextids();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $params = ['userid' => $userid] + $contextparams;

        // Only delete the roles assignments where the user is assigned in all contexts.
        $DB->delete_records_select('role_assignments',
            "userid = :userid AND contextid {$contextsql}", $params);
    }
    /**
     * Delete user entries in role_assignments related to the feature
     * Assign user roles to cohort feature.
     *
     * @param  int $userid The user ID.
     */
    public static function delete_user_role_to_cohort(int $userid) {
        global $DB;

        // Delete entries where userid is a mentor by tool_cohortroles.
        $DB->delete_records('role_assignments', ['userid' => $userid, 'component' => 'tool_cohortroles']);
    }
    /**
     * Get all the localised roles name in a simple array.
     *
     * @return array Array of name of the roles by roleid.
     */
    protected static function get_roles_name() {
        $roles = role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL);
        $rolesnames = array();
        foreach ($roles as $role) {
            $rolesnames[$role->id] = $role->localname;
        }
        return $rolesnames;
    }
    /**
     * Get all the permissions name in a simple array.
     *
     * @return array Array of permissions name.
     */
    protected static function get_permissions_name() {
        $strpermissions = array(
            CAP_INHERIT => get_string('inherit', 'role'),
            CAP_ALLOW => get_string('allow', 'role'),
            CAP_PREVENT => get_string('prevent', 'role'),
            CAP_PROHIBIT => get_string('prohibit', 'role')
        );
        return $strpermissions;
    }
}