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
 * Test for invalid files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\abstract_testcase;
use tool_ally\webservice\invalid_files;
use tool_ally\webservice\files;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for invalid files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_invalid_files extends abstract_testcase {
    /**
     * Test the web service.
     */
    public function test_service() {
        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $course1  = $this->getDataGenerator()->create_course();
        $assign11 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $assign12 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $assign13 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $course2  = $this->getDataGenerator()->create_course();
        $assign21 = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);
        $assign22 = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);
        $assign23 = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);
        $invalid1 = $this->create_notwhitelisted_assign_file($assign11, 'invalid1.txt');
        $invalid2 = $this->create_notwhitelisted_assign_file($assign22, 'invalid2.txt');
        $invalid3 = $this->create_notwhitelisted_assign_file($assign13, 'invalid3.txt');
        $valid1   = $this->create_whitelisted_assign_file($assign21, 'valid1.txt');
        $valid2   = $this->create_whitelisted_assign_file($assign12, 'valid2.txt');
        $valid3   = $this->create_whitelisted_assign_file($assign23, 'valid3.txt');

        // First page with 2 files per page.
        $page = 0;
        $perpage = 2;

        // Should be getting exactly 2 invalid files.
        $files = invalid_files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(invalid_files::service_returns(), $files);

        $this->assertCount(2, $files);
        $file = reset($files);

        $this->match_files($course1, $invalid1, $file);

        $file = next($files);

        $this->match_files($course2, $invalid2, $file);

        // Should be getting exactly 2 valid files.
        $files = files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(files::service_returns(), $files);

        $this->assertCount(2, $files);
        $file = reset($files);

        $this->match_files($course2, $valid1, $file);

        $file = next($files);

        $this->match_files($course1, $valid2, $file);

        // Second page with 2 files per page.
        $page = 1;

        // Should be getting exactly 1 invalid file.
        $files = invalid_files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(invalid_files::service_returns(), $files);

        $this->assertCount(1, $files);
        $file = reset($files);

        $this->match_files($course1, $invalid3, $file);

        // Should be getting exactly 1 valid file.
        $files = files::service($page, $perpage);
        $files = \external_api::clean_returnvalue(files::service_returns(), $files);

        $this->assertCount(1, $files);
        $file = reset($files);

        $this->match_files($course2, $valid3, $file);

    }
}
