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
 * This file contains an abstract definition of an LTI service
 *
 * @package    mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_lti\local\ltiservice;

defined('MOODLE_INTERNAL') || die;

/**
 * The mod_lti\local\ltiservice\response class.
 *
 * @package    mod_lti
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response {

    /** @var int HTTP response code. */
    private $code;
    /** @var string HTTP response reason. */
    private $reason;
    /** @var string HTTP request method. */
    private $requestmethod;
    /** @var string HTTP request accept header. */
    private $accept;
    /** @var string HTTP response content type. */
    private $contenttype;
    /** @var string HTTP request body. */
    private $data;
    /** @var string HTTP response body. */
    private $body;
    /** @var array HTTP response codes. */
    private $responsecodes;
    /** @var array HTTP additional headers. */
    private $additionalheaders;

    /**
     * Class constructor.
     */
    public function __construct() {

        $this->code = 200;
        $this->reason = '';
        $this->requestmethod = $_SERVER['REQUEST_METHOD'];
        $this->accept = '';
        $this->contenttype = '';
        $this->data = '';
        $this->body = '';
        $this->responsecodes = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            300 => 'Multiple Choices',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            415 => 'Unsupported Media Type',
            500 => 'Internal Server Error',
            501 => 'Not Implemented'
        );
        $this->additionalheaders = array();

    }

    /**
     * Get the response code.
     *
     * @return int
     */
    public function get_code() {
        return $this->code;
    }

    /**
     * Set the response code.
     *
     * @param int $code Response code
     */
    public function set_code($code) {
        $this->code = $code;
        $this->reason = '';
    }

    /**
     * Get the response reason.
     *
     * @return string
     */
    public function get_reason() {
        if (empty($this->reason)) {
            $this->reason = $this->responsecodes[$this->code];
        }
        // Use generic reason for this category (based on first digit) if a specific reason is not defined.
        if (empty($this->reason)) {
            $this->reason = $this->responsecodes[intval($this->code / 100) * 100];
        }
        return $this->reason;
    }

    /**
     * Set the response reason.
     *
     * @param string $reason Reason
     */
    public function set_reason($reason) {
        $this->reason = $reason;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function get_request_method() {
        return $this->requestmethod;
    }

    /**
     * Get the request accept header.
     *
     * @return string
     */
    public function get_accept() {
        return $this->accept;
    }

    /**
     * Set the request accept header.
     *
     * @param string $accept Accept header value
     */
    public function set_accept($accept) {
        $this->accept = $accept;
    }

    /**
     * Get the response content type.
     *
     * @return string
     */
    public function get_content_type() {
        return $this->contenttype;
    }

    /**
     * Set the response content type.
     *
     * @param string $contenttype Content type
     */
    public function set_content_type($contenttype) {
        $this->contenttype = $contenttype;
    }

    /**
     * Get the request body.
     *
     * @return string
     */
    public function get_request_data() {
        return $this->data;
    }

    /**
     * Set the response body.
     *
     * @param string $data Body data
     */
    public function set_request_data($data) {
        $this->data = $data;
    }

    /**
     * Set the response body.
     *
     * @param string $body Body data
     */
    public function set_body($body) {
        $this->body = $body;
    }

    /**
     * Add an additional header.
     *
     * @param string $header The new header
     */
    public function add_additional_header($header) {
        array_push($this->additionalheaders, $header);
    }

    /**
     * Send the response.
     */
    public function send() {
        header("HTTP/1.0 {$this->code} {$this->get_reason()}");
        foreach ($this->additionalheaders as $header) {
            header($header);
        }
        if (($this->code >= 200) && ($this->code < 300)) {
            if (!empty($this->contenttype)) {
                header("Content-Type: {$this->contenttype};charset=UTF-8");
            }
            if (!empty($this->body)) {
                echo $this->body;
            }
        }
    }

}
