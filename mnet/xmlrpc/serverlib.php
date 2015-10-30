<?php

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
 *
 * @throws mnet_server_exception
 *
 * @return string XML with any encryption envolope removed
 */
function mnet_server_strip_encryption($HTTP_RAW_POST_DATA) {
    $remoteclient = get_mnet_remote_client();
    $crypt_parser = new mnet_encxml_parser();
    $crypt_parser->parse($HTTP_RAW_POST_DATA);
    $mnet = get_mnet_environment();

    if (!$crypt_parser->payload_encrypted) {
        return $HTTP_RAW_POST_DATA;
    }

    // Make sure we know who we're talking to
    $host_record_exists = $remoteclient->set_wwwroot($crypt_parser->remote_wwwroot);

    if (false == $host_record_exists) {
        throw new mnet_server_exception(7020, 'wrong-wwwroot', $crypt_parser->remote_wwwroot);
    }

    // This key is symmetric, and is itself encrypted. Can be decrypted using our private key
    $key  = array_pop($crypt_parser->cipher);
    // This data is symmetrically encrypted, can be decrypted using the above key
    $data = array_pop($crypt_parser->cipher);

    $crypt_parser->free_resource();
    $payload          = '';    // Initialize payload var

    //                                          &$payload
    $isOpen = openssl_open(base64_decode($data), $payload, base64_decode($key), $mnet->get_private_key());
    if ($isOpen) {
        $remoteclient->was_encrypted();
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
            $remoteclient->was_encrypted();
            $remoteclient->encrypted_to($keyresource);
            $remoteclient->set_pushkey();
            return $payload;
        }
    }

    //If after all that we still couldn't decrypt the message, error out.
    throw new mnet_server_exception(7023, 'encryption-invalid');
}

/* Strip signature envelope (if present), try to verify any signature using our record of remote peer's public key.
 *
 * @param string $plaintextmessage XML envelope containing XMLRPC request and signature
 *
 * @return string XMLRPC request
 */
function mnet_server_strip_signature($plaintextmessage) {
    $remoteclient = get_mnet_remote_client();
    $sig_parser = new mnet_encxml_parser();
    $sig_parser->parse($plaintextmessage);

    if ($sig_parser->signature == '') {
        return $plaintextmessage;
    }

    // Record that the request was signed in some way
    $remoteclient->was_signed();

    // Load any information we have about this mnet peer
    $remoteclient->set_wwwroot($sig_parser->remote_wwwroot);

    $payload = base64_decode($sig_parser->data_object);
    $signature = base64_decode($sig_parser->signature);
    $certificate = $remoteclient->public_key;

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
        $currkey = mnet_get_public_key($remoteclient->wwwroot, $remoteclient->application);
        // If the key the remote peer is currently publishing is different to $certificate
        if($currkey != $certificate) {
            // if pushkey is already set, it means the request was encrypted to an old key
            // in mnet_server_strip_encryption.
            // if we call refresh_key() here before pushing out our new key,
            // and the other site ALSO has a new key,
            // we'll get into an infinite keyswap loop
            // so push just bail here, and push out the new key.
            // the next request will get through to refresh_key
            if ($remoteclient->pushkey) {
                return false;
            }
            // Try and get the server's new key through trusted means
            $remoteclient->refresh_key();
            // If we did manage to re-key, try to verify the signature again using the new public key.
            $certificate = $remoteclient->public_key;
            $signature_verified = openssl_verify($payload, $signature, $certificate);
        }
    }

    if ($signature_verified == 1) {
        $remoteclient->signature_verified();
        $remoteclient->touch();
    }

    $sig_parser->free_resource();

    return $payload;
}

/**
 * Return the proper XML-RPC content to report an error in the local language.
 *
 * @param  int    $code   The ID code of the error message
 * @param  string $text   The full string of the error message (get_string will <b>not be called</b>)
 * @param  string $param  The $a param for the error message in the lang file
 * @return string $text   The text of the error message
 */
function mnet_server_fault($code, $text, $param = null) {
    if (!is_numeric($code)) {
        $code = 0;
    }
    $code = intval($code);
    return mnet_server_fault_xml($code, $text);
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
    global $CFG;
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

    if ($code != 7025) { // new key responses
        mnet_debug("XMLRPC Error Response $code: $text");
        //mnet_debug($return);
    }

    return $return;
}


/**
 * Package a response in any required envelope, and return it to the client
 *
 * @param   string   $response      The XMLRPC response string
 * @param   resource $privatekey    The private key to sign the response with
 * @return  string                  The encoded response string
 */
