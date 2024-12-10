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

namespace core\router\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to handle notification of deprecated API routes.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deprecated_api_route_middleware extends validation_middleware {
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // Fetch the reflected method for the current request. This will be used to determine if the route is deprecated.
        $reflectedmethod = \core\router\util::get_reflected_method_from_request($request);
        if ($reflectedmethod) {
            $reference = [$reflectedmethod->class, $reflectedmethod->name];
            $deprecation = \core\deprecation::from($reference);
            if ($deprecation && $deprecation->emit) {
                // Either the route method, or the route itself is deprecated.
                if ($deprecation->final) {
                    // Emit a deprecation notice for the final route.
                    // This throws an exception.
                    \core\deprecation::emit_deprecation($reference);
                }

                // Log a deprecation warning for non-final routes.
                $deprecationstring = \core\deprecation::get_deprecation_string($deprecation);
                // phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative
                error_log(sprintf(
                    "%s: %s",
                    $deprecationstring,
                    $request->getUri()->__toString(),
                ));
            }
        }

        return $handler->handle($request);
    }
}
