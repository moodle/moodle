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

use Slim\Exception\HttpMethodNotAllowedException;

/**
 * A response for when a request method is not allowed.
 *
 * @package    core
 * @copyright  2026 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class method_not_allowed_response extends exception_response {
    #[\Override]
    public static function get_exception_status_code(): int {
        return 405;
    }

    #[\Override]
    protected static function get_additional_headers(\Exception $exception): array {
        $headers = parent::get_additional_headers($exception);
        if ($exception instanceof HttpMethodNotAllowedException) {
            $headers['Allow'] = implode(', ', $exception->getAllowedMethods());
        }
        return $headers;
    }
}
