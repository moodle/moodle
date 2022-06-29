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

namespace enrol_lti\local\ltiadvantage\lib;

use Packback\Lti1p3\Interfaces\IHttpClient;
use Packback\Lti1p3\Interfaces\IHttpException;
use Packback\Lti1p3\Interfaces\IHttpResponse;

/**
 * An implementation of IHTTPClient delegating to a curl object, for use with the lib/lti1p3 library code.
 *
 * @package    enrol_lti
 * @copyright  2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class http_client implements IHttpClient {

    /** @var \curl a curl client instance. */
    private $curlclient;

    /**
     * Constructor.
     *
     * @param \curl $curlclient a curl client instance.
     */
    public function __construct(\curl $curlclient) {
        $this->curlclient = $curlclient;
    }

    /**
     * Make an HTTP request to the given URL.
     *
     * @param string $method the HTTP method to use.
     * @param string $url the URL to send the request to.
     * @param array $options an array of request options, mainly used to set headers and body.
     * @return IHttpResponse the response
     * @throws \Exception if the curl client encounters any errors making the request.
     * @throws IHttpException if the response contains a 400-level or 500-level status code.
     */
    public function request(string $method, string $url, array $options): IHttpResponse {
        $this->curlclient->resetHeader();
        $this->curlclient->resetopt();
        if (isset($options['headers'])) {
            $headers = $options['headers'];
            array_walk(
                $headers,
                function(&$val, $key) {
                    $val = "$key: $val";
                }
            );
            $this->curlclient->setHeader($headers);
        }
        if ($method == 'POST') {
            $res = $this->curlclient->post($url, $options['body'] ?? null, ['CURLOPT_HEADER' => 1]);
        } else if ($method == 'GET') {
            $res = $this->curlclient->get($url, [], ['CURLOPT_HEADER' => 1]);
        } else {
            throw new \Exception('Sorry, that HTTP method is not supported yet.');
        }

        $info = $this->curlclient->get_info();
        if (!$this->curlclient->get_errno() && !$this->curlclient->error) {
            // No errors, so format the response.
            $headersize = $info['header_size'];
            $resheaders = substr($res, 0, $headersize);
            $resbody = substr($res, $headersize);
            $headerlines = array_filter(explode("\r\n", $resheaders));
            $parsedresponseheaders = [
                'httpstatus' => array_shift($headerlines)
            ];
            foreach ($headerlines as $headerline) {
                $headerbits = explode(':', $headerline, 2);
                if (count($headerbits) == 2) {
                    // Only parse headers having colon separation.
                    $parsedresponseheaders[$headerbits[0]] = $headerbits[1];
                }
            }
            $response = new http_response(['headers' => $parsedresponseheaders, 'body' => $resbody], intval($info['http_code']));
            if ($response->getStatusCode() >= 400) {
                throw new http_exception($response, "An HTTP error status was received: '{$response->getHeaders()['httpstatus']}'");
            }
            return $response;
        }
        // The curl client experienced errors, so report that.
        throw new \Exception("There was a cURL error when making the request: errno: {$this->curlclient->get_errno()},
            error: {$this->curlclient->error}.");
    }
}
