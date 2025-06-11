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
use theme_snap\webservice\ws_course_sections;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Test course toc
 * @author    Sebastian Gracia
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
class webservice_ws_course_toc_test extends \advanced_testcase {

    public function test_service_parameters() {
        $params = \theme_snap\webservice\ws_course_sections::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = \theme_snap\webservice\ws_course_sections::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service() {

        $this->resetAfterTest();
        global $OUTPUT;

        $shortname = 'test';
        $action = 'toc';

        // Create course.
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);

        $nullformat = null;
        $loadmodules = true;
        $toc = new \theme_snap\renderables\course_toc($course, $nullformat, $loadmodules);

        $expected = [
            'toc' => $toc->export_for_template($OUTPUT),
        ];

        $serviceresult = \theme_snap\webservice\ws_course_sections::service($course->shortname, $action, 0, 0, 0);

        $this->assertTrue(is_array($serviceresult));
        $this->assertEquals($expected['toc']->footer, $serviceresult['toc']->footer);
        $this->assertEquals($expected['toc']->chapters, $serviceresult['toc']->chapters);
    }

}
