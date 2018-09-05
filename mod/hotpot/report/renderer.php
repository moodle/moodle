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
 * Render an attempt at a HotPot quiz
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent renderer class
require_once($CFG->dirroot.'/mod/hotpot/renderer.php');

// get hotpot_report_table class
require_once($CFG->dirroot.'/mod/hotpot/report/tablelib.php');

// get hotpot_user_filtering class
require_once($CFG->dirroot.'/mod/hotpot/report/userfiltering.php');

/**
 * mod_hotpot_report_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_report_renderer extends mod_hotpot_renderer {

    protected $tablecolumns = array();

    protected $filterfields = array();

    protected $userfilter = array();

    protected $attemptfilter = array();

    public $mode = '';

    public $questions = array();

    public $has_questioncolumns = false;

    /**
     * init
     *
     * @param xxx $hotpot
     */
    protected function init($hotpot)   {
        global $DB;

        // save a reference to the $hotpot record
        $this->hotpot = $hotpot;

        // remove grade filter and column, if not required
        if (! $hotpot->gradeweighting) {
            if (array_key_exists('grade', $this->filterfields)) {
                unset($this->filterfields['grade']);
            }
            $i = array_search('grade', $this->tablecolumns);
            if (is_numeric($i)) {
                array_splice($this->tablecolumns, $i, 1);
            }
        }

        // add question numbers to $tablecolumns
        if ($this->has_questioncolumns) {
            if ($records = $DB->get_records('hotpot_questions', array('hotpotid' => $this->hotpot->id), '', 'id,name,text')) {
                $this->questions = array_values($records);
            }
        }
    }

    /**
     * render_report
     *
     * @param xxx $hotpot
     */
    public function render_report($hotpot)  {
        $this->init($hotpot);
        echo $this->header();
        echo $this->reportcontent();
        echo $this->footer();
    }

    /**
     * reportcontent
     */
    public function reportcontent()  {
        global $DB, $USER;

        // check capabilities
        if ($this->hotpot->can_reviewallattempts()) {
            $userid = 0; // all users
        } else if ($this->hotpot->can_reviewmyattempts()) {
            // current user can only review their own attempts
            $userid = $USER->id;
        } else {
            // has_capability('mod/hotpot:review', $this->hotpot->context))
            // should already have been checked in "mod/hotpot/report.php"
            return false;
        }

        // set baseurl for this page (used for filters and table)
        $baseurl = $this->hotpot->report_url($this->mode)->out();

        // display user and attempt filters
        $this->display_filters($baseurl);

        // create report table
        $uniqueid = $this->page->pagetype.'-'.$this->mode;
        $table = new hotpot_report_table($uniqueid, $this);

        // set the table columns
        $tablecolumns = $this->tablecolumns;
        if (! $this->hotpot->can_deleteattempts()) {
            // remove the select column from students view
            $i = array_search('selected', $tablecolumns);
            if (is_numeric($i)) {
                array_splice($tablecolumns, $i, 1);
            }
        }
        if ($this->has_questioncolumns) {
            $i = array_search('penalties', $tablecolumns);
            if ($i===false) {
                $i = array_search('score', $tablecolumns);
            }
            if ($i===false) {
                $i = count($tablecolumns);
            }
            array_splice($tablecolumns, $i, 0, $this->get_question_columns());
        }

        // setup the report table
        $table->setup_report_table($tablecolumns, $baseurl);

        // setup sql to COUNT records
        list($select, $from, $where, $params) = $this->count_sql($userid);
        $table->set_count_sql("SELECT $select FROM $from WHERE $where", $params);

        // setup sql to SELECT records
        list($select, $from, $where, $params) = $this->select_sql($userid);
        $table->set_sql($select, $from, $where, $params);

        // extract attempt records
        $table->query_db($table->get_page_size());

        // extract question responses, if required
        if ($this->has_questioncolumns) {
            $this->add_responses_to_rawdata($table);
        }
        $this->adjust_grades_in_rawdata($table);

        // display the table
        $table->build_table();
        $table->finish_html();

        // display the legend
        $table->print_legend();
    }

    /**
     * display_filters
     */
    function display_filters($baseurl) {
        if (count($this->filterfields) && $this->hotpot->can_reviewattempts()) {

            $user_filtering = new hotpot_user_filtering($this->filterfields, $baseurl);

            $this->userfilter = $user_filtering->get_sql_filter();
            $this->attemptfilter = $user_filtering->get_sql_filter_attempts();

            $user_filtering->display_add();
            $user_filtering->display_active();
        }
    }

    /**
     * add_filter_params
     *
     * @param string $userfields (can be "")
     * @param integer $userid    (can be 0)
     * @param integer $attemptid (can be 0)
     * @param string $select
     * @param string $from
     * @param string $where
     * @param array $params
     * @return void, but may modify $select $from $where $params
     */
    function add_filter_params($userfields, $userid, $attemptid, $select, $from, $where, $params) {

        // search string to detect db fieldname in a filter string
        // - not preceded by {:`"'_. a-z 0-9
        // - starts with lowercase a-z
        // - followed by lowercase a-z, 0-9 or underscore
        // - not followed by }:`"'_. a-z 0-9
        $before = '[{:`"'."'".'a-zA-Z0-9_.]';
        $after  = '[}:`"'."'".'a-zA-Z0-9_.]';
        $search = "/(?<!$before)([a-z][a-z0-9_]*)(?!$after)/";

        $require_usertable = false;
        $require_attempttable = false;

        // add user fields. if required
        if (in_array('fullname', $this->tablecolumns)) {
            $select .= $userfields;
            $where  .= ' AND ha.userid=u.id';
            $require_usertable = true;
        }

        if ($userid) {
            // a specific user
            $where .= ' AND ha.userid=:userid';
            $params['userid'] = $userid;
            $require_usertable = true;
        } else {
            // user filters
            list($filterwhere, $filterparams) = $this->userfilter;
            if ($filterwhere) {
                $filterwhere = preg_replace($search, 'u.$1', $filterwhere);
                $where  .= ' AND '.$filterwhere;
                $params += $filterparams;
                $require_usertable = true;
            }
        }

        if ($attemptid) {
            // a specific attempt
            $where .= ' AND ha.id=:attemptid';
            $params['attemptid'] = $attemptid;
            $require_attempttable = true;
        } else {
            // attempt filters
            list($filterwhere, $filterparams) = $this->attemptfilter;
            if ($filterwhere) {
                $filterwhere = preg_replace($search, 'ha.$1', $filterwhere);
                $where  .= ' AND '.$filterwhere;
                $params += $filterparams;
                $require_attempttable = true;
            }
        }

        // add user table if needed
        if ($require_usertable && strpos($from, '{user}')===false) {
            $from .= ' INNER JOIN {user} u ON ha.userid = u.id';
        }

        // add attempt table if needed
        if ($require_attempttable && strpos($from, '{hotpot_attempts}')===false) {
            $from = '{hotpot_attempts} ha '.$from;
        }

        // join to grade tables if necessary
        if (strpos($select, 'gg')===false && strpos($where, 'gg')===false) {
            // grade tables not required
        } else if (strpos($from, 'grade_grades')===false) {
            // grade tables are required, but missing, so add them to $from
            // the "gg" table alias is added by the "set_sql()" method of this class
            // or the "get_sql_filter_attempts()" method of the hotpot "grade" filter
            // Note that we expect that there will always be a "grade_items" record
            // but there may not necessarily be a "grade_grades" record
            $from   .= ' LEFT JOIN {grade_items} gi  ON gi.courseid = :courseid'.
                                                  ' AND gi.itemtype = :itemtype'.
                                                  ' AND gi.itemmodule = :itemmodule'.
                                                  ' AND gi.iteminstance = :iteminstance'.
                       ' LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id'.
                                                 '  AND gg.userid = ha.userid';
            $params += array('courseid' => $this->hotpot->course->id,
                             'itemtype' => 'mod',
                             'itemmodule' => 'hotpot',
                             'iteminstance' => $this->hotpot->id);
        }

        return array($select, $from, $where, $params);
    }

    /**
     * count_sql
     *
     * @param xxx $userid (optional, default=0)
     * @param xxx $attemptid (optional, default=0)
     * @return xxx
     */
    function count_sql($userid=0, $attemptid=0) {
        $params = array();

        $select = 'COUNT(1)';
        $from   = '{hotpot_attempts} ha';
        $where  = 'hotpotid=:hotpotid';
        $params['hotpotid'] = $this->hotpot->id;

        // there is no explicit specification of the user fields because they cause
        // an error on MS-SQL: Column 'mdl_user.id' is invalid in the select list
        // because it is not contained in either an aggregate function or the GROUP BY clause.
        // $userfields = ', '.$this->get_userfields('u', null, 'userid');
        $userfields = '';

        return $this->add_filter_params($userfields, $userid, $attemptid, $select, $from, $where, $params);
    }

    /**
     * select_sql
     *
     * @param xxx $userid (optional, default=0)
     * @param xxx $attemptid (optional, default=0)
     * @return xxx
     */
    function select_sql($userid=0, $attemptid=0) {

        // the standard way to get Moodle grades is thus:
        // $grades = grade_get_grades($this->hotpot->course->id, 'mod', 'hotpot', $this->hotpot->id, $userid);
        // $grade = $grades->items[0]->grades[$USER->id]->grade;

        // get question fields, if any
        $select_questions = '';
        foreach ($this->get_question_columns() as $column) {
            $select_questions .= "1 AS $column, ";
        }

        // sql to select all attempts at this HotPot (and Moodle grade)
        $round_grade = '(CASE WHEN gg.rawgrade IS NULL THEN 0 ELSE ROUND(gg.rawgrade, 0) END)';
        $select = 'ha.*, (ha.timemodified - ha.timestart) AS duration,'.$select_questions." $round_grade AS grade";
        $from   = '{hotpot_attempts} ha';
        $where  = 'ha.hotpotid=:hotpotid';
        $params = array('hotpotid' => $this->hotpot->id);

        // Note: we don't need "u.id AS userid" because we already have ha.userid
        // and it causes Debug info: ORA-00918: column ambiguously defined (CONTRIB-4910)
        $userfields = ','.$this->get_userfields('u', null, 'userid');
        $userfields = str_replace('u.id AS userid,', '', $userfields);
        //$userfields = ',u.firstname,u.lastname,u.picture,u.imagealt,u.email';
        return $this->add_filter_params($userfields, $userid, $attemptid, $select, $from, $where, $params);
    }

    /**
     * add_responses_to_rawdata
     *
     * @param xxx $table (passed by reference)
     * @return xxx
     */
    function add_responses_to_rawdata(&$table)   {
        global $DB;

        if (empty($table->rawdata) || empty($this->questions)) {
            return false;
        }

        // get question column names (indexed by questionid)
        $question_columns = $this->get_question_columns(true);

        // empty out the response fields in the the rawdata
        $attemptids = array_keys($table->rawdata);
        foreach ($attemptids as $attemptid) {
            foreach ($question_columns as $questionid => $column) {
                $table->rawdata[$attemptid]->$column = '';
            }
        }

        // set sql to select responses for these attempts
        list($a_filter, $a_params) = $DB->get_in_or_equal($attemptids);
        list($q_filter, $q_params) = $DB->get_in_or_equal(array_keys($question_columns));

        $select = '*';
        $from   = '{hotpot_responses}';
        $where  = "attemptid $a_filter AND questionid $q_filter";
        $params = array_merge($a_params, $q_params);

        // get the responses for these attempts
        if (! $responses = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {
            return false;
        }

        foreach ($responses as $response) {
            $attemptid = $response->attemptid;
            if (empty($table->rawdata[$attemptid])) {
                return false; // shouldn't happen
            }

            $questionid = $response->questionid;
            if (empty($question_columns[$questionid])) {
                return false; // shouldn't happen
            }

            $column = $question_columns[$questionid];
            $this->add_response_to_rawdata($table, $attemptid, $column, $response);
        }
    }

    /**
     * add_response_to_rawdata
     *
     * @param xxx $table (passed by reference)
     * @param xxx $attemptid
     * @param xxx $column
     * @param xxx $response
     */
    function add_response_to_rawdata(&$table, $attemptid, $column, $response)  {
        // $table->rawdata[$attemptid]->$column = $response->somefield;
    }

    /**
     * get_question_columns
     *
     * @param xxx $index_by_id (optional, default=false)
     * @return xxx
     */
    function get_question_columns($index_by_id=false)  {
        $question_columns = array();
        foreach ($this->questions as $i => $question) {
            if ($index_by_id) {
                $id = $question->id;
            } else {
                $id = $i;
            }
            $question_columns[$id] = "q_$i";
        }
        return $question_columns;
    }

    /**
     * adjust_grades_in_rawdata
     *
     * this function adjusts the grade values
     *
     * @param xxx $table (passed by reference)
     * @return xxx
     */
    function adjust_grades_in_rawdata(&$table)   {
        if (empty($table->rawdata)) {
            return false; // no records
        }
        if (! $table->has_column('grade')) {
            return false; // no grade column
        }
        if (empty($table->column_suppress) || empty($table->column_suppress['grade'])) {
            return false; // grade column is not suppressed (i.e. it is always printed)
        }

        $userid = 0;
        $grade  = 0;
        $prefix = '';

        foreach ($table->rawdata as $id => $record) {
            if ($userid) {
                if ($userid==$record->userid) {
                    // same user - do nothing
                } else {
                    if ($grade==$record->grade) {
                        // oops, same grade as previous user - we must adjust the grade value,
                        // so that "print_row()" (lib/tablelib.php) does not suppress this grade
                        if ($prefix) {
                            $prefix = '';
                        } else {
                            // add an empty span tag to make grade different from previous user's
                            $prefix = html_writer::tag('span', '');
                        }
                    } else {
                        // different grade from previous user, so we can unset the prefix
                        $prefix = '';
                    }
                }
                if ($prefix) {
                    $table->rawdata[$id]->grade = $prefix.$table->rawdata[$id]->grade;
                }
            }
            $userid = $record->userid;
            $grade  = $record->grade;
        }
    }

    /**
     * get_userfields
     *
     * @param string $tableprefix name of database table prefix in query
     * @param array  $extrafields extra fields to be included in result (do not include TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     * @param string $idalias     alias of id field
     * @param string $fieldprefix prefix to add to all columns in their aliases, does not apply to 'id'
     * @return string
     */
     function get_userfields($tableprefix = '', array $extrafields = null, $idalias = 'id', $fieldprefix = '') {
        if (class_exists('user_picture')) { // Moodle >= 2.6
            return user_picture::fields($tableprefix, $extrafields, $idalias, $fieldprefix);
        }
        // Moodle <= 2.5
        $fields = array('id', 'firstname', 'lastname', 'picture', 'imagealt', 'email');
        if ($tableprefix || $extrafields || $idalias) {
            if ($tableprefix) {
                $tableprefix .= '.';
            }
            if ($extrafields) {
                $fields = array_unique(array_merge($fields, $extrafields));
            }
            if ($idalias) {
                $idalias = " AS $idalias";
            }
            if ($fieldprefix) {
                $fieldprefix = " AS $fieldprefix";
            }
            foreach ($fields as $i => $field) {
                $fields[$i] = "$tableprefix$field".($field=='id' ? $idalias : ($fieldprefix=='' ? '' : "$fieldprefix$field"));
            }
        }
        return implode(',', $fields);
        //return 'u.id AS userid, u.username, u.firstname, u.lastname, u.picture, u.imagealt, u.email';
    }
}
