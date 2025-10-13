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
 * @package    mod_feedback
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
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
     * @param string $role
     * @param array|null $expected
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
        $activity = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);

        $this->setUser($currentuser);

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
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
                'value' => get_string('view'),
            ],
        ];
    }

    /**
     * Test get_due_date_overview.
     *
     * @param string $user
     * @param bool $hasduedate
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_due_date_overview')]
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
        $this->assertEquals(get_string('duedate', 'mod_feedback'), $item->get_name());
        $expectedvalue = $hasduedate ? $moddata['timeclose'] : null;
        $this->assertEquals($expectedvalue, $item->get_value());
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_due_date_overview(): \Generator {
        yield 'Teacher with due date' => [
            'user' => 'teacher',
            'hasduedate' => true,
        ];
        yield 'Student with due date' => [
            'user' => 'student',
            'hasduedate' => true,
        ];
        yield 'Teacher without due date' => [
            'user' => 'teacher',
            'hasduedate' => false,
        ];
        yield 'Student without due date' => [
            'user' => 'student',
            'hasduedate' => false,
        ];
    }

    /**
     * Test get_extra_responses_overview.
     *
     * @param string $user
     * @param bool $expectnull
     * @param bool $hasresponses
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_get_extra_responses_overview')]
    public function test_get_extra_responses_overview(string $user, bool $expectnull, bool $hasresponses): void {
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
        $method = $reflection->getMethod('get_extra_responses_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

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
     * Data provider for get_extra_responses_overview.
     *
     * @return \Generator
     */
    public static function provider_get_extra_responses_overview(): \Generator {
        yield 'Teacher with responses' => [
            'user' => 'teacher',
            'expectnull' => false,
            'hasresponses' => true,
        ];
        yield 'Student with responses' => [
            'user' => 'student',
            'expectnull' => true,
            'hasresponses' => true,
        ];
        yield 'Teacher without responses' => [
            'user' => 'teacher',
            'expectnull' => false,
            'hasresponses' => false,
        ];
        yield 'Student without responses' => [
            'user' => 'student',
            'expectnull' => true,
            'hasresponses' => false,
        ];
    }

    /**
     * Test get_extra_responses_overview_with_groups().
     *
     * @param int $groupmode The group mode of the course.
     * @param string $currentuser The user to set for the test.
     * @param int $expectedcount The expected number of completeds.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_feedback_get_extra_responses_overview_with_groups')]
    public function test_get_extra_responses_overview_with_groups(
        int $groupmode,
        string $currentuser,
        int $expectedcount,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'groupmode' => $groupmode,
            'groupmodeforce' => true,
        ]);
        $allgroups = [
            'groupa' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupb' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupc' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
        ];

        // Participant:  Role:           Groups:
        // student1a     student         groupa
        // student2a     student         groupa
        // student3b     student         groupb
        // teacher1      editingteacher  groupa
        // teacher2      teacher         groupa
        // teacher3      teacher         groupb
        // teacher4      teacher         groupc
        // teacher5      teacher         (no group)
        // teacher6      editingteacher  (no group) .
        $student1a = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $student1a->id,
        ]);
        $student2a = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $student2a->id,
        ]);
        $student3b = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupb']->id,
            'userid' => $student3b->id,
        ]);
        $teachers['teacher1'] = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $teachers['teacher1']->id,
        ]);
        $teachers['teacher2'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $teachers['teacher2']->id,
        ]);
        $teachers['teacher3'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupb']->id,
            'userid' => $teachers['teacher3']->id,
        ]);
        $teachers['teacher4'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupc']->id,
            'userid' => $teachers['teacher4']->id,
        ]);
        $teachers['teacher5'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $teachers['teacher6'] = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $activity = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Add a multichoice item to the feedback and create responses for it.
        /** @var  \mod_feedback_generator $feedbackgenerator */
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $item = $feedbackgenerator->create_item_multichoice($activity, ['values' => "y\nn"]);
        $feedbackgenerator->create_response([
            'userid' => $student1a->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'y',
        ]);
        $feedbackgenerator->create_response([
            'userid' => $student2a->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'n',
        ]);
        $feedbackgenerator->create_response([
            'userid' => $student3b->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'y',
        ]);

        $this->setUser($teachers[$currentuser]);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_responses_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        $this->assertEquals(get_string('responses', 'mod_feedback'), $item->get_name());
        $this->assertEquals($expectedcount, $item->get_value());
    }

    /**
     * Data provider for feedback_get_extra_responses_overview_with_groups.
     *
     * @return \Generator
     */
    public static function provider_feedback_get_extra_responses_overview_with_groups(): \Generator {
        yield 'Separate groups - Editing teacher' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher1',
            'expectedcount' => 3,
        ];
        yield 'Separate groups - Non-editing teacher (groupa)' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher2',
            'expectedcount' => 2,
        ];
        yield 'Separate groups - Non-editing teacher (groupb)' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher3',
            'expectedcount' => 1,
        ];
        yield 'Separate groups - Non-editing teacher (groupc)' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher4',
            'expectedcount' => 0,
        ];
        yield 'Separate groups - Non-editing teacher (no group)' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher5',
            'expectedcount' => 3, // Although the expected count should be 0, this information will never be shown to the user.
        ];
        yield 'Separate groups - Editing teacher (no group)' => [
            'groupmode' => SEPARATEGROUPS,
            'currentuser' => 'teacher6',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Editing teacher' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher1',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Non-editing teacher (groupa)' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher2',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Non-editing teacher (groupb)' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher3',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Non-editing teacher (groupc)' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher4',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Non-editing teacher (no group)' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher5',
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Editing teacher (no group)' => [
            'groupmode' => VISIBLEGROUPS,
            'currentuser' => 'teacher6',
            'expectedcount' => 3,
        ];
        yield 'No groups - Editing teacher' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher1',
            'expectedcount' => 3,
        ];
        yield 'No groups - Non-editing teacher (groupa)' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher2',
            'expectedcount' => 3,
        ];
        yield 'No groups - Non-editing teacher (groupb)' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher3',
            'expectedcount' => 3,
        ];
        yield 'No groups - Non-editing teacher (groupc)' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher4',
            'expectedcount' => 3,
        ];
        yield 'No groups - Non-editing teacher (no group)' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher5',
            'expectedcount' => 3,
        ];
        yield 'No groups - Editing teacher (no group)' => [
            'groupmode' => NOGROUPS,
            'currentuser' => 'teacher6',
            'expectedcount' => 3,
        ];
    }

    /**
     * Test get_extra_submitted_overview.
     *
     * @param string $user
     * @param bool $expectnull
     * @param bool $hasresponses
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_submitted_overview')]
    public function test_get_extra_submitted_overview(string $user, bool $expectnull, bool $hasresponses = false): void {
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

        if ($hasresponses) {
            $this->setUser($student);
            $feedbackgenerator->create_response([
                'userid' => $student->id,
                'cmid' => $cm->id,
                'anonymous' => false,
                $itemcreated->name => 'y',
            ]);
        }

        if ($user == 'admin') {
            $this->setAdminUser();
        } else {
            $this->setUser(($user == 'teacher') ? $teacher : $student);
        }

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
     * @return \Generator
     */
    public static function provider_test_get_extra_submitted_overview(): \Generator {
        yield 'Teacher' => [
            'user' => 'teacher',
            'expectnull' => true,
        ];
        yield 'Admin' => [
            'user' => 'admin',
            'expectnull' => true,
        ];
        yield 'Student without responses' => [
            'user' => 'student',
            'expectnull' => false,
            'hasresponses' => false,
        ];
        yield 'Student with responses' => [
            'user' => 'student',
            'expectnull' => false,
            'hasresponses' => true,
        ];
    }
}
