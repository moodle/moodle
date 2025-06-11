<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;
use PhpXmlRpc\Helper\Http;
use PhpXmlRpc\Helper\Interop;
use PhpXmlRpc\Helper\XMLParser;

/**
 * Manages global configuration for operation of the library.
 */
class PhpXmlRpc
{
    /**
     * @var int[]
     */
    public static $xmlrpcerr = array(
        'unknown_method' => 1, // server
        /// @deprecated. left in for BC
        'invalid_return' => 2, // client
        'incorrect_params' => 3, // server
        'introspect_unknown' => 4, // server
        'http_error' => 5, // client
        'no_data' => 6, // client
        'no_ssl' => 7, // client
        'curl_fail' => 8, // client
        'invalid_request' => 15, // server
        'no_curl' => 16, // client
        'server_error' => 17, // server
        'multicall_error' => 18, // client
        'multicall_notstruct' => 9, // client
        'multicall_nomethod' => 10, // client
        'multicall_notstring' => 11, // client
        'multicall_recursion' => 12, // client
        'multicall_noparams' => 13, // client
        'multicall_notarray' => 14, // client
        'no_http2' => 19, // client
        'unsupported_option' => 20, // client
        // the following 3 are meant to give greater insight than 'invalid_return'. They use the same code for BC,
        // but you can override their value in your own code
        'invalid_xml' => 2, // client
        'xml_not_compliant' => 2, // client
        'xml_parsing_error' => 2, // client

        /// @todo verify: can these conflict with $xmlrpcerrxml?
        'cannot_decompress' => 103,
        'decompress_fail' => 104,
        'dechunk_fail' => 105,
        'server_cannot_decompress' => 106,
        'server_decompress_fail' => 107,
    );

    /**
     * @var string[]
     */
    public static $xmlrpcstr = array(
        'unknown_method' => 'Unknown method',
        /// @deprecated. left in for BC
        'invalid_return' => 'Invalid response payload (you can use the setDebug method to allow analysis of the response)',
        'incorrect_params' => 'Incorrect parameters passed to method',
        'introspect_unknown' => "Can't introspect: method unknown",
        'http_error' => "Didn't receive 200 OK from remote server",
        'no_data' => 'No data received from server',
        'no_ssl' => 'No SSL support compiled in',
        'curl_fail' => 'CURL error',
        'invalid_request' => 'Invalid request payload',
        'no_curl' => 'No CURL support compiled in',
        'server_error' => 'Internal server error',
        'multicall_error' => 'Received from server invalid multicall response',
        'multicall_notstruct' => 'system.multicall expected struct',
        'multicall_nomethod' => 'Missing methodName',
        'multicall_notstring' => 'methodName is not a string',
        'multicall_recursion' => 'Recursive system.multicall forbidden',
        'multicall_noparams' => 'Missing params',
        'multicall_notarray' => 'params is not an array',
        'no_http2' => 'No HTTP/2 support compiled in',
        'unsupported_option' => 'Some client option is not supported with the transport method currently in use',
        // the following 3 are meant to give greater insight than 'invalid_return'. They use the same string for BC,
        // but you can override their value in your own code
        'invalid_xml' => 'Invalid response payload (you can use the setDebug method to allow analysis of the response)',
        'xml_not_compliant' => 'Invalid response payload (you can use the setDebug method to allow analysis of the response)',
        'xml_parsing_error' => 'Invalid response payload (you can use the setDebug method to allow analysis of the response)',

        'cannot_decompress' => 'Received from server compressed HTTP and cannot decompress',
        'decompress_fail' => 'Received from server invalid compressed HTTP',
        'dechunk_fail' => 'Received from server invalid chunked HTTP',
        'server_cannot_decompress' => 'Received from client compressed HTTP request and cannot decompress',
        'server_decompress_fail' => 'Received from client invalid compressed HTTP request',
    );

