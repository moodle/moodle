<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\PhpXmlRpc;
use PhpXmlRpc\Value;

/**
 * Deals with parsing the XML.
 * @see http://xmlrpc.com/spec.md
 *
 * @todo implement an interface to allow for alternative implementations
 *       - make access to $_xh protected, return more high-level data structures
 *       - add parseRequest, parseResponse, parseValue methods
 * @todo if iconv() or mb_string() are available, we could allow to convert the received xml to a custom charset encoding
 *       while parsing, which is faster than doing it later by going over the rebuilt data structure
 */
class XMLParser
{
    const RETURN_XMLRPCVALS = 'xmlrpcvals';
    const RETURN_EPIVALS = 'epivals';
    const RETURN_PHP = 'phpvals';

    const ACCEPT_REQUEST = 1;
    const ACCEPT_RESPONSE = 2;
    const ACCEPT_VALUE = 4;
    const ACCEPT_FAULT = 8;

    // Used to store state during parsing and to pass parsing results to callers.
    // Quick explanation of components:
    //  private:
    //    ac - used to accumulate values
    //    stack - array with genealogy of xml elements names used to validate nesting of xmlrpc elements
    //    valuestack - array used for parsing arrays and structs
    //    lv - used to indicate "looking for a value": implements the logic to allow values with no types to be strings
    //  public:
    //    isf - used to indicate an xml parsing fault (3), invalid xmlrpc fault (2) or xmlrpc response fault (1)
    //    isf_reason - used for storing xmlrpc response fault string
    //    value - used to store the value in responses
    //    method - used to store method name in requests
    //    params - used to store parameters in requests
    //    pt - used to store the type of each received parameter. Useful if parameters are automatically decoded to php values
    //    rt  - 'methodcall', 'methodresponse', 'value' or 'fault' (the last one used only in EPI emulation mode)
    public $_xh = array(
        'ac' => '',
        'stack' => array(),
        'valuestack' => array(),
        'isf' => 0,
        'isf_reason' => '',
        'value' => null,
        'method' => false,
        'params' => array(),
        'pt' => array(),
        'rt' => '',
    );

    public $xmlrpc_valid_parents = array(
        'VALUE' => array('MEMBER', 'DATA', 'PARAM', 'FAULT'),
        'BOOLEAN' => array('VALUE'),
        'I4' => array('VALUE'),
        'I8' => array('VALUE'),
        'EX:I8' => array('VALUE'),
        'INT' => array('VALUE'),
        'STRING' => array('VALUE'),
        'DOUBLE' => array('VALUE'),
        'DATETIME.ISO8601' => array('VALUE'),
        'BASE64' => array('VALUE'),
        'MEMBER' => array('STRUCT'),
        'NAME' => array('MEMBER'),
        'DATA' => array('ARRAY'),
        'ARRAY' => array('VALUE'),
        'STRUCT' => array('VALUE'),
        'PARAM' => array('PARAMS'),
        'METHODNAME' => array('METHODCALL'),
        'PARAMS' => array('METHODCALL', 'METHODRESPONSE'),
        'FAULT' => array('METHODRESPONSE'),
        'NIL' => array('VALUE'), // only used when extension activated
        'EX:NIL' => array('VALUE'), // only used when extension activated
    );

    /** @var array $parsing_options */
    protected $parsing_options = array();
    /** @var int $accept self::ACCEPT_REQUEST | self::ACCEPT_RESPONSE by default */
    protected $accept = 3;
    /** @var int $maxChunkLength 4 MB by default. Any value below 10MB should be good */
    protected $maxChunkLength = 4194304;

    /**
     * @param array $options passed to the xml parser
     */
    public function __construct(array $options = array())
    {
        $this->parsing_options = $options;
    }

