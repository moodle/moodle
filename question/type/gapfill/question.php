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
 * Gapfill question definition class. Mainly about runtime
 *
 * @package    qtype_gapfill
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Gapfill question definition class.
 *
 * @package    qtype_gapfill
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapfill_question extends question_graded_automatically_with_countback {

    /**
     * Apparently not used
     *
     * @var string
     */
    public $answer;

    /**
     * Display draggables after the body of the question
     * @var boolean
     */
    public $optionsaftertext;

    /**
     * answerdisplay is a string of either gapfill,dropdown or drag drop
     * @var string
     */
    public $answerdisplay;


    /**
     * The only place this appears to be used is in the tests, can it be deleted?
     * @var array
     */
    public $shuffledanswers;

    /**
     * Should letterhints be generated in interactive with feedback mode. This takes the form
     * of adding an additional letter to the hints shown.
     *
     * @var boolean
     */
    public $letterhints;

    /**
     * Draggable options will dissapear after the first time they have been used, i.e.
     * they can only be used once.
     *
     * @var boolean
     */
    public $singleuse;

    /**
     * Values from the gapfill_settings table for per gap
     * feedback.
     *
     * @var \stdClass
     */
    public $itemsettings;


    /**
     * Where an answer can be correct in more than one gap, only give a mark for one of them
     * e.g. if it is olympic medals gold,silver and bronze, only give one mark if gold is
     * entered in every gap
     * @var boolean
     */
    public $noduplicates;

    /**
     * Disable regular expression processing, useful if you want maths symbols or html tags
     * treated as simple literals
     * @var boolean
     */
    public $disableregex;

    /**
     * Set the size of every gap to the size of the larges so students do not
     * get an idea of the correct answer from gap sizes
     *
     * @var boolean
     */
    public $fixedgapsize;

    /**
     * The size of the biggest gap (used when fixedgapsize is true
     * @var int
     */
    public $maxgapsize;

    /**
     * Feedback when the response is entirely correct
     * @var string
     */
    public $correctfeedback = '';
    /**
     * Feedback when the response is partially correct
     * @var string
     */
    public $partiallycorrectfeedback = '';
    /**
     * Feedback when the response is incorrect
     * @var string
     */
    public $incorrectfeedback = '';
    /**
     * Typically html
     * @var string
     */
    public $correctfeedbackformat;
    /**
     * Typically html
     * @var string
     */
    public $partiallycorrectfeedbackformat;
    /**
     * Typically html
     * @var string
     */
    public $incorrectfeedbackformat;


    /**
     * its a whole number, it's only called fraction because it is referred to that in core
     * code
     * @var int
     */
    public $fraction;

    /**
     * How many gaps in this question
     * @var number
     */
    public $gapcount;

    /**
     * wronganswers is used, but would be better named as distractors
     * @var string comma delimited
     */
    public $wronganswers;

    /**
     * By default Cat is treated the same as cat. Setting it to 1 will make it case sensitive
     * @var boolean
     */
    public $casesensitive;

    /**
     * array of strings as correct question answers
     * @var rray
     */
    public $answers = array();

    /**
     * checks for gaps that get a mark for being left black i.e. [!!]
     * @var string
     */
    public $blankregex = "/!.*!/";

    /**
     * the characters indicating a field to fill i.e. [cat] creates
     * a field where the correct answer is cat
     *
     * @var string
     */
    public $delimitchars = "[]";

    /**
     * @var array place number => group number of the places in the question
     * text where choices can be put. Places are numbered from 1.
     */
    public $places = array();

    /**
     * @var array of strings, one longer than $places, which is achieved by
     * indexing from 0. The bits of question text that go between the placeholders.
     */
    public $textfragments;

    /** @var array index of the right choice for each stem. */
    public $rightchoices;

    /**
     * An array with all correct answers and distractors/wrong answers
     * @var array
     */
    public $allanswers = array();

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step and doing initialisation
     *
     * @param question_attempt_step $step
     * @param number $variant (apparently not used)
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        /* this is for multiple values in any order with the | (or operator)
         * it takes the first occurance of an or, splits it into separate fields
         * that will be draggable when answering. It then discards any subsequent
         * fields with an | in it.
         */
        $answers = [];
        foreach ($this->allanswers as $answer) {
            if (strpos($answer, '|')) {
                $answers = array_merge($answers, explode("|", $answer));
            } else {
                array_push($answers, $answer);
            }
        }
        /* array_unique is for when you have multiple identical answers separated
         * by |, i.e. olympic medals as [gold|silve|bronze]
         */
        $this->allanswers = array_unique($answers);
        shuffle($this->allanswers);
        $step->set_qt_var('_allanswers', serialize($this->allanswers));
    }

    /**
     * get the length of the correct answer and if the | is used
     * the length of the longest of the correct answers
     * @param string $answer
     * @return number
     */
    public function get_size($answer) {
        $answer = htmlspecialchars_decode($answer);
        $words = explode("|", $answer);
        $maxlen = max(array_map('strlen', $words));
        return $maxlen;
    }

    /**
     * returns string of place key value prepended with p, i.e. p0 or p1 etc
     * @param int $place stem number
     * @return string the question-type variable name.
     */
    public function field($place) {
        return 'p' . $place;
    }
    /**
     * get expected data types (?)
     * @return array
     */
    public function get_expected_data() {
        $data = [];
        foreach (array_keys($this->places) as $key) {
            $data['p' . $key] = PARAM_RAW_TRIMMED;
        }
        return $data;
    }

    /**
     * Value returned will be written to responsesummary field of
     * the question_attempts table
     *
     * @param array $response
     * @return string
     */
    public function summarise_response(array $response) {
        $summary = "";
        foreach ($response as $value) {
            $summary .= " " . $value . " ";
        }
        return $summary;
    }

    /**
     * Has the user put something in every gap?
     * @param array $response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        $gapsfilled = 0;
        $iscomplete = true;
        foreach ($this->answers as $rightanswer) {
            $answergiven = array_shift($response);
            if ((!($answergiven == "")) || (preg_match($this->blankregex, $rightanswer->answer))) {
                $gapsfilled++;
            }
        }

        if ($gapsfilled < $this->gapcount) {
            $iscomplete = false;
        }
        return $iscomplete;
    }

    /**
     * Returns prompt asking for answer. Called from renderer
     * if question state is invalid.
     *
     * @param array $response
     * @return string
     */
    public function get_validation_error(array $response) {
        if (!$this->is_gradable_response($response)) {
            return get_string('pleaseenterananswer', 'qtype_gapfill');
        }
    }

    /**
     * What is the correct value for the field
     *
     * @param number $place
     * @return number
     */
    public function get_right_choice_for($place) {
        return $this->places[$place];
    }

    /**
     *
     * @param array $prevresponse
     * @param array $newresponse
     * @return boolean
     *
     * Don't change answer if it is the same
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        /* if you are moving from viewing one question to another this will
         * discard the processing if the answer has not changed. If you don't
         * use this method it will constantantly generate new question steps and
         * the question will be repeatedly set to incomplete. This is a comparison of
         * the equality of two arrays.
         */
        if ($prevresponse == $newresponse) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * A question is gradable if at least one gap response is not blank
     *
     * @param array $response
     * @return boolean
     */
    public function is_gradable_response(array $response) {
        foreach ($response as $answergiven) {
            if (($answergiven !== "")) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return array containing answers that would get full marks
     *
     * @return array
     */
    public function get_correct_response() {
        $response = array();
        foreach ($this->places as $place => $answer) {
            $response[$this->field($place)] = $answer;
        }
        return $response;
    }

    /**
     * Incrementally fill the gap with the letters from the correct answer when in
     * interactive with multiple tries mode
     *
     * @param question_attempt $qa
     * @param array $inputattributes
     * @param string $rightanswer
     * @param string $currentanswer
     * @return array
     */
    public function get_letter_hints(question_attempt $qa, array $inputattributes, $rightanswer, $currentanswer) {
          /* implements  letterhints */
        $question = $qa->get_question();
        if ($qa->get_behaviour_name() == 'interactivecountback') {
            $triesleft = $qa->get_last_behaviour_var('_triesleft');
            $hintcount = count($question->hints);
            $offset = ($hintcount + 1) - $triesleft;
            if (!$question->is_correct_response($currentanswer, $rightanswer)) {
                $hint = substr($rightanswer, 0, $offset);
                $data = $qa->get_last_step()->get_submitted_data();
                if (isset($data['-tryagain'])) {
                    $inputattributes['value'] = $hint;
                }
            }
        }
        return $inputattributes;
    }
    /**
     * called from within renderer in interactive mode
     *
     * @param string $answergiven
     * @param string $rightanswer
     * @return boolean
     */
    public function is_correct_response($answergiven, $rightanswer) {
        if (!$this->casesensitive == 1) {
            $answergiven = core_text::strtolower($answergiven, 'UTF-8');
            $rightanswer = core_text::strtolower($rightanswer, 'UTF-8');
        }

        if ($this->compare_response_with_answer($answergiven, $rightanswer, $this->disableregex)) {
            return true;
        } else if (($answergiven == "") && (preg_match($this->blankregex, $rightanswer))) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Communicate with the dragdrop.js script
     * tell it if singleuse of draggables is
     * enabled
     * @param int $singleuse
     * @return void
     */
    public function initjs($singleuse = 0) {
        global $PAGE;
        $PAGE->requires->js_call_amd('qtype_gapfill/dragdrop', 'init', ['singleuse' => $singleuse ?? 0]);
    }

    /**
     * Check if singleuse is enabled and if so
     * hide this draggable if it has already
     * been dropped into a gap
     *
     * @param string $draggable
     * @param question_attempt $qa
     * @param string $cssclasses
     * @return string
     */
    public function is_used(string $draggable, question_attempt $qa, string $cssclasses): string {
        if ((($this->singleuse == 1) && in_array($draggable, $qa->get_last_qt_data()))) {
            return $cssclasses .= ' hide ';
        }
        return $cssclasses;
    }

    /**
     *
     * @param array $response Passed in from the submitted form
     * @return array
     *
     * Find count of correct answers, used for displaying marks
     * for question. Compares answergiven with right/correct answer
     */
    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach (array_keys($this->places) as $place) {
            $rightanswer = $this->get_right_choice_for($place);
            if (!isset($response[$this->field($place)])) {
                continue;
            }
            $answergiven = $response[$this->field($place)];
            if (!array_key_exists($this->field($place), $response)) {
                continue;
            }
            if (!$this->casesensitive == 1) {
                $answergiven = core_text::strtolower($answergiven, 'UTF-8');
                $rightanswer = core_text::strtolower($rightanswer, 'UTF-8');
            }
            if ($this->compare_response_with_answer($answergiven, $rightanswer, $this->disableregex)) {
                $numright++;
            }
        }
        return [$numright, $this->gapcount];
    }

    /**
     * Given a response, reset the parts that are wrong to a blank string.
     * Relevent when usinginteractive with multiple tries behaviour
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        foreach (array_keys($this->places) as $place) {
            if (!array_key_exists($this->field($place), $response)) {
                continue;
            }
            $answergiven = $response[$this->field($place)];
            $rightanswer = $this->get_right_choice_for($place);
            if (!$this->casesensitive == 1) {
                $answergiven = core_text::strtolower($answergiven);
                $rightanswer = core_text::strtolower($rightanswer);
            }
            if (!$this->compare_response_with_answer($answergiven, $rightanswer, $this->disableregex)) {
                $response[$this->field($place)] = '';
            }
        }
        return $response;
    }

    /**
     * Useful with multiple correct answers per gap, e.g. gold|bronze|silver
     * This code ensures that only one correct answer gets mark credit
     * @param array $response
     * @return array
     */
    public function discard_duplicates(array $response) {
        if ($this->noduplicates == 1) {
            /*
             * find unique values then keeping the same
             * keys but nonanswer in any duplicate non !! gaps
             */
            $au = array_unique($response);
            /* Hash of flatted answer values is is guaranteed
              not to to be an answer for any gap */
            $nonanswer = hash('ripemd160', implode(' ', $this->places));
            foreach (array_keys($response) as $key) {
                $response[$key] = $nonanswer;
            }
            $response = array_merge($response, $au);
            return $response;
        } else {
            return $response;
        }
    }

    /**
     * Calculate grade and returns an array in the form
     * array(2) (
     * [0] => (int) 1
     * [1] => question_state_gradedright object etc etc etc
     *
     * @param array $response
     * @return array
     */
    public function grade_response(array $response) {
        $response = $this->discard_duplicates($response);
        $right = $this->get_num_parts_right($response)[0];
        $this->fraction = $right / $this->gapcount;
        $grade = array($this->fraction, question_state::graded_state_for_fraction($this->fraction));
        return $grade;
    }

    /**
     * Required by the interface question_automatically_gradable_with_countback.
     *
     * @param array $responses
     * @param array $totaltries
     * @return number
     */
    public function compute_final_grade($responses, $totaltries) {
        if (($this->noduplicates == 1) && (count($responses) > 0)) {
             $responses[0] = $this->discard_duplicates($responses[0]);
        }
        $totalscore = 0;
        foreach (array_keys($this->places) as $place) {
            $fieldname = $this->field($place);
            $lastwrongindex = -1;
            $finallyright = false;
            foreach ($responses as $i => $response) {
                $rcfp = $this->get_right_choice_for($place);
                /* break out the loop if response does not contain the key */
                if (!array_key_exists($fieldname, $response)) {
                    continue;
                }
                $resp = $response[$fieldname];
                if (!$this->compare_response_with_answer($resp, $rcfp, $this->disableregex)) {
                    $lastwrongindex = $i;
                    $finallyright = false;
                } else {
                    $finallyright = true;
                }
            }

            if ($finallyright) {
                $totalscore += max(0, 1 - ($lastwrongindex + 1) * $this->penalty);
            }
        }
        return $totalscore / $this->gapcount;
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * Component and filearea refers to fields in the mdl_files table
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
        if ($component == 'question' && in_array($filearea, array('correctfeedback',
                    'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea);
        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    /**
     * Compare the answer given with the correct answer, does it match?
     * To normalise white spaces add
     * $answergiven = preg_replace('/\s+/', ' ', $answergiven);
     *  before if($disableregex etc etc
     *
     * @param string $answergiven
     * @param string $answer
     * @param boolean $disableregex
     * @return boolean
     */
    public function compare_response_with_answer($answergiven, $answer, $disableregex = false) {
        /* converts things like &lt; into < */
        $answer = htmlspecialchars_decode($answer);
        $answergiven = htmlspecialchars_decode($answergiven);

        if ($disableregex == true) {
            /* use the | operator without regular expressions. Useful for
             * programming languages or math related questions which use
             * special characters such as ()and slashes. Introduced with
             * gapfill 1.8
             */
            $correctness = false;
            $answerparts = explode("|", $answer);

            foreach ($answerparts as $answer) {
                // TODO Find why it was not checked earlier, plus strcasecmp would be more concise.
                if (!$this->casesensitive == 1) {
                    $answergiven = core_text::strtolower($answergiven, 'UTF-8');
                    $answer = core_text::strtolower($answer, 'UTF-8');
                }
                if (strcmp(trim($answergiven), trim($answer)) == 0) {
                    $correctness = true;
                } else if (preg_match($this->blankregex, $answer) && $answergiven == "") {
                    $correctness = true;
                }
            }
            return $correctness;
        }

        $pattern = str_replace('/', '\/', $answer);
        $regexp = "";
        /* if the gap contains | then only match complete words
         * this is to avoid a situation where [cat|dog]
         * would match catty or bigcat and adog and doggy
         */
        if (strpos($pattern, "|")) {
            $regexp = '/\b(' . $pattern . ')\b/u';
        } else {
            $regexp = '/^' . $pattern . '$/u';
        }

        // Make the match insensitive if requested to, not sure this is necessary.
        if (!$this->casesensitive) {
            $regexp .= 'i';
        }
        /* the @ is to suppress warnings, e.g. someone forgot to turn off regex matching */
        if (@preg_match($regexp, trim($answergiven))) {
            return true;
        } else if (preg_match($this->blankregex, $answer) && $answergiven == "") {
            return true;
        } else {
            return false;
        }
    }
    /**
     * get an array with information about marking of gap in the form
     * array(1) (  [p1] => array(3)(
     * [value] => (string) 0
     *  [fraction] => (int) 1
     *  [duplicate] => (string) false
     * ))
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return array
     */
    public function get_markedgaps(question_attempt $qa, question_display_options $options) {
        $markedgaps = array();
        $question = $qa->get_question();
        $correctgaps = array();
        foreach (array_keys($question->textfragments) as $place) {
            if ($place < 1) {
                continue;
            }
            $fieldname = $question->field($place);
            $rightanswer = $question->get_right_choice_for($place);
            if (($options->correctness) || ( $options->numpartscorrect)) {
                $response = $qa->get_last_qt_data();

                if (array_key_exists($fieldname, $response)) {
                    if ($question->is_correct_response($response[$fieldname], $rightanswer)) {
                        $markedgaps[$fieldname]['value'] = $response[$fieldname];
                        $markedgaps[$fieldname]['fraction'] = 1;
                        $correctgaps[] = $response[$fieldname];
                    } else {
                        $markedgaps[$fieldname]['value'] = $response[$fieldname];
                        $markedgaps[$fieldname]['fraction'] = 0;
                    }
                }
            }
        }
        $arrunique = array_unique($correctgaps);
        $arrduplicates = array_diff_assoc($correctgaps, $arrunique);
        foreach ($markedgaps as $fieldname => $gap) {
            if (in_array($gap['value'], $arrduplicates)) {
                $markedgaps[$fieldname]['duplicate'] = 'true';
            } else {
                $markedgaps[$fieldname]['duplicate'] = 'false';
            }
        }
        return $markedgaps;
    }
}
