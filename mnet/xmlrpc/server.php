<?php
/**
 * An XML-RPC server
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

// Make certain that config.php doesn't display any errors, and that it doesn't
// override our do-not-display-errors setting:
ini_set('display_errors',0);
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
ini_set('display_errors',0);

// Include MNET stuff:
require_once $CFG->dirroot.'/mnet/lib.php';
require_once $CFG->dirroot.'/mnet/remote_client.php';

if ($CFG->mnet_dispatcher_mode === 'off') {
    print_error('mnetdisabled', 'mnet');
}

// Content type for output is not html:
header('Content-type: text/xml; charset=utf-8');

// PHP 5.2.2: $HTTP_RAW_POST_DATA not populated bug:
// http://bugs.php.net/bug.php?id=41293
if (empty($HTTP_RAW_POST_DATA)) {
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}

if (!empty($CFG->mnet_rpcdebug)) {
    trigger_error("HTTP_RAW_POST_DATA");
    trigger_error($HTTP_RAW_POST_DATA);
}

if (!isset($_SERVER)) {
    exit(mnet_server_fault(712, "phperror"));
}


// New global variable which ONLY gets set in this server page, so you know that
// if you've been called by a remote Moodle, this should be set:
$MNET_REMOTE_CLIENT = new mnet_remote_client();

$plaintextmessage = mnet_server_strip_encryption($HTTP_RAW_POST_DATA);
$xmlrpcrequest = mnet_server_strip_signature($plaintextmessage);

if($MNET_REMOTE_CLIENT->pushkey == true) {
    // The peer used one of our older public keys, we will return a
    // signed/encrypted error message containing our new public key
    // Sign message with our old key, and encrypt to the peer's private key.
    exit(mnet_server_fault_xml(7025, $MNET->public_key, $MNET_REMOTE_CLIENT->useprivatekey));
}
// Have a peek at what the request would be if we were to process it
$params = xmlrpc_decode_request($xmlrpcrequest, $method);

// One of three conditions need to be met before we continue processing this request:
// 1. Request is properly encrypted and signed
// 2. Request is for a keyswap (we don't mind enencrypted or unsigned requests for a public key)
// 3. Request is properly signed and we're happy with it being unencrypted
if ((($MNET_REMOTE_CLIENT->request_was_encrypted == true) && ($MNET_REMOTE_CLIENT->signatureok == true))
    || (($method == 'system.keyswap') || ($method == 'system/keyswap'))
    || (($MNET_REMOTE_CLIENT->signatureok == true) && ($MNET_REMOTE_CLIENT->plaintext_is_ok() == true))) {
    $response = mnet_server_dispatch($xmlrpcrequest);
} else {
    if (($MNET_REMOTE_CLIENT->request_was_encrypted == false) && ($MNET_REMOTE_CLIENT->plaintext_is_ok() == false)) {
        exit(mnet_server_fault(7021, 'forbidden-transport'));
    }

    if ($MNET_REMOTE_CLIENT->request_was_signed == false) {
        // Request was not signed
        exit(mnet_server_fault(711, 'verifysignature-error'));
    }

    if ($MNET_REMOTE_CLIENT->signatureok == false) {
        // We were unable to verify the signature
        exit(mnet_server_fault(710, 'verifysignature-invalid'));
    }
}


if (!empty($CFG->mnet_rpcdebug)) {
    trigger_error("XMLRPC Payload");
    trigger_error(print_r($xmlrpcrequest,1));
}

/**
 * -----XML-Envelope---------------------------------
 * |                                                |
 * |    Encrypted-Symmetric-key----------------     |
 * |    |_____________________________________|     |
 * |                                                |
 * |    Encrypted data-------------------------     |
 * |    |                                     |     |
 * |    |  -XML-Envelope------------------    |     |
 * |    |  |                             |    |     |
 * |    |  |  --Signature-------------   |    |     |
 * |    |  |  |______________________|   |    |     |
 * |    |  |                             |    |     |
 * |    |  |  --Signed-Payload--------   |    |     |
 * |    |  |  |                      |   |    |     |
 * |    |  |  |   XML-RPC Request    |   |    |     |
 * |    |  |  |______________________|   |    |     |
 * |    |  |                             |    |     |
 * |    |  |_____________________________|    |     |
 * |    |_____________________________________|     |
 * |                                                |
 * |________________________________________________|
 *
 */

