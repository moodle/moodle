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

use core\param;
use core\router\schema\example;
use core\router\schema\referenced_object;
use core\router\schema\specification;

/**
 * A standard response for user preferences.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stacktrace extends type_base implements referenced_object {
    /** @var array The stacks in the trace */
    protected array $content;

    /**
     * Constructor for a new stacktrace object.
     */
    public function __construct() {
        $this->content = [
            'file' => new scalar_type(param::PATH),
            'line' => new scalar_type(param::INT),
            'function' => new scalar_type(param::RAW),
            'args' => new array_of_things(),
            'class' => new scalar_type(param::RAW),
            'type' => new scalar_type(param::RAW),
        ];

        $pathroot = '/Users/example/Sites/moodle';
        parent::__construct(
            examples: [
                new example(
                    name: 'A sample stacktrace',
                    value: [
                        [
                            "file" => "{$pathroot}/lib/classes/router/schema/objects/array_of_strings.php",
                            "line" => 48,
                            "function" => "validate_param",
                            "args" => [
                                "string",
                                "int",
                                false,
                                "The value 'string' was not of type string.",
                            ],
                        ],
                        [
                            "file" => "{$pathroot}/lib/classes/router/schema/objects/schema_object.php",
                            "line" => 85,
                            "function" => "validate_data",
                            "class" => "core\\router\\schema\\objects\\array_of_strings",
                            "type" => "->",
                            "args" => [
                                [
                                    "additionalProp1" => "string",
                                    "additionalProp2" => "string",
                                    "additionalProp3" => "string",
                                ],
                            ],
                        ],
                        [
                            "file" => "{$pathroot}/lib/classes/router/route.php",
                            "line" => 264,
                            "function" => "validate_data",
                            "class" => "core\\router\\schema\\objects\\schema_object",
                            "type" => "->",
                            "args" => [
                                [
                                    "preferences" => [
                                        "additionalProp1" => "string",
                                        "additionalProp2" => "string",
                                        "additionalProp3" => "string",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ),
            ],
        );
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $additionalproperties = new \stdClass();

        foreach ($this->content as $name => $content) {
            $additionalproperties->{$name} = $content->get_openapi_description($api, $path);
        }

        $data = parent::get_openapi_description($api, $path);
        $data->type = 'array';
        $data->items = (object) [
            'type' => 'object',
            'properties' => $additionalproperties,
        ];

        return $data;
    }

    #[\Override]
    public function validate_data($data) {
        // Do not validate the data at all.
        // Stacktraces tend to be used with exceptions and we want whatever was passed through to come out.
        return $data;
    }
}
