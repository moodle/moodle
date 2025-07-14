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

use core\exception\coding_exception;
use core\router\schema\parameter;
use core\router\schema\response\response;
use core\router\schema\request_body;
use Attribute;

/**
 * Routing attribute.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class route {
    /** @var string[] The list of HTTP Methods */
    protected null|array $method = null;

    /**
     * The parent route, if relevant.
     *
     * A method-level route may have a class-level route as a parent. The two are combined to provide
     * a fully-qualified path.
     *
     * @var route|null
     */
    protected readonly ?route $parentroute;

    /**
     * Constructor for a new Moodle route.
     *
     * @param string $title A title to briefly describe the route (not translated)
     * @param string $description A verbose explanation of the operation behavior (not translated)
     * @param string $summary A short summary of what the operation does (not translated)
     * @param null|string[] $security A list of security mechanisms
     * @param null|string $path The path to match
     * @param null|array|string $method The method, or methods, supported
     * @param parameter[] $pathtypes Validators for the path arguments
     * @param parameter[] $queryparams Validators for the path arguments
     * @param parameter[] $headerparams Validators for the path arguments
     * @param request_body|null $requestbody Validators for the path arguments
     * @param response[] $responses A list of possible response types
     * @param bool $deprecated Whether this endpoint is deprecated
     * @param string[] $tags A list of tags
     * @param bool $cookies Whether this request requires cookies
     * @param bool $abortafterconfig Whether to abort after configuration
     * @param mixed[] ...$extra Any additional arguments not yet supported in this version of Moodle
     * @throws coding_exception
     */
    public function __construct(
        /** @var string A title to briefly describe the route (not translated) */
        public readonly string $title = '',

        /** @var string A verbose explanation of the operation behavior (not translated) */
        public readonly string $description = '',

        /** @var string A short summary of what the operation does (not translated) */
        public readonly string $summary = '',

        /** @var array<string> A list of security mechanisms */
        public readonly ?array $security = null,

        /**
         * The path to the route.
         *
         * This is relative to the parent route, if one exists.
         * A route must be set on one, or both, of the class and method level routes.
         *
         * @var string|null
         */
        public ?string $path = null,

        null|array|string $method = null,

        /** @var parameter[] A list of param types for path arguments */
        protected readonly array $pathtypes = [],

        /** @var parameter[] A list of query parameters with matching types */
        protected readonly array $queryparams = [],

        /** @var parameter[] A list of header parameters */
        protected readonly array $headerparams = [],

        /** @var null|request_body A list of parameters found in the body */
        public readonly ?request_body $requestbody = null,

        /** @var response[] A list of possible response types */
        protected readonly array $responses = [],

        /** @var bool Whether this endpoint is deprecated */
        public readonly bool $deprecated = false,

        /** @var string[] A list of tags */
        public readonly array $tags = [],

        /** @var bool Whether this request may use cookies */
        public readonly bool $cookies = true,

        /** @var bool Whether to abort after configuration */
        public readonly bool $abortafterconfig = false,

        /** @var null|array Whether to require login or not */
        public readonly ?require_login $requirelogin = null,

        /** @var string[] The list of scopes required to access this page */
        public readonly ?array $scopes = null,

        // Note. We do not make use of these extras.
        // These allow us to add additional arguments in future versions, whilst allowing plugins to use this version.
        ...$extra,
    ) {
        // Normalise the method.
        if (is_string($method)) {
            $method = [$method];
        }
        $this->method = $method;

        // Validate the query parameters.
        if (count(array_filter($this->queryparams, fn($pathtype) => !is_a($pathtype, parameter::class)))) {
            throw new coding_exception('All query parameters must be an instance of \core\router\parameter.');
        }
        if (count(array_filter($this->queryparams, fn($pathtype) => $pathtype->get_in() !== 'query'))) {
            throw new coding_exception('All query parameters must be in the query.');
        }

        // Validate the path parameters.
        if (count(array_filter($this->pathtypes, fn($pathtype) => !is_a($pathtype, parameter::class)))) {
            throw new coding_exception('All path parameters must be an instance of \core\router\parameter.');
        }
        if (count(array_filter($this->pathtypes, fn($pathtype) => $pathtype->get_in() !== 'path'))) {
            throw new coding_exception('All path properties must be in the path.');
        }

        // Validate the header parameters.
        if (count(array_filter($this->headerparams, fn($pathtype) => !is_a($pathtype, parameter::class)))) {
            throw new coding_exception('All path parameters must be an instance of \core\router\parameter.');
        }
        if (count(array_filter($this->headerparams, fn($pathtype) => $pathtype->get_in() !== 'header'))) {
            throw new coding_exception('All header properties must be in the path.');
        }
    }

    /**
     * Set the parent route, usually a Class-level route.
     *
     * @param route $parent
     * @return self
     */
    public function set_parent(route $parent): self {
        $this->parentroute = $parent;
        return $this;
    }

    /**
     * Get the fully-qualified path for this route relative to root.
     *
     * This includes the path of any parent route.
     *
     * @return string
     */
    public function get_path(): string {
        $path = $this->path ?? '';

        if (isset($this->parentroute)) {
            $path = $this->parentroute->get_path() . $path;
        }
        return $path;
    }

    /**
     * Get the list of HTTP methods associated with this route.
     *
     * @param null|string[] $default The default methods to use if none are set
     * @return null|string[]
     */
    public function get_methods(?array $default = null): ?array {
        $methods = $this->method;

        if (isset($this->parentroute)) {
            $parentmethods = $this->parentroute->get_methods();
            if ($methods) {
                $methods = array_unique(
                    array_merge($parentmethods ?? [], $methods),
                );
            } else {
                $methods = $parentmethods;
            }
        }

        // If there are no methods from either this attribute or any parent, use the default.
        $methods = $methods ?? $default;

        if ($methods) {
            sort($methods);
        }

        return $methods;
    }

    /**
     * Get the list of path parameters, including any from the parent.
     *
     * @return array
     */
    public function get_path_parameters(): array {
        $parameters = [];

        if (isset($this->parentroute)) {
            $parameters = $this->parentroute->get_path_parameters();
        }
        foreach ($this->pathtypes as $parameter) {
            $parameters[$parameter->get_name()] = $parameter;
        }

        return $parameters;
    }

    /**
     * Get the list of path parameters, including any from the parent.
     *
     * @return array
     */
    public function get_header_parameters(): array {
        $parameters = [];

        if (isset($this->parentroute)) {
            $parameters = $this->parentroute->get_header_parameters();
        }
        foreach ($this->headerparams as $parameter) {
            $parameters[$parameter->get_name()] = $parameter;
        }

        return $parameters;
    }

    /**
     * Get the list of path parameters, including any from the parent.
     *
     * @return array
     */
    public function get_query_parameters(): array {
        $parameters = [];

        if (isset($this->parentroute)) {
            $parameters = $this->parentroute->get_query_parameters();
        }
        foreach ($this->queryparams as $parameter) {
            $parameters[$parameter->get_name()] = $parameter;
        }

        return $parameters;
    }

    /**
     * Get the request body for this route.
     *
     * @return request_body|null
     */
    public function get_request_body(): ?request_body {
        return $this->requestbody;
    }

    /**
     * Whether this route expects a request body.
     *
     * @return bool
     */
    public function has_request_body(): bool {
        return $this->requestbody !== null;
    }

    /**
     * Get all responses.
     *
     * @return response[]
     */
    public function get_responses(): array {
        return $this->responses;
    }

    /**
     * Get the response with the specified response code.
     *
     * @param int $statuscode
     * @return response|null
     */
    public function get_response_with_status_code(int $statuscode): ?response {
        foreach ($this->get_responses() as $response) {
            if ($response->get_status_code() === $statuscode) {
                return $response;
            }
        }

        return null;
    }

    /**
     * Whether this route expects any validatable parameters.
     * That is, any parameter in the path, query params, or the request body.
     *
     * @return bool
     */
    public function has_any_validatable_parameter(): bool {
        return count($this->get_path_parameters()) || count($this->get_query_parameters()) || $this->has_request_body();
    }
}
