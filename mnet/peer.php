<?php
/**
 * An object to represent lots of information about an RPC-peer machine
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

require_once($CFG->libdir . '/filelib.php'); // download_file_content() used here

class mnet_peer {

    /** No SSL verification. */
    const SSL_NONE = 0;

    /** SSL verification for host. */
    const SSL_HOST = 1;

    /** SSL verification for host and peer. */
    const SSL_HOST_AND_PEER = 2;

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
    var $bootstrapped       = false; // set when the object is populated

    /** @var int $sslverification The level of SSL verification to apply. */
    public $sslverification = self::SSL_HOST_AND_PEER;

    /*
     * Fetch information about a peer identified by wwwroot
     * If information does not preexist in db, collect it together based on
     * supplied information
     *
     * @param string $wwwroot - address of peer whose details we want
     * @param string $pubkey - to use if we add a record to db for new peer
     * @param int $application - table id - what kind of peer are we talking to
     * @return bool - indication of success or failure
     */
    function bootstrap($wwwroot, $pubkey = null, $application) {
        global $DB;

        if (substr($wwwroot, -1, 1) == '/') {
            $wwwroot = substr($wwwroot, 0, -1);
        }

        // If a peer record already exists for this address,
        // load that info and return
        if ($this->set_wwwroot($wwwroot)) {
            return true;
        }

        $hostname = mnet_get_hostname_from_uri($wwwroot);
        // Get the IP address for that host - if this fails, it will return the hostname string
        $ip_address = gethostbyname($hostname);

        // Couldn't find the IP address?
        if ($ip_address === $hostname && !preg_match('/^\d+\.\d+\.\d+.\d+$/',$hostname)) {
            throw new moodle_exception('noaddressforhost', 'mnet', '', $hostname);
        }

        $this->name = $wwwroot;

        // TODO: In reality, this will be prohibitively slow... need another
        // default - maybe blank string
        $homepage = download_file_content($wwwroot);
        if (!empty($homepage)) {
            $count = preg_match("@<title>(.*)</title>@siU", $homepage, $matches);
            if ($count > 0) {
                $this->name = $matches[1];
            }
        }

        $this->wwwroot              = $wwwroot;
        $this->ip_address           = $ip_address;
        $this->deleted              = 0;

        $this->application = $DB->get_record('mnet_application', array('name'=>$application));
        if (empty($this->application)) {
            $this->application = $DB->get_record('mnet_application', array('name'=>'moodle'));
        }

        $this->applicationid = $this->application->id;

        if(empty($pubkey)) {
            $this->public_key           = clean_param(mnet_get_public_key($this->wwwroot, $this->application), PARAM_PEM);
        } else {
            $this->public_key           = clean_param($pubkey, PARAM_PEM);
        }
        $this->public_key_expires   = $this->check_common_name($this->public_key);
        $this->last_connect_time    = 0;
        $this->last_log_id          = 0;
        if ($this->public_key_expires == false) {
            $this->public_key == '';
            return false;
        }
        $this->bootstrapped = true;
    }

    /*
     * Delete mnet peer
     * the peer is marked as deleted in the database
     * we delete current sessions.
     * @return bool - success
     */
    function delete() {
        global $DB;

        if ($this->deleted) {
            return true;
        }

        $this->delete_all_sessions();

        $this->deleted = 1;
        return $this->commit();
    }

    function count_live_sessions() {
        global $DB;
        $obj = $this->delete_expired_sessions();
        return $DB->count_records('mnet_session', array('mnethostid'=>$this->id));
    }

    function delete_expired_sessions() {
        global $DB;
        $now = time();
        return $DB->delete_records_select('mnet_session', " mnethostid = ? AND expires < ? ", array($this->id, $now));
    }

    function delete_all_sessions() {
        global $CFG, $DB;
        // TODO: Expires each PHP session individually
        $sessions = $DB->get_records('mnet_session', array('mnethostid'=>$this->id));

        if (count($sessions) > 0 && file_exists($CFG->dirroot.'/auth/mnet/auth.php')) {
            require_once($CFG->dirroot.'/auth/mnet/auth.php');
            $auth = new auth_plugin_mnet();
            $auth->end_local_sessions($sessions);
        }

        $deletereturn = $DB->delete_records('mnet_session', array('mnethostid'=>$this->id));
        return true;
    }

    function check_common_name($key) {
        $credentials = $this->check_credentials($key);
        return $credentials['validTo_time_t'];
    }

    function check_credentials($key) {
        $credentials = openssl_x509_parse($key);
        if ($credentials == false) {
            $this->error[] = array('code' => 3, 'text' => get_string("nonmatchingcert", 'mnet', array('subject' => '','host' => '')));
            return false;
        } elseif (array_key_exists('subjectAltName', $credentials['subject']) && $credentials['subject']['subjectAltName'] != $this->wwwroot) {
            $a['subject'] = $credentials['subject']['subjectAltName'];
            $a['host'] = $this->wwwroot;
            $this->error[] = array('code' => 5, 'text' => get_string("nonmatchingcert", 'mnet', $a));
            return false;
        } else if ($credentials['subject']['CN'] !== substr($this->wwwroot, 0, 64)) {
            $a['subject'] = $credentials['subject']['CN'];
            $a['host'] = $this->wwwroot;
            $this->error[] = array('code' => 4, 'text' => get_string("nonmatchingcert", 'mnet', $a));
            return false;
        } else {
            if (array_key_exists('subjectAltName', $credentials['subject'])) {
                $credentials['wwwroot'] = $credentials['subject']['subjectAltName'];
            } else {
                $credentials['wwwroot'] = $credentials['subject']['CN'];
            }
            return $credentials;
        }
    }

    function commit() {
        global $DB;
        $obj = new stdClass();

        $obj->wwwroot               = $this->wwwroot;
        $obj->ip_address            = $this->ip_address;
        $obj->name                  = $this->name;
        $obj->public_key            = $this->public_key;
        $obj->public_key_expires    = $this->public_key_expires;
        $obj->deleted               = $this->deleted;
        $obj->last_connect_time     = $this->last_connect_time;
        $obj->last_log_id           = $this->last_log_id;
        $obj->force_theme           = $this->force_theme;
        $obj->theme                 = $this->theme;
        $obj->applicationid         = $this->applicationid;
        $obj->sslverification       = $this->sslverification;

        if (isset($this->id) && $this->id > 0) {
            $obj->id = $this->id;
            return $DB->update_record('mnet_host', $obj);
        } else {
            $this->id = $DB->insert_record('mnet_host', $obj);
            return $this->id > 0;
        }
    }

    function touch() {
        $this->last_connect_time = time();
        $this->commit();
    }

    function set_name($newname) {
        if (is_string($newname) && strlen($newname <= 80)) {
            $this->name = $newname;
            return true;
        }
        return false;
    }

    function set_applicationid($applicationid) {
        if (is_numeric($applicationid) && $applicationid == intval($applicationid)) {
            $this->applicationid = $applicationid;
            return true;
        }
        return false;
    }

    /**
     * Load information from db about an mnet peer into this object's properties
     *
     * @param string $wwwroot - address of peer whose details we want to load
     * @return bool - indication of success or failure
     */
    function set_wwwroot($wwwroot) {
        global $CFG, $DB;

        $hostinfo = $DB->get_record('mnet_host', array('wwwroot'=>$wwwroot));

        if ($hostinfo != false) {
            $this->populate($hostinfo);
            return true;
        }
        return false;
    }

    function set_id($id) {
        global $CFG, $DB;

        if (clean_param($id, PARAM_INT) != $id) {
            $this->errno[]  = 1;
            $this->errmsg[] = 'Your id ('.$id.') is not legal';
            return false;
        }

        $sql = "
                SELECT
                    h.*
                FROM
                    {mnet_host} h
                WHERE
                    h.id = ?";

        if ($hostinfo = $DB->get_record_sql($sql, array($id))) {
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
        global $DB;
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
        $this->sslverification      = $hostinfo->sslverification;
        $this->application = $DB->get_record('mnet_application', array('id'=>$this->applicationid));
        $this->bootstrapped = true;
    }

    function get_public_key() {
        if (isset($this->public_key_ref)) return $this->public_key_ref;
        $this->public_key_ref = openssl_pkey_get_public($this->public_key);
        return $this->public_key_ref;
    }
}
