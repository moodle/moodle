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
 * OIDC client.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * OpenID Connect Client
 */
class oidcclient {
    /** @var httpclientinterface An HTTP client to use. */
    protected $httpclient;

    /** @var string The client ID. */
    protected $clientid;

    /** @var string The client secret. */
    protected $clientsecret;

    /** @var string The client redirect URI. */
    protected $redirecturi;

    /** @var array Array of endpoints. */
    protected $endpoints = [];

    /** @var string The resource of the token. */
    protected $tokenresource;

    /** @var string The scope of the token. */
    protected $scope;

    /**
     * Constructor.
     *
     * @param httpclientinterface $httpclient An HTTP client to use for background communication.
     */
    public function __construct(httpclientinterface $httpclient) {
        $this->httpclient = $httpclient;
    }

    /**
     * Set client details/credentials.
     *
     * @param string $id The registered client ID.
     * @param string $secret The registered client secret.
     * @param string $redirecturi The registered client redirect URI.
     * @param string $tokenresource The API URL
     * @param string $scope The requested OID scope.
     */
    public function setcreds($id, $secret, $redirecturi, $tokenresource = '', $scope = '') {
        $this->clientid = $id;
        $this->clientsecret = $secret;
        $this->redirecturi = $redirecturi;
        if (!empty($tokenresource)) {
            $this->tokenresource = $tokenresource;
        } else {
            if (auth_oidc_is_local_365_installed()) {
                if (\local_o365\rest\o365api::use_chinese_api() === true) {
                    $this->tokenresource = 'https://microsoftgraph.chinacloudapi.cn';
                } else {
                    $this->tokenresource = 'https://graph.microsoft.com';
                }
            } else {
                $this->tokenresource = 'https://graph.microsoft.com';
            }

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
     * Get the set token resource.
     *
     * @return string The set token resource.
     */
    public function get_tokenresource() {
        return (isset($this->tokenresource)) ? $this->tokenresource : null;
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
                throw new moodle_exception('erroroidcclientinvalidendpoint', 'auth_oidc');
            }
            $this->endpoints[$type] = $uri;
        }
    }

    /**
     * Validate the return the endpoint.
     * @param $endpoint
     * @return mixed|null
     */
    public function get_endpoint($endpoint) {
        return (isset($this->endpoints[$endpoint])) ? $this->endpoints[$endpoint] : null;
    }

    /**
     * Get an array of authorization request parameters.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the OIDC request.
     * @param bool $selectaccount Whether to prompt the user to select an account.
     * @return array Array of request parameters.
     */
    protected function getauthrequestparams($promptlogin = false, array $stateparams = array(), array $extraparams = array(),
        bool $selectaccount = false) {
        global $SESSION;

        $nonce = 'N'.uniqid();

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientid,
            'scope' => $this->scope,
            'nonce' => $nonce,
            'response_mode' => 'form_post',
            'state' => $this->getnewstate($nonce, $stateparams),
            'redirect_uri' => $this->redirecturi
        ];

        if (get_config('auth_oidc', 'idptype') != AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
            $params['resource'] = $this->tokenresource;
        }

        if ($promptlogin === true) {
            $params['prompt'] = 'login';
        } else if ($selectaccount === true) {
            $params['prompt'] = 'select_account';
        } else {
            $silentloginmode = get_config('auth_oidc', 'silentloginmode');
            $source = optional_param('source', '', PARAM_RAW);
            if ($silentloginmode && $source != 'loginpage') {
                $params['prompt'] = 'none';
            }
        }

        $domainhint = get_config('auth_oidc', 'domainhint');
        if (!empty($domainhint)) {
            $params['domain_hint'] = $domainhint;
        }

        $params = array_merge($params, $extraparams);

