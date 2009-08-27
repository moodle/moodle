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

/*
 * XML-RPC server class
 */

require_once($CFG->dirroot.'/webservice/lib.php');

final class xmlrpc_server extends webservice_server {

    public function __construct() {

        $this->set_protocolname("XML-RPC");
        $this->set_protocolid("xmlrpc");
    }
  
    public function run() {
        $enable = $this->get_enable();
        if (empty($enable)) {
            die;
        }
        include "Zend/Loader.php";
        Zend_Loader::registerAutoload();

        Zend_XmlRpc_Server_Fault::attachFaultException('moodle_exception');

        // retrieve the token from the url
        // if the token doesn't exist, set a class containing only get_token()
        $token = optional_param('token',null,PARAM_ALPHANUM);
        if (empty($token)) {
            $server = new Zend_XmlRpc_Server();
            $server->setClass("ws_authentication", "authentication");
            echo $server->handle();
        } else { // if token exist, do the authentication here
            /// TODO: following function will need to be modified
            $user = webservice_lib::mock_check_token($token);
            if (empty($user)) {
                throw new moodle_exception('wrongidentification');
            } else {
                /// TODO: probably change this
                global $USER;
                $USER = $user;
            }

            //retrieve the api name
            $classpath = optional_param('classpath', null, PARAM_SAFEDIR);
            require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

            /// run the server
            $server = new Zend_XmlRpc_Server(); 
            $server->setClass($classpath."_external", $classpath);
            echo $server->handle();
        }
    }

}


?>
