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

use block_quickmail\validators\message_form_validator;

class block_quickmail_message_form_validator_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        submits_compose_message_form;

    public function test_validate_subject_is_missing() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'subject' => ''
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Missing subject line.', $validator->errors[0]);
    }

    public function test_validate_body_is_missing_with_no_substitution_codes() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => ''
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Missing message body.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_one() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => 'Hey [:firstname I think I may have [:messed up'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_two() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => 'Hey [:firstname I am trying:] this again, did it work?'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_three() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => 'Hey [: firstname:] let me try this again :('
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_four() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => ':] and again'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_five() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => ' :]is this it?[:'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_body_with_substitution_code_typo_scenario_six() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => '[: nothisisit:]'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Message body substitution codes not formatted properly.', $validator->errors[0]);
    }

    public function test_validate_compose_body_with_invalid_substitution_code() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'body' => 'Hello [:firstname:] lets try an [:invalidcode:]. Is your email still [:email:]?'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Custom data key "invalidcode" is not allowed.', $validator->errors[0]);
    }

    public function test_validate_additional_email_list_is_valid() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'additional_emails' => 'test@email.com, another@email.com'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertFalse($validator->has_errors());
    }

    public function test_validate_additional_email_list_is_invalid() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'additional_emails' => 'invalid@email, another@email.com'
        ]);

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('The additional email "invalid@email" you entered is invalid', $validator->errors[0]);
    }

    public function test_validate_invalid_message_type_is_invalid() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'invalid');

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('That send method is not allowed.', $validator->errors[0]);
    }

    public function test_validate_unsupported_message_type_is_invalid() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $this->update_system_config_value('block_quickmail_message_types_available', 'email');

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'message');

        $validator = new message_form_validator($composeformdata);
        $validator->for_course($course);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('That send method is not allowed.', $validator->errors[0]);
    }

}
