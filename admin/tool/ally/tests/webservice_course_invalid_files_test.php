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
 * Test for course invalid files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local;
use tool_ally\webservice\course_invalid_files;
use tool_ally\webservice\course_files;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for course invalid files webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_course_invalid_files_test extends abstract_testcase {
    /**
     * Test the web service.
     */
    public function test_service() {
        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $course1 = $this->getDataGenerator()->create_course();
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $course2 = $this->getDataGenerator()->create_course();
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);
        $expectedfile1 = $this->create_notwhitelisted_assign_file($assign1);
        $expectedfile2 = $this->create_whitelisted_assign_file($assign2);

        $files = course_invalid_files::service([$course1->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(1, $files);
        $file = reset($files);

        $this->assertEquals($expectedfile1->get_pathnamehash(), $file['id']);
        $this->assertEquals($course1->id, $file['courseid']);
        $this->assertEquals($expectedfile1->get_filename(), $file['name']);
        $this->assertEquals($expectedfile1->get_mimetype(), $file['mimetype']);
        $this->assertEquals($expectedfile1->get_contenthash(), $file['contenthash']);
        $this->assertEquals($expectedfile1->get_timemodified(), local::iso_8601_to_timestamp($file['timemodified']));

        $files = course_files::service([$course1->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(0, $files);

        $files = course_invalid_files::service([$course2->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(0, $files);

        $files = course_files::service([$course2->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(1, $files);
        $file = reset($files);

        $this->assertEquals($expectedfile2->get_pathnamehash(), $file['id']);
        $this->assertEquals($course2->id, $file['courseid']);
        $this->assertEquals($expectedfile2->get_filename(), $file['name']);
        $this->assertEquals($expectedfile2->get_mimetype(), $file['mimetype']);
        $this->assertEquals($expectedfile2->get_contenthash(), $file['contenthash']);
        $this->assertEquals($expectedfile2->get_timemodified(), local::iso_8601_to_timestamp($file['timemodified']));

        // Create a new assignment not whitelisted.
        $this->create_notwhitelisted_assign_file($assign2);

        $files = course_invalid_files::service([$course1->id, $course2->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(2, $files);

        $files = course_files::service([$course1->id, $course2->id]);
        $files = \external_api::clean_returnvalue(course_invalid_files::service_returns(), $files);

        $this->assertCount(1, $files);

    }
}
