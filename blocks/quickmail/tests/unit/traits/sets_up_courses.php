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

// Course set up helpers.
trait sets_up_courses {

    /**
     * Creates a course within a category with 1 teacher, 4 students
     *
     * @return array  course, user_teacher, students[]
     */
    public function setup_course_with_teacher_and_students() {

        // Segun Babalola, 2020-10-30.
        // Some tests are failing because the FERPA and notification settings are not in place.
        // Adding them here (close to test data creation) to avoid those failures.
        $this->update_system_config_value('block_quickmail_notifications_enabled', true);
        $this->update_system_config_value('block_quickmail_ferpa', 'noferpa');

        // Create a course category.
        $category = $this->getDataGenerator()->create_category();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a user (teacher).
        $userteacher = $this->getDataGenerator()->create_user([
            'email' => 'teacher@example.com',
            'username' => 'teacher'
        ]);

        // Create a user (student1).
        $userstudent1 = $this->getDataGenerator()->create_user([
            'email' => 'student1@example.com',
            'username' => 'student1'
        ]);

        // Create a user (student2).
        $userstudent2 = $this->getDataGenerator()->create_user([
            'email' => 'student2@example.com',
            'username' => 'student2'
        ]);

        // Create a user (student3).
        $userstudent3 = $this->getDataGenerator()->create_user([
            'email' => 'student3@example.com',
            'username' => 'student3'
        ]);

        // Create a user (student4).
        $userstudent4 = $this->getDataGenerator()->create_user([
            'email' => 'student4@example.com',
            'username' => 'student4'
        ]);

        // Enrol the teacher in the course.
        $this->getDataGenerator()->enrol_user($userteacher->id, $course->id, 4, 'manual');

        // Enrol the students in the course.
        $this->getDataGenerator()->enrol_user($userstudent1->id, $course->id, 5, 'manual');
        $this->getDataGenerator()->enrol_user($userstudent2->id, $course->id, 5, 'manual');
        $this->getDataGenerator()->enrol_user($userstudent3->id, $course->id, 5, 'manual');
        $this->getDataGenerator()->enrol_user($userstudent4->id, $course->id, 5, 'manual');

        return [
            $course,
            $userteacher,
            [
                $userstudent1,
                $userstudent2,
                $userstudent3,
                $userstudent4
            ]
        ];
    }

    public function get_role_list() {
        $archetypes = get_role_archetypes();

        $results = [];
        $i = 1;

        foreach ($archetypes as $archetype) {
            $results[$i] = $archetype;
            $i++;
        }

        return $results;
    }

    // Manager.
    // Coursecreator.
    // Editingteacher.
    // Teacher.
    // Student.
    // Guest.
    // User.
    // Frontpage.

    // Returns [course, context, enrolled_users].
    public function setup_course_with_users($params = []) {
        // Create a course category.
        $category = $this->getDataGenerator()->create_category();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        $roles = $this->get_role_list();

        // Initialize user results container.
        $enrolledusers = [];

        foreach ($roles as $rolename) {
            $enrolledusers[$rolename] = [];
        }

        foreach ($roles as $roleid => $rolename) {
            if (array_key_exists($rolename, $params)) {
                foreach (range(1, $params[$rolename]) as $i) {
                    $handle = $rolename . $i;

                    // Create a user.
                    $user = $this->getDataGenerator()->create_user([
                        'email' => $handle . '@example.com',
                        'username' => $handle
                    ]);

                    // Enroll user in course.
                    $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid, 'manual');

                    $enrolledusers[$rolename][] = $user;
                }
            }
        }

        return [
            $course,
            context_course::instance($course->id),
            $enrolledusers
        ];
    }

    /**
     * Returns a test course with:
     * - 1 editing teacher
     * - 3 teachers
     * - 40 students
     * - "red" group with 11 members (1 teacher, 10 students)
     * - "yellow" group with 15 members (1 teacher, 14 students)
     * - "blue" group with 15 members (1 teacher, 14 students)
     *
     * @return array [course, course_context, users, groups]
     */
    public function create_course_with_users_and_groups() {
        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users([
            'editingteacher' => 1,
            'teacher' => 3,
            'student' => 40,
        ]);

        $groups = [];

        $studentstart = 1;

        foreach (['red', 'yellow', 'blue'] as $color) {
            // Create a group .
            $groups[$color] = $this->getDataGenerator()->create_group([
                'course_id' => $course->id,
                'courseid' => $course->id,
                'name' => $color
            ]);

            // Assign the first teacher to the group.
            $this->getDataGenerator()->create_group_member([
                'userid' => $users['teacher'][0]->id,
                'groupid' => $groups[$color]->id]
            );

            // Assign a chunk of 10 unique users to the group.
            foreach (range($studentstart, $studentstart + 9) as $i) {
                $this->getDataGenerator()->create_group_member([
                    'userid' => $users['student'][$i - 1]->id,
                    'groupid' => $groups[$color]->id]
                );
            }

            $studentstart += 10;
        }

        // Assign first 4 students to group yellow.
        // (these users are in group red as well).
        foreach (range(1, 4) as $i) {
            $this->getDataGenerator()->create_group_member([
                'userid' => $users['student'][$i - 1]->id,
                'groupid' => $groups['yellow']->id]
            );
        }

        // Assign first 4 students to group blue.
        // (these users are in group yellow as well).
        foreach (range(11, 14) as $i) {
            $this->getDataGenerator()->create_group_member([
                'userid' => $users['student'][$i - 1]->id,
                'groupid' => $groups['blue']->id]
            );
        }

        return [$course, $coursecontext, $users, $groups];
    }

    /*
     * FOR SOME REASON THIS DOES NOT WORK !! :(
     */
    public function assign_configuration_to_course($course, $overrideparams) {
        global $DB, $CFG;

        $params = $this->get_course_config_params($overrideparams);

        $dataobjects = [];

        // Iterate over each given param, inserting each record for this course.
        foreach ($params as $name => $value) {
            $config = new \stdClass;
            $config->coursesid = $course->id;
            $config->name = $name;
            $config->value = $value;

            $dataobjects[] = $config;
        }

        $DB->insert_records('block_quickmail_config', $dataobjects);
    }

    public function report_user_access_in_course($user, $course, $time) {
        global $DB;

        $record = new stdClass();
        $record->userid = $user->id;
        $record->course_id = $course->id;
        $record->courseid = $course->id;
        $record->timeaccess = $time;

        $DB->insert_record('user_lastaccess', $record);
    }

    public function assign_role_id_to_user_in_course($roleid, $user, $course) {
        $coursecontext = \context_course::instance($course->id);

        role_assign($roleid, $user->id, $coursecontext->id);
    }

}
