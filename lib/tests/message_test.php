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

namespace core;

/**
 * Test script for message class.
 *
 * Test classes for \core\message\message.
 *
 * @package core
 * @category test
 * @copyright 2015 onwards Ankit Agarwal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class message_test extends \advanced_testcase {

    /**
     * Test the method get_eventobject_for_processor().
     */
    public function test_get_eventobject_for_processor(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $USER;
        $message->userto = $user;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';
        $message->contexturl = 'http://GalaxyFarFarAway.com';
        $message->contexturlname = 'Context name';
        $message->replyto = "random@example.com";
        $message->replytoname = fullname($USER);
        $message->attachname = 'attachment';
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for all types of messages.
        $message->set_additional_content('test', $content);

        // Create a file instance.
        $usercontext = \context_user::instance($user->id);
        $file = new \stdClass;
        $file->contextid = $usercontext->id;
        $file->component = 'user';
        $file->filearea  = 'private';
        $file->itemid    = 0;
        $file->filepath  = '/';
        $file->filename  = '1.txt';
        $file->source    = 'test';

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($file, 'file1 content');
        $message->attachment = $file;

        $stdClass = $message->get_eventobject_for_processor('test');

        $this->assertSame($message->courseid, $stdClass->courseid);
        $this->assertSame($message->component, $stdClass->component);
        $this->assertSame($message->name, $stdClass->name);
        $this->assertSame($message->userfrom, $stdClass->userfrom);
        $this->assertSame($message->userto, $stdClass->userto);
        $this->assertSame($message->subject, $stdClass->subject);
        $this->assertSame(' test ' . $message->fullmessage . ' test ', $stdClass->fullmessage);
        $this->assertSame(' test ' . $message->fullmessagehtml . ' test ', $stdClass->fullmessagehtml);
        $this->assertSame(' test ' . $message->smallmessage . ' test ', $stdClass->smallmessage);
        $this->assertSame($message->notification, $stdClass->notification);
        $this->assertSame($message->contexturl, $stdClass->contexturl);
        $this->assertSame($message->contexturlname, $stdClass->contexturlname);
        $this->assertSame($message->replyto, $stdClass->replyto);
        $this->assertSame($message->replytoname, $stdClass->replytoname);
        $this->assertSame($message->attachname, $stdClass->attachname);

        // Extra content for fullmessage only.
        $content = array('fullmessage' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('test', $content);
        $stdClass = $message->get_eventobject_for_processor('test');
        $this->assertSame(' test ' . $message->fullmessage . ' test ', $stdClass->fullmessage);
        $this->assertSame($message->fullmessagehtml, $stdClass->fullmessagehtml);
        $this->assertSame($message->smallmessage, $stdClass->smallmessage);

        // Extra content for fullmessagehtml and smallmessage only.
        $content = array('fullmessagehtml' => array('header' => ' test ', 'footer' => ' test '),
                         'smallmessage' => array('header' => ' testsmall ', 'footer' => ' testsmall '));
        $message->set_additional_content('test', $content);
        $stdClass = $message->get_eventobject_for_processor('test');
        $this->assertSame($message->fullmessage, $stdClass->fullmessage);
        $this->assertSame(' test ' . $message->fullmessagehtml . ' test ', $stdClass->fullmessagehtml);
        $this->assertSame(' testsmall ' . $message->smallmessage . ' testsmall ', $stdClass->smallmessage);

        // Extra content for * and smallmessage.
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '),
                         'smallmessage' => array('header' => ' testsmall ', 'footer' => ' testsmall '));
        $message->set_additional_content('test', $content);
        $stdClass = $message->get_eventobject_for_processor('test');
        $this->assertSame(' test ' . $message->fullmessage . ' test ', $stdClass->fullmessage);
        $this->assertSame(' test ' . $message->fullmessagehtml . ' test ', $stdClass->fullmessagehtml);
        $this->assertSame(' testsmall ' . ' test ' .  $message->smallmessage . ' test ' . ' testsmall ', $stdClass->smallmessage);
    }

    /**
     * Test sending messages as email works with the new class.
     */
    public function test_send_message(): void {
        global $DB, $CFG;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');

        // Test basic email processor.
        $this->assertFileExists("$CFG->dirroot/message/output/email/version.php");
        $this->assertFileExists("$CFG->dirroot/message/output/popup/version.php");

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_enabled', 'email', $user2);

        // Extra content for all types of messages.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->userto            = $user2;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $recordexists = $DB->record_exists('messages', array('id' => $messageid));
        $this->assertSame(true, $recordexists);
        $this->assertSame($user1->email, $email->from);
        $this->assertSame($user2->email, $email->to);
        $this->assertSame(get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/test.*message body.*test/s', $email->body);
        $sink->clear();

        // Test that event fired includes the courseid.
        $eventsink = $this->redirectEvents();
        $messageid = message_send($message);
        $events = $eventsink->get_events();
        $event = reset($events);
        $this->assertEquals($message->courseid, $event->other['courseid']);
        $eventsink->clear();
        $sink->clear();

        // Extra content for small message only. Shouldn't show up in emails as we sent fullmessage and fullmessagehtml only in
        // the emails.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->userto            = $user2;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $content = array('smallmessage' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $recordexists = $DB->record_exists('messages', array('id' => $messageid));
        $this->assertSame(true, $recordexists);
        $this->assertSame($user1->email, $email->from);
        $this->assertSame($user2->email, $email->to);
        $this->assertSame(get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertDoesNotMatchRegularExpression('/test.*message body test/', $email->body);

        // Test that event fired includes the courseid.
        $eventsink = $this->redirectEvents();
        $messageid = message_send($message);
        $events = $eventsink->get_events();
        $event = reset($events);
        $this->assertEquals($message->courseid, $event->other['courseid']);
        $eventsink->close();
        $sink->close();
    }

    public function test_send_message_with_prefix(): void {
        global $DB, $CFG;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');
        set_config('emailsubjectprefix', '[Prefix Text]');

        // Test basic email processor.
        $this->assertFileExists("$CFG->dirroot/message/output/email/version.php");
        $this->assertFileExists("$CFG->dirroot/message/output/popup/version.php");

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_enabled', 'email', $user2);

        // Check that prefix is ammended to the subject of the email.
        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = get_string('unreadnewmessage', 'message', fullname($user1));
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);
        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame('[Prefix Text] '. get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);
        $sink->clear();
    }

    /**
     * Test get_messages_by_component method.
     *
     * @covers \phpunit_message_sink::get_messages_by_component
     * @covers \phpunit_message_sink::get_messages_by_component_and_type
     */
    public function test_get_messages_by_component(): void {
        $this->resetAfterTest();

        // Create users.
        $admin = get_admin();
        $user = $this->getDataGenerator()->create_user();
        // Create course.
        $course = $this->getDataGenerator()->create_course();

        // Redirect messages.
        $sink = $this->redirectMessages();

        // Create the first message.
        // This message will belong to mod_forum component.
        $message1 = new \core\message\message();
        $message1->courseid = $course->id;
        $message1->component = 'mod_forum';
        $message1->name = 'posts';
        $message1->userfrom = $admin;
        $message1->userto = $user;
        $message1->subject = 'Test message 1';
        $message1->fullmessage = 'Message body 1';
        $message1->fullmessageformat = FORMAT_MARKDOWN;
        $message1->fullmessagehtml = '<p>Message body 1</p>';
        $message1->smallmessage = 'Small message 1';
        $message1->notification = 1;
        message_send($message1);

        // Create the second message.
        // This message will belong to core component.
        $message2 = new \core\message\message();
        $message2->courseid = $course->id;
        $message2->component = 'moodle';
        $message2->name = 'instantmessage';
        $message2->userfrom = $admin;
        $message2->userto = $user;
        $message2->subject = 'Test message 2';
        $message2->fullmessage = 'Message body 2';
        $message2->fullmessageformat = FORMAT_MARKDOWN;
        $message2->fullmessagehtml = '<p>Message body 2</p>';
        $message2->smallmessage = 'Small message 2';
        $message2->notification = 1;
        message_send($message2);

        // Create the third message.
        // This message will belong to core component but different name.
        $message3 = new \core\message\message();
        $message3->courseid = SITEID;
        $message3->component = 'moodle';
        $message3->name = 'messagecontactrequests';
        $message3->userfrom = $admin;
        $message3->userto = $user;
        $message3->subject = 'Test message 3';
        $message3->fullmessage = 'Message body 3';
        $message3->fullmessageformat = FORMAT_MARKDOWN;
        $message3->fullmessagehtml = '<p>Message body 3</p>';
        $message3->smallmessage = 'Small message 3';
        $message3->notification = 1;
        message_send($message3);

        // Sink should contain three messages.
        $this->assertCount(3, $sink->get_messages());
        // Sink should contain one message for mod_forum component.
        $messages = $sink->get_messages_by_component('mod_forum');
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertSame($message1->component, $message->component);
        $this->assertSame($message1->name, $message->eventtype);
        $this->assertSame($message1->subject, $message->subject);
        // Sink should contain two messages for core component.
        $messages = $sink->get_messages_by_component('core');
        $this->assertCount(2, $messages);
        foreach ($messages as $message) {
            $expectedmessage = $message->eventtype == 'messagecontactrequests' ? $message3 : $message2;
            $this->assertSame($expectedmessage->component, $message->component);
            $this->assertSame($expectedmessage->name, $message->eventtype);
            $this->assertSame($expectedmessage->subject, $message->subject);
        }
        // Sink should contain one message for core component with type is messagecontactrequests.
        $messages = $sink->get_messages_by_component_and_type('core', 'messagecontactrequests');
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertSame($message3->component, $message->component);
        $this->assertSame($message3->name, $message->eventtype);
        $this->assertSame($message3->subject, $message->subject);

        // Clear sink.
        $sink->clear();
    }
}
