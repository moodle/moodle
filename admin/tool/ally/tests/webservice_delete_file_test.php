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
 * Test for file delete webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\delete_file;
use tool_ally\local;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for file delete webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_delete_file_test extends abstract_testcase {
    /**
     * Test the web service.
     *
     */
    public function test_service() {
        global $DB;

        $this->resetAfterTest();

        $datagen = $this->getDataGenerator();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('moodle/course:managefiles', \context_system::instance()->id, $roleid);

        $teacher = $datagen->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);

        $course      = $datagen->create_course();
        $resource    = $datagen->create_module('resource', ['course' => $course->id]);
        $file        = $this->get_resource_file($resource);

        $datagen->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $return = delete_file::service($file->get_pathnamehash(), $teacher->id);
        $return = \external_api::clean_returnvalue(delete_file::service_returns(), $return);

        $this->assertSame($return['success'], true);

        // Fetching the new deleted file throws an exception.
        $this->expectException(\coding_exception::class);
        $this->get_resource_file($resource);
    }

    public function test_service_invalid_user() {
        $this->resetAfterTest();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('moodle/course:managefiles', \context_system::instance()->id, $roleid);

        $otheruser = $this->getDataGenerator()->create_user();

        $course      = $this->getDataGenerator()->create_course();
        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $file        = $this->get_resource_file($resource);

        $this->expectException(\moodle_exception::class);
        $return = delete_file::service($file->get_pathnamehash(), $otheruser->id);
        $return = \external_api::clean_returnvalue(delete_file::service_returns(), $return);

        // Check file hasn't been deleted.
        $this->assertInstanceOf(\stored_file, $this->get_resource_file($resource));
    }

    public function test_service_invalid_file() {
        global $DB;

        $this->resetAfterTest();

        $datagen = $this->getDataGenerator();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('moodle/course:managefiles', \context_system::instance()->id, $roleid);

        $teacher = $datagen->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);

        $course      = $datagen->create_course();

        $datagen->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $nonexistantfile = 'BADC0FFEE';
        $this->expectException(\moodle_exception::class);
        delete_file::service($nonexistantfile, $teacher->id);
    }
}
