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

use stdClass;
use moodle_url;
use context_system;
use moodle_exception;

/**
 * Static list of api methods for system oauth2 configuration.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Initializes a record for one of the standard issuers to be displayed in the settings.
     * The issuer is not yet created in the database.
     * @param string $type One of google, facebook, microsoft, nextcloud, imsobv2p1
     * @return \core\oauth2\issuer
     */
    public static function init_standard_issuer($type) {
        require_capability('moodle/site:config', context_system::instance());

        $classname = self::get_service_classname($type);
        if (class_exists($classname)) {
            return $classname::init();
        }
        throw new moodle_exception('OAuth 2 service type not recognised: ' . $type);
    }

    /**
     * Create endpoints for standard issuers, based on the issuer created from submitted data.
     * @param string $type One of google, facebook, microsoft, nextcloud, imsobv2p1
     * @param issuer $issuer issuer the endpoints should be created for.
     * @return \core\oauth2\issuer
     */
    public static function create_endpoints_for_standard_issuer($type, $issuer) {
        require_capability('moodle/site:config', context_system::instance());

        $classname = self::get_service_classname($type);
        if (class_exists($classname)) {
            $classname::create_endpoints($issuer);
            return $issuer;
        }
        throw new moodle_exception('OAuth 2 service type not recognised: ' . $type);
    }

    /**
     * Create one of the standard issuers.
     *
     * @param string $type One of google, facebook, microsoft, MoodleNet, nextcloud or imsobv2p1
     * @param string|false $baseurl Baseurl (only required for nextcloud, imsobv2p1 and moodlenet)
     * @return \core\oauth2\issuer
     */
    public static function create_standard_issuer($type, $baseurl = false) {
        require_capability('moodle/site:config', context_system::instance());

        switch ($type) {
            case 'imsobv2p1':
                if (!$baseurl) {
                    throw new moodle_exception('IMS OBv2.1 service type requires the baseurl parameter.');
                }
            case 'nextcloud':
                if (!$baseurl) {
                    throw new moodle_exception('Nextcloud service type requires the baseurl parameter.');
                }
            case 'moodlenet':
                if (!$baseurl) {
                    throw new moodle_exception('MoodleNet service type requires the baseurl parameter.');
                }
            case 'google':
            case 'facebook':
            case 'microsoft':
                $classname = self::get_service_classname($type);
                $issuer = $classname::init();
                if ($baseurl) {
                    $issuer->set('baseurl', $baseurl);
                }
                $issuer->create();
                return self::create_endpoints_for_standard_issuer($type, $issuer);
        }

        throw new moodle_exception('OAuth 2 service type not recognised: ' . $type);
    }


    /**
     * List all the issuers, ordered by the sortorder field
     *
     * @param bool $includeloginonly also include issuers that are configured to be shown only on login page,
     *     By default false, in this case the method returns all issuers that can be used in services
     * @return \core\oauth2\issuer[]
     */
    public static function get_all_issuers(bool $includeloginonly = false) {
        if ($includeloginonly) {
            return issuer::get_records([], 'sortorder');
        } else {
            return array_values(issuer::get_records_select('showonloginpage<>?', [issuer::LOGINONLY], 'sortorder'));
        }
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
        $class = self::get_client_classname($issuer->get('servicetype'));
        $client = new $class($issuer, null, $scopes, true);

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
     * @param bool $autorefresh Should the client support the use of refresh tokens to persist access across sessions.
     * @return \core\oauth2\client
     */
    public static function get_user_oauth_client(issuer $issuer, moodle_url $currenturl, $additionalscopes = '',
            $autorefresh = false) {
        $class = self::get_client_classname($issuer->get('servicetype'));
        $client = new $class($issuer, $currenturl, $additionalscopes, false, $autorefresh);

        return $client;
    }

    /**
     * Get the client classname for an issuer.
     *
     * @param string $type The OAuth issuer type (google, facebook...).
     * @return string The classname for the custom client or core client class if the class for the defined type
     *                 doesn't exist or null type is defined.
     */
    protected static function get_client_classname(?string $type): string {
        // Default core client class.
        $classname = 'core\\oauth2\\client';

        if (!empty($type)) {
            $typeclassname = 'core\\oauth2\\client\\' . $type;
            if (class_exists($typeclassname)) {
                $classname = $typeclassname;
            }
        }

        return $classname;
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
        if (empty($issuer->get('image')) && !empty($issuer->get('baseurl'))) {
            $baseurl = parse_url($issuer->get('baseurl'));
            $imageurl = $baseurl['scheme'] . '://' . $baseurl['host'] . '/favicon.ico';
            $issuer->set('image', $imageurl);
            $issuer->update();
        }
    }

    /**
     * Take the data from the mform and update the issuer.
     *
     * @param stdClass $data
     * @return \core\oauth2\issuer
     */
    public static function update_issuer($data) {
        return self::create_or_update_issuer($data, false);
    }

    /**
     * Take the data from the mform and create the issuer.
     *
     * @param stdClass $data
     * @return \core\oauth2\issuer
     */
    public static function create_issuer($data) {
        return self::create_or_update_issuer($data, true);
    }

    /**
     * Take the data from the mform and create or update the issuer.
     *
     * @param stdClass $data Form data for them issuer to be created/updated.
     * @param bool $create If true, the issuer will be created; otherwise, it will be updated.
     * @return issuer The created/updated issuer.
     */
    protected static function create_or_update_issuer($data, bool $create): issuer {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer($data->id ?? 0, $data);

        // Will throw exceptions on validation failures.
        if ($create) {
            $issuer->create();

            // Perform service discovery.
            $classname = self::get_service_classname($issuer->get('servicetype'));
            $classname::discover_endpoints($issuer);
            self::guess_image($issuer);
        } else {
            $issuer->update();
        }

        return $issuer;
    }

    /**
     * Get the service classname for an issuer.
     *
     * @param string $type The OAuth issuer type (google, facebook...).
     *
     * @return string The classname for this issuer or "Custom" service class if the class for the defined type doesn't exist
     *                 or null type is defined.
     */
    protected static function get_service_classname(?string $type): string {
        // Default custom service class.
        $classname = 'core\\oauth2\\service\\custom';

        if (!empty($type)) {
            $typeclassname = 'core\\oauth2\\service\\' . $type;
            if (class_exists($typeclassname)) {
                $classname = $typeclassname;
            }
        }

        return $classname;
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
        $class = self::get_client_classname($issuer->get('servicetype'));
        $client = new $class($issuer, $returnurl, $scopes, true);

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
        // Get email.
        if (isset($userinfo['email'])) {
            $record->email = $userinfo['email'];
        } else if ($issuer->get_system_email()) {
            $record->email = $issuer->get_system_email();
        } else {
            $record->email = '';
        }
        // Get username.
        if (isset($userinfo['username'])) {
            $record->username = $userinfo['username'];
        } else if ($issuer->get_system_email()) {
            $record->username = $issuer->get_system_email();
        }

        $systemaccount = new system_account(0, $record);

        $systemaccount->create();

        $client->log_out();
        return true;
    }
}
