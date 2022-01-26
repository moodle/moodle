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
 * External function test for get_user_attempts.
 *
 * @package    mod_h5pactivity
 * @category   external
 * @since      Moodle 3.11
 * @copyright  2020 Ilya Tregubov <ilya@moodle.com>
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
 * External function test for get_user_attempts.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_user_attempts_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of get_user_attempts getting more than one user at once.
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

        $users = ['editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher')];

        // Prepare users.
        foreach ($participants as $participant) {
            if ($participant == 'noenrolled') {
                $users[$participant] = $this->getDataGenerator()->create_user();
            } else {
                $users[$participant] = $this->getDataGenerator()->create_and_enrol($course, 'student');
            }
        }

        // Generate attempts (student1 with 1 attempt, student2 with 2 etc).
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $attemptcount = 1;
        foreach ($users as $key => $user) {
            if (($key == 'noattempts') || ($key == 'noenrolled') || ($key == 'editingteacher')) {
                $countattempts[$user->id] = 0;
            } else {
                $params = ['cmid' => $cm->id, 'userid' => $user->id];
                for ($i = 1; $i <= $attemptcount; $i++) {
                    $generator->create_content($activity, $params);
                }
                $countattempts[$user->id] = $attemptcount;
                $attemptcount++;
            }
        }

        // Execute external method.
        $this->setUser($users[$loginuser]);

        if ($loginuser == 'student1') {
            $this->expectException('moodle_exception');
            $this->expectExceptionMessage('h5pactivity:reviewattempts required view attempts' .
                ' of all enrolled users');
        }
        $result = get_user_attempts::execute($activity->id);
        $result = external_api::clean_returnvalue(
            get_user_attempts::execute_returns(),
            $result
        );

        $this->assertCount(count($warnings), $result['warnings']);
        // Teacher is excluded.
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
                'editingteacher',
                ['student1', 'student2', 'student3', 'student4', 'student5'],
                [],
                ['student1', 'student2', 'student3', 'student4', 'student5'],
            ],
            'Teacher checking 2 students with atempts and one not' => [
                'editingteacher',
                ['student1', 'student2', 'noattempts'],
                [],
                ['student1', 'student2', 'noattempts'],
            ],
            'Teacher checking no students' => [
                'editingteacher',
                [],
                [],
                [],
            ],
            'Teacher checking one student and a no enrolled user' => [
                'editingteacher',
                ['student1', 'noenrolled'],
                [],
                ['student1'],
            ],

            // Permission check.
            'Student checking attempts and another user' => [
                'student1',
                ['student1', 'student2'],
                ['student2'],
                ['student1'],
            ],
        ];
    }

    /**
     * Data provider for {@see test_execute_with_sortorder}
     *
     * @return array[]
     */
    public function execute_with_sortorder(): array {
        return [
            'Sort by id' => ['id', ['user01', 'user02']],
            'Sort by id desc' => ['id desc', ['user02', 'user01']],
            'Sort by id asc' => ['id asc', ['user01', 'user02']],
            'Sort by firstname' => ['firstname', ['user01', 'user02']],
            'Sort by firstname desc' => ['firstname desc', ['user02', 'user01']],
            'Sort by firstname asc' => ['firstname asc', ['user01', 'user02']],
            'Sort by lastname' => ['lastname', ['user02', 'user01']],
            'Sort by lastname desc' => ['lastname desc', ['user01', 'user02']],
            'Sort by lastname asc' => ['lastname asc', ['user02', 'user01']],
            // Edge cases (should fall back to default).
            'Sort by empty string' => ['', ['user01', 'user02']],
            'Sort by invalid field' => ['invalid', ['user01', 'user02']],
        ];
    }

    /**
     * Test external execute method with sortorder
     *
     * @param string $sortorder
     * @param string[] $expectedorder
     *
     * @dataProvider execute_with_sortorder
     */
    public function test_execute_with_sortorder(string $sortorder, array $expectedorder): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course, module.
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);

        // Couple of enrolled users in the course.
        $users['user01'] = $this->getDataGenerator()->create_and_enrol($course, 'student', [
            'username' => 'user01',
            'firstname' => 'Adam',
            'lastname' => 'Zebra',
        ]);
        $users['user02'] = $this->getDataGenerator()->create_and_enrol($course, 'student', [
            'username' => 'user02',
            'firstname' => 'Zoe',
            'lastname' => 'Apples',
        ]);

        $result = external_api::clean_returnvalue(
            get_user_attempts::execute_returns(),
            get_user_attempts::execute($module->id, $sortorder)
        );

        // Map expected order of usernames to user IDs.
        $expectedorderbyuserid = array_map(static function(string $username) use ($users): int {
            return $users[$username]->id;
        }, $expectedorder);

        // The order should match the ordering of user attempt user IDs.
        $this->assertEquals($expectedorderbyuserid, array_column($result['usersattempts'], 'userid'));
    }
}
