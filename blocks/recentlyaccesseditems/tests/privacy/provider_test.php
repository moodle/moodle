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
 * Block recentlyaccesseditems privacy provider tests.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */
namespace block_recentlyaccesseditems\privacy;

defined('MOODLE_INTERNAL') || die();

use block_recentlyaccesseditems\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;

/**
 * Block Recently accessed items privacy provider tests.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);
        $teacher = $generator->create_user();
        $teachercontext = \context_user::instance($teacher->id);

        // Enrol users in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $generator->enrol_user($teacher->id, $course->id, 'teacher');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Check nothing is found before block is populated.
        $contextlist1 = provider::get_contexts_for_userid($student->id);
        $this->assertCount(0, $contextlist1);
        $contextlist2 = provider::get_contexts_for_userid($teacher->id);
        $this->assertCount(0, $contextlist2);

        // Generate some recent activity for both users.
        $this->setUser($student);
        $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                    'objectid' => $forum->id]);
        $event->trigger();

        $this->setUser($teacher);
        $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                    'objectid' => $chat->id]);
        $event->trigger();

        // Ensure provider only fetches the users's own context.
        $contextlist1 = provider::get_contexts_for_userid($student->id);
        $this->assertCount(1, $contextlist1);
        $this->assertEquals($studentcontext, $contextlist1->current());

        $contextlist2 = provider::get_contexts_for_userid($teacher->id);
        $this->assertCount(1, $contextlist2);
        $this->assertEquals($teachercontext, $contextlist2->current());
    }

    /**
     * Test getting users in the context ID related to this plugin.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $component = 'block_recentlyaccesseditems';

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);
        $teacher = $generator->create_user();
        $teachercontext = \context_user::instance($teacher->id);

        // Enrol users in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $generator->enrol_user($teacher->id, $course->id, 'teacher');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Check nothing is found before block is populated.
        $userlist1 = new \core_privacy\local\request\userlist($studentcontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        $userlist2 = new \core_privacy\local\request\userlist($teachercontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        // Generate some recent activity for both users.
        $this->setUser($student);
        $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                    'objectid' => $forum->id]);
        $event->trigger();
        $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                    'objectid' => $chat->id]);
        $event->trigger();

        $this->setUser($teacher);
        $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                    'objectid' => $forum->id]);
        $event->trigger();
        $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                    'objectid' => $chat->id]);
        $event->trigger();

        // Ensure provider only fetches the user whose user context is checked.
        $userlist1 = new \core_privacy\local\request\userlist($studentcontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $this->assertEquals($student, $userlist1->current());

        $userlist2 = new \core_privacy\local\request\userlist($teachercontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertEquals($teacher, $userlist2->current());
    }

    /**
     * Test fetching information about user data stored.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('block_recentlyaccesseditems');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('block_recentlyaccesseditems', $table->get_name());

        $privacyfields = $table->get_privacy_fields();
        $this->assertCount(4, $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('courseid', $privacyfields);
        $this->assertArrayHasKey('cmid', $privacyfields);
        $this->assertArrayHasKey('timeaccess', $privacyfields);

        $this->assertEquals('privacy:metadata:block_recentlyaccesseditemstablesummary', $table->get_summary());
    }

    /**
     * Test exporting data for an approved contextlist.
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $component = 'block_recentlyaccesseditems';

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);

        // Enrol user in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Generate some recent activity.
        $this->setUser($student);
        $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                'objectid' => $forum->id]);
        $event->trigger();
        $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                'objectid' => $chat->id]);
        $event->trigger();

        // Confirm data is present.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);

        // Export data for student.
        $approvedlist = new approved_contextlist($student, $component, [$studentcontext->id]);
        provider::export_user_data($approvedlist);

        // Confirm student's data is exported.
        $writer = \core_privacy\local\request\writer::with_context($studentcontext);
        $this->assertTrue($writer->has_any_data());

        delete_course($course, false);
        $sc = \context_user::instance($student->id);
        $approvedlist = new approved_contextlist($student, $component, [$sc->id]);
        provider::export_user_data($approvedlist);
        $writer = \core_privacy\local\request\writer::with_context($sc);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test exporting data for an approved contextlist with a deleted course
     */
    public function test_export_user_data_with_deleted_course() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $component = 'block_recentlyaccesseditems';

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);

        // Enrol user in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Generate some recent activity.
        $this->setUser($student);
        $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                'objectid' => $forum->id]);
        $event->trigger();
        $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                'objectid' => $chat->id]);
        $event->trigger();

        // Confirm data is present.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
        delete_course($course, false);

        // Export data for student.
        $approvedlist = new approved_contextlist($student, $component, [$studentcontext->id]);
        provider::export_user_data($approvedlist);

        // Confirm student's data is exported.
        $writer = \core_privacy\local\request\writer::with_context($studentcontext);
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test deleting data for all users within an approved contextlist.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);
        $teacher = $generator->create_user();

        // Enrol users in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $generator->enrol_user($teacher->id, $course->id, 'teacher');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Generate some recent activity for both users.
        $users = [$student, $teacher];
        foreach ($users as $user) {
            $this->setUser($user);
            $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                        'objectid' => $forum->id]);
            $event->trigger();
            $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                        'objectid' => $chat->id]);
            $event->trigger();
        }

        // Confirm data is present for both users.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);

        // Attempt system context deletion (should have no effect).
        $systemcontext = \context_system::instance();
        provider::delete_data_for_all_users_in_context($systemcontext);

        $params = ['courseid' => $course->id];
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(4, $result);

        // Delete all data in student context.
        provider::delete_data_for_all_users_in_context($studentcontext);

        // Confirm only student data is deleted.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(0, $result);

        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
    }

    /**
     * Test deleting data within an approved contextlist for a user.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $component = 'block_recentlyaccesseditems';

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);
        $teacher = $generator->create_user();
        $teachercontext = \context_user::instance($teacher->id);

        // Enrol users in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $generator->enrol_user($teacher->id, $course->id, 'teacher');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Generate some recent activity for both users.
        $users = [$student, $teacher];
        foreach ($users as $user) {
            $this->setUser($user);
            $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                        'objectid' => $forum->id]);
            $event->trigger();
            $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                        'objectid' => $chat->id]);
            $event->trigger();
        }

        // Confirm data is present for both users.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);

        // Attempt system context deletion (should have no effect).
        $systemcontext = \context_system::instance();
        $approvedlist = new approved_contextlist($teacher, $component, [$systemcontext->id]);
        provider::delete_data_for_user($approvedlist);

        $params = ['courseid' => $course->id];
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(4, $result);

        // Attempt to delete teacher data in student user context (should have no effect).
        $approvedlist = new approved_contextlist($teacher, $component, [$studentcontext->id]);
        provider::delete_data_for_user($approvedlist);

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(4, $result);

        // Delete teacher data in their own user context.
        $approvedlist = new approved_contextlist($teacher, $component, [$teachercontext->id]);
        provider::delete_data_for_user($approvedlist);

        // Confirm only teacher data is deleted.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);

        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(0, $result);
    }

    /**
     * Test deleting data within a context for an approved userlist.
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $component = 'block_recentlyaccesseditems';

        $student = $generator->create_user();
        $studentcontext = \context_user::instance($student->id);
        $teacher = $generator->create_user();
        $teachercontext = \context_user::instance($teacher->id);

        // Enrol users in course and add course items.
        $course = $generator->create_course();
        $generator->enrol_user($student->id, $course->id, 'student');
        $generator->enrol_user($teacher->id, $course->id, 'teacher');
        $forum = $generator->create_module('forum', ['course' => $course]);
        $chat = $generator->create_module('chat', ['course' => $course]);

        // Generate some recent activity for all users.
        $users = [$student, $teacher];
        foreach ($users as $user) {
            $this->setUser($user);
            $event = \mod_forum\event\course_module_viewed::create(['context' => \context_module::instance($forum->cmid),
                        'objectid' => $forum->id]);
            $event->trigger();
            $event = \mod_chat\event\course_module_viewed::create(['context' => \context_module::instance($chat->cmid),
                        'objectid' => $chat->id]);
            $event->trigger();
        }

        // Confirm data is present for all 3 users.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);

        // Attempt system context deletion (should have no effect).
        $systemcontext = \context_system::instance();
        $approvedlist = new approved_userlist($systemcontext, $component, [$student->id, $teacher->id]);
        provider::delete_data_for_users($approvedlist);

        $params = ['courseid' => $course->id];
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(4, $result);

        // Attempt to delete data in another user's context (should have no effect).
        $approvedlist = new approved_userlist($studentcontext, $component, [$teacher->id]);
        provider::delete_data_for_users($approvedlist);

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(4, $result);

        // Delete users' data in teacher's context.
        $approvedlist = new approved_userlist($teachercontext, $component, [$student->id, $teacher->id]);
        provider::delete_data_for_users($approvedlist);

        // Confirm only teacher data is deleted.
        $params = [
            'courseid' => $course->id,
            'userid' => $student->id,
        ];

        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(2, $result);
        $params['userid'] = $teacher->id;
        $result = $DB->count_records('block_recentlyaccesseditems', $params);
        $this->assertEquals(0, $result);
    }
}
