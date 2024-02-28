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
 * Running tasks table.
 *
 * @package    tool_task
 * @copyright  2019 The Open University
 * @copyright  2020 Mikhail Golenkov <golenkovm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
use core\task\manager;

/**
 * Table to display list of running task.
 *
 * @copyright  2019 The Open University
 * @copyright  2020 Mikhail Golenkov <golenkovm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class running_tasks_table extends \table_sql {

    /**
     * Constructor for the running tasks table.
     */
    public function __construct() {
        parent::__construct('runningtasks');

        $columnheaders = [
            'classname'    => get_string('classname', 'tool_task'),
            'type'         => get_string('tasktype', 'admin'),
            'time'         => get_string('taskage', 'tool_task'),
            'timestarted'  => get_string('started', 'tool_task'),
            'hostname'     => get_string('hostname', 'tool_task'),
            'pid'          => get_string('pid', 'tool_task'),
        ];
        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // The name column is a header.
        $this->define_header_column('classname');

        // This table is not collapsible.
        $this->collapsible(false);

        // Allow pagination.
        $this->pageable(true);
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     * @throws \dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $sort = $this->get_sql_sort();
        $this->rawdata = \core\task\manager::get_running_tasks($sort);
    }

    /**
     * Format the classname cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_classname($row): string {
        $output = $row->classname;
        if ($row->type == 'scheduled') {
            if (class_exists($row->classname)) {
                $task = new $row->classname;
                if ($task instanceof \core\task\scheduled_task) {
                    $output .= \html_writer::tag('div', $task->get_name(), ['class' => 'task-class']);
                }
            }
        } else if ($row->type == 'adhoc') {
            $output .= \html_writer::tag('div',
                get_string('adhoctaskid', 'tool_task', $row->id), ['class' => 'task-class']);
        }
        return $output;
    }

    /**
     * Format the type cell.
     *
     * @param   \stdClass $row
     * @return  string
     * @throws  \coding_exception
     */
    public function col_type($row): string {
        if ($row->type == 'scheduled') {
            $output = \html_writer::span(get_string('scheduled', 'tool_task'), 'badge bg-primary text-white');
        } else if ($row->type == 'adhoc') {
            $output = \html_writer::span(get_string('adhoc', 'tool_task'), 'badge bg-dark text-white');
        } else {
            // This shouldn't ever happen.
            $output = '';
        }
        return $output;
    }

    /**
     * Format the time cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_time($row): string {
        global $OUTPUT;

        $taskmethod = "{$row->type}_task_from_record";
        $task = manager::$taskmethod($row);

        $result = $task->get_runtime_result();
        $extra = '';
        if ($result->get_status() != $result::OK) {
            $extra = '<br>';
            $extra .= $OUTPUT->check_result($result);
            $extra .= ' ';
            $extra .= $result->get_details();
        }

        return format_time($row->time) . $extra;
    }

    /**
     * Format the timestarted cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_timestarted($row): string {
        return userdate($row->timestarted);
    }
}
