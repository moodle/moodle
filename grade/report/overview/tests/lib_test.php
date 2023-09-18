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

namespace gradereport_overview;

/**
 * Overview grade report lib functions unit tests
 *
 * @package gradereport_overview
 * @copyright 2023 The Open University
 * @covers \grade_report_overview
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Require the library file we're about to test, and other requirements.
     */
    protected function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/grade/report/overview/lib.php');
        require_once($CFG->dirroot . '/grade/querylib.php');
    }

    /**
     * Data provider for true or false value.
     *
     * @return array Two options, one with true and one with false
     */
    public function true_or_false(): array {
        return [
            [true],
            [false]
        ];
    }

    /**
     * Tests the regrade_all_courses_if_needed function, which is supposed to regrade all the
     * courses that the current user is enrolled on (if they need update).
     *
     * This is tested with both frontend and backend - the frontend option should not actually
     * do the progress bar/continue button (which can't be tested from here because it calls exit)
     * because these courses are small.
     *
     * @dataProvider true_or_false
     * @param bool $frontend True to use the front-end parameter to the function under test
     */
    public function test_regrade_all_courses_if_needed(bool $frontend): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create 3 courses and a test user. The test user belongs to 2 of them, while another user
        // belongs to the other course.
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $course1 = $generator->create_course();
        $generator->enrol_user($user->id, $course1->id, 'student');
        $course2 = $generator->create_course();
        $generator->enrol_user($otheruser->id, $course2->id, 'student');
        $course3 = $generator->create_course();
        $generator->enrol_user($user->id, $course3->id, 'student');

        // We need permission to create grades (even though it's a data generator).
        $this->setAdminUser();

        // Set up each course grade.
        foreach ([$course1, $course2, $course3] as $course) {
            // Create an assignment and get its grade item.
            $assign = $generator->create_module('assign', ['course' => $course->id]);
            $modinfo = get_fast_modinfo($course->id);
            $cm = $modinfo->get_cm($assign->cmid);
            $items = grade_get_grade_items_for_activity($cm, true);
            $item = reset($items);

            // Set a grade in the assignment, either for the normal test user or the other user
            // depending on course.
            if ($course === $course2) {
                $userid = $otheruser->id;
            } else {
                $userid = $user->id;
            }
            $generator->create_grade_grade([
                'itemid' => $item->id,
                'userid' => $userid,
                'teamsubmission' => false,
                'attemptnumber' => 0,
                'grade' => 50
            ]);

            // Bodge the final grade so that it needs regrading and is set wrong.
            $course->gradeitemid = $DB->get_field('grade_items', 'id',
                    ['courseid' => $course->id, 'itemtype' => 'course'], MUST_EXIST);
            $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $course->gradeitemid]);
            $DB->set_field('grade_grades', 'finalgrade', 25.0, ['itemid' => $course->gradeitemid]);
        }

        // Construct the overview report and call regrade_all_courses_if_needed.
        $gpr = new \stdClass();
        $report = new \grade_report_overview($user->id, $gpr, '');
        $report->regrade_all_courses_if_needed($frontend);

        // This should have regraded courses 1 and 3, but not 2 (because the user doesn't belong).
        $this->assertEqualsWithDelta(50.0, $DB->get_field('grade_grades', 'finalgrade',
                ['itemid' => $course1->gradeitemid]), 1.0);
        $this->assertEqualsWithDelta(25.0, $DB->get_field('grade_grades', 'finalgrade',
                ['itemid' => $course2->gradeitemid]), 1.0);
        $this->assertEqualsWithDelta(50.0, $DB->get_field('grade_grades', 'finalgrade',
                ['itemid' => $course3->gradeitemid]), 1.0);
    }
}
