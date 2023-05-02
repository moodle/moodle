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
 * Manage all calls to the Microsoft Graph API.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\rest;

use coding_exception;
use core_date;
use core_text;
use DateTime;
use Exception;
use local_o365\oauth2\clientdata;
use local_o365\obj\o365user;
use local_o365\utils;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Client for unified Microsoft 365 API.
 */
class unified extends o365api {
    /**
     * @var string The general API area of the class.
     */
    public $apiarea = 'graph';

    /**
     * Determine if the API client is configured.
     *
     * @return bool Whether the API client is configured.
     */
    public static function is_configured() : bool {
        // Since legacy APIs are removed, unified is always configured.
        return true;
    }

    /**
     * Get the API client's oauth2 resource.
     *
     * @return string The resource for oauth2 tokens.
     */
    public static function get_tokenresource() : string {
        $oidcresource = get_config('auth_oidc', 'oidcresource');
        if (!empty($oidcresource)) {
            return $oidcresource;
        } else {
            return (static::use_chinese_api() === true) ? 'https://microsoftgraph.chinacloudapi.cn' : 'https://graph.microsoft.com';
        }
    }

    /**
     * Get the base URI that API calls should be sent to.
     *
     * @return string|bool The URI to send API calls to, or false if a precondition failed.
     */
    public function get_apiuri() {
        $oidcresource = get_config('auth_oidc', 'oidcresource');
        if (!empty($oidcresource)) {
            return $oidcresource;
        } else {
            return (static::use_chinese_api() === true) ? 'https://microsoftgraph.chinacloudapi.cn' : 'https://graph.microsoft.com';
        }
    }

    /**
     * Generate an api area.
     *
     * @param string $apimethod The API method being called.
     * @return string a simplified api area string.
     */
    protected function generate_apiarea(string $apimethod) : string {
        $apimethod = explode('/', $apimethod);
        foreach ($apimethod as $apicomponent) {
            $validareas = ['applications', 'groups', 'calendars', 'events', 'trendingaround', 'users'];
            $apicomponent = strtolower($apicomponent);
            $apicomponent = explode('?', $apicomponent);
            $apicomponent = reset($apicomponent);
            if (in_array($apicomponent, $validareas)) {
                return $apicomponent;
            }
        }
        return 'graph';
    }

    /**
     * Make an API call.
     *
     * @param string $httpmethod The HTTP method to use. get/post/patch/merge/delete.
     * @param string $apimethod The API endpoint/method to call.
     * @param string $params Additional parameters to include.
     * @param array $options Additional options for the request.
     * @return string|array The result of the API call.
     */
    public function betaapicall(string $httpmethod, string $apimethod, string $params = '', array $options = []) {
        if ($apimethod[0] !== '/') {
            $apimethod = '/' . $apimethod;
        }
        $apimethod = '/beta' . $apimethod;
        if (empty($options['apiarea'])) {
            $options['apiarea'] = $this->generate_apiarea($apimethod);
        }
        return parent::apicall($httpmethod, $apimethod, $params, $options);
    }

    /**
     * Make an API call.
     *
     * @param string $httpmethod The HTTP method to use. get/post/patch/merge/delete.
     * @param string $apimethod The API endpoint/method to call.
     * @param string $params Additional parameters to include.
     * @param array $options Additional options for the request.
     * @return string|array The result of the API call.
     */
    public function apicall($httpmethod, $apimethod, $params = '', $options = []) {
        if ($apimethod[0] !== '/') {
            $apimethod = '/' . $apimethod;
        }
        if (empty($options['apiarea'])) {
            $options['apiarea'] = $this->generate_apiarea($apimethod);
        }
        $apimethod = '/v1.0' . $apimethod;
        return parent::apicall($httpmethod, $apimethod, $params, $options);
    }

    /**
     * Test a tenant value.
     *
     * @param string $tenant A tenant string to test.
     * @return bool True if tenant succeeded, false if not.
     */
    public function test_tenant(string $tenant) : bool {
        if (!is_string($tenant)) {
            throw new coding_exception('tenant value must be a string');
        }
        $oidcconfig = get_config('auth_oidc');
        $appinfo = $this->get_application_info();
        if (isset($appinfo['value']) && isset($appinfo['value'][0]['id'])) {
            return $appinfo['value'][0]['id'] === $oidcconfig->clientid;
        }
        return false;
    }

    /**
     * Get the name of the default domain in the tenant associated with the current account.
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_default_domain_name_in_tenant() : string {
        $response = $this->apicall('get', '/domains');
        $response = $this->process_apicall_response($response, ['value' => null]);
        foreach ($response['value'] as $domain) {
            if (!empty($domain['isDefault']) && isset($domain['id'])) {
                return $domain['id'];
            }
        }
        throw new moodle_exception('erroracpapcantgettenant', 'local_o365');
    }

    /**
     * Get the names of the all domains in the tenant associated with the current account, with the default domain being the first.
     *
     * @return array
     * @throws \moodle_exception
     */
    public function get_all_domain_names_in_tenant() {
        $response = $this->apicall('get', '/domains');
        $response = $this->process_apicall_response($response, ['value' => null]);
        $defaultdomainname = '';
        $domainnames = [];

        foreach ($response['value'] as $domain) {
            if (isset($domain['id'])) {
                if (!empty($domain['isVerified'])) {
                    if (!empty($domain['isDefault'])) {
                        $defaultdomainname = $domain['id'];
                    } else {
                        $domainnames[] = $domain['id'];
                    }
                }
            }

        }

        array_unshift($domainnames, $defaultdomainname);

        return $domainnames;
    }

    /**
     * Get the OneDrive URL associated with the current account.
     *
     * @return string The OneDrive URL string.
     */
    public function get_odburl() : string {
        $tenant = $this->get_default_domain_name_in_tenant();
        $suffix = '.onmicrosoft.com';
        $sufflen = strlen($suffix);
        if (substr($tenant, -$sufflen) === $suffix) {
            $prefix = substr($tenant, 0, -$sufflen);
            return $prefix . '-my.sharepoint.com';
        }
        throw new moodle_exception('erroracpcantgettenant', 'local_o365');
    }

    /**
     * Validate that a given url is a valid OneDrive for Business SharePoint URL.
     *
     * @param string $tokenresource Uncleaned, unvalidated URL to check.
     * @param clientdata $clientdata oAuth2 Credentials
     * @return bool Whether the received resource is valid or not.
     */
    public function validate_resource(string $tokenresource, clientdata $clientdata) : bool {
        $cleanresource = clean_param($tokenresource, PARAM_URL);
        if ($cleanresource !== $tokenresource) {
            return false;
        }
        $fullcleanresource = 'https://' . $cleanresource;
        $token = utils::get_app_or_system_token($fullcleanresource, $clientdata, $this->httpclient);
        return !empty($token);
    }

    /**
     * Assign a user to an Azure app.
     *
     * @param int $muserid
     * @param string $userobjectid
     * @param string $appobjectid
     * @return string|null
     */
    public function assign_user(int $muserid, string $userobjectid, string $appobjectid) : ?string {
        global $DB;
        $record = $DB->get_record('local_o365_appassign', ['muserid' => $muserid]);
        if (empty($record) || $record->assigned == 0) {
            $roleid = '00000000-0000-0000-0000-000000000000';
            $endpoint = '/users/' . $userobjectid . '/appRoleAssignments/';
            $params = ['id' => $roleid, 'resourceId' => $appobjectid, 'principalId' => $userobjectid,];
            $response = $this->betaapicall('post', $endpoint, json_encode($params));
            if (empty($record)) {
                $record = new stdClass();
                $record->muserid = $muserid;
                $record->assigned = 1;
                $DB->insert_record('local_o365_appassign', $record);
            } else {
                $record->assigned = 1;
                $DB->update_record('local_o365_appassign', $record);
            }
            return $response;
        }
        return null;
    }

