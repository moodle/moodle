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
 * This file contains the class moodle_google_curlio.
 *
 * @package core_google
 * @copyright 2013 Frédéric Massart
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/google/Google/IO/Curl.php');
require_once($CFG->libdir . '/google/Google/IO/Exception.php');

/**
 * Class moodle_google_curlio.
 *
 * The initial purpose of this class is to add support for our
 * class curl in Google_IO_Curl. It mostly entirely overrides it.
 *
 * @package core_google
 * @copyright 2013 Frédéric Massart
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class moodle_google_curlio extends Google_IO_Curl {

    /** @var array associate array of constant value and their name. */
    private static $constants = null;

    /** @var array options. */
    private $options = array();

    /**
     * Send the request via our curl object.
     *
     * @param curl $curl prepared curl object.
     * @param Google_HttpRequest $request The request.
     * @return string result of the request.
     */
    private function do_request($curl, $request) {
        $url = $request->getUrl();
        $method = $request->getRequestMethod();
        switch (strtoupper($method)) {
            case 'POST':
                $ret = $curl->post($url, $request->getPostBody());
                break;
            case 'GET':
                $ret = $curl->get($url);
                break;
            case 'HEAD':
                $ret = $curl->head($url);
                break;
            case 'PUT':
                $ret = $curl->put($url);
                break;
            default:
                throw new coding_exception('Unknown request type: ' . $method);
                break;
        }
        return $ret;
    }

    /**
     * Execute an API request.
     *
     * This is a copy/paste from the parent class that uses Moodle's implementation
     * of curl. Portions have been removed or altered.
     *
     * @param Google_Http_Request $request the http request to be executed
     * @return Google_Http_Request http request with the response http code, response
     * headers and response body filled in
     * @throws Google_IO_Exception on curl or IO error
     */
    public function executeRequest(Google_Http_Request $request) {
        $curl = new curl();

        if ($request->getPostBody()) {
            $curl->setopt(array('CURLOPT_POSTFIELDS' => $request->getPostBody()));
        }

        $requestHeaders = $request->getRequestHeaders();
        if ($requestHeaders && is_array($requestHeaders)) {
            $curlHeaders = array();
            foreach ($requestHeaders as $k => $v) {
                $curlHeaders[] = "$k: $v";
            }
            $curl->setopt(array('CURLOPT_HTTPHEADER' => $curlHeaders));
        }

        $curl->setopt(array('CURLOPT_URL' => $request->getUrl()));

        $curl->setopt(array('CURLOPT_CUSTOMREQUEST' => $request->getRequestMethod()));
        $curl->setopt(array('CURLOPT_USERAGENT' => $request->getUserAgent()));

        $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => false));
        $curl->setopt(array('CURLOPT_SSL_VERIFYPEER' => true));
        $curl->setopt(array('CURLOPT_RETURNTRANSFER' => true));
        $curl->setopt(array('CURLOPT_HEADER' => true));

        if ($request->canGzip()) {
            $curl->setopt(array('CURLOPT_ENCODING' => 'gzip,deflate'));
        }

        $curl->setopt($this->options);
        $respdata = $this->do_request($curl, $request);

        $infos = $curl->get_info();
        $respheadersize = $infos['header_size'];
        $resphttpcode = (int) $infos['http_code'];
        $curlerrornum = $curl->get_errno();
        $curlerror = $curl->error;

        if ($respdata != CURLE_OK) {
            throw new Google_IO_Exception($curlerror);
        }

        list($responseHeaders, $responseBody) = $this->parseHttpResponse($respdata, $respheadersize);
        return array($responseBody, $responseHeaders, $resphttpcode);
    }

    /**
     * Set curl options.
     *
     * We overwrite this method to ensure that the data passed meets
     * the requirement of our curl implementation and so that the keys
     * are strings, and not curl constants.
     *
     * @param array $optparams Multiple options used by a cURL session.
     * @return void
     */
    public function setOptions($optparams) {
        $safeparams = array();
        foreach ($optparams as $name => $value) {
            if (!is_string($name)) {
                $name = $this->get_option_name_from_constant($name);
            }
            $safeparams[$name] = $value;
        }
        $this->options = $options + $this->options;
    }

    /**
     * Set the maximum request time in seconds.
     *
     * Overridden to use the right option key.
     *
     * @param $timeout in seconds
     */
    public function setTimeout($timeout) {
        // Since this timeout is really for putting a bound on the time
        // we'll set them both to the same. If you need to specify a longer
        // CURLOPT_TIMEOUT, or a tigher CONNECTTIMEOUT, the best thing to
        // do is use the setOptions method for the values individually.
        $this->options['CURLOPT_CONNECTTIMEOUT'] = $timeout;
        $this->options['CURLOPT_TIMEOUT'] = $timeout;
    }

    /**
     * Get the maximum request time in seconds.
     *
     * Overridden to use the right option key.
     *
     * @return timeout in seconds.
     */
    public function getTimeout() {
       return $this->options['CURLOPT_TIMEOUT'];
    }

    /**
     * Return the name of an option based on the constant value.
     *
     * @param int $constant value of a CURL constant.
     * @return string name of the constant if found, or throws exception.
     * @throws coding_exception when the constant is not found.
     * @since Moodle 2.5
     */
    public function get_option_name_from_constant($constant) {
        if (is_null(self::$constants)) {
            $constants = get_defined_constants(true);
            $constants = isset($constants['curl']) ? $constants['curl'] : array();
            $constants = array_flip($constants);
            self::$constants = $constants;
        }
        if (isset(self::$constants[$constant])) {
            return self::$constants[$constant];
        }
        throw new coding_exception('Unknown curl constant value: ' . $constant);
    }

}
