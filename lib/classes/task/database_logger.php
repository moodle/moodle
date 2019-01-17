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
 * Database logger for task logging.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Database logger for task logging.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_logger implements task_logger {

    /** @var int Type constant for a scheduled task */
    const TYPE_SCHEDULED = 0;

    /** @var int Type constant for an adhoc task */
    const TYPE_ADHOC = 1;

    /**
     * Whether the task is configured and ready to log.
     *
     * @return  bool
     */
    public static function is_configured() : bool {
        return true;
    }

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
            int $dbreads, int $dbwrites, float $timestart, float $timeend) {
        global $DB;

        // Write this log to the database.
        $logdata = (object) [
            'type' => is_a($task, scheduled_task::class) ? self::TYPE_SCHEDULED : self::TYPE_ADHOC,
            'component' => $task->get_component(),
            'classname' => get_class($task),
            'userid' => 0,
            'timestart' => $timestart,
            'timeend' => $timeend,
            'dbreads' => $dbreads,
            'dbwrites' => $dbwrites,
            'result' => (int) $failed,
            'output' => file_get_contents($logpath),
        ];

        if (is_a($task, adhoc_task::class) && $userid = $task->get_userid()) {
            $logdata->userid = $userid;
        }

        $logdata->id = $DB->insert_record('task_log', $logdata);
    }

    /**
     * Whether this task logger has a report available.
     *
     * @return  bool
     */
    public static function has_log_report() : bool {
        return true;
    }

    /**
     * Get any URL available for viewing relevant task log reports.
     *
     * @param   string      $classname The task class to fetch for
     * @return  \moodle_url
     */
    public static function get_url_for_task_class(string $classname) : \moodle_url {
        global $CFG;

        return new \moodle_url("/{$CFG->admin}/tasklogs.php", [
                'filter' => $classname,
            ]);
    }

    /**
     * Cleanup old task logs.
     */
    public static function cleanup() {
        global $CFG, $DB;

        // Delete logs older than the retention period.
        $params = [
            'retentionperiod' => time() - $CFG->task_logretention,
        ];
        $logids = $DB->get_fieldset_select('task_log', 'id', 'timestart < :retentionperiod', $params);
        self::delete_task_logs($logids);

        // Delete logs to retain a minimum number of logs.
        $sql = "SELECT classname FROM {task_log} GROUP BY classname HAVING COUNT(classname) > :retaincount";
        $params = [
            'retaincount' => $CFG->task_logretainruns,
        ];
        $classes = $DB->get_fieldset_sql($sql, $params);

        foreach ($classes as $classname) {
            $notinsql = "";
            $params = [
                'classname' => $classname,
            ];

            $retaincount = (int) $CFG->task_logretainruns;
            if ($retaincount) {
                $keeplogs = $DB->get_records('task_log', [
                        'classname' => $classname,
                    ], 'timestart DESC', 'id', 0, $retaincount);

                if ($keeplogs) {
                    list($notinsql, $params) = $DB->get_in_or_equal(array_keys($keeplogs), SQL_PARAMS_NAMED, 'p', false);
                    $params['classname'] = $classname;
                    $notinsql = " AND id {$notinsql}";
                }
            }

            $logids = $DB->get_fieldset_select('task_log', 'id', "classname = :classname {$notinsql}", $params);
            self::delete_task_logs($logids);
        }
    }

    /**
     * Delete task logs for the specified logs.
     *
     * @param   array   $logids
     */
    public static function delete_task_logs(array $logids) {
        global $DB;

        if (empty($logids)) {
            return;
        }

        $DB->delete_records_list('task_log', 'id', $logids);
    }
}
