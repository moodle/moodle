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
 * Testcase class for Ally courses webservices.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../webservice/tests/helpers.php');

use tool_ally\webservice\courses;

/**
 * Testcase class for Ally courses webservices.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_courses_testcase extends \externallib_advanced_testcase {

    public function test_service() {
        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $course1 = $this->getDataGenerator()->create_course(['shortname' => 'C1', 'fullname' => 'Course 1']);
        $course2 = $this->getDataGenerator()->create_course(['shortname' => 'C2', 'fullname' => 'Course 2']);
        $course3 = $this->getDataGenerator()->create_course(['shortname' => 'C3', 'fullname' => 'Course 3']);

        // Test the default parameter values.
        $courses = courses::service(-1, 100);

        $this->assertCount(4, $courses);

        // Remove the site course.
        array_shift($courses);

        foreach ([$course1, $course2, $course3] as $courseexp) {
            $course = array_shift($courses);

            $this->assertEquals($courseexp->id, $course['id']);
            $this->assertEquals($courseexp->shortname, $course['shortname']);
            $this->assertEquals($courseexp->fullname, $course['fullname']);
        }

        // Test paging.
        $courses = courses::service(1, 1);
        $this->assertCount(1, $courses);
        $this->assertEquals($course1->id, $courses[0]['id']);

        // Test more paging.
        $courses = courses::service(1, 2);
        $this->assertCount(2, $courses);

        $course = array_shift($courses);
        $this->assertEquals($course2->id, $course['id']);
        $course = array_shift($courses);
        $this->assertEquals($course3->id, $course['id']);

        $courses = courses::service(3, 1);
        $this->assertCount(1, $courses);

        $course = array_shift($courses);
        $this->assertEquals($course3->id, $course['id']);
    }
}
