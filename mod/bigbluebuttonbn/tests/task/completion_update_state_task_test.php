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
use mod_bigbluebuttonbn\task\completion_update_state;

/**
 * Completion_update_state task tests.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2024 Catalyst IT
 * @author    Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_bigbluebuttonbn\task\completion_update_state
 */
final class completion_update_state_task_test extends advanced_testcase {

    /**
     * Providers data to test_invalid_customdata test
     * @return array
     */
    public static function invalid_customdata_provider(): array {
        return [
            'empty' => [
                'customdata' => [],
                'expectoutput' => "",
                'expectexceptionmessage' => "Task customdata was missing bigbluebuttonbn->id or userid",
            ],
            'bbb id set but is invalid' => [
                'customdata' => [
                    'bigbluebuttonbn' => -1,
                ],
                'expectoutput' => "",
                'expectexceptionmessage' => "Task customdata was missing bigbluebuttonbn->id or userid",
            ],
            'bbb id is valid, but there is no user' => [
                'customdata' => [
                    'bigbluebuttonbn' => ':bbb',
                ],
                'expectoutput' => "",
                'expectexceptionmessage' => "Task customdata was missing bigbluebuttonbn->id or userid",
            ],
            'bbb id is valid, but the user is not given' => [
                'customdata' => [
                    'bigbluebuttonbn' => ':bbb',
                ],
                'expectoutput' => "",
                'expectexceptionmessage' => "Task customdata was missing bigbluebuttonbn->id or userid",
            ],
            'bbb id is valid, but the user given is invalid' => [
                'customdata' => [
                    'bigbluebuttonbn' => ':bbb',
                    'userid' => -1,
                ],
                'expectoutput' => "User does not exist, ignoring.\n",
                'expectexceptionmessage' => "",
            ],
            'bbb and userid is valid' => [
                'customdata' => [
                    'bigbluebuttonbn' => ':bbb',
                    'userid' => ':userid',
                ],
                // Expects this output, since all the necessary data is there.
                'expectoutput' => "Task completion_update_state running for user :userid\nCompletion not enabled\n",
                'expectexceptionmessage' => "",
            ],
        ];
    }
    /**
     * Tests the task handles an invalid cmid gracefully.
     * @param array $customdata customdata to set (with placeholders to replace with real data).
     * @param string $expectoutput any output expected from the test, or empty to not expect output.
     * @param string $expectexceptionmessage exception message expected from test, or empty to expect nothing.
     * @dataProvider invalid_customdata_provider
     */
    public function test_invalid_customdata(array $customdata, string $expectoutput, string $expectexceptionmessage): void {
        $this->resetAfterTest();
        $customdata = (object) $customdata;

        // Replace any placeholders in the customdata.
        if (!empty($customdata->bigbluebuttonbn) && $customdata->bigbluebuttonbn == ':bbb') {
            $course = $this->getDataGenerator()->create_course();
            $module = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
            $customdata->bigbluebuttonbn = $module;
        }

        $user = $this->getDataGenerator()->create_user();

        // Replace userid placeholders.
        if (!empty($customdata->userid) && $customdata->userid == ':userid') {
            $customdata->userid = $user->id;
        }

        $task = new completion_update_state();
        $task->set_custom_data($customdata);

        if (!empty($expectoutput)) {
            $expectoutput = str_replace(':userid', $user->id, $expectoutput);
            $this->expectOutputString($expectoutput);
        }

        if (!empty($expectexceptionmessage)) {
            $this->expectExceptionMessage($expectexceptionmessage);
        }
        $task->execute();
    }
}
