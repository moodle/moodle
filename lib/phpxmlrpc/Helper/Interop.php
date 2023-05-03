<?php

namespace PhpXmlRpc\Helper;

/**
 * A helper dedicated to support Interoperability features
 */
class Interop
{
    /// @todo review - should we use the range -32099 .. -32000 for some server erors?
    public static $xmlrpcerr = array(
        'unknown_method' => -32601,
        'invalid_return' => 2,
        'incorrect_params' => -32602,
        'introspect_unknown' => -32601, // this shares the same code but has a separate meaning from 'unknown_method'...
        'http_error' => -32300,
        'no_data' => -32700,
        'no_ssl' => -32400,
        'curl_fail' => -32400,
        'invalid_request' => -32600,
        'no_curl' => -32400,
        'server_error' => -32500,
        'multicall_error' => -32700,
        'multicall_notstruct' => -32600,
        'multicall_nomethod' => -32601,
        'multicall_notstring' => -32600,
        'multicall_recursion' => -32603,
        'multicall_noparams' => -32602,
        'multicall_notarray' => -32600,
        'no_http2' => -32400,
        'invalid_xml' => -32700,
        'xml_not_compliant' => -32700,
        'xml_parsing_error' => -32700,
        'cannot_decompress' => -32400,
        'decompress_fail' => -32300,
        'dechunk_fail' => -32300,
        'server_cannot_decompress' => -32300,
        'server_decompress_fail' => -32300,
    );

    public static $xmlrpcerruser = -32000;
}
