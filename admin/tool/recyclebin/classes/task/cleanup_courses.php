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
 * Recycle bin cron task.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_recyclebin\task;

/**
 * This task deletes expired category recyclebin items.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_courses extends \core\task\scheduled_task {
    /**
     * Task name.
     */
    public function get_name() {
        return get_string('cleancategoryrecyclebin', 'tool_recyclebin');
    }

    /**
     * Delete all expired items.
     */
    public function execute() {
        global $DB;

        // Delete courses.
        $lifetime = get_config('tool_recyclebin', 'course_expiry');
        if (!\tool_recyclebin\category::is_enabled() || $lifetime <= 0) {
            return true;
        }

        $items = $DB->get_recordset_select('tool_recyclebin_category', 'deleted < ?', array(time() - (86400 * $lifetime)));
        foreach ($items as $item) {
            mtrace("[RecycleBin] Deleting course {$item->id}...");

            $bin = new \tool_recyclebin\category($item->category);
            $bin->delete_item($item);
        }
        $items->close();

        return true;
    }
}
