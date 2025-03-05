<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Exception\NoSuchMethodException;
use PhpXmlRpc\Exception\ValueErrorException;
use PhpXmlRpc\Helper\Http;
use PhpXmlRpc\Helper\Interop;
use PhpXmlRpc\Helper\Logger;
use PhpXmlRpc\Helper\XMLParser;
use PhpXmlRpc\Traits\CharsetEncoderAware;
use PhpXmlRpc\Traits\DeprecationLogger;
use PhpXmlRpc\Traits\ParserAware;

/**
 * Allows effortless implementation of XML-RPC servers
 *
 * @property string[] $accepted_compression deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property bool $allow_system_funcs deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property bool $compress_response deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property int $debug deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property int $exception_handling deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property string $functions_parameters_type deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property array $phpvals_encoding_options deprecated - public access left in purely for BC. Access via getOption()/setOption()
 * @property string $response_charset_encoding deprecated - public access left in purely for BC. Access via getOption()/setOption()
 */
class Server
{
    use CharsetEncoderAware;
    use DeprecationLogger;
    use ParserAware;

    const OPT_ACCEPTED_COMPRESSION = 'accepted_compression';
    const OPT_ALLOW_SYSTEM_FUNCS = 'allow_system_funcs';
    const OPT_COMPRESS_RESPONSE = 'compress_response';
    const OPT_DEBUG = 'debug';
    const OPT_EXCEPTION_HANDLING = 'exception_handling';
    const OPT_FUNCTIONS_PARAMETERS_TYPE = 'functions_parameters_type';
    const OPT_PHPVALS_ENCODING_OPTIONS = 'phpvals_encoding_options';
    const OPT_RESPONSE_CHARSET_ENCODING = 'response_charset_encoding';

    /** @var string */
    protected static $responseClass = '\\PhpXmlRpc\\Response';

    /**
     * @var string
     * Defines how functions in $dmap will be invoked: either using an xml-rpc Request object or plain php values.
     * Valid strings are 'xmlrpcvals', 'phpvals' or 'epivals' (the latter only for use by polyfill-xmlrpc).
     *
     * @todo create class constants for these
     */
    protected $functions_parameters_type = 'xmlrpcvals';

    /**
     * @var array
     * Option used for fine-tuning the encoding the php values returned from functions registered in the dispatch map
     * when the functions_parameters_type member is set to 'phpvals'.
     * @see Encoder::encode for a list of values
     */
    protected $phpvals_encoding_options = array('auto_dates');

    /**
     * @var int
     * Controls whether the server is going to echo debugging messages back to the client as comments in response body.
     * SECURITY SENSITIVE!
     * Valid values:
     * 0 =
     * 1 =
     * 2 =
     * 3 =
     */
    protected $debug = 1;

    /**
     * @var int
     * Controls behaviour of server when the invoked method-handler function throws an exception (within the `execute` method):
     * 0 = catch it and return an 'internal error' xml-rpc response (default)
     * 1 = SECURITY SENSITIVE DO NOT ENABLE ON PUBLIC SERVERS!!! catch it and return an xml-rpc response with the error
     *     corresponding to the exception, both its code and message.
     * 2 = allow the exception to float to the upper layers
     * Can be overridden per-method-handler in the dispatch map
     */
    protected $exception_handling = 0;

    /**
     * @var bool
     * When set to true, it will enable HTTP compression of the response, in case the client has declared its support
     * for compression in the request.
     * Automatically set at constructor time.
     */
    protected $compress_response = false;

    /**
     * @var string[]
     * List of http compression methods accepted by the server for requests. Automatically set at constructor time.
     * NB: PHP supports deflate, gzip compressions out of the box if compiled w. zlib
     */
    protected $accepted_compression = array();

    /**
     * @var bool
     * Shall we serve calls to system.* methods?
     */
    protected $allow_system_funcs = true;

    /**
     * List of charset encodings natively accepted for requests.
     * Set at constructor time.
     * @deprecated UNUSED so far by this library. It is still accessible by subclasses but will be dropped in the future.
     */
    private $accepted_charset_encodings = array();

    /**
     * @var string
     * Charset encoding to be used for response.
     * NB: if we can, we will convert the generated response from internal_encoding to the intended one.
     * Can be:
     * - a supported xml encoding (only UTF-8 and ISO-8859-1, unless mbstring is enabled),
     * - null (leave unspecified in response, convert output stream to US_ASCII),
     * - 'auto' (use client-specified charset encoding or same as request if request headers do not specify it (unless request is US-ASCII: then use library default anyway).
     * NB: pretty dangerous if you accept every charset and do not have mbstring enabled)
     */
    protected $response_charset_encoding = '';

    protected static $options = array(
        self::OPT_ACCEPTED_COMPRESSION,
        self::OPT_ALLOW_SYSTEM_FUNCS,
        self::OPT_COMPRESS_RESPONSE,
        self::OPT_DEBUG,
        self::OPT_EXCEPTION_HANDLING,
        self::OPT_FUNCTIONS_PARAMETERS_TYPE,
        self::OPT_PHPVALS_ENCODING_OPTIONS,
        self::OPT_RESPONSE_CHARSET_ENCODING,
    );

