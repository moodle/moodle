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

namespace core_courseformat\local\overview;

/**
 * Tests for course
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_courseformat\local\overview\overviewfactory
 */
final class overviewfactory_test extends \advanced_testcase {
    #[\Override()]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/tests/fixtures/wrongcm_activityoverview.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test create method on resource activities.
     *
     * @covers ::create
     * @dataProvider create_resource_provider
     * @param string $resourcetype
     */
    public function test_create_resource(
        string $resourcetype,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module($resourcetype, ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);

        $this->assertInstanceOf(resourceoverview::class, $overview);
    }

    /**
     * Data provider for test_create_resource.
     *
     * @return array
     */
    public static function create_resource_provider(): array {
        return [
            'book' => [
                'resourcetype' => 'book',
            ],
            'folder' => [
                'resourcetype' => 'folder',
            ],
            'page' => [
                'resourcetype' => 'page',
            ],
            'resource' => [
                'resourcetype' => 'resource',
            ],
            'url' => [
                'resourcetype' => 'url',
            ],
        ];
    }

    /**
     * Test create method on a fake activity with a wrong class.
     *
     * @covers ::create
     */
    public function test_create_exception(
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        // We know the factory will only use the modname to create the overview,
        // this is a small trick to make the factory to use a wrong class and
        // won't happen in a real code. However, this is the easiest way to test
        // the exception.
        $reflection = new \ReflectionClass($cm);
        $property = $reflection->getProperty('modname');
        $property->setAccessible(true);
        $property->setValue($cm, 'wrongcm');

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches("/.* must extend core_courseformat\\\\activityoverviewbase.*/");
        overviewfactory::create($cm);
    }
}
