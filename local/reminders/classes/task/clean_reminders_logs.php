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
 * Capability definition(s) for the reminder plugin.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_reminders\task;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/reminders/lib.php');

/**
 * Handler class to cron task of cleaning older reminder tables.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clean_reminders_logs extends \core\task\scheduled_task {

    /**
     * Executes the cleaning cron task.
     *
     * @return void nothing.
     */
    public function execute() {
        clean_local_reminders_logs();
    }

    /**
     * Returns clean task name as 'Clean Local Reminders Logs'.
     *
     * @return string cleaning task name.
     */
    public function get_name() {
        return get_string('reminderstaskclean', 'local_reminders');
    }

}
