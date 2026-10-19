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

use core\exception\insufficient_scope_exception;

/**
 * A response for when access is denied to a resource due to insufficient scope.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class insufficient_scope_response extends access_denied_response {
    #[\Override]
    protected static function add_additional_headers(
        \Psr\Http\Message\ResponseInterface $response,
        \Exception $exception,
    ): \Psr\Http\Message\ResponseInterface {
        $response = parent::add_additional_headers($response, $exception);

        // Now add the WWW-Authenticate header for insufficient scope.
        $authenticateheader = 'Bearer realm="Moodle API", error="insufficient_scope"';
        if ($exception instanceof insufficient_scope_exception) {
            foreach ($exception->scopesets as $scopeset) {
                $response = $response->withAddedHeader(
                    'WWW-Authenticate',
                    $authenticateheader
                        . ', error_description="' . $exception->hint . '"'
                        . ', scope="' . implode(' ', $scopeset->requiredscopes) . '"',
                );
            }
        } else {
            $response = $response->withAddedHeader(
                'WWW-Authenticate',
                $authenticateheader,
            );
        }

        return $response;
    }
}
