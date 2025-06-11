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
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A controller to make it easier to implement a route.
 *
 * This controller adds the Container to the constructor which allows controllers to support DI.
 *
 * This trait is entirely optional.
 * @package core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait route_controller {
    /**
     * Constructor for Route Controllers.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        /** @var ContainerInterface The DI Container */
        protected ContainerInterface $container,
    ) {
    }

    /**
     * Generate a Page Not Found result.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Slim\Exception\HttpNotFoundException
     */
    protected function page_not_found(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        throw new \Slim\Exception\HttpNotFoundException($request);
    }

    /**
     * Redirect to a URL.
     *
     * @param ResponseInterface $response
     * @param string|moodle_url $url
     * @return ResponseInterface
     */
    protected function redirect(
        ResponseInterface $response,
        string|moodle_url $url,
    ): ResponseInterface {
        return $response
            ->withStatus(302)
            ->withHeader('Location', (string) $url);
    }

    /**
     * Redirect to the requested callable.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array|callable|string $callable
     * @param null|array $pathparams
     * @param null|array $queryparams
     * @param null|array $excludeparams A list of any parameters to remove the URI during the redirect
     * @return ResponseInterface
     */
    protected function redirect_to_callable(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array|callable|string $callable,
        ?array $pathparams = null,
        ?array $queryparams = null,
        ?array $excludeparams = null,
    ): ResponseInterface {
        // Provide defaults for the path and query params if not specified.
        if ($pathparams === null) {
            $pathparams = $request->getQueryParams();
        }
        if ($queryparams === null) {
            $queryparams = $request->getQueryParams();
        }

        // Generate a URI from the callable and the parameters.
        $url = util::get_path_for_callable(
            $callable,
            $pathparams ?? [],
            $queryparams ?? [],
        );

        // Remove any params.
        $url->remove_params($excludeparams);

        return $this->redirect($response, $url);
    }

    /**
     * Get a parameter from the query params after validation.
     *
     * @param ServerRequestInterface $request
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get_param(
        ServerRequestInterface $request,
        string $key,
        mixed $default = null,
    ): mixed {
        $params = $request->getQueryParams();
        if (array_key_exists($key, $params)) {
            return $params[$key];
        } else {
            debugging("Missing parameter: $key");
        }

        return $default;
    }
}
