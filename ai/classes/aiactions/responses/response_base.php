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

namespace core_ai\aiactions\responses;

use core\exception\coding_exception;

/**
 * Action response base class.
 * Any method that processes an action must return an instance of this class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class response_base {
    /** @var int The timestamp of when the response was created. */
    private int $timecreated;

    /**
     * Constructor.
     *
     * @param bool $success The success status of the action.
     * @param string $actionname The name of the action that was processed.
     * @param int $errorcode Error code. Must exist if success is false.
     * @param string $errormessage Error message. Must exist if success is false
     * @param string $model The model used to generate the response.
     */
    public function __construct(
        /** @var bool The success status of the action. */
        private bool $success,
        /** @var string The name of the action that was processed. */
        private string $actionname,
        /** @var int  Error code. Must exist if status is error. */
        private int $errorcode = 0,
        /** @var string Error message. Must exist if status is error */
        private string $errormessage = '',
        /** @var string The model used to generate the response (if available). */
        protected ?string $model = null,

    ) {
        $this->timecreated = \core\di::get(\core\clock::class)->time();
        if (!$success && ($errorcode == 0 || empty($errormessage))) {
            throw new coding_exception('Error code and message must exist in an error response.');
        }
    }

    /**
     * Set the response data returned by the AI provider.
     *
     * @param array $response The response data returned by the AI provider.
     */
    abstract public function set_response_data(array $response): void;

    /**
     * Get the response data returned by the AI provider.
     *
     * @return array
     */
    abstract public function get_response_data(): array;

    /**
     * Get the success status of the action.
     *
     * @return bool
     */
    public function get_success(): bool {
        return $this->success;
    }

    /**
     * Get the timestamp of when the response was created.
     *
     * @return int
     */
    public function get_timecreated(): int {
        return $this->timecreated;
    }

    /**
     * Get the name of the action that was processed.
     *
     * @return string
     */
    public function get_actionname(): string {
        return $this->actionname;
    }

    /**
     * Get the error code.
     *
     * @return int
     */
    public function get_errorcode(): int {
        return $this->errorcode;
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    public function get_errormessage(): string {
        return $this->errormessage;
    }

    /**
     * Get the model used to generate the response (if available).
     *
     * @return ?string
     */
    public function get_model_used(): ?string {
        return $this->model;
    }
}
