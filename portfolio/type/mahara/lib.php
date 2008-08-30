<?php

define('PORTFOLIO_MAHARA_ERR_NETWORKING_OFF', 'err_networkingoff');
define('PORTFOLIO_MAHARA_ERR_NOHOSTS', 'err_nomnethosts');

require_once($CFG->dirroot . '/lib/portfoliolib.php');
require_once($CFG->dirroot . '/mnet/lib.php');

define('PORTFOLIO_MAHARA_QUEUE', PORTFOLIO_TIME_HIGH);
define('PORTFOLIO_MAHARA_IMMEDIATE', PORTFOLIO_TIME_MODERATE);

class portfolio_plugin_mahara extends portfolio_plugin_pull_base {

    private $hosts; // used in the admin config form
    private $mnethost; // privately set during export from the admin config value (mnethostid)
    private $hostrecord; // the host record that corresponds to the peer
    private $token; // during-transfer token
    private $sendtype; // whatever mahara has said it can handle (immediate or queued)
    private $filesmanifest; // manifest of files to send to mahara (set during prepare_package and sent later)

    public static function get_allowed_config() {
        return array('mnethostid');
    }

    public static function supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE);
    }

    public function expected_time($callertime) {
        if ($this->sendtype == PORTFOLIO_MAHARA_QUEUE) {
            return PORTFOLIO_TIME_FORCEQUEUE;
        }
        return $callertime;
    }

    public static function has_admin_config() {
        return true;
    }

    public function admin_config_form(&$mform) {
        if ($errorcode = self::plugin_sanity_check()) {
            return $errorcode; // processing stops when we return a string.
        }
        if (!empty($this) && $errorcode = $this->instance_sanity_check()) {
            return $errorcode;
        }
        $strrequired = get_string('required');
        $hosts = self::get_mnet_hosts(); // this is called by sanity check but it's ok because it's cached
        foreach ($hosts as $host) {
            $hosts[$host->id] = $host->name;
        }
        $mform->addElement('select', 'mnethostid', get_string('mnethost', 'portfolio_mahara'), $hosts);
        $mform->addRule('mnethostid', $strrequired, 'required', null, 'client');
    }


    public static function plugin_sanity_check() {
        /* @todo more here like
            - check for services in the plugins that are configured
        */
        global $CFG, $DB;
        $errorcode = 0;
        if (!isset($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode != 'strict') {
            $errorcode =  PORTFOLIO_MAHARA_ERR_NETWORKING_OFF;
        }
        if (!self::get_mnet_hosts()) {
            $errorcode =  PORTFOLIO_MAHARA_ERR_NOHOSTS;
        }
        if (!empty($errorcode)) { // disable the plugins // @todo
            $DB->set_field('portfolio_instance', 'visible', 0, array('plugin' => 'mahara'));
        }
        return $errorcode;
    }

    private static function get_mnet_hosts() {
        global $DB, $CFG;
        static $hosts;
        if (isset($this) && is_object($this) && isset($this->hosts)) {
            return $this->hosts;
        } else if (!isset($this) && isset($hosts)) {
            return $hosts;
        }
        $hosts = $DB->get_records_sql('  SELECT
                                    h.id,
                                    h.wwwroot,
                                    h.ip_address,
                                    h.name,
                                    h.public_key,
                                    h.public_key_expires,
                                    h.transport,
                                    h.portno,
                                    h.last_connect_time,
                                    h.last_log_id,
                                    h.applicationid,
                                    a.name as app_name,
                                    a.display_name as app_display_name,
                                    a.xmlrpc_server_url
                                FROM {mnet_host} h
                                    JOIN {mnet_application} a ON h.applicationid=a.id
                                    JOIN {mnet_host2service} hs1 ON hs1.hostid = h.id
                                    JOIN {mnet_service} s1 ON hs1.serviceid = s1.id
                                    JOIN {mnet_host2service} hs2 ON hs2.hostid = h.id
                                    JOIN {mnet_service} s2 ON hs2.serviceid = s2.id
                                    JOIN {mnet_host2service} hs3 ON hs3.hostid = h.id
                                    JOIN {mnet_service} s3 ON hs3.serviceid = s3.id
                                WHERE
                                    h.id <> ? AND
                                    h.deleted = 0 AND
                                    a.name = ? AND
                                    s1.name = ? AND hs1.publish = ? AND
                                    s2.name = ? AND hs2.subscribe = ? AND
                                    s3.name = ? AND hs3.subscribe = ?',
                        array($CFG->mnet_localhost_id, 'mahara', 'sso_idp', 1, 'sso_sp', 1, 'pf', 1));;
        if (empty($hosts)) { $hosts = array(); }
        if (isset($this) && is_object($this)) {
            $this->hosts = $hosts;
        }
        return $hosts;
    }

    public function prepare_package() {
        $files = $this->exporter->get_tempfiles();
        foreach ($files as $f) {
            $this->filesmanifest[$f->get_contenthash()] = array(
                'filename' => $f->get_filename(),
                'sha1'     => $f->get_contenthash(),
            );
        }
        $zipper = new zip_packer();

        $filename = 'portfolio-export.zip';
        if ($newfile = $zipper->archive_to_storage($files, SYSCONTEXTID, 'portfolio_exporter', $this->exporter->get('id'), '/final/', $filename, $this->user->id)) {
            $this->set('file', $newfile);
            return true;
        }
        return false;
    }

    public function send_package() {
        global $CFG;
        global $MNET;
        if (empty($MNET)) {
            $MNET = new mnet_environment();
            $MNET->init();
        } // no idea why this happens :(
        // send the 'content_ready' request to mahara
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        $client = new mnet_xmlrpc_client();
        $client->set_method('portfolio/mahara/lib.php/send_content_ready');
        $client->add_param($this->token);
        $client->add_param($this->get('user')->username);
        $client->add_param($this->resolve_format());
        $client->add_param($this->filesmanifest);
        $client->add_param($this->get_export_config('wait'));
        $this->ensure_mnethost();
        if (!$client->send($this->mnethost)) {
            foreach ($client->error as $errormessage) {
                list($code, $message) = array_map('trim',explode(':', $errormessage, 2));
                $message .= "ERROR $code:<br/>$errormessage<br/>";
            }
            throw new portfolio_export_exception($this->get('exporter'), 'failedtoping', 'portfolio_mahara', '', $message);
        }
        // we should get back...  an ok and a status
        // either we've been waiting a while and mahara has fetched the file or has queued it.
        $response = (object)$client->response;
        if (!$response->status) {
            throw new portfolio_export_exception($this->get('exporter'), 'failedtoping', 'portfolio_mahara');
        }
        return true;
    }

    public function get_continue_url() {
        $this->ensure_mnethost();
        return $this->hostrecord->wwwroot . '/artefact/file/'; // @todo penny this might change later when we change formats.
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        global $CFG;
        return $CFG->wwwroot . '/portfolio/type/mahara/preconfig.php?id=' . $this->exporter->get('id');
    }

    public function verify_file_request_params($params) {
        return false;
        // the data comes from an xmlrpc request,
        // not a request to file.php
    }

    /**
    * sends the 'content_intent' ping to mahara
    * if all goes well, this will set the 'token' and 'sendtype' member variables.
    */
    public function send_intent() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        $client = new mnet_xmlrpc_client();
        $client->set_method('portfolio/mahara/lib.php/send_content_intent');
        $client->add_param($this->get('user')->username);
        $this->ensure_mnethost();
        if (!$client->send($this->mnethost)) {
            foreach ($client->error as $errormessage) {
                list($code, $message) = array_map('trim',explode(':', $errormessage, 2));
                $message .= "ERROR $code:<br/>$errormessage<br/>";
            }
            throw new portfolio_export_exception($this->get('exporter'), 'failedtoping', 'portfolio_mahara', '', $message);
        }
        // we should get back... the send type and a shared token
        $response = (object)$client->response;
        if (empty($response->sendtype) || empty($response->token)) {
            throw new portfolio_export_exception($this->get('exporter'), 'senddisallowed', 'portfolio_mahara');
        }
        switch ($response->sendtype) {
            case 'immediate':
                $this->sendtype = PORTFOLIO_MAHARA_IMMEDIATE;
                break;
            case 'queue':
                $this->sendtype = PORTFOLIO_MAHARA_QUEUE;
                break;
            case 'none':
            default:
                throw new portfolio_export_exception($this->get('exporter'), 'senddisallowed', 'portfolio_mahara');
        }
        $this->token = $response->token;
        $this->get('exporter')->save();
        // put the entry in the mahara queue table now too
        $q = new stdClass;
        $q->token = $this->token;
        $q->transferid = $this->get('exporter')->get('id');
        $DB->insert_record('portfolio_mahara_queue', $q);
    }

    private function ensure_mnethost() {
        if (!empty($this->hostrecord) && !empty($this->mnethost)) {
            return;
        }
        global $DB;
        $this->hostrecord = $DB->get_record('mnet_host', array('id' => $this->get_config('mnethostid')));
        $this->mnethost = new mnet_peer();
        $this->mnethost->set_wwwroot($this->hostrecord->wwwroot);
    }

    public static function mnet_publishes() {
        $pf= array();
        $pf['name']        = 'pf'; // Name & Description go in lang file
        $pf['apiversion']  = 1;
        $pf['methods']     = array('send_content_intent', 'send_content_ready', 'fetch_file');

        return array($pf);
    }

    /**
    * xmlrpc (mnet) function to get the file.
    * reads in the file and returns it base_64 encoded
    * so that it can be enrypted by mnet.
    *
    * @param string $token the token recieved previously during send_content_intent
    */
    public static function fetch_file($token) {
        global $DB, $MNET_REMOTE_CLIENT;;
        try {
            $transferid = $DB->get_field('portfolio_mahara_queue', 'transferid', array('token' => $token));
            $exporter = portfolio_exporter::rewaken_object($transferid);
        } catch (portfolio_exception $e) {
            exit(mnet_server_fault(8010, 'invalid transfer id'));
        }
        if ($exporter->get('instance')->get_config('mnethostid') != $MNET_REMOTE_CLIENT->id) {
            exit(mnet_server_fault(8011, "remote host didn't match saved host"));
        }
        global $CFG;
        try {
            $i = $exporter->get('instance');
            $f = $i->get('file');
            if (empty($f)) {
                exit(mnet_server_fault(8012, 'could not find file in transfer object - weird error'));
            }
            $c = $f->get_content();
            $contents = base64_encode($c);
        } catch (Exception $e) {
            exit(mnet_server_fault(8013, 'could not get file to send'));
        }
        $exporter->process_stage_cleanup(true);
        return $contents;
    }

    public function cleanup() {
        global $DB;
        $DB->delete_records('portfolio_mahara_queue', array('transferid' => $this->get('exporter')->get('id'), 'token' => $this->token));
    }


    private function resolve_format() {
        $thisformat = $this->get_export_config('format');
        $allformats = portfolio_supported_formats();
        $thisobj = new $allformats[$thisformat];
        foreach ($this->supported_formats() as $f) {
            $class = $allformats[$f];
            if ($thisobj instanceof $class) {
                return $f;
            }
        }
    }

/*
    public function __wakeup() {
        global $CFG;
        if (empty($CFG)) {
            return; // too early
        }
        require_once($CFG->dirroot . '/mnet/lib.php');
    }
*/
}

?>
