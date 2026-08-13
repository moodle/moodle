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
 * Tests for the unconfigured_view renderable.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2026 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\output\unconfigured_view
 */
final class unconfigured_view_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var instance $instance */
    private instance $instance;

    /**
     * Set up test course with a BBB activity.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
        [, $cm] = $this->create_instance($this->course);
        $this->instance = instance::get_from_cmid($cm->id);
    }

    /**
     * Admin sees the admin message and a settings link.
     *
     * @covers ::export_for_template
     */
    public function test_admin_sees_settings_link(): void {
        global $PAGE;

        $this->setAdminUser();
        $renderable = new unconfigured_view($this->instance);
        $data = $renderable->export_for_template($PAGE->get_renderer('core'));

        $this->assertEquals(
            get_string('unconfigured_view_heading', 'mod_bigbluebuttonbn'),
            $data['heading']
        );
        $this->assertEquals(
            get_string('unconfigured_view_admin', 'mod_bigbluebuttonbn'),
            $data['message']
        );
        $this->assertNotEmpty($data['settingsurl']);
        $this->assertStringContainsString('modsettingbigbluebuttonbn', $data['settingsurl']);
        $this->assertEquals(
            get_string('unconfigured_view_settings_link', 'mod_bigbluebuttonbn'),
            $data['settingslinktext']
        );
    }

    /**
     * Teacher (editing teacher with manageactivities but not site:config) sees teacher message, no settings link.
     *
     * @covers ::export_for_template
     */
    public function test_teacher_sees_contact_admin_message(): void {
        global $PAGE;

        $teacher = $this->getDataGenerator()->create_and_enrol($this->course, 'editingteacher');
        $this->setUser($teacher);
        $renderable = new unconfigured_view($this->instance);
        $data = $renderable->export_for_template($PAGE->get_renderer('core'));

        $this->assertEquals(
            get_string('unconfigured_view_teacher', 'mod_bigbluebuttonbn'),
            $data['message']
        );
        $this->assertArrayNotHasKey('settingsurl', $data);
    }

    /**
     * Student sees student message, no settings link.
     *
     * @covers ::export_for_template
     */
    public function test_student_sees_contact_teacher_message(): void {
        global $PAGE;

        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $this->setUser($student);
        $renderable = new unconfigured_view($this->instance);
        $data = $renderable->export_for_template($PAGE->get_renderer('core'));

        $this->assertEquals(
            get_string('unconfigured_view_student', 'mod_bigbluebuttonbn'),
            $data['message']
        );
        $this->assertArrayNotHasKey('settingsurl', $data);
    }
}