/* Strip encryption envelope (if present) and decrypt data
 *
 * @param string $HTTP_RAW_POST_DATA The XML that the client sent
 * @return string XML with any encryption envolope removed
 */
function mnet_server_strip_encryption($HTTP_RAW_POST_DATA) {
    global $MNET, $MNET_REMOTE_CLIENT;
    $crypt_parser = new mnet_encxml_parser();
    $crypt_parser->parse($HTTP_RAW_POST_DATA);

    if (!$crypt_parser->payload_encrypted) {
        return $HTTP_RAW_POST_DATA;
    }

    // Make sure we know who we're talking to
    $host_record_exists = $MNET_REMOTE_CLIENT->set_wwwroot($crypt_parser->remote_wwwroot);

    if (false == $host_record_exists) {
        exit(mnet_server_fault(7020, 'wrong-wwwroot', $crypt_parser->remote_wwwroot));
    }

    // This key is symmetric, and is itself encrypted. Can be decrypted using our private key
    $key  = array_pop($crypt_parser->cipher);
    // This data is symmetrically encrypted, can be decrypted using the above key
    $data = array_pop($crypt_parser->cipher);

    $crypt_parser->free_resource();
    $payload          = '';    // Initialize payload var

    //                                          &$payload
    $isOpen = openssl_open(base64_decode($data), $payload, base64_decode($key), $MNET->get_private_key());
    if ($isOpen) {
        $MNET_REMOTE_CLIENT->was_encrypted();
        return $payload;
    }

    // Decryption failed... let's try our archived keys
    $openssl_history = get_config('mnet', 'openssl_history');
    if(empty($openssl_history)) {
        $openssl_history = array();
        set_config('openssl_history', serialize($openssl_history), 'mnet');
    } else {
        $openssl_history = unserialize($openssl_history);
    }
    foreach($openssl_history as $keyset) {
        $keyresource = openssl_pkey_get_private($keyset['keypair_PEM']);
        $isOpen      = openssl_open(base64_decode($data), $payload, base64_decode($key), $keyresource);
        if ($isOpen) {
            // It's an older code, sir, but it checks out

            $MNET_REMOTE_CLIENT->was_encrypted();
            $MNET_REMOTE_CLIENT->encrypted_to($keyresource);
            $MNET_REMOTE_CLIENT->set_pushkey();
            return $payload;
        }
    }

    //If after all that we still couldn't decrypt the message, error out.
    exit(mnet_server_fault(7023, 'encryption-invalid'));
}

/* Strip signature envelope (if present), try to verify any signature using our record of remote peer's public key.
 *
 * @param string $plaintextmessage XML envelope containing XMLRPC request and signature
 * @return string XMLRPC request
 */
function mnet_server_strip_signature($plaintextmessage) {
    global $MNET, $MNET_REMOTE_CLIENT;
    $sig_parser = new mnet_encxml_parser();
    $sig_parser->parse($plaintextmessage);

    if ($sig_parser->signature == '') {
        return $plaintextmessage;
    }

    // Record that the request was signed in some way
    $MNET_REMOTE_CLIENT->was_signed();

    // Load any information we have about this mnet peer
    $MNET_REMOTE_CLIENT->set_wwwroot($crypt_parser->remote_wwwroot);

    $payload = base64_decode($sig_parser->data_object);
    $signature = base64_decode($sig_parser->signature);
    $certificate = $MNET_REMOTE_CLIENT->public_key;

    // If we don't have any certificate for the host, don't try to check the signature
    // Just return the parsed request
    if ($certificate == false) {
        return $payload;
    }

    // Does the signature match the data and the public cert?
    $signature_verified = openssl_verify($payload, $signature, $certificate);
    if ($signature_verified == 0) {
        // $signature was not generated for $payload using $certificate
        // Get the key the remote peer is currently publishing:
        $currkey = mnet_get_public_key($MNET_REMOTE_CLIENT->wwwroot, $MNET_REMOTE_CLIENT->application);
        // If the key the remote peer is currently publishing is different to $certificate
        if($currkey != $certificate) {
            // Try and get the server's new key through trusted means
            $MNET_REMOTE_CLIENT->refresh_key();
            // If we did manage to re-key, try to verify the signature again using the new public key.
            $certificate = $MNET_REMOTE_CLIENT->public_key;
            $signature_verified = openssl_verify($payload, $signature, $certificate);
        }
    }

    if ($signature_verified == 1) {
        $MNET_REMOTE_CLIENT->signature_verified();
        $MNET_REMOTE_CLIENT->touch();
    }

    $sig_parser->free_resource();

    return $payload;
}