    /**
     * @var string
     * The charset encoding used by the server for received requests and by the client for received responses when
     * received charset cannot be determined and mbstring extension is not enabled.
     */
    public static $xmlrpc_defencoding = "UTF-8";
    /**
     * @var string[]
     * The list of preferred encodings used by the server for requests and by the client for responses to detect the
     * charset of the received payload when
     * - the charset cannot be determined by looking at http headers, xml declaration or BOM
     * - mbstring extension is enabled
     */
    public static $xmlrpc_detectencodings = array();
    /**
     * @var string
     * The encoding used internally by PHP.
     * String values received as xml will be converted to this, and php strings will be converted to xml as if
     * having been coded with this.
     * Valid also when defining names of xml-rpc methods
     */
    public static $xmlrpc_internalencoding = "UTF-8";

    /**
     * @var string
     */
    public static $xmlrpcName = "XML-RPC for PHP";
    /**
     * @var string
     */
    public static $xmlrpcVersion = "4.10.1";

    /**
     * @var int
     * Let user errors start at 800
     */
    public static $xmlrpcerruser = 800;
    /**
     * @var int
     * Let XML parse errors start at 100
     */
    public static $xmlrpcerrxml = 100;

    /**
     * @var bool
     * Set to TRUE to enable correct decoding of <NIL/> and <EX:NIL/> values
     */
    public static $xmlrpc_null_extension = false;

    /**
     * @var bool
     * Set to TRUE to make the library use DateTime objects instead of strings for all values parsed from incoming XML.
     * NB: if the received strings are not parseable as dates, NULL will be returned. To prevent that, enable as
     * well `xmlrpc_reject_invalid_values`, so that invalid dates will be rejected by the library
     */
    public static $xmlrpc_return_datetimes = false;

    /**
     * @var bool
     * Set to TRUE to make the library reject incoming xml which uses invalid data for xml-rpc elements, such
     * as base64 strings which can not be decoded, dateTime strings which do not represent a valid date, invalid bools,
     * floats and integers, method names with forbidden characters, or struct members missing the value or name
     */
    public static $xmlrpc_reject_invalid_values = false;

    /**
     * @var bool
     * Set to TRUE to enable encoding of php NULL values to <EX:NIL/> instead of <NIL/>
     */
    public static $xmlrpc_null_apache_encoding = false;

    public static $xmlrpc_null_apache_encoding_ns = "http://ws.apache.org/xmlrpc/namespaces/extensions";

    /**
     * @var int
     * Number of decimal digits used to serialize Double values.
     * @todo rename :'-(
     */
    public static $xmlpc_double_precision = 128;

    /**
     * @var string
     * Used to validate received date values. Alter this if the server/client you are communicating with uses date
     * formats non-conformant with the spec
     * NB: the string should not match any data which php can not successfully use in a DateTime object constructor call
     * NB: atm, the Date helper uses this regexp and expects to find matches in a specific order
     */
    public static $xmlrpc_datetime_format = '/^([0-9]{4})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])T([01][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]|60)$/';

    /**
     * @var string
     * Used to validate received integer values. Alter this if the server/client you are communicating with uses
     * formats non-conformant with the spec.
     * We keep in spaces for BC, even though they are forbidden by the spec.
     * NB: the string should not match any data which php can not successfully cast to an integer
     */
    public static $xmlrpc_int_format = '/^[ \t]*[+-]?[0-9]+[ \t]*$/';

    /**
     * @var string
     * Used to validate received double values. Alter this if the server/client you are communicating with uses
     * formats non-conformant with the spec, e.g. with leading/trailing spaces/tabs/newlines.
     * We keep in spaces for BC, even though they are forbidden by the spec.
     * NB: the string should not match any data which php can not successfully cast to a float
     */
    public static $xmlrpc_double_format = '/^[ \t]*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?[ \t]*$/';

    /**
     * @var string
     * Used to validate received methodname values.
     * According to the spec: "The string may only contain identifier characters, upper and lower-case A-Z, the numeric
     * characters, 0-9, underscore, dot, colon and slash".
     * We keep in leading and trailing spaces for BC, even though they are forbidden by the spec.
     * But what about "identifier characters"? Is that meant to be 'identifier characters: upper and lower-case A-Z, ...'
     * or something else? If the latter, there is no consensus across programming languages about what is a valid
     * identifier character. PHP has one of the most crazy definitions of what is a valid identifier character, allowing
     * _bytes_ in range x80-xff, without even specifying a character set (and then lowercasing anyway in some cases)...
     */
    public static $xmlrpc_methodname_format = '|^[ \t]*[a-zA-Z0-9_.:/]+[ \t]*$|';

