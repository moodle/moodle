<?php
/**
 * Info about the local environment, wrt RPC
 *
 * This should really be a singleton. A PHP5 Todo I guess.
 */

class mnet_environment {

    var $id                 = 0;
    var $wwwroot            = '';
    var $ip_address         = '';
    var $public_key         = '';
    var $public_key_expires = 0;
    var $last_connect_time  = 0;
    var $last_log_id        = 0;
    var $keypair            = array();

    function mnet_environment() {
        return true;
    }

    function init() {
        global $CFG;

        // Bootstrap the object data on first load.
        if (empty($CFG->mnet_localhost_id) ) {
            $this->get_keypair();

            $hostobject                     = new stdClass();
            $hostobject->wwwroot            = $CFG->wwwroot;
            $hostobject->ip_address         = $_SERVER['SERVER_ADDR'];
            $hostobject->public_key         = $this->keypair['certificate'];
            $hostobject->public_key_expires = '';
            $hostobject->last_connect_time  = '0';
            $hostobject->last_log_id        = '0';
            $hostobject->deleted            = 0;

            $this->id = insert_record('mnet_host',$hostobject, true);

            $temparr = (array)get_object_vars($hostobject);

            foreach($temparr as $key => $value) {
                $this->$key = $value;
            }

            unset($temparr, $hostobject);

            set_config('mnet_localhost_id', $this->id);
            $CFG->mnet_localhost_id = $this->id;
        } else {
            $hostobject = get_record('mnet_host','id', $CFG->mnet_localhost_id);
            $temparr = (array)get_object_vars($hostobject);

            foreach($temparr as $key => $value) {
                $this->$key = $value;
            }

            unset($temparr, $hostobject);
        }

        // We need to set up a record that represents 'all hosts'. Any rights
        // granted to this host will be conferred on all hosts.
        if (empty($CFG->mnet_all_hosts_id) ) {
            $hostobject                     = new stdClass();
            $hostobject->wwwroot            = '';
            $hostobject->ip_address         = '';
            $hostobject->public_key         = '';
            $hostobject->public_key_expires = '';
            $hostobject->last_connect_time  = '0';
            $hostobject->last_log_id        = '0';
            $hostobject->deleted            = 0;
            $hostobject->name               = 'All Hosts';

            $hostobject->id = insert_record('mnet_host',$hostobject, true);
            set_config('mnet_all_hosts_id', $hostobject->id);
            $CFG->mnet_all_hosts_id = $hostobject->id;
            unset($hostobject);
        }
    }

    function get_keypair() {
        if (!empty($this->keypair)) return true;
        if ($result = get_record_select('config', " name = 'openssl'")) {
            $this->keypair               = unserialize($result->value);
            $this->keypair['privatekey'] = openssl_pkey_get_private($this->keypair['keypair_PEM']);
            $this->keypair['publickey']  = openssl_pkey_get_public($this->keypair['certificate']);
        } else {
            $this->keypair = mnet_generate_keypair();
        }
        return true;
    }

    function get_private_key() {
        if (empty($this->keypair)) $this->get_keypair();
        if (isset($this->keypair['privatekey'])) return $this->keypair['privatekey'];
        $this->keypair['privatekey'] = openssl_pkey_get_private($this->keypair['keypair_PEM']);
        return $this->keypair['privatekey'];
    }

    function get_public_key() {
        if (!isset($this->keypair)) $this->get_keypair();
        if (isset($this->keypair['publickey'])) return $this->keypair['publickey'];
        $this->keypair['publickey'] = openssl_pkey_get_public($this->keypair['certificate']);
        return $this->keypair['publickey'];
    }

    /**
     * Note that the openssl_sign function computes the sha1 hash, and then
     * signs the hash.
     */
    function sign_message($message) {
        $bool = openssl_sign($message, $signature, $this->get_private_key());
        return $signature;
    }
}

?>