    /**
     * @var mixed
     * Extra data passed at runtime to method handling functions. Used only by EPI layer
     * @internal
     */
    public $user_data = null;

    /**
     * Array defining php functions exposed as xml-rpc methods by this server.
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

    /**
     * @param array[] $dispatchMap the dispatch map with definition of exposed services
     *                             Array keys are the names of the method names.
     *                             Each array value is an array with the following members:
     *                             - function (callable)
     *                             - docstring (optional)
     *                             - signature (array, optional)
     *                             - signature_docs (array, optional)
     *                             - parameters_type (string, optional). Valid values: 'phpvals', 'xmlrpcvals'
     *                             - exception_handling (int, optional)
     * @param boolean $serviceNow set to false in order to prevent the server from running upon construction
     */
    public function __construct($dispatchMap = null, $serviceNow = true)
    {
        // if ZLIB is enabled, let the server by default accept compressed requests,
        // and compress responses sent to clients that support them
        if (function_exists('gzinflate')) {
            $this->accepted_compression[] = 'gzip';
        }
        if (function_exists('gzuncompress')) {
            $this->accepted_compression[] = 'deflate';
        }
        if (function_exists('gzencode') || function_exists('gzcompress')) {
            $this->compress_response = true;
        }

        // by default the xml parser can support these 3 charset encodings
        $this->accepted_charset_encodings = array('UTF-8', 'ISO-8859-1', 'US-ASCII');

        // dispMap is a dispatch array of methods mapped to function names and signatures.
        // If a method doesn't appear in the map then an unknown method error is generated.
        // milosch - changed to make passing dispMap optional. Instead, you can use the addToMap() function
        // to add functions manually (borrowed from SOAPX4)
        if ($dispatchMap) {
            $this->setDispatchMap($dispatchMap);
            if ($serviceNow) {
                $this->service();
            }
        }
    }

