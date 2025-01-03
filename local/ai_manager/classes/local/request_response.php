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

namespace local_ai_manager\local;

use Psr\Http\Message\StreamInterface;

/**
 * Data object class for storing prompt result information in a defined way.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class request_response {

    /** @var StreamInterface The response object containing the response stream */
    private StreamInterface $response;

    /** @var int The status code of the response */
    private int $code;

    /** @var string The error message if there was an error */
    private string $errormessage;

    /** @var string The debug info if there was an error */
    private string $debuginfo;

    /**
     * Private constructor to avoid object creation without static create function.
     *
     * Additional data is being injected by the setters.
     */
    private function __construct() {
    }

    /**
     * Standard setter.
     *
     * @param StreamInterface $response the response as stream
     */
    public function set_response(StreamInterface $response): void {
        $this->response = $response;
    }

    /**
     * Standard setter.
     *
     * @param string $errormessage the error message to store in this object
     */
    public function set_errormessage(string $errormessage): void {
        $this->errormessage = $errormessage;
    }

    /**
     * Standard setter.
     *
     * @param string $debuginfo the debug info to store in this object
     */
    public function set_debuginfo(string $debuginfo): void {
        $this->debuginfo = $debuginfo;
    }

    /**
     * Standard setter.
     *
     * @param int $code The status code of the response
     */
    public function set_code(int $code): void {
        $this->code = $code;
    }

    /**
     * Standard getter.
     *
     * @return string the error message (can be empty, if there was no error)
     */
    public function get_errormessage(): string {
        return $this->errormessage;
    }

    /**
     * Standard getter.
     *
     * @return string the debug info (can be empty, if there was no error)
     */
    public function get_debuginfo(): string {
        return $this->debuginfo;
    }

    /**
     * Standard getter.
     *
     * @return StreamInterface the response stream object
     */
    public function get_response(): StreamInterface {
        return $this->response;
    }

    /**
     * Standard getter.
     *
     * @return int the status code of the response
     */
    public function get_code(): int {
        return $this->code;
    }

    /**
     * Static create function for a request_response object in case of an error.
     *
     * @param int $code the status code
     * @param string $errormessage the error message
     * @param string $debuginfo the debug info
     * @param ?StreamInterface $rawresponse the raw response object, or null if not available
     * @return request_response the request_response object containing all information about the error
     */
    public static function create_from_error(int $code, string $errormessage, string $debuginfo,
            ?StreamInterface $rawresponse = null): request_response {
        $requestresponse = new self();
        $requestresponse->set_code($code);
        $requestresponse->set_errormessage($errormessage);
        $requestresponse->set_debuginfo($debuginfo);
        if (!empty($rawresponse)) {
            $requestresponse->set_response($rawresponse);
        }
        return $requestresponse;
    }

    /**
     * Static create function for a request_response object in case of a successful response.
     *
     * @param StreamInterface $response the response as stream
     * @return request_response the request_response object containing the response in a structured way
     */
    public static function create_from_result(StreamInterface $response): request_response {
        $requestresponse = new self();
        $requestresponse->set_code(200);
        $requestresponse->set_response($response);
        return $requestresponse;
    }
}
