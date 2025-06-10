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
use local_o365\oauth2\systemapiusertoken;
use local_o365\oauth2\token;
use local_o365\obj\o365user;
use local_o365\rest\unified;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * General purpose utility class.
 */
class utils {
    /**
     * Determine whether essential configuration has been completed.
     *
     * @return bool Whether the plugins are configured.
     */
    public static function is_configured() {
        return auth_oidc_is_setup_complete();
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
                $token = utils::get_app_or_system_token($graphresource, $clientdata, $httpclient);
                if ($token) {
                    return true;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get an app token if available or fall back to system API user token.
     *
     * @param string $tokenresource The desired resource.
     * @param clientdata $clientdata Client credentials.
     * @param httpclientinterface $httpclient An HTTP client.
     * @param bool $forcecreate
     * @param bool $throwexception
     * @return apptoken|systemapiusertoken|null An app or system token.
     * @throws Exception
     */
    public static function get_app_or_system_token(string $tokenresource, clientdata $clientdata, httpclientinterface $httpclient,
        bool $forcecreate = false, bool $throwexception = true) {
        $token = null;
        try {
            if (static::is_configured_apponlyaccess() === true) {
                $token = apptoken::instance(null, $tokenresource, $clientdata, $httpclient, $forcecreate);
            }
        } catch (Exception $e) {
            static::debug($e->getMessage(), __METHOD__ . ' (app)', $e);
        }

        if (empty($token)) {
            try {
                $token = systemapiusertoken::instance(null, $tokenresource, $clientdata, $httpclient);
            } catch (Exception $e) {
                static::debug($e->getMessage(), __METHOD__ . ' (system)', $e);
            }
        }

        if (!empty($token)) {
            return $token;
        } else {
            if ($throwexception) {
                throw new Exception('Could not get app or system token');
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
     * Determine whether app-only access is enabled.
     *
     * @return bool Enabled/disabled.
     */
    public static function is_enabled_apponlyaccess() {
        $apponlyenabled = get_config('local_o365', 'enableapponlyaccess');
        return (!empty($apponlyenabled)) ? true : false;
    }

    /**
     * Determine whether the app only access is configured.
     *
     * @return bool Whether the app only access is configured.
     */
    public static function is_configured_apponlyaccess() {
        // App only access requires unified api to be enabled.
        $apponlyenabled = static::is_enabled_apponlyaccess();
        if (empty($apponlyenabled)) {
            return false;
        }
        $aadtenant = get_config('local_o365', 'aadtenant');
        $aadtenantid = get_config('local_o365', 'aadtenantid');
        if (empty($aadtenant) && empty($aadtenantid)) {
            return false;
        }
        return true;
    }

    /**
     * Determine whether app-only access is both configured and active.
     *
     * @return bool Whether app-only access is active.
     */
    public static function is_active_apponlyaccess() : bool {
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
        $aadresource = unified::get_tokenresource();
        [$idsql, $idparams] = $DB->get_in_or_equal($userids);
        $sql = 'SELECT u.id as userid
                  FROM {user} u
             LEFT JOIN {local_o365_token} localtok ON localtok.user_id = u.id
             LEFT JOIN {auth_oidc_token} authtok ON authtok.tokenresource = ? AND authtok.userid = u.id
                 WHERE u.id ' . $idsql . '
                       AND (localtok.id IS NOT NULL OR authtok.id IS NOT NULL)';
        $params = [$aadresource];
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
            if ($val instanceof \moodle_exception) {
                $valinfo['debuginfo'] = $val->debuginfo;
                $valinfo['errorcode'] = $val->errorcode;
                $valinfo['module'] = $val->module;
            }
            return print_r($valinfo, true);
        } else {
            return print_r($val, true);
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
            $fullmessage = (!empty($where)) ? $where : 'Unknown function';
            $fullmessage .= ': '.$message;
            $fullmessage .= ' Data: '.static::tostring($debugdata);
            $event = api_call_failed::create(['other' => $fullmessage]);
            $event->trigger();
        }
    }

    /**
     * Construct an API client.
     *
     * @param int|null $userid
     * @return unified A constructed unified API client, or throw an error.
     */
    public static function get_api(int $userid = null) {
        $tokenresource = unified::get_tokenresource();
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        if (!empty($userid)) {
            $token = token::instance($userid, $tokenresource, $clientdata, $httpclient);
        } else {
            $token = static::get_app_or_system_token($tokenresource, $clientdata, $httpclient);
        }
        if (empty($token)) {
            throw new Exception('No token available for system user. Please run local_o365 health check.');
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
        set_config('multitenants', json_encode($additionaltenants), 'local_o365');

        // Cleanup legacy multi tenants configurations.
        $configuredlegacytenants = get_config('local_o365', 'legacymultitenants');
        if (!empty($configuredlegacytenants)) {
            $configuredlegacytenants = json_decode($configuredlegacytenants, true);
            if (is_array($configuredlegacytenants)) {
                $configuredlegacytenants = array_diff($configuredlegacytenants, $tenantdomainnames);
            }
            set_config('legacymultitenants', json_encode($configuredlegacytenants), 'local_o365');
        }

        // Update restrictions.
        $hostingtenant = get_config('local_o365', 'aadtenant');
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
                set_config('legacymultitenants', json_encode($legacyadditionaltenantdomains), 'local_o365');
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
            set_config('multitenants', json_encode($configuredtenants), 'local_o365');
        }

        // Update restrictions.
        $userrestrictions = get_config('auth_oidc', 'userrestrictions');
        $userrestrictions = (!empty($userrestrictions)) ? explode("\n", $userrestrictions) : [];
        foreach ($revokeddomains as $revokeddomain) {
            $regex = '@' . str_replace('.', '\.', $revokeddomain) . '$';
            $userrestrictions = array_diff($userrestrictions, [$regex]);
        }
        $userrestrictions = implode("\n", $userrestrictions);
        set_config('userrestrictions', $userrestrictions, 'auth_oidc');
    }

    /**
     * Delete an additional tenant from the legacy additional tenant settings.
     *
     * @param $tenant
     * @return bool|void
     */
    public static function deletelegacyadditionaltenant($tenant) {
        $o365config = get_config('local_o365');
        if (empty($o365config->legacymultitenants)) {
            return true;
        }
        $configuredlegacytenants = json_decode($o365config->legacymultitenants, true);
        if (!is_array($configuredlegacytenants)) {
            $configuredlegacytenants = [];
        }
        $configuredlegacytenants = array_diff($configuredlegacytenants, [$tenant]);
        set_config('legacymultitenants', json_encode($configuredlegacytenants), 'local_o365');
    }

    /**
     * Get the tenant for a user.
     *
     * @param int $userid The ID of the user.
     * @return string The tenant for the user. Empty string unless different from the host tenant.
     */
    public static function get_tenant_for_user(int $userid) : string {
        try {
            $clientdata = clientdata::instance_from_oidc();
            $httpclient = new httpclient();
            $tokenresource = unified::get_tokenresource();
            $token = token::instance($userid, $tokenresource, $clientdata, $httpclient);
            if (!empty($token)) {
                $apiclient = new unified($token, $httpclient);
                $tenant = $apiclient->get_default_domain_name_in_tenant();
                $tenant = clean_param($tenant, PARAM_TEXT);
                return ($tenant != get_config('local_o365', 'aadtenant')) ? $tenant : '';
            }
        } catch (Exception $e) {
            // Do nothing.
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
        } catch (Exception $e) {
            // Do nothing.
        }
        return '';
    }
}
