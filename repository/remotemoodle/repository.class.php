<?php
/**
 * repository_remotemoodle class
 * This is a subclass of repository class
 * @author Jerome Mouneyrac
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/repository/lib.php');


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
    }

    /**
     *
     * @return <type>
     */
    public static function mnet_publishes() {
        $pf= array();
        $pf['name']        = 'remoterep'; // Name & Description go in lang file
        $pf['apiversion']  = 1;
        $pf['methods']     = array('getFileList', 'retrieveFile');

        return array($pf);
    }

    /**
     *
     * @global <type> $DB
     * @global <type> $USER
     * @global <type> $MNET_REMOTE_CLIENT
     * @param <type> $username
     * @param <type> $pathnamehash
     * @return <type>
     */
    public function retrieveFile($username, $source) {
        global $DB, $USER, $MNET_REMOTE_CLIENT;
        $USER = $DB->get_record('user',array('username' => $username, 'mnethostid' => $MNET_REMOTE_CLIENT->id));
        $file = unserialize(base64_decode($source));
 
        $contextid = $file[0];
        $filearea = $file[1];
        $itemid = $file[2];
        $filepath = $file[3];
        $filename = $file[4];
             
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(get_context_instance_by_id($contextid), $filearea, $itemid, $filepath, $filename);
        if (empty($fileinfo)) {
            exit(mnet_server_fault(9016, get_string('usercannotaccess', 'repository_remotemoodle',  $file)));
        }
         
        $fs = get_file_storage();
        $sf = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename);
        $contents = base64_encode($sf->get_content());
        return array($contents, $sf->get_filename());
    }

    /**
     *
     * @global <type> $DB
     * @global <type> $USER
     * @global <type> $MNET_REMOTE_CLIENT
     * @global <type> $CFG
     * @param <type> $username
     * @return <type>
     */
    public function getFileList($username, $search) {
        global $DB, $USER, $MNET_REMOTE_CLIENT, $CFG;     
        $USER = $DB->get_record('user',array('username' => $username, 'mnethostid' => $MNET_REMOTE_CLIENT->id));
        if (empty($USER)) {
            exit(mnet_server_fault(9016, get_string('usernotfound', 'repository_remotemoodle',  $username)));
        }
        $ret = array();
        $ret['nologin'] = true;
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
        $browser = get_file_browser();
        $itemid = null;
        $filename = null;
        $filearea = null;
        $path = '/';
        $ret['dynload'] = false;

        if ($fileinfo = $browser->get_file_info(get_system_context(), $filearea, $itemid, $path, $filename)) {

            $ret['path'] = array();
            $params = $fileinfo->get_params();
            $filearea = $params['filearea'];
            $ret['path'][] = $this->_encode_path($filearea, $path, $fileinfo->get_visible_name());
            if ($fileinfo->is_directory()) {
                $level = $fileinfo->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $ret['path'][] = $this->_encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }
            }
            $filecount = $this->build_tree($fileinfo, $search, $ret['dynload'], $ret['list']);
            $ret['path'] = array_reverse($ret['path']);
        }

        if (empty($ret['list'])) {
            exit(mnet_server_fault(9016, get_string('emptyfilelist', 'repository_local')));
        } else {
            return $ret;
        }
        
    }

    /**
     *
     * @param <type> $filearea
     * @param <type> $path
     * @param <type> $visiblename
     * @return <type>
     */
    public function _encode_path($filearea, $path, $visiblename) {
        return array('path'=>serialize(array($filearea, $path)), 'name'=>$visiblename);
    }

   /**
     * Builds a tree of files, to be used by get_listing(). This function is
     * then called recursively.
     *
     * @param $fileinfo an object returned by file_browser::get_file_info()
     * @param $search searched string
     * @param $dynamicmode bool no recursive call is done when in dynamic mode
     * @param $list - the array containing the files under the passed $fileinfo
     * @returns int the number of files found
     *
     * todo: take $search into account, and respect a threshold for dynamic loading
     */
    public function build_tree($fileinfo, $search, $dynamicmode, &$list) {
        global $CFG;

        $filecount = 0;
        $children = $fileinfo->get_children();

        foreach ($children as $child) {
            $filename = $child->get_visible_name();
            $filesize = $child->get_filesize();
            $filesize = $filesize ? display_size($filesize) : '';
            $filedate = $child->get_timemodified();
            $filedate = $filedate ? userdate($filedate) : '';
            $filetype = $child->get_mimetype();

            if ($child->is_directory()) {
                $path = array();
                $level = $child->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $path[] = $this->_encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }

                $tmp = array(
                    'title' => $child->get_visible_name(),
                    'size' => 0,
                    'date' => $filedate,
                    'path' => array_reverse($path),
                    'thumbnail' => $CFG->pixpath .'/f/folder.gif'
                );

                $_search = $search;
                if ($search && stristr($tmp['title'], $search) !== false) {
                    $_search = false;
                }
                $tmp['children'] = array();
                $_filecount = $this->build_tree($child, $_search, $dynamicmode, $tmp['children']);
                if ($search && $_filecount) {
                    $tmp['expanded'] = 1;
                }

                if (!$search || $_filecount || (stristr($tmp['title'], $search) !== false)) {
                    $list[] = $tmp;
                    $filecount += $_filecount;
                }

            } else { // not a directory
                // skip the file, if we're in search mode and it's not a match
                if ($search && (stristr($filename, $search) === false)) {
                    continue;
                }

                //retrieve the stored file id
                $fs = get_file_storage();
                $params = $child->get_params();

                $source = serialize(array($params['contextid'], $params['filearea'], $params['itemid'], $params['filepath'], $params['filename']));
                $list[] = array(
                    'title' => $filename,
                    'size' => $filesize,
                    'date' => $filedate,
                    'source' => base64_encode($source),
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo_from_type("icon", $filetype)
                );

                $filecount++;
            }
        }
        return $filecount;
    }


    /**
     *
     * @global <type> $SESSION
     * @param <type> $ajax
     * @return <type>
     */
    public function print_login($ajax = true) {
        global $SESSION;
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

        //check that the host has a version >2.0
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        $this->ensure_environment();
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer']));
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);
        $client = new mnet_xmlrpc_client();
        $client->set_method('system/listMethods');
        $client->send($mnet_peer);
        $services = $client->response;
        if (array_search('repository/remotemoodle/repository.class.php/getFileList', $services) === false) {
            echo json_encode(array('e'=>get_string('connectionfailure','repository_remotemoodle')));
            exit;
        }
         
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        //retrieve the host url
        $this->ensure_environment();
      
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer']));
             
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);
  
        //connect to the remote moodle and retrieve the list of files       
        $client = new mnet_xmlrpc_client();
      
        $client->set_method('repository/remotemoodle/repository.class.php/getFileList');
        $client->add_param($USER->username);
        $client->add_param($search);
         
        if (!$client->send($mnet_peer)) {
            $message =" ";
            foreach ($client->error as $errormessage) {
                $message .= "ERROR: $errormessage . ";
            }
            echo json_encode(array('e'=>$message));
            exit;
        }
        
        $services = $client->response;

         if (empty($services)) {
            echo json_encode(array('e'=>get_string('failtoretrievelist','repository_remotemoodle')));
            exit;
        }

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

        $client->set_method('repository/remotemoodle/repository.class.php/retrieveFile');
        $client->add_param($USER->username);
        $client->add_param($url);

        if (!$client->send($mnet_peer)) {
            $message =" ";
            foreach ($client->error as $errormessage) {
                $message .= "ERROR: $errormessage . ";
            }
            echo json_encode(array('e'=>$message));
            exit;
        }

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
                                    a.name = ? AND
                                    h.name <> ?',
                        array($CFG->mnet_localhost_id, 'moodle', 'All Hosts'));
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