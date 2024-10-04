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
 * Syncing enrolments task.
 *
 * @package   enrol_category
 * @author    Farhan Karmali <farhan6318@gmail.com>
 * @copyright Farhan Karmali
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_category\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Syncing enrolments task.
 *
 * @package   enrol_category
 * @author    Farhan Karmali <farhan6318@gmail.com>
 * @copyright Farhan Karmali
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_category_sync extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('enrolcategorysynctask', 'enrol_category');
    }

    /**
     * Run task for syncing category enrolments.
     */
    public function execute() {
        global $CFG;

        if (!enrol_is_enabled('category')) {
            return;
        }

        require_once("$CFG->dirroot/enrol/category/locallib.php");
        $trace = new \null_progress_trace();
        enrol_category_sync_full($trace);
    }
}
