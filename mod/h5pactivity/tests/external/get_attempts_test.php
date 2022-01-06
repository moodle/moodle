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
 * External function test for get_attempts.
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
use external_api;
use externallib_advanced_testcase;

/**
 * External function test for get_attempts.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_attempts_testcase extends externallib_advanced_testcase {

    /**
     * Test the behaviour of get_attempts.
     *
     * @dataProvider execute_data
     * @param int $grademethod the activity grading method
     * @param string $loginuser the user which calls the webservice
     * @param string|null $participant the user to get the data
     * @param bool $createattempts if the student user has attempts created
     * @param int|null $count the expected number of attempts returned (null for exception)
     */
    public function test_execute(int $grademethod, string $loginuser, ?string $participant,
            bool $createattempts, ?int $count): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => 1, 'grademethod' => $grademethod]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 teacher, 2 students, 1 unenroled user.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'other' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'noenrolled' => $this->getDataGenerator()->create_user(),
        ];

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        if ($createattempts) {
            $user = $users['student'];
            $params = ['cmid' => $cm->id, 'userid' => $user->id];
            $generator->create_content($activity, $params);
            $generator->create_content($activity, $params);
        }

        // Create another user with 2 attempts to validate no cross attempts are returned.
        $user = $users['other'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // Execute external method.
        $this->setUser($users[$loginuser]);
        $userids = ($participant) ? [$users[$participant]->id] : [];
        $checkuserid = ($participant) ? $users[$participant]->id : $users[$loginuser]->id;

        $result = get_attempts::execute($activity->id, $userids);
        $result = external_api::clean_returnvalue(
            get_attempts::execute_returns(),
            $result
        );

        // Validate general structure.
        $this->assertArrayHasKey('activityid', $result);
        $this->assertArrayHasKey('usersattempts', $result);
        $this->assertArrayHasKey('warnings', $result);

        $this->assertEquals($activity->id, $result['activityid']);

        if ($count === null) {
            $this->assertCount(1, $result['warnings']);
            $this->assertCount(0, $result['usersattempts']);
            return;
        }

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['usersattempts']);

        $userattempts = $result['usersattempts'][0];
        $this->assertEquals($checkuserid, $userattempts['userid']);

        // Validate scored attempts.
        if ($grademethod == manager::GRADEMANUAL || $grademethod == manager::GRADEAVERAGEATTEMPT || $count == 0) {
            $this->assertArrayNotHasKey('scored', $userattempts);
        } else {
            $this->assertArrayHasKey('scored', $userattempts);
            list($dbgrademethod, $title) = $manager->get_selected_attempt();
            $this->assertEquals($grademethod, $dbgrademethod);
            $this->assertEquals($grademethod, $userattempts['scored']['grademethod']);
            $this->assertEquals($title, $userattempts['scored']['title']);
            $this->assertCount(1, $userattempts['scored']['attempts']);
        }

        // Validate returned attempts.
        $this->assertCount($count, $userattempts['attempts']);
        foreach ($userattempts['attempts'] as $attempt) {
            $this->assertArrayHasKey('id', $attempt);
            $this->assertEquals($checkuserid, $attempt['userid']);
            $this->assertArrayHasKey('timecreated', $attempt);
            $this->assertArrayHasKey('timemodified', $attempt);
            $this->assertArrayHasKey('attempt', $attempt);
            $this->assertArrayHasKey('rawscore', $attempt);
            $this->assertArrayHasKey('maxscore', $attempt);
            $this->assertArrayHasKey('duration', $attempt);
            $this->assertArrayHasKey('completion', $attempt);
            $this->assertArrayHasKey('success', $attempt);
            $this->assertArrayHasKey('scaled', $attempt);
        }
    }

    /**
     * Data provider for the test_execute tests.
     *
     * @return  array
     */
    public function execute_data(): array {
        return [
            // Teacher checking a user with attempts.
            'Manual grade, Teacher asking participant with attempts' => [
                manager::GRADEMANUAL, 'editingteacher', 'student', true, 2
            ],
            'Highest grade, Teacher asking participant with attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'editingteacher', 'student', true, 2
            ],
            'Average grade, Teacher asking participant with attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'editingteacher', 'student', true, 2
            ],
            'Last grade, Teacher asking participant with attempts' => [
                manager::GRADELASTATTEMPT, 'editingteacher', 'student', true, 2
            ],
            'First grade, Teacher asking participant with attempts' => [
                manager::GRADEFIRSTATTEMPT, 'editingteacher', 'student', true, 2
            ],
            // Teacher checking a user without attempts.
            'Manual grade, Teacher asking participant without attempts' => [
                manager::GRADEMANUAL, 'editingteacher', 'student', false, 0
            ],
            'Highest grade, Teacher asking participant without attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'editingteacher', 'student', false, 0
            ],
            'Average grade, Teacher asking participant without attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'editingteacher', 'student', false, 0
            ],
            'Last grade, Teacher asking participant without attempts' => [
                manager::GRADELASTATTEMPT, 'editingteacher', 'student', false, 0
            ],
            'First grade, Teacher asking participant without attempts' => [
                manager::GRADEFIRSTATTEMPT, 'editingteacher', 'student', false, 0
            ],
            // Student checking own attempts specifying userid.
            'Manual grade, check same user attempts report with attempts' => [
                manager::GRADEMANUAL, 'student', 'student', true, 2
            ],
            'Highest grade, check same user attempts report with attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'student', 'student', true, 2
            ],
            'Average grade, check same user attempts report with attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'student', 'student', true, 2
            ],
            'Last grade, check same user attempts report with attempts' => [
                manager::GRADELASTATTEMPT, 'student', 'student', true, 2
            ],
            'First grade, check same user attempts report with attempts' => [
                manager::GRADEFIRSTATTEMPT, 'student', 'student', true, 2
            ],
            // Student checking own attempts.
            'Manual grade, check own attempts report with attempts' => [
                manager::GRADEMANUAL, 'student', null, true, 2
            ],
            'Highest grade, check own attempts report with attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'student', null, true, 2
            ],
            'Average grade, check own attempts report with attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'student', null, true, 2
            ],
            'Last grade, check own attempts report with attempts' => [
                manager::GRADELASTATTEMPT, 'student', null, true, 2
            ],
            'First grade, check own attempts report with attempts' => [
                manager::GRADEFIRSTATTEMPT, 'student', null, true, 2
            ],
            // Student checking own report without attempts.
            'Manual grade, check own attempts report without attempts' => [
                manager::GRADEMANUAL, 'student', 'student', false, 0
            ],
            'Highest grade, check own attempts report without attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'student', 'student', false, 0
            ],
            'Average grade, check own attempts report without attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'student', 'student', false, 0
            ],
            'Last grade, check own attempts report without attempts' => [
                manager::GRADELASTATTEMPT, 'student', 'student', false, 0
            ],
            'First grade, check own attempts report without attempts' => [
                manager::GRADEFIRSTATTEMPT, 'student', 'student', false, 0
            ],
            // Student trying to get another user attempts.
            'Manual grade, student trying to stalk another student' => [
                manager::GRADEMANUAL, 'student', 'other', false, null
            ],
            'Highest grade, student trying to stalk another student' => [
                manager::GRADEHIGHESTATTEMPT, 'student', 'other', false, null
            ],
            'Average grade, student trying to stalk another student' => [
                manager::GRADEAVERAGEATTEMPT, 'student', 'other', false, null
            ],
            'Last grade, student trying to stalk another student' => [
                manager::GRADELASTATTEMPT, 'student', 'other', false, null
            ],
            'First grade, student trying to stalk another student' => [
                manager::GRADEFIRSTATTEMPT, 'student', 'other', false, null
            ],
            // Teacher trying to get a non enroled user attempts.
            'Manual grade, teacher trying to get an non enrolled user attempts' => [
                manager::GRADEMANUAL, 'editingteacher', 'noenrolled', false, null
            ],
            'Highest grade, teacher trying to get an non enrolled user attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'editingteacher', 'noenrolled', false, null
            ],
            'Average grade, teacher trying to get an non enrolled user attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'editingteacher', 'noenrolled', false, null
            ],
            'Last grade, teacher trying to get an non enrolled user attempts' => [
                manager::GRADELASTATTEMPT, 'editingteacher', 'noenrolled', false, null
            ],
            'First grade, teacher trying to get an non enrolled user attempts' => [
                manager::GRADEFIRSTATTEMPT, 'editingteacher', 'noenrolled', false, null
            ],
            // Student trying to get a non enroled user attempts.
            'Manual grade, student trying to get an non enrolled user attempts' => [
                manager::GRADEMANUAL, 'student', 'noenrolled', false, null
            ],
            'Highest grade, student trying to get an non enrolled user attempts' => [
                manager::GRADEHIGHESTATTEMPT, 'student', 'noenrolled', false, null
            ],
            'Average grade, student trying to get an non enrolled user attempts' => [
                manager::GRADEAVERAGEATTEMPT, 'student', 'noenrolled', false, null
            ],
            'Last grade, student trying to get an non enrolled user attempts' => [
                manager::GRADELASTATTEMPT, 'student', 'noenrolled', false, null
            ],
            'First grade, student trying to get an non enrolled user attempts' => [
                manager::GRADEFIRSTATTEMPT, 'student', 'noenrolled', false, null
            ],
        ];
    }

    /**
     * Test the behaviour of get_attempts when tracking is not enabled.
     *
     */
    public function test_execute_no_tracking(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => 0]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 teacher, 1 student.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        // Execute external method.
        $this->setUser($users['editingteacher']);

        $result = get_attempts::execute($activity->id, [$users['student']->id]);
        $result = external_api::clean_returnvalue(
            get_attempts::execute_returns(),
            $result
        );

        $this->assertCount(1, $result['warnings']);
        $this->assertCount(0, $result['usersattempts']);
    }

    /**
     * Test the behaviour of get_attempts when own review is not allowed.
     *
     */
    public function test_execute_no_own_review(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course, 'enabletracking' => 1, 'reviewmode' => manager::REVIEWNONE]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 student.
        $users = [
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        // Execute external method.
        $this->setUser($users['student']);

        $result = get_attempts::execute($activity->id);
        $result = external_api::clean_returnvalue(
            get_attempts::execute_returns(),
            $result
        );

        $this->assertCount(1, $result['warnings']);
        $this->assertCount(0, $result['usersattempts']);
    }

    /**
     * Test the behaviour of get_attempts getting more than one user at once.
     *
     * @dataProvider execute_multipleusers_data
     * @param string $loginuser the user which calls the webservice
     * @param string[] $participants the users to get the data
     * @param string[] $warnings the expected users with warnings
     * @param string[] $resultusers expected users in the resultusers
     */
    public function test_execute_multipleusers(string $loginuser, array $participants,
            array $warnings, array $resultusers): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
                ['course' => $course]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Prepare users: 1 teacher, 2 students with attempts, 1 student without, 1 no enrolled.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student1' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student2' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'noattempts' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'noenrolled' => $this->getDataGenerator()->create_user(),
        ];

        // Generate attempts (student1 with 1 attempt, student2 with 2).
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $user = $users['student1'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);

        $user = $users['student2'];
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        $countattempts = [
            $users['editingteacher']->id => 0,
            $users['student1']->id => 1,
            $users['student2']->id => 2,
            $users['noattempts']->id => 0,
            $users['noenrolled']->id => 0,
        ];

        // Execute external method.
        $this->setUser($users[$loginuser]);

        $userids = [];
        foreach ($participants as $participant) {
            $userids[] = $users[$participant]->id;
        }

        $result = get_attempts::execute($activity->id, $userids);
        $result = external_api::clean_returnvalue(
            get_attempts::execute_returns(),
            $result
        );

        $this->assertCount(count($warnings), $result['warnings']);
        $this->assertCount(count($resultusers), $result['usersattempts']);

        $expectedwarnings = [];
        foreach ($warnings as $warninguser) {
            $id = $users[$warninguser]->id;
            $expectedwarnings[$id] = $warninguser;
        }

        foreach ($result['warnings'] as $warning) {
            $this->assertEquals('user', $warning['item']);
            $this->assertEquals(1, $warning['warningcode']);
            $this->assertArrayHasKey($warning['itemid'], $expectedwarnings);
        }

        $expectedusers = [];
        foreach ($resultusers as $resultuser) {
            $id = $users[$resultuser]->id;
            $expectedusers[$id] = $resultuser;
        }

        foreach ($result['usersattempts'] as $usersattempts) {
            $this->assertArrayHasKey('userid', $usersattempts);
            $userid = $usersattempts['userid'];
            $this->assertArrayHasKey($userid, $expectedusers);
            $this->assertCount($countattempts[$userid], $usersattempts['attempts']);
            if ($countattempts[$userid]) {
                $this->assertArrayHasKey('scored', $usersattempts);
            }
        }
    }

    /**
     * Data provider for the test_execute_multipleusers.
     *
     * @return  array
     */
    public function execute_multipleusers_data(): array {
        return [
            // Teacher checks.
            'Teacher checking students with attempts' => [
                'editingteacher', ['student1', 'student2'], [], ['student1', 'student2']
            ],
            'Teacher checking one student with atempts and one not' => [
                'editingteacher', ['student1', 'noattempts'], [], ['student1', 'noattempts']
            ],
            'Teacher checking no students' => [
                'editingteacher', [], [], ['editingteacher']
            ],
            'Teacher checking one student and a no enrolled user' => [
                'editingteacher', ['student1', 'noenrolled'], ['noenrolled'], ['student1']
            ],
            // Student checks.
            'Student checking self attempts and another user' => [
                'student1', ['student1', 'student2'], ['student2'], ['student1']
            ],
            'Student checking no students' => [
                'student1', [], [], ['student1']
            ],
            'Student checking self attempts and a no enrolled user' => [
                'student1', ['student1', 'noenrolled'], ['noenrolled'], ['student1']
            ],
        ];
    }
}
