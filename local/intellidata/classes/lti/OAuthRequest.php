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
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 * @codingStandardsIgnoreFile
 */

namespace local_intellidata\lti;

class OAuthRequest {

    private $parameters;
    private $httpmethod;
    private $httpurl;
    // For debug purposes.
    public $basestring;
    public static $version = '1.0';
    public static $postinput = 'php://input';
    public $base_string;

    public function __construct($httpmethod, $httpurl, $parameters = null) {
        @$parameters || $parameters = [];
        $this->parameters = $parameters;
        $this->httpmethod = $httpmethod;
        $this->httpurl = $httpurl;
    }

    /**
     * attempt to build up a request from what was passed to the server
     */
    public static function from_request($httpmethod = null, $httpurl = null, $parameters = null) {
        $scheme = (!is_https()) ? 'http' : 'https';
        $port = "";
        if ($_SERVER['SERVER_PORT'] != "80" && $_SERVER['SERVER_PORT'] != "443" && strpos(':', $_SERVER['HTTP_HOST']) < 0) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        @$httpurl || $httpurl = $scheme .
            '://' . $_SERVER['HTTP_HOST'] .
            $port .
            $_SERVER['REQUEST_URI'];
        @$httpmethod || $httpmethod = $_SERVER['REQUEST_METHOD'];

        // We weren't handed any parameters, so let's find the ones relevant to
        // this request.
        // If you run XML-RPC or similar you should use this to provide your own
        // parsed parameter-list.
        if (!$parameters) {
            // Find request headers.
            $requestheaders = OAuthUtil::get_headers();

            // Parse the query-string to find GET parameters.
            $parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

            $ourpost = $_POST;
            // Add POST Parameters if they exist.
            $parameters = array_merge($parameters, $ourpost);

            // We have a Authorization-header with OAuth data. Parse the header
            // and add those overriding any duplicates from GET or POST.
            if (@substr($requestheaders['Authorization'], 0, 6) == "OAuth ") {
                $headerparameters = OAuthUtil::split_header($requestheaders['Authorization']);
                $parameters = array_merge($parameters, $headerparameters);
            }

        }

        return new OAuthRequest($httpmethod, $httpurl, $parameters);
    }

    /**
     * pretty much a helper function to set up the request
     */
    public static function from_consumer_and_token($consumer, $token, $httpmethod, $httpurl, $parameters = null) {
        @$parameters || $parameters = [];
        $defaults = [
            "oauth_version" => self::$version,
            "oauth_nonce" => self::generate_nonce(),
            "oauth_timestamp" => self::generate_timestamp(),
            "oauth_consumer_key" => $consumer->key,
        ];
        if ($token) {
            $defaults['oauth_token'] = $token->key;
        }

        $parameters = array_merge($defaults, $parameters);

        // Parse the query-string to find and add GET parameters.
        $parts = parse_url($httpurl);
        if (isset($parts['query'])) {
            $qparms = OAuthUtil::parse_parameters($parts['query']);
            $parameters = array_merge($qparms, $parameters);
        }

        return new OAuthRequest($httpmethod, $httpurl, $parameters);
    }

    /**
     * @param $name
     * @param $value
     * @param bool $allowduplicates
     */
    public function set_parameter($name, $value, $allowduplicates = true) {
        if ($allowduplicates && isset($this->parameters[$name])) {
            // We have already added parameter(s) with this name, so add to the list.
            if (is_scalar($this->parameters[$name])) {
                // This is the first duplicate, so transform scalar (string)
                // into an array so we can add the duplicates.
                $this->parameters[$name] = [$this->parameters[$name]];
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function get_parameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @return array|mixed|null
     */
    public function get_parameters() {
        return $this->parameters;
    }

    /**
     * @param $name
     */
    public function unset_parameter($name) {
        unset($this->parameters[$name]);
    }

    /**
     * The request parameters, sorted and concatenated into a normalized string.
     * @return string
     */
    public function get_signable_parameters() {
        // Grab all parameters.
        $params = $this->parameters;

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 The oauth_signature parameter MUST be excluded.
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }

        return OAuthUtil::build_http_query($params);
    }

    /**
     * Returns the base string of this request
     *
     * The base string defined as the method, the url
     * and the parameters (normalized), each urlencoded
     * and the concated with &.
     */
    public function get_signature_base_string() {
        $parts = [
            $this->get_normalized_http_method(),
            $this->get_normalized_http_url(),
            $this->get_signable_parameters(),
        ];

        $parts = OAuthUtil::urlencode_rfc3986($parts);

        return implode('&', $parts);
    }

    /**
     * just uppercases the http method
     */
    public function get_normalized_http_method() {
        return strtoupper($this->httpmethod);
    }

    /**
     * parses the url and rebuilds it to be
     * scheme://host/path
     */
    public function get_normalized_http_url() {
        $parts = parse_url($this->httpurl);

        $port = @$parts['port'];
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = @$parts['path'];

        $port || $port = ($scheme == 'https') ? '443' : '80';

        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }

    /**
     * builds a url usable for a GET request
     */
    public function to_url() {
        $postdata = $this->to_postdata();
        $out = $this->get_normalized_http_url();
        if ($postdata) {
            $out .= '?'.$postdata;
        }
        return $out;
    }

    /**
     * builds the data one would send in a POST request
     */
    public function to_postdata() {
        return OAuthUtil::build_http_query($this->parameters);
    }

    /**
     * builds the Authorization: header
     */
    public function to_header() {
        $out = 'Authorization: OAuth realm=""';
        foreach ($this->parameters as $k => $v) {
            if (substr($k, 0, 5) != "oauth") {
                continue;
            }
            if (is_array($v)) {
                throw new OAuthException('Arrays not supported in headers');
            }
            $out .= ',' .
                OAuthUtil::urlencode_rfc3986($k) .
                '="' .
                OAuthUtil::urlencode_rfc3986($v) .
                '"';
        }
        return $out;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->to_url();
    }

    /**
     * @param $signaturemethod
     * @param $consumer
     * @param $token
     */
    public function sign_request($signaturemethod, $consumer, $token) {
        $this->set_parameter("oauth_signature_method", $signaturemethod->get_name(), false);
        $signature = $this->build_signature($signaturemethod, $consumer, $token);
        $this->set_parameter("oauth_signature", $signature, false);
    }

    /**
     * @param $signaturemethod
     * @param $consumer
     * @param $token
     * @return mixed
     */
    public function build_signature($signaturemethod, $consumer, $token) {
        $signature = $signaturemethod->build_signature($this, $consumer, $token);
        return $signature;
    }

    /**
     * util function: current timestamp
     */
    private static function generate_timestamp() {
        return time();
    }

    /**
     * util function: current nonce
     */
    private static function generate_nonce() {
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt.$rand); // MD5 look nicer than numbers.
    }
}
