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
 * Adds category tasks to the Panopto plugin
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2018
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../../config.php');
}
require_once(dirname(__FILE__) . '/../lib/panopto_data.php');

/**
 * Handlers for each different course category event type.
 *
 * @copyright Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * When a category is created: course_category_created is called
 * When a category is moved or updated: course_category_updated is called
 */
class block_panopto_categorytasks {

    /**
     * Called when a category has been created
     *
     * @param \core\event\course_category_created $event
     */
    public static function coursecategorycreated(\core\event\course_category_created $event) {

        if (!\panopto_data::is_main_block_configured() ||
            !\panopto_data::has_minimum_version()) {
            return;
        }

        if (get_config('block_panopto', 'enforce_category_structure')) {

            $task = new \block_panopto\task\ensure_category();
            $task->set_custom_data([
                'categoryid' => $event->contextinstanceid,
            ]);

            if (get_config('block_panopto', 'async_tasks')) {
                \core\task\manager::queue_adhoc_task($task);
            } else {
                $task->execute();
            }
        }
    }

    /**
     * Called when a category has been created
     *
     * @param \core\event\course_category_updated $event
     */
    public static function coursecategoryupdated(\core\event\course_category_updated $event) {
        if (!\panopto_data::is_main_block_configured() ||
            !\panopto_data::has_minimum_version()) {
            return;
        }

        if (get_config('block_panopto', 'enforce_category_structure')) {

            $task = new \block_panopto\task\ensure_category();
            $task->set_custom_data([
                'categoryid' => $event->contextinstanceid,
            ]);

            if (get_config('block_panopto', 'async_tasks')) {
                \core\task\manager::queue_adhoc_task($task);
            } else {
                $task->execute();
            }
        }
    }
}
