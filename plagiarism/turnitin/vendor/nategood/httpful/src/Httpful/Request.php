<?php

namespace Httpful;

use Httpful\Exception\ConnectionErrorException;

/**
 * Clean, simple class for sending HTTP requests
 * in PHP.
 *
 * There is an emphasis of readability without loosing concise
 * syntax.  As such, you will notice that the library lends
 * itself very nicely to "chaining".  You will see several "alias"
 * methods: more readable method definitions that wrap
 * their more concise counterparts.  You will also notice
 * no public constructor.  This two adds to the readability
 * and "chainabilty" of the library.
 *
 * @author Nate Good <me@nategood.com>
 * 
 * @method self sendsJson()
 * @method self sendsXml()
 * @method self sendsForm()
 * @method self sendsPlain()
 * @method self sendsText()
 * @method self sendsUpload()
 * @method self sendsHtml()
 * @method self sendsXhtml()
 * @method self sendsJs()
 * @method self sendsJavascript()
 * @method self sendsYaml()
 * @method self sendsCsv()
 * @method self expectsJson()
 * @method self expectsXml()
 * @method self expectsForm()
 * @method self expectsPlain()
 * @method self expectsText()
 * @method self expectsUpload()
 * @method self expectsHtml()
 * @method self expectsXhtml()
 * @method self expectsJs()
 * @method self expectsJavascript()
 * @method self expectsYaml()
 * @method self expectsCsv()
 */
class Request
{

    // Option constants
    const SERIALIZE_PAYLOAD_NEVER   = 0;
    const SERIALIZE_PAYLOAD_ALWAYS  = 1;
    const SERIALIZE_PAYLOAD_SMART   = 2;

    const MAX_REDIRECTS_DEFAULT     = 25;

    public $uri,
           $method                  = Http::GET,
           $headers                 = array(),
           $raw_headers             = '',
           $strict_ssl              = false,
           $content_type,
           $expected_type,
           $additional_curl_opts    = array(),
           $auto_parse              = true,
           $serialize_payload_method = self::SERIALIZE_PAYLOAD_SMART,
           $username,
           $password,
           $serialized_payload,
           $payload,
           $parse_callback,
           $error_callback,
           $send_callback,
           $follow_redirects        = false,
           $max_redirects           = self::MAX_REDIRECTS_DEFAULT,
           $payload_serializers     = array();

    // Options
    // private $_options = array(
    //     'serialize_payload_method' => self::SERIALIZE_PAYLOAD_SMART
    //     'auto_parse' => true
    // );

    // Curl Handle
    public $_ch,
           $_debug;

    // Template Request object
    private static $_template;

    /**
     * We made the constructor protected to force the factory style.  This was
     * done to keep the syntax cleaner and better the support the idea of
     * "default templates".  Very basic and flexible as it is only intended
     * for internal use.
     * @param array $attrs hash of initial attribute values
     */
    protected function __construct($attrs = null)
    {
        if (!is_array($attrs)) return;
        foreach ($attrs as $attr => $value) {
            $this->$attr = $value;
        }
    }

    // Defaults Management

    /**
     * Let's you configure default settings for this
     * class from a template Request object.  Simply construct a
     * Request object as much as you want to and then pass it to
     * this method.  It will then lock in those settings from
     * that template object.
     * The most common of which may be default mime
     * settings or strict ssl settings.
     * Again some slight memory overhead incurred here but in the grand
     * scheme of things as it typically only occurs once
     * @param Request $template
     */
    public static function ini(Request $template)
    {
        self::$_template = clone $template;
    }

    /**
     * Reset the default template back to the
     * library defaults.
     */
    public static function resetIni()
    {
        self::_initializeDefaults();
    }

    /**
     * Get default for a value based on the template object
     * @param string|null $attr Name of attribute (e.g. mime, headers)
     *    if null just return the whole template object;
     * @return mixed default value
     */
    public static function d($attr)
    {
        return isset($attr) ? self::$_template->$attr : self::$_template;
    }

    // Accessors

    /**
     * @return bool does the request have a timeout?
     */
    public function hasTimeout()
    {
        return isset($this->timeout);
    }

