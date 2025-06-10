<?php

/**
 * @ignore
 * OAuthSimple - A simpler version of OAuth
 *
 * https://github.com/jrconlin/oauthsimple
 *
 * @author jr conlin <src@jrconlin.com>
 * @copyright unitedHeroes.net 2011
 * @version 1.3
 * @license See license.txt
 *
 */
class OAuthSimple {

    protected $_secrets;
    protected $_default_signature_method;
    protected $_action;
    protected $_nonce_chars;

    /**
     * Constructor
     *
     * @access public
     * @param api_key (String) The API Key (sometimes referred to as the consumer key) This value is usually supplied by the site you wish to use.
     * @param shared_secret (String) The shared secret. This value is also usually provided by the site you wish to use.
     * @return OAuthSimple (Object)
     */
    function __construct($APIKey = "", $sharedSecret = "") {

        if (!empty($APIKey)) {
            $this->_secrets['consumer_key'] = $APIKey;
        }

        if (!empty($sharedSecret)) {
            $this->_secrets['shared_secret'] = $sharedSecret;
        }

        $this->_default_signature_method = "HMAC-SHA1";
        $this->_action = "GET";
        $this->_nonce_chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        return $this;
    }

    /**
     * Reset the parameters and URL
     *
     * @access public
     * @return OAuthSimple (Object)
     */
    public function reset() {
        $this->_parameters = Array();
        $this->path = NULL;
        $this->sbs = NULL;

        return $this;
    }

    // Generate the body_hash
    public function genBodyHash($request) {
        $this->oauth_body_hash = base64_encode(sha1($request, true));
    }

    /**
     * Set the parameters either from a hash or a string
     *
     * @access public
     * @param parameters (string, object) List of parameters for the call, this can either be a URI string (e.g. "foo=bar&gorp=banana" or an object/hash)
     * @return OAuthSimple (Object)
     */
    public function setParameters($parameters = Array()) {

        if (is_string($parameters)) {
            $parameters = $this->_parseParameterString($parameters);
        }
        if (empty($this->_parameters)) {
            $this->_parameters = $parameters;
        } else if (!empty($parameters)) {
            $this->_parameters = array_merge($this->_parameters, $parameters);
        }
        if (empty($this->_parameters['oauth_nonce'])) {
            $this->_getNonce();
        }
        if (empty($this->_parameters['oauth_timestamp'])) {
            $this->_getTimeStamp();
        }
        if (empty($this->_parameters['oauth_consumer_key'])) {
            $this->_getApiKey();
        }
        if (empty($this->_parameters['oauth_token'])) {
            $this->_getAccessToken();
        }
        if (empty($this->_parameters['oauth_signature_method'])) {
            $this->setSignatureMethod();
        }
        if (empty($this->_parameters['oauth_version'])) {
            $this->_parameters['oauth_version'] = "1.0";
        }
        if (isset($this->oauth_body_hash)) {
            $this->_parameters['oauth_body_hash'] = $this->oauth_body_hash;
        }
        return $this;
    }

    /**
     * Convenience method for setParameters
     *
     * @access public
     * @see setParameters
     */
    public function setQueryString($parameters) {
        return $this->setParameters($parameters);
    }

    /**
     * Set the target URL (does not include the parameters)
     *
     * @param path (String) the fully qualified URI (excluding query arguments) (e.g "http://example.org/foo")
     * @return OAuthSimple (Object)
     */
    public function setURL($path) {
        if (empty($path)) {
            throw new OAuthSimpleException('No path specified for OAuthSimple.setURL');
        }
        $this->_path = $path;

        return $this;
    }

    /**
     * Convenience method for setURL
     *
     * @param path (String)
     * @see setURL
     */
    public function setPath($path) {
        return $this->_path = $path;
    }

    /**
     * Set the "action" for the url, (e.g. GET,POST, DELETE, etc.)
     *
     * @param action (String) HTTP Action word.
     * @return OAuthSimple (Object)
     */
    public function setAction($action) {
        if (empty($action)) {
            $action = 'GET';
        }
        $action = strtoupper($action);
        if (preg_match('/[^A-Z]/', $action)) {
            throw new OAuthSimpleException('Invalid action specified for OAuthSimple.setAction');
        }
        $this->_action = $action;

        return $this;
    }

