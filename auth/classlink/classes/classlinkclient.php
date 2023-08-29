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
 * @package auth_classlink
 * @author Gopal Sharma <gopalsharma66@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2020 Gopal Sharma <gopalsharma66@gmail.com>
 */

namespace auth_classlink;

/**
 * ClassLink OAuth2 Client
 */
class classlinkclient {
    /** @var \auth_classlink\httpclientinterface An HTTP client to use. */
    protected $httpclient;

    /** @var string The client ID. */
    protected $clientid;

    /** @var string The client secret. */
    protected $clientsecret;

    /** @var string The client redirect URI. */
    protected $redirecturi;

    /** @var array Array of endpoints. */
    protected $endpoints = [];

    /**
     * Constructor.
     *
     * @param \auth_classlink\httpclientinterface $httpclient An HTTP client to use for background communication.
     */
    public function __construct(\auth_classlink\httpclientinterface $httpclient) {
        $this->httpclient = $httpclient;
    }

    /**
     * Set client details/credentials.
     *
     * @param string $id The registered client ID.
     * @param string $secret The registered client secret.
     * @param string $redirecturi The registered client redirect URI.
     */
    public function setcreds($id, $secret, $redirecturi, $resource) {
        $this->clientid = $id;
        $this->clientsecret = $secret;
        $this->redirecturi = $redirecturi;
        $this->resource = (!empty($resource)) ? $resource : 'https://graph.windows.net';
    }

    /**
     * Get the set client ID.
     *
     * @return string The set client ID.
     */
    public function get_clientid() {
        return (isset($this->clientid)) ? $this->clientid : null;
    }

    /**
     * Get the set client secret.
     *
     * @return string The set client secret.
     */
    public function get_clientsecret() {
        return (isset($this->clientsecret)) ? $this->clientsecret : null;
    }

    /**
     * Get the set redirect URI.
     *
     * @return string The set redirect URI.
     */
    public function get_redirecturi() {
        return (isset($this->redirecturi)) ? $this->redirecturi : null;
    }

    /**
     * Get the set resource.
     *
     * @return string The set resource.
     */
    public function get_resource() {
        return (isset($this->resource)) ? $this->resource : null;
    }

    /**
     * Set classlink endpoints.
     *
     * @param array $endpoints Array of endpoints. Can have keys 'auth', and 'token'.
     */
    public function setendpoints($endpoints) {
        foreach ($endpoints as $type => $uri) {
            if (clean_param($uri, PARAM_URL) !== $uri) {
                throw new \moodle_exception('errorclasslinkclientinvalidendpoint', 'auth_classlink');
            }
            $this->endpoints[$type] = $uri;
        }
    }

    public function get_endpoint($endpoint) {
        return (isset($this->endpoints[$endpoint])) ? $this->endpoints[$endpoint] : null;
    }

    /**
     * Get an array of authorization request parameters.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the classlink request.
     * @return array Array of request parameters.
     */
    protected function getauthrequestparams($promptlogin = false, array $stateparams = array(), array $extraparams = array()) {
        $nonce = 'N'.uniqid();
        $lang = current_language();
        $params = [
            'scope' => get_config('auth_classlink', 'scope'), // Get the custom scope from settings.
            'client_id' => $this->clientid,
            'nonce' => $nonce,
            'response_mode' => 'form_post',
            'resource' => $this->resource,
            'response_type' => 'code',
            'state' => $this->getnewstate($nonce, $stateparams),
            'redirect_uri' => $this->redirecturi,
            'ui_locales' => $lang
        ];
        if ($promptlogin === true) {
            $params['prompt'] = 'login';
        }

        $domainhint = get_config('auth_classlink', 'domainhint');
        if (!empty($domainhint)) {
            $params['domain_hint'] = $domainhint;
        }

        $params = array_merge($params, $extraparams);
        return $params;
    }

    /**
     * Generate a new state parameter.
     *
     * @param string $nonce The generated nonce value.
     * @return string The new state value.
     */
    protected function getnewstate($nonce, array $stateparams = array()) {
        global $DB;
        $staterec = new \stdClass;
        $staterec->sesskey = sesskey();
        $staterec->state = random_string(15);
        $staterec->nonce = $nonce;
        $staterec->timecreated = time();
        $staterec->additionaldata = serialize($stateparams);
        $DB->insert_record('auth_classlink_state', $staterec);
        return $staterec->state;
    }

    /**
     * Perform an authorization request by redirecting resource owner's user agent to auth endpoint.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the classlink request.
     */
    public function authrequest($promptlogin = false, array $stateparams = array(), array $extraparams = array()) {
        global $DB;
        if (empty($this->clientid)) {
            throw new \moodle_exception('errorclasslinkclientnocreds', 'auth_classlink');
        }

        if (empty($this->endpoints['auth'])) {
            throw new \moodle_exception('errorclasslinkclientnoauthendpoint', 'auth_classlink');
        }

        $params = $this->getauthrequestparams($promptlogin, $stateparams, $extraparams);
        $redirecturl = new \moodle_url($this->endpoints['auth'], $params);
        redirect($redirecturl);
    }

    /**
     * Make a token request using the resource-owner credentials login flow.
     *
     * @param string $username The resource owner's username.
     * @param string $password The resource owner's password.
     * @return array Received parameters.
     */
    public function rocredsrequest($username, $password) {
        if (empty($this->endpoints['token'])) {
            throw new \moodle_exception('errorclasslinkclientnotokenendpoint', 'auth_classlink');
        }

        if (strpos($this->endpoints['token'], 'https://') !== 0) {
            throw new \moodle_exception('errorclasslinkclientinsecuretokenendpoint', 'auth_classlink');
        }

        $params = [
            'scope' => get_config('auth_classlink', 'scope'),
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'resource' => $this->resource,
            'client_id' => $this->clientid,
            'client_secret' => $this->clientsecret
        ];

        try {
            $returned = $this->httpclient->post($this->endpoints['token'], $params);
            return \auth_classlink\utils::process_json_response($returned, ['token_type' => null, 'id_token' => null]);
        } catch (\Exception $e) {
            \auth_classlink\utils::debug('Error in rocredsrequest request', 'classlinkclient::rocredsrequest', $e->getMessage());
            return false;
        }
    }

    /**
     * Exchange an authorization code for an access token.
     *
     * @param string $tokenendpoint The token endpoint URI.
     * @param string $code An authorization code.
     * @return array Received parameters.
     */
    public function tokenrequest($code) {
        if (empty($this->endpoints['token'])) {
            throw new \moodle_exception('errorclasslinkclientnotokenendpoint', 'auth_classlink');
        }

        $params = [
            'scope' => get_config('auth_classlink', 'scope'),
            'client_id' => $this->clientid,
            'client_secret' => $this->clientsecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirecturi
        ];

        $returned = $this->httpclient->post($this->endpoints['token'], $params);

        return \auth_classlink\utils::process_json_response($returned, ['id_token' => null]);
    }
}
