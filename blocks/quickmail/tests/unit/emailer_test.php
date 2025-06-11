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

class block_quickmail_emailer_testcase extends advanced_testcase {

    use has_general_helpers,
        sends_emails;

    public function test_emailer_sends_to_an_email() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        $user = $this->getDataGenerator()->create_user([
            'email' => 'teacher@example.com',
            'username' => 'teacher'
        ]);

        $subject = 'Hello world';
        $body = 'This is one fine body.';

        $emailer = new block_quickmail_emailer($user, $subject, $body);
        $emailer->to_email('student@example.com');
        $emailer->send();

        $this->assertEquals(1, $this->email_sink_email_count($sink));
        $this->assertEquals($subject, $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, $body));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 1, 'from'));
        $this->assertEquals('student@example.com', $this->email_in_sink_attr($sink, 1, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_emailer_sends_to_a_user() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        $sendinguser = $this->getDataGenerator()->create_user([
            'email' => 'teacher@example.com',
            'username' => 'teacher'
        ]);

        $receivinguser = $this->getDataGenerator()->create_user([
            'email' => 'student@example.com',
            'username' => 'student'
        ]);

        $subject = 'Hello world';
        $body = 'This is one fine body.';

        $emailer = new block_quickmail_emailer($sendinguser, $subject, $body);
        $emailer->to_user($receivinguser);
        $emailer->send();

        $this->assertEquals(1, $this->email_sink_email_count($sink));
        $this->assertEquals($subject, $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, $body));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 1, 'from'));
        $this->assertEquals('student@example.com', $this->email_in_sink_attr($sink, 1, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_emailer_sends_email_using_correct_replyto_params() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        $user = $this->getDataGenerator()->create_user([
            'email' => 'teacher@example.com',
            'username' => 'teacher'
        ]);

        $subject = 'Hello world';
        $body = 'This is one fine body.';

        $emailer = new block_quickmail_emailer($user, $subject, $body);
        $emailer->to_email('student@example.com');
        $emailer->reply_to('reply@here.com', 'Reply Name');
        $emailer->send();

        $this->assertEquals(1, $this->email_sink_email_count($sink));
        $this->assertEquals($subject, $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, $body));
        $this->assertEquals('student@example.com', $this->email_in_sink_attr($sink, 1, 'to'));

        $this->close_email_sink($sink);
    }

}
