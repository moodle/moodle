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
 * Create a table to display attempts at a HotPot quiz
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent classes (table_sql and flexible_table)
require_once($CFG->dirroot.'/lib//tablelib.php');

/**
 * hotpot_report_table
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_report_table extends table_sql {

    /** @var string field in the attempt records that refers to the user id */
    public $useridfield = 'userid';

    /** @var mod_hotpot_report_renderer for the current page */
    protected $output;

    /** @var string time format used for the "timemodified" column */
    protected $timeformat = 'strftimerecentfull';

    /** @var string localized format used for the "timemodified" column */
    protected $strtimeformat;

    /** @var array list of distinct values stored in response columns */
    protected $legend = array();

    /**
     * Constructor
     *
     * @param int $uniqueid
     */
    function __construct($uniqueid, $output) {
        parent::__construct($uniqueid);
        $this->output = $output;
        $this->strtimeformat = get_string($this->timeformat);
    }

    /**
     * setup_report_table
     *
     * @param xxx $tablecolumns
     * @param xxx $baseurl
     * @param xxx $usercount (optional, default value = 10)
     */
    function setup_report_table($tablecolumns, $baseurl, $usercount=10)  {

        // generate headers (using "header_xxx()" methods below)
        $tableheaders = array();
        foreach ($tablecolumns as $tablecolumn) {
            $tableheaders[] = $this->format_header($tablecolumn);
        }

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);
        $this->define_baseurl($baseurl);

        if ($this->has_column('fullname')) {
            $this->pageable(true);
            $this->sortable(true);
            $this->initialbars($usercount > 20);

            // this information is only printed once per user
            $this->column_suppress('fullname');
            $this->column_suppress('picture');
            $this->column_suppress('grade');

            // special css class for "picture" column
            $this->column_class('picture', 'picture');
        } else {
            $this->pageable(false);
            $this->sortable(false);
            // you can set specific columns to be unsortable:
            // $this->no_sorting('columnname');
        }

        // basically all columns are centered
        $this->column_style_all('text-align', 'center');

        // some columns are not centered
        if ($this->has_column('fullname')) {
            $this->column_style('fullname', 'text-align', '');
        }
        if ($this->has_column('responsefield')) {
            $this->column_style('responsefield', 'text-align', 'right');
        }

        // attributes in the table tag
        $this->set_attribute('id', 'attempts');
        $this->set_attribute('align', 'center');
        $this->set_attribute('class', $this->output->mode);

        parent::setup();
    }

    /**
     * wrap_html_start
     */
    function wrap_html_start() {

        // check this table has a "selected" column
        if (! $this->has_column('selected')) {
            return false;
        }

        // check user can delete attempts
        if (! $this->output->hotpot->can_deleteattempts()) {
            return false;
        }

        // start form
        $url = $this->output->hotpot->report_url($this->output->mode);
        $params = array('id'=>'attemptsform', 'method'=>'post', 'action'=>$url->out_omit_querystring());
        echo html_writer::start_tag('form', $params);

        // create hidden fields
        $params = array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey());
        $hidden_fields = html_writer::input_hidden_params($url).
                         html_writer::empty_tag('input', $params)."\n";

        // put hidden fields in a containiner (for strict XHTML compatability)
        $params = array('style'=>'display: none;');
        echo html_writer::tag('div', $hidden_fields, $params);
    }

    /**
     * wrap_html_finish
     */
    function wrap_html_finish() {
        global $PAGE;

        // check this table has a "selected" column
        if (! $this->has_column('selected')) {
            return false;
        }

        // check user can delete attempts
        if (! $this->output->hotpot->can_deleteattempts()) {
            return false;
        }

        // start "commands" div
        $params = array('id' => 'commands');
        echo html_writer::start_tag('div', $params);

        // add "select all/none" links
        if ( method_exists($PAGE->requires, 'js_amd_inline')) {
            // Moodle >= 3.3
            echo html_writer::tag('a', get_string('selectall', 'quiz'), array('href' => '#', 'id' => 'selectall'));
            echo ' / ';
            echo html_writer::tag('a', get_string('selectnone', 'quiz'), array('href' => '#', 'id' => 'selectnone'));
            $PAGE->requires->js_amd_inline("
            require(['jquery'], function($) {
                $('#selectall').click(function(e) {
                    $('#attempts').find('input:checkbox').prop('checked', true);
                    e.preventDefault();
                });
                $('#selectnone').click(function(e) {
                    $('#attempts').find('input:checkbox').prop('checked', false);
                    e.preventDefault();
                });
            });");
        } else {
            // Moodle <= 3.2
            $href = "javascript:select_all_in('TABLE',null,'attempts');";
            echo html_writer::tag('a', get_string('selectall', 'quiz'), array('href' => $href));
            echo ' / ';
            $href = "javascript:deselect_all_in('TABLE',null,'attempts');";
            echo html_writer::tag('a', get_string('selectnone', 'quiz'), array('href' => $href));
        }

        echo ' &nbsp; ';

        // add button to delete attempts
        $confirm = addslashes_js(get_string('confirmdeleteattempts', 'mod_hotpot'));
        $onclick = ''
            ."if(confirm('$confirm') && this.form && this.form.elements['confirmed']) {"
                ."this.form.elements['confirmed'].value = '1';"
                ."return true;"
            ."} else {"
                ."return false;"
            ."}"
        ;
        echo html_writer::empty_tag('input', array('type'=>'submit', 'onclick'=>"$onclick", 'name'=>'delete', 'value'=>get_string('deleteattempts', 'mod_hotpot')));
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'confirmed', 'value'=>'0'))."\n";
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'action', 'value'=>'deleteselected'))."\n";

        // finish "commands" div
        echo html_writer::end_tag('div');

        // finish the "attemptsform" form
        echo html_writer::end_tag('form');
    }

    ////////////////////////////////////////////////////////////////////////////////
    // functions to format header cells                                           //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * format_header
     *
     * @param xxx $tablecolumn
     * @return xxx
     */
    function format_header($tablecolumn)  {
        $method = 'header_'.$tablecolumn;
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $this->header_other($tablecolumn);
        }
    }

    /**
     * header_picture
     *
     * @return xxx
     */
    function header_picture()  {
        return '';
    }

    /**
     * header_fullname
     *
     * @return xxx
     */
    function header_fullname()  {
        return get_string('name');
    }

    /**
     * header_grade
     *
     * @return xxx
     */
    function header_grade()  {
        $grademethod = $this->output->hotpot->format_grademethod();

        $gradeweighting = $this->output->hotpot->gradeweighting;
        if ($gradeweighting != 100) {
            $grademethod = $gradeweighting." x $grademethod/100";
        }

        $params = array('class' => 'grademethod');
        $grademethod = html_writer::tag('span', $grademethod, $params);

        $br = html_writer::empty_tag('br');
        return get_string('grade').$br.'('.$grademethod.')';
    }

    /**
     * header_selected
     *
     * @return xxx
     */
    function header_selected()  {
        return '';
    }

    /**
     * header_attempt
     *
     * @return xxx
     */
    function header_attempt()  {
        return get_string('attemptnumber', 'mod_hotpot');
    }

    /**
     * header_timemodified
     *
     * @return xxx
     */
    function header_timemodified()  {
        return get_string('time', 'quiz');
    }

    /**
     * header_status
     *
     * @return xxx
     */
    function header_status()  {
        return get_string('status', 'mod_hotpot');
    }

    /**
     * header_duration
     *
     * @return xxx
     */
    function header_duration()  {
        return get_string('duration', 'mod_hotpot');
    }

    /**
     * header_penalties
     *
     * @return xxx
     */
    function header_penalties()  {
        return get_string('penalties', 'mod_hotpot');
    }

    /**
     * header_score
     *
     * @return xxx
     */
    function header_score()  {
        return get_string('score', 'quiz');
    }

    /**
     * header_responsefield
     *
     * @return xxx
     */
    function header_responsefield()  {
        return '';
    }

    /**
     * header_other
     *
     * @return xxx
     */
    function header_other($column)  {
        if (substr($column, 0, 2)=='q_') {
            $a = intval(substr($column, 2)) + 1;
            return get_string('questionshort', 'mod_hotpot', $a);
        } else {
            return $column;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // functions to format data cells                                             //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * col_selected
     *
     * @param xxx $row
     * @return xxx
     */
    function col_selected($row)  {
        return html_writer::checkbox('selected['.$row->id.']', 1, false);
    }

    /**
     * col_picture
     *
     * @param xxx $row
     * @return xxx
     */
    function col_picture($row)  {
        $user = new stdClass();
        $fields = explode(',', $this->output->get_userfields());
        foreach ($fields as $field) {
            if ($field=='id') {
                $user->$field = $row->userid;
            } else {
                $user->$field = $row->$field;
            }
        }
        return $this->output->user_picture($user, array('courseid'=>$this->output->hotpot->course->id));
    }

    /**
     * col_grade
     *
     * @param xxx $row
     * @return xxx
     */
    function col_grade($row)  {
        if (isset($row->grade)) {
            return $row->grade.'%';
        } else {
            return '&nbsp;';
        }
    }

    /**
     * col_attempt
     *
     * @param xxx $row
     * @return xxx
     */
    function col_attempt($row)  {
        $text = "$row->attempt";
        return $this->format_review_link($text, $row);
    }

    /**
     * col_timemodified
     *
     * @param xxx $row
     * @return xxx
     */
    function col_timemodified($row)  {
        $text = trim(userdate($row->timemodified, $this->strtimeformat));
        return $this->format_review_link($text, $row);
    }

    /**
     * col_status
     *
     * @param xxx $row
     * @return xxx
     */
    function col_status($row)  {
        $text = hotpot::format_status($row->status);
        return $this->format_review_link($text, $row);
    }

    /**
     * col_duration
     *
     * @param xxx $row
     * @return xxx
     */
    function col_duration($row)  {
        if ($row->duration) {
            $text = format_time($row->duration);
        } else {
            $text = ''; // format_text(0) returns "now"
        }
        return $this->format_review_link($text, $row);
    }

    /**
     * col_penalties
     *
     * @param xxx $row
     * @return xxx
     */
    function col_penalties($row)  {
        $text = "$row->penalties";
        return $this->format_review_link($text, $row);
    }

    /**
     * col_score
     *
     * @param xxx $row
     * @return xxx
     */
    function col_score($row)  {
        $text = "$row->score";
        return $this->format_review_link($text, $row);
    }

    /**
     * other_cols
     *
     * @param xxx $column
     * @param xxx $row
     * @return xxx
     */
    function other_cols($column, $row) {

        if (! property_exists($row, $column)) {
            return $column;
        }

        if ($column=='responsefield') {
            return get_string($row->$column, 'mod_hotpot');
        }

        // format columns Q-1 .. Q-99
        return $this->format_text($row->$column);
    }

    /**
     * format_review_link
     *
     * @param xxx $text
     * @param xxx $row from hotpot_attempts table
     * @return xxx
     */
    function format_review_link($text, $row)  {
        if (strlen($text) && $this->output->hotpot->can_reviewattempt($row)) {
            $url = $this->output->hotpot->review_url($row);
            $text = html_writer::link($url, $text);
        }
        return $text;
    }

    /**
     * override parent class method, because we may want to specify a default sort
     *
     * @return xxx
     */
    function get_sql_sort()  {

        // if user has specified a sort column, use that
        if ($sort = parent::get_sql_sort()) {
            return $sort;
        }

        // if there is a "fullname" column, sort by first/last name
        if ($this->has_column('fullname')) {
            $sort = 'u.firstname, u.lastname';
            if ($this->has_column('attempt')) {
                $sort .= ', ha.attempt ASC';
            }
            return $sort;
        }

        // no sort column, and no "fullname" column
        return '';
    }

    /**
     * has_column
     *
     * @param xxx $column
     * @return xxx
     */
    public function has_column($column)  {
        return array_key_exists($column, $this->columns);
    }

    /**
     * delete_rows
     *
     * @param xxx $delete_rows
     */
    function delete_rows($delete_rows)  {
        foreach ($delete_rows as $id => $delete_flag) {
            if ($delete_flag) {
                unset($this->rawdata[$id]);
            }
        }
    }

    /**
     * delete_columns
     *
     * @param xxx $delete_columns
     */
    function delete_columns($delete_columns)  {
        $newcolnum = 0;
        foreach($this->columns as $column => $oldcolnum) {
            if (empty($delete_columns[$column])) {
                $this->columns[$column] = $newcolnum++;
            } else {
                unset($this->columns[$column]);
                unset($this->headers[$oldcolnum]);
                foreach (array_keys($this->rawdata) as $id) {
                    unset($this->rawdata[$id]->$column);
                }
            }
        }
        // reset indexes on headers
        $this->headers = array_values($this->headers);
    }

    /**
     * set_legend
     *
     * @param xxx $column
     * @param xxx $value
     * @return xxx
     */
    function set_legend($column, $value) {
        if (empty($column) || empty($value)) {
            return '';
        }

        // if necessary, append this $column to the legend
        if (empty($this->legend[$column])) {
            $this->legend[$column] = array();
        }

        // get the $i(ndex) of this $value in this $column
        $i = array_search($value, $this->legend[$column]);
        if ($i===false) {
            $i = count($this->legend[$column]);
            $this->legend[$column][$i] = $value;
        }

        // return the $value's index (as A, B, C)
        return $this->format_legend_index($i);
    }

    /**
     * print_legend
     */
    function print_legend()  {
        if (empty($this->legend)) {
            return false;
        }

        $stringids = array();
        foreach ($this->legend as $column => $responses) {
            foreach ($responses as $i => $stringid) {
                $stringids[$stringid] = true;
            }
        }
        $strings = hotpot::get_strings(array_keys($stringids));
        unset($stringids, $column, $responses, $i, $stringid);

        foreach ($this->legend as $column => $responses) {
            echo html_writer::start_tag('table');
            echo html_writer::start_tag('tbody');
            foreach ($responses as $i => $response) {
                if (isset($strings[$response])) {
                    $response_string = $strings[$response]->string;
                } else {
                    $response_string = 'Unrecognized string id: '.$response;
                }
                echo html_writer::tag('tr',
                    html_writer::tag('td', $this->format_header($column)).
                    html_writer::tag('td', $this->format_legend_index($i)).
                    html_writer::tag('td', $response_string)
                );
                $column = '&nbsp;';
            }
            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
        }
    }

    /**
     * format_legend_index
     *
     * @param xxx $i
     * @return xxx
     */
    function format_legend_index($i)  {
        // convert numeric index to A, B, ... Z, AA, AB, ...
        if ($i < 26) {
            return chr(ord('A') + $i);
        } else {
            return $this->format_legend_index(intval($i/26)-1).$this->format_legend_index($i % 26);
        }
    }
}
