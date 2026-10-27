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

use core\param;
use core\router\route;
use core\router\schema\parameters\path_parameter;
use core\router\schema\response\content\payload_response_type;
use core\router\schema\response\response;
use core\router\schema\specification;
use core\tests\router\route_testcase;

/**
 * Tests for the specification.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\specification
 */
final class specification_test extends route_testcase {
    public function test_customised_referenced_object_gets_its_own_component(): void {
        $spec = new specification();
        $default = (new \core\router\parameters\path_user())->get_openapi_schema($spec);
        $renamed = (new \core\router\parameters\path_user(name: 'userId'))->get_openapi_schema($spec);
        $renamedagain = (new \core\router\parameters\path_user(name: 'userIdentifier'))->get_openapi_schema($spec);
        $again = (new \core\router\parameters\path_user(name: 'userId'))->get_openapi_schema($spec);

        $this->assertNotEquals($default->{'$ref'}, $renamed->{'$ref'});
        $this->assertEquals($renamed->{'$ref'}, $again->{'$ref'});
        $this->assertNotEquals($default->{'$ref'}, $renamedagain->{'$ref'});
        $this->assertNotEquals($renamed->{'$ref'}, $renamedagain->{'$ref'});

        $params = $spec->get_schema()->components->parameters;

        $this->assertEquals(
            'user',
            $params->{substr($default->{'$ref'}, strlen('#/components/parameters/'))}->name,
        );
        $this->assertEquals(
            'userId',
            $params->{substr($renamed->{'$ref'}, strlen('#/components/parameters/'))}->name,
        );
        $this->assertEquals(
            'userIdentifier',
            $params->{substr($renamedagain->{'$ref'}, strlen('#/components/parameters/'))}->name,
        );
    }

    public function test_default_reference_is_independent_of_order(): void {
        $first = new specification();
        $renamed = (new \core\router\parameters\path_user(name: 'userId'))->get_openapi_schema($first);
        $default = (new \core\router\parameters\path_user())->get_openapi_schema($first);

        $second = new specification();
        $defaultfirst = (new \core\router\parameters\path_user())->get_openapi_schema($second);

        $this->assertEquals($defaultfirst->{'$ref'}, $default->{'$ref'});
        $this->assertNotEquals($default->{'$ref'}, $renamed->{'$ref'});
    }

    public function test_reference_is_stable_before_and_after_schema(): void {
        $spec = new specification();
        $default = new \core\router\parameters\path_user();
        $renamed = new \core\router\parameters\path_user(name: 'userId');

        $before = $renamed->get_reference();
        $this->assertNotEquals($default->get_reference(), $before);
        $this->assertEquals($before, $renamed->get_openapi_schema($spec)->{'$ref'});
        $this->assertEquals($before, $renamed->get_reference());
        $this->assertEquals(
            $before,
            (new \core\router\parameters\path_user(name: 'userId'))->get_reference(),
        );
    }

    public function test_reference_is_independent_of_path(): void {
        $spec = new specification();
        $renamed = new \core\router\parameters\path_user(name: 'userId');

        $this->assertEquals(
            $renamed->get_openapi_schema($spec, '/a')->{'$ref'},
            $renamed->get_openapi_schema($spec, '/b')->{'$ref'},
        );
    }

    public function test_reference_is_independent_of_nested_reference_calculation(): void {
        $make = fn (): \core\router\schema\parameter => new \core\router\schema\parameter(
            name: 'a',
            in: 'query',
            example: new \core\router\schema\example(name: 'x', value: 'y'),
        );

        $parent = $make();
        $reference = $parent->get_reference();

        $childfirst = $make();
        $reflection = new \ReflectionProperty($childfirst, 'example');
        $reflection->getValue($childfirst)->get_reference();

        $this->assertEquals($reference, $childfirst->get_reference());
    }

    public function test_reference_differs_for_different_closures(): void {
        $one = new \core\router\schema\example(name: 'x', value: fn () => 1);
        $two = new \core\router\schema\example(name: 'x', value: fn () => 2);

        $this->assertNotEquals($one->get_reference(), $two->get_reference());
    }

