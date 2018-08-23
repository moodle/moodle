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
 * Contains class containing unit tests for mod/chat/lib.php.
 *
 * @package mod_chat
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing unit tests for mod/chat/lib.php.
 *
 * @package mod_chat
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_lib_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_chat_core_calendar_provide_event_action_chattime_event_yesterday() {
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a chat.
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id,
            'chattime' => time() - DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $chat->id, CHAT_EVENT_TYPE_CHATTIME);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_chat_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_chat_core_calendar_provide_event_action_chattime_event_today() {
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a chat.
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id,
            'chattime' => usergetmidnight(time())));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $chat->id, CHAT_EVENT_TYPE_CHATTIME);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_chat_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enterchat', 'chat'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_chat_core_calendar_provide_event_action_chattime_event_tonight() {
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a chat.
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id,
            'chattime' => usergetmidnight(time()) + (23 * HOURSECS)));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $chat->id, CHAT_EVENT_TYPE_CHATTIME);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_chat_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enterchat', 'chat'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_chat_core_calendar_provide_event_action_chattime_event_tomorrow() {
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a chat.
        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id,
            'chattime' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $chat->id, CHAT_EVENT_TYPE_CHATTIME);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_chat_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enterchat', 'chat'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    /**
     * Test for chat_get_sessions().
     */
    public function test_chat_get_sessions() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Setup test data.
        $this->setAdminUser();
        $course = $generator->create_course();
        $chat = $generator->create_module('chat', ['course' => $course->id]);

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($user1->id, $course->id, $studentrole->id);
        $generator->enrol_user($user2->id, $course->id, $studentrole->id);

        // Login as user 1.
        $this->setUser($user1);
        $chatsid = chat_login_user($chat->id, 'ajax', 0, $course);
        $chatuser = $DB->get_record('chat_users', ['sid' => $chatsid]);

        // This is when the session starts (when the user enters the chat).
        $sessionstart = $chatuser->lastping;

        // Send some messages.
        chat_send_chatmessage($chatuser, 'hello!');
        chat_send_chatmessage($chatuser, 'bye bye!');

        // Login as user 2.
        $this->setUser($user2);
        $chatsid = chat_login_user($chat->id, 'ajax', 0, $course);
        $chatuser = $DB->get_record('chat_users', ['sid' => $chatsid]);

        // Send a message and take note of this message ID.
        $messageid = chat_send_chatmessage($chatuser, 'greetings!');

        // This is when the session ends (timestamp of the last message sent to the chat).
        $sessionend = $DB->get_field('chat_messages', 'timestamp', ['id' => $messageid]);

        // Get the messages for this chat session.
        $messages = chat_get_session_messages($chat->id, false, 0, 0, 'timestamp DESC');

        // We should have 3 user and 2 system (enter) messages.
        $this->assertCount(5, $messages);

        // Fetch the chat sessions from the messages we retrieved.
        $sessions = chat_get_sessions($messages, true);

        // There should be only one session.
        $this->assertCount(1, $sessions);

        // Get this session.
        $session = reset($sessions);

        // Confirm that the start and end times of the session matches.
        $this->assertEquals($sessionstart, $session->sessionstart);
        $this->assertEquals($sessionend, $session->sessionend);
        // Confirm we have 2 participants in the chat.
        $this->assertCount(2, $session->sessionusers);
    }

    /**
     * Test for chat_get_sessions with messages belonging to multiple sessions.
     */
    public function test_chat_get_sessions_multiple() {
        $messages = [];
        $gap = 5; // 5 secs.

        $now = time();
        $timestamp = $now;

        // Messages belonging to 3 sessions. Session 1 has 10 messages, 2 has 15, 3 has 25.
        $sessionusers = [];
        $sessiontimes = [];
        $session = 0; // Incomplete session.
        for ($i = 1; $i <= 50; $i++) {
            // Take note of expected session times as we go through.
            switch ($i) {
                case 1:
                    // Session 1 start time.
                    $sessiontimes[0]['start'] = $timestamp;
                    break;
                case 10:
                    // Session 1 end time.
                    $sessiontimes[0]['end'] = $timestamp;
                    break;
                case 11:
                    // Session 2 start time.
                    $sessiontimes[1]['start'] = $timestamp;
                    break;
                case 25:
                    // Session 2 end time.
                    $sessiontimes[1]['end'] = $timestamp;
                    break;
                case 26:
                    // Session 3 start time.
                    $sessiontimes[2]['start'] = $timestamp;
                    break;
                case 50:
                    // Session 3 end time.
                    $sessiontimes[2]['end'] = $timestamp;
                    break;
            }

            // User 1 to 5.
            $user = rand(1, 5);

            // Let's also include system messages as well. Give them to pop in 1-in-10 chance.
            $issystem = rand(1, 10) == 10;

            if ($issystem) {
                $message = 'enter';
            } else {
                $message = 'Message ' . $i;
                if (!isset($sessionusers[$session][$user])) {
                    $sessionusers[$session][$user] = 1;
                } else {
                    $sessionusers[$session][$user]++;
                }
            }
            $messages[] = (object)[
                'id' => $i,
                'chatid' => 1,
                'userid' => $user,
                'message' => $message,
                'issystem' => $issystem,
                'timestamp' => $timestamp,
            ];

            // Set the next timestamp.
            if ($i == 10 || $i == 25) {
                // New session.
                $session++;
                $timestamp += CHAT_SESSION_GAP + 1;
            } else {
                $timestamp += $gap;
            }
        }
        // Reverse sort the messages so they're in descending order.
        rsort($messages);

        // Get chat sessions showing only complete ones.
        $completesessions = chat_get_sessions($messages);
        // Session 1 is incomplete, so there should only be 2 sessions when $showall is false.
        $this->assertCount(2, $completesessions);

        // Reverse sort sessions so they are in ascending order matching our expected session times and users.
        $completesessions = array_reverse($completesessions);
        foreach ($completesessions as $index => $session) {
            // We increment index by 1 because the incomplete expected session (index=0) is not included.
            $expectedindex = $index + 1;

            // Check the session users.
            $users = $sessionusers[$expectedindex];
            $this->assertCount(count($users), $session->sessionusers);
            // Check the message counts for each user in this session.
            foreach ($users as $userid => $messagecount) {
                $this->assertEquals($messagecount, $session->sessionusers[$userid]);
            }

            $sessionstart = $sessiontimes[$expectedindex]['start'];
            $sessionend = $sessiontimes[$expectedindex]['end'];
            $this->assertEquals($sessionstart, $session->sessionstart);
            $this->assertEquals($sessionend, $session->sessionend);
        }

        // Get all the chat sessions.
        $allsessions = chat_get_sessions($messages, true);
        // When showall is true, we should get 3 sessions.
        $this->assertCount(3, $allsessions);

        // Reverse sort sessions so they are in ascending order matching our expected session times and users.
        $allsessions = array_reverse($allsessions);
        foreach ($allsessions as $index => $session) {
            // Check the session users.
            $users = $sessionusers[$index];
            $this->assertCount(count($users), $session->sessionusers);
            // Check the message counts for each user in this session.
            foreach ($users as $userid => $messagecount) {
                $this->assertEquals($messagecount, $session->sessionusers[$userid]);
            }

            $sessionstart = $sessiontimes[$index]['start'];
            $sessionend = $sessiontimes[$index]['end'];
            $this->assertEquals($sessionstart, $session->sessionstart);
            $this->assertEquals($sessionend, $session->sessionend);
        }
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid
     * @param int $instanceid The chat id.
     * @param string $eventtype The event type. eg. ASSIGN_EVENT_TYPE_DUE.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'chat';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }
}
