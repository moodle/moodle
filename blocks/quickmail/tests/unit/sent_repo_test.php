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

use block_quickmail\repos\sent_repo;
use block_quickmail\persistents\message;
use block_quickmail\repos\pagination\paginated;

class block_quickmail_sent_repo_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_get_for_user() {
        $this->resetAfterTest(true);

        // Create 3 sents for user id: 1.
        $sent1 = $this->create_message();
        $sent2 = $this->create_message();
        $sent3 = $this->create_message();

        // Create 2 sents for user id: 2.
        $sent4 = $this->create_message();
        $sent4->set('user_id', 2);
        $sent4->update();
        $sent5 = $this->create_message();
        $sent5->set('user_id', 2);
        $sent5->update();

        // Create a non-sent message for user id: 1.
        $sent6 = $this->create_message(false);

        // Create a message for user: 1, course: 2.
        $sent7 = $this->create_message();
        $sent7->set('course_id', 2);
        $sent7->update();

        // Get all sents for user: 1.
        $sents = sent_repo::get_for_user(1);

        $this->assertCount(4, $sents->data);

        // Get all sents for user: 1, course: 1.
        $sents = sent_repo::get_for_user(1, 1);

        $this->assertCount(3, $sents->data);

        // Get all sents for user: 1, course: 2.
        $sents = sent_repo::get_for_user(1, 2);

