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

namespace core_courseformat;

/**
 * Tests for course
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_courseformat\activityoverviewbase
 */
final class activityoverviewbase_test extends \advanced_testcase {
    #[\Override()]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/fake_activityoverview.php');
        require_once($CFG->libdir . '/gradelib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test get_name_overview method.
     *
     * @covers ::get_name_overview
     */
    public function test_get_name_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Test!']);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $result = $overview->get_name_overview();
        $this->assertEquals(get_string('name'), $result->get_name());
        $this->assertEquals('Test!', $result->get_value());
        $this->assertInstanceOf(\core_courseformat\output\local\overview\activityname::class, $result->get_content());
    }

    /**
     * Test get_completion_overview method.
     *
     * @covers ::get_completion_overview
     * @dataProvider provider_get_completion_overview
     * @param int $setcompletion the completion status
     */
    public function test_get_completion_overview(
        int $setcompletion,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'completion' => \COMPLETION_TRACKING_AUTOMATIC]
        );

        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $completion = (object) [
            'coursemoduleid' => $cm->id,
            'timemodified' => time(),
            'viewed' => \COMPLETION_NOT_VIEWED,
            'overrideby' => null,
            'id' => 0,
            'completionstate' => $setcompletion,
            'userid' => $user->id,
        ];
        $comletioninfo = new \completion_info($course);
        $comletioninfo->internal_set_data($cm, $completion, true);

        $this->setUser($user);

        $result = $overview->get_completion_overview();
        $this->assertEquals(get_string('completion_status', 'completion'), $result->get_name());
        $this->assertEquals($setcompletion, $result->get_value());
        $this->assertInstanceOf(\core_courseformat\output\local\content\cm\completion::class, $result->get_content());
    }

    /**
     * Data provider for test_get_completion_overview.
     *
     * @return array the testing scenarios
     */
    public static function provider_get_completion_overview(): array {
        return [
            'complet' => [
                'setcompletion' => \COMPLETION_COMPLETE,
            ],
            'incomplete' => [
                'setcompletion' => \COMPLETION_INCOMPLETE,
            ],
            'complete pass' => [
                'setcompletion' => \COMPLETION_COMPLETE_PASS,
            ],
            'complete fail' => [
                'setcompletion' => \COMPLETION_COMPLETE_FAIL,
            ],
        ];
    }

    /**
     * Test get_completion_overview method on an activity with no completion.
     *
     * @covers ::get_completion_overview
     */
    public function test_get_completion_overview_no_completion(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );

        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $this->setUser($user);

        $result = $overview->get_completion_overview();
        $this->assertEquals(get_string('completion_status', 'completion'), $result->get_name());
        $this->assertEquals(null, $result->get_value());
        $this->assertEquals('-', $result->get_content());
    }

    /**
     * Test get_grades_overviews method.
     *
     * @covers ::get_grades_overviews
     */
    public function test_get_grades_overviews(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create some modules.
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );
        $workshop = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id],
            ['grade' => 100.0],
        );
        $page = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id]
        );

        // Assignments have one grade item.
        $assignitems = \grade_item::fetch_all([
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => (int) $assign->id,
            'courseid' => $course->id,
        ]);
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = reset($assignitems)->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 88;
        $gradegrade->finalgrade = 88;
        $gradegrade->insert();

        // Workshops have two grade items.
        $workshopitems = array_values(
            \grade_item::fetch_all([
                'itemtype' => 'mod',
                'itemmodule' => 'workshop',
                'iteminstance' => (int) $workshop->id,
                'courseid' => $course->id,
                ['grade' => 100.0],
            ])
        );
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = reset($workshopitems)->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 77;
        $gradegrade->finalgrade = 77;
        $gradegrade->insert();

        // Validate student grades.
        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertCount(1, $result);
        $this->assertEquals(get_string('gradenoun'), $result[0]->get_name());
        $this->assertEquals(88, $result[0]->get_value());
        $this->assertEquals('88.00', $result[0]->get_content());

        // Validate workshop gradeitems (having two grade, they should return an empty array).
        $cm = $modinfo->get_cm($workshop->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate page has no gradeitems.
        $cm = $modinfo->get_cm($page->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate teacher does not has grade overiviews items.
        $this->setUser($teacher);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);;

        // Validate workshop gradeitems (having two grade, they should return an empty array).
        $cm = $modinfo->get_cm($workshop->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate page has no gradeitems.
        $cm = $modinfo->get_cm($page->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);
    }

    /**
     * Test get_grades_overviews when the grade item is hidden.
     *
     * @covers ::get_grades_overviews
     */
    /**
     * Test get_grades_overviews method.
     *
     * @covers ::get_grades_overviews
     */
    public function test_get_grades_overviews_hidden(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create some modules.
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );

        // Assignments have one grade item.
        $assignitems = \grade_item::fetch_all([
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => (int) $assign->id,
            'courseid' => $course->id,
        ]);
        $gradeitem = reset($assignitems);
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = $gradeitem->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 88;
        $gradegrade->finalgrade = 88;
        $gradegrade->insert();

        // Hide some grades.
        $gradeitem->set_hidden(1, true);

        // Validate student grades.
        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertCount(1, $result);
        $this->assertEquals(get_string('gradenoun'), $result[0]->get_name());
        $this->assertEquals('-', $result[0]->get_value());
        $this->assertEquals('-', $result[0]->get_content());
    }
}
