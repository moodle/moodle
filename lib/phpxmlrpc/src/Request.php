<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Exception\HttpException;
use PhpXmlRpc\Helper\Http;
use PhpXmlRpc\Helper\XMLParser;
use PhpXmlRpc\Traits\CharsetEncoderAware;
use PhpXmlRpc\Traits\DeprecationLogger;
use PhpXmlRpc\Traits\ParserAware;
use PhpXmlRpc\Traits\PayloadBearer;

/**
 * This class provides the representation of a request to an XML-RPC server.
 * A client sends a PhpXmlrpc\Request to a server, and receives back an PhpXmlrpc\Response.
 *
 * @todo feature creep - add a protected $httpRequest member, in the same way the Response has one
 *
 * @property string $methodname deprecated - public access left in purely for BC. Access via method()/__construct()
 * @property Value[] $params deprecated - public access left in purely for BC. Access via getParam()/__construct()
 * @property int $debug deprecated - public access left in purely for BC. Access via .../setDebug()
 * @property string $payload deprecated - public access left in purely for BC. Access via getPayload()/setPayload()
 * @property string $content_type deprecated - public access left in purely for BC. Access via getContentType()/setPayload()
 */
class Request
{
    use CharsetEncoderAware;
    use DeprecationLogger;
    use ParserAware;
    use PayloadBearer;

    /** @var string */
    protected $methodname;
    /** @var Value[] */
    protected $params = array();
    /** @var int */
    protected $debug = 0;

    /**
     * holds data while parsing the response. NB: Not a full Response object
     * @deprecated will be removed in a future release; still accessible by subclasses for the moment
     */
    private $httpResponse = array();

    /**
     * @param string $methodName the name of the method to invoke
     * @param Value[] $params array of parameters to be passed to the method (NB: Value objects, not plain php values)
     */
    public function __construct($methodName, $params = array())
    {
        $this->methodname = $methodName;
        foreach ($params as $param) {
            $this->addParam($param);
        }
    }

    /**
     * Gets/sets the xml-rpc method to be invoked.
     *
     * @param string $methodName the method to be set (leave empty not to set it)
     * @return string the method that will be invoked
     */
    public function method($methodName = '')
    {
        if ($methodName != '') {
            $this->methodname = $methodName;
        }

        return $this->methodname;
    }

