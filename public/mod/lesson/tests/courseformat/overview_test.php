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
     * @covers ::get_due_date_overview
     * @dataProvider provider_test_get_due_date_overview
     *
     * @param int|null $timeincrement the time increment in seconds to add to the current time for the deadline.
     * @return void
     */
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
     * @return array
     */
    public static function provider_test_get_due_date_overview(): array {
        return [
            'no_due' => [
                'timeincrement' => null,
            ],
            'past_due' => [
                'timeincrement' => -1 * (4 * DAYSECS),
            ],
            'future_due' => [
                'timeincrement' => (4 * DAYSECS),
            ],
        ];
    }

    /**
     * Test get_actions_overview.
     *
     * @covers ::get_actions_overview
     * @dataProvider provider_test_get_actions_overview
     *
     * @param string $role
     * @param array|null $expected
     * @return void
     */
    public function test_get_actions_overview(
        string $role,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $lesson = $this->getDataGenerator()->create_module( 'lesson', ['course' => $course->id]);

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
     * @return array
     */
    public static function provider_test_get_actions_overview(): array {
        return [
            'Student' => [
                'role' => 'student',
                'expected' => null,
            ],
            'Teacher' => [
                'role' => 'editingteacher',
                'expected' => [
                    'name' => get_string('actions'),
                    'value' => '',
                ],
            ],
        ];
    }

    /**
     * Test get_extra_totalattempts_overview.
     *
     * @covers ::get_extra_totalattempts_overview
     * @dataProvider provider_test_get_extra_totalattempts_overview
     *
     * @param string $role
     * @param bool $hasentries
     * @param bool $hasretakes
     * @param array|null $expected
     * @return void
     */
    public function test_get_extra_totalattempts_overview(
        string $role,
        bool $hasentries,
        bool $hasretakes,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);

        $lessonmodule = $this->getDataGenerator()->create_module(
            'lesson',
            ['course' => $course, 'retake' => $hasretakes]
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

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
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
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => [
                    'name' => get_string('totalattepmts', 'mod_lesson'),
                    'value' => 2,
                ],
            ],
            'Teacher (with attempts without retakes)' => [
                'role' => 'editingteacher',
                'hasentries' => true,
                'hasretakes' => false,
                'expected' => [
                    'name' => get_string('totalattepmts', 'mod_lesson'),
                    'value' => null,
                ],
            ],
            'Teacher (without attempts)' => [
                'role' => 'editingteacher',
                'hasentries' => false,
                'hasretakes' => true,
                'expected' => [
                    'name' => get_string('totalattepmts', 'mod_lesson'),
                    'value' => 0,
                ],
            ],
            'Student' => [
                'role' => 'student',
                'hasentries' => true,
                'hasretakes' => true,
                'expected' => null,
            ],
        ];
    }

    /**
     * Test get_extra_attemptedstudents_overview.
     *
     * @covers ::get_extra_attemptedstudents_overview
     * @dataProvider provider_test_get_extra_attemptedstudents_overview
     *
     * @param string $role
     * @param bool $hasentries
     * @param array|null $expected
     * @return void
     */
    public function test_get_extra_attemptedstudents_overview(
        string $role,
        bool $hasentries,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);

        $lessonmodule = $this->getDataGenerator()->create_module('lesson', ['course' => $course]);
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
                'hasentries' => true,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 2,
                ],
            ],
            'Teacher (without attempts)' => [
                'role' => 'editingteacher',
                'hasentries' => false,
                'expected' => [
                    'name' => get_string('studentswhoattempted', 'mod_lesson'),
                    'value' => 0,
                ],
            ],
            'Student' => [
                'role' => 'student',
                'hasentries' => true,
                'expected' => null,
            ],
        ];
    }
}
