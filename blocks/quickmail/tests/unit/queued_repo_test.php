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

use block_quickmail\repos\queued_repo;
use block_quickmail\persistents\message;
use block_quickmail\persistents\message_recipient;
use block_quickmail\repos\pagination\paginated;

class block_quickmail_queued_repo_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_find_or_null() {
        $this->resetAfterTest(true);

        $queued = $this->create_message(true);

        $foundqueued = queued_repo::find_or_null($queued->get('id'));

        $this->assertInstanceOf(message::class, $foundqueued);

        $message = $this->create_message(false);

        $notfoundqueued = queued_repo::find_or_null($message->get('id'));

        $this->assertNull($notfoundqueued);
    }

    public function test_find_for_user_or_null() {
        $this->resetAfterTest(true);

        $queued = $this->create_message(true);

        $foundqueued = queued_repo::find_for_user_or_null($queued->get('id'), 1);

        $this->assertInstanceOf(message::class, $foundqueued);

        $differentuserqueued = queued_repo::find_for_user_or_null($queued->get('id'), 2);

        $this->assertNull($differentuserqueued);

        $differentmessageidqueued = queued_repo::find_for_user_or_null($queued->get('id') + 1, 1);

        $this->assertNull($differentmessageidqueued);

        $message = $this->create_message(false);

        $notfoundmessage = queued_repo::find_for_user_or_null($message->get('id'), 1);

        $this->assertNull($notfoundmessage);
    }

    public function test_find_for_user_course_or_null() {
        $this->resetAfterTest(true);

        $queued = $this->create_message(true);

        $foundqueued = queued_repo::find_for_user_course_or_null($queued->get('id'), 1, 1);

        $this->assertInstanceOf(message::class, $foundqueued);

        $differentuserqueued = queued_repo::find_for_user_course_or_null($queued->get('id'), 2, 1);

        $this->assertNull($differentuserqueued);

        $differentmessageidqueued = queued_repo::find_for_user_course_or_null($queued->get('id') + 1, 1, 1);

        $this->assertNull($differentmessageidqueued);

        $message = $this->create_message(false);

        $differentcoursequeued = queued_repo::find_for_user_course_or_null($queued->get('id'), 1, 2);

        $this->assertNull($differentcoursequeued);

        $message = $this->create_message(false);

        $notfoundmessage = queued_repo::find_for_user_course_or_null($message->get('id'), 1, 1);

        $this->assertNull($notfoundmessage);
    }


    public function test_get_for_user() {
        $this->resetAfterTest(true);

        // Create 3 queueds for user id: 1.
        $queued1 = $this->create_message(true);
        $queued2 = $this->create_message(true);
        $queued3 = $this->create_message(true);

        // Create 2 queueds for user id: 2.
        $queued4 = $this->create_message(true);
        $queued4->set('user_id', 2);
        $queued4->update();
        $queued5 = $this->create_message(true);
        $queued5->set('user_id', 2);
        $queued5->update();

        // Create a non-queued message for user id: 1.
        $queued6 = $this->create_message();

        // Create a soft-deleted message for user id: 1.
        $queued7 = $this->create_message(true);
        $queued7->soft_delete();

        // Create a message for user: 1, course: 2.
        $queued8 = $this->create_message(true);
        $queued8->set('course_id', 2);
        $queued8->update();

        // Get all queueds for user: 1.
        $queueds = queued_repo::get_for_user(1, 0);

        $this->assertCount(4, $queueds->data);

        // Get all queueds for user: 1, course: 1.
        $queueds = queued_repo::get_for_user(1, 1);

        $this->assertCount(3, $queueds->data);

        // Get all queueds for user: 1, course: 2.
        $queueds = queued_repo::get_for_user(1, 2);

        $this->assertCount(1, $queueds->data);
    }

    public function test_sorts_get_for_user() {
        $this->resetAfterTest(true);

        $createdqueueds = $this->create_test_queueds();

        // Get all queueds for user: 1.
        $queueds = queued_repo::get_for_user(1, 0);
        $this->assertCount(7, $queueds->data);
        $this->assertEquals('date', $queueds->data[0]->get('subject'));

        // Sort by id.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'asc'
        ]);
        $this->assertEquals($createdqueueds[0]->id, $queueds->data[0]->get('id'));

        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'desc'
        ]);
        $this->assertEquals($createdqueueds[6]->id, $queueds->data[0]->get('id'));

        // Sort by course.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'course',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1, $queueds->data[0]->get('course_id'));

        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'course',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5, $queueds->data[0]->get('course_id'));

        // Sort by subject.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'subject',
            'dir' => 'asc'
        ]);
        $this->assertEquals('apple', $queueds->data[0]->get('subject'));

        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'subject',
            'dir' => 'desc'
        ]);
        $this->assertEquals('grape', $queueds->data[0]->get('subject'));

        // Sort by (time) created.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'created',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1111111111, $queueds->data[0]->get('timecreated'));

        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'created',
            'dir' => 'desc'
        ]);
        $this->assertEquals(8888888888, $queueds->data[0]->get('timecreated'));

        // Sort by (time) scheduled.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'scheduled',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1010101010, $queueds->data[0]->get('to_send_at'));

        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'scheduled',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5454545454, $queueds->data[0]->get('to_send_at'));
    }

    public function test_sorts_get_for_user_and_course() {
        $this->resetAfterTest(true);

        $createdqueueds = $this->create_test_queueds();

        // Get all queueds for user: 1, course: 1.
        $queueds = queued_repo::get_for_user(1, 1);
        $this->assertCount(4, $queueds->data);
        $this->assertEquals('date', $queueds->data[0]->get('subject'));

        // Sort by id.
        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'id',
            'dir' => 'asc'
        ]);
        $this->assertEquals($createdqueueds[0]->id, $queueds->data[0]->get('id'));

        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'id',
            'dir' => 'desc'
        ]);
        $this->assertEquals($createdqueueds[6]->id, $queueds->data[0]->get('id'));

        // Sort by course.
        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'course',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1, $queueds->data[0]->get('course_id'));

        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'course',
            'dir' => 'desc'
        ]);
        $this->assertEquals(1, $queueds->data[0]->get('course_id'));

        // Sort by subject.
        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'subject',
            'dir' => 'asc'
        ]);
        $this->assertEquals('apple', $queueds->data[0]->get('subject'));

        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'subject',
            'dir' => 'desc'
        ]);
        $this->assertEquals('fig', $queueds->data[0]->get('subject'));

        // Sort by (time) created.
        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'created',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1111111111, $queueds->data[0]->get('timecreated'));

        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'created',
            'dir' => 'desc'
        ]);
        $this->assertEquals(8888888888, $queueds->data[0]->get('timecreated'));

        // Sort by (time) scheduled.
        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'scheduled',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1010101010, $queueds->data[0]->get('to_send_at'));

        $queueds = queued_repo::get_for_user(1, 1, [
            'sort' => 'scheduled',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5454545454, $queueds->data[0]->get('to_send_at'));
    }

    public function test_gets_paginated_results_for_user() {
        $this->resetAfterTest(true);

        // Create 30 queueds for user id: 1.
        foreach (range(1, 30) as $i) {
            $this->create_message(true);
        }

        // Get all queueds for user: 1.
        $queueds = queued_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'asc',
            'paginate' => true,
            'page' => '2',
            'per_page' => '4',
            'uri' => '/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc',
        ]);

        $this->assertCount(4, $queueds->data);
        $this->assertInstanceOf(paginated::class, $queueds->pagination);
        $this->assertEquals(8, $queueds->pagination->page_count);
        $this->assertEquals(4, $queueds->pagination->offset);
        $this->assertEquals(4, $queueds->pagination->per_page);
        $this->assertEquals(2, $queueds->pagination->current_page);
        $this->assertEquals(3, $queueds->pagination->next_page);
        $this->assertEquals(1, $queueds->pagination->previous_page);
        $this->assertEquals(30, $queueds->pagination->total_count);
        $this->assertEquals('/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc&page=2',
            $queueds->pagination->uri_for_page);
        $this->assertEquals('/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc&page=1',
            $queueds->pagination->first_page_uri);
        $this->assertEquals('/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc&page=8',
            $queueds->pagination->last_page_uri);
        $this->assertEquals('/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc&page=3',
            $queueds->pagination->next_page_uri);
        $this->assertEquals('/blocks/quickmail/queued.php?courseid=7&sort=subject&dir=asc&page=1',
            $queueds->pagination->previous_page_uri);
    }

    public function test_get_all_messages_to_send() {
        $this->resetAfterTest(true);

        // Should produce 6 queued messages with send times in the future AND 1 with scheduled time in the past.
        $createdqueueds = $this->create_test_queueds();

        $messages = queued_repo::get_all_messages_to_send();

        $this->assertCount(1, $messages);

        $now = time();

        // Update 3 of these messages to have send times in the past.
        foreach ($createdqueueds as $key => $message) {
            if (in_array($key, [1, 3, 5])) {
                $message = new message($message->id);
                $sentat = $now - $key;
                $message->set('to_send_at', $sentat);
                $message->update();
            }
        }

        $messages = queued_repo::get_all_messages_to_send();

        $this->assertCount(4, $messages);

        // Change one of these updated messages to a draft.
        $message = new message($createdqueueds[1]->id);
        $message->set('is_draft', 1);
        $message->update();

        $messages = queued_repo::get_all_messages_to_send();

        $this->assertCount(3, $messages);

        // Change another one of these updated messages to sent status.
        $message = new message($createdqueueds[3]->id);

        $sentat = $now + 100000;

        $message->set('sent_at', $sentat);
        $message->update();

        $this->mark_message_recips_as_sent_to($message, $sentat);

        $messages = queued_repo::get_all_messages_to_send();

        $this->assertCount(2, $messages);

        // Change another one of these updated messages to sending status.
        $message = new message($createdqueueds[5]->id);
        $message->set('is_sending', 1);
        $message->update();

        $messages = queued_repo::get_all_messages_to_send();

        $this->assertCount(1, $messages);
    }

    // Helpers.
    private function create_message($isqueued = false) {
        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'to_send_at' => $isqueued ? time() : 0
        ]);

        message_recipient::create_for_message($message, [
            'user_id' => 1,
            'sent_at' => 0,
        ]);

        return $message;
    }

    private function mark_message_recips_as_sent_to($message, $sentat) {
        foreach ($message->get_message_recipients() as $recipient) {
            $recipient->set('sent_at', $sentat);
            $recipient->update();
        }
    }

    /**
     * Creates test queued messages, returns an array of messages
     * Note: may need to update timestamps based on actual time of running these tests
     *
     * @return array
     */
    private function create_test_queueds() {
        global $DB;

        $queueds = [];

        // Id: 144000.
        $queued1 = $this->create_message(true);
        $queued1->set('course_id', 1);
        $queued1->set('subject', 'date');
        $queued1->update();
        $queued = $queued1->to_record();
        $queued->timecreated = 8888888888;
        $queued->to_send_at = 3232323232;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144001.
        $queued2 = $this->create_message(true);
        $queued2->set('course_id', 5);
        $queued2->set('subject', 'elderberry');
        $queued2->update();
        $queued = $queued2->to_record();
        $queued->timecreated = 4444444444;
        $queued->to_send_at = 5252525252;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144002.
        $queued3 = $this->create_message(true);
        $queued3->set('course_id', 3);
        $queued3->set('subject', 'coconut');
        $queued3->update();
        $queued = $queued3->to_record();
        $queued->timecreated = 7777777777;
        $queued->to_send_at = 1919191919;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144003.
        $queued4 = $this->create_message(true);
        $queued4->set('course_id', 1);
        $queued4->set('subject', 'apple');
        $queued4->update();
        $queued = $queued4->to_record();
        $queued->timecreated = 1111111111;
        $queued->to_send_at = 5454545454;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144004.
        $queued5 = $this->create_message(true);
        $queued5->set('course_id', 1);
        $queued5->set('subject', 'banana');
        $queued5->update();
        $queued = $queued5->to_record();
        $queued->timecreated = 2222222222;
        $queued->to_send_at = 3333333333;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144005.
        $queued6 = $this->create_message(true);
        $queued6->set('course_id', 2);
        $queued6->set('subject', 'grape');
        $queued6->update();
        $queued = $queued6->to_record();
        $queued->timecreated = 1212121212;
        $queued->to_send_at = 2525252525;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        // Id: 144006.
        $queued7 = $this->create_message(true);
        $queued7->set('course_id', 1);
        $queued7->set('subject', 'fig');
        $queued7->update();
        $queued = $queued7->to_record();
        $queued->timecreated = 3434343434;
        $queued->to_send_at = 1010101010;
        $DB->update_record('block_quickmail_messages', $queued);
        $queueds[] = $queued;

        return $queueds;
    }

}
