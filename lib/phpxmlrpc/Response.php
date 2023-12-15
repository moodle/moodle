<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Exception\StateErrorException;
use PhpXmlRpc\Traits\CharsetEncoderAware;
use PhpXmlRpc\Traits\DeprecationLogger;
use PhpXmlRpc\Traits\PayloadBearer;

/**
 * This class provides the representation of the response of an XML-RPC server.
 * Server-side, a server method handler will construct a Response and pass it as its return value.
 * An identical Response object will be returned by the result of an invocation of the send() method of the Client class.
 *
 * @property Value|string|mixed $val deprecated - public access left in purely for BC. Access via value()/__construct()
 * @property string $valtyp deprecated - public access left in purely for BC. Access via valueType()/__construct()
 * @property int $errno deprecated - public access left in purely for BC. Access via faultCode()/__construct()
 * @property string $errstr deprecated - public access left in purely for BC. Access faultString()/__construct()
 * @property string $payload deprecated - public access left in purely for BC. Access via getPayload()/setPayload()
 * @property string $content_type deprecated - public access left in purely for BC. Access via getContentType()/setPayload()
 * @property array $hdrs deprecated. Access via httpResponse()['headers'], set via $httpResponse['headers']
 * @property array _cookies deprecated. Access via httpResponse()['cookies'], set via $httpResponse['cookies']
 * @property string $raw_data deprecated. Access via httpResponse()['raw_data'], set via $httpResponse['raw_data']
 */
class Response
{
    use CharsetEncoderAware;
    use DeprecationLogger;
    use PayloadBearer;

    /** @var Value|string|mixed */
    protected $val = 0;
    /** @var string */
    protected $valtyp;
    /** @var int */
    protected $errno = 0;
    /** @var string */
    protected $errstr = '';

    protected $httpResponse = array('headers' => array(), 'cookies' => array(), 'raw_data' => '', 'status_code' => null);

    /**
     * @param Value|string|mixed $val either a Value object, a php value or the xml serialization of an xml-rpc value (a string).
     *                                Note that using anything other than a Value object wll have an impact on serialization.
     * @param integer $fCode set it to anything but 0 to create an error response. In that case, $val is discarded
     * @param string $fString the error string, in case of an error response
     * @param string $valType The type of $val passed in. Either 'xmlrpcvals', 'phpvals' or 'xml'. Leave empty to let
     *                        the code guess the correct type by looking at $val - in which case strings are assumed
     *                        to be serialized xml
     * @param array|null $httpResponse this should be set when the response is being built out of data received from
     *                                 http (i.e. not when programmatically building a Response server-side). Array
     *                                 keys should include, if known: headers, cookies, raw_data, status_code
     *
     * @todo add check that $val / $fCode / $fString is of correct type? We could at least log a warning for fishy cases...
     *       NB: as of now we do not do it, since it might be either an xml-rpc value or a plain php val, or a complete
     *       xml chunk, depending on usage of Client::send() inside which the constructor is called.
     */
    public function __construct($val, $fCode = 0, $fString = '', $valType = '', $httpResponse = null)
    {
        if ($fCode != 0) {
            // error response
            $this->errno = $fCode;
            $this->errstr = $fString;
        } else {
            // successful response
            $this->val = $val;
            if ($valType == '') {
                // user did not declare type of response value: try to guess it
                if (is_object($this->val) && is_a($this->val, 'PhpXmlRpc\Value')) {
                    $this->valtyp = 'xmlrpcvals';
                } elseif (is_string($this->val)) {
                    $this->valtyp = 'xml';
                } else {
                    $this->valtyp = 'phpvals';
                }
            } else {
                $this->valtyp = $valType;
                // user declares the type of resp value: we "almost" trust it... but log errors just in case
                if (($this->valtyp == 'xmlrpcvals' && (!is_a($this->val, 'PhpXmlRpc\Value'))) ||
                    ($this->valtyp == 'xml' && (!is_string($this->val)))) {
                    $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': value passed in does not match type ' . $valType);
                }
            }
        }