    /**
     * @return bool has the internal curl request been initialized?
     */
    public function hasBeenInitialized()
    {
        return isset($this->_ch);
    }

    /**
     * @return bool Is this request setup for basic auth?
     */
    public function hasBasicAuth()
    {
        return isset($this->password) && isset($this->username);
    }

    /**
     * @return bool Is this request setup for digest auth?
     */
    public function hasDigestAuth()
    {
        return isset($this->password) && isset($this->username) && $this->additional_curl_opts[CURLOPT_HTTPAUTH] == CURLAUTH_DIGEST;
    }

    /**
     * Specify a HTTP timeout
     * @param float|int $timeout seconds to timeout the HTTP call
     * @return Request
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    // alias timeout
    public function timeoutIn($seconds)
    {
        return $this->timeout($seconds);
    }

    /**
     * If the response is a 301 or 302 redirect, automatically
     * send off another request to that location
     * @param bool|int $follow follow or not to follow or maximal number of redirects
     * @return Request
     */
    public function followRedirects($follow = true)
    {
        $this->max_redirects = $follow === true ? self::MAX_REDIRECTS_DEFAULT : max(0, $follow);
        $this->follow_redirects = (bool) $follow;
        return $this;
    }

    /**
     * @see Request::followRedirects()
     * @return Request
     */
    public function doNotFollowRedirects()
    {
        return $this->followRedirects(false);
    }

    /**
     * Actually send off the request, and parse the response
     * @return Response with parsed results
     * @throws ConnectionErrorException when unable to parse or communicate w server
     */
    public function send()
    {
        if (!$this->hasBeenInitialized())
            $this->_curlPrep();

        $result = curl_exec($this->_ch);

        $response = $this->buildResponse($result);

        curl_close($this->_ch);
        unset($this->_ch);

        return $response;
    }
    public function sendIt()
    {
        return $this->send();
    }

    // Setters

