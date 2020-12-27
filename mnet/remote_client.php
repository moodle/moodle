<?php
/**
 * An object to represent lots of information about an RPC-peer machine
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

class mnet_remote_client extends mnet_peer {

    // If the remote client is trying to execute a method on an object instead
    // of just a function, we'll instantiate the proper class and store it in
    // this 'object_to_call' property, or 'static_location' if it wants to be called statically
    var $object_to_call         = false;
    var $static_location        = false;
    var $request_was_encrypted  = false;
    var $request_was_signed     = false;
    var $signatureok = false; // True if we have successfully verified that the request was signed by an established peer
    var $pushkey = false; // True if we need to tell the remote peer about our current public key
    var $useprivatekey = ''; // The private key we should use to sign pushkey response

    function was_encrypted() {
        $this->request_was_encrypted  = true;
    }

    /* Record private key to use in pushkey response
     * Called when we have decrypted a request using an old (but still acceptable) keypair
     * @param $keyresource the private key we should use to sign the response.
     */
    function encrypted_to($keyresource) {
        $this->useprivatekey = $keyresource;
    }

    function set_pushkey() {
        $this->pushkey = true;
    }

    function was_signed() {
        $this->request_was_signed  = true;
    }

    function signature_verified() {
        $this->signatureok = true;
    }

    function object_to_call($object) {
        $this->object_to_call = $object;
    }

    function static_location($location) {
        $this->static_location = $location;
    }

    function plaintext_is_ok() {
        global $CFG;

        $trusted_hosts = explode(',', get_config('mnet', 'mnet_trusted_hosts'));

        foreach($trusted_hosts as $host) {
            if (address_in_subnet(getremoteaddr(), $host)) {
                return true;
            }
        }

        return false;
    }

    function refresh_key() {
        mnet_debug("remote client refreshing key");
        global $CFG;
        // set up an RPC request
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';
        $mnetrequest = new mnet_xmlrpc_client();
        // Use any method - listServices is pretty lightweight.
        $mnetrequest->set_method('system/listServices');

        // Do RPC call and store response
        if ($mnetrequest->send($this) === true) {
            mnet_debug("refresh key request complete");
            // Ok - we actually don't care about the result
            $temp = new mnet_peer();
            $temp->set_id($this->id);
            if($this->public_key != $temp->public_key) {
                $newkey = clean_param($temp->public_key, PARAM_PEM);
                if(!empty($newkey)) {
                    $this->public_key = $newkey;
                    return true;
                }
            }
        }
        return false;
    }
}
