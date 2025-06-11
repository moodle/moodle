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
use theme_snap\webservice\ws_course_module;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Test ws_course_module web service
 *
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class webservice_ws_course_module_test extends \advanced_testcase {

    public function test_service_parameters() {
        $params = ws_course_module::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = ws_course_module::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service_course_module() {
        $this->resetAfterTest();

        global $CFG;
        $CFG->theme = 'snap';

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $duedate = time() + WEEKSECS;
        $params = [
            'name' => 'Assignment1',
            'course' => 1,
            'duedate' => $duedate,
            'grade' => 100,
        ];
        $assign = $generator->create_instance($params);
        $cmid = $assign->cmid;

        $serviceresult = ws_course_module::service($cmid);
        $this->assertStringContainsString('<li', $serviceresult['html']);
        $this->assertStringContainsString('data-type="Assignment"', $serviceresult['html']);
        $this->assertStringContainsString('id="module-' . $cmid, $serviceresult['html']);
        $this->assertStringContainsString('<div class="snap-assettype">Assignment</div>', $serviceresult['html']);
    }
}
