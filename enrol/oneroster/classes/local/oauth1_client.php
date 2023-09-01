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
 * One Roster Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/oauthlib.php');

use moodle_url;
use oauth_helper as abstract_oauth_client;
use stdClass;

/**
 * One Roster Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oauth1_client extends abstract_oauth_client {
    /** @var string */
    protected $baseurl;

    /** @var string The URL of the OAuth2 Server */
    protected $tokenurl;

    /** @var string The client id */
    protected $clientid;

    /** @var string the secret associated with the client id */
    protected $clientsecret;

    /**
     * Get an instance of the One Roster API.
     *
     * @param string $tokenurl The OAuth server
     * @param string $server The server hosting the One Roster endpoint
     * @param string $clientid The OAuth Client ID.
     * @param string $clientsecret The secret associated with the ClientId
     */
    public function __construct(string $tokenurl, string $server, string $clientid, string $clientsecret) {
        // Get the Base URL for this version of the One Roster API.
        $this->baseurl = $this->get_base_url($server)->out(false);

        parent::__construct([
            'oauth_consumer_key' => $clientid,
            'oauth_consumer_secret' => $clientsecret,
            'api_root' => $server,
        ]);
        $this->access_token = '';
        $this->access_token_secret = '';
    }

    /**
     * Get all of the scopes required for this OAuth2 Implementation.
     *
     * @return string[]
     */
    abstract protected function get_all_scopes(): array;

    /**
     * Authenticate against the One Roster API.
     *
     * Not required for OAuth 1.0.
     */
    public function authenticate(): void {
    }

    /**
     * Get the request information.
     *
     * @return  array
     */
    public function get_request_info(): array {
        return $this->http->info;
    }

    /**
     * Initilize oauth request parameters, including:
     *    oauth_consumer_key="0685bd9184jfhq22",
     *    oauth_token="ad180jjd733klru7",
     *    oauth_signature_method="HMAC-SHA1",
     *    oauth_signature="wOJIO9A2W5mFwDgiDvZbTSMK%2FPY%3D",
     *    oauth_timestamp="137131200",
     *    oauth_nonce="4572616e48616d6d65724c61686176",
     *    oauth_version="1.0"
     * To access protected resources, oauth_token should be defined
     *
     * @param string $url
     * @param string $params
     * @param string $method
     * @return array
     */
    public function prepare_oauth_parameters($url, $params, $method = 'POST') {
        if (is_array($params)) {
            $oauthparams = $params;
        } else {
            $oauthparams = [];
        }
        unset($oauthparams['oauth_token']);
        $oauthparams['oauth_version'] = '1.0';
        $oauthparams['oauth_nonce'] = $this->get_nonce();
        $oauthparams['oauth_timestamp'] = $this->get_timestamp();
        $oauthparams['oauth_consumer_key'] = $this->consumer_key;
        $oauthparams['oauth_signature_method'] = 'HMAC-SHA256';

        $oauthparams['oauth_signature'] = $this->sign($method, $url, $oauthparams, $this->sign_secret);

        return $oauthparams;
    }

    /**
     * Create signature for oauth request.
     *
     * @param   string $method
     * @param   string $url
     * @param   array $params
     * @param   string $secret
     * @return  string
     */
    public function sign($method, $url, $params, $secret) {
        $moodleurl = new moodle_url($url, $params);
        $sig = [
            strtoupper($method),
            rawurlencode($moodleurl->out_omit_querystring()),
            rawurlencode($this->get_signable_parameters($moodleurl->params())),
        ];

        $basestring = implode('&', $sig);
        return base64_encode(hash_hmac('sha256', $basestring, $secret, true));
    }
}
