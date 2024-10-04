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
 * @covers     \mod_glossary\courseformat\overview
 * @package    mod_glossary
 * @category   test
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
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
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
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

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
        );
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
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => null,
            ],
            'Teacher with entries (non-require approval)' => [
                'role' => 'editingteacher',
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('actions'),
                    'value' => 0,
                ],
            ],
            'Teacher without entries (require approval)' => [
                'role' => 'editingteacher',
                'requireapproval' => true,
                'hasentries' => false,
                'expected' => [
                    'name' => get_string('actions'),
                    'value' => 0,
                ],
            ],
            'Teacher with entries (require approval)' => [
                'role' => 'editingteacher',
                'requireapproval' => true,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('actions'),
                    'value' => 2,
                ],
            ],
        ];
    }

    /**
     * Test get_extra_totalentries_overview.
     *
     * @covers ::get_extra_totalentries_overview
     * @dataProvider provider_test_get_extra_totalentries_overview
     *
     * @param string $role
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array $expected
     * @return void
     */
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
     * @return array
     */
    public static function provider_test_get_extra_totalentries_overview(): array {
        return [
            'Teacher with entries (non-require approval)' => [
                'role' => 'editingteacher',
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('entries', 'mod_glossary'),
                    'value' => 4,
                ],
            ],
            'Student without entries' => [
                'role' => 'student',
                'requireapproval' => false,
                'hasentries' => false,
                'expected' => [
                    'name' => get_string('totalentries', 'mod_glossary'),
                    'value' => 0,
                ],
            ],
            'Student with entries (non-require approval)' => [
                'role' => 'student',
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('totalentries', 'mod_glossary'),
                    'value' => 4,
                ],
            ],
            'Student with entries (require approval)' => [
                'role' => 'student',
                'requireapproval' => true,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('totalentries', 'mod_glossary'),
                    'value' => 3,
                ],
            ],
        ];
    }

    /**
     * Test get_extra_myentries_overview.
     *
     * @covers ::get_extra_myentries_overview
     * @dataProvider provider_test_get_extra_myentries_overview
     *
     * @param string $role
     * @param bool $requireapproval
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
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
     * @return array
     */
    public static function provider_test_get_extra_myentries_overview(): array {
        return [
            'Teacher' => [
                'role' => 'editingteacher',
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => null,
            ],
            'Student without responses' => [
                'role' => 'student',
                'requireapproval' => false,
                'hasentries' => false,
                'expected' => [
                    'name' => get_string('myentries', 'mod_glossary'),
                    'value' => 0,
                ],
            ],
            'Student with responses (non-require approval)' => [
                'role' => 'student',
                'requireapproval' => false,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('myentries', 'mod_glossary'),
                    'value' => 1,
                ],
            ],
            'Student with responses (require approval)' => [
                'role' => 'student',
                'requireapproval' => true,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('myentries', 'mod_glossary'),
                    'value' => 1,
                ],
            ],
        ];
    }
}
