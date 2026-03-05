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

namespace core\router\parameters;

use core\exception\not_found_exception;
use core\param;
use core\router\schema\example;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait for route parameters that pass a course module identifier, to attach the course module data and context to the request.
 *
 * This code used in {@see query_coursemodule} that can be reused for other parameter types referencing a course module.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait coursemodule_parameter_trait {
    /**
     * Create a new coursemodule parameter.
     *
     * @param string $name The name of the parameter to use for the course identifier
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'coursemodule',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::RAW;
        $extra['description'] = <<<EOF
        The course module identifier.

        This can be the id of the course module or the idnumber of the course module.

        If specifying a course module idnumber, the value should be in the format `idnumber:[idnumber]`.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'A course module id',
                value: 54,
            ),
            new example(
                name: 'A course module specified by its idnumber',
                value: 'idnumber:000117-physics-101-1',
            ),
        ];

        parent::__construct(...$extra);
    }

    /**
     * Get the course module record for the given identifier.
     *
     * @param string $value A course module id or idnumber
     * @return object Course module record.
     * @throws not_found_exception If the course module cannot be found
     */
    protected function get_data_for_value(string $value): mixed {
        global $DB;

        $data = false;

        if (is_numeric($value)) {
            $data = $DB->get_record('course_modules', [
                'id' => $value,
            ]);
        } else if (str_starts_with($value, 'idnumber:')) {
            $data = $DB->get_record('course_modules', [
                'idnumber' => substr($value, strlen('idnumber:')),
            ]);
        }

        if ($data) {
            return $data;
        }

        throw new not_found_exception('coursemodule', $value);
    }

    /**
     * Add course module data and course module context parameters to the request.
     *
     * @param ServerRequestInterface $request
     * @param string $value
     * @return ServerRequestInterface
     */
    public function add_attributes_for_parameter_value(
        ServerRequestInterface $request,
        string $value,
    ): ServerRequestInterface {
        $data = $this->get_data_for_value($value);

        return $request
            ->withAttribute($this->get_value_name('data'), $data)
            ->withAttribute($this->get_value_name('context'), \core\context\module::instance($data->id));
    }

    /**
     * Get the schema for the parameter type, with valid patterns.
     *
     * @param param $type
     * @return \stdClass
     */
    public function get_schema_from_type(param $type): \stdClass {
        $schema = parent::get_schema_from_type($type);

        $schema->pattern = "^(";
        $schema->pattern .= implode("|", [
            '\d+',
            'idnumber:.+',
        ]);
        $schema->pattern .= ")$";

        return $schema;
    }

    /**
     * Get the name of the parameter with the given suffix.
     *
     * @param string $suffix
     * @return string
     */
    protected function get_value_name(string $suffix): string {
        return $this->name . $suffix;
    }
}
