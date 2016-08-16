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

/**
 * Moodle XML-RPC client
 *
 * It has been implemented for unit testing purpose (all protocols have similar client)
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
        if ($this->token) {
            $this->serverurl->param('wstoken', $this->token);
        }

        // Set output options.
        $outputoptions = array(
            'encoding' => 'utf-8'
        );

        // Encode the request.
        // See MDL-53962 - needed for backwards compatibility on <= 3.0
        $params = array_values($params);
        $request = xmlrpc_encode_request($functionname, $params, $outputoptions);

        // Set the headers.
        $headers = array(
            'Content-Length' => strlen($request),
            'Content-Type' => 'text/xml; charset=utf-8',
            'Host' => $this->serverurl->get_host(),
            'User-Agent' => 'Moodle XML-RPC Client/1.0',
        );

        // Get the response.
        $response = download_file_content($this->serverurl, $headers, $request);

        // Decode the response.
        $result = xmlrpc_decode($response);
        if (is_array($result) && xmlrpc_is_fault($result)) {
            throw new Exception($result['faultString'], $result['faultCode']);
        }

        return $result;
    }
}
