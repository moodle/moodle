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
        unset_config('noemailever');

        // Disable instantmessage provider.
        $disableprovidersetting = 'moodle_instantmessage_disable';
        set_config($disableprovidersetting, 1, 'message');
        $preferences = get_message_output_default_preferences();
        $this->assertTrue($preferences->$disableprovidersetting == 1);

        $message = new stdClass();
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
        $this->assertTrue(phpunit_util::is_redirecting_phpmailer());
        message_send($message);
        $emails = $sink->get_messages();
        $this->assertEmpty($emails);

        // Check message is sent.
        set_config($disableprovidersetting, 0, 'message');
        $preferences = get_message_output_default_preferences();
        $this->assertTrue($preferences->$disableprovidersetting == 0);

        $sink = $this->redirectEmails();
        $this->assertTrue(phpunit_util::is_redirecting_phpmailer());
        message_send($message);
        $emails = $sink->get_messages();
        $email = reset($emails);
        $this->assertEquals($email->subject, 'message subject 1');
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

        $message = new stdClass();
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
        $this->assertTrue(phpunit_util::is_redirecting_phpmailer());
        message_send($message);

        // Get the email that we just sent.
        $emails = $sink->get_messages();
        $email = reset($emails);
        $this->assertTrue(strpos($email->body, 'Content-Disposition: attachment;') !== false);
        $this->assertTrue(strpos($email->body, 'emailtest.txt') !== false);
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
