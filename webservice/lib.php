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
                    require_once($path."/lib.php");
                    $classname = $file."_server";
                    $protocols[] = new $classname;
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
        if ($token == 465465465468468464) {
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
     * Generate description array from the phpdoc
     * TODO: works but it need serious refactoring
     * @param <type> $file
     * @param <type> $class
     * @return <type>
     */
    public static function generate_webservice_description($file, $class){
        require_once($file);
        require_once "Zend/Loader.php";
        Zend_Loader::registerAutoload();
        $reflection = Zend_Server_Reflection::reflectClass($class);
        $description = array();

        foreach($reflection->getMethods() as $method){

            if ($method->getName()!="get_function_webservice_description"
                && $method->getName()!="get_descriptions" ) {
                $docBlock = $method->getDocComment();


                //retrieve the return and add it into the description if not array|object
                preg_match_all('/@return\s+(\w+)\s+((?:\$)?\w+)/', $docBlock, $returnmatches);

                //retrieve the subparam and subreturn
                preg_match_all('/\s*\*\s*@(subparam|subreturn)\s+(\w+)\s+(\$\w+(?::\w+|->\w+)+)((?:\s+(?:optional|required|multiple))*)/', $docBlock, $matches);

                for($i=0;$i<sizeof($matches[1]);$i++){
                    //   var_dump(strpos("optional", $matches[4][$i]));
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

                    if (empty($description[$method->getName()])) {
                        $description[$method->getName()] = array();
                    }

                    if (strpos($returnmatches[1][0] ,"object")===false && strpos($returnmatches[1][0],"array")===false) {
                        $description[$method->getName()]['return'] = array($returnmatches[2][0] => $returnmatches[1][0]);
                    }

                    if ($matches[1][$i] == "subparam" || $matches[1][$i] == "subreturn") {


                        ///algorythm parts
                        ///1. We compare the string to the description array
                        ///   When we find a attribut that is not in the description, we retrieve all the rest of the string
                        ///2. We create the missing part of the description array, starting from the end of the rest of the string
                        ///3. We add the missing part to the description array

                        ///Part 1.


                        ///construct the description array
                        if (strpos($matches[3][$i], "->")===false && strpos($matches[3][$i], ":")===false) {
                            //no separator
                            $otherparam = $matches[3][$i];
                        }
                        else if (strpos($matches[3][$i], "->")===false || (strpos($matches[3][$i], ":")!==false && (strpos($matches[3][$i], ":") < strpos($matches[3][$i], "->")))) {
                            $separator = ":";
                            $separatorsize=1;

                        } else {
                            $separator = "->";
                            $separatorsize=2;
                        }

                        $param = substr($matches[3][$i],1,strpos($matches[3][$i], $separator)-1);

                        $otherparam = substr($matches[3][$i],strpos($matches[3][$i], $separator)+$separatorsize);
                        $parsingdesc = $description[$method->getName()];
                        
                        if (!empty($parsingdesc) && array_key_exists($descriptiontype, $parsingdesc)){
                            $parsingdesc = $parsingdesc[$descriptiontype];
                        }
                        $descriptionpath=array();

                        $creationfinished = false;
                        unset($type);

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
                                if (!array_key_exists('multiple:'.$param, $parsingdesc)){

                                    $desctoadd = webservice_lib::create_end_of_description(":".$param.$separator.$otherparam, $matches[2][$i]);

                                    if(empty($descriptionpath) ) {
                                        if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                            $desctoadd = array($descriptiontype => $desctoadd);
                                        }
                                        $paramtoadd = $descriptiontype;
                                    } else {
                                        $paramtoadd = 'multiple:'.$param;
                                    }
                                    webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);
                                    $creationfinished = true;
                                } else {
                                    if(empty($descriptionpath)) {
                                        $descriptionpath[] = $descriptiontype;
                                    }
                                    $descriptionpath[] = 'multiple:'.$param;
                                    $parsingdesc = $parsingdesc['multiple:'.$param];
                                }
                            } else {
                                if (!array_key_exists($param, $parsingdesc)){

                                    $desctoadd = webservice_lib::create_end_of_description("->".$param.$separator.$otherparam, $matches[2][$i]);

                                    if(empty($descriptionpath)) {

                                        if (empty($description[$method->getName()]) || !array_key_exists($descriptiontype, $description[$method->getName()])) {
                                            $desctoadd = array($descriptiontype => $desctoadd);
                                        }
                                        $paramtoadd = $descriptiontype;

                                    } else {
                                        $paramtoadd = $param;
                                    }
                                    webservice_lib::add_end_of_description($paramtoadd, $desctoadd, $description[$method->getName()], $descriptionpath);

                                    $creationfinished = true;
                                } else {
                                    if(empty($descriptionpath)) {
                                        $descriptionpath[] = $descriptiontype;
                                    }
                                    $descriptionpath[] = $param;
                                    $parsingdesc = $parsingdesc[$param];
                                }
                            }

                        }

                        if (!$creationfinished) {

                            if (!empty($type) && $type==":") {

                                $desctoadd = webservice_lib::create_end_of_description($separator.$otherparam, $matches[2][$i]);

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
                                $desctoadd = webservice_lib::create_end_of_description($separator.$otherparam, $matches[2][$i]);
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
            }
        }

        return $description;
    }

    /**
     * TODO: works but it needs refactoring
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
            webservice_lib::add_end_of_description($param, $desctoadd, &$descriptionlevel[$descriptionpath[$level]], $descriptionpath, $level+1);
        }

    }


    /**
     * TODO: works but it needs refactoring
     * @param <type> $stringtoadd
     * @param <type> $type
     * @return <type>
     */
    public static function create_end_of_description($stringtoadd, $type) {

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

    public function __construct() {
    }

    abstract public function run();

    public function get_protocolname() {
        return $this->protocolname;
    }

    public function set_protocolname($protocolname) {
        $this->protocolname = $protocolname;
    }

    public function get_enable() {
        return get_config($this->get_protocolname(), "enable");
    }

    public function set_enable($enable) {
        set_config("enable", $enable, $this->get_protocolname());
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
    function tmp_get_token($params) {
        if ($params['username'] == 'wsuser' && $params['password'] == 'wspassword') {
            return '465465465468468464';
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

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'username', $this->username);
        $param = new stdClass();
        $param->username = $this->username;
        $wsuser = $DB->get_record("user", array("username" => $this->username));

        $mform->addElement('text', 'ipwhitelist', get_string('ipwhitelist', 'admin'), array('value'=>get_user_preferences("ipwhitelist", "", $wsuser->id),'size' => '40'));
        $mform->addElement('static', null, '',  get_string('ipwhitelistdesc','admin', $param));

        $this->add_action_buttons(true, get_string('savechanges','admin'));
    }
}

?>
