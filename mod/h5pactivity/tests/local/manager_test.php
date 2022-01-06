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
     * Test static get_selected_attempt.
     *
     * @dataProvider get_selected_attempt_data
     * @param int $enabletracking if tracking is enabled
     * @param int $gradingmethod new grading method
     * @param int $result the expected result
     */
    public function test_get_selected_attempt(int $enabletracking, int $gradingmethod, int $result) {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking, 'grademethod' => $gradingmethod]);

        $manager = manager::create_from_instance($activity);

        $selected = $manager->get_selected_attempt();

        $this->assertEquals($result, $selected[0]);
        $this->assertNotEmpty($selected[1]);
    }

    /**
     * Data provider for get_users_scaled_score.
     *
     * @return array
     */
    public function get_selected_attempt_data(): array {
        return [
            'Tracking with max attempt method' => [
                1, manager::GRADEHIGHESTATTEMPT, manager::GRADEHIGHESTATTEMPT
            ],
            'Tracking with average attempt method' => [
                1, manager::GRADEAVERAGEATTEMPT, manager::GRADEAVERAGEATTEMPT
            ],
            'Tracking with last attempt method' => [
                1, manager::GRADELASTATTEMPT, manager::GRADELASTATTEMPT
            ],
            'Tracking with first attempt method' => [
                1, manager::GRADEFIRSTATTEMPT, manager::GRADEFIRSTATTEMPT
            ],
            'Tracking with manual attempt grading' => [
                1, manager::GRADEMANUAL, manager::GRADEMANUAL
            ],
            'No tracking with max attempt method' => [
                0, manager::GRADEHIGHESTATTEMPT, manager::GRADEMANUAL
            ],
            'No tracking with average attempt method' => [
                0, manager::GRADEAVERAGEATTEMPT, manager::GRADEMANUAL
            ],
            'No tracking with last attempt method' => [
                0, manager::GRADELASTATTEMPT, manager::GRADEMANUAL
            ],
            'No tracking with first attempt method' => [
                0, manager::GRADEFIRSTATTEMPT, manager::GRADEMANUAL
            ],
            'No tracking with manual attempt grading' => [
                0, manager::GRADEMANUAL, manager::GRADEMANUAL
            ],
        ];
    }

    /**
     * Test static get_review_modes.
     */
    public function test_get_review_modes() {
        $methods = manager::get_review_modes();
        $this->assertCount(2, $methods);
        $this->assertNotEmpty($methods[manager::REVIEWCOMPLETION]);
        $this->assertNotEmpty($methods[manager::REVIEWNONE]);
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
     * Test static can_view_all_attempts.
     *
     * @dataProvider can_view_all_attempts_data
     * @param int $enabletracking if tracking is enabled
     * @param bool $usestudent if test must be done with a user role
     * @param bool $useloggedin if test must be done with the loggedin user
     * @param bool $result the expected result
     */
    public function test_can_view_all_attempts(int $enabletracking, bool $usestudent, bool $useloggedin, bool $result) {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking]);

        $manager = manager::create_from_instance($activity);

        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $loggedin = $USER;

        // We want to test what when the method is called to check a different user than $USER.
        if (!$usestudent) {
            $loggedin = $user;
            $user = $USER;
        }

        if ($useloggedin) {
            $this->setUser($user);
            $user = null;
        } else {
            $this->setUser($loggedin);
        }

        $this->assertEquals($result, $manager->can_view_all_attempts($user));
    }

    /**
     * Data provider for test_can_view_all_attempts.
     *
     * @return array
     */
    public function can_view_all_attempts_data(): array {
        return [
            // No tracking cases.
            'No tracking with admin using $USER' => [
                0, false, false, false
            ],
            'No tracking with student using $USER' => [
                0, true, false, false
            ],
            'No tracking with admin loggedin' => [
                0, false, true, false
            ],
            'No tracking with student loggedin' => [
                0, true, true, false
            ],
            // Tracking enabled cases.
            'Tracking with admin using $USER' => [
                1, false, false, true
            ],
            'Tracking with student using $USER' => [
                1, true, false, false
            ],
            'Tracking with admin loggedin' => [
                1, false, true, true
            ],
            'Tracking with student loggedin' => [
                1, true, true, false
            ],
        ];
    }

    /**
     * Test static can_view_own_attempts.
     *
     * @dataProvider can_view_own_attempts_data
     * @param int $enabletracking if tracking is enabled
     * @param int $reviewmode the attempt review mode
     * @param bool $useloggedin if test must be done with the loggedin user
     * @param bool $hasattempts if the student have attempts
     * @param bool $result the expected result
     */
    public function test_can_view_own_attempts(int $enabletracking, int $reviewmode,
            bool $useloggedin, bool $hasattempts, bool $result) {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking, 'reviewmode' => $reviewmode]);

        $manager = manager::create_from_instance($activity);

        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        if ($hasattempts) {
            $this->generate_fake_attempts($activity, $user, 1);
        }

        if ($useloggedin) {
            $this->setUser($user);
            $user = null;
        }

        $this->assertEquals($result, $manager->can_view_own_attempts($user));
    }

    /**
     * Data provider for test_can_view_own_attempts.
     *
     * @return array
     */
    public function can_view_own_attempts_data(): array {
        return [
            // No tracking cases.
            'No tracking, review none, using $USER, without attempts' => [
                0, manager::REVIEWNONE, false, false, false
            ],
            'No tracking, review enabled, using $USER, without attempts' => [
                0, manager::REVIEWCOMPLETION, false, false, false
            ],
            'No tracking, review none, loggedin, without attempts' => [
                0, manager::REVIEWNONE, true, false, false
            ],
            'No tracking, review enabled, loggedin, without attempts' => [
                0, manager::REVIEWCOMPLETION, true, false, false
            ],
            'No tracking, review none, using $USER, with attempts' => [
                0, manager::REVIEWNONE, false, true, false
            ],
            'No tracking, review enabled, using $USER, with attempts' => [
                0, manager::REVIEWCOMPLETION, false, true, false
            ],
            'No tracking, review none, loggedin, with attempts' => [
                0, manager::REVIEWNONE, true, true, false
            ],
            'No tracking, review enabled, loggedin, with attempts' => [
                0, manager::REVIEWCOMPLETION, true, true, false
            ],
            // Tracking enabled cases.
            'Tracking enabled, review none, using $USER, without attempts' => [
                1, manager::REVIEWNONE, false, false, false
            ],
            'Tracking enabled, review enabled, using $USER, without attempts' => [
                1, manager::REVIEWCOMPLETION, false, false, true
            ],
            'Tracking enabled, review none, loggedin, without attempts' => [
                1, manager::REVIEWNONE, true, false, false
            ],
            'Tracking enabled, review enabled, loggedin, without attempts' => [
                1, manager::REVIEWCOMPLETION, true, false, true
            ],
            'Tracking enabled, review none, using $USER, with attempts' => [
                1, manager::REVIEWNONE, false, true, false
            ],
            'Tracking enabled, review enabled, using $USER, with attempts' => [
                1, manager::REVIEWCOMPLETION, false, true, true
            ],
            'Tracking enabled, review none, loggedin, with attempts' => [
                1, manager::REVIEWNONE, true, true, false
            ],
            'Tracking enabled, review enabled, loggedin, with attempts' => [
                1, manager::REVIEWCOMPLETION, true, true, true
            ],
        ];
    }

    /**
     * Test static count_attempts of one user.
     */
    public function test_count_attempts() {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course]);

        $manager = manager::create_from_instance($activity);

        // User without attempts.
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // User with 1 attempt.
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user2, 1);

        // User with 2 attempts.
        $user3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user3, 1);

        // Incomplete user2 and 3 has only 3 attempts completed.
        $this->assertEquals(0, $manager->count_attempts($user1->id));
        $this->assertEquals(3, $manager->count_attempts($user2->id));
        $this->assertEquals(3, $manager->count_attempts($user3->id));
    }

    /**
     * Test static count_attempts of all active participants.
     *
     * @dataProvider count_attempts_all_data
     * @param bool $canview if the student role has mod_h5pactivity/view capability
     * @param bool $cansubmit if the student role has mod_h5pactivity/submit capability
     * @param bool $extrarole if an extra role without submit capability is required
     * @param int $result the expected result
     */
    public function test_count_attempts_all(bool $canview, bool $cansubmit, bool $extrarole, int $result) {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course]
        );

        $manager = manager::create_from_instance($activity);

        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student']);

        $newcap = ($canview) ? CAP_ALLOW : CAP_PROHIBIT;
        role_change_permission($roleid, $manager->get_context(), 'mod/h5pactivity:view', $newcap);

        $newcap = ($cansubmit) ? CAP_ALLOW : CAP_PROHIBIT;
        role_change_permission($roleid, $manager->get_context(), 'mod/h5pactivity:submit', $newcap);

        // Teacher with review capability and attempts (should not be listed).
        if ($extrarole) {
            $user1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
            $this->generate_fake_attempts($activity, $user1, 1);
        }

        // Student with attempts.
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user2, 1);

        // Another student with attempts.
        $user3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user3, 1);

        $this->assertEquals($result, $manager->count_attempts());
    }

    /**
     * Data provider for test_count_attempts_all.
     *
     * @return array
     */
    public function count_attempts_all_data(): array {
        return [
            'Students with both view and submit capability' => [true, true, false, 6],
            'Students without view but with submit capability' => [false, true, false, 0],
            'Students with view but without submit capability' => [true, false, false, 6],
            'Students without both view and submit capability' => [false, false, false, 0],
            'Students with both view and submit capability and extra role' => [true, true, true, 6],
            'Students without view but with submit capability and extra role' => [false, true, true, 0],
            'Students with view but without submit capability and extra role' => [true, false, true, 6],
            'Students without both view and submit capability and extra role' => [false, false, true, 0],
        ];
    }

    /**
     * Test static count_attempts of all active participants.
     *
     * Most method scenarios are tested in test_count_attempts_all so we only
     * need to test the with $allpotentialusers true and false.
     *
     * @dataProvider get_active_users_join_data
     * @param bool $allpotentialusers if the join should return all potential users or only the submitted ones.
     * @param int $result the expected result
     */
    public function test_get_active_users_join(bool $allpotentialusers, int $result) {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course]
        );

        $manager = manager::create_from_instance($activity);

        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->generate_fake_attempts($activity, $user1, 1);

        // Student with attempts.
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user2, 1);

        // 2 more students without attempts.
        $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_and_enrol($course, 'student');

        $usersjoin = $manager->get_active_users_join($allpotentialusers);

        // Final SQL.
        $num = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT u.id)
               FROM {user} u $usersjoin->joins
              WHERE $usersjoin->wheres",
            array_merge($usersjoin->params)
        );

        $this->assertEquals($result, $num);
    }

    /**
     * Data provider for test_get_active_users_join.
     *
     * @return array
     */
    public function get_active_users_join_data(): array {
        return [
            'All potential users' => [
                'allpotentialusers' => true,
                'result' => 3,
            ],
            'Users with attempts' => [
                'allpotentialusers' => false,
                'result' => 1,
            ],
        ];
    }

    /**
     * Test static count_attempts.
     */
    public function test_count_users_attempts() {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course]);

        $manager = manager::create_from_instance($activity);

        // User without attempts.
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // User with 1 attempt.
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user2, 1);

        // User with 2 attempts.
        $user3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->generate_fake_attempts($activity, $user3, 1);

        $attempts = $manager->count_users_attempts();
        $this->assertArrayNotHasKey($user1->id, $attempts);
        $this->assertArrayHasKey($user2->id, $attempts);
        $this->assertEquals(4, $attempts[$user2->id]);
        $this->assertArrayHasKey($user3->id, $attempts);
        $this->assertEquals(4, $attempts[$user3->id]);
    }

    /**
     * Test static get_report.
     *
     * @dataProvider get_report_data
     * @param int $enabletracking if tracking is enabled
     * @param int $reviewmode the attempt review mode
     * @param bool $createattempts if the student have attempts
     * @param string $role the user role (student or editingteacher)
     * @param array $results the expected classname (or null)
     */
    public function test_get_report(int $enabletracking, int $reviewmode, bool $createattempts,
            string $role, array $results) {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking, 'reviewmode' => $reviewmode]);

        $manager = manager::create_from_instance($activity);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);

        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'otheruser' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        $attempts = [];
        if ($createattempts) {
            $this->generate_fake_attempts($activity, $users['student'], 1);
            $this->generate_fake_attempts($activity, $users['otheruser'], 2);
            $attempts['student'] = attempt::last_attempt($users['student'], $cm);
            $attempts['otheruser'] = attempt::last_attempt($users['otheruser'], $cm);
        }

        $classnamebase = 'mod_h5pactivity\\local\\report\\';

        $attemptid = null;
        if (isset($attempts['student'])) {
            $attemptid = $attempts['student']->get_id() ?? null;
        }
        $userid = $users['student']->id;

        // Check reports.
        $this->setUser($users[$role]);

        $report = $manager->get_report(null, null);
        if ($results[0] === null) {
            $this->assertNull($report);
        } else {
            $this->assertEquals($classnamebase.$results[0], get_class($report));
        }

        $report = $manager->get_report($userid, null);
        if ($results[1] === null) {
            $this->assertNull($report);
        } else {
            $this->assertEquals($classnamebase.$results[1], get_class($report));
        }

        $report = $manager->get_report($userid, $attemptid);
        if ($results[2] === null) {
            $this->assertNull($report);
        } else {
            $this->assertEquals($classnamebase.$results[2], get_class($report));
        }

        // Check that student cannot access another student reports.
        if ($role == 'student') {
            $attemptid = null;
            if (isset($attempts['otheruser'])) {
                $attemptid = $attempts['otheruser']->get_id() ?? null;
            }
            $userid = $users['otheruser']->id;

            $report = $manager->get_report($userid, null);
            $this->assertNull($report);

            $report = $manager->get_report($userid, $attemptid);
            $this->assertNull($report);
        }
    }

    /**
     * Data provider for test_get_report.
     *
     * @return array
     */
    public function get_report_data(): array {
        return [
            // No tracking scenarios.
            'No tracking, review none, no attempts, teacher' => [
                0, manager::REVIEWNONE, false, 'editingteacher', [null, null, null]
            ],
            'No tracking, review own, no attempts, teacher' => [
                0, manager::REVIEWCOMPLETION, false, 'editingteacher', [null, null, null]
            ],
            'No tracking, review none, no attempts, student' => [
                0, manager::REVIEWNONE, false, 'student', [null, null, null]
            ],
            'No tracking, review own, no attempts, student' => [
                0, manager::REVIEWCOMPLETION, false, 'student', [null, null, null]
            ],
            'No tracking, review none, with attempts, teacher' => [
                0, manager::REVIEWNONE, true, 'editingteacher', [null, null, null]
            ],
            'No tracking, review own, with attempts, teacher' => [
                0, manager::REVIEWCOMPLETION, true, 'editingteacher', [null, null, null]
            ],
            'No tracking, review none, with attempts, student' => [
                0, manager::REVIEWNONE, true, 'student', [null, null, null]
            ],
            'No tracking, review own, with attempts, student' => [
                0, manager::REVIEWCOMPLETION, true, 'student', [null, null, null]
            ],
            // Tracking enabled scenarios.
            'Tracking enabled, review none, no attempts, teacher' => [
                1, manager::REVIEWNONE, false, 'editingteacher', ['participants', 'attempts', 'attempts']
            ],
            'Tracking enabled, review own, no attempts, teacher' => [
                1, manager::REVIEWCOMPLETION, false, 'editingteacher', ['participants', 'attempts', 'attempts']
            ],
            'Tracking enabled, review none, no attempts, student' => [
                1, manager::REVIEWNONE, false, 'student', [null, null, null]
            ],
            'Tracking enabled, review own, no attempts, student' => [
                1, manager::REVIEWCOMPLETION, false, 'student', ['attempts', 'attempts', 'attempts']
            ],
            'Tracking enabled, review none, with attempts, teacher' => [
                1, manager::REVIEWNONE, true, 'editingteacher', ['participants', 'attempts', 'results']
            ],
            'Tracking enabled, review own, with attempts, teacher' => [
                1, manager::REVIEWCOMPLETION, true, 'editingteacher', ['participants', 'attempts', 'results']
            ],
            'Tracking enabled, review none, with attempts, student' => [
                1, manager::REVIEWNONE, true, 'student', [null, null, null]
            ],
            'Tracking enabled, review own, with attempts, student' => [
                1, manager::REVIEWCOMPLETION, true, 'student', ['attempts', 'attempts', 'results']
            ],
        ];
    }

    /**
     * Test get_attempt method.
     *
     * @dataProvider get_attempt_data
     * @param string $attemptname the attempt to use
     * @param string|null $result the expected attempt ID or null for none
     */
    public function test_get_attempt(string $attemptname, ?string $result): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);

        $otheractivity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $othercm = get_coursemodule_from_id('h5pactivity', $otheractivity->cmid, 0, false, MUST_EXIST);

        $manager = manager::create_from_instance($activity);

        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $attempts = ['inexistent' => 0];

        $this->generate_fake_attempts($activity, $user, 1);
        $attempt = attempt::last_attempt($user, $cm);
        $attempts['current'] = $attempt->get_id();

        $this->generate_fake_attempts($otheractivity, $user, 1);
        $attempt = attempt::last_attempt($user, $othercm);
        $attempts['other'] = $attempt->get_id();

        $attempt = $manager->get_attempt($attempts[$attemptname]);
        if ($result === null) {
            $this->assertNull($attempt);
        } else {
            $this->assertEquals($attempts[$attemptname], $attempt->get_id());
            $this->assertEquals($activity->id, $attempt->get_h5pactivityid());
            $this->assertEquals($user->id, $attempt->get_userid());
            $this->assertEquals(4, $attempt->get_attempt());
        }
    }

    /**
     * Data provider for test_get_attempt.
     *
     * @return array
     */
    public function get_attempt_data(): array {
        return [
            'Get the current activity attempt' => [
                'current', 'current'
            ],
            'Try to get another activity attempt' => [
                'other', null
            ],
            'Try to get an inexistent attempt' => [
                'inexistent', null
            ],
        ];
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
