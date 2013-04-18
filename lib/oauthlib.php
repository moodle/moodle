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
        if (!empty($this->oauth_callback)) {
            $oauth_params['oauth_callback'] = $this->oauth_callback->out(false);
        }
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
     * Request token for authentication
     * This is the first step to use OAuth, it will return oauth_token and oauth_token_secret
     * @return array
     */
    public function request_token() {
        $this->sign_secret = $this->consumer_secret.'&';
        $params = $this->prepare_oauth_parameters($this->request_token_api, array(), 'GET');
        $content = $this->http->get($this->request_token_api, $params);
        // Including:
        //     oauth_token
        //     oauth_token_secret
        $result = $this->parse_result($content);
        if (empty($result['oauth_token'])) {
            // failed
            var_dump($result);
            exit;
        }
        // build oauth authrize url
        if (!empty($this->oauth_callback)) {
            // url must be rawurlencode
            $result['authorize_url'] = $this->authorize_url . '?oauth_token='.$result['oauth_token'].'&oauth_callback='.rawurlencode($this->oauth_callback->out(false));
        } else {
            // no callback
            $result['authorize_url'] = $this->authorize_url . '?oauth_token='.$result['oauth_token'];
        }
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
        $content = $this->http->post($this->access_token_api, $params);
        $keys = $this->parse_result($content);
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
        $oauth_params = $this->prepare_oauth_parameters($url, array('oauth_token'=>$token), $method);
        $this->setup_oauth_http_header($oauth_params);
        $content = call_user_func_array(array($this->http, strtolower($method)), array($url, $params));
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
