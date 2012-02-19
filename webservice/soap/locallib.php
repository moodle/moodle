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
 * SOAP web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");
require_once 'Zend/Soap/Server.php';

/**
 * The Zend XMLRPC server but with a fault that returns debuginfo
 */
class moodle_zend_soap_server extends Zend_Soap_Server {

    /**
     * Generate a server fault
     *
     * Note that the arguments are reverse to those of SoapFault.
     *
     * Moodle note: the difference with the Zend server is that we throw a SoapFault exception
     * with the debuginfo integrated to the exception message when DEBUG >= NORMAL
     *
     * If an exception is passed as the first argument, its message and code
     * will be used to create the fault object if it has been registered via
     * {@Link registerFaultException()}.
     *
     * @link   http://www.w3.org/TR/soap12-part1/#faultcodes
     * @param  string|Exception $fault
     * @param  string $code SOAP Fault Codes
     * @return SoapFault
     */
    public function fault($fault = null, $code = "Receiver")
    {
        //intercept any exceptions with debug info and transform it in Moodle exception
        if ($fault instanceof Exception) {
            //add the debuginfo to the exception message if debuginfo must be returned
            if (debugging() and isset($fault->debuginfo)) {
                $fault = new SoapFault('Receiver', $fault->getMessage() . ' | DEBUG INFO: ' . $fault->debuginfo);
            }
        }

        return parent::fault($fault, $code);
    }
}

/**
 * SOAP service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_soap_server extends webservice_zend_server {
    /**
     * Contructor
     * @param bool $simple use simple authentication
     */
    public function __construct($authmethod) {
         // must not cache wsdl - the list of functions is created on the fly
        ini_set('soap.wsdl_cache_enabled', '0');
        require_once 'Zend/Soap/Server.php';
        require_once 'Zend/Soap/AutoDiscover.php';

        if (optional_param('wsdl', 0, PARAM_BOOL)) {
            parent::__construct($authmethod, 'Zend_Soap_AutoDiscover');
        } else {
            parent::__construct($authmethod, 'moodle_zend_soap_server');
        }
        $this->wsname = 'soap';
    }

    /**
     * Set up zend service class
     * @return void
     */
    protected function init_zend_server() {
        global $CFG;

        parent::init_zend_server();

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $username = optional_param('wsusername', '', PARAM_RAW);
            $password = optional_param('wspassword', '', PARAM_RAW);
            // aparently some clients and zend soap server does not work well with "&" in urls :-(
            //TODO: the zend error has been fixed in the last Zend SOAP version, check that is fixed and remove obsolete code
            $url = $CFG->wwwroot.'/webservice/soap/simpleserver.php/'.urlencode($username).'/'.urlencode($password);
            // the Zend server is using this uri directly in xml - weird :-(
            $this->zend_server->setUri(htmlentities($url));
        } else {
            $wstoken = optional_param('wstoken', '', PARAM_RAW);
            $url = $CFG->wwwroot.'/webservice/soap/server.php?wstoken='.urlencode($wstoken);
            // the Zend server is using this uri directly in xml - weird :-(
            $this->zend_server->setUri(htmlentities($url));
        }

        if (!optional_param('wsdl', 0, PARAM_BOOL)) {
            $this->zend_server->setReturnResponse(true);
            //TODO: the error handling in Zend Soap server is useless, XML-RPC is much, much better :-(
            $this->zend_server->registerFaultException('moodle_exception');
            $this->zend_server->registerFaultException('webservice_parameter_exception'); //deprecated since Moodle 2.2 - kept for backward compatibility
            $this->zend_server->registerFaultException('invalid_parameter_exception');
            $this->zend_server->registerFaultException('invalid_response_exception');
            //when DEBUG >= NORMAL then the thrown exceptions are "casted" into a PHP SoapFault expception
            //in order to diplay the $debuginfo (see moodle_zend_soap_server class - MDL-29435)
            if (debugging()) {
                $this->zend_server->registerFaultException('SoapFault');
            }
        }
    }

    /**
     * This method parses the $_POST and $_GET superglobals and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *
     * @return void
     */
    protected function parse_request() {
        parent::parse_request();

        if (!$this->username or !$this->password) {
            //note: this is the workaround for the trouble with & in soap urls
            $authdata = get_file_argument();
            $authdata = explode('/', trim($authdata, '/'));
            if (count($authdata) == 2) {
                list($this->username, $this->password) = $authdata;
            }
        }
    }

    /**
     * Send the error information to the WS client
     * formatted as XML document.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        // Zend Soap server fault handling is incomplete compared to XML-RPC :-(
        // we can not use: echo $this->zend_server->fault($ex);
        //TODO: send some better response in XML
        if ($ex) {
            $info = $ex->getMessage();
            if (debugging() and isset($ex->debuginfo)) {
                $info .= ' - '.$ex->debuginfo;
            }
        } else {
            $info = 'Unknown error';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
<SOAP-ENV:Body><SOAP-ENV:Fault>
<faultcode>MOODLE:error</faultcode>
<faultstring>'.$info.'</faultstring>
</SOAP-ENV:Fault></SOAP-ENV:Body></SOAP-ENV:Envelope>';

        $this->send_headers();
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: inline; filename="response.xml"');

        echo $xml;
    }

    protected function generate_simple_struct_class(external_single_structure $structdesc) {
        global $USER;
        // let's use unique class name, there might be problem in unit tests
        $classname = 'webservices_struct_class_000000';
        while(class_exists($classname)) {
            $classname++;
        }

        $fields = array();
        foreach ($structdesc->keys as $name => $fieldsdesc) {
            $type = $this->get_phpdoc_type($fieldsdesc);
            $fields[] = '    /** @var '.$type." */\n" .
                        '    public $'.$name.';';
        }

        $code = '
/**
 * Virtual struct class for web services for user id '.$USER->id.' in context '.$this->restricted_context->id.'.
 */
class '.$classname.' {
'.implode("\n", $fields).'
}
';
        eval($code);
        return $classname;
    }
}

/**
 * SOAP test client class
 */
class webservice_soap_test_client implements webservice_test_client_interface {
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
        require_once 'Zend/Soap/Client.php';
        $client = new Zend_Soap_Client($serverurl.'&wsdl=1');
        return $client->__call($function, $params);
    }
}