    /**
     * Add a parameter to the list of parameters to be used upon method invocation.
     * Checks that $params is actually a Value object and not a plain php value.
     *
     * @param Value $param
     * @return boolean false on failure
     */
    public function addParam($param)
    {
        // check: do not add to self params which are not xml-rpc values
        if (is_object($param) && is_a($param, 'PhpXmlRpc\Value')) {
            $this->params[] = $param;

            return true;
        } else {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': value passed in must be a PhpXmlRpc\Value');
            return false;
        }
    }

    /**
     * Returns the nth parameter in the request. The index zero-based.
     *
     * @param integer $i the index of the parameter to fetch (zero based)
     * @return Value the i-th parameter
     */
    public function getParam($i)
    {
        return $this->params[$i];
    }

    /**
     * Returns the number of parameters in the message.
     *
     * @return integer the number of parameters currently set
     */
    public function getNumParams()
    {
        return count($this->params);
    }

    /**
     * Returns xml representation of the message, XML prologue included. Sets `payload` and `content_type` properties
     *
     * @param string $charsetEncoding
     * @return string the xml representation of the message, xml prologue included
     */
    public function serialize($charsetEncoding = '')
    {
        $this->createPayload($charsetEncoding);

        return $this->payload;
    }

    /**
     * @internal this function will become protected in the future (and be folded into serialize)
     *
     * @param string $charsetEncoding
     * @return void
     */
    public function createPayload($charsetEncoding = '')
    {
        $this->logDeprecationUnlessCalledBy('serialize');

        if ($charsetEncoding != '') {
            $this->content_type = 'text/xml; charset=' . $charsetEncoding;
        } else {
            $this->content_type = 'text/xml';
        }

        $result = $this->xml_header($charsetEncoding);
        $result .= '<methodName>' . $this->getCharsetEncoder()->encodeEntities(
                $this->methodname, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</methodName>\n";
        $result .= "<params>\n";
        foreach ($this->params as $p) {
            $result .= "<param>\n" . $p->serialize($charsetEncoding) .
                "</param>\n";
        }
        $result .= "</params>\n";
        $result .= $this->xml_footer();

        $this->payload = $result;
    }

    /**
     * @internal this function will become protected in the future (and be folded into serialize)
     *
     * @param string $charsetEncoding
     * @return string
     */
    public function xml_header($charsetEncoding = '')
    {
        $this->logDeprecationUnlessCalledBy('createPayload');

        if ($charsetEncoding != '') {
            return "<?xml version=\"1.0\" encoding=\"$charsetEncoding\" ?" . ">\n<methodCall>\n";
        } else {
            return "<?xml version=\"1.0\"?" . ">\n<methodCall>\n";
        }
    }

    /**
     * @internal this function will become protected in the future (and be folded into serialize)
     *
     * @return string
     */
    public function xml_footer()
    {
        $this->logDeprecationUnlessCalledBy('createPayload');

        return '</methodCall>';
    }

    /**
     * Given an open file handle, read all data available and parse it as an xml-rpc response.
     *
     * NB: the file handle is not closed by this function.
     * NNB: might have trouble in rare cases to work on network streams, as we check for a read of 0 bytes instead of
     *      feof($fp). But since checking for feof(null) returns false, we would risk an infinite loop in that case,
     *      because we cannot trust the caller to give us a valid pointer to an open file...
     *
     * @param resource $fp stream pointer
     * @param bool $headersProcessed
     * @param string $returnType
     * @return Response
     *
     * @todo arsing Responses is not really the responsibility of the Request class. Maybe of the Client...
     * @todo feature creep - add a flag to disable trying to parse the http headers
     */
    public function parseResponseFile($fp, $headersProcessed = false, $returnType = 'xmlrpcvals')
    {
        $ipd = '';
        // q: is there an optimal buffer size? Is there any value in making the buffer size a tuneable?
        while ($data = fread($fp, 32768)) {
            $ipd .= $data;
        }
        return $this->parseResponse($ipd, $headersProcessed, $returnType);
    }

    /**
     * Parse the xml-rpc response contained in the string $data and return a Response object.
     *
     * When $this->debug has been set to a value greater than 0, will echo debug messages to screen while decoding.
     *
     * @param string $data the xml-rpc response, possibly including http headers
     * @param bool $headersProcessed when true prevents parsing HTTP headers for interpretation of content-encoding and
     *                               consequent decoding
     * @param string $returnType decides return type, i.e. content of response->value(). Either 'xmlrpcvals', 'xml' or
     *                           'phpvals'
     * @return Response
     *
     * @todo parsing Responses is not really the responsibility of the Request class. Maybe of the Client...
     * @todo what about only populating 'raw_data' in httpResponse when debug mode is > 0?
     * @todo feature creep - allow parsing data gotten from a stream pointer instead of a string: read it piecewise,
     *       looking first for separation between headers and body, then for charset indicators, server debug info and
     *       </methodResponse>. That would require a notable increase in code complexity...
     */
    public function parseResponse($data = '', $headersProcessed = false, $returnType = XMLParser::RETURN_XMLRPCVALS)
    {
        if ($this->debug > 0) {
            $this->getLogger()->debug("---GOT---\n$data\n---END---");
        }

        $this->httpResponse = array('raw_data' => $data, 'headers' => array(), 'cookies' => array());

        if ($data == '') {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': no response received from server.');
            return new Response(0, PhpXmlRpc::$xmlrpcerr['no_data'], PhpXmlRpc::$xmlrpcstr['no_data']);
        }

        // parse the HTTP headers of the response, if present, and separate them from data
        if (substr($data, 0, 4) == 'HTTP') {
            $httpParser = new Http();
            try {
                $httpResponse = $httpParser->parseResponseHeaders($data, $headersProcessed, $this->debug > 0);
            } catch (HttpException $e) {
                // failed processing of HTTP response headers
                // save into response obj the full payload received, for debugging
                return new Response(0, $e->getCode(), $e->getMessage(), '', array('raw_data' => $data, 'status_code' => $e->statusCode()));
            } catch(\Exception $e) {
                return new Response(0, $e->getCode(), $e->getMessage(), '', array('raw_data' => $data));
            }
        } else {
            $httpResponse = $this->httpResponse;
        }

        // be tolerant of extra whitespace in response body
        $data = trim($data);

        /// @todo optimization creep - return an error msg if $data == ''

        // be tolerant of junk after methodResponse (e.g. javascript ads automatically inserted by free hosts)
        // idea from Luca Mariano, originally in PEARified version of the lib
        $pos = strrpos($data, '</methodResponse>');
        if ($pos !== false) {
            $data = substr($data, 0, $pos + 17);
        }

        // try to 'guestimate' the character encoding of the received response
        $respEncoding = XMLParser::guessEncoding(
            isset($httpResponse['headers']['content-type']) ? $httpResponse['headers']['content-type'] : '',
            $data
        );

        if ($this->debug >= 0) {
            $this->httpResponse = $httpResponse;
        } else {
            $httpResponse = null;
        }

        if ($this->debug > 0) {
            $start = strpos($data, '<!-- SERVER DEBUG INFO (BASE64 ENCODED):');
            if ($start) {
                $start += strlen('<!-- SERVER DEBUG INFO (BASE64 ENCODED):');
                /// @todo what if there is no end tag?
                $end = strpos($data, '-->', $start);
                $comments = substr($data, $start, $end - $start);
                $this->getLogger()->debug("---SERVER DEBUG INFO (DECODED)---\n\t" .
                    str_replace("\n", "\n\t", base64_decode($comments)) . "\n---END---", array('encoding' => $respEncoding));
            }
        }

        // if the user wants back raw xml, give it to her
        if ($returnType == 'xml') {
            return new Response($data, 0, '', 'xml', $httpResponse);
        }

        /// @todo move this block of code into the XMLParser
        if ($respEncoding != '') {
            // Since parsing will fail if charset is not specified in the xml declaration,
            // the encoding is not UTF8 and there are non-ascii chars in the text, we try to work round that...
            // The following code might be better for mb_string enabled installs, but makes the lib about 200% slower...
            //if (!is_valid_charset($respEncoding, array('UTF-8')))
            if (!in_array($respEncoding, array('UTF-8', 'US-ASCII')) && !XMLParser::hasEncoding($data)) {
                if (function_exists('mb_convert_encoding')) {
                    $data = mb_convert_encoding($data, 'UTF-8', $respEncoding);
                } else {
                    if ($respEncoding == 'ISO-8859-1') {
                        $data = utf8_encode($data);
                    } else {
                        $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': unsupported charset encoding of received response: ' . $respEncoding);
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

        $xmlRpcParser = $this->getParser();
        $_xh = $xmlRpcParser->parse($data, $returnType, XMLParser::ACCEPT_RESPONSE, $options);
        // BC
        if (!is_array($_xh)) {
            $_xh = $xmlRpcParser->_xh;
        }

        // first error check: xml not well-formed
        if ($_xh['isf'] == 3) {

            // BC break: in the past for some cases we used the error message: 'XML error at line 1, check URL'

            // Q: should we give back an error with variable error number, as we do server-side? But if we do, will
            //    we be able to tell apart the two cases? In theory, we never emit invalid xml on our end, but
            //    there could be proxies meddling with the request, or network data corruption...

            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['invalid_xml'],
                PhpXmlRpc::$xmlrpcstr['invalid_xml'] . ' ' . $_xh['isf_reason'], '', $httpResponse);

            if ($this->debug > 0) {
                $this->getLogger()->debug($_xh['isf_reason']);
            }
        }
        // second error check: xml well-formed but not xml-rpc compliant
        elseif ($_xh['isf'] == 2) {
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['xml_not_compliant'],
                PhpXmlRpc::$xmlrpcstr['xml_not_compliant'] . ' ' . $_xh['isf_reason'], '', $httpResponse);

            /// @todo echo something for the user? check if it was already done by the parser...
            //if ($this->debug > 0) {
            //    $this->getLogger()->debug($_xh['isf_reason']);
            //}
        }
        // third error check: parsing of the response has somehow gone boink.
        /// @todo shall we omit the 2nd part of this check, since we trust the parsing code?
        ///       Either that, or check the fault results too...
        elseif ($_xh['isf'] > 3 || ($returnType == XMLParser::RETURN_XMLRPCVALS && !$_xh['isf'] && !is_object($_xh['value']))) {
            // something odd has happened and it's time to generate a client side error indicating something odd went on
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['xml_parsing_error'], PhpXmlRpc::$xmlrpcstr['xml_parsing_error'],
                '', $httpResponse
            );

            /// @todo echo something for the user?
        } else {
            if ($this->debug > 1) {
                $this->getLogger()->debug(
                    "---PARSED---\n".var_export($_xh['value'], true)."\n---END---"
                );
            }

            $v = $_xh['value'];

            if ($_xh['isf']) {
                /// @todo we should test (here or preferably in the parser) if server sent an int and a string, and/or
                ///       coerce them into such...
                if ($returnType == XMLParser::RETURN_XMLRPCVALS) {
                    $errNo_v = $v['faultCode'];
                    $errStr_v = $v['faultString'];
                    $errNo = $errNo_v->scalarVal();
                    $errStr = $errStr_v->scalarVal();
                } else {
                    $errNo = $v['faultCode'];
                    $errStr = $v['faultString'];
                }

                if ($errNo == 0) {
                    // FAULT returned, errno needs to reflect that
                    /// @todo feature creep - add this code to PhpXmlRpc::$xmlrpcerr
                    $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': fault response received with faultCode 0 or null. Converted it to -1');
                    /// @todo in Encoder::decodeXML, we use PhpXmlRpc::$xmlrpcerr['invalid_return'] for this case (see
                    ///       also the todo 17 lines above)
                    $errNo = -1;
                }

                $r = new Response(0, $errNo, $errStr, '', $httpResponse);
            } else {
                $r = new Response($v, 0, '', $returnType, $httpResponse);
            }
        }

        return $r;
    }

    /**
     * Kept the old name even if Request class was renamed, for BC.
     *
     * @return string
     */
    public function kindOf()
    {
        return 'msg';
    }

    /**
     * Enables/disables the echoing to screen of the xml-rpc responses received.
     *
     * @param integer $level values <0, 0, 1, >1 are supported
     * @return $this
     */
    public function setDebug($level)
    {
        $this->debug = $level;
        return $this;
    }

    // *** BC layer ***

    // we have to make this return by ref in order to allow calls such as `$resp->_cookies['name'] = ['value' => 'something'];`
    public function &__get($name)
    {
        switch ($name) {
            case 'me':
            case 'mytype':
            case '_php_class':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Getting property Request::' . $name . ' is deprecated');
                return $this->$name;
            case 'httpResponse':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Request')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Getting property Request::' . $name . ' is deprecated');
                    return $this->httpResponse;
                } else {
                    trigger_error("Cannot access protected property Request::httpResponse in " . __FILE__, E_USER_ERROR);
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
            case 'methodname':
            case 'params':
            case 'debug':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Setting property Request::' . $name . ' is deprecated');
                $this->$name = $value;
                break;
            case 'httpResponse':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Request')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Setting property Request::' . $name . ' is deprecated');
                    $this->httpResponse = $value;
                } else {
                    trigger_error("Cannot access protected property Request::httpResponse in " . __FILE__, E_USER_ERROR);
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
            case 'methodname':
            case 'params':
            case 'debug':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Checking property Request::' . $name . ' is deprecated');
                return isset($this->$name);
            case 'httpResponse':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Request')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Checking property Request::' . $name . ' is deprecated');
                    return isset($this->httpResponse);
                }
                // break through voluntarily
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch ($name) {
            case 'methodname':
            case 'params':
            case 'debug':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Unsetting property Request::' . $name . ' is deprecated');
                unset($this->$name);
                break;
            case 'httpResponse':
                // manually implement the 'protected property' behaviour
                $canAccess = false;
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]) && isset($trace[1]['class'])) {
                    if (is_subclass_of($trace[1]['class'], 'PhpXmlRpc\Request')) {
                        $canAccess = true;
                    }
                }
                if ($canAccess) {
                    $this->logDeprecation('Unsetting property Request::' . $name . ' is deprecated');
                    unset($this->httpResponse);
                } else {
                    trigger_error("Cannot access protected property Request::httpResponse in " . __FILE__, E_USER_ERROR);
                }
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
