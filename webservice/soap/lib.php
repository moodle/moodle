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
 * SOAP server class
 */

require_once('../lib.php');

final class soap_server extends webservice_server {

    public function __construct() {

        $this->set_protocolname("Soap");
    }

    /**
     * Run SOAP server
     * @global <type> $CFG
     * @global <type> $USER
     */
    public function run() {
        global $CFG;
        // retrieve the token from the url
        // if the token doesn't exist, set a class containing only get_token()
        $token = optional_param('token',null,PARAM_ALPHANUM);
        if (empty($token)) {
            $server = new SoapServer($CFG->wwwroot."/webservice/soap/generatewsdl.php");
            $server->setClass("ws_authentication");
            $server->handle();
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
            $classpath = optional_param(classpath,null,PARAM_ALPHA);
            require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

            /// run the server
            $server = new SoapServer($CFG->wwwroot."/webservice/soap/generatewsdl.php?token=".$token);
            $server->setClass($classpath."_external"); //TODO: pass $user as parameter
            $server->handle();
        }
    }

    /**
     * Run Zend SOAP server
     * @global <type> $CFG
     * @global <type> $USER
     */
    public function zend_run() {
        global $CFG;
        include "Zend/Loader.php";
        Zend_Loader::registerAutoload();

        // retrieve the token from the url
        // if the token doesn't exist, set a class containing only get_token()
        $token = optional_param('token',null,PARAM_ALPHANUM);


        ///this is a hack, because there is a bug in Zend framework (http://framework.zend.com/issues/browse/ZF-5736)
        if (empty($token)) {
            $relativepath = get_file_argument();
            $args = explode('/', trim($relativepath, '/'));
            if (count($args) == 2) {
                $token   = (integer)$args[0];
                $classpath    = $args[1];
            }
        }

        if (empty($token)) {
         
            if(isset($_GET['wsdl'])) {
                $autodiscover = new Zend_Soap_AutoDiscover();
                $autodiscover->setClass('ws_authentication');
                $autodiscover->handle();
            } else {

                $soap = new Zend_Soap_Server($CFG->wwwroot."/webservice/soap/zend_soap_server.php?wsdl"); // this current file here
                $soap->setClass('ws_authentication');
                $soap->handle();
            }
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
            if (empty($classpath)) {
                $classpath = optional_param('classpath',null,PARAM_ALPHANUM);
            }
            require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

            /// run the server      
            if(isset($_GET['wsdl'])) {
                $autodiscover = new Zend_Soap_AutoDiscover();

                //this is a hack, because there is a bug in Zend framework (http://framework.zend.com/issues/browse/ZF-5736)
                $autodiscover->setUri($CFG->wwwroot."/webservice/soap/zend_soap_server.php/".$token."/".$classpath);
                $autodiscover->setClass($classpath."_external");
                $autodiscover->handle();
            } else {
                $soap = new Zend_Soap_Server($CFG->wwwroot."/webservice/soap/zend_soap_server.php?token=".$token."&classpath=".$classpath."&wsdl"); // this current file here
                $soap->setClass($classpath."_external");
                $soap->handle();
            }
        }
    }

}


?>
