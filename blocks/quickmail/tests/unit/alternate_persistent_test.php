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

use block_quickmail\persistents\alternate_email;

class block_quickmail_alternate_persistent_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_getters_before_confirmed() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create "only", not-confirmed.
        $alternate = $this->create_alternate($userteacher, $course);

        $this->assertEquals(0, $alternate->get('is_validated'));
        $this->assertEquals(0, $alternate->get('timedeleted'));
        $this->assertEquals('Firsty Lasty', $alternate->get_fullname());
        $this->assertEquals(\block_quickmail_string::get('alternate_waiting'), $alternate->get_status());
    }

    public function test_get_status() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'email@one.com');

        $this->assertEquals(\block_quickmail_string::get('alternate_waiting'), $alternate->get_status());

        $alternate->set('is_validated', 1);
        $alternate->update();

        $this->assertEquals(\block_quickmail_string::get('alternate_confirmed'), $alternate->get_status());
    }

    public function test_get_scope() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'email@one.com');

        $this->assertEquals(\block_quickmail_string::get('alternate_availability_only',
            (object) ['courseshortname' => $course->shortname]),
            $alternate->get_scope());

        $alternate = $this->create_alternate($userteacher, $course, 'user', 'email@two.com');

        $this->assertEquals(\block_quickmail_string::get('alternate_availability_user'), $alternate->get_scope());

        $alternate = $this->create_alternate($userteacher, $course, 'course', 'email@three.com');

        $this->assertEquals(\block_quickmail_string::get('alternate_availability_course',
            (object) ['courseshortname' => $course->shortname]),
            $alternate->get_scope());
    }

    public function test_get_domain() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'email@a-big-email-domain-with-dashes.com');

        $this->assertEquals('a-big-email-domain-with-dashes.com', $alternate->get_domain());
    }

    public function test_gets_allowed_role_ids_as_array() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = $this->create_alternate($userteacher, $course, 'course', 'email@example.com', true, '3,4,5');

        $allowedroles = $alternate->get_allowed_roles();

        $this->assertIsArray($allowedroles);
        $this->assertCount(3, $allowedroles);

        $alternate = $this->create_alternate($userteacher, $course, 'course', 'email@example.com', true, '');

        $allowedroles = $alternate->get_allowed_roles();

        $this->assertIsArray($allowedroles);
        $this->assertCount(0, $allowedroles);
    }

    public function test_gets_all_for_user() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'email@one.com');
        $alternate = $this->create_alternate($userteacher, $course, 'user', 'email@two.com');
        $alternate = $this->create_alternate($userteacher, $course, 'course', 'email@three.com');

        $alternates = alternate_email::get_all_for_user($userteacher->id);

        $this->assertCount(3, $alternates);

        $alternates = alternate_email::get_all_for_user($userstudents[0]->id);

        $this->assertCount(0, $alternates);
    }

    public function test_get_flat_array_for_course_user() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $student1 = $userstudents[0];
        $student2 = $userstudents[1];

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'unconfirmed@one.com');
        $alternate = $this->create_alternate($userteacher, $course, 'user', 'unconfirmed@two.com');
        $alternate = $this->create_alternate($userteacher, $course, 'course', 'unconfirmed@three.com');

        $alternate = $this->create_alternate($userteacher, $course, 'only', 'teacher@one.com', true);
        $alternate = $this->create_alternate($userteacher, $course, 'user', 'teacher@two.com', true);
        $alternate = $this->create_alternate($userteacher, $course, 'course', 'teacher@three.com', true);

        $alternate = $this->create_alternate($student1, $course, 'only', 'student1@one.com', true);
        $alternate = $this->create_alternate($student1, $course, 'user', 'student1@two.com', true);
        $alternate = $this->create_alternate($student1, $course, 'course', 'student1@three.com', true);

        $alternate = $this->create_alternate($student2, $course, 'only', 'student2@one.com', true);
        $alternate = $this->create_alternate($student2, $course, 'user', 'student2@two.com', true);
        $alternate = $this->create_alternate($student2, $course, 'course', 'student2@three.com', true);

        $alternates = alternate_email::get_flat_array_for_course_user($course->id, $userteacher);

        $this->assertIsArray($alternates);
        $this->assertCount(6, $alternates);
    }

    public function test_get_flat_array_for_course_user_while_limiting_by_roles() {
        $this->resetAfterTest(true);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users([
            'editingteacher' => 2,
            'teacher' => 2,
            'student' => 2,
        ]);

        // Get all characters in this thing.

        // Role id: 3.
        $editingteacher1 = $users['editingteacher'][0];
        $editingteacher2 = $users['editingteacher'][1];

        // Role id: 4.
        $teacher1 = $users['teacher'][0];
        $teacher2 = $users['teacher'][1];

        // Role id: 5.
        $student1 = $users['student'][0];
        $student2 = $users['student'][1];

        // Create an alternate for editing teacher 1, available for everyone in course.
        $alternate1editingteacher1 = $this->create_alternate(
                                         $editingteacher1,
                                         $course,
                                         'course',
                                         'editingteacher1@example.com',
                                         true,
                                         '');

        // Create an alternate for editing teacher 1, available for only editing teacher roles in course.
        $alternate2editingteacher1 = $this->create_alternate(
                                         $editingteacher1,
                                         $course,
                                         'course',
                                         'editingteacher2@example.com',
                                         true,
                                         '3');

        // Create an alternate for editing teacher 1, available for editing teacher AND teacher roles in course.
        $alternate3editingteacher1 = $this->create_alternate(
                                         $editingteacher1,
                                         $course,
                                         'course',
                                         'editingteacher2@example.com',
                                         true,
                                         '3,4');

        // Grab alternates for teacher 1.
        $alternates = alternate_email::get_flat_array_for_course_user($course->id, $teacher1, false);

        // Teacher 1 should have access to only 2.
        $this->assertIsArray($alternates);
        $this->assertCount(2, $alternates);
    }



    /**
     * Helpers
     *
     * Only
     * User
     * Course
     *
     */
    private function create_alternate($setupuser,
                                      $course,
                                      $availability = 'only',
                                      $email = '',
                                      $confirmed = false,
                                      $allowedroleids = '') {
        $courseid = $availability !== 'user'
            ? $course->id
            : 0;

        $userid = $availability !== 'course'
            ? $setupuser->id
            : 0;

        return alternate_email::create_new([
            'setup_user_id' => $setupuser->id,
            'firstname' => 'Firsty',
            'lastname' => 'Lasty',
            'allowed_role_ids' => $allowedroleids,
            'course_id' => $courseid,
            'user_id' => $userid,
            'email' => $email ?: 'some@email.com',
            'is_validated' => (int) $confirmed
        ]);
    }
}
