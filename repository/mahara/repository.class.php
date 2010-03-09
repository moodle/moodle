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
 * repository_mahara class
 * This plugin allowed to connect a retrieve a file from Mahara site
 * This is a subclass of repository class
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Jerome Mouneyrac <mouneyrac@moodle.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/repository/lib.php');

class repository_mahara extends repository {

    /**
     * Constructor
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
        $this->mnet = get_mnet_environment();
    }

  /**
     *
     * @return <type>
     */
    public function check_login() {
        //check session
        global $SESSION;
        return !empty($SESSION->loginmahara);
    }


    /**
     * Display the file listing - no login required
     * @global <type> $SESSION
     * @param <type> $ajax
     * @return <type>
     */
    public function print_login($ajax = true) {
        global $SESSION, $CFG, $DB;
        //jump to the peer to create a session

        $mnetauth = get_auth_plugin('mnet');
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer'])); //need to retrieve the host url
        $url = $mnetauth->start_jump_session($host->id, '/repository/repository_ajax.php?callback=yes&repo_id='.$this->id, true);

        //set session
        $SESSION->loginmahara = true;

        $ret = array();
        $popup_btn = new stdclass;
        $popup_btn->type = 'popup';
        $popup_btn->url = $url;
        $ret['login'] = array($popup_btn);
        return $ret;
    }

    /**
     * Display the file listing for the search term
     * @param <type> $search_text
     * @return <type>
     */
    public function search($search_text) {
        return $this->get_listing('', '', $search_text);
    }

    /**
     * Retrieve the file listing - file picker function
     * @global <type> $CFG
     * @global <type> $DB
     * @global <type> $USER
     * @param <type> $encodedpath
     * @param <type> $search
     * @return <type>
     */
    public function get_listing($path = null, $page = 1, $search = '') {
        global $CFG, $DB, $USER, $OUTPUT;

        ///check that Mahara has a good version
        ///We also check that the "get file list" method has been activated (if it is not
        ///the method will not be returned by the system method system/listMethods)
        require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');

        ///check that the peer has been setup
        if (!array_key_exists('peer',$this->options)) {
            echo json_encode(array('e'=>get_string('error').' 9010: '.get_string('hostnotfound','repository_mahara')));
            exit;
        }

        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer'])); //need to retrieve the host url

        ///check that the peer host exists into the database
        if (empty($host)) {
            echo json_encode(array('e'=>get_string('error').' 9011: '.get_string('hostnotfound','repository_mahara')));
            exit;
        }

        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);
        $client = new mnet_xmlrpc_client();
        $client->set_method('system/listMethods');
        $client->send($mnet_peer);
        $services = $client->response;

        if (empty($search)) {
            $methodname = 'get_folder_files';
        } else {
            $methodname = 'search_folders_and_files';
        }

        if (array_key_exists('repository/mahara/repository.class.php/'.$methodname, $services) === false) {
            echo json_encode(array('e'=>get_string('connectionfailure','repository_mahara')));
            exit;
        }

        ///connect to the remote moodle and retrieve the list of files
        $client->set_method('repository/mahara/repository.class.php/'.$methodname);
        $client->add_param($USER->username);
        if (empty($search)) {
             $client->add_param($path);
        } else {
             $client->add_param($search);
        }

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
        if (empty($search)) {
             $newpath = $services[0];
            $filesandfolders = $services[1];
        } else {
            $newpath = '';
            $filesandfolders = $services;
        }

        ///display error message if we could retrieve the list or if nothing were returned
        if (empty($filesandfolders)) {
            echo json_encode(array('e'=>get_string('failtoretrievelist','repository_mahara')));
            exit;
        }


        $list = array();
         if (!empty($filesandfolders['folders'])) {
            foreach ($filesandfolders['folders'] as $folder) {
                $list[] =  array('path'=>$folder['id'], 'title'=>$folder['title'], 'date'=>$folder['mtime'], 'size'=>'0', 'children'=>array(), 'thumbnail' => $OUTPUT->pix_url('f/folder'));
            }
        }
        if (!empty($filesandfolders['files'])) {
            foreach ($filesandfolders['files'] as $file) {
                if ($file['artefacttype'] == 'image') {
                    $thumbnail = $host->wwwroot."/artefact/file/download.php?file=".$file['id']."&size=70x55";
                } else {
                    $thumbnail = $OUTPUT->pix_url(file_extension_icon( $file['title'], 32));
                }
                $list[] = array( 'title'=>$file['title'], 'date'=>$file['mtime'], 'source'=>$file['id'], 'thumbnail' => $thumbnail);
            }
        }


        $filepickerlisting = array(
            'path' => $newpath,
            'dynload' => 1,
            'nosearch' => 0,
            'list'=> $list,
            'manage'=> $host->wwwroot.'/artefact/file/'
        );

        return $filepickerlisting;
    }



    /**
     * Download a file
     * @global object $CFG
     * @param string $url the url of file
     * @param string $file save location
     * @return string the location of the file
     * @see curl package
     */
    public function get_file($id, $file = '') {
        global $CFG, $DB, $USER;

        ///set mnet environment and set the mnet host
        $host = $DB->get_record('mnet_host',array('id' => $this->options['peer'])); //retrieve the host url
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($host->wwwroot);

        ///create the client and set the method to call
        $client = new mnet_xmlrpc_client();
        $client->set_method('repository/mahara/repository.class.php/get_file');
        $client->add_param($USER->username);
        $client->add_param($id);

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

        return $path;

    }

    /**
     * Add Instance settings input to Moodle form
     * @global <type> $CFG
     * @global <type> $DB
     * @param <type> $
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
            array($CFG->mnet_localhost_id, 'mahara', 'All Hosts'));
        $peers = array();
        foreach($hosts as $host) {
            $peers[$host->id] = $host->name;
        }


        $mform->addElement('select', 'peer', get_string('peer', 'repository_mahara'),$peers);
        $mform->addRule('peer', get_string('required'), 'required', null, 'client');

        if (empty($peers)) {
            $mform->addElement('static', null, '',  get_string('nopeer','repository_mahara'));
        }
    }

    /**
     * Names of the instance settings
     * @return <type>
     */
    public static function get_instance_option_names() {
        ///the administrator just need to set a peer
        return array('peer');
    }
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
}

