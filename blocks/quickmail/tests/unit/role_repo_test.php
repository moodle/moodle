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

use block_quickmail\repos\role_repo;

class block_quickmail_role_repo_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_get_course_selectable_roles() {
        $this->resetAfterTest(true);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users([
            'editingteacher' => 1,
            'teacher' => 3,
            'student' => 40,
        ]);

        $roles = role_repo::get_course_selectable_roles($course);

        $this->assertCount(3, $roles);
        $this->assertIsArray($roles);
        $this->assertArrayHasKey(3, $roles);
        $this->assertIsObject($roles[3]);
        $this->assertObjectHasAttribute('id', $roles[3]);
        $this->assertObjectHasAttribute('name', $roles[3]);
        $this->assertObjectHasAttribute('shortname', $roles[3]);

        // Update the course's settings to exclude editingteacher.
        $newparams = [
            'allowstudents' => '1',
            'roleselection' => '4,5',
            'receipt' => '1',
            'prepend_class' => 'fullname',
            'ferpa' => 'noferpa',
            'downloads' => '1',
            'additionalemail' => '1',
            'default_message_type' => 'email',
            'message_types_available' => 'email',
        ];

        // Update the courses config.
        block_quickmail_config::update_course_config($course, $newparams);

        $roles = role_repo::get_course_selectable_roles($course);

        $this->assertCount(2, $roles);
        $this->assertIsArray($roles);
        $this->assertArrayHasKey(4, $roles);
        $this->assertIsObject($roles[4]);
        $this->assertObjectHasAttribute('id', $roles[4]);
        $this->assertObjectHasAttribute('name', $roles[4]);
        $this->assertObjectHasAttribute('shortname', $roles[4]);
    }

    public function test_get_alternate_email_role_selection_array() {
        $this->resetAfterTest(true);

        $roleselectionarray = role_repo::get_alternate_email_role_selection_array();

        $this->assertCount(3, $roleselectionarray);
        $this->assertIsArray($roleselectionarray);
        $this->assertArrayHasKey(3, $roleselectionarray);
        $this->assertIsString($roleselectionarray[3]);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users([
            'editingteacher' => 1,
            'teacher' => 3,
            'student' => 40,
        ]);

        // Update the course's settings to exclude editingteacher.
        $newparams = [
            'roleselection' => '4,5',
        ];

        // Update the courses config.
        block_quickmail_config::update_course_config($course, $newparams);

        $roleselectionarray = role_repo::get_alternate_email_role_selection_array($course);

        $this->assertCount(2, $roleselectionarray);
    }

    public function test_get_user_role_id_array_in_course() {
        $this->resetAfterTest(true);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users([
            'editingteacher' => 1, // Role id: 3.
            'teacher' => 1, // Role id: 4.
            'student' => 1, // Role id: 5.
        ]);

        $editingteacher = $users['editingteacher'][0];
        $teacher = $users['teacher'][0];
        $student = $users['student'][0];

        // Editingteacher.
        $roleids = role_repo::get_user_roles_in_course($editingteacher->id, $course->id);

        $this->assertIsArray($roleids);
        $this->assertCount(1, $roleids);
        $this->assertContains(3, $roleids);

        // Teacher.
        $roleids = role_repo::get_user_roles_in_course($teacher->id, $course->id);

        $this->assertCount(1, $roleids);
        $this->assertContains(4, $roleids);

        // Student.
        $roleids = role_repo::get_user_roles_in_course($student->id, $course->id);

        $this->assertCount(1, $roleids);
        $this->assertContains(5, $roleids);

        // A nobody.
        $nobody = $this->getDataGenerator()->create_user();

        $roleids = role_repo::get_user_roles_in_course($nobody->id, $course->id);

        $this->assertCount(0, $roleids);

        // Add editingteacher role to teacher.
        $this->assign_role_id_to_user_in_course(3, $teacher, $course);

        $roleids = role_repo::get_user_roles_in_course($teacher->id, $course->id);

        // Should have both roles now.
        $this->assertCount(2, $roleids);
        $this->assertContains(4, $roleids);
        $this->assertContains(3, $roleids);
    }

}
