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

use Packback\Lti1p3\Interfaces\IHttpResponse;

/**
 * An implementation of IHTTPResponse, for use with the lib/lti1p3 library code.
 *
 * @package    enrol_lti
 * @copyright  2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class http_response implements IHttpResponse {

    /** @var string HTTP response body */
    private $body;

    /** @var array HTTP response header lines */
    private $headers;

    /** @var int http status code */
    private $statuscode;

    /**
     * Constructor.
     *
     * @param array $payload the array containing the body and headers.
     * @param int $statuscode the HTTP status code.
     */
    public function __construct(array $payload, int $statuscode) {
        $this->parse_payload($payload);
        $this->statuscode = $statuscode;
    }

    /**
     * Parse the array containing headers and body into instance vars.
     *
     * @param array $payload
     */
    private function parse_payload(array $payload): void {
        $this->headers = $payload['headers'];
        $this->body = $payload['body'];
    }

    /**
     * Get the HTTP response body string.
     *
     * @return string the HTTP response body.
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Get the HTTP headers array.
     *
     * @return array the array containing the headers.
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Get the HTTP response status code.
     *
     * @return int the HTTP response status code.
     */
    public function getStatusCode(): int {
        return $this->statuscode;
    }
}
