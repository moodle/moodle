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
 * Tests for resource overview
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_courseformat\local\overview\resourceoverview
 */
final class resourceoverview_test extends \advanced_testcase {
    /**
     * Test get_extra_overview_items method.
     *
     * @covers ::get_extra_overview_items
     */
    public function test_get_extra_overview_items(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module('url', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);

        $result = $overview->get_extra_overview_items();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertInstanceOf(\core_courseformat\local\overview\overviewitem::class, $result['type']);
    }

    /**
     * Test get_extra_type_overview method.
     *
     * @covers ::get_extra_overview_items
     * @covers ::get_extra_type_overview
     * @dataProvider get_extra_type_overview_provider
     * @param string $resourcetype
     * @param string|null $expected
     */
    public function test_get_extra_type_overview(
        string $resourcetype,
        ?string $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module($resourcetype, ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);

        $items = $overview->get_extra_overview_items();
        $result = $items['type'];

        if ($expected === null) {
            $this->assertNull($result);
            return;
        }

        $this->assertEquals(get_string('resource_type'), $result->get_name());
        $this->assertEquals($expected, $result->get_value());
        $this->assertEquals($expected, $result->get_content());
    }

    /**
     * Data provider for test_get_extra_type_overview.
     *
     * @return array
     */
    public static function get_extra_type_overview_provider(): array {
        return [
            'book' => [
                'resourcetype' => 'book',
                'expected' => 'Book',
            ],
            'folder' => [
                'resourcetype' => 'folder',
                'expected' => 'Folder',
            ],
            'page' => [
                'resourcetype' => 'page',
                'expected' => 'Page',
            ],
            'resource' => [
                'resourcetype' => 'resource',
                'expected' => 'File',
            ],
            'url' => [
                'resourcetype' => 'url',
                'expected' => 'URL',
            ],
            // Activities without integration.
            'bigbluebuttonbn' => [
                'resourcetype' => 'bigbluebuttonbn',
                'expected' => null,
            ],
            'choice' => [
                'resourcetype' => 'choice',
                'expected' => null,
            ],
            'data' => [
                'resourcetype' => 'data',
                'expected' => null,
            ],
            'forum' => [
                'resourcetype' => 'forum',
                'expected' => null,
            ],
            'glossary' => [
                'resourcetype' => 'glossary',
                'expected' => null,
            ],
            'h5pactivity' => [
                'resourcetype' => 'h5pactivity',
                'expected' => null,
            ],
            'lesson' => [
                'resourcetype' => 'lesson',
                'expected' => null,
            ],
            'lti' => [
                'resourcetype' => 'lti',
                'expected' => null,
            ],
            'qbank' => [
                'resourcetype' => 'qbank',
                'expected' => null,
            ],
            'quiz' => [
                'resourcetype' => 'quiz',
                'expected' => null,
            ],
            'scorm' => [
                'resourcetype' => 'scorm',
                'expected' => null,
            ],
            'wiki' => [
                'resourcetype' => 'wiki',
                'expected' => null,
            ],
        ];
    }
}
