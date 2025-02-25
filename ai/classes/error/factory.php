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
 * Factory class for creating error objects.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class factory {
    /**
     * Create an error object based on the error code.
     *
     * @param int $errorcode The error code.
     * @param string $errormessage The error message.
     * @param string $errorsource The error source.
     * @return base
     * @throws \InvalidArgumentException
     */
    public static function create(int $errorcode, string $errormessage, string $errorsource = 'upstream'): base {
        // Check for specific error classes.
        $errorhandlers = [
            401 => unauthorized::class,
            404 => notfound::class,
            429 => ratelimit::class,
        ];
        if (isset($errorhandlers[$errorcode])) {
            return new $errorhandlers[$errorcode]($errormessage, $errorsource);
        }

        // Handle 5xx range.
        if ($errorcode >= 500 && $errorcode < 600) {
            return new serverfailure($errorcode, $errormessage, $errorsource);
        }

        // Default handler.
        if ($errorcode > 0) {
            return new base($errorcode, $errormessage, $errorsource);
        }

        throw new \InvalidArgumentException("Invalid error code: $errorcode");
    }
}
