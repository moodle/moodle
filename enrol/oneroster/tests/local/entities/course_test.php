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

/**
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\entities;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/entity_testcase.php');
use enrol_oneroster\local\entities\entity_testcase;

use stdClass;
use OutOfRangeException;

/**
 * One Roster tests for the course entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  enrol_oneroster\local\entity
 * @covers  enrol_oneroster\local\entities\course
 */
class course_testcase extends entity_testcase {

    /**
     * Test the properties of the entity.
     */
    public function test_entity(): void {
        $container = $this->get_mocked_container();

        $entity = new course($container, '12345');
        $this->assertInstanceOf(course::class, $entity);
    }

    /**
     * Ensure that preloading of entity data means that endpoint is not called.
     */
    public function test_preload(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering
            ->expects($this->never())
            ->method('execute');
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $entity = new course($container, '12345', (object) [
            'sourcedId' => 'preloadedObject'
        ]);

        // The get_data() function should contain the data.
        $data = $entity->get_data();
        $this->assertIsObject($data);
        $this->assertEquals('preloadedObject', $data->sourcedId);

        // And it can be retrieved via `get().
        $this->assertEquals('preloadedObject', $entity->get('sourcedId'));

        // Non-existent objects return null.
        $this->assertNull($entity->get('fake'));
    }

    /**
     * Ensure that the get function calls the web service correctly.
     */
    public function test_get(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering
            ->expects($this->once())
            ->method('execute')
            ->willReturn((object) [
                'course' => (object) [
                    'sourcedId' => '12345',
                    'name' => 'Example courseanisation',
                ],
            ]);
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $entity = new course($container, '12345');

        // The get_data() function should contain the data.
        $data = $entity->get_data();
        $this->assertIsObject($data);
        $this->assertEquals('12345', $data->sourcedId);
        $this->assertEquals('Example courseanisation', $data->name);

        // And it can be retrieved via `get()` without incurring another fetch.
        $this->assertEquals('12345', $entity->get('sourcedId'));
        $this->assertEquals('Example courseanisation', $entity->get('name'));

        // Non-existent objects return null.
        $this->assertNull($entity->get('fake'));
    }

    /**
     * An OutOfRangeException exception should be thrown when the data does not contain an 'course' attribute.
     */
    public function test_get_missing_structure(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering->method('execute')->willReturn((object) [
            'sourcedId' => 'foo',
        ]);
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $this->expectException(OutOfRangeException::class);

        $entity = new course($container, '12345');
        $data = $entity->get_data();
    }

    /**
     * Ensure that the get_org function fetches the parent where one is defined.
     *
     * @dataProvider parent_provider
     * @param   stdClass $parentdata
     */
    public function test_get_org_defined(stdClass $parentdata): void {
        $container = $this->get_mocked_container();

        $entity = new course($container, 'example', (object) [
            'sourcedId' => 'example',
            'org' => $parentdata,
        ]);

        $entityfactory = $this->mock_entity_factory($container, [
            'fetch_org_by_id',
        ]);

        $parentorg = new org($container, $parentdata->sourcedId, $parentdata);
        $container->method('get_entity_factory')->willReturn($entityfactory);

        $entityfactory
            ->expects($this->once())
            ->method('fetch_org_by_id')
            ->with($this->equalTo($parentdata->sourcedId))
            ->willReturn($parentorg);

        $this->assertEquals($parentorg, $entity->get_org());
    }

    /**
     * Data providet for get_org tests where a parent is defined.
     *
     * @return  array
     */
    public function parent_provider(): array {
        return [
            [(object) ['sourcedId' => 'other example']],
            [(object) ['sourcedId' => '0']],
            [(object) ['sourcedId' => 0]],
        ];
    }
}
