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
require_once($CFG->libdir . '/google/io/Google_IO.php');

/**
 * Class moodle_google_curlio.
 *
 * The initial purpose of this class is to add support for our
 * class curl in Google_CurlIO.
 *
 * @package core_google
 * @copyright 2013 Frédéric Massart
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class moodle_google_curlio extends Google_CurlIO {

    /** @var array associate array of constant value and their name. */
    private static $constants = null;

    /**
     * Private variables have to be redefined here.
     */
    private static $ENTITY_HTTP_METHODS = array("POST" => null, "PUT" => null);
    private static $HOP_BY_HOP = array(
        'connection', 'keep-alive', 'proxy-authenticate', 'proxy-authorization',
        'te', 'trailers', 'transfer-encoding', 'upgrade');
    private $curlParams = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_FOLLOWLOCATION' => 0,
        'CURLOPT_FAILONERROR' => false,
        'CURLOPT_SSL_VERIFYPEER' => true,
        'CURLOPT_HEADER' => true,
        'CURLOPT_VERBOSE' => false
    );

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
     * Send an API request to Google.
     *
     * This method overwrite the parent one so that the Google SDK will use our class
     * curl to proceed with the requests. This allows us to have control over the
     * proxy parameters and other stuffs.
     *
     * Note that the caching support of the Google SDK has been removed from this function.
     *
     * @param Google_HttpRequest $request the http request to be executed
     * @return Google_HttpRequest http request with the response http code, response
     * headers and response body filled in
     * @throws Google_IOException on curl or IO error
     */
    public function makeRequest(Google_HttpRequest $request) {

        if (array_key_exists($request->getRequestMethod(), self::$ENTITY_HTTP_METHODS)) {
            $request = $this->processEntityRequest($request);
        }

        $curl = new curl();
        $curl->setopt($this->curlParams);
        $curl->setopt(array('CURLOPT_URL' => $request->getUrl()));

        $requestHeaders = $request->getRequestHeaders();
        if ($requestHeaders && is_array($requestHeaders)) {
            $parsed = array();
            foreach ($requestHeaders as $k => $v) {
                $parsed[] = "$k: $v";
            }
            $curl->setHeader($parsed);
        }

        $curl->setopt(array(
            'CURLOPT_CUSTOMREQUEST' => $request->getRequestMethod(),
            'CURLOPT_USERAGENT' => $request->getUserAgent()
        ));

        $respdata = $this->do_request($curl, $request);

        // Retry if certificates are missing.
        if ($curl->get_errno() == CURLE_SSL_CACERT) {
            error_log('SSL certificate problem, verify that the CA cert is OK.' .
                    ' Retrying with the CA cert bundle from google-api-php-client.');
            $curl->setopt(array('CURLOPT_CAINFO' => dirname(__FILE__) . '/io/cacerts.pem'));
            $respdata = $this->do_request($curl, $request);
        }

        $infos = $curl->get_info();
        $respheadersize = $infos['header_size'];
        $resphttpcode = (int) $infos['http_code'];
        $curlerrornum = $curl->get_errno();
        $curlerror = $curl->error;

        if ($curlerrornum != CURLE_OK) {
          throw new Google_IOException("HTTP Error: ($resphttpcode) $curlerror");
        }

        // Parse out the raw response into usable bits.
        list($responseHeaders, $responseBody) = self::parseHttpResponse($respdata, $respheadersize);

        // Fill in the apiHttpRequest with the response values.
        $request->setResponseHttpCode($resphttpcode);
        $request->setResponseHeaders($responseHeaders);
        $request->setResponseBody($responseBody);

        return $request;
    }

    /**
    * Set curl options.
    *
    * We overwrite this method to ensure that the data passed meets
    * the requirement of our curl implementation and so that the keys
    * are strings, and not curl constants.
    *
    * @param array $optCurlParams Multiple options used by a cURL session.
    * @return void
    */
    public function setOptions($optCurlParams) {
        $safeParams = array();
        foreach ($optCurlParams as $name => $value) {
            if (!is_string($name)) {
                $name = $this->get_option_name_from_constant($name);
            }
            $safeParams[$name] = $vale;
        }
        parent::setOptions($safeParams);
    }

    /**
     * Return the name of an option based on the constant value.
     *
     * @param int $constant value of a CURL constant.
     * @return string name of the constant if found, or throws exception.
     * @throws coding_exception when the constant is not found.
     * @since 2.5
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
