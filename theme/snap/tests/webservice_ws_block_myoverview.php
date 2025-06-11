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
use theme_snap\webservice\ws_block_myoverview;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Test Course Overview block web service for Snap
 * @author    Daniel Cifuentes
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class webservice_ws_block_myoverview extends \advanced_testcase {

    public function test_service_parameters() {
        $params = ws_block_myoverview::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = ws_block_myoverview::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service() {
        global $DB;

        $this->resetAfterTest();

        $startdate = gmmktime('0', '0', '0', 10, 24, 2023);
        $enddate = gmmktime('0', '0', '0', 10, 24, 2024);

        $course = $this->getDataGenerator()->create_course(['startdate' => $startdate, 'enddate' => $enddate]);
        $user = $this->getDataGenerator()->create_user();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $studentrole->id);

        $this->setUser($user);

        $classification = 'all';
        $limit = 0;
        $offset = 0;
        $sort = 'fullname';
        $customfieldname = null;
        $customfieldvalue = null;
        $searchvalue = null;
        $yeardata = '2022';
        $progress = null;

        $result = ws_block_myoverview::service(
            $classification,
            $limit,
            $offset,
            $sort,
            $customfieldname,
            $customfieldvalue,
            $searchvalue,
            $yeardata,
            $progress
        );

        $this->assertEmpty($result["courses"]);

        $classification = 'all';
        $limit = 0;
        $offset = 0;
        $sort = 'fullname';
        $customfieldname = null;
        $customfieldvalue = null;
        $searchvalue = null;
        $yeardata = '2024';
        $progress = null;

        $result = ws_block_myoverview::service(
            $classification,
            $limit,
            $offset,
            $sort,
            $customfieldname,
            $customfieldvalue,
            $searchvalue,
            $yeardata,
            $progress
        );

        $this->assertNotEmpty($result["courses"]);
    }
}