        if (is_array($httpResponse)) {
            $this->httpResponse = array_merge(array('headers' => array(), 'cookies' => array(), 'raw_data' => '', 'status_code' => null), $httpResponse);
        }
    }

    /**
     * Returns the error code of the response.
     *
     * @return integer the error code of this response (0 for not-error responses)
     */
    public function faultCode()
    {
        return $this->errno;
    }

    /**
     * Returns the error code of the response.
     *
     * @return string the error string of this response ('' for not-error responses)
     */
    public function faultString()
    {
        return $this->errstr;
    }

    /**
     * Returns the value received by the server. If the Response's faultCode is non-zero then the value returned by this
     * method should not be used (it may not even be an object).
     *
     * @return Value|string|mixed the Value object returned by the server. Might be an xml string or plain php value
     *                            depending on the convention adopted when creating the Response
     */
    public function value()
    {
        return $this->val;
    }

    /**
     * @return string
     */
    public function valueType()
    {
        return $this->valtyp;
    }

    /**
     * Returns an array with the cookies received from the server.
     * Array has the form: $cookiename => array ('value' => $val, $attr1 => $val1, $attr2 => $val2, ...)
     * with attributes being e.g. 'expires', 'path', domain'.
     * NB: cookies sent as 'expired' by the server (i.e. with an expiry date in the past) are still present in the array.
     * It is up to the user-defined code to decide how to use the received cookies, and whether they have to be sent back
     * with the next request to the server (using $client->setCookie) or not.
     * The values are filled in at constructor time, and might not be set for specific debug values used.
     *
     * @return array[] array of cookies received from the server
     */
    public function cookies()
    {
        return $this->httpResponse['cookies'];
    }

    /**
     * Returns an array with info about the http response received from the server.
     * The values are filled in at constructor time, and might not be set for specific debug values used.
     *
     * @return array array with keys 'headers', 'cookies', 'raw_data' and 'status_code'.
     */
    public function httpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * Returns xml representation of the response, XML prologue _not_ included. Sets `payload` and `content_type` properties
     *
     * @param string $charsetEncoding the charset to be used for serialization. If null, US-ASCII is assumed
     * @return string the xml representation of the response
     * @throws StateErrorException if the response was built out of a value of an unsupported type
     */
    public function serialize($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            $this->content_type = 'text/xml; charset=' . $charsetEncoding;
        } else {
            $this->content_type = 'text/xml';
        }

        if (PhpXmlRpc::$xmlrpc_null_apache_encoding) {
            $result = "<methodResponse xmlns:ex=\"" . PhpXmlRpc::$xmlrpc_null_apache_encoding_ns . "\">\n";
        } else {
            $result = "<methodResponse>\n";
        }
        if ($this->errno) {
            // Let non-ASCII response messages be tolerated by clients by xml-encoding non ascii chars
            $result .= "<fault>\n" .
                "<value>\n<struct><member><name>faultCode</name>\n<value><int>" . $this->errno .
                "</int></value>\n</member>\n<member>\n<name>faultString</name>\n<value><string>" .
                $this->getCharsetEncoder()->encodeEntities($this->errstr, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) .
                "</string></value>\n</member>\n</struct>\n</value>\n</fault>";
        } else {
            if (is_object($this->val) && is_a($this->val, 'PhpXmlRpc\Value')) {
                $result .= "<params>\n<param>\n" . $this->val->serialize($charsetEncoding) . "</param>\n</params>";
            } else if (is_string($this->val) && $this->valtyp == 'xml') {
                $result .= "<params>\n<param>\n" .
                    $this->val .
                    "</param>\n</params>";
            } else if ($this->valtyp == 'phpvals') {
                    $encoder = new Encoder();
                    $val = $encoder->encode($this->val);
                    $result .= "<params>\n<param>\n" . $val->serialize($charsetEncoding) . "</param>\n</params>";
            } else {
                throw new StateErrorException('cannot serialize xmlrpc response objects whose content is native php values');
            }
        }
        $result .= "\n</methodResponse>";

        $this->payload = $result;

        return $result;
    }

    /**
     * @param string $charsetEncoding
     * @return string
     */
    public function xml_header($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            return "<?xml version=\"1.0\" encoding=\"$charsetEncoding\"?" . ">\n";
        } else {
            return "<?xml version=\"1.0\"?" . ">\n";
        }
    }

    // *** BC layer ***

    // we have to make this return by ref in order to allow calls such as `$resp->_cookies['name'] = ['value' => 'something'];`
    public function &__get($name)
    {
        switch ($name) {
            case 'val':
            case 'valtyp':
            case 'errno':
            case 'errstr':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Getting property Response::' . $name . ' is deprecated');
                return $this->$name;
            case 'hdrs':
                $this->logDeprecation('Getting property Response::' . $name . ' is deprecated');
                return $this->httpResponse['headers'];
            case '_cookies':
                $this->logDeprecation('Getting property Response::' . $name . ' is deprecated');
                return $this->httpResponse['cookies'];
            case 'raw_data':
                $this->logDeprecation('Getting property Response::' . $name . ' is deprecated');
                return $this->httpResponse['raw_data'];
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
            case 'val':
            case 'valtyp':
            case 'errno':
            case 'errstr':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Setting property Response::' . $name . ' is deprecated');
                $this->$name = $value;
                break;
            case 'hdrs':
                $this->logDeprecation('Setting property Response::' . $name . ' is deprecated');
                $this->httpResponse['headers'] = $value;
                break;
            case '_cookies':
                $this->logDeprecation('Setting property Response::' . $name . ' is deprecated');
                $this->httpResponse['cookies'] = $value;
                break;
            case 'raw_data':
                $this->logDeprecation('Setting property Response::' . $name . ' is deprecated');
                $this->httpResponse['raw_data'] = $value;
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
            case 'val':
            case 'valtyp':
            case 'errno':
            case 'errstr':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Checking property Response::' . $name . ' is deprecated');
                return isset($this->$name);
            case 'hdrs':
                $this->logDeprecation('Checking property Response::' . $name . ' is deprecated');
                return isset($this->httpResponse['headers']);
            case '_cookies':
                $this->logDeprecation('Checking property Response::' . $name . ' is deprecated');
                return isset($this->httpResponse['cookies']);
            case 'raw_data':
                $this->logDeprecation('Checking property Response::' . $name . ' is deprecated');
                return isset($this->httpResponse['raw_data']);
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch ($name) {
            case 'val':
            case 'valtyp':
            case 'errno':
            case 'errstr':
            case 'payload':
            case 'content_type':
                $this->logDeprecation('Setting property Response::' . $name . ' is deprecated');
                unset($this->$name);
                break;
            case 'hdrs':
                $this->logDeprecation('Unsetting property Response::' . $name . ' is deprecated');
                unset($this->httpResponse['headers']);
                break;
            case '_cookies':
                $this->logDeprecation('Unsetting property Response::' . $name . ' is deprecated');
                unset($this->httpResponse['cookies']);
                break;
            case 'raw_data':
                $this->logDeprecation('Unsetting property Response::' . $name . ' is deprecated');
                unset($this->httpResponse['raw_data']);
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
