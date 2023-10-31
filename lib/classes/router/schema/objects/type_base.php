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

namespace core\router\schema\objects;

use core\router\schema\openapi_base;
use core\router\schema\specification;

/**
 * Part of the OpenAPI Schema.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class type_base extends openapi_base {
    /**
     * Note: We do not implement the $example, because it has been deprecated in OpenApi 3.0.
     *
     * @param array $examples
     * @param mixed[] ...$extra
     */
    public function __construct(
        /** @var array Any examples that may be present for the type */
        protected array $examples = [],
        ...$extra,
    ) {
        parent::__construct(...$extra);
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = (object) [];

        if (count($this->examples)) {
            $data->examples = [];
            foreach ($this->examples as $example) {
                $data->examples[] = $example->get_openapi_schema(
                    api: $api,
                );
            }
        }

        return $data;
    }

    /**
     * Get the additional OpenAPI properties if relevant.
     *
     * @param string|null|type_base $type
     * @return bool|array
     */
    protected function get_additional_properties(string|null|type_base $type): bool|array {
        // The additionalProperties are described here:
        // https://spec.openapis.org/oas/v3.1.0#schema-object-examples.
        if ($type === null) {
            return true;
        }

        if (is_a($type, self::class)) {
            // This type is a reference to another schema object.
            return [
                '$ref' => $type->get_reference(),
            ];
        }

        // TODO MDL-82243: Validate against supported OpenAPI types.
        return [
            'type' => $type,
        ];
    }

    /**
     * Validate the data against the type.
     *
     * @param mixed $data
     */
    abstract public function validate_data(mixed $data);
}
