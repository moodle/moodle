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
 * This task deletes expired course recyclebin items.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_course_bin extends \core\task\scheduled_task {

    /**
     * Task name.
     */
    public function get_name() {
        return get_string('taskcleanupcoursebin', 'tool_recyclebin');
    }

    /**
     * Delete all expired items.
     */
    public function execute() {
        global $DB;

        // Check if the course bin is disabled or there is no expiry time.
        $lifetime = get_config('tool_recyclebin', 'coursebinexpiry');
        if (!\tool_recyclebin\course_bin::is_enabled() || $lifetime <= 0) {
            return true;
        }

        // Get the items we can delete.
        $items = $DB->get_recordset_select('tool_recyclebin_course', 'timecreated <= :timecreated',
            array('timecreated' => time() - $lifetime));
        foreach ($items as $item) {
            mtrace("[tool_recyclebin] Deleting item '{$item->id}' from the course recycle bin ...");
            $bin = new \tool_recyclebin\course_bin($item->courseid);
            $bin->delete_item($item);
        }
        $items->close();

        return true;
    }
}
