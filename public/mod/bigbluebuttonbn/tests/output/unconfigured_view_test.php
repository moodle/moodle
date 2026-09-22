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

namespace mod_bigbluebuttonbn\output;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Unconfigured view output tests.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2026 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
final class unconfigured_view_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Check data for admin users.
     *
     * @covers \mod_bigbluebuttonbn\output\unconfigured_view::export_for_template
     */
    public function test_export_for_template_admin(): void {
        global $PAGE;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        [, , $activity] = $this->create_instance();
        $instance = instance::get_from_instanceid($activity->id);
        $view = new unconfigured_view($instance);
        $data = $view->export_for_template($PAGE->get_renderer('mod_bigbluebuttonbn'));

        $this->assertEquals(get_string('unconfigured_view_heading', 'mod_bigbluebuttonbn'), $data['heading']);
        $this->assertEquals(get_string('unconfigured_view_admin', 'mod_bigbluebuttonbn'), $data['message']);
        $this->assertEquals(get_string('unconfigured_view_settings_link', 'mod_bigbluebuttonbn'), $data['settingslinktext']);
        $this->assertArrayHasKey('settingsurl', $data);
    }

    /**
     * Check data for teachers.
     *
     * @covers \mod_bigbluebuttonbn\output\unconfigured_view::export_for_template
     */
    public function test_export_for_template_teacher(): void {
        global $PAGE;

        $this->resetAfterTest(true);
        $course = $this->get_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        [, , $activity] = $this->create_instance($course);
        $instance = instance::get_from_instanceid($activity->id);
        $view = new unconfigured_view($instance);
        $data = $view->export_for_template($PAGE->get_renderer('mod_bigbluebuttonbn'));

        $this->assertEquals(get_string('unconfigured_view_heading', 'mod_bigbluebuttonbn'), $data['heading']);
        $this->assertEquals(get_string('unconfigured_view_teacher', 'mod_bigbluebuttonbn'), $data['message']);
        $this->assertArrayNotHasKey('settingsurl', $data);
    }

    /**
     * Check data for students.
     *
     * @covers \mod_bigbluebuttonbn\output\unconfigured_view::export_for_template
     */
    public function test_export_for_template_student(): void {
        global $PAGE;

        $this->resetAfterTest(true);
        $course = $this->get_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        [, , $activity] = $this->create_instance($course);
        $instance = instance::get_from_instanceid($activity->id);
        $view = new unconfigured_view($instance);
        $data = $view->export_for_template($PAGE->get_renderer('mod_bigbluebuttonbn'));

        $this->assertEquals(get_string('unconfigured_view_heading', 'mod_bigbluebuttonbn'), $data['heading']);
        $this->assertEquals(get_string('unconfigured_view_student', 'mod_bigbluebuttonbn'), $data['message']);
        $this->assertArrayNotHasKey('settingsurl', $data);
    }
}
