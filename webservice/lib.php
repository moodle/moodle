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
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

require_once(dirname(dirname(__FILE__)) . '/lib/formslib.php');

/**
 * Returns detailed information about external function
 * @param string $functionname name of external function
 * @return aray
 */
function ws_get_function_info($functionname) {
    global $CFG, $DB;

    $function = $DB->get_record('external_functions', array('name'=>$functionname), '*', MUST_EXIST);

    $defpath = get_component_directory($function->component);
    if (!file_exists("$defpath/db/services.php")) {
        //TODO: maybe better throw invalid parameter exception
        return null;
    }

    $functions = array();
    include("$defpath/db/services.php");

    if (empty($functions[$functionname])) {
        return null;
    }

    $desc = $functions[$functionname];
    if (empty($desc['classpath'])) {
        $desc['classpath'] = "$defpath/externallib.php";
    } else {
        $desc['classpath'] = "$CFG->dirroot/".$desc['classpath'];
    }
    $desc['component'] = $function->component;

    return $desc;
}

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
            while( false !== ($file = readdir($dh))) {
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
                else {
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
    public static function setListApiFiles( &$files, $directorypath ) {
        global $CFG;

        if(is_dir($directorypath)) { //check that we are browsing a folder not a file

            if( $dh = opendir($directorypath)) {
                while( false !== ($file = readdir($dh))) {

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

    public static function services_discovery() {
        global $CFG, $DB;
        $externalfiles = array();
        webservice_lib::setListApiFiles($externalfiles, $CFG->dirroot);

        $wsnotification = array();

        //retrieve all saved services
        $services = $DB->get_records('external_services', array('custom' => 0)); //we only retrieve not custom service
        $dbservices = array();
        foreach ($services as $service) {
            $dbservices[$service->name] = false; //value false will define obsolote status
        //once we parse all services from the external files
        }

        //retrieve all saved servicefunction association including their function name,
        //service name, function id and service id

        $servicesfunctions = $DB->get_records_sql("SELECT fs.id as id, fs.enabled as enabled, s.name as servicename, s.id as serviceid, f.name as functionname, f.id as functionid
                                    FROM {external_services} s, {external_functions} f, {external_services_functions} fs
                                   WHERE fs.externalserviceid = s.id AND fs.externalfunctionid = f.id AND s.custom = 0");
        $dbservicesfunctions = array();
        foreach ($servicesfunctions as $servicefunction) {
            $dbservicesfunctions[$servicefunction->servicename][$servicefunction->functionname] = array('serviceid' => $servicefunction->serviceid,
                'functionid' => $servicefunction->functionid,
                'id' => $servicefunction->id,
                'notobsolete' => false);
        //once we parse all services from the external files
        }

        foreach ($externalfiles as $file) {
            require($file);
            $classpath = substr($file,strlen($CFG->dirroot)+1); //remove the dir root + / from the file path
            $classpath = substr($classpath,0,strlen($classpath) - 13); //remove /external.php from the classpath
            $classpath = str_replace('/','_',$classpath); //convert all / into _
            $classname = $classpath."_external";
            $api = new $classname();
            if (method_exists($api, 'get_descriptions')) {
                $descriptions = $api->get_descriptions();

                //retrieve all saved function into the DB for this specific external file/component
                $functions = $DB->get_records('external_functions', array('component' => $classpath));
                //remove the obsolete ones
                $dbfunctions = array();
                foreach ($functions as $function) {
                    $dbfunctions[$function->name] = false; //value false is not important we just need the key
                    if (!array_key_exists($function->name, $descriptions)) {
                    //remove all obsolete function from the db
                        $DB->delete_records('external_functions', array('name' => $function->name, 'component' => $classpath));
                    }
                }

                foreach ($descriptions as $functionname => $functiondescription) {
                    if (array_key_exists('service', $functiondescription) && !empty($functiondescription['service'])) { //check that the service has been set in the description
                    //only create the one not already saved into the database
                        if (!array_key_exists($functionname, $dbfunctions)) {
                            $newfunction = new object();
                            $newfunction->component = $classpath;
                            $newfunction->name = $functionname;
                            $DB->insert_record('external_functions', $newfunction);
                            $notifparams = new object();
                            $notifparams->functionname = $functionname;
                            $notifparams->servicename = $functiondescription['service'];
                            $wsnotification[] = get_string('wsinsertfunction','webservice', $notifparams);
                        }

                        //check if the service is into the database
                        if (!array_key_exists($functiondescription['service'], $dbservices)) {
                            $newservice = new object();
                            $newservice->name = $functiondescription['service'];
                            $newservice->enabled = 0;
                            $newservice->custom = 0;
                            $DB->insert_record('external_services', $newservice);
                        }
                        $dbservices[$functiondescription['service']] = true; //mark the service as not obsolete
                    //and add it if it wasn't in the list
                    }
                    else {
                        $errors = new object();
                        $errors->classname = $classname;
                        $errors->functionname = $functionname;
                        throw new moodle_exception("wsdescriptionserviceisempty",'','', $errors);
                    }

                    //check if the couple service/function is into the database
                    if (!array_key_exists($functiondescription['service'], $dbservicesfunctions) || !array_key_exists($functionname, $dbservicesfunctions[$functiondescription['service']])) {
                        $newassociation = new object();
                        $newassociation->externalserviceid = $DB->get_field('external_services','id',array('name' => $functiondescription['service']));
                        $newassociation->externalfunctionid = $DB->get_field('external_functions','id',array('name' => $functionname, 'component' => $classpath));
                        $newassociation->enabled = 0;
                        $DB->insert_record('external_services_functions', $newassociation);
                    }
                    $dbservicesfunctions[$functiondescription['service']][$functionname]['notobsolete'] = true;
                }
            }
            else {
                throw new moodle_exception("wsdoesntextendbaseclass",'','', $classname);
            }
        }

        //remove all obsolete service (not the custom ones)
        foreach ($dbservices as $servicename => $notobsolete) {
            if (!$notobsolete) {
                $DB->delete_records('external_services', array('name' => $servicename));
            }
        }

        //remove all obsolete association (not the custom ones)
        foreach ($dbservicesfunctions as $servicename => $servicefunctions ) {
            foreach ($servicefunctions as $functioname => $servicefunction) {
                if (!$servicefunction['notobsolete']) {
                    $DB->delete_records('external_services_functions', array('id' => $servicefunction['id']));
                    $notifparams = new object();
                    $notifparams->functionname = $functionname;
                    $notifparams->servicename = $servicename;
                    $wsnotification[] = get_string('wsdeletefunction','webservice', $notifparams);
                }
            }
        }

        return $wsnotification;
    }

    /**
     * Check if the Moodle site has the web service protocol enable
     * @global object $CFG
     * @param string $protocol
     */
    function display_webservices_availability($protocol) {
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
        $mform->setType('username', PARAM_RAW);
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
        $mform->setType('settings', PARAM_RAW);
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

/**
 * Form for web service server settings (administration)
 */
final class wsservicesettings_form extends moodleform {
    protected $settings;

    /**
     * Definition of the moodleform
     */
    public function definition() {
        global $DB,$CFG;
        $serviceid = $this->_customdata['serviceid'];
        $mform =& $this->_form;

        $mform->addElement('hidden', 'serviceid', $serviceid);
        $mform->setType('serviceid', PARAM_INT);
        $param = new stdClass();

     //   require_once($CFG->dirroot . '/webservice/'. $settings . '/lib.php');
      //  $servername = $settings.'_server';
      //  $server = new $servername();
      //  $server->settings_form($mform);

        // set the data if we have some.
    //    $data = array();
     //   $option_names = $server->get_setting_names();
    //    foreach ($option_names as $config) {
    //        $data[$config] = get_config($settings, $config);
    //    }
    //    $this->set_data($data);
        $service = $DB->get_record('external_services',array('id' => $serviceid));

        $mform->addElement('text', 'servicename', get_string('servicename', 'webservice'));
        $mform->setDefault('servicename',get_string($service->name, 'webservice'));
        if (!empty($serviceid)) {
            $mform->disabledIf('servicename', 'serviceid', 'eq', $serviceid);
        }

        if (empty($serviceid)) {
            //display list of functions to select
        }

        //display list of functions associated to the service
        
        

        $this->add_action_buttons(true,  get_string('savechanges','admin'));
    }
}


?>
