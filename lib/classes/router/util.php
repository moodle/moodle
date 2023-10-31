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

use moodle_url;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Routing Helper Utilities.
 *
 * This class includes a variety of helpers for working with routes, including:
 * - redirectors
 * - callable to route name converters
 * - callable to path converters
 * - helpers to fetch the \core\router\route instance
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util {
    /**
     * Redirect to the specified URL, carrying all parameters across too.
     *
     * @param string|moodle_url $path
     * @param array $excludeparams Any parameters to exclude from the query params
     * @codeCoverageIgnore
     */
    public static function redirect_with_params(
        string|moodle_url $path,
        array $excludeparams = [],
    ): never {
        $params = $_GET;
        $url = new moodle_url(
            $path,
            $params,
        );
        $url->remove_params($excludeparams);

        redirect($url);
    }

    /**
     * Redirect to the route at the callable supplied.
     *
     * @param callable|array|string $callable
     * @param array $params Any parameters to include in the path
     * @codeCoverageIgnore
     */
    public static function redirect_to_callable(
        callable|array|string $callable,
        array $params = [],
    ): never {
        $params = array_merge(
            $_GET,
            $params,
        );

        $url = self::get_path_for_callable($callable, $params, $params);

        redirect($url);
    }

    /**
     * Get the route name for the specified callable.
     *
     * @param callable|array|string $callable
     * @return string
     * @throws \coding_exception If the callable could not be resolved into an Array format
     */
    public static function get_route_name_for_callable(
        callable|array|string $callable,
    ): string {
        $resolver = \core\di::get(\Invoker\CallableResolver::class);
        $callable = $resolver->resolve($callable);

        if (!is_array($callable)) {
            throw new \coding_exception('Resolved callable must be in array form');
        }

        return get_class($callable[0]) . '::' . $callable[1];
    }

    /**
     * Get the URI path for the specified callable.
     *
     * @param string|array|callable $callable the Callable to get the URI for
     * @param array $params Any parameters to include in the path
     * @param array $queryparams Any parameters to include in the query string
     * @return moodle_url
     */
    public static function get_path_for_callable(
        string|array|callable $callable,
        array $params,
        array $queryparams,
    ): moodle_url {
        global $CFG;

        $router = \core\di::get(\core\router::class);
        $app = $router->get_app();
        $parser = $app->getRouteCollector()->getRouteParser();

        $routename = self::get_route_name_for_callable($callable);

        return new moodle_url(
            url: $parser->fullUrlFor(
                new Uri($CFG->wwwroot),
                $routename,
                $params,
                $queryparams,
            ),
        );
    }

    /**
     * Get the route attribute for the specified request.
     *
     * @param ServerRequestInterface $request
     * @return null|route
     */
    public static function get_route_instance_for_request(ServerRequestInterface $request): ?route {
        if ($route = $request->getAttribute(route::class)) {
            return $route;
        }

        $context = RouteContext::fromRequest($request);
        if ($slimroute = $context->getRoute()) {
            return self::get_route_instance_for_method($slimroute->getCallable());
        }

        // This should not be encountered - the route should always be set.
        return null; // @codeCoverageIgnore
    }

    /**
     * Get the instance of the \route\router\route attribute for the specified callable if one is available.
     *
     * @param callable|array|string $callable
     * @return null|route The route if one was found.
     */
    public static function get_route_instance_for_method(callable|array|string $callable): ?route {
        // Normalise the callable using the resolver.
        // This happens in the same way that Slim does so.
        $resolver = \core\di::get(\Invoker\CallableResolver::class);
        $callable = $resolver->resolve($callable);

        if (!is_array($callable)) {
            // The callable could not be resolved into an array.
            return null;
        }

        // Locate the Class for this callable.
        $classinfo = new \ReflectionClass($callable[0]);

        // Locate the method for this callable.
        $methodinfo = $classinfo->getMethod($callable[1]);
        if (!$methodinfo) {
            // The method does not exist. This shouldn't be possible because the resolver will throw an exception.
            return null; // @codeCoverageIgnore
        }

        return self::attempt_get_route_instance_for_method($classinfo, $methodinfo);
    }

    /**
     * Attempt to get the route instance for the specified method, handling any errors in the code along the way.
     *
     * @param \ReflectionClass $classinfo
     * @param \ReflectionMethod $methodinfo
     * @return null|route
     */
    private static function attempt_get_route_instance_for_method(
        \ReflectionClass $classinfo,
        \ReflectionMethod $methodinfo,
    ): ?route {
        $instantiator = function (array $attributes) {
            global $CFG;
            try {
                return $attributes ? $attributes[0]->newInstance() : null;
            // @codeCoverageIgnoreStart
            } catch (\Throwable $e) {
                // The route attribute could not be instantiated.
                // When debugging, this is useful to know.
                // When not, log to error_log.
                if (!$CFG->debugdisplay) {
                    debugging('Could not instantiate route attribute: ' . $e->getMessage());
                    return null;
                }

                default_exception_handler($e);
            }
            // @codeCoverageIgnoreEnd
        };

        $methodattributes = $methodinfo->getAttributes(route::class);
        $methodroute = $instantiator($methodattributes);

        if (!$methodroute) {
            // No route found.
            return null;
        }

        $classattributes = $classinfo->getAttributes(route::class);
        if ($classattributes) {
            $classinstance = $instantiator($classattributes);
            if ($classinstance) {
                // The class has a #route attribute.
                $methodroute->set_parent($classinstance);
            }
        }

        return $methodroute;
    }
}