    /**
     * @var bool
     * Set this to false to have a warning added to the log whenever user code uses a deprecated method/parameter/property
     */
    public static $xmlrpc_silence_deprecations = true;

    // *** BC layer ***

    /**
     * Inject a logger into all classes of the PhpXmlRpc library which use one
     *
     * @param $logger
     * @return void
     */
    public static function setLogger($logger)
    {
        Charset::setLogger($logger);
        Client::setLogger($logger);
        Encoder::setLogger($logger);
        Http::setLogger($logger);
        Request::setLogger($logger);
        Server::setLogger($logger);
        Value::setLogger($logger);
        Wrapper::setLogger($logger);
        XMLParser::setLogger($logger);
    }

    /**
     * Makes the library use the error codes detailed at https://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
     *
     * @return void
     *
     * @tofo feature creep - allow switching back to the original set of codes; querying the current mode
     */
    public static function useInteropFaults()
    {
        self::$xmlrpcerr = Interop::$xmlrpcerr;

        self::$xmlrpcerruser = -Interop::$xmlrpcerruser;
    }

    /**
     * A function to be used for compatibility with legacy code: it creates all global variables which used to be declared,
     * such as library version etc...
     * @return void
     *
     * @deprecated
     */
    public static function exportGlobals()
    {
        $reflection = new \ReflectionClass('PhpXmlRpc\PhpXmlRpc');
        foreach ($reflection->getStaticProperties() as $name => $value) {
            if (!in_array($name, array('xmlrpc_return_datetimes', 'xmlrpc_reject_invalid_values', 'xmlrpc_datetime_format',
                'xmlrpc_int_format', 'xmlrpc_double_format', 'xmlrpc_methodname_format', 'xmlrpc_silence_deprecations'))) {
                $GLOBALS[$name] = $value;
            }
        }

        // NB: all the variables exported into the global namespace below here do NOT guarantee 100% compatibility,
        // as they are NOT reimported back during calls to importGlobals()

        $reflection = new \ReflectionClass('PhpXmlRpc\Value');
        foreach ($reflection->getStaticProperties() as $name => $value) {
            if (!in_array($name, array('logger', 'charsetEncoder'))) {
                $GLOBALS[$name] = $value;
            }
        }

        /// @todo mke it possible to inject the XMLParser and Charset, as we do in other classes

        $parser = new Helper\XMLParser();
        $GLOBALS['xmlrpc_valid_parents'] = $parser->xmlrpc_valid_parents;

        $charset = Charset::instance();
        $GLOBALS['xml_iso88591_Entities'] = $charset->getEntities('iso88591');
    }

    /**
     * A function to be used for compatibility with legacy code: it gets the values of all global variables which used
     * to be declared, such as library version etc... and sets them to php classes.
     * It should be used by code which changed the values of those global variables to alter the working of the library.
     * Example code:
     * 1. include xmlrpc.inc
     * 2. set the values, e.g. $GLOBALS['xmlrpc_internalencoding'] = 'UTF-8';
     * 3. import them: PhpXmlRpc\PhpXmlRpc::importGlobals();
     * 4. run your own code.
     *
     * @return void
     *
     * @deprecated
     *
     * @todo this function does not import back xmlrpc_valid_parents and xml_iso88591_Entities
     */
    public static function importGlobals()
    {
        $reflection = new \ReflectionClass('PhpXmlRpc\PhpXmlRpc');
        foreach ($reflection->getStaticProperties() as $name => $value) {
            if (!in_array($name, array('xmlrpc_return_datetimes', 'xmlrpc_reject_invalid_values', 'xmlrpc_datetime_format',
                'xmlrpc_int_format', 'xmlrpc_double_format', 'xmlrpc_methodname_format', 'xmlrpc_silence_deprecations')))
            {
                if (isset($GLOBALS[$name])) {
                    self::$$name = $GLOBALS[$name];
                }
            }
        }
    }
}
