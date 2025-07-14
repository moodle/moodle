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

use core\component;
use core\router\schema\specification;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use Throwable;

/**
 * Moodle Router.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class apidocs {
    /**
     * Generate the API docs for the API.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function openapi_docs(
        ResponseInterface $response,
    ): ResponseInterface {
        global $PAGE;
        $PAGE->set_context(\core\context\system::instance());

        $api = new specification();

        $classes = $this->get_openapi_classes_names();
        foreach (array_keys($classes) as $classname) {
            $classinfo = new ReflectionClass($classname);
            [$component] = explode('\\', $classinfo->getNamespaceName());

            $classroutes = $classinfo->getAttributes(route::class);

            if ($classroutes) {
                foreach ($classroutes as $classroute) {
                    $parentroute = $classroute->newInstance();
                    $this->get_api_docs_for_route(
                        component: $component,
                        classinfo: $classinfo,
                        api: $api,
                        parentcontexts: [$parentroute],
                    );
                }
            } else {
                $this->get_api_docs_for_route(
                    component: $component,
                    classinfo: $classinfo,
                    api: $api,
                );
            }
        }

        // At the moment only json is supported. This could be extended to support other formats in future.
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody(\GuzzleHttp\Psr7\Utils::streamFor(
                json_encode(
                    $api,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                ),
            ));
    }

    /**
     * Get the list of OpenAPI Class Names.
     *
     * @return string[]
     */
    protected function get_openapi_classes_names(): array {
        global $CFG;

        $classes = [];
        foreach (component::get_component_names(true) as $component) {
            try {
                $classes = array_merge(
                    $classes,
                    component::get_component_classes_in_namespace(
                        component: $component,
                        namespace: 'route\api',
                    ),
                );
            // @codeCoverageIgnoreStart
            } catch (Throwable $error) {
                // Some kind of error occurred whilst loading routes in this component.
                // When debugging, this is useful to know.
                // When not, log to error_log.
                if (!$CFG->debugdisplay) {
                    debugging('Error loading route data: ' . $error->getMessage());
                } else {
                    default_exception_handler($error);
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $classes;
    }


    /**
     * Get the API Docs for the specified Route.
     *
     * @param string $component The component that the route relates to
     * @param ReflectionClass $classinfo
     * @param specification $api
     * @param array $parentcontexts
     * @return self
     */
    protected function get_api_docs_for_route(
        string $component,
        ReflectionClass $classinfo,
        specification $api,
        array $parentcontexts = [],
    ): self {
        $methods = $classinfo->getMethods();
        foreach ($methods as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            // Get the route attribute for this method.
            $routeattribute = util::get_route_instance_for_method(
                [$classinfo->getName(), $method->getName()],
            );

            if ($routeattribute === null) {
                // This method has no route attribute. Maybe just a helper method.
                continue;
            }

            // Add this path to the OpenAPI specification.
            $api->add_path(
                component: $component,
                route: $routeattribute,
            );
        }

        return $this;
    }
}