    /**
     * @param string $uri
     * @return Request
     */
    public function uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * User Basic Auth.
     * Only use when over SSL/TSL/HTTPS.
     * @param string $username
     * @param string $password
     * @return Request
     */
    public function basicAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }
    // @alias of basicAuth
    public function authenticateWith($username, $password)
    {
        return $this->basicAuth($username, $password);
    }
    // @alias of basicAuth
    public function authenticateWithBasic($username, $password)
    {
        return $this->basicAuth($username, $password);
    }

    // @alias of ntlmAuth
    public function authenticateWithNTLM($username, $password)
    {
        return $this->ntlmAuth($username, $password);
    }

    public function ntlmAuth($username, $password)
    {
        $this->addOnCurlOption(CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        return $this->basicAuth($username, $password);
    }

    /**
     * User Digest Auth.
     * @param string $username
     * @param string $password
     * @return Request
     */
    public function digestAuth($username, $password)
    {
        $this->addOnCurlOption(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        return $this->basicAuth($username, $password);
    }

    // @alias of digestAuth
    public function authenticateWithDigest($username, $password)
    {
        return $this->digestAuth($username, $password);
    }

    /**
     * @return bool is this request setup for client side cert?
     */
    public function hasClientSideCert()
    {
        return isset($this->client_cert) && isset($this->client_key);
    }

    /**
     * Use Client Side Cert Authentication
     * @param string $key file path to client key
     * @param string $cert file path to client cert
     * @param string $passphrase for client key
     * @param string $encoding default PEM
     * @return Request
     */
    public function clientSideCert($cert, $key, $passphrase = null, $encoding = 'PEM')
    {
        $this->client_cert          = $cert;
        $this->client_key           = $key;
        $this->client_passphrase    = $passphrase;
        $this->client_encoding      = $encoding;

        return $this;
    }
    // @alias of basicAuth
    public function authenticateWithCert($cert, $key, $passphrase = null, $encoding = 'PEM')
    {
        return $this->clientSideCert($cert, $key, $passphrase, $encoding);
    }

    /**
     * Set the body of the request
     * @param mixed $payload
     * @param string $mimeType currently, sets the sends AND expects mime type although this
     *    behavior may change in the next minor release (as it is a potential breaking change).
     * @return Request
     */
    public function body($payload, $mimeType = null)
    {
        $this->mime($mimeType);
        $this->payload = $payload;
        // Iserntentially don't call _serializePayload yet.  Wait until
        // we actually send off the request to convert payload to string.
        // At that time, the `serialized_payload` is set accordingly.
        return $this;
    }

    /**
     * Helper function to set the Content type and Expected as same in
     * one swoop
     * @param string $mime mime type to use for content type and expected return type
     * @return Request
     */
    public function mime($mime)
    {
        if (empty($mime)) return $this;
        $this->content_type = $this->expected_type = Mime::getFullMime($mime);
        if ($this->isUpload()) {
            $this->neverSerializePayload();
        }
        return $this;
    }
    // @alias of mime
    public function sendsAndExpectsType($mime)
    {
        return $this->mime($mime);
    }
    // @alias of mime
    public function sendsAndExpects($mime)
    {
        return $this->mime($mime);
    }

    /**
     * Set the method.  Shouldn't be called often as the preferred syntax
     * for instantiation is the method specific factory methods.
     * @param string $method
     * @return Request
     */
    public function method($method)
    {
        if (empty($method)) return $this;
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $mime
     * @return Request
     */
    public function expects($mime)
    {
        if (empty($mime)) return $this;
        $this->expected_type = Mime::getFullMime($mime);
        return $this;
    }
    // @alias of expects
    public function expectsType($mime)
    {
        return $this->expects($mime);
    }

    public function attach($files)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        foreach ($files as $key => $file) {
            $mimeType = finfo_file($finfo, $file);
            if (function_exists('curl_file_create')) {
                $this->payload[$key] = curl_file_create($file, $mimeType);
            } else {
                $this->payload[$key] = '@' . $file;
	            if ($mimeType) {
		            $this->payload[$key] .= ';type=' . $mimeType;
	            }
            }
        }
        $this->sendsType(Mime::UPLOAD);
        return $this;
    }

    /**
     * @param string $mime
     * @return Request
     */
    public function contentType($mime)
    {
        if (empty($mime)) return $this;
        $this->content_type  = Mime::getFullMime($mime);
        if ($this->isUpload()) {
            $this->neverSerializePayload();
        }
        return $this;
    }
    // @alias of contentType
    public function sends($mime)
    {
        return $this->contentType($mime);
    }
    // @alias of contentType
    public function sendsType($mime)
    {
        return $this->contentType($mime);
    }

    /**
     * Do we strictly enforce SSL verification?
     * @param bool $strict
     * @return Request
     */
    public function strictSSL($strict)
    {
        $this->strict_ssl = $strict;
        return $this;
    }
    public function withoutStrictSSL()
    {
        return $this->strictSSL(false);
    }
    public function withStrictSSL()
    {
        return $this->strictSSL(true);
    }

    /**
     * Use proxy configuration
     * @param string $proxy_host Hostname or address of the proxy
     * @param int $proxy_port Port of the proxy. Default 80
     * @param string $auth_type Authentication type or null. Accepted values are CURLAUTH_BASIC, CURLAUTH_NTLM. Default null, no authentication
     * @param string $auth_username Authentication username. Default null
     * @param string $auth_password Authentication password. Default null
     * @return Request
     */
    public function useProxy($proxy_host, $proxy_port = 80, $auth_type = null, $auth_username = null, $auth_password = null, $proxy_type = Proxy::HTTP)
    {
        $this->addOnCurlOption(CURLOPT_PROXY, "{$proxy_host}:{$proxy_port}");
        $this->addOnCurlOption(CURLOPT_PROXYTYPE, $proxy_type);
        if (in_array($auth_type, array(CURLAUTH_BASIC,CURLAUTH_NTLM))) {
            $this->addOnCurlOption(CURLOPT_PROXYAUTH, $auth_type)
                ->addOnCurlOption(CURLOPT_PROXYUSERPWD, "{$auth_username}:{$auth_password}");
        }
        return $this;
    }

    /**
     * Shortcut for useProxy to configure SOCKS 4 proxy
     * @see Request::useProxy
     * @return Request
     */
    public function useSocks4Proxy($proxy_host, $proxy_port = 80, $auth_type = null, $auth_username = null, $auth_password = null)
    {
        return $this->useProxy($proxy_host, $proxy_port, $auth_type, $auth_username, $auth_password, Proxy::SOCKS4);
    }

    /**
     * Shortcut for useProxy to configure SOCKS 5 proxy
     * @see Request::useProxy
     * @return Request
     */
    public function useSocks5Proxy($proxy_host, $proxy_port = 80, $auth_type = null, $auth_username = null, $auth_password = null)
    {
        return $this->useProxy($proxy_host, $proxy_port, $auth_type, $auth_username, $auth_password, Proxy::SOCKS5);
    }

    /**
     * @return bool is this request setup for using proxy?
     */
    public function hasProxy()
    {
        /* We must be aware that proxy variables could come from environment also.
           In curl extension, http proxy can be specified not only via CURLOPT_PROXY option, 
           but also by environment variable called http_proxy.
        */
        return isset($this->additional_curl_opts[CURLOPT_PROXY]) && is_string($this->additional_curl_opts[CURLOPT_PROXY]) ||
            getenv("http_proxy");
    }

    /**
     * Determine how/if we use the built in serialization by
     * setting the serialize_payload_method
     * The default (SERIALIZE_PAYLOAD_SMART) is...
     *  - if payload is not a scalar (object/array)
     *    use the appropriate serialize method according to
     *    the Content-Type of this request.
     *  - if the payload IS a scalar (int, float, string, bool)
     *    than just return it as is.
     * When this option is set SERIALIZE_PAYLOAD_ALWAYS,
     * it will always use the appropriate
     * serialize option regardless of whether payload is scalar or not
     * When this option is set SERIALIZE_PAYLOAD_NEVER,
     * it will never use any of the serialization methods.
     * Really the only use for this is if you want the serialize methods
     * to handle strings or not (e.g. Blah is not valid JSON, but "Blah"
     * is).  Forcing the serialization helps prevent that kind of error from
     * happening.
     * @param int $mode
     * @return Request
     */
    public function serializePayload($mode)
    {
        $this->serialize_payload_method = $mode;
        return $this;
    }

    /**
     * @see Request::serializePayload()
     * @return Request
     */
    public function neverSerializePayload()
    {
        return $this->serializePayload(self::SERIALIZE_PAYLOAD_NEVER);
    }

    /**
     * This method is the default behavior
     * @see Request::serializePayload()
     * @return Request
     */
    public function smartSerializePayload()
    {
        return $this->serializePayload(self::SERIALIZE_PAYLOAD_SMART);
    }

    /**
     * @see Request::serializePayload()
     * @return Request
     */
    public function alwaysSerializePayload()
    {
        return $this->serializePayload(self::SERIALIZE_PAYLOAD_ALWAYS);
    }

    /**
     * Add an additional header to the request
     * Can also use the cleaner syntax of
     * $Request->withMyHeaderName($my_value);
     * @see Request::__call()
     *
     * @param string $header_name
     * @param string $value
     * @return Request
     */
    public function addHeader($header_name, $value)
    {
        $this->headers[$header_name] = $value;
        return $this;
    }

    /**
     * Add group of headers all at once.  Note: This is
     * here just as a convenience in very specific cases.
     * The preferred "readable" way would be to leverage
     * the support for custom header methods.
     * @param array $headers
     * @return Request
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }
        return $this;
    }

    /**
     * @param bool $auto_parse perform automatic "smart"
     *    parsing based on Content-Type or "expectedType"
     *    If not auto parsing, Response->body returns the body
     *    as a string.
     * @return Request
     */
    public function autoParse($auto_parse = true)
    {
        $this->auto_parse = $auto_parse;
        return $this;
    }

    /**
     * @see Request::autoParse()
     * @return Request
     */
    public function withoutAutoParsing()
    {
        return $this->autoParse(false);
    }

    /**
     * @see Request::autoParse()
     * @return Request
     */
    public function withAutoParsing()
    {
        return $this->autoParse(true);
    }

    /**
     * Use a custom function to parse the response.
     * @param \Closure $callback Takes the raw body of
     *    the http response and returns a mixed
     * @return Request
     */
    public function parseWith(\Closure $callback)
    {
        $this->parse_callback = $callback;
        return $this;
    }

    /**
     * @see Request::parseResponsesWith()
     * @param \Closure $callback
     * @return Request
     */
    public function parseResponsesWith(\Closure $callback)
    {
        return $this->parseWith($callback);
    }

    /**
     * Callback called to handle HTTP errors. When nothing is set, defaults
     * to logging via `error_log`
     * @param \Closure $callback (string $error)
     * @return Request
     */
    public function whenError(\Closure $callback)
    {
        $this->error_callback = $callback;
        return $this;
    }

    /**
     * Callback invoked after payload has been serialized but before
     * the request has been built.
     * @param \Closure $callback (Request $request)
     * @return Request
     */
    public function beforeSend(\Closure $callback)
    {
        $this->send_callback = $callback;
        return $this;
    }

    /**
     * Register a callback that will be used to serialize the payload
     * for a particular mime type.  When using "*" for the mime
     * type, it will use that parser for all responses regardless of the mime
     * type.  If a custom '*' and 'application/json' exist, the custom
     * 'application/json' would take precedence over the '*' callback.
     *
     * @param string $mime mime type we're registering
     * @param \Closure $callback takes one argument, $payload,
     *    which is the payload that we'll be
     * @return Request
     */
    public function registerPayloadSerializer($mime, \Closure $callback)
    {
        $this->payload_serializers[Mime::getFullMime($mime)] = $callback;
        return $this;
    }

    /**
     * @see Request::registerPayloadSerializer()
     * @param \Closure $callback
     * @return Request
     */
    public function serializePayloadWith(\Closure $callback)
    {
        return $this->registerPayloadSerializer('*', $callback);
    }

    /**
     * Magic method allows for neatly setting other headers in a
     * similar syntax as the other setters.  This method also allows
     * for the sends* syntax.
     * @param string $method "missing" method name called
     *    the method name called should be the name of the header that you
     *    are trying to set in camel case without dashes e.g. to set a
     *    header for Content-Type you would use contentType() or more commonly
     *    to add a custom header like X-My-Header, you would use xMyHeader().
     *    To promote readability, you can optionally prefix these methods with
     *    "with"  (e.g. withXMyHeader("blah") instead of xMyHeader("blah")).
     * @param array $args in this case, there should only ever be 1 argument provided
     *    and that argument should be a string value of the header we're setting
     * @return Request
     */
    public function __call($method, $args)
    {
        // This method supports the sends* methods
        // like sendsJSON, sendsForm
        //!method_exists($this, $method) &&
        if (substr($method, 0, 5) === 'sends') {
            $mime = strtolower(substr($method, 5));
            if (Mime::supportsMimeType($mime)) {
                $this->sends(Mime::getFullMime($mime));
                return $this;
            }
            // else {
            //     throw new \Exception("Unsupported Content-Type $mime");
            // }
        }
        if (substr($method, 0, 7) === 'expects') {
            $mime = strtolower(substr($method, 7));
            if (Mime::supportsMimeType($mime)) {
                $this->expects(Mime::getFullMime($mime));
                return $this;
            }
            // else {
            //     throw new \Exception("Unsupported Content-Type $mime");
            // }
        }

        // This method also adds the custom header support as described in the
        // method comments
        if (count($args) === 0)
            return;

        // Strip the sugar.  If it leads with "with", strip.
        // This is okay because: No defined HTTP headers begin with with,
        // and if you are defining a custom header, the standard is to prefix it
        // with an "X-", so that should take care of any collisions.
        if (substr($method, 0, 4) === 'with')
            $method = substr($method, 4);

        // Precede upper case letters with dashes, uppercase the first letter of method
        $header = ucwords(implode('-', preg_split('/([A-Z][^A-Z]*)/', $method, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));
        $this->addHeader($header, $args[0]);
        return $this;
    }

    // Internal Functions

    /**
     * This is the default template to use if no
     * template has been provided.  The template
     * tells the class which default values to use.
     * While there is a slight overhead for object
     * creation once per execution (not once per
     * Request instantiation), it promotes readability
     * and flexibility within the class.
     */
    private static function _initializeDefaults()
    {
        // This is the only place you will
        // see this constructor syntax.  It
        // is only done here to prevent infinite
        // recusion.  Do not use this syntax elsewhere.
        // It goes against the whole readability
        // and transparency idea.
        self::$_template = new Request(array('method' => Http::GET));

        // This is more like it...
        self::$_template
            ->withoutStrictSSL();
    }

    /**
     * Set the defaults on a newly instantiated object
     * Doesn't copy variables prefixed with _
     * @return Request
     */
    private function _setDefaults()
    {
        if (!isset(self::$_template))
            self::_initializeDefaults();
        foreach (self::$_template as $k=>$v) {
            if ($k[0] != '_')
                $this->$k = $v;
        }
        return $this;
    }

    private function _error($error)
    {
        // TODO add in support for various Loggers that follow
        // PSR 3 https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
        if (isset($this->error_callback)) {
            $this->error_callback->__invoke($error);
        } else {
            error_log($error);
        }
    }

    /**
     * Factory style constructor works nicer for chaining.  This
     * should also really only be used internally.  The Request::get,
     * Request::post syntax is preferred as it is more readable.
     * @param string $method Http Method
     * @param string $mime Mime Type to Use
     * @return Request
     */
    public static function init($method = null, $mime = null)
    {
        // Setup our handlers, can call it here as it's idempotent
        Bootstrap::init();

        // Setup the default template if need be
        if (!isset(self::$_template))
            self::_initializeDefaults();

        $request = new Request();
        return $request
               ->_setDefaults()
               ->method($method)
               ->sendsType($mime)
               ->expectsType($mime);
    }

    /**
     * Does the heavy lifting.  Uses de facto HTTP
     * library cURL to set up the HTTP request.
     * Note: It does NOT actually send the request
     * @return Request
     * @throws \Exception
     */
    public function _curlPrep()
    {
        // Check for required stuff
        if (!isset($this->uri))
            throw new \Exception('Attempting to send a request before defining a URI endpoint.');

        if (isset($this->payload)) {
            $this->serialized_payload = $this->_serializePayload($this->payload);
        }

        if (isset($this->send_callback)) {
            call_user_func($this->send_callback, $this);
        }

        $ch = curl_init($this->uri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        if ($this->method === Http::HEAD) {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        if ($this->hasBasicAuth()) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }

        if ($this->hasClientSideCert()) {

            if (!file_exists($this->client_key))
                throw new \Exception('Could not read Client Key');

            if (!file_exists($this->client_cert))
                throw new \Exception('Could not read Client Certificate');

            curl_setopt($ch, CURLOPT_SSLCERTTYPE,   $this->client_encoding);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE,    $this->client_encoding);
            curl_setopt($ch, CURLOPT_SSLCERT,       $this->client_cert);
            curl_setopt($ch, CURLOPT_SSLKEY,        $this->client_key);
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD,  $this->client_passphrase);
            // curl_setopt($ch, CURLOPT_SSLCERTPASSWD,  $this->client_cert_passphrase);
        }

        if ($this->hasTimeout()) {
            if (defined('CURLOPT_TIMEOUT_MS')) {
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout * 1000);
            } else {
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            }
        }

        if ($this->follow_redirects) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $this->max_redirects);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->strict_ssl);
        // zero is safe for all curl versions
        $verifyValue = $this->strict_ssl + 0;
        //Support for value 1 removed in cURL 7.28.1 value 2 valid in all versions
        if ($verifyValue > 0) $verifyValue++;
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifyValue);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // https://github.com/nategood/httpful/issues/84
        // set Content-Length to the size of the payload if present
        if (isset($this->payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->serialized_payload);
            if (!$this->isUpload()) {
                $this->headers['Content-Length'] =
                    $this->_determineLength($this->serialized_payload);
            }
        }

        $headers = array();
        // https://github.com/nategood/httpful/issues/37
        // Except header removes any HTTP 1.1 Continue from response headers
        $headers[] = 'Expect:';

        if (!isset($this->headers['User-Agent'])) {
            $headers[] = $this->buildUserAgent();
        }

        $headers[] = "Content-Type: {$this->content_type}";

        // allow custom Accept header if set
        if (!isset($this->headers['Accept'])) {
            // http://pretty-rfc.herokuapp.com/RFC2616#header.accept
            $accept = 'Accept: */*; q=0.5, text/plain; q=0.8, text/html;level=3;';

            if (!empty($this->expected_type)) {
                $accept .= "q=0.9, {$this->expected_type}";
            }

            $headers[] = $accept;
        }

        // Solve a bug on squid proxy, NONE/411 when miss content length
        if (!isset($this->headers['Content-Length']) && !$this->isUpload()) {
            $this->headers['Content-Length'] = 0;
        }

        foreach ($this->headers as $header => $value) {
            $headers[] = "$header: $value";
        }

        $url = \parse_url($this->uri);
        $path = (isset($url['path']) ? $url['path'] : '/').(isset($url['query']) ? '?'.$url['query'] : '');
        $this->raw_headers = "{$this->method} $path HTTP/1.1\r\n";
        $host = (isset($url['host']) ? $url['host'] : 'localhost').(isset($url['port']) ? ':'.$url['port'] : '');
        $this->raw_headers .= "Host: $host\r\n";
        $this->raw_headers .= \implode("\r\n", $headers);
        $this->raw_headers .= "\r\n";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($this->_debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        curl_setopt($ch, CURLOPT_HEADER, 1);

        // If there are some additional curl opts that the user wants
        // to set, we can tack them in here
        foreach ($this->additional_curl_opts as $curlopt => $curlval) {
            curl_setopt($ch, $curlopt, $curlval);
        }

        $this->_ch = $ch;

        return $this;
    }

    /**
     * @param string $str payload
     * @return int length of payload in bytes
     */
    public function _determineLength($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, '8bit');
        } else {
            return strlen($str);
        }
    }

    /**
     * @return bool
     */
    public function isUpload()
    {
        return Mime::UPLOAD == $this->content_type;
    }

    /**
     * @return string
     */
    public function buildUserAgent()
    {
        $user_agent = 'User-Agent: Httpful/' . Httpful::VERSION . ' (cURL/';
        $curl = \curl_version();

        if (isset($curl['version'])) {
            $user_agent .= $curl['version'];
        } else {
            $user_agent .= '?.?.?';
        }

        $user_agent .= ' PHP/'. PHP_VERSION . ' (' . PHP_OS . ')';

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $user_agent .= ' ' . \preg_replace('~PHP/[\d\.]+~U', '',
                $_SERVER['SERVER_SOFTWARE']);
        } else {
            if (isset($_SERVER['TERM_PROGRAM'])) {
                $user_agent .= " {$_SERVER['TERM_PROGRAM']}";
            }

            if (isset($_SERVER['TERM_PROGRAM_VERSION'])) {
                $user_agent .= "/{$_SERVER['TERM_PROGRAM_VERSION']}";
            }
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent .= " {$_SERVER['HTTP_USER_AGENT']}";
        }

        $user_agent .= ')';

        return $user_agent;
    }

    /**
     * Takes a curl result and generates a Response from it
     * @return Response
     */
    public function buildResponse($result) {
        if ($result === false) {
            if ($curlErrorNumber = curl_errno($this->_ch)) {
                $curlErrorString = curl_error($this->_ch);
                $this->_error($curlErrorString);

                $exception = new ConnectionErrorException('Unable to connect to "'.$this->uri.'": '
                        . $curlErrorNumber . ' ' . $curlErrorString);

                $exception->setCurlErrorNumber($curlErrorNumber)
                    ->setCurlErrorString($curlErrorString);

                throw $exception;
            }

            $this->_error('Unable to connect to "'.$this->uri.'".');
            throw new ConnectionErrorException('Unable to connect to "'.$this->uri.'".');
        }

        $info = curl_getinfo($this->_ch);

        // Remove the "HTTP/1.x 200 Connection established" string and any other headers added by proxy
        $proxy_regex = "/HTTP\/1\.[01] 200 Connection established.*?\r\n\r\n/si";
        if ($this->hasProxy() && preg_match($proxy_regex, $result)) {
            $result = preg_replace($proxy_regex, '', $result);
        }

        $response = explode("\r\n\r\n", $result, 2 + $info['redirect_count']);

        $body = array_pop($response);
        $headers = array_pop($response);

        return new Response($body, $headers, $this, $info);
    }

    /**
     * Semi-reluctantly added this as a way to add in curl opts
     * that are not otherwise accessible from the rest of the API.
     * @param string $curlopt
     * @param mixed $curloptval
     * @return Request
     */
    public function addOnCurlOption($curlopt, $curloptval)
    {
        $this->additional_curl_opts[$curlopt] = $curloptval;
        return $this;
    }

    /**
     * Turn payload from structured data into
     * a string based on the current Mime type.
     * This uses the auto_serialize option to determine
     * it's course of action.  See serialize method for more.
     * Renamed from _detectPayload to _serializePayload as of
     * 2012-02-15.
     *
     * Added in support for custom payload serializers.
     * The serialize_payload_method stuff still holds true though.
     * @see Request::registerPayloadSerializer()
     *
     * @param mixed $payload
     * @return string
     */
    private function _serializePayload($payload)
    {
        if (empty($payload) || $this->serialize_payload_method === self::SERIALIZE_PAYLOAD_NEVER)
            return $payload;

        // When we are in "smart" mode, don't serialize strings/scalars, assume they are already serialized
        if ($this->serialize_payload_method === self::SERIALIZE_PAYLOAD_SMART && is_scalar($payload))
            return $payload;

        // Use a custom serializer if one is registered for this mime type
        if (isset($this->payload_serializers['*']) || isset($this->payload_serializers[$this->content_type])) {
            $key = isset($this->payload_serializers[$this->content_type]) ? $this->content_type : '*';
            return call_user_func($this->payload_serializers[$key], $payload);
        }

        return Httpful::get($this->content_type)->serialize($payload);
    }

    /**
     * HTTP Method Get
     * @param string $uri optional uri to use
     * @param string $mime expected
     * @return Request
     */
    public static function get($uri, $mime = null)
    {
        return self::init(Http::GET)->uri($uri)->mime($mime);
    }


    /**
     * Like Request:::get, except that it sends off the request as well
     * returning a response
     * @param string $uri optional uri to use
     * @param string $mime expected
     * @return Response
     */
    public static function getQuick($uri, $mime = null)
    {
        return self::get($uri, $mime)->send();
    }

    /**
     * HTTP Method Post
     * @param string $uri optional uri to use
     * @param string $payload data to send in body of request
     * @param string $mime MIME to use for Content-Type
     * @return Request
     */
    public static function post($uri, $payload = null, $mime = null)
    {
        return self::init(Http::POST)->uri($uri)->body($payload, $mime);
    }

    /**
     * HTTP Method Put
     * @param string $uri optional uri to use
     * @param string $payload data to send in body of request
     * @param string $mime MIME to use for Content-Type
     * @return Request
     */
    public static function put($uri, $payload = null, $mime = null)
    {
        return self::init(Http::PUT)->uri($uri)->body($payload, $mime);
    }

    /**
     * HTTP Method Patch
     * @param string $uri optional uri to use
     * @param string $payload data to send in body of request
     * @param string $mime MIME to use for Content-Type
     * @return Request
     */
    public static function patch($uri, $payload = null, $mime = null)
    {
        return self::init(Http::PATCH)->uri($uri)->body($payload, $mime);
    }

    /**
     * HTTP Method Delete
     * @param string $uri optional uri to use
     * @return Request
     */
    public static function delete($uri, $mime = null)
    {
        return self::init(Http::DELETE)->uri($uri)->mime($mime);
    }

    /**
     * HTTP Method Head
     * @param string $uri optional uri to use
     * @return Request
     */
    public static function head($uri)
    {
        return self::init(Http::HEAD)->uri($uri);
    }

    /**
     * HTTP Method Options
     * @param string $uri optional uri to use
     * @return Request
     */
    public static function options($uri)
    {
        return self::init(Http::OPTIONS)->uri($uri);
    }
}
