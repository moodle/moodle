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
 * Testcase class for Ally course_user_count webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../webservice/tests/helpers.php');

use tool_ally\webservice\course_user_count;

/**
 * Testcase class for Ally course_user_count webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_course_user_count_test extends \externallib_advanced_testcase {

    public function test_user_count() {
        global $DB;

        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $course = $this->getDataGenerator()->create_course(['shortname' => 'C1', 'fullname' => 'The Course']);

        $studentcount = 10;
        for ($i = 0; $i < $studentcount; $i++) {
            $studentuser = self::getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($studentuser->id, $course->id, $studentrole->id, 'manual');
        }

        $instructorcount = 5;
        for ($i = 0; $i < $instructorcount; $i++) {
            $instructoruser = self::getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($instructoruser->id, $course->id, $teacherrole->id, 'manual');
        }

        $otherusercount = 3;
        for ($i = 0; $i < $otherusercount; $i++) {
            self::getDataGenerator()->create_user(); // Other users should not appear in count.
        }

        // Test the default parameter values.
        $usercount = course_user_count::service($course->id);

        $this->assertCount(3, $usercount);
        $this->assertEquals($course->id, $usercount['id']);

        // Review if the amount of users enrolled in the course is the same as expected.
        $this->assertEquals($studentcount, $usercount['studentcount']);
        $this->assertEquals($instructorcount, $usercount['instructorcount']);
    }
}
