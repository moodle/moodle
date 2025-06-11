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
 * Test gradebook_accessible functionality.
 *
 * @package   theme_snap
 * @category  phpunit
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;
use theme_snap\output\shared;

class gradebookaccess_test extends \advanced_testcase {

    public function test_gradebookaccess_gradesavailableforstuds() {
        global $DB, $PAGE;

        $this->resetAfterTest(true);

        // Get the id for the necessary roles.
        $studentrole = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $editteacherrole = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));

        // Create a course with grades enabled to students.
        $course1 = $this->getDataGenerator()->create_course(array('showgrades' => 1));
        $PAGE->set_course($course1); // This becomes necessary because gradebook_accessible depends on $COURSE.

        // Create two users.
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();

        // Enrol users to created course.
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole);
        $this->getDataGenerator()->enrol_user($teacher->id, $course1->id, $editteacherrole);

        $this->setUser($teacher); // Set the teacher as active user.

        // Check functionality of gradebook_accessible.
        $coursecontext = \context_course::instance($course1->id);
        $isavailable = shared::gradebook_accessible($coursecontext);
        $this->assertTrue($isavailable);

        $this->setUser($student); // Set the student as active user.
        $isavailable = shared::gradebook_accessible($coursecontext);
        $this->assertTrue($isavailable); // As long as showgrades is active, must be available for studs.
    }

    public function test_gradebookaccess_gradesnotavailableforstuds() {
        global $DB, $PAGE;

        $this->resetAfterTest(true);

        // Get the id for the necessary roles.
        $studentrole = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $editteacherrole = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));

        // Create a course with grades disabled to students.
        $course2 = $this->getDataGenerator()->create_course(array('showgrades' => 0));
        $PAGE->set_course($course2); // This becomes necessary because gradebook_accessible depends on $COURSE.

        // Create two users.
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();

        // Enrol users to created course.
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole);
        $this->getDataGenerator()->enrol_user($teacher->id, $course2->id, $editteacherrole);

        $this->setUser($teacher); // Set the teacher as active user.

        // Check functionality of gradebook_accessible.
        $coursecontext = \context_course::instance($course2->id);
        $isavailable = shared::gradebook_accessible($coursecontext);
        $this->assertTrue($isavailable);

        $this->setUser($student); // Set the student as active user.
        $isavailable = shared::gradebook_accessible($coursecontext);
        $this->assertFalse($isavailable); // As long as showgrades is not active, mustn't be available for studs.
    }
}
