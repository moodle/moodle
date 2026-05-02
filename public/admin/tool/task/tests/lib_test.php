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

namespace tool_task;

/**
 * Test for the lib class.
 *
 * @package    tool_task
 * @copyright  2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {
    /**
     * Data provider for mtrace
     *
     * @return array
     */
    public static function tool_task_mtrace_wrapper_provider(): array {
        return [
            [
                'A url http://moodle.com',
                'A url <a target="_blank" href="http://moodle.com">http://moodle.com</a>',
            ],
            [
                'A url https://moodle.com',
                'A url <a target="_blank" href="https://moodle.com">https://moodle.com</a>',
            ],
            [
                'A url https://moodle.com post text',
                'A url <a target="_blank" href="https://moodle.com">https://moodle.com</a> post text',
            ],
            [
                'A url https://moodle.com. In a paragraph',
                'A url <a target="_blank" href="https://moodle.com">https://moodle.com</a>. In a paragraph',
            ],
            [
                'A url https://localhost post text',
                'A url <a target="_blank" href="https://localhost">https://localhost</a> post text',
            ],
            [
                'A url https://main.localhost post text',
                'A url <a target="_blank" href="https://main.localhost">https://main.localhost</a> post text',
            ],
            [
                'email info@moodle.com after',
                'email <a href="mailto:info@moodle.com">info@moodle.com</a> after',
            ],
            [
                'A sentence that ends in info@moodle.com. With another sentence.',
                'A sentence that ends in <a href="mailto:info@moodle.com">info@moodle.com</a>. With another sentence.',
            ],
        ];
    }
    /**
     * Test validations for minute field.
     * @dataProvider tool_task_mtrace_wrapper_provider
     * @param string $output task output
     * @param string $expected html
     * @covers ::tool_task_mtrace_wrapper
     */
    public function test_tool_task_mtrace_wrapper(string $output, string $expected): void {
        global $CFG;
        require_once("{$CFG->dirroot}/{$CFG->admin}/tool/task/lib.php");

        $this->expectOutputString($expected);
        $result = tool_task_mtrace_wrapper($output);
    }
}
