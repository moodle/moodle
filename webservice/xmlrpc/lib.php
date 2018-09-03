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
 * Moodle XML-RPC library
 *
 * @package    webservice_xmlrpc
 * @copyright  2009 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle XML-RPC client
 *
 * @package    webservice_xmlrpc
 * @copyright  2010 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_xmlrpc_client {

    /** @var moodle_url The XML-RPC server url. */
    protected $serverurl;

    /** @var string The token for the XML-RPC call. */
    protected $token;

    /**
     * Constructor
     *
     * @param string $serverurl a Moodle URL
     * @param string $token the token used to do the web service call
     */
    public function __construct($serverurl, $token) {
        $this->serverurl = new moodle_url($serverurl);
        $this->token = $token;
    }

    /**
     * Set the token used to do the XML-RPC call
     *
     * @param string $token the token used to do the web service call
     */
    public function set_token($token) {
        $this->token = $token;
    }

    /**
     * Execute client WS request with token authentication
     *
     * @param string $functionname the function name
     * @param array $params An associative array containing the the parameters of the function being called.
     * @return mixed The decoded XML RPC response.
     * @throws moodle_exception
     */
    public function call($functionname, $params = array()) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        if ($this->token) {
            $this->serverurl->param('wstoken', $this->token);
        }

        $request = $this->encode_request($functionname, $params);

        // Set the headers.
        $headers = array(
            'Content-Length' => strlen($request),
            'Content-Type' => 'text/xml; charset=utf-8',
            'Host' => $this->serverurl->get_host(),
            'User-Agent' => 'Moodle XML-RPC Client/1.0',
        );

        // Get the response.
        $response = download_file_content($this->serverurl->out(false), $headers, $request);

        // Decode the response.
        $result = $this->decode_response($response);
        if (is_array($result) && xmlrpc_is_fault($result)) {
            throw new Exception($result['faultString'], $result['faultCode']);
        }

        return $result;
    }

    /**
     * Generates XML for a method request.
     *
     * @param string $functionname Name of the method to call.
     * @param mixed $params Method parameters compatible with the method signature.
     * @return string
     */
    protected function encode_request($functionname, $params) {

        $outputoptions = array(
            'encoding' => 'utf-8',
            'escaping' => 'markup',
        );

        // See MDL-53962 - needed for backwards compatibility on <= 3.0.
        $params = array_values($params);

        return xmlrpc_encode_request($functionname, $params, $outputoptions);
    }

    /**
     * Parses and decodes the response XML
     *
     * @param string $response
     * @return array
     */
    protected function decode_response($response) {
        // XMLRPC server in Moodle encodes response using function xmlrpc_encode_request() with method==null
        // see {@link webservice_xmlrpc_server::prepare_response()} . We should use xmlrpc_decode_request() for decoding too.
        $method = null;
        $encoding = null;
        if (preg_match('/^<\?xml version="1.0" encoding="([^"]*)"\?>/', $response, $matches)) {
            // Sometimes xmlrpc_decode_request() fails to recognise encoding, let's help it.
            $encoding = $matches[1];
        }
        $r = xmlrpc_decode_request($response, $method, $encoding);
        return $r;
    }
}
