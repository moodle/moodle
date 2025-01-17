<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\PhpXmlRpc;
use PhpXmlRpc\Traits\DeprecationLogger;
use PhpXmlRpc\Value;

/**
 * Deals with parsing the XML.
 * @see http://xmlrpc.com/spec.md
 *
 * @todo implement an interface to allow for alternative implementations
 *       - make access to $_xh protected, return more high-level data structures
 *       - move the private parts of $_xh to the internal-use parsing-options config
 *       - add parseRequest, parseResponse, parseValue methods
 * @todo if iconv() or mb_string() are available, we could allow to convert the received xml to a custom charset encoding
 *       while parsing, which is faster than doing it later by going over the rebuilt data structure
 * @todo rename? This is an xml-rpc parser, not a generic xml parser...
 *
 * @property array $xmlrpc_valid_parents deprecated - public access left in purely for BC
 * @property int $accept deprecated - (protected) access left in purely for BC
 */
class XMLParser
{
    use DeprecationLogger;

    const RETURN_XMLRPCVALS = 'xmlrpcvals';
    const RETURN_EPIVALS = 'epivals';
    const RETURN_PHP = 'phpvals';

    const ACCEPT_REQUEST = 1;
    const ACCEPT_RESPONSE = 2;
    const ACCEPT_VALUE = 4;
    const ACCEPT_FAULT = 8;

    /**
     * @var int
     * The max length beyond which data will get truncated in error messages
     */
    protected $maxLogValueLength = 100;

    /**
     * @var array
     * Used to store state during parsing and to pass parsing results to callers.
     * Quick explanation of components:
     *  private:
     *    ac - used to accumulate values
     *    stack - array with genealogy of xml elements names, used to validate nesting of xml-rpc elements
     *    valuestack - array used for parsing arrays and structs
     *    lv - used to indicate "looking for a value": implements the logic to allow values with no types to be strings
     *         (values: 0=not looking, 1=looking, 3=found)
     *  public:
     *    isf - used to indicate an xml-rpc response fault (1), invalid xml-rpc fault (2), xml parsing fault (3)
     *    isf_reason - used for storing xml-rpc response fault string
     *    value - used to store the value in responses
     *    method - used to store method name in requests
     *    params - used to store parameters in requests
     *    pt - used to store the type of each received parameter. Useful if parameters are automatically decoded to php values
     *    rt - 'methodcall', 'methodresponse', 'value' or 'fault' (the last one used only in EPI emulation mode)
     */
    protected $_xh = array(
        'ac' => '',
        'stack' => array(),
        'valuestack' => array(),
        'lv' => 0,
        'isf' => 0,
        'isf_reason' => '',
        'value' => null,
        'method' => false,
        'params' => array(),
        'pt' => array(),
        'rt' => '',
    );

