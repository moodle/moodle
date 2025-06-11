<?php
/**
 * An XML-RPC client
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

require_once $CFG->dirroot.'/mnet/lib.php';

/**
 * Class representing an XMLRPC request against a remote machine
 */
class mnet_xmlrpc_client {

    var $method   = '';
    var $params   = array();
    var $timeout  = 60;
    var $error    = array();
    var $response = '';
    var $mnet     = null;

    /**
     * Constructor
     */
    public function __construct() {
        // make sure we've got this set up before we try and do anything else
        $this->mnet = get_mnet_environment();
    }

    /**
     * Allow users to override the default timeout
     * @param   int $timeout    Request timeout in seconds
     * $return  bool            True if param is an integer or integer string
     */
    public function set_timeout($timeout) {
        if (!is_integer($timeout)) {
            if (is_numeric($timeout)) {
                $this->timeout = (integer)$timeout;
                return true;
            }
            return false;
        }
        $this->timeout = $timeout;
        return true;
    }

    /**
     * Set the path to the method or function we want to execute on the remote
     * machine. Examples:
     * mod/scorm/functionname
     * auth/mnet/methodname
     * In the case of auth and enrolment plugins, an object will be created and
     * the method on that object will be called
     */
    public function set_method($xmlrpcpath) {
        if (is_string($xmlrpcpath)) {
            $this->method = $xmlrpcpath;
            $this->params = array();
            return true;
        }
        $this->method = '';
        $this->params = array();
        return false;
    }

    /**
     * Add a parameter to the array of parameters.
     *
     * @param  string  $argument    A transport ID, as defined in lib.php
     * @param  string  $type        The argument type, can be one of:
     *                              i4
     *                              i8
     *                              int
     *                              double
     *                              string
     *                              boolean
     *                              datetime | dateTime.iso8601
     *                              base64
     *                              null
     *                              array
     *                              struct
     * @return bool                 True on success
     */
    public function add_param($argument, $type = 'string') {

        // Convert any use of the old 'datetime' to the correct 'dateTime.iso8601' one.
        $type = ($type === 'datetime' ? 'dateTime.iso8601' : $type);

        // BC fix, if some argument is array and comes as string, change type to array (sequentials)
        // or struct (associative).
        // This is the behavior of the encode_request() method from the xmlrpc extension.
        // Note that uses in core have been fixed, but there may be others using that.
        if (is_array($argument) && $type === 'string') {
            if (array_keys($argument) === range(0, count($argument) - 1)) {
                $type = 'array';
            } else {
                $type = 'struct';
            }
            mnet_debug('Incorrect ' . $type . ' param passed as string in mnet_xmlrpc_client->add_param(): ' .
                json_encode($argument));
        }

        if (!isset(\PhpXmlRpc\Value::$xmlrpcTypes[$type])) { // Arrived here, still erong type? Let's stop.
            return false;
        }

        // If we are array or struct, we need to ensure that, recursively, all the elements are proper values.
        // or serialize, used later on send()  won't work with them. Encoder::encode() provides us with that.
        if ($type === 'array' || $type === 'struct') {
            $encoder = new \PhpXmlRpc\Encoder();
            $this->params[] = $encoder->encode($argument);
        } else {
            // Normal scalar case.
            $this->params[] = new \PhpXmlRpc\Value($argument, $type);
        }
        return true;
    }