        return $params;
    }

    /**
     * Return params for an admin consent request.
     *
     * @param array $stateparams
     * @param array $extraparams
     * @return array
     */
    protected function getadminconsentrequestparams(array $stateparams = [], array $extraparams = []) {
        $nonce = 'N'.uniqid();

        $params = [
            'client_id' => $this->clientid,
            'scope' => 'https://graph.microsoft.com/.default',
            'state' => $this->getnewstate($nonce, $stateparams),
            'redirect_uri' => $this->redirecturi,
        ];

        $params = array_merge($params, $extraparams);

        return $params;
    }

    /**
     * Generate a new state parameter.
     *
     * @param string $nonce The generated nonce value.
     * @param array $stateparams
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
        $DB->insert_record('auth_oidc_state', $staterec);
        return $staterec->state;
    }

    /**
     * Perform an authorization request by redirecting resource owner's user agent to auth endpoint.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the OIDC request.
     * @param bool $selectaccount Whether to prompt the user to select an account.
     */
    public function authrequest($promptlogin = false, array $stateparams = array(), array $extraparams = array(),
        bool $selectaccount = false) {
        if (empty($this->clientid)) {
            throw new moodle_exception('erroroidcclientnocreds', 'auth_oidc');
        }

        if (empty($this->endpoints['auth'])) {
            throw new moodle_exception('erroroidcclientnoauthendpoint', 'auth_oidc');
        }

        $params = $this->getauthrequestparams($promptlogin, $stateparams, $extraparams, $selectaccount);
        $redirecturl = new moodle_url($this->endpoints['auth'], $params);
        redirect($redirecturl);
    }

    /**
     * Perform an admin consent request when using a Microsoft Identity Platform type IdP.
     *
     * @param array $stateparams
     * @param array $extraparams
     * @return void
     */
    public function adminconsentrequest(array $stateparams = [], array $extraparams = []) {
        $adminconsentendpoint = 'https://login.microsoftonline.com/organizations/v2.0/adminconsent';
        $params = $this->getadminconsentrequestparams($stateparams, $extraparams);
        $redirecturl = new moodle_url($adminconsentendpoint, $params);
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
            throw new moodle_exception('erroroidcclientnotokenendpoint', 'auth_oidc');
        }

        if (strpos($this->endpoints['token'], 'https://') !== 0) {
            throw new moodle_exception('erroroidcclientinsecuretokenendpoint', 'auth_oidc');
        }

        $params = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => 'openid profile email',
            'client_id' => $this->clientid,
            'client_secret' => $this->clientsecret,
        ];

        if (get_config('auth_oidc', 'idptype') != AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
            $params['resource'] = $this->tokenresource;
        }

        try {
            $returned = $this->httpclient->post($this->endpoints['token'], $params);
            return utils::process_json_response($returned, ['token_type' => null, 'id_token' => null]);
        } catch (moodle_exception $e) {
            utils::debug('Error in rocredsrequest request', __METHOD__, $e->getMessage());
            return false;
        }
    }

    /**
     * Exchange an authorization code for an access token.
     *
     * @param string $code An authorization code.
     * @return array Received parameters.
     */
    public function tokenrequest($code) {
        if (empty($this->endpoints['token'])) {
            throw new moodle_exception('erroroidcclientnotokenendpoint', 'auth_oidc');
        }

        $params = [
            'client_id' => $this->clientid,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirecturi,
        ];

        switch (get_config('auth_oidc', 'clientauthmethod')) {
            case AUTH_OIDC_AUTH_METHOD_CERTIFICATE:
                $params['client_assertion_type'] = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
                $params['client_assertion'] = static::generate_client_assertion();
                $params['tenant'] = 'common';
                break;
            default:
                $params['client_secret'] = $this->clientsecret;
        }
        $returned = $this->httpclient->post($this->endpoints['token'], $params);
        return utils::process_json_response($returned, ['id_token' => null]);
    }

    /**
     * Request an access token in Microsoft Identity Platform.
     *
     * @return array
     */
    public function app_access_token_request() {
        $params = [
            'client_id' => $this->clientid,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ];

        switch (get_config('auth_oidc', 'clientauthmethod')) {
            case AUTH_OIDC_AUTH_METHOD_CERTIFICATE:
                $params['client_assertion_type'] = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
                $params['client_assertion'] = static::generate_client_assertion();
                break;
            default:
                $params['client_secret'] = $this->clientsecret;
        }

        $tokenendpoint = $this->endpoints['token'];

        $returned = $this->httpclient->post($tokenendpoint, $params);
        return utils::process_json_response($returned, ['access_token' => null]);
    }

    /**
     * Calculate the return the assertion used in the token request in certificate connection method.
     *
     * @return string
     * @throws moodle_exception
     */
    public static function generate_client_assertion() : string {
        $authoidcconfig = get_config('auth_oidc');
        $certsource = $authoidcconfig->clientcertsource;

        $clientcertpassphrase = null;
        if (property_exists($authoidcconfig, 'clientcertpassphrase')) {
            $clientcertpassphrase = $authoidcconfig->clientcertpassphrase;
        }

        if ($certsource == AUTH_OIDC_AUTH_CERT_SOURCE_TEXT) {
            $cert = openssl_x509_read($authoidcconfig->clientcert);
            $privatekey = openssl_pkey_get_private($authoidcconfig->clientprivatekey, $clientcertpassphrase);
        } else if ($certsource == AUTH_OIDC_AUTH_CERT_SOURCE_FILE) {
            $cert = openssl_x509_read(utils::get_certpath());
            $privatekey = openssl_pkey_get_private(utils::get_keypath(), $clientcertpassphrase);
        } else {
            throw new moodle_exception('errorinvalidcertificatesource', 'auth_oidc');
        }
        
        $sh1hash = openssl_x509_fingerprint($cert);
        $x5t = base64_encode(hex2bin($sh1hash));

        $jwt = new jwt();
        $jwt->set_header(['alg' => 'RS256', 'typ' => 'JWT', 'x5t' => $x5t]);
        $jwt->set_claims([
            'aud' => $authoidcconfig->tokenendpoint,
            'exp' => strtotime('+10min'),
            'iss' => $authoidcconfig->clientid,
            'jti' => bin2hex(openssl_random_pseudo_bytes(16)),
            'nbf' => time(),
            'sub' => $authoidcconfig->clientid,
            'iat' => time(),
        ]);

        return $jwt->assert_token($privatekey);
    }
}
