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

namespace enrol_meta\external;

use core_external\external_api;

/**
 * Tests for add_instances external class
 *
 * @package    enrol_meta
 * @group      enrol_meta
 * @category   test
 * @copyright  2021 WKS KV Bildung
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class add_instances_test extends \core_external\tests\externallib_testcase {
    /**
     * Test setup
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test add_instances no instances.
     */
    public function test_add_instances_no_instances(): void {
        $this->expectException(\invalid_parameter_exception::class);
        add_instances::execute([]);
    }

    /**
     * Test add_instances missing courses.
     */
    public function test_add_instances_missing_courses(): void {
        $course = self::getDataGenerator()->create_course();

        // Missing meta course.
        try {
            add_instances::execute([['metacourseid' => 1000, 'courseid' => $course->id]]);
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString(get_string('wsinvalidmetacourse', 'enrol_meta', 1000), $e->getMessage());
        }

        // Missing linked course.
        try {
            add_instances::execute([['metacourseid' => $course->id, 'courseid' => 1000]]);
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString(get_string('wsinvalidcourse', 'enrol_meta', 1000), $e->getMessage());
        }
    }

    /**
     * Test add_instances missing capabilities.
     */
    public function test_add_instances_missing_capabilities(): void {
        $metacourse = self::getDataGenerator()->create_course();
        $course = self::getDataGenerator()->create_course();
        $user = self::getDataGenerator()->create_user();
        $this::setUser($user);

        // Missing rights in meta course.
        try {
            add_instances::execute([['metacourseid' => $metacourse->id, 'courseid' => $course->id]]);
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString(get_string('wsinvalidmetacourse', 'enrol_meta', $metacourse->id), $e->getMessage());
        }

        // Add rights for metacourse.
        $metacontext = \context_course::instance($metacourse->id);
        $roleid = $this->assignUserCapability('enrol/meta:config', $metacontext->id);
        $this->assignUserCapability('moodle/course:view', $metacontext->id, $roleid);
        $this->assignUserCapability('moodle/course:enrolconfig', $metacontext->id, $roleid);

        // Missing rights for linked course.
        try {
            add_instances::execute([['metacourseid' => $metacourse->id, 'courseid' => $course->id]]);
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString(get_string('wsinvalidcourse', 'enrol_meta', $course->id), $e->getMessage());
        }

        // Add rights for linked course.
        $context = \context_course::instance($course->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);
        $this->assignUserCapability('enrol/meta:selectaslinked', $context->id, $roleid);

        $result = add_instances::execute([['metacourseid' => $metacourse->id, 'courseid' => $course->id]]);
        $this->assertNotEmpty($result);
    }

    /**
     * Test add_instances.
     */
    public function test_add_instances(): void {
        global $DB;
        $metacourse = self::getDataGenerator()->create_course();
        $course = self::getDataGenerator()->create_course();

        // Sanity check.
        $enrolrecords = $DB->count_records('enrol',
            ['enrol' => 'meta', 'courseid' => $metacourse->id, 'customint1' => $course->id]);
        $this->assertEquals(0, $enrolrecords);

        // Add instance.
        $result = add_instances::execute([['metacourseid' => $metacourse->id, 'courseid' => $course->id]]);
        $result = external_api::clean_returnvalue(add_instances::execute_returns(), $result);
        $this->assertEquals($result[0]['metacourseid'], $metacourse->id);
        $this->assertEquals($result[0]['courseid'], $course->id);
        $this->assertEquals($result[0]['status'], 1);

        // Check instance was created.
        $enrolrecords = $DB->count_records('enrol',
            ['enrol' => 'meta', 'courseid' => $result[0]['metacourseid'], 'customint1' => $result[0]['courseid']]);
        $this->assertEquals(1, $enrolrecords);

        // Add same instance.
        $result = add_instances::execute([['metacourseid' => $metacourse->id, 'courseid' => $course->id]]);
        $result = external_api::clean_returnvalue(add_instances::execute_returns(), $result);
        $this->assertEquals($result[0]['metacourseid'], $metacourse->id);
        $this->assertEquals($result[0]['courseid'], $course->id);
        $this->assertEquals($result[0]['status'], 0);

        // Check no new instance was created.
        $enrolrecords = $DB->count_records('enrol',
            ['enrol' => 'meta', 'courseid' => $result[0]['metacourseid'], 'customint1' => $result[0]['courseid']]);
        $this->assertEquals(1, $enrolrecords);
    }
}
