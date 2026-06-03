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
 * Cache filepool usage scheduled task.
 *
 * @package    core
 * @copyright  2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

/**
 * Scheduled task to pre-cache file pool disk usage for the site registration page.
 *
 * Running this task ensures the expensive aggregate query on the files table is
 * executed in the background, so the result is already cached when an admin visits
 * the registration page.
 */
class cache_filepool_usage_task extends scheduled_task {
    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskcachefilepoolusage', 'admin');
    }

    /**
     * Execute the task.
     */
    public function execute(): void {
        // Purge the existing cached value so get_filepool_usage() recalculates a fresh result.
        \cache::make('core', 'hub_filepoolusage')->purge();
        $size = \core\hub\registration::get_filepool_usage();
        mtrace(get_string('diskusagesize', 'hub', $size));
    }
}