/**
 * Return the proper XML-RPC content to report an error in the local language.
 *
 * @param  int    $code   The ID code of the error message
 * @param  string $text   The array-key of the error message in the lang file
 *                        or the full string (will be detected by the function
 * @param  string $param  The $a param for the error message in the lang file
 * @return string $text   The text of the error message
 */
function mnet_server_fault($code, $text, $param = null) {
    global $MNET_REMOTE_CLIENT;
    if (!is_numeric($code)) {
        $code = 0;
    }
    $code = intval($code);

    $string = get_string($text, 'mnet', $param);
    if (strpos($string, '[[') === 0) {
        $string = $text;
    }

    return mnet_server_fault_xml($code, $string);
}

/**
 * Return the proper XML-RPC content to report an error.
 *
 * @param  int      $code   The ID code of the error message
 * @param  string   $text   The error message
 * @param  resource $privatekey The private key that should be used to sign the response
 * @return string   $text   The XML text of the error message
 */
function mnet_server_fault_xml($code, $text, $privatekey = null) {
    global $MNET_REMOTE_CLIENT, $CFG;
    // Replace illegal XML chars - is this already in a lib somewhere?
    $text = str_replace(array('<','>','&','"',"'"), array('&lt;','&gt;','&amp;','&quot;','&apos;'), $text);

    $return = mnet_server_prepare_response('<?xml version="1.0"?>
<methodResponse>
   <fault>
      <value>
         <struct>
            <member>
               <name>faultCode</name>
               <value><int>'.$code.'</int></value>
            </member>
            <member>
               <name>faultString</name>
               <value><string>'.$text.'</string></value>
            </member>
         </struct>
      </value>
   </fault>
</methodResponse>', $privatekey);

    if (!empty($CFG->mnet_rpcdebug)) {
        trigger_error("XMLRPC Error Response $code: $text");
        trigger_error(print_r($return,1));
    }

    return $return;
}

/**
 * Dummy function for the XML-RPC dispatcher - use to call a method on an object
 * or to call a function
 *
 * Translate XML-RPC's strange function call syntax into a more straightforward
 * PHP-friendly alternative. This dummy function will be called by the
 * dispatcher, and can be used to call a method on an object, or just a function
 * 
 * The methodName argument (eg. mnet/testlib/mnet_concatenate_strings)
 * is ignored.
 *
 * @param  string  $methodname     We discard this - see 'functionname'
 * @param  array   $argsarray      Each element is an argument to the real
 *                                 function
 * @param  string  $functionname   The name of the PHP function you want to call
 * @return mixed                   The return value will be that of the real
 *                                 function, whateber it may be.
 */
function mnet_server_dummy_method($methodname, $argsarray, $functionname) {
    global $MNET_REMOTE_CLIENT;
    
    if (is_object($MNET_REMOTE_CLIENT->object_to_call)) {
        return @call_user_method_array($functionname, $MNET_REMOTE_CLIENT->object_to_call, $argsarray);
    } else if (!empty($MNET_REMOTE_CLIENT->static_location)) {
        return @call_user_func_array(array($MNET_REMOTE_CLIENT->static_location, $functionname), $argsarray);
    } else {
        return @call_user_func_array($functionname, $argsarray);
    }
}

/**
 * Package a response in any required envelope, and return it to the client
 *
 * @param   string   $response      The XMLRPC response string
 * @param   resource $privatekey    The private key to sign the response with
 * @return  string                  The encoded response string
 */
function mnet_server_prepare_response($response, $privatekey = null) {
    global $MNET_REMOTE_CLIENT;

    if ($MNET_REMOTE_CLIENT->request_was_signed) {
        $response = mnet_sign_message($response, $privatekey);
    }

    if ($MNET_REMOTE_CLIENT->request_was_encrypted) {
        $response = mnet_encrypt_message($response, $MNET_REMOTE_CLIENT->public_key);
    }

    return $response;
}

/**
 * If security checks are passed, dispatch the request to the function/method
 *
 * The config variable 'mnet_dispatcher_mode' can be:
 * strict:      Only execute functions that are in specific files
 * off:         The default - don't execute anything
 *
 * @param  string  $payload    The XML-RPC request
 * @return                     No return val - just echo the response
 */
function mnet_server_dispatch($payload) {
    global $CFG, $MNET_REMOTE_CLIENT;
    // xmlrpc_decode_request returns an array of parameters, and the $method
    // variable (which is passed by reference) is instantiated with the value from
    // the methodName tag in the xml payload
    //            xmlrpc_decode_request($xml,                   &$method)
    $params     = xmlrpc_decode_request($payload, $method);

    // $method is something like: "mod/forum/lib.php/forum_add_instance"
    // $params is an array of parameters. A parameter might itself be an array.

    // Whitelist characters that are permitted in a method name
    // The method name must not begin with a / - avoid absolute paths
    // A dot character . is only allowed in the filename, i.e. something.php
    if (0 == preg_match("@^[A-Za-z0-9]+/[A-Za-z0-9/_\.-]+(\.php/)?[A-Za-z0-9_-]+$@",$method)) {
        exit(mnet_server_fault(713, 'nosuchfunction'));
    }

    if(preg_match("/^system\./", $method)) {
        $callstack  = explode('.', $method);
    } else {
        $callstack  = explode('/', $method);
        // callstack will look like array('mod', 'forum', 'lib.php', 'forum_add_instance');
    }

    /**
     * What has the site administrator chosen as his dispatcher setting?
     * strict:      Only execute functions that are in specific files
     * off:         The default - don't execute anything
     */
    ////////////////////////////////////// OFF
    if (!isset($CFG->mnet_dispatcher_mode) ) {
        set_config('mnet_dispatcher_mode', 'off');
        exit(mnet_server_fault(704, 'nosuchservice'));
    } elseif ('off' == $CFG->mnet_dispatcher_mode) {
        exit(mnet_server_fault(704, 'nosuchservice'));

    ////////////////////////////////////// SYSTEM METHODS
    } elseif ($callstack[0] == 'system') {
        $functionname = $callstack[1];
        $xmlrpcserver = xmlrpc_server_create();

        // I'm adding the canonical xmlrpc references here, however we've
        // already forbidden that the period (.) should be allowed in the call
        // stack, so if someone tries to access our XMLRPC in the normal way,
        // they'll already have received a RPC server fault message.

        // Maybe we should allow an easement so that regular XMLRPC clients can
        // call our system methods, and find out what we have to offer?

        xmlrpc_server_register_method($xmlrpcserver, 'system.listMethods', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/listMethods', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.methodSignature', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/methodSignature', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.methodHelp', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/methodHelp', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.listServices', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/listServices', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.listFiles', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/listFiles', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.retrieveFile', 'mnet_system');
        xmlrpc_server_register_method($xmlrpcserver, 'system/retrieveFile', 'mnet_system');

        xmlrpc_server_register_method($xmlrpcserver, 'system.keyswap', 'mnet_keyswap');
        xmlrpc_server_register_method($xmlrpcserver, 'system/keyswap', 'mnet_keyswap');

        if ($method == 'system.listMethods'     ||
            $method == 'system/listMethods'     ||
            $method == 'system.methodSignature' ||
            $method == 'system/methodSignature' ||
            $method == 'system.methodHelp'      ||
            $method == 'system/methodHelp'      ||
            $method == 'system.listServices'    ||
            $method == 'system/listServices'    ||
            $method == 'system.listFiles'       ||
            $method == 'system/listFiles'       ||
            $method == 'system.retrieveFile'    ||
            $method == 'system/retrieveFile'    ||
            $method == 'system.keyswap'         ||
            $method == 'system/keyswap') {

            $response = xmlrpc_server_call_method($xmlrpcserver, $payload, $MNET_REMOTE_CLIENT, array("encoding" => "utf-8"));
            $response = mnet_server_prepare_response($response);
        } else {
            exit(mnet_server_fault(7018, 'nosuchfunction'));
        }

        xmlrpc_server_destroy($xmlrpcserver);
        echo $response;
    ////////////////////////////////////// STRICT AUTH
    } elseif ($callstack[0] == 'auth') {

        // Break out the callstack into its elements
        list($base, $plugin, $filename, $methodname) = $callstack;

        // We refuse to include anything that is not auth.php
        if ($filename == 'auth.php' && is_enabled_auth($plugin)) {
            $authclass   = 'auth_plugin_'.$plugin;
            $includefile = '/auth/'.$plugin.'/auth.php';
            $response    = mnet_server_invoke_method($includefile, $methodname, $method, $payload, $authclass);
            $response = mnet_server_prepare_response($response);
            echo $response;
        } else {
            // Generate error response - unable to locate function
            exit(mnet_server_fault(702, 'nosuchfunction'));
        }

    ////////////////////////////////////// STRICT ENROL
    } elseif ($callstack[0] == 'enrol') {

        // Break out the callstack into its elements
        list($base, $plugin, $filename, $methodname) = $callstack;

        if ($filename == 'enrol.php' && is_enabled_enrol($plugin)) {
            $enrolclass  = 'enrolment_plugin_'.$plugin;
            $includefile = '/enrol/'.$plugin.'/enrol.php';
            $response    = mnet_server_invoke_method($includefile, $methodname, $method, $payload, $enrolclass);
            $response = mnet_server_prepare_response($response);
            echo $response;
        } else {
            // Generate error response - unable to locate function
            exit(mnet_server_fault(703, 'nosuchfunction'));
        }

    } else if ($callstack[0] == 'portfolio') {
        // Break out the callstack into its elements
        list($base, $plugin, $filename, $methodname) = $callstack;

        if ($filename == 'lib.php') {
            $pluginclass = 'portfolio_plugin_' . $plugin;
            $includefile = '/portfolio/type/'.$plugin.'/lib.php';
            $response    = mnet_server_invoke_method($includefile, $methodname, $method, $payload, $pluginclass);
            $response = mnet_server_prepare_response($response);
            echo $response;
        } else {
            // Generate error response - unable to locate function
            exit(mnet_server_fault(7012, 'nosuchfunction'));
        }

    } else if ($callstack[0] == 'repository') {
        // Break out the callstack into its elements
        list($base, $plugin, $filename, $methodname) = $callstack;

        if ($filename == 'repository.class.php') {
            $pluginclass = 'repository_' . $plugin;
            $includefile = '/repository/'.$plugin.'/repository.class.php';
            debugging(print_r($includefile,true));
            $response    = mnet_server_invoke_method($includefile, $methodname, $method, $payload, $pluginclass);
            $response = mnet_server_prepare_response($response);
            echo $response;
        } else {
            // Generate error response - unable to locate function
            exit(mnet_server_fault(7012, 'nosuchfunction'));
        }

    ////////////////////////////////////// STRICT MOD/*
    } elseif ($callstack[0] == 'mod' || 'dangerous' == $CFG->mnet_dispatcher_mode) {
        list($base, $module, $filename, $functionname) = $callstack;

    ////////////////////////////////////// STRICT MOD/*
        if ($base == 'mod' && $filename == 'rpclib.php') {
            $includefile = '/mod/'.$module.'/rpclib.php';
            $response    = mnet_server_invoke_method($includefile, $functionname, $method, $payload);
            $response = mnet_server_prepare_response($response);
            echo $response;

    ////////////////////////////////////// DANGEROUS
        } elseif ('dangerous' == $CFG->mnet_dispatcher_mode && $MNET_REMOTE_CLIENT->plaintext_is_ok()) {

            $functionname = array_pop($callstack);

            if ($MNET_REMOTE_CLIENT->plaintext_is_ok()) {

                $filename = clean_param(implode('/',$callstack), PARAM_PATH);
                if (0 == preg_match("/php$/", $filename)) {
                    // Filename doesn't end in 'php'; possible attack?
                    // Generate error response - unable to locate function
                    exit(mnet_server_fault(7012, 'nosuchfunction'));
                } 

                // The call stack holds the path to any include file
                $includefile = $CFG->dirroot.'/'.$filename;

                $response = mnet_server_invoke_method($includefile, $functionname, $method, $payload);
                echo $response;
            }

        } else {
            // Generate error response - unable to locate function
            exit(mnet_server_fault(7012, 'nosuchfunction'));
        }

    } else {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7012, 'nosuchfunction'));
    }
}

