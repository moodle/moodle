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

// New global variable which ONLY gets set in this server page, so you know that
// if you've been called by a remote Moodle, this should be set:
$MNET_REMOTE_CLIENT = new mnet_remote_client();

// Peek at the message to see if it's an XML-ENC document. If it is, note that
// the client connection was encrypted, and strip the xml-encryption and
// xml-signature wrappers from the XML-RPC payload 
if (strpos(substr($HTTP_RAW_POST_DATA, 0, 100), '<encryptedMessage>')) {
    $MNET_REMOTE_CLIENT->was_encrypted();
// Extract the XML-RPC payload from the XML-ENC and XML-SIG wrappers.
    $payload = mnet_server_strip_wrappers($HTTP_RAW_POST_DATA);
} else {
    $params = xmlrpc_decode_request($HTTP_RAW_POST_DATA, $method);
    if ($method == 'system.keyswap'      ||
        $method == 'system/keyswap') {
        
        // OK
        
    } elseif ($MNET_REMOTE_CLIENT->plaintext_is_ok() == false) {
        exit(mnet_server_fault(7021, 'forbidden-transport'));
    }
    // Looks like plaintext is ok. It is assumed that a plaintext call:
    //   1. Came from a trusted host on your local network
    //   2. Is *not* from a Moodle - otherwise why skip encryption/signing?
    //   3. Is free to execute ANY function in Moodle
    //   4. Cannot execute any methods (as it can't instantiate a class first)
    // To execute a method, you'll need to create a wrapper function that first
    // instantiates the class, and then calls the method.
    $payload  = $HTTP_RAW_POST_DATA;
}

if (!empty($CFG->mnet_rpcdebug)) {
    trigger_error("XMLRPC Payload");
    trigger_error(print_r($payload,1));
}

// Parse and action the XML-RPC payload
$response = mnet_server_dispatch($payload);

/**
 * Strip the encryption (XML-ENC) and signature (XML-SIG) wrappers and return the XML-RPC payload
 *
 * IF COMMUNICATION TAKES PLACE OVER UNENCRYPTED HTTP:
 * The payload will have been encrypted with a symmetric key. This key will
 * itself have been encrypted using your public key. The key is decrypted using
 * your private key, and then used to decrypt the XML payload.
 *
 * IF COMMUNICATION TAKES PLACE OVER UNENCRYPTED HTTP *OR* ENCRYPTED HTTPS:
 * In either case, there will be an XML wrapper which contains your XML-RPC doc
 * as an object element, a signature for that doc, and various standards-
 * compliant info to aid in verifying the signature.
 *
 * This function parses the encryption wrapper, decrypts the contents, parses
 * the signature wrapper, and if the signature matches the payload, it returns
 * the payload, which should be an XML-RPC request.
 * If there is an error, or the signatures don't match, it echoes an XML-RPC
 * error and exits.
 *
 * See the W3C's {@link http://www.w3.org/TR/xmlenc-core/ XML Encryption Syntax and Processing}
 * and {@link http://www.w3.org/TR/2001/PR-xmldsig-core-20010820/ XML-Signature Syntax and Processing}
 * guidelines for more detail on the XML.
 *
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
 * @uses $db
 * @param   string  $HTTP_RAW_POST_DATA   The XML that the client sent
 * @return  string                        The XMLRPC payload.
 */
