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
 * Form for scheduled tasks admin pages.
 *
 * @deprecated since Moodle 3.9 MDL-63580. Please use the \core\task\manager.
 * @todo final deprecation. To be removed in Moodle 4.3 MDL-63594.
 *
 * @package    tool_task
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_task;

defined('MOODLE_INTERNAL') || die();

/**
 * Running tasks from CLI.
 *
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class run_from_cli {

    /**
     * Find the path of PHP CLI binary.
     *
     * @return string|false The PHP CLI executable PATH
     */
    protected static function find_php_cli_path() {
        global $CFG;

        if (!empty($CFG->pathtophp) && is_executable(trim($CFG->pathtophp))) {
            return $CFG->pathtophp;
        }

        return false;
    }

    /**
     * Returns if Moodle have access to PHP CLI binary or not.
     *
     * @return bool
     */
    public static function is_runnable():bool {
        debugging('run_from_cli class is deprecated. Please use \core\task\manager::run_from_cli() instead.',
            DEBUG_DEVELOPER);
        return \core\task\manager::is_runnable();
    }

    /**
     * Executes a cron from web invocation using PHP CLI.
     *
     * @param \core\task\task_base $task Task that be executed via CLI.
     * @return bool
     * @throws \moodle_exception
     */
    public static function execute(\core\task\task_base $task):bool {
        debugging('run_from_cli class is deprecated. Please use \core\task\manager::run_from_cli() instead.',
            DEBUG_DEVELOPER);
        return \core\task\manager::run_from_cli($task);
    }
}
