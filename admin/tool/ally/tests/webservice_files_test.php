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
 * Test for files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\abstract_testcase;
use tool_ally\webservice\files;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_files_test extends abstract_testcase {
    /**
     * Test the web service.
     */
    public function test_service() {
        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $course       = $this->getDataGenerator()->create_course();
        $resource1    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $resource2    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $resource3    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $expfile1     = $this->get_resource_file($resource1);
        $expfile2     = $this->get_resource_file($resource2);
        $expfile3     = $this->get_resource_file($resource3);

        // First page with 2 files per page.
        $page = 0;
        $perpage = 2;

        $files = files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(files::service_returns(), $files);

        $this->assertCount(2, $files);
        $file = reset($files);

        $this->match_files($course, $expfile1, $file);

        $file = next($files);

        $this->match_files($course, $expfile2, $file);

        // Second page with 2 files per page.
        $page = 1;

        $files = files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(files::service_returns(), $files);

        $this->assertCount(1, $files);
        $file = reset($files);

        $this->match_files($course, $expfile3, $file);
    }
}
