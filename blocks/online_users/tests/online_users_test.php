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
 * Online users tests
 *
 * @package    block_online_users
 * @category   test
 * @copyright  2015 University of Nottingham <www.nottingham.ac.uk>
 * @author     Barry Oosthuizen <barry.oosthuizen@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_online_users\fetcher;

defined('MOODLE_INTERNAL') || die();

/**
 * Online users testcase
 *
 * @package    block_online_users
 * @category   test
 * @copyright  2015 University of Nottingham <www.nottingham.ac.uk>
 * @author     Barry Oosthuizen <barry.oosthuizen@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_online_users_testcase extends advanced_testcase {

    protected $data;

    /**
     * Tests initial setup.
     *
     * Prepare the site with some courses, groups, users and
     * simulate various recent accesses.
     */
    protected function setUp() {

        // Generate (simulated) recently logged-in users.
        $generator = $this->getDataGenerator()->get_plugin_generator('block_online_users');
        $this->data = $generator->create_logged_in_users();

        // Confirm we have modified the site and requires reset.
        $this->resetAfterTest(true);
    }

    /**
     * Check logged in group 1, 2 & 3 members in course 1 (should be 3, 4 and 0).
     *
     * @param array $data Array of user, course and group objects
     * @param int $now Current Unix timestamp
     * @param int $timetoshowusers The time window (in seconds) to check for the latest logged in users
     */
    public function test_fetcher_course1_group_members() {
        global $CFG;

        $groupid = $this->data['group1']->id;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_course::instance($this->data['course1']->id);
        $courseid = $this->data['course1']->id;
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals(3, $usercount, 'There was a problem counting the number of online users in group 1');
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users in group 1');

        $groupid = $this->data['group2']->id;
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users in group 2');
        $this->assertEquals(4, $usercount, 'There was a problem counting the number of online users in group 2');

        $groupid = $this->data['group3']->id;
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users in group 3');
        $this->assertEquals(0, $usercount, 'There was a problem counting the number of online users in group 3');
    }

    /**
     * Check logged in users in courses 1 & 2 (should be 9 and 0).
     *
     * @param array $data Array of user, course and group objects
     * @param int $now Current Unix timestamp
     * @param int $timetoshowusers The time window (in seconds) to check for the latest logged in users
     */
    public function test_fetcher_courses() {

        global $CFG;

        $currentgroup = null;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_course::instance($this->data['course1']->id);
        $courseid = $this->data['course1']->id;
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, false, $courseid);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users in course 1');
        $this->assertEquals(9, $usercount, 'There was a problem counting the number of online users in course 1');

        $courseid = $this->data['course2']->id;
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, false, $courseid);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users in course 2');
        $this->assertEquals(0, $usercount, 'There was a problem counting the number of online users in course 2');
    }

    /**
     * Check logged in at the site level (should be 12).
     *
     * @param int $now Current Unix timestamp
     * @param int $timetoshowusers The time window (in seconds) to check for the latest logged in users
     */
    public function test_fetcher_sitelevel() {
        global $CFG;

        $currentgroup = null;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_system::instance();
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, true);

        $usercount = $onlineusers->count_users();
        $users = $onlineusers->get_users();
        $this->assertEquals($usercount, count($users), 'There was a problem counting the number of online users at site level');
        $this->assertEquals(12, $usercount, 'There was a problem counting the number of online users at site level');
    }

    /**
     * Check user visibility setting for course group members.
     */
    public function test_user_visibility_course1_group1_members() {
        global $CFG;

        $groupid = $this->data['group1']->id;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_course::instance($this->data['course1']->id);
        $courseid = $this->data['course1']->id;
        $user1 = $this->data['user1'];
        $user2 = $this->data['user2'];
        // Set user2 as logged user.
        $this->setUser($user2);
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(3, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user1 as logged user.
        $this->setUser($user1);
        // Set visibility to 'hide' for user1.
        set_user_preference('block_online_users_uservisibility', 0);
        // Test if the fetcher gets all the users including user1.
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(3, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user2 as logged user.
        $this->setUser($user2);
        // Test if the fetcher gets all the users excluding user1.
        $onlineusers = new fetcher($groupid, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should not be displayed in the online users block.
        $this->assertEquals(2, $usercount);
        $this->assertFalse(array_key_exists($user1->id, $users));
    }

    /**
     * Check user visibility setting at course level.
     */
    public function test_user_visibility_courses() {
        global $CFG;

        $currentgroup = null;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_course::instance($this->data['course1']->id);
        $courseid = $this->data['course1']->id;
        $user1 = $this->data['user1'];
        $user2 = $this->data['user2'];
        // Set user2 as logged user.
        $this->setUser($user2);
        // Test if the fetcher gets all the users including user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(9, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user1 as logged user.
        $this->setUser($user1);
        // Set visibility to 'hide' for user1.
        set_user_preference('block_online_users_uservisibility', 0);
        // Test if the fetcher gets all the users including user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(9, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user2 as logged user.
        $this->setUser($user2);
        // Test if the fetcher gets all the users excluding user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, false, $courseid);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should not be displayed in the online users block.
        $this->assertEquals(8, $usercount);
        $this->assertFalse(array_key_exists($user1->id, $users));
    }

    /**
     * Check user visibility setting at site level.
     */
    public function test_user_visibility_sitelevel() {
        global $CFG;

        $currentgroup = null;
        $now = time();
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        $context = context_system::instance();
        $user1 = $this->data['user1'];
        $user2 = $this->data['user2'];
        // Set user2 as logged user.
        $this->setUser($user2);
        // Test if the fetcher gets all the users including user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, true);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(12, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user1 as logged user.
        $this->setUser($user1);
        // Set visibility to 'hide' for user1.
        set_user_preference('block_online_users_uservisibility', 0);
        // Test if the fetcher gets all the users including user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, true);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should be displayed in the online users block.
        $this->assertEquals(12, $usercount);
        $this->assertTrue(array_key_exists($user1->id, $users));
        // Set user2 as logged user.
        $this->setUser($user2);
        // Test if the fetcher gets all the users excluding user1.
        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $context, true);
        $users = $onlineusers->get_users();
        $usercount = $onlineusers->count_users();
        // User1 should not be displayed in the online users block.
        $this->assertEquals(11, $usercount);
        $this->assertFalse(array_key_exists($user1->id, $users));
    }
}
