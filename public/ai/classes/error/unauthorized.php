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
 * Error 401 handler class.
 *
 * The HTTP 401 Unauthorised error code is used when a request requires user authentication,
 * but the authentication has either not been provided or failed.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class unauthorized extends base {
    /**
     * Error code for 401.
     */
    const ERROR_CODE = 401;

    /**
     * Constructor for the error handler.
     *
     * @param string $errormessage The error message.
     * @param string $errorsource The error source.
     */
    public function __construct(
        /**
         * @var string The error message.
         */
        private readonly string $errormessage,
        /**
         * @var string The error source.
         */
        private readonly string $errorsource = "upstream",
    ) {
        parent::__construct(static::ERROR_CODE, $errormessage, $errorsource);
    }

    #[\Override]
    public function get_errormessage(): string {
        if ($this->messagetype === static::ERROR_TYPE_MINIMAL && $this->errorsource === static::ERROR_SOURCE_UPSTREAM) {
            return get_string('error:401:upstreamless', 'core_ai');
        }
        return $this->errormessage;
    }
}
