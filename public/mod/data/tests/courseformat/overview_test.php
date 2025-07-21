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

namespace mod_data\courseformat;

use core_courseformat\local\overview\overviewfactory;
use mod_data\manager;

/**
 * Tests for Database activity overview
 *
 * @covers     \mod_data\courseformat\overview
 * @package    mod_data
 * @category   test
 * @copyright  2025 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
     *
     * @covers ::get_actions_overview
     * @dataProvider provider_test_get_actions_overview
     *
     * @param string $role
     * @param bool $needsapproval
     * @param array $entries
     * @param array|null $expected
     * @return void
     */
    public function test_get_actions_overview(
            string $role,
            bool $needsapproval,
            array $entries,
            ?array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $activity = $this->getDataGenerator()->create_module(
            manager::MODULE,
            ['course' => $course, 'approval' => $needsapproval],
        );

        // Add a field.
        /** @var \mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)[
            'name' => 'myfield',
            'type' => 'text',
        ];
        $field = $generator->create_field($fieldrecord, $activity);
        foreach ($entries as $entry => $approved) {
            $generator->create_entry(
                $activity,
                [$field->field->id => 'Example entry: '.$entry],
                0,
                [],
                ['approved' => $approved],
            );
        }

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $item = overviewfactory::create($cm)->get_actions_overview();

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals($expected['value'], $item->get_value());

        $content = $item->get_content();
        $this->assertInstanceOf(\action_link::class, $content);
        $this->assertStringContainsString($expected['link'], $content->text);
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return array
     */
    public static function provider_test_get_actions_overview(): array {
        return [
            'Student' => [
                'role' => 'student',
                'needsapproval' => false,
                'entries' => [1, 0],
                'expected' => null,
            ],
            'Teacher with entries (non-require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => false,
                'entries' => [1, 0],
                'expected' => [
                    'link' => get_string('view', 'moodle'),
                    'value' => 0,
                ],
            ],
            'Teacher without entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [],
                'expected' => [
                    'link' => get_string('view', 'moodle'),
                    'value' => 0,
                ],
            ],
            'Teacher with entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [1, 0],
                'expected' => [
                    'link' => get_string('approve', 'data'),
                    'value' => 1,
                ],
            ],
            'Teacher with approved entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [1, 1],
                'expected' => [
                    'link' => get_string('view', 'moodle'),
                    'value' => 0,
                ],
            ],
        ];
    }

    /**
     * Test get_extra_overview_items.
     *
     * @covers ::get_extra_overview_items
     * @dataProvider provider_test_get_entries_overview
     *
     * @param string $role
     * @param bool $needsapproval
     * @param array $entries
     * @param array $myentries
     * @param array $expected
     * @return void
     */
    public function test_get_extra_entries_overview(
        string $role,
        bool $needsapproval,
        array $entries,
        array $myentries,
        array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            manager::MODULE,
            ['course' => $course, 'approval' => $needsapproval],
        );

        // Add a field.
        /** @var \mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)[
            'name' => 'myfield',
            'type' => 'text',
        ];
        $field = $generator->create_field($fieldrecord, $activity);
        foreach ($entries as $entry => $approved) {
            $generator->create_entry(
                $activity,
                [$field->field->id => 'Example entry: '.$entry],
                0,
                [],
                ['approved' => $approved],
            );
        }

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);
        foreach ($myentries as $entry => $approved) {
            $generator->create_entry(
                $activity,
                [$field->field->id => 'Example entry: '.$entry],
                0,
                [],
                ['approved' => $approved],
            );
        }

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();

        if (is_null($expected['totalentries'])) {
            $this->assertNull($items['totalentries']);
        } else {
            $this->assertEquals($expected['totalentries'], $items['totalentries']->get_value());
        }

        if (is_null($expected['myentries'])) {
            $this->assertNull($items['myentries']);
        } else {
            $this->assertEquals($expected['myentries'], $items['myentries']->get_value());
        }
    }

    /**
     * Data provider for test entry related extras.
     *
     * @return array
     */
    public static function provider_test_get_entries_overview(): array {
        return [
            'Student not needing approval' => [
                'role' => 'student',
                'needsapproval' => false,
                'entries' => [1, 0],
                'myentries' => [1, 0],
                'expected' => [
                    'myentries' => 2,
                    'totalentries' => 4,
                ],
            ],
            'Student needing approval' => [
                'role' => 'student',
                'needsapproval' => true,
                'entries' => [1, 0],
                'myentries' => [1, 0],
                'expected' => [
                    'myentries' => 2,
                    'totalentries' => 2,
                ],
            ],
            'Teacher with entries (non-require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => false,
                'entries' => [1, 0],
                'myentries' => [1, 0],
                'expected' => [
                    'myentries' => null,
                    'totalentries' => 4,
                ],
            ],
            'Teacher without entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [],
                'myentries' => [],
                'expected' => [
                    'myentries' => null,
                    'totalentries' => 0,
                ],
            ],
            'Teacher with entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [1, 0],
                'myentries' => [1, 0],
                'expected' => [
                    'myentries' => null,
                    'totalentries' => 4,
                ],
            ],
            'Teacher with approved entries (require approval)' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [1, 1],
                'myentries' => [1, 1],
                'expected' => [
                    'myentries' => null,
                    'totalentries' => 4,
                ],
            ],
        ];
    }

    /**
     * Test get_extra_overview_items with groups.
     *
     * @covers ::get_extra_overview_items
     */
    public function test_get_extra_entries_overview_with_groups(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            manager::MODULE,
            ['course' => $course, 'groupmode' => SEPARATEGROUPS],
        );

        // Add a field.
        /** @var \mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)[
            'name' => 'myfield',
            'type' => 'text',
        ];
        $field = $generator->create_field($fieldrecord, $activity);
        $generator->create_entry(
            $activity,
            [$field->field->id => 'Example entry: All participants'],
        );

        // Create entries for each group.
        $generator->create_entry(
            $activity,
            [$field->field->id => 'G1'],
            $g1->id,
        );
        $generator->create_entry(
            $activity,
            [$field->field->id => 'G2'],
            $g2->id,
        );

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $noneditingteacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        groups_add_member($g1, $noneditingteacher->id);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($g1, $student->id);
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($g2, $otherstudent->id);

        $generator->create_entry(
            $activity,
            [$field->field->id => 'G1'],
            $g1->id,
            [],
            null,
            $student->id
        );
        $generator->create_entry(
            $activity,
            [$field->field->id => 'G2'],
            $g2->id,
            [],
            null,
            $otherstudent->id
        );

        // Editing teachers can see everything.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(5, $items['totalentries']->get_value());
        $this->assertNull($items['myentries']);

        // Non-editing teachers can see their groups and all participants.
        $this->setUser($noneditingteacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(3, $items['totalentries']->get_value());
        $this->assertNull($items['myentries']);

        // Students can see their groups and all participants.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(3, $items['totalentries']->get_value());
        $this->assertEquals(1, $items['myentries']->get_value());
    }

    /**
     * Test get_extra_comments_overview.
     *
     * @covers ::get_extra_comments_overview
     * @dataProvider provider_test_get_comments_overview
     *
     * @param string $role
     * @param bool $needsapproval
     * @param array $entries
     * @param int $expected
     * @return void
     */
    public function test_get_extra_comments_overview(
        string $role,
        bool $needsapproval,
        array $entries,
        int $expected
    ): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->usecomments = true;

        $course = $this->getDataGenerator()->create_course(['enablecomment' => 1]);
        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
                manager::MODULE,
                ['course' => $course, 'approval' => $needsapproval, 'comments' => 1],
        );

        // Add a field.
        /** @var \mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)[
                'name' => 'myfield',
                'type' => 'text',
        ];
        $field = $generator->create_field($fieldrecord, $activity);
        foreach ($entries as $entry) {
            $entryid = $generator->create_entry(
                $activity,
                [$field->field->id => 'Example entry'],
                0,
                [],
                ['approved' => $entry['approved']],
            );
            if ($entry['comments']) {
                $commentdata = [
                    [
                        'contextlevel' => 'module',
                        'instanceid' => $activity->cmid,
                        'component' => 'mod_data',
                        'content' => 'abc',
                        'itemid' => $entryid,
                        'area' => 'database_entry',
                    ],
                ];
                \core_comment_external::add_comments($commentdata);
            }
        }

        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($currentuser);

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();

        $this->assertEquals($expected, $items['comments']->get_value());
    }

    /**
     * Data provider for test comments extras.
     *
     * @return array
     */
    public static function provider_test_get_comments_overview(): array {
        return [
            'Student not needing approval with no comments' => [
                'role' => 'student',
                'needsapproval' => false,
                'entries' => [
                    ['approved' => 1, 'comments' => false],
                    ['approved' => 0, 'comments' => false],
                ],
                'expected' => 0,
            ],
            'Student not needing approval with comments' => [
                'role' => 'student',
                'needsapproval' => false,
                'entries' => [
                    ['approved' => 1, 'comments' => true],
                    ['approved' => 0, 'comments' => true],
                ],
                'expected' => 1,
            ],
            'Student needing approval with no comments' => [
                'role' => 'student',
                'needsapproval' => true,
                'entries' => [
                    ['approved' => 1, 'comments' => false],
                    ['approved' => 0, 'comments' => false],
                ],
                'expected' => 0,
            ],
            'Student needing approval with comments' => [
                'role' => 'student',
                'needsapproval' => true,
                'entries' => [
                    ['approved' => 1, 'comments' => true],
                    ['approved' => 0, 'comments' => true],
                ],
                'expected' => 1,
            ],
            'Teacher not needing approval with no comments' => [
                'role' => 'editingteacher',
                'needsapproval' => false,
                'entries' => [
                    ['approved' => 1, 'comments' => false],
                    ['approved' => 0, 'comments' => false],
                ],
                'expected' => 0,
            ],
            'Teacher not needing approval with comments' => [
                'role' => 'editingteacher',
                'needsapproval' => false,
                'entries' => [
                    ['approved' => 1, 'comments' => true],
                    ['approved' => 0, 'comments' => true],
                ],
                'expected' => 2,
            ],
            'Teacher needing approval with no comments' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [
                    ['approved' => 1, 'comments' => false],
                    ['approved' => 0, 'comments' => false],
                ],
                'expected' => 0,
            ],
            'Teacher needing approval with comments' => [
                'role' => 'editingteacher',
                'needsapproval' => true,
                'entries' => [
                    ['approved' => 1, 'comments' => true],
                    ['approved' => 0, 'comments' => true],
                ],
                'expected' => 2,
            ],
        ];
    }
}