    /**
     * Set the signatures (as well as validate the ones you have)
     *
     * @param signatures (object) object/hash of the token/signature pairs {api_key:, shared_secret:, oauth_token: oauth_secret:}
     * @return OAuthSimple (Object)
     */
    public function signatures($signatures) {
        if (!empty($signatures) && !is_array($signatures)) {
            throw new OAuthSimpleException('Must pass dictionary array to OAuthSimple.signatures');
        }
        if (!empty($signatures)) {
            if (empty($this->_secrets)) {
                $this->_secrets = Array();
            }
            $this->_secrets = array_merge($this->_secrets, $signatures);
        }
        if (isset($this->_secrets['api_key'])) {
            $this->_secrets['consumer_key'] = $this->_secrets['api_key'];
        }
        if (isset($this->_secrets['access_token'])) {
            $this->_secrets['oauth_token'] = $this->_secrets['access_token'];
        }
        if (isset($this->_secrets['access_secret'])) {
            $this->_secrets['oauth_secret'] = $this->_secrets['access_secret'];
        }
        if (isset($this->_secrets['access_token_secret'])) {
            $this->_secrets['oauth_secret'] = $this->_secrets['access_token_secret'];
        }
        if (empty($this->_secrets['consumer_key'])) {
            throw new OAuthSimpleException('Missing required consumer_key in OAuthSimple.signatures');
        }
        if (empty($this->_secrets['shared_secret'])) {
            throw new OAuthSimpleException('Missing requires shared_secret in OAuthSimple.signatures');
        }
        if (!empty($this->_secrets['oauth_token']) && empty($this->_secrets['oauth_secret'])) {
            throw new OAuthSimpleException('Missing oauth_secret for supplied oauth_token in OAuthSimple.signatures');
        }

        return $this;
    }

    public function setTokensAndSecrets($signatures) {
        return $this->signatures($signatures);
    }

    /**
     * Set the signature method (currently only Plaintext or SHA-MAC1)
     *
     * @param method (String) Method of signing the transaction (only PLAINTEXT and SHA-MAC1 allowed for now)
     * @return OAuthSimple (Object)
     */
    public function setSignatureMethod($method = "") {
        if (empty($method)) {
            $method = $this->_default_signature_method;
        }
        $method = strtoupper($method);
        switch ($method) {
            case 'PLAINTEXT':
            case 'HMAC-SHA1':
                $this->_parameters['oauth_signature_method'] = $method;
                break;
            default:
                throw new OAuthSimpleException("Unknown signing method $method specified for OAuthSimple.setSignatureMethod");
                break;
        }

        return $this;
    }

    /** sign the request
     *
     * note: all arguments are optional, provided you've set them using the
     * other helper functions.
     *
     * @param args (Array) hash of arguments for the call {action, path, parameters (array), method, signatures (array)} all arguments are optional.
     * @return Signed Values (Array) - array map of signed values
     */
    public function sign($args = array()) {
        if (!empty($args['action'])) {
            $this->setAction($args['action']);
        }
        if (!empty($args['path'])) {
            $this->setPath($args['path']);
        }
        if (!empty($args['method'])) {
            $this->setSignatureMethod($args['method']);
        }
        if (!empty($args['signatures'])) {
            $this->signatures($args['signatures']);
        }
        if (empty($args['parameters'])) {
            $args['parameters'] = array();
        }
        $this->setParameters($args['parameters']);
        $normParams = $this->_normalizedParameters();
        $this->_parameters['oauth_signature'] = $this->_generateSignature($normParams);

        return Array(
            'parameters' => $this->_parameters,
            'signature' => self::_oauthEscape($this->_parameters['oauth_signature']),
            'signed_url' => $this->_path . '?' . $this->_normalizedParameters(),
            'header' => $this->getHeaderString(),
            'sbs' => $this->sbs
        );
    }

    /**
     * Return a formatted "header" string
     *
     * NOTE: This doesn't set the "Authorization: " prefix, which is required.
     * It's not set because various set header functions prefer different
     * ways to do that.
     *
     * @param args (Array)
     * @return result (String)
     */
    public function getHeaderString($args = array()) {
        if (empty($this->_parameters['oauth_signature'])) {
            $this->sign($args);
        }
        $result = 'OAuth ';

        foreach ($this->_parameters as $pName => $pValue) {
            if (strpos($pName, 'oauth_') !== 0) {
                continue;
            }
            if (is_array($pValue)) {
                foreach ($pValue as $val) {
                    $result .= $pName . '="' . self::_oauthEscape($val) . '", ';
                }
            } else {
                $result .= $pName . '="' . self::_oauthEscape($pValue) . '", ';
            }
        }

        return preg_replace('/, $/', '', $result);
    }

    public function getParamArray( $args = array() ) {
        if (empty($this->_parameters['oauth_signature'])) {
            $this->sign($args);
        }
        $result = array();

        foreach ($this->_parameters as $pName => $pValue) {
            if (strpos($pName, 'oauth_') !== 0) {
                continue;
            }
            if (is_array($pValue)) {
                foreach ($pValue as $val) {
                    $result[$pName] = self::_oauthEscape($val);
                }
            } else {
                $result[$pName] = self::_oauthEscape($pValue);
            }
        }

        return $result;
    }

