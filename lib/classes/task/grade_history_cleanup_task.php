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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;
defined('MOODLE_INTERNAL') || die();

/**
 * Simple task to clean grade history tables.
 *
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_history_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskgradehistorycleanup', 'admin');
    }

    /**
     * Cleanup history tables.
     */
    public function execute() {
        global $CFG, $DB;

        if (!empty($CFG->gradehistorylifetime)) {
            $now = time();
            $histlifetime = $now - ($CFG->gradehistorylifetime * DAYSECS);
            $tables = [
                'grade_outcomes_history',
                'grade_categories_history',
                'grade_items_history',
                'grade_grades_history',
                'scale_history'
            ];
            foreach ($tables as $table) {
                if ($DB->delete_records_select($table, "timemodified < ?", [$histlifetime])) {
                    mtrace("    Deleted old grade history records from '$table'");
                }
            }
        }
    }
}
