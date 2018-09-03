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
 * Render the analysis report for a given HotPot activity
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/report/renderer.php');

/**
 * mod_hotpot_report_analysis_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_report_analysis_renderer extends mod_hotpot_report_renderer {
    public $mode = 'analysis';

    public $tablecolumns = array('responsefield');

    public $filterfields = array('status'=>0);

    public $has_questioncolumns = true;

    /**
     * add_responses_to_rawdata
     *
     * @param xxx $table (passed by reference)
     * @return xxx
     */
    function add_responses_to_rawdata(&$table) {
        // attach each response to its parent attempt
        // using the "add_response_to_rawdata()" method
        parent::add_responses_to_rawdata($table);

        // the fields we are interested in, in the order we want them
        $fields = array('correct', 'wrong', 'ignored', 'hints', 'clues', 'checks'); // , 'weighting'
        $string_fields = array('correct', 'wrong', 'ignored');

        // statistics about the $q(uestions)
        $q = array();

        // statistics about the $f(ields)
        $f = array();

        $question_columns = $this->get_question_columns(true);

        // get hi and lo scores
        list($hi_score, $lo_score) = $this->get_hi_lo_scores($table->rawdata);

        // compile statistics about questions in attempts (=rows)
        foreach ($table->rawdata as $row) {

            // is this a high score? or a low score?
            $is_hi_score = ($row->score >= $hi_score);
            $is_lo_score = ($row->score <  $lo_score);

            foreach ($question_columns as $id => $column) {

                if (empty($row->$column)) {
                    continue;
                }

                if (! isset($q[$id])) {
                    $q[$id] = array('count' => array('hi'=>0, 'lo'=>0, 'correct'=>0, 'total'=>0, 'sum'=>0));
                }

                foreach ($row->$column as $response) {

                    // increment counts of sums and totals
                    $q[$id]['count']['sum'] += $response->score;
                    $q[$id]['count']['total']++;

                    if ($response->score==100) {
                        $q[$id]['count']['correct']++;
                        if ($is_hi_score) {
                            $q[$id]['count']['hi']++;
                        } else if ($is_lo_score) {
                            $q[$id]['count']['lo']++;
                        }
                    }

                    // add field statistics
                    foreach($fields as $field) {

                        if (! isset($f[$field])) {
                            $f[$field] = array('count' => 0);
                        }

                        if (! isset($q[$id][$field])) {
                            $q[$id][$field] = array('count' => 0);
                        }

                        $values = explode(',', $response->$field);
                        $values = array_filter($values);
                        $values = array_unique($values);

                        foreach($values as $value) {
                            // $value should be an integer (hotpot_strings.id or count)
                            if (is_numeric($value)) {
                                $f[$field]['count']++;
                                if (! isset($q[$id][$field][$value])) {
                                    $q[$id][$field][$value] = 0;
                                }
                                $q[$id][$field]['count']++;
                                $q[$id][$field][$value]++;
                            }
                        } // foreach($values as $value)
                    } // end foreach($fields as $field)
                } // end foreach ($row->$column as $response)
            } // end foreach ($question_columns as $id => $column)
        } // end foreach ($table->rawdata as $row)

        // replace the rawdata array with a new array of statistics
        $table->rawdata = array();

        // we must also unset the table->use_pages property
        // which was automatically set by $table->query_db()
        // because it forces the creation of a paging bar
        // which this report does not need or want
        $table->pageable(false);

        $d_index_max = 10;

        // initialize main rows in the statistics table
        foreach ($fields as $row => $field) {
            $table->rawdata[$row] = (object)array('responsefield' => $field);
        }

        // set indexes for aggregate rows
        $row_max = count($fields) - 1;
        $average_row = $row_max + 1;
        $percent_row = $row_max + 2;
        $d_index_row = $row_max + 3;

        // add aggregate rows
        $table->rawdata[$average_row] = (object)array('responsefield' => 'average');
        $table->rawdata[$percent_row] = (object)array('responsefield' => 'percent');
        $table->rawdata[$d_index_row] = (object)array('responsefield' => 'd_index');

        // arrays used to detect empty rows and columns
        $delete_rows = array_fill(0, $d_index_row, true);
        $delete_columns = array();
        $delete_all_columns = true;

        // format the statistics
        foreach ($question_columns as $id => $column) {

            // assume this column is empty
            $delete_columns[$column] = true;

            // add details about each field
            foreach ($fields as $row => $field) {

                // detect if this field is a string field
                // if it is, then the field values are string ids
                // and will need to be converted to strings later
                $is_string_field = in_array($field, $string_fields);

                // get the value of each response to this field and the count of that value
                $values = array();
                if (isset($q[$id])) {
                    foreach ($q[$id][$field] as $value => $count) {
                        if (is_numeric($value) && $count) {
                            if ($is_string_field) {
                                $value = $table->set_legend($column, $value);
                            }
                            $percent  = round(100*$count/$q[$id]['count']['total']).'%';
                            $percent  = html_writer::tag('span', $percent, array('class'=>'percent'));
                            $values[] = html_writer::tag('li', $percent.' '.$value, array('class'=>$field));
                        }
                    }
                }

                // sort the values by frequency (using user-defined function)
                usort($values, array($this, 'usort_statistics'));

                // add statistics values for this field
                if (count($values)) {
                    $delete_all_columns = false;
                    $delete_rows[$row] = false;
                    $delete_columns[$column] = false;
                    $values = implode("\n", $values);
                    $params = array('class'=>'response');
                    $table->rawdata[$row]->$column = html_writer::tag('ul', $values, $params);
                } else {
                    $table->rawdata[$row]->$column = '&nbsp;';
                }

 				// set percent correct and discrimination index for this question
				$average = '';
				$percent = '';
				$d_index = '';
				if (isset($q[$id]['count'])) {
					// average and percent correct
					if ($q[$id]['count']['total']) {
						$average = round($q[$id]['count']['sum'] / $q[$id]['count']['total']).'%';
						$percent = round(100*$q[$id]['count']['correct'] / $q[$id]['count']['total']).'%';
						$percent .= ' ('.$q[$id]['count']['correct'].'/'.$q[$id]['count']['total'].')';
					}
					// discrimination index
					if ($q[$id]['count']['lo']) {
						$d_index = min($d_index_max, round($q[$id]['count']['hi'] / $q[$id]['count']['lo'], 1));
					} else {
						$d_index = $q[$id]['count']['hi'] ? $d_index_max : 0;
					}
					$d_index .= ' ('.$q[$id]['count']['hi'].'/'.$q[$id]['count']['lo'].')';
                    $delete_rows[$average_row] = false;
                    $delete_rows[$percent_row] = false;
                    $delete_rows[$d_index_row] = false;
				}
				$table->rawdata[$average_row]->$column = $average;
				$table->rawdata[$percent_row]->$column = $percent;
				$table->rawdata[$d_index_row]->$column = $d_index;

            } // end foreach field
        }

        // remove ununsed rows and columns
        if ($delete_all_columns) {
            $table->rawdata = array();
        } else {
            $table->delete_rows($delete_rows);
            $table->delete_columns($delete_columns);
        }
    }

    /**
     * get_hi_lo_scores
     *
     * @param xxx $attempts (passed by reference)
     * @return xxx
     */
    function get_hi_lo_scores(&$attempts)  {

        // get attempt scores
        $scores = array();
        foreach ($attempts as $attempt) {
            $scores[] = $attempt->score;
        }

        // sort and count attempt scores
        sort($scores);
        $count = count($scores);

        // return hi and lo score
        if ($count==0) {
            return array(0, 0);
        }
        if ($count==1) {
            return array(0, $scores[0]);
        }
        $hi_score = $scores[round($count*2/3)];
        $lo_score = $scores[round($count*1/3)];
        return array($hi_score, $lo_score);
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
        if (! is_array($table->rawdata[$attemptid]->$column)) {
            $table->rawdata[$attemptid]->$column = array();
        }

        array_push($table->rawdata[$attemptid]->$column, $response);
    }

    /**
     * usort_statistics
     *
     * @param string $a value from array to be sorted
     * @param string $b value from array to be sorted
     * @return int 1 (put $a after $b), -1 (put $a before $b), or 0 ($a and $b are equal)
     */
    function usort_statistics($a, $b)  {
        // sorts by percent (descending( and text (ascending)
        // assuming first chars in $a and $b are a percentage

        // extract $a's percent and text
        $a = strip_tags($a);
        if ($pos = strpos($a, '%')) {
            $a_percent = intval(substr($a, 0, $pos));
            $a_text    = trim(substr($a, $pos + 1));
        } else {
            $a_percent = intval($a);
            $a_text    = '';
        }

        // extract $b's percent and text
        $b = strip_tags($b);
        if ($pos = strpos($b, '%')) {
            $b_percent = intval(substr($b, 0, $pos));
            $b_text    = trim(substr($b, $pos + 1));
        } else {
            $b_percent = intval(strip_tags($b));
            $b_text    = '';
        }

        // sort by percents (descending)
        if ($a_percent < $b_percent) {
            return 1;
        }
        if ($a_percent > $b_percent) {
            return -1;
        }

        // percents are equal, so sort by texts (ascending)
        if ($a_text > $b_text) {
            return 1;
        }
        if ($a_text < $b_text) {
            return -1;
        }

        // percents and texts are equal - shouldn't happen !!
        return 0;
    }
}
