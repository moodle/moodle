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

namespace core_ai\error;

/**
 * Base class for handling errors.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base {
    /** @var int Error type for less. It means the LMS debug level is < "ALL" */
    protected const ERROR_TYPE_MINIMAL = 0;

    /** @var int Error type for more. It means the LMS debug level is >= "ALL". */
    protected const ERROR_TYPE_DETAILED = 1;

    /** @var string Error code for upstream errors. */
    protected const ERROR_SOURCE_UPSTREAM = "upstream";

    /** @var int The type of error message. */
    protected int $messagetype;

    /**
     * Constructor for the error handler.
     *
     * @param int $errorcode The error code.
     * @param string $errormessage The error message.
     * @param string $errorsource The error source.
     */
    public function __construct(
        /**
         * @var int The error code.
         */
        private readonly int $errorcode,
        /**
         * @var string The error message.
         */
        private readonly string $errormessage,
        /**
         * @var string The error source.
         */
        private readonly string $errorsource = "upstream",
    ) {
        $this->messagetype = static::get_message_error_type();
    }

    /**
     * Get the error code.
     *
     * @return int The error code.
     */
    public function get_errorcode(): int {
        return $this->errorcode;
    }

    /**
     * Get the error name.
     *
     * @return string The error source.
     */
    public function get_error(): string {
        if ($this->messagetype === static::ERROR_TYPE_MINIMAL) {
            return get_string('error:defaultname', 'core_ai');
        }

        return "{$this->errorcode}: {$this->get_errorcode_description($this->errorcode)}";
    }

    /**
     * Get the error message.
     *
     * @return string The error message.
     */
    public function get_errormessage(): string {
        if ($this->messagetype === static::ERROR_TYPE_MINIMAL) {
            return get_string('error:defaultmessage', 'core_ai');
        }

        return $this->errormessage;
    }

    /**
     * Get the error message.
     *
     * @return string The error message.
     */
    public function get_errorsource(): string {
        return $this->errorsource;
    }

    /**
     * Get the error data and return its details.
     *
     * @return array The error details.
     */
    public function get_error_details(): array {
        return [
            'success' => false,
            'errorcode' => static::get_errorcode(),
            'error' => static::get_error(),
            'errormessage' => static::get_errormessage(),
        ];
    }

    /**
     * Get the message error type based on the debug configuration.
     *
     * @return int The message error type.
     */
    protected function get_message_error_type(): int {
        global $CFG;

        if ($CFG->debug === DEBUG_ALL || $CFG->debug === DEBUG_DEVELOPER) {
            return static::ERROR_TYPE_DETAILED;
        }

        return static::ERROR_TYPE_MINIMAL;
    }

    /**
     * Get the error code description.
     *
     * @param int $errorcode The error code.
     * @return string The error code description.
     */
    protected function get_errorcode_description(int $errorcode): string {
        $errordescription = [
            400 => get_string('error:400', 'core_ai'),
            401 => get_string('error:401', 'core_ai'),
            404 => get_string('error:404', 'core_ai'),
            429 => get_string('error:429', 'core_ai'),
            500 => get_string('error:500', 'core_ai'),
            503 => get_string('error:503', 'core_ai'),
        ];

        return $errordescription[$errorcode] ?? get_string('error:unknown', 'core_ai');
    }
}