    /**
     * Get a list of groups.
     *
     * @param string $skiptoken Skip token.
     * @return array List of groups.
     */
    public function get_groups(string $skiptoken = '') : array {
        $endpoint = '/groups';
        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }
        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Create a Microsoft 365 group using the details provided.
     *
     * @param string $name
     * @param string|null $mailnickname
     * @param array|null $extra
     * @return array|null
     * @throws moodle_exception
     */
    public function create_group(string $name, string $mailnickname = null, array $extra = null) : ?array {
        if (empty($mailnickname)) {
            $mailnickname = $name;
        }

        if (!empty($mailnickname)) {
            $mailnickname = core_text::strtolower($mailnickname);
            $mailnickname = preg_replace('/[^a-z0-9_]+/iu', '', $mailnickname);
            $mailnickname = trim($mailnickname);
        }

        if (empty($mailnickname)) {
            // Cannot generate a good mailnickname because there's nothing but non-alphanum chars to work with. So generate one.
            $mailnickname = 'group' . uniqid();
        }

        $groupdata = [
            'groupTypes' => ['Unified'],
            'displayName' => $name,
            'mailEnabled' => false,
            'securityEnabled' => false,
            'mailNickname' => $mailnickname,
            'visibility' => 'Private',
            'resourceBehaviorOptions' => ['HideGroupInOutlook', 'WelcomeEmailDisabled'],
        ];

        if (!empty($extra)) {
            // Set extra parameters.
            foreach ($extra as $name => $value) {
                $groupdata[$name] = $value;
            }
        }

        // Description cannot be set and empty.
        if (empty($groupdata['description'])) {
            unset($groupdata['description']);
        }

        $response = $this->apicall('post', '/groups', json_encode($groupdata));
        $expectedparams = ['id' => null];
        try {
            $response = $this->process_apicall_response($response, $expectedparams);
        } catch (Exception $e) {
            $expectedexception = 'Another object with the same value for property mailNickname already exists.';
            if ($e->a == $expectedexception) {
                $mailnickname .= '_ ' . sprintf('%04d', random_int(0, 9999));
                return $this->create_group($name, $mailnickname, $extra);
            } else {
                utils::debug($e->getMessage(), __METHOD__, $e);
                throw $e;
            }
        }

        return $response;
    }

    /**
     * Update a group.
     *
     * @param array $groupdata Array containing parameters for update.
     * @return string Null string on success, json string on failure.
     */
    public function update_group(array $groupdata) : string {
        // Check for required parameters.
        if (empty($groupdata['id'])) {
            throw new moodle_exception('invalidgroupdata', 'local_o365');
        }
        if (!isset($groupdata['mailEnabled'])) {
            $groupdata['mailEnabled'] = false;
        }
        if (!isset($groupdata['securityEnabled'])) {
            $groupdata['securityEnabled'] = false;
        }
        if (!isset($groupdata['groupTypes'])) {
            $groupdata['groupTypes'] = ['Unified'];
        }

        // Description cannot be empty.
        if (empty($groupdata['description'])) {
            unset($groupdata['description']);
        }
        $response = $this->apicall('patch', '/groups/' . $groupdata['id'], json_encode($groupdata));

        return $this->process_apicall_response($response);
    }

