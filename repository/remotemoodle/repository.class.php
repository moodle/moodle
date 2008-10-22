<?php
/**
 * repository_local class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 *
 */
class repository_remotemoodle extends repository {

    /**
     *
     * @global <type> $SESSION
     * @global <type> $action
     * @global <type> $CFG
     * @param <type> $repositoryid
     * @param <type> $context
     * @param <type> $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        // TODO:
        // get the parameter from client side
        // $this->context can be used here.
        // When user upload a file, $action == 'upload'
        // You can use $_FILES to find that file
    }

    /**
     *
     * @global <type> $SESSION
     * @param <type> $ajax
     * @return <type>
     */
    public function print_login($ajax = true) {
        global $SESSION;
        // TODO
        // Return file list in moodle
        return $this->get_listing();
    }

    /**
     *
     * @param <type> $search_text
     * @return <type>
     */
    public function search($search_text) {
        return $this->get_listing('', $search_text);
    }

    /**
     *
     * @global <type> $MNET
     */
    private function ensure_environment() {
        global $MNET;
         
        if (empty($MNET)) {
            $MNET = new mnet_environment();
            $MNET->init();
        }
    }

    /**
     *
     * @global <type> $CFG
     * @param <type> $encodedpath
     * @param <type> $search
     * @return <type>
     */
    public function get_listing($encodedpath = '', $search = '') {
        global $CFG, $DB, $USER;
        
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        //retrieve the host url
        $this->ensure_environment();
      
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer']));
             
        $mnetauth = get_auth_plugin('mnet');
        $url      = $mnetauth->start_jump_session($host->id, '');
     
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);
  
        //connect to the remote moodle and retrieve the list of files
        $client = new mnet_xmlrpc_client();
      
        $client->set_method('system/listFiles');
        $client->add_param($USER->username); 
         
        $client->send($mnet_peer);
        
        $services = $client->response;

        return $services;
    }

   

      /**
     * Download a file
     * @global object $CFG
     * @param string $url the url of file
     * @param string $file save location
     * @return string the location of the file
     * @see curl package
     */
    public function get_file($url, $file = '') {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        //retrieve the host url
        $this->ensure_environment();

        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer']));
        $mnetauth = get_auth_plugin('mnet');
        $mnetauth->start_jump_session($host->id, '');

        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);

        //connect to the remote moodle and retrieve the list of files
        $client = new mnet_xmlrpc_client();

        $client->set_method('system/retrieveFile');
        $client->add_param($USER->username);
        $client->add_param($url);

        $client->send($mnet_peer);

        $services = $client->response;
        $content = base64_decode($services[0]);
        $file = $services[1];

        if (!file_exists($CFG->dataroot.'/temp/download')) {
            mkdir($CFG->dataroot.'/temp/download/', 0777, true);
        }
        if (is_dir($CFG->dataroot.'/temp/download')) {
            $dir = $CFG->dataroot.'/temp/download/';
        }
        if (empty($file)) {
            $file = uniqid('repo').'_'.time().'.tmp';
        }
        if (file_exists($dir.$file)) {
            $file = uniqid('m').$file;
        }
        
        $fp = fopen($dir.$file, 'w');
        fwrite($fp,$content);
        fclose($fp);
         
        return $dir.$file;
       
    }

    /**
     * Add Instance settings input to Moodle form
     * @param <type> $
     */
    public function instance_config_form(&$mform) {
        global $CFG, $DB;
        
        //retrieve all peers
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
                                WHERE
                                    h.id <> ? AND
                                    h.deleted = 0 AND
                                    a.name = ?',
                        array($CFG->mnet_localhost_id, 'moodle'));
        $peers = array();
        foreach($hosts as $host) {
            $peers[$host->id] = $host->name;        
        }

        $mform->addElement('select', 'peer', get_string('peer', 'repository_remotemoodle'),$peers);
        $mform->addRule('peer', get_string('required'), 'required', null, 'client');
    }

    /**
     * Names of the instance settings
     * @return <type>
     */
    public static function get_instance_option_names() {
        return array('peer');
    }
}
?>