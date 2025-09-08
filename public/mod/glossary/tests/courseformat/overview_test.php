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

namespace mod_glossary\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Glossary
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
     *
     * @param string $role
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_actions_overview')]
    public function test_get_actions_overview(
        string $role,
        bool $requireapproval,
        bool $hasentries,
        ?array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $activity = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'defaultapproval' => !$requireapproval],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        if ($hasentries) {
            /** @var \mod_glossary_generator $glossarygenerator */
            $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $currentuser->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $teacher1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student1->id,
                'approved' => (int)!$requireapproval,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student2->id,
                'approved' => (int)!$requireapproval,
            ]);
        }

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
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => null,
        ];
        yield 'Teacher with entries (non-require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('actions'),
                'value' => 0,
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher without entries (require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => true,
            'hasentries' => false,
            'expected' => [
                'name' => get_string('actions'),
                'value' => 0,
                'content' => get_string('view'),
            ],
        ];
        yield 'Teacher with entries (require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => true,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('actions'),
                'value' => 2,
                'content' => get_string('approve', 'mod_glossary'),
            ],
        ];
    }

    /**
     * Test get_extra_comments_overview.
     *
     * @param string $role The role of the current user.
     * @param bool $requireapproval Whether approval is required for entries.
     * @param bool $hasentries Whether there are entries in the glossary.
     * @param string|null $expected Expected value for the overview item.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_comments_overview')]
    public function test_get_extra_comments_overview(
        string $role,
        bool $requireapproval,
        bool $hasentries,
        ?string $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $activity = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'defaultapproval' => !$requireapproval, 'allowcomments' => true],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        if ($hasentries) {
            /** @var \mod_glossary_generator $glossarygenerator */
            $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
            $entry1 = $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $currentuser->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $teacher1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student1->id,
                'approved' => 1,
            ]);
            $entry4 = $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student2->id,
                'approved' => (int)!$requireapproval,
            ]);
            $entry5 = $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $currentuser->id,
                'approved' => (int)!$requireapproval,
            ]);

            /** @var \core_comment_generator $generator */
            $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');
            $cmtoptions = new \stdClass();
            $cmtoptions->context = \context_module::instance($activity->cmid);
            $cmtoptions->instanceid = $activity->cmid;
            $cmtoptions->component = 'mod_glossary';
            $cmtoptions->area = 'glossary_entry';
            $cmtoptions->content = 'My comment';

            $cmtoptions->itemid = $entry1->id;
            $cmtoptions->userid = $teacher1->id;
            $generator->create_comment($cmtoptions);

            $cmtoptions->itemid = $entry1->id;
            $cmtoptions->userid = $currentuser->id;
            $generator->create_comment($cmtoptions);

            $cmtoptions->itemid = $entry4->id;
            $cmtoptions->userid = $teacher1->id;
            $generator->create_comment($cmtoptions);

            // This comment won't be displayed when the current user is a student.
            $cmtoptions->itemid = $entry5->id;
            $cmtoptions->userid = $teacher1->id;
            $generator->create_comment($cmtoptions);

            $cmtoptions->itemid = $entry5->id;
            $cmtoptions->userid = $currentuser->id;
            $generator->create_comment($cmtoptions);
        }

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_comments_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(get_string('comments', 'glossary'), $item->get_name());
        $this->assertEquals($expected, $item->get_value());
    }

    /**
     * Data provider for test_get_extra_comments_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_extra_comments_overview(): \Generator {
        yield 'Teacher without responses' => [
            'role' => 'editingteacher',
            'requireapproval' => false,
            'hasentries' => false,
            'expected' => '0',
        ];
        yield 'Teacher with responses (non-require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => '5',
        ];
        yield 'Teacher with responses (require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => true,
            'hasentries' => true,
            'expected' => '5',
        ];
        yield 'Student without responses' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => false,
            'expected' => '0',
        ];
        yield 'Student with responses (non-require approval)' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => '5',
        ];
        yield 'Student with responses (require approval)' => [
            'role' => 'student',
            'requireapproval' => true,
            'hasentries' => true,
            'expected' => '4', // One comment is from an unapproved entry created by a different user.
        ];
    }

    /**
     * Test get_extra_comments_overview when comments are not allowed.
     *
     * @param bool $usecomments Whether comments are allowed globally.
     * @param bool $allowcomments Whether comments are allowed in the glossary.
     * @param string $expected Expected value for the overview item.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_comments_overview_with_comments_disabled')]
    public function test_get_extra_comments_overview_with_comments_disabled(
        bool $usecomments,
        bool $allowcomments,
        string $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('usecomments', $usecomments);

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'allowcomments' => $allowcomments],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_comments_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        $this->assertEquals(get_string('comments', 'glossary'), $item->get_name());
        $this->assertEquals(0, $item->get_value());
        $this->assertEquals($expected, $item->get_content());
    }

    /**
     * Data provider for test_get_extra_comments_overview_with_comments_disabled.
     *
     * @return \Generator
     */
    public static function provider_test_get_extra_comments_overview_with_comments_disabled(): \Generator {
        yield 'Use comments disabled, allow comments disabled' => [
            'usecomments' => false,
            'allowcomments' => false,
            'expected' => '-',
        ];
        yield 'Use comments enabled, allow comments disabled' => [
            'usecomments' => true,
            'allowcomments' => false,
            'expected' => '-',
        ];
        yield 'Use comments disabled, allow comments enabled' => [
            'usecomments' => false,
            'allowcomments' => true,
            'expected' => '-',
        ];
        yield 'Use comments enabled, allow comments enabled' => [
            'usecomments' => true,
            'allowcomments' => true,
            'expected' => '0',
        ];
    }

    /**
     * Test get_extra_totalentries_overview.
     *
     * @param string $role
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_totalentries_overview')]
    public function test_get_extra_totalentries_overview(
        string $role,
        bool $requireapproval,
        bool $hasentries,
        array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $activity = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'defaultapproval' => !$requireapproval],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        if ($hasentries) {
            /** @var \mod_glossary_generator $glossarygenerator */
            $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $currentuser->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $teacher1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student2->id,
                'approved' => (int)!$requireapproval,
            ]);
        }

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_totalentries_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
        );
    }

    /**
     * Data provider for test_get_extra_submitted_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_extra_totalentries_overview(): \Generator {
        yield 'Teacher with entries (non-require approval)' => [
            'role' => 'editingteacher',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('entries', 'mod_glossary'),
                'value' => 4,
            ],
        ];
        yield 'Student without entries' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => false,
            'expected' => [
                'name' => get_string('totalentries', 'mod_glossary'),
                'value' => 0,
            ],
        ];
        yield 'Student with entries (non-require approval)' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('totalentries', 'mod_glossary'),
                'value' => 4,
            ],
        ];
        yield 'Student with entries (require approval)' => [
            'role' => 'student',
            'requireapproval' => true,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('totalentries', 'mod_glossary'),
                'value' => 3,
            ],
        ];
    }

    /**
     * Test get_extra_myentries_overview.
     *
     * @param string $role
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_myentries_overview')]
    public function test_get_extra_myentries_overview(
        string $role,
        bool $requireapproval,
        bool $hasentries,
        ?array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $activity = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'defaultapproval' => !$requireapproval],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        if ($hasentries) {
            /** @var \mod_glossary_generator $glossarygenerator */
            $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $currentuser->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $teacher1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student1->id,
                'approved' => 1,
            ]);
            $glossarygenerator->create_entry([
                'glossaryid' => $activity->id,
                'userid' => $student2->id,
                'approved' => (int)!$requireapproval,
            ]);
        }

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_myentries_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
        );
    }

    /**
     * Data provider for test_get_extra_submitted_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_extra_myentries_overview(): \Generator {
        yield 'Teacher' => [
            'role' => 'editingteacher',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => null,
        ];
        yield 'Student without responses' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => false,
            'expected' => [
                'name' => get_string('myentries', 'mod_glossary'),
                'value' => 0,
            ],
        ];
        yield 'Student with responses (non-require approval)' => [
            'role' => 'student',
            'requireapproval' => false,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('myentries', 'mod_glossary'),
                'value' => 1,
            ],
        ];
        yield 'Student with responses (require approval)' => [
            'role' => 'student',
            'requireapproval' => true,
            'hasentries' => true,
            'expected' => [
                'name' => get_string('myentries', 'mod_glossary'),
                'value' => 1,
            ],
        ];
    }
}
