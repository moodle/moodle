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
 * ORDERING question definition classes.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents an ORDERING question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_question extends question_graded_automatically {

    const SELECT_ALL        = 0;
    const SELECT_RANDOM     = 1;
    const SELECT_CONTIGUOUS = 2;

    const LAYOUT_VERTICAL   = 0;
    const LAYOUT_HORIZONTAL = 1;

    const GRADING_ABSOLUTE_POSITION              = 0;
    const GRADING_RELATIVE_NEXT_EXCLUDE_LAST     = 1;
    const GRADING_RELATIVE_NEXT_INCLUDE_LAST     = 2;
    const GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT = 3;
    const GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT = 4;
    const GRADING_LONGEST_ORDERED_SUBSET         = 5;
    const GRADING_LONGEST_CONTIGUOUS_SUBSET      = 6;

    /** fields from "qtype_ordering_options" */
    public $correctfeedback;
    public $correctfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;

    /** records from "question_answers" table */
    public $answers;

    /** records from "qtype_ordering_options" table */
    public $options;

    /** array of answerids in correct order */
    public $correctresponse;

    /** array current order of answerids */
    public $currentresponse;

    public function start_attempt(question_attempt_step $step, $variant) {
        $answers = $this->get_ordering_answers();
        $options = $this->get_ordering_options();

        $countanswers = count($answers);

        // sanitize "selecttype"
        $selecttype = $options->selecttype;
        $selecttype = max(0, $selecttype);
        $selecttype = min(2, $selecttype);

        // sanitize "selectcount"
        $selectcount = $options->selectcount;
        $selectcount = max(3, $selectcount);
        $selectcount = min($countanswers, $selectcount);

        // ensure consistency between "selecttype" and "selectcount"
        switch (true) {
            case ($selecttype==0): $selectcount = $countanswers; break;
            case ($selectcount==$countanswers): $selecttype = 0; break;
        }

        // extract answer ids
        switch ($selecttype) {
            case 0: // all
                $answerids = array_keys($answers);
                break;

            case 1: // random subset
                $answerids = array_rand($answers, $selectcount);
                break;

            case 2: // contiguous subset
                $answerids = array_keys($answers);
                $offset = mt_rand(0, $countanswers - $selectcount);
                $answerids = array_slice($answerids, $offset, $selectcount, true);
                break;
        }

        $this->correctresponse = $answerids;
        $step->set_qt_var('_correctresponse', implode(',', $this->correctresponse));

        shuffle($answerids);
        $this->currentresponse = $answerids;
        $step->set_qt_var('_currentresponse', implode(',', $this->currentresponse));
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $answers = $this->get_ordering_answers();
        $options = $this->get_ordering_options();
        $this->currentresponse = array_filter(explode(',', $step->get_qt_var('_currentresponse')));
        $this->correctresponse = array_filter(explode(',', $step->get_qt_var('_correctresponse')));
    }

    public function get_expected_data() {
        $name = $this->get_response_fieldname();
        return array($name => PARAM_TEXT);
    }

    public function get_correct_response() {
        $correctresponse = $this->correctresponse;
        foreach ($correctresponse as $position => $answerid) {
            $answer = $this->answers[$answerid];
            $correctresponse[$position] = $answer->md5key;
        }
        $name = $this->get_response_fieldname();
        return array($name => implode(',', $correctresponse));
    }

    public function summarise_response(array $response) {
        return '';
    }

    public function classify_response(array $response) {
        return array();
    }

    public function is_complete_response(array $response) {
        return true;
    }

    public function is_gradable_response(array $response) {
        return true;
    }

    public function get_validation_error(array $response) {
        return '';
    }

    public function is_same_response(array $old, array $new) {
        $name = $this->get_response_fieldname();
        return (isset($old[$name]) && isset($new[$name]) && $old[$name]==$new[$name]);
    }

    public function grade_response(array $response) {
        $this->update_current_response($response);

        $countcorrect = 0;
        $countanswers = 0;

        $options = $this->get_ordering_options();
        switch ($options->gradingtype) {

            case self::GRADING_ABSOLUTE_POSITION:
                $correctresponse = $this->correctresponse;
                $currentresponse = $this->currentresponse;
                foreach ($correctresponse as $position => $answerid) {
                    if (isset($currentresponse[$position])) {
                        if ($currentresponse[$position]==$answerid) {
                            $countcorrect++;
                        }
                    }
                    $countanswers++;
                }
                break;

            case self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
            case self::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                $currentresponse = $this->get_next_answerids($this->currentresponse, ($options->gradingtype==2));
                $correctresponse = $this->get_next_answerids($this->correctresponse, ($options->gradingtype==2));
                foreach ($correctresponse as $thisanswerid => $nextanswerid) {
                    if (isset($currentresponse[$thisanswerid])) {
                        if ($currentresponse[$thisanswerid]==$nextanswerid) {
                            $countcorrect++;
                        }
                    }
                    $countanswers++;
                }
                break;

            case self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
            case self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                $currentresponse = $this->get_previous_and_next_answerids($this->currentresponse, ($options->gradingtype==4));
                $correctresponse = $this->get_previous_and_next_answerids($this->correctresponse, ($options->gradingtype==4));
                foreach ($correctresponse as $thisanswerid => $answerids) {
                    if (isset($currentresponse[$thisanswerid])) {
                        $prev = $currentresponse[$thisanswerid]->prev;
                        $prev = array_intersect($prev, $answerids->prev);
                        $countcorrect += count($prev);
                        $next = $currentresponse[$thisanswerid]->next;
                        $next = array_intersect($next, $answerids->next);
                        $countcorrect += count($next);
                    }
                    $countanswers += count($answerids->prev);
                    $countanswers += count($answerids->next);
                }
                break;

            case self::GRADING_LONGEST_ORDERED_SUBSET:
            case self::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                $subset = $this->get_ordered_subset($options->gradingtype==5);
                $countcorrect = count($subset);
                $countanswers = count($this->currentresponse);
                break;
        }
        if ($countanswers==0) {
            $fraction = 0;
        } else {
            $fraction = ($countcorrect / $countanswers);
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component=='question') {
            if ($filearea=='answer') {
                $answerid = reset($args); // "itemid" is answer id
                return array_key_exists($answerid, $this->answers);

            }
            if (in_array($filearea, $this->qtype->feedback_fields)) {
                return $this->check_combined_feedback_file_access($qa, $options, $filearea);

            }
            if ($filearea=='hint') {
                return $this->check_hint_file_access($qa, $options, $args);
            }
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

    ////////////////////////////////////////////////////////////////////
    // custom methods
    ////////////////////////////////////////////////////////////////////

    public function get_response_fieldname() {
        return 'response_'.$this->id;
    }

    public function update_current_response($response) {
        $name = $this->get_response_fieldname();
        if (isset($response[$name])) {
            $ids = explode(',', $response[$name]);
            foreach ($ids as $i => $id) {
                foreach ($this->answers as $answer) {
                    if ($id==$answer->md5key) {
                        $ids[$i] = $answer->id;
                        break;
                    }
                }
            }
            $this->currentresponse = $ids;
        }
    }

    public function get_ordering_options() {
        global $DB;
        if ($this->options===null) {
            $this->options = $DB->get_record('qtype_ordering_options', array('questionid' => $this->id));
            if (empty($this->options)) {
                $this->options = (object)array(
                    'questionid' => $this->id,
                    'layouttype' => 0, // vertical
                    'selecttype' => 0, // all answers
                    'selectcount' => 0,
                    'gradingtype' => 0, // absolute
                    'correctfeedback' => '',
                    'correctfeedbackformat' => FORMAT_MOODLE, // =0
                    'incorrectfeedback' => '',
                    'incorrectfeedbackformat' => FORMAT_MOODLE, // =0
                    'partiallycorrectfeedback' => '',
                    'partiallycorrectfeedbackformat' => FORMAT_MOODLE // =0
                );
                $this->options->id = $DB->insert_record('qtype_ordering_options', $this->options);
            }
        }
        return $this->options;
    }

    public function get_ordering_answers() {
        global $CFG, $DB;
        if ($this->answers===null) {
            $this->answers = $DB->get_records('question_answers', array('question' => $this->id), 'fraction,id');
            if ($this->answers) {
                if (isset($CFG->passwordsaltmain)) {
                    $salt = $CFG->passwordsaltmain;
                } else {
                    $salt = ''; // complex_random_string()
                }
                foreach ($this->answers as $answerid => $answer) {
                    $this->answers[$answerid]->md5key = 'ordering_item_'.md5($salt.$answer->answer);
                }
            } else {
                $this->answers = array();
            }
        }
        return $this->answers;
    }

    public function get_ordering_layoutclass() {
        $options = $this->get_ordering_options();
        switch ($options->layouttype) {
            case 0:  return 'vertical';
            case 1:  return 'horizontal';
            default: return ''; // shouldn't happen !!
        }
    }

    public function get_next_answerids($answerids, $last_item=false) {
        $nextanswerids = array();
        $i_max = count($answerids);
        $i_max--;
        if ($last_item) {
            $nextanswerid = 0;
        } else {
            $nextanswerid = $answerids[$i_max];
            $i_max--;
        }
        for ($i=$i_max; $i>=0; $i--) {
            $thisanswerid = $answerids[$i];
            $nextanswerids[$thisanswerid] = $nextanswerid;
            $nextanswerid = $thisanswerid;
        }
        return $nextanswerids;
    }

    public function get_previous_and_next_answerids($answerids, $all=false) {
        $prevnextanswerids = array();
        $next = $answerids;
        $prev = array();
        while ($answerid = array_shift($next)) {
            if ($all) {
                $prevnextanswerids[$answerid] = (object)array(
                    'prev' => $prev,
                    'next' => $next
                );
            } else {
                $prevnextanswerids[$answerid] = (object)array(
                    'prev' => array(empty($prev) ? 0 : $prev[0]),
                    'next' => array(empty($next) ? 0 : $next[0])
                );
            }
            array_unshift($prev, $answerid);
        }
        return $prevnextanswerids;
    }

    public function get_ordered_subset($contiguous) {
        $positions = $this->get_ordered_positions($this->correctresponse,
                                                  $this->currentresponse);
        $subsets = $this->get_ordered_subsets($positions, $contiguous, count($positions));
        $countcorrect = 1; // i.e. ignore single item subsets
        $subsetcorrect = array();
        foreach ($subsets as $subset) {
            $count = count($subset);
            if ($count > $countcorrect) {
                $countcorrect = $count;
                $subsetcorrect = $subset;
            }
        }
        return $subsetcorrect;
    }

    public function get_ordered_positions($correctresponse, $currentresponse) {
        $positions = array();
        foreach ($currentresponse as $answerid) {
            $positions[] = array_search($answerid, $correctresponse);
        }
        return $positions;
    }

    public function get_ordered_subsets($positions, $contiguous, $i_max, $i_min=0, $previous=-1) {

        // $subsets is the collection of all subsets within $positions
        $subsets = array();

        // $subset is the main (=earliest or leftmost) subset within $positions
        $subset = array();

        for ($i=$i_min; $i<$i_max; $i++) {
            $current = $positions[$i];

            // if $current is out of sequence, skip it
            if ($current < $previous) {
                continue;
            }

            // if $current is not the position immediately after
            // $previous, then we have a non-contiguous sequence
            if ($current > ($previous + 1)) {

                // fetch all the subsets in the tail of $positions,
                $tailsets = $this->get_ordered_subsets($positions, $contiguous, $i_max, $i+1, $previous);
                foreach ($tailsets as $tailset) {
                    if ($contiguous) {
                        // add this tail subset
                        $subsets[] = $tailset;
                    } else {
                        // prepend $subset-so-far to each tail subset
                        $subsets[] = array_merge($subset, $tailset);
                    }
                }
            }

            // decide if we want to add this $i(tem) to the main $subset
            if ($contiguous==false || $previous < 0 || $current==($previous + 1)) {

                // add $i to the main subset
                $subset[] = $i;

                // update the $previous value
                $previous = $current;
            }
        }
        if (count($subset)) {
            // put the main $subset first
            array_unshift($subsets, $subset);
        }
        return $subsets;
    }

    static public function get_types($types, $type) {
        if ($type===null) {
            return $types; // return all $types
        }
        if (array_key_exists($type, $types)) {
            return $types[$type]; // one $type
        }
        return $type; // shouldn't happen !!
    }

    static public function get_select_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::SELECT_ALL        => get_string('selectall',        $plugin),
            self::SELECT_RANDOM     => get_string('selectrandom',     $plugin),
            self::SELECT_CONTIGUOUS => get_string('selectcontiguous', $plugin)
        );
        return self::get_types($types, $type);
    }

    static public function get_layout_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::LAYOUT_VERTICAL   => get_string('vertical',   $plugin),
            self::LAYOUT_HORIZONTAL => get_string('horizontal', $plugin)
        );
        return self::get_types($types, $type);
    }

    static public function get_grading_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::GRADING_ABSOLUTE_POSITION              => get_string('absoluteposition',           $plugin),
            self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST     => get_string('relativenextexcludelast',    $plugin),
            self::GRADING_RELATIVE_NEXT_INCLUDE_LAST     => get_string('relativenextincludelast',    $plugin),
            self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT => get_string('relativeonepreviousandnext', $plugin),
            self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT => get_string('relativeallpreviousandnext', $plugin),
            self::GRADING_LONGEST_ORDERED_SUBSET         => get_string('longestorderedsubset',       $plugin),
            self::GRADING_LONGEST_CONTIGUOUS_SUBSET      => get_string('longestcontiguoussubset',    $plugin)
        );
        return self::get_types($types, $type);
    }
}
