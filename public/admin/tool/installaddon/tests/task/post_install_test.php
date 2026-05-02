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

declare(strict_types=1);

namespace tool_installaddon\task;

/**
 * Unit tests for the post install task.
 *
 * @package   tool_installaddon
 * @category  test
 * @copyright 2026 Safat Shahin <safat.shahin@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \tool_installaddon\task\post_install
 */
final class post_install_test extends \advanced_testcase {
    /**
     * Tests that the task sets the activity chooser footer plugin to tool_installaddon.
     */
    public function test_execute_sets_activitychooseractivefooter_config(): void {
        $this->resetAfterTest();

        set_config('activitychooseractivefooter', 'hidden');

        $task = new post_install();
        $task->execute();

        $this->assertSame(
            'tool_installaddon',
            get_config('core', 'activitychooseractivefooter'),
        );
    }
}
