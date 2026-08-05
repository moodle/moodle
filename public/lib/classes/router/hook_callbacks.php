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

/**
 * Class hook_callbacks
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Provide DI Configuration for the Router system.
     *
     * @param \core\hook\di_configuration $hook
     * @codeCoverageIgnore
     */
    public static function provide_di_configuration(
        \core\hook\di_configuration $hook,
    ): void {
        $hook->add_definition(
            request_validator_interface::class,
            \DI\get(request_validator::class),
        );
        $hook->add_definition(
            response_validator_interface::class,
            \DI\get(response_validator::class),
        );
        $hook->add_definition(
            route_loader_interface::class,
            \DI\get(route_loader::class),
        );

        // Until Moodle 6.0 we will allow the router to be unconfigured for backwards compatibility.
        // To support legacy shims like /course/tags.php we need a custom route resolver which tries both.
        // The following definition can be removed in Moodle 6.0.
        // - \Slim\Interfaces\RouteResolverInterface
        // - \Slim\Interfaces\RouteCollectorInterface
        // - \Psr\Http\Message\ResponseFactoryInterface
        // Along with the route_resolver class itself.
        // TODO MDL-87637 Remove in Moodle 6.0.
        $hook->add_definition(
            \Slim\Interfaces\RouteResolverInterface::class,
            \DI\get(route_resolver::class),
        );
        $hook->add_definition(
            \Slim\Interfaces\RouteCollectorInterface::class,
            \DI\get(\Slim\Routing\RouteCollector::class),
        );
        $hook->add_definition(
            \Psr\Http\Message\ResponseFactoryInterface::class,
            \DI\factory([\Slim\Factory\AppFactory::class, 'determineResponseFactory']),
        );
    }
}
