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
 * Unit Tests for the abstract userlist Class
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\userlist_base;

/**
 * Tests for the \core_privacy API's userlist base functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\userlist_base
 */
class userlist_base_test extends advanced_testcase {
    /**
     * Ensure that get_userids returns the list of unique userids.
     *
     * @dataProvider    get_userids_provider
     * @param   array   $input List of user IDs
     * @param   array   $expected list of userids
     * @param   int     $count Expected count
     * @covers ::get_userids
     */
    public function test_get_userids($input, $expected, $count): void {
        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids($input);

        $result = $uut->get_userids();
        $this->assertCount($count, $result);

        // Note: Array order is not guaranteed and should not matter.
        foreach ($expected as $userid) {
            $this->assertNotFalse(array_search($userid, $result));
        }
    }

    /**
     * Provider for the list of userids.
     *
     * @return array
     */
    public static function get_userids_provider(): array {
        return [
            'basic' => [
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
                5,
            ],
            'duplicates' => [
                [1, 1, 2, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
                5,
            ],
            'Mixed order with duplicates' => [
                [5, 4, 2, 5, 4, 1, 3, 4, 1, 5, 5, 5, 2, 4, 1, 2],
                [1, 2, 3, 4, 5],
                5,
            ],
        ];
    }

    /**
     * Ensure that get_users returns the correct list of users.
     *
     * @covers ::get_users
     */
    public function test_get_users(): void {
        $this->resetAfterTest();

        $users = [];
        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $otheruser = $this->getDataGenerator()->create_user();

        $ids = array_keys($users);

        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids($ids);

        $result = $uut->get_users();

        sort($users);
        sort($result);

        $this->assertCount(3, $result);
        $this->assertEquals($users, $result);
    }

    /**
     * Ensure that the userlist_base is countable.
     *
     * @dataProvider    get_userids_provider
     * @param   array   $input List of user IDs
     * @param   array   $expected list of userids
     * @param   int     $count Expected count
     * @covers ::count
     */
    public function test_countable($input, $expected, $count): void {
        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids($input);

        $this->assertCount($count, $uut);
    }

    /**
     * Ensure that the userlist_base iterates over the set of users.
     *
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     */
    public function test_user_iteration(): void {
        $this->resetAfterTest();

        $users = [];
        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $user = $this->getDataGenerator()->create_user();
        $users[$user->id] = $user;

        $otheruser = $this->getDataGenerator()->create_user();

        $ids = array_keys($users);

        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids($ids);

        foreach ($uut as $key => $user) {
            $this->assertTrue(isset($users[$user->id]));
            $this->assertEquals($users[$user->id], $user);
        }
    }

    /**
     * Test that a deleted user is still returned.
     * If a user has data then it still must be deleted, even if they are deleted.
     *
     * @covers ::count
     */
    public function test_current_user_one_user(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids([$user->id]);

        $this->assertCount(1, $uut);
        $this->assertEquals($user, $uut->current());

        delete_user($user);
        $u = $uut->current();
        $this->assertEquals($user->id, $u->id);
    }

    /**
     * Test that an invalid user returns no entry.
     *
     * @covers ::count
     */
    public function test_current_user_invalid(): void {
        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids([-100]);

        $this->assertCount(1, $uut);
        $this->assertNull($uut->current());
    }

    /**
     * Test that where an invalid user is listed, the next user in the list is returned instead.
     *
     * @covers ::count
     */
    public function test_current_user_two_users(): void {
        $this->resetAfterTest();

        $u1 = $this->getDataGenerator()->create_user();

        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $uut->set_userids([-100, $u1->id]);

        $this->assertCount(2, $uut);
        $this->assertEquals($u1, $uut->current());
    }

    /**
     * Ensure that the component specified in the constructor is used and available.
     *
     * @covers ::set_component
     */
    public function test_set_component_in_constructor(): void {
        $uut = new test_userlist_base(\context_system::instance(), 'core_tests');
        $this->assertEquals('core_tests', $uut->get_component());
    }

    /**
     * Ensure that the context specified in the constructor is available.
     *
     * @covers ::__construct
     */
    public function test_set_context_in_constructor(): void {
        $context = \context_user::instance(\core_user::get_user_by_username('admin')->id);

        $uut = new test_userlist_base($context, 'core_tests');
        $this->assertEquals($context, $uut->get_context());
    }
}

/**
 * A test class extending the userlist_base allowing setting of the userids.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_userlist_base extends userlist_base {
    /**
     * Set the contextids for the test class.
     *
     * @param   int[]   $contexids  The list of contextids to use.
     */
    public function set_userids(array $userids): userlist_base {
        return parent::set_userids($userids);
    }
}