    public function test_basics(): void {
        global $CFG;

        $spec = new specification();
        $schema = $spec->get_schema();

        $this->assertIsObject($schema);

        // We comply with OpenAPI 3.1.0.
        $this->assertObjectHasProperty('openapi', $schema);
        $this->assertEquals('3.1.0', $schema->openapi);

        // INfo should include our license.
        $this->assertObjectHasProperty('info', $schema);
        $this->assertObjectHasProperty('license', $schema->info);
        $this->assertStringContainsString('GNU GPL v3 or later', $schema->info->license->name);
        $this->assertObjectHasProperty('url', $schema->info->license);

        // The server list should contain the currenet URI during finalisation.
        $this->assertObjectHasProperty('servers', $schema);
        $this->assertIsArray($schema->servers);
        $this->assertCount(1, $schema->servers);
        $server = $schema->servers[0];
        $this->assertStringStartsWith($CFG->wwwroot, $server->url);

        $this->assertObjectHasProperty('paths', $schema);
        $this->assertObjectHasProperty('components', $schema);
        $this->assertObjectHasProperty('security', $schema);
        $this->assertObjectHasProperty('externalDocs', $schema);

        // Calculated parameters should only be set once.
        $schema = $spec->get_schema();
        $this->assertCount(1, $schema->servers);

        $this->assertJson(json_encode($spec));
    }

    /**
     * Test the add_path method.
     *
     * @dataProvider add_path_provider
     * @param string $component
     * @param string $path
     * @param string $expectedpath
     */
    public function test_add_path(
        string $component,
        string $path,
        string $expectedpath,
    ): void {
        $spec = new specification();

        $spec->add_path(
            $component,
            new route(
                path: $path,
            ),
        );

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($expectedpath, $schema->paths);
    }

    /**
     * Data provider for add_path.
     *
     * @return array
     */
    public static function add_path_provider(): array {
        return [
            'Core' => [
                'core',
                '/example/path',
                '/core/example/path',
            ],
            'Core Subsystem' => [
                'core_access',
                '/example/path',
                '/access/example/path',
            ],
            'An activity' => [
                'mod_assign',
                '/example/path',
                '/mod_assign/example/path',
            ],
        ];
    }

    public function test_add_path_with_option(): void {
        $spec = new specification();

        $spec->add_path(
            'core',
            new route(
                path: '/example/path/with[/{option}]',
                pathtypes: [
                    new path_parameter(name: 'option', type: param::INT),
                ],
            ),
        );

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty('/core/example/path/with', $schema->paths);
        $this->assertObjectHasProperty('/core/example/path/with/{option}', $schema->paths);
    }

    /**
     * Test add_path with optional parameters.
     *
     * @param string $component
     * @param string $path
     * @param array $expectedpaths
     * @dataProvider add_path_with_options_provider
     */
    public function test_add_path_with_options(
        string $component,
        string $path,
        array $expectedpaths,
    ): void {
        $spec = new specification();

        $spec->add_path(
            $component,
            new route(
                path: $path,
                pathtypes: [
                    new path_parameter(name: 'optional', type: param::INT),
                    new path_parameter(name: 'extras', type: param::INT),
                ],
            ),
        );

        $schema = $spec->get_schema();
        foreach ($expectedpaths as $expectedpath) {
            $this->assertObjectHasProperty($expectedpath, $schema->paths);
        }
    }

    /**
     * Data provider for add_path with optional parameters.
     *
     * @return \Iterator
     */
    public static function add_path_with_options_provider(): \Iterator {
        yield 'With options' => [
            'component' => 'core',
            'path' => '/example/path/with[/{optional}][/{extras}]',
            'expectedpaths' => [
                '/core/example/path/with',
                '/core/example/path/with/{optional}',
                '/core/example/path/with/{optional}/{extras}',
            ],
        ];
        yield 'With greedy unlimited options' => [
            'component' => 'core',
            'path' => '/example/path/with[/{optional}][/{extras:.*}]',
            'expectedpaths' => [
                '/core/example/path/with',
                '/core/example/path/with/{optional}',
                '/core/example/path/with/{optional}/{extras}',
            ],
        ];
        yield 'With non-greedy unlimited options' => [
            'component' => 'core',
            'path' => '/example/path/with[/{optional}][/{extras:.*?}]',
            'expectedpaths' => [
                '/core/example/path/with',
                '/core/example/path/with/{optional}',
                '/core/example/path/with/{optional}/{extras}',
            ],
        ];
    }

    public function test_add_parameter(): void {
        $spec = new specification();

        /** @var path_parameter&\PHPUnit\Framework\MockObject\MockObject $child */
        $child = $this->getMockBuilder(path_parameter::class)
            ->onlyMethods([])
            ->setConstructorArgs([
                'name' => 'example',
                'type' => param::INT,
            ])
            ->getMock();

        $this->assertFalse($spec->is_reference_defined($child->get_reference(true)));

        $spec->add_component($child);

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($child->get_reference(false), $schema->components->parameters);
        $this->assertTrue($spec->is_reference_defined($child->get_reference(true)));
    }

