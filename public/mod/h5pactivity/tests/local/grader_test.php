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

/**
 * mod_h5pactivity grader tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;

use grade_item;
use stdClass;

/**
 * Grader tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class grader_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');
    }

    /**
     * Test for grade item delete.
     */
    public function test_grade_item_delete(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grader = new grader($activity);

        // Force a user grade.
        $this->generate_fake_attempt($activity, $user, 5, 10);
        $grader->update_grades($user->id);

        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, $user->id);
        $this->assertNotEquals(0, count($gradeinfo->items));
        $this->assertArrayHasKey($user->id, $gradeinfo->items[0]->grades);

        $grader->grade_item_delete();

        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, $user->id);
        $this->assertEquals(0, count($gradeinfo->items));
    }

    /**
     * Test for grade item update.
     *
     * @dataProvider grade_item_update_data
     * @param int $newgrade new activity grade
     * @param bool $reset if has to reset grades
     * @param string $idnumber the new idnumber
     */
    public function test_grade_item_update(int $newgrade, bool $reset, string $idnumber): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Force a user initial grade.
        $grader = new grader($activity);
        $this->generate_fake_attempt($activity, $user, 5, 10);
        $grader->update_grades($user->id);

        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, $user->id);
        $this->assertNotEquals(0, count($gradeinfo->items));
        $item = array_shift($gradeinfo->items);
        $this->assertArrayHasKey($user->id, $item->grades);
        $this->assertEquals(50, round($item->grades[$user->id]->grade));

        // Module grade value determine the way gradebook acts. That means that the expected
        // result depends on this value.
        // - Grade > 0: regular max grade value.
        // - Grade = 0: no grading is used (but grademax remains the same).
        // - Grade < 0: a scaleid is used (value = -scaleid).
        if ($newgrade > 0) {
            $grademax = $newgrade;
            $scaleid = null;
            $usergrade = ($newgrade > 50) ? 50 : $newgrade;
        } else if ($newgrade == 0) {
            $grademax = 100;
            $scaleid = null;
            $usergrade = null; // No user grades expected.
        } else if ($newgrade < 0) {
            $scale = $this->getDataGenerator()->create_scale(array("scale" => "value1, value2, value3"));
            $newgrade = -1 * $scale->id;
            $grademax = 3;
            $scaleid = $scale->id;
            $usergrade = 3; // 50 value will ve converted to "value 3" on scale.
        }

        // Update grade item.
        $activity->grade = $newgrade;

        // In case a reset is need, usergrade will be empty.
        if ($reset) {
            $param = 'reset';
            $usergrade = null;
        } else {
            // Individual user gradings will be tested as a subcall of update_grades.
            $param = null;
        }

        $grader = new grader($activity, $idnumber);
        $grader->grade_item_update($param);

        // Check new grade item and grades.
        grade_regrade_final_grades($course->id);
        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, $user->id);
        $item = array_shift($gradeinfo->items);
        $this->assertEquals($scaleid, $item->scaleid);
        $this->assertEquals($grademax, $item->grademax);
        $this->assertArrayHasKey($user->id, $item->grades);
        if ($usergrade) {
            $this->assertEquals($usergrade, round($item->grades[$user->id]->grade));
        } else {
            $this->assertEmpty($item->grades[$user->id]->grade);
        }
        if (!empty($idnumber)) {
            $gradeitem = grade_item::fetch(['idnumber' => $idnumber, 'courseid' => $course->id]);
            $this->assertInstanceOf('grade_item', $gradeitem);
        }
    }

    /**
     * Data provider for test_grade_item_update.
     *
     * @return array
     */
    public static function grade_item_update_data(): array {
        return [
            'Change idnumber' => [
                100, false, 'newidnumber'
            ],
            'Increase max grade to 110' => [
                110, false, ''
            ],
            'Decrease max grade to 80' => [
                40, false, ''
            ],
            'Decrease max grade to 40 (less than actual grades)' => [
                40, false, ''
            ],
            'Reset grades' => [
                100, true, ''
            ],
            'Disable grades' => [
                0, false, ''
            ],
            'Use scales' => [
                -1, false, ''
            ],
            'Use scales with reset' => [
                -1, true, ''
            ],
        ];
    }

    /**
     * Test for grade update.
     *
     * @dataProvider update_grades_data
     * @param int $newgrade the new activity grade
     * @param bool $all if has to be applied to all students or just to one
     * @param int $completion 1 all student have the activity completed, 0 one have incompleted
     * @param array $results expected results (user1 grade, user2 grade)
     */
    public function test_update_grades(int $newgrade, bool $all, int $completion, array $results): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Force a user initial grade.
        $grader = new grader($activity);
        $this->generate_fake_attempt($activity, $user1, 5, 10);
        $this->generate_fake_attempt($activity, $user2, 3, 12, $completion);
        $grader->update_grades();

        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, [$user1->id, $user2->id]);
        $this->assertNotEquals(0, count($gradeinfo->items));
        $item = array_shift($gradeinfo->items);
        $this->assertArrayHasKey($user1->id, $item->grades);
        $this->assertArrayHasKey($user2->id, $item->grades);
        $this->assertEquals(50, $item->grades[$user1->id]->grade);
        // Uncompleted attempts does not generate grades.
        if ($completion) {
            $this->assertEquals(25, $item->grades[$user2->id]->grade);
        } else {
            $this->assertNull($item->grades[$user2->id]->grade);

        }

        // Module grade value determine the way gradebook acts. That means that the expected
        // result depends on this value.
        // - Grade > 0: regular max grade value.
        // - Grade <= 0: no grade calculation is used (scale and no grading).
        if ($newgrade < 0) {
            $scale = $this->getDataGenerator()->create_scale(array("scale" => "value1, value2, value3"));
            $activity->grade = -1 * $scale->id;
        } else {
            $activity->grade = $newgrade;
        }

        $userid = ($all) ? 0 : $user1->id;

        $grader = new grader($activity);
        $grader->update_grades($userid);

        // Check new grade item and grades.
        grade_regrade_final_grades($course->id);
        $gradeinfo = grade_get_grades($course->id, 'mod', 'h5pactivity', $activity->id, [$user1->id, $user2->id]);
        $item = array_shift($gradeinfo->items);
        $this->assertArrayHasKey($user1->id, $item->grades);
        $this->assertArrayHasKey($user2->id, $item->grades);
        $this->assertEquals($results[0], $item->grades[$user1->id]->grade);
        $this->assertEquals($results[1], $item->grades[$user2->id]->grade);
    }

    /**
     * Data provider for test_grade_item_update.
     *
     * @return array
     */
    public static function update_grades_data(): array {
        return [
            // Quantitative grade, all attempts completed.
            'Same grademax, all users, all completed' => [
                100, true, 1, [50, 25]
            ],
            'Same grademax, one user, all completed' => [
                100, false, 1, [50, 25]
            ],
            'Increade max, all users, all completed' => [
                200, true, 1, [100, 50]
            ],
            'Increade max, one user, all completed' => [
                200, false, 1, [100, 25]
            ],
            'Decrease max, all users, all completed' => [
                50, true, 1, [25, 12.5]
            ],
            'Decrease max, one user, all completed' => [
                50, false, 1, [25, 25]
            ],
            // Quantitative grade, some attempts not completed.
            'Same grademax, all users, not completed' => [
                100, true, 0, [50, null]
            ],
            'Same grademax, one user, not completed' => [
                100, false, 0, [50, null]
            ],
            'Increade max, all users, not completed' => [
                200, true, 0, [100, null]
            ],
            'Increade max, one user, not completed' => [
                200, false, 0, [100, null]
            ],
            'Decrease max, all users, not completed' => [
                50, true, 0, [25, null]
            ],
            'Decrease max, one user, not completed' => [
                50, false, 0, [25, null]
            ],
            // No grade (no grade will be used).
            'No grade, all users, all completed' => [
                0, true, 1, [null, null]
            ],
            'No grade, one user, all completed' => [
                0, false, 1, [null, null]
            ],
            'No grade, all users, not completed' => [
                0, true, 0, [null, null]
            ],
            'No grade, one user, not completed' => [
                0, false, 0, [null, null]
            ],
            // Scale (grate item will updated but without regrading).
            'Scale, all users, all completed' => [
                -1, true, 1, [3, 3]
            ],
            'Scale, one user, all completed' => [
                -1, false, 1, [3, 3]
            ],
            'Scale, all users, not completed' => [
                -1, true, 0, [3, null]
            ],
            'Scale, one user, not completed' => [
                -1, false, 0, [3, null]
            ],
        ];
    }

    /**
     * Create a fake attempt for a specific user.
     *
     * @param stdClass $activity activity instance record.
     * @param stdClass $user user record
     * @param int $rawscore score obtained
     * @param int $maxscore attempt max score
     * @param int $completion 1 for activity completed, 0 for not completed yet
     * @return stdClass the attempt record
     */
    private function generate_fake_attempt(stdClass $activity, stdClass $user,
            int $rawscore, int $maxscore, int $completion = 1): stdClass {
        global $DB;

        $attempt = (object)[
            'h5pactivityid' => $activity->id,
            'userid' => $user->id,
            'timecreated' => 10,
            'timemodified' => 20,
            'attempt' => 1,
            'rawscore' => $rawscore,
            'maxscore' => $maxscore,
            'duration' => 2,
            'completion' => $completion,
            'success' => 0,
        ];
        $attempt->scaled = $attempt->rawscore / $attempt->maxscore;
        $attempt->id = $DB->insert_record('h5pactivity_attempts', $attempt);
        return $attempt;
    }
}
