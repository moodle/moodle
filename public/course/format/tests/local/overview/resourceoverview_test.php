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
 */
#[\PHPUnit\Framework\Attributes\CoversClass(resourceoverview::class)]
final class resourceoverview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
     *
     * @param string $role The role of the user to test.
     * @param string $resourcetype The type of resource to create.
     * @param array|null $expected Expected overview item data.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_actions_overview')]
    public function test_get_actions_overview(
        string $role,
        string $resourcetype,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $resource = $this->getDataGenerator()->create_module($resourcetype, ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($resource->cmid);

        $this->setUser($currentuser);
        $cm = get_fast_modinfo($course)->get_cm($resource->cmid);
        $item = overviewfactory::create($cm)->get_actions_overview();

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals($expected['name'], $item->get_name());
        $this->assertEquals($expected['value'], $item->get_value());
        $this->assertStringContainsString($expected['content'], $item->get_content()->text);
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_actions_overview(): \Generator {
        yield 'Student' => [
            'role' => 'student',
            'resourcetype' => 'url',
            'expected' => null,
        ];
        yield 'Teacher - Book' => [
            'role' => 'editingteacher',
            'resourcetype' => 'book',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher - Folder' => [
            'role' => 'editingteacher',
            'resourcetype' => 'folder',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher - Page' => [
            'role' => 'editingteacher',
            'resourcetype' => 'page',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher - Resource' => [
            'role' => 'editingteacher',
            'resourcetype' => 'resource',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher - URL' => [
            'role' => 'editingteacher',
            'resourcetype' => 'url',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher - Non resource' => [
            'role' => 'editingteacher',
            'resourcetype' => 'lti',
            'expected' => null,
        ];
    }

    /**
     * Test get_extra_overview_items method.
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

        $activity = $this->getDataGenerator()->create_module('lti', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);

        $result = $overview->get_extra_overview_items();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertNull($result['type']);
    }

    /**
     * Test get_extra_type_overview method.
     *
     * @param string $resourcetype
     * @param string|null $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_type_overview_provider')]
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

        if ($expected === null) {
            $this->assertTrue(!array_key_exists('type', $items) || $items['type'] === null);
            return;
        }

        $result = $items['type'];
        $this->assertEquals(get_string('resource_type'), $result->get_name());
        $this->assertEquals($expected, $result->get_value());
        $this->assertEquals($expected, $result->get_content());
    }

    /**
     * Data provider for test_get_extra_type_overview.
     *
     * @return \Generator
     */
    public static function get_extra_type_overview_provider(): \Generator {
        yield 'book' => [
            'resourcetype' => 'book',
            'expected' => 'Book',
        ];
        yield 'folder' => [
            'resourcetype' => 'folder',
            'expected' => 'Folder',
        ];
        yield 'page' => [
            'resourcetype' => 'page',
            'expected' => 'Page',
        ];
        yield 'resource' => [
            'resourcetype' => 'resource',
            'expected' => 'File',
        ];
        yield 'url' => [
            'resourcetype' => 'url',
            'expected' => 'URL',
        ];
        // Non-resource activities.
        yield 'bigbluebuttonbn' => [
            'resourcetype' => 'bigbluebuttonbn',
            'expected' => null,
        ];
        yield 'choice' => [
            'resourcetype' => 'choice',
            'expected' => null,
        ];
        yield 'data' => [
            'resourcetype' => 'data',
            'expected' => null,
        ];
        yield 'forum' => [
            'resourcetype' => 'forum',
            'expected' => null,
        ];
        yield 'glossary' => [
            'resourcetype' => 'glossary',
            'expected' => null,
        ];
        yield 'h5pactivity' => [
            'resourcetype' => 'h5pactivity',
            'expected' => null,
        ];
        yield 'lesson' => [
            'resourcetype' => 'lesson',
            'expected' => null,
        ];
        yield 'lti' => [
            'resourcetype' => 'lti',
            'expected' => null,
        ];
        yield 'qbank' => [
            'resourcetype' => 'qbank',
            'expected' => null,
        ];
        yield 'quiz' => [
            'resourcetype' => 'quiz',
            'expected' => null,
        ];
        yield 'scorm' => [
            'resourcetype' => 'scorm',
            'expected' => null,
        ];
        yield 'wiki' => [
            'resourcetype' => 'wiki',
            'expected' => null,
        ];
    }
}