    /**
     * @param string $name see all the OPT_ constants
     * @param mixed $value
     * @return $this
     * @throws ValueErrorException on unsupported option
     */
    public function setOption($name, $value)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION :
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                $this->$name = $value;
                break;
            default:
                throw new ValueErrorException("Unsupported option '$name'");
        }

        return $this;
    }

    /**
     * @param string $name see all the OPT_ constants
     * @return mixed
     * @throws ValueErrorException on unsupported option
     */
    public function getOption($name)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION:
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                return $this->$name;
            default:
                throw new ValueErrorException("Unsupported option '$name'");
        }
    }

    /**
     * Returns the complete list of Server options.
     * @return array
     */
    public function getOptions()
    {
        $values = array();
        foreach(static::$options as $opt) {
            $values[$opt] = $this->getOption($opt);
        }
        return $values;
    }

    /**
     * @param array $options key:  see all the OPT_ constants
     * @return $this
     * @throws ValueErrorException on unsupported option
     */
    public function setOptions($options)
    {
        foreach($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Set debug level of server.
     *
     * @param integer $level debug lvl: determines info added to xml-rpc responses (as xml comments)
     *                    0 = no debug info,
     *                    1 = msgs set from user with debugmsg(),
     *                    2 = add complete xml-rpc request (headers and body),
     *                    3 = add also all processing warnings happened during method processing
     *                    (NB: this involves setting a custom error handler, and might interfere
     *                    with the standard processing of the php function exposed as method. In
     *                    particular, triggering a USER_ERROR level error will not halt script
     *                    execution anymore, but just end up logged in the xml-rpc response)
     *                    Note that info added at level 2 and 3 will be base64 encoded
     * @return $this
     */
    public function setDebug($level)
    {
        $this->debug = $level;
        return $this;
    }

    /**
     * Add a string to the debug info that can be later serialized by the server as part of the response message.
     * Note that for best compatibility, the debug string should be encoded using the PhpXmlRpc::$xmlrpc_internalencoding
     * character set.
     *
     * @param string $msg
     * @return void
     */
    public static function xmlrpc_debugmsg($msg)
    {
        static::$_xmlrpc_debuginfo .= $msg . "\n";
    }

    /**
     * Add a string to the debug info that will be later serialized by the server as part of the response message
     * (base64 encoded) when debug level >= 2
     *
     * @param string $msg
     * @return void
     */
    public static function error_occurred($msg)
    {
        static::$_xmlrpcs_occurred_errors .= $msg . "\n";
    }

    /**
     * Return a string with the serialized representation of all debug info.
     *
     * @internal this function will become protected in the future
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
     * Execute the xml-rpc request, printing the response.
     *
     * @param string $data the request body. If null, the http POST request will be examined
     * @param bool $returnPayload When true, return the response but do not echo it or any http header
     *
     * @return Response|string the response object (usually not used by caller...) or its xml serialization
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
            $this->debugMsg("+++GOT+++\n" . $data . "\n+++END+++");
        }

        $resp = $this->parseRequestHeaders($data, $reqCharset, $respCharset, $respEncoding);
        if (!$resp) {
            // this actually executes the request
            $resp = $this->parseRequest($data, $reqCharset);

            // save full body of request into response, for debugging purposes.
            // NB: this is the _request_ data, not the response's own data, unlike what happens client-side
            /// @todo try to move this injection to the resp. constructor or use a non-deprecated access method. Or, even
            ///       better: just avoid setting this, and set debug info of the received http request in the request
            ///       object instead? It's not like the developer misses access to _SERVER, _COOKIES though...
            ///       Last but not least: the raw data might be of use to handler functions - but in decompressed form...
            $resp->raw_data = $rawData;
        }

        if ($this->debug > 2 && static::$_xmlrpcs_occurred_errors != '') {
            $this->debugMsg("+++PROCESSING ERRORS AND WARNINGS+++\n" .
                static::$_xmlrpcs_occurred_errors . "+++END+++");
        }

        $header = $resp->xml_header($respCharset);
        if ($this->debug > 0) {
            $header .= $this->serializeDebug($respCharset);
        }

        // Do not create response serialization if it has already happened. Helps to build json magic
        /// @todo what if the payload was created targeting a different charset than $respCharset?
        ///       Also, if we do not call serialize(), the request will not set its content-type to have the charset declared
        $payload = $resp->getPayload();
        if (empty($payload)) {
            $payload = $resp->serialize($respCharset);
        }
        $payload = $header . $payload;

        if ($returnPayload) {
            return $payload;
        }

        // if we get a warning/error that has output some text before here, then we cannot
        // add a new header. We cannot say we are sending xml, either...
        if (!headers_sent()) {
            header('Content-Type: ' . $resp->getContentType());
            // we do not know if client actually told us an accepted charset, but if it did we have to tell it what we did
            header("Vary: Accept-Charset");

            // http compression of output: only if we can do it, and we want to do it, and client asked us to,
            // and php ini settings do not force it already
            $phpNoSelfCompress = !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler');
            if ($this->compress_response && $respEncoding != '' && $phpNoSelfCompress) {
                if (strpos($respEncoding, 'gzip') !== false && function_exists('gzencode')) {
                    $payload = gzencode($payload);
                    header("Content-Encoding: gzip");
                    header("Vary: Accept-Encoding");
                } elseif (strpos($respEncoding, 'deflate') !== false && function_exists('gzcompress')) {
                    $payload = gzcompress($payload);
                    header("Content-Encoding: deflate");
                    header("Vary: Accept-Encoding");
                }
            }

            // Do not output content-length header if php is compressing output for us: it will mess up measurements.
            // Note that Apache/mod_php will add (and even alter!) the Content-Length header on its own, but only for
            // responses up to 8000 bytes
            if ($phpNoSelfCompress) {
                header('Content-Length: ' . (int)strlen($payload));
            }
        } else {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': http headers already sent before response is fully generated. Check for php warning or error messages');
        }

        print $payload;

        // return response, in case subclasses want it
        return $resp;
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
     * @param string $parametersType to allow single method handlers to receive php values instead of a Request, or vice-versa
     * @param int $exceptionHandling @see $this->exception_handling
     * @return void
     *
     * @todo raise a warning if the user tries to register a 'system.' method
     */
    public function addToMap($methodName, $function, $sig = null, $doc = false, $sigDoc = false, $parametersType = false,
        $exceptionHandling = false)
    {
       $this->add_to_map($methodName, $function, $sig, $doc, $sigDoc, $parametersType, $exceptionHandling);
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
     * @param string $parametersType to allow single method handlers to receive php values instead of a Request, or vice-versa
     * @param int $exceptionHandling @see $this->exception_handling
     * @return void
     *
     * @todo raise a warning if the user tries to register a 'system.' method
     * @deprecated use addToMap instead
     */
    public function add_to_map($methodName, $function, $sig = null, $doc = false, $sigDoc = false, $parametersType = false,
        $exceptionHandling = false)
    {
        $this->logDeprecationUnlessCalledBy('addToMap');

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
        if ($parametersType) {
            $this->dmap[$methodName]['parameters_type'] = $parametersType;
        }
        if ($exceptionHandling !== false) {
            $this->dmap[$methodName]['exception_handling'] = $exceptionHandling;
        }
    }

    /**
     * Verify type and number of parameters received against a list of known signatures.
     *
     * @param array|Request $in array of either xml-rpc value objects or xml-rpc type definitions
     * @param array $sigs array of known signatures to match against
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
                            $pt = $p->scalarTyp();
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
            return array(0, "Wanted {$wanted}, got {$got} at param {$pno}");
        } else {
            return array(0, "No method signature matches number of parameters");
        }
    }

    /**
     * Parse http headers received along with xml-rpc request. If needed, inflate request.
     *
     * @return Response|null null on success or an error Response
     */
    protected function parseRequestHeaders(&$data, &$reqEncoding, &$respEncoding, &$respCompression)
    {
        // check if $_SERVER is populated: it might have been disabled via ini file
        // (this is true even when in CLI mode)
        if (count($_SERVER) == 0) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': cannot parse request headers as $_SERVER is not populated');
        }

        if ($this->debug > 1) {
            if (function_exists('getallheaders')) {
                $this->debugMsg(''); // empty line
                foreach (getallheaders() as $name => $val) {
                    $this->debugMsg("HEADER: $name: $val");
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
                /// @todo test separately for gzinflate and gzuncompress
                if (function_exists('gzinflate') && in_array($contentEncoding, $this->accepted_compression)) {
                    if ($contentEncoding == 'deflate' && $degzdata = @gzuncompress($data)) {
                        $data = $degzdata;
                        if ($this->debug > 1) {
                            $this->debugMsg("\n+++INFLATED REQUEST+++[" . strlen($data) . " chars]+++\n" . $data . "\n+++END+++");
                        }
                    } elseif ($contentEncoding == 'gzip' && $degzdata = @gzinflate(substr($data, 10))) {
                        $data = $degzdata;
                        if ($this->debug > 1) {
                            $this->debugMsg("+++INFLATED REQUEST+++[" . strlen($data) . " chars]+++\n" . $data . "\n+++END+++");
                        }
                    } else {
                        $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['server_decompress_fail'],
                            PhpXmlRpc::$xmlrpcstr['server_decompress_fail'], '', array('raw_data' => $rawData)
                        );

                        return $r;
                    }
                } else {
                    $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['server_cannot_decompress'],
                        PhpXmlRpc::$xmlrpcstr['server_cannot_decompress'], '', array('raw_data' => $rawData)
                    );

                    return $r;
                }
            }
        }

        // check if client specified accepted charsets, and if we know how to fulfill the request
        if ($this->response_charset_encoding == 'auto') {
            $respEncoding = '';
            if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
                // here we check if we can match the client-requested encoding with the encodings we know we can generate.
                // we parse q=0.x preferences instead of preferring the first charset specified
                $http = new Http();
                $clientAcceptedCharsets = $http->parseAcceptHeader($_SERVER['HTTP_ACCEPT_CHARSET']);
                $knownCharsets = $this->getCharsetEncoder()->knownCharsets();
                foreach ($clientAcceptedCharsets as $accepted) {
                    foreach ($knownCharsets as $charset) {
                        if (strtoupper($accepted) == strtoupper($charset)) {
                            $respEncoding = $charset;
                            break 2;
                        }
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
        $parser = $this->getParser();
        $reqEncoding = $parser->guessEncoding(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '',
            $data);

        return null;
    }

    /**
     * Parse an xml chunk containing an xml-rpc request and execute the corresponding php function registered with the
     * server.
     * @internal this function will become protected in the future
     *
     * @param string $data the xml request
     * @param string $reqEncoding (optional) the charset encoding of the xml request
     * @return Response
     * @throws \Exception in case the executed method does throw an exception (and depending on server configuration)
     *
     * @todo either rename this function or move the 'execute' part out of it...
     */
    public function parseRequest($data, $reqEncoding = '')
    {
        // decompose incoming XML into request structure

        /// @todo move this block of code into the XMLParser
        if ($reqEncoding != '') {
            // Since parsing will fail if
            // - charset is not specified in the xml declaration,
            // - the encoding is not UTF8 and
            // - there are non-ascii chars in the text,
            // we try to work round that...
            // The following code might be better for mb_string enabled installs, but it makes the lib about 200% slower...
            //if (!is_valid_charset($reqEncoding, array('UTF-8')))
            if (!in_array($reqEncoding, array('UTF-8', 'US-ASCII')) && !XMLParser::hasEncoding($data)) {
                if (function_exists('mb_convert_encoding')) {
                    $data = mb_convert_encoding($data, 'UTF-8', $reqEncoding);
                } else {
                    if ($reqEncoding == 'ISO-8859-1') {
                        $data = utf8_encode($data);
                    } else {
                        $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': unsupported charset encoding of received request: ' . $reqEncoding);
                    }
                }
            }
        }
        // PHP internally might use ISO-8859-1, so we have to tell the xml parser to give us back data in the expected charset.
        // What if internal encoding is not in one of the 3 allowed? We use the broadest one, i.e. utf8
        if (in_array(PhpXmlRpc::$xmlrpc_internalencoding, array('UTF-8', 'ISO-8859-1', 'US-ASCII'))) {
            $options = array(XML_OPTION_TARGET_ENCODING => PhpXmlRpc::$xmlrpc_internalencoding);
        } else {
            $options = array(XML_OPTION_TARGET_ENCODING => 'UTF-8', 'target_charset' => PhpXmlRpc::$xmlrpc_internalencoding);
        }
        // register a callback with the xml parser for when it finds the method name
        $options['methodname_callback'] = array($this, 'methodNameCallback');

        $xmlRpcParser = $this->getParser();
        try {
            // NB: during parsing, the actual type of php values built will be automatically switched from
            // $this->functions_parameters_type to the one defined in the method signature, if defined there. This
            // happens via the parser making a call to $this->methodNameCallback as soon as it finds the desired method
            $_xh = $xmlRpcParser->parse($data, $this->functions_parameters_type, XMLParser::ACCEPT_REQUEST, $options);
            // BC
            if (!is_array($_xh)) {
                $_xh = $xmlRpcParser->_xh;
            }
        } catch (NoSuchMethodException $e) {
            return new static::$responseClass(0, $e->getCode(), $e->getMessage());
        }

        if ($_xh['isf'] == 3) {
            // (BC) we return XML error as a faultCode
            preg_match('/^XML error ([0-9]+)/', $_xh['isf_reason'], $matches);
            return new static::$responseClass(
                0,
                PhpXmlRpc::$xmlrpcerrxml + (int)$matches[1],
                $_xh['isf_reason']);
        } elseif ($_xh['isf']) {
            /// @todo separate better the various cases, as we have done in Request::parseResponse: invalid xml-rpc vs.
            ///       parsing error
            return new static::$responseClass(
                0,
                PhpXmlRpc::$xmlrpcerr['invalid_request'],
                PhpXmlRpc::$xmlrpcstr['invalid_request'] . ' ' . $_xh['isf_reason']);
        } else {
            // small layering violation in favor of speed and memory usage: we should allow the 'execute' method handle
            // this, but in the most common scenario (xml-rpc values type server with some methods registered as phpvals)
            // that would mean a useless encode+decode pass
            if ($this->functions_parameters_type != 'xmlrpcvals' ||
                (isset($this->dmap[$_xh['method']]['parameters_type']) &&
                    ($this->dmap[$_xh['method']]['parameters_type'] != 'xmlrpcvals')
                )
            ) {
                if ($this->debug > 1) {
                    $this->debugMsg("\n+++PARSED+++\n" . var_export($_xh['params'], true) . "\n+++END+++");
                }

                return $this->execute($_xh['method'], $_xh['params'], $_xh['pt']);
            } else {
                // build a Request object with data parsed from xml and add parameters in
                $req = new Request($_xh['method']);
                /// @todo for more speed, we could just pass in the array to the constructor (and loose the type validation)...
                for ($i = 0; $i < count($_xh['params']); $i++) {
                    $req->addParam($_xh['params'][$i]);
                }

                if ($this->debug > 1) {
                    $this->debugMsg("\n+++PARSED+++\n" . var_export($req, true) . "\n+++END+++");
                }

                return $this->execute($req);
            }
        }
    }

    /**
     * Execute a method invoked by the client, checking parameters used.
     *
     * @param Request|string $req either a Request obj or a method name
     * @param mixed[] $params array with method parameters as php types (only if $req is method name)
     * @param string[] $paramTypes array with xml-rpc types of method parameters (only if $req is method name)
     * @return Response
     *
     * @throws \Exception in case the executed method does throw an exception (and depending on server configuration)
     */
    protected function execute($req, $params = null, $paramTypes = null)
    {
        static::$_xmlrpcs_occurred_errors = '';
        static::$_xmlrpc_debuginfo = '';

        if (is_object($req)) {
            $methodName = $req->method();
        } else {
            $methodName = $req;
        }

        $sysCall = $this->isSyscall($methodName);
        $dmap = $sysCall ? $this->getSystemDispatchMap() : $this->dmap;

        if (!isset($dmap[$methodName]['function'])) {
            // No such method
            return new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['unknown_method'], PhpXmlRpc::$xmlrpcstr['unknown_method']);
        }

        // Check signature
        if (isset($dmap[$methodName]['signature'])) {
            $sig = $dmap[$methodName]['signature'];
            if (is_object($req)) {
                list($ok, $errStr) = $this->verifySignature($req, $sig);
            } else {
                list($ok, $errStr) = $this->verifySignature($paramTypes, $sig);
            }
            if (!$ok) {
                // Didn't match.
                return new static::$responseClass(
                    0,
                    PhpXmlRpc::$xmlrpcerr['incorrect_params'],
                    PhpXmlRpc::$xmlrpcstr['incorrect_params'] . ": {$errStr}"
                );
            }
        }

        $func = $dmap[$methodName]['function'];

        // let the 'class::function' syntax be accepted in dispatch maps
        if (is_string($func) && strpos($func, '::')) {
            $func = explode('::', $func);
        }

        // build string representation of function 'name'
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
            $this->getLogger()->error("XML-RPC: " . __METHOD__ . ": function '$funcName' registered as method handler is not callable");
            return new static::$responseClass(
                0,
                PhpXmlRpc::$xmlrpcerr['server_error'],
                PhpXmlRpc::$xmlrpcstr['server_error'] . ": no function matches method"
            );
        }

        if (isset($dmap[$methodName]['exception_handling'])) {
            $exception_handling = (int)$dmap[$methodName]['exception_handling'];
        } else {
            $exception_handling = $this->exception_handling;
        }

        // We always catch all errors generated during processing of user function, and log them as part of response;
        // if debug level is 3 or above, we also serialize them in the response as comments
        self::$_xmlrpcs_prev_ehandler = set_error_handler(array('\PhpXmlRpc\Server', '_xmlrpcs_errorHandler'));

        /// @todo what about using output-buffering as well, in case user code echoes anything to screen?

        try {
            // Allow mixed-convention servers
            if (is_object($req)) {
                // call an 'xml-rpc aware' function
                if ($sysCall) {
                    $r = call_user_func($func, $this, $req);
                } else {
                    $r = call_user_func($func, $req);
                }
                if (!is_a($r, 'PhpXmlRpc\Response')) {
                    $this->getLogger()->error("XML-RPC: " . __METHOD__ . ": function '$funcName' registered as method handler does not return an xmlrpc response object but a " . gettype($r));
                    if (is_a($r, 'PhpXmlRpc\Value')) {
                        $r = new static::$responseClass($r);
                    } else {
                        $r = new static::$responseClass(
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
                        $r = call_user_func_array($func, array($methodName, $params, $this->user_data));
                        // mimic EPI behaviour: if we get an array that looks like an error, make it an error response
                        if (is_array($r) && array_key_exists('faultCode', $r) && array_key_exists('faultString', $r)) {
                            $r = new static::$responseClass(0, (integer)$r['faultCode'], (string)$r['faultString']);
                        } else {
                            // functions using EPI api should NOT return resp objects, so make sure we encode the
                            // return type correctly
                            $encoder = new Encoder();
                            $r = new static::$responseClass($encoder->encode($r, array('extension_api')));
                        }
                    } else {
                        $r = call_user_func_array($func, $params);
                    }
                }
                // the return type can be either a Response object or a plain php value...
                if (!is_a($r, '\PhpXmlRpc\Response')) {
                    // q: what should we assume here about automatic encoding of datetimes and php classes instances?
                    // a: let the user decide
                    $encoder = new Encoder();
                    $r = new static::$responseClass($encoder->encode($r, $this->phpvals_encoding_options));
                }
            }
        /// @todo bump minimum php version to 7.1 and use a single catch clause instead of the duplicate blocks
        } catch (\Exception $e) {
            // (barring errors in the lib) an uncaught exception happened in the called function, we wrap it in a
            // proper error-response
            switch ($exception_handling) {
                case 2:
                    if (self::$_xmlrpcs_prev_ehandler) {
                        set_error_handler(self::$_xmlrpcs_prev_ehandler);
                        self::$_xmlrpcs_prev_ehandler = null;
                    } else {
                        restore_error_handler();
                    }
                    throw $e;
                case 1:
                    $errCode = $e->getCode();
                    if ($errCode == 0) {
                        $errCode = PhpXmlRpc::$xmlrpcerr['server_error'];
                    }
                    $r = new static::$responseClass(0, $errCode, $e->getMessage());
                    break;
                default:
                    $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['server_error'], PhpXmlRpc::$xmlrpcstr['server_error']);
            }
        } catch (\Error $e) {
            // (barring errors in the lib) an uncaught exception happened in the called function, we wrap it in a
            // proper error-response
            switch ($exception_handling) {
                case 2:
                    if (self::$_xmlrpcs_prev_ehandler) {
                        set_error_handler(self::$_xmlrpcs_prev_ehandler);
                        self::$_xmlrpcs_prev_ehandler = null;
                    } else {
                        restore_error_handler();
                    }
                    throw $e;
                case 1:
                    $errCode = $e->getCode();
                    if ($errCode == 0) {
                        $errCode = PhpXmlRpc::$xmlrpcerr['server_error'];
                    }
                    $r = new static::$responseClass(0, $errCode, $e->getMessage());
                    break;
                default:
                    $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['server_error'], PhpXmlRpc::$xmlrpcstr['server_error']);
            }
        }

        // note: restore the error handler we found before calling the user func, even if it has been changed
        // inside the func itself
        if (self::$_xmlrpcs_prev_ehandler) {
            set_error_handler(self::$_xmlrpcs_prev_ehandler);
            self::$_xmlrpcs_prev_ehandler = null;
        } else {
            restore_error_handler();
        }

        return $r;
    }

    /**
     * Registered as callback for when the XMLParser has found the name of the method to execute.
     * Handling that early allows to 1. stop parsing the rest of the xml if there is no such method registered, and
     * 2. tweak the type of data that the parser will return, in case the server uses mixed-calling-convention
     *
     * @internal
     * @param $methodName
     * @param XMLParser $xmlParser
     * @param null|resource $parser
     * @return void
     * @throws NoSuchMethodException
     *
     * @todo feature creep - we could validate here that the method in the dispatch map is valid, but that would mean
     *       dirtying a lot the logic, as we would have back to both parseRequest() and execute() methods the info
     *       about the matched method handler, in order to avoid doing the work twice...
     */
    public function methodNameCallback($methodName, $xmlParser, $parser = null)
    {
        $sysCall = $this->isSyscall($methodName);
        $dmap = $sysCall ? $this->getSystemDispatchMap() : $this->dmap;

        if (!isset($dmap[$methodName]['function'])) {
            // No such method
            throw new NoSuchMethodException(PhpXmlRpc::$xmlrpcstr['unknown_method'], PhpXmlRpc::$xmlrpcerr['unknown_method']);
        }

        // alter on-the-fly the config of the xml parser if needed
        if (isset($dmap[$methodName]['parameters_type']) &&
            $dmap[$methodName]['parameters_type'] != $this->functions_parameters_type) {
            /// @todo this should be done by a method of the XMLParser
            switch ($dmap[$methodName]['parameters_type']) {
                case XMLParser::RETURN_PHP:
                    xml_set_element_handler($parser, array($xmlParser, 'xmlrpc_se'), array($xmlParser, 'xmlrpc_ee_fast'));
                    break;
                case XMLParser::RETURN_EPIVALS:
                    xml_set_element_handler($parser, array($xmlParser, 'xmlrpc_se'), array($xmlParser, 'xmlrpc_ee_epi'));
                    break;
                /// @todo log a warning on unsupported return type
                case XMLParser::RETURN_XMLRPCVALS:
                default:
                    xml_set_element_handler($parser, array($xmlParser, 'xmlrpc_se'), array($xmlParser, 'xmlrpc_ee'));
            }
        }
    }

    /**
     * Add a string to the 'internal debug message' (separate from 'user debug message').
     *
     * @param string $string
     * @return void
     */
    protected function debugMsg($string)
    {
        $this->debug_info .= $string . "\n";
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
     * @param array $dmap
     * @return $this
     */
    public function setDispatchMap($dmap)
    {
        $this->dmap = $dmap;
        return $this;
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

    /**
     * @return array[]
     */
    public function getCapabilities()
    {
        $outAr = array(
            // xml-rpc spec: always supported
            'xmlrpc' => array(
                'specUrl' => 'http://www.xmlrpc.com/spec', // NB: the spec sits now at http://xmlrpc.com/spec.md
                'specVersion' => 1
            ),
            // if we support system.xxx functions, we always support multicall, too...
            'system.multicall' => array(
                // Note that, as of 2006/09/17, the following URL does not respond anymore
                'specUrl' => 'http://www.xmlrpc.com/discuss/msgReader$1208',
                'specVersion' => 1
            ),
            // introspection: version 2! we support 'mixed', too.
            // note: the php xml-rpc extension says this instead:
            //   url http://xmlrpc-epi.sourceforge.net/specs/rfc.introspection.php, version 20010516
            'introspection' => array(
                'specUrl' => 'http://phpxmlrpc.sourceforge.net/doc-2/ch10.html',
                'specVersion' => 2,
            ),
        );

        // NIL extension
        if (PhpXmlRpc::$xmlrpc_null_extension) {
            $outAr['nil'] = array(
                // Note that, as of 2023/01, the following URL does not respond anymore
                'specUrl' => 'http://www.ontosys.com/xml-rpc/extensions.php',
                'specVersion' => 1
            );
        }

        // support for "standard" error codes
        if (PhpXmlRpc::$xmlrpcerr['unknown_method'] === Interop::$xmlrpcerr['unknown_method']) {
            $outAr['faults_interop'] = array(
                'specUrl' => 'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php',
                'specVersion' => 20010516
            );
        }

        return $outAr;
    }

    /**
     * @internal handler of a system. method
     *
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_getCapabilities($server, $req = null)
    {
        $encoder = new Encoder();
        return new static::$responseClass($encoder->encode($server->getCapabilities()));
    }

    /**
     * @internal handler of a system. method
     *
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

        return new static::$responseClass(new Value($outAr, 'array'));
    }

    /**
     * @internal handler of a system. method
     *
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_methodSignature($server, $req)
    {
        // let's accept as parameter either an xml-rpc value or string
        if (is_object($req)) {
            $methName = $req->getParam(0);
            $methName = $methName->scalarVal();
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
                $r = new static::$responseClass(new Value($sigs, 'array'));
            } else {
                // NB: according to the official docs, we should be returning a
                // "none-array" here, which means not-an-array
                $r = new static::$responseClass(new Value('undef', 'string'));
            }
        } else {
            $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['introspect_unknown'], PhpXmlRpc::$xmlrpcstr['introspect_unknown']);
        }

        return $r;
    }

    /**
     * @internal handler of a system. method
     *
     * @param Server $server
     * @param Request $req
     * @return Response
     */
    public static function _xmlrpcs_methodHelp($server, $req)
    {
        // let's accept as parameter either an xml-rpc value or string
        if (is_object($req)) {
            $methName = $req->getParam(0);
            $methName = $methName->scalarVal();
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
                $r = new static::$responseClass(new Value($dmap[$methName]['docstring'], 'string'));
            } else {
                $r = new static::$responseClass(new Value('', 'string'));
            }
        } else {
            $r = new static::$responseClass(0, PhpXmlRpc::$xmlrpcerr['introspect_unknown'], PhpXmlRpc::$xmlrpcstr['introspect_unknown']);
        }

        return $r;
    }

    /**
     * @internal this function will become protected in the future
     *
     * @param $err
     * @return Value
     */
    public static function _xmlrpcs_multicall_error($err)
    {
        if (is_string($err)) {
            $str = PhpXmlRpc::$xmlrpcstr["multicall_{$err}"];
            $code = PhpXmlRpc::$xmlrpcerr["multicall_{$err}"];
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
     * @internal this function will become protected in the future
     *
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
        if ($methName->kindOf() != 'scalar' || $methName->scalarTyp() != 'string') {
            return static::_xmlrpcs_multicall_error('notstring');
        }
        if ($methName->scalarVal() == 'system.multicall') {
            return static::_xmlrpcs_multicall_error('recursion');
        }

        $params = @$call['params'];
        if (!$params) {
            return static::_xmlrpcs_multicall_error('noparams');
        }
        if ($params->kindOf() != 'array') {
            return static::_xmlrpcs_multicall_error('notarray');
        }

        $req = new Request($methName->scalarVal());
        foreach ($params as $i => $param) {
            if (!$req->addParam($param)) {
                $i++; // for error message, we count params from 1
                return static::_xmlrpcs_multicall_error(new static::$responseClass(0,
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
     * @internal this function will become protected in the future
     *
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
     * @internal handler of a system. method
     *
     * @param Server $server
     * @param Request|array $req
     * @return Response
     */
    public static function _xmlrpcs_multicall($server, $req)
    {
        $result = array();
        // let's accept a plain list of php parameters, beside a single xml-rpc msg object
        if (is_object($req)) {
            $calls = $req->getParam(0);
            foreach ($calls as $call) {
                $result[] = static::_xmlrpcs_multicall_do_call($server, $call);
            }
        } else {
            $numCalls = count($req);
            for ($i = 0; $i < $numCalls; $i++) {
                $result[$i] = static::_xmlrpcs_multicall_do_call_phpvals($server, $req[$i]);
            }
        }

        return new static::$responseClass(new Value($result, 'array'));
    }

    /**
     * Error handler used to track errors that occur during server-side execution of PHP code.
     * This allows to report back to the client whether an internal error has occurred or not
     * using an xml-rpc response object, instead of letting the client deal with the html junk
     * that a PHP execution error on the server generally entails.
     *
     * NB: in fact a user defined error handler can only handle WARNING, NOTICE and USER_* errors.
     *
     * @internal
     */
    public static function _xmlrpcs_errorHandler($errCode, $errString, $filename = null, $lineNo = null, $context = null)
    {
        // obey the @ protocol
        if (error_reporting() == 0) {
            return;
        }

        // From PHP 8.4 the E_STRICT constant has been deprecated and will emit deprecation notices.
        // PHP core and core extensions since PHP 8.0 and later do not emit E_STRICT notices at all.
        // On PHP 7 series before PHP 7.4, some functions conditionally emit E_STRICT notices.
        if (PHP_VERSION_ID >= 70400) {
            static::error_occurred($errString);
        } elseif ($errCode != E_STRICT) {
                static::error_occurred($errString);
        }

        // Try to avoid as much as possible disruption to the previous error handling mechanism in place
        if (self::$_xmlrpcs_prev_ehandler == '') {
            // The previous error handler was the default: all we should do is log error to the default error log
            // (if level high enough)
            if (ini_get('log_errors') && (intval(ini_get('error_reporting')) & $errCode)) {
                // we can't use the functionality of LoggerAware, because this is a static method
                if (self::$logger === null) {
                    self::$logger = Logger::instance();
                }
                self::$logger->error($errString);
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

    // *** BC layer ***

    /**
     * @param string $charsetEncoding
     * @return string
     *
     * @deprecated this method was moved to the Response class
     */
    protected function xml_header($charsetEncoding = '')
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        if ($charsetEncoding != '') {
            return "<?xml version=\"1.0\" encoding=\"$charsetEncoding\"?" . ">\n";
        } else {
            return "<?xml version=\"1.0\"?" . ">\n";
        }
    }

    // we have to make this return by ref in order to allow calls such as `$resp->_cookies['name'] = ['value' => 'something'];`
    public function &__get($name)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION :
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                $this->logDeprecation('Getting property Request::' . $name . ' is deprecated');
                return $this->$name;
            case 'accepted_charset_encodings':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Server')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Getting property Request::' . $name . ' is deprecated');
                    return $this->accepted_compression;
                } else {
                    trigger_error("Cannot access protected property Server::accepted_charset_encodings in " . __FILE__, E_USER_ERROR);
                }
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
                $result = null;
                return $result;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION :
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                $this->logDeprecation('Setting property Request::' . $name . ' is deprecated');
                $this->$name = $value;
                break;
            case 'accepted_charset_encodings':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Server')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Setting property Request::' . $name . ' is deprecated');
                    $this->accepted_compression = $value;
                } else {
                    trigger_error("Cannot access protected property Server::accepted_charset_encodings in " . __FILE__, E_USER_ERROR);
                }
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __set(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }

    public function __isset($name)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION :
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                $this->logDeprecation('Checking property Request::' . $name . ' is deprecated');
                return isset($this->$name);
            case 'accepted_charset_encodings':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Server')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Checking property Request::' . $name . ' is deprecated');
                    return isset($this->accepted_compression);
                }
                // break through voluntarily
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch ($name) {
            case self::OPT_ACCEPTED_COMPRESSION :
            case self::OPT_ALLOW_SYSTEM_FUNCS:
            case self::OPT_COMPRESS_RESPONSE:
            case self::OPT_DEBUG:
            case self::OPT_EXCEPTION_HANDLING:
            case self::OPT_FUNCTIONS_PARAMETERS_TYPE:
            case self::OPT_PHPVALS_ENCODING_OPTIONS:
            case self::OPT_RESPONSE_CHARSET_ENCODING:
                $this->logDeprecation('Unsetting property Request::' . $name . ' is deprecated');
                unset($this->$name);
                break;
            case 'accepted_charset_encodings':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Server')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Unsetting property Request::' . $name . ' is deprecated');
                    unset($this->accepted_compression);
                } else {
                    trigger_error("Cannot access protected property Server::accepted_charset_encodings in " . __FILE__, E_USER_ERROR);
                }
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