/**
 * Execute the system functions - mostly for introspection
 *
 * @param  string  $method    XMLRPC method name, e.g. system.listMethods
 * @param  array   $params    Array of parameters from the XMLRPC request
 * @param  string  $hostinfo  Hostinfo object from the mnet_host table
 * @return mixed              Response data - any kind of PHP variable
 */
function mnet_system($method, $params, $hostinfo) {
    global $CFG, $DB;

    if (empty($hostinfo)) return array();

    $id_list = $hostinfo->id;
    if (!empty($CFG->mnet_all_hosts_id)) {
        $id_list .= ', '.$CFG->mnet_all_hosts_id;
    }

    if ('system.listMethods' == $method || 'system/listMethods' == $method) {
        if (count($params) == 0) {
            $query = '
                SELECT DISTINCT
                    rpc.function_name,
                    rpc.xmlrpc_path,
                    rpc.enabled,
                    rpc.help,
                    rpc.profile
                FROM
                    {mnet_host2service} h2s,
                    {mnet_service2rpc} s2r,
                    {mnet_rpc} rpc
                WHERE
                    s2r.rpcid = rpc.id AND
                    h2s.serviceid = s2r.serviceid AND 
                    h2s.hostid in ('.$id_list .') AND
                    h2s.publish =\'1\'
                ORDER BY
                    rpc.xmlrpc_path ASC';
            $params = array();

        } else {
            $query = '
                SELECT DISTINCT
                    rpc.function_name,
                    rpc.xmlrpc_path,
                    rpc.enabled,
                    rpc.help,
                    rpc.profile
                FROM
                    {mnet_host2service} h2s,
                    {mnet_service2rpc} s2r,
                    {mnet_service} svc,
                    {mnet_rpc} rpc
                WHERE
                    s2r.rpcid = rpc.id AND
                    h2s.serviceid = s2r.serviceid AND 
                    h2s.hostid in ('.$id_list .') AND
                    h2s.publish =\'1\' AND
                    svc.id = h2s.serviceid AND
                    svc.name = ?
                ORDER BY
                    rpc.xmlrpc_path ASC';
            $params = array($params[0]);

        }
        $resultset = array_values($DB->get_records_sql($query, $params));
        $methods = array();
        foreach($resultset as $result) {
            $methods[] = $result->xmlrpc_path;
        }
        return $methods;
    } elseif ('system.methodSignature' == $method || 'system/methodSignature' == $method) {
        $query = '
            SELECT DISTINCT
                rpc.function_name,
                rpc.xmlrpc_path,
                rpc.enabled,
                rpc.help,
                rpc.profile
            FROM
                {mnet_host2service} h2s,
                {mnet_service2rpc} s2r,
                {mnet_rpc rpc}
            WHERE
                rpc.xmlrpc_path = ? AND
                s2r.rpcid = rpc.id AND
                h2s.serviceid = s2r.serviceid AND 
                h2s.publish =\'1\' AND
                h2s.hostid in ('.$id_list .')';
        $params = array($params[0]);

        $result = $DB->get_records_sql($query, $params);
        $methodsigs = array();

        if (is_array($result)) {
            foreach($result as $method) {
                $methodsigs[] = unserialize($method->profile);
            }
        }

        return $methodsigs;
    } elseif ('system.methodHelp' == $method || 'system/methodHelp' == $method) {
        $query = '
            SELECT DISTINCT
                rpc.function_name,
                rpc.xmlrpc_path,
                rpc.enabled,
                rpc.help,
                rpc.profile
            FROM
                {mnet_host2service} h2s,
                {mnet_service2rpc} s2r,
                {mnet_rpc} rpc
            WHERE
                rpc.xmlrpc_path = ? AND
                s2r.rpcid = rpc.id AND
                h2s.publish =\'1\' AND
                h2s.serviceid = s2r.serviceid AND 
                h2s.hostid in ('.$id_list .')';
        $params = array($params[0]);

        $result = $DB->get_record_sql($query, $params);

        if (is_object($result)) {
            return $result->help;
        }
    } elseif ('system.listServices' == $method || 'system/listServices' == $method) {
        $query = '
            SELECT DISTINCT
                s.id,
                s.name,
                s.apiversion,
                h2s.publish,
                h2s.subscribe
            FROM
                {mnet_host2service} h2s,
                {mnet_service} s
            WHERE
                h2s.serviceid = s.id AND
               (h2s.publish =\'1\' OR h2s.subscribe =\'1\') AND
                h2s.hostid in ('.$id_list .')
            ORDER BY
                s.name ASC';
        $params = array();

        $result = $DB->get_records_sql($query, $params);
        $services = array();

        if (is_array($result)) {
            foreach($result as $service) {
                $services[] = array('name' => $service->name, 
                                    'apiversion' => $service->apiversion, 
                                    'publish' => $service->publish, 
                                    'subscribe' => $service->subscribe);
            }
        }

        return $services;
    } elseif ('system/retrieveFile' == $method) {
        global $DB, $USER;

        $USER = $DB->get_record('user',array('username' => $params[0], 'mnethostid' => $hostinfo->id));
        $pathnamehash = $params[1];
        $fs = get_file_storage();
    
        $sf = $fs->get_file_by_hash($pathnamehash);
     
        $contents = base64_encode($sf->get_content());
    
        return array($contents, $sf->get_filename());
    } elseif ('system/listFiles' == $method) {

        global $DB, $USER;
      
        $USER = $DB->get_record('user',array('username' => $params[0], 'mnethostid' => $hostinfo->id));

        $ret = array();
        $search = '';
        // no login required
        $ret['nologin'] = true;
        // todo: link to file manager
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary

        $browser = get_file_browser();
        $itemid = null;
        $filename = null;
        $filearea = null;
        $path = '/';
        $ret['dynload'] = false;

        if ($fileinfo = $browser->get_file_info(get_system_context(), $filearea, $itemid, $path, $filename)) {
            
            $ret['path'] = array();
            $params = $fileinfo->get_params();
            $filearea = $params['filearea'];
            //todo: fix this call, and similar ones here and in build_tree - encoding path works only for real folders
            $ret['path'][] = _encode_path($filearea, $path, $fileinfo->get_visible_name());
            if ($fileinfo->is_directory()) {
                $level = $fileinfo->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $ret['path'][] = _encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }
            }
            $filecount = build_tree($fileinfo, $search, $ret['dynload'], $ret['list']);
            $ret['path'] = array_reverse($ret['path']);
        } else {
            // throw some "context/filearea/item/path/file not found" exception?
        }

        if (empty($ret['list'])) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        } else {   
                return $ret;       
        }
    }
    exit(mnet_server_fault(7019, 'nosuchfunction'));
}

 /**
     *
     * @param <type> $filearea
     * @param <type> $path
     * @param <type> $visiblename
     * @return <type>
     */
    function _encode_path($filearea, $path, $visiblename) {
        return array('path'=>serialize(array($filearea, $path)), 'name'=>$visiblename);
    }

 /**
     * Builds a tree of files, to be used by get_listing(). This function is
     * then called recursively.
     *
     * @param $fileinfo an object returned by file_browser::get_file_info()
     * @param $search searched string
     * @param $dynamicmode bool no recursive call is done when in dynamic mode
     * @param $list - the array containing the files under the passed $fileinfo
     * @returns int the number of files found
     *
     * todo: take $search into account, and respect a threshold for dynamic loading
     */
    function build_tree($fileinfo, $search, $dynamicmode, &$list) {
        global $CFG;
    
        $filecount = 0;
        $children = $fileinfo->get_children();
   
        foreach ($children as $child) {
            $filename = $child->get_visible_name();
            $filesize = $child->get_filesize();
            $filesize = $filesize ? display_size($filesize) : '';
            $filedate = $child->get_timemodified();
            $filedate = $filedate ? userdate($filedate) : '';
            $filetype = $child->get_mimetype();

            if ($child->is_directory()) {
                $path = array();
                $level = $child->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $path[] = _encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }

                $tmp = array(
                    'title' => $child->get_visible_name(),
                    'size' => 0,
                    'date' => $filedate,
                    'path' => array_reverse($path),
                    'thumbnail' => $CFG->pixpath .'/f/folder.gif'
                );

              
                    $_search = $search;
                    if ($search && stristr($tmp['title'], $search) !== false) {
                        $_search = false;
                    }
                    $tmp['children'] = array();
                    $_filecount = build_tree($child, $_search, $dynamicmode, $tmp['children']);
                    if ($search && $_filecount) {
                        $tmp['expanded'] = 1;
                    }

             

                if (!$search || $_filecount || (stristr($tmp['title'], $search) !== false)) {
                    $list[] = $tmp;
                    $filecount += $_filecount;
                }

            } else { // not a directory
                // skip the file, if we're in search mode and it's not a match
                if ($search && (stristr($filename, $search) === false)) {
                    continue;
                }

                //retrieve the stored file id
                  $fs = get_file_storage();
                  $params = $child->get_params();
              
                  $pathnamehash = $fs->get_pathname_hash($params['contextid'], $params['filearea'], $params['itemid'], $params['filepath'], $params['filename']);
   

                $list[] = array(
                    'title' => $filename,
                    'size' => $filesize,
                    'date' => $filedate,          
                   'source' => $pathnamehash,
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo_from_type("icon", $filetype)
                );
            
                $filecount++;
            }
        }

        return $filecount;
    }

