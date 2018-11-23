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


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

/**
 * OAuth helper class
 *
 * 1. You can extends oauth_helper to add specific functions, such as twitter extends oauth_helper
 * 2. Call request_token method to get oauth_token and oauth_token_secret, and redirect user to authorize_url,
 *    developer needs to store oauth_token and oauth_token_secret somewhere, we will use them to request
 *    access token later on
 * 3. User approved the request, and get back to moodle
 * 4. Call get_access_token, it takes previous oauth_token and oauth_token_secret as arguments, oauth_token
 *    will be used in OAuth request, oauth_token_secret will be used to bulid signature, this method will
 *    return access_token and access_secret, store these two values in database or session
 * 5. Now you can access oauth protected resources by access_token and access_secret using oauth_helper::request
 *    method (or get() post())
 *
 * Note:
 * 1. This class only support HMAC-SHA1
 * 2. oauth_helper class don't store tokens and secrets, you must store them manually
 * 3. Some functions are based on http://code.google.com/p/oauth/
 *
 * @package    moodlecore
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class oauth_helper {
    /** @var string consumer key, issued by oauth provider*/
    protected $consumer_key;
    /** @var string consumer secret, issued by oauth provider*/
    protected $consumer_secret;
    /** @var string oauth root*/
    protected $api_root;
    /** @var string request token url*/
    protected $request_token_api;
    /** @var string authorize url*/
    protected $authorize_url;
    protected $http_method;
    /** @var string */
    protected $access_token_api;
    /** @var curl */
    protected $http;
    /** @var array options to pass to the next curl request */
    protected $http_options;

    /**
     * Contructor for oauth_helper.
     * Subclass can override construct to build its own $this->http
     *
     * @param array $args requires at least three keys, oauth_consumer_key
     *                    oauth_consumer_secret and api_root, oauth_helper will
     *                    guess request_token_api, authrize_url and access_token_api
     *                    based on api_root, but it not always works
     */
    function __construct($args) {
        if (!empty($args['api_root'])) {
            $this->api_root = $args['api_root'];
        } else {
            $this->api_root = '';
        }
        $this->consumer_key = $args['oauth_consumer_key'];
        $this->consumer_secret = $args['oauth_consumer_secret'];

        if (empty($args['request_token_api'])) {
            $this->request_token_api = $this->api_root . '/request_token';
        } else {
            $this->request_token_api = $args['request_token_api'];
        }

        if (empty($args['authorize_url'])) {
            $this->authorize_url = $this->api_root . '/authorize';
        } else {
            $this->authorize_url = $args['authorize_url'];
        }

        if (empty($args['access_token_api'])) {
            $this->access_token_api = $this->api_root . '/access_token';
        } else {
            $this->access_token_api = $args['access_token_api'];
        }

        if (!empty($args['oauth_callback'])) {
            $this->oauth_callback = new moodle_url($args['oauth_callback']);
        }
        if (!empty($args['access_token'])) {
            $this->access_token = $args['access_token'];
        }
        if (!empty($args['access_token_secret'])) {
            $this->access_token_secret = $args['access_token_secret'];
        }
        $this->http = new curl(array('debug'=>false));
        $this->http_options = array();
    }

    /**
     * Build parameters list:
     *    oauth_consumer_key="0685bd9184jfhq22",
     *    oauth_nonce="4572616e48616d6d65724c61686176",
     *    oauth_token="ad180jjd733klru7",
     *    oauth_signature_method="HMAC-SHA1",
     *    oauth_signature="wOJIO9A2W5mFwDgiDvZbTSMK%2FPY%3D",
     *    oauth_timestamp="137131200",
     *    oauth_version="1.0"
     *    oauth_verifier="1.0"
     * @param array $param
     * @return string
     */
    function get_signable_parameters($params){
        $sorted = $params;
        ksort($sorted);

        $total = array();
        foreach ($sorted as $k => $v) {
            if ($k == 'oauth_signature') {
                continue;
            }

            $total[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        return implode('&', $total);
    }

    /**
     * Create signature for oauth request
     * @param string $url
     * @param string $secret
     * @param array $params
     * @return string
     */
    public function sign($http_method, $url, $params, $secret) {
        $sig = array(
            strtoupper($http_method),
            preg_replace('/%7E/', '~', rawurlencode($url)),
            rawurlencode($this->get_signable_parameters($params)),
        );

        $base_string = implode('&', $sig);
        $sig = base64_encode(hash_hmac('sha1', $base_string, $secret, true));
        return $sig;
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
     * @param string $token
     * @param string $http_method
     * @return array
     */
    public function prepare_oauth_parameters($url, $params, $http_method = 'POST') {
        if (is_array($params)) {
            $oauth_params = $params;
        } else {
            $oauth_params = array();
        }
        $oauth_params['oauth_version']	    = '1.0';
        $oauth_params['oauth_nonce']	    = $this->get_nonce();
        $oauth_params['oauth_timestamp']    = $this->get_timestamp();
        $oauth_params['oauth_consumer_key'] = $this->consumer_key;
        $oauth_params['oauth_signature_method']	= 'HMAC-SHA1';
        $oauth_params['oauth_signature']	= $this->sign($http_method, $url, $oauth_params, $this->sign_secret);
        return $oauth_params;
    }

    public function setup_oauth_http_header($params) {

        $total = array();
        ksort($params);
        foreach ($params as $k => $v) {
            $total[] = rawurlencode($k) . '="' . rawurlencode($v).'"';
        }
        $str = implode(', ', $total);
        $str = 'Authorization: OAuth '.$str;
        $this->http->setHeader('Expect:');
        $this->http->setHeader($str);
    }

    /**
     * Sets the options for the next curl request
     *
     * @param array $options
     */
    public function setup_oauth_http_options($options) {
        $this->http_options = $options;
    }

    /**
     * Request token for authentication
     * This is the first step to use OAuth, it will return oauth_token and oauth_token_secret
     * @return array
     */
    public function request_token() {
        $this->sign_secret = $this->consumer_secret.'&';

        if (empty($this->oauth_callback)) {
            $params = [];
        } else {
            $params = ['oauth_callback' => $this->oauth_callback->out(false)];
        }

        $params = $this->prepare_oauth_parameters($this->request_token_api, $params, 'GET');
        $content = $this->http->get($this->request_token_api, $params, $this->http_options);
        // Including:
        //     oauth_token
        //     oauth_token_secret
        $result = $this->parse_result($content);
        if (empty($result['oauth_token'])) {
            throw new moodle_exception('oauth1requesttoken', 'core_error', '', null, $content);
        }
        // Build oauth authorize url.
        $result['authorize_url'] = $this->authorize_url . '?oauth_token='.$result['oauth_token'];

        return $result;
    }

    /**
     * Set oauth access token for oauth request
     * @param string $token
     * @param string $secret
     */
    public function set_access_token($token, $secret) {
        $this->access_token = $token;
        $this->access_token_secret = $secret;
    }

    /**
     * Request oauth access token from server
     * @param string $method
     * @param string $url
     * @param string $token
     * @param string $secret
     */
    public function get_access_token($token, $secret, $verifier='') {
        $this->sign_secret = $this->consumer_secret.'&'.$secret;
        $params = $this->prepare_oauth_parameters($this->access_token_api, array('oauth_token'=>$token, 'oauth_verifier'=>$verifier), 'POST');
        $this->setup_oauth_http_header($params);
        // Should never send the callback in this request.
        unset($params['oauth_callback']);
        $content = $this->http->post($this->access_token_api, $params, $this->http_options);
        $keys = $this->parse_result($content);

        if (empty($keys['oauth_token']) || empty($keys['oauth_token_secret'])) {
            throw new moodle_exception('oauth1accesstoken', 'core_error', '', null, $content);
        }

        $this->set_access_token($keys['oauth_token'], $keys['oauth_token_secret']);
        return $keys;
    }

    /**
     * Request oauth protected resources
     * @param string $method
     * @param string $url
     * @param string $token
     * @param string $secret
     */
    public function request($method, $url, $params=array(), $token='', $secret='') {
        if (empty($token)) {
            $token = $this->access_token;
        }
        if (empty($secret)) {
            $secret = $this->access_token_secret;
        }
        // to access protected resource, sign_secret will alwasy be consumer_secret+token_secret
        $this->sign_secret = $this->consumer_secret.'&'.$secret;
        if (strtolower($method) === 'post' && !empty($params)) {
            $oauth_params = $this->prepare_oauth_parameters($url, array('oauth_token'=>$token) + $params, $method);
        } else {
            $oauth_params = $this->prepare_oauth_parameters($url, array('oauth_token'=>$token), $method);
        }
        $this->setup_oauth_http_header($oauth_params);
        $content = call_user_func_array(array($this->http, strtolower($method)), array($url, $params, $this->http_options));
        // reset http header and options to prepare for the next request
        $this->http->resetHeader();
        // return request return value
        return $content;
    }

    /**
     * shortcut to start http get request
     */
    public function get($url, $params=array(), $token='', $secret='') {
        return $this->request('GET', $url, $params, $token, $secret);
    }

    /**
     * shortcut to start http post request
     */
    public function post($url, $params=array(), $token='', $secret='') {
        return $this->request('POST', $url, $params, $token, $secret);
    }

    /**
     * A method to parse oauth response to get oauth_token and oauth_token_secret
     * @param string $str
     * @return array
     */
    public function parse_result($str) {
        if (empty($str)) {
            throw new moodle_exception('error');
        }
        $parts = explode('&', $str);
        $result = array();
        foreach ($parts as $part){
            list($k, $v) = explode('=', $part, 2);
            $result[urldecode($k)] = urldecode($v);
        }
        if (empty($result)) {
            throw new moodle_exception('error');
        }
        return $result;
    }

    /**
     * Set nonce
     */
    function set_nonce($str) {
        $this->nonce = $str;
    }
    /**
     * Set timestamp
     */
    function set_timestamp($time) {
        $this->timestamp = $time;
    }
    /**
     * Generate timestamp
     */
    function get_timestamp() {
        if (!empty($this->timestamp)) {
            $timestamp = $this->timestamp;
            unset($this->timestamp);
            return $timestamp;
        }
        return time();
    }
    /**
     * Generate nonce for oauth request
     */
    function get_nonce() {
        if (!empty($this->nonce)) {
            $nonce = $this->nonce;
            unset($this->nonce);
            return $nonce;
        }
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt . $rand);
    }
}

/**
 * OAuth 2.0 Client for using web access tokens.
 *
 * http://tools.ietf.org/html/draft-ietf-oauth-v2-22
 *
 * @package   core
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oauth2_client extends curl {
    /** @var string $clientid client identifier issued to the client */
    private $clientid = '';
    /** @var string $clientsecret The client secret. */
    private $clientsecret = '';
    /** @var moodle_url $returnurl URL to return to after authenticating */
    private $returnurl = null;
    /** @var string $scope of the authentication request */
    protected $scope = '';
    /** @var stdClass $accesstoken access token object */
    protected $accesstoken = null;
    /** @var string $refreshtoken refresh token string */
    protected $refreshtoken = '';
    /** @var string $mocknextresponse string */
    private $mocknextresponse = '';
    /** @var array $upgradedcodes list of upgraded codes in this request */
    private static $upgradedcodes = [];
    /** @var bool basicauth */
    protected $basicauth = false;

    /**
     * Returns the auth url for OAuth 2.0 request
     * @return string the auth url
     */
    abstract protected function auth_url();

    /**
     * Returns the token url for OAuth 2.0 request
     * @return string the auth url
     */
    abstract protected function token_url();

    /**
     * Constructor.
     *
     * @param string $clientid
     * @param string $clientsecret
     * @param moodle_url $returnurl
     * @param string $scope
     */
    public function __construct($clientid, $clientsecret, moodle_url $returnurl, $scope) {
        parent::__construct();
        $this->clientid = $clientid;
        $this->clientsecret = $clientsecret;
        $this->returnurl = $returnurl;
        $this->scope = $scope;
        $this->accesstoken = $this->get_stored_token();
    }

    /**
     * Is the user logged in? Note that if this is called
     * after the first part of the authorisation flow the token
     * is upgraded to an accesstoken.
     *
     * @return boolean true if logged in
     */
    public function is_logged_in() {
        // Has the token expired?
        if (isset($this->accesstoken->expires) && time() >= $this->accesstoken->expires) {
            $this->log_out();
            return false;
        }

        // We have a token so we are logged in.
        if (isset($this->accesstoken->token)) {
            // Check that the access token has all the requested scopes.
            $scopemissing = false;
            $scopecheck = ' ' . $this->accesstoken->scope . ' ';

            $requiredscopes = explode(' ', $this->scope);
            foreach ($requiredscopes as $requiredscope) {
                if (strpos($scopecheck, ' ' . $requiredscope . ' ') === false) {
                    $scopemissing = true;
                    break;
                }
            }
            if (!$scopemissing) {
                return true;
            }
        }

        // If we've been passed then authorization code generated by the
        // authorization server try and upgrade the token to an access token.
        $code = optional_param('oauth2code', null, PARAM_RAW);
        // Note - sometimes we may call is_logged_in twice in the same request - we don't want to attempt
        // to upgrade the same token twice.
        if ($code && !in_array($code, self::$upgradedcodes) && $this->upgrade_token($code)) {
            return true;
        }

        return false;
    }

    /**
     * Callback url where the request is returned to.
     *
     * @return moodle_url url of callback
     */
    public static function callback_url() {
        global $CFG;

        return new moodle_url('/admin/oauth2callback.php');
    }

    /**
     * An additional array of url params to pass with a login request.
     *
     * @return array of name value pairs.
     */
    public function get_additional_login_parameters() {
        return [];
    }

    /**
     * Returns the login link for this oauth request
     *
     * @return moodle_url login url
     */
    public function get_login_url() {

        $callbackurl = self::callback_url();
        $params = array_merge(
            [
                'client_id' => $this->clientid,
                'response_type' => 'code',
                'redirect_uri' => $callbackurl->out(false),
                'state' => $this->returnurl->out_as_local_url(false),
                'scope' => $this->scope,
            ],
            $this->get_additional_login_parameters()
        );

        return new moodle_url($this->auth_url(), $params);
    }

    /**
     * Given an array of name value pairs - build a valid HTTP POST application/x-www-form-urlencoded string.
     *
     * @param array $params Name / value pairs.
     * @return string POST data.
     */
    public function build_post_data($params) {
        $result = [];
        foreach ($params as $name => $value) {
            $result[] = urlencode($name) . '=' . urlencode($value);
        }
        return implode('&', $result);
    }

    /**
     * Upgrade a authorization token from oauth 2.0 to an access token
     *
     * @param string $code the code returned from the oauth authenticaiton
     * @return boolean true if token is upgraded succesfully
     */
    public function upgrade_token($code) {
        $callbackurl = self::callback_url();
        $params = array('code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callbackurl->out(false),
        );

        if ($this->basicauth) {
            $idsecret = urlencode($this->clientid) . ':' . urlencode($this->clientsecret);
            $this->setHeader('Authorization: Basic ' . base64_encode($idsecret));
        } else {
            $params['client_id'] = $this->clientid;
            $params['client_secret'] = $this->clientsecret;
        }

        // Requests can either use http GET or POST.
        if ($this->use_http_get()) {
            $response = $this->get($this->token_url(), $params);
        } else {
            $response = $this->post($this->token_url(), $this->build_post_data($params));
        }

        if ($this->info['http_code'] !== 200) {
            throw new moodle_exception('Could not upgrade oauth token');
        }

        $r = json_decode($response);

        if (is_null($r)) {
            throw new moodle_exception("Could not decode JSON token response");
        }

        if (!empty($r->error)) {
            throw new moodle_exception($r->error . ' ' . $r->error_description);
        }

        if (!isset($r->access_token)) {
            return false;
        }

        if (isset($r->refresh_token)) {
            $this->refreshtoken = $r->refresh_token;
        }

        // Store the token an expiry time.
        $accesstoken = new stdClass;
        $accesstoken->token = $r->access_token;
        if (isset($r->expires_in)) {
            // Expires 10 seconds before actual expiry.
            $accesstoken->expires = (time() + ($r->expires_in - 10));
        }
        $accesstoken->scope = $this->scope;
        // Also add the scopes.
        self::$upgradedcodes[] = $code;
        $this->store_token($accesstoken);

        return true;
    }

    /**
     * Logs out of a oauth request, clearing any stored tokens
     */
    public function log_out() {
        $this->store_token(null);
    }

    /**
     * Make a HTTP request, adding the access token we have
     *
     * @param string $url The URL to request
     * @param array $options
     * @param mixed $acceptheader mimetype (as string) or false to skip sending an accept header.
     * @return bool
     */
    protected function request($url, $options = array(), $acceptheader = 'application/json') {
        $murl = new moodle_url($url);

        if ($this->accesstoken) {
            if ($this->use_http_get()) {
                // If using HTTP GET add as a parameter.
                $murl->param('access_token', $this->accesstoken->token);
            } else {
                $this->setHeader('Authorization: Bearer '.$this->accesstoken->token);
            }
        }

        if ($acceptheader) {
            $this->setHeader('Accept: ' . $acceptheader);
        }

        $response = parent::request($murl->out(false), $options);

        $this->resetHeader();

        return $response;
    }

    /**
     * Multiple HTTP Requests
     * This function could run multi-requests in parallel.
     *
     * @param array $requests An array of files to request
     * @param array $options An array of options to set
     * @return array An array of results
     */
    protected function multi($requests, $options = array()) {
        if ($this->accesstoken) {
            $this->setHeader('Authorization: Bearer '.$this->accesstoken->token);
        }
        return parent::multi($requests, $options);
    }

    /**
     * Returns the tokenname for the access_token to be stored
     * through multiple requests.
     *
     * The default implentation is to use the classname combiend
     * with the scope.
     *
     * @return string tokenname for prefernce storage
     */
    protected function get_tokenname() {
        // This is unusual but should work for most purposes.
        return get_class($this).'-'.md5($this->scope);
    }

    /**
     * Store a token between requests. Currently uses
     * session named by get_tokenname
     *
     * @param stdClass|null $token token object to store or null to clear
     */
    protected function store_token($token) {
        global $SESSION;

        $this->accesstoken = $token;
        $name = $this->get_tokenname();

        if ($token !== null) {
            $SESSION->{$name} = $token;
        } else {
            unset($SESSION->{$name});
        }
    }

    /**
     * Get a refresh token!!!
     *
     * @return string
     */
    public function get_refresh_token() {
        return $this->refreshtoken;
    }

    /**
     * Retrieve a token stored.
     *
     * @return stdClass|null token object
     */
    protected function get_stored_token() {
        global $SESSION;

        $name = $this->get_tokenname();

        if (isset($SESSION->{$name})) {
            return $SESSION->{$name};
        }

        return null;
    }

    /**
     * Get access token.
     *
     * This is just a getter to read the private property.
     *
     * @return string
     */
    public function get_accesstoken() {
        return $this->accesstoken;
    }

    /**
     * Get the client ID.
     *
     * This is just a getter to read the private property.
     *
     * @return string
     */
    public function get_clientid() {
        return $this->clientid;
    }

    /**
     * Get the client secret.
     *
     * This is just a getter to read the private property.
     *
     * @return string
     */
    public function get_clientsecret() {
        return $this->clientsecret;
    }

    /**
     * Should HTTP GET be used instead of POST?
     * Some APIs do not support POST and want oauth to use
     * GET instead (with the auth_token passed as a GET param).
     *
     * @return bool true if GET should be used
     */
    protected function use_http_get() {
        return false;
    }
}
