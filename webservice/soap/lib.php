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

require_once($CFG->dirroot.'/webservice/lib.php');

final class soap_server extends webservice_server {

    public function __construct() {
        $this->set_protocolname("Soap");
        $this->set_protocolid("soap");
    }

  
    /**
     * Run Zend SOAP server
     * @global <type> $CFG
     * @global <type> $USER
     */
    public function run() {
        $enable = $this->get_enable();
        if (empty($enable)) {
            die;
        }
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
                /*
                $autodiscover->setComplexTypeStrategy('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
                $autodiscover->setOperationBodyStyle(
                    array('use' => 'literal',
                          'namespace' => $CFG->wwwroot)
                );
               
                $autodiscover->setBindingStyle(
                    array('style' => 'rpc')
                );
*/
                $autodiscover->setClass('ws_authentication');
                $autodiscover->handle();
            } else {

                $soap = new Zend_Soap_Server($CFG->wwwroot."/webservice/soap/server.php?wsdl"); // this current file here
                
                $soap->registerFaultException('moodle_exception');
                            
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
                $autodiscover->setUri($CFG->wwwroot."/webservice/soap/server.php/".$token."/".$classpath);
                $autodiscover->setClass($classpath."_external");
                $autodiscover->handle();
            } else {
                $soap = new Zend_Soap_Server($CFG->wwwroot."/webservice/soap/server.php?token=".$token."&classpath=".$classpath."&wsdl"); // this current file here
                $soap->setClass($classpath."_external");
                $soap->registerFaultException('moodle_exception');
                $soap->handle();
            }
        }
    }

}


?>
