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

/**
 * Data object class for storing prompt result information in a defined way.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prompt_response {

    /** @var string The model which has been used by the external AI tool to generate the reponse. */
    private string $model;

    /** @var usage The usage object containing the usage information (used tokens etc.) */
    private usage $usage;

    /** @var string The content of the response */
    private string $content;

    /** @var int The status code of the response */
    private int $code;

    /** @var string If there has been an error, this variable contains the error message */
    private string $errormessage = '';

    /** @var string If there has been an error, this variable contains additional debugging information */
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
     * @param string $model The model which has been used
     */
    public function set_model(string $model): void {
        $this->model = $model;
    }

    /**
     * Standard setter.
     *
     * @param usage $usage The usage object to be set
     */
    public function set_usage(usage $usage): void {
        $this->usage = $usage;
    }

    /**
     * Standard setter.
     *
     * @param string $content The content of the response
     */
    public function set_content(string $content): void {
        $this->content = $content;
    }

    /**
     * Standard setter.
     *
     * @param string $errormessage The error message to store
     */
    public function set_errormessage(string $errormessage): void {
        $this->errormessage = $errormessage;
    }

    /**
     * Standard setter.
     *
     * @param string $debuginfo The debug info to store
     */
    public function set_debuginfo(string $debuginfo): void {
        $this->debuginfo = $debuginfo;
    }

    /**
     * Standard setter.
     *
     * @param int $code the status code of the response
     */
    public function set_code(int $code): void {
        $this->code = $code;
    }

    /**
     * Standard getter.
     *
     * @return string the model name which has been returned by the external AI tool
     */
    public function get_modelinfo(): string {
        return $this->model;
    }

    /**
     * Standard getter.
     *
     * @return usage the usage object containing the usage information from the response
     */
    public function get_usage(): usage {
        return $this->usage;
    }

    /**
     * Standard getter.
     *
     * @return string the content of the response
     */
    public function get_content(): string {
        return $this->content;
    }

    /**
     * Standard getter.
     *
     * @return string the error message (can be empty if there were no error)
     */
    public function get_errormessage(): string {
        return $this->errormessage;
    }

    /**
     * Standard getter.
     *
     * @return string the debug info (can be empty if there were no error)
     */
    public function get_debuginfo(): string {
        return $this->debuginfo;
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
     * Static create function for a prompt_response object in case of an error.
     *
     * @param int $code the status code of the response
     * @param string $errormessage the error message
     * @param string $debuginfo the debug info
     * @return prompt_response the prompt_response object containing the error information
     */
    public static function create_from_error(int $code, string $errormessage, string $debuginfo): prompt_response {
        if ($code === 200) {
            throw new \coding_exception('You cannot create an error with code 200');
        }
        $promptresponse = new self();
        $promptresponse->set_code($code);
        $promptresponse->set_errormessage($errormessage);
        $promptresponse->set_debuginfo($debuginfo);
        return $promptresponse;
    }

    /**
     * Static create function for a prompt_response object in case of a successful response.
     *
     * @param string $model the model which has been used
     * @param usage $usage the usage information
     * @param string $content the content of the response
     * @return prompt_response the prompt_response object containing all information of the response in a structured way
     */
    public static function create_from_result(string $model, usage $usage, string $content): prompt_response {
        $promptresponse = new self();
        $promptresponse->set_code(200);
        $promptresponse->set_model($model);
        $promptresponse->set_usage($usage);
        $promptresponse->set_content($content);
        return $promptresponse;
    }
}
