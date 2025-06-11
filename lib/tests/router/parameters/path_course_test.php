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
use core\router\schema\referenced_object;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use stdClass;

/**
 * Tests for the Course Path paraemter.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\parameter
 * @covers     \core\router\schema\parameters\path_parameter
 * @covers     \core\router\parameters\path_course
 */
final class path_course_test extends route_testcase {
    public function test_course_id(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \core\context\course::instance($course->id);

        $param = new path_course();
        $request = new ServerRequest('GET', '/course/view');
        $newrequest = $param->add_attributes_for_parameter_value($request, $course->id);

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('course'));
        $this->assertInstanceOf(\core\context\course::class, $newrequest->getAttribute('coursecontext'));

        $this->assertEquals($course->id, $newrequest->getAttribute('course')->id);
        $this->assertEquals($coursecontext->id, $newrequest->getAttribute('coursecontext')->id);
    }

    public function test_course_idnumber(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course((object) [
            'idnumber' => '000117-physics-101-1',
        ]);
        $coursecontext = \core\context\course::instance($course->id);

        $param = new path_course();
        $request = new ServerRequest('GET', '/course/view');
        $newrequest = $param->add_attributes_for_parameter_value($request, "idnumber:{$course->idnumber}");

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('course'));
        $this->assertInstanceOf(\core\context\course::class, $newrequest->getAttribute('coursecontext'));

        $this->assertEquals($course->id, $newrequest->getAttribute('course')->id);
        $this->assertEquals($coursecontext->id, $newrequest->getAttribute('coursecontext')->id);
    }

    public function test_course_name(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \core\context\course::instance($course->id);

        $param = new path_course();
        $request = new ServerRequest('GET', '/course/view');
        $newrequest = $param->add_attributes_for_parameter_value($request, "name:{$course->shortname}");

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('course'));
        $this->assertInstanceOf(\core\context\course::class, $newrequest->getAttribute('coursecontext'));

        $this->assertEquals($course->id, $newrequest->getAttribute('course')->id);
        $this->assertEquals($coursecontext->id, $newrequest->getAttribute('coursecontext')->id);
    }

    public function test_validation(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \core\context\course::instance($course->id);

        $request = $this->create_route(
            '/course/view/{course}',
            "/course/view/name:{$course->shortname}",
        );
        $route = $this->get_slim_route_from_request($request);

        $param = new path_course();
        $newrequest = $param->validate($request, $route);

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('course'));
        $this->assertInstanceOf(\core\context\course::class, $newrequest->getAttribute('coursecontext'));

        $this->assertEquals($course->id, $newrequest->getAttribute('course')->id);
        $this->assertEquals($coursecontext->id, $newrequest->getAttribute('coursecontext')->id);
    }

    /**
     * Tests for when a course was not found.
     *
     * @dataProvider invalid_course_provider
     * @param string $searchkey
     */
    public function test_course_not_found(string $searchkey): void {
        $param = new path_course();
        $request = new ServerRequest('GET', '/course/view');

        $this->expectException(not_found_exception::class);
        $param->add_attributes_for_parameter_value($request, $searchkey);
    }

    /**
     * Data provider for test_course_not_found.
     */
    public static function invalid_course_provider(): array {
        return [
            'id' => ['999999'],
            'idnumber' => ['idnumber:999999'],
            'name' => ['name:999999'],
            'random string' => ['ksdjflajsdfkjaf:jkladjg9pomadbs902po3'],
        ];
    }

    public function test_schema(): void {
        $param = new path_course();
        $api = new \core\router\schema\specification();
        $api->add_component($param);
        $result = $param->get_openapi_schema($api);

        // Should be a reference.
        $this->assertInstanceOf(referenced_object::class, $param);

        $schema = $this->get_api_component_schema($api, $param);
        $this->assertIsObject($schema);
        $this->assertIsObject($schema->schema);
        $this->assertObjectHasProperty('pattern', $schema->schema);

        // We do provide some examples here. Make sure they're valid per the regexp.
        $this->assertIsArray($schema->examples);
        foreach ($schema->examples as $example) {
            $this->assertMatchesRegularExpression("/{$schema->schema->pattern}/", $example->value);
        }

        // Some deliberately invalid ones.
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'id');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'id:1');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'id;1');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'idnumber');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'idnumber:');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'idnumber;12344');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'name');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'name:');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'name;12345');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'shortname');
        $this->assertDoesNotMatchRegularExpression("/{$schema->schema->pattern}/", 'shortname:12345');
    }
}
