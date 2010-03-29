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
 * repository_remotemoodle class
 * This plugin allowed to connect a retrieve a file from another Moodle site
 * This is a subclass of repository class
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/repository/lib.php');


class repository_remotemoodle extends repository {

    /**
     * Constructor of remotemoodle plugin, used to setup mnet environment
     * @global object $SESSION
     * @global object $CFG
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $SESSION, $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->mnet = get_mnet_environment();
    }

    /**
     * Retrieve a file for a user of the Moodle client calling this function
     * The file is encoded in base64
     * @global object $DB
     * @global object $USER
     * @param string $username
     * @param string $source
     * @return array
     */
    public function retrieveFile($username, $source) {
        global $DB, $USER;

        $remoteclient = get_mnet_remote_client();

        ///check the the user is known
        ///he has to be previously connected to the server site in order to be in the database
        //TODO: MDL-21318 this looks problematic, because global $USER would need to be set back after this,
        //      also is the user allowed to roam?
        $USER = $DB->get_record('user',array('username' => $username, 'mnethostid' => $remoteclient->id));
        if (empty($USER)) {
            throw new mnet_server_exception(9012, get_string('usernotfound', 'repository_remotemoodle',  $username));
        }

        $file = unserialize(base64_decode($source));
        $contextid = $file[0];
        $filearea = $file[1];
        $itemid = $file[2];
        $filepath = $file[3];
        $filename = $file[4];

        ///check that the user has read permission on this file
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(get_context_instance_by_id($contextid), $filearea, $itemid, $filepath, $filename);
        if (empty($fileinfo)) {
            throw new mnet_server_exception(9013, get_string('usercannotaccess', 'repository_remotemoodle',  $file));
        }

        ///retrieve the file with file API functions and return it encoded in base64
        $fs = get_file_storage();
        $sf = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename);
        $contents = base64_encode($sf->get_content());
        return array($contents, $sf->get_filename());
    }

    /**
     * Retrieve file list for a user of the Moodle client calling this function
     * @global object $DB
     * @global object $USER
     * @global object $CFG
     * @param string $username
     * @param string $search
     * @return array
     */
    public function getFileList($username, $search) {
        global $DB, $USER, $CFG;

        $remoteclient = get_mnet_remote_client();
        ///check the the user is known
        ///he has to be previously connected to the server site in order to be in the database
        //TODO: MDL-21318 this looks problematic, because global $USER would need to be set back after this,
        //      also is the user allowed to roam?
        $USER = $DB->get_record('user',array('username' => $username, 'mnethostid' => $remoteclient->id));
        if (empty($USER)) {
            throw new mnet_server_exception(9012, get_string('usernotfound', 'repository_remotemoodle',  $username));
        }

        try {
            return repository::get_user_file_tree($search);
        }
        catch (Exception $e) {
            throw new mnet_server_exception(9014, get_string('failtoretrievelist', 'repository_remotemoodle'));
        }
    }

    /**
     * Display the file listing - no login required
     * @return array
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Display the file listing for the search term
     * @param string $search_text
     * @return array
     */
    public function search($search_text) {
        return $this->get_listing('', '', $search_text);
    }

    /**
     * Retrieve the file listing - file picker function
     * @global object $CFG
     * @global object $DB
     * @global object $USER
     * @param string $encodedpath
     * @param int $page
     * @param string $search
     * @return array
     */
    public function get_listing($encodedpath = '', $page = '', $search = '') {
        global $CFG, $DB, $USER;

        ///check that the host has a version >2.0
        ///for that we check that the host has the getFileList() method implemented
        ///We also check that this method has been activated (if it is not
        ///the method will not be returned by the system method system/listMethods)
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');

        ///check that the peer has been setup
        if (!array_key_exists('peer',$this->options)) {
            echo json_encode(array('e'=>get_string('error').' 9010: '.get_string('hostnotfound','repository_remotemoodle')));
            exit;
        }

        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer'])); //need to retrieve the host url

        ///check that the peer host exists into the database
        if (empty($host)) {
           echo json_encode(array('e'=>get_string('error').' 9011: '.get_string('hostnotfound','repository_remotemoodle')));
           exit;
        }

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

        ///connect to the remote moodle and retrieve the list of files
        $client->set_method('repository/remotemoodle/repository.class.php/getFileList');
        $client->add_param($USER->username);
        $client->add_param($search);

        ///call the method and manage host error
        if (!$client->send($mnet_peer)) {
            $message =" ";
            foreach ($client->error as $errormessage) {
                $message .= "ERROR: $errormessage . ";
            }
            echo json_encode(array('e'=>$message)); //display all error messages
            exit;
        }

        $services = $client->response;
        ///display error message if we could retrieve the list or if nothing were returned
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

        ///set mnet environment and set the mnet host
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer'])); //retrieve the host url
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);

        ///create the client and set the method to call
        $client = new mnet_xmlrpc_client();
        $client->set_method('repository/remotemoodle/repository.class.php/retrieveFile');
        $client->add_param($USER->username);
        $client->add_param($url);

        ///call the method and manage host error
        if (!$client->send($mnet_peer)) {
            $message =" ";
            foreach ($client->error as $errormessage) {
                $message .= "ERROR: $errormessage . ";
            }
            echo json_encode(array('e'=>$message));
            exit;
        }

        $services = $client->response; //service contains the file content in the first case of the array,
                                       //and the filename in the second

        //the content has been encoded in base64, need to decode it
        $content = base64_decode($services[0]);
        $file = $services[1]; //filename

        ///create a temporary folder with a file
        $path = $this->prepare_file($file);
        ///fill the file with the content
        $fp = fopen($path, 'w');
        fwrite($fp,$content);
        fclose($fp);

        return array('path'=>$path);

    }

    /**
     * Add Instance settings input to Moodle form
     * @global object $CFG
     * @global object $DB
     * @param object $mform
     */
    public function instance_config_form($mform) {
        global $CFG, $DB;

        //retrieve only Moodle peers
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

        if (empty($peers)) {
            $mform->addElement('static', null, '',  get_string('nopeer','repository_remotemoodle'));
        }
    }

    /**
     * Names of the instance settings
     * @return array
     */
    public static function get_instance_option_names() {
        ///the administrator just need to set a peer
        return array('peer');
    }
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
}

