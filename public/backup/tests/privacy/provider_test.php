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
 * Privacy provider tests.
 *
 * @package    core_backup
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_backup\privacy;

use core_backup\privacy\provider;
use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $context = \context_course::instance($course->id);
        $this->assertEquals($context->id, $contextforuser->id);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata1 = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user1->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata1);

        // Create another user who will perform a backup operation.
        $user2 = $this->getDataGenerator()->create_user();
        $bcdata2 = (object) [
            'backupid' => 2,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user2->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata2);

        // Create another backup_controllers record.
        $bcdata3 = (object) [
            'backupid' => 3,
            'operation' => 'backup',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user1->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time() + DAYSECS,
            'timemodified' => time() + DAYSECS,
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata3);

        $coursecontext = \context_course::instance($course->id);

        // Export all of the data for the context.
        $this->export_context_data_for_user($user1->id, $coursecontext, 'core_backup');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext);
        $this->assertTrue($writer->has_any_data());

        $data = (array) $writer->get_data([get_string('backup'), $course->id]);

        $this->assertCount(2, $data);

        $bc1 = array_shift($data);
        $this->assertEquals('restore', $bc1['operation']);

        $bc2 = array_shift($data);
        $this->assertEquals('backup', $bc2['operation']);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata1 = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user1->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata1);

        // Create another user who will perform a backup operation.
        $user2 = $this->getDataGenerator()->create_user();
        $bcdata2 = (object) [
            'backupid' => 2,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user2->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata2);

        // Before deletion, we should have 2 operations.
        $count = $DB->count_records('backup_controllers', ['itemid' => $course->id]);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $coursecontext = \context_course::instance($course->id);
        provider::delete_data_for_all_users_in_context($coursecontext);

        // After deletion, the operations for that course should have been deleted.
        $count = $DB->count_records('backup_controllers', ['itemid' => $course->id]);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata1 = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user1->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata1);

        // Create another user who will perform a backup operation.
        $user2 = $this->getDataGenerator()->create_user();
        $bcdata2 = (object) [
            'backupid' => 2,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user2->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata2);

        // Before deletion, we should have 2 operations.
        $count = $DB->count_records('backup_controllers', ['itemid' => $course->id]);
        $this->assertEquals(2, $count);

        $coursecontext = \context_course::instance($course->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_backup',
            [\context_system::instance()->id, $coursecontext->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the backup operation for the user should have been deleted.
        $count = $DB->count_records('backup_controllers', ['itemid' => $course->id, 'userid' => $user1->id]);
        $this->assertEquals(0, $count);

        // Confirm we still have the other users record.
        $bcs = $DB->get_records('backup_controllers');
        $this->assertCount(1, $bcs);
        $lastsubmission = reset($bcs);
        $this->assertNotEquals($user1->id, $lastsubmission->userid);
    }

    /**
     * Test that only users with a course and module context are fetched.
     */
    public function test_get_users_in_context(): void {
        global $DB;

        $this->resetAfterTest();

        $component = 'core_backup';

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $coursecontext = \context_course::instance($course->id);
        $activitycontext = \context_module::instance($activity->cmid);

        // The list of users for course context should return the user.
        $userlist = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create a course backup.
        // Just insert directly into the 'backup_controllers' table.
        $bcdata = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];

        $DB->insert_record('backup_controllers', $bcdata);

        // The list of users for the course context should return user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // Create an activity backup.
        // Just insert directly into the 'backup_controllers' table.
        $bcdata = (object) [
            'backupid' => 2,
            'operation' => 'restore',
            'type' => 'activity',
            'itemid' => $activity->cmid,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user2->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];

        $DB->insert_record('backup_controllers', $bcdata);

        // The list of users for the course context should return user2.
        $userlist = new \core_privacy\local\request\userlist($activitycontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user2->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $systemcontext = \context_system::instance();
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users(): void {
        global $DB;

        $this->resetAfterTest();

        $component = 'core_backup';

        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = \context_course::instance($course2->id);
        // Create an activity.
        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $activitycontext = \context_module::instance($activity->cmid);
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user3 = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata1 = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $course1->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user1->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata1);

        // Just insert directly into the 'backup_controllers' table.
        $bcdata2 = (object) [
            'backupid' => 2,
            'operation' => 'backup',
            'type' => 'course',
            'itemid' => $course1->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user2->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata2);

        // Just insert directly into the 'backup_controllers' table.
        $bcdata3 = (object) [
            'backupid' => 3,
            'operation' => 'restore',
            'type' => 'activity',
            'itemid' => $activity->cmid,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $user3->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata3);

        // The list of users for coursecontext should return user1 and user2.
        $userlist1 = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);
        $expected = [$user1->id, $user2->id];
        $actual = $userlist1->get_userids();
        $this->assertEqualsCanonicalizing($expected, $actual);

        // The list of users for coursecontext2 should not return users.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        // The list of users for activitycontext should return user3.
        $userlist3 = new \core_privacy\local\request\userlist($activitycontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(1, $userlist3);
        $expected = [$user3->id];
        $actual = $userlist3->get_userids();
        $this->assertEquals($expected, $actual);

        // Add user1 to the approved user list.
        $approvedlist = new approved_userlist($coursecontext, $component, [$user1->id]);
        // Delete user data using delete_data_for_user for usercontext1.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in coursecontext - The user list should now return only user2.
        $userlist1 = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user2->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        // Re-fetch users in activitycontext - The user list should not be empty (user3).
        $userlist3 = new \core_privacy\local\request\userlist($activitycontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(1, $userlist3);

        // Add user1 to the approved user list.
        $approvedlist = new approved_userlist($activitycontext, $component, [$user3->id]);
        // Delete user data using delete_data_for_user for usercontext1.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in activitycontext - The user list should not return any users.
        $userlist3 = new \core_privacy\local\request\userlist($activitycontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // User data should be only removed in the course context and module context.
        $systemcontext = \context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
    }
}
