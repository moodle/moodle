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
 * This file contains the definition for the grading table which subclassses easy_table
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Extends table_sql to provide a table of assignment submissions
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_grading_table extends table_sql implements renderable {
    /** @var assign $assignment */
    private $assignment = null;
    /** @var int $perpage */
    private $perpage = 10;
    /** @var int $rownum (global index of current row in table) */
    private $rownum = -1;
    /** @var renderer_base for getting output */
    private $output = null;
    /** @var stdClass gradinginfo */
    private $gradinginfo = null;
    /** @var int $tablemaxrows */
    private $tablemaxrows = 10000;
    /** @var boolean $quickgrading */
    private $quickgrading = false;

    /**
     * overridden constructor keeps a reference to the assignment class that is displaying this table
     *
     * @param assign $assignment The assignment class
     * @param int $perpage how many per page
     * @param string $filter The current filter
     * @param int $rowoffset For showing a subsequent page of results
     * @param bool $quickgrading Is this table wrapped in a quickgrading form?
     */
    function __construct(assign $assignment, $perpage, $filter, $rowoffset, $quickgrading) {
        global $CFG, $PAGE, $DB;
        parent::__construct('mod_assign_grading');
        $this->assignment = $assignment;
        $this->perpage = $perpage;
        $this->quickgrading = $quickgrading;
        $this->output = $PAGE->get_renderer('mod_assign');

        $this->define_baseurl(new moodle_url($CFG->wwwroot . '/mod/assign/view.php', array('action'=>'grading', 'id'=>$assignment->get_course_module()->id)));

        // do some business - then set the sql

        $currentgroup = groups_get_activity_group($assignment->get_course_module(), true);

        if ($rowoffset) {
            $this->rownum = $rowoffset - 1;
        }

        $users = array_keys( $assignment->list_participants($currentgroup, true));
        if (count($users) == 0) {
            // insert a record that will never match to the sql is still valid.
            $users[] = -1;
        }

        $params = array();
        $params['assignmentid1'] = (int)$this->assignment->get_instance()->id;
        $params['assignmentid2'] = (int)$this->assignment->get_instance()->id;

        $extrauserfields = get_extra_user_fields($this->assignment->get_context());

        $fields = user_picture::fields('u', $extrauserfields) . ', u.id as userid, ';
        $fields .= 's.status as status, s.id as submissionid, s.timecreated as firstsubmission, s.timemodified as timesubmitted, ';
        $fields .= 'g.id as gradeid, g.grade as grade, g.timemodified as timemarked, g.timecreated as firstmarked, g.mailed as mailed, g.locked as locked';
        $from = '{user} u LEFT JOIN {assign_submission} s ON u.id = s.userid AND s.assignment = :assignmentid1' .
                        ' LEFT JOIN {assign_grades} g ON u.id = g.userid AND g.assignment = :assignmentid2';

        $userparams = array();
        $userindex = 0;

        list($userwhere, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
        $where = 'u.id ' . $userwhere;
        $params = array_merge($params, $userparams);

        // The filters do not make sense when there are no submissions, so do not apply them.
        if ($this->assignment->is_any_submission_plugin_enabled()) {
            if ($filter == ASSIGN_FILTER_SUBMITTED) {
                $where .= ' AND s.timecreated > 0 ';
            }
            if ($filter == ASSIGN_FILTER_REQUIRE_GRADING) {
                $where .= ' AND (s.timemodified IS NOT NULL AND
                                 s.status = :submitted AND
                                 (s.timemodified > g.timemodified OR g.timemodified IS NULL))';
                $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            }
            if (strpos($filter, ASSIGN_FILTER_SINGLE_USER) === 0) {
                $userfilter = (int) array_pop(explode('=', $filter));
                $where .= ' AND (u.id = :userid)';
                $params['userid'] = $userfilter;
            }
        }
        $this->set_sql($fields, $from, $where, $params);

        $columns = array();
        $headers = array();

        // Select
        if (!$this->is_downloading()) {
            $columns[] = 'select';
            $headers[] = get_string('select') . '<div class="selectall"><input type="checkbox" name="selectall" title="' . get_string('selectall') . '"/></div>';
        }

        // User picture
        $columns[] = 'picture';
        $headers[] = get_string('pictureofuser');

        // Fullname
        $columns[] = 'fullname';
        $headers[] = get_string('fullname');
        foreach ($extrauserfields as $extrafield) {
            $columns[] = $extrafield;
            $headers[] = get_user_field_name($extrafield);
        }

        // Submission status
        if ($assignment->is_any_submission_plugin_enabled()) {
            $columns[] = 'status';
            $headers[] = get_string('status');
        }


        // Grade
        $columns[] = 'grade';
        $headers[] = get_string('grade');
        if (!$this->is_downloading()) {
            // We have to call this column userid so we can use userid as a default sortable column.
            $columns[] = 'userid';
            $headers[] = get_string('edit');
        }

        // Submission plugins
        if ($assignment->is_any_submission_plugin_enabled()) {
            $columns[] = 'timesubmitted';
            $headers[] = get_string('lastmodifiedsubmission', 'assign');

            foreach ($this->assignment->get_submission_plugins() as $plugin) {
                if ($plugin->is_visible() && $plugin->is_enabled()) {
                    $columns[] = 'assignsubmission_' . $plugin->get_type();
                    $headers[] = $plugin->get_name();
                }
            }
        }

        // time marked
        $columns[] = 'timemarked';
        $headers[] = get_string('lastmodifiedgrade', 'assign');

        // Feedback plugins
        foreach ($this->assignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                $columns[] = 'assignfeedback_' . $plugin->get_type();
                $headers[] = $plugin->get_name();
            }
        }

        // final grade
        $columns[] = 'finalgrade';
        $headers[] = get_string('finalgrade', 'grades');

        // load the grading info for all users
        $this->gradinginfo = grade_get_grades($this->assignment->get_course()->id, 'mod', 'assign', $this->assignment->get_instance()->id, $users);

        if (!empty($CFG->enableoutcomes) && !empty($this->gradinginfo->outcomes)) {
            $columns[] = 'outcomes';
            $headers[] = get_string('outcomes', 'grades');
        }


        // set the columns
        $this->define_columns($columns);
        $this->define_headers($headers);
        foreach ($extrauserfields as $extrafield) {
             $this->column_class($extrafield, $extrafield);
        }
        // We require at least one unique column for the sort.
        $this->sortable(true, 'userid');
        $this->no_sorting('finalgrade');
        $this->no_sorting('userid');
        $this->no_sorting('select');
        $this->no_sorting('outcomes');

        foreach ($this->assignment->get_submission_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                $this->no_sorting('assignsubmission_' . $plugin->get_type());
            }
        }
        foreach ($this->assignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                $this->no_sorting('assignfeedback_' . $plugin->get_type());
            }
        }

    }

    /**
     * Before adding each row to the table make sure rownum is incremented
     *
     * @param array $row row of data from db used to make one row of the table.
     * @return array one row for the table
     */
    function format_row($row) {
        if ($this->rownum < 0) {
            $this->rownum = $this->currpage * $this->pagesize;
        } else {
            $this->rownum += 1;
        }

        return parent::format_row($row);
    }

    /**
     * Add the userid to the row class so it can be updated via ajax
     *
     * @param stdClass $row The row of data
     * @return string The row class
     */
    function get_row_class($row) {
        return 'user' . $row->userid;
    }

    /**
     * Return the number of rows to display on a single page
     *
     * @return int The number of rows per page
     */
    function get_rows_per_page() {
        return $this->perpage;
    }

    /**
     * Display a grade with scales etc.
     *
     * @param string $grade
     * @param boolean $editable
     * @param int $userid The user id of the user this grade belongs to
     * @param int $modified Timestamp showing when the grade was last modified
     * @return string The formatted grade
     */
    function display_grade($grade, $editable, $userid, $modified) {
        if ($this->is_downloading()) {
            return $grade;
        }
        $o = $this->assignment->display_grade($grade, $editable, $userid, $modified);
        return $o;
    }

    /**
     * Format a list of outcomes
     *
     * @param stdClass $row
     * @return string
     */
    function col_outcomes(stdClass $row) {
        $outcomes = '';
        foreach($this->gradinginfo->outcomes as $index=>$outcome) {
            $options = make_grades_menu(-$outcome->scaleid);

            $options[0] = get_string('nooutcome', 'grades');
            if ($this->quickgrading && !($outcome->grades[$row->userid]->locked)) {
                $select = '<select name="outcome_' . $index . '_' . $row->userid . '" class="quickgrade">';
                foreach ($options as $optionindex => $optionvalue) {
                    $selected = '';
                    if ($outcome->grades[$row->userid]->grade == $optionindex) {
                        $selected = 'selected="selected"';
                    }
                    $select .= '<option value="' . $optionindex . '"' . $selected . '>' . $optionvalue . '</option>';
                }
                $select .= '</select>';
                $outcomes .= $this->output->container($outcome->name . ': ' . $select, 'outcome');
            } else {
                $outcomes .= $this->output->container($outcome->name . ': ' . $options[$outcome->grades[$row->userid]->grade], 'outcome');
            }
        }

        return $outcomes;
    }


    /**
     * Format a user picture for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_picture(stdClass $row) {
        if ($row->picture) {
            return $this->output->user_picture($row);
        }
        return '';
    }

    /**
     * Format a user record for display (link to profile)
     *
     * @param stdClass $row
     * @return string
     */
    function col_fullname($row) {
        $courseid = $this->assignment->get_course()->id;
        $link= new moodle_url('/user/view.php', array('id' =>$row->id, 'course'=>$courseid));
        return $this->output->action_link($link, fullname($row));
    }

    /**
     * Insert a checkbox for selecting the current row for batch operations
     *
     * @param stdClass $row
     * @return string
     */
    function col_select(stdClass $row) {
        return '<input type="checkbox" name="selectedusers" value="' . $row->userid . '"/>';
    }

    /**
     * Return a users grades from the listing of all grade data for this assignment
     *
     * @param int $userid
     * @return mixed stdClass or false
     */
    private function get_gradebook_data_for_user($userid) {
        if (isset($this->gradinginfo->items[0]) && $this->gradinginfo->items[0]->grades[$userid]) {
            return $this->gradinginfo->items[0]->grades[$userid];
        }
        return false;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_grade(stdClass $row) {
        $o = '';

        $link = '';
        $separator = '';
        $grade = '';

        if (!$this->is_downloading()) {
            $name = fullname($row);
            $icon = $this->output->pix_icon('gradefeedback', get_string('gradeuser', 'assign', $name), 'mod_assign');
            $url = new moodle_url('/mod/assign/view.php',
                                            array('id' => $this->assignment->get_course_module()->id,
                                                  'rownum'=>$this->rownum,'action'=>'grade'));
            $link = $this->output->action_link($url, $icon);
            $separator = $this->output->spacer(array(), true);
        }
        $gradingdisabled = $this->assignment->grading_disabled($row->id);
        $grade = $this->display_grade($row->grade, $this->quickgrading && !$gradingdisabled, $row->userid, $row->timemarked);

        //return $grade . $separator . $link;
        return $link . $separator . $grade;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_finalgrade(stdClass $row) {
        $o = '';

        $grade = $this->get_gradebook_data_for_user($row->userid);
        if ($grade) {
            $o = $this->display_grade($grade->grade, false, $row->userid, $row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_timemarked(stdClass $row) {
        $o = '-';

        if ($row->timemarked && $row->grade !== NULL && $row->grade >= 0) {
            $o = userdate($row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_timesubmitted(stdClass $row) {
        $o = '-';

        if ($row->timesubmitted) {
            $o = userdate($row->timesubmitted);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_status(stdClass $row) {
        $o = '';

        if ($this->assignment->is_any_submission_plugin_enabled()) {

            $o .= $this->output->container(get_string('submissionstatus_' . $row->status, 'assign'), array('class'=>'submissionstatus' .$row->status));
            if ($this->assignment->get_instance()->duedate && $row->timesubmitted > $this->assignment->get_instance()->duedate) {
                $o .= $this->output->container(get_string('submittedlateshort', 'assign', format_time($row->timesubmitted - $this->assignment->get_instance()->duedate)), 'latesubmission');
            }
            if ($row->locked) {
                $o .= $this->output->container(get_string('submissionslockedshort', 'assign'), 'lockedsubmission');
            }
            if ($row->grade !== NULL && $row->grade >= 0) {
                $o .= $this->output->container(get_string('graded', 'assign'), 'submissiongraded');
            }
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_userid(stdClass $row) {
        $edit = '';

        $actions = array();

        $url = new moodle_url('/mod/assign/view.php',
                                            array('id' => $this->assignment->get_course_module()->id,
                                                  'rownum'=>$this->rownum,'action'=>'grade'));
        if (!$row->grade) {
           $description = get_string('grade');
        }else{
           $description = get_string('updategrade','assign');
        }
        $actions[$url->out(false)] = $description;

        if (!$row->status || $row->status == ASSIGN_SUBMISSION_STATUS_DRAFT || !$this->assignment->get_instance()->submissiondrafts) {
            if (!$row->locked) {
                $url = new moodle_url('/mod/assign/view.php', array('id' => $this->assignment->get_course_module()->id,
                                                                    'userid'=>$row->id,
                                                                    'action'=>'lock',
                                                                    'sesskey'=>sesskey(),
                                                                    'page'=>$this->currpage));
                $description = get_string('preventsubmissionsshort', 'assign');
                $actions[$url->out(false)] = $description;
            } else {
                $url = new moodle_url('/mod/assign/view.php', array('id' => $this->assignment->get_course_module()->id,
                                                                    'userid'=>$row->id,
                                                                    'action'=>'unlock',
                                                                    'sesskey'=>sesskey(),
                                                                    'page'=>$this->currpage));
                $description = get_string('allowsubmissionsshort', 'assign');
                $actions[$url->out(false)] = $description;
            }
        }
        if ($row->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED && $this->assignment->get_instance()->submissiondrafts) {
            $url = new moodle_url('/mod/assign/view.php', array('id' => $this->assignment->get_course_module()->id,
                                                                'userid'=>$row->id,
                                                                'action'=>'reverttodraft',
                                                                'sesskey'=>sesskey(),
                                                                'page'=>$this->currpage));
            $description = get_string('reverttodraftshort', 'assign');
            $actions[$url->out(false)] = $description;
        }

        $edit .= $this->output->container_start(array('yui3-menu', 'actionmenu'), 'actionselect' . $row->id);
        $edit .= $this->output->container_start(array('yui3-menu-content'));
        $edit .= html_writer::start_tag('ul');
        $edit .= html_writer::start_tag('li', array('class'=>'menuicon'));

        $menuicon = $this->output->pix_icon('i/menu', get_string('actions'));
        $edit .= $this->output->action_link('#menu' . $row->id, $menuicon, null, array('class'=>'yui3-menu-label'));
        $edit .= $this->output->container_start(array('yui3-menu', 'yui3-loading'), 'menu' . $row->id);
        $edit .= $this->output->container_start(array('yui3-menu-content'));
        $edit .= html_writer::start_tag('ul');

        foreach ($actions as $url => $description) {
            $edit .= html_writer::start_tag('li', array('class'=>'yui3-menuitem'));

            $edit .= $this->output->action_link($url, $description, null, array('class'=>'yui3-menuitem-content'));

            $edit .= html_writer::end_tag('li');
        }
        $edit .= html_writer::end_tag('ul');
        $edit .= $this->output->container_end();
        $edit .= $this->output->container_end();
        $edit .= html_writer::end_tag('li');
        $edit .= html_writer::end_tag('ul');

        $edit .= $this->output->container_end();
        $edit .= $this->output->container_end();

        return $edit;
    }

    /**
     * Write the plugin summary with an optional link to view the full feedback/submission.
     *
     * @param assign_plugin $plugin Submission plugin or feedback plugin
     * @param stdClass $item Submission or grade
     * @param string $returnaction The return action to pass to the view_submission page (the current page)
     * @param string $returnparams The return params to pass to the view_submission page (the current page)
     * @return string The summary with an optional link
     */
    private function format_plugin_summary_with_link(assign_plugin $plugin, stdClass $item, $returnaction, $returnparams) {
        $link = '';
        $showviewlink = false;

        $summary = $plugin->view_summary($item, $showviewlink);
        $separator = '';
        if ($showviewlink) {
            $icon = $this->output->pix_icon('t/preview', get_string('view' . substr($plugin->get_subtype(), strlen('assign')), 'mod_assign'));
            $link = $this->output->action_link(
                                new moodle_url('/mod/assign/view.php',
                                               array('id' => $this->assignment->get_course_module()->id,
                                                     'sid'=>$item->id,
                                                     'gid'=>$item->id,
                                                     'plugin'=>$plugin->get_type(),
                                                     'action'=>'viewplugin' . $plugin->get_subtype(),
                                                     'returnaction'=>$returnaction,
                                                     'returnparams'=>http_build_query($returnparams))),
                                $icon);
            $separator = $this->output->spacer(array(), true);
        }

        return $link . $separator . $summary;
    }


    /**
     * Format the submission and feedback columns
     *
     * @param string $colname The column name
     * @param stdClass $row The submission row
     * @return mixed string or NULL
     */
    function other_cols($colname, $row){
        if (($pos = strpos($colname, 'assignsubmission_')) !== false) {
            $plugin = $this->assignment->get_submission_plugin_by_type(substr($colname, strlen('assignsubmission_')));

            if ($plugin->is_visible() && $plugin->is_enabled()) {
                if ($row->submissionid) {
                    $submission = new stdClass();
                    $submission->id = $row->submissionid;
                    $submission->timecreated = $row->firstsubmission;
                    $submission->timemodified = $row->timesubmitted;
                    $submission->assignment = $this->assignment->get_instance()->id;
                    $submission->userid = $row->userid;
                    return $this->format_plugin_summary_with_link($plugin, $submission, 'grading', array());
                }
            }
            return '';
        }
        if (($pos = strpos($colname, 'feedback_')) !== false) {
            $plugin = $this->assignment->get_feedback_plugin_by_type(substr($colname, strlen('assignfeedback_')));
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                $grade = null;
                if ($row->gradeid) {
                    $grade = new stdClass();
                    $grade->id = $row->gradeid;
                    $grade->timecreated = $row->firstmarked;
                    $grade->timemodified = $row->timemarked;
                    $grade->assignment = $this->assignment->get_instance()->id;
                    $grade->userid = $row->userid;
                    $grade->grade = $row->grade;
                    $grade->mailed = $row->mailed;
                }
                if ($this->quickgrading && $plugin->supports_quickgrading()) {
                    return $plugin->get_quickgrading_html($row->userid, $grade);
                } else if ($grade) {
                    return $this->format_plugin_summary_with_link($plugin, $grade, 'grading', array());
                }
            }
            return '';
        }
        return $row->$colname;
    }

    /**
     * Using the current filtering and sorting - load all rows and return a single column from them
     *
     * @param string $columnname The name of the raw column data
     * @return array of data
     */
    function get_column_data($columnname) {
        $this->setup();
        $this->currpage = 0;
        $this->query_db($this->tablemaxrows);
        $result = array();
        foreach ($this->rawdata as $row) {
            $result[] = $row->$columnname;
        }
        return $result;
    }
    /**
     * Using the current filtering and sorting - load a single row and return a single column from it
     *
     * @param int $rownumber The rownumber to load
     * @param string $columnname The name of the raw column data
     * @param bool $lastrow Set to true if this is the last row in the table
     * @return mixed string or false
     */
    function get_cell_data($rownumber, $columnname, $lastrow) {
        $this->setup();
        $this->currpage = $rownumber;
        $this->query_db(1);
        if ($rownumber == $this->totalrows-1) {
            $lastrow = true;
        }
        foreach ($this->rawdata as $row) {
            return $row->$columnname;
        }
        return false;
    }

    /**
     * Return things to the renderer
     *
     * @return string the assignment name
     */
    function get_assignment_name() {
        return $this->assignment->get_instance()->name;
    }

    /**
     * Return things to the renderer
     *
     * @return int the course module id
     */
    function get_course_module_id() {
        return $this->assignment->get_course_module()->id;
    }

    /**
     * Return things to the renderer
     *
     * @return int the course id
     */
    function get_course_id() {
        return $this->assignment->get_course()->id;
    }

    /**
     * Return things to the renderer
     *
     * @return stdClass The course context
     */
    function get_course_context() {
        return $this->assignment->get_course_context();
    }

    /**
     * Return things to the renderer
     *
     * @return bool Does this assignment accept submissions
     */
    function submissions_enabled() {
        return $this->assignment->is_any_submission_plugin_enabled();
    }

    /**
     * Return things to the renderer
     *
     * @return bool Can this user view all grades (the gradebook)
     */
    function can_view_all_grades() {
        return has_capability('gradereport/grader:view', $this->assignment->get_course_context()) && has_capability('moodle/grade:viewall', $this->assignment->get_course_context());
    }

    /**
     * Override the table show_hide_link to not show for select column
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        if ($index > 0) {
            return parent::show_hide_link($column, $index);
        }
        return '';
    }
}
