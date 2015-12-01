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
 * block_online_users data generator
 *
 * @package    block_online_users
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Online users block data generator class
 *
 * @package    block_online_users
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_online_users_generator extends testing_block_generator {

    /**
     * Create (simulated) logged in users and add some of them to groups in a course
     */
    public function create_logged_in_users() {
        global $DB;

        $generator = advanced_testcase::getDataGenerator();
        $data = array();

        // Create 2 courses.
        $course1 = $generator->create_course();
        $data['course1'] = $course1;
        $course2 = $generator->create_course();
        $data['course2'] = $course2;

        // Create 9 (simulated) logged in users enroled into $course1.
        for ($i = 1; $i <= 9; $i++) {
            $user = $generator->create_user();
            $DB->set_field('user', 'lastaccess', time(), array('id' => $user->id));
            $generator->enrol_user($user->id, $course1->id);
            $DB->insert_record('user_lastaccess', array('userid' => $user->id, 'courseid' => $course1->id, 'timeaccess' => time()));
            $data['user' . $i] = $user;
        }
        // Create 3 (simulated) logged in users who are not enroled into $course1.
        for ($i = 10; $i <= 12; $i++) {
            $user = $generator->create_user();
            $DB->set_field('user', 'lastaccess', time(), array('id' => $user->id));
            $data['user' . $i] = $user;
        }

        // Create 3 groups in course 1.
        $group1 = $generator->create_group(array('courseid' => $course1->id));
        $data['group1'] = $group1;
        $group2 = $generator->create_group(array('courseid' => $course1->id));
        $data['group2'] = $group2;
        $group3 = $generator->create_group(array('courseid' => $course1->id));
        $data['group3'] = $group3;

        // Add 3 users to course group 1.
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $data['user1']->id));
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $data['user2']->id));
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $data['user3']->id));

        // Add 4 users to course group 2.
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user3']->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user4']->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user5']->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $data['user6']->id));

        return $data; // Return the user, course and group objects.
    }
}
