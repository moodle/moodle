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

/**
 * XML-RPC service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_xmlrpc_server extends webservice_zend_server {
    /**
     * Contructor
     * @param bool $simple use simple authentication
     */
    public function __construct($simple) {
        require_once 'Zend/XmlRpc/Server.php';
        parent::__construct($simple, 'Zend_XmlRpc_Server');
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
    }

    /**
     * Clean XML-RPC reponse
     * @param array $response the response to clean
     * @return array $response the cleaned response
     */
    protected function clean_response($response) {
        //check that the response is not an exception/server fault
        if (!($response instanceof Zend_XmlRpc_Server_Fault)) {
            $methodname = $this->zend_server->getRequest()->getMethod(); //retrieve the method name called by the client
            $function = external_function_info($methodname); //retrieve the description of the method name
            if (is_object($function)) { //if the method is not an object (no description found),
                                        // do not make any change on the response
                varlog($response);
                $returnvalue = $response->getReturnValue();
                $returnvalue = $this->clean_returnvalue($function->returns_desc, $returnvalue);
                $response->setReturnValue($returnvalue);
            }
        }
        return $response;
    }


    /**
     * Clean response, if anything is incorrect
     * invalid_parameter_exception is thrown, if an attribut is unknow from the description,
     * just ignore it.
     * Note: this is a recursive method
     * @param external_description $description description of parameters
     * @param mixed $response the actual parameters
     * @return mixed params with added defaults for optional items, invalid_parameters_exception thrown if any problem found
     */
    private function clean_returnvalue(external_description $description, $response) {
        if ($description instanceof external_value) {
            if (is_array($response) or is_object($response)) {
                throw new invalid_parameter_exception(get_string('errorscalartype', 'webservice'));
            }

            if ($description->type == PARAM_BOOL) {
                // special case for PARAM_BOOL - we want true/false instead of the usual 1/0 - we can not be too strict here ;-)
                if (is_bool($response) or $response === 0 or $response === 1 or $response === '0' or $response === '1') {
                    return (bool)$response;
                }
            }
            return validate_param($response, $description->type, $description->allownull, get_string('errorinvalidparamsapi', 'webservice'));

        } else if ($description instanceof external_single_structure) {
            if (!is_array($response)) {
                throw new invalid_parameter_exception(get_string('erroronlyarray', 'webservice'));
            }
            $result = array();
            foreach ($description->keys as $key=>$subdesc) {
                if (!array_key_exists($key, $response)) {
                    if ($subdesc->required == VALUE_REQUIRED) {
                        throw new invalid_parameter_exception(get_string('errormissingkey', 'webservice', $key));
                    }
                    if ($subdesc instanceof external_value) {
                            if ($subdesc->required == VALUE_DEFAULT) {
                                try {
                                    $result[$key] = $this->clean_returnvalue($subdesc, $subdesc->default);
                                } catch (invalid_parameter_exception $e) {
                                    throw new webservice_parameter_exception('invalidextparam',$key);
                                }
                            }
                        }
                } else {
                    try {
                        $result[$key] = $this->clean_returnvalue($subdesc, $response[$key]);
                    } catch (invalid_parameter_exception $e) {
                        //it's ok to display debug info as here the information is useful for ws client/dev
                        throw new webservice_parameter_exception('invalidextparam',$key." (".$e->debuginfo.")");
                    }
                }
                unset($response[$key]);
            }

            return $result;

        } else if ($description instanceof external_multiple_structure) {
            if (!is_array($response)) {
                throw new invalid_parameter_exception(get_string('erroronlyarray', 'webservice'));
            }
            $result = array();
            foreach ($response as $param) {
                $result[] = $this->clean_returnvalue($description->content, $param);
            }
            return $result;

        } else {
            throw new invalid_parameter_exception(get_string('errorinvalidparamsdesc', 'webservice'));
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