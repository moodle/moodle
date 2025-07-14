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

namespace core\router;

use core\exception\invalid_parameter_exception;
use core\exception\response_aware_exception;
use core\router;
use core\router\response\exception_response;
use core\router\response\invalid_parameter_response;
use core\router\schema\response\response_type;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller Invoker for the Moodle Router.
 *
 * This class handles invocation of the route callable, and the conversion of the response into an appropriate format.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_handler {
    /**
     * Create a new response handler.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        /** @var ContainerInterface */
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * Invoke a route callable.
     *
     * Note: Much of this is copied from the parent class, but we need to handle the response differently.
     *
     * @param ResponseInterface|response_type $response The response object.
     * @return ResponseInterface The response from the callable.
     */
    public function standardise_response(
        ResponseInterface | response_type $response,
    ): ResponseInterface {
        if ($response instanceof ResponseInterface) {
            // An object implementing ResponseInterface is returned, so we can just return it.
            return $response;
        }

        $responsefactory = $this->container->get(router::class)->get_response_factory();

        // This must be a response\response_type.
        return $response->get_response($responsefactory);
    }

    /**
     * Get the response from an exception.
     *
     * @param ServerRequestInterface $request
     * @param \Exception $exception
     *
     * @return ResponseInterface
     */
    public function get_response_from_exception(
        ServerRequestInterface $request,
        \Exception $exception,
    ): ResponseInterface {
        $response = match (true) {
            // Newer exceptions may be response-aware, so we can use the response class they specify.
            (is_a($exception, response_aware_exception::class)) => $exception->get_response_classname()::get_response(
                $request,
                $exception,
            ),

            // Some legacy expressions are here for the moment.
            is_a($exception, invalid_parameter_exception::class) => invalid_parameter_response::get_response(
                $request,
                $exception,
            ),

            // Otherwise use the default.
            default => exception_response::get_response($request, $exception),
        };

        return $this->standardise_response($response);
    }
}
