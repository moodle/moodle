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
 * MoodleNet share progress table.
 *
 * @package    core
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\moodlenet;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use html_writer;
use moodle_url;
use stdClass;
use table_sql;

/**
 * MoodleNet share progress table.
 *
 * @package    core
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class share_progress_table extends table_sql {

    /** @var int The user id records will be displayed for. */
    protected $userid;

    /**
     * Set up the table.
     *
     * @param string $uniqueid Unique id of table.
     * @param moodle_url $url The base URL.
     * @param int $userid The user id.
     */
    public function __construct($uniqueid, $url, $userid) {
        parent::__construct($uniqueid);
        $this->userid = $userid;
        $this->define_table_columns();
        $this->define_baseurl($url);
        $this->define_table_configs();
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
        $this->set_default_per_page(25);
    }

    /**
     * Set up the columns and headers.
     */
    protected function define_table_columns() {
        // Define headers and columns.
        $cols = [
            'name' => get_string('name'),
            'type' => get_string('moodlenet:columntype'),
            'timecreated' => get_string('moodlenet:columnsenddate'),
            'status' => get_string('moodlenet:columnsendstatus'),
        ];

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
        $this->column_class('status', 'text-center');
    }

    /**
     * Name column.
     *
     * @param stdClass $row Row data.
     * @return string
     */
    protected function col_name(stdClass $row): string {
        global $OUTPUT;

        $class = '';
        // Track deletion of resources on Moodle.
        $deleted = false;
        // Courses.
        if ($row->type == share_recorder::TYPE_COURSE) {
            if ($row->fullname) {
                $name = $row->fullname;
            } else {
                $name = get_string('moodlenet:deletedcourse');
                $deleted = true;
            }
        // Activities.
        } else if ($row->type == share_recorder::TYPE_ACTIVITY) {
            if ($cm = get_coursemodule_from_id('', $row->cmid)) {
                $name = $cm->name;
            } else {
                $name = get_string('moodlenet:deletedactivity');
                $deleted = true;
            }
        }
        if ($deleted) {
            $class = 'font-italic';
        }
        // Add a link to the resource if it was recorded.
        if (!empty($row->resourceurl)) {
            // Apply bold to resource links that aren't deleted.
            if (!$deleted) {
                $class = 'font-weight-bold';
            }
            $icon = $OUTPUT->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ms-1']);
            $text = $name . $icon;
            $attributes = [
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
            ];
            $name = html_writer::link($row->resourceurl, $text, $attributes);
        }

        return html_writer::span($name, $class);
    }

    /**
     * Type column.
     *
     * @param stdClass $row Row data.
     * @return string
     */
    protected function col_type(stdClass $row): string {
        // Courses.
        if ($row->type == share_recorder::TYPE_COURSE) {
            $type = get_string('course');
        // Activities.
        } else if ($row->type == share_recorder::TYPE_ACTIVITY) {
            if ($row->modname) {
                $type = get_string('modulename', $row->modname);
            } else {
                // Alternatively, default to 'activity'.
                $type = get_string('activity');
            }
        }

        return $type;
    }

    /**
     * Time created column (Send date).
     *
     * @param stdClass $row Row data.
     * @return string
     */
    protected function col_timecreated(stdClass $row): string {
        $format = get_string('strftimedatefullshort', 'core_langconfig');
        return userdate($row->timecreated, $format);
    }

    /**
     * Status column (Send status).
     *
     * @param stdClass $row Row data.
     * @return string
     */
    protected function col_status(stdClass $row): string {
        // Display a badge indicating the status of the share.
        if ($row->status == share_recorder::STATUS_IN_PROGRESS) {
            $status = html_writer::span(get_string('inprogress'), 'badge bg-warning text-dark');
        } else if ($row->status == share_recorder::STATUS_SENT) {
            $status = html_writer::span(get_string('sent'), 'badge bg-success text-white');
        } else if ($row->status == share_recorder::STATUS_ERROR) {
            $status = html_writer::span(get_string('error'), 'badge bg-danger text-white');
        }

        return $status;
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return array containing sql to use and an array of params.
     */
    protected function get_sql_and_params($count = false) {
        if ($count) {
            $select = "COUNT(1)";
        } else {
            $select = "msp.id, msp.type, msp.courseid, msp.cmid, msp.timecreated, " .
                "msp.resourceurl, msp.status, c.fullname, md.name AS modname";
        }

        $sql = "SELECT $select
                  FROM {moodlenet_share_progress} msp
             LEFT JOIN {course} c ON c.id = msp.courseid
             LEFT JOIN {course_modules} cm ON cm.course = msp.courseid
                       AND cm.id = msp.cmid
             LEFT JOIN {modules} md ON md.id = cm.module
                 WHERE msp.userid = :userid";

        $params = ['userid' => $this->userid];

        if (!$count) {
            $sql .= " ORDER BY msp.status DESC, msp.timecreated DESC";
        }

        return [$sql, $params];
    }

    /**
     * Query the DB.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        list($countsql, $countparams) = $this->get_sql_and_params(true);
        list($sql, $params) = $this->get_sql_and_params();
        $total = $DB->count_records_sql($countsql, $countparams);
        $this->pagesize($pagesize, $total);
        $this->rawdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Notification to display when there are no results.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $OUTPUT->notification(get_string('moodlenet:nosharedresources'), 'info');
    }
}
