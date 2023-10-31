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

namespace core\router\schema\parameters;

use core\router\route;
use core\router\schema\parameter;
use core\router\schema\specification;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\Route as RoutingRoute;
use stdClass;

/**
 * Routing parameter for validation.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_parameter extends parameter {
    /**
     * Create an instance of a new path parameter.
     *
     * @param mixed ...$extra Additional parameters for the parameter
     */
    public function __construct(
        ...$extra,
    ) {
        $extra['in'] = parameter::IN_PATH;
        parent::__construct(
            ...$extra,
        );
    }

    /**
     * Validate the path parameter.
     *
     * @param ServerRequestInterface $request
     * @param RoutingRoute $route
     * @return ServerRequestInterface The modified request with parameters updated
     */
    public function validate(
        ServerRequestInterface $request,
        RoutingRoute $route,
    ): ServerRequestInterface {
        $args = $route->getArguments();

        $value = $route->getArgument($this->name);

        $this->type->validate_param(
            param: $value,
            allownull: NULL_ALLOWED,
        );

        if (is_a($this, mapped_property_parameter::class)) {
            // Unfortunately args must be a string, but mapped properties can be an object.
            // Remove the argument, and instead provide the mapped property as an attribute.
            unset($args[$this->name]);
            $route->setArguments($args);
            $request = $this->add_attributes_for_parameter_value($request, $value);
        }

        return $request;
    }

    #[\Override]
    final public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?stdClass {
        if ($path && !str_contains($path, "{{$this->name}}")) {
            // In OpenAPI, Path parameters can never be optional.
            return null;
        }
        $data = parent::get_openapi_description(
            api: $api,
            path: $path,
        );
        $data->required = true;

        return $data;
    }

    /**
     * Check whether this parameter is required for the given route.
     *
     * @param route $route
     * @return bool
     */
    public function is_required(route $route): bool {
        $path = $route->get_path();

        // Find the position of the parameter in the path.
        $paramposition = strpos($path, '{' . $this->name . '}');

        // If _any_ part of the path before the parameter contains a '[' character, then this _must_ be optional.
        // A required parameter cannot follow an optional parameter.
        return !str_contains(substr($path, 0, $paramposition), '[');
    }
}
