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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\persistents\message;
use block_quickmail\persistents\message_recipient;

class block_quickmail_persistent_concerns_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    private function create_message() {
        return message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
        ]);
    }

    private function create_message_and_recipient() {
        $message = $this->create_message();

        $recipient = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => 1,
        ]);

        return [$message, $recipient];
    }

    public function test_create_new() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals(1, $message->get('course_id'));
        $this->assertEquals(1, $message->get('user_id'));
        $this->assertEquals('email', $message->get('message_type'));
    }

    public function test_find_or_null() {
        $this->resetAfterTest(true);

        $fetched = message::find_or_null(1);

        $this->assertNull($fetched);

        $message = $this->create_message();

        $fetched = message::find_or_null($message->get('id'));

        $this->assertNotNull($fetched);
        $this->assertInstanceOf(message::class, $fetched);
    }

    public function test_get_readable_date() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $timestamp = $message->get('timecreated');

        // Segun Babalola, 2020-10-30.
        // Actual dates are using config format, so use the same format here to prevent test failure.
        $datetimeformat = get_string('strftimedatetime', 'langconfig');

        $this->assertEquals(userdate($timestamp, $datetimeformat), $message->get_readable_date('timecreated'));
    }

    public function test_supports_soft_deletes() {
        $this->resetAfterTest(true);

        list($message, $recipient) = $this->create_message_and_recipient();

        $this->assertTrue($message::supports_soft_deletes());
        $this->assertFalse($recipient::supports_soft_deletes());
    }

    public function test_hard_and_soft_delete() {
        $this->resetAfterTest(true);

        $message1 = $this->create_message();
        $message2 = $this->create_message();

        $message1id = $message1->get('id');
        $message2id = $message2->get('id');

        $message1->hard_delete();
        $message2->soft_delete();

        global $DB;

        $deletedmessage1 = $DB->get_record('block_quickmail_messages', ['id' => $message1id]);
        $deletedmessage2 = $DB->get_record('block_quickmail_messages', ['id' => $message2id]);

        $this->assertFalse($deletedmessage1);
        $this->assertIsObject($deletedmessage2);
        $this->assertGreaterThan(0, $message2->get('timedeleted'));
        $this->assertTrue($message2->is_soft_deleted());
    }

    public function test_belongs_to_a_course() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $message = message::create_new([
            'course_id' => $course->id,
            'user_id' => 1,
            'message_type' => 'email',
        ]);

        $messagecourse = $message->get_course();

        $this->assertIsObject($messagecourse);
        $this->assertEquals($course->id, $messagecourse->id);
        $this->assertTrue($message->is_owned_by_course($course));
        $this->assertTrue($message->is_owned_by_course($course->id));

        $message = message::create_new([
            'course_id' => 47,
            'user_id' => 1,
            'message_type' => 'email',
        ]);

        $nonexistentcourse = $message->get_course();

        $this->assertNull($nonexistentcourse);
    }

    public function test_gets_a_course_property() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $message = message::create_new([
            'course_id' => $course->id,
            'user_id' => 1,
            'message_type' => 'email',
        ]);

        $shortname = $message->get_course_property('shortname');

        $this->assertEquals($course->shortname, $shortname);

        $shortname = $message->get_course_property('shortname2', 'this instead');

        $this->assertEquals('this instead', $shortname);
    }

    public function test_belongs_to_a_message() {
        $this->resetAfterTest(true);

        list($message, $recipient) = $this->create_message_and_recipient();

        $recipientmessage = $recipient->get_message();

        $this->assertInstanceOf(message::class, $recipientmessage);
        $this->assertEquals($message->get('id'), $recipientmessage->get('id'));

        $secondrecipient = message_recipient::create_for_message($message, [
            'user_id' => 1
        ]);

        $this->assertInstanceOf(message_recipient::class, $secondrecipient);
        $this->assertEquals(1, $secondrecipient->get('user_id'));
    }

    public function test_belongs_to_a_user() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => $user->id,
            'message_type' => 'email',
        ]);

        $messageuser = $message->get_user();

        $this->assertIsObject($messageuser);
        $this->assertEquals($user->id, $messageuser->id);
        $this->assertTrue($message->is_owned_by_user($user));
        $this->assertTrue($message->is_owned_by_user($user->id));

        global $DB;

        $DB->delete_records('user', ['id' => $messageuser->id]);

        $nonexistentuser = $message->get_user();

        $this->assertNull($nonexistentuser);
    }

}
