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
 * XML-RPC web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");
require_once 'Zend/XmlRpc/Server.php';

/**
 * The Zend XMLRPC server but with a fault that return debuginfo
 */
class moodle_zend_xmlrpc_server extends Zend_XmlRpc_Server {

    /**
     * Raise an xmlrpc server fault
     *
     * Moodle note: the difference with the Zend server is that we throw a plain PHP Exception
     * with the debuginfo integrated to the exception message when DEBUG >= NORMAL
     *
     * @param string|Exception $fault
     * @param int $code
     * @return Zend_XmlRpc_Server_Fault
     */
    public function fault($fault = null, $code = 404)
    {
        //intercept any exceptions with debug info and transform it in Moodle exception
        if ($fault instanceof Exception) {
            //add the debuginfo to the exception message if debuginfo must be returned
            if (debugging() and isset($fault->debuginfo)) {
                $fault = new Exception($fault->getMessage() . ' | DEBUG INFO: ' . $fault->debuginfo, 0);
            }
        }

        return parent::fault($fault, $code);
    }
}

/**
 * XML-RPC service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_xmlrpc_server extends webservice_zend_server {
    /**
     * Contructor
     * @param integer $authmethod authentication method one of WEBSERVICE_AUTHMETHOD_*
     */
    public function __construct($authmethod) {
        require_once 'Zend/XmlRpc/Server.php';
        parent::__construct($authmethod, 'moodle_zend_xmlrpc_server');
        $this->wsname = 'xmlrpc';
    }

    /**
     * Set up zend service class
     * @return void
     */
    protected function init_zend_server() {
        parent::init_zend_server();
        // this exception indicates request failed
        Zend_XmlRpc_Server_Fault::attachFaultException('moodle_exception');
        //when DEBUG >= NORMAL then the thrown exceptions are "casted" into a plain PHP Exception class
        //in order to display the $debuginfo (see moodle_zend_xmlrpc_server class - MDL-29435)
        if (debugging()) {
            Zend_XmlRpc_Server_Fault::attachFaultException('Exception');
        }
    }

}

/**
 * XML-RPC test client class
 */
class webservice_xmlrpc_test_client implements webservice_test_client_interface {
    /**
     * Execute test client WS request
     * @param string $serverurl
     * @param string $function
     * @param array $params
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params) {
        //zend expects 0 based array with numeric indexes
        $params = array_values($params);

        require_once 'Zend/XmlRpc/Client.php';
        $client = new Zend_XmlRpc_Client($serverurl);
        return $client->call($function, $params);
    }
}