    public function test_add_header(): void {
        $spec = new specification();

        /** @var header_object&\PHPUnit\Framework\MockObject\MockObject $child */
        $child = $this->getMockBuilder(header_object::class)
            ->onlyMethods([])
            ->setConstructorArgs([
                'name' => 'example',
                'type' => param::INT,
            ])
            ->getMock();

        $this->assertFalse($spec->is_reference_defined($child->get_reference(true)));

        $spec->add_component($child);

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($child->get_reference(false), $schema->components->headers);
        $this->assertTrue($spec->is_reference_defined($child->get_reference(true)));
    }

    public function test_add_response(): void {
        $spec = new specification();

        /** @var response&\PHPUnit\Framework\MockObject\MockObject $child */
        $child = $this->getMockBuilder(response::class)
            ->onlyMethods([])
            ->setConstructorArgs([
            ])
            ->getMock();

        $this->assertFalse($spec->is_reference_defined($child->get_reference(true)));

        $spec->add_component($child);

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($child->get_reference(false), $schema->components->responses);
        $this->assertTrue($spec->is_reference_defined($child->get_reference(true)));
    }

    public function test_add_example(): void {
        $spec = new specification();

        /** @var example&\PHPUnit\Framework\MockObject\MockObject $child */
        $child = $this->getMockBuilder(example::class)
            ->onlyMethods([])
            ->setConstructorArgs([
                'name' => 'An excellent example',
            ])
            ->getMock();
        $this->assertFalse($spec->is_reference_defined($child->get_reference(true)));

        $spec->add_component($child);

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($child->get_reference(false), $schema->components->examples);
        $this->assertTrue($spec->is_reference_defined($child->get_reference(true)));
    }

    public function test_add_request_body(): void {
        $spec = new specification();

        $child = new request_body(
            description: 'example',
            required: true,
        );

        $this->assertFalse($spec->is_reference_defined($child->get_reference(true)));

        $spec->add_component($child);

        $schema = $spec->get_schema();
        $this->assertObjectHasProperty($child->get_reference(false), $schema->components->requestBodies);

        $this->assertTrue($spec->is_reference_defined($child->get_reference(true)));

        $requestschema = $spec->get_openapi_schema_for_route(
            route: new route(
                path: '/example/path',
                requestbody: $child,
            ),
            component: '',
            path: '/example/path',
        );

        $this->assertObjectHasProperty('requestBody', $requestschema->get);
        $this->assertEquals('example', $requestschema->get->requestBody->description);
        $this->assertTrue($requestschema->get->requestBody->required);
    }

    public function test_route_security_in_schema(): void {
        $spec = new specification();

        $route = new route(
            path: '/example/path',
            security: ['example'],
        );

        $spec->add_path(
            'core',
            $route,
        );

        $requestschema = $spec->get_openapi_schema_for_route(
            route: $route,
            component: '',
            path: '/example/path',
        );

        $this->assertObjectHasProperty('security', $requestschema->get);
        $this->assertEquals(['example'], $requestschema->get->security);
    }

    public function test_is_reference_defined(): void {
        $spec = new specification();
        $this->assertFalse($spec->is_reference_defined('example'));
        $this->assertFalse($spec->is_reference_defined('#/components/fake/component'));
    }

    public function test_deprecated_route(): void {
        $spec = new specification();
        $route = new route(
            path: '/example/path',
            deprecated: true,
        );

        $spec->add_path(
            'core',
            $route,
        );

        $requestschema = $spec->get_openapi_schema_for_route(
            route: $route,
            component: '',
            path: '/example/path',
        );

        $this->assertObjectHasProperty('deprecated', $requestschema->get);
        $this->assertTrue($requestschema->get->deprecated);
    }

    public function test_response(): void {
        $spec = new specification();
        $route = new route(
            path: '/example/path',
            responses: [
                new response(
                    status: 200,
                    description: 'example',
                    content: new payload_response_type(),
                ),
            ],
        );

        $spec->add_path(
            'core',
            $route,
        );

        $requestschema = $spec->get_openapi_schema_for_route(
            route: $route,
            component: '',
            path: '/example/path',
        );

        $this->assertObjectHasProperty('responses', $requestschema->get);
        $this->assertArrayHasKey('200', $requestschema->get->responses);
        $this->assertObjectHasProperty('content', $requestschema->get->responses['200']);
        $this->assertObjectHasProperty('application/json', $requestschema->get->responses['200']->content);
    }
}
