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
 * Trait for route parameters that pass a course identifier, to attach the course data and context to the request.
 *
 * This holds common code used in {@see path_course} and {@see query_course}.
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait course_parameter_trait {
    /**
     * Create a new course parameter.
     *
     * @param string $name The name of the parameter to use for the course identifier
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'course',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::RAW;
        $extra['description'] = <<<EOF
        The course identifier.

        This can be the id of the course, the idnumber of the course, or the shortname of the course.

        If specifying a course idnumber, the value should be in the format `idnumber:[idnumber]`.

        If specifying a course shortname, the value should be in the format `name:[shortname]`.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'A course id',
                value: 54,
            ),
            new example(
                name: 'A course specified by its idnumber',
                value: 'idnumber:000117-physics-101-1',
            ),
            new example(
                name: 'A course specified by its shortname',
                value: 'name:000117-phys101-0',
            ),
        ];

        parent::__construct(...$extra);
    }

    /**
     * Get the course object for the given identifier.
     *
     * @param string $value A course id, idnumber, or shortname
     * @return object
     * @throws not_found_exception If the course cannot be found
     */
    protected function get_course_for_value(string $value): mixed {
        global $DB;

        $data = false;

        if (is_numeric($value)) {
            $data = $DB->get_record('course', [
                'id' => $value,
            ]);
        } else if (str_starts_with($value, 'idnumber:')) {
            $data = $DB->get_record('course', [
                'idnumber' => substr($value, strlen('idnumber:')),
            ]);
        } else if (str_starts_with($value, 'name:')) {
            $data = $DB->get_record('course', [
                'shortname' => substr($value, strlen('name:')),
            ]);
        }

        if ($data) {
            return $data;
        }

        throw new not_found_exception('course', $value);
    }

    /**
     * Add course and course context parameters to the request.
     *
     * @param ServerRequestInterface $request
     * @param string $value
     * @return ServerRequestInterface
     */
    public function add_attributes_for_parameter_value(
        ServerRequestInterface $request,
        string $value,
    ): ServerRequestInterface {
        $course = $this->get_course_for_value($value);

        return $request
            ->withAttribute($this->name, $course)
            ->withAttribute("{$this->name}context", \core\context\course::instance($course->id));
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
            'name:.+',
        ]);
        $schema->pattern .= ")$";

        return $schema;
    }
}
