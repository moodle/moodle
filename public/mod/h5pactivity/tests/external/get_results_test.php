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
 * External function test for get_results.
 *
 * @package    mod_h5pactivity
 * @category   external
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use mod_h5pactivity\local\manager;
use core_external\external_api;
use externallib_advanced_testcase;
use dml_missing_record_exception;

/**
 * External function test for get_results.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_results_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of get_results.
     *
     * @dataProvider execute_data
     * @param int $enabletracking the activity tracking enable
     * @param int $reviewmode the activity review mode
     * @param string $loginuser the user which calls the webservice
     * @param string|null $participant the user to get the data
     * @param bool $createattempts if the student user has attempts created
     * @param int|null $count the expected number of attempts returned (null for exception)
     */
    public function test_execute(int $enabletracking, int $reviewmode, string $loginuser,
            ?string $participant, bool $createattempts, ?int $count): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => $enabletracking, 'reviewmode' => $reviewmode]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 teacher, 1 student and 1 unenroled user.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'other' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        $attempts = [];

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        if ($createattempts) {
            $user = $users['student'];
            $params = ['cmid' => $cm->id, 'userid' => $user->id];
            $attempts['student'] = $generator->create_content($activity, $params);
        }

        // Create another 2 attempts for the user "other" to validate no cross attempts are returned.
        $user = $users['other'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $attempts['other'] = $generator->create_content($activity, $params);

        // Execute external method.
        $this->setUser($users[$loginuser]);

        $attemptid = $attempts[$participant]->id ?? 0;

        $result = get_results::execute($activity->id, [$attemptid]);
        $result = external_api::clean_returnvalue(
            get_results::execute_returns(),
            $result
        );

        // Validate general structure.
        $this->assertArrayHasKey('activityid', $result);
        $this->assertArrayHasKey('attempts', $result);
        $this->assertArrayHasKey('warnings', $result);

        $this->assertEquals($activity->id, $result['activityid']);

        if ($count === null) {
            $this->assertCount(1, $result['warnings']);
            $this->assertCount(0, $result['attempts']);
            return;
        }

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['attempts']);

        // Validate attempt.
        $attempt = $result['attempts'][0];
        $this->assertEquals($attemptid, $attempt['id']);

        // Validate results.
        $this->assertArrayHasKey('results', $attempt);
        $this->assertCount($count, $attempt['results']);
        foreach ($attempt['results'] as $value) {
            $this->assertEquals($attemptid, $value['attemptid']);
            $this->assertArrayHasKey('subcontent', $value);
            $this->assertArrayHasKey('rawscore', $value);
            $this->assertArrayHasKey('maxscore', $value);
            $this->assertArrayHasKey('duration', $value);
            $this->assertArrayHasKey('track', $value);
            if (isset($value['options'])) {
                foreach ($value['options'] as $option) {
                    $this->assertArrayHasKey('description', $option);
                    $this->assertArrayHasKey('id', $option);
                }
            }
        }
    }

    /**
     * Data provider for the test_execute tests.
     *
     * @return  array
     */
    public static function execute_data(): array {
        return [
            'Teacher reviewing an attempt' => [
                1, manager::REVIEWCOMPLETION, 'editingteacher', 'student', true, 1
            ],
            'Teacher try to review an inexistent attempt' => [
                1, manager::REVIEWCOMPLETION, 'editingteacher', 'student', false, null
            ],
            'Teacher reviewing attempt with student review mode off' => [
                1, manager::REVIEWNONE, 'editingteacher', 'student', true, 1
            ],
            'Student reviewing own attempt' => [
                1, manager::REVIEWCOMPLETION, 'student', 'student', true, 1
            ],
            'Student reviewing an inexistent attempt' => [
                1, manager::REVIEWCOMPLETION, 'student', 'student', false, null
            ],
            'Student reviewing own attempt with review mode off' => [
                1, manager::REVIEWNONE, 'student', 'student', true, null
            ],
            'Student try to stalk other student attempt' => [
                1, manager::REVIEWCOMPLETION, 'student', 'other', false, null
            ],
            'Teacher trying to review an attempt without tracking enabled' => [
                0, manager::REVIEWNONE, 'editingteacher', 'student', true, null
            ],
            'Student trying to review an attempt without tracking enabled' => [
                0, manager::REVIEWNONE, 'editingteacher', 'student', true, null
            ],
            'Student trying to stalk another student attempt without tracking enabled' => [
                0, manager::REVIEWNONE, 'editingteacher', 'student', true, null
            ],
        ];
    }

    /**
     * Test the behaviour of get_results.
     *
     * @dataProvider execute_multipleattempts_data
     * @param string $loginuser the user which calls the webservice
     * @param array $getattempts the attempts to get the data
     * @param array $warnings warnigns expected
     * @param array $reports data expected
     *
     */
    public function test_execute_multipleattempts(string $loginuser,
            array $getattempts, array $warnings, array $reports): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 teacher, 2 student.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student1' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student2' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        $attempts = [];

        // Generate attempts for student 1 and 2.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $user = $users['student1'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $attempts['student1_1'] = $generator->create_content($activity, $params);
        $attempts['student1_2'] = $generator->create_content($activity, $params);

        $user = $users['student2'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $attempts['student2_1'] = $generator->create_content($activity, $params);
        $attempts['student2_2'] = $generator->create_content($activity, $params);

        // Execute external method.
        $this->setUser($users[$loginuser]);

        $attemptids = [];
        foreach ($getattempts as $getattempt) {
            $attemptids[] = $attempts[$getattempt]->id ?? 0;
        }

        $result = get_results::execute($activity->id, $attemptids);
        $result = external_api::clean_returnvalue(
            get_results::execute_returns(),
            $result
        );

        // Validate general structure.
        $this->assertArrayHasKey('activityid', $result);
        $this->assertArrayHasKey('attempts', $result);
        $this->assertArrayHasKey('warnings', $result);

        $this->assertEquals($activity->id, $result['activityid']);

        $this->assertCount(count($warnings), $result['warnings']);
        $this->assertCount(count($reports), $result['attempts']);

        // Validate warnings.
        $expectedwarnings = [];
        foreach ($warnings as $warningattempt) {
            $id = $attempts[$warningattempt]->id ?? 0;
            $expectedwarnings[$id] = $warningattempt;
        }
        foreach ($result['warnings'] as $warning) {
            $this->assertEquals('h5pactivity_attempts', $warning['item']);
            $this->assertEquals(1, $warning['warningcode']);
            $this->assertArrayHasKey($warning['itemid'], $expectedwarnings);
        }

        // Validate attempts.
        $expectedattempts = [];
        foreach ($reports as $expectedattempt) {
            $id = $attempts[$expectedattempt]->id;
            $expectedattempts[$id] = $expectedattempt;
        }
        foreach ($result['attempts'] as $value) {
            $this->assertArrayHasKey($value['id'], $expectedattempts);
        }
    }

    /**
     * Data provider for the test_execute_multipleattempts tests.
     *
     * @return  array
     */
    public static function execute_multipleattempts_data(): array {
        return [
            // Teacher cases.
            'Teacher reviewing students attempts' => [
                'editingteacher', ['student1_1', 'student2_1'], [], ['student1_1', 'student2_1']
            ],
            'Teacher reviewing invalid attempt' => [
                'editingteacher', ['student1_1', 'invalid'], ['invalid'], ['student1_1']
            ],
            'Teacher reviewing empty attempts list' => [
                'editingteacher', [], [], []
            ],
            // Student cases.
            'Student reviewing own students attempts' => [
                'student1', ['student1_1', 'student1_2'], [], ['student1_1', 'student1_2']
            ],
            'Student reviewing invalid attempt' => [
                'student1', ['student1_1', 'invalid'], ['invalid'], ['student1_1']
            ],
            'Student reviewing trying to access another user attempts' => [
                'student1', ['student1_1', 'student2_1'], ['student2_1'], ['student1_1']
            ],
            'Student reviewing empty attempts list' => [
                'student1', [], [], ['student1_1', 'student1_2']
            ],
        ];
    }

    /**
     * Test the behaviour of get_results using mixed activityid.
     *
     * @dataProvider execute_mixactivities_data
     * @param string $activityname the activity name to use
     * @param string $attemptname the attempt name to use
     * @param string $expectedwarnings expected warning attempt
     * @param string $expectedattempt expected result attempt
     *
     */
    public function test_execute_mixactivities(string $activityname, string $attemptname,
            string $expectedwarnings, string $expectedattempt): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create 2 courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Prepare users: 1 teacher, 1 student.
        $user = $this->getDataGenerator()->create_and_enrol($course1, 'student');
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'student');

        // Create our base activity.
        $activity11 = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course1]);
        $manager11 = manager::create_from_instance($activity11);
        $cm11 = $manager11->get_coursemodule();

        // Create a second activity in the same course to check if the retuned attempt is the correct one.
        $activity12 = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course1]);
        $manager12 = manager::create_from_instance($activity12);
        $cm12 = $manager12->get_coursemodule();

        // Create a second activity on a different course.
        $activity21 = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course2]);
        $manager21 = manager::create_from_instance($activity21);
        $cm21 = $manager21->get_coursemodule();

        $activities = [
            '11' => $activity11->id,
            '12' => $activity12->id,
            '21' => $activity21->id,
            'inexistent' => 0,
        ];

        // Generate attempts.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $params = ['cmid' => $cm11->id, 'userid' => $user->id];
        $attempt11 = $generator->create_content($activity11, $params);
        $params = ['cmid' => $cm12->id, 'userid' => $user->id];
        $attempt12 = $generator->create_content($activity12, $params);
        $params = ['cmid' => $cm21->id, 'userid' => $user->id];
        $attempt21 = $generator->create_content($activity21, $params);

        $attempts = [
            '11' => $attempt11->id,
            '12' => $attempt12->id,
            '21' => $attempt21->id,
            'inexistent' => 0,
        ];

        if ($activityname == 'inexistent') {
            $this->expectException(dml_missing_record_exception::class);
        }

        // Execute external method.
        $this->setUser($user);

        $attemptid = $attempts[$attemptname];

        $result = get_results::execute($activities[$activityname], [$attemptid]);
        $result = external_api::clean_returnvalue(
            get_results::execute_returns(),
            $result
        );

        // Validate general structure.
        $this->assertArrayHasKey('activityid', $result);
        $this->assertArrayHasKey('attempts', $result);
        $this->assertArrayHasKey('warnings', $result);

        if (empty($expectedwarnings)) {
            $this->assertEmpty($result['warnings']);
        } else {
            $this->assertEquals('h5pactivity_attempts', $result['warnings'][0]['item']);
            $this->assertEquals(1, $result['warnings'][0]['warningcode']);
            $this->assertEquals($attempts[$expectedwarnings], $result['warnings'][0]['itemid']);
        }

        if (empty($expectedattempt)) {
            $this->assertEmpty($result['attempts']);
        } else {
            $this->assertEquals($attempts[$expectedattempt], $result['attempts'][0]['id']);
        }
    }

    /**
     * Data provider for the test_execute_multipleattempts tests.
     *
     * @return  array
     */
    public static function execute_mixactivities_data(): array {
        return [
            // Teacher cases.
            'Correct activity id' => [
                '11', '11', '', '11'
            ],
            'Wrong activity id' => [
                '21', '11', '11', ''
            ],
            'Inexistent activity id' => [
                'inexistent', '11', '', ''
            ],
            'Inexistent attempt id' => [
                '11', 'inexistent', 'inexistent', ''
            ],
        ];
    }
}
