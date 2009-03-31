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


require_once($CFG->dirroot.'/webservice/lib.php');

/*
 * AMF server class
 */
final class amf_server extends webservice_server {


    public function __construct() {
        //set web service proctol name
        $this->set_protocolname("Amf");
        $this->set_protocolid("amf");
    }

    /**
     * Run the AMF server
     */
    public function run() {
        $enable = $this->get_enable();
        if (empty($enable)) {
            die;
        }
        include "Zend/Loader.php";
        Zend_Loader::registerAutoload();

        //retrieve the api name
        $classpath = optional_param('classpath','user',PARAM_ALPHA);
        require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

        /// run the Zend AMF server
        $server = new Zend_Amf_Server();
        $debugmode = get_config($this->get_protocolid(),'debug');
        if (!empty($debugmode)) {
            $server->setProduction(false);
        } else {
            $server->setProduction(true);
        }
        $server->setClass($classpath."_external");
        $response = $server->handle();
        echo $response;
    }

    /**
     * Names of the server settings
     * @return array
     */
    public static function get_setting_names() {
        return array('debug');
    }

    public function settings_form(&$mform) {
        $debug = get_config($this->get_protocolid(), 'debug');
        $debug = true;
        if (empty($debug)) {
            $debug = false;
        }
        $mform->addElement('checkbox', 'debug', get_string('amfdebug', 'webservice'));
    }
}

?>
