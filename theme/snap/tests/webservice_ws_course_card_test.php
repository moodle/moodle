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
namespace theme_snap;
use theme_snap\webservice\ws_course_card;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Test course card web service
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
class webservice_ws_course_card_test extends \advanced_testcase {

    public function test_service_parameters() {
        $params = ws_course_card::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = ws_course_card::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Enrol user to course.
        $sturole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $sturole->id);

        $this->setUser($user);

        $serviceresult = ws_course_card::service($course->shortname, 1);
        $this->assertTrue(is_array($serviceresult));
    }
}
