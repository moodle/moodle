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
use theme_snap\webservice\ws_coursetools_block_actions;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Test Course tools blocks web service for Snap
 * @author    Daniel Cifuentes
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class webservice_ws_coursetools_block_actions extends \advanced_testcase {

    public function test_service_parameters() {
        $params = ws_coursetools_block_actions::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = ws_coursetools_block_actions::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $editingteacheruser = $this->getDataGenerator()->create_user();
        $editingteacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($editingteacheruser->id,
            $course->id,
            $editingteacherrole->id);

        $studentuser = $this->getDataGenerator()->create_user();
        $studentuserrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($studentuser->id,
            $course->id,
            $studentuserrole->id);

        // Add blocks.
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_pagelayout('course');
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $blockcalendar = $page->blocks->add_block_at_end_of_default_region('calendar_upcoming');
        $blockcomments = $page->blocks->add_block_at_end_of_default_region('comments');

        // Test actions permissions.
        $this->setUser($studentuser);

        // Hide action denied.
        try {
            ws_coursetools_block_actions::service(
                [
                    'action' => 'bui_hideid',
                    'id' => $blockcalendar->instance->id,
                    'courseid' => $course->id
                ]
            );
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        $this->assertCount(0, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcalendar->instance->id, 'visible' => 0]));

        // Show action denied.
        try {
            ws_coursetools_block_actions::service(
                [
                    'action' => 'bui_showid',
                    'id' => $blockcalendar->instance->id,
                    'courseid' => $course->id
                ]
            );
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        $this->assertCount(0, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcalendar->instance->id, 'visible' => 1]));

        // Delete action denied.
        try {
            ws_coursetools_block_actions::service(
                [
                    'action' => 'bui_hideid',
                    'id' => $blockcalendar->instance->id,
                    'courseid' => $course->id
                ]
            );
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        $this->assertCount(1, $DB->get_records('block_instances',
            ['id' => $blockcalendar->instance->id]));

        // Test actions from editing teacher.
        $this->setUser($editingteacheruser);

        // Hide action allowed.
        $hideblock = ws_coursetools_block_actions::service(
            [
                'action' => 'bui_hideid',
                'id' => $blockcalendar->instance->id,
                'courseid' => $course->id
            ]
        );
        $this->assertTrue($hideblock['success']);
        $this->assertCount(1, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcalendar->instance->id, 'visible' => 0]));

        // Show action allowed.
        $showblock = ws_coursetools_block_actions::service(
            [
                'action' => 'bui_showid',
                'id' => $blockcomments->instance->id,
                'courseid' => $course->id
            ]
        );
        $this->assertTrue($showblock['success']);
        $this->assertCount(1, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcomments->instance->id, 'visible' => 1]));

        // Wrong action denied.
        try {
            ws_coursetools_block_actions::service(
                [
                    'action' => 'wrongaction',
                    'id' => $blockcomments->instance->id,
                    'courseid' => $course->id
                ]
            );
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }

        // Delete action allowed.
        $deleteblock = ws_coursetools_block_actions::service(
            [
                'action' => 'bui_deleteid',
                'id' => $blockcomments->instance->id,
                'courseid' => $course->id
            ]
        );

        // Check that the right block was deleted.
        $this->assertTrue($deleteblock['success']);
        $this->assertCount(0, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcomments->instance->id]));
        $this->assertCount(0, $DB->get_records('block_instances',
            ['id' => $blockcomments->instance->id]));
        $this->assertCount(1, $DB->get_records('block_positions',
            ['blockinstanceid' => $blockcalendar->instance->id]));
        $this->assertCount(1, $DB->get_records('block_instances',
            ['id' => $blockcalendar->instance->id]));

    }
}
