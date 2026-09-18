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

namespace core\router\response;

use core\exception\www_authenticate_aware_exception;

/**
 * Trait for handling WWW-Authenticate headers in HTTP responses.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait www_authenticate_header_trait {
    /**
     * Get the WWW-Authenticate header value.
     *
     * @param \Exception $exception
     * @param string $error The error code to include in the WWW-Authenticate header.
     * @return array{"WWW-Authenticate": string}
     */
    protected static function get_www_authenticate_header(
        \Exception $exception,
        string $error,
    ): array {
        // Now add the WWW-Authenticate header for the specified error.
        $authenticateheader = 'Bearer realm="Moodle API"';

        if ($exception instanceof www_authenticate_aware_exception) {
            $authenticateparams = $exception->get_www_authenticate_params();
        } else {
            $authenticateparams = ['error="' . $error . '"'];
        }

        if (!empty($authenticateparams)) {
            $authenticateheader .= ', ' . implode(', ', $authenticateparams);
        }

        return ['WWW-Authenticate' => $authenticateheader];
    }
}