/**
 * Initialize the object (if necessary), execute the method or function, and 
 * return the response
 *
 * @param  string  $includefile    The file that contains the object definition
 * @param  string  $methodname     The name of the method to execute
 * @param  string  $method         The full path to the method
 * @param  string  $payload        The XML-RPC request payload
 * @param  string  $class          The name of the class to instantiate (or false)
 * @return string                  The XML-RPC response
 */
function mnet_server_invoke_method($includefile, $methodname, $method, $payload, $class=false) {

    $permission = mnet_permit_rpc_call($includefile, $methodname, $class);

    if (RPC_NOSUCHFILE == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(705, 'nosuchfile', $includefile));
    }

    if (RPC_NOSUCHFUNCTION == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(706, 'nosuchfunction'));
    }

    if (RPC_FORBIDDENFUNCTION == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(707, 'forbidden-function'));
    }

    if (RPC_NOSUCHCLASS == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7013, 'nosuchfunction'));
    }

    if (RPC_NOSUCHMETHOD == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7014, 'nosuchmethod'));
    }

    if (RPC_NOSUCHFUNCTION == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7014, 'nosuchmethod'));
    }

    if (RPC_FORBIDDENMETHOD == $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7015, 'nosuchfunction'));
    }

    if (0 < $permission) {
        // Generate error response - unable to locate function
        exit(mnet_server_fault(7019, 'unknownerror'));
    }

    if (RPC_OK == $permission) {
        $xmlrpcserver = xmlrpc_server_create();
        $bool = xmlrpc_server_register_method($xmlrpcserver, $method, 'mnet_server_dummy_method');
        $response = xmlrpc_server_call_method($xmlrpcserver, $payload, $methodname, array("encoding" => "utf-8"));
        $bool = xmlrpc_server_destroy($xmlrpcserver);
        return $response;
    }
}

/**
 * Accepts a public key from a new remote host and returns the public key for
 * this host. If 'register all hosts' is turned on, it will bootstrap a record
 * for the remote host in the mnet_host table (if it's not already there)
 * 
 * @param  string  $function      XML-RPC requires this but we don't... discard!
 * @param  array   $params        Array of parameters
 *                                $params[0] is the remote wwwroot
 *                                $params[1] is the remote public key
 * @return string                 The XML-RPC response
 */
function mnet_keyswap($function, $params) {
    global $CFG, $MNET;
    $return = array();

    if (!empty($CFG->mnet_register_allhosts)) {
        $mnet_peer = new mnet_peer();
        @list($wwwroot, $pubkey, $application) = each($params);
        $keyok = $mnet_peer->bootstrap($wwwroot, $pubkey, $application);
        if ($keyok) {
            $mnet_peer->commit();
        }
    }
    return $MNET->public_key;
}

?>
