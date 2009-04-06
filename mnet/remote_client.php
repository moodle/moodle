<?php // $Id$
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
    // this 'object_to_call' property.
    var $object_to_call         = false;
    var $request_was_encrypted  = false;
    var $request_was_signed     = false;

    function was_encrypted() {
        $this->request_was_encrypted  = true;
    }

    function was_signed() {
        $this->request_was_signed  = true;
    }

    function object_to_call($object) {
        $this->object_to_call = $object;
    }

    function plaintext_is_ok() {
        global $CFG;

        $trusted_hosts = explode(',', get_config('mnet', 'mnet_trusted_hosts'));

        foreach($trusted_hosts as $host) {
            list($network, $mask) = explode('/', $host.'/');
            if (empty($network)) continue;
            if (strlen($mask) == 0) $mask = 32;
            
            if (ip_in_range($_SERVER['REMOTE_ADDR'], $network, $mask)) {
                return true;
            }
        }

        return false;
    }

    function refresh_key() {
        global $CFG;
        // set up an RPC request
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';
        $mnetrequest = new mnet_xmlrpc_client();
        // Use any method - listServices is pretty lightweight.
        $mnetrequest->set_method('system/listServices');

        // Do RPC call and store response
        if ($mnetrequest->send($this) === true) {
            // Ok - we actually don't care about the result
            $temp = new mnet_peer();
            $temp->set_id($this->id);
            if($this->public_key != $temp->public_key) {
                $newkey = clean_param($temp->public_key, PARAM_PEM);
                if(!empty($newkey)) {
                    $this->public_key = $newkey;
                    $this->updateparams->public_key = $newkey;
                    $this->commit();
                    return true;
                }
            }
        }
        return false;
    }
}
?>
