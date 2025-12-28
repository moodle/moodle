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

namespace core_admin\reportbuilder\local\entities;

use advanced_testcase;

/**
 * Unit tests for task_log entity
 *
 * @package     core_admin
 * @covers      \core_admin\reportbuilder\local\entities\task_log
 * @copyright   2025 Brendan Heywood <brendan@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class task_log_test extends advanced_testcase {
    /**
     * Data provider for test_format_classname
     *
     * @return array
     */
    public static function format_classname_provider(): array {
        return [
            'Non-existent class shows only classname div' => [
                'classname' => 'some\made\up\task_class',
                'expectname' => false,
            ],
            'Existing non-task class shows only classname div' => [
                'classname' => \stdClass::class,
                'expectname' => false,
            ],
            'Valid task class shows task name and classname div' => [
                'classname' => \core\task\analytics_cleanup_task::class,
                'expectname' => true,
            ],
        ];
    }

    /**
     * Tests format_classname output for various class inputs
     *
     * @dataProvider format_classname_provider
     * @param string $classname the class to format
     * @param bool $expectname whether a task name should appear before the div
     */
    public function test_format_classname(string $classname, bool $expectname): void {
        $result = task_log::format_classname($classname);

        // The classname div should always be present.
        $this->assertStringContainsString(
            '<div class="small text-muted">' . '\\' . $classname . '</div>',
            $result
        );

        if ($expectname) {
            $task = new $classname();
            $this->assertStringContainsString($task->get_name(), $result);
            // Task name should appear before the classname div.
            $this->assertLessThan(
                strpos($result, '<div'),
                strpos($result, $task->get_name())
            );
        } else {
            // Output should start directly with the div — no preceding task name.
            $this->assertStringStartsWith('<div', $result);
        }
    }
}
