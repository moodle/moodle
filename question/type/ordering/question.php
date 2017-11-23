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
 * Ordering question definition classes.
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this script.
defined('MOODLE_INTERNAL') || die();

/**
 * Represents an ordering question.
 *
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_question extends question_graded_automatically {

    /** Select all answers */
    const SELECT_ALL = 0;
    /** Select random set of answers */
    const SELECT_RANDOM = 1;
    /** Select contiguous subset of answers */
    const SELECT_CONTIGUOUS = 2;

    /** Show answers in vertical list */
    const LAYOUT_VERTICAL = 0;
    /** Show answers in one horizontal line */
    const LAYOUT_HORIZONTAL = 1;


    /** @var int Zero grade on any error */
    const GRADING_ALL_OR_NOTHING = -1;
    /** @var int Counts items, placed into right absolute place */
    const GRADING_ABSOLUTE_POSITION = 0;
    /** @var int Every sequential pair in right order is graded (last pair is excluded) */
    const GRADING_RELATIVE_NEXT_EXCLUDE_LAST = 1;
    /** @var int Every sequential pair in right order is graded (last pair is included) */
    const GRADING_RELATIVE_NEXT_INCLUDE_LAST = 2;
    /** @var int Single answers that are placed before and after each answer is graded if in right order*/
    const GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT = 3;
    /** @var int All answers that are placed before and after each answer is graded if in right order*/
    const GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT = 4;
    /** @var int Only longest ordered subset is graded */
    const GRADING_LONGEST_ORDERED_SUBSET = 5;
    /** @var int Only longest ordered and contiguous subset is graded */
    const GRADING_LONGEST_CONTIGUOUS_SUBSET = 6;
    /** @var int Items are graded relative to their position in the correct answer */
    const GRADING_RELATIVE_TO_CORRECT = 7;

    // Fields from "qtype_ordering_options" table.
    /** @var string */
    public $correctfeedback;
    /** @var int */
    public $correctfeedbackformat;
    /** @var string */
    public $incorrectfeedback;
    /** @var int */
    public $incorrectfeedbackformat;
    /** @var string */
    public $partiallycorrectfeedback;
    /** @var int */
    public $partiallycorrectfeedbackformat;

    /** @var array Records from "question_answers" table */
    public $answers;

    /** @var array Records from "qtype_ordering_options" table */
    public $options;

    /** @var array of answerids in correct order */
    public $correctresponse;

    /** @var array contatining current order of answerids */
    public $currentresponse;

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step.
     *
     * This is where the question can do any initialisation required on a
     * per-attempt basis. For example, this is where the multiple choice
     * question type randomly shuffles the choices (if that option is set).
     *
     * Any information about how the question has been set up for this attempt
     * should be stored in the $step, by calling $step->set_qt_var(...).
     *
     * @param question_attempt_step $step The first step of the {@link question_attempt}
     *      being started. Can be used to store state.
     * @param int $variant which variant of this question to start. Will be between
     *      1 and {@link get_num_variants()} inclusive.
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        $answers = $this->get_ordering_answers();
        $options = $this->get_ordering_options();

        $countanswers = count($answers);

        // Sanitize "selecttype".
        $selecttype = $options->selecttype;
        $selecttype = max(0, $selecttype);
        $selecttype = min(2, $selecttype);

        // Sanitize "selectcount".
        $selectcount = $options->selectcount;
        $selectcount = max(3, $selectcount);
        $selectcount = min($countanswers, $selectcount);

        // Ensure consistency between "selecttype" and "selectcount".
        switch (true) {
            case ($selecttype == self::SELECT_ALL):
                $selectcount = $countanswers;
                break;
            case ($selectcount == $countanswers):
                $selecttype = self::SELECT_ALL;
                break;
        }

        // Extract answer ids.
        switch ($selecttype) {
            case self::SELECT_ALL:
                $answerids = array_keys($answers);
                break;

            case self::SELECT_RANDOM:
                $answerids = array_rand($answers, $selectcount);
                break;

            case self::SELECT_CONTIGUOUS:
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

    /**
     * When an in-progress {@link question_attempt} is re-loaded from the
     * database, this method is called so that the question can re-initialise
     * its internal state as needed by this attempt.
     *
     * For example, the multiple choice question type needs to set the order
     * of the choices to the order that was set up when start_attempt was called
     * originally. All the information required to do this should be in the
     * $step object, which is the first step of the question_attempt being loaded.
     *
     * @param question_attempt_step $step The first step of the {@link question_attempt}
     *      being loaded.
     */
    public function apply_attempt_state(question_attempt_step $step) {
        $answers = $this->get_ordering_answers();
        $options = $this->get_ordering_options();
        $this->currentresponse = array_filter(explode(',', $step->get_qt_var('_currentresponse')));
        $this->correctresponse = array_filter(explode(',', $step->get_qt_var('_correctresponse')));
    }

    /**
     * What data may be included in the form submission when a student submits
     * this question in its current state?
     *
     * This information is used in calls to optional_param. The parameter name
     * has {@link question_attempt::get_field_prefix()} automatically prepended.
     *
     * @return array|string variable name => PARAM_... constant, or, as a special case
     *      that should only be used in unavoidable, the constant question_attempt::USE_RAW_DATA
     *      meaning take all the raw submitted data belonging to this question.
     */
    public function get_expected_data() {
        $name = $this->get_response_fieldname();
        return array($name => PARAM_TEXT);
    }

    /**
     * What data would need to be submitted to get this question correct.
     * If there is more than one correct answer, this method should just
     * return one possibility. If it is not possible to compute a correct
     * response, this method should return null.
     *
     * @return array|null parameter name => value.
     */
    public function get_correct_response() {
        $correctresponse = $this->correctresponse;
        foreach ($correctresponse as $position => $answerid) {
            $answer = $this->answers[$answerid];
            $correctresponse[$position] = $answer->md5key;
        }
        $name = $this->get_response_fieldname();
        return array($name => implode(',', $correctresponse));
    }

    /**
     * Produce a plain text summary of a response.
     *
     * @param array $response a response, as might be passed to {@link grade_response()}.
     * @return string a plain text summary of that response, that could be used in reports.
     */
    public function summarise_response(array $response) {
        $name = $this->get_response_fieldname();
        if (array_key_exists($name, $response)) {
            $items = explode(',', $response[$name]);
        } else {
            $items = array(); // shouldn't happen !!
        }
        $answerids = array();
        foreach ($this->answers as $answer) {
            $answerids[$answer->md5key] = $answer->id;
        }
        foreach ($items as $i => $item) {
            if (array_key_exists($item, $answerids)) {
                $item = $this->answers[$answerids[$item]];
                $item = $this->html_to_text($item->answer, $item->answerformat);
                $item = shorten_text($item, 10, true); // force truncate at 10 chars
                $items[$i] = $item;
            } else {
                $items[$i] = ''; // shouldn't happen !!
            }
        }
        return implode('; ', array_filter($items));
    }

    /**
     * Categorise the student's response according to the categories defined by
     * get_possible_responses.
     *
     * @param array $response a response, as might be passed to {@link grade_response()}.
     * @return array subpartid => {@link question_classified_response} objects.
     *      returns an empty array if no analysis is possible.
     */
    public function classify_response(array $response) {
        return array();
    }

    /**
     * Used by many of the behaviours, to work out whether the student's
     * response to the question is complete. That is, whether the question attempt
     * should move to the COMPLETE or INCOMPLETE state.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response is a complete answer to this question.
     */
    public function is_complete_response(array $response) {
        return true;
    }

    /**
     * Use by many of the behaviours to determine whether the student
     * has provided enough of an answer for the question to be graded automatically,
     * or whether it must be considered aborted.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response can be graded.
     */
    public function is_gradable_response(array $response) {
        return true;
    }

    /**
     * In situations where is_gradable_response() returns false, this method
     * should generate a description of what the problem is.
     * @param array $response
     * @return string the message
     */
    public function get_validation_error(array $response) {
        return '';
    }

    /**
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $old the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $new the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    public function is_same_response(array $old, array $new) {
        $name = $this->get_response_fieldname();
        return (isset($old[$name]) && isset($new[$name]) && $old[$name] == $new[$name]);
    }

    /**
     * Grade a response to the question, returning a fraction between
     * get_min_fraction() and get_max_fraction(), and the corresponding {@link question_state}
     * right, partial or wrong.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (float, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        $this->update_current_response($response);

        $countcorrect = 0;
        $countanswers = 0;

        $options = $this->get_ordering_options();
        $gradingtype = $options->gradingtype;
        switch ($gradingtype) {

            case self::GRADING_ALL_OR_NOTHING:
            case self::GRADING_ABSOLUTE_POSITION:
                $correctresponse = $this->correctresponse;
                $currentresponse = $this->currentresponse;
                foreach ($correctresponse as $position => $answerid) {
                    if (array_key_exists($position, $currentresponse)) {
                        if ($currentresponse[$position] == $answerid) {
                            $countcorrect++;
                        }
                    }
                    $countanswers++;
                }
                if ($gradingtype == self::GRADING_ALL_OR_NOTHING && $countcorrect < $countanswers) {
                    $countcorrect = 0;
                }
                break;

            case self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
            case self::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                $lastitem = ($gradingtype == self::GRADING_RELATIVE_NEXT_INCLUDE_LAST);
                $currentresponse = $this->get_next_answerids($this->currentresponse, $lastitem);
                $correctresponse = $this->get_next_answerids($this->correctresponse, $lastitem);
                foreach ($correctresponse as $thisanswerid => $nextanswerid) {
                    if (array_key_exists($thisanswerid, $currentresponse)) {
                        if ($currentresponse[$thisanswerid] == $nextanswerid) {
                            $countcorrect++;
                        }
                    }
                    $countanswers++;
                }
                break;

            case self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
            case self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                $all = ($gradingtype == self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT);
                $currentresponse = $this->get_previous_and_next_answerids($this->currentresponse, $all);
                $correctresponse = $this->get_previous_and_next_answerids($this->correctresponse, $all);
                foreach ($correctresponse as $thisanswerid => $answerids) {
                    if (array_key_exists($thisanswerid, $currentresponse)) {
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
                $contiguous = ($gradingtype == self::GRADING_LONGEST_CONTIGUOUS_SUBSET);
                $subset = $this->get_ordered_subset($contiguous);
                $countcorrect = count($subset);
                $countanswers = count($this->currentresponse);
                break;

            case self::GRADING_RELATIVE_TO_CORRECT:
                $correctresponse = $this->correctresponse;
                $currentresponse = $this->currentresponse;
                $count = (count($correctresponse) - 1);
                foreach ($correctresponse as $position => $answerid) {
                    if (in_array($answerid, $currentresponse)) {
                        $currentposition = array_search($answerid, $currentresponse);
                        $currentscore = ($count - abs($position - $currentposition));
                        if ($currentscore > 0) {
                            $countcorrect += $currentscore;
                        }
                    }
                    $countanswers += $count;
                }
                break;
        }
        if ($countanswers == 0) {
            $fraction = 0;
        } else {
            $fraction = ($countcorrect / $countanswers);
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    /**
     * Checks whether the user has permission to access a particular file.
     *
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question') {
            if ($filearea == 'answer') {
                $answerid = reset($args); // Value of "itemid" is answer id.
                return array_key_exists($answerid, $this->answers);
            }
            if (in_array($filearea, $this->qtype->feedbackfields)) {
                return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);
            }
            if ($filearea == 'hint') {
                return $this->check_hint_file_access($qa, $options, $args);
            }
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

    ///////////////////////////////////////////////////////
    // methods from "question_graded_automatically" class
    // see "question/type/questionbase.php"
    ///////////////////////////////////////////////////////

    /**
     * Check a request for access to a file belonging to a combined feedback field.
     *
     * Fix a bug in Moodle 2.9 & 3.0, in which this method does not declare $args,
     * so trying to use $args[0] always fails and images in feedback are not shown.
     *
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @return bool whether access to the file should be allowed.
     */
    protected function check_combined_feedback_file_access($qa, $options, $filearea, $args = null) {
        $state = $qa->get_state();
        if (! $state->is_finished()) {
            $response = $qa->get_last_qt_data();
            if (! $this->is_gradable_response($response)) {
                return false;
            }
            list($fraction, $state) = $this->grade_response($response);
        }
        if ($state->get_feedback_class().'feedback' == $filearea) {
            return ($this->id == reset($args));
        } else {
            return false;
        }
    }

    ///////////////////////////////////////////////////////
    // Custom methods
    ///////////////////////////////////////////////////////

    /**
     * Returns response mform field name
     *
     * @return string
     */
    public function get_response_fieldname() {
        return 'response_'.$this->id;
    }

    /**
     * Convert response data from mform into array
     *
     * @param array $response Form data
     * @return array
     */
    public function update_current_response($response) {
        $name = $this->get_response_fieldname();
        if (array_key_exists($name, $response)) {
            $ids = explode(',', $response[$name]);
            foreach ($ids as $i => $id) {
                foreach ($this->answers as $answer) {
                    if ($id == $answer->md5key) {
                        $ids[$i] = $answer->id;
                        break;
                    }
                }
            }
            $this->currentresponse = $ids;
        }
    }

    /**
     * Loads from DB and returns options for question instance
     *
     * @return object
     */
    public function get_ordering_options() {
        global $DB;
        if ($this->options === null) {
            $this->options = $DB->get_record('qtype_ordering_options', array('questionid' => $this->id));
            if (empty($this->options)) {
                $this->options = (object)array(
                    'questionid' => $this->id,
                    'layouttype' => self::LAYOUT_VERTICAL,
                    'selecttype' => self::SELECT_ALL,
                    'selectcount' => 0,
                    'gradingtype' => self::GRADING_ABSOLUTE_POSITION,
                    'showgradingdetails' => 1,
                    'correctfeedback' => '',
                    'correctfeedbackformat' => FORMAT_MOODLE,
                    'incorrectfeedback' => '',
                    'incorrectfeedbackformat' => FORMAT_MOODLE,
                    'partiallycorrectfeedback' => '',
                    'partiallycorrectfeedbackformat' => FORMAT_MOODLE
                );
                $this->options->id = $DB->insert_record('qtype_ordering_options', $this->options);
            }
        }
        return $this->options;
    }

    /**
     * Loads from DB and returns array of answers objects
     *
     * @return array of objects
     */
    public function get_ordering_answers() {
        global $CFG, $DB;
        if ($this->answers === null) {
            $this->answers = $DB->get_records('question_answers', array('question' => $this->id), 'fraction,id');
            if ($this->answers) {
                if (isset($CFG->passwordsaltmain)) {
                    $salt = $CFG->passwordsaltmain;
                } else {
                    $salt = '';
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

    /**
     * Returns layoutclass
     *
     * @return string
     */
    public function get_ordering_layoutclass() {
        $options = $this->get_ordering_options();
        switch ($options->layouttype) {
            case self::LAYOUT_VERTICAL:
                return 'vertical';
            case self::LAYOUT_HORIZONTAL:
                return 'horizontal';
            default:
                return ''; // Shouldn't happen !!
        }
    }

    /**
     * Returns array of next answers
     *
     * @param array $answerids array of answers id
     * @param bool $lastitem Include last item?
     * @return array of id of next answer
     */
    public function get_next_answerids($answerids, $lastitem = false) {
        $nextanswerids = array();
        $imax = count($answerids);
        $imax--;
        if ($lastitem) {
            $nextanswerid = 0;
        } else {
            $nextanswerid = $answerids[$imax];
            $imax--;
        }
        for ($i = $imax; $i >= 0; $i--) {
            $thisanswerid = $answerids[$i];
            $nextanswerids[$thisanswerid] = $nextanswerid;
            $nextanswerid = $thisanswerid;
        }
        return $nextanswerids;
    }

    /**
     * Returns prev and next answers array
     *
     * @param array $answerids array of answers id
     * @param bool $all include all answers
     * @return array of array('prev' => previd, 'next' => nextid)
     */
    public function get_previous_and_next_answerids($answerids, $all = false) {
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

    /**
     * Search for best ordered subset
     *
     * @param bool $contiguous
     * @return array
     */
    public function get_ordered_subset($contiguous) {

        $positions = $this->get_ordered_positions($this->correctresponse,
                                                  $this->currentresponse);

        $subsets = $this->get_ordered_subsets($positions,
                                              $contiguous,
                                              count($positions));

        // The best subset (longest and leftmost).
        $bestsubset = array();

        // The length of the best subset
        // initializing this to 1 means
        // we ignore single item subsets.
        $bestcount = 1;

        foreach ($subsets as $subset) {
            $count = count($subset);
            if ($count > $bestcount) {
                $bestcount = $count;
                $bestsubset = $subset;
            }
        }
        return $bestsubset;
    }

    /**
     * Get array of right answer positions for current response
     *
     * @param array $correctresponse
     * @param array $currentresponse
     * @return array
     */
    public function get_ordered_positions($correctresponse, $currentresponse) {
        $positions = array();
        foreach ($currentresponse as $answerid) {
            $positions[] = array_search($answerid, $correctresponse);
        }
        return $positions;
    }

    /**
     * Get all ordered subsets in the positions array
     *
     * @param array   $positions
     * @param boolean $contiguous TRUE if searching only for contiguous subsets; otherwise FALSE
     * @param integer $imax the length of the $positions array
     * @param integer $imin (optional, default = 0) the index in $position at which to start checking values
     * @param integer $previous (optional, default = -1) the minimum allowed value. Any values less than this will be skipped.
     */
    public function get_ordered_subsets($positions, $contiguous, $imax, $imin=0, $previous=-1) {

        // Var $subsets is the collection of all subsets within $positions.
        $subsets = array();

        // Var $subset is the main (=earliest or leftmost) subset within $positions.
        $subset = array();

        for ($i = $imin; $i < $imax; $i++) {
            $current = $positions[$i];

            switch (true) {

                case ($previous < 0 || $current == ($previous + 1)):
                    // First item, or next item in a contiguous sequence
                    // there is no need to search for $tailsets.
                    $tailsets = array();
                    $prependsubset = false;
                    $appendtosubset = true;
                    break;

                case ($current < $previous || ($contiguous && $current > ($previous + 1))):
                    // Here $current breaks the sequence, so look for subsets that start here.
                    $tailsets = $this->get_ordered_subsets($positions, $contiguous, $imax, $i);
                    $prependsubset = false;
                    $appendtosubset = false;
                    break;

                case ($current > $previous):
                    // A non-contiguous sequence,
                    // so search for subsets in the tail.
                    $tailsets = $this->get_ordered_subsets($positions, $contiguous, $imax, $i + 1, $previous);
                    $prependsubset = true;
                    $appendtosubset = true;
                    break;

                default: // shouldn't happen !!
                    $tailsets = array();
                    $prependsubset = false;
                    $appendtosubset = false;
            }

            // Append any $tailsets that were found.
            foreach ($tailsets as $tailset) {
                if ($prependsubset) {
                    // Prepend $subset-so-far to each tail subset.
                    $subsets[] = array_merge($subset, $tailset);
                } else {
                    // Add this tail subset.
                    $subsets[] = $tailset;
                }
            }

            // Add $i to the main subset
            // update the $previous value.
            if ($appendtosubset) {
                $subset[] = $i;
                $previous = $current;
            }
        }
        if (count($subset)) {
            // Put the main $subset first.
            array_unshift($subsets, $subset);
        }
        return $subsets;
    }

    /**
     * Helper function for get_select_types, get_layout_types, get_grading_types
     *
     * @param array $types
     * @param int $type
     * @return array|string array if $type is not specified and single string if $type is specified
     */
    static public function get_types($types, $type) {
        if ($type === null) {
            return $types; // Return all $types.
        }
        if (array_key_exists($type, $types)) {
            return $types[$type]; // One $type.
        }
        return $type; // Shouldn't happen !!
    }

    /**
     * Returns availibe values and descriptions for field "selecttype"
     *
     * @param int $type
     * @return array|string array if $type is not specified and single string if $type is specified
     */
    static public function get_select_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::SELECT_ALL        => get_string('selectall',        $plugin),
            self::SELECT_RANDOM     => get_string('selectrandom',     $plugin),
            self::SELECT_CONTIGUOUS => get_string('selectcontiguous', $plugin)
        );
        return self::get_types($types, $type);
    }

    /**
     * Returns availibe values and descriptions for field "layouttype"
     *
     * @param int $type
     * @return array|string array if $type is not specified and single string if $type is specified
     */
    static public function get_layout_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::LAYOUT_VERTICAL   => get_string('vertical',   $plugin),
            self::LAYOUT_HORIZONTAL => get_string('horizontal', $plugin)
        );
        return self::get_types($types, $type);
    }

    /**
     * Returns availibe values and descriptions for field "gradingtype"
     *
     * @param int $type
     * @return array|string array if $type is not specified and single string if $type is specified
     */
    static public function get_grading_types($type=null) {
        $plugin = 'qtype_ordering';
        $types = array(
            self::GRADING_ALL_OR_NOTHING                 => get_string('allornothing',               $plugin),
            self::GRADING_ABSOLUTE_POSITION              => get_string('absoluteposition',           $plugin),
            self::GRADING_RELATIVE_TO_CORRECT            => get_string('relativetocorrect',          $plugin),
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
