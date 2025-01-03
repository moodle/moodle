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

namespace local_ai_manager;

use local_ai_manager\local\tenant;
use local_ai_manager\local\userinfo;
use local_ai_manager\local\userusage;
use moodle_url;
use stdClass;

/**
 * Base class for connector subplugins.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_manager_utils {

    /**
     * API function to retrieve entries from the ai_manager logging table.
     *
     * @param string $component the component which has logged the records
     * @param int $contextid the contextid
     * @param int $userid the userid of the user, optional
     * @param int $itemid the itemid, optional
     * @param bool $includedeleted if log entries which are marked as deleted, should be included in the result
     * @return array array of records of the log table
     */
    public static function get_log_entries(string $component, int $contextid, int $userid = 0, int $itemid = 0,
            bool $includedeleted = true): array {
        global $DB;
        $params = [
                'component' => $component,
                'contextid' => $contextid,
        ];
        if (!empty($userid)) {
            $params['userid'] = $userid;
        }
        if (!empty($itemid)) {
            $params['itemid'] = $itemid;
        }
        if (empty($includedeleted)) {
            // The column 'deleted' is defined to have the value 0 by default, so we should be safe to use this as query param.
            $params['deleted'] = 0;
        }
        $records = $DB->get_records('local_ai_manager_request_log', $params, 'timecreated ASC');
        return !empty($records) ? $records : [];
    }

    /**
     * API function to mark log entries as deleted.
     *
     * @param string $component the component which has logged the records
     * @param int $contextid the contextid
     * @param int $userid the userid of the user, optional
     * @param int $itemid the itemid, optional
     * @return void
     */
    public static function mark_log_entries_as_deleted(string $component, int $contextid, int $userid = 0, int $itemid = 0): void {
        global $DB;
        $params = [
                'component' => $component,
                'contextid' => $contextid,
        ];
        if (!empty($userid)) {
            $params['userid'] = $userid;
        }
        if (!empty($itemid)) {
            $params['itemid'] = $itemid;
        }
        // We intentionally do this one by one despite maybe not being very efficient to avoid running into transaction size limit
        // on DB layer.
        $rs = $DB->get_recordset('local_ai_manager_request_log', $params, '', 'id, deleted');
        foreach ($rs as $record) {
            $record->deleted = 1;
            $DB->update_record('local_ai_manager_request_log', $record);
        }
        $rs->close();
    }

    /**
     * API function to check, if an itemid already exists.
     *
     * @param string $component the component to check
     * @param int $contextid the contextid to check
     * @param int $itemid the itemid that should be checked for existence
     * @return bool if the passed itemid in the context of the component and contextid already exists
     */
    public static function itemid_exists(string $component, int $contextid, int $itemid): bool {
        global $DB;
        return $DB->record_exists('local_ai_manager_request_log',
                [
                        'component' => $component,
                        'contextid' => $contextid,
                        'itemid' => $itemid,
                ]);
    }

    /**
     * API function to get the next unused itemid.
     *
     * @param string $component the component to retrieve the itemid for
     * @param int $contextid the contextid of the context to retrieve the itemid for
     * @return int the unused itemid
     */
    public static function get_next_free_itemid(string $component, int $contextid): int {
        global $DB;
        $sql = "SELECT MAX(itemid) as maxitemid FROM {local_ai_manager_request_log} "
                . "WHERE component = :component AND contextid = :contextid";
        $max =
                intval($DB->get_field_sql($sql, ['component' => $component, 'contextid' => $contextid]));
        return empty($max) ? 1 : $max + 1;
    }

    /**
     * API helper function to get the connector instance of a purpose
     *
     * @param string $purpose the purpose to get the connector instance for
     * @param ?int $userid the userid of the user to determine the correct tenant
     * @return base_instance the connector instance object
     */
    public static function get_connector_instance_by_purpose(string $purpose, ?int $userid = null): base_instance {
        global $USER;
        if (is_null($userid)) {
            $tenant = \core\di::get(tenant::class);
        } else {
            $user = \core_user::get_user($userid);
            $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
            $tenant = new tenant($user->{$tenantfield});
            \core\di::set(tenant::class, $tenant);
        }
        $userinfo = new userinfo(empty($userid) ? $USER->id : $userid);
        $factory = \core\di::get(\local_ai_manager\local\connector_factory::class);
        return $factory->get_connector_instance_by_purpose($purpose, $userinfo->get_role());
    }

    /**
     * API function to get all needed information about the AI configuration for a user.
     *
     * @param stdClass $user the user to retrieve the information for
     * @param ?string $tenant the tenant to retrieve the information for. If null, the current tenant will be used
     * @return array complex associative array containing all the needed configurations
     */
    public static function get_ai_config(stdClass $user, ?string $tenant = null): array {
        if (!is_null($tenant)) {
            $tenant = new tenant($tenant);
            \core\di::set(tenant::class, $tenant);
        }
        $configmanager = \core\di::get(\local_ai_manager\local\config_manager::class);
        $tenant = \core\di::get(tenant::class);
        $userinfo = new userinfo($user->id);

        $purposes = [];
        $purposeconfig = $configmanager->get_purpose_config($userinfo->get_role());
        $factory = \core\di::get(\local_ai_manager\local\connector_factory::class);
        foreach (base_purpose::get_all_purposes() as $purpose) {
            $purposeinstance = $factory->get_purpose_by_purpose_string($purpose);
            $userusage = new userusage($purposeinstance, $user->id);
            $purposes[] = [
                    'purpose' => $purpose,
                    'isconfigured' => !empty($purposeconfig[$purpose]),
                    'limitreached' => $userusage->get_currentusage() >=
                            $configmanager->get_max_requests($purposeinstance, $userinfo->get_role()),
                    'lockedforrole' => $configmanager->get_max_requests($purposeinstance, $userinfo->get_role()) === 0,
            ];
        }

        $tools = [];
        foreach (\local_ai_manager\plugininfo\aitool::get_enabled_plugins() as $toolname) {
            $tool['name'] = $toolname;
            $addurl = new moodle_url('/local/ai_manager/edit_instance.php',
                    [
                            'tenant' => $tenant->get_identifier(),
                            'returnurl' => (new moodle_url('/local/ai_manager/tenant_config.php',
                                    ['tenant' => $tenant->get_identifier()]))->out(),
                            'connectorname' => $toolname,
                    ]);
            $tool['addurl'] = $addurl->out(false);
            $tools[] = $tool;
        }
        // If the warning url is empty, we will not show a link.
        $aiwarningurl = get_config('local_ai_manager', 'aiwarningurl') ?: '';

        return [
                'tenantenabled' => $configmanager->is_tenant_enabled(),
                'userlocked' => $userinfo->is_locked(),
                'userconfirmed' => $userinfo->is_confirmed(),
                'role' => userinfo::get_role_as_string($userinfo->get_role()),
                'aiwarningurl' => $aiwarningurl,
                'purposes' => $purposes,
                'tools' => $tools,
        ];
    }
}
