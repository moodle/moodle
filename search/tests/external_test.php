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
 * External function unit tests.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * External function unit tests.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_testcase extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Checks the get_relevant_users function used when selecting users in search filter.
     */
    public function test_get_relevant_users() {
        // Set up two users to search for and one to do the searching.
        $generator = $this->getDataGenerator();
        $student1 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Aardvark']);
        $student2 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Beetle']);
        $student3 = $generator->create_user(['firstname' => 'Zebedee', 'lastname' => 'Boing']);
        $course = $generator->create_course();
        $generator->enrol_user($student1->id, $course->id, 'student');
        $generator->enrol_user($student2->id, $course->id, 'student');
        $generator->enrol_user($student3->id, $course->id, 'student');

        // As student 3, search for the other two.
        $this->setUser($student3);
        $result = external::get_relevant_users('Amelia', 0);
        $this->assertCount(2, $result);

        // Check that the result contains all the expected fields.
        $this->assertEquals($student1->id, $result[0]->id);
        $this->assertEquals('Amelia Aardvark', $result[0]->fullname);
        $this->assertStringContainsString('/u/f2', $result[0]->profileimageurlsmall);

        // Check we aren't leaking information about user email address (for instance).
        $this->assertObjectNotHasAttribute('email', $result[0]);

        // Note: We are not checking search permissions, search by different fields, etc. as these
        // are covered by the core_user::search unit test.
    }
}
