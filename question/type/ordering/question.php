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
 * Represents an ordering question.
 *
 * @package    qtype_ordering
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

    /** The minimum number of items to create a subset */
    const MIN_SUBSET_ITEMS = 2;

    /** Default value for numberingstyle */
    const NUMBERING_STYLE_DEFAULT = 'none';

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

    /** @var int {@see LAYOUT_VERTICAL} or {@see LAYOUT_HORIZONTAL}. */
    public $layouttype;

    /** @var int {@see SELECT_ALL}, {@see SELECT_RANDOM} or {@see SELECT_CONTIGUOUS}. */
    public $selecttype;

    /** @var int if {@see $selecttype} is not SELECT_ALL, then the number to select. */
    public $selectcount;

    /** @var int Which grading strategy to use. One of the GRADING_... constants. */
    public $gradingtype;

    /** @var bool Should details of the grading calculation be shown to students. */
    public $showgrading;

    /** @var string How to number the items. A key from the array returned by {@see get_numbering_styles()}. */
    public $numberingstyle;

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

    /** @var array of answerids in correct order */
    public $correctresponse;

    /** @var array contatining current order of answerids */
    public $currentresponse;

    /** @var array of scored for every item */
    protected $itemscores = [];

    public function start_attempt(question_attempt_step $step, $variant) {
        $countanswers = count($this->answers);

        // Sanitize "selecttype".
        $selecttype = $this->selecttype;
        $selecttype = max(0, $selecttype);
        $selecttype = min(2, $selecttype);

        // Sanitize "selectcount".
        $selectcount = $this->selectcount;
        $selectcount = max(self::MIN_SUBSET_ITEMS, $selectcount);
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
                $answerids = array_keys($this->answers);
                break;

            case self::SELECT_RANDOM:
                $answerids = array_rand($this->answers, $selectcount);
                break;

            case self::SELECT_CONTIGUOUS:
                $answerids = array_keys($this->answers);
                $offset = mt_rand(0, $countanswers - $selectcount);
                $answerids = array_slice($answerids, $offset, $selectcount);
                break;
        }

        $this->correctresponse = $answerids;
        $step->set_qt_var('_correctresponse', implode(',', $this->correctresponse));

        shuffle($answerids);
        $this->currentresponse = $answerids;
        $step->set_qt_var('_currentresponse', implode(',', $this->currentresponse));
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->currentresponse = array_filter(explode(',', $step->get_qt_var('_currentresponse')));
        $this->correctresponse = array_filter(explode(',', $step->get_qt_var('_correctresponse')));
    }

    public function validate_can_regrade_with_other_version(question_definition $otherversion): ?string {
        $basemessage = parent::validate_can_regrade_with_other_version($otherversion);
        if ($basemessage) {
            return $basemessage;
        }

        if (count($this->answers) != count($otherversion->answers)) {
            return get_string('regradeissuenumitemschanged', 'qtype_ordering');
        }

        return null;
    }

    public function update_attempt_state_data_for_new_version(
            question_attempt_step $oldstep, question_definition $oldquestion) {
        parent::update_attempt_state_data_for_new_version($oldstep, $oldquestion);

        $mapping = array_combine(array_keys($oldquestion->answers), array_keys($this->answers));

        $oldorder = explode(',', $oldstep->get_qt_var('_currentresponse'));
        $neworder = [];
        foreach ($oldorder as $oldid) {
            $neworder[] = $mapping[$oldid] ?? $oldid;
        }

        $oldcorrect = explode(',', $oldstep->get_qt_var('_correctresponse'));
        $newcorrect = [];
        foreach ($oldcorrect as $oldid) {
            $newcorrect[] = $mapping[$oldid] ?? $oldid;
        }

        return [
            '_currentresponse' => implode(',', $neworder),
            '_correctresponse' => implode(',', $newcorrect),
        ];
    }

    public function get_expected_data() {
        $name = $this->get_response_fieldname();
        return [$name => PARAM_TEXT];
    }

    public function get_correct_response() {
        $correctresponse = $this->correctresponse;
        foreach ($correctresponse as $position => $answerid) {
            $answer = $this->answers[$answerid];
            $correctresponse[$position] = $answer->md5key;
        }
        $name = $this->get_response_fieldname();
        return [$name => implode(',', $correctresponse)];
    }

    public function summarise_response(array $response) {
        $name = $this->get_response_fieldname();
        $items = [];
        if (array_key_exists($name, $response)) {
            $items = explode(',', $response[$name]);
        }
        $answerids = [];
        foreach ($this->answers as $answer) {
            $answerids[$answer->md5key] = $answer->id;
        }
        foreach ($items as $i => $item) {
            if (array_key_exists($item, $answerids)) {
                $item = $this->answers[$answerids[$item]];
                $item = $this->html_to_text($item->answer, $item->answerformat);
                $item = shorten_text($item, 10, true); // Force truncate at 10 chars.
                $items[$i] = $item;
            } else {
                $items[$i] = ''; // Shouldn't happen!
            }
        }
        return implode('; ', array_filter($items));
    }

    public function classify_response(array $response) {
        $this->update_current_response($response);
        $fraction = 1 / count($this->correctresponse);

        $classifiedresponse = [];
        foreach ($this->correctresponse as $position => $answerid) {
            if (in_array($answerid, $this->currentresponse)) {
                $currentposition = array_search($answerid, $this->currentresponse);
            }

            $answer = $this->answers[$answerid];
            $subqid = question_utils::to_plain_text($answer->answer, $answer->answerformat);

            // Truncate responses longer than 100 bytes because they cannot be stored in the database.
            // CAUTION: This will mess up answers which are not unique within the first 100 chars!
            $maxbytes = 100;
            if (strlen($subqid) > $maxbytes) {
                // If the truncation point is in the middle of a multi-byte unicode char,
                // we remove the incomplete part with a preg_match() that is unicode aware.
                $subqid = substr($subqid, 0, $maxbytes);
                if (preg_match('/^(.|\n)*/u', '', $subqid, $match)) {
                    $subqid = $match[0];
                }
            }

            $classifiedresponse[$subqid] = new question_classified_response(
                $currentposition + 1,
                get_string('positionx', 'qtype_ordering', $currentposition + 1),
                ($currentposition == $position) * $fraction
            );
        }

        return $classifiedresponse;
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
        return (isset($old[$name]) && isset($new[$name]) && $old[$name] == $new[$name]);
    }

    public function grade_response(array $response) {
        $this->update_current_response($response);

        $countcorrect = 0;
        $countanswers = 0;

        $gradingtype = $this->gradingtype;
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
        return [
            $fraction,
            question_state::graded_state_for_fraction($fraction),
        ];
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question') {
            if ($filearea == 'answer') {
                $answerid = reset($args); // Value of "itemid" is answer id.
                return array_key_exists($answerid, $this->answers);
            }
            if (in_array($filearea, ['correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'])) {
                return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);
            }
            if ($filearea == 'hint') {
                return $this->check_hint_file_access($qa, $options, $args);
            }
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

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

    // Custom methods.

    /**
     * Returns response mform field name
     *
     * @return string
     */
    public function get_response_fieldname(): string {
        return 'response_' . $this->id;
    }

    /**
     * Unpack the students' response into an array which updates the question currentresponse.
     *
     * @param array $response Form data
     */
    public function update_current_response(array $response) {
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
            // Note: TH mentions that this is a bit of a hack.
            $this->currentresponse = $ids;
        }
    }

    /**
     * Returns layoutclass
     *
     * @return string
     */
    public function get_ordering_layoutclass(): string {
        switch ($this->layouttype) {
            case self::LAYOUT_VERTICAL:
                return 'vertical';
            case self::LAYOUT_HORIZONTAL:
                return 'horizontal';
            default:
                return ''; // Shouldn't happen!
        }
    }

    /**
     * Returns array of next answers
     *
     * @param array $answerids array of answers id
     * @param bool $lastitem Include last item?
     * @return array of id of next answer
     */
    public function get_next_answerids(array $answerids, bool $lastitem = false): array {
        $nextanswerids = [];
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
    public function get_previous_and_next_answerids(array $answerids, bool $all = false): array {
        $prevnextanswerids = [];
        $next = $answerids;
        $prev = [];
        while ($answerid = array_shift($next)) {
            if ($all) {
                $prevnextanswerids[$answerid] = (object) [
                    'prev' => $prev,
                    'next' => $next,
                ];
            } else {
                $prevnextanswerids[$answerid] = (object) [
                    'prev' => [empty($prev) ? 0 : $prev[0]],
                    'next' => [empty($next) ? 0 : $next[0]],
                ];
            }
            array_unshift($prev, $answerid);
        }
        return $prevnextanswerids;
    }

    /**
     * Search for best ordered subset
     *
     * @param bool $contiguous A flag indicating whether only contiguous values should be considered for inclusion in the subset.
     * @return array
     */
    public function get_ordered_subset(bool $contiguous): array {

        $positions = $this->get_ordered_positions($this->correctresponse, $this->currentresponse);
        $subsets = $this->get_ordered_subsets($positions, $contiguous);

        // The best subset (longest and leftmost).
        $bestsubset = [];

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
    public function get_ordered_positions(array $correctresponse, array $currentresponse): array {
        $positions = [];
        foreach ($currentresponse as $answerid) {
            $positions[] = array_search($answerid, $correctresponse);
        }
        return $positions;
    }

    /**
     * Get all ordered subsets in the positions array
     *
     * @param array $positions maps an item's current position to its correct position
     * @param bool $contiguous TRUE if searching only for contiguous subsets; otherwise FALSE
     * @return array of ordered subsets from within the $positions array
     */
    public function get_ordered_subsets(array $positions, bool $contiguous): array {

        // Var $subsets is the collection of all subsets within $positions.
        $subsets = [];

        // Loop through the values at each position.
        foreach ($positions as $p => $value) {

            // Is $value a "new" value that cannot be added to any $subsets found so far?
            $isnew = true;

            // An array of new and saved subsets to be added to $subsets.
            $new = [];

            // Append the current value to any subsets to which it belongs
            // i.e. any subset whose end value is less than the current value.
            foreach ($subsets as $s => $subset) {

                // Get value at end of $subset.
                $end = $positions[end($subset)];

                switch (true) {

                    case ($value == ($end + 1)):
                        // For a contiguous value, we simply append $p to the subset.
                        $isnew = false;
                        $subsets[$s][] = $p;
                        break;

                    case $contiguous:
                        // If the $contiguous flag is set, we ignore non-contiguous values.
                        break;

                    case ($value > $end):
                        // For a non-contiguous value, we save the subset so far,
                        // because a value between $end and $value may be found later,
                        // and then append $p to the subset.
                        $isnew = false;
                        $new[] = $subset;
                        $subsets[$s][] = $p;
                        break;
                }
            }

            // If this is a "new" value, add it as a new subset.
            if ($isnew) {
                $new[] = [$p];
            }

            // Append any "new" subsets that were found during this iteration.
            if (count($new)) {
                $subsets = array_merge($subsets, $new);
            }
        }

        return $subsets;
    }

    /**
     * Helper function for get_select_types, get_layout_types, get_grading_types
     *
     * @param array $types
     * @param int $type
     * @return array|string array if $type is not specified and single string if $type is specified
     * @throws coding_exception
     * @codeCoverageIgnore
     */
    public static function get_types(array $types, $type): array|string {
        if ($type === null) {
            return $types; // Return all $types.
        }
        if (array_key_exists($type, $types)) {
            return $types[$type]; // One $type.
        }

        throw new coding_exception('Invalid type: ' . $type);
    }

    /**
     * Returns available values and descriptions for field "selecttype"
     *
     * @param int|null $type
     * @return array|string array if $type is not specified and single string if $type is specified
     * @codeCoverageIgnore
     */
    public static function get_select_types(int $type = null): array|string {
        $plugin = 'qtype_ordering';
        $types = [
            self::SELECT_ALL => get_string('selectall', $plugin),
            self::SELECT_RANDOM => get_string('selectrandom', $plugin),
            self::SELECT_CONTIGUOUS => get_string('selectcontiguous', $plugin),
        ];
        return self::get_types($types, $type);
    }

    /**
     * Returns available values and descriptions for field "layouttype"
     *
     * @param int|null $type
     * @return array|string array if $type is not specified and single string if $type is specified
     * @codeCoverageIgnore
     */
    public static function get_layout_types(int $type = null): array|string {
        $plugin = 'qtype_ordering';
        $types = [
            self::LAYOUT_VERTICAL   => get_string('vertical',   $plugin),
            self::LAYOUT_HORIZONTAL => get_string('horizontal', $plugin),
        ];
        return self::get_types($types, $type);
    }

    /**
     * Returns available values and descriptions for field "gradingtype"
     *
     * @param int|null $type
     * @return array|string array if $type is not specified and single string if $type is specified
     * @codeCoverageIgnore
     */
    public static function get_grading_types(int $type = null): array|string {
        $plugin = 'qtype_ordering';
        $types = [
            self::GRADING_ALL_OR_NOTHING => get_string('allornothing', $plugin),
            self::GRADING_ABSOLUTE_POSITION => get_string('absoluteposition', $plugin),
            self::GRADING_RELATIVE_TO_CORRECT => get_string('relativetocorrect', $plugin),
            self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST => get_string('relativenextexcludelast', $plugin),
            self::GRADING_RELATIVE_NEXT_INCLUDE_LAST => get_string('relativenextincludelast', $plugin),
            self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT => get_string('relativeonepreviousandnext', $plugin),
            self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT => get_string('relativeallpreviousandnext', $plugin),
            self::GRADING_LONGEST_ORDERED_SUBSET => get_string('longestorderedsubset', $plugin),
            self::GRADING_LONGEST_CONTIGUOUS_SUBSET => get_string('longestcontiguoussubset', $plugin),
        ];
        return self::get_types($types, $type);
    }

    /**
     * Get the numbering styles supported.
     *
     * For each style, there should be a corresponding lang string 'numberingstylexxx' in the qtype_ordering language file,
     * a case in the switch statement in number_in_style, and it should be listed in the definition of this column in install.xml.
     *
     * @param string|null $style The specific numbering style to retrieve.
     * @return array|string Numbering style(s).
     *                      The keys are style identifiers, and the values are the corresponding language strings.
     * @codeCoverageIgnore
     */
    public static function get_numbering_styles(string $style = null): array|string {
        $plugin = 'qtype_ordering';
        $styles = [
            'none' => get_string('numberingstylenone', $plugin),
            'abc' => get_string('numberingstyleabc', $plugin),
            'ABCD' => get_string('numberingstyleABCD', $plugin),
            '123' => get_string('numberingstyle123', $plugin),
            'iii' => get_string('numberingstyleiii', $plugin),
            'IIII' => get_string('numberingstyleIIII', $plugin),
        ];
        return self::get_types($styles, $style);
    }

    /**
     * Return the number of subparts of this response that are correct|partial|incorrect.
     *
     * @param array $response A response.
     * @return array Array of three elements: the number of correct subparts,
     * the number of partial correct subparts and the number of incorrect subparts.
     */
    public function get_num_parts_right(array $response): array {
        $this->update_current_response($response);
        $gradingtype = $this->gradingtype;

        $numright = 0;
        $numpartial = 0;
        $numincorrect = 0;
        list($correctresponse, $currentresponse) = $this->get_response_depend_on_grading_type($gradingtype);

        foreach ($this->currentresponse as $position => $answerid) {
            [$fraction, $score, $maxscore] =
                $this->get_fraction_maxscore_score_of_item($position, $answerid, $correctresponse, $currentresponse);
            if (is_null($fraction)) {
                continue;
            }

            if ($fraction > 0.999999) {
                $numright++;
            } else if ($fraction < 0.000001) {
                $numincorrect++;
            } else {
                $numpartial++;
            }
        }

        return [$numright, $numpartial, $numincorrect];
    }

    /**
     * Returns the grade for one item, base on the fraction scale.
     *
     * @param int $position The position of the current response.
     * @param int $answerid The answerid of the current response.
     * @param array $correctresponse The correct response list base on grading type.
     * @param array $currentresponse The current response list base on grading type.
     * @return array.
     */
    protected function get_fraction_maxscore_score_of_item(
        int $position,
        int $answerid,
        array $correctresponse,
        array $currentresponse
    ): array {
        $gradingtype = $this->gradingtype;

        $score    = 0;
        $maxscore = null;

        switch ($gradingtype) {
            case self::GRADING_ALL_OR_NOTHING:
            case self::GRADING_ABSOLUTE_POSITION:
                if (isset($correctresponse[$position])) {
                    if ($correctresponse[$position] == $answerid) {
                        $score = 1;
                    }
                    $maxscore = 1;
                }
                break;
            case self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
            case self::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                if (isset($correctresponse[$answerid])) {
                    if (isset($currentresponse[$answerid]) && $currentresponse[$answerid] == $correctresponse[$answerid]) {
                        $score = 1;
                    }
                    $maxscore = 1;
                }
                break;

            case self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
            case self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                if (isset($correctresponse[$answerid])) {
                    $maxscore = 0;
                    $prev = $correctresponse[$answerid]->prev;
                    $maxscore += count($prev);
                    $prev = array_intersect($prev, $currentresponse[$answerid]->prev);
                    $score += count($prev);
                    $next = $correctresponse[$answerid]->next;
                    $maxscore += count($next);
                    $next = array_intersect($next, $currentresponse[$answerid]->next);
                    $score += count($next);
                }
                break;

            case self::GRADING_LONGEST_ORDERED_SUBSET:
            case self::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                if (isset($correctresponse[$position])) {
                    if (isset($currentresponse[$position])) {
                        $score = $currentresponse[$position];
                    }
                    $maxscore = 1;
                }
                break;

            case self::GRADING_RELATIVE_TO_CORRECT:
                if (isset($correctresponse[$position])) {
                    $maxscore = (count($correctresponse) - 1);
                    $answerid = $currentresponse[$position];
                    $correctposition = array_search($answerid, $correctresponse);
                    $score = ($maxscore - abs($correctposition - $position));
                    if ($score < 0) {
                        $score = 0;
                    }
                }
                break;
        }
        $fraction = $maxscore ? $score / $maxscore : $maxscore;

        return [$fraction, $score, $maxscore];
    }

    /**
     * Get correcresponse and currentinfo depending on grading type.
     *
     * @param string $gradingtype The kind of grading.
     * @return array Correctresponse and currentresponsescore in one array.
     */
    protected function get_response_depend_on_grading_type(string $gradingtype): array {

        $correctresponse = [];
        $currentresponse = [];
        switch ($gradingtype) {
            case self::GRADING_ALL_OR_NOTHING:
            case self::GRADING_ABSOLUTE_POSITION:
            case self::GRADING_RELATIVE_TO_CORRECT:
                $correctresponse = $this->correctresponse;
                $currentresponse = $this->currentresponse;
                break;

            case self::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
            case self::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                $lastitem = ($gradingtype == self::GRADING_RELATIVE_NEXT_INCLUDE_LAST);
                $correctresponse = $this->get_next_answerids($this->correctresponse, $lastitem);
                $currentresponse = $this->get_next_answerids($this->currentresponse, $lastitem);
                break;

            case self::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
            case self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                $all = ($gradingtype == self::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT);
                $correctresponse = $this->get_previous_and_next_answerids($this->correctresponse, $all);
                $currentresponse = $this->get_previous_and_next_answerids($this->currentresponse, $all);
                break;

            case self::GRADING_LONGEST_ORDERED_SUBSET:
            case self::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                $correctresponse = $this->correctresponse;
                $currentresponse = $this->currentresponse;
                $contiguous = ($gradingtype == self::GRADING_LONGEST_CONTIGUOUS_SUBSET);
                $subset = $this->get_ordered_subset($contiguous);
                foreach ($currentresponse as $position => $answerid) {
                    if (array_search($position, $subset) === false) {
                        $currentresponse[$position] = 0;
                    } else {
                        $currentresponse[$position] = 1;
                    }
                }
                break;
        }

        return [$correctresponse, $currentresponse];
    }

    /**
     * Returns score for one item depending on correctness and question settings.
     *
     * @param question_definition $question question definition object
     * @param int $position The position of the current response.
     * @param int $answerid The answerid of the current response.
     * @return array (score, maxscore, fraction, percent, class)
     */
    public function get_ordering_item_score(question_definition $question, int $position, int $answerid): array {

        if (!isset($this->itemscores[$position])) {

            [$correctresponse, $currentresponse] = $this->get_response_depend_on_grading_type($this->gradingtype);

            $percent  = 0;    // 100 * $fraction.
            [$fraction, $score, $maxscore] =
                $this->get_fraction_maxscore_score_of_item($position, $answerid, $correctresponse, $currentresponse);

            if ($maxscore === null) {
                // An unscored item is either an illegal item
                // or last item of RELATIVE_NEXT_EXCLUDE_LAST
                // or an item in an incorrect ALL_OR_NOTHING
                // or an item from an unrecognized grading type.
                $class = 'unscored';
            } else {
                if ($maxscore > 0) {
                    $percent = round(100 * $fraction, 0);
                }
                $class = match (true) {
                    $fraction > 0.999999 => 'correct',
                    $fraction < 0.000001 => 'incorrect',
                    $fraction >= 0.66 => 'partial66',
                    $fraction >= 0.33 => 'partial33',
                    default => 'partial00',
                };
            }

            $itemscores = [
                'score' => $score,
                'maxscore' => $maxscore,
                'fraction' => $fraction,
                'percent' => $percent,
                'class' => $class,
            ];
            $this->itemscores[$position] = $itemscores;
        }

        return $this->itemscores[$position];
    }

}
