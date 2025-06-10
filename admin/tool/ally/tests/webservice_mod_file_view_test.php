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
 * Test for file module completion web service.
 * @author    Guy Thomas <citricity@gmail.com>
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\mod_file_view;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/abstract_testcase.php');
require_once($CFG->dirroot . '/files/externallib.php');

/**
 * Test for file module completion web service.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_mod_file_view_test extends abstract_testcase {
    /**
     * Test the web service.
     *
     */
    public function test_service() {
        global $CFG, $DB;
        $this->markTestSkipped('To be reviewed in INT-18689');
        $this->resetAfterTest();

        $CFG->enablecompletion = true;

        $datagen = $this->getDataGenerator();

        $student = $datagen->create_user();

        $course = $datagen->create_course((object) ['enablecompletion' => 1]);

        // Assign capabilities to user testing web service call (this would normally be web service user).
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('mod/resource:view', \context_system::instance()->id, $roleid);

        // Enrol student on course.
        $datagen->enrol_user($student->id, $course->id, 'student');
        $resource = $datagen->create_module('resource', [
                'course' => $course->id,
                'completion' => COMPLETION_TRACKING_AUTOMATIC
            ]
        );
        $DB->set_field('course_modules', 'completionview', 1,
            array('id' => $resource->cmid));
        $file = $this->get_resource_file($resource);

        $result = mod_file_view::service($file->get_pathnamehash(), $student->id);
        $this->assertSame(['success' => true], $result);

        $viewed = $DB->get_field('course_modules_completion', 'viewed', ['coursemoduleid' => $resource->cmid]);
        $this->assertEquals('1', $viewed);
    }

    public function test_service_user_without_access_to_resource() {
        global $DB;

        $this->resetAfterTest();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('mod/resource:view', \context_system::instance()->id, $roleid);

        $datagen = $this->getDataGenerator();
        $student = $datagen->create_user();
        $course = $datagen->create_course();
        $resource    = $datagen->create_module('resource', ['course' => $course->id]);
        $file  = $this->get_resource_file($resource);

        // Enrol student on course.
        $sturole = $DB->get_record('role', ['shortname' => 'student']);
        $datagen->enrol_user($student->id, $course->id, 'student');

        assign_capability('mod/resource:view', CAP_PROHIBIT, $sturole->id, $file->get_contextid());

        accesslib_clear_all_caches_for_unit_testing();

        $this->expectException(\moodle_exception::class);
        mod_file_view::service($file->get_pathnamehash(), $student->id);
    }

    /**
     * Get a guaranteed invalid user id.
     * @return mixed
     */
    protected function get_invalid_user_id() {
        global $DB;
        $rs = $DB->get_records_select('user', '', [], 'ORDER BY id DESC', 'id', 0, 1);
        $row = current($rs);
        $lastid = $row->id + 1;
        return $lastid;
    }

    public function test_service_invalid_user() {
        $this->resetAfterTest();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('mod/resource:view', \context_system::instance()->id, $roleid);

        $course      = $this->getDataGenerator()->create_course();
        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $file        = $this->get_resource_file($resource);

        $this->expectException(\moodle_exception::class);
        $invaliduserid = $this->get_invalid_user_id();
        mod_file_view::service($file->get_pathnamehash(), $invaliduserid);
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

        // Can use fake as file check will fail before it is used.
        $nonexistantfile = 'BADC0FFEE';
        $this->expectException(\moodle_exception::class);
        mod_file_view::service($nonexistantfile, $teacher->id);
    }
}
