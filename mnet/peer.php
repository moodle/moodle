<?php // $Id$
/**
 * An object to represent lots of information about an RPC-peer machine
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

class mnet_peer {

    //Unless stated otherwise, properties of this object are unescaped, and unsafe to
    //insert into the db without further processing.
    var $id                 = 0;
    var $wwwroot            = '';
    var $ip_address         = '';
    var $name               = '';
    var $public_key         = '';
    var $public_key_expires = 0;
    var $last_connect_time  = 0;
    var $last_log_id        = 0;
    var $force_theme        = 0;
    var $theme              = '';
    var $applicationid      = 1; // Default of 1 == Moodle
    var $keypair            = array();
    var $error              = array();
    //Object whose properties need to be put into the database:
    //(properties here are slashescaped)
    var $updateparams;

    function mnet_peer() {
        $this->updateparams = new stdClass();
        return true;
    }

    function bootstrap($wwwroot, $pubkey = null, $application) {

        if (substr($wwwroot, -1, 1) == '/') {
            $wwwroot = substr($wwwroot, 0, -1);
        }

        if ( ! $this->set_wwwroot($wwwroot) ) {
            $hostname = mnet_get_hostname_from_uri($wwwroot);

            // Get the IP address for that host - if this fails, it will
            // return the hostname string
            $ip_address = gethostbyname($hostname);

            // Couldn't find the IP address?
            if ($ip_address === $hostname && !preg_match('/^\d+\.\d+\.\d+.\d+$/',$hostname)) {
                $this->error[] = array('code' => 2, 'text' => get_string("noaddressforhost", 'mnet'));
                return false;
            }

            $this->name = stripslashes($wwwroot);
            $this->updateparams->name = $wwwroot;

            // TODO: In reality, this will be prohibitively slow... need another
            // default - maybe blank string
            $homepage = file_get_contents($wwwroot);
            if (!empty($homepage)) {
                $count = preg_match("@<title>(.*)</title>@siU", $homepage, $matches);
                if ($count > 0) {
                    $this->name = $matches[1];
                    $this->updateparams->name = addslashes($matches[1]);
                }
            }

            $this->wwwroot = stripslashes($wwwroot);
            $this->updateparams->wwwroot = $wwwroot;
            $this->ip_address = $ip_address;
            $this->updateparams->ip_address = $ip_address;
            $this->deleted = 0;
            $this->updateparams->deleted = 0;

            $this->application = get_record('mnet_application', 'name', $application);
            if (empty($this->application)) {
                $this->application = get_record('mnet_application', 'name', 'moodle');
            }

            $this->applicationid = $this->application->id;
            $this->updateparams->applicationid = $this->application->id;

            if(empty($pubkey)) {
                $pubkeytemp = clean_param(mnet_get_public_key($this->wwwroot, $this->application), PARAM_PEM);
            } else {
                $pubkeytemp = clean_param($pubkey, PARAM_PEM);
            }
            $this->public_key_expires = $this->check_common_name($pubkeytemp);

            if ($this->public_key_expires == false) {
                return false;
            }
            $this->updateparams->public_key_expires = $this->public_key_expires;

            $this->updateparams->public_key = $pubkeytemp;
            $this->public_key = $pubkeytemp;

            $this->last_connect_time = 0;
            $this->updateparams->last_connect_time = 0;
            $this->last_log_id = 0;
            $this->updateparams->last_log_id = 0;
        }

        return true;
    }

    function delete() {
        if ($this->deleted) return true;

        $users = count_records('user','mnethostid', $this->id);
        if ($users > 0) {
            $this->deleted = 1;
            $this->updateparams->deleted = 1;
        }

        $actions = count_records('mnet_log','hostid', $this->id);
        if ($actions > 0) {
            $this->deleted = 1;
            $this->updateparams->deleted = 1;
        }

        $obj = delete_records('mnet_rpc2host', 'host_id', $this->id);

        $this->delete_all_sessions();

        // If we don't have any activity records for which the mnet_host table
        // provides a foreign key, then we can delete the record. Otherwise, we
        // just mark it as deleted.
        if (0 == $this->deleted) {
            delete_records('mnet_host', "id", $this->id);
        } else {
            $this->commit();
        }
    }

    function count_live_sessions() {
        $obj = $this->delete_expired_sessions();
        return count_records('mnet_session','mnethostid', $this->id);
    }

    function delete_expired_sessions() {
        $now = time();
        return delete_records_select('mnet_session', " mnethostid = '{$this->id}' AND expires < '$now' ");
    }

    function delete_all_sessions() {
        global $CFG;
        // TODO: Expires each PHP session individually
        // $sessions = get_records('mnet_session', 'mnethostid', $this->id);
        $sessions = get_records('mnet_session', 'mnethostid', $this->id);

        if (count($sessions) > 0 && file_exists($CFG->dirroot.'/auth/mnet/auth.php')) {
            require_once($CFG->dirroot.'/auth/mnet/auth.php');
            $auth = new auth_plugin_mnet();
            $auth->end_local_sessions($sessions);
        }

        $deletereturn = delete_records_select('mnet_session', " mnethostid = '{$this->id}'");
        return true;
    }

    function check_common_name($key) {
        $credentials = openssl_x509_parse($key);
        if ($credentials == false) {
            $this->error[] = array('code' => 3, 'text' => get_string("nonmatchingcert", 'mnet', array('','')));
            return false;
        } elseif ($credentials['subject']['CN'] != $this->wwwroot) {
            $a[] = $credentials['subject']['CN'];
            $a[] = $this->wwwroot;
            $this->error[] = array('code' => 4, 'text' => get_string("nonmatchingcert", 'mnet', $a));
            return false;
        } else {
            return $credentials['validTo_time_t'];
        }
    }

    function commit() {
        $obj = $this->updateparams;

        if (isset($this->id) && $this->id > 0) {
            $obj->id = $this->id;
            $dbresult = update_record('mnet_host', $obj);
        } else {
            $this->id = insert_record('mnet_host', $obj);
            $dbresult = ($this->id > 0);
        }
        //If the insert/update was successful, clear the parameters that need updating
        if ($dbresult) {
            $this->updateparams = new stdClass();
        }
        return $dbresult;
    }

    function touch() {
        $this->last_connect_time = time();
        $this->updateparams->last_connect_time = time();
        $this->commit();
    }

    function set_name($newname) {
        if (is_string($newname) && strlen($newname <= 80)) {
            $this->name = stripslashes($newname);
            $this->updateparams->name = $newname;
            return true;
        }
        return false;
    }

    function set_applicationid($applicationid) {
        if (is_numeric($applicationid) && $applicationid == intval($applicationid)) {
            $this->applicationid = $applicationid;
            $this->updateparams->applicationid = $applicationid;
            return true;
        }
        return false;
    }

    function set_wwwroot($wwwroot) {
        global $CFG;

        $hostinfo = get_record('mnet_host', 'wwwroot', $wwwroot);

        if ($hostinfo != false) {
            $this->populate($hostinfo);
            return true;
        }
        return false;
    }

    function set_id($id) {
        global $CFG;

        if (clean_param($id, PARAM_INT) != $id) {
            $this->errno[]  = 1;
            $this->errmsg[] = 'Your id ('.$id.') is not legal';
            return false;
        }

        $sql = "
                SELECT
                    h.*
                FROM
                    {$CFG->prefix}mnet_host h
                WHERE
                    h.id = '". $id ."'";

        if ($hostinfo = get_record_sql($sql)) {
            $this->populate($hostinfo);
            return true;
        }
        return false;
    }

    /**
     * Several methods can be used to get an 'mnet_host' record. They all then
     * send it to this private method to populate this object's attributes.
     * 
     * @param   object  $hostinfo   A database record from the mnet_host table
     * @return  void
     */
    function populate($hostinfo) {
        $this->id                   = $hostinfo->id;
        $this->wwwroot              = $hostinfo->wwwroot;
        $this->ip_address           = $hostinfo->ip_address;
        $this->name                 = $hostinfo->name;
        $this->deleted              = $hostinfo->deleted;
        $this->public_key           = $hostinfo->public_key;
        $this->public_key_expires   = $hostinfo->public_key_expires;
        $this->last_connect_time    = $hostinfo->last_connect_time;
        $this->last_log_id          = $hostinfo->last_log_id;
        $this->force_theme          = $hostinfo->force_theme;
        $this->theme                = $hostinfo->theme;
        $this->applicationid        = $hostinfo->applicationid;
        $this->application = get_record('mnet_application', 'id', $this->applicationid);
    }

    function get_public_key() {
        if (isset($this->public_key_ref)) return $this->public_key_ref;
        $this->public_key_ref = openssl_pkey_get_public($this->public_key);
        return $this->public_key_ref;
    }
}

?>
