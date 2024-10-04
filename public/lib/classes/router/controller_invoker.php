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

use Invoker\InvokerInterface;
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
class controller_invoker implements \Slim\Interfaces\InvocationStrategyInterface {
    /**
     * Create a new controller invoker.
     *
     * @param ContainerInterface $container
     * @param InvokerInterface $invoker
     */
    public function __construct(
        /** @var ContainerInterface The DI container */
        protected ContainerInterface $container,
        /** @var InvokerInterface The invoker */
        protected InvokerInterface $invoker,
    ) {
    }

    #[\Override]
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments, // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
    ): ResponseInterface {
        // Inject the request and response by parameter name.
        $parameters = [
            'request'  => self::inject_route_arguments(
                $request,
                $routeArguments, // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
            ),
            'response' => $response,
        ];

        // Inject the route arguments by name.
        $parameters += $routeArguments; // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase

        // Inject the attributes defined on the request.
        $parameters += $request->getAttributes();

        $result = $this->invoker->call($callable, $parameters);

        return $this->container->get(response_handler::class)->standardise_response($result);
    }

    /**
     * Helper to inject route arguments.
     *
     * This is based on the ControllerInvoker.
     *
     * @param ServerRequestInterface $request
     * @param array $routeargs
     * @return ServerRequestInterface
     */
    private static function inject_route_arguments(
        ServerRequestInterface $request,
        array $routeargs,
    ): ServerRequestInterface {
        $args = $request;
        foreach ($routeargs as $key => $value) {
            // Note: This differs to upstream where route args always override attributes.
            // We apply mapped parameters via route attributes and must therefore override the route args.
            if (!$args->getAttribute($key)) {
                $args = $args->withAttribute($key, $value);
            }
        }
        return $args;
    }
}
