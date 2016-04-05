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
 * Contains class mod_feedback_responses_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Class mod_feedback_responses_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_responses_table extends table_sql {

    /** @var cm_info */
    protected $cm;

    /** @var int */
    protected $grandtotal = null;

    /** @var bool */
    protected $showall = false;

    /** @var string */
    protected $showallparamname = 'showall';

    /**
     * Constructor
     *
     * @param cm_info $cm
     */
    public function __construct(cm_info $cm) {
        $this->cm = $cm;

        parent::__construct('feedback-showentry-list-' . $cm->instance);

        $this->showall = optional_param($this->showallparamname, 0, PARAM_BOOL);
        $this->define_baseurl(new moodle_url('/mod/feedback/show_entries.php',
            ['id' => $this->cm->id]));
        if ($this->showall) {
            $this->baseurl->param($this->showallparamname, $this->showall);
        }

        $this->init();
    }

    /**
     * Initialises table
     */
    protected function init() {

        $tablecolumns = array('userpic', 'fullname', 'completed_timemodified');
        $tableheaders = array(get_string('userpic'), get_string('fullnameuser'), get_string('date'));

        $context = context_module::instance($this->cm->id);
        if (has_capability('mod/feedback:deletesubmissions', $context)) {
            $tablecolumns[] = 'deleteentry';
            $tableheaders[] = '';
        }

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);

        $this->sortable(true, 'lastname', SORT_ASC);
        $this->collapsible(false);
        $this->set_attribute('id', 'showentrytable');

        $params = array();
        $params['anon'] = FEEDBACK_ANONYMOUS_NO;
        $params['instance'] = $this->cm->instance;
        $params['notdeleted'] = 0;

        $ufields = user_picture::fields('u', null, 'userid');
        $fields = 'DISTINCT c.id, c.timemodified as completed_timemodified, '.$ufields;
        $from = '{user} u, {feedback_completed} c';
        $where = 'anonymous_response = :anon
                AND u.id = c.userid
                AND c.feedback = :instance
                AND u.deleted = :notdeleted';

        $group = groups_get_activity_group($this->cm, true);
        if ($group) {
            $from .= ', {groups_members} g';
            $where .= ' AND g.groupid = :group AND g.userid = c.userid';
            $params['group'] = $group;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(DISTINCT c.id) FROM $from WHERE $where", $params);
    }

    /**
     * Prepares column userpic for display
     * @param stdClass $row
     * @return string
     */
    public function col_userpic($row) {
        global $OUTPUT;
        return $OUTPUT->user_picture($row, array('courseid' => $this->cm->course));
    }

    /**
     * Prepares column deleteentry for display
     * @param stdClass $row
     * @return string
     */
    public function col_deleteentry($row) {
        $context = context_module::instance($this->cm->id);
        if (has_capability('mod/feedback:deletesubmissions', $context)) {
            $deleteentryurl = new moodle_url($this->baseurl, ['delete' => $row->id]);
            return html_writer::link($deleteentryurl, get_string('delete_entry', 'feedback'));
        }
    }

    /**
     * Returns a link for viewing a single response
     * @param stdClass $row
     * @return \moodle_url
     */
    protected function get_link_single_entry($row) {
        return new moodle_url($this->baseurl, ['userid' => $row->userid, 'showcompleted' => $row->id]);
    }

    /**
     * Prepares column completed_timemodified for display
     * @param stdClass $student
     * @return string
     */
    public function col_completed_timemodified($student) {
        return html_writer::link($this->get_link_single_entry($student),
                userdate($student->completed_timemodified));
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    public function query_db($pagesize, $useinitialsbar=true) {
        global $DB;
        $this->totalrows = $grandtotal = $this->get_total_responses_count();
        $this->initialbars($useinitialsbar);

        list($wsql, $wparams) = $this->get_sql_where();
        if ($wsql) {
            $this->countsql .= ' AND '.$wsql;
            $this->countparams = array_merge($this->countparams, $wparams);

            $this->sql->where .= ' AND '.$wsql;
            $this->sql->params = array_merge($this->sql->params, $wparams);

            $this->totalrows  = $DB->count_records_sql($this->countsql, $this->countparams);
        }

        if ($this->totalrows > $pagesize) {
            $this->pagesize($pagesize, $this->totalrows);
        }

        if ($sort = $this->get_sql_sort()) {
            $sort = "ORDER BY $sort";
        }
        $sql = "SELECT
                {$this->sql->fields}
                FROM {$this->sql->from}
                WHERE {$this->sql->where}
                {$sort}";

        $this->rawdata = $DB->get_recordset_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
    }

    /**
     * Returns total number of reponses (without any filters applied)
     * @return int
     */
    public function get_total_responses_count() {
        global $DB;
        if ($this->grandtotal === null) {
            $this->grandtotal = $DB->count_records_sql($this->countsql, $this->countparams);
        }
        return $this->grandtotal;
    }

    /**
     * Displays the table
     */
    public function display() {
        global $OUTPUT;
        groups_print_activity_menu($this->cm, $this->baseurl);
        $grandtotal = $this->get_total_responses_count();
        if (!$grandtotal) {
            echo $OUTPUT->box(get_string('nothingtodisplay'), 'generalbox nothingtodisplay');
            return;
        }
        $this->out($this->showall ? $grandtotal : FEEDBACK_DEFAULT_PAGE_COUNT,
                $grandtotal > FEEDBACK_DEFAULT_PAGE_COUNT);

        // Toggle 'Show all' link.
        if ($this->totalrows > FEEDBACK_DEFAULT_PAGE_COUNT) {
            if (!$this->use_pages) {
                echo html_writer::div(html_writer::link(new moodle_url($this->baseurl, [$this->showallparamname => 0]),
                        get_string('showperpage', '', FEEDBACK_DEFAULT_PAGE_COUNT)), 'showall');
            } else {
                echo html_writer::div(html_writer::link(new moodle_url($this->baseurl, [$this->showallparamname => 1]),
                        get_string('showall', '', $this->totalrows)), 'showall');
            }
        }
    }

    /**
     * Returns links to previous/next responses in the list
     * @param stdClass $record
     * @return array array of three elements [$prevresponseurl, $returnurl, $nextresponseurl]
     */
    public function get_reponse_navigation_links($record) {
        $this->setup();
        $grandtotal = $this->get_total_responses_count();
        $this->query_db($grandtotal);
        $lastrow = $thisrow = $nextrow = null;
        $counter = 0;
        $page = 0;
        while ($this->rawdata->valid()) {
            $row = $this->rawdata->current();
            if ($row->id == $record->id) {
                $page = $this->showall ? 0 : floor($counter / FEEDBACK_DEFAULT_PAGE_COUNT);
                $thisrow = $row;
                $this->rawdata->next();
                $nextrow = $this->rawdata->valid() ? $this->rawdata->current() : null;
                break;
            }
            $lastrow = $row;
            $this->rawdata->next();
            $counter++;
        }
        $this->rawdata->close();
        if (!$thisrow) {
            $lastrow = null;
        }
        return [
            $lastrow ? $this->get_link_single_entry($lastrow) : null,
            new moodle_url($this->baseurl, [$this->request[TABLE_VAR_PAGE] => $page]),
            $nextrow ? $this->get_link_single_entry($nextrow) : null,
        ];
    }
}