        $this->assertCount(1, $sents->data);
    }

    public function test_sorts_get_for_user() {
        $this->resetAfterTest(true);

        $this->create_test_sents();

        /*
         *  Segun Babalola, 2020-10-30
         *  This test has assertions that checks hard-coded DB id values.
         *  The DB tables used depend on auto-increment ID values, so not sure how the values are guaranteed to retain
         *  their specific hard-coded vlaues over the years this test has existed for.
         *
         *  I'm changing the tests to look for minimum and maximums where appropriate instead.
         */
        // Get all sents for user: 1.
        $sents = sent_repo::get_for_user(1);

        $this->assertCount(7, $sents->data);
        $this->assertEquals('date', $sents->data[0]->get('subject'));

        // Sort by id.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'asc'
        ]);
        $this->assertEquals(min($this->extract_ids($sents->data)), $sents->data[0]->get('id'));

        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'desc'
        ]);
        $this->assertEquals(max($this->extract_ids($sents->data)), $sents->data[0]->get('id'));

        // Sort by course.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'course',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1, $sents->data[0]->get('course_id'));

        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'course',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5, $sents->data[0]->get('course_id'));

        // Sort by subject.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'subject',
            'dir' => 'asc'
        ]);
        $this->assertEquals('apple', $sents->data[0]->get('subject'));

        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'subject',
            'dir' => 'desc'
        ]);
        $this->assertEquals('grape', $sents->data[0]->get('subject'));

        // Sort by (time) created.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'created',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1111111111, $sents->data[0]->get('timecreated'));

        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'created',
            'dir' => 'desc'
        ]);
        $this->assertEquals(8888888888, $sents->data[0]->get('timecreated'));

        // Sort by (time) modified.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'modified',
            'sir' => 'asc'
        ]);
        $this->assertEquals(1010101010, $sents->data[0]->get('timemodified'));

        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'modified',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5454545454, $sents->data[0]->get('timemodified'));
    }

    public function test_sorts_get_for_user_and_course() {
        $this->resetAfterTest(true);

        $this->create_test_sents();

        // Get all sents for user: 1, course: 1.
        $sents = sent_repo::get_for_user(1, 1);
        $this->assertCount(4, $sents->data);
        $this->assertEquals('date', $sents->data[0]->get('subject'));

        // Sort by id.
        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'id',
            'dir' => 'asc'
        ]);
        $this->assertEquals(min($this->extract_ids($sents->data)), $sents->data[0]->get('id'));

        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'id',
            'dir' => 'desc'
        ]);
        $this->assertEquals(max($this->extract_ids($sents->data)), $sents->data[0]->get('id'));

        // Sort by course.
        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'course',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1, $sents->data[0]->get('course_id'));

        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'course',
            'dir' => 'desc'
        ]);
        $this->assertEquals(1, $sents->data[0]->get('course_id'));

        // Sort by subject.
        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'subject',
            'dir' => 'asc'
        ]);
        $this->assertEquals('apple', $sents->data[0]->get('subject'));

        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'subject',
            'dir' => 'desc'
        ]);
        $this->assertEquals('fig', $sents->data[0]->get('subject'));

        // Sort by (time) created.
        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'created',
            'dir' => 'asc'
        ]);
        $this->assertEquals(1111111111, $sents->data[0]->get('timecreated'));

        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'created',
            'dir' => 'desc'
        ]);
        $this->assertEquals(8888888888, $sents->data[0]->get('timecreated'));

        // Sort by (time) modified.
        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'modified',
            'sir' => 'asc'
        ]);
        $this->assertEquals(1010101010, $sents->data[0]->get('timemodified'));

        $sents = sent_repo::get_for_user(1, 1, [
            'sort' => 'modified',
            'dir' => 'desc'
        ]);
        $this->assertEquals(5454545454, $sents->data[0]->get('timemodified'));
    }

    public function test_gets_paginated_results_for_user() {
        $this->resetAfterTest(true);

        // Create 30 sents for user id: 1.
        foreach (range(1, 30) as $i) {
            $this->create_message(true);
        }

        // Get all sents for user: 1.
        $sents = sent_repo::get_for_user(1, 0, [
            'sort' => 'id',
            'dir' => 'asc',
            'paginate' => true,
            'page' => '2',
            'per_page' => '4',
            'uri' => '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc',
        ]);

        $this->assertCount(4, $sents->data);
        $this->assertInstanceOf(paginated::class, $sents->pagination);
        $this->assertEquals(8, $sents->pagination->page_count);
        $this->assertEquals(4, $sents->pagination->offset);
        $this->assertEquals(4, $sents->pagination->per_page);
        $this->assertEquals(2, $sents->pagination->current_page);
        $this->assertEquals(3, $sents->pagination->next_page);
        $this->assertEquals(1, $sents->pagination->previous_page);
        $this->assertEquals(30, $sents->pagination->total_count);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=2',
            $sents->pagination->uri_for_page);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1',
            $sents->pagination->first_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=8',
            $sents->pagination->last_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=3',
            $sents->pagination->next_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1',
            $sents->pagination->previous_page_uri);
    }

    // Helpers.
    private function extract_ids(array $psents) {
        return array_map( function($sent) {
            return $sent->get('id');
        }, $psents);
    }
    private function create_message($issent = true) {
        return message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'sent_at' => $issent ? time() : 0
        ]);
    }

    private function create_test_sents() {
        global $DB;

        // Id: 144000.
        $sent1 = $this->create_message();
        $sent1->set('course_id', 1);
        $sent1->set('subject', 'date');
        $sent1->update();
        $sent = $sent1->to_record();
        $sent->timecreated = 8888888888;
        $sent->timemodified = 3232323232;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144001.
        $sent2 = $this->create_message();
        $sent2->set('course_id', 5);
        $sent2->set('subject', 'elderberry');
        $sent2->update();
        $sent = $sent2->to_record();
        $sent->timecreated = 4444444444;
        $sent->timemodified = 5252525252;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144002.
        $sent3 = $this->create_message();
        $sent3->set('course_id', 3);
        $sent3->set('subject', 'coconut');
        $sent3->update();
        $sent = $sent3->to_record();
        $sent->timecreated = 7777777777;
        $sent->timemodified = 1919191919;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144003.
        $sent4 = $this->create_message();
        $sent4->set('course_id', 1);
        $sent4->set('subject', 'apple');
        $sent4->update();
        $sent = $sent4->to_record();
        $sent->timecreated = 1111111111;
        $sent->timemodified = 5454545454;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144004.
        $sent5 = $this->create_message();
        $sent5->set('course_id', 1);
        $sent5->set('subject', 'banana');
        $sent5->update();
        $sent = $sent5->to_record();
        $sent->timecreated = 2222222222;
        $sent->timemodified = 3333333333;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144005.
        $sent6 = $this->create_message();
        $sent6->set('course_id', 2);
        $sent6->set('subject', 'grape');
        $sent6->update();
        $sent = $sent6->to_record();
        $sent->timecreated = 1212121212;
        $sent->timemodified = 2525252525;
        $DB->update_record('block_quickmail_messages', $sent);

        // Id: 144006.
        $sent7 = $this->create_message();
        $sent7->set('course_id', 1);
        $sent7->set('subject', 'fig');
        $sent7->update();
        $sent = $sent7->to_record();
        $sent->timecreated = 3434343434;
        $sent->timemodified = 1010101010;
        $DB->update_record('block_quickmail_messages', $sent);
    }

}