    /**
     * Send the request to the server - decode and return the response
     *
     * @param  object   $mnet_peer      A mnet_peer object with details of the
     *                                  remote host we're connecting to
     * @param  bool     $rekey         The rekey attribute stops us from
     *                                  getting into a loop.
     * @return mixed                    A PHP variable, as returned by the
     */
    public function send($mnet_peer, bool $rekey = false) {
        global $CFG, $DB;

        if (!$this->permission_to_call($mnet_peer)) {
            mnet_debug("tried and wasn't allowed to call a method on $mnet_peer->wwwroot");
            return false;
        }

        $request = new \PhpXmlRpc\Request($this->method, $this->params);
        $requesttext = $request->serialize('utf-8');

        $signedrequest = mnet_sign_message($requesttext);
        $encryptedrequest = mnet_encrypt_message($signedrequest, $mnet_peer->public_key);

        $client = $this->prepare_http_request($mnet_peer);

        $timestamp_send    = time();
        mnet_debug("about to send the xmlrpc request");
        $response = $client->send($encryptedrequest, $this->timeout);
        mnet_debug("managed to complete a xmlrpc request");
        $timestamp_receive = time();

        if ($response->faultCode()) {
            $this->error[] = $response->faultCode() .':'. $response->faultString();
            return false;
        }

        $rawresponse = trim($response->value()); // Because MNet responses ARE NOT valid xmlrpc, don't try any PhpXmlRpc facility.

        $mnet_peer->touch();

        $crypt_parser = new mnet_encxml_parser();
        $crypt_parser->parse($rawresponse);

        // If we couldn't parse the message, or it doesn't seem to have encrypted contents,
        // give the most specific error msg available & return
        if (!$crypt_parser->payload_encrypted) {
            if (! empty($crypt_parser->remoteerror)) {
                $this->error[] = '4: remote server error: ' . $crypt_parser->remoteerror;
            } else if (! empty($crypt_parser->error)) {
                $crypt_parser_error = $crypt_parser->error[0];

                $message = '3:XML Parse error in payload: '.$crypt_parser_error['string']."\n";
                if (array_key_exists('lineno', $crypt_parser_error)) {
                    $message .= 'At line number: '.$crypt_parser_error['lineno']."\n";
                }
                if (array_key_exists('line', $crypt_parser_error)) {
                    $message .= 'Which reads: '.$crypt_parser_error['line']."\n";
                }
                $this->error[] = $message;
            } else {
                $this->error[] = '1:Payload not encrypted ';
            }

            $crypt_parser->free_resource();
            return false;
        }

        $key  = array_pop($crypt_parser->cipher);
        $data = array_pop($crypt_parser->cipher);

        $crypt_parser->free_resource();

        // Initialize payload var
        $decryptedenvelope = '';

        //                                          &$decryptedenvelope
        $isOpen = openssl_open(base64_decode($data), $decryptedenvelope, base64_decode($key),
            $this->mnet->get_private_key(), 'RC4');

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
                $isOpen      = openssl_open(base64_decode($data), $decryptedenvelope, base64_decode($key), $keyresource, 'RC4');
                if ($isOpen) {
                    // It's an older code, sir, but it checks out
                    break;
                }
            }
        }

        if (!$isOpen) {
            trigger_error("None of our keys could open the payload from host {$mnet_peer->wwwroot} with id {$mnet_peer->id}.");
            $this->error[] = '3:No key match';
            return false;
        }

        if (strpos(substr($decryptedenvelope, 0, 100), '<signedMessage>')) {
            $sig_parser = new mnet_encxml_parser();
            $sig_parser->parse($decryptedenvelope);
        } else {
            $this->error[] = '2:Payload not signed: ' . $decryptedenvelope;
            return false;
        }

        // Margin of error is the time it took the request to complete.
        $margin_of_error  = $timestamp_receive - $timestamp_send;

        // Guess the time gap between sending the request and the remote machine
        // executing the time() function. Marginally better than nothing.
        $hysteresis       = ($margin_of_error) / 2;

        $remote_timestamp = $sig_parser->remote_timestamp - $hysteresis;
        $time_offset      = $remote_timestamp - $timestamp_send;
        if ($time_offset > 0) {
            $threshold = get_config('mnet', 'drift_threshold');
            if(empty($threshold)) {
                // We decided 15 seconds was a pretty good arbitrary threshold
                // for time-drift between servers, but you can customize this in
                // the config_plugins table. It's not advised though.
                set_config('drift_threshold', 15, 'mnet');
                $threshold = 15;
            }
            if ($time_offset > $threshold) {
                $this->error[] = '6:Time gap with '.$mnet_peer->name.' ('.$time_offset.' seconds) is greater than the permitted maximum of '.$threshold.' seconds';
                return false;
            }
        }

        $xmlrpcresponse = base64_decode($sig_parser->data_object);
        // Let's convert the xmlrpc back to PHP structure.
        $response = null;
        $encoder = new \PhpXmlRpc\Encoder();
        $oresponse = $encoder->decodeXML($xmlrpcresponse); // First, to internal PhpXmlRpc\Response structure.
        if ($oresponse instanceof \PhpXmlRpc\Response) {
            // Special handling of fault responses (because value() doesn't handle them properly).
            if ($oresponse->faultCode()) {
                $response = ['faultCode' => $oresponse->faultCode(), 'faultString' => $oresponse->faultString()];
            } else {
                $response = $encoder->decode($oresponse->value()); // Normal Response conversion to PHP.
            }
        } else {
            // Maybe this is just a param, let's convert it too.
            $response = $encoder->decode($oresponse);
        }
        $this->response = $response;

        // xmlrpc errors are pushed onto the $this->error stack
        if (is_array($this->response) && array_key_exists('faultCode', $this->response)) {
            // The faultCode 7025 means we tried to connect with an old SSL key
            // The faultString is the new key - let's save it and try again
            // The rekey attribute stops us from getting into a loop
            if($this->response['faultCode'] == 7025 && empty($rekey)) {
                mnet_debug('recieved an old-key fault, so trying to get the new key and update our records');
                // If the new certificate doesn't come thru clean_param() unmolested, error out
                if($this->response['faultString'] != clean_param($this->response['faultString'], PARAM_PEM)) {
                    $this->error[] = $this->response['faultCode'] . " : " . $this->response['faultString'];
                }
                $record                     = new stdClass();
                $record->id                 = $mnet_peer->id;
                $record->public_key         = $this->response['faultString'];
                $details                    = openssl_x509_parse($record->public_key);
                if(!isset($details['validTo_time_t'])) {
                    $this->error[] = $this->response['faultCode'] . " : " . $this->response['faultString'];
                }
                $record->public_key_expires = $details['validTo_time_t'];
                $DB->update_record('mnet_host', $record);

                // Create a new peer object populated with the new info & try re-sending the request
                $rekeyed_mnet_peer = new mnet_peer();
                $rekeyed_mnet_peer->set_id($record->id);
                return $this->send($rekeyed_mnet_peer, true); // Re-send mnet_peer with the new key.
            }
            if (!empty($CFG->mnet_rpcdebug)) {
                if (get_string_manager()->string_exists('error'.$this->response['faultCode'], 'mnet')) {
                    $guidance = get_string('error'.$this->response['faultCode'], 'mnet');
                } else {
                    $guidance = '';
                }
            } else {
                $guidance = '';
            }
            $this->error[] = $this->response['faultCode'] . " : " . $this->response['faultString'] ."\n".$guidance;
        }

        // ok, it's signed, but is it signed with the right certificate ?
        // do this *after* we check for an out of date key
        $verified = openssl_verify($xmlrpcresponse, base64_decode($sig_parser->signature), $mnet_peer->public_key);
        if ($verified != 1) {
            $this->error[] = 'Invalid signature';
        }

        return empty($this->error);
    }

    /**
     * Check that we are permitted to call method on specified peer
     *
     * @param object $mnet_peer A mnet_peer object with details of the remote host we're connecting to
     * @return bool True if we permit calls to method on specified peer, False otherwise.
     */
    public function permission_to_call($mnet_peer) {
        global $DB, $CFG, $USER;

        // Executing any system method is permitted.
        $system_methods = array('system/listMethods', 'system/methodSignature', 'system/methodHelp', 'system/listServices');
        if (in_array($this->method, $system_methods) ) {
            return true;
        }

        $hostids = array($mnet_peer->id);
        if (!empty($CFG->mnet_all_hosts_id)) {
            $hostids[] = $CFG->mnet_all_hosts_id;
        }
        // At this point, we don't care if the remote host implements the
        // method we're trying to call. We just want to know that:
        // 1. The method belongs to some service, as far as OUR host knows
        // 2. We are allowed to subscribe to that service on this mnet_peer

        list($hostidsql, $hostidparams) = $DB->get_in_or_equal($hostids);

        $sql = "SELECT r.id
                  FROM {mnet_remote_rpc} r
            INNER JOIN {mnet_remote_service2rpc} s2r ON s2r.rpcid = r.id
            INNER JOIN {mnet_host2service} h2s ON h2s.serviceid = s2r.serviceid
                 WHERE r.xmlrpcpath = ?
                       AND h2s.subscribe = ?
                       AND h2s.hostid $hostidsql";

        $params = array($this->method, 1);
        $params = array_merge($params, $hostidparams);

        if ($DB->record_exists_sql($sql, $params)) {
            return true;
        }

        $this->error[] = '7:User with ID '. $USER->id .
                         ' attempted to call unauthorised method '.
                         $this->method.' on host '.
                         $mnet_peer->wwwroot;
        return false;
    }

    /**
     * Generate a \PhpXmlRpc\Client handle and prepare it for sending to an mnet host
     *
     * @param object $mnet_peer A mnet_peer object with details of the remote host the request will be sent to
     * @return \PhpXmlRpc\Client handle - the almost-ready-to-send http request
     */
    public function prepare_http_request($mnet_peer) {
        $uri = $mnet_peer->wwwroot . $mnet_peer->application->xmlrpc_server_url;

        // Instantiate the xmlrpc client to be used for the client request
        // and configure it the way we want.
        $client = new \PhpXmlRpc\Client($uri);
        $client->setUseCurl(\PhpXmlRpc\Client::USE_CURL_ALWAYS);
        $client->setUserAgent('Moodle');
        $client->return_type = 'xml'; // Because MNet responses ARE NOT valid xmlrpc, don't try any validation.

        // TODO: Link this to DEBUG DEVELOPER or with MNET debugging...
        // $client->setdebug(1); // See a good number of complete requests and responses.

        $verifyhost = 0;
        $verifypeer = false;
        if ($mnet_peer->sslverification == mnet_peer::SSL_HOST_AND_PEER) {
            $verifyhost = 2;
            $verifypeer = true;
        } else if ($mnet_peer->sslverification == mnet_peer::SSL_HOST) {
            $verifyhost = 2;
        }
        $client->setSSLVerifyHost($verifyhost);
        $client->setSSLVerifyPeer($verifypeer);

        return $client;
    }
}
