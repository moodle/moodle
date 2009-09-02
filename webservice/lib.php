<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   webservice
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

require_once(dirname(dirname(__FILE__)) . '/lib/formslib.php');

/**
 * web service library
 */
final class webservice_lib {

    /**
     * Return list of all web service protocol into the webservice folder
     * @global <type> $CFG
     * @return <type>
     */
    public static function get_list_protocols() {
        global $CFG;
        $protocols = array();
        $directorypath = $CFG->dirroot . "/webservice";
        if( $dh = opendir($directorypath)) {
            while( false !== ($file = readdir($dh)))
            {
                if( $file == '.' || $file == '..' || $file == 'CVS') {   // Skip '.' and '..'
                    continue;
                }
                $path = $directorypath . '/' . $file;
                ///browse the subfolder
                if( is_dir($path) ) {
                    if ($file != 'db') { //we don't want to browse the 'db' subfolder of webservice folder
                    require_once($path."/lib.php");
                    $classname = $file."_server";
                    $protocols[] = new $classname;
                    }
                }
                ///retrieve api.php file
                else  {
                    continue;
                }
            }
            closedir($dh);
        }
        return $protocols;
    }

    /**
     * Temporary Authentication method to be modified/removed
     * @global <type> $DB
     * @param <type> $token
     * @return <type>
     */
    public static function mock_check_token($token) {
        //fake test
        if ($token == 456) {
            ///retrieve the user
            global $DB;
            $user = $DB->get_record('user', array('username'=>'wsuser', 'mnethostid'=>1));

            if (empty($user)) {
                return false;
            }

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Retrieve all external.php from Moodle (except the one of the exception list)
     * @param <type> $
     * @param <type> $directorypath
     * @return boolean true if n
     */
    public static function setListApiFiles( &$files, $directorypath )
    {
        global $CFG;

        if(is_dir($directorypath)){ //check that we are browsing a folder not a file

            if( $dh = opendir($directorypath))
            {
                while( false !== ($file = readdir($dh)))
                {

                    if( $file == '.' || $file == '..') {   // Skip '.' and '..'
                        continue;
                    }
                    $path = $directorypath . '/' . $file;
                    ///browse the subfolder
                    if( is_dir($path) ) {
                        webservice_lib::setListApiFiles($files, $path);
                    }
                    ///retrieve api.php file
                    else if ($file == "external.php") {
                        $files[] = $path;
                    }
                }
                closedir($dh);

            }
        }

    }

    /**
     * Check if the Moodle site has the web service protocol enable
     * @global object $CFG
     * @param string $protocol
     */
    function display_webservices_availability($protocol){
        global $CFG;

        $available = true;

        echo get_string('webservicesenable','webservice').": ";
        if (empty($CFG->enablewebservices)) {
            echo "<strong style=\"color:red\">".get_string('fail','webservice')."</strong>";
            $available = false;
        } else {
            echo "<strong style=\"color:green\">".get_string('ok','webservice')."</strong>";
        }
        echo "<br/>";

        foreach(webservice_lib::get_list_protocols() as $wsprotocol) {
            if (strtolower($wsprotocol->get_protocolid()) == strtolower($protocol)) {
                echo get_string('protocolenable','webservice',array($wsprotocol->get_protocolid())).": ";
                if ( get_config($wsprotocol-> get_protocolid(), "enable")) {
                    echo "<strong style=\"color:green\">".get_string('ok','webservice')."</strong>";
                } else {
                    echo "<strong style=\"color:red\">".get_string('fail','webservice')."</strong>";
                    $available = false;
                }
                echo "<br/>";
                continue;
            }
        }

        //check debugging
        if ($CFG->debugdisplay) {
            echo "<strong style=\"color:red\">".get_string('debugdisplayon','webservice')."</strong>";
            $available = false;
        }

        return $available;
    }

}

/**
 * Web Service server base class
 */
abstract class webservice_server {

    /**
     * Web Service Protocol name (eg. SOAP, REST, XML-RPC,...)
     * @var String
     */
    private $protocolname;

    /**
     * Web Service Protocol id (eg. soap, rest, xmlrpc...)
     * @var String
     */
    private $protocolid;

    public function __construct() {
    }

    abstract public function run();

    public function get_protocolname() {
        return $this->protocolname;
    }

    public function get_protocolid() {
        return $this->protocolid;
    }

    public function set_protocolname($protocolname) {
        $this->protocolname = $protocolname;
    }

    public function set_protocolid($protocolid) {
        $this->protocolid = $protocolid;
    }

    public function get_enable() {
        return get_config($this->get_protocolid(), "enable");
    }

    public function set_enable($enable) {
        set_config("enable", $enable, $this->get_protocolid());
    }

    /**
     * Names of the server settings
     * @return array
     */
    public static function get_setting_names() {
        return array();
    }

    public function settings_form(&$mform) {
    }

}

/**
 * Temporary authentication class to be removed/modified
 */
class ws_authentication {
    /**
     *
     * @param object|struct $params
     * @return integer
     */
    function get_token($params) {
        $params->username = clean_param($params->username, PARAM_ALPHANUM);
        $params->password = clean_param($params->password, PARAM_ALPHANUM);
        if ($params->username == 'wsuser' && $params->password == 'wspassword') {
            return '456';
        } else {
            throw new moodle_exception('wrongusernamepassword');
        }      
    }
}

/**
 * Form for web service user settings (administration)
 */
final class wsuser_form extends moodleform {
    protected $username;

    /**
     * Definition of the moodleform
     */
    public function definition() {
        global $DB;
        $this->username = $this->_customdata['username'];
        $mform =& $this->_form;

        $mform->addElement('hidden', 'username', $this->username);
        $param = new stdClass();
        $param->username = $this->username;
        $wsuser = $DB->get_record("user", array("username" => $this->username));

        $mform->addElement('text', 'ipwhitelist', get_string('ipwhitelist', 'admin'), array('value'=>get_user_preferences("ipwhitelist", "", $wsuser->id),'size' => '40'));
        $mform->addElement('static', null, '',  get_string('ipwhitelistdesc','admin', $param));

        $this->add_action_buttons(true, get_string('savechanges','admin'));
    }
}

/**
 * Form for web service server settings (administration)
 */
final class wssettings_form extends moodleform {
    protected $settings;

    /**
     * Definition of the moodleform
     */
    public function definition() {
        global $DB,$CFG;
        $settings = $this->_customdata['settings'];
        $mform =& $this->_form;

        $mform->addElement('hidden', 'settings', $settings);
        $param = new stdClass();

        require_once($CFG->dirroot . '/webservice/'. $settings . '/lib.php');
        $servername = $settings.'_server';
        $server = new $servername();
        $server->settings_form($mform);

        // set the data if we have some.
        $data = array();
        $option_names = $server->get_setting_names();
        foreach ($option_names as $config) {
            $data[$config] = get_config($settings, $config);
        }
        $this->set_data($data);


        $this->add_action_buttons(true, get_string('savechanges','admin'));
    }
}

?>
