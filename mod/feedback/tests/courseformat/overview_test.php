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

namespace mod_feedback\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Feedback
 *
 * @covers \mod_feedback\courseformat\overview
 * @package    mod_feedback
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');
        parent::setUpBeforeClass();
    }


    /**
     * Test get_actions_overview.
     *
     * @covers ::get_actions_overview
     * @dataProvider provider_test_get_actions_overview
     *
     * @param string $user
     * @param bool $expectnull
     * @param bool $hasresponses
     * @return void
     */
    public function test_get_actions_overview(string $user, bool $expectnull, bool $hasresponses): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module(
            'feedback',
            ['course' => $course->id],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $itemcreated = $feedbackgenerator->create_item_multichoice($activity, ['values' => "y\nn"]);

        $expectedresonses = 0;
        if ($hasresponses) {
            $this->setUser($student);
            $feedbackgenerator->create_response([
                'userid' => $student->id,
                'cmid' => $cm->id,
                'anonymous' => false,
                $itemcreated->name => 'y',
            ]);
            $expectedresonses = 1;
        }

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $item = overviewfactory::create($cm)->get_actions_overview();

        // Students should not see item.
        if ($expectnull) {
            $this->assertNull($item);
            return;
        }

        // Teachers should see item.
        $this->assertEquals(get_string('responses', 'mod_feedback'), $item->get_name());
        $this->assertEquals($expectedresonses, $item->get_value());
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return array
     */
    public static function provider_test_get_actions_overview(): array {
        return [
            'Teacher with responses' => [
                'user' => 'teacher',
                'expectnull' => false,
                'hasresponses' => true,
            ],
            'Student with responses' => [
                'user' => 'student',
                'expectnull' => true,
                'hasresponses' => true,
            ],
            'Teacher without responses' => [
                'user' => 'teacher',
                'expectnull' => false,
                'hasresponses' => false,
            ],
            'Student without responses' => [
                'user' => 'student',
                'expectnull' => true,
                'hasresponses' => false,
            ],
        ];
    }

    /**
     * Test get_due_date_overview.
     * @covers ::get_due_date_overview
     * @dataProvider provider_test_get_due_date_overview
     * @param string $user
     * @param bool $hasduedate
     * @return void
     */
    public function test_get_due_date_overview(string $user, bool $hasduedate): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $moddata = [
            'course' => $course->id,
            'timeclose' => $hasduedate ? time() + 3600 : 0,
        ];

        $activity = $this->getDataGenerator()->create_module('feedback', $moddata);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $item = overviewfactory::create($cm)->get_due_date_overview();

        // Teachers should see item.
        $this->assertEquals(get_string('feedbackclose', 'mod_feedback'), $item->get_name());
        $expectedvalue = $hasduedate ? $moddata['timeclose'] : null;
        $this->assertEquals($expectedvalue, $item->get_value());
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return array
     */
    public static function provider_test_get_due_date_overview(): array {
        return [
            'Teacher with due date' => [
                'user' => 'teacher',
                'hasduedate' => true,
            ],
            'Student with due date' => [
                'user' => 'student',
                'hasduedate' => true,
            ],
            'Teacher without due date' => [
                'user' => 'teacher',
                'hasduedate' => false,
            ],
            'Student without due date' => [
                'user' => 'student',
                'hasduedate' => false,
            ],
        ];
    }

    /**
     * Test get_extra_submitted_overview.
     *
     * @covers ::get_extra_submitted_overview
     * @dataProvider provider_test_get_extra_submitted_overview
     *
     * @param string $user
     * @param bool $expectnull
     * @param bool $hasresponses
     * @return void
     */
    public function test_get_extra_submitted_overview(string $user, bool $expectnull, bool $hasresponses): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module(
            'feedback',
            ['course' => $course->id],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $itemcreated = $feedbackgenerator->create_item_multichoice($activity, ['values' => "y\nn"]);

        $expectedresonses = 0;
        if ($hasresponses) {
            $this->setUser($student);
            $feedbackgenerator->create_response([
                'userid' => $student->id,
                'cmid' => $cm->id,
                'anonymous' => false,
                $itemcreated->name => 'y',
            ]);
            $expectedresonses = 1;
        }

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submitted_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        // Students should not see item.
        if ($expectnull) {
            $this->assertNull($item);
            return;
        }

        // Teachers should see item.
        $this->assertEquals(get_string('responded', 'mod_feedback'), $item->get_name());
        $this->assertEquals($hasresponses, $item->get_value());
    }

    /**
     * Data provider for test_get_extra_submitted_overview.
     *
     * @return array
     */
    public static function provider_test_get_extra_submitted_overview(): array {
        return [
            'Teacher with responses' => [
                'user' => 'teacher',
                'expectnull' => true,
                'hasresponses' => true,
            ],
            'Student with responses' => [
                'user' => 'student',
                'expectnull' => false,
                'hasresponses' => true,
            ],
            'Teacher without responses' => [
                'user' => 'teacher',
                'expectnull' => true,
                'hasresponses' => false,
            ],
            'Student without responses' => [
                'user' => 'student',
                'expectnull' => false,
                'hasresponses' => false,
            ],
        ];
    }
}
