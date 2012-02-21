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
 * Moodle SOAP library
 *
 * @package    webservice_soap
 * @copyright  2009 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once 'Zend/Soap/Client.php';

/**
 * Moodle SOAP client
 *
 * It has been implemented for unit testing purpose (all protocols have similar client)
 *
 * @package    webservice_soap
 * @copyright  2010 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_soap_client extends Zend_Soap_Client {

    /** @var string server url e.g. https://yyyyy.com/server.php */
    private $serverurl;

    /**
     * Constructor
     *
     * @param string $serverurl a Moodle URL
     * @param string $token the token used to do the web service call
     * @param array $options PHP SOAP client options - see php.net
     */
    public function __construct($serverurl, $token, $options = null) {
        $this->serverurl = $serverurl;
        $wsdl = $serverurl . "?wstoken=" . $token . '&wsdl=1';
        parent::__construct($wsdl, $options);
    }

    /**
     * Set the token used to do the SOAP call
     *
     * @param string $token the token used to do the web service call
     */
    public function set_token($token) {
        $wsdl = $this->serverurl . "?wstoken=" . $token . '&wsdl=1';
        $this->setWsdl($wsdl);
    }

    /**
     * Execute client WS request with token authentication
     *
     * @param string $functionname the function name
     * @param array $params the parameters of the function
     * @return mixed
     */
    public function call($functionname, $params) {
        global $DB, $CFG;

        //zend expects 0 based array with numeric indexes
        $params = array_values($params);

        //traditional Zend soap client call (integrating the token into the URL)
        $result = $this->__call($functionname, $params);

        return $result;
    }

}