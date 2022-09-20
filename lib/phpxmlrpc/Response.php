<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;

/**
 * This class provides the representation of the response of an XML-RPC server.
 * Server-side, a server method handler will construct a Response and pass it as its return value.
 * An identical Response object will be returned by the result of an invocation of the send() method of the Client class.
 *
 * @property array $hdrs deprecated, use $httpResponse['headers']
 * @property array _cookies deprecated, use $httpResponse['cookies']
 * @property string $raw_data deprecated, use $httpResponse['raw_data']
 */
class Response
{
    protected static $charsetEncoder;

    /// @todo: do these need to be public?
    /** @internal */
    public $val = 0;
    /** @internal */
    public $valtyp;
    /** @internal */
    public $errno = 0;
    /** @internal */
    public $errstr = '';
    public $payload;
    public $content_type = 'text/xml';
    protected $httpResponse = array('headers' => array(), 'cookies' => array(), 'raw_data' => '', 'status_code' => null);

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
     * @param Value|string|mixed $val either a Value object, a php value or the xml serialization of an xmlrpc value (a string)
     * @param integer $fCode set it to anything but 0 to create an error response. In that case, $val is discarded
     * @param string $fString the error string, in case of an error response
     * @param string $valType The type of $val passed in. Either 'xmlrpcvals', 'phpvals' or 'xml'. Leave empty to let
     *                        the code guess the correct type.
     * @param array|null $httpResponse
     *
     * @todo add check that $val / $fCode / $fString is of correct type???
     *       NB: as of now we do not do it, since it might be either an xmlrpc value or a plain php val, or a complete
     *       xml chunk, depending on usage of Client::send() inside which creator is called...
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
                // user declares type of resp value: believe him
                $this->valtyp = $valType;
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
     * Returns an array with the cookies received from the server.
     * Array has the form: $cookiename => array ('value' => $val, $attr1 => $val1, $attr2 => $val2, ...)
     * with attributes being e.g. 'expires', 'path', domain'.
     * NB: cookies sent as 'expired' by the server (i.e. with an expiry date in the past) are still present in the array.
     * It is up to the user-defined code to decide how to use the received cookies, and whether they have to be sent back
     * with the next request to the server (using Client::setCookie) or not.
     *
     * @return array[] array of cookies received from the server
     */
    public function cookies()
    {
        return $this->httpResponse['cookies'];
    }

    /**
     * @return array array with keys 'headers', 'cookies', 'raw_data' and 'status_code'
     */
    public function httpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * Returns xml representation of the response. XML prologue not included.
     *
     * @param string $charsetEncoding the charset to be used for serialization. If null, US-ASCII is assumed
     *
     * @return string the xml representation of the response
     *
     * @throws \Exception
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
                Charset::instance()->encodeEntities($this->errstr, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</string></value>\n</member>\n" .
                "</struct>\n</value>\n</fault>";
        } else {
            if (!is_object($this->val) || !is_a($this->val, 'PhpXmlRpc\Value')) {
                if (is_string($this->val) && $this->valtyp == 'xml') {
                    $result .= "<params>\n<param>\n" .
                        $this->val .
                        "</param>\n</params>";
                } else {
                    /// @todo try to build something serializable using the Encoder...
                    throw new \Exception('cannot serialize xmlrpc response objects whose content is native php values');
                }
            } else {
                $result .= "<params>\n<param>\n" .
                    $this->val->serialize($charsetEncoding) .
                    "</param>\n</params>";
            }
        }
        $result .= "\n</methodResponse>";
        $this->payload = $result;

        return $result;
    }

    // BC layer

    public function __get($name)
    {
        //trigger_error('getting property Response::' . $name . ' is deprecated', E_USER_DEPRECATED);

        switch($name) {
            case 'hdrs':
                return $this->httpResponse['headers'];
            case '_cookies':
                return $this->httpResponse['cookies'];
            case 'raw_data':
                return $this->httpResponse['raw_data'];
            default:
                $trace = debug_backtrace();
                trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
                return null;
        }
    }

    public function __set($name, $value)
    {
        //trigger_error('setting property Response::' . $name . ' is deprecated', E_USER_DEPRECATED);

        switch($name) {
            case 'hdrs':
                $this->httpResponse['headers'] = $value;
                break;
            case '_cookies':
                $this->httpResponse['cookies'] = $value;
                break;
            case 'raw_data':
                $this->httpResponse['raw_data'] = $value;
                break;
            default:
                $trace = debug_backtrace();
                trigger_error('Undefined property via __set(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }

    public function __isset($name)
    {
        switch($name) {
            case 'hdrs':
                return isset($this->httpResponse['headers']);
            case '_cookies':
                return isset($this->httpResponse['cookies']);
            case 'raw_data':
                return isset($this->httpResponse['raw_data']);
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch($name) {
            case 'hdrs':
                unset($this->httpResponse['headers']);
                break;
            case '_cookies':
                unset($this->httpResponse['cookies']);
                break;
            case 'raw_data':
                unset($this->httpResponse['raw_data']);
                break;
            default:
                $trace = debug_backtrace();
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
