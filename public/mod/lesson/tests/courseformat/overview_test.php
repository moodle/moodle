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

namespace mod_lesson\courseformat;

use core_courseformat\local\overview\overviewfactory;
use lesson;

/**
 * Tests for Lesson overview.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright   2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Helper function to create lesson pages with multichoice questions.
     *
     * @param lesson $lesson The lesson object.
     * @param int $count The number of multichoice questions to create.
     */
    private function create_lesson_pages(lesson $lesson, int $count): void {
        /** @var \mod_lesson_generator $lessongenerator */
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        for ($i = 0; $i < $count; $i++) {
            $lessongenerator->create_page([
                'title' => 'Multichoice question' . ($i + 1),
                'content' => 'Question content',
                'qtype' => 'multichoice',
                'lessonid' => $lesson->id,
            ]);
            $lessongenerator->create_answer(['page' => 'Multichoice question' . ($i + 1), 'answer' => 'A', 'score' => 1]);
            $lessongenerator->create_answer(['page' => 'Multichoice question' . ($i + 1), 'answer' => 'B']);
        }
        $lessongenerator->finish_generate_answer();
    }

    /**
     * Test get_due_date_overview.
     *
     * @param int|null $timeincrement the time increment in seconds to add to the current time for the deadline.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_due_date_overview')]
    public function test_get_due_date_overview(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $deadline = $timeincrement ? $this->mock_clock_with_frozen()->time() + $timeincrement : 0;
        $lesson = $this->getDataGenerator()->create_module(
            'lesson',
            [
                'course' => $course->id,
                'deadline' => $deadline,
            ],
        );

        $this->setUser($student);

        $cm = get_fast_modinfo($course)->get_cm($lesson->cmid);
        $item = overviewfactory::create($cm)->get_due_date_overview();

        $this->assertEquals(get_string('duedate', 'lesson'), $item->get_name());
        $this->assertEquals($deadline, $item->get_value());
    }

    /**
     * Provider for test_get_due_date_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_due_date_overview(): \Generator {
        yield 'no_due' => [
            'timeincrement' => null,
        ];
        yield 'past_due' => [
            'timeincrement' => -1 * (4 * DAYSECS),
        ];
        yield 'future_due' => [
            'timeincrement' => (4 * DAYSECS),
        ];
    }

    /**
     * Test get_actions_overview.
     *
     * @param string $role
     * @param array|null $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_actions_overview')]
    public function test_get_actions_overview(
        string $role,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $lesson = $this->getDataGenerator()->create_module('lesson', ['course' => $course->id]);

        $this->setUser($currentuser);

        $cm = get_fast_modinfo($course)->get_cm($lesson->cmid);
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
     * @return \Generator
     */
    public static function provider_test_get_actions_overview(): \Generator {
        yield 'Student' => [
            'role' => 'student',
            'expected' => null,
        ];
        yield 'Teacher' => [
            'role' => 'editingteacher',
            'expected' => [
                'name' => get_string('actions'),
                'value' => '',
            ],
        ];
    }

    /**
     * Test get_extra_totalattempts_overview.
     *
     * @param string $role
     * @param int $groupmode
     * @param bool $hasentries
     * @param bool $hasretakes
     * @param array|null $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_totalattempts_overview')]
    public function test_get_extra_totalattempts_overview(
        string $role,
        int $groupmode,
        bool $hasentries,
        bool $hasretakes,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);

        if ($groupmode != NOGROUPS) {
            $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $currentuser->id, 'groupid' => $group1->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        }

        $lessonmodule = $this->getDataGenerator()->create_module(
            'lesson',
            ['course' => $course, 'retake' => $hasretakes, 'groupmode' => $groupmode]
        );
        $cm = get_fast_modinfo($course)->get_cm($lessonmodule->cmid);
        $lesson = new lesson($lessonmodule);
        /** @var  \mod_lesson_generator $lessongenerator */
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $this->create_lesson_pages($lesson, 2);

        if ($hasentries) {
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $student1->id,
                'grade' => 50,
            ]);
            if ($hasretakes) {
                // If we can retake, create another attempt for student1.
                $lessongenerator->create_submission([
                    'lessonid' => $lesson->id,
                    'userid' => $student1->id,
                    'grade' => 100,
                ]);
            }
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $student2->id,
                'grade' => 100,
            ]);
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $currentuser->id,
                'grade' => 100,
            ]);
        }

        $this->setUser($currentuser);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_totalattempts_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(get_string('totalattepmts', 'mod_lesson'), $item->get_name());
        $this->assertEquals($expected['value'], $item->get_value());
        if ($expected['averageindialog'] === null) {
            $itemcontent = $item->get_content();
            $this->assertIsString($itemcontent); // In this case the content is a string instead of content object.
            $this->assertEquals($expected['value'], $item->get_value());
            return;
        }
        $this->assertEquals(
            $expected['averageindialog'],
            $item->get_content()->get_items()[0]->value
        );
    }

    /**
     * Data provider for test_get_extra_totalattempts_overview.
     *
     * @return array
     */
    public static function provider_test_get_extra_totalattempts_overview(): array {
        return [
            'Teacher (with attempts)' => [
                'role' => 'editingteacher',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 4,
                    'averageindialog' => 1.3,
                ],
            ],
            'Teacher (with attempts) (Separate Groups)' => [
                'role' => 'editingteacher',
                'groupmode' => SEPARATEGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 4,
                    'averageindialog' => 1.3,
                ],
            ],
            'Teacher (with attempts) (Visible Groups)' => [
                'role' => 'editingteacher',
                'groupmode' => VISIBLEGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 4,
                    'averageindialog' => 1.3,
                ],
            ],
            'Teacher (with attempts without retakes)' => [
                'role' => 'editingteacher',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'hasretakes' => false,
                'expected' => [
                    'value' => null,
                    'averageindialog' => null,
                ],
            ],
            'Teacher (without attempts)' => [
                'role' => 'editingteacher',
                'groupmode' => NOGROUPS,
                'hasentries' => false,
                'hasretakes' => true,
                'expected' => [
                    'value' => 0,
                    'averageindialog' => 0,
                ],
            ],
            'Non-editing Teacher (with attempts)' => [
                'role' => 'teacher',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 4,
                    'averageindialog' => 1.3,
                ],
            ],
            'Non-editing Teacher (with attempts) (Separate Groups)' => [
                'role' => 'teacher',
                'groupmode' => SEPARATEGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 3,
                    'averageindialog' => 1.5,
                ],

            ],
            'Non-editing Teacher (with attempts) (Visible Groups)' => [
                'role' => 'teacher',
                'groupmode' => VISIBLEGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'value' => 4,
                    'averageindialog' => 1.3,
                ],
            ],
            'Student' => [
                'role' => 'student',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => null,
            ],
        ];
    }

    /**
     * Test get_extra_attemptedstudents_overview.
     *
     * @param string $role
     * @param int $groupmode
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_attemptedstudents_overview')]
    public function test_get_extra_attemptedstudents_overview(
        string $role,
        int $groupmode,
        bool $hasentries,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);

        if ($groupmode != NOGROUPS) {
            $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $currentuser->id, 'groupid' => $group1->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        }

        $lessonmodule = $this->getDataGenerator()->create_module(
            'lesson',
            ['course' => $course, 'groupmode' => $groupmode]
        );
        $cm = get_fast_modinfo($course)->get_cm($lessonmodule->cmid);
        $lesson = new lesson($lessonmodule);
        /** @var  \mod_lesson_generator $lessongenerator */
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $this->create_lesson_pages($lesson, 2);

        if ($hasentries) {
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $student1->id,
                'grade' => 100,
            ]);
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $student2->id,
                'grade' => 100,
            ]);
            $lessongenerator->create_submission([
                'lessonid' => $lesson->id,
                'userid' => $currentuser->id,
                'grade' => 100,
            ]);
        }

        $this->setUser($currentuser);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_attemptedstudents_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => (int)$item->get_value()]
        );
    }

    /**
     * Data provider for test_get_extra_attemptedstudents_overview.
     *
     * @return array
     */
    public static function provider_test_get_extra_attemptedstudents_overview(): array {
        return [
            'Teacher (with attempts)' => [
                'role' => 'editingteacher',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 3,
                ],
            ],
            'Teacher (with attempts) (Separate Groups)' => [
                'role' => 'editingteacher',
                'groupmode' => SEPARATEGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 3,
                ],
            ],
            'Teacher (with attempts) (Visible Groups)' => [
                'role' => 'editingteacher',
                'groupmode' => VISIBLEGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 3,
                ],
            ],
            'Teacher (without attempts)' => [
                'role' => 'editingteacher',
                'groupmode' => NOGROUPS,
                'hasentries' => false,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 0,
                ],
            ],
            'Non-editing Teacher (with attempts)' => [
                'role' => 'teacher',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 3,
                ],
            ],
            'Non-editing Teacher (with attempts) (Separate Groups)' => [
                'role' => 'teacher',
                'groupmode' => SEPARATEGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 2,
                ],
            ],
            'Non-editing Teacher (with attempts) (Visible Groups)' => [
                'role' => 'teacher',
                'groupmode' => VISIBLEGROUPS,
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 3,
                ],
            ],
            'Student' => [
                'role' => 'student',
                'groupmode' => NOGROUPS,
                'hasentries' => true,
                'expected' => null,
            ],
        ];
    }
}
