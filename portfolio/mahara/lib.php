<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains the class definition for the mahara portfolio plugin
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage portfolio
 * @copyright 2009 Penny Leach
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('PORTFOLIO_MAHARA_ERR_NETWORKING_OFF', 'err_networkingoff');
define('PORTFOLIO_MAHARA_ERR_NOHOSTS', 'err_nomnethosts');
define('PORTFOLIO_MAHARA_ERR_INVALIDHOST', 'err_invalidhost');
define('PORTFOLIO_MAHARA_ERR_NOMNETAUTH', 'err_nomnetauth');

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/plugin.php');
require_once($CFG->libdir . '/portfolio/exporter.php');
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
    private $totalsize; // total size of all included files added together
    private $continueurl; // if we've been sent back a specific url to continue to (eg folder id)

    protected function init() {
        $this->mnet = get_mnet_environment();
    }

    public function __wakeup() {
        $this->mnet = get_mnet_environment();
    }

    public static function get_name() {
        return get_string('pluginname', 'portfolio_mahara');
    }

    public static function get_allowed_config() {
        return array('mnethostid', 'enableleap2a');
    }

    public function supported_formats() {
        if ($this->get_config('enableleap2a')) {
            return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_LEAP2A);
        }
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

    public static function admin_config_form(&$mform) {
        $strrequired = get_string('required');
        $hosts = self::get_mnet_hosts(); // this is called by sanity check but it's ok because it's cached
        foreach ($hosts as $host) {
            $hosts[$host->id] = $host->name;
        }
        $mform->addElement('select', 'mnethostid', get_string('mnethost', 'portfolio_mahara'), $hosts);
        $mform->addRule('mnethostid', $strrequired, 'required', null, 'client');
        $mform->setType('mnethostid', PARAM_INT);
        $mform->addElement('selectyesno', 'enableleap2a', get_string('enableleap2a', 'portfolio_mahara'));
        $mform->setType('enableleap2a', PARAM_BOOL);
    }

    public function instance_sanity_check() {
        // make sure the host record exists since we don't have referential integrity
        if (!is_enabled_auth('mnet')) {
            return PORTFOLIO_MAHARA_ERR_NOMNETAUTH;
        }
        try {
            $this->ensure_mnethost();
        }
        catch (portfolio_exception $e) {
            return PORTFOLIO_MAHARA_ERR_INVALIDHOST;
        }
        // make sure we have the right services
        $hosts = $this->get_mnet_hosts();
        if (!array_key_exists($this->get_config('mnethostid'), $hosts)) {
            return PORTFOLIO_MAHARA_ERR_INVALIDHOST;
        }
        return 0;
    }

    public static function plugin_sanity_check() {
        global $CFG, $DB;
        $errorcode = 0;
        if (!isset($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode != 'strict') {
            $errorcode =  PORTFOLIO_MAHARA_ERR_NETWORKING_OFF;
        }
        if (!is_enabled_auth('mnet')) {
            $errorcode = PORTFOLIO_MAHARA_ERR_NOMNETAUTH;
        }
        if (!self::get_mnet_hosts()) {
            $errorcode =  PORTFOLIO_MAHARA_ERR_NOHOSTS;
        }
        return $errorcode;
    }

    private static function get_mnet_hosts() {
        global $DB, $CFG;
        static $hosts;
        if (isset($hosts)) {
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
                                    s3.name = ? AND hs3.subscribe = ? AND
                                    s3.name = ? AND hs3.publish = ?',
                        array($CFG->mnet_localhost_id, 'mahara', 'sso_idp', 1, 'sso_sp', 1, 'pf', 1, 'pf', 1));
        return $hosts;
    }

    public function prepare_package() {
        $files = $this->exporter->get_tempfiles();
        $this->totalsize = 0;
        foreach ($files as $f) {
            $this->filesmanifest[$f->get_contenthash()] = array(
                'filename' => $f->get_filename(),
                'sha1'     => $f->get_contenthash(),
                'size'     => $f->get_filesize(),
            );
            $this->totalsize += $f->get_filesize();
        }

        $this->set('file', $this->exporter->zip_tempfiles());  // this will throw a file_exception which the exporter catches separately.
    }

    public function send_package() {
        global $CFG;
        // send the 'content_ready' request to mahara
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        $client = new mnet_xmlrpc_client();
        $client->set_method('portfolio/mahara/lib.php/send_content_ready');
        $client->add_param($this->token);
        $client->add_param($this->get('user')->username);
        $client->add_param($this->resolve_format());
        $client->add_param(array(
            'filesmanifest' => $this->filesmanifest,
            'zipfilesha1'   => $this->get('file')->get_contenthash(),
            'zipfilesize'   => $this->get('file')->get_filesize(),
            'totalsize'     => $this->totalsize,
        ));
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
        if ($response->type =='queued') {
            $this->exporter->set_forcequeue();
        }
        if (isset($response->querystring)) {
            $this->continueurl = $response->querystring;
        }
        // if we're not queuing the logging might have already happened
        $this->exporter->update_log_url($this->get_static_continue_url());
    }

    public function get_static_continue_url() {
        $remoteurl = '';
        if ($this->resolve_format() == 'file') {
            $remoteurl = '/artefact/file/'; // we hopefully get the files that were imported highlighted
        }
        if (isset($this->continueurl)) {
            $remoteurl .= $this->continueurl;
        }
        return $remoteurl;
    }

    public function resolve_static_continue_url($remoteurl) {
        global $CFG;
        $this->ensure_mnethost();
        $u = new moodle_url('/auth/mnet/jump.php', array('hostid' => $this->get_config('mnethostid'), 'wantsurl' => $remoteurl));
        return $u->out();
    }

    public function get_interactive_continue_url() {
        return $this->resolve_static_continue_url($this->get_static_continue_url());
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        global $CFG;
        return $CFG->wwwroot . '/portfolio/mahara/preconfig.php?id=' . $this->exporter->get('id');
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
        if (!$this->hostrecord = $DB->get_record('mnet_host', array('id' => $this->get_config('mnethostid')))) {
            throw new portfolio_plugin_exception(PORTFOLIO_MAHARA_ERR_INVALIDHOST, 'portfolio_mahara');
        }
        $this->mnethost = new mnet_peer();
        $this->mnethost->set_wwwroot($this->hostrecord->wwwroot);
    }

    /**
    * xmlrpc (mnet) function to get the file.
    * reads in the file and returns it base_64 encoded
    * so that it can be enrypted by mnet.
    *
    * @param string $token the token recieved previously during send_content_intent
    */
    public static function fetch_file($token) {
        global $DB;
        $remoteclient = get_mnet_remote_client();
        try {
            if (!$transferid = $DB->get_field('portfolio_mahara_queue', 'transferid', array('token' => $token))) {
                throw new mnet_server_exception(8009, 'mnet_notoken', 'portfolio_mahara');
            }
            $exporter = portfolio_exporter::rewaken_object($transferid);
        } catch (portfolio_exception $e) {
            throw new mnet_server_exception(8010, 'mnet_noid', 'portfolio_mahara');
        }
        if ($exporter->get('instance')->get_config('mnethostid') != $remoteclient->id) {
            throw new mnet_server_exception(8011, 'mnet_wronghost', 'portfolio_mahara');
        }
        global $CFG;
        try {
            $i = $exporter->get('instance');
            $f = $i->get('file');
            if (empty($f) || !($f instanceof stored_file)) {
                throw new mnet_server_exception(8012, 'mnet_nofile', 'portfolio_mahara');
            }
            try {
                $c = $f->get_content();
            } catch (file_exception $e) {
                throw new mnet_server_exception(8013, 'mnet_nofilecontents', 'portfolio_mahara', $e->getMessage());
            }
            $contents = base64_encode($c);
        } catch (Exception $e) {
            throw new mnet_server_exception(8013, 'mnet_nofile', 'portfolio_mahara');
        }
        $exporter->log_transfer();
        $exporter->process_stage_cleanup(true);
        return $contents;
    }

    public function cleanup() {
        global $DB;
        $DB->delete_records('portfolio_mahara_queue', array('transferid' => $this->get('exporter')->get('id'), 'token' => $this->token));
    }


    /**
     * internal helper function, that converts between the format constant,
     * which might be too specific (eg 'image') and the class in our *supported* list
     * which might be higher up the format hierarchy tree (eg 'file')
     */
    private function resolve_format() {
        global $CFG;
        $thisformat = $this->get_export_config('format');
        $allformats = portfolio_supported_formats();
        require_once($CFG->libdir . '/portfolio/formats.php');
        $thisobj = new $allformats[$thisformat];
        foreach ($this->supported_formats() as $f) {
            $class = $allformats[$f];
            if ($thisobj instanceof $class) {
                return $f;
            }
        }
    }
}


