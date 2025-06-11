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

namespace core\router\schema\response\content;

use core\exception\coding_exception;
use core\router\schema\example;
use core\router\schema\openapi_base;
use core\router\schema\objects\type_base;
use core\router\schema\specification;

/**
 * An OpenAPI MediaType.
 * https://swagger.io/specification/#media-type-object
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class media_type extends openapi_base {
    /**
     * Create a new instance of a media_type definition.
     *
     * @param type_base|null $schema The OpenAPI Schema to use
     * @param example|null $example An example of the media type
     * @param example[] $examples An array of examples of the media type
     * @param bool $required Whether the media_type is required
     * @param mixed[] ...$extra
     * @throws coding_exception
     */
    public function __construct(
        /** @var type_base|null The OpenAPI Schema to use */
        protected ?type_base $schema = null,
        /** @var example|null An example of the media type */
        protected ?example $example = null,
        /** @var example[] An array of examples of the media type */
        protected array $examples = [],

        /** @var bool Whether the media_type is required */
        protected bool $required = false,

        ...$extra,
    ) {
        if ($example) {
            if (count($examples)) {
                throw new coding_exception('Only one of example or examples can be specified.');
            }
            $this->examples[$example->get_name()] = $example;
        }

        parent::__construct(...$extra);
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = (object) [];

        if ($this->schema) {
            $data->schema = $this->schema->get_openapi_schema(
                api: $api,
            );
        }

        if (count($this->examples)) {
            $data->examples = [];
            foreach ($this->examples as $example) {
                $data->examples[$example->get_name()] = $example->get_openapi_schema($api);
            }
        }

        if ($this->required) {
            $data->required = true;
        }

        return $data;
    }

    /**
     * Get the schema for this media type.
     *
     * @return type_base
     */
    public function get_schema(): type_base {
        return $this->schema;
    }

    /**
     * Get the mimetype for this media type.
     *
     * @return string
     */
    public function get_mimetype(): string {
        return static::get_encoding();
    }

    /**
     * Get the encoding for this media type.
     *
     * @return string
     */
    abstract public static function get_encoding(): string;


    /**
     * Whether this query parameter is required.
     *
     * @return bool
     */
    public function is_required(): bool {
        return $this->required;
    }
}
