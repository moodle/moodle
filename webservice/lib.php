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
                     $protocols[] = $file;
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
     * if set to false the server cannot be run
     * @var String
     */
    private $enable;

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
        return $this->enable;
    }

    public function set_enable($enable) {
        $this->enable = $enable;
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

?>
