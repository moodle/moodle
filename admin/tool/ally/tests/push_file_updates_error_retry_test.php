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
 * Tests for file push error retrying.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use Prophecy\Argument;
use tool_ally\prophesize_deprecation_workaround_mixin;
use tool_ally\push_config;
use tool_ally\file_processor;
use tool_ally\task\file_updates_task;
use tool_ally\push_file_updates;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');
require_once(__DIR__.'/prophesize_deprecation_workaround_mixin.php');

/**
 * Tests for file push error retrying.
 *
 * @class     tool_ally_push_file_updates_error_retry_test
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class push_file_updates_error_retry_test extends abstract_testcase {
    use prophesize_deprecation_workaround_mixin;

    public function test_retry_increase_push_disabled_task_reset() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Let's set cli only push.
        set_config('push_cli_only', 1, 'tool_ally');

        // Try to push a file update.
        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $filecreated = $this->get_resource_file($resource);
        $this->assertFalse(file_processor::push_file_update($filecreated));

        // Recreate push config to get current counter values.
        $config = new push_config();

        // Cli only remains the same, no more updates can be done until task is run.
        $this->assertTrue($config->is_cli_only());

        // Ally task ran a while back so, please send updates (This resets the push error counter if it works).
        set_config('push_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        // Since the file was not pushed above, the task should call cURL push once.
        $updates = $this->createMock(push_file_updates::class);
        $updates->expects($this->once())
        ->method('send')
        ->with($this->isType('array'))
        ->willReturn(true);

        $task          = new file_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $task->updates = $updates;
        $task->execute();

        // Recreate push config to get current counter values.
        unset($config);
        $config = new push_config();

        // Live updates (Non-cli) should have been reinstated by task.
        $this->assertFalse($config->is_cli_only());
    }
}
