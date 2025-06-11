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
 * Test for Process status webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\abstract_testcase;
use tool_ally\webservice\processors_status;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for content webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_processors_status extends abstract_testcase {
    public function test_service_return() {
        $returns = processors_status::service_returns();
        $this->assertTrue($returns instanceof \external_single_structure);
    }
    public function test_service() {

        // Save timestamp to compare to oldest event created.
        $basetime = time();
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('push_cli_only', 1, 'tool_ally');
        // Trigger 3 course event.
        $course = $this->getDataGenerator()->create_course();
        $coursetime = time();
        $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_course();
        $section = $this->getDataGenerator()->create_course_section(
            ['section' => 1, 'course' => $course]);
        // Trigger 3 content event.
        course_update_section($course, $section, ['summary' => 'test string']);
        $contenttime = time();
        course_update_section($course, $section, ['summary' => 'test string1']);
        course_update_section($course, $section, ['summary' => 'test string2']);

        $returns = processors_status::execute_service();
        $this->assertObjectHasAttribute('is_valid', $returns);
        $this->assertTrue($returns->is_valid == false);
        $this->assertObjectHasAttribute('is_cli_only', $returns);
        $this->assertTrue($returns->is_cli_only == 1);
        $this->assertObjectHasAttribute('when_cli_only_on', $returns);
        $this->assertObjectHasAttribute('when_cli_only_off', $returns);
        $this->assertObjectHasAttribute('content_events', $returns);
        $this->assertEquals(3, $returns->content_events);
        $this->assertObjectHasAttribute('oldest_content_event', $returns);
        $this->assertTrue(($basetime <= $returns->oldest_content_event) &&  ($returns->oldest_content_event <= $contenttime));
        $this->assertObjectHasAttribute('course_events', $returns);
        $this->assertEquals(3, $returns->course_events);
        $this->assertObjectHasAttribute('oldest_course_event', $returns);
        $this->assertTrue(($basetime <= $returns->oldest_course_event) && ($returns->oldest_course_event <= $coursetime));
    }
}
