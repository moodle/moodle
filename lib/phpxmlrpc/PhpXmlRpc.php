<?php

namespace PhpXmlRpc;

/**
 * Manages global configuration for operation of the library.
 */
class PhpXmlRpc
{
    static public $xmlrpcerr = array(
        'unknown_method' => 1,
        'invalid_return' => 2,
        'incorrect_params' => 3,
        'introspect_unknown' => 4,
        'http_error' => 5,
        'no_data' => 6,
        'no_ssl' => 7,
        'curl_fail' => 8,
        'invalid_request' => 15,
        'no_curl' => 16,
        'server_error' => 17,
        'multicall_error' => 18,
        'multicall_notstruct' => 9,
        'multicall_nomethod' => 10,
        'multicall_notstring' => 11,
        'multicall_recursion' => 12,
        'multicall_noparams' => 13,
        'multicall_notarray' => 14,
        'no_http2' => 15,

        'cannot_decompress' => 103,
        'decompress_fail' => 104,
        'dechunk_fail' => 105,
        'server_cannot_decompress' => 106,
        'server_decompress_fail' => 107,
    );

    static public $xmlrpcstr = array(
        'unknown_method' => 'Unknown method',
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

        'cannot_decompress' => 'Received from server compressed HTTP and cannot decompress',
        'decompress_fail' => 'Received from server invalid compressed HTTP',
        'dechunk_fail' => 'Received from server invalid chunked HTTP',
        'server_cannot_decompress' => 'Received from client compressed HTTP request and cannot decompress',
        'server_decompress_fail' => 'Received from client invalid compressed HTTP request',
    );

    // The charset encoding used by the server for received requests and by the client for received responses when
    // received charset cannot be determined and mbstring extension is not enabled
    public static $xmlrpc_defencoding = "UTF-8";

    // The list of encodings used by the server for requests and by the client for responses to detect the charset of
    // the received payload when
    // - the charset cannot be determined by looking at http headers, xml declaration or BOM
    // - mbstring extension is enabled
    public static $xmlrpc_detectencodings = array();

    // The encoding used internally by PHP.
    // String values received as xml will be converted to this, and php strings will be converted to xml as if
    // having been coded with this.
    // Valid also when defining names of xmlrpc methods
    public static $xmlrpc_internalencoding = "UTF-8";

    public static $xmlrpcName = "XML-RPC for PHP";
    public static $xmlrpcVersion = "4.8.1";

    // let user errors start at 800
    public static $xmlrpcerruser = 800;
    // let XML parse errors start at 100
    public static $xmlrpcerrxml = 100;

    // set to TRUE to enable correct decoding of <NIL/> and <EX:NIL/> values
    public static $xmlrpc_null_extension = false;

    // set to TRUE to enable encoding of php NULL values to <EX:NIL/> instead of <NIL/>
    public static $xmlrpc_null_apache_encoding = false;

    public static $xmlrpc_null_apache_encoding_ns = "http://ws.apache.org/xmlrpc/namespaces/extensions";

    // number of decimal digits used to serialize Double values
    public static $xmlpc_double_precision = 128;

    /**
     * A function to be used for compatibility with legacy code: it creates all global variables which used to be declared,
     * such as library version etc...
     */
    public static function exportGlobals()
    {
        $reflection = new \ReflectionClass('PhpXmlRpc\PhpXmlRpc');
        foreach ($reflection->getStaticProperties() as $name => $value) {
            $GLOBALS[$name] = $value;
        }

        // NB: all the variables exported into the global namespace below here do NOT guarantee 100% compatibility,
        // as they are NOT reimported back during calls to importGlobals()

        $reflection = new \ReflectionClass('PhpXmlRpc\Value');
        foreach ($reflection->getStaticProperties() as $name => $value) {
            $GLOBALS[$name] = $value;
        }

        $parser = new Helper\XMLParser();
        $reflection = new \ReflectionClass('PhpXmlRpc\Helper\XMLParser');
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $name => $value) {
            if (in_array($value->getName(), array('xmlrpc_valid_parents')))
            {
                $GLOBALS[$value->getName()] = $value->getValue($parser);
            }
        }

        $charset = Helper\Charset::instance();
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
     */
    public static function importGlobals()
    {
        $reflection = new \ReflectionClass('PhpXmlRpc\PhpXmlRpc');
        $staticProperties = $reflection->getStaticProperties();
        foreach ($staticProperties as $name => $value) {
            if (isset($GLOBALS[$name])) {
                self::$$name = $GLOBALS[$name];
            }
        }
    }
}
