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
 * Interface for task logging.
 *
 * @package    core
 * @category   task
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for task logging.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface task_logger {
    /**
     * Whether the task is configured and ready to log.
     *
     * @return  bool
     */
    public static function is_configured(): bool;

    /**
     * Store the log for the specified task.
     *
     * @param   task_base   $task The task that the log belongs to.
     * @param   string      $logpath The path to the log on disk
     * @param   bool        $failed Whether the task failed
     * @param   int         $dbreads The number of DB reads
     * @param   int         $dbwrites The number of DB writes
     * @param   float       $timestart The start time of the task
     * @param   float       $timeend The end time of the task
     */
    public static function store_log_for_task(task_base $task, string $logpath, bool $failed,
            int $dbreads, int $dbwrites, float $timestart, float $timeend);

    /**
     * Whether this task logger has a report available.
     *
     * @return  bool
     */
    public static function has_log_report(): bool;

    /**
     * Get any URL available for viewing relevant task log reports.
     *
     * @param   string      $classname The task class to fetch for
     * @return  \moodle_url
     */
    public static function get_url_for_task_class(string $classname): \moodle_url;
}
