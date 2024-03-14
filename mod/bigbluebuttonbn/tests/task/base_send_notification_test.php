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
     * Check if set instance ID works correctly
     *
     */
    public function test_set_instance_id(): void {
        $this->resetAfterTest();
        $stub = $this->getMockForAbstractClass(
            base_send_notification::class,
            [],
            '',
            true,
            true,
            true,
            []
        );

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        $stub->set_instance_id($instancedata->id);

        $rc = new \ReflectionClass(base_send_notification::class);
        $rcm = $rc->getMethod('get_instance');
        $instance = $rcm->invoke($stub);

        $this->assertEquals($instancedata->id, $instance->get_instance_id());
    }
}
