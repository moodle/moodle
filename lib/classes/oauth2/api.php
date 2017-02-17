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
 * @package    core_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

require_once($CFG->libdir . '/filelib.php');

use context_system;
use curl;
use stdClass;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Static list of api methods for system oauth2 configuration.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Called from install.php and upgrade.php - install the default list of issuers
     * @return int The number of issuers installed.
     */
    public static function install_default_issuers() {
        // Setup default list of identity issuers.
        $record = (object) [
            'name' => 'Google',
            'image' => 'https://accounts.google.com/favicon.ico',
            'behaviour' => issuer::BEHAVIOUR_OPENID_CONNECT,
            'baseurl' => 'http://accounts.google.com/',
            'clientid' => '',
            'clientsecret' => '',
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

        $record = (object) [
            'name' => 'Microsoft',
            'image' => 'https://www.microsoft.com/favicon.ico',
            'behaviour' => issuer::BEHAVIOUR_MICROSOFT,
            'baseurl' => 'http://login.microsoftonline.com/common/oauth2/v2.0/',
            'clientid' => '',
            'clientsecret' => '',
            'showonloginpage' => true
        ];

        $issuer = new issuer(0, $record);
        $issuer->create();

        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'authorization_endpoint',
            'url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize'
        ];
        $endpoint = new endpoint(0, $record);
        $endpoint->create();

        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'token_endpoint',
            'url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token'
        ];
        $endpoint = new endpoint(0, $record);
        $endpoint->create();

        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'end_session_endpoint',
            'url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token'
        ];
        $endpoint = new endpoint(0, $record);
        $endpoint->create();
        return issuer::count_records();
    }

    public static function get_all_issuers() {
        return issuer::get_records([], 'sortorder');
    }

    public static function get_issuer($id) {
        return new issuer($id);
    }

    public static function get_system_account(issuer $issuer) {
        return system_account::get_record(['issuerid' => $issuer->get('id')]);
    }

    public static function get_system_oauth_client(issuer $issuer) {
    }

    public static function get_user_oauth_client(issuer $issuer, moodle_url $currenturl, $additionalscopes = '') {
        $client = \core\oauth2\client::create($issuer, $currenturl, $additionalscopes);

        if (!$client->is_logged_in()) {
            redirect($client->get_login_url());
        }
        return $client;
    }

    public static function get_endpoints(issuer $issuer) {
        require_capability('moodle/site:config', context_system::instance());

        return endpoint::get_records(['issuerid' => $issuer->get('id')]);
    }

    protected static function guess_image($issuer) {
        if (empty($issuer->get('image'))) {
            $baseurl = parse_url($issuer->get('discoveryurl'));
            $imageurl = $baseurl['scheme'] . '://' . $baseurl['host'] . '/favicon.ico';
            $issuer->set('image', $imageurl);
            $issuer->update();
        }
    }

    /**
     * If the behaviour supports discovery for this issuer, try and determine the list of valid endpoints.
     *
     * @param issuer $issuer
     * @return int The number of discovered services.
     */
    protected static function discover_endpoints($issuer) {
        $curl = new curl();

        if ($issuer->get('behaviour') != issuer::BEHAVIOUR_OPENID_CONNECT) {
            return 0;
        }

        $url = $issuer->get_endpoint_url('discovery');
        if (!$url) {
            $url = $issuer->get('url') . '/.well-known/openid-configuration';
        }

        if (!$json = $curl->get($issuer->get_endpoint_url('discovery'))) {
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

        return endpoint::count_records(['issuerid' => $issuer->get('id')]);
    }

    public static function update_issuer($data) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer(0, $data);

        // Will throw exceptions on validation failures.
        $issuer->update();

        // Perform service discovery.
        self::discover_endpoints($issuer);
        self::guess_image($issuer);
    }

    public static function create_issuer($data) {
        require_capability('moodle/site:config', context_system::instance());
        $issuer = new issuer(0, $data);

        // Will throw exceptions on validation failures.
        $issuer->create();

        // Perform service discovery.
        self::discover_endpoints($issuer);
        self::guess_image($issuer);
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
        $issuer->delete();
    }

    public static function connect_system_account($issuer, $returnurl) {
        require_capability('moodle/site:config', context_system::instance());

        // We need to authenticate with an oauth 2 client AS a system user and get a refresh token for offline access.
        $scopesrequired = 'openid email profile';

        // Allow callbacks to inject non-standard scopes to the auth request.

        $client = client::create($issuer, $returnurl, $scopesrequired, true);

        if (!optional_param('response', false, PARAM_BOOL)) {
            $client->log_out();
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
        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->refreshtoken = $refreshtoken;
        $record->grantedscopes = $scopesrequired;

        $systemaccount = new system_account(0, $record);

        $systemaccount->create();

        $client->log_out();
        return true;
    }
}
