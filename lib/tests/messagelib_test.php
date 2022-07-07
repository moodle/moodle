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
 * Tests for messagelib.php.
 *
 * @package    core_message
 * @category   phpunit
 * @copyright  2012 The Open Universtiy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_messagelib_testcase extends advanced_testcase {

    public function test_message_provider_disabled() {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        // Disable instantmessage provider.
        $disableprovidersetting = 'moodle_instantmessage_disable';
        set_config($disableprovidersetting, 1, 'message');
        $preferences = get_message_output_default_preferences();
        $this->assertTrue($preferences->$disableprovidersetting == 1);

        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = get_admin();
        $message->userto            = $this->getDataGenerator()->create_user();;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = 0;

        // Check message is not sent.
        $sink = $this->redirectEmails();
        message_send($message);
        $emails = $sink->get_messages();
        $this->assertEmpty($emails);

        // Check message is sent.
        set_config($disableprovidersetting, 0, 'message');
        $preferences = get_message_output_default_preferences();
        $this->assertTrue($preferences->$disableprovidersetting == 0);

        $sink = $this->redirectEmails();
        message_send($message);
        $emails = $sink->get_messages();
        $email = reset($emails);
        $this->assertEquals(get_string('unreadnewmessage', 'message', fullname(get_admin())), $email->subject);
    }
    public function test_message_get_providers_for_user() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $quiz = $generator->create_module('quiz', array('course' => $course->id));
        $user = $generator->create_user();

        $coursecontext = context_course::instance($course->id);
        $quizcontext = context_module::instance($quiz->cmid);
        $frontpagecontext = context_course::instance(SITEID);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // The user is a student in a course, and has the capability for quiz
        // confirmation emails in one quiz in that course.
        role_assign($studentrole->id, $user->id, $coursecontext->id);
        assign_capability('mod/quiz:emailconfirmsubmission', CAP_ALLOW, $studentrole->id, $quizcontext->id);

        // Give this message type to the front page role.
        assign_capability('mod/quiz:emailwarnoverdue', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagecontext->id);

        $providers = message_get_providers_for_user($user->id);
        $this->assertTrue($this->message_type_present('mod_forum', 'posts', $providers));
        $this->assertTrue($this->message_type_present('mod_quiz', 'confirmation', $providers));
        $this->assertTrue($this->message_type_present('mod_quiz', 'attempt_overdue', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'submission', $providers));

        // A user is a student in a different course, they should not get confirmation.
        $course2 = $generator->create_course(array('category' => $cat->id));
        $user2 = $generator->create_user();
        $coursecontext2 = context_course::instance($course2->id);
        role_assign($studentrole->id, $user2->id, $coursecontext2->id);
        accesslib_clear_all_caches_for_unit_testing();
        $providers = message_get_providers_for_user($user2->id);
        $this->assertTrue($this->message_type_present('mod_forum', 'posts', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'confirmation', $providers));

        // Now remove the frontpage role id, and attempt_overdue message should go away.
        unset_config('defaultfrontpageroleid');
        accesslib_clear_all_caches_for_unit_testing();

        $providers = message_get_providers_for_user($user->id);
        $this->assertTrue($this->message_type_present('mod_quiz', 'confirmation', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'attempt_overdue', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'submission', $providers));
    }

    public function test_message_get_providers_for_user_more() {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // It would probably be better to use a quiz instance as it has capability controlled messages
        // however mod_quiz doesn't have a data generator.
        // Instead we're going to use backup notifications and give and take away the capability at various levels.
        $assign = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        $modulecontext = context_module::instance($assign->cmid);

        // Create and enrol a teacher.
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolplugin->add_instance($course);
        $enrolinstances = enrol_get_instances($course->id, false);
        foreach ($enrolinstances as $enrolinstance) {
            if ($enrolinstance->enrol === 'manual') {
                break;
            }
        }
        $enrolplugin->enrol_user($enrolinstance, $teacher->id);

        // Make the teacher the current user.
        $this->setUser($teacher);

        // Teacher shouldn't have the required capability so they shouldn't be able to see the backup message.
        $this->assertFalse(has_capability('moodle/site:config', $modulecontext));
        $providers = message_get_providers_for_user($teacher->id);
        $this->assertFalse($this->message_type_present('moodle', 'backup', $providers));

        // Give the user the required capability in an activity module.
        // They should now be able to see the backup message.
        assign_capability('moodle/site:config', CAP_ALLOW, $teacherrole->id, $modulecontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $modulecontext = context_module::instance($assign->cmid);
        $this->assertTrue(has_capability('moodle/site:config', $modulecontext));

        $providers = message_get_providers_for_user($teacher->id);
        $this->assertTrue($this->message_type_present('moodle', 'backup', $providers));

        // Prohibit the capability for the user at the course level.
        // This overrules the CAP_ALLOW at the module level.
        // They should not be able to see the backup message.
        assign_capability('moodle/site:config', CAP_PROHIBIT, $teacherrole->id, $coursecontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $modulecontext = context_module::instance($assign->cmid);
        $this->assertFalse(has_capability('moodle/site:config', $modulecontext));

        $providers = message_get_providers_for_user($teacher->id);
        // Actually, handling PROHIBITs would be too expensive. We do not
        // care if users with PROHIBITs see a few more preferences than they should.
        // $this->assertFalse($this->message_type_present('moodle', 'backup', $providers));
    }

    public function test_send_message_redirection() {
        global $DB;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Test basic message redirection.
        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';
        $message->customdata = ['datakey' => 'data'];

        $sink = $this->redirectMessages();
        $this->setCurrentTimeStart();
        $messageid = message_send($message);
        $savedmessages = $sink->get_messages();
        $this->assertCount(1, $savedmessages);
        $savedmessage = reset($savedmessages);
        $this->assertEquals($messageid, $savedmessage->id);
        $this->assertEquals($user1->id, $savedmessage->useridfrom);
        $this->assertEquals($user2->id, $savedmessage->useridto);
        $this->assertEquals($message->fullmessage, $savedmessage->fullmessage);
        $this->assertEquals($message->fullmessageformat, $savedmessage->fullmessageformat);
        $this->assertEquals($message->fullmessagehtml, $savedmessage->fullmessagehtml);
        $this->assertEquals($message->smallmessage, $savedmessage->smallmessage);
        $this->assertEquals($message->smallmessage, $savedmessage->smallmessage);
        $this->assertEquals($message->notification, $savedmessage->notification);
        $this->assertEquals($message->customdata, $savedmessage->customdata);
        $this->assertStringContainsString('datakey', $savedmessage->customdata);
        // Check it was a unserialisable json.
        $customdata = json_decode($savedmessage->customdata);
        $this->assertEquals('data', $customdata->datakey);
        $this->assertEquals(1, $customdata->courseid);
        $this->assertTimeCurrent($savedmessage->timecreated);
        $record = $DB->get_record('messages', array('id' => $savedmessage->id), '*', MUST_EXIST);
        unset($savedmessage->useridto);
        unset($savedmessage->notification);
        $this->assertEquals($record, $savedmessage);
        $sink->clear();
        $this->assertTrue($DB->record_exists('message_user_actions', array('userid' => $user2->id, 'messageid' => $messageid,
            'action' => \core_message\api::MESSAGE_ACTION_READ)));
        $DB->delete_records('messages', array());

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1->id;
        $message->userto = $user2->id;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $sink = $this->redirectMessages();
        $messageid = message_send($message);
        $savedmessages = $sink->get_messages();
        $this->assertCount(1, $savedmessages);
        $savedmessage = reset($savedmessages);
        $this->assertEquals($messageid, $savedmessage->id);
        $this->assertEquals($user1->id, $savedmessage->useridfrom);
        $this->assertEquals($user2->id, $savedmessage->useridto);
        $this->assertEquals($message->fullmessage, $savedmessage->fullmessage);
        $this->assertEquals($message->fullmessageformat, $savedmessage->fullmessageformat);
        $this->assertEquals($message->fullmessagehtml, $savedmessage->fullmessagehtml);
        $this->assertEquals($message->smallmessage, $savedmessage->smallmessage);
        $this->assertEquals($message->smallmessage, $savedmessage->smallmessage);
        $this->assertEquals($message->notification, $savedmessage->notification);
        $this->assertTimeCurrent($savedmessage->timecreated);
        $record = $DB->get_record('messages', array('id' => $savedmessage->id), '*', MUST_EXIST);
        unset($savedmessage->useridto);
        unset($savedmessage->notification);
        $this->assertEquals($record, $savedmessage);
        $sink->clear();
        $this->assertTrue($DB->record_exists('message_user_actions', array('userid' => $user2->id, 'messageid' => $messageid,
            'action' => \core_message\api::MESSAGE_ACTION_READ)));
        $DB->delete_records('messages', array());

        // Test phpunit problem detection.

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'xxxxx';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $sink = $this->redirectMessages();
        try {
            message_send($message);
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        $this->assertCount(0, $sink->get_messages());
        $this->assertDebuggingCalled('Attempt to send msg from a provider xxxxx/instantmessage '.
            'that is inactive or not allowed for the user id='.$user2->id);

        $message->component = 'moodle';
        $message->name = 'xxx';
        $sink = $this->redirectMessages();
        try {
            message_send($message);
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        $this->assertCount(0, $sink->get_messages());
        $this->assertDebuggingCalled('Attempt to send msg from a provider moodle/xxx '.
            'that is inactive or not allowed for the user id='.$user2->id);
        $sink->close();
        $this->assertFalse($DB->record_exists('messages', array()));

        // Invalid users.

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = -1;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $messageid = message_send($message);
        $this->assertFalse($messageid);
        $this->assertDebuggingCalled('Attempt to send msg to unknown user');

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = -1;
        $message->userto = $user2;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $messageid = message_send($message);
        $this->assertFalse($messageid);
        $this->assertDebuggingCalled('Attempt to send msg from unknown user');

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = core_user::NOREPLY_USER;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $messageid = message_send($message);
        $this->assertFalse($messageid);
        $this->assertDebuggingCalled('Attempt to send msg to internal (noreply) user');

        // Some debugging hints for devs.

        unset($user2->emailstop);
        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $sink = $this->redirectMessages();
        $messageid = message_send($message);
        $savedmessages = $sink->get_messages();
        $this->assertCount(1, $savedmessages);
        $savedmessage = reset($savedmessages);
        $this->assertEquals($messageid, $savedmessage->id);
        $this->assertEquals($user1->id, $savedmessage->useridfrom);
        $this->assertEquals($user2->id, $savedmessage->useridto);
        $this->assertDebuggingCalled('Necessary properties missing in userto object, fetching full record');
        $sink->clear();
        $user2->emailstop = '0';
    }

    public function test_send_message() {
        global $DB, $CFG;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');

        // Test basic email redirection.
        $this->assertFileExists("$CFG->dirroot/message/output/email/version.php");
        $this->assertFileExists("$CFG->dirroot/message/output/popup/version.php");

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email' AND name <> 'popup'");
        get_message_processors(true, true);

        $eventsink = $this->redirectEvents();

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'none', $user2);

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

        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('messages', array());
        $DB->delete_records('message_user_actions', array());
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $eventsink->clear();

        // No messages are sent when the feature is disabled.
        $CFG->messaging = 0;

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

        $messageid = message_send($message);
        $this->assertFalse($messageid);
        $this->assertDebuggingCalled('Attempt to send msg from a provider moodle/instantmessage '.
            'that is inactive or not allowed for the user id='.$user2->id);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $sink->clear();
        $DB->delete_records('messages', array());
        $DB->delete_records('message_user_actions', array());
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        $eventsink->clear();

        // Example of a message that is sent and viewed.
        $CFG->messaging = 1;

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
        $message->notification      = '1';

        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('notifications', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('messages', array()));
        $DB->delete_records('notifications', array());
        $events = $eventsink->get_events();
        $this->assertCount(2, $events);
        $this->assertInstanceOf('\core\event\notification_sent', $events[0]);
        $this->assertInstanceOf('\core\event\notification_viewed', $events[1]);
        $eventsink->clear();

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);

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

        $user2->emailstop = '1';

        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('messages', array());
        $DB->delete_records('message_user_actions', array());
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $eventsink->clear();
        $user2->emailstop = '0';

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);

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

        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $this->assertSame($user1->email, $email->from);
        $this->assertSame($user2->email, $email->to);
        $this->assertSame(get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('message_user_actions', array());
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $eventsink->clear();

        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email,popup', $user2);

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

        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $this->assertSame($user1->email, $email->from);
        $this->assertSame($user2->email, $email->to);
        $this->assertSame(get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('messages', array());
        $DB->delete_records('message_user_actions', array());
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $eventsink->clear();

        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'popup', $user2);

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

        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('messages', array());
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $eventsink->clear();

        $this->assertFalse($DB->is_transaction_started());
        $transaction = $DB->start_delegated_transaction();
        if (!$DB->is_transaction_started()) {
            $this->markTestSkipped('Databases that do not support transactions should not be used at all!');
        }
        $transaction->allow_commit();

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'none', $user2);

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

        $transaction = $DB->start_delegated_transaction();
        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $DB->delete_records('messages', array());
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        $eventsink->clear();
        $transaction->allow_commit();
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);

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

        $transaction = $DB->start_delegated_transaction();
        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $savedmessage = $DB->get_record('messages', array('id' => $messageid), '*', MUST_EXIST);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $transaction->allow_commit();
        $events = $eventsink->get_events();
        $this->assertCount(2, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[1]);
        $eventsink->clear();

        $transaction = $DB->start_delegated_transaction();
        message_send($message);
        message_send($message);
        $this->assertCount(3, $DB->get_records('messages'));
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        $transaction->allow_commit();
        $events = $eventsink->get_events();
        $this->assertCount(2, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $this->assertInstanceOf('\core\event\message_sent', $events[1]);
        $eventsink->clear();
        $DB->delete_records('messages', array());

        $transaction = $DB->start_delegated_transaction();
        message_send($message);
        message_send($message);
        $this->assertCount(2, $DB->get_records('messages'));
        $this->assertCount(0, $DB->get_records('message_user_actions'));
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        try {
            $transaction->rollback(new Exception('ignore'));
        } catch (Exception $e) {
            $this->assertSame('ignore', $e->getMessage());
        }
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        $this->assertCount(0, $DB->get_records('messages'));
        message_send($message);
        $this->assertCount(1, $DB->get_records('messages'));
        $this->assertCount(0, $DB->get_records('message_user_actions'));
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\message_sent', $events[0]);
        $sink->clear();
    }

    /**
     * Tests calling message_send() with $eventdata representing a message to an individual conversation.
     *
     * This test will verify:
     * - that the 'messages' record is created.
     * - that the processors will be called for each conversation member, except the sender.
     * - the a single event will be generated - 'message_sent'
     *
     * Note: We won't redirect/capture messages in this test because doing so causes message_send() to return early, before
     * processors and events code is called. We need to test this code here, as we generally redirect messages elsewhere and we
     * need to be sure this is covered.
     */
    public function test_message_send_to_conversation_individual() {
        global $DB;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        // Create some users and a conversation between them.
        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');
        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id], '1:1 project discussion');

        // Generate the message.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->convid            = $conversation->id;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';

        // Content specific to the email processor.
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        // Ensure we're going to hit the email processor for this user.
        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);

        // Now, send a message and verify the message processors (in this case, email) are hit.
        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);

        // Verify the record was created in 'messages'.
        $recordexists = $DB->record_exists('messages', ['id' => $messageid]);
        $this->assertTrue($recordexists);

        // Verify the email information.
        $this->assertSame($user1->email, $email->from);
        $this->assertSame($user2->email, $email->to);

        // The message subject is generated during the call for conversation messages,
        // as the conversation may have many members having different lang preferences.
        $this->assertSame(get_string('unreadnewmessage', 'message', fullname($user1)), $email->subject);

        // The email content will have had an emailtagline appended to it, based on lang prefs,
        // so verify the expected beginning and ends.
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertRegExp('/test message body.*test/s', $email->body);
        $sink->clear();

        // Now, send the message again, and verify that the event fired includes the courseid and conversationid.
        $eventsink = $this->redirectEvents();
        $messageid = message_send($message);
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(\core\event\message_sent::class, $event);
        $this->assertEquals($user1->id, $event->userid);
        $this->assertEquals($user2->id, $event->relateduserid);
        $this->assertEquals($message->courseid, $event->other['courseid']);

        $eventsink->clear();
        $sink->clear();
    }

    /**
     * Tests calling message_send() with $eventdata representing a message to a self-conversation.
     *
     * This test will verify:
     * - that the 'messages' record is created.
     * - that the processors is not called (for now self-conversations are not processed).
     * - the a single event will be generated - 'message_sent'
     *
     * Note: We won't redirect/capture messages in this test because doing so causes message_send() to return early, before
     * processors and events code is called. We need to test this code here, as we generally redirect messages elsewhere and we
     * need to be sure this is covered.
     */
    public function test_message_send_to_self_conversation() {
        global $DB;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        // Create some users and a conversation between them.
        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        set_config('allowedemaildomains', 'example.com');
        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF,
            [$user1->id]);

        // Generate the message.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->convid            = $conversation->id;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';

        // Content specific to the email processor.
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        // Ensure we're going to hit the email processor for this user.
        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user1);

        // Now, send a message and verify the message processors are empty (self-conversations are not processed for now).
        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $sink->clear();
    }

    /**
     * Tests calling message_send() with $eventdata representing a message to an group conversation.
     *
     * This test will verify:
     * - that the 'messages' record is created.
     * - that the processors will be called for each conversation member, except the sender.
     * - the a single event will be generated - 'group_message_sent'
     *
     * Note: We won't redirect/capture messages in this test because doing so causes message_send() to return early, before
     * processors and events code is called. We need to test this code here, as we generally redirect messages elsewhere and we
     * need to be sure this is covered.
     */
    public function test_message_send_to_conversation_group() {
        global $DB;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Create some users and a conversation between them.
        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');

        // Create a group in the course.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1->id, $user1->id);
        groups_add_member($group1->id, $user2->id);
        groups_add_member($group1->id, $user3->id);

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id],
            'Group project discussion',
            \core_message\api::MESSAGE_CONVERSATION_ENABLED,
            'core_group',
            'groups',
            $group1->id,
            context_course::instance($course->id)->id
        );

        // Generate the message.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->convid            = $conversation->id;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';

        // Content specific to the email processor.
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        // Ensure the email processor is enabled for the recipient users.
        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user3);

        // Now, send a message and verify the email processor are hit.
        $messageid = message_send($message);

        $sink = $this->redirectEmails();
        $task = new \message_email\task\send_email_task();
        $task->execute();
        $emails = $sink->get_messages();
        $this->assertCount(2, $emails);

        // Verify the record was created in 'messages'.
        $recordexists = $DB->record_exists('messages', ['id' => $messageid]);
        $this->assertTrue($recordexists);

        // Now, send the message again, and verify that the event fired includes the courseid and conversationid.
        $eventsink = $this->redirectEvents();
        $messageid = message_send($message);
        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(\core\event\group_message_sent::class, $event);
        $this->assertEquals($user1->id, $event->userid);
        $this->assertNull($event->relateduserid);
        $this->assertEquals($message->courseid, $event->other['courseid']);
        $this->assertEquals($message->convid, $event->other['conversationid']);
        $eventsink->clear();
        $sink->clear();
    }

    /**
     * Verify that sending a message to a conversation is an action which can be buffered by the manager if in a DB transaction.
     *
     * This should defer all processor calls (for 2 members in this case), and event creation (1 event).
     */
    public function test_send_message_to_conversation_group_with_buffering() {
        global $DB, $CFG;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');

        // Create a group in the course.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1->id, $user1->id);
        groups_add_member($group1->id, $user2->id);
        groups_add_member($group1->id, $user3->id);

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id],
            'Group project discussion',
            \core_message\api::MESSAGE_CONVERSATION_ENABLED,
            'core_group',
            'groups',
            $group1->id,
            context_course::instance($course->id)->id
        );

        // Test basic email redirection.
        $this->assertFileExists("$CFG->dirroot/message/output/email/version.php");
        $this->assertFileExists("$CFG->dirroot/message/output/popup/version.php");

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email' AND name <> 'popup'");
        get_message_processors(true, true);

        $eventsink = $this->redirectEvents();

        // Will always use the pop-up processor.
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user2);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user3);

        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->convid            = $conversation->id;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';

        $transaction = $DB->start_delegated_transaction();
        $sink = $this->redirectEmails();
        message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $sink->clear();
        $this->assertFalse($DB->record_exists('message_user_actions', array()));
        $events = $eventsink->get_events();
        $this->assertCount(0, $events);
        $eventsink->clear();
        $transaction->allow_commit();
        $events = $eventsink->get_events();
        $task = new \message_email\task\send_email_task();
        $task->execute();
        $emails = $sink->get_messages();
        $this->assertCount(2, $emails);
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\group_message_sent', $events[0]);
    }

    public function test_rollback() {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

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

        $mailsink = $this->redirectEmails();

        // Sending outside of a transaction is fine.
        message_send($message);
        $this->assertEquals(1, $mailsink->count());

        $transaction1 = $DB->start_delegated_transaction();

        $mailsink->clear();
        message_send($message);
        $this->assertEquals(0, $mailsink->count());

        $transaction2 = $DB->start_delegated_transaction();

        $mailsink->clear();
        message_send($message);
        $this->assertEquals(0, $mailsink->count());

        try {
            $transaction2->rollback(new Exception('x'));
            $this->fail('Expecting exception');
        } catch (Exception $e) {}
        $this->assertDebuggingNotCalled();
        $this->assertEquals(0, $mailsink->count());

        $this->assertTrue($DB->is_transaction_started());

        try {
            $transaction1->rollback(new Exception('x'));
            $this->fail('Expecting exception');
        } catch (Exception $e) {}
        $this->assertDebuggingNotCalled();
        $this->assertEquals(0, $mailsink->count());

        $this->assertFalse($DB->is_transaction_started());

        message_send($message);
        $this->assertEquals(1, $mailsink->count());
    }

    public function test_forced_rollback() {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        set_config('noemailever', 1);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

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

        message_send($message);
        $this->assertDebuggingCalled('Not sending email due to $CFG->noemailever config setting');

        $transaction1 = $DB->start_delegated_transaction();

        message_send($message);
        $this->assertDebuggingNotCalled();

        $transaction2 = $DB->start_delegated_transaction();

        message_send($message);
        $this->assertDebuggingNotCalled();

        $DB->force_transaction_rollback();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertDebuggingNotCalled();

        message_send($message);
        $this->assertDebuggingCalled('Not sending email due to $CFG->noemailever config setting');
    }

    public function test_message_attachment_send() {
        global $CFG;
        $this->preventResetByRollback();
        $this->resetAfterTest();

        // Set config setting to allow attachments.
        $CFG->allowattachments = true;
        unset_config('noemailever');

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        // Create a test file.
        $fs = get_file_storage();
        $filerecord = array(
                'contextid' => $context->id,
                'component' => 'core',
                'filearea'  => 'unittest',
                'itemid'    => 99999,
                'filepath'  => '/',
                'filename'  => 'emailtest.txt'
        );
        $file = $fs->create_file_from_string($filerecord, 'Test content');

        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = get_admin();
        $message->userto            = $user;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->attachment        = $file;
        $message->attachname        = 'emailtest.txt';
        $message->notification      = 0;

        // Make sure we are redirecting emails.
        $sink = $this->redirectEmails();
        message_send($message);

        // Get the email that we just sent.
        $emails = $sink->get_messages();
        $email = reset($emails);
        $this->assertTrue(strpos($email->body, 'Content-Disposition: attachment;') !== false);
        $this->assertTrue(strpos($email->body, 'emailtest.txt') !== false);

        // Check if the stored file still exists after remove the temporary attachment.
        $storedfileexists = $fs->file_exists($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
                                             $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename']);
        $this->assertTrue($storedfileexists);
    }

    public function test_send_message_when_muted() {
        $this->preventResetByRollback();
        $this->resetAfterTest();

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        // Create a conversation between the users.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $userfrom->id,
                $userto->id
            ]
        );

        $message = new \core\message\message();
        $message->courseid = 1;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $userfrom;
        $message->convid = $conversation->id;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = 'small message';
        $message->notification = '0';

        $sink = $this->redirectEmails();
        message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $sink->clear();

        // Mute the conversation.
        \core_message\api::mute_conversation($userto->id, $conversation->id);

        $sink = $this->redirectEmails();
        message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $sink->clear();
    }

    /**
     * Is a particular message type in the list of message types.
     * @param string $component
     * @param string $name a message name.
     * @param array $providers as returned by message_get_providers_for_user.
     * @return bool whether the message type is present.
     */
    protected function message_type_present($component, $name, $providers) {
        foreach ($providers as $provider) {
            if ($provider->component == $component && $provider->name == $name) {
                return true;
            }
        }
        return false;
    }
}
