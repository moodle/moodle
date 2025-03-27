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

namespace core;

use core\router\middleware\cors_middleware;
use core\router\middleware\error_handling_middleware;
use core\router\middleware\moodle_api_authentication_middleware;
use core\router\middleware\moodle_authentication_middleware;
use core\router\middleware\moodle_bootstrap_middleware;
use core\router\middleware\moodle_route_attribute_middleware;
use core\router\middleware\uri_normalisation_middleware;
use core\router\middleware\validation_middleware;
use core\router\request_validator_interface;
use core\router\response_handler;
use core\router\response_validator_interface;
use core\router\route_loader_interface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Middleware\ErrorMiddleware;

/**
 * Moodle Router.
 *
 * This class represents the Moodle Router, which handles all aspects of Routing in Moodle.
 *
 * It should not normally be accessed or used outside of its own unit tests, the route_testcase, and the `r.php` handler.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class router {
    /** @var string The base path to use for all requests */
    public readonly string $basepath;

    /** @var App The SlimPHP App */
    protected readonly App $app;

    /**
     * Create a new Router.
     *
     * @param response_handler $responsehandler
     * @param route_loader_interface $routeloader
     * @param request_validator_interface $requestvalidator
     * @param response_validator_interface $responsevalidator
     * @param null|string $basepath
     */
    public function __construct(
        /** @var response_handler */
        protected response_handler $responsehandler,

        /** @var route_loader_interface The router loader to use */
        protected readonly route_loader_interface $routeloader,

        /** @var request_validator_interface */
        protected request_validator_interface $requestvalidator,

        /** @var response_validator_interface */
        protected response_validator_interface $responsevalidator,

        ?string $basepath = null,
    ) {
        if ($basepath === null) {
            $basepath = $this->guess_basepath();
        }
        $this->basepath = $basepath;
    }

    /**
     * Guess the basepath for the Router.
     *
     * @return string
     */
    protected function guess_basepath(): string {
        global $CFG;

        // Moodle is not guaranteed to exist at the domain root.
        // Strip out the current script.
        $scriptroot = parse_url($CFG->wwwroot, PHP_URL_PATH) ?? '';
        $scriptfile = str_replace(
            realpath($CFG->dirroot),
            '',
            realpath($_SERVER['SCRIPT_FILENAME']),
        );
        // Replace occurrences of backslashes with forward slashes, especially on Windows.
        $scriptfile = str_replace('\\', '/', $scriptfile);

        // The server is not configured to rewrite unknown requests to automatically use the router.
        $userphp = false;
        if ($_SERVER && array_key_exists('REQUEST_URI', $_SERVER)) {
            if (str_starts_with($_SERVER['REQUEST_URI'], "{$scriptroot}/r.php")) {
                $userphp = true;
            }
        }

        if ($CFG->routerconfigured !== true || $userphp) {
            $scriptroot .= '/r.php';
        }

        return $scriptroot;
    }

    /**
     * Get the configured SlimPHP Application.
     *
     * @return App
     */
    public function get_app(): App {
        if (!isset($this->app)) {
            $this->create_app($this->basepath);
        }

        return $this->app;
    }

    /**
     * Get the Response Factory for the Router.
     *
     * @return ResponseFactoryInterface
     */
    public function get_response_factory(): ResponseFactoryInterface {
        return $this->get_app()->getResponseFactory();
    }

    /**
     * Create the configured SlimPHP Application.
     *
     * @param string $basepath The base path of the Moodle instance
     */
    protected function create_app(
        string $basepath = '',
    ): void {
        // Create an App using the DI Bridge.
        $this->app = router\bridge::create();

        // Add Middleware to the App.
        // Note: App Middleware is called before any Group or Route middleware.
        $this->add_middleware();
        $this->configure_caching();
        $this->configure_routes();

        // Configure the basepath for Moodle.
        $this->app->setBasePath($basepath);
    }

    /**
     * Add Middleware to the App.
     */
    protected function add_middleware(): void {
        // Middleware is added like an onion.
        // For a Response, the outer-most middleware is executed first, and the inner-most middleware is executed last.
        // For a Request, the inner-most middleware is executed first, and the outer-most middleware is executed last.

        // Add the body parsing middleware from Slim.
        // See https://www.slimframework.com/docs/v4/middleware/body-parsing.html for further information.
        $this->app->addBodyParsingMiddleware();

        // Add Middleware to Bootstrap Moodle from a request.
        $this->app->add(di::get(moodle_bootstrap_middleware::class));

        // Add the Moodle route attribute to the request.
        // This must be processed after the Routing Middleware has been processed on the request.
        $this->app->add(di::get(moodle_route_attribute_middleware::class));

        // Add the Routing Middleware as one of the outer-most middleware.
        // This allows the Route to be accessed before it is handled.
        // See https://www.slimframework.com/docs/v4/cookbook/retrieving-current-route.html for further information.
        $this->app->addRoutingMiddleware();

        // Add request normalisation middleware to standardise the URI.
        // This must be done before the Routing Middleware to ensure that the route is matched correctly.
        $this->app->add(di::get(uri_normalisation_middleware::class));

        // Add the Error Handling Middleware to the App.
        $this->add_error_handler_middleware();
    }

    /**
     * Add the Error Handling Middleware to the RouteGroup.
     */
    protected function add_error_handler_middleware(): void {
        // Add the Error Handling Middleware and configure it to show Moodle Errors for HTML pages.
        $errormiddleware = new ErrorMiddleware(
            $this->app->getCallableResolver(),
            $this->app->getResponseFactory(),
            displayErrorDetails: true,
            logErrors: true,
            logErrorDetails: true,
        );

        // Set a custom error handler for the HttpNotFoundException and HttpForbiddenException.
        // We route these to a custom error handler to ensure that the error is displayed with a feedback form.
        $errormiddleware->setErrorHandler(
            [
                HttpNotFoundException::class,
                HttpForbiddenException::class,
            ],
            new router\error_handler($this->app),
        );

        $errormiddleware->getDefaultErrorHandler()->registerErrorRenderer('text/html', router\error_renderer::class);

        $this->app->add($errormiddleware);
    }

    /**
     * Configure the API routes.
     */
    protected function configure_routes(): void {
        $routegroups = $this->routeloader->configure_routes($this->app);
        foreach ($routegroups as $name => $collection) {
            match ($name) {
                route_loader_interface::ROUTE_GROUP_API => $this->configure_api_route($collection),
                route_loader_interface::ROUTE_GROUP_PAGE => $this->configure_standard_route($collection),
                route_loader_interface::ROUTE_GROUP_SHIM => $this->configure_shim_route($collection),
                default => null,
            };
        }
    }

    /**
     * Configure the API Route Middleware.
     *
     * @param RouteGroupInterface $group
     */
    protected function configure_api_route(RouteGroupInterface $group): void {
        $group
            ->add(di::get(error_handling_middleware::class))
            // Add a Middleware to set the CORS headers for all REST Responses.
            ->add(di::get(cors_middleware::class))
            ->add(di::get(moodle_api_authentication_middleware::class))
            ->add(di::get(validation_middleware::class));
    }

    /**
     * Configure the Standard page Route Middleware.
     *
     * @param RouteGroupInterface $group
     */
    protected function configure_standard_route(RouteGroupInterface $group): void {
        $group
            ->add(di::get(moodle_authentication_middleware::class))
            ->add(di::get(validation_middleware::class));
    }

    /**
     * Configure the Shim Route Middleware.
     *
     * @param RouteGroupInterface $group
     */
    protected function configure_shim_route(RouteGroupInterface $group): void {
        $group
            // Note: In the future we may wish to add a shim middleware to notify users of updated bookmarks.
            ->add(di::get(validation_middleware::class));
    }

    /**
     * Configure caching for the routes.
     */
    protected function configure_caching(): void {
        global $CFG;

        // Note: Slim uses a file cache and is not compatible with MUC.
        $this->app->getRouteCollector()->setCacheFile(
            sprintf(
                "%s/routes.%s.cache",
                $CFG->cachedir,
                sha1($this->basepath),
            ),
        );
    }

    /**
     * Handle the specified Request.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle_request(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return $this->get_app()->handle($request);
    }

    /**
     * Serve the current request using global variables.
     *
     * @codeCoverageIgnore
     */
    public function serve(): void {
        $this->get_app()->run();
    }
}
