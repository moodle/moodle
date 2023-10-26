<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;
use PhpXmlRpc\Helper\Logger;
use PhpXmlRpc\Helper\XMLParser;

/**
 * Allows effortless implementation of XML-RPC servers
 */
class Server
{
    protected static $logger;
    protected static $parser;
    protected static $charsetEncoder;

    /**
     * Defines how functions in dmap will be invoked: either using an xmlrpc request object
     * or plain php values.
     * Valid strings are 'xmlrpcvals', 'phpvals' or 'epivals'
     * @todo create class constants for these
     */
    public $functions_parameters_type = 'xmlrpcvals';

    /**
     * Option used for fine-tuning the encoding the php values returned from
     * functions registered in the dispatch map when the functions_parameters_types
     * member is set to 'phpvals'
     * @see Encoder::encode for a list of values
     */
    public $phpvals_encoding_options = array('auto_dates');

    /**
     * Controls whether the server is going to echo debugging messages back to the client as comments in response body.
     * Valid values: 0,1,2,3
     */
    public $debug = 1;

    /**
     * Controls behaviour of server when the invoked user function throws an exception:
     * 0 = catch it and return an 'internal error' xmlrpc response (default)
     * 1 = catch it and return an xmlrpc response with the error corresponding to the exception
     * 2 = allow the exception to float to the upper layers
     */
    public $exception_handling = 0;

    /**
     * When set to true, it will enable HTTP compression of the response, in case
     * the client has declared its support for compression in the request.
     * Set at constructor time.
     */
    public $compress_response = false;

    /**
     * List of http compression methods accepted by the server for requests. Set at constructor time.
     * NB: PHP supports deflate, gzip compressions out of the box if compiled w. zlib
     */
    public $accepted_compression = array();

    /// Shall we serve calls to system.* methods?
    public $allow_system_funcs = true;

    /**
     * List of charset encodings natively accepted for requests.
     * Set at constructor time.
     * UNUSED so far...
     */
    public $accepted_charset_encodings = array();

    /**
     * Charset encoding to be used for response.
     * NB: if we can, we will convert the generated response from internal_encoding to the intended one.
     * Can be: a supported xml encoding (only UTF-8 and ISO-8859-1 at present, unless mbstring is enabled),
     * null (leave unspecified in response, convert output stream to US_ASCII),
     * 'default' (use xmlrpc library default as specified in xmlrpc.inc, convert output stream if needed),
     * or 'auto' (use client-specified charset encoding or same as request if request headers do not specify it (unless request is US-ASCII: then use library default anyway).
     * NB: pretty dangerous if you accept every charset and do not have mbstring enabled)
     */
    public $response_charset_encoding = '';

    /**
     * Extra data passed at runtime to method handling functions. Used only by EPI layer
     */
    public $user_data = null;

    /**
     * Array defining php functions exposed as xmlrpc methods by this server.
     * @var array[] $dmap
     */
    protected $dmap = array();

    /**
     * Storage for internal debug info.
     */
    protected $debug_info = '';

    protected static $_xmlrpc_debuginfo = '';
    protected static $_xmlrpcs_occurred_errors = '';
    protected static $_xmlrpcs_prev_ehandler = '';