    /**
     * Get group info.
     *
     * @param string $objectid The object ID of the group.
     * @return array Array of returned o365 group data.
     */
    public function get_group(string $objectid) : array {
        $response = $this->apicall('get', '/groups/' . $objectid);
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get group urls.
     *
     * @param string $objectid The object ID of the group.
     * @return array Array of returned o365 group urls, null on no group data found.
     */
    public function get_group_urls(string $objectid) : ?array {
        $group = $this->get_group($objectid);
        if (empty($group['mailNickname'])) {
            return null;
        }
        $config = get_config('local_o365');
        $url = preg_replace("/-my.sharepoint.com/", ".sharepoint.com", $config->odburl);
        // First time visiting the onedrive or notebook urls will result in a "please wait while we provision onedrive" message.
        $o365urls = [
            'onedrive' => 'https://' . $url . '/_layouts/groupstatus.aspx?id=' . $objectid . '&target=documents',
            'notebook' => 'https://' . $url . '/_layouts/groupstatus.aspx?id=' . $objectid . '&target=notebook',
            'conversations' => 'https://outlook.office.com/owa/?path=/group/' . $group['mail'] . '/mail',
            'calendar' => 'https://outlook.office365.com/owa/?path=/group/' . $group['mail'] . '/calendar',
        ];
        try {
            [$rawteam, $teamurl, $lockstatus] = $this->get_team($objectid);
            if ($teamurl) {
                $o365urls['team'] = $teamurl;
            }
        } catch (Exception $e) {
            // Do nothing.
        }
        return $o365urls;
    }

    /**
     * Return an array containing the team with the given object ID, along with its URL and lcoked status.
     *
     * @param string $objectid
     * @return array
     */
    public function get_team(string $objectid) {
        $response = $this->apicall('get', '/teams/' . $objectid);
        $response = $this->process_apicall_response($response);

        if (array_key_exists('webUrl', $response) && $response['webUrl']) {
            $teamsurl = $response['webUrl'];
        } else {
            $teamsurl = 'https://teams.microsoft.com';
        }

        $lockstatus = TEAM_LOCKED_STATUS_UNKNOWN;
        if (array_key_exists('isMembershipLimitedToOwners', $response)) {
            if ($response['isMembershipLimitedToOwners']) {
                $lockstatus = TEAM_LOCKED;
            } else {
                $lockstatus = TEAM_UNLOCKED;
            }
        }

        return [$response, $teamsurl, $lockstatus];
    }

    /**
     * Get a group by its displayName
     *
     * @param string $name The group name,
     * @return array Array of group information, or null if group not found.
     */
    public function get_group_by_name(string $name) : ?array {
        $response = $this->apicall('get', '/groups?$filter=displayName' . rawurlencode(' eq \'' . $name . '\''));
        $expectedparams = ['value' => null];
        $groups = $this->process_apicall_response($response, $expectedparams);
        return (isset($groups['value'][0])) ? $groups['value'][0] : null;
    }

    /**
     * Delete a group.
     *
     * @param string $objectid The object ID of the group.
     * @return bool|string True if group successfully deleted, otherwise returned string (may contain error info, etc).
     */
    public function delete_group(string $objectid) {
        if (empty($objectid)) {
            return null;
        }
        $response = $this->apicall('delete', '/groups/' . $objectid);
        return ($response === '') ? true : $response;
    }

    /**
     * Get a list of recently deleted groups.
     *
     * @param string $skiptoken
     * @return array Array of returned information.
     */
    public function list_deleted_groups(string $skiptoken = '') : array {
        $endpoint = '/directory/deleteditems/Microsoft.Graph.Group';

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->betaapicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Restore a recently deleted group.
     *
     * @param string $objectid The Object ID of the group to be restored.
     * @return array Array of returned information.
     */
    public function restore_deleted_group(string $objectid) : array {
        $response = $this->betaapicall('post', '/directory/deleteditems/' . $objectid . '/restore');
        return $this->process_apicall_response($response);
    }

    /**
     * Get a list of group members.
     *
     * @param string $groupobjectid The object ID of the group.
     * @param string $skiptoken
     * @return array Array of returned members.
     */
    public function get_group_members(string $groupobjectid, string $skiptoken = '') : array {
        $endpoint = '/groups/' . $groupobjectid . '/members';
        $odataqueries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];

        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a list of group owners.
     *
     * @param string $groupobjectid The object ID of the group.
     * @param string $skiptoken
     * @return array|null
     */
    public function get_group_owners(string $groupobjectid, string $skiptoken = '') : ?array {
        $endpoint = '/groups/' . $groupobjectid . '/owners';

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];

        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Return the list of files in a group.
     *
     * @param string $groupid
     * @param string $parentid The parent id to use.
     * @param string $skiptoken
     * @return array|null Returned response, or null if error.
     */
    public function get_group_files(string $groupid, string $parentid = '', string $skiptoken = '') : ?array {
        if (!empty($parentid) && $parentid !== '/') {
            $endpoint = "/groups/$groupid/drive/items/$parentid/children";
        } else {
            $endpoint = "/groups/$groupid/drive/root/children";
        }

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a file's metadata by its file id.
     *
     * @param string $groupid
     * @param string $fileid The file's ID.
     * @return array|null The file's content.
     */
    public function get_group_file_metadata(string $groupid, string $fileid) : ?array {
        $response = $this->apicall('get', "/groups/$groupid/drive/items/$fileid");
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Create a readonly sharing link for a group file.
     *
     * @param string $groupid
     * @param string $fileid OneDrive file id.
     * @return string Sharing link url.
     */
    public function get_group_file_sharing_link(string $groupid, string $fileid) : string {
        $params = ['type' => 'view', 'scope' => 'organization'];
        $apiresponse = $this->apicall('post', "/groups/$groupid/drive/items/$fileid/createLink", json_encode($params));
        $response = $this->process_apicall_response($apiresponse);
        return $response['link']['webUrl'];
    }

    /**
     * Get a file's content by it's file id.
     *
     * @param string $groupid
     * @param string $fileid The file's ID.
     * @return string The file's content.
     */
    public function get_group_file_by_id(string $groupid, string $fileid) : string {
        return $this->apicall('get', "/groups/$groupid/drive/items/$fileid/content");
    }

    /**
     * Add member to a Microsoft 365 group using group API.
     *
     * @param string $groupobjectid The object ID of the group to add to.
     * @param string $memberobjectid The object ID of the item to add (can be group object id or user object id).
     * @return array|null
     * @throws moodle_exception
     */
    public function add_member_to_group_using_group_api(string $groupobjectid, string $memberobjectid) {
        $endpoint = '/groups/' . $groupobjectid . '/members/$ref';
        $data = ['@odata.id' => $this->get_apiuri() . '/v1.0/directoryObjects/' . $memberobjectid];
        $response = $this->betaapicall('post', $endpoint, json_encode($data));
        return $this->process_apicall_response($response);
    }

    /**
     * Add owner to a Microsoft 365 group using group API.
     *
     * @param string $groupobjectid The object ID of the group to add to.
     * @param string $memberobjectid The object ID of the item to add (user object id).
     * @return array|null
     * @throws moodle_exception
     */
    public function add_owner_to_group_using_group_api(string $groupobjectid, string $memberobjectid) {
        $endpoint = '/groups/' . $groupobjectid . '/owners/$ref';
        $data = ['@odata.id' => $this->get_apiuri() . '/v1.0/users/' . $memberobjectid];
        $response = $this->betaapicall('post', $endpoint, json_encode($data));
        return $this->process_apicall_response($response);
    }

    /**
     * Remove member from a Microsoft 365 group using group API.
     *
     * @param string $groupobjectid The object ID of the group to remove from.
     * @param string $memberobjectid The object ID of the item to remove (can be group object id or user object id).
     * @return bool
     */
    public function remove_member_from_group_using_group_api(string $groupobjectid, string $memberobjectid) : bool {
        $endpoint = '/groups/' . $groupobjectid . '/members/' . $memberobjectid . '/$ref';
        $this->betaapicall('delete', $endpoint);
        if ($this->check_expected_http_code(['204'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove owner from a Microsoft 365 group using group API
     *
     * @param string $groupobjectid The object ID of the group to remove from.
     * @param string $ownerobjectid The object ID of the item to remove (can be group object id or user object id).
     * @return bool
     */
    public function remove_owner_from_group_using_group_api(string $groupobjectid, string $ownerobjectid) : bool {
        $endpoint = '/groups/' . $groupobjectid . '/owners/' . $ownerobjectid . '/$ref';
        $this->betaapicall('delete', $endpoint);
        if ($this->check_expected_http_code(['204'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add member to a Microsoft 365 group using teams API.
     *
     * @param string $groupobjectid
     * @param string $userobjectid
     * @return array|null
     * @throws moodle_exception
     */
    public function add_member_to_group_using_teams_api(string $groupobjectid, string $userobjectid) : ?array {
        $endpoint = '/teams/' . $groupobjectid . '/members';
        $data = [
            '@odata.type' => '#microsoft.graph.aadUserConversationMember',
            'roles' => ['member'],
            'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('$userobjectid')",
        ];
        $response = $this->betaapicall('post', $endpoint, json_encode($data));

        return $this->process_apicall_response($response, ['id' => null]);
    }

    /**
     * Add owner to a Microsoft 365 group using teams API.
     *
     * @param string $groupobjectid
     * @param string $userobjectid
     * @return array|null
     * @throws moodle_exception
     */
    public function add_owner_to_group_using_teams_api(string $groupobjectid, string $userobjectid) : ?array {
        $endpoint = '/teams/' . $groupobjectid . '/members';
        $data = [
            '@odata.type' => '#microsoft.graph.aadUserConversationMember',
            'roles' => ['owner'],
            'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('$userobjectid')",
        ];
        $response = $this->betaapicall('post', $endpoint, json_encode($data));

        return $this->process_apicall_response($response, ['id' => null]);
    }

    /**
     * Get the aadUserConversationMember ID for the given user in the given group.
     *
     * @param string $groupobjectid
     * @param string $userobjectid
     * @return string
     * @throws moodle_exception
     */
    public function get_aad_user_conversation_member_id(string $groupobjectid, string $userobjectid) : string {
        $endpoint = '/teams/' . $groupobjectid . '/members/?$filter=microsoft.graph.aadUserConversationMember/userId%20in%20("' .
            $userobjectid . '")';

        $response = $this->apicall('get', $endpoint);
        $response = $this->process_apicall_response($response, ['value' => null]);
        $aaduserconversationmemberid = '';
        if (count($response['value']) == 1) {
            $memberrecord = reset($response['value']);
            $aaduserconversationmemberid = $memberrecord['id'];
        }

        return $aaduserconversationmemberid;
    }

    /**
     * Remove the member with the given aadUserConversationMember ID from the group with the given ID, using teams API.
     *
     * @param string $groupobjectid
     * @param string $aaduserconversationmemberid
     * @return array|null
     * @throws moodle_exception
     */
    public function remove_owner_and_member_from_group_using_teams_api(string $groupobjectid,
        string $aaduserconversationmemberid) : ?array {
        $endpoint = '/teams/' . $groupobjectid . '/members/' . $aaduserconversationmemberid;

        $response = $this->apicall('delete', $endpoint);
        return $this->process_apicall_response($response);
    }

    /**
     * Create a group file.
     *
     * @param string $groupid The group Id.
     * @param string $filename The file's name.
     * @param string $content The file's content.
     * @param string $contenttype
     * @return array|null file upload response.
     */
    public function create_group_file(string $groupid, string $filename, string $content,
        string $contenttype = 'text/plain') : ?array {
        $filename = rawurlencode($filename);
        $endpoint = "/groups/$groupid/drive/root:/$filename:/content";
        $fileresponse = $this->apicall('put', $endpoint, ['file' => $content], ['contenttype' => $contenttype]);
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($fileresponse, $expectedparams);
    }

    /**
     * Get an array of general user fields to query for.
     *
     * @param bool $guestuser if the fields are for a guest user.
     * @return array Array of user fields.
     */
    protected function get_default_user_fields(bool $guestuser = false) : array {
        $defaultfields =
            ['id', 'userPrincipalName', 'displayName', 'givenName', 'surname', 'mail', 'streetAddress', 'city', 'postalCode',
                'state', 'country', 'jobTitle', 'department', 'companyName', 'preferredLanguage', 'employeeId', 'businessPhones',
                'faxNumber', 'mobilePhone', 'officeLocation', 'manager', 'teams', 'roles', 'groups', 'accountEnabled',
                'onPremisesExtensionAttributes',];
        if (!$guestuser) {
            $defaultfields[] = 'preferredName';
        }

        return $defaultfields;
    }

    /**
     * Get all users in the configured directory.
     *
     * @param string|array $params Requested user parameters.
     * @param string $skiptoken A skiptoken param from a previous get_users query. For pagination.
     * @return array|null Array of user information, or null if failure.
     */
    public function get_users($params = 'default', string $skiptoken = '') : ?array {
        $endpoint = "/users";
        $odataqueries = [];

        // Select params.
        if ($params === 'default') {
            $params = $this->get_default_user_fields();
        }
        if (is_array($params)) {
            $excludedfields = ['preferredName', 'teams', 'groups', 'roles'];
            foreach ($excludedfields as $excludedfield) {
                if (($key = array_search($excludedfield, $params)) !== false) {
                    unset($params[$key]);
                }
            }
            $odataqueries[] = '$select=' . implode(',', $params);
        }

        // Skip token.
        if (!empty($skiptoken) && is_string($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }

        // Process and append odata params.
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Return users delta.
     *
     * @param array|string $params
     * @param string|null $skiptoken
     * @param string|null $deltatoken
     * @return array
     * @throws moodle_exception
     */
    public function get_users_delta($params, string $skiptoken = null, string $deltatoken = null) : array {
        $endpoint = "/users/delta";
        $odataqueries = [];

        // Select params.
        if ($params === 'default') {
            $params = $this->get_default_user_fields();
        }
        if (is_array($params)) {
            $excludedfields = ['preferredName', 'teams', 'groups', 'roles'];
            foreach ($excludedfields as $excludedfield) {
                if (($key = array_search($excludedfield, $params)) !== false) {
                    unset($params[$key]);
                }
            }
            $odataqueries[] = '$select=' . implode(',', $params);
        }
        // Delta/skip tokens.
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        } else {
            if (!empty($deltatoken)) {
                $odataqueries[] = '$deltatoken=' . $deltatoken;
            }
        }

        // Process and append odata params.
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $result = $this->process_apicall_response($response, ['value' => null]);
        $users = [];
        $skiptoken = null;
        $deltatoken = null;

        if (!empty($result) && is_array($result)) {
            if (!empty($result['value']) && is_array($result['value'])) {
                $users = $result['value'];
            }

            if (isset($result['@odata.nextLink'])) {
                $skiptoken = $this->extract_param_from_link($result['@odata.nextLink'], '$skiptoken');
            }

            if (isset($result['@odata.deltaLink'])) {
                $deltatoken = $this->extract_param_from_link($result['@odata.deltaLink'], '$deltatoken');
            }
        }

        return [$users, $skiptoken, $deltatoken];
    }

    /**
     * Get user manager by passing user AD id.
     *
     * @param string $userobjectid - user AD id
     * @return array|null
     */
    public function get_user_manager(string $userobjectid) : ?array {
        $endpoint = "users/$userobjectid/manager";
        $response = $this->apicall('get', $endpoint);
        try {
            $result = $this->process_apicall_response($response);
        } catch (Exception $e) {
            return null;
        }

        return $result;
    }

    /**
     * Get Microsoft 365 groups by passing user AD id.
     *
     * @param string $userobjectid - user AD id
     * @return array|null
     */
    public function get_user_groups(string $userobjectid) : ?array {
        $endpoint = "users/$userobjectid/transitiveMemberOf/microsoft.graph.group";

        $response = $this->apicall('get', $endpoint);
        if ($this->check_expected_http_code(['200'])) {
            return $this->process_apicall_response($response, ['value' => null]);
        } else {
            return ['value' => []];
        }
    }

    /**
     * Get Microsoft 365 groups, including transitive groups, by passing user AD ID.
     *
     * @param string $userobjectid
     * @return array|null
     */
    public function get_user_transitive_groups(string $userobjectid) : ?array {
        $endpoint = "users/$userobjectid/getMemberGroups";
        $response = $this->apicall('post', $endpoint, json_encode(['securityEnabledOnly' => false]));
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Get user teams by passing user AD id
     *
     * @param string $userobjectid - user AD id
     * @return array|null
     */
    public function get_user_teams(string $userobjectid) : ?array {
        $endpoint = "users/$userobjectid/joinedTeams";
        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Get user objects by passing user AD id
     *
     * @param string $userobjectid - user AD id
     * @param bool $securityenabledonly - return only secure groups
     * @return array|null
     */
    public function get_user_objects(string $userobjectid, bool $securityenabledonly = true) : ?array {
        $endpoint = "users/$userobjectid/getMemberObjects";
        $data = ['securityEnabledOnly' => $securityenabledonly];
        $response = $this->apicall('post', $endpoint, json_encode($data));
        $result = $this->process_apicall_response($response, ['value' => null]);
        return $result['value'];
    }

    /**
     * Get directory objects by passing objects ids.
     *
     * @param array $ids - objects ids which data should be returned
     * @param string|null $types - collection of resource types that specifies the set of resource collections to search (optional).
     * @return array|null
     */
    public function get_directory_objects(array $ids, string $types = null) : ?array {
        $endpoint = "directoryObjects/getByIds";
        $data = ['ids' => $ids];
        if (!empty($types)) {
            $data['types'] = $types;
        }
        $response = $this->apicall('post', $endpoint, json_encode($data));
        $result = $this->process_apicall_response($response, ['value' => null]);
        return $result['value'];
    }

    /**
     * Extract a parameter value from a URL.
     *
     * @param string $link A URL.
     * @param string $param Parameter name.
     * @return string|null The extracted deltalink value, or null if none found.
     */
    protected function extract_param_from_link(string $link, string $param) : ?string {
        $link = parse_url($link);
        if (isset($link['query'])) {
            $output = [];
            parse_str($link['query'], $output);
            if (isset($output[$param])) {
                return $output[$param];
            }
        }
        return null;
    }

    /**
     * Get a list of recently deleted users in the last 30 days.
     *
     * @param string $skiptoken
     * @return array Array of returned information.
     */
    public function list_deleted_users(string $skiptoken = '') : array {
        $endpoint = '/directory/deleteditems/Microsoft.Graph.User';

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->betaapicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Get a user by the user's userPrincipalName
     *
     * @param string $upn The user's userPrincipalName
     * @return array Array of user data.
     */
    public function get_user_by_upn(string $upn) : array {
        $endpoint = '/users/' . rawurlencode($upn);
        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['id' => null, 'userPrincipalName' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a list of the user's o365 calendars.
     *
     * @param string $upn The user's userPrincipalName
     * @param string $skip
     * @return array|null Returned response, or null if error.
     */
    public function get_calendars(string $upn, string $skip = '') : ?array {
        $endpoint = '/users/' . $upn . '/calendars';

        $odataqueries = [];
        if (empty($skip) || !is_string($skip)) {
            $skip = '';
        }
        if (!empty($skip)) {
            $odataqueries[] = '$skip=' . $skip;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];
        $return = $this->process_apicall_response($response, $expectedparams);
        foreach ($return['value'] as $i => $calendar) {
            // Set legacy values.
            if (!isset($calendar['Id']) && isset($calendar['id'])) {
                $return['value'][$i]['Id'] = $calendar['id'];
            }
            if (!isset($calendar['Name']) && isset($calendar['name'])) {
                $return['value'][$i]['Name'] = $calendar['name'];
            }
        }
        return $return;
    }

    /**
     * Create a new calendar in the user's o365 calendars.
     *
     * @param string $name The calendar's title.
     * @param string $upn User's userPrincipalName
     * @return array|null Returned response, or null if error.
     */
    public function create_calendar(string $name, string $upn) : ?array {
        $calendardata = json_encode(['name' => $name]);
        $response = $this->apicall('post', '/users/' . $upn . '/calendars', $calendardata);
        $expectedparams = ['id' => null];
        $return = $this->process_apicall_response($response, $expectedparams);
        if (!isset($return['Id']) && isset($return['id'])) {
            $return['Id'] = $return['id'];
        }
        if (!isset($return['Name']) && isset($return['name'])) {
            $return['Name'] = $return['name'];
        }
        return $return;
    }

    /**
     * Update a existing o365 calendar.
     *
     * @param string $calendearid The calendar's title.
     * @param array $updated Array of updated information. Keys are 'name'.
     * @param string $upn user's userPrincipalName
     * @return array|null Returned response, or null if error.
     */
    public function update_calendar(string $calendearid, array $updated, string $upn) : ?array {
        if (empty($calendearid) || empty($updated)) {
            return [];
        }
        $updateddata = [];
        if (!empty($updated['name'])) {
            $updateddata['name'] = $updated['name'];
        }
        $updateddata = json_encode($updateddata);
        $response = $this->apicall('patch', '/users/' . $upn . '/calendars/' . $calendearid, $updateddata);
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Create a new event in the user's o365 calendar.
     *
     * @param string $subject The event's title/subject.
     * @param string $body The event's body/description.
     * @param int $starttime The timestamp when the event starts.
     * @param int $endtime The timestamp when the event ends.
     * @param array $attendees Array of moodle user objects that are attending the event.
     * @param array $other Other parameters to include.
     * @param string|null $calendarid The o365 ID of the calendar to create the event in.
     * @param string $upn user's userPrincipalName
     * @return array|null Returned response, or null if error.
     */
    public function create_event(string $subject, string $body, int $starttime, int $endtime, array $attendees, array $other,
        ?string $calendarid, string $upn) : ?array {
        $eventdata = [
            'subject' => $subject,
            'body' => [
                'contentType' => 'HTML',
                'content' => $body,
            ],
            'start' => [
                'dateTime' => date('c', $starttime),
                'timeZone' => date('T', $starttime),
            ],
            'end' => [
                'dateTime' => date('c', $endtime),
                'timeZone' => date('T', $endtime),
            ],
            'attendees' => [],
            'responseRequested' => false, // Sets meeting appears as accepted.
        ];
        foreach ($attendees as $attendee) {
            $eventdata['attendees'][] = [
                'EmailAddress' => [
                    'Address' => $attendee->email,
                    'Name' => $attendee->firstname.' '.$attendee->lastname,
                ],
                'type' => 'Resource'
            ];
        }
        $eventdata = array_merge($eventdata, $other);
        $eventdata = json_encode($eventdata);
        $endpoint = (!empty($calendarid)) ? '/users/' . $upn . '/calendars/' . $calendarid . '/events' :
            '/users/' . $upn . '/calendar/events';
        $response = $this->apicall('post', $endpoint, $eventdata);
        $expectedparams = ['id' => null];
        $return = $this->process_apicall_response($response, $expectedparams);
        if (!isset($return['Id']) && isset($return['id'])) {
            $return['Id'] = $return['id'];
        }
        return $return;
    }

    /**
     * Create a new event in the course group's o365 calendar.
     *
     * @param string $subject The event's title/subject.
     * @param string $body The event's body/description.
     * @param int $starttime The timestamp when the event starts.
     * @param int $endtime The timestamp when the event ends.
     * @param array $attendees Array of moodle user objects that are attending the event.
     * @param array $other Other parameters to include.
     * @param string $calendarid The o365 ID of the calendar to create the event in.
     * @return array|null Returned response, or null if error.
     */
    public function create_group_event(string $subject, string $body, int $starttime, int $endtime, array $attendees,
        array $other = [], $calendarid = null) {
        $eventdata = [
            'subject' => $subject,
            'body' => [
                'contentType' => 'HTML',
                'content' => $body,
            ],
            'start' => [
                'dateTime' => date('c', $starttime),
                'timeZone' => date('T', $starttime),
            ],
            'end' => [
                'dateTime' => date('c', $endtime),
                'timeZone' => date('T', $endtime),
            ],
            'attendees' => [],
        ];
        foreach ($attendees as $attendee) {
            $eventdata['attendees'][] = [
                'EmailAddress' => [
                    'Address' => $attendee->email,
                    'Name' => $attendee->firstname.' '.$attendee->lastname,
                ],
                'type' => 'Resource'
            ];
        }
        $eventdata = array_merge($eventdata, $other);
        $eventdata = json_encode($eventdata);
        $endpoint = "/groups/{$calendarid}/calendar/events";
        $response = $this->apicall('post', $endpoint, $eventdata);
        $expectedparams = ['id' => null];
        $return = $this->process_apicall_response($response, $expectedparams);
        if (!isset($return['Id']) && isset($return['id'])) {
            $return['Id'] = $return['id'];
        }
        return $return;
    }

    /**
     * Get a list of events.
     *
     * @param string $calendarid The calendar ID to get events from. If empty, primary calendar used.
     * @param string $since datetime date('c') to get events since.
     * @param string $upn user's userPrincipalName
     * @param string $skip
     * @return array Array of events.
     */
    public function get_events(string $calendarid, string $since, string $upn, string $skip = '') : array {
        core_date::set_default_server_timezone();
        $endpoint = (!empty($calendarid)) ? '/users/' . $upn . '/calendars/' . $calendarid . '/events' :
            '/users/' . $upn . '/calendar/events';

        $odataqueries = [];
        if (empty($skip) || !is_string($skip)) {
            $skip = '';
        }
        if (!empty($skip)) {
            $odataqueries[] = '$skip=' . $skip;
        }
        if (!empty($since)) {
            // Pass datetime in UTC, regardless of Moodle timezone setting.
            $sincedt = new DateTime('@' . $since);
            $since = urlencode($sincedt->format('Y-m-d\TH:i:s\Z'));
            $odataqueries[] = '$filter=CreatedDateTime%20ge%20' . $since;
        }

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];
        $return = $this->process_apicall_response($response, $expectedparams);
        foreach ($return['value'] as $i => $event) {
            // Converts params to the old legacy parameter used by the rest of the code from the new unified parameter.
            if (!isset($event['Id']) && isset($event['id'])) {
                $return['value'][$i]['Id'] = $event['id'];
            }
            if (!isset($event['Subject']) && isset($event['subject'])) {
                $return['value'][$i]['Subject'] = $event['subject'];
            }
            if (!isset($event['Body']) && isset($event['body'])) {
                $return['value'][$i]['Body'] = $event['body'];
                if (!isset($return['value'][$i]['Body']['Content']) && isset($return['value'][$i]['body']['content'])) {
                    $return['value'][$i]['Body']['Content'] = $return['value'][$i]['body']['content'];
                }
            }
            if (!isset($event['Start']) && isset($event['start'])) {
                if (is_array($event['start'])) {
                    $return['value'][$i]['Start'] = $event['start']['dateTime'] . ' ' . $event['start']['timeZone'];
                } else {
                    $return['value'][$i]['Start'] = $event['start'];
                }
            }
            if (!isset($event['End']) && isset($event['end'])) {
                if (is_array($event['end'])) {
                    $return['value'][$i]['End'] = $event['end']['dateTime'] . ' ' . $event['end']['timeZone'];
                } else {
                    $return['value'][$i]['End'] = $event['end'];
                }
            }
        }
        return $return;
    }

    /**
     * Update an event.
     *
     * @param string $outlookeventid The event ID in o365 outlook.
     * @param array $updated Array of updated information. Keys are 'subject', 'body', 'starttime', 'endtime', and 'attendees'.
     * @param string $upn user's userPrincipalName
     * @return array|null Returned response, or null if error.
     * @throws moodle_exception
     */
    public function update_event(string $outlookeventid, array $updated, string $upn) : ?array {
        if (empty($outlookeventid) || empty($updated)) {
            return [];
        }
        $updateddata = [];
        if (!empty($updated['subject'])) {
            $updateddata['subject'] = $updated['subject'];
        }
        if (!empty($updated['body'])) {
            $updateddata['body'] = ['contentType' => 'HTML', 'content' => $updated['body']];
        }
        if (!empty($updated['starttime'])) {
            $updateddata['start'] =
                ['dateTime' => date('c', $updated['starttime']), 'timeZone' => date('T', $updated['starttime']),];
        }
        if (!empty($updated['endtime'])) {
            $updateddata['end'] = ['dateTime' => date('c', $updated['endtime']), 'timeZone' => date('T', $updated['endtime']),];
        }
        if (!empty($updated['responseRequested'])) {
            $updateddata['responseRequested'] = $updated['responseRequested'];
        }
        if (isset($updated['attendees'])) {
            $updateddata['attendees'] = [];
            foreach ($updated['attendees'] as $attendee) {
                $updateddata['attendees'][] =
                    ['emailAddress' => ['address' => $attendee->email, 'name' => $attendee->firstname . ' ' . $attendee->lastname,],
                        'type' => 'resource'];
            }
        }
        $updateddata = json_encode($updateddata);
        $response = $this->apicall('patch', '/users/' . $upn . '/events/' . $outlookeventid, $updateddata);
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Delete an event.
     *
     * @param string $outlookeventid The event ID in o365 outlook.
     * @param string $upn user's userPrincipalName
     * @return bool Success/Failure.
     */
    public function delete_event(string $outlookeventid, string $upn) : bool {
        if (!empty($outlookeventid)) {
            $this->apicall('delete', '/users/' . $upn . '/events/' . $outlookeventid);
        }
        return true;
    }

    /**
     * Create a file.
     *
     * @param string $parentid
     * @param string $filename
     * @param string $content
     * @param string $contenttype
     * @param string $o365userid
     * @return array|null
     */
    public function create_file(string $parentid, string $filename, string $content, string $contenttype,
        string $o365userid) : ?array {
        $filename = rawurlencode($filename);
        if (!empty($parentid)) {
            $endpoint = "/users/$o365userid/drive/items/$parentid:/$filename:/content";
        } else {
            $endpoint = "/users/$o365userid/drive/items/root:/$filename:/content";
        }
        $fileresponse = $this->apicall('put', $endpoint, ['file' => $content], ['contenttype' => $contenttype]);
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($fileresponse, $expectedparams);
    }

    /**
     * List a user's files.
     *
     * @param string $parentid The parent id to use.
     * @param string $o365userid user's Office 365 account object ID
     * @param string $skiptoken
     * @return array|null Returned response, or null if error.
     */
    public function get_user_files(string $parentid, string $o365userid, string $skiptoken = '') : ?array {
        if (!empty($parentid) && $parentid !== '/') {
            $endpoint = "/users/$o365userid/drive/items/$parentid/children";
        } else {
            $endpoint = "/users/$o365userid/drive/root/children";
        }

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);

        $expectedparams = ['value' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get files from trendingAround api.
     *
     * @param string $upn user's userPrincipalName
     * @param string $skiptoken
     * @return array|null Returned response, or null if error.
     */
    public function get_trending_files(string $upn, string $skiptoken = '') : ?array {
        $endpoint = '/users/' . $upn . '/trendingAround';

        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];

        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a file's data by its file information.
     *
     * @param string $fileinfo The file's drive id and file id.
     * @return array|null The file's content.
     */
    public function get_file_data(string $fileinfo) : ?array {
        $response = $this->apicall('get', "/$fileinfo");
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a file's content by its file URL.
     *
     * @param string $url The file's URL.
     * @return string The file's content.
     */
    public function get_file_by_url(string $url) : string {
        return $this->httpclient->download_file($url);
    }

    /**
     * Get a file's metadata by its file ID.
     *
     * @param string $fileid The file's ID.
     * @param string $o365userid user's Microsoft 365 account object ID
     * @return array|null The file's metadata.
     */
    public function get_file_metadata(string $fileid, string $o365userid) : ?array {
        $response = $this->apicall('get', "/users/$o365userid/drive/items/$fileid");
        $expectedparams = ['id' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get a file's content by it's file id.
     *
     * @param string $fileid The file's ID.
     * @param string $o365userid user's Microsoft 365 account object ID
     * @return string The file's content.
     */
    public function get_file_by_id(string $fileid, string $o365userid) : string {
        return $this->apicall('get', "/users/$o365userid/drive/items/$fileid/content");
    }

    /**
     * Get information on the current application.
     *
     * @return array|null Array of application information, or null if failure.
     */
    public function get_application_info() : ?array {
        $oidcconfig = get_config('auth_oidc');
        $endpoint = '/applications/?$filter=appId%20eq%20\'' . $oidcconfig->clientid . '\'';
        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get information on the current application.
     *
     * @return array|null Array of application information, or null if failure.
     */
    public function get_application_serviceprincipal_info() : ?array {
        $oidcconfig = get_config('auth_oidc');
        $endpoint = '/servicePrincipals/?$filter=appId%20eq%20\'' . $oidcconfig->clientid . '\'';
        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];
        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Get the service principal object for the Microsoft Graph API.
     *
     * @return array Array representing service principal object.
     */
    public function get_unified_api_serviceprincipal_info() : ?array {
        static $response = null;
        if (empty($response)) {
            $graphperms = $this->get_required_permissions('graph');
            $endpoint = '/servicePrincipals?$filter=appId%20eq%20\'' . $graphperms['appId'] . '\'';
            $response = $this->betaapicall('get', $endpoint);
            $expectedparams = ['value' => null];
            $response = $this->process_apicall_response($response, $expectedparams);
        }
        return $response;
    }

    /**
     * Get all available permissions for the Microsoft Graph API.
     *
     * @return array Array of available permissions, include descriptions and keys.
     */
    public function get_available_permissions() : ?array {
        $svc = $this->get_unified_api_serviceprincipal_info();
        if (empty($svc) || !is_array($svc)) {
            return null;
        }
        if (!isset($svc['value']) || !isset($svc['value'][0])) {
            return null;
        }
        if (isset($svc['value'][0]['oauth2Permissions'])) {
            return $svc['value'][0]['oauth2Permissions'];
        } else if (isset($svc['value'][0]['publishedPermissionScopes'])) {
            return $svc['value'][0]['publishedPermissionScopes'];
        } else {
            return null;
        }
    }

    /**
     * Get all available app-only permissions for the graph api.
     *
     * @return array Array of available app-only permissions, indexed by permission name.
     */
    public function get_graph_available_apponly_permissions() : array {
        // Get list of permissions and associated IDs.
        $graphsp = $this->get_unified_api_serviceprincipal_info();
        $graphsp = $graphsp['value'][0];
        $graphperms = [];
        foreach ($graphsp['appRoles'] as $perm) {
            $graphperms[$perm['value']] = $perm;
        }
        return $graphperms;
    }

    /**
     * Get currently configured app-only permissions for the graph api.
     *
     * @return array Array of current app-only permissions, indexed by permission name.
     */
    public function get_graph_current_apponly_permissions() : array {
        // Get available permissions.
        $graphsp = $this->get_unified_api_serviceprincipal_info();
        $graphsp = $graphsp['value'][0];
        $graphappid = $graphsp['appId'];
        $graphperms = [];
        foreach ($graphsp['appRoles'] as $perm) {
            $graphperms[$perm['id']] = $perm;
        }

        // Get a list of configured permissions for the graph api within the client application.
        $appinfo = $this->get_application_info();
        $appinfo = $appinfo['value'][0];
        $graphresource = null;
        foreach ($appinfo['requiredResourceAccess'] as $requiredresource) {
            if ($requiredresource['resourceAppId'] === $graphappid) {
                $graphresource = $requiredresource;
                break;
            }
        }
        if (empty($graphresource)) {
            throw new Exception('Unable to find graph api in application.');
        }

        // Translate to permission information.
        $currentperms = [];
        foreach ($graphresource['resourceAccess'] as $requiredresource) {
            if ($requiredresource['type'] === 'Role') {
                if (isset($graphperms[$requiredresource['id']])) {
                    $perminfo = $graphperms[$requiredresource['id']];
                    $currentperms[$perminfo['value']] = $perminfo;
                }
            }
        }
        return $currentperms;
    }

    /**
     * Get information on the current application.
     *
     * @param string $resourceid
     * @return array|null Array of application information, or null if failure.
     */
    public function get_permission_grants(string $resourceid = '') : ?array {
        $appinfo = $this->get_application_serviceprincipal_info();
        if (empty($appinfo) || !is_array($appinfo)) {
            return null;
        }
        if (!isset($appinfo['value']) || !isset($appinfo['value'][0]) || !isset($appinfo['value'][0]['id'])) {
            return null;
        }
        $appobjectid = $appinfo['value'][0]['id'];
        $endpoint = '/oauth2PermissionGrants?$filter=clientId%20eq%20\'' . $appobjectid . '\'';
        if (!empty($resourceid)) {
            $endpoint .= '%20and%20resourceId%20eq%20\'' . $resourceid . '\'';
        }
        $response = $this->betaapicall('get', $endpoint);
        return $this->process_apicall_response($response);
    }

    /**
     * Get currently assigned permissions for the Microsoft Graph API.
     *
     * @return array Array of permission keys.
     */
    public function get_unified_api_permissions() : ?array {
        $apiinfo = $this->get_unified_api_serviceprincipal_info();
        if (empty($apiinfo) || !is_array($apiinfo)) {
            return null;
        }
        if (!isset($apiinfo['value']) || !isset($apiinfo['value'][0]) || !isset($apiinfo['value'][0]['id'])) {
            return null;
        }
        $apiobjectid = $apiinfo['value'][0]['id'];
        $permgrants = $this->get_permission_grants($apiobjectid);
        if (empty($permgrants) || !is_array($permgrants)) {
            return null;
        }
        if (!isset($permgrants['value']) || !isset($permgrants['value'][0]) || !isset($permgrants['value'][0]['scope'])) {
            return null;
        }
        return explode(' ', $permgrants['value'][0]['scope']);
    }

    /**
     * Get an array of the current required permissions for the graph api.
     *
     * @return array Array of required Azure AD permissions.
     */
    public function get_graph_required_permissions() : array {
        $allperms = $this->get_required_permissions();
        if (isset($allperms['graph'])) {
            return $allperms['graph']['requiredDelegatedPermissionsUsingAppPermissions'];
        } else {
            return [];
        }
    }

    /**
     * Get required app-only permissions for the graph api.
     *
     * @return array Array of required Azure AD application permissions.
     */
    public function get_graph_required_apponly_permissions() : array {
        $allperms = $this->get_required_permissions();
        if (isset($allperms['graph'])) {
            return $allperms['graph']['requiredAppPermissions'];
        } else {
            return [];
        }
    }

    /**
     * Check application Graph API permissions.
     *
     * @return array
     */
    public function check_graph_apponly_permissions() : array {
        $this->token->refresh();
        $requiredperms = $this->get_graph_required_apponly_permissions();
        $currentperms = $this->get_graph_current_apponly_permissions();
        $availableperms = $this->get_graph_available_apponly_permissions();

        $missingperms = [];

        foreach ($requiredperms as $requiredperm => $alternativeperms) {
            $haspermission = false;
            if (array_key_exists($requiredperm, $currentperms)) {
                $haspermission = true;
            } else {
                foreach ($alternativeperms as $alternativeperm) {
                    if (array_key_exists($alternativeperm, $currentperms)) {
                        $haspermission = true;
                        break;
                    }
                }
            }

            if (!$haspermission) {
                $missingperms[] = $requiredperm;
            }
        }

        if (empty($missingperms)) {
            return [];
        }

        // Assemble friendly names for permissions.
        $permnames = [];
        foreach ($availableperms as $perminfo) {
            if (!isset($perminfo['value']) || !isset($perminfo['adminConsentDisplayName'])) {
                continue;
            }
            $permnames[$perminfo['value']] = $perminfo['adminConsentDisplayName'];
        }

        $missingpermsreturn = [];
        foreach ($missingperms as $missingperm) {
            $missingpermsreturn[$missingperm] = (isset($permnames[$missingperm])) ? $permnames[$missingperm] : $missingperm;
        }

        return $missingpermsreturn;
    }

    /**
     * Check whether all required permissions are present.
     *
     * @return array Array of missing permissions, permission key as array key, human-readable name as values.
     */
    public function check_graph_delegated_permissions() : ?array {
        $this->token->refresh();
        $currentperms = $this->get_unified_api_permissions();
        $requiredperms = $this->get_graph_required_permissions();
        $availableperms = $this->get_available_permissions();

        if ($currentperms === null || $availableperms === null) {
            return null;
        }

        $missingperms = [];

        foreach ($requiredperms as $requiredperm => $alternativeperms) {
            $haspermission = false;
            if (in_array($requiredperm, $currentperms)) {
                $haspermission = true;
            } else {
                foreach ($alternativeperms as $alternativeperm) {
                    if (in_array($alternativeperm, $currentperms)) {
                        $haspermission = true;
                        break;
                    }
                }
            }

            if (!$haspermission) {
                $missingperms[] = $requiredperm;
            }
        }

        if (empty($missingperms)) {
            return [];
        }

        // Assemble friendly names for permissions.
        $permnames = [];
        foreach ($availableperms as $perminfo) {
            if (!isset($perminfo['value']) || !isset($perminfo['adminConsentDisplayName'])) {
                continue;
            }
            $permnames[$perminfo['value']] = $perminfo['adminConsentDisplayName'];
        }

        $missingpermsreturn = [];
        foreach ($missingperms as $missingperm) {
            $missingpermsreturn[$missingperm] = (isset($permnames[$missingperm])) ? $permnames[$missingperm] : $missingperm;
        }

        return $missingpermsreturn;
    }

    /**
     * Get a users photo.
     *
     * @param string $user User to retrieve photo.
     * @return string|false Returned binary photo data, false if there is no photo.
     */
    public function get_photo(string $user) {
        $photo = $this->apicall('get', "/users/$user/photo/\$value");

        // Check if response is binary.
        if (preg_match('~[^\x20-\x7E\t\r\n]~', $photo) > 0) {
            return $photo;
        } else {
            return false;
        }
    }

    /**
     * Create readonly link for onedrive file.
     *
     * @param string $fileid onedrive file id.
     * @param string $o365userid
     * @return string Readonly file url.
     */
    public function get_sharing_link(string $fileid, string $o365userid) : string {
        $params = ['type' => 'view', 'scope' => 'organization'];
        $apiresponse = $this->apicall('post', "/users/$o365userid/drive/items/$fileid/createLink", json_encode($params));
        $response = $this->process_apicall_response($apiresponse);
        return $response['link']['webUrl'];
    }

    /**
     * Get a specific user's information.
     *
     * @param string $oid The user's object id.
     * @param bool $guestuser if the user is a guest user.
     * @return array|null Array of user information, or null if failure.
     */
    public function get_user(string $oid, bool $guestuser = false) : ?array {
        $endpoint = "/users/$oid";
        $odataqueries = [];

        $params = $this->get_default_user_fields($guestuser);
        $context = 'https://graph.microsoft.com/v1.0/$metadata#users(';
        $context = $context . join(',', $params) . ')/$entity';
        $odataqueries[] = '$select=' . implode(',', $params);

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['@odata.context' => $context, 'id' => null, 'userPrincipalName' => null,];

        $result = $this->process_apicall_response($response, $expectedparams);
        if (!empty($result['id'])) {
            $result['objectId'] = $result['id'];
        }
        return $result;
    }

    /**
     * Get the Azure AD UPN of a connected Moodle user.
     *
     * @param stdClass|int $user The Moodle user.
     * @return string|bool The user's Azure AD UPN, or false if failure.
     */
    public static function get_muser_upn($user) {
        global $DB;
        $now = time();

        if (is_numeric($user)) {
            $user = $DB->get_record('user', ['id' => $user]);
            if (empty($user)) {
                utils::debug('User not found', __METHOD__, $user);
                return false;
            }
        }

        // Get user UPN.
        $userobjectdata = $DB->get_record('local_o365_objects', ['type' => 'user', 'moodleid' => $user->id]);
        if (empty($userobjectdata)) {
            // Get user data.
            $o365user = o365user::instance_from_muserid($user->id);
            if (empty($o365user)) {
                // No o365 user data for the user is available.
                utils::debug('Could not construct o365user class for user.', __METHOD__, $user->username);
                return false;
            }
            try {
                $apiclient = utils::get_api();
            } catch (Exception $e) {
                utils::debug($e->getMessage(), __METHOD__, $e);
                return false;
            }

            $isguestuser = false;
            if (stripos($user->username, '_ext_') !== false) {
                $isguestuser = true;
            }
            $userdata = $apiclient->get_user($o365user->objectid, $isguestuser);

            if (static::is_configured() && empty($userdata['objectId']) && !empty($userdata['id'])) {
                $userdata['objectId'] = $userdata['id'];
            }
            $userobjectdata = (object) ['type' => 'user', 'subtype' => '', 'objectid' => $userdata['objectId'],
                'o365name' => $userdata['userPrincipalName'], 'moodleid' => $user->id, 'timecreated' => $now,
                'timemodified' => $now,];
            $userobjectdata->id = $DB->insert_record('local_o365_objects', $userobjectdata);
        }

        return $userobjectdata->o365name;
    }

    /**
     * Provision an app in a team.
     *
     * @param string $groupobjectid
     * @param string $appid
     * @return bool
     */
    public function provision_app(string $groupobjectid, string $appid) : bool {
        $endpoint = '/teams/' . $groupobjectid . '/installedApps';
        $data = ['teamsApp@odata.bind' => $this->get_apiuri() . '/beta/appCatalogs/teamsApps/' . $appid,];
        $this->betaapicall('post', $endpoint, json_encode($data));

        // If the request was successful, it would return 201; otherwise, if the request failed with "duplicate",
        // it would return 409, and it means the app has already been provisioned.
        if ($this->check_expected_http_code(['201', '409'])) {
            return true;
        } else {
            throw new moodle_exception('errorprovisioningapp', 'local_o365');
        }
    }

    /**
     * Return the ID of the app with the given internalId in the catalog.
     *
     * @param string $externalappid
     * @return string|null
     * @throws moodle_exception
     */
    public function get_catalog_app_id(string $externalappid) : ?string {
        $moodleappid = null;

        $endpoint = '/appCatalogs/teamsApps?$filter=externalId' . rawurlencode(' eq \'' . $externalappid . '\'');
        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];
        try {
            $response = $this->process_apicall_response($response, $expectedparams);
            if (count($response['value']) > 0) {
                $moodleapp = array_shift($response['value']);
                $moodleappid = $moodleapp['id'];
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $moodleappid;
    }

    /**
     * Return the ID of the general channel of the team.
     *
     * @param string $groupobjectid
     * @return string|null
     */
    public function get_general_channel_id(string $groupobjectid) : ?string {
        $generalchannelid = null;

        $endpoint = '/teams/' . $groupobjectid . '/channels?$filter=displayName' . rawurlencode(' eq \'General\'');
        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];
        $response = $this->process_apicall_response($response, $expectedparams);
        if (count($response['value']) > 0) {
            $generalchannel = array_shift($response['value']);
            $generalchannelid = $generalchannel['id'];
        }

        return $generalchannelid;
    }

    /**
     * Add a tab of app to a channel.
     *
     * @param string $groupobjectid
     * @param string $channelid
     * @param string $appid
     * @param array $tabconfiguration
     * @return string
     */
    public function add_tab_to_channel(string $groupobjectid, string $channelid, string $appid, array $tabconfiguration) : string {
        $endpoint = '/teams/' . $groupobjectid . '/channels/' . $channelid . '/tabs';
        $requestparams = ['displayName' => get_string('tab_moodle', 'local_o365'),
            'teamsApp@odata.bind' => $this->get_apiuri() . '/beta/appCatalogs/teamsApps/' . $appid,
            'configuration' => $tabconfiguration,];

        return $this->betaapicall('post', $endpoint, json_encode($requestparams));
    }

    /**
     * Update the name of a Team.
     *
     * @param string $objectid
     * @param string $displayname
     * @return string
     */
    public function update_team_name(string $objectid, string $displayname) : string {
        $endpoint = '/teams/' . $objectid;

        $teamdata = ['displayName' => $displayname,];

        return $this->betaapicall('patch', $endpoint, json_encode($teamdata));
    }

    /**
     * Archive a Team.
     *
     * @param string $objectid
     * @return array|bool|null
     */
    public function archive_team(string $objectid) {
        $endpoint = '/teams/' . $objectid . '/archive';

        $result = $this->betaapicall('post', $endpoint);
        if ($this->check_expected_http_code(['202'])) {
            return true;
        } else {
            return $this->process_apicall_response($result);
        }
    }

    /**
     * Get user timezone in Outlook settings.
     *
     * @param string $upn
     * @return array|null|false
     */
    public function get_user_timezone_by_upn(string $upn) {
        $endpoint = '/users/' . $upn . '/mailboxSettings/timeZone';
        try {
            $response = $this->betaapicall('get', $endpoint);
            $expectedparams = ['value' => null];
            return $this->process_apicall_response($response, $expectedparams);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get a list of teams.
     *
     * @param string $skiptoken
     * @return array|null
     */
    public function get_teams(string $skiptoken = '') : ?array {
        $endpoint = '/groups?$filter=resourceProvisioningOptions/Any(x:x%20eq%20\'Team\')';
        $odataqueries = [];
        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }
        if (!empty($odataqueries)) {
            $endpoint .= '&' . implode('&', $odataqueries);
        }
        $response = $this->betaapicall('get', $endpoint);
        $expectedparams = ['value' => null];

        return $this->process_apicall_response($response, $expectedparams);
    }

    /**
     * Return the list of SDS schools.
     *
     * @param string $skiptoken
     * @return array|null
     */
    public function get_schools(string $skiptoken = '') : ?array {
        $endpoint = '/education/schools';
        $odataqueries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Return the list of classes in the SDS school with the given object ID.
     *
     * @param string $schoolobjectid
     * @param string $skiptoken
     * @return array|null
     */
    public function get_school_classes(string $schoolobjectid, string $skiptoken = '') : ?array {
        $endpoint = '/education/schools/' . $schoolobjectid . '/classes';
        $odataquries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataquries[] = '$skiptoken=' . $skiptoken;
        }

        if (!empty($odataquries)) {
            $endpoint .= '?' . implode('&', $odataquries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Return the list of teachers in the class with the given object ID.
     *
     * @param string $classobjectid
     * @param string $skiptoken
     * @return array|null
     */
    public function get_school_class_teachers(string $classobjectid, string $skiptoken = '') : ?array {
        $endpoint = '/education/classes/' . $classobjectid . '/teachers';
        $odataqueries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Return the list of members in the class with the given object ID.
     *
     * @param string $classobjectid
     * @param string $skiptoken
     * @return array|null
     */
    public function get_school_class_members(string $classobjectid, string $skiptoken = '') : ?array {
        $endpoint = '/education/classes/' . $classobjectid . '/members';
        $odataqueries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Return the list of users in the SDS school with the given object ID.
     *
     * @param string $schoolobjectid
     * @param string $skiptoken
     * @return array|null
     */
    public function get_school_users(string $schoolobjectid, string $skiptoken = '') : ?array {
        $endpoint = '/education/schools/' . $schoolobjectid . '/users';
        $odataqueries = [];

        if (empty($skiptoken) || !is_string($skiptoken)) {
            $skiptoken = '';
        }
        if (!empty($skiptoken)) {
            $odataqueries[] = '$skiptoken=' . $skiptoken;
        }

        if (!empty($odataqueries)) {
            $endpoint .= '?' . implode('&', $odataqueries);
        }

        $response = $this->apicall('get', $endpoint);
        return $this->process_apicall_response($response, ['value' => null]);
    }

    /**
     * Determine if the tenant that the Azure app is created in has Education license.
     *
     * @return bool
     */
    public function has_education_license() : bool {
        $endpoint = '/organization';
        $odataqueries = [];
        $odataqueries[] = '$select=assignedPlans';
        $endpoint .= '?' . implode('&', $odataqueries);

        $response = $this->apicall('get', $endpoint);
        try {
            $response = $this->process_apicall_response($response, ['value' => null]);
            $assignedplans = reset($response['value']);
            if (isset($assignedplans['assignedPlans'])) {
                $assignedplans = $assignedplans['assignedPlans'];
                foreach ($assignedplans as $assignedplan) {
                    if (isset($assignedplan['servicePlanId']) && in_array($assignedplan['servicePlanId'], EDUCATION_LICENSE_IDS)) {
                        return true;
                    }
                }
            }
        } catch (Exception $e) {
            // Failed to get assigned plans.
            utils::debug($e->getMessage(), __METHOD__, $e);
        }

        return false;
    }

    /**
     * Create an education class group using the information provided.
     *
     * @param string $displayname
     * @param string $mailnickname
     * @param string $description
     * @param string $externalid
     * @param string $externalname
     * @return array|null
     * @throws moodle_exception
     */
    public function create_educationclass_group(string $displayname, string $mailnickname, string $description, string $externalid,
        string $externalname) : ?array {
        global $SITE;

        if (!empty($mailnickname)) {
            $mailnickname = core_text::strtolower($mailnickname);
            $mailnickname = preg_replace('/[^a-z0-9_]+/iu', '', $mailnickname);
            $mailnickname = trim($mailnickname);
        }

        if (empty($mailnickname)) {
            // Cannot generate a good mailnickname because there's nothing but non-alphanum chars to work with. So generate one.
            $mailnickname = 'group' . uniqid();
        }

        $groupdata = [
            'description' => $description,
            'displayName' => $displayname,
            'externalId' => $externalid,
            'externalName' => $externalname,
            'externalSourceDetail' => 'Moodle',
            'mailNickname' => $mailnickname,
        ];

        // Description cannot be set and empty.
        if (empty($groupdata['description'])) {
            unset($groupdata['description']);
        }

        $endpoint = '/education/classes';

        $response = $this->betaapicall('post', $endpoint, json_encode($groupdata));
        $expectedparams = ['id' => null];

        try {
            $response = $this->process_apicall_response($response, $expectedparams);
        } catch (Exception $e) {
            $expectedexception = 'Another object with the same value for property mailNickname already exists.';
            if ($e->a == $expectedexception) {
                $mailnickname .= '_' . sprintf('%04d', random_int(0, 9999));
                return $this->create_educationclass_group($displayname, $mailnickname, $description, $externalid, $externalname);
            } else {
                utils::debug($e->getMessage(), __METHOD__, $e);
                throw $e;
            }
        }

        return $response;
    }

    /**
     * Update LMS attributes for Education groups.
     *
     * @param string $groupobjectid
     * @param array $lmsattributes
     * @return array|bool|null
     * @throws moodle_exception
     */
    public function update_education_group_with_lms_data(string $groupobjectid, array $lmsattributes) {
        $endpoint = '/groups/' . $groupobjectid;

        $response = $this->betaapicall('patch', $endpoint, json_encode($lmsattributes));
        if ($this->check_expected_http_code(['204'])) {
            return true;
        } else {
            return $this->process_apicall_response($response);
        }
    }

    /**
     * Add chunk of users to groups with the given role.
     *
     * @param string $groupobjectid
     * @param string $role
     * @param array $userobjectids
     * @return bool
     * @throws moodle_exception
     */
    public function add_chunk_users_to_group(string $groupobjectid, string $role, array $userobjectids) : bool {
        $endpoint = '/groups/' . $groupobjectid;

        if ($role == 'owner') {
            $rolename = 'owners@odata.bind';
        } else if ($role == 'member') {
            $rolename = 'members@odata.bind';
        } else {
            return false;
        }

        $userlist = [];
        foreach ($userobjectids as $userobjectid) {
            $userlist[] = 'https://graph.microsoft.com/v1.0/directoryObjects/' . $userobjectid;
        }

        $userparam = [$rolename => $userlist];

        $response = $this->apicall('patch', $endpoint, json_encode($userparam));

        if ($this->check_expected_http_code(['204'])) {
            return true;
        } else {
            $this->process_apicall_response($response);
            return false;
        }
    }

    /**
     * Create a class team from the education group with the given object ID.
     *
     * @param string $groupobjectid
     * @return array|bool|null
     * @throws moodle_exception
     */
    public function create_class_team_from_education_group(string $groupobjectid) {
        $endpoint = '/teams';

        $teamparams = [
            'template@odata.bind' => "https://graph.microsoft.com/v1.0/teamsTemplates('educationClass')",
            'group@odata.bind' => "https://graph.microsoft.com/v1.0/groups('" . $groupobjectid . "')",
        ];

        $response = $this->betaapicall('post', $endpoint, json_encode($teamparams));

        if ($this->check_expected_http_code(['202'])) {
            return true;
        } else {
            return $this->process_apicall_response($response);
        }
    }

    /**
     * Create a standard team from the group with the given object ID.
     *
     * @param string $groupobjectid
     * @return array|bool|null
     * @throws moodle_exception
     */
    public function create_standard_team_from_group(string $groupobjectid) {
        $endpoint = '/teams';

        $teamparams = [
            'template@odata.bind' => "https://graph.microsoft.com/v1.0/teamsTemplates('standard')",
            'group@odata.bind' => "https://graph.microsoft.com/v1.0/groups('" . $groupobjectid . "')",
        ];
        $response = $this->apicall('post', $endpoint, json_encode($teamparams));

        if ($this->check_expected_http_code(['202'])) {
            return true;
        } else {
            return $this->process_apicall_response($response);
        }
    }

    /**
     * Check if the HTTP status code of the last Graph API call is among the expected HTTP code.
     *
     * @param array $expectedhttpcodes
     * @return bool
     */
    private function check_expected_http_code(array $expectedhttpcodes) : bool {
        $httpclientinfo = (array) $this->httpclient->info;

        return in_array($httpclientinfo['http_code'], $expectedhttpcodes);
    }

    /**
     * Get the app credential details of the app with the given appID.
     *
     * @param string $appid
     * @return array|null
     * @throws moodle_exception
     */
    public function get_app_credentials(string $appid) {
        $endpoint = '/applications';

        $odataqueries = [
            '$filter=appID%20eq%20\'' . $appid . '\'',
            '$select=passwordCredentials',
        ];
        $endpoint .= '?' . implode('&', $odataqueries);

        $response = $this->apicall('get', $endpoint);
        $expectedparams = ['value' => null];

        return $this->process_apicall_response($response, $expectedparams);
    }
}