function mnet_server_prepare_response($response, $privatekey = null) {
    $remoteclient = get_mnet_remote_client();
    if ($remoteclient->request_was_signed) {
        $response = mnet_sign_message($response, $privatekey);
    }

    if ($remoteclient->request_was_encrypted) {
        $response = mnet_encrypt_message($response, $remoteclient->public_key);
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
 *
 * @throws mnet_server_exception
 *
 * @return                     No return val - just echo the response
 */
function mnet_server_dispatch($payload) {
    global $CFG, $DB;
    $remoteclient = get_mnet_remote_client();
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
        throw new mnet_server_exception(713, 'nosuchfunction');
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
        throw new mnet_server_exception(704, 'nosuchservice');
    } elseif ('off' == $CFG->mnet_dispatcher_mode) {
        throw new mnet_server_exception(704, 'nosuchservice');

    ////////////////////////////////////// SYSTEM METHODS
    } elseif ($callstack[0] == 'system') {
        $functionname = $callstack[1];
        $xmlrpcserver = xmlrpc_server_create();

        // register all the system methods
        $systemmethods = array('listMethods', 'methodSignature', 'methodHelp', 'listServices', 'listFiles', 'retrieveFile', 'keyswap');
        foreach ($systemmethods as $m) {
            // I'm adding the canonical xmlrpc references here, however we've
            // already forbidden that the period (.) should be allowed in the call
            // stack, so if someone tries to access our XMLRPC in the normal way,
            // they'll already have received a RPC server fault message.

            // Maybe we should allow an easement so that regular XMLRPC clients can
            // call our system methods, and find out what we have to offer?
            $handler = 'mnet_system';
            if ($m == 'keyswap') {
                $handler = 'mnet_keyswap';
            }
            if ($method == 'system.' . $m || $method == 'system/' . $m) {
                xmlrpc_server_register_method($xmlrpcserver, $method, $handler);
                $response = xmlrpc_server_call_method($xmlrpcserver, $payload, $remoteclient, array("encoding" => "utf-8"));
                $response = mnet_server_prepare_response($response);
                echo $response;
                xmlrpc_server_destroy($xmlrpcserver);
                return;
            }
        }
        throw new mnet_server_exception(7018, 'nosuchfunction');

    ////////////////////////////////////  NORMAL PLUGIN DISPATCHER
    } else {
        // anything else comes from some sort of plugin
        if ($rpcrecord = $DB->get_record('mnet_rpc', array('xmlrpcpath' => $method))) {
            $response    = mnet_server_invoke_plugin_method($method, $callstack, $rpcrecord, $payload);
            $response = mnet_server_prepare_response($response);
            echo $response;
            return;
    // if the rpc record isn't found, check to see if dangerous mode is on
    ////////////////////////////////////// DANGEROUS
        } else if ('dangerous' == $CFG->mnet_dispatcher_mode && $remoteclient->plaintext_is_ok()) {
            $functionname = array_pop($callstack);

            $filename = clean_param(implode('/',$callstack), PARAM_PATH);
            if (0 == preg_match("/php$/", $filename)) {
                // Filename doesn't end in 'php'; possible attack?
                // Generate error response - unable to locate function
                throw new mnet_server_exception(7012, 'nosuchfunction');
            }

            // The call stack holds the path to any include file
            $includefile = $CFG->dirroot.'/'.$filename;

            $response = mnet_server_invoke_dangerous_method($includefile, $functionname, $method, $payload);
            echo $response;
            return;
        }
    }
    throw new mnet_server_exception(7012, 'nosuchfunction');
}

/**
 * Execute the system functions - mostly for introspection
 *
 * @param  string  $method    XMLRPC method name, e.g. system.listMethods
 * @param  array   $params    Array of parameters from the XMLRPC request
 * @param  string  $hostinfo  Hostinfo object from the mnet_host table
 *
 * @throws mnet_server_exception
 *
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
        $query = '
            SELECT DISTINCT
                rpc.functionname,
                rpc.xmlrpcpath
            FROM
                {mnet_host2service} h2s
                JOIN {mnet_service2rpc} s2r ON h2s.serviceid = s2r.serviceid
                JOIN {mnet_rpc} rpc ON s2r.rpcid = rpc.id
                JOIN {mnet_service} svc ON svc.id = s2r.serviceid
            WHERE
                h2s.hostid in ('.$id_list .') AND
                h2s.publish = 1 AND rpc.enabled = 1
               ' . ((count($params) > 0) ?  'AND svc.name = ? ' : '') . '
            ORDER BY
                rpc.xmlrpcpath ASC';
        if (count($params) > 0) {
            $params = array($params[0]);
        }
        $methods = array();
        foreach ($DB->get_records_sql($query, $params) as $result) {
            $methods[] = $result->xmlrpcpath;
        }
        return $methods;
    } elseif (in_array($method, array('system.methodSignature', 'system/methodSignature', 'system.methodHelp', 'system/methodHelp'))) {
        $query = '
            SELECT DISTINCT
                rpc.functionname,
                rpc.help,
                rpc.profile
            FROM
                {mnet_host2service} h2s,
                {mnet_service2rpc} s2r,
                {mnet_rpc} rpc
            WHERE
                rpc.xmlrpcpath = ? AND
                s2r.rpcid = rpc.id AND
                h2s.publish = 1 AND rpc.enabled = 1 AND
                h2s.serviceid = s2r.serviceid AND
                h2s.hostid in ('.$id_list .')';
        $params = array($params[0]);

        if (!$result = $DB->get_record_sql($query, $params)) {
            return false;
        }
        if (strpos($method, 'methodSignature') !== false) {
            return unserialize($result->profile);
        }
        return $result->help;
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
               (h2s.publish = 1 OR h2s.subscribe = 1) AND
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
    }
    throw new mnet_server_exception(7019, 'nosuchfunction');
}

/**
 * Invoke a normal style plugin method
 * This will verify permissions first.
 *
 * @param string   $method the full xmlrpc method that was called eg auth/mnet/auth.php/user_authorise
 * @param array    $callstack  the exploded callstack
 * @param stdclass $rpcrecord  the record from mnet_rpc
 *
 * @return mixed the response from the invoked method
 */
function mnet_server_invoke_plugin_method($method, $callstack, $rpcrecord, $payload) {
    mnet_verify_permissions($rpcrecord); // will throw exceptions
    mnet_setup_dummy_method($method, $callstack, $rpcrecord);
    $methodname = array_pop($callstack);

    $xmlrpcserver = xmlrpc_server_create();
    xmlrpc_server_register_method($xmlrpcserver, $method, 'mnet_server_dummy_method');
    $response = xmlrpc_server_call_method($xmlrpcserver, $payload, $methodname, array("encoding" => "utf-8"));
    xmlrpc_server_destroy($xmlrpcserver);
    return $response;
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
 *
 * @throws mnet_server_exception
 *
 * @return string                  The XML-RPC response
 */
function mnet_server_invoke_dangerous_method($includefile, $methodname, $method, $payload) {

    if (file_exists($CFG->dirroot . $includefile)) {
        require_once $CFG->dirroot . $includefile;
        // $callprefix matches the rpc convention
        // of not having a leading slash
        $callprefix = preg_replace('!^/!', '', $includefile);
    } else {
        throw new mnet_server_exception(705, "nosuchfile");
    }

    if ($functionname != clean_param($functionname, PARAM_PATH)) {
        throw new mnet_server_exception(7012, "nosuchfunction");
    }

    if (!function_exists($functionname)) {
        throw new mnet_server_exception(7012, "nosuchfunction");
    }
    $xmlrpcserver = xmlrpc_server_create();
    xmlrpc_server_register_method($xmlrpcserver, $method, 'mnet_server_dummy_method');
    $response = xmlrpc_server_call_method($xmlrpcserver, $payload, $methodname, array("encoding" => "utf-8"));
    xmlrpc_server_destroy($xmlrpcserver);
    return $response;
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
    global $CFG;
    $return = array();
    $mnet = get_mnet_environment();

    if (!empty($CFG->mnet_register_allhosts)) {
        $mnet_peer = new mnet_peer();
        @list($wwwroot, $pubkey, $application) = each($params);
        $keyok = $mnet_peer->bootstrap($wwwroot, $pubkey, $application);
        if ($keyok) {
            $mnet_peer->commit();
        }
    }
    return $mnet->public_key;
}

/**
 * Verify that the requested xmlrpc method can be called
 * This just checks the method exists in the rpc table and is enabled.
 *
 * @param stdclass $rpcrecord  the record from mnet_rpc
 *
 * @throws mnet_server_exception
 */
function mnet_verify_permissions($rpcrecord) {
    global $CFG, $DB;
    $remoteclient = get_mnet_remote_client();

    $id_list = $remoteclient->id;
    if (!empty($CFG->mnet_all_hosts_id)) {
        $id_list .= ', '.$CFG->mnet_all_hosts_id;
    }

    $sql = "SELECT
            r.*, h2s.publish
        FROM
            {mnet_rpc} r
            JOIN {mnet_service2rpc} s2r ON s2r.rpcid = r.id
            LEFT JOIN {mnet_host2service} h2s ON h2s.serviceid = s2r.serviceid
        WHERE
            r.id = ? AND
            h2s.hostid in ($id_list)";

    $params = array($rpcrecord->id);

    if (!$permission = $DB->get_record_sql($sql, $params)) {
        throw new mnet_server_exception(7012, "nosuchfunction");
    } else if (!$permission->publish || !$permission->enabled) {
        throw new mnet_server_exception(707, "nosuchfunction");
    }
}

/**
 * Figure out exactly what needs to be called and stashes it in $remoteclient
 * Does some further verification that the method is callable
 *
 * @param string   $method the full xmlrpc method that was called eg auth/mnet/auth.php/user_authorise
 * @param array    $callstack  the exploded callstack
 * @param stdclass $rpcrecord  the record from mnet_rpc
 *
 * @throws mnet_server_exception
 */
function mnet_setup_dummy_method($method, $callstack, $rpcrecord) {
    global $CFG;
    $remoteclient = get_mnet_remote_client();
    // verify that the callpath in the stack matches our records
    // callstack will look like array('mod', 'forum', 'lib.php', 'forum_add_instance');
    $path = core_component::get_plugin_directory($rpcrecord->plugintype, $rpcrecord->pluginname);
    $path = substr($path, strlen($CFG->dirroot)+1); // this is a bit hacky and fragile, it is not guaranteed that plugins are in dirroot
    array_pop($callstack);
    $providedpath =  implode('/', $callstack);
    if ($providedpath != $path . '/' . $rpcrecord->filename) {
        throw new mnet_server_exception(705, "nosuchfile");
    }
    if (!file_exists($CFG->dirroot . '/' . $providedpath)) {
        throw new mnet_server_exception(705, "nosuchfile");
    }
    require_once($CFG->dirroot . '/' . $providedpath);
    if (!empty($rpcrecord->classname)) {
        if (!class_exists($rpcrecord->classname)) {
            throw new mnet_server_exception(708, 'nosuchclass');
        }
        if (!$rpcrecord->static) {
            try {
                $object = new $rpcrecord->classname;
            } catch (Exception $e) {
                throw new mnet_server_exception(709, "classerror");
            }
            if (!is_callable(array($object, $rpcrecord->functionname))) {
                throw new mnet_server_exception(706, "nosuchfunction");
            }
            $remoteclient->object_to_call($object);
        } else {
            if (!is_callable(array($rpcrecord->classname, $rpcrecord->functionname))) {
                throw new mnet_server_exception(706, "nosuchfunction");
            }
            $remoteclient->static_location($rpcrecord->classname);
        }
    }
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
 * @throws mnet_server_exception
 *
 * @param  string  $methodname     We discard this - see 'functionname'
 * @param  array   $argsarray      Each element is an argument to the real
 *                                 function
 * @param  string  $functionname   The name of the PHP function you want to call
 * @return mixed                   The return value will be that of the real
 *                                 function, whatever it may be.
 */
function mnet_server_dummy_method($methodname, $argsarray, $functionname) {
    $remoteclient = get_mnet_remote_client();
    try {
        if (is_object($remoteclient->object_to_call)) {
            return @call_user_func_array(array($remoteclient->object_to_call,$functionname), $argsarray);
        } else if (!empty($remoteclient->static_location)) {
            return @call_user_func_array(array($remoteclient->static_location, $functionname), $argsarray);
        } else {
            return @call_user_func_array($functionname, $argsarray);
        }
    } catch (Exception $e) {
        exit(mnet_server_fault($e->getCode(), $e->getMessage()));
    }
}
/**
 * mnet server exception.  extends moodle_exception, but takes slightly different arguments.
 * and unlike the rest of moodle, the actual int error code is used.
 * this exception should only be used during an xmlrpc server request, ie, not for client requests.
 */
class mnet_server_exception extends moodle_exception {

    /**
     * @param int    $intcode      the numerical error associated with this fault.  this is <b>not</b> the string errorcode
     * @param string $langkey      the error message in full (<b>get_string will not be used</b>)
     * @param string $module       the language module, defaults to 'mnet'
     * @param mixed  $a            params for get_string
     */
    public function __construct($intcode, $languagekey, $module='mnet', $a=null) {
        parent::__construct($languagekey, $module, '', $a);
        $this->code    = $intcode;

    }
}

