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

namespace mod_bigbluebuttonbn\task;

use advanced_testcase;

/**
 * Class containing the scheduled task for lti module.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\base_send_notification
 * @coversDefaultClass \mod_bigbluebuttonbn\task\base_send_notification
 */
class base_send_notification_test extends advanced_testcase {

    /**
     * Returns mock base_send_notification class
     *
     * @return base_send_notification
     */
    private function get_mock(): base_send_notification {
        return $this->getMockForAbstractClass(
            base_send_notification::class,
            [],
            '',
            true,
            true,
            true,
            []
        );
    }

    /**
     * Returns reflection method for base_send_notification->get_instance
     *
     * @return \ReflectionMethod
     */
    private function get_instance_reflection(): \ReflectionMethod {
        $rc = new \ReflectionClass(base_send_notification::class);
        $rcm = $rc->getMethod('get_instance');
        $rcm->setAccessible(true);
        return $rcm;
    }

    /**
     * Check if set instance ID works correctly
     */
    public function test_set_instance_id(): void {
        $this->resetAfterTest();
        $stub = $this->get_mock();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        $stub->set_instance_id($instancedata->id);

        $rcm = $this->get_instance_reflection();
        $instance = $rcm->invoke($stub);
        $this->assertNotNull($instance);
        $this->assertEquals($instancedata->id, $instance->get_instance_id());
    }

    /**
     * Check if instanceid missing is checked and handled.
     */
    public function test_set_instanceid_missing(): void {
        $this->resetAfterTest();
        $stub = $this->get_mock();
        $rcm = $this->get_instance_reflection();

        // This should throw a coding exception since there is no instanceid set.
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Task custom data was missing instanceid");
        $rcm->invoke($stub);
    }
}
