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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_iomadoidc;

/**
 * OpenID Connect Client
 */
class iomadoidcclient {
    /** @var \auth_iomadoidc\httpclientinterface An HTTP client to use. */
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
     * @param \auth_iomadoidc\httpclientinterface $httpclient An HTTP client to use for background communication.
     */
    public function __construct(\auth_iomadoidc\httpclientinterface $httpclient) {
        $this->httpclient = $httpclient;
    }

    /**
     * Set client details/credentials.
     *
     * @param string $id The registered client ID.
     * @param string $secret The registered client secret.
     * @param string $scope The requested OID scope.
     * @param string $redirecturi The registered client redirect URI.
     */
    public function setcreds($id, $secret, $redirecturi, $resource, $scope) {
        $this->clientid = $id;
        $this->clientsecret = $secret;
        $this->redirecturi = $redirecturi;
        if (!empty($resource)) {
            $this->resource = $resource;
        } else {
            $this->resource = (static::use_chinese_api() === true) ? 'https://microsoftgraph.chinacloudapi.cn' : 'https://graph.microsoft.com';
        }
        $this->scope = (!empty($scope)) ? $scope : 'openid profile email';
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
     * Get the set scope.
     *
     * @return string The set scope.
     */
    public function get_scope() {
        return (isset($this->scope)) ? $this->scope : null;
    }

    /**
     * Set OIDC endpoints.
     *
     * @param array $endpoints Array of endpoints. Can have keys 'auth', and 'token'.
     */
    public function setendpoints($endpoints) {
        foreach ($endpoints as $type => $uri) {
            if (clean_param($uri, PARAM_URL) !== $uri) {
                throw new \moodle_exception('erroriomadoidcclientinvalidendpoint', 'auth_iomadoidc');
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
     * @param array $extraparams Additional parameters to send with the OIDC request.
     * @return array Array of request parameters.
     */
    protected function getauthrequestparams($promptlogin = false, array $stateparams = array(), array $extraparams = array()) {
        global $SESSION;

        $nonce = 'N'.uniqid();
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientid,
            'scope' =>  $this->scope,
            'nonce' => $nonce,
            'response_mode' => 'form_post',
            'resource' => $this->resource,
            'state' => $this->getnewstate($nonce, $stateparams),
            'redirect_uri' => $this->redirecturi
        ];
        if ($promptlogin === true) {
            $params['prompt'] = 'login';
        }

        $domainhint = get_config('auth_iomadoidc' . "_" . $SESSION->currenteditingcompany, 'domainhint');
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
        $DB->insert_record('auth_iomadoidc_state', $staterec);
        return $staterec->state;
    }

    /**
     * Perform an authorization request by redirecting resource owner's user agent to auth endpoint.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the OIDC request.
     */
    public function authrequest($promptlogin = false, array $stateparams = array(), array $extraparams = array()) {
        global $DB;
        if (empty($this->clientid)) {
            throw new \moodle_exception('erroriomadoidcclientnocreds', 'auth_iomadoidc');
        }

        if (empty($this->endpoints['auth'])) {
            throw new \moodle_exception('erroriomadoidcclientnoauthendpoint', 'auth_iomadoidc');
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
            throw new \moodle_exception('erroriomadoidcclientnotokenendpoint', 'auth_iomadoidc');
        }

        if (strpos($this->endpoints['token'], 'https://') !== 0) {
            throw new \moodle_exception('erroriomadoidcclientinsecuretokenendpoint', 'auth_iomadoidc');
        }

        $params = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => 'openid profile email',
            'resource' => $this->resource,
            'client_id' => $this->clientid,
            'client_secret' => $this->clientsecret,
        ];

        try {
            $returned = $this->httpclient->post($this->endpoints['token'], $params);
            return \auth_iomadoidc\utils::process_json_response($returned, ['token_type' => null, 'id_token' => null]);
        } catch (\Exception $e) {
            \auth_iomadoidc\utils::debug('Error in rocredsrequest request', 'iomadoidcclient::rocredsrequest', $e->getMessage());
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
            throw new \moodle_exception('erroriomadoidcclientnotokenendpoint', 'auth_iomadoidc');
        }

        $params = [
            'client_id' => $this->clientid,
            'client_secret' => $this->clientsecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirecturi,
        ];

        $returned = $this->httpclient->post($this->endpoints['token'], $params);
        return \auth_iomadoidc\utils::process_json_response($returned, ['id_token' => null]);
    }
}
