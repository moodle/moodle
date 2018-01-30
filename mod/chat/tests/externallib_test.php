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
 * External mod_chat functions unit tests
 *
 * @package    mod_chat
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External mod_chat functions unit tests
 *
 * @package    mod_chat
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_chat_external_testcase extends externallib_advanced_testcase {

    /**
     * Test login user
     */
    public function test_login_user() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);

        // Test session started.
        $sid = $DB->get_field('chat_users', 'sid', array('userid' => $user->id, 'chatid' => $chat->id));
        $this->assertEquals($result['chatsid'], $sid);

    }

    /**
     * Test get chat users
     */
    public function test_get_chat_users() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id);

        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);

        $this->setUser($user2);
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);

        // Get users.
        $result = mod_chat_external::get_chat_users($result['chatsid']);
        $result = external_api::clean_returnvalue(mod_chat_external::get_chat_users_returns(), $result);

        // Check correct users.
        $this->assertCount(2, $result['users']);
        $found = 0;
        foreach ($result['users'] as $user) {
            if ($user['id'] == $user1->id or $user['id'] == $user2->id) {
                $found++;
            }
        }
        $this->assertEquals(2, $found);

    }

    /**
     * Test send and get chat messages
     */
    public function test_send_get_chat_message() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];

        $result = mod_chat_external::send_chat_message($chatsid, 'hello!');
        $result = external_api::clean_returnvalue(mod_chat_external::send_chat_message_returns(), $result);

        // Test messages received.

        $result = mod_chat_external::get_chat_latest_messages($chatsid, 0);
        $result = external_api::clean_returnvalue(mod_chat_external::get_chat_latest_messages_returns(), $result);

        foreach ($result['messages'] as $message) {
            // Ommit system messages, like user just joined in.
            if ($message['system']) {
                continue;
            }
            $this->assertEquals('hello!', $message['message']);
        }
    }

    /**
     * Test view_chat
     */
    public function test_view_chat() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));
        $context = context_module::instance($chat->cmid);
        $cm = get_coursemodule_from_instance('chat', $chat->id);

        // Test invalid instance id.
        try {
            mod_chat_external::view_chat(0);
            $this->fail('Exception expected due to invalid mod_chat instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_chat_external::view_chat($chat->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_chat_external::view_chat($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::view_chat_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_chat\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodlechat = new \moodle_url('/mod/chat/view.php', array('id' => $cm->id));
        $this->assertEquals($moodlechat, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/chat:chat', CAP_PROHIBIT, $studentrole->id, $context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_chat_external::view_chat($chat->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    /**
     * Test get_chats_by_courses
     */
    public function test_get_chats_by_courses() {
        global $DB, $USER, $CFG;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set global chat method.
        $CFG->chat_method = 'header_js';

        $course1 = self::getDataGenerator()->create_course();
        $chatoptions1 = array(
                              'course' => $course1->id,
                              'name' => 'First Chat'
                             );
        $chat1 = self::getDataGenerator()->create_module('chat', $chatoptions1);
        $course2 = self::getDataGenerator()->create_course();
        $chatoptions2 = array(
                              'course' => $course2->id,
                              'name' => 'Second Chat'
                             );
        $chat2 = self::getDataGenerator()->create_module('chat', $chatoptions2);
        $student1 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enroll Student1 in Course1.
        self::getDataGenerator()->enrol_user($student1->id,  $course1->id, $studentrole->id);
        $this->setUser($student1);

        $chats = mod_chat_external::get_chats_by_courses();
        // We need to execute the return values cleaning process to simulate the web service server.
        $chats = external_api::clean_returnvalue(mod_chat_external::get_chats_by_courses_returns(), $chats);
        $this->assertCount(1, $chats['chats']);
        $this->assertEquals('First Chat', $chats['chats'][0]['name']);
        // We see 12 fields.
        $this->assertCount(12, $chats['chats'][0]);

        // As Student you cannot see some chat properties like 'section'.
        $this->assertFalse(isset($chats['chats'][0]['section']));

        // Student1 is not enrolled in course2. The webservice will return a warning!
        $chats = mod_chat_external::get_chats_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $chats = external_api::clean_returnvalue(mod_chat_external::get_chats_by_courses_returns(), $chats);
        $this->assertCount(0, $chats['chats']);
        $this->assertEquals(1, $chats['warnings'][0]['warningcode']);

        // Now as admin.
        $this->setAdminUser();
        // As Admin we can see this chat.
        $chats = mod_chat_external::get_chats_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $chats = external_api::clean_returnvalue(mod_chat_external::get_chats_by_courses_returns(), $chats);

        $this->assertCount(1, $chats['chats']);
        $this->assertEquals('Second Chat', $chats['chats'][0]['name']);
        $this->assertEquals('header_js', $chats['chats'][0]['chatmethod']);
        // We see 17 fields.
        $this->assertCount(17, $chats['chats'][0]);
        // As an Admin you can see some chat properties like 'section'.
        $this->assertEquals(0, $chats['chats'][0]['section']);

        // Enrol student in the second course.
        self::getDataGenerator()->enrol_user($student1->id,  $course2->id, $studentrole->id);
        $this->setUser($student1);
        $chats = mod_chat_external::get_chats_by_courses();
        $chats = external_api::clean_returnvalue(mod_chat_external::get_chats_by_courses_returns(), $chats);
        $this->assertCount(2, $chats['chats']);

    }

    /**
     * Test get_sessions_empty_chat
     */
    public function test_get_sessions_empty_chat() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $result = mod_chat_external::get_sessions($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertEmpty($result['sessions']);
        $this->assertEmpty($result['warnings']);
    }


    /**
     * Test get_sessions_no_permissions_for_student
     */
    public function test_get_sessions_no_permissions_for_student() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        // Disable logs for students.
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id, 'studentlogs' => 0));
        // The admin has permissions to check logs.
        $result = mod_chat_external::get_sessions($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertEmpty($result['sessions']);
        $this->assertEmpty($result['warnings']);

        $user = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        unassign_capability('mod/chat:readlog', $studentrole->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $this->setUser($user);
        // Students don't have permissions.
        $this->expectException('moodle_exception');
        mod_chat_external::get_sessions($chat->id);
    }

    /**
     * Test get_sessions_not_completed_session
     */
    public function test_get_sessions_not_completed_session() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Start a chat and send just one message.
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];
        $result = mod_chat_external::send_chat_message($chatsid, 'hello!');
        $result = external_api::clean_returnvalue(mod_chat_external::send_chat_message_returns(), $result);

        // Check session is not marked as completed so it is not returned.
        $result = mod_chat_external::get_sessions($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertEmpty($result['sessions']);
        $this->assertEmpty($result['warnings']);

        // Pass showall parameter to indicate that we want not completed sessions.
        $result = mod_chat_external::get_sessions($chat->id, 0, true);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertCount(1, $result['sessions']); // One session.
        $this->assertFalse($result['sessions'][0]['iscomplete']); // Session not complete.
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test get_sessions_completed_session
     */
    public function test_get_sessions_completed_session() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id);

        // Start a chat and completeit.
        $this->setUser($user1);
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];
        $result = mod_chat_external::send_chat_message($chatsid, 'hello!');
        $result = external_api::clean_returnvalue(mod_chat_external::send_chat_message_returns(), $result);
        $this->setUser($user2);
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];
        $result = mod_chat_external::send_chat_message($chatsid, 'hello to you!');
        $result = external_api::clean_returnvalue(mod_chat_external::send_chat_message_returns(), $result);
        // Need to change first messages and last message times to mark the session completed.
        // We receive 4 messages (2 system messages that indicates user joined and the 2 messages sent by the users).
        $messages = $DB->get_records('chat_messages', array('chatid' => $chat->id));
        // Messages just one hour ago and 70 seconds between them.
        $timegap = 0;
        $timenow = time();
        foreach ($messages as $message) {
            $DB->set_field('chat_messages', 'timestamp', $timenow - HOURSECS + $timegap, array('id' => $message->id));
            $timegap += 70;
        }
        // Check session is completed.
        $result = mod_chat_external::get_sessions($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertCount(1, $result['sessions']); // One session.
        $this->assertTrue($result['sessions'][0]['iscomplete']); // Session complete.
        $this->assertEquals($timenow - HOURSECS + 70, $result['sessions'][0]['sessionstart']);  // First not system message time.
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test get_session_messages
     */
    public function test_get_session_messages() {
        global $DB;

        $this->resetAfterTest(true);

        // Setup test data.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id);

        // Start a chat and send a few messages.
        $this->setUser($user1);
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];
        mod_chat_external::send_chat_message($chatsid, 'hello!');
        mod_chat_external::send_chat_message($chatsid, 'bye bye!');

        $this->setUser($user2);
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];
        mod_chat_external::send_chat_message($chatsid, 'greetings!');

        // Pass showall parameter to indicate that we want not completed sessions.
        $result = mod_chat_external::get_sessions($chat->id, 0, true);
        $result = external_api::clean_returnvalue(mod_chat_external::get_sessions_returns(), $result);
        $this->assertCount(1, $result['sessions']); // One session.

        $sessionstart = $result['sessions'][0]['sessionstart'];
        $sessionend = $result['sessions'][0]['sessionend'];
        $result = mod_chat_external::get_session_messages($chat->id, $sessionstart, $sessionend);
        $result = external_api::clean_returnvalue(mod_chat_external::get_session_messages_returns(), $result);
        $this->assertCount(5, $result['messages']); // 2 system + 3 personal messages.
        $found = 0;
        foreach ($result['messages'] as $message) {
            if (!$message['issystem']) {
                if ($message['userid'] == $user1->id) {
                    if ($message['message'] != 'hello!') {
                        $this->assertEquals('bye bye!', $message['message']);
                        $found++;
                    }
                } else {
                    $this->assertEquals($user2->id, $message['userid']);
                    $this->assertEquals('greetings!', $message['message']);
                    $found++;
                }
            }
        }
        $this->assertEquals(2, $found);
    }
}
