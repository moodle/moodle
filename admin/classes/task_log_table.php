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
 * Task log table.
 *
 * @package    core_admin
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Table to display list of task logs.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_log_table extends \table_sql {

    /**
     * Constructor for the task_log table.
     *
     * @param   string      $filter
     * @param   int         $resultfilter
     */
    public function __construct(string $filter = '', int $resultfilter = null) {
        global $DB;

        if (-1 === $resultfilter) {
            $resultfilter = null;
        }

        parent::__construct('tasklogs');

        $columnheaders = [
            'classname'  => get_string('name'),
            'type'       => get_string('tasktype', 'admin'),
            'userid'     => get_string('user', 'admin'),
            'timestart'  => get_string('task_starttime', 'admin'),
            'duration'   => get_string('task_duration', 'admin'),
            'hostname'   => get_string('hostname', 'tool_task'),
            'pid'        => get_string('pid', 'tool_task'),
            'db'         => get_string('task_dbstats', 'admin'),
            'result'     => get_string('task_result', 'admin'),
            'actions'    => '',
        ];
        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // The name column is a header.
        $this->define_header_column('classname');

        // This table is not collapsible.
        $this->collapsible(false);

        // The actions class should not wrap. Use the BS text utility class.
        $this->column_class('actions', 'text-nowrap');

        // Allow pagination.
        $this->pageable(true);

        // Allow sorting. Default to sort by timestarted DESC.
        $this->sortable(true, 'timestart', SORT_DESC);

        // Add filtering.
        $where = [];
        $params = [];
        if (!empty($filter)) {
            $orwhere = [];
            $filter = str_replace('\\', '\\\\', $filter);

            // Check the class name.
            $orwhere[] = $DB->sql_like('classname', ':classfilter', false, false);
            $params['classfilter'] = '%' . $DB->sql_like_escape($filter) . '%';

            $orwhere[] = $DB->sql_like('output', ':outputfilter', false, false);
            $params['outputfilter'] = '%' . $DB->sql_like_escape($filter) . '%';

            $where[] = "(" . implode(' OR ', $orwhere) . ")";
        }

        if (null !== $resultfilter) {
            $where[] = 'tl.result = :result';
            $params['result'] = $resultfilter;
        }

        $where = implode(' AND ', $where);

        $this->set_sql('', '', $where, $params);
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        // Fetch the attempts.
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }

        // TODO Does not support custom user profile fields (MDL-70456).
        $userfieldsapi = \core_user\fields::for_identity(\context_system::instance(), false)->with_userpic();
        $userfields = $userfieldsapi->get_sql('u', false, 'user', 'userid2', false)->selects;

        $where = '';
        if (!empty($this->sql->where)) {
            $where = "WHERE {$this->sql->where}";
        }

        $sql = "SELECT
                    tl.id, tl.type, tl.component, tl.classname, tl.userid, tl.timestart, tl.timeend,
                    tl.hostname, tl.pid,
                    tl.dbreads, tl.dbwrites, tl.result,
                    tl.dbreads + tl.dbwrites AS db,
                    tl.timeend - tl.timestart AS duration,
                    {$userfields}
                FROM {task_log} tl
           LEFT JOIN {user} u ON u.id = tl.userid
                {$where}
                {$sort}";

        $this->pagesize($pagesize, $DB->count_records_sql("SELECT COUNT('x') FROM {task_log} tl {$where}", $this->sql->params));
        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }

    /**
     * Format the name cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_classname($row) : string {
        $output = '';
        if (class_exists($row->classname)) {
            $task = new $row->classname;
            if ($task instanceof \core\task\scheduled_task) {
                $output = $task->get_name();
            }
        }

        $output .= \html_writer::tag('div', "\\{$row->classname}", [
                'class' => 'task-class',
            ]);
        return $output;
    }

    /**
     * Format the type cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_type($row) : string {
        if (\core\task\database_logger::TYPE_SCHEDULED == $row->type) {
            return get_string('task_type:scheduled', 'admin');
        } else {
            return get_string('task_type:adhoc', 'admin');
        }
    }

    /**
     * Format the timestart cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_result($row) : string {
        if ($row->result) {
            return get_string('task_result:failed', 'admin');
        } else {
            return get_string('success');
        }
    }

    /**
     * Format the timestart cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_timestart($row) : string {
        return userdate($row->timestart, get_string('strftimedatetimeshort', 'langconfig'));
    }

    /**
     * Format the duration cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_duration($row) : string {
        $duration = round($row->timeend - $row->timestart, 2);

        if (empty($duration)) {
            // The format_time function returns 'now' when the difference is exactly 0.
            // Note: format_time performs concatenation in exactly this fashion so we should do this for consistency.
            return '0 ' . get_string('secs', 'moodle');
        }

        return format_time($duration);
    }

    /**
     * Format the DB details cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_db($row) : string {
        $output = '';

        $output .= \html_writer::div(get_string('task_stats:dbreads', 'admin', $row->dbreads));
        $output .= \html_writer::div(get_string('task_stats:dbwrites', 'admin', $row->dbwrites));

        return $output;
    }

    /**
     * Format the actions cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_actions($row) : string {
        global $OUTPUT;

        $actions = [];

        $url = new \moodle_url('/admin/tasklogs.php', ['logid' => $row->id]);

        // Quick view.
        $actions[] = $OUTPUT->action_icon(
            $url,
            new \pix_icon('e/search', get_string('view')),
            new \popup_action('click', $url)
        );

        // Download.
        $actions[] = $OUTPUT->action_icon(
            new \moodle_url($url, ['download' => true]),
            new \pix_icon('t/download', get_string('download'))
        );

        return implode('&nbsp;', $actions);
    }

    /**
     * Format the user cell.
     *
     * @param   \stdClass $row
     * @return  string
     */
    public function col_userid($row) : string {
        if (empty($row->userid)) {
            return '';
        }

        $user = (object) [];
        username_load_fields_from_object($user, $row, 'user');

        return fullname($user);
    }
}
