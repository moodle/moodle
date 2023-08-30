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

use moodle_exception;
use moodle_url;
use oauth2_client as abstract_oauth_client;
use stdClass;

/**
 * One Roster Client for OAuth 2.0.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oauth2_client extends abstract_oauth_client {
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
     * @param string $tokenurl The OAuth2 server
     * @param string $server The server hosting the One Roster endpoint
     * @param string $clientid The OAuth2 Client ID.
     * @param string $clientsecret The secret associated witht the oauth2 client
     */
    public function __construct(string $tokenurl, string $server, string $clientid, string $clientsecret) {
        // Get the Base URL for this version of the One Roster API.
        $this->baseurl = $this->get_base_url($server)->out(false);

        $this->tokenurl = $tokenurl;

        parent::__construct(
            $clientid,
            $clientsecret,

            // One Roster does not require a return url because it is entirely performed in CLI without user
            // interaction.
            new moodle_url(''),

            // The list of scope to authenticate for.
            implode(' ', $this->get_all_scopes())
        );

        // The parent oauth2_client class has these as private but the authenticate method there only works for
        // browser-based authentication.
        // The One Roster implementation needs to customise the authentication method for non-browser use.
        $this->clientid = $clientid;
        $this->clientsecret = $clientsecret;
    }

    /**
     * Returns the auth url for OAuth 2.0 Client request>
     *
     * @return  string the auth url
     */
    protected function auth_url() {
        throw new \coding_exception('One Roster does not support client-side OAuth2 Authentication');
    }

    /**
     * Return the token URL.
     *
     * @return  moodle_url
     */
    protected function token_url(): moodle_url {
        return new moodle_url("{$this->tokenurl}");
    }

    /**
     * Get all of the scopes required for this OAuth2 Implementation.
     *
     * @return string[]
     */
    abstract protected function get_all_scopes(): array;

    /**
     * Authenticate against the One Roster API.
     */
    public function authenticate(): void {
        $requestedscopes = implode(' ', $this->get_all_scopes());

        $params = [
            'grant_type' => 'client_credentials',
            'scope' => $requestedscopes,
        ];

        // Basic auth isbased on a base64-encoded clientid and secret.
        $idsecret = base64_encode(urlencode($this->clientid) . ':' . urlencode($this->clientsecret));
        $this->setHeader("Authorization: Basic {$idsecret}");

        $this->setHeader('Content-Type: application/x-www-form-urlencoded');
        $request = $this->post(
            $this->token_url(),
            $this->build_post_data($params)
        );

        $info = $this->get_request_info();
        if ($info['http_code'] < 200 || $info['http_code'] >= 300) {
            throw new moodle_exception('Could not upgrade oauth token');
        }

        $response = json_decode($request);

        if (is_null($response)) {
            throw new moodle_exception("Could not decode JSON token response: " . $request);
        }

        if (!empty($response->error)) {
            throw new moodle_exception($response->error . ' ' . $response->error_description);
        }

        if (!isset($response->access_token)) {
            throw new moodle_exception('No access token found in request');
        }

        // Store the access token for future requests against this API.
        $this->set_access_token(
            // The returned access token.
            $response->access_token,

            // Store the token an expiry time.
            // Expires 10 seconds before actual expiry.
            time() + $response->expires_in - 10,

            // Authorised scopes.
            // This should be returned in the response, but poorly behaved clients may not do so.
            property_exists($response, 'scope') ? $response->scope : $requestedscopes
        );
    }

    /**
     * Set the access token to use.
     *
     * This can use a previously retrieved access token to avoid reauthentication.
     *
     * @param   string $token
     * @param   int $expiry
     * @param   string $scope
     */
    public function set_access_token(string $token, int $expiry, string $scope): void {
        $this->accesstoken = (object) [
            // The returned access token.
            'token' => $token,

            // Store the token an expiry time.
            // Expires 10 seconds before actual expiry.
            'expires' => $expiry - 10,

            // Authorised scopes.
            'scope' => $scope,
        ];
    }

    /**
     * Fetch the access token data.
     *
     * @return  stdClass
     */
    public function get_access_token(): stdClass {
        return $this->accesstoken;
    }

    /**
     * Get request info.
     *
     * @return  array
     */
    public function get_request_info(): array {
        return $this->info;
    }

    /**
     * Get the Base URL for this One Roster API version.
     *
     * @param string $server The hostname
     * @return moodle_url
     */
    abstract protected function get_base_url(string $server): moodle_url;
}
