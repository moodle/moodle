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
 * Recently accessed items helper.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recentlyaccesseditems;

defined('MOODLE_INTERNAL') || die();

/**
 * Recently accessed items helper.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Returns a list of the most recently items accessed by the logged user
     *
     * @param int $limit Restrict result set to this amount
     * @return array List of recent items accessed by userid
     */
    public static function get_recent_items(int $limit = 0) {
        global $USER, $DB;

        $userid = $USER->id;

        $courses = array();
        $recentitems = array();

        if (!isloggedin() or \core\session\manager::is_loggedinas() or isguestuser()) {
            // No access tracking.
            return $recentitems;
        }

        // Determine sort sql clause.
        $sort = 'timeaccess DESC';

        $paramsql = array('userid' => $userid);
        $records = $DB->get_records('block_recentlyaccesseditems', $paramsql, $sort);
        $order = 0;

        // Get array of items by course. Use $order index to keep sql sorted results.
        foreach ($records as $record) {
            $courses[$record->courseid][$order++] = $record;
        }

        // Group by courses to reduce get_fast_modinfo requests.
        foreach ($courses as $key => $items) {
            $modinfo = get_fast_modinfo($key);
            if (!can_access_course($modinfo->get_course(), null, '', true)) {
                continue;
            }
            foreach ($items as $key => $item) {
                // Exclude not visible items.
                if (!$modinfo->cms[$item->cmid]->uservisible) {
                    continue;
                }
                $item->modname = $modinfo->cms[$item->cmid]->modname;
                $item->name = $modinfo->cms[$item->cmid]->name;
                $item->coursename = get_course_display_name_for_list($modinfo->get_course());
                $recentitems[$key] = $item;
            }
        }

        ksort($recentitems);

        // Apply limit.
        if (!$limit) {
            $limit = count($recentitems);
        }
        $recentitems = array_slice($recentitems, 0, $limit);

        return $recentitems;
    }
}