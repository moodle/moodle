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
 * General purpose utility class.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@noevation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365;

use auth_oidc\jwt;
use Exception;
use local_o365\event\api_call_failed;
use local_o365\oauth2\apptoken;
use local_o365\oauth2\clientdata;
use local_o365\oauth2\token;
use local_o365\obj\o365user;
use local_o365\rest\unified;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * General purpose utility class.
 */
class utils {
    /**
     * @var string RESOURCE_NOT_EXIST_ERROR
     */
    const RESOURCE_NOT_EXIST_ERROR = 'does not exist or one of its queried reference-property objects are not present';

    /**
     * Determine whether essential configuration has been completed.
     *
     * @return bool Whether the plugins are configured.
     */
    public static function is_configured() {
        $authoidcsetupcomplete = auth_oidc_is_setup_complete();

        if ($authoidcsetupcomplete) {
            $idptype = get_config('auth_oidc', 'idptype');
            if (in_array($idptype, [AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID, AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the Moodle site is connected to Microsoft 365 services using the integration.
     *
     * @return bool
     */
    public static function is_connected() {
        if (static::is_configured()) {
            $httpclient = new httpclient();
            $clientdata = clientdata::instance_from_oidc();
            $graphresource = unified::get_tokenresource();
            try {
                $token = static::get_application_token($graphresource, $clientdata, $httpclient);
                if ($token) {
                    return true;
                }
            } catch (moodle_exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get an application token if available.
     *
     * @param string $tokenresource The desired resource.
     * @param clientdata $clientdata Client credentials.
     * @param httpclientinterface $httpclient An HTTP client.
     * @param bool $forcecreate
     * @param bool $throwexception
     * @return bool|token|null A token, or null if none available.
     * @throws moodle_exception
     */
    public static function get_application_token(string $tokenresource, clientdata $clientdata, httpclientinterface $httpclient,
        bool $forcecreate = false, bool $throwexception = true) {
        $token = null;
        try {
            if (static::is_configured_apponlyaccess() === true) {
                $token = apptoken::instance(null, $tokenresource, $clientdata, $httpclient, $forcecreate);
            }
        } catch (moodle_exception $e) {
            static::debug($e->getMessage(), __METHOD__ . ' (app)', $e);
        }

        if (!empty($token)) {
            return $token;
        } else {
            if ($throwexception) {
                throw new moodle_exception('errorcannotgettoken', 'local_o365');
            } else {
                return $token;
            }
        }
    }

    /**
     * Get the tenant from an ID Token.
     *
     * @param jwt $idtoken The ID token.
     * @return string|null The tenant, or null is failure.
     */
    public static function get_tenant_from_idtoken(jwt $idtoken) {
        $iss = $idtoken->claim('iss');
        $parsediss = parse_url($iss);
        if (!empty($parsediss['path'])) {
            $tenant = trim($parsediss['path'], '/');
            if (!empty($tenant)) {
                return $tenant;
            }
        }
        return null;
    }

    /**
     * Determine whether the app only access is configured.
     *
     * @return bool Whether the app only access is configured.
     */
    public static function is_configured_apponlyaccess() {
        $entratenant = get_config('local_o365', 'entratenant');
        $entratenantid = get_config('local_o365', 'entratenantid');
        if (empty($entratenant) && empty($entratenantid)) {
            return false;
        }
        return true;
    }

    /**
     * Determine whether app-only access is both configured and active.
     *
     * @return bool Whether app-only access is active.
     */
    public static function is_active_apponlyaccess(): bool {
        return static::is_configured_apponlyaccess() === true && unified::is_configured() === true;
    }

    /**
     * Filters an array of userids to users that are currently connected to O365.
     *
     * @param array $userids The full array of userids.
     * @return array Array of userids that are o365 connected.
     */
    public static function limit_to_o365_users(array $userids) {
        global $DB;
        if (empty($userids)) {
            return [];
        }
        $tokenresource = unified::get_tokenresource();
        [$idsql, $idparams] = $DB->get_in_or_equal($userids);
        $sql = 'SELECT u.id as userid
                  FROM {user} u
             LEFT JOIN {local_o365_token} localtok ON localtok.user_id = u.id
             LEFT JOIN {auth_oidc_token} authtok ON authtok.tokenresource = ? AND authtok.userid = u.id
                 WHERE u.id ' . $idsql . '
                       AND (localtok.id IS NOT NULL OR authtok.id IS NOT NULL)';
        $params = [$tokenresource];
        $params = array_merge($params, $idparams);
        $records = $DB->get_recordset_sql($sql, $params);
        $return = [];
        foreach ($records as $record) {
            $return[$record->userid] = (int)$record->userid;
        }
        return array_values($return);
    }

    /**
     * Get the UPN of the connected Microsoft 365 account.
     *
     * @param int $userid The Moodle user id.
     * @return string|null The UPN of the connected Microsoft 365 account, or null if none found.
     */
    public static function get_o365_upn($userid) {
        $o365user = o365user::instance_from_muserid($userid);
        return (!empty($o365user)) ? $o365user->upn : null;
    }

    /**
     * Get the object ID of the connected Microsoft 365 account.
     *
     * @param int $userid
     * @return null
     */
    public static function get_o365_userid($userid) {
        $o365user = o365user::instance_from_muserid($userid);
        return (!empty($o365user)) ? $o365user->objectid : null;
    }

    /**
     * Determine if a user is connected to Microsoft 365.
     *
     * @param int $userid The user's ID.
     * @return bool Whether they are connected (true) or not (false).
     */
    public static function is_o365_connected($userid) {
        $o365user = o365user::instance_from_muserid($userid);
        return (!empty($o365user)) ? true : false;
    }

    /**
     * Convert any value into a debuggable string.
     *
     * @param mixed $val The variable to convert.
     * @return string A string representation.
     */
    public static function tostring($val) {
        if (is_scalar($val)) {
            if (is_bool($val)) {
                return '(bool)'.(string)(int)$val;
            } else {
                return '('.gettype($val).')'.(string)$val;
            }
        } else if (is_null($val)) {
            return '(null)';
        } else if ($val instanceof Exception) {
            $valinfo = [
                'file' => $val->getFile(),
                'line' => $val->getLine(),
                'message' => $val->getMessage(),
            ];
            if ($val instanceof moodle_exception) {
                $valinfo['debuginfo'] = $val->debuginfo;
                $valinfo['errorcode'] = $val->errorcode;
                $valinfo['module'] = $val->module;
            }
            return json_encode($valinfo, JSON_PRETTY_PRINT);
        } else {
            return json_encode($val, JSON_PRETTY_PRINT);
        }
    }

    /**
     * Record a debug message.
     *
     * @param string $message The debug message to log.
     * @param string $where
     * @param object $debugdata
     */
    public static function debug($message, $where = '', $debugdata = null) {
        $debugmode = (bool)get_config('local_o365', 'debugmode');
        if ($debugmode === true) {
            $backtrace = debug_backtrace();
            $otherdata = [
                'other' => [
                    'message' => $message,
                    'where' => $where,
                    'debugdata' => $debugdata,
                    'backtrace' => $backtrace,
                ],
            ];
            $event = api_call_failed::create($otherdata);
            $event->trigger();
        }
    }

    /**
     * Construct an API client.
     *
     * @param int|null $userid
     * @return unified A constructed unified API client, or throw an error.
     * @throws moodle_exception
     */
    public static function get_api(?int $userid = null) {
        $tokenresource = unified::get_tokenresource();
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        if (!empty($userid)) {
            $token = token::instance($userid, $tokenresource, $clientdata, $httpclient);
        } else {
            $token = static::get_application_token($tokenresource, $clientdata, $httpclient);
        }
        if (empty($token)) {
            throw new moodle_exception('errornotokenforsysmemuser', 'local_o365');
        }

        $apiclient = new unified($token, $httpclient);

        return $apiclient;
    }

    /**
     * Enable an additional Microsoft 365 tenant.
     *
     * @param string $tenantid
     * @param array $tenantdomainnames
     */
    public static function enableadditionaltenant(string $tenantid, array $tenantdomainnames) {
        static::updatemultitenantssettings();

        $multitenantsconfig = get_config('local_o365', 'multitenants');
        $additionaltenants = json_decode($multitenantsconfig, true);
        if (!is_array($additionaltenants)) {
            $additionaltenants = [];
        }

        if (!array_key_exists($tenantid, $additionaltenants)) {
            $additionaltenants[$tenantid] = $tenantdomainnames;
        }
        $additionaltenantsencoded = json_encode($additionaltenants);
        $existingmultitenantssetting = get_config('local_o365', 'multitenants');
        if ($existingmultitenantssetting != $additionaltenantsencoded) {
            add_to_config_log('multitenants', $existingmultitenantssetting, $additionaltenantsencoded, 'local_o365');
        }
        set_config('multitenants', $additionaltenantsencoded, 'local_o365');

        // Cleanup legacy multi tenants configurations.
        $configuredlegacytenants = get_config('local_o365', 'legacymultitenants');
        $originalconfiguredlegacytenants = $configuredlegacytenants;
        if (!empty($configuredlegacytenants)) {
            $configuredlegacytenants = json_decode($configuredlegacytenants, true);
            if (is_array($configuredlegacytenants)) {
                $configuredlegacytenants = array_diff($configuredlegacytenants, $tenantdomainnames);
            }
            if ($originalconfiguredlegacytenants != json_encode($configuredlegacytenants)) {
                add_to_config_log('legacymultitenants', $originalconfiguredlegacytenants, json_encode($configuredlegacytenants),
                    'local_o365');
            }
            set_config('legacymultitenants', json_encode($configuredlegacytenants), 'local_o365');
        }

        // Update restrictions.
        $hostingtenant = get_config('local_o365', 'entratenant');
        $newrestrictions = ['@' . str_replace('.', '\.', $hostingtenant) . '$'];
        foreach ($additionaltenants as $configuredtenantdomains) {
            foreach ($configuredtenantdomains as $configuredtenantdomain) {
                $newrestrictions[] = '@' . str_replace('.', '\.', $configuredtenantdomain) . '$';
            }
        }
        $userrestrictions = get_config('auth_oidc', 'userrestrictions');
        $userrestrictions = explode("\n", $userrestrictions);
        $userrestrictions = array_merge($userrestrictions, $newrestrictions);
        $userrestrictions = array_unique($userrestrictions);
        $userrestrictions = implode("\n", $userrestrictions);
        $existinguserrestrictionssetting = get_config('auth_oidc', 'userrestrictions');
        if ($existinguserrestrictionssetting != $userrestrictions) {
            add_to_config_log('userrestrictions', $existinguserrestrictionssetting, $userrestrictions, 'auth_oidc');
        }
        set_config('userrestrictions', $userrestrictions, 'auth_oidc');
    }

    /**
     * Update multitenants configuration settings for the March 2022 upgrade.
     *  - The old multitenants configuration contains a json encoded array of the initial domain names of each additional tenant,
     * e.g. ["contoso.onmicrosoft.com"].
     *  - The new multitenants configuration contains a json encoded array having tenant ID as key, and an array of verified domains
     * as value. e.g. {"00000000-0000-0000-0000-000000000000":["contoso.onmicrosoft.com","contoso.com"]}.
     *
     * @return void
     */
    public static function updatemultitenantssettings() {
        $multitenantsconfig = get_config('local_o365', 'multitenants');
        $additionaltenantdomains = [];

        $legacymultitenantsconfig = get_config('local_o365', 'legacymultitenants');
        $legacyadditionaltenantdomains = json_decode($legacymultitenantsconfig, true);
        if (!is_array($legacyadditionaltenantdomains)) {
            $legacyadditionaltenantdomains = [];
        }

        if (!empty($multitenantsconfig)) {
            $multitenantsconfig = json_decode($multitenantsconfig, true);
            if (is_array($multitenantsconfig) && count($multitenantsconfig) != 0) {
                if (array_keys($multitenantsconfig)[0] != '0') {
                    // Configuration array keys are not numbers - already migrated.
                    return true;
                }
                foreach ($multitenantsconfig as $currenttenantid => $currenttenantdomainnames) {
                    if (is_int($currenttenantid) || strlen($currenttenantid) != 36) {
                        // Not real tenant ID, this contains settings in old format.
                        if (is_array($currenttenantdomainnames)) {
                            $legacyadditionaltenantdomains = array_merge($legacyadditionaltenantdomains, $currenttenantdomainnames);
                        } else {
                            $legacyadditionaltenantdomains = array_merge($legacyadditionaltenantdomains,
                                [$currenttenantdomainnames]);
                        }
                    } else {
                        $additionaltenantdomains[$currenttenantid] = $currenttenantdomainnames;
                    }
                }
                $existinglegacymultitenantssetting = get_config('local_o365', 'legacymultitenants');
                if ($existinglegacymultitenantssetting != json_encode($legacyadditionaltenantdomains)) {
                    add_to_config_log('legacymultitenants', $existinglegacymultitenantssetting,
                        json_encode($legacyadditionaltenantdomains), 'local_o365');
                }
                set_config('legacymultitenants', json_encode($legacyadditionaltenantdomains), 'local_o365');
            }

            $existingmultitenantssetting = get_config('local_o365', 'multitenants');
            if ($existingmultitenantssetting != json_encode($additionaltenantdomains)) {
                add_to_config_log('multitenants', $existingmultitenantssetting, json_encode($additionaltenantdomains),
                    'local_o365');
            }
            set_config('multitenants', json_encode($additionaltenantdomains), 'local_o365');
        }
    }

    /**
     * Disable an additional Microsoft 365 tenant.
     *
     * @param string $tenantid
     * @return bool|void
     */
    public static function disableadditionaltenant(string $tenantid) {
        $o365config = get_config('local_o365');
        if (empty($o365config->multitenants)) {
            return true;
        }
        $configuredtenants = json_decode($o365config->multitenants, true);
        if (!is_array($configuredtenants)) {
            $configuredtenants = [];
        }

        $revokeddomains = [];
        if (array_key_exists($tenantid, $configuredtenants)) {
            $revokeddomains = $configuredtenants[$tenantid];
            unset($configuredtenants[$tenantid]);
            $existingmultitenantssetting = get_config('local_o365', 'multitenants');
            if ($existingmultitenantssetting != json_encode($configuredtenants)) {
                add_to_config_log('multitenants', $existingmultitenantssetting, json_encode($configuredtenants), 'local_o365');
            }
            set_config('multitenants', json_encode($configuredtenants), 'local_o365');
        }

        // Update restrictions.
        $userrestrictions = get_config('auth_oidc', 'userrestrictions');
        $originaluserrestrictions = $userrestrictions;
        $userrestrictions = (!empty($userrestrictions)) ? explode("\n", $userrestrictions) : [];
        foreach ($revokeddomains as $revokeddomain) {
            $regex = '@' . str_replace('.', '\.', $revokeddomain) . '$';
            $userrestrictions = array_diff($userrestrictions, [$regex]);
        }
        $userrestrictions = implode("\n", $userrestrictions);
        if ($originaluserrestrictions != $userrestrictions) {
            add_to_config_log('userrestrictions', $originaluserrestrictions, $userrestrictions, 'auth_oidc');
        }
        set_config('userrestrictions', $userrestrictions, 'auth_oidc');
    }

    /**
     * Delete an additional tenant from the legacy additional tenant settings.
     *
     * @param string $tenant
     * @return bool|void
     */
    public static function deletelegacyadditionaltenant(string $tenant) {
        $o365config = get_config('local_o365');
        if (empty($o365config->legacymultitenants)) {
            return true;
        }
        $configuredlegacytenants = json_decode($o365config->legacymultitenants, true);
        if (!is_array($configuredlegacytenants)) {
            $configuredlegacytenants = [];
        }
        $configuredlegacytenants = array_diff($configuredlegacytenants, [$tenant]);
        $existinglegacymultitenantssetting = get_config('local_o365', 'legacymultitenants');
        if ($existinglegacymultitenantssetting != json_encode($configuredlegacytenants)) {
            add_to_config_log('legacymultitenants', $existinglegacymultitenantssetting, json_encode($configuredlegacytenants),
                'local_o365');
        }
        set_config('legacymultitenants', json_encode($configuredlegacytenants), 'local_o365');
    }

    /**
     * Get the tenant for a user.
     *
     * @param int $userid The ID of the user.
     * @return string The tenant for the user. Empty string unless different from the host tenant.
     */
    public static function get_tenant_for_user(int $userid): string {
        try {
            $clientdata = clientdata::instance_from_oidc();
            $httpclient = new httpclient();
            $tokenresource = unified::get_tokenresource();
            $token = token::instance($userid, $tokenresource, $clientdata, $httpclient);
            if (!empty($token)) {
                $apiclient = new unified($token, $httpclient);
                $tenant = $apiclient->get_default_domain_name_in_tenant();
                $tenant = clean_param($tenant, PARAM_TEXT);
                return ($tenant != get_config('local_o365', 'entratenant')) ? $tenant : '';
            }
        } catch (moodle_exception $e) {
            return '';
        }
        return '';
    }

    /**
     * Get the OneDrive for Business URL for a user.
     *
     * @param int $userid The ID of the user.
     * @return string The OneDrive for Business URL for the user.
     */
    public static function get_odburl_for_user($userid) {
        try {
            $clientdata = clientdata::instance_from_oidc();
            $httpclient = new httpclient();
            $tokenresource = unified::get_tokenresource();
            $token = token::instance($userid, $tokenresource, $clientdata, $httpclient);
            if (!empty($token)) {
                $apiclient = new unified($token, $httpclient);
                $tenant = $apiclient->get_odburl();
                $tenant = clean_param($tenant, PARAM_TEXT);
                return ($tenant != get_config('local_o365', 'odburl')) ? $tenant : '';
            }
        } catch (moodle_exception $e) {
            return '';
        }
        return '';
    }

    /**
     * Get the cached Microsoft account oid for the Moodle user with the given ID.
     *
     * @param int $userid The ID of the user.
     * @return string The Microsoft account uid for the user.
     */
    public static function get_microsoft_account_oid_by_user_id(int $userid) {
        global $DB;

        $oid = $DB->get_field('local_o365_objects', 'objectid', ['moodleid' => $userid, 'type' => 'user']);

        return $oid;
    }

    /**
     * Get the list of connected users with their Moodle user ID and Microsoft 365 user ID.
     *
     * @return array
     */
    public static function get_connected_users(): array {
        global $DB;

        $connectedusers = [];

        $userobjectrecords = $DB->get_records('local_o365_objects', ['type' => 'user']);
        foreach ($userobjectrecords as $userobjectrecord) {
            $connectedusers[$userobjectrecord->moodleid] = $userobjectrecord->objectid;
        }

        return $connectedusers;
    }

    /**
     * Update Groups cache.
     *
     * @param unified $graphclient
     * @param int $baselevel
     * @return bool
     */
    public static function update_groups_cache(unified $graphclient, int $baselevel = 0): bool {
        global $DB;

        static::mtrace("Update groups cache.", $baselevel);

        try {
            $grouplist = $graphclient->get_groups();
        } catch (moodle_exception $e) {
            static::mtrace("Failed to fetch groups. Error: " . $e->getMessage(), $baselevel + 1);

            return false;
        }

        $existingcacherecords = $DB->get_records('local_o365_groups_cache');
        $existinggroupsbyoid = [];
        $existingnotfoundgroupsbyoid = [];
        foreach ($existingcacherecords as $existingcacherecord) {
            if ($existingcacherecord->not_found_since) {
                $existingnotfoundgroupsbyoid[$existingcacherecord->objectid] = $existingcacherecord;
            } else {
                $existinggroupsbyoid[$existingcacherecord->objectid] = $existingcacherecord;
            }
        }

        foreach ($grouplist as $group) {
            if (array_key_exists($group['id'], $existingnotfoundgroupsbyoid)) {
                $cacherecord = $existingnotfoundgroupsbyoid[$group['id']];
                $cacherecord->name = $group['displayName'];
                $cacherecord->description = $group['description'];
                $cacherecord->not_found_since = 0;
                $DB->update_record('local_o365_groups_cache', $cacherecord);
                unset($existingnotfoundgroupsbyoid[$group['id']]);
                static::mtrace("Unset not found flag for group {$group['id']}.", $baselevel + 1);
            } else if (array_key_exists($group['id'], $existinggroupsbyoid)) {
                $cacherecord = $existinggroupsbyoid[$group['id']];
                if ($cacherecord->name != $group['displayName'] || $cacherecord->description != $group['description']) {
                    $cacherecord->name = $group['displayName'];
                    $cacherecord->description = $group['description'];
                    $DB->update_record('local_o365_groups_cache', $cacherecord);
                    static::mtrace("Updated group ID {$group['id']} in cache.", $baselevel + 1);
                } else {
                    static::mtrace("Group ID {$group['id']} in cache is up to date.", $baselevel + 1);
                }
                unset($existinggroupsbyoid[$group['id']]);
            } else {
                $cacherecord = new stdClass();
                $cacherecord->objectid = $group['id'];
                $cacherecord->name = $group['displayName'];
                $cacherecord->description = $group['description'];
                $DB->insert_record('local_o365_groups_cache', $cacherecord);
                static::mtrace("Added group ID {$group['id']} to cache.", $baselevel + 1);
            }
        }

        foreach ($existinggroupsbyoid as $oldcacherecord) {
            $oldcacherecord->not_found_since = time();
            $DB->update_record('local_o365_groups_cache', $oldcacherecord);
            static::mtrace("Marked group {$oldcacherecord->objectid} as not found in the cache.", $baselevel + 1);
        }

        static::mtrace("Finished updating groups cache.", $baselevel);
        static::mtrace("", $baselevel);

        return true;
    }

    /**
     * Clean up non-existing groups from the database.
     *
     * @param int $baselevel
     * @return void
     */
    public static function clean_up_not_found_groups(int $baselevel = 1): void {
        global $DB;

        static::mtrace('Clean up non-existing groups from database', $baselevel);

        $cutofftime = strtotime('-5 minutes');
        $sql = "SELECT *
                  FROM {local_o365_groups_cache}
                 WHERE not_found_since != 0
                   AND not_found_since < :cutofftime";
        $records = $DB->get_records_sql($sql, ['cutofftime' => $cutofftime]);

        foreach ($records as $record) {
            $DB->delete_records('local_o365_groups_cache', ['objectid' => $record->objectid]);
            $DB->delete_records('local_o365_objects', ['objectid' => $record->objectid]);
            $DB->delete_records('local_o365_teams_cache', ['objectid' => $record->objectid]);
            static::mtrace('Deleted non-existing group ' . $record->objectid . ' from groups cache.', $baselevel + 1);
        }

        static::mtrace('Finished cleaning up non-existing groups from database.', $baselevel);
        static::mtrace('', $baselevel);
    }

    /**
     * Print a message to the debugging console.
     *
     * @param string $message
     * @param int $level
     * @param string $eol
     * @return void
     */
    public static function mtrace(string $message, int $level = 0, string $eol = "\n"): void {
        if ($level) {
            $message = str_repeat('...', $level) . ' ' . $message;
        }
        mtrace($message, $eol);
    }

    /**
     * Extract GUID from error message.
     *
     * @param string $errormessage
     * @return string|null
     */
    public static function extract_guid_from_error_message(string $errormessage): ?string {
        $pattern = '/[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}/';
        preg_match($pattern, $errormessage, $matches);
        return $matches[0] ?? null;
    }
}