    /**
     * @param string $data
     * @param string $returnType
     * @param int $accept a bit-combination of self::ACCEPT_REQUEST, self::ACCEPT_RESPONSE, self::ACCEPT_VALUE
     * @param array $options
     */
    public function parse($data, $returnType = self::RETURN_XMLRPCVALS, $accept = 3, $options = array())
    {
        $this->_xh = array(
            'ac' => '',
            'stack' => array(),
            'valuestack' => array(),
            'isf' => 0,
            'isf_reason' => '',
            'value' => null,
            'method' => false, // so we can check later if we got a methodname or not
            'params' => array(),
            'pt' => array(),
            'rt' => '',
        );

        $len = strlen($data);

        // we test for empty documents here to save on resource allocation and simply the chunked-parsing loop below
        if ($len == 0) {
            $this->_xh['isf'] = 3;
            $this->_xh['isf_reason'] = 'XML error 5: empty document';
            return;
        }

        $parser = xml_parser_create();

        foreach ($this->parsing_options as $key => $val) {
            xml_parser_set_option($parser, $key, $val);
        }
        foreach ($options as $key => $val) {
            xml_parser_set_option($parser, $key, $val);
        }
        // always set this, in case someone tries to disable it via options...
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);

        xml_set_object($parser, $this);

        switch($returnType) {
            case self::RETURN_PHP:
                xml_set_element_handler($parser, 'xmlrpc_se', 'xmlrpc_ee_fast');
                break;
            case self::RETURN_EPIVALS:
                xml_set_element_handler($parser, 'xmlrpc_se', 'xmlrpc_ee_epi');
                break;
            default:
                xml_set_element_handler($parser, 'xmlrpc_se', 'xmlrpc_ee');
        }

        xml_set_character_data_handler($parser, 'xmlrpc_cd');
        xml_set_default_handler($parser, 'xmlrpc_dh');

        $this->accept = $accept;

        // @see ticket #70 - we have to parse big xml docks in chunks to avoid errors
        for ($offset = 0; $offset < $len; $offset += $this->maxChunkLength) {
            $chunk = substr($data, $offset, $this->maxChunkLength);
            // error handling: xml not well formed
            if (!xml_parse($parser, $chunk, $offset + $this->maxChunkLength >= $len)) {
                $errCode = xml_get_error_code($parser);
                $errStr = sprintf('XML error %s: %s at line %d, column %d', $errCode, xml_error_string($errCode),
                    xml_get_current_line_number($parser), xml_get_current_column_number($parser));

                $this->_xh['isf'] = 3;
                $this->_xh['isf_reason'] = $errStr;
                break;
            }
        }