function mnet_server_strip_wrappers($HTTP_RAW_POST_DATA) {
    global $MNET, $MNET_REMOTE_CLIENT;
    if (isset($_SERVER)) {

        $crypt_parser = new mnet_encxml_parser();
        $crypt_parser->parse($HTTP_RAW_POST_DATA);

        // Make sure we know who we're talking to
        $host_record_exists = $MNET_REMOTE_CLIENT->set_wwwroot($crypt_parser->remote_wwwroot);

        if (false == $host_record_exists) {
            exit(mnet_server_fault(7020, 'wrong-wwwroot', $crypt_parser->remote_wwwroot));
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != $MNET_REMOTE_CLIENT->ip_address) {
            exit(mnet_server_fault(7017, 'wrong-ip'));
        }

        if ($crypt_parser->payload_encrypted) {

            $key  = array_pop($crypt_parser->cipher);  // This key is Symmetric
            $data = array_pop($crypt_parser->cipher);

            $crypt_parser->free_resource();

            $payload          = '';    // Initialize payload var
            $push_current_key = false; // True if we need to push a fresh key to the peer

            //                                          &$payload
            $isOpen = openssl_open(base64_decode($data), $payload, base64_decode($key), $MNET->get_private_key());

            if (!$isOpen) {
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
                        $push_current_key = true;
                    }
                }
            }

            if (!$isOpen) {
                exit(mnet_server_fault(7023, 'encryption-invalid'));
            }

            if (strpos(substr($payload, 0, 100), '<signedMessage>')) {
                $MNET_REMOTE_CLIENT->was_signed();
                $sig_parser = new mnet_encxml_parser();
                $sig_parser->parse($payload);
            } else {
                exit(mnet_server_fault(7022, 'verifysignature-error'));
            }

        } else {
            exit(mnet_server_fault(7024, 'payload-not-encrypted'));
        }

        unset($payload);

        // if the peer used one of our public keys that have expired, we will
        // return a signed/encrypted error message with our new public key 
        if($push_current_key) {
            // NOTE: Here, we use the 'mnet_server_fault_xml' to avoid
            // get_string being called on our public_key
            exit(mnet_server_fault_xml(7025, $MNET->public_key));
        }

        /**
         * Get the certificate (i.e. public key) from the remote server.
         */
        $certificate = $MNET_REMOTE_CLIENT->public_key;

        if ($certificate == false) {
            exit(mnet_server_fault(709, 'nosuchpublickey'));
        }

        $payload = base64_decode($sig_parser->data_object);

        // Does the signature match the data and the public cert?
        $signature_verified = openssl_verify($payload, base64_decode($sig_parser->signature), $certificate);
        if ($signature_verified == 1) {
            $MNET_REMOTE_CLIENT->touch();
            // Parse the XML
        } elseif ($signature_verified == 0) {
            $currkey = mnet_get_public_key($MNET_REMOTE_CLIENT->wwwroot, $MNET_REMOTE_CLIENT->application->xmlrpc_server_url);
            if($currkey != $certificate) {
                // Has the server updated its certificate since our last 
                // handshake?
                if(!$MNET_REMOTE_CLIENT->refresh_key()) {
                    exit(mnet_server_fault(7026, 'verifysignature-invalid'));
                }
            } else {
                exit(mnet_server_fault(710, 'verifysignature-invalid'));
            }
        } else {
            exit(mnet_server_fault(711, 'verifysignature-error'));
        }

        $sig_parser->free_resource();

        return $payload;
    } else {
        exit(mnet_server_fault(712, "phperror"));
    }
}

/**
 * Return the proper XML-RPC content to report an error in the local language.
 *
 * @param  int    $code   The ID code of the error message
 * @param  string $text   The array-key of the error message in the lang file
 * @param  string $param  The $a param for the error message in the lang file
 * @return string $text   The text of the error message
 */
function mnet_server_fault($code, $text, $param = null) {
    global $MNET_REMOTE_CLIENT;
    if (!is_numeric($code)) {
        $code = 0;
    }
    $code = intval($code);

    $text = get_string($text, 'mnet', $param);
    return mnet_server_fault_xml($code, $text);
}

/**
 * Return the proper XML-RPC content to report an error.
 *
 * @param  int    $code   The ID code of the error message
 * @param  string $text   The error message
 * @return string $text   The XML text of the error message
 */