    private function _parseParameterString($paramString) {
        $elements = explode('&', $paramString);
        $result = array();
        foreach ($elements as $element) {
            list ($key, $token) = explode('=', $element);
            if ($token) {
                $token = urldecode($token);
            }
            if (!empty($result[$key])) {
                if (!is_array($result[$key])) {
                    $result[$key] = array($result[$key], $token);
                } else {
                    array_push($result[$key], $token);
                }
            }
            else
                $result[$key] = $token;
        }
        return $result;
    }

    private static function _oauthEscape($string) {
        if ($string === 0) {
            return 0;
        }
        if ($string == '0') {
            return '0';
        }
        if (strlen($string) == 0) {
            return '';
        }
        if (is_array($string)) {
            throw new OAuthSimpleException('Array passed to _oauthEscape');
        }
        $string = rawurlencode($string);

        $string = str_replace('+', '%20', $string);
        $string = str_replace('!', '%21', $string);
        $string = str_replace('*', '%2A', $string);
        $string = str_replace('\'', '%27', $string);
        $string = str_replace('(', '%28', $string);
        $string = str_replace(')', '%29', $string);

        return $string;
    }

    private function _getNonce($length = 5) {
        $result = '';
        $cLength = strlen($this->_nonce_chars);
        for ($i = 0; $i < $length; $i++) {
            $rnum = mt_rand(0, $cLength);
            $result .= substr($this->_nonce_chars, $rnum, 1);
        }
        $result .= getmypid();
        $this->_parameters['oauth_nonce'] = sha1($result);

        return $result;
    }

    private function _getApiKey() {
        if (empty($this->_secrets['consumer_key'])) {
            throw new OAuthSimpleException('No consumer_key set for OAuthSimple');
        }
        $this->_parameters['oauth_consumer_key'] = $this->_secrets['consumer_key'];

        return $this->_parameters['oauth_consumer_key'];
    }

    private function _getAccessToken() {
        if (!isset($this->_secrets['oauth_secret'])) {
            return '';
        }
        if (!isset($this->_secrets['oauth_token'])) {
            throw new OAuthSimpleException('No access token (oauth_token) set for OAuthSimple.');
        }
        $this->_parameters['oauth_token'] = $this->_secrets['oauth_token'];

        return $this->_parameters['oauth_token'];
    }

    private function _getTimeStamp() {
        return $this->_parameters['oauth_timestamp'] = time();
    }

    private function _normalizedParameters() {
        $normalized_keys = array();
        $return_array = array();

        foreach ($this->_parameters as $paramName => $paramValue) {
            if (!preg_match('/\w+_secret/', $paramName) OR (strpos($paramValue, '@') !== 0 && !file_exists(substr($paramValue, 1)))) {
                if (is_array($paramValue)) {
                    $normalized_keys[self::_oauthEscape($paramName)] = array();
                    foreach ($paramValue as $item) {
                        array_push($normalized_keys[self::_oauthEscape($paramName)], self::_oauthEscape($item));
                    }
                } else {
                    $normalized_keys[self::_oauthEscape($paramName)] = self::_oauthEscape($paramValue);
                }
            }
        }

        ksort($normalized_keys);

        foreach ($normalized_keys as $key => $val) {
            if (is_array($val)) {
                sort($val);
                foreach ($val as $element) {
                    array_push($return_array, $key . "=" . $element);
                }
            } else {
                array_push($return_array, $key . '=' . $val);
            }
        }

        return join("&", $return_array);
    }

    private function _generateSignature() {
        $secretKey = '';
        if (isset($this->_secrets['shared_secret'])) {
            $secretKey = self::_oauthEscape($this->_secrets['shared_secret']);
        }

        $secretKey .= '&';
        if (isset($this->_secrets['oauth_secret'])) {
            $secretKey .= self::_oauthEscape($this->_secrets['oauth_secret']);
        }
        switch ($this->_parameters['oauth_signature_method']) {
            case 'PLAINTEXT':
                return urlencode($secretKey);
                ;
            case 'HMAC-SHA1':
                $this->sbs = self::_oauthEscape($this->_action) . '&' . self::_oauthEscape($this->_path) . '&' . self::_oauthEscape($this->_normalizedParameters());

                return base64_encode(hash_hmac('sha1', $this->sbs, $secretKey, TRUE));
            default:
                throw new OAuthSimpleException('Unknown signature method for OAuthSimple');
                break;
        }
    }

}

/**
 * @ignore
 */
class OAuthSimpleException extends Exception {

    public function __construct($err, $isDebug = FALSE) {
        self::log_error($err);
        if ($isDebug) {
            self::display_error($err, TRUE);
        }
    }

    public static function log_error($err) {
        error_log($err, 0);
    }

    public static function display_error($err, $kill = FALSE) {
        print_r($err);
        if ($kill === FALSE) {
            die();
        }
    }

}

//?>
