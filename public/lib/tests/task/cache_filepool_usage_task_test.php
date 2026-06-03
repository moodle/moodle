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

/**
 * Unit tests for the cache_filepool_usage_task scheduled task.
 *
 * @package    core
 * @copyright  2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\task\cache_filepool_usage_task
 */
final class cache_filepool_usage_task_test extends \advanced_testcase {
    /**
     * Test that executing the task refreshes a stale cached value.
     */
    public function test_execute_refreshes_stale_cache(): void {
        $this->resetAfterTest();

        // Pre-populate the cache with a stale value.
        $cache = \cache::make('core', 'hub_filepoolusage');
        $stalevalue = 999.999;
        $cache->set('filepoolusage', $stalevalue);

        // Add a file so the real value differs from the stale one.
        $fs = get_file_storage();
        $content = str_repeat('b', 1048576); // 1 MB.
        $fs->create_file_from_string([
            'contextid' => \context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'tasktest.txt',
        ], $content);

        // The task logs disk usage via mtrace(); mark that output as expected.
        $this->expectOutputRegex('/^Disk usage size \(.+\)\n$/');

        $task = new cache_filepool_usage_task();
        $task->execute();

        // The task should have recalculated the value, replacing the stale cache entry.
        $refreshed = $cache->get('filepoolusage');
        $this->assertNotFalse($refreshed);
        $this->assertNotEquals($stalevalue, $refreshed, 'The stale cached value should have been replaced.');
    }
}
