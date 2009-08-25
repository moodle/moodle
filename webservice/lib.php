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
     * Generate web service description array from the phpdoc for a given class
     * @param string $file the class file
     * @param string $class the class name
     * @return array description
     *
     *
       -------
       Example
       -------
     * Docnlock: @ subparam string $params:searches->search - the string to search
     * $params is considered as the first element, searches the second, and search the terminal
     * Except the terminal element, all other will be generated as an array
     * => left element are generated as an associative array.
     * If the following character is ':' so the right element is a key named 'multiple:element_name'
     * If the following character is '->' so the right element will be named 'element_name'
     * Rule: If a key is named 'multiple:xxx' other key must be 'multiple:yyy'

       Docblock of  mock_function
       ---------------------------
       @ param array|struct $params
       @ subparam string $params:searches->search - the string to search
       @ subparam string $params:searches->search2 optional - optional string to search
       @ subparam string $params:searches->search3 - the string to search
       @ subparam string $params:airport->planes:plane->company->employees:employee->name - name of a employee of a company of a plane of an airport
       @ return array users
       @ subreturn integer $users:user->id
       @ subreturn integer $users:user->auth

       Generated description array
       ---------------------------
       description["mock_function"]=>
              array(3) {
                ["params"]=>
                array(2) {
                  ["multiple:searches"]=>
                  array(2) {
                    ["search"]=>
                    string(6) "string"
                    ["search3"]=>
                    string(6) "string"
                  }
                  ["multiple:airport"]=>
                  array(1) {
                    ["planes"]=>
                    array(1) {
                      ["multiple:plane"]=>
                      array(1) {
                        ["company"]=>
                        array(1) {
                          ["employees"]=>
                          array(1) {
                            ["multiple:employee"]=>
                            array(1) {
                              ["name"]=>
                              string(6) "string"
                            }
                          }
                        }
                      }
                    }
                  }
                }
                ["optional"]=>
                array(1) {
                  ["multiple:searches"]=>
                  array(1) {
                    ["search2"]=>
                    string(6) "string"
                  }
                }
                ["return"]=>
                array(1) {
                  ["multiple:user"]=>
                  array(13) {
                    ["id"]=>
                    string(7) "integer"
                    ["auth"]=>
                    string(7) "integer"
                  }
                }
     */
    public static function generate_webservice_description($file, $class){
        require_once($file);
        require_once "Zend/Loader.php";
        Zend_Loader::registerAutoload();
        $reflection = Zend_Server_Reflection::reflectClass($class);
        $description = array();

        foreach($reflection->getMethods() as $method){

            $docBlock = $method->getDocComment();

            //retrieve the return and add it into the description if not array|object
            preg_match_all('/@return\s+(\w+)\s+((?:\$)?\w+)/', $docBlock, $returnmatches);

            //retrieve the subparam and subreturn
            preg_match_all('/\s*\*\s*@(subparam|subreturn)\s+(\w+)\s+(\$\w+(?::\w+|->\w+)+)((?:\s+(?:optional|required|multiple))*)/', $docBlock, $matches);

            /// process every @subparam and @subreturn line of the docblock
            for($i=0;$i<sizeof($matches[1]);$i++){

                /// identify the description type of the docblock line: is it params, optional or return (first key of a description method array)
                switch ($matches[1][$i]) {
                    case "subparam":
                        if (strpos($matches[4][$i], "optional")!==false) {
                            $descriptiontype = "optional";
                        } else {
                            $descriptiontype = "params" ;
                        }
                        break;
                    case "subreturn":
                        $descriptiontype = "return";
                        break;
                }

                /// init description[method]
                if (empty($description[$method->getName()])) {
                    $description[$method->getName()] = array();
                }

                /// directly set description[method][return] if the return value is a primary type
                if (strpos($returnmatches[1][0] ,"object")===false && strpos($returnmatches[1][0],"array")===false) {
                    $description[$method->getName()]['return'] = array($returnmatches[2][0] => $returnmatches[1][0]);
                }


                ///algorythm parts
                ///1. We compare the string to the description array
                ///   When we find a attribut that is not in the description, we retrieve all the rest of the string
                ///2. We create the missing part of the description array, starting from the end of the rest of the string
                ///3. We add the missing part to the description array

                ///Part 1.


                /// extract the first part into $param (has to be $params in the case of @subparam, or anything in the case of $subreturn)
                /// extract the second part
                if (strpos($matches[3][$i], "->")===false || (strpos($matches[3][$i], ":")!==false && (strpos($matches[3][$i], ":") < strpos($matches[3][$i], "->")))) {
                    $separator = ":";
                    $separatorsize=1;

                } else {
                    $separator = "->";
                    $separatorsize=2;
                }

                $param = substr($matches[3][$i],1,strpos($matches[3][$i], $separator)-1); //first element/part/array
                //for example for the line @subparam string $params:students->student->name
                //    @params is the first element/part/array of this docnlock line
                //    students is the second element/part/array
                //    ...
                //    name is the terminal element, this element will be generated as String here


                $otherparam = substr($matches[3][$i],strpos($matches[3][$i], $separator)+$separatorsize); //rest of the line
                $parsingdesc = $description[$method->getName()]; //$pasingdesc is the current position of the algorythm into the description array
                //it is used to check if a element already exist into the description array

                if (!empty($parsingdesc) && array_key_exists($descriptiontype, $parsingdesc)){
                    $parsingdesc = $parsingdesc[$descriptiontype];
                }
                $descriptionpath=array(); //we save in this variable the description path (e.g all keys to go deep into the description array)
                //it will be used to know where to add a new part the description array

                $creationfinished = false; //it's used to stop the algorythm when we find a new element that we can add to the descripitoin
                unset($type);

                /// try to extract the other elements and add them to the descripition id there are not already in the description
                while(!$creationfinished && (strpos($otherparam, ":") || strpos($otherparam, "->"))) {
                    if (strpos($otherparam, "->")===false || (strpos($otherparam, ":")!==false && (strpos($otherparam, ":") < strpos($otherparam, "->")))) {
                        $type = $separator;

                        $separator = ":";
                        $separatorsize=1;
                    } else {
                        $type = $separator;
                        $separator = "->";
                        $separatorsize=2;
                    }

                    $param = substr($otherparam,0,strpos($otherparam, $separator));

                    $otherparam = substr($otherparam,strpos($otherparam, $separator)+$separatorsize);


                    if ($type==":") {
                        /// this element is not already in the description array yet and it is a non associative array
                        /// we add it (and its sub structure) to the description array
                        if (!array_key_exists('multiple:'.$param, $parsingdesc)){

                            $desctoadd = webservice_lib::create_end_of_descriptionline(":".$param.$separator.$otherparam, $matches[2][$i]);

                            if(empty($descriptionpath) ) {
                                if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                    $desctoadd = array($descriptiontype => $desctoadd);
                                }
                                $paramtoadd = $descriptiontype;
                            } else {
                                $paramtoadd = 'multiple:'.$param;
                            }
                            webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);
                            $creationfinished = true; // we do not want to keep going to parse this line,
                            // neither add again the terminal element of the line to the descripiton
                        } else {
                            if(empty($descriptionpath)) {
                                $descriptionpath[] = $descriptiontype;
                            }
                            $descriptionpath[] = 'multiple:'.$param;
                            $parsingdesc = $parsingdesc['multiple:'.$param];
                        }
                    } else {
                        /// this element is not in the description array yet and it is a associative array
                        /// we add it (and its sub structure) to the description array
                        if (!array_key_exists($param, $parsingdesc)){

                            $desctoadd = webservice_lib::create_end_of_descriptionline("->".$param.$separator.$otherparam, $matches[2][$i]);

                            if(empty($descriptionpath)) {

                                if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                    $desctoadd = array($descriptiontype => $desctoadd);
                                }
                                $paramtoadd = $descriptiontype;

                            } else {
                                $paramtoadd = $param;
                            }
                            webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);

                            $creationfinished = true; // we do not want to keep going to parse this line,
                            // neither add again the terminal element of the line to the descripiton
                        } else {
                            if(empty($descriptionpath)) {
                                $descriptionpath[] = $descriptiontype;
                            }
                            $descriptionpath[] = $param;
                            $parsingdesc = $parsingdesc[$param];
                        }
                    }

                }
                /// Add the "terminal" element of the line to the description array
                if (!$creationfinished) {

                    if (!empty($type) && $type==":") {

                        $desctoadd = webservice_lib::create_end_of_descriptionline($separator.$otherparam, $matches[2][$i]);

                        if(empty($descriptionpath)) {
                            if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                $desctoadd = array($descriptiontype => $desctoadd);
                            }
                            $paramtoadd = $descriptiontype;
                        } else {
                            $paramtoadd = 'multiple:'.$param;
                        }

                        webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);

                    } else {
                        $desctoadd = webservice_lib::create_end_of_descriptionline($separator.$otherparam, $matches[2][$i]);
                        if(empty($descriptionpath)) {

                            if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                $desctoadd = array($descriptiontype => $desctoadd);
                            }
                            $paramtoadd = $descriptiontype;

                        } else {
                            $paramtoadd = $param;
                        }
                        webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);
                    }
                }

            }
        }
        //                echo "<pre>";
        //                var_dump($description);
        //                echo "</pre>";
        return $description;

    }

    /**
     * Add a description part to the descripition array
     * @param <type> $param
     * @param <type> $desctoadd
     * @param <type> $descriptionlevel
     * @param <type> $descriptionpath
     * @param <type> $level
     */
    public static function add_end_of_description($param, $desctoadd, &$descriptionlevel, $descriptionpath, $level= 0){
        if (sizeof($descriptionpath)==0 || sizeof($descriptionpath)==$level+1) {

            if (is_array($descriptionlevel) && !empty($descriptionlevel)) {
                foreach($desctoadd as $key=>$value) {
                    if ($key!="params" && $key!="optional" && $key!="return") { //TODO
                        $descriptionlevel[$param][$key] = $value;
                    } else {
                        $descriptionlevel[$param] = $value;
                    }
                }
            } else {
                $descriptionlevel = $desctoadd;
            }
        } else {
            webservice_lib::add_end_of_description($param, $desctoadd, $descriptionlevel[$descriptionpath[$level]], $descriptionpath, $level+1);
        }

    }


    /**
     * We create a description part for the description array
     * Structure explained in the "generate_webservice_description" dockblock
     * @param <type> $stringtoadd
     * @param <type> $type
     * @return <type>
     */
    public static function create_end_of_descriptionline($stringtoadd, $type) {

        if (strrpos($stringtoadd, "->")===false || (strrpos($stringtoadd, ":")!==false && (strrpos($stringtoadd, ":") > strrpos($stringtoadd, "->")))) {
            $separator = ":";
            $separatorsize=1;
        } else {
            $separator = "->";
            $separatorsize=2;
        }

        $param = substr($stringtoadd,strrpos($stringtoadd, $separator)+$separatorsize);
        $result = array( $param => $type);

        $otherparam = substr($stringtoadd,0,strlen($stringtoadd)-strlen($param)-$separatorsize);

        while(strrpos($otherparam, ":")!==false || strrpos($otherparam, "->")!==false) {
            if (strrpos($otherparam, "->")===false || (strrpos($otherparam, ":")!==false && (strrpos($otherparam, ":") > strrpos($otherparam, "->")))) {
                $separator = ":";
                $separatorsize=1;
            } else {
                $separator = "->";
                $separatorsize=2;
            }
            $param = substr($otherparam,strrpos($otherparam, $separator)+$separatorsize);
            $otherparam = substr($otherparam,0,strrpos($otherparam, $separator));

            if ($separator==":") {
                $result = array('multiple:'.$param  => $result);
            } else {
                $result = array($param => $result);
            }

        }

        return $result;

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
     * @param array|struct $params
     * @return integer
     */
    function get_token($params) {     
        if ($params['username'] == 'wsuser' && $params['password'] == 'wspassword') {
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
