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
 * Regexp question definition class.
 *
 * @package    qtype_regexp
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a regexp question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp_question extends question_graded_by_strategy
        implements question_response_answer_comparer {

    /** @var bool whether answers should be graded case-sensitively. */
    public $usecase;

    /** @var usehint : hint mode :: None / Letter / Word */
    public $usehint;

    /** @var bool whether all correct alternate answers should be displayed to student on review page. */
    public $studentshowalternate;

    /** @var closest */
    public $closest;

    /** @var array of question_answer. */
    public $answers = [];

    /**
     * Contruct new question.
     */
    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    /**
     * Get expected data.
     */
    public function get_expected_data() {
        return ['answer' => PARAM_RAW_TRIMMED];
    }

    /**
     * Get data.
     */
    public function get_data() {
        return ['answer' => PARAM_RAW_TRIMMED];
    }

    /**
     * Summarise response.
     * @param array $response
     */
    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    /**
     * Summarise response with help.
     * @param array $response
     */
    public function summarise_response_withhelp(array $response) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');
        if (isset($response['answer'])) {
            $answer = $response['answer'];
            $closest = find_closest($this, $currentanswer = $answer, $correctresponse = false, $hintadded = true);
            return $answer.' => '.$closest[0];
        } else {
            return null;
        }
    }

    /**
     * Check if response is complete.
     * @param array $response
     */
    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    /**
     * Check validation.
     * @param array $response
     */
    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_regexp');
    }

    /**
     * Check if is same response.
     * @param array $prevresponse
     * @param array $newresponse
     **/
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    /**
     * Get answers
     **/
    public function get_answers() {
        return $this->answers;
    }

    /**
     * Compare response with answer.
     * @param array $response
     * @param question_answer $answer
     * @return boolean
     */
    public function compare_response_with_answer(array $response, question_answer $answer) {
        global $CFG, $currentanswerwithhint;
        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');
        $response['answer'] = remove_blanks ($response['answer']);
        if ($currentanswerwithhint) {
            $response['answer'] = $currentanswerwithhint;
        }
        if (!$this->usecase) {
            $correctresponse = $this->get_correct_response();
            if (strtoupper($response['answer']) == strtoupper($correctresponse['answer'])) {
                return true;
            }
        }
        if ($response == $this->get_correct_response()) {
            return true;
        }

        // Do NOT match student response against Answer 1 : if it matches, already matched by get_correctresponse() above
        // and Answer 1 may contain metacharacters that do not follow correct regex syntax.
        // Get id of Answer 1.
        foreach ($this->answers as $key => $value) {
            break;
        }
        // If this is Answer 1 then return; do not try to match.
        if ($key == $answer->id) {
            return;
        }
        $answer->answer = has_permutations($answer->answer); // JR added permutations OCT 2012.
        return self::compare_string_with_wildcard(
                $response['answer'], $answer->answer, $answer->fraction, !$this->usecase);
    }

    /**
     * Compare string with wildcard.
     * @param string $string
     * @param string $pattern
     * @param int $grade
     * @param boolean $ignorecase
     * @return boolean
     */
    public static function compare_string_with_wildcard($string, $pattern, $grade, $ignorecase) {
        if (substr($pattern, 0, 2) != '--') {
            // Answers with a positive grade must be anchored for strict match.
            // Incorrect answers are not strictly matched.
            if ($grade > 0) {
                $regexp = '/^' . $pattern . '$/';
            } else {
                $regexp = '/' . $pattern. '/';
            }
            $regexp .= 'u'; // For potential utf-8 characters.
            // Make the match insensitive if requested to.
            if ($ignorecase) {
                $regexp .= 'i';
            }
            if (preg_match($regexp, trim($string))) {
                return true;
            }
        }
        // Testing for absence of needed (right) elements in student's answer, through initial -- coding.
        if (substr($pattern, 0, 2) == '--') {
            if ($ignorecase) {
                $ignorecase = 'i';
            }

            $response1 = substr($pattern, 2);
            $response0 = $string;

            // Testing for absence of more than one needed word.
            if (preg_match('/^.*\&\&.*$/', $response1)) {
                $pattern = '/&&[^(|)]*/';
                $missingstrings = preg_match_all($pattern, $response1, $matches, PREG_OFFSET_CAPTURE);
                $strmissingstrings = $matches[0][0][0];
                $strmissingstrings = substr($strmissingstrings, 2);
                $openparenpos = $matches[0][0][1] - 1;
                $closeparenpos = $openparenpos + strlen($strmissingstrings) + 4;
                $start = substr($response1 , 0, $openparenpos);
                $finish = substr($response1 , $closeparenpos);
                $missingstrings = explode ('&&', $strmissingstrings);
                foreach ($missingstrings as $missingstring) {
                    $missingstring = $start.$missingstring.$finish;
                    if (preg_match('/'.$missingstring.'/'.$ignorecase, $response0) == 0 ) {
                        return true;
                    }
                }
            } else {  // This is *not* a NOT (a OR b OR c etc.) request.
                if (preg_match('/^'.$response1.'$/'.$ignorecase, $response0) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks whether the user is allow to be served a particular file.
     * Copied from shortanswer/question.php
     * @param array $qa
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea,
            $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $this->get_matching_answer(['answer' => $currentanswer]);
            $answerid = reset($args); // Itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    /**
     * Create the appropriate behaviour for an attempt at this quetsion,
     * given the desired (archetypal) behaviour.
     *
     * This default implementation will suit most normal graded questions.
     *
     * If your question is of a patricular type, then it may need to do something
     * different. For example, if your question can only be graded manually, then
     * it should probably return a manualgraded behaviour, irrespective of
     * what is asked for.
     *
     * If your question wants to do somthing especially complicated is some situations,
     * then you may wish to return a particular behaviour related to the
     * one asked for. For example, you migth want to return a
     * qbehaviour_interactive_adapted_for_myqtype.
     *
     * @param question_attempt $qa the attempt we are creating a behaviour for.
     * @param string $preferredbehaviour the requested type of behaviour.
     * @return question_behaviour the new behaviour object.
     */
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        GLOBAL $CFG;
        // Check that regexpadaptivewithhelp behaviour has been installed
        // if not installed, then the regexp questions will follow the "standard" behaviours
        // and Help button will not be available.
        // NOTE: from 2.2 you cannot install regexp if corresponding behaviours have not been installed first
        // see plugin->dependencies in version.php file.
        // Only use the regexpadaptivewithhelp behaviour is question uses hint.
        if ($this->usehint) {
            if ($preferredbehaviour == 'adaptive' && file_exists($CFG->dirroot.'/question/behaviour/regexpadaptivewithhelp/')) {
                return question_engine::make_behaviour('regexpadaptivewithhelp', $qa, $preferredbehaviour);
            }
            if ($preferredbehaviour == 'adaptivenopenalty' &&
                            file_exists($CFG->dirroot.'/question/behaviour/regexpadaptivewithhelpnopenalty/')) {
                return question_engine::make_behaviour('regexpadaptivewithhelpnopenalty', $qa, $preferredbehaviour);
            }
        }
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }
}
