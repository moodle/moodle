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
 * Class for loading/storing oauth2 endpoints from the DB.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

use context_system;
use curl;
use stdClass;
use moodle_exception;
use moodle_url;


/**
 * Static list of api methods for system oauth2 configuration.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Create a google ready OAuth 2 service.
     * @return \core\oauth2\issuer
     */
    private static function create_google() {
        $record = (object) [
            'name' => 'Google',
            'image' => 'https://accounts.google.com/favicon.ico',
            'baseurl' => 'http://accounts.google.com/',
            'loginparamsoffline' => 'access_type=offline&prompt=consent',
            'showonloginpage' => true
        ];

        $issuer = new issuer(0, $record);
        $issuer->create();

        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'discovery_endpoint',
            'url' => 'https://accounts.google.com/.well-known/openid-configuration'
        ];
        $endpoint = new endpoint(0, $record);
        $endpoint->create();
        return $issuer;
    }

    /**
     * Create a facebook ready OAuth 2 service.
     * @return \core\oauth2\issuer
     */
    private static function create_facebook() {
        // Facebook is a custom setup.
        $record = (object) [
            'name' => 'Facebook',
            'image' => 'https://facebookbrand.com/wp-content/themes/fb-branding/prj-fb-branding/assets/images/fb-art.png',
            'baseurl' => '',
            'loginscopes' => 'public_profile email',
            'loginscopesoffline' => 'public_profile email',
            'showonloginpage' => true
        ];

        $issuer = new issuer(0, $record);
        $issuer->create();

        $endpoints = [
            'authorization_endpoint' => 'https://www.facebook.com/v2.8/dialog/oauth',
            'token_endpoint' => 'https://graph.facebook.com/v2.8/oauth/access_token',
            'userinfo_endpoint' => 'https://graph.facebook.com/v2.8/me?fields=id,first_name,last_name,link,picture,name,email'
        ];

        foreach ($endpoints as $name => $url) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'name' => $name,
                'url' => $url
            ];
            $endpoint = new endpoint(0, $record);
            $endpoint->create();
        }

        // Create the field mappings.
        $mapping = [
            'name' => 'alternatename',
            'last_name' => 'lastname',
            'email' => 'email',
            'first_name' => 'firstname',
            'picture-data-url' => 'picture',
            'link' => 'url',
        ];
        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal
            ];
            $userfieldmapping = new user_field_mapping(0, $record);
            $userfieldmapping->create();
        }
        return $issuer;
    }

    /**
     * Create a microsoft ready OAuth 2 service.
     * @return \core\oauth2\issuer
     */
    private static function create_microsoft() {
        // Microsoft is a custom setup.
        $record = (object) [
            'name' => 'Microsoft',
            'image' => 'https://www.microsoft.com/favicon.ico',
            'baseurl' => '',
            'loginscopes' => 'openid profile email user.read',
            'loginscopesoffline' => 'openid profile email user.read offline_access',
            'showonloginpage' => true
        ];

        $issuer = new issuer(0, $record);
        $issuer->create();

        $endpoints = [
            'authorization_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'token_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'userinfo_endpoint' => 'https://graph.microsoft.com/v1.0/me/',
            'userpicture_endpoint' => 'https://graph.microsoft.com/v1.0/me/photo/$value',
        ];

        foreach ($endpoints as $name => $url) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'name' => $name,
                'url' => $url
            ];
            $endpoint = new endpoint(0, $record);
            $endpoint->create();
        }

        // Create the field mappings.
        $mapping = [
            'givenName' => 'firstname',
            'surname' => 'lastname',
            'userPrincipalName' => 'email',
            'displayName' => 'alternatename',
            'officeLocation' => 'address',
            'mobilePhone' => 'phone1',
            'preferredLanguage' => 'lang'
        ];
        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal
            ];
            $userfieldmapping = new user_field_mapping(0, $record);
            $userfieldmapping->create();
        }
        return $issuer;
    }

    /**
     * Create one of the standard issuers.
     * @param string $type One of google, facebook, microsoft
     * @return \core\oauth2\issuer
     */
    public static function create_standard_issuer($type) {
        require_capability('moodle/site:config', context_system::instance());
        if ($type == 'google') {
            return self::create_google();
        } else if ($type == 'microsoft') {
            return self::create_microsoft();
        } else if ($type == 'facebook') {
            return self::create_facebook();
        } else {
            throw new moodle_exception('OAuth 2 service type not recognised: ' . $type);
        }
    }

    /**
     * List all the issuers, ordered by the sortorder field
     * @return \core\oauth2\issuer[]
     */
    public static function get_all_issuers() {
        return issuer::get_records([], 'sortorder');
    }

    /**
     * Get a single issuer by id.
     *
     * @param int $id
     * @return \core\oauth2\issuer
     */
    public static function get_issuer($id) {
        return new issuer($id);
    }

    /**
     * Get a single endpoint by id.
     *
     * @param int $id
     * @return \core\oauth2\endpoint
     */
    public static function get_endpoint($id) {
        return new endpoint($id);
    }

    /**
     * Get a single user field mapping by id.
     *
     * @param int $id
     * @return \core\oauth2\user_field_mapping
     */
    public static function get_user_field_mapping($id) {
        return new user_field_mapping($id);
    }

    /**
     * Get the system account for an installed OAuth service.
     * Never ever ever expose this to a webservice because it contains the refresh token which grants API access.
     *
     * @param \core\oauth2\issuer $issuer
     * @return system_account|false
     */
    public static function get_system_account(issuer $issuer) {
        return system_account::get_record(['issuerid' => $issuer->get('id')]);
    }

    /**
     * Get the full list of system scopes required by an oauth issuer.
     * This includes the list required for login as well as any scopes injected by the oauth2_system_scopes callback in plugins.
     *
     * @param \core\oauth2\issuer $issuer
     * @return string
     */
    public static function get_system_scopes_for_issuer($issuer) {
        $scopes = $issuer->get('loginscopesoffline');

        $pluginsfunction = get_plugins_with_function('oauth2_system_scopes', 'lib.php');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                // Get additional scopes from the plugin.
                $pluginscopes = $pluginfunction($issuer);
                if (empty($pluginscopes)) {
                    continue;
                }

                // Merge the additional scopes with the existing ones.
                $additionalscopes = explode(' ', $pluginscopes);

                foreach ($additionalscopes as $scope) {
                    if (!empty($scope)) {
                        if (strpos(' ' . $scopes . ' ', ' ' . $scope . ' ') === false) {
                            $scopes .= ' ' . $scope;
                        }
                    }
                }
            }
        }

        return $scopes;
    }

    /**
     * Get an authenticated oauth2 client using the system account.
     * This call uses the refresh token to get an access token.
     *
     * @param \core\oauth2\issuer $issuer
     * @return \core\oauth2\client|false An authenticated client (or false if the token could not be upgraded)
     * @throws moodle_exception Request for token upgrade failed for technical reasons
     */
    public static function get_system_oauth_client(issuer $issuer) {
        $systemaccount = self::get_system_account($issuer);
        if (empty($systemaccount)) {
            return false;
        }
        // Get all the scopes!
        $scopes = self::get_system_scopes_for_issuer($issuer);

        $client = new \core\oauth2\client($issuer, null, $scopes, true);

        if (!$client->is_logged_in()) {
            if (!$client->upgrade_refresh_token($systemaccount)) {
                return false;
            }
        }
        return $client;
    }

    /**
     * Get an authenticated oauth2 client using the current user account.
     * This call does the redirect dance back to the current page after authentication.
     *
     * @param \core\oauth2\issuer $issuer The desired OAuth issuer
     * @param moodle_url $currenturl The url to the current page.
     * @param string $additionalscopes The additional scopes required for authorization.
     * @return \core\oauth2\client
     */
    public static function get_user_oauth_client(issuer $issuer, moodle_url $currenturl, $additionalscopes = '') {
        $client = new \core\oauth2\client($issuer, $currenturl, $additionalscopes);

        return $client;
    }

    /**
     * Get the list of defined endpoints for this OAuth issuer
     *
     * @param \core\oauth2\issuer $issuer The desired OAuth issuer
     * @return \core\oauth2\endpoint[]
     */
    public static function get_endpoints(issuer $issuer) {
        return endpoint::get_records(['issuerid' => $issuer->get('id')]);
    }

    /**
     * Get the list of defined mapping from OAuth user fields to moodle user fields.
     *
     * @param \core\oauth2\issuer $issuer The desired OAuth issuer
     * @return \core\oauth2\user_field_mapping[]
     */
    public static function get_user_field_mappings(issuer $issuer) {
        return user_field_mapping::get_records(['issuerid' => $issuer->get('id')]);
    }

    /**
     * Guess an image from the discovery URL.
     *
     * @param \core\oauth2\issuer $issuer The desired OAuth issuer
     */
    protected static function guess_image($issuer) {
        if (empty($issuer->get('image'))) {
            $baseurl = parse_url($issuer->get('baseurl'));
            $imageurl = $baseurl['scheme'] . '://' . $baseurl['host'] . '/favicon.ico';
            $issuer->set('image', $imageurl);
            $issuer->update();
        }
    }

    /**
     * If the discovery endpoint exists for this issuer, try and determine the list of valid endpoints.
     *
     * @param issuer $issuer
     * @return int The number of discovered services.
     */
    protected static function discover_endpoints($issuer) {
        $curl = new curl();

        if (empty($issuer->get('baseurl'))) {
            return 0;
        }

        $url = $issuer->get_endpoint_url('discovery');
        if (!$url) {
            $url = $issuer->get('baseurl') . '/.well-known/openid-configuration';
        }

        if (!$json = $curl->get($url)) {
            $msg = 'Could not discover end points for identity issuer' . $issuer->get('name');
            throw new moodle_exception($msg);
        }

        if ($msg = $curl->error) {
            throw new moodle_exception('Could not discover service endpoints: ' . $msg);
        }

        $info = json_decode($json);
        if (empty($info)) {
            $msg = 'Could not discover end points for identity issuer' . $issuer->get('name');
            throw new moodle_exception($msg);
        }

        foreach (endpoint::get_records(['issuerid' => $issuer->get('id')]) as $endpoint) {
            if ($endpoint->get('name') != 'discovery_endpoint') {
                $endpoint->delete();
            }
        }

        foreach ($info as $key => $value) {
            if (substr_compare($key, '_endpoint', - strlen('_endpoint')) === 0) {
                $record = new stdClass();
                $record->issuerid = $issuer->get('id');
                $record->name = $key;
                $record->url = $value;

                $endpoint = new endpoint(0, $record);
                $endpoint->create();
            }

            if ($key == 'scopes_supported') {
                $issuer->set('scopessupported', implode(' ', $value));
                $issuer->update();
            }
        }

        // We got to here - must be a decent OpenID connect service. Add the default user field mapping list.
        foreach (user_field_mapping::get_records(['issuerid' => $issuer->get('id')]) as $userfieldmapping) {
            $userfieldmapping->delete();
        }

        // Create the field mappings.
        $mapping = [
            'given_name' => 'firstname',
            'middle_name' => 'middlename',
            'family_name' => 'lastname',
            'email' => 'email',
            'website' => 'url',
            'nickname' => 'alternatename',
            'picture' => 'picture',
            'address' => 'address',
            'phone' => 'phone1',
            'locale' => 'lang'
        ];
        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal
            ];
            $userfieldmapping = new user_field_mapping(0, $record);
            $userfieldmapping->create();
        }

        return endpoint::count_records(['issuerid' => $issuer->get('id')]);
    }

    /**
     * Take the data from the mform and update the issuer.
     *
     * @param stdClass $data
     * @return \core\oauth2\issuer
     */
    public static function update_issuer($data) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer(0, $data);

        // Will throw exceptions on validation failures.
        $issuer->update();

        // Perform service discovery.
        self::discover_endpoints($issuer);
        self::guess_image($issuer);
        return $issuer;
    }

    /**
     * Take the data from the mform and create the issuer.
     *
     * @param stdClass $data
     * @return \core\oauth2\issuer
     */
    public static function create_issuer($data) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer(0, $data);

        // Will throw exceptions on validation failures.
        $issuer->create();

        // Perform service discovery.
        self::discover_endpoints($issuer);
        self::guess_image($issuer);
        return $issuer;
    }

    /**
     * Take the data from the mform and update the endpoint.
     *
     * @param stdClass $data
     * @return \core\oauth2\endpoint
     */
    public static function update_endpoint($data) {
        require_capability('moodle/site:config', context_system::instance());
        $endpoint = new endpoint(0, $data);

        // Will throw exceptions on validation failures.
        $endpoint->update();

        return $endpoint;
    }

    /**
     * Take the data from the mform and create the endpoint.
     *
     * @param stdClass $data
     * @return \core\oauth2\endpoint
     */
    public static function create_endpoint($data) {
        require_capability('moodle/site:config', context_system::instance());
        $endpoint = new endpoint(0, $data);

        // Will throw exceptions on validation failures.
        $endpoint->create();
        return $endpoint;
    }

    /**
     * Take the data from the mform and update the user field mapping.
     *
     * @param stdClass $data
     * @return \core\oauth2\user_field_mapping
     */
    public static function update_user_field_mapping($data) {
        require_capability('moodle/site:config', context_system::instance());
        $userfieldmapping = new user_field_mapping(0, $data);

        // Will throw exceptions on validation failures.
        $userfieldmapping->update();

        return $userfieldmapping;
    }

    /**
     * Take the data from the mform and create the user field mapping.
     *
     * @param stdClass $data
     * @return \core\oauth2\user_field_mapping
     */
    public static function create_user_field_mapping($data) {
        require_capability('moodle/site:config', context_system::instance());
        $userfieldmapping = new user_field_mapping(0, $data);

        // Will throw exceptions on validation failures.
        $userfieldmapping->create();
        return $userfieldmapping;
    }

    /**
     * Reorder this identity issuer.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the identity issuer to move.
     * @return boolean
     */
    public static function move_up_issuer($id) {
        require_capability('moodle/site:config', context_system::instance());
        $current = new issuer($id);

        $sortorder = $current->get('sortorder');
        if ($sortorder == 0) {
            return false;
        }

        $sortorder = $sortorder - 1;
        $current->set('sortorder', $sortorder);

        $filters = array('sortorder' => $sortorder);
        $children = issuer::get_records($filters, 'id');
        foreach ($children as $needtoswap) {
            $needtoswap->set('sortorder', $sortorder + 1);
            $needtoswap->update();
        }

        // OK - all set.
        $result = $current->update();

        return $result;
    }

    /**
     * Reorder this identity issuer.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the identity issuer to move.
     * @return boolean
     */
    public static function move_down_issuer($id) {
        require_capability('moodle/site:config', context_system::instance());
        $current = new issuer($id);

        $max = issuer::count_records();
        if ($max > 0) {
            $max--;
        }

        $sortorder = $current->get('sortorder');
        if ($sortorder >= $max) {
            return false;
        }
        $sortorder = $sortorder + 1;
        $current->set('sortorder', $sortorder);

        $filters = array('sortorder' => $sortorder);
        $children = issuer::get_records($filters);
        foreach ($children as $needtoswap) {
            $needtoswap->set('sortorder', $sortorder - 1);
            $needtoswap->update();
        }

        // OK - all set.
        $result = $current->update();

        return $result;
    }

    /**
     * Disable an identity issuer.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the identity issuer to disable.
     * @return boolean
     */
    public static function disable_issuer($id) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer($id);

        $issuer->set('enabled', 0);
        return $issuer->update();
    }


    /**
     * Enable an identity issuer.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the identity issuer to enable.
     * @return boolean
     */
    public static function enable_issuer($id) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer($id);

        $issuer->set('enabled', 1);
        return $issuer->update();
    }

    /**
     * Delete an identity issuer.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the identity issuer to delete.
     * @return boolean
     */
    public static function delete_issuer($id) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer($id);

        $systemaccount = self::get_system_account($issuer);
        if ($systemaccount) {
            $systemaccount->delete();
        }
        $endpoints = self::get_endpoints($issuer);
        if ($endpoints) {
            foreach ($endpoints as $endpoint) {
                $endpoint->delete();
            }
        }

        // Will throw exceptions on validation failures.
        return $issuer->delete();
    }

    /**
     * Delete an endpoint.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the endpoint to delete.
     * @return boolean
     */
    public static function delete_endpoint($id) {
        require_capability('moodle/site:config', context_system::instance());
        $endpoint = new endpoint($id);

        // Will throw exceptions on validation failures.
        return $endpoint->delete();
    }

    /**
     * Delete a user_field_mapping.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param int $id The id of the user_field_mapping to delete.
     * @return boolean
     */
    public static function delete_user_field_mapping($id) {
        require_capability('moodle/site:config', context_system::instance());
        $userfieldmapping = new user_field_mapping($id);

        // Will throw exceptions on validation failures.
        return $userfieldmapping->delete();
    }

    /**
     * Perform the OAuth dance and get a refresh token.
     *
     * Requires moodle/site:config capability at the system context.
     *
     * @param \core\oauth2\issuer $issuer
     * @param moodle_url $returnurl The url to the current page (we will be redirected back here after authentication).
     * @return boolean
     */
    public static function connect_system_account($issuer, $returnurl) {
        require_capability('moodle/site:config', context_system::instance());

        // We need to authenticate with an oauth 2 client AS a system user and get a refresh token for offline access.
        $scopes = self::get_system_scopes_for_issuer($issuer);

        // Allow callbacks to inject non-standard scopes to the auth request.

        $client = new client($issuer, $returnurl, $scopes, true);

        if (!optional_param('response', false, PARAM_BOOL)) {
            $client->log_out();
        }

        if (optional_param('error', '', PARAM_RAW)) {
            return false;
        }

        if (!$client->is_logged_in()) {
            redirect($client->get_login_url());
        }

        $refreshtoken = $client->get_refresh_token();
        if (!$refreshtoken) {
            return false;
        }

        $systemaccount = self::get_system_account($issuer);
        if ($systemaccount) {
            $systemaccount->delete();
        }

        $userinfo = $client->get_userinfo();

        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->refreshtoken = $refreshtoken;
        $record->grantedscopes = $scopes;
        $record->email = isset($userinfo['email']) ? $userinfo['email'] : '';
        $record->username = $userinfo['username'];

        $systemaccount = new system_account(0, $record);

        $systemaccount->create();

        $client->log_out();
        return true;
    }
}
