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

namespace core\router\schema;

use core\exception\coding_exception;
use core\param;
use core\router\route;
use core\router\schema\objects\type_base;
use stdClass;

/**
 * OpenAPI parameter.
 *
 * https://spec.openapis.org/oas/v3.1.0#parameter-object
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class parameter extends openapi_base {
    /** @var string A query parameter */
    public const IN_QUERY = 'query';

    /** @var string A header parameter */
    public const IN_HEADER = 'header';

    /** @var string A URI path parameter */
    public const IN_PATH = 'path';

    /** @var string A cookie parameter */
    public const IN_COOKIE = 'cookie';

    /**
     * Constructor for a Parameter Object.
     *
     * @param string $name The name of the parameter. Parameter names are case sensitive.
     * - If in is "path", the name field MUST correspond to a template expression occurring within the
     *   path field in the Paths Object.
     *   See Path Templating for further information.
     * - If in is "header" and the name field is "Accept", "Content-Type" or "Authorization",
     *   the parameter definition SHALL be ignored.
     * - For all other cases, the name corresponds to the parameter name used by the in property.
     * @param string $in The location of the parameter. Possible values are "query", "header", "path" or "cookie".
     * @param null|string $description
     * @param null|bool $required
     * @param null|bool $deprecated Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     * @param null|param $type A Moodle parameter type, which can be used instead of a schema.
     * @param mixed $default The default value
     * @param null|type_base $schema
     * @param null|example $example
     * @param example[] $examples
     * @param mixed[] ...$extra
     * @throws coding_exception
     */
    public function __construct(
        /** @var string The name of the parameter. Parameter names are case sensitive */
        protected string $name,
        /** @var string The location of the parameter */
        protected string $in,
        /** @var string|null A description of the parameter */
        protected ?string $description = null,
        /** @var bool|null Whether the parameter is required */
        protected ?bool $required = null,
        /** @var bool|null Whether the parameter is deprecated */
        protected ?bool $deprecated = false,
        /** @var param|null A Moodle parameter type */
        protected ?param $type = null,
        /** @var mixed|null The default value of the parameter */
        protected mixed $default = null,
        /** @var type_base|null The schema */
        protected ?type_base $schema = null,
        /** @var example|null An example */
        protected ?example $example = null,
        /** @var example[] An array of examples */
        protected array $examples = [],
        ...$extra,
    ) {
        if ($example) {
            if (count($examples)) {
                throw new coding_exception('Only one of example or examples can be specified.');
            }
            $this->examples[$example->get_name()] = $example;
        }

        if ($required === true && $default !== null) {
            throw new coding_exception('A parameter cannot be required and have a default value.');
        }

        parent::__construct(...$extra);
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?stdClass {
        $data = (object) [
            // The `name`, and `in` values are required.
            'name' => $this->name,
            'in' => $this->in,
        ];

        if ($this->description !== null) {
            $data->description = $this->description;
        }

        // Allow another schema to be passed.
        if ($this->schema !== null) {
            $data->schema = $this->schema->get_openapi_schema($api, $path);
        } else {
            $data->schema = $this->get_schema_from_type($this->type);
        }

        if (count($this->examples) > 0) {
            $data->examples = [];
            foreach ($this->examples as $example) {
                $data->examples[$example->get_name()] = $example->get_openapi_schema(
                    api: $api,
                );
            }
        }

        return $data;
    }

    /**
     * Get the OpenAPI 'in' property.
     *
     * @return string
     */
    public function get_in(): string {
        return $this->in;
    }

    /**
     * Fetch the underlying param.
     *
     * @return param
     */
    public function get_type(): param {
        return $this->type;
    }

    /**
     * Whether this property is required.
     *
     * @param route $route
     * @return bool
     */
    public function is_required(route $route): bool {
        return $this->required ?? false;
    }

    /**
     * Get the name of the parameter.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }
}
