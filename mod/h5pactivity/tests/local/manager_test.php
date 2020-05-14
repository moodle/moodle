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
 * mod_h5pactivity manager tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;
use context_module;
use stdClass;

/**
 * Manager tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_testcase extends \advanced_testcase {

    /**
     * Test for static create methods.
     */
    public function test_create() {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $manager = manager::create_from_instance($activity);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($activity->id, $manageractivity->id);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $this->assertEquals($context->id, $managercontext->id);

        $manager = manager::create_from_coursemodule($cm);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($activity->id, $manageractivity->id);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $this->assertEquals($context->id, $managercontext->id);
    }

    /**
     * Test for is_tracking_enabled.
     *
     * @dataProvider is_tracking_enabled_data
     * @param bool $login if the user is logged in
     * @param string $role user role in course
     * @param int $enabletracking if tracking is enabled
     * @param bool $expected expected result
     */
    public function test_is_tracking_enabled(bool $login, string $role, int $enabletracking, bool $expected) {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking]);

        $user = $this->getDataGenerator()->create_and_enrol($course, $role);
        if ($login) {
            $this->setUser($user);
            $param = null;
        } else {
            $param = $user;
        }

        $manager = manager::create_from_instance($activity);
        $this->assertEquals($expected, $manager->is_tracking_enabled($param));
    }

    /**
     * Data provider for is_tracking_enabled.
     *
     * @return array
     */
    public function is_tracking_enabled_data(): array {
        return [
            'Logged student, tracking enabled' => [
                true, 'student', 1, true
            ],
            'Logged student, tracking disabled' => [
                true, 'student', 0, false
            ],
            'Logged teacher, tracking enabled' => [
                true, 'editingteacher', 1, false
            ],
            'Logged teacher, tracking disabled' => [
                true, 'editingteacher', 0, false
            ],
            'No logged student, tracking enabled' => [
                true, 'student', 1, true
            ],
            'No logged student, tracking disabled' => [
                true, 'student', 0, false
            ],
            'No logged teacher, tracking enabled' => [
                true, 'editingteacher', 1, false
            ],
            'No logged teacher, tracking disabled' => [
                true, 'editingteacher', 0, false
            ],
        ];
    }

    /**
     * Test for get_users_scaled_score.
     *
     * @dataProvider get_users_scaled_score_data
     * @param int $enabletracking if tracking is enabled
     * @param int $gradingmethod new grading method
     * @param array $result1 student 1 results (scaled, timemodified, attempt number)
     * @param array $result2 student 2 results (scaled, timemodified, attempt number)
     */
    public function test_get_users_scaled_score(int $enabletracking, int $gradingmethod, array $result1, array $result2) {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking, 'grademethod' => $gradingmethod]);

        // Generate two users with 4 attempts each.
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user1, 1);
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user2, 2);

        $manager = manager::create_from_instance($activity);

        // Get all users scaled scores.
        $scaleds = $manager->get_users_scaled_score();

        // No results will be returned if tracking is dsabled or manual grading method is defined.
        if (empty($result1)) {
            $this->assertNull($scaleds);
            return;
        }

        $this->assertCount(2, $scaleds);

        // Check expected user1 scaled score.
        $scaled = $scaleds[$user1->id];
        $this->assertEquals($user1->id, $scaled->userid);
        $this->assertEquals($result1[0], $scaled->scaled);
        $this->assertEquals($result1[1], $scaled->timemodified);
        if ($result1[2]) {
            $attempt = $DB->get_record('h5pactivity_attempts', ['id' => $scaled->attemptid]);
            $this->assertEquals($attempt->h5pactivityid, $activity->id);
            $this->assertEquals($attempt->userid, $scaled->userid);
            $this->assertEquals($attempt->scaled, round($scaled->scaled, 5));
            $this->assertEquals($attempt->timemodified, $scaled->timemodified);
            $this->assertEquals($result1[2], $attempt->attempt);
        } else {
            $this->assertEquals(0, $scaled->attemptid);
        }

        // Check expected user2 scaled score.
        $scaled = $scaleds[$user2->id];
        $this->assertEquals($user2->id, $scaled->userid);
        $this->assertEquals($result2[0], round($scaled->scaled, 5));
        $this->assertEquals($result2[1], $scaled->timemodified);
        if ($result2[2]) {
            $attempt = $DB->get_record('h5pactivity_attempts', ['id' => $scaled->attemptid]);
            $this->assertEquals($attempt->h5pactivityid, $activity->id);
            $this->assertEquals($attempt->userid, $scaled->userid);
            $this->assertEquals($attempt->scaled, $scaled->scaled);
            $this->assertEquals($attempt->timemodified, $scaled->timemodified);
            $this->assertEquals($result2[2], $attempt->attempt);
        } else {
            $this->assertEquals(0, $scaled->attemptid);
        }

        // Now check a single user record.
        $scaleds = $manager->get_users_scaled_score($user2->id);
        $this->assertCount(1, $scaleds);
        $scaled2 = $scaleds[$user2->id];
        $this->assertEquals($scaled->userid, $scaled2->userid);
        $this->assertEquals($scaled->scaled, $scaled2->scaled);
        $this->assertEquals($scaled->attemptid, $scaled2->attemptid);
        $this->assertEquals($scaled->timemodified, $scaled2->timemodified);
    }

    /**
     * Data provider for get_users_scaled_score.
     *
     * @return array
     */
    public function get_users_scaled_score_data(): array {
        return [
            'Tracking with max attempt method' => [
                1, manager::GRADEHIGHESTATTEMPT, [1.00000, 31, 2], [0.66667, 32, 2]
            ],
            'Tracking with average attempt method' => [
                1, manager::GRADEAVERAGEATTEMPT, [0.61111, 51, 0], [0.52222, 52, 0]
            ],
            'Tracking with last attempt method' => [
                1, manager::GRADELASTATTEMPT, [0.33333, 51, 3], [0.40000, 52, 3]
            ],
            'Tracking with first attempt method' => [
                1, manager::GRADEFIRSTATTEMPT, [0.50000, 11, 1], [0.50000, 12, 1]
            ],
            'Tracking with manual attempt grading' => [
                1, manager::GRADEMANUAL, [], []
            ],
            'No tracking with max attempt method' => [
                0, manager::GRADEHIGHESTATTEMPT, [], []
            ],
            'No tracking with average attempt method' => [
                0, manager::GRADEAVERAGEATTEMPT, [], []
            ],
            'No tracking with last attempt method' => [
                0, manager::GRADELASTATTEMPT, [], []
            ],
            'No tracking with first attempt method' => [
                0, manager::GRADEFIRSTATTEMPT, [], []
            ],
            'No tracking with manual attempt grading' => [
                0, manager::GRADEMANUAL, [], []
            ],
        ];
    }

    /**
     * Test static get_grading_methods.
     */
    public function test_get_grading_methods() {
        $methods = manager::get_grading_methods();
        $this->assertCount(5, $methods);
        $this->assertNotEmpty($methods[manager::GRADEHIGHESTATTEMPT]);
        $this->assertNotEmpty($methods[manager::GRADEAVERAGEATTEMPT]);
        $this->assertNotEmpty($methods[manager::GRADELASTATTEMPT]);
        $this->assertNotEmpty($methods[manager::GRADEFIRSTATTEMPT]);
        $this->assertNotEmpty($methods[manager::GRADEMANUAL]);
    }

    /**
     * Test get_grader method.
     */
    public function test_get_grader() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $manager = manager::create_from_instance($activity);
        $grader = $manager->get_grader();

        $this->assertInstanceOf('mod_h5pactivity\local\grader', $grader);
    }

    /**
     * Insert fake attempt data into h5pactiviyt_attempts.
     *
     * This function insert 4 attempts. 3 of them finished with different gradings
     * and timestamps and 1 unfinished.
     *
     * @param stdClass $activity the activity record
     * @param stdClass $user user record
     * @param int $basescore a score to be used to generate all attempts
     */
    private function generate_fake_attempts(stdClass $activity, stdClass $user, int $basescore) {
        global $DB;

        $attempt = (object)[
            'h5pactivityid' => $activity->id,
            'userid' => $user->id,
            'timecreated' => $basescore,
            'timemodified' => ($basescore + 10),
            'attempt' => 1,
            'rawscore' => $basescore,
            'maxscore' => ($basescore + $basescore),
            'duration' => $basescore,
            'completion' => 1,
            'success' => 1,
        ];
        $attempt->scaled = $attempt->rawscore / $attempt->maxscore;
        $DB->insert_record('h5pactivity_attempts', $attempt);

        $attempt = (object)[
            'h5pactivityid' => $activity->id,
            'userid' => $user->id,
            'timecreated' => ($basescore + 20),
            'timemodified' => ($basescore + 30),
            'attempt' => 2,
            'rawscore' => $basescore,
            'maxscore' => ($basescore + $basescore - 1),
            'duration' => $basescore,
            'completion' => 1,
            'success' => 1,
        ];
        $attempt->scaled = $attempt->rawscore / $attempt->maxscore;
        $DB->insert_record('h5pactivity_attempts', $attempt);

        $attempt = (object)[
            'h5pactivityid' => $activity->id,
            'userid' => $user->id,
            'timecreated' => ($basescore + 40),
            'timemodified' => ($basescore + 50),
            'attempt' => 3,
            'rawscore' => $basescore,
            'maxscore' => ($basescore + $basescore + 1),
            'duration' => $basescore,
            'completion' => 1,
            'success' => 0,
        ];
        $attempt->scaled = $attempt->rawscore / $attempt->maxscore;
        $DB->insert_record('h5pactivity_attempts', $attempt);

        // Unfinished attempt.
        $attempt = (object)[
            'h5pactivityid' => $activity->id,
            'userid' => $user->id,
            'timecreated' => ($basescore + 60),
            'timemodified' => ($basescore + 60),
            'attempt' => 4,
            'rawscore' => $basescore,
            'maxscore' => $basescore,
            'duration' => $basescore,
        ];
        $DB->insert_record('h5pactivity_attempts', $attempt);
    }
}
