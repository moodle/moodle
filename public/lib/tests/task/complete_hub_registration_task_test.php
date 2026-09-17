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

namespace core\task;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for {@see complete_hub_registration_task}.
 *
 * @package    core
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(complete_hub_registration_task::class)]
final class complete_hub_registration_task_test extends \advanced_testcase {
    /**
     * When the site is not registered (for example, it was unregistered before the task ran),
     * the task must not attempt to contact the hub.
     */
    public function test_execute_does_nothing_when_not_registered(): void {
        $this->resetAfterTest();

        $mocktask = $this->getMockBuilder(complete_hub_registration_task::class)
            ->onlyMethods(['send_full_registration'])
            ->getMock();
        $mocktask->expects($this->never())->method('send_full_registration');

        $mocktask->execute();
    }

    /**
     * When the site is registered, the task must send the full site info to the hub.
     */
    public function test_execute_sends_full_registration_when_registered(): void {
        global $DB;
        $this->resetAfterTest();

        $DB->insert_record('registration_hubs', [
            'token' => 'sometoken',
            'hubname' => 'moodle',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'sometoken',
            'timemodified' => time(),
        ]);

        $mocktask = $this->getMockBuilder(complete_hub_registration_task::class)
            ->onlyMethods(['send_full_registration'])
            ->getMock();
        $mocktask->expects($this->once())->method('send_full_registration');

        $mocktask->execute();
    }

    /**
     * A failure sending the full registration must propagate out of execute() so the adhoc task
     * manager's built-in fail-delay retry mechanism reschedules it, instead of the failure being
     * swallowed.
     */
    public function test_execute_propagates_failure_for_retry(): void {
        global $DB;
        $this->resetAfterTest();

        $DB->insert_record('registration_hubs', [
            'token' => 'sometoken',
            'hubname' => 'moodle',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'sometoken',
            'timemodified' => time(),
        ]);

        $mocktask = $this->getMockBuilder(complete_hub_registration_task::class)
            ->onlyMethods(['send_full_registration'])
            ->getMock();
        $mocktask->method('send_full_registration')
            ->willThrowException(new \moodle_exception('errorconnect', 'hub'));

        $this->expectException(\moodle_exception::class);
        $mocktask->execute();
    }
}
