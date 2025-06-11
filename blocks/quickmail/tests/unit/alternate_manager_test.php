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

use block_quickmail\services\alternate_manager;
use block_quickmail\persistents\alternate_email;
use block_quickmail\exceptions\validation_exception;

class block_quickmail_alternate_manager_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sends_emails;

    public function test_does_not_create_alternate_if_given_invalid_data() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $this->expectException(validation_exception::class);

        $formdata = [
            'email' => '',
            'firstname' => '',
            'lastname' => '',
            'availability' => '',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, $course->id);
    }

    public function test_creating_with_availability_only_requires_course() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $this->expectException(validation_exception::class);

        $formdata = [
            'email' => '',
            'firstname' => '',
            'lastname' => '',
            'availability' => 'only',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, 0);
    }

    public function test_creating_with_availability_course_requires_course() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $this->expectException(validation_exception::class);

        $formdata = [
            'email' => '',
            'firstname' => '',
            'lastname' => '',
            'availability' => 'course',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, 0);
    }

    public function test_creates_alternate_record_with_availability_only_successfully() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $formdata = [
            'email' => 'an@email.com',
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'availability' => 'only',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, $course->id);

        $this->assertInstanceOf(alternate_email::class, $alternate);
        $this->assertEquals('an@email.com', $alternate->get('email'));
        $this->assertEquals('Firsty', $alternate->get('firstname'));
        $this->assertEquals('Lasty', $alternate->get('lastname'));
        $this->assertEquals('', $alternate->get('allowed_role_ids'));
        $this->assertEquals($userteacher->id, $alternate->get('setup_user_id'));
        $this->assertEquals($course->id, $alternate->get('course_id'));
        $this->assertEquals($userteacher->id, $alternate->get('user_id'));
        $this->assertEquals(0, $alternate->get('is_validated'));
    }

    public function test_creates_alternate_record_with_availability_course_successfully() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $formdata = [
            'email' => 'an@email.com',
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'availability' => 'course',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, $course->id);

        $this->assertInstanceOf(alternate_email::class, $alternate);
        $this->assertEquals('an@email.com', $alternate->get('email'));
        $this->assertEquals('Firsty', $alternate->get('firstname'));
        $this->assertEquals('Lasty', $alternate->get('lastname'));
        $this->assertEquals('', $alternate->get('allowed_role_ids'));
        $this->assertEquals($userteacher->id, $alternate->get('setup_user_id'));
        $this->assertEquals($course->id, $alternate->get('course_id'));
        $this->assertEquals(0, $alternate->get('user_id'));
        $this->assertEquals(0, $alternate->get('is_validated'));
    }

    public function test_creates_alternate_record_with_availability_user_successfully() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $formdata = [
            'email' => 'an@email.com',
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'availability' => 'user',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, 0);

        $this->assertInstanceOf(alternate_email::class, $alternate);
        $this->assertEquals('an@email.com', $alternate->get('email'));
        $this->assertEquals('Firsty', $alternate->get('firstname'));
        $this->assertEquals('Lasty', $alternate->get('lastname'));
        $this->assertEquals('', $alternate->get('allowed_role_ids'));
        $this->assertEquals($userteacher->id, $alternate->get('setup_user_id'));
        $this->assertEquals(0, $alternate->get('course_id'));
        $this->assertEquals($userteacher->id, $alternate->get('user_id'));
        $this->assertEquals(0, $alternate->get('is_validated'));
    }

    public function test_sends_confirmation_email_to_user_after_creating_alternate() {
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $formdata = [
            'email' => 'an@email.com',
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'availability' => 'only',
            'allowed_role_ids' => [],
        ];

        $alternate = alternate_manager::create_alternate_for_user($userteacher, $formdata, $course->id);

        $this->assertEquals(1, $this->email_sink_email_count($sink));
        $this->assertEquals(\block_quickmail_string::get('alternate_subject'), $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertEquals('an@email.com', $this->email_in_sink_attr($sink, 1, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_does_not_resend_confirmation_email_for_invalid_alternate_id() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $this->expectException(validation_exception::class);

        $wrongid = $alternate->get('id') + 1;

        alternate_manager::resend_confirmation_email_for_user($wrongid, $userteacher);
    }

    public function test_does_not_resend_confirmation_email_to_an_invalid_user() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $this->expectException(validation_exception::class);

        alternate_manager::resend_confirmation_email_for_user($alternate->get('id'), $userstudents[0]);
    }

    public function test_does_not_resend_confirmation_email_for_already_confirmed() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => true
        ]);

        $this->expectException(validation_exception::class);

        alternate_manager::resend_confirmation_email_for_user($alternate->get('id'), $userteacher);
    }

    public function test_resends_confirmation_email_to_user() {
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        alternate_manager::resend_confirmation_email_for_user($alternate->get('id'), $userteacher);

        $this->assertEquals(1, $this->email_sink_email_count($sink));
        $this->assertEquals(\block_quickmail_string::get('alternate_subject'), $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertEquals($userteacher->email, $this->email_in_sink_attr($sink, 1, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_does_not_confirm_invalid_alternate() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $this->assertEquals(0, $alternate->get('is_validated'));

        $this->expectException(validation_exception::class);

        // Generate, or fetch existing, token for this user and alternate instance.
        // Note: This does not expire!
        $token = get_user_key('blocks/quickmail', $userteacher->id, $alternate->get('id'));

        $wrongid = $alternate->get('id') + 1;

        $alternate = alternate_manager::confirm_alternate_for_user($wrongid, $token, $userteacher);

        $this->assertEquals(0, $alternate->get('is_validated'));
    }

    public function test_does_not_confirm_confirmed_alternate() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => true
        ]);

        $this->expectException(validation_exception::class);

        // Generate, or fetch existing, token for this user and alternate instance.
        // Note: This does not expire!
        $token = get_user_key('blocks/quickmail', $userteacher->id, $alternate->get('id'));

        $alternate = alternate_manager::confirm_alternate_for_user($alternate->get('id'), $token, $userteacher);

        $this->assertEquals(0, $alternate->get('is_validated'));
    }

    public function test_confirms_unconfirmed_alternate() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $this->assertEquals(0, $alternate->get('is_validated'));

        // Generate, or fetch existing, token for this user and alternate instance.
        // Note: This does not expire!
        $token = get_user_key('blocks/quickmail', $userteacher->id, $alternate->get('id'));

        $alternate = alternate_manager::confirm_alternate_for_user($alternate->get('id'), $token, $userteacher);

        $this->assertEquals(1, $alternate->get('is_validated'));
    }

    public function test_does_not_delete_alternate_for_non_setup_user() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $this->expectException(validation_exception::class);

        alternate_manager::delete_alternate_email_for_user($alternate->get('id'), $userstudents[0]);
    }

    public function test_deletes_alternate_for_setup_user() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = alternate_email::create_new([
            'setup_user_id' => $userteacher->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => '',
            'course_id' => $course->id,
            'user_id' => $userteacher->id,
            'email' => $userteacher->email,
            'is_validated' => false
        ]);

        $result = alternate_manager::delete_alternate_email_for_user($alternate->get('id'), $userteacher);

        $this->assertTrue($result);
        $this->assertEquals(0, $alternate->get('timedeleted'));
    }

}