    public function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = Logger::instance();
        }
        return self::$logger;
    }

    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public function getParser()
    {
        if (self::$parser === null) {
            self::$parser = new XMLParser();
        }
        return self::$parser;
    }

    public static function setParser($parser)
    {
        self::$parser = $parser;
    }

    public function getCharsetEncoder()
    {
        if (self::$charsetEncoder === null) {
            self::$charsetEncoder = Charset::instance();
        }
        return self::$charsetEncoder;
    }

    public function setCharsetEncoder($charsetEncoder)
    {
        self::$charsetEncoder = $charsetEncoder;
    }

    /**
     * @param array[] $dispatchMap the dispatch map with definition of exposed services
     *                             Array keys are the names of the method names.
     *                             Each array value is an array with the following members:
     *                             - function (callable)
     *                             - docstring (optional)
     *                             - signature (array, optional)
     *                             - signature_docs (array, optional)
     *                             - parameters_type (string, optional)
     * @param boolean $serviceNow set to false to prevent the server from running upon construction
     */
    public function __construct($dispatchMap = null, $serviceNow = true)
    {
        // if ZLIB is enabled, let the server by default accept compressed requests,
        // and compress responses sent to clients that support them
        if (function_exists('gzinflate')) {
            $this->accepted_compression = array('gzip', 'deflate');
            $this->compress_response = true;
        }

        // by default the xml parser can support these 3 charset encodings
        $this->accepted_charset_encodings = array('UTF-8', 'ISO-8859-1', 'US-ASCII');

        // dispMap is a dispatch array of methods mapped to function names and signatures.
        // If a method doesn't appear in the map then an unknown method error is generated
        /* milosch - changed to make passing dispMap optional.
        * instead, you can use the class add_to_map() function
        * to add functions manually (borrowed from SOAPX4)
        */
        if ($dispatchMap) {
            $this->dmap = $dispatchMap;
            if ($serviceNow) {
                $this->service();
            }
        }
    }

    /**
     * Set debug level of server.
     *
     * @param integer $level debug lvl: determines info added to xmlrpc responses (as xml comments)
     *                    0 = no debug info,
     *                    1 = msgs set from user with debugmsg(),
     *                    2 = add complete xmlrpc request (headers and body),
     *                    3 = add also all processing warnings happened during method processing
     *                    (NB: this involves setting a custom error handler, and might interfere
     *                    with the standard processing of the php function exposed as method. In
     *                    particular, triggering an USER_ERROR level error will not halt script
     *                    execution anymore, but just end up logged in the xmlrpc response)
     *                    Note that info added at level 2 and 3 will be base64 encoded
     */
    public function setDebug($level)
    {
        $this->debug = $level;
    }

    /**
     * Add a string to the debug info that can be later serialized by the server as part of the response message.
     * Note that for best compatibility, the debug string should be encoded using the PhpXmlRpc::$xmlrpc_internalencoding
     * character set.
     *
     * @param string $msg
     */
    public static function xmlrpc_debugmsg($msg)
    {
        static::$_xmlrpc_debuginfo .= $msg . "\n";
    }

    /**
     * Add a string to the debug info that will be later serialized by the server as part of the response message
     * (base64 encoded, only when debug level >= 2)
     *
     * character set.
     * @param string $msg
     */
    public static function error_occurred($msg)
    {
        static::$_xmlrpcs_occurred_errors .= $msg . "\n";
    }

    /**
     * Return a string with the serialized representation of all debug info.
     *
     * @param string $charsetEncoding the target charset encoding for the serialization
     *
     * @return string an XML comment (or two)
     */
    public function serializeDebug($charsetEncoding = '')
    {
        // Tough encoding problem: which internal charset should we assume for debug info?
        // It might contain a copy of raw data received from client, ie with unknown encoding,
        // intermixed with php generated data and user generated data...
        // so we split it: system debug is base 64 encoded,
        // user debug info should be encoded by the end user using the INTERNAL_ENCODING
        $out = '';
        if ($this->debug_info != '') {
            $out .= "<!-- SERVER DEBUG INFO (BASE64 ENCODED):\n" . base64_encode($this->debug_info) . "\n-->\n";
        }
        if (static::$_xmlrpc_debuginfo != '') {
            $out .= "<!-- DEBUG INFO:\n" . $this->getCharsetEncoder()->encodeEntities(str_replace('--', '_-', static::$_xmlrpc_debuginfo), PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "\n-->\n";
            // NB: a better solution MIGHT be to use CDATA, but we need to insert it
            // into return payload AFTER the beginning tag
            //$out .= "<![CDATA[ DEBUG INFO:\n\n" . str_replace(']]>', ']_]_>', static::$_xmlrpc_debuginfo) . "\n]]>\n";
        }

        return $out;
    }

    /**
     * Execute the xmlrpc request, printing the response.
     *
     * @param string $data the request body. If null, the http POST request will be examined
     * @param bool $returnPayload When true, return the response but do not echo it or any http header
     *
     * @return Response|string the response object (usually not used by caller...) or its xml serialization
     *
     * @throws \Exception in case the executed method does throw an exception (and depending on server configuration)
     */
    public function service($data = null, $returnPayload = false)
    {
        if ($data === null) {
            $data = file_get_contents('php://input');
        }
        $rawData = $data;

        // reset internal debug info
        $this->debug_info = '';

        // Save what we received, before parsing it
        if ($this->debug > 1) {
            $this->debugmsg("+++GOT+++\n" . $data . "\n+++END+++");
        }

        $r = $this->parseRequestHeaders($data, $reqCharset, $respCharset, $respEncoding);
        if (!$r) {
            // this actually executes the request
            $r = $this->parseRequest($data, $reqCharset);

            // save full body of request into response, for more debugging usages.
            // Note that this is the _request_ data, not the response's own data, unlike what happens client-side
            /// @todo try to move this injection to the resp. constructor or use a non-deprecated access method
            $r->raw_data = $rawData;
        }

        if ($this->debug > 2 && static::$_xmlrpcs_occurred_errors) {
            $this->debugmsg("+++PROCESSING ERRORS AND WARNINGS+++\n" .
                static::$_xmlrpcs_occurred_errors . "+++END+++");
        }

        $payload = $this->xml_header($respCharset);
        if ($this->debug > 0) {
            $payload = $payload . $this->serializeDebug($respCharset);
        }

        // Do not create response serialization if it has already happened. Helps building json magic
        if (empty($r->payload)) {
            $r->serialize($respCharset);
        }
        $payload = $payload . $r->payload;

        if ($returnPayload) {
            return $payload;
        }

        // if we get a warning/error that has output some text before here, then we cannot
        // add a new header. We cannot say we are sending xml, either...
        if (!headers_sent()) {
            header('Content-Type: ' . $r->content_type);
            // we do not know if client actually told us an accepted charset, but if he did
            // we have to tell him what we did
            header("Vary: Accept-Charset");

            // http compression of output: only
            // if we can do it, and we want to do it, and client asked us to,
            // and php ini settings do not force it already
            /// @todo check separately for gzencode and gzcompress functions, in case of polyfills
            $phpNoSelfCompress = !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler');
            if ($this->compress_response && function_exists('gzencode') && $respEncoding != ''
                && $phpNoSelfCompress
            ) {
                if (strpos($respEncoding, 'gzip') !== false) {
                    $payload = gzencode($payload);
                    header("Content-Encoding: gzip");
                    header("Vary: Accept-Encoding");
                } elseif (strpos($respEncoding, 'deflate') !== false) {
                    $payload = gzcompress($payload);
                    header("Content-Encoding: deflate");
                    header("Vary: Accept-Encoding");
                }
            }

            // Do not output content-length header if php is compressing output for us:
            // it will mess up measurements.
            // Note that Apache/mod_php will add (and even alter!) the Content-Length header on its own, but only for
            // responses up to 8000 bytes
            if ($phpNoSelfCompress) {
                header('Content-Length: ' . (int)strlen($payload));
            }
        } else {
            $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': http headers already sent before response is fully generated. Check for php warning or error messages');
        }

        print $payload;

        // return request, in case subclasses want it
        return $r;
    }

    /**
     * Add a method to the dispatch map.
     *
     * @param string $methodName the name with which the method will be made available
     * @param callable $function the php function that will get invoked
     * @param array[] $sig the array of valid method signatures.
     *                     Each element is one signature: an array of strings with at least one element
     *                     First element = type of returned value. Elements 2..N = types of parameters 1..N
     * @param string $doc method documentation
     * @param array[] $sigDoc the array of valid method signatures docs, following the format of $sig but with
     *                        descriptions instead of types (one string for return type, one per param)
     *
     * @todo raise a warning if the user tries to register a 'system.' method
     * @todo allow setting parameters_type
     */
    public function add_to_map($methodName, $function, $sig = null, $doc = false, $sigDoc = false)
    {
        $this->dmap[$methodName] = array(
            'function' => $function,
            'docstring' => $doc,
        );
        if ($sig) {
            $this->dmap[$methodName]['signature'] = $sig;
        }
        if ($sigDoc) {
            $this->dmap[$methodName]['signature_docs'] = $sigDoc;
        }
    }

    /**
     * Verify type and number of parameters received against a list of known signatures.
     *
     * @param array|Request $in array of either xmlrpc value objects or xmlrpc type definitions
     * @param array $sigs array of known signatures to match against
     *
     * @return array int, string
     */
    protected function verifySignature($in, $sigs)
    {
        // check each possible signature in turn
        if (is_object($in)) {
            $numParams = $in->getNumParams();
        } else {
            $numParams = count($in);
        }
        foreach ($sigs as $curSig) {
            if (count($curSig) == $numParams + 1) {
                $itsOK = 1;
                for ($n = 0; $n < $numParams; $n++) {
                    if (is_object($in)) {
                        $p = $in->getParam($n);
                        if ($p->kindOf() == 'scalar') {
                            $pt = $p->scalartyp();
                        } else {
                            $pt = $p->kindOf();
                        }
                    } else {
                        $pt = ($in[$n] == 'i4') ? 'int' : strtolower($in[$n]); // dispatch maps never use i4...
                    }

                    // param index is $n+1, as first member of sig is return type
                    if ($pt != $curSig[$n + 1] && $curSig[$n + 1] != Value::$xmlrpcValue) {
                        $itsOK = 0;
                        $pno = $n + 1;
                        $wanted = $curSig[$n + 1];
                        $got = $pt;
                        break;
                    }
                }
                if ($itsOK) {
                    return array(1, '');
                }
            }
        }
        if (isset($wanted)) {
            return array(0, "Wanted ${wanted}, got ${got} at param ${pno}");
        } else {
            return array(0, "No method signature matches number of parameters");
        }
    }

    /**
     * Parse http headers received along with xmlrpc request. If needed, inflate request.
     *
     * @return Response|null null on success or an error Response
     */
    protected function parseRequestHeaders(&$data, &$reqEncoding, &$respEncoding, &$respCompression)
    {
        // check if $_SERVER is populated: it might have been disabled via ini file
        // (this is true even when in CLI mode)
        if (count($_SERVER) == 0) {
            $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': cannot parse request headers as $_SERVER is not populated');
        }

        if ($this->debug > 1) {
            if (function_exists('getallheaders')) {
                $this->debugmsg(''); // empty line
                foreach (getallheaders() as $name => $val) {
                    $this->debugmsg("HEADER: $name: $val");
                }
            }
        }

        if (isset($_SERVER['HTTP_CONTENT_ENCODING'])) {
            $contentEncoding = str_replace('x-', '', $_SERVER['HTTP_CONTENT_ENCODING']);
        } else {
            $contentEncoding = '';
        }

        $rawData = $data;

        // check if request body has been compressed and decompress it
        if ($contentEncoding != '' && strlen($data)) {
            if ($contentEncoding == 'deflate' || $contentEncoding == 'gzip') {
                // if decoding works, use it. else assume data wasn't gzencoded
                if (function_exists('gzinflate') && in_array($contentEncoding, $this->accepted_compression)) {
                    if ($contentEncoding == 'deflate' && $degzdata = @gzuncompress($data)) {
                        $data = $degzdata;
                        if ($this->debug > 1) {
                            $this->debugmsg("\n+++INFLATED REQUEST+++[" . strlen($data) . " chars]+++\n" . $data . "\n+++END+++");
                        }
                    } elseif ($contentEncoding == 'gzip' && $degzdata = @gzinflate(substr($data, 10))) {
                        $data = $degzdata;
                        if ($this->debug > 1) {
                            $this->debugmsg("+++INFLATED REQUEST+++[" . strlen($data) . " chars]+++\n" . $data . "\n+++END+++");
                        }
                    } else {
                        $r = new Response(0, PhpXmlRpc::$xmlrpcerr['server_decompress_fail'],
                            PhpXmlRpc::$xmlrpcstr['server_decompress_fail'], '', array('raw_data' => $rawData)
                        );

                        return $r;
                    }
                } else {
                    $r = new Response(0, PhpXmlRpc::$xmlrpcerr['server_cannot_decompress'],
                        PhpXmlRpc::$xmlrpcstr['server_cannot_decompress'], '', array('raw_data' => $rawData)
                    );

                    return $r;
                }
            }
        }

        // check if client specified accepted charsets, and if we know how to fulfill
        // the request
        if ($this->response_charset_encoding == 'auto') {
            $respEncoding = '';
            if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
                // here we should check if we can match the client-requested encoding
                // with the encodings we know we can generate.
                /// @todo we should parse q=0.x preferences instead of getting first charset specified...
                $clientAcceptedCharsets = explode(',', strtoupper($_SERVER['HTTP_ACCEPT_CHARSET']));
                // Give preference to internal encoding
                $knownCharsets = array(PhpXmlRpc::$xmlrpc_internalencoding, 'UTF-8', 'ISO-8859-1', 'US-ASCII');
                foreach ($knownCharsets as $charset) {
                    foreach ($clientAcceptedCharsets as $accepted) {
                        if (strpos($accepted, $charset) === 0) {
                            $respEncoding = $charset;
                            break;
                        }
                    }
                    if ($respEncoding) {
                        break;
                    }
                }
            }
        } else {
            $respEncoding = $this->response_charset_encoding;
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $respCompression = $_SERVER['HTTP_ACCEPT_ENCODING'];
        } else {
            $respCompression = '';
        }

        // 'guestimate' request encoding
        /// @todo check if mbstring is enabled and automagic input conversion is on: it might mingle with this check???
        $reqEncoding = XMLParser::guessEncoding(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '',
            $data);

        return null;
    }

    /**
     * Parse an xml chunk containing an xmlrpc request and execute the corresponding
     * php function registered with the server.
     *
     * @param string $data the xml request
     * @param string $reqEncoding (optional) the charset encoding of the xml request
     *
     * @return Response
     *
     * @throws \Exception in case the executed method does throw an exception (and depending on server configuration)
     *
     * @internal this function will become protected in the future
     * @todo either rename this function or move the 'execute' part out of it...
     */
    public function parseRequest($data, $reqEncoding = '')
    {
        // decompose incoming XML into request structure

        if ($reqEncoding != '') {
            // Since parsing will fail if
            // - charset is not specified in the xml prologue,
            // - the encoding is not UTF8 and
            // - there are non-ascii chars in the text,
            // we try to work round that...
            // The following code might be better for mb_string enabled installs, but
            // makes the lib about 200% slower...
            //if (!is_valid_charset($reqEncoding, array('UTF-8')))
            if (!in_array($reqEncoding, array('UTF-8', 'US-ASCII')) && !XMLParser::hasEncoding($data)) {
                if ($reqEncoding == 'ISO-8859-1') {
                    $data = utf8_encode($data);
                } else {
                    if (extension_loaded('mbstring')) {
                        $data = mb_convert_encoding($data, 'UTF-8', $reqEncoding);
                    } else {
                        $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': invalid charset encoding of received request: ' . $reqEncoding);
                    }
                }
            }
        }

        // PHP internally might use ISO-8859-1, so we have to tell the xml parser to give us back data in the expected charset.
        // What if internal encoding is not in one of the 3 allowed? We use the broadest one, ie. utf8
        // This allows to send data which is native in various charset,
        // by extending xmlrpc_encode_entities() and setting xmlrpc_internalencoding
        if (!in_array(PhpXmlRpc::$xmlrpc_internalencoding, array('UTF-8', 'ISO-8859-1', 'US-ASCII'))) {
            /// @todo emit a warning
            $options = array(XML_OPTION_TARGET_ENCODING => 'UTF-8');
        } else {
            $options = array(XML_OPTION_TARGET_ENCODING => PhpXmlRpc::$xmlrpc_internalencoding);
        }

        $xmlRpcParser = $this->getParser();
        $xmlRpcParser->parse($data, $this->functions_parameters_type, XMLParser::ACCEPT_REQUEST, $options);
        if ($xmlRpcParser->_xh['isf'] > 2) {
            // (BC) we return XML error as a faultCode
            preg_match('/^XML error ([0-9]+)/', $xmlRpcParser->_xh['isf_reason'], $matches);
            $r = new Response(0,
                PhpXmlRpc::$xmlrpcerrxml + $matches[1],
                $xmlRpcParser->_xh['isf_reason']);
        } elseif ($xmlRpcParser->_xh['isf']) {
            $r = new Response(0,
                PhpXmlRpc::$xmlrpcerr['invalid_request'],
                PhpXmlRpc::$xmlrpcstr['invalid_request'] . ' ' . $xmlRpcParser->_xh['isf_reason']);
        } else {
            // small layering violation in favor of speed and memory usage:
            // we should allow the 'execute' method handle this, but in the
            // most common scenario (xmlrpc values type server with some methods
            // registered as phpvals) that would mean a useless encode+decode pass
            if ($this->functions_parameters_type != 'xmlrpcvals' ||
                (isset($this->dmap[$xmlRpcParser->_xh['method']]['parameters_type']) &&
                    ($this->dmap[$xmlRpcParser->_xh['method']]['parameters_type'] != 'xmlrpcvals')
                )
            ) {
                if ($this->debug > 1) {
                    $this->debugmsg("\n+++PARSED+++\n" . var_export($xmlRpcParser->_xh['params'], true) . "\n+++END+++");
                }
                $r = $this->execute($xmlRpcParser->_xh['method'], $xmlRpcParser->_xh['params'], $xmlRpcParser->_xh['pt']);
            } else {
                // build a Request object with data parsed from xml
                $req = new Request($xmlRpcParser->_xh['method']);
                // now add parameters in
                for ($i = 0; $i < count($xmlRpcParser->_xh['params']); $i++) {
                    $req->addParam($xmlRpcParser->_xh['params'][$i]);
                }

                if ($this->debug > 1) {
                    $this->debugmsg("\n+++PARSED+++\n" . var_export($req, true) . "\n+++END+++");
                }
                $r = $this->execute($req);
            }
        }

        return $r;
    }

    /**
     * Execute a method invoked by the client, checking parameters used.
     *
     * @param Request|string $req either a Request obj or a method name
     * @param mixed[] $params array with method parameters as php types (only if m is method name)
     * @param string[] $paramTypes array with xmlrpc types of method parameters (only if m is method name)
     *
     * @return Response
     *
     * @throws \Exception in case the executed method does throw an exception (and depending on server configuration)
     */
    protected function execute($req, $params = null, $paramTypes = null)
    {
        static::$_xmlrpcs_occurred_errors = '';
        static::$_xmlrpc_debuginfo = '';

        if (is_object($req)) {
            $methName = $req->method();
        } else {
            $methName = $req;
        }
        $sysCall = $this->isSyscall($methName);
        $dmap = $sysCall ? $this->getSystemDispatchMap() : $this->dmap;

        if (!isset($dmap[$methName]['function'])) {
            // No such method
            return new Response(0,
                PhpXmlRpc::$xmlrpcerr['unknown_method'],
                PhpXmlRpc::$xmlrpcstr['unknown_method']);
        }

        // Check signature
        if (isset($dmap[$methName]['signature'])) {
            $sig = $dmap[$methName]['signature'];
            if (is_object($req)) {
                list($ok, $errStr) = $this->verifySignature($req, $sig);
            } else {
                list($ok, $errStr) = $this->verifySignature($paramTypes, $sig);
            }
            if (!$ok) {
                // Didn't match.
                return new Response(
                    0,
                    PhpXmlRpc::$xmlrpcerr['incorrect_params'],
                    PhpXmlRpc::$xmlrpcstr['incorrect_params'] . ": ${errStr}"
                );
            }
        }

        $func = $dmap[$methName]['function'];
        // let the 'class::function' syntax be accepted in dispatch maps
        if (is_string($func) && strpos($func, '::')) {
            $func = explode('::', $func);
        }

        if (is_array($func)) {
            if (is_object($func[0])) {
                $funcName = get_class($func[0]) . '->' . $func[1];
            } else {
                $funcName = implode('::', $func);
            }
        } else if ($func instanceof \Closure) {
            $funcName = 'Closure';
        } else {
            $funcName = $func;
        }

        // verify that function to be invoked is in fact callable
        if (!is_callable($func)) {
            $this->getLogger()->errorLog("XML-RPC: " . __METHOD__ . ": function '$funcName' registered as method handler is not callable");
            return new Response(
                0,
                PhpXmlRpc::$xmlrpcerr['server_error'],
                PhpXmlRpc::$xmlrpcstr['server_error'] . ": no function matches method"
            );
        }

        // If debug level is 3, we should catch all errors generated during
        // processing of user function, and log them as part of response
        if ($this->debug > 2) {
            self::$_xmlrpcs_prev_ehandler = set_error_handler(array('\PhpXmlRpc\Server', '_xmlrpcs_errorHandler'));
        }

        try {
            // Allow mixed-convention servers
            if (is_object($req)) {
                if ($sysCall) {
                    $r = call_user_func($func, $this, $req);
                } else {
                    $r = call_user_func($func, $req);
                }
                if (!is_a($r, 'PhpXmlRpc\Response')) {
                    $this->getLogger()->errorLog("XML-RPC: " . __METHOD__ . ": function '$funcName' registered as method handler does not return an xmlrpc response object but a " . gettype($r));
                    if (is_a($r, 'PhpXmlRpc\Value')) {
                        $r = new Response($r);
                    } else {
                        $r = new Response(
                            0,
                            PhpXmlRpc::$xmlrpcerr['server_error'],
                            PhpXmlRpc::$xmlrpcstr['server_error'] . ": function does not return xmlrpc response object"
                        );
                    }
                }
            } else {
                // call a 'plain php' function
                if ($sysCall) {
                    array_unshift($params, $this);
                    $r = call_user_func_array($func, $params);
                } else {
                    // 3rd API convention for method-handling functions: EPI-style
                    if ($this->functions_parameters_type == 'epivals') {
                        $r = call_user_func_array($func, array($methName, $params, $this->user_data));
                        // mimic EPI behaviour: if we get an array that looks like an error, make it
                        // an error response
                        if (is_array($r) && array_key_exists('faultCode', $r) && array_key_exists('faultString', $r)) {
                            $r = new Response(0, (integer)$r['faultCode'], (string)$r['faultString']);
                        } else {
                            // functions using EPI api should NOT return resp objects,
                            // so make sure we encode the return type correctly
                            $encoder = new Encoder();
                            $r = new Response($encoder->encode($r, array('extension_api')));
                        }
                    } else {
                        $r = call_user_func_array($func, $params);
                    }
                }
                // the return type can be either a Response object or a plain php value...
                if (!is_a($r, '\PhpXmlRpc\Response')) {
                    // what should we assume here about automatic encoding of datetimes
                    // and php classes instances???
                    $encoder = new Encoder();
                    $r = new Response($encoder->encode($r, $this->phpvals_encoding_options));
                }
            }
        } catch (\Exception $e) {
            // (barring errors in the lib) an uncatched exception happened
            // in the called function, we wrap it in a proper error-response
            switch ($this->exception_handling) {
                case 2:
                    if ($this->debug > 2) {
                        if (self::$_xmlrpcs_prev_ehandler) {
                            set_error_handler(self::$_xmlrpcs_prev_ehandler);
                        } else {
                            restore_error_handler();
                        }
                    }
                    throw $e;
                case 1:
                    $r = new Response(0, $e->getCode(), $e->getMessage());
                    break;
                default:
                    $r = new Response(0, PhpXmlRpc::$xmlrpcerr['server_error'], PhpXmlRpc::$xmlrpcstr['server_error']);
            }
        }
        if ($this->debug > 2) {
            // note: restore the error handler we found before calling the
            // user func, even if it has been changed inside the func itself
            if (self::$_xmlrpcs_prev_ehandler) {
                set_error_handler(self::$_xmlrpcs_prev_ehandler);
            } else {
                restore_error_handler();
            }
        }

        return $r;
    }

    /**
     * Add a string to the 'internal debug message' (separate from 'user debug message').
     *
     * @param string $string
     */
    protected function debugmsg($string)
    {
        $this->debug_info .= $string . "\n";
    }

    /**
     * @param string $charsetEncoding
     * @return string
     */
    protected function xml_header($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            return "<?xml version=\"1.0\" encoding=\"$charsetEncoding\"?" . ">\n";
        } else {
            return "<?xml version=\"1.0\"?" . ">\n";
        }
    }

    /**
     * @param string $methName
     * @return bool
     */
    protected function isSyscall($methName)
    {
        return (strpos($methName, "system.") === 0);
    }

    /**
     * @return array[]
     */
    public function getDispatchMap()
    {
        return $this->dmap;
    }

    /**
     * @return array[]
     */
    public function getSystemDispatchMap()
    {
        if (!$this->allow_system_funcs) {
            return array();
        }

        return array(
            'system.listMethods' => array(
                'function' => 'PhpXmlRpc\Server::_xmlrpcs_listMethods',
                // listMethods: signature was either a string, or nothing.
                // The useless string variant has been removed
                'signature' => array(array(Value::$xmlrpcArray)),
                'docstring' => 'This method lists all the methods that the XML-RPC server knows how to dispatch',
                'signature_docs' => array(array('list of method names')),
            ),
            'system.methodHelp' => array(
                'function' => 'PhpXmlRpc\Server::_xmlrpcs_methodHelp',
                'signature' => array(array(Value::$xmlrpcString, Value::$xmlrpcString)),
                'docstring' => 'Returns help text if defined for the method passed, otherwise returns an empty string',
                'signature_docs' => array(array('method description', 'name of the method to be described')),
            ),
            'system.methodSignature' => array(
                'function' => 'PhpXmlRpc\Server::_xmlrpcs_methodSignature',
                'signature' => array(array(Value::$xmlrpcArray, Value::$xmlrpcString)),
                'docstring' => 'Returns an array of known signatures (an array of arrays) for the method name passed. If no signatures are known, returns a none-array (test for type != array to detect missing signature)',
                'signature_docs' => array(array('list of known signatures, each sig being an array of xmlrpc type names', 'name of method to be described')),
            ),
            'system.multicall' => array(
                'function' => 'PhpXmlRpc\Server::_xmlrpcs_multicall',
                'signature' => array(array(Value::$xmlrpcArray, Value::$xmlrpcArray)),
                'docstring' => 'Boxcar multiple RPC calls in one request. See http://www.xmlrpc.com/discuss/msgReader$1208 for details',
                'signature_docs' => array(array('list of response structs, where each struct has the usual members', 'list of calls, with each call being represented as a struct, with members "methodname" and "params"')),
            ),
            'system.getCapabilities' => array(
                'function' => 'PhpXmlRpc\Server::_xmlrpcs_getCapabilities',
                'signature' => array(array(Value::$xmlrpcStruct)),
                'docstring' => 'This method lists all the capabilities that the XML-RPC server has: the (more or less standard) extensions to the xmlrpc spec that it adheres to',
                'signature_docs' => array(array('list of capabilities, described as structs with a version number and url for the spec')),
            ),
        );
    }

    /* Functions that implement system.XXX methods of xmlrpc servers */

    /**
     * @return array[]
     */
    public function getCapabilities()
    {
        $outAr = array(
            // xmlrpc spec: always supported
            'xmlrpc' => array(
                'specUrl' => 'http://www.xmlrpc.com/spec',
                'specVersion' => 1
            ),
            // if we support system.xxx functions, we always support multicall, too...
            // Note that, as of 2006/09/17, the following URL does not respond anymore
            'system.multicall' => array(
                'specUrl' => 'http://www.xmlrpc.com/discuss/msgReader$1208',
                'specVersion' => 1
            ),
            // introspection: version 2! we support 'mixed', too
            'introspection' => array(
                'specUrl' => 'http://phpxmlrpc.sourceforge.net/doc-2/ch10.html',
                'specVersion' => 2,
            ),
        );

        // NIL extension
        if (PhpXmlRpc::$xmlrpc_null_extension) {
            $outAr['nil'] = array(
                'specUrl' => 'http://www.ontosys.com/xml-rpc/extensions.php',
                'specVersion' => 1
            );
        }

        return $outAr;
    }

    /**
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_getCapabilities($server, $req = null)
    {
        $encoder = new Encoder();
        return new Response($encoder->encode($server->getCapabilities()));
    }

    /**
     * @param Server $server
     * @param Request $req if called in plain php values mode, second param is missing
     * @return Response
     */
    public static function _xmlrpcs_listMethods($server, $req = null)
    {
        $outAr = array();
        foreach ($server->dmap as $key => $val) {
            $outAr[] = new Value($key, 'string');
        }
        foreach ($server->getSystemDispatchMap() as $key => $val) {
            $outAr[] = new Value($key, 'string');
        }

        return new Response(new Value($outAr, 'array'));
    }

    /**
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_methodSignature($server, $req)
    {
        // let accept as parameter both an xmlrpc value or string
        if (is_object($req)) {
            $methName = $req->getParam(0);
            $methName = $methName->scalarval();
        } else {
            $methName = $req;
        }
        if ($server->isSyscall($methName)) {
            $dmap = $server->getSystemDispatchMap();
        } else {
            $dmap = $server->dmap;
        }
        if (isset($dmap[$methName])) {
            if (isset($dmap[$methName]['signature'])) {
                $sigs = array();
                foreach ($dmap[$methName]['signature'] as $inSig) {
                    $curSig = array();
                    foreach ($inSig as $sig) {
                        $curSig[] = new Value($sig, 'string');
                    }
                    $sigs[] = new Value($curSig, 'array');
                }
                $r = new Response(new Value($sigs, 'array'));
            } else {
                // NB: according to the official docs, we should be returning a
                // "none-array" here, which means not-an-array
                $r = new Response(new Value('undef', 'string'));
            }
        } else {
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['introspect_unknown'], PhpXmlRpc::$xmlrpcstr['introspect_unknown']);
        }

        return $r;
    }

    /**
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_methodHelp($server, $req)
    {
        // let accept as parameter both an xmlrpc value or string
        if (is_object($req)) {
            $methName = $req->getParam(0);
            $methName = $methName->scalarval();
        } else {
            $methName = $req;
        }
        if ($server->isSyscall($methName)) {
            $dmap = $server->getSystemDispatchMap();
        } else {
            $dmap = $server->dmap;
        }
        if (isset($dmap[$methName])) {
            if (isset($dmap[$methName]['docstring'])) {
                $r = new Response(new Value($dmap[$methName]['docstring'], 'string'));
            } else {
                $r = new Response(new Value('', 'string'));
            }
        } else {
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['introspect_unknown'], PhpXmlRpc::$xmlrpcstr['introspect_unknown']);
        }

        return $r;
    }

    public static function _xmlrpcs_multicall_error($err)
    {
        if (is_string($err)) {
            $str = PhpXmlRpc::$xmlrpcstr["multicall_${err}"];
            $code = PhpXmlRpc::$xmlrpcerr["multicall_${err}"];
        } else {
            $code = $err->faultCode();
            $str = $err->faultString();
        }
        $struct = array();
        $struct['faultCode'] = new Value($code, 'int');
        $struct['faultString'] = new Value($str, 'string');

        return new Value($struct, 'struct');
    }

    /**
     * @param Server $server
     * @param Value $call
     * @return Value
     */
    public static function _xmlrpcs_multicall_do_call($server, $call)
    {
        if ($call->kindOf() != 'struct') {
            return static::_xmlrpcs_multicall_error('notstruct');
        }
        $methName = @$call['methodName'];
        if (!$methName) {
            return static::_xmlrpcs_multicall_error('nomethod');
        }
        if ($methName->kindOf() != 'scalar' || $methName->scalartyp() != 'string') {
            return static::_xmlrpcs_multicall_error('notstring');
        }
        if ($methName->scalarval() == 'system.multicall') {
            return static::_xmlrpcs_multicall_error('recursion');
        }

        $params = @$call['params'];
        if (!$params) {
            return static::_xmlrpcs_multicall_error('noparams');
        }
        if ($params->kindOf() != 'array') {
            return static::_xmlrpcs_multicall_error('notarray');
        }

        $req = new Request($methName->scalarval());
        foreach($params as $i => $param) {
            if (!$req->addParam($param)) {
                $i++; // for error message, we count params from 1
                return static::_xmlrpcs_multicall_error(new Response(0,
                    PhpXmlRpc::$xmlrpcerr['incorrect_params'],
                    PhpXmlRpc::$xmlrpcstr['incorrect_params'] . ": probable xml error in param " . $i));
            }
        }

        $result = $server->execute($req);

        if ($result->faultCode() != 0) {
            return static::_xmlrpcs_multicall_error($result); // Method returned fault.
        }

        return new Value(array($result->value()), 'array');
    }

    /**
     * @param Server $server
     * @param Value $call
     * @return Value
     */
    public static function _xmlrpcs_multicall_do_call_phpvals($server, $call)
    {
        if (!is_array($call)) {
            return static::_xmlrpcs_multicall_error('notstruct');
        }
        if (!array_key_exists('methodName', $call)) {
            return static::_xmlrpcs_multicall_error('nomethod');
        }
        if (!is_string($call['methodName'])) {
            return static::_xmlrpcs_multicall_error('notstring');
        }
        if ($call['methodName'] == 'system.multicall') {
            return static::_xmlrpcs_multicall_error('recursion');
        }
        if (!array_key_exists('params', $call)) {
            return static::_xmlrpcs_multicall_error('noparams');
        }
        if (!is_array($call['params'])) {
            return static::_xmlrpcs_multicall_error('notarray');
        }

        // this is a simplistic hack, since we might have received
        // base64 or datetime values, but they will be listed as strings here...
        $pt = array();
        $wrapper = new Wrapper();
        foreach ($call['params'] as $val) {
            // support EPI-encoded base64 and datetime values
            if ($val instanceof \stdClass && isset($val->xmlrpc_type)) {
                $pt[] = $val->xmlrpc_type == 'datetime' ? Value::$xmlrpcDateTime : $val->xmlrpc_type;
            } else {
                $pt[] = $wrapper->php2XmlrpcType(gettype($val));
            }
        }

        $result = $server->execute($call['methodName'], $call['params'], $pt);

        if ($result->faultCode() != 0) {
            return static::_xmlrpcs_multicall_error($result); // Method returned fault.
        }

        return new Value(array($result->value()), 'array');
    }

    /**
     * @param Server $server
     * @param Request|array $req
     * @return Response
     */
    public static function _xmlrpcs_multicall($server, $req)
    {
        $result = array();
        // let accept a plain list of php parameters, beside a single xmlrpc msg object
        if (is_object($req)) {
            $calls = $req->getParam(0);
            foreach($calls as $call) {
                $result[] = static::_xmlrpcs_multicall_do_call($server, $call);
            }
        } else {
            $numCalls = count($req);
            for ($i = 0; $i < $numCalls; $i++) {
                $result[$i] = static::_xmlrpcs_multicall_do_call_phpvals($server, $req[$i]);
            }
        }

        return new Response(new Value($result, 'array'));
    }

    /**
     * Error handler used to track errors that occur during server-side execution of PHP code.
     * This allows to report back to the client whether an internal error has occurred or not
     * using an xmlrpc response object, instead of letting the client deal with the html junk
     * that a PHP execution error on the server generally entails.
     *
     * NB: in fact a user defined error handler can only handle WARNING, NOTICE and USER_* errors.
     */
    public static function _xmlrpcs_errorHandler($errCode, $errString, $filename = null, $lineNo = null, $context = null)
    {
        // obey the @ protocol
        if (error_reporting() == 0) {
            return;
        }

        //if($errCode != E_NOTICE && $errCode != E_WARNING && $errCode != E_USER_NOTICE && $errCode != E_USER_WARNING)
        if ($errCode != E_STRICT) {
            \PhpXmlRpc\Server::error_occurred($errString);
        }
        // Try to avoid as much as possible disruption to the previous error handling
        // mechanism in place
        if (self::$_xmlrpcs_prev_ehandler == '') {
            // The previous error handler was the default: all we should do is log error
            // to the default error log (if level high enough)
            if (ini_get('log_errors') && (intval(ini_get('error_reporting')) & $errCode)) {
                if (self::$logger === null) {
                    self::$logger = Logger::instance();
                }
                self::$logger->errorLog($errString);
            }
        } else {
            // Pass control on to previous error handler, trying to avoid loops...
            if (self::$_xmlrpcs_prev_ehandler != array('\PhpXmlRpc\Server', '_xmlrpcs_errorHandler')) {
                if (is_array(self::$_xmlrpcs_prev_ehandler)) {
                    // the following works both with static class methods and plain object methods as error handler
                    call_user_func_array(self::$_xmlrpcs_prev_ehandler, array($errCode, $errString, $filename, $lineNo, $context));
                } else {
                    $method = self::$_xmlrpcs_prev_ehandler;
                    $method($errCode, $errString, $filename, $lineNo, $context);
                }
            }
        }
    }
}