function mnet_server_fault_xml($code, $text) {
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
</methodResponse>');

    if (!empty($CFG->mnet_rpcdebug)) {
        trigger_error("XMLRPC Error Response");
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
    
    if (!is_object($MNET_REMOTE_CLIENT->object_to_call)) {
        return @call_user_func_array($functionname, $argsarray);
    } else {
        return @call_user_method_array($functionname, $MNET_REMOTE_CLIENT->object_to_call, $argsarray);
    }
}

/**
 * Package a response in any required envelope, and return it to the client
 *
 * @param   string  $response      The XMLRPC response string
 * @return  string                 The encoded response string
 */
function mnet_server_prepare_response($response) {
    global $MNET_REMOTE_CLIENT;

    if ($MNET_REMOTE_CLIENT->request_was_signed) {
        $response = mnet_sign_message($response);
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

    // $method is something like: "mod/forum/lib/forum_add_instance"
    // $params is an array of parameters. A parameter might itself be an array.

    // Whitelist characters that are permitted in a method name
    // The method name must not begin with a / - avoid absolute paths
    // A dot character . is only allowed in the filename, i.e. something.php
    if (0 == preg_match("@^[A-Za-z0-9]+/[A-Za-z0-9/_-]+(\.php/)?[A-Za-z0-9_-]+$@",$method)) {
        exit(mnet_server_fault(713, 'nosuchfunction'));
    }

    $callstack  = explode('/', $method);
    // callstack will look like array('mod', 'forum', 'lib', 'forum_add_instance');

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

        xmlrpc_server_register_method($xmlrpcserver, 'system.keyswap', 'mnet_keyswap');
        xmlrpc_server_register_method($xmlrpcserver, 'system/keyswap', 'mnet_keyswap');

        if ($method == 'system.listMethods'     ||
            $method == 'system/listMethods'     ||
            $method == 'system.methodSignature' ||
            $method == 'system/methodSignature' ||
            $method == 'system.methodHelp'      ||
            $method == 'system/methodHelp'      ||
            $method == 'system.listServices'      ||
            $method == 'system/listServices'      ||
            $method == 'system.keyswap'      ||
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

    ////////////////////////////////////// STRICT MOD/*
    } elseif ($callstack[0] == 'mod' || 'promiscuous' == $CFG->mnet_dispatcher_mode) {
        list($base, $module, $filename, $functionname) = $callstack;

    ////////////////////////////////////// STRICT MOD/*
        if ($base == 'mod' && $filename == 'rpclib.php') {
            $includefile = '/mod/'.$module.'/rpclib.php';
            $response    = mnet_server_invoke_method($includefile, $functionname, $method, $payload);
            $response = mnet_server_prepare_response($response);
            echo $response;

    ////////////////////////////////////// PROMISCUOUS
        } elseif ('promiscuous' == $CFG->mnet_dispatcher_mode && $MNET_REMOTE_CLIENT->plaintext_is_ok()) {

            $functionname = array_pop($callstack);
            $filename     = array_pop($callstack);

            if ($MNET_REMOTE_CLIENT->plaintext_is_ok()) {

                // The call stack holds the path to any include file
                $includefile = $CFG->dirroot.'/'.implode('/',$callstack).'/'.$filename.'.php';

                $response = mnet_server_invoke_function($includefile, $functionname, $method, $payload);
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
    global $CFG;

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
                    '.$CFG->prefix.'mnet_host2service h2s,
                    '.$CFG->prefix.'mnet_service2rpc s2r,
                    '.$CFG->prefix.'mnet_rpc rpc
                WHERE
                    s2r.rpcid = rpc.id AND
                    h2s.serviceid = s2r.serviceid AND 
                    h2s.hostid in ('.$id_list .')
                ORDER BY
                    rpc.xmlrpc_path ASC';

        } else {
            $query = '
                SELECT DISTINCT
                    rpc.function_name,
                    rpc.xmlrpc_path,
                    rpc.enabled,
                    rpc.help,
                    rpc.profile
                FROM
                    '.$CFG->prefix.'mnet_host2service h2s,
                    '.$CFG->prefix.'mnet_service2rpc s2r,
                    '.$CFG->prefix.'mnet_service svc,
                    '.$CFG->prefix.'mnet_rpc rpc
                WHERE
                    s2r.rpcid = rpc.id AND
                    h2s.serviceid = s2r.serviceid AND 
                    h2s.hostid in ('.$id_list .') AND
                    svc.id = h2s.serviceid AND
                    svc.name = \''.$params[0].'\'
                ORDER BY
                    rpc.xmlrpc_path ASC';

        }
        $resultset = array_values(get_records_sql($query));
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
                '.$CFG->prefix.'mnet_host2service h2s,
                '.$CFG->prefix.'mnet_service2rpc s2r,
                '.$CFG->prefix.'mnet_rpc rpc
            WHERE
                rpc.xmlrpc_path = \''.$params[0].'\' AND
                s2r.rpcid = rpc.id AND
                h2s.serviceid = s2r.serviceid AND 
                h2s.hostid in ('.$id_list .')';

        $result = get_records_sql($query);
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
                '.$CFG->prefix.'mnet_host2service h2s,
                '.$CFG->prefix.'mnet_service2rpc s2r,
                '.$CFG->prefix.'mnet_rpc rpc
            WHERE
                rpc.xmlrpc_path = \''.$params[0].'\' AND
                s2r.rpcid = rpc.id AND
                h2s.serviceid = s2r.serviceid AND 
                h2s.hostid in ('.$id_list .')';

        $result = get_record_sql($query);

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
                '.$CFG->prefix.'mnet_host2service h2s,
                '.$CFG->prefix.'mnet_service s
            WHERE
                h2s.serviceid = s.id AND
                h2s.hostid in ('.$id_list .')
            ORDER BY
                s.name ASC';

        $result = get_records_sql($query);
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
    }
    exit(mnet_server_fault(7019, 'nosuchfunction'));
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