        xml_parser_free($parser);
    }

    /**
     * xml parser handler function for opening element tags.
     * @internal
     * @param resource $parser
     * @param string $name
     * @param $attrs
     * @param bool $acceptSingleVals DEPRECATED use the $accept parameter instead
     */
    public function xmlrpc_se($parser, $name, $attrs, $acceptSingleVals = false)
    {
        // if invalid xmlrpc already detected, skip all processing
        if ($this->_xh['isf'] < 2) {

            // check for correct element nesting
            if (count($this->_xh['stack']) == 0) {
                // top level element can only be of 2 types
                /// @todo optimization creep: save this check into a bool variable, instead of using count() every time:
                ///       there is only a single top level element in xml anyway
                // BC
                if ($acceptSingleVals === false) {
                    $accept = $this->accept;
                } else {
                    $accept = self::ACCEPT_REQUEST | self::ACCEPT_RESPONSE | self::ACCEPT_VALUE;
                }
                if (($name == 'METHODCALL' && ($accept & self::ACCEPT_REQUEST)) ||
                    ($name == 'METHODRESPONSE' && ($accept & self::ACCEPT_RESPONSE)) ||
                    ($name == 'VALUE' && ($accept & self::ACCEPT_VALUE)) ||
                    ($name == 'FAULT' && ($accept & self::ACCEPT_FAULT))) {
                    $this->_xh['rt'] = strtolower($name);
                } else {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = 'missing top level xmlrpc element. Found: ' . $name;

                    return;
                }
            } else {
                // not top level element: see if parent is OK
                $parent = end($this->_xh['stack']);
                if (!array_key_exists($name, $this->xmlrpc_valid_parents) || !in_array($parent, $this->xmlrpc_valid_parents[$name])) {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "xmlrpc element $name cannot be child of $parent";

                    return;
                }
            }

            switch ($name) {
                // optimize for speed switch cases: most common cases first
                case 'VALUE':
                    /// @todo we could check for 2 VALUE elements inside a MEMBER or PARAM element
                    $this->_xh['vt'] = 'value'; // indicator: no value found yet
                    $this->_xh['ac'] = '';
                    $this->_xh['lv'] = 1;
                    $this->_xh['php_class'] = null;
                    break;
                case 'I8':
                case 'EX:I8':
                    if (PHP_INT_SIZE === 4) {
                        // INVALID ELEMENT: RAISE ISF so that it is later recognized!!!
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = "Received i8 element but php is compiled in 32 bit mode";

                        return;
                    }
                    // fall through voluntarily
                case 'I4':
                case 'INT':
                case 'STRING':
                case 'BOOLEAN':
                case 'DOUBLE':
                case 'DATETIME.ISO8601':
                case 'BASE64':
                    if ($this->_xh['vt'] != 'value') {
                        // two data elements inside a value: an error occurred!
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = "$name element following a {$this->_xh['vt']} element inside a single value";

                        return;
                    }
                    $this->_xh['ac'] = ''; // reset the accumulator
                    break;
                case 'STRUCT':
                case 'ARRAY':
                    if ($this->_xh['vt'] != 'value') {
                        // two data elements inside a value: an error occurred!
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = "$name element following a {$this->_xh['vt']} element inside a single value";

                        return;
                    }
                    // create an empty array to hold child values, and push it onto appropriate stack
                    $curVal = array();
                    $curVal['values'] = array();
                    $curVal['type'] = $name;
                    // check for out-of-band information to rebuild php objs
                    // and in case it is found, save it
                    if (@isset($attrs['PHP_CLASS'])) {
                        $curVal['php_class'] = $attrs['PHP_CLASS'];
                    }
                    $this->_xh['valuestack'][] = $curVal;
                    $this->_xh['vt'] = 'data'; // be prepared for a data element next
                    break;
                case 'DATA':
                    if ($this->_xh['vt'] != 'data') {
                        // two data elements inside a value: an error occurred!
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = "found two data elements inside an array element";

                        return;
                    }
                case 'METHODCALL':
                case 'METHODRESPONSE':
                case 'PARAMS':
                    // valid elements that add little to processing
                    break;
                case 'METHODNAME':
                case 'NAME':
                    /// @todo we could check for 2 NAME elements inside a MEMBER element
                    $this->_xh['ac'] = '';
                    break;
                case 'FAULT':
                    $this->_xh['isf'] = 1;
                    break;
                case 'MEMBER':
                    // set member name to null, in case we do not find in the xml later on
                    $this->_xh['valuestack'][count($this->_xh['valuestack']) - 1]['name'] = '';
                    //$this->_xh['ac']='';
                // Drop trough intentionally
                case 'PARAM':
                    // clear value type, so we can check later if no value has been passed for this param/member
                    $this->_xh['vt'] = null;
                    break;
                case 'NIL':
                case 'EX:NIL':
                    if (PhpXmlRpc::$xmlrpc_null_extension) {
                        if ($this->_xh['vt'] != 'value') {
                            // two data elements inside a value: an error occurred!
                            $this->_xh['isf'] = 2;
                            $this->_xh['isf_reason'] = "$name element following a {$this->_xh['vt']} element inside a single value";

                            return;
                        }
                        $this->_xh['ac'] = ''; // reset the accumulator
                        break;
                    }
                // if here, we do not support the <NIL/> extension, so
                // drop through intentionally
                default:
                    // INVALID ELEMENT: RAISE ISF so that it is later recognized!!!
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "found not-xmlrpc xml element $name";
                    break;
            }

            // Save current element name to stack, to validate nesting
            $this->_xh['stack'][] = $name;

            /// @todo optimization creep: move this inside the big switch() above
            if ($name != 'VALUE') {
                $this->_xh['lv'] = 0;
            }
        }
    }

    /**
     * xml parser handler function for opening element tags.
     * Used in decoding xml chunks that might represent single xmlrpc values as well as requests, responses.
     * @deprecated
     * @param resource $parser
     * @param $name
     * @param $attrs
     */
    public function xmlrpc_se_any($parser, $name, $attrs)
    {
        $this->xmlrpc_se($parser, $name, $attrs, true);
    }

    /**
     * xml parser handler function for close element tags.
     * @internal
     * @param resource $parser
     * @param string $name
     * @param int $rebuildXmlrpcvals >1 for rebuilding xmlrpcvals, 0 for rebuilding php values, -1 for xmlrpc-extension compatibility
     */
    public function xmlrpc_ee($parser, $name, $rebuildXmlrpcvals = 1)
    {
        if ($this->_xh['isf'] < 2) {
            // push this element name from stack
            // NB: if XML validates, correct opening/closing is guaranteed and
            // we do not have to check for $name == $currElem.
            // we also checked for proper nesting at start of elements...
            $currElem = array_pop($this->_xh['stack']);

            switch ($name) {
                case 'VALUE':
                    // This if() detects if no scalar was inside <VALUE></VALUE>
                    if ($this->_xh['vt'] == 'value') {
                        $this->_xh['value'] = $this->_xh['ac'];
                        $this->_xh['vt'] = Value::$xmlrpcString;
                    }

                    if ($rebuildXmlrpcvals > 0) {
                        // build the xmlrpc val out of the data received, and substitute it
                        $temp = new Value($this->_xh['value'], $this->_xh['vt']);
                        // in case we got info about underlying php class, save it
                        // in the object we're rebuilding
                        if (isset($this->_xh['php_class'])) {
                            $temp->_php_class = $this->_xh['php_class'];
                        }
                        $this->_xh['value'] = $temp;
                    } elseif ($rebuildXmlrpcvals < 0) {
                        if ($this->_xh['vt'] == Value::$xmlrpcDateTime) {
                            $this->_xh['value'] = (object)array(
                                'xmlrpc_type' => 'datetime',
                                'scalar' => $this->_xh['value'],
                                'timestamp' => \PhpXmlRpc\Helper\Date::iso8601Decode($this->_xh['value'])
                            );
                        } elseif ($this->_xh['vt'] == Value::$xmlrpcBase64) {
                            $this->_xh['value'] = (object)array(
                                'xmlrpc_type' => 'base64',
                                'scalar' => $this->_xh['value']
                            );
                        }
                    } else {
                        /// @todo this should handle php-serialized objects,
                        /// since std deserializing is done by php_xmlrpc_decode,
                        /// which we will not be calling...
                        //if (isset($this->_xh['php_class'])) {
                        //}
                    }

                    // check if we are inside an array or struct:
                    // if value just built is inside an array, let's move it into array on the stack
                    $vscount = count($this->_xh['valuestack']);
                    if ($vscount && $this->_xh['valuestack'][$vscount - 1]['type'] == 'ARRAY') {
                        $this->_xh['valuestack'][$vscount - 1]['values'][] = $this->_xh['value'];
                    }
                    break;
                case 'BOOLEAN':
                case 'I4':
                case 'I8':
                case 'EX:I8':
                case 'INT':
                case 'STRING':
                case 'DOUBLE':
                case 'DATETIME.ISO8601':
                case 'BASE64':
                    $this->_xh['vt'] = strtolower($name);
                    /// @todo: optimization creep - remove the if/elseif cycle below
                    /// since the case() in which we are already did that
                    if ($name == 'STRING') {
                        $this->_xh['value'] = $this->_xh['ac'];
                    } elseif ($name == 'DATETIME.ISO8601') {
                        if (!preg_match('/^[0-9]{8}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $this->_xh['ac'])) {
                            Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': invalid value received in DATETIME: ' . $this->_xh['ac']);
                        }
                        $this->_xh['vt'] = Value::$xmlrpcDateTime;
                        $this->_xh['value'] = $this->_xh['ac'];
                    } elseif ($name == 'BASE64') {
                        /// @todo check for failure of base64 decoding / catch warnings
                        $this->_xh['value'] = base64_decode($this->_xh['ac']);
                    } elseif ($name == 'BOOLEAN') {
                        // special case here: we translate boolean 1 or 0 into PHP
                        // constants true or false.
                        // Strings 'true' and 'false' are accepted, even though the
                        // spec never mentions them (see eg. Blogger api docs)
                        // NB: this simple checks helps a lot sanitizing input, ie no
                        // security problems around here
                        if ($this->_xh['ac'] == '1' || strcasecmp($this->_xh['ac'], 'true') == 0) {
                            $this->_xh['value'] = true;
                        } else {
                            // log if receiving something strange, even though we set the value to false anyway
                            if ($this->_xh['ac'] != '0' && strcasecmp($this->_xh['ac'], 'false') != 0) {
                                Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': invalid value received in BOOLEAN: ' . $this->_xh['ac']);
                            }
                            $this->_xh['value'] = false;
                        }
                    } elseif ($name == 'DOUBLE') {
                        // we have a DOUBLE
                        // we must check that only 0123456789-.<space> are characters here
                        // NOTE: regexp could be much stricter than this...
                        if (!preg_match('/^[+-eE0123456789 \t.]+$/', $this->_xh['ac'])) {
                            /// @todo: find a better way of throwing an error than this!
                            Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': non numeric value received in DOUBLE: ' . $this->_xh['ac']);
                            $this->_xh['value'] = 'ERROR_NON_NUMERIC_FOUND';
                        } else {
                            // it's ok, add it on
                            $this->_xh['value'] = (double)$this->_xh['ac'];
                        }
                    } else {
                        // we have an I4/I8/INT
                        // we must check that only 0123456789-<space> are characters here
                        if (!preg_match('/^[+-]?[0123456789 \t]+$/', $this->_xh['ac'])) {
                            /// @todo find a better way of throwing an error than this!
                            Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': non numeric value received in INT: ' . $this->_xh['ac']);
                            $this->_xh['value'] = 'ERROR_NON_NUMERIC_FOUND';
                        } else {
                            // it's ok, add it on
                            $this->_xh['value'] = (int)$this->_xh['ac'];
                        }
                    }
                    $this->_xh['lv'] = 3; // indicate we've found a value
                    break;
                case 'NAME':
                    $this->_xh['valuestack'][count($this->_xh['valuestack']) - 1]['name'] = $this->_xh['ac'];
                    break;
                case 'MEMBER':
                    // add to array in the stack the last element built,
                    // unless no VALUE was found
                    if ($this->_xh['vt']) {
                        $vscount = count($this->_xh['valuestack']);
                        $this->_xh['valuestack'][$vscount - 1]['values'][$this->_xh['valuestack'][$vscount - 1]['name']] = $this->_xh['value'];
                    } else {
                        Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': missing VALUE inside STRUCT in received xml');
                    }
                    break;
                case 'DATA':
                    $this->_xh['vt'] = null; // reset this to check for 2 data elements in a row - even if they're empty
                    break;
                case 'STRUCT':
                case 'ARRAY':
                    // fetch out of stack array of values, and promote it to current value
                    $currVal = array_pop($this->_xh['valuestack']);
                    $this->_xh['value'] = $currVal['values'];
                    $this->_xh['vt'] = strtolower($name);
                    if (isset($currVal['php_class'])) {
                        $this->_xh['php_class'] = $currVal['php_class'];
                    }
                    break;
                case 'PARAM':
                    // add to array of params the current value,
                    // unless no VALUE was found
                    if ($this->_xh['vt']) {
                        $this->_xh['params'][] = $this->_xh['value'];
                        $this->_xh['pt'][] = $this->_xh['vt'];
                    } else {
                        Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ': missing VALUE inside PARAM in received xml');
                    }
                    break;
                case 'METHODNAME':
                    $this->_xh['method'] = preg_replace('/^[\n\r\t ]+/', '', $this->_xh['ac']);
                    break;
                case 'NIL':
                case 'EX:NIL':
                    if (PhpXmlRpc::$xmlrpc_null_extension) {
                        $this->_xh['vt'] = 'null';
                        $this->_xh['value'] = null;
                        $this->_xh['lv'] = 3;
                        break;
                    }
                // drop through intentionally if nil extension not enabled
                case 'PARAMS':
                case 'FAULT':
                case 'METHODCALL':
                case 'METHORESPONSE':
                    break;
                default:
                    // End of INVALID ELEMENT!
                    // shall we add an assert here for unreachable code???
                    break;
            }
        }
    }

    /**
     * Used in decoding xmlrpc requests/responses without rebuilding xmlrpc Values.
     * @internal
     * @param resource $parser
     * @param string $name
     */
    public function xmlrpc_ee_fast($parser, $name)
    {
        $this->xmlrpc_ee($parser, $name, 0);
    }

    /**
     * Used in decoding xmlrpc requests/responses while building xmlrpc-extension Values (plain php for all but base64 and datetime).
     * @internal
     * @param resource $parser
     * @param string $name
     */
    public function xmlrpc_ee_epi($parser, $name)
    {
        $this->xmlrpc_ee($parser, $name, -1);
    }

    /**
     * xml parser handler function for character data.
     * @internal
     * @param resource $parser
     * @param string $data
     */
    public function xmlrpc_cd($parser, $data)
    {
        // skip processing if xml fault already detected
        if ($this->_xh['isf'] < 2) {
            // "lookforvalue==3" means that we've found an entire value
            // and should discard any further character data
            if ($this->_xh['lv'] != 3) {
                $this->_xh['ac'] .= $data;
            }
        }
    }

    /**
     * xml parser handler function for 'other stuff', ie. not char data or
     * element start/end tag. In fact it only gets called on unknown entities...
     * @internal
     * @param $parser
     * @param string data
     */
    public function xmlrpc_dh($parser, $data)
    {
        // skip processing if xml fault already detected
        if ($this->_xh['isf'] < 2) {
            if (substr($data, 0, 1) == '&' && substr($data, -1, 1) == ';') {
                $this->_xh['ac'] .= $data;
            }
        }

        //return true;
    }

    /**
     * xml charset encoding guessing helper function.
     * Tries to determine the charset encoding of an XML chunk received over HTTP.
     * NB: according to the spec (RFC 3023), if text/xml content-type is received over HTTP without a content-type,
     * we SHOULD assume it is strictly US-ASCII. But we try to be more tolerant of non conforming (legacy?) clients/servers,
     * which will be most probably using UTF-8 anyway...
     * In order of importance checks:
     * 1. http headers
     * 2. BOM
     * 3. XML declaration
     * 4. guesses using mb_detect_encoding()
     *
     * @param string $httpHeader the http Content-type header
     * @param string $xmlChunk xml content buffer
     * @param string $encodingPrefs comma separated list of character encodings to be used as default (when mb extension is enabled).
     *                              This can also be set globally using PhpXmlRpc::$xmlrpc_detectencodings
     * @return string the encoding determined. Null if it can't be determined and mbstring is enabled,
     *                PhpXmlRpc::$xmlrpc_defencoding if it can't be determined and mbstring is not enabled
     *
     * @todo explore usage of mb_http_input(): does it detect http headers + post data? if so, use it instead of hand-detection!!!
     */
    public static function guessEncoding($httpHeader = '', $xmlChunk = '', $encodingPrefs = null)
    {
        // discussion: see http://www.yale.edu/pclt/encoding/
        // 1 - test if encoding is specified in HTTP HEADERS

        // Details:
        // LWS:           (\13\10)?( |\t)+
        // token:         (any char but excluded stuff)+
        // quoted string: " (any char but double quotes and control chars)* "
        // header:        Content-type = ...; charset=value(; ...)*
        //   where value is of type token, no LWS allowed between 'charset' and value
        // Note: we do not check for invalid chars in VALUE:
        //   this had better be done using pure ereg as below
        // Note 2: we might be removing whitespace/tabs that ought to be left in if
        //   the received charset is a quoted string. But nobody uses such charset names...

        /// @todo this test will pass if ANY header has charset specification, not only Content-Type. Fix it?
        $matches = array();
        if (preg_match('/;\s*charset\s*=([^;]+)/i', $httpHeader, $matches)) {
            return strtoupper(trim($matches[1], " \t\""));
        }

        // 2 - scan the first bytes of the data for a UTF-16 (or other) BOM pattern
        //     (source: http://www.w3.org/TR/2000/REC-xml-20001006)
        //     NOTE: actually, according to the spec, even if we find the BOM and determine
        //     an encoding, we should check if there is an encoding specified
        //     in the xml declaration, and verify if they match.
        /// @todo implement check as described above?
        /// @todo implement check for first bytes of string even without a BOM? (It sure looks harder than for cases WITH a BOM)
        if (preg_match('/^(\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\x00\x00\xFF\xFE|\xFE\xFF\x00\x00)/', $xmlChunk)) {
            return 'UCS-4';
        } elseif (preg_match('/^(\xFE\xFF|\xFF\xFE)/', $xmlChunk)) {
            return 'UTF-16';
        } elseif (preg_match('/^(\xEF\xBB\xBF)/', $xmlChunk)) {
            return 'UTF-8';
        }

        // 3 - test if encoding is specified in the xml declaration
        // Details:
        // SPACE:         (#x20 | #x9 | #xD | #xA)+ === [ \x9\xD\xA]+
        // EQ:            SPACE?=SPACE? === [ \x9\xD\xA]*=[ \x9\xD\xA]*
        if (preg_match('/^<\?xml\s+version\s*=\s*' . "((?:\"[a-zA-Z0-9_.:-]+\")|(?:'[a-zA-Z0-9_.:-]+'))" .
            '\s+encoding\s*=\s*' . "((?:\"[A-Za-z][A-Za-z0-9._-]*\")|(?:'[A-Za-z][A-Za-z0-9._-]*'))/",
            $xmlChunk, $matches)) {
            return strtoupper(substr($matches[2], 1, -1));
        }

        // 4 - if mbstring is available, let it do the guesswork
        if (extension_loaded('mbstring')) {
            if ($encodingPrefs == null && PhpXmlRpc::$xmlrpc_detectencodings != null) {
                $encodingPrefs = PhpXmlRpc::$xmlrpc_detectencodings;
            }
            if ($encodingPrefs) {
                $enc = mb_detect_encoding($xmlChunk, $encodingPrefs);
            } else {
                $enc = mb_detect_encoding($xmlChunk);
            }
            // NB: mb_detect likes to call it ascii, xml parser likes to call it US_ASCII...
            // IANA also likes better US-ASCII, so go with it
            if ($enc == 'ASCII') {
                $enc = 'US-' . $enc;
            }

            return $enc;
        } else {
            // no encoding specified: as per HTTP1.1 assume it is iso-8859-1?
            // Both RFC 2616 (HTTP 1.1) and 1945 (HTTP 1.0) clearly state that for text/xxx content types
            // this should be the standard. And we should be getting text/xml as request and response.
            // BUT we have to be backward compatible with the lib, which always used UTF-8 as default...
            return PhpXmlRpc::$xmlrpc_defencoding;
        }
    }

    /**
     * Helper function: checks if an xml chunk as a charset declaration (BOM or in the xml declaration)
     *
     * @param string $xmlChunk
     * @return bool
     */
    public static function hasEncoding($xmlChunk)
    {
        // scan the first bytes of the data for a UTF-16 (or other) BOM pattern
        //     (source: http://www.w3.org/TR/2000/REC-xml-20001006)
        if (preg_match('/^(\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\x00\x00\xFF\xFE|\xFE\xFF\x00\x00)/', $xmlChunk)) {
            return true;
        } elseif (preg_match('/^(\xFE\xFF|\xFF\xFE)/', $xmlChunk)) {
            return true;
        } elseif (preg_match('/^(\xEF\xBB\xBF)/', $xmlChunk)) {
            return true;
        }

        // test if encoding is specified in the xml declaration
        // Details:
        // SPACE:         (#x20 | #x9 | #xD | #xA)+ === [ \x9\xD\xA]+
        // EQ:            SPACE?=SPACE? === [ \x9\xD\xA]*=[ \x9\xD\xA]*
        if (preg_match('/^<\?xml\s+version\s*=\s*' . "((?:\"[a-zA-Z0-9_.:-]+\")|(?:'[a-zA-Z0-9_.:-]+'))" .
            '\s+encoding\s*=\s*' . "((?:\"[A-Za-z][A-Za-z0-9._-]*\")|(?:'[A-Za-z][A-Za-z0-9._-]*'))/",
            $xmlChunk, $matches)) {
            return true;
        }

        return false;
    }
}
