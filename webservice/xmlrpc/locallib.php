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
 * @package    webservice_xmlrpc
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");

/**
 * XML-RPC service server implementation.
 *
 * @package    webservice_xmlrpc
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class webservice_xmlrpc_server extends webservice_base_server {

    /** @var string $response The XML-RPC response string. */
    private $response;

    /**
     * Contructor
     *
     * @param string $authmethod authentication method of the web service (WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN, ...)
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod);
        $this->wsname = 'xmlrpc';
    }

    /**
     * This method parses the request input, it needs to get:
     *  1/ user authentication - username+password or token
     *  2/ function name
     *  3/ function parameters
     */
    protected function parse_request() {
        // Retrieve and clean the POST/GET parameters from the parameters specific to the server.
        parent::set_web_service_call_settings();

        // Get GET and POST parameters.
        $methodvariables = array_merge($_GET, $_POST);

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $this->username = isset($methodvariables['wsusername']) ? $methodvariables['wsusername'] : null;
            unset($methodvariables['wsusername']);

            $this->password = isset($methodvariables['wspassword']) ? $methodvariables['wspassword'] : null;
            unset($methodvariables['wspassword']);
        } else {
            $this->token = isset($methodvariables['wstoken']) ? $methodvariables['wstoken'] : null;
            unset($methodvariables['wstoken']);
        }

        // Get the XML-RPC request data.
        $rawpostdata = file_get_contents("php://input");
        $methodname = null;

        // Decode the request to get the decoded parameters and the name of the method to be called.
        $decodedparams = xmlrpc_decode_request($rawpostdata, $methodname);
        $methodinfo = external_api::external_function_info($methodname);
        $methodparams = array_keys($methodinfo->parameters_desc->keys);

        // Add the decoded parameters to the methodvariables array.
        if (is_array($decodedparams)) {
            foreach ($decodedparams as $index => $param) {
                // See MDL-53962 - XML-RPC requests will usually be sent as an array (as in, one with indicies).
                // We need to use a bit of "magic" to add the correct index back. Zend used to do this for us.
                $methodvariables[$methodparams[$index]] = $param;
            }
        }

        $this->functionname = $methodname;
        $this->parameters = $methodvariables;
    }

    /**
     * Prepares the response.
     */
    protected function prepare_response() {
        try {
            if (!empty($this->function->returns_desc)) {
                $validatedvalues = external_api::clean_returnvalue($this->function->returns_desc, $this->returns);
                $encodingoptions = array(
                    "encoding" => "utf-8",
                    "verbosity" => "no_white_space"
                );
                // We can now convert the response to the requested XML-RPC format.
                $this->response = xmlrpc_encode_request(null, $validatedvalues, $encodingoptions);
            }
        } catch (invalid_response_exception $ex) {
            $this->response = $this->generate_error($ex);
        }
    }

    /**
     * Send the result of function call to the WS client.
     */
    protected function send_response() {
        $this->prepare_response();
        $this->send_headers();
        echo $this->response;
    }

    /**
     * Send the error information to the WS client.
     *
     * @param Exception $ex
     */
    protected function send_error($ex = null) {
        $this->send_headers();
        echo $this->generate_error($ex);
    }

    /**
     * Sends the headers for the XML-RPC response.
     */
    protected function send_headers() {
        // Standard headers.
        header('HTTP/1.1 200 OK');
        header('Connection: close');
        header('Content-Length: ' . strlen($this->response));
        header('Content-Type: text/xml; charset=utf-8');
        header('Date: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
        header('Server: Moodle XML-RPC Server/1.0');
        // Other headers.
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        // Allow cross-origin requests only for Web Services.
        // This allow to receive requests done by Web Workers or webapps in different domains.
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * Generate the XML-RPC fault response.
     *
     * @param Exception $ex The exception.
     * @param int $faultcode The faultCode to be included in the fault response
     * @return string The XML-RPC fault response xml containing the faultCode and faultString.
     */
    protected function generate_error(Exception $ex, $faultcode = 404) {
        $error = $ex->getMessage();

        if (!empty($ex->errorcode)) {
            // The faultCode must be an int, so we obtain a hash of the errorcode then get an integer value of the hash.
            $faultcode = base_convert(md5($ex->errorcode), 16, 10);

            // We strip the $code to 8 digits (and hope for no error code collisions).
            // Collisions should be pretty rare, and if needed the client can retrieve
            // the accurate errorcode from the last | in the exception message.
            $faultcode = substr($faultcode, 0, 8);

            // Add the debuginfo to the exception message if debuginfo must be returned.
            if (debugging() and isset($ex->debuginfo)) {
                $error .= ' | DEBUG INFO: ' . $ex->debuginfo . ' | ERRORCODE: ' . $ex->errorcode;
            } else {
                $error .= ' | ERRORCODE: ' . $ex->errorcode;
            }
        }

        $fault = array(
            'faultCode' => (int) $faultcode,
            'faultString' => $error
        );

        $encodingoptions = array(
            "encoding" => "utf-8",
            "verbosity" => "no_white_space"
        );

        return xmlrpc_encode_request(null, $fault, $encodingoptions);
    }
}

/**
 * XML-RPC test client class
 *
 * @package    webservice_xmlrpc
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class webservice_xmlrpc_test_client implements webservice_test_client_interface {
    /**
     * Execute test client WS request
     * @param string $serverurl server url (including token parameter or username/password parameters)
     * @param string $function function name
     * @param array $params parameters of the called function
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params) {
        global $CFG;

        $url = new moodle_url($serverurl);
        $token = $url->get_param('wstoken');
        require_once($CFG->dirroot . '/webservice/xmlrpc/lib.php');
        $client = new webservice_xmlrpc_client($serverurl, $token);
        return $client->call($function, $params);
    }
}
