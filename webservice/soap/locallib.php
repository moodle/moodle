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
 * @package    webservice_soap
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once($CFG->dirroot . '/webservice/lib.php');
use webservice_soap\wsdl;

/**
 * SOAP service server implementation.
 *
 * @package    webservice_soap
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class webservice_soap_server extends webservice_base_server {

    /** @var moodle_url The server URL. */
    protected $serverurl;

    /** @var  SoapServer The Soap */
    protected $soapserver;

    /** @var  string The response. */
    protected $response;

    /** @var  string The class name of the virtual class generated for this web service. */
    protected $serviceclass;

    /** @var bool WSDL mode flag. */
    protected $wsdlmode;

    /** @var \webservice_soap\wsdl The object for WSDL generation. */
    protected $wsdl;

    /**
     * Contructor.
     *
     * @param string $authmethod authentication method of the web service (WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN, ...)
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod);
         // Must not cache wsdl - the list of functions is created on the fly.
        ini_set('soap.wsdl_cache_enabled', '0');
        $this->wsname = 'soap';
        $this->wsdlmode = false;
    }

    /**
     * This method parses the $_POST and $_GET superglobals and looks for the following information:
     * - User authentication parameters:
     *   - Username + password (wsusername and wspassword), or
     *   - Token (wstoken)
     */
    protected function parse_request() {
        // Retrieve and clean the POST/GET parameters from the parameters specific to the server.
        parent::set_web_service_call_settings();

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $this->username = optional_param('wsusername', null, PARAM_RAW);
            $this->password = optional_param('wspassword', null, PARAM_RAW);

            if (!$this->username or !$this->password) {
                // Workaround for the trouble with & in soap urls.
                $authdata = get_file_argument();
                $authdata = explode('/', trim($authdata, '/'));
                if (count($authdata) == 2) {
                    list($this->username, $this->password) = $authdata;
                }
            }
            $this->serverurl = new moodle_url('/webservice/soap/simpleserver.php/' . $this->username . '/' . $this->password);
        } else {
            $this->token = optional_param('wstoken', null, PARAM_RAW);

            $this->serverurl = new moodle_url('/webservice/soap/server.php');
            $this->serverurl->param('wstoken', $this->token);
        }

        if ($wsdl = optional_param('wsdl', 0, PARAM_INT)) {
            $this->wsdlmode = true;
        }
    }

    /**
     * Runs the SOAP web service.
     *
     * @throws coding_exception
     * @throws moodle_exception
     * @throws webservice_access_exception
     */
    public function run() {
        // We will probably need a lot of memory in some functions.
        raise_memory_limit(MEMORY_EXTRA);

        // Set some longer timeout since operations may need longer time to finish.
        external_api::set_timeout();

        // Set up exception handler.
        set_exception_handler(array($this, 'exception_handler'));

        // Init all properties from the request data.
        $this->parse_request();

        // Authenticate user, this has to be done after the request parsing. This also sets up $USER and $SESSION.
        $this->authenticate_user();

        // Make a list of all functions user is allowed to execute.
        $this->init_service_class();

        if ($this->wsdlmode) {
            // Generate the WSDL.
            $this->generate_wsdl();
        }

        // Log the web service request.
        $params = array(
            'other' => array(
                'function' => 'unknown'
            )
        );
        $event = \core\event\webservice_function_called::create($params);
        $logdataparams = array(SITEID, 'webservice_soap', '', '', $this->serviceclass . ' ' . getremoteaddr(), 0, $this->userid);
        $event->set_legacy_logdata($logdataparams);
        $event->trigger();

        // Handle the SOAP request.
        $this->handle();

        // Session cleanup.
        $this->session_cleanup();
        die;
    }

    /**
     * Generates the WSDL.
     */
    protected function generate_wsdl() {
        // Initialise WSDL.
        $this->wsdl = new wsdl($this->serviceclass, $this->serverurl);
        // Register service struct classes as complex types.
        foreach ($this->servicestructs as $structinfo) {
            $this->wsdl->add_complex_type($structinfo->classname, $structinfo->properties);
        }
        // Register the method for the WSDL generation.
        foreach ($this->servicemethods as $methodinfo) {
            $this->wsdl->register($methodinfo->name, $methodinfo->inputparams, $methodinfo->outputparams, $methodinfo->description);
        }
    }

    /**
     * Handles the web service function call.
     */
    protected function handle() {
        if ($this->wsdlmode) {
            // Prepare the response.
            $this->response = $this->wsdl->to_xml();

            // Send the results back in correct format.
            $this->send_response();
        } else {
            $wsdlurl = clone($this->serverurl);
            $wsdlurl->param('wsdl', 1);

            $options = array(
                'uri' => $this->serverurl->out(false)
            );
            // Initialise the SOAP server.
            $this->soapserver = new SoapServer($wsdlurl->out(false), $options);
            if (!empty($this->serviceclass)) {
                $this->soapserver->setClass($this->serviceclass);
                // Get all the methods for the generated service class then register to the SOAP server.
                $functions = get_class_methods($this->serviceclass);
                $this->soapserver->addFunction($functions);
            }

            // Get soap request from raw POST data.
            $soaprequest = file_get_contents('php://input');
            // Handle the request.
            try {
                $this->soapserver->handle($soaprequest);
            } catch (Exception $e) {
                $this->fault($e);
            }
        }
    }

    /**
     * Send the error information to the WS client formatted as an XML document.
     *
     * @param Exception $ex the exception to send back
     */
    protected function send_error($ex = null) {
        if ($ex) {
            $info = $ex->getMessage();
            if (debugging() and isset($ex->debuginfo)) {
                $info .= ' - '.$ex->debuginfo;
            }
        } else {
            $info = 'Unknown error';
        }

        // Initialise new DOM document object.
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Fault node.
        $fault = $dom->createElement('SOAP-ENV:Fault');
        // Faultcode node.
        $fault->appendChild($dom->createElement('faultcode', 'MOODLE:error'));
        // Faultstring node.
        $fault->appendChild($dom->createElement('faultstring', $info));

        // Body node.
        $body = $dom->createElement('SOAP-ENV:Body');
        $body->appendChild($fault);

        // Envelope node.
        $envelope = $dom->createElement('SOAP-ENV:Envelope');
        $envelope->setAttribute('xmlns:SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $envelope->appendChild($body);
        $dom->appendChild($envelope);

        // Send headers.
        $this->send_headers();

        // Output the XML.
        echo $dom->saveXML();
    }

    /**
     * Send the result of function call to the WS client.
     */
    protected function send_response() {
        $this->send_headers();
        echo $this->response;
    }

    /**
     * Internal implementation - sending of page headers.
     */
    protected function send_headers() {
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        header('Content-Length: ' . count($this->response));
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: inline; filename="response.xml"');
    }

    /**
     * Generate a server fault.
     *
     * Note that the parameter order is the reverse of SoapFault's constructor parameters.
     *
     * Moodle note: basically we return the faultactor (errorcode) and faultdetails (debuginfo).
     *
     * If an exception is passed as the first argument, its message and code
     * will be used to create the fault object.
     *
     * @link   http://www.w3.org/TR/soap12-part1/#faultcodes
     * @param  string|Exception $fault
     * @param  string $code SOAP Fault Codes
     */
    public function fault($fault = null, $code = 'Receiver') {
        $allowedfaultmodes = array(
            'VersionMismatch', 'MustUnderstand', 'DataEncodingUnknown',
            'Sender', 'Receiver', 'Server'
        );
        if (!in_array($code, $allowedfaultmodes)) {
            $code = 'Receiver';
        }

        // Intercept any exceptions and add the errorcode and debuginfo (optional).
        $actor = null;
        $details = null;
        $errorcode = 'unknownerror';
        $message = get_string($errorcode);
        if ($fault instanceof Exception) {
            // Add the debuginfo to the exception message if debuginfo must be returned.
            $actor = isset($fault->errorcode) ? $fault->errorcode : null;
            $errorcode = $actor;
            if (debugging()) {
                $message = $fault->getMessage();
                $details = isset($fault->debuginfo) ? $fault->debuginfo : null;
            }
        } else if (is_string($fault)) {
            $message = $fault;
        }

        $this->soapserver->fault($code, $message . ' | ERRORCODE: ' . $errorcode, $actor, $details);
    }
}

/**
 * SOAP test client class
 *
 * @package    webservice_soap
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class webservice_soap_test_client implements webservice_test_client_interface {

    /**
     * Execute test client WS request
     *
     * @param string $serverurl server url (including token parameter or username/password parameters)
     * @param string $function function name
     * @param array $params parameters of the called function
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params) {
        global $CFG;

        require_once($CFG->dirroot . '/webservice/soap/lib.php');
        $client = new webservice_soap_client($serverurl);
        return $client->call($function, $params);
    }
}