    /**
     * @var array[]
     */
    protected $xmlrpc_valid_parents = array(
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
    //protected $accept = 3;

    /** @var int $maxChunkLength 4 MB by default. Any value below 10MB should be good */
    protected $maxChunkLength = 4194304;
    /** @var array
     * Used keys: accept, target_charset, methodname_callback, plus the ones set here.
     * We initialize it partially to help keep BC with subclasses which might have reimplemented `parse()` but not
     * the element handler methods
     */
    protected $current_parsing_options = array(
        'xmlrpc_null_extension' => false,
        'xmlrpc_return_datetimes' => false,
        'xmlrpc_reject_invalid_values' => false
    );

    /**
     * @param array $options integer keys: options passed to the inner xml parser
     *                       string keys:
     *                       - target_charset (string)
     *                       - methodname_callback (callable)
     *                       - xmlrpc_null_extension (bool)
     *                       - xmlrpc_return_datetimes (bool)
     *                       - xmlrpc_reject_invalid_values (bool)
     */
    public function __construct(array $options = array())
    {
        $this->parsing_options = $options;
    }

    /**
     * Parses an xml-rpc xml string. Results of the parsing are found in $this->['_xh'].
     * Logs to the error log any issues which do not cause the parsing to fail.
     *
     * @param string $data
     * @param string $returnType self::RETURN_XMLRPCVALS, self::RETURN_PHP, self::RETURN_EPIVALS
     * @param int $accept a bit-combination of self::ACCEPT_REQUEST, self::ACCEPT_RESPONSE, self::ACCEPT_VALUE
     * @param array $options integer-key options are passed to the xml parser, string-key options are used independently.
     *                       These options are added to options received in the constructor.
     *                       Note that if options xmlrpc_null_extension, xmlrpc_return_datetimes and xmlrpc_reject_invalid_values
     *                       are not set, the default settings from PhpXmlRpc\PhpXmlRpc are used
     * @return array see the definition of $this->_xh for the meaning of the results
     * @throws \Exception this can happen if a callback function is set and it does throw (i.e. we do not catch exceptions)
     *
     * @todo refactor? we could 1. return the parsed data structure, and 2. move $returnType and $accept into options
     * @todo feature-creep make it possible to pass in options overriding usage of PhpXmlRpc::$xmlrpc_XXX_format, so
     *       that parsing will be completely independent of global state. Note that it might incur a small perf hit...
     */
    public function parse($data, $returnType = self::RETURN_XMLRPCVALS, $accept = 3, $options = array())
    {
        $this->_xh = array(
            'ac' => '',
            'stack' => array(),
            'valuestack' => array(),
            'lv' => 0,
            'isf' => 0,
            'isf_reason' => '',
            'value' => null,
            'method' => false, // so we can check later if we got a methodname or not
            'params' => false, // so we can check later if we got a params tag or not
            'pt' => array(),
            'rt' => '',
        );

        $len = strlen($data);

        // we test for empty documents here to save on resource allocation and simplify the chunked-parsing loop below
        if ($len == 0) {
            $this->_xh['isf'] = 3;
            $this->_xh['isf_reason'] = 'XML error 5: empty document';
            return $this->_xh;
        }

        $this->current_parsing_options = array('accept' => $accept);

        $mergedOptions = $this->parsing_options;
        foreach ($options as $key => $val) {
            $mergedOptions[$key] = $val;
        }

        foreach ($mergedOptions as $key => $val) {
            // q: can php be built without ctype? should we use a regexp?
            if (is_string($key) && !ctype_digit($key)) {
                /// @todo on invalid options, throw/error-out instead of logging an error message?
                switch($key) {
                    case 'target_charset':
                        if (function_exists('mb_convert_encoding')) {
                            $this->current_parsing_options['target_charset'] = $val;
                        } else {
                            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ": 'target_charset' option is unsupported without mbstring");
                        }
                        break;

                    case 'methodname_callback':
                        if (is_callable($val)) {
                            $this->current_parsing_options['methodname_callback'] = $val;
                        } else {
                            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ": Callback passed as 'methodname_callback' is not callable");
                        }
                        break;

                    case 'xmlrpc_null_extension':
                    case 'xmlrpc_return_datetimes':
                    case 'xmlrpc_reject_invalid_values':
                        $this->current_parsing_options[$key] = $val;
                        break;

                    default:
                        $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ": unsupported option: $key");
                }
                unset($mergedOptions[$key]);
            }
        }

        if (!isset($this->current_parsing_options['xmlrpc_null_extension'])) {
            $this->current_parsing_options['xmlrpc_null_extension'] = PhpXmlRpc::$xmlrpc_null_extension;
        }
        if (!isset($this->current_parsing_options['xmlrpc_return_datetimes'])) {
            $this->current_parsing_options['xmlrpc_return_datetimes'] = PhpXmlRpc::$xmlrpc_return_datetimes;
        }
        if (!isset($this->current_parsing_options['xmlrpc_reject_invalid_values'])) {
            $this->current_parsing_options['xmlrpc_reject_invalid_values'] = PhpXmlRpc::$xmlrpc_reject_invalid_values;
        }

        // NB: we use '' instead of null to force charset detection from the xml declaration
        $parser = xml_parser_create('');

        foreach ($mergedOptions as $key => $val) {
            xml_parser_set_option($parser, $key, $val);
        }

        // always set this, in case someone tries to disable it via options...
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);

        switch ($returnType) {
            case self::RETURN_PHP:
                xml_set_element_handler($parser, array($this, 'xmlrpc_se'), array($this, 'xmlrpc_ee_fast'));
                break;
            case self::RETURN_EPIVALS:
                xml_set_element_handler($parser, array($this, 'xmlrpc_se'), array($this, 'xmlrpc_ee_epi'));
                break;
            /// @todo log an error / throw / error-out on unsupported return type
            case XMLParser::RETURN_XMLRPCVALS:
            default:
                xml_set_element_handler($parser, array($this, 'xmlrpc_se'), array($this, 'xmlrpc_ee'));
        }

        xml_set_character_data_handler($parser, array($this, 'xmlrpc_cd'));
        xml_set_default_handler($parser, array($this, 'xmlrpc_dh'));

        try {
            // @see ticket #70 - we have to parse big xml docs in chunks to avoid errors
            for ($offset = 0; $offset < $len; $offset += $this->maxChunkLength) {
                $chunk = substr($data, $offset, $this->maxChunkLength);
                // error handling: xml not well formed
                if (!@xml_parse($parser, $chunk, $offset + $this->maxChunkLength >= $len)) {
                    $errCode = xml_get_error_code($parser);
                    $errStr = sprintf('XML error %s: %s at line %d, column %d', $errCode, xml_error_string($errCode),
                        xml_get_current_line_number($parser), xml_get_current_column_number($parser));
                    $this->_xh['isf'] = 3;
                    $this->_xh['isf_reason'] = $errStr;
                }
                // no need to parse further if we already have a fatal error
                if ($this->_xh['isf'] >= 2) {
                    break;
                }
            }
        /// @todo bump minimum php version to 5.5 and use a finally clause instead of doing cleanup 3 times
        } catch (\Exception $e) {
            xml_parser_free($parser);
            $this->current_parsing_options = array();
            /// @todo should we set $this->_xh['isf'] and $this->_xh['isf_reason'] ?
            throw $e;
        } catch (\Error $e) {
            xml_parser_free($parser);
            $this->current_parsing_options = array();
                //$this->accept = $prevAccept;
                /// @todo should we set $this->_xh['isf'] and $this->_xh['isf_reason'] ?
            throw $e;
        }

        xml_parser_free($parser);
        $this->current_parsing_options = array();

        // BC
        if ($this->_xh['params'] === false) {
            $this->_xh['params'] = array();
        }

        return $this->_xh;
    }

    /**
     * xml parser handler function for opening element tags.
     * @internal
     *
     * @param resource $parser
     * @param string $name
     * @param $attrs
     * @param bool $acceptSingleVals DEPRECATED use the $accept parameter instead
     * @return void
     *
     * @todo optimization creep: throw when setting $this->_xh['isf'] > 1, to completely avoid further xml parsing
     *       and remove the checking for $this->_xh['isf'] >= 2 everywhere
     */
    public function xmlrpc_se($parser, $name, $attrs, $acceptSingleVals = false)
    {
        // if invalid xml-rpc already detected, skip all processing
        if ($this->_xh['isf'] >= 2) {
            return;
        }

        // check for correct element nesting
        if (count($this->_xh['stack']) == 0) {
            // top level element can only be of 2 types
            /// @todo optimization creep: save this check into a bool variable, instead of using count() every time:
            ///       there is only a single top level element in xml anyway

            // BC
            if ($acceptSingleVals === false) {
                $accept = $this->current_parsing_options['accept'];
            } else {
                $this->logDeprecation('Using argument $acceptSingleVals for method ' . __METHOD__ . ' is deprecated');
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
                $curVal = array(
                    'values' => array(),
                    'type' => $name,
                );
                // check for out-of-band information to rebuild php objs and, in case it is found, save it
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
                // valid elements that add little to processing
                break;

            case 'PARAMS':
                $this->_xh['params'] = array();
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
                $this->_xh['valuestack'][count($this->_xh['valuestack']) - 1]['name'] = null;
                //$this->_xh['ac']='';
                // Drop trough intentionally

            case 'PARAM':
                // clear value type, so we can check later if no value has been passed for this param/member
                $this->_xh['vt'] = null;
                break;

            case 'NIL':
            case 'EX:NIL':
                if ($this->current_parsing_options['xmlrpc_null_extension']) {
                    if ($this->_xh['vt'] != 'value') {
                        // two data elements inside a value: an error occurred!
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = "$name element following a {$this->_xh['vt']} element inside a single value";

                        return;
                    }
                    // reset the accumulator - q: is this necessary at all here? we don't use it on _ee anyway for NILs
                    $this->_xh['ac'] = '';

                } else {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = 'Invalid NIL value received. Support for NIL can be enabled via \\PhpXmlRpc\\PhpXmlRpc::$xmlrpc_null_extension';

                    return;
                }
                break;

            default:
                // INVALID ELEMENT: RAISE ISF so that it is later recognized
                /// @todo feature creep = allow a callback instead
                $this->_xh['isf'] = 2;
                $this->_xh['isf_reason'] = "found not-xmlrpc xml element $name";

                return;
        }

        // Save current element name to stack, to validate nesting
        $this->_xh['stack'][] = $name;

        /// @todo optimization creep: move this inside the big switch() above
        if ($name != 'VALUE') {
            $this->_xh['lv'] = 0;
        }
    }

    /**
     * xml parser handler function for close element tags.
     * @internal
     *
     * @param resource $parser
     * @param string $name
     * @param int $rebuildXmlrpcvals >1 for rebuilding xmlrpcvals, 0 for rebuilding php values, -1 for xmlrpc-extension compatibility
     * @return void
     * @throws \Exception this can happen if a callback function is set and it does throw (i.e. we do not catch exceptions)
     *
     * @todo optimization creep: throw when setting $this->_xh['isf'] > 1, to completely avoid further xml parsing
     *       and remove the checking for $this->_xh['isf'] >= 2 everywhere
     */
    public function xmlrpc_ee($parser, $name, $rebuildXmlrpcvals = 1)
    {
        if ($this->_xh['isf'] >= 2) {
            return;
        }

        // push this element name from stack
        // NB: if XML validates, correct opening/closing is guaranteed and we do not have to check for $name == $currElem.
        // we also checked for proper nesting at start of elements...
        $currElem = array_pop($this->_xh['stack']);

        switch ($name) {
            case 'VALUE':
                // If no scalar was inside <VALUE></VALUE>, it was a string value
                if ($this->_xh['vt'] == 'value') {
                    $this->_xh['value'] = $this->_xh['ac'];
                    $this->_xh['vt'] = Value::$xmlrpcString;
                }

                // in case there is charset conversion required, do it here, to catch both cases of string values
                if (isset($this->current_parsing_options['target_charset']) && $this->_xh['vt'] === Value::$xmlrpcString) {
                    $this->_xh['value'] = mb_convert_encoding($this->_xh['value'], $this->current_parsing_options['target_charset'], 'UTF-8');
                }

                if ($rebuildXmlrpcvals > 0) {
                    // build the xml-rpc val out of the data received, and substitute it
                    $temp = new Value($this->_xh['value'], $this->_xh['vt']);
                    // in case we got info about underlying php class, save it in the object we're rebuilding
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
                    /// @todo this should handle php-serialized objects, since std deserializing is done
                    ///       by php_xmlrpc_decode, which we will not be calling...
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

            case 'STRING':
                $this->_xh['vt'] = Value::$xmlrpcString;
                $this->_xh['lv'] = 3; // indicate we've found a value
                $this->_xh['value'] = $this->_xh['ac'];
                break;

            case 'BOOLEAN':
                $this->_xh['vt'] = Value::$xmlrpcBoolean;
                $this->_xh['lv'] = 3; // indicate we've found a value
                // We translate boolean 1 or 0 into PHP constants true or false. Strings 'true' and 'false' are accepted,
                // even though the spec never mentions them (see e.g. Blogger api docs)
                // NB: this simple checks helps a lot sanitizing input, i.e. no security problems around here
                // Note the non-strict type check: it will allow ' 1 '
                /// @todo feature-creep: use a flexible regexp, the same as we do with int, double and datetime.
                ///       Note that using a regexp would also make this test less sensitive to phpunit shenanigans, and
                ///       to changes in the way php compares strings (since 8.0, leading and trailing newlines are
                ///       accepted when deciding if a string numeric...)
                if ($this->_xh['ac'] == '1' || strcasecmp($this->_xh['ac'], 'true') === 0) {
                    $this->_xh['value'] = true;
                } else {
                    // log if receiving something strange, even though we set the value to false anyway
                    /// @todo to be consistent with the other types, we should return a value outside the good-value domain, e.g. NULL
                    if ($this->_xh['ac'] != '0' && strcasecmp($this->_xh['ac'], 'false') !== 0) {
                        if (!$this->handleParsingError('invalid data received in BOOLEAN value: ' .
                            $this->truncateValueForLog($this->_xh['ac']), __METHOD__)) {
                            return;
                        }
                    }
                    $this->_xh['value'] = false;
                }
                break;

            case 'EX:I8':
                $name = 'i8';
                // fall through voluntarily
            case 'I4':
            case 'I8':
            case 'INT':
                // NB: we build the Value object with the original xml element name found, except for ex:i8. The
                // `Value::scalarTyp()` function will do some normalization of the data
                $this->_xh['vt'] = strtolower($name);
                $this->_xh['lv'] = 3; // indicate we've found a value
                if (!preg_match(PhpXmlRpc::$xmlrpc_int_format, $this->_xh['ac'])) {
                    if (!$this->handleParsingError('non numeric data received in INT value: ' .
                        $this->truncateValueForLog($this->_xh['ac']), __METHOD__)) {
                        return;
                    }
                    /// @todo: find a better way of reporting an error value than this! Use NaN?
                    $this->_xh['value'] = 'ERROR_NON_NUMERIC_FOUND';
                } else {
                    // it's ok, add it on
                    $this->_xh['value'] = (int)$this->_xh['ac'];
                }
                break;

            case 'DOUBLE':
                $this->_xh['vt'] = Value::$xmlrpcDouble;
                $this->_xh['lv'] = 3; // indicate we've found a value
                if (!preg_match(PhpXmlRpc::$xmlrpc_double_format, $this->_xh['ac'])) {
                    if (!$this->handleParsingError('non numeric data received in DOUBLE value: ' .
                        $this->truncateValueForLog($this->_xh['ac']), __METHOD__)) {
                        return;
                    }

                    $this->_xh['value'] = 'ERROR_NON_NUMERIC_FOUND';
                } else {
                    // it's ok, add it on
                    $this->_xh['value'] = (double)$this->_xh['ac'];
                }
                break;

            case 'DATETIME.ISO8601':
                $this->_xh['vt'] = Value::$xmlrpcDateTime;
                $this->_xh['lv'] = 3; // indicate we've found a value
                if (!preg_match(PhpXmlRpc::$xmlrpc_datetime_format, $this->_xh['ac'])) {
                    if (!$this->handleParsingError('invalid data received in DATETIME value: ' .
                        $this->truncateValueForLog($this->_xh['ac']), __METHOD__)) {
                        return;
                    }
                }
                if ($this->current_parsing_options['xmlrpc_return_datetimes']) {
                    try {
                        $this->_xh['value'] = new \DateTime($this->_xh['ac']);

                    // the default regex used to validate the date string a few lines above should make this case impossible,
                    // but one never knows...
                    } catch(\Exception $e) {
                        // what to do? We can not guarantee that a valid date can be created. We return null...
                        if (!$this->handleParsingError('invalid data received in DATETIME value. Error ' .
                            $e->getMessage(), __METHOD__)) {
                            return;
                        }
                    }
                } else {
                    $this->_xh['value'] = $this->_xh['ac'];
                }
                break;

            case 'BASE64':
                $this->_xh['vt'] = Value::$xmlrpcBase64;
                $this->_xh['lv'] = 3; // indicate we've found a value
                if ($this->current_parsing_options['xmlrpc_reject_invalid_values']) {
                    $v = base64_decode($this->_xh['ac'], true);
                    if ($v === false) {
                        $this->_xh['isf'] = 2;
                        $this->_xh['isf_reason'] = 'Invalid data received in BASE64 value: '. $this->truncateValueForLog($this->_xh['ac']);
                        return;
                    }
                } else {
                    $v = base64_decode($this->_xh['ac']);
                    if ($v === '' && $this->_xh['ac'] !== '') {
                        // only the empty string should decode to the empty string
                        $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': invalid data received in BASE64 value: ' .
                            $this->truncateValueForLog($this->_xh['ac']));
                    }
                }
                $this->_xh['value'] = $v;
                break;

            case 'NAME':
                $this->_xh['valuestack'][count($this->_xh['valuestack']) - 1]['name'] = $this->_xh['ac'];
                break;

            case 'MEMBER':
                // add to array in the stack the last element built, unless no VALUE or no NAME were found
                if ($this->_xh['vt']) {
                    $vscount = count($this->_xh['valuestack']);
                    if ($this->_xh['valuestack'][$vscount - 1]['name'] === null) {
                        if (!$this->handleParsingError('missing NAME inside STRUCT in received xml', __METHOD__)) {
                            return;
                        }
                        $this->_xh['valuestack'][$vscount - 1]['name'] = '';
                    }
                    $this->_xh['valuestack'][$vscount - 1]['values'][$this->_xh['valuestack'][$vscount - 1]['name']] = $this->_xh['value'];
                } else {
                    if (!$this->handleParsingError('missing VALUE inside STRUCT in received xml', __METHOD__)) {
                        return;
                    }
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
                // add to array of params the current value, unless no VALUE was found
                /// @todo should we also check if there were two VALUE inside the PARAM?
                if ($this->_xh['vt']) {
                    $this->_xh['params'][] = $this->_xh['value'];
                    $this->_xh['pt'][] = $this->_xh['vt'];
                } else {
                    if (!$this->handleParsingError('missing VALUE inside PARAM in received xml', __METHOD__)) {
                        return;
                    }
                }
                break;

            case 'METHODNAME':
                if (!preg_match(PhpXmlRpc::$xmlrpc_methodname_format, $this->_xh['ac'])) {
                    if (!$this->handleParsingError('invalid data received in METHODNAME: '.
                        $this->truncateValueForLog($this->_xh['ac']), __METHOD__)) {
                        return;
                    }
                }
                $methodName = trim($this->_xh['ac']);
                $this->_xh['method'] = $methodName;
                // we allow the callback to f.e. give us back a mangled method name by manipulating $this
                if (isset($this->current_parsing_options['methodname_callback'])) {
                    call_user_func($this->current_parsing_options['methodname_callback'], $methodName, $this, $parser);
                }
                break;

            case 'NIL':
            case 'EX:NIL':
                // NB: if NIL support is not enabled, parsing stops at element start. So this If is redundant
                //if ($this->current_parsing_options['xmlrpc_null_extension']) {
                    $this->_xh['vt'] = 'null';
                    $this->_xh['value'] = null;
                    $this->_xh['lv'] = 3;
                //}
                break;

            /// @todo add extra checking:
            ///       - FAULT should contain a single struct with the 2 expected members (check their name and type)
            case 'PARAMS':
            case 'FAULT':
                break;

            case 'METHODCALL':
                /// @todo should we allow to accept this case via a call to handleParsingError ?
                if ($this->_xh['method'] === false) {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "missing METHODNAME element inside METHODCALL";
                }
                break;

            case 'METHODRESPONSE':
                /// @todo should we allow to accept these cases via a call to handleParsingError ?
                if ($this->_xh['isf'] != 1 && $this->_xh['params'] === false) {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "missing both FAULT and PARAMS elements inside METHODRESPONSE";
                } elseif ($this->_xh['isf'] == 0 && count($this->_xh['params']) !== 1) {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "PARAMS element inside METHODRESPONSE should have exactly 1 PARAM";
                } elseif ($this->_xh['isf'] == 1 && $this->_xh['params'] !== false) {
                    $this->_xh['isf'] = 2;
                    $this->_xh['isf_reason'] = "both FAULT and PARAMS elements found inside METHODRESPONSE";
                }
                break;

            default:
                // End of INVALID ELEMENT
                // Should we add an assert here for unreachable code? When an invalid element is found in xmlrpc_se,
                // $this->_xh['isf'] is set to 2...
                break;
        }
    }

    /**
     * Used in decoding xml-rpc requests/responses without rebuilding xml-rpc Values.
     * @internal
     *
     * @param resource $parser
     * @param string $name
     * @return void
     */
    public function xmlrpc_ee_fast($parser, $name)
    {
        $this->xmlrpc_ee($parser, $name, 0);
    }

    /**
     * Used in decoding xml-rpc requests/responses while building xmlrpc-extension Values (plain php for all but base64 and datetime).
     * @internal
     *
     * @param resource $parser
     * @param string $name
     * @return void
     */
    public function xmlrpc_ee_epi($parser, $name)
    {
        $this->xmlrpc_ee($parser, $name, -1);
    }

    /**
     * xml parser handler function for character data.
     * @internal
     *
     * @param resource $parser
     * @param string $data
     * @return void
     */
    public function xmlrpc_cd($parser, $data)
    {
        // skip processing if xml fault already detected
        if ($this->_xh['isf'] >= 2) {
            return;
        }

        // "lookforvalue == 3" means that we've found an entire value and should discard any further character data
        if ($this->_xh['lv'] != 3) {
            $this->_xh['ac'] .= $data;
        }
    }

    /**
     * xml parser handler function for 'other stuff', i.e. not char data or element start/end tag.
     * In fact, it only gets called on unknown entities...
     * @internal
     *
     * @param $parser
     * @param string data
     * @return void
     */
    public function xmlrpc_dh($parser, $data)
    {
        // skip processing if xml fault already detected
        if ($this->_xh['isf'] >= 2) {
            return;
        }

        if (substr($data, 0, 1) == '&' && substr($data, -1, 1) == ';') {
            $this->_xh['ac'] .= $data;
        }
    }

    /**
     * xml charset encoding guessing helper function.
     * Tries to determine the charset encoding of an XML chunk received over HTTP.
     *
     * NB: according to the spec (RFC 3023), if text/xml content-type is received over HTTP without a content-type,
     * we SHOULD assume it is strictly US-ASCII. But we try to be more tolerant of non-conforming (legacy?) clients/servers,
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
     * @todo as of 2023, the relevant RFC for XML Media Types is now 7303, and for HTTP it is 9110. Check if the order of
     *       precedence implemented here is still correct
     * @todo explore usage of mb_http_input(): does it detect http headers + post data? if so, use it instead of hand-detection!!!
     * @todo feature-creep make it possible to pass in options overriding usage of PhpXmlRpc static variables, to make
     *       the method independent of global state
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
        if (preg_match('/^(?:\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\x00\x00\xFF\xFE|\xFE\xFF\x00\x00)/', $xmlChunk)) {
            return 'UCS-4';
        } elseif (preg_match('/^(?:\xFE\xFF|\xFF\xFE)/', $xmlChunk)) {
            return 'UTF-16';
        } elseif (preg_match('/^(?:\xEF\xBB\xBF)/', $xmlChunk)) {
            return 'UTF-8';
        }

        // 3 - test if encoding is specified in the xml declaration
        /// @todo this regexp will fail if $xmlChunk uses UTF-32/UCS-4, and most likely UTF-16/UCS-2 as well. In that
        ///       case we leave the guesswork up to mbstring - which seems to be able to detect it, starting with php 5.6.
        ///       For lower versions, we could attempt usage of mb_ereg...
        // Details:
        // SPACE:         (#x20 | #x9 | #xD | #xA)+ === [ \x9\xD\xA]+
        // EQ:            SPACE?=SPACE? === [ \x9\xD\xA]*=[ \x9\xD\xA]*
        // We could be stricter on version number: VersionNum ::= '1.' [0-9]+
        if (preg_match('/^<\?xml\s+version\s*=\s*' . "((?:\"[a-zA-Z0-9_.:-]+\")|(?:'[a-zA-Z0-9_.:-]+'))" .
            '\s+encoding\s*=\s*' . "((?:\"[A-Za-z][A-Za-z0-9._-]*\")|(?:'[A-Za-z][A-Za-z0-9._-]*'))/",
            $xmlChunk, $matches)) {
            return strtoupper(substr($matches[2], 1, -1));
        }

        // 4 - if mbstring is available, let it do the guesswork
        if (function_exists('mb_detect_encoding')) {
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
            // No encoding specified: assume it is iso-8859-1, as per HTTP1.1?
            // Both RFC 2616 (HTTP 1.1) and RFC 1945 (HTTP 1.0) clearly state that for text/xxx content types
            // this should be the standard. And we should be getting text/xml as request and response.
            // BUT we have to be backward compatible with the lib, which always used UTF-8 as default. Moreover,
            // RFC 7231, which obsoletes the two RFC mentioned above, has changed the rules. It says:
            // "The default charset of ISO-8859-1 for text media types has been removed; the default is now whatever
            // the media type definition says."
            return PhpXmlRpc::$xmlrpc_defencoding;
        }
    }

    /**
     * Helper function: checks if an xml chunk has a charset declaration (BOM or in the xml declaration).
     *
     * @param string $xmlChunk
     * @return bool
     *
     * @todo rename to hasEncodingDeclaration
     */
    public static function hasEncoding($xmlChunk)
    {
        // scan the first bytes of the data for a UTF-16 (or other) BOM pattern
        //     (source: http://www.w3.org/TR/2000/REC-xml-20001006)
        if (preg_match('/^(?:\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\x00\x00\xFF\xFE|\xFE\xFF\x00\x00)/', $xmlChunk)) {
            return true;
        } elseif (preg_match('/^(?:\xFE\xFF|\xFF\xFE)/', $xmlChunk)) {
            return true;
        } elseif (preg_match('/^(?:\xEF\xBB\xBF)/', $xmlChunk)) {
            return true;
        }

        // test if encoding is specified in the xml declaration
        // Details:
        // SPACE:         (#x20 | #x9 | #xD | #xA)+ === [ \x9\xD\xA]+
        // EQ:            SPACE?=SPACE? === [ \x9\xD\xA]*=[ \x9\xD\xA]*
        // We could be stricter on version number: VersionNum ::= '1.' [0-9]+
        if (preg_match('/^<\?xml\s+version\s*=\s*' . "((?:\"[a-zA-Z0-9_.:-]+\")|(?:'[a-zA-Z0-9_.:-]+'))" .
            '\s+encoding\s*=\s*' . "((?:\"[A-Za-z][A-Za-z0-9._-]*\")|(?:'[A-Za-z][A-Za-z0-9._-]*'))/",
            $xmlChunk)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $message
     * @param string $method method/file/line info
     * @return bool false if the caller has to stop parsing
     */
    protected function handleParsingError($message, $method = '')
    {
        if ($this->current_parsing_options['xmlrpc_reject_invalid_values']) {
            $this->_xh['isf'] = 2;
            $this->_xh['isf_reason'] = ucfirst($message);
            return false;
        } else {
            $this->getLogger()->error('XML-RPC: ' . ($method != '' ? $method . ': ' : '') . $message);
            return true;
        }
    }

    /**
     * Truncates unsafe data
     * @param string $data
     * @return string
     */
    protected function truncateValueForLog($data)
    {
        if (strlen($data) > $this->maxLogValueLength) {
            return substr($data, 0, $this->maxLogValueLength - 3) . '...';
        }

        return $data;
    }

    // *** BC layer ***

    /**
     * xml parser handler function for opening element tags.
     * Used in decoding xml chunks that might represent single xml-rpc values as well as requests, responses.
     * @deprecated
     *
     * @param resource $parser
     * @param $name
     * @param $attrs
     * @return void
     */
    public function xmlrpc_se_any($parser, $name, $attrs)
    {
        // this will be spamming the log if this method is in use...
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        $this->xmlrpc_se($parser, $name, $attrs, true);
    }

    public function &__get($name)
    {
        switch ($name) {
            case '_xh':
            case 'xmlrpc_valid_parents':
                $this->logDeprecation('Getting property XMLParser::' . $name . ' is deprecated');
                return $this->$name;
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
            // this should only ever be called by subclasses which overtook `parse()`
            case 'accept':
                $this->logDeprecation('Setting property XMLParser::' . $name . ' is deprecated');
                $this->current_parsing_options['accept'] = $value;
                break;
            case '_xh':
            case 'xmlrpc_valid_parents':
                $this->logDeprecation('Setting property XMLParser::' . $name . ' is deprecated');
                $this->$name = $value;
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
            case 'accept':
                $this->logDeprecation('Checking property XMLParser::' . $name . ' is deprecated');
                return isset($this->current_parsing_options['accept']);
            case '_xh':
            case 'xmlrpc_valid_parents':
                $this->logDeprecation('Checking property XMLParser::' . $name . ' is deprecated');
                return isset($this->$name);
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch ($name) {
            // q: does this make sense at all?
            case 'accept':
                $this->logDeprecation('Unsetting property XMLParser::' . $name . ' is deprecated');
                unset($this->current_parsing_options['accept']);
                break;
            case '_xh':
            case 'xmlrpc_valid_parents':
                $this->logDeprecation('Unsetting property XMLParser::' . $name . ' is deprecated');
                unset($this->$name);
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
