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

    /**
     * Constructor returns true
     */
    function mnet_xmlrpc_client() {
        return true;
    }

    /**
     * Allow users to override the default timeout
     * @param   int $timeout    Request timeout in seconds
     * $return  bool            True if param is an integer or integer string
     */
    function set_timeout($timeout) {
        if (!is_integer($timeout)) {
            if (is_numeric($timeout)) {
                $this->timeout = (integer($timeout));
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
        global $CFG, $MNET;

        $this->uri = $mnet_peer->wwwroot.
               '/mnet/xmlrpc/server.php';

        // Initialize with the target URL
        $ch = curl_init($this->uri);

        $system_methods = array('system/listMethods', 'system/methodSignature', 'system/methodHelp', 'system/listServices');

        if (in_array($this->method, $system_methods) ) {

            // Executing any system method is permitted.

        } else {

            // Find methods that we subscribe to on this host
            $sql = "
                SELECT
                    *
                FROM
                    {$CFG->prefix}mnet_rpc r,
                    {$CFG->prefix}mnet_service2rpc s2r,
                    {$CFG->prefix}mnet_host2service h2s
                WHERE
                    r.xmlrpc_path = '{$this->method}' AND
                    s2r.rpcid = r.id AND
                    s2r.serviceid = h2s.serviceid AND
                    h2s.subscribe = '1'";

            $permission = get_record_sql($sql);
            if ($permission == false) {
                // TODO: Handle attempt to call not-permitted method
                echo '<pre>'.$sql.'</pre>';
                return false;
            }

        }
        $this->requesttext = xmlrpc_encode_request($this->method, $this->params);
        $rq = $this->requesttext;
        $rq = mnet_sign_message($this->requesttext);
        $this->signedrequest = $rq;
        $rq = mnet_encrypt_message($rq, $mnet_peer->public_key);
        $this->encryptedrequest = $rq;

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));

        $this->rawresponse = curl_exec($ch);
        if ($this->rawresponse == false) {
            $this->error[] = array(curl_errno($ch), curl_error($ch));
        }

        $crypt_parser = new mnet_encxml_parser();
        $crypt_parser->parse($this->rawresponse);

        if ($crypt_parser->payload_encrypted) {

            $key  = array_pop($crypt_parser->cipher);
            $data = array_pop($crypt_parser->cipher);

            $crypt_parser->free_resource();

            // Initialize payload var
            $payload = '';

            //                                          &$payload
            $isOpen = openssl_open(base64_decode($data), $payload, base64_decode($key), $MNET->get_private_key());

            if (!$isOpen) {
                return false;
            }

            if (strpos(substr($payload, 0, 100), '<signedMessage>')) {
                $sig_parser = new mnet_encxml_parser();
                $sig_parser->parse($payload);
            } else {
                return false;
            }

        } else {
            $crypt_parser->free_resource();
            return false;
        }

        $this->xmlrpcresponse = base64_decode($sig_parser->data_object);
        $this->response       = xmlrpc_decode($this->xmlrpcresponse);
        curl_close($ch);

        // xmlrpc errors are pushed onto the $this->error stack
        if (isset($this->response['faultCode'])) {
            $this->error[] = $this->response['faultCode'] . " : " . $this->response['faultString'];
        }
        return empty($this->error);
    }
}
?>
