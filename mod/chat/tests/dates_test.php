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
 * Contains unit tests for mod_chat\dates.
 *
 * @package   mod_chat
 * @category  test
 * @copyright 2021 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_chat;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_chat\dates.
 */
class dates_test extends advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        // Chat module is disabled by default, enable it for testing.
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('chat', 1);
    }

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public function get_dates_for_module_provider(): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/chat/lib.php');

        $now = time();
        $past = $now - DAYSECS;
        $future = $now + DAYSECS;

        $dailynextchattime = $past + 2 * DAYSECS;
        $weeklynextchattime = $past + 7 * DAYSECS;
        $label = get_string('nextchattime', 'mod_chat');
        return [
            'chattime in the past' => [
                $past, CHAT_SCHEDULE_NONE, []
            ],
            'chattime in the past' => [
                $past, CHAT_SCHEDULE_SINGLE, []
            ],
            'chattime in the future' => [
                $future, CHAT_SCHEDULE_SINGLE, [
                    [
                        'label' => $label,
                        'timestamp' => $future,
                        'dataid' => 'chattime',
                    ],
                ]
            ],
            'future chattime weekly' => [
                $future, CHAT_SCHEDULE_WEEKLY, [
                    [
                        'label' => $label,
                        'timestamp' => $future,
                        'dataid' => 'chattime',
                    ]
                ]
            ],
            'future chattime daily' => [
                $future, CHAT_SCHEDULE_DAILY, [
                    [
                        'label' => $label,
                        'timestamp' => $future,
                        'dataid' => 'chattime',
                    ]
                ]
            ],
            'past chattime daily' => [
                $past, CHAT_SCHEDULE_DAILY, [
                    [
                        'label' => $label,
                        'timestamp' => $dailynextchattime,
                        'dataid' => 'chattime',
                    ],
                ]
            ],
            'past chattime weekly' => [
                $past, CHAT_SCHEDULE_WEEKLY, [
                    [
                        'label' => $label,
                        'timestamp' => $weeklynextchattime,
                        'dataid' => 'chattime',
                    ],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $chattime
     * @param int|null $schedule
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $chattime, ?int $schedule, array $expected) {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $chat = ['course' => $course->id];
        $chat['chattime'] = $chattime;
        $chat['schedule'] = $schedule;
        $modchat = $this->getDataGenerator()->create_module('chat', $chat);
        $this->setUser($user);
        $cm = get_coursemodule_from_instance('chat', $modchat->id);
        $cminfo = cm_info::create($cm);
        $dates = activity_dates::get_dates_for_module($cminfo, (int) $user->id);
        $this->assertEquals($expected, $dates);
    }
}
