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
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function mnet_xmlrpc_client() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Allow users to override the default timeout
     * @param   int $timeout    Request timeout in seconds
     * $return  bool            True if param is an integer or integer string
     */
    function set_timeout($timeout) {
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
    function set_method($xmlrpcpath) {
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
     *                              none
     *                              empty
     *                              base64
     *                              boolean
     *                              datetime
     *                              double
     *                              int
     *                              string
     *                              array
     *                              struct
     *                              In its weakly-typed wisdom, PHP will (currently)
     *                              ignore everything except datetime and base64
     * @return bool                 True on success
     */
    function add_param($argument, $type = 'string') {

        $allowed_types = array('none',
                               'empty',
                               'base64',
                               'boolean',
                               'datetime',
                               'double',
                               'int',
                               'i4',
                               'string',
                               'array',
                               'struct');
        if (!in_array($type, $allowed_types)) {
            return false;
        }

        if ($type != 'datetime' && $type != 'base64') {
            $this->params[] = $argument;
            return true;
        }

        // Note weirdness - The type of $argument gets changed to an object with
        // value and type properties.
        // bool xmlrpc_set_type ( string &value, string type )
        xmlrpc_set_type($argument, $type);
        $this->params[] = $argument;
        return true;
    }

    /**
     * Send the request to the server - decode and return the response
     *
     * @param  object   $mnet_peer      A mnet_peer object with details of the
     *                                  remote host we're connecting to
     * @return mixed                    A PHP variable, as returned by the
     *                                  remote function
     */
    function send($mnet_peer) {
        global $CFG, $DB;


        if (!$this->permission_to_call($mnet_peer)) {
            mnet_debug("tried and wasn't allowed to call a method on $mnet_peer->wwwroot");
            return false;
        }

        $this->requesttext = xmlrpc_encode_request($this->method, $this->params, array("encoding" => "utf-8", "escaping" => "markup"));
        $this->signedrequest = mnet_sign_message($this->requesttext);
        $this->encryptedrequest = mnet_encrypt_message($this->signedrequest, $mnet_peer->public_key);

        $httprequest = $this->prepare_http_request($mnet_peer);
        curl_setopt($httprequest, CURLOPT_POSTFIELDS, $this->encryptedrequest);

        $timestamp_send    = time();
        mnet_debug("about to send the curl request");
        $this->rawresponse = curl_exec($httprequest);
        mnet_debug("managed to complete a curl request");
        $timestamp_receive = time();

        if ($this->rawresponse === false) {
            $this->error[] = curl_errno($httprequest) .':'. curl_error($httprequest);
            return false;
        }
        curl_close($httprequest);

        $this->rawresponse = trim($this->rawresponse);

        $mnet_peer->touch();

        $crypt_parser = new mnet_encxml_parser();
        $crypt_parser->parse($this->rawresponse);

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

        $this->xmlrpcresponse = base64_decode($sig_parser->data_object);
        $this->response       = xmlrpc_decode($this->xmlrpcresponse);

        // xmlrpc errors are pushed onto the $this->error stack
        if (is_array($this->response) && array_key_exists('faultCode', $this->response)) {
            // The faultCode 7025 means we tried to connect with an old SSL key
            // The faultString is the new key - let's save it and try again
            // The re_key attribute stops us from getting into a loop
            if($this->response['faultCode'] == 7025 && empty($mnet_peer->re_key)) {
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
                $rekeyed_mnet_peer->re_key = true;
                return $this->send($rekeyed_mnet_peer);
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
        $verified = openssl_verify($this->xmlrpcresponse, base64_decode($sig_parser->signature), $mnet_peer->public_key);
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

    function permission_to_call($mnet_peer) {
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
     * Generate a curl handle and prepare it for sending to an mnet host
     *
     * @param object $mnet_peer A mnet_peer object with details of the remote host the request will be sent to
     * @return cURL handle - the almost-ready-to-send http request
     */
    function prepare_http_request ($mnet_peer) {
        $this->uri = $mnet_peer->wwwroot . $mnet_peer->application->xmlrpc_server_url;

        // Initialize request the target URL
        $httprequest = curl_init($this->uri);
        curl_setopt($httprequest, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($httprequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($httprequest, CURLOPT_POST, true);
        curl_setopt($httprequest, CURLOPT_USERAGENT, 'Moodle');
        curl_setopt($httprequest, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));

        $verifyhost = 0;
        $verifypeer = false;
        if ($mnet_peer->sslverification == mnet_peer::SSL_HOST_AND_PEER) {
            $verifyhost = 2;
            $verifypeer = true;
        } else if ($mnet_peer->sslverification == mnet_peer::SSL_HOST) {
            $verifyhost = 2;
        }
        curl_setopt($httprequest, CURLOPT_SSL_VERIFYHOST, $verifyhost);
        curl_setopt($httprequest, CURLOPT_SSL_VERIFYPEER, $verifypeer);
        return $httprequest;
    }
}
