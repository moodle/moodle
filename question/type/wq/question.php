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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/wq/quizzes/quizzes.php');

class qtype_wq_question extends question_graded_automatically {
    /**
     * @var question_definition
     *   The base question.
     * **/
    public $base;

    /**
     * @var com_wiris_quizzes_api_Question
     *   The com.wiris.quizzes.api.Question object for this question.
     * **/
    public $wirisquestion;

    /**
     * @var com_wiris_quizzes_api_QuestionInstance
     *   The com.wiris.quizzes.api.QuestionInstance object for the current
     *   attempt.
     * **/
    public $wirisquestioninstance;

    /**
     * @var int
     * Number of lines of the auxiliar text field.
     * 10 by default.
     */
    public $auxiliartextfieldlines = 10;

    /**
     * @var bool
     * Whether this question is corrupt and its wirisquestion was removed from the database.
     */
    public $corrupt = false;

    public function __construct(?question_definition $base = null) {
        $this->base = $base;
    }

    /**
     * Initializes Wiris Quizzes question calling the service in order to get the value
     * of the variables to render the question.
     *
     * @param question_attempt_step $step
     *   The attempt step.
     * @param int $variant
     *   The random seed to be used in this question.
     * **/
    public function start_attempt(question_attempt_step $step, $variant) {
        global $USER;

        if ($this->corrupt) {
            $a = new stdClass();
            $a->questionname = $this->name;
            throw new moodle_exception('corruptquestion_attempt', 'qtype_wq', '', $a);
        }

        $this->base->start_attempt($step, $variant);

        // Get variables from Wiris Quizzes service.
        $builder = com_wiris_quizzes_api_Quizzes::getInstance();
        $text = $this->join_all_text();
        $this->wirisquestioninstance = $builder->newQuestionInstance($this->wirisquestion);
        $this->wirisquestioninstance->setRandomSeed($variant);
        $this->wirisquestioninstance->setParameter('user_id', $USER->id);

        // Begin testing code. It's never used in production.
        global $CFG;
        if (isset($CFG->wq_random_seed) && $CFG->wq_random_seed != 'false') {
            $this->wirisquestioninstance->setRandomSeed($CFG->wq_random_seed);
            set_config('wq_random_seed', 'false');
        }
        // End testing code.

        // Create request to call service.
        $request = $builder->newVariablesRequestWithQuestionData($text, $this->wirisquestioninstance);
        // Do the call only if needed.
        if (!$request->isEmpty()) {
            $response = $this->call_wiris_service($request);
            $this->wirisquestioninstance->update($response);
        }
        // Save the result.
        $step->set_qt_var('_qi', $this->wirisquestioninstance->serialize());
    }
    /**
     * Initializes a question from an intermediate state. It reads the question
     * instance form the saved XML and updates the plotter image cache if
     * necessary.
     * **/
    public function apply_attempt_state(question_attempt_step $step) {
        $this->base->apply_attempt_state($step);
        // Recover the questioninstance variable saved on start_attempt().
        $xml = $step->get_qt_var('_qi');
        $builder = com_wiris_quizzes_api_Quizzes::getInstance();
        $this->wirisquestioninstance = $builder->readQuestionInstance($xml, $this->wirisquestion);

        // Be sure that plotter images don't got removed, and recompute them
        // otherwise.
        if (!$this->wirisquestioninstance->areVariablesReady()) {
            // We make a new request to the service if plotter images are not cached.
            $request = $builder->newVariablesRequestWithQuestionData($this->join_all_text(), $this->wirisquestioninstance);
            $response = $this->call_wiris_service($request);
            $this->wirisquestioninstance->update($response);
            // We don't need to save this question instance in database because
            // only the plotter image files were updated.
        }

        // On manual regrade, xml could change. We can't get xml from qt variable
        // So we need to recompute variables.
        // Each attempt builds on the last (question_attempt_step_read_only) shouldn't recompute variables.
        if ($step->get_state() instanceof question_state_complete && !($step instanceof question_attempt_step_read_only)) {
            $request = $builder->newVariablesRequestWithQuestionData($this->join_all_text(), $this->wirisquestioninstance);
            $response = $this->call_wiris_service($request);
            $this->wirisquestioninstance->update($response);
            // Save the result.
            $step->set_qt_var('_qi', $this->wirisquestioninstance->serialize());
        }
    }

    public function get_question_summary() {
        $text = $this->base->get_question_summary();
        return $this->expand_variables_text($text);
    }

    public function get_num_variants() {
        if ($this->wirisquestion->getAlgorithm() != null) {
            return 65536;
        } else {
            return 1;
        }
    }

    public function get_min_fraction() {
        return $this->base->get_min_fraction();
    }

    public function get_max_fraction() {
        return $this->base->get_max_fraction();
    }

    public function clear_wrong_from_response(array $response) {
        return $this->base->clear_wrong_from_response($response);
    }

    public function get_num_parts_right(array $response) {
        return $this->base->get_num_parts_right($response);
    }

    public function get_expected_data() {
        $expected = $this->base->get_expected_data();
        $expected['_sqi'] = PARAM_RAW_TRIMMED;
        $expected['auxiliar_text'] = question_attempt::PARAM_RAW_FILES;
        $expected['attachments'] = question_attempt::PARAM_FILES;
        return $expected;
    }

    public function get_correct_response() {
        return $this->base->get_correct_response();
    }

    public function prepare_simulated_post_data($simulatedresponse) {
        return $this->base->prepare_simulated_post_data($simulatedresponse);
    }

    public function format_text($text, $format, $qa, $component, $filearea, $itemid, $clean = false) {
        if ($format == FORMAT_PLAIN) {
            $text = $this->base->format_text($text, $format, $qa, $component, $filearea, $itemid, $clean);
            $format = FORMAT_HTML;
        }
        $text = $this->expand_variables($text);
        return $this->base->format_text($text, $format, $qa, $component, $filearea, $itemid, $clean);
    }

    private function mathml_to_safe($input) {
        $safe = array('«', '»', '¨', '§', '`');
        $mathml = array('<', '>', '"', '&', '\'');
        return str_replace($mathml, $safe, $input);
    }

    public function expand_variables($text) {
        if (isset($this->wirisquestioninstance)) {
            $text = $this->wirisquestioninstance->expandVariables($text);
        }

        if (get_config('qtype_wq', 'filtercodes_compatibility')) {
            $text = $this->filtercodes_compatibility($text);
        }
        if (get_config('qtype_wq', 'mathjax_compatibity')) {
            $text = $this->mathjax_compatibility($text);
        }

        return $text;
    }

    /**
     * If MathType is in client mode and we want to use MathJax to render LaTeX,
     * the MathJax plugin can conflict with standard MathML due to supporting
     * HTML inside formulae. This function replaces MathML special chars with a MathType's
     * safe enconding so MathJax does not interact with it.
     */
    private function mathjax_compatibility($text) {
        return preg_replace_callback(
            '/<math.*?<\/math>/s',
            function ($matches) {
                return $this->mathml_to_safe($matches[0]);
            },
            $text
        );
    }

    private function filtercodes_compatibility($text) {
        $text = str_replace('[{', '[[{', $text);
        $text = str_replace('}]', '}]]', $text);
        return $text;
    }

    public function expand_variables_text($text) {
        if (isset($this->wirisquestioninstance)) {
            $text = $this->wirisquestioninstance->expandVariablesText($text);
        }
        return $text;
    }

    public function expand_variables_mathml($text) {
        if (isset($this->wirisquestioninstance)) {
            $text = $this->wirisquestioninstance->expandVariablesMathML($text);
        }
        return $text;
    }

    public function html_to_text($text, $format) {
        return $this->base->html_to_text($text, $format);
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'response_auxiliar_text') {
            // Response attachments visible if the question has them.
            return true;
        } else {
            return $this->base->check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    /**
     * question_response_answer_comparer interface.
     * **/
    public function compare_response_with_answer(array $response, question_answer $answer) {
        return $this->base->compare_response_with_answer($response, $answer);
    }
    public function get_answers() {
        return $this->base->get_answers();
    }
    /**
     * question_manually_gradable interface
     * **/
    public function is_complete_response(array $response) {
        return $this->base->is_complete_response($response);
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        $baseresponse = $this->base->is_same_response($prevresponse, $newresponse);
        $sqicompare = ((empty($newresponse['_sqi']) && empty($prevresponse['_sqi'])) || (!empty($prevresponse['_sqi']) &&
            !empty($newresponse['_sqi']) && $newresponse['_sqi'] == $prevresponse['_sqi']));
        $auxiliarcompare = ((empty($newresponse['auxiliar_text']) && empty($prevresponse['auxiliar_text'])) ||
            (!empty($prevresponse['auxiliar_text']) &&
                !empty($newresponse['auxiliar_text']) && $newresponse['auxiliar_text'] == $prevresponse['auxiliar_text']));
        return $baseresponse && $sqicompare && $auxiliarcompare;
    }

    public function summarise_response(array $response) {
        $text = $this->base->summarise_response($response);
        $text = $this->expand_variables_text($text);
        return $text;
    }

    public function classify_response(array $response) {
        return $this->base->classify_response($response);
    }
    /**
     * question_automatically_gradable interface
     * **/
    public function is_gradable_response(array $response) {
        return $this->base->is_gradable_response($response);
    }

    public function get_validation_error(array $response) {
        return $this->base->get_validation_error($response);
    }

    public function grade_response(array $response) {
        return $this->base->grade_response($response);
    }

    public function get_hint($hintnumber, question_attempt $qa) {
        return $this->base->get_hint($hintnumber, $qa);
    }

    public function get_right_answer_summary() {
        $text = $this->base->get_right_answer_summary();
        return $this->expand_variables_text($text);
    }
    public function format_hint(question_hint $hint, question_attempt $qa) {
        return $this->format_text(
            $hint->hint,
            $hint->hintformat,
            $qa,
            'question',
            'hint',
            $hint->id
        );
    }
    /**
     * interface question_automatically_gradable_with_countback
     * **/
    public function compute_final_grade($responses, $totaltries) {
        return $this->base->compute_final_grade($responses, $totaltries);
    }
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        return $this->base->make_behaviour($qa, $preferredbehaviour);
    }

    /**
     * Custom interface.
     * **/

    /**
     * @return All the text of the question in a single string so Wiris Quizzes
     * can extract the variable placeholders.
     */
    public function join_all_text() {
        // Question text and general feedback.
        $text = $this->questiontext . ' ' . $this->generalfeedback;
        // Hints.
        foreach ($this->hints as $hint) {
            $text .= ' ' . $hint->hint;
        }

        return $text;
    }

    /**
     * @return String Return all the question text without feedback texts.
     */
    public function join_question_text() {
        $text = $this->questiontext;
        foreach ($this->hints as $hint) {
            $text .= ' ' . $hint->hint;
        }
        return $text;
    }

    /**
     *
     * @return String Return the general feedback text in a single string so Wiris
     * quizzes can extract the variable placeholders.
     */
    public function join_feedback_text() {
        return $this->generalfeedback;
    }

    public function call_wiris_service($request) {
        global $COURSE;
        global $USER;
        global $CFG;

        $builder = com_wiris_quizzes_api_Quizzes::getInstance();
        $metaproperty = ((!empty($COURSE) ? $COURSE->id : '') . '/' . (!empty($question) ? $question->id : ''));

        // Add meta properties.
        $request->addMetaProperty('questionref', $metaproperty);
        $request->addMetaProperty('userref', (!empty($USER) ? $USER->id : ''));
        $request->addMetaProperty('qtype', $this->qtype->name());
        $request->addMetaProperty(
            'wqversion',
            // @codingStandardsIgnoreLine
            $builder->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$VERSION)
        );
        $request->addMetaProperty('moodleversion', explode(' ', $CFG->release)[0]);

        $service = $builder->getQuizzesService();

        $isdebugmodeenabled = get_config('qtype_wq', 'debug_mode_enabled') == '1';
        $islogmodeenabled = get_config('qtype_wq', 'log_server_errors') == '1';

        if ($isdebugmodeenabled) {
            // @codingStandardsIgnoreLine
            print_object($request->serialize());
        }

        try {
            $response = $service->execute($request);
        } catch (Exception $e) {
            global $CFG;

            $a = new stdClass();
            $a->questionname = $this->name;

            $link = null;
            $cmid = optional_param('cmid', null, PARAM_RAW);
            if ($cmid != null) {
                $link = $CFG->wwwroot . '/mod/quiz/view.php?id=' . $cmid;
            }

            if ($isdebugmodeenabled) {
                // @codingStandardsIgnoreLine
                print_object($e);
            }

            if ($islogmodeenabled) {
                // @codingStandardsIgnoreLine
                error_log('WIRISQUIZZES SERVER ERROR --- REQUEST: --- ' . $request->serialize());
            }

            throw new moodle_exception('wirisquestionincorrect', 'qtype_wq', $link, $a, '');
        }

        if ($isdebugmodeenabled) {
            // @codingStandardsIgnoreLine
            print_object($response->serialize());
        }
        return $response;
    }


    public function update_attempt_state_data_for_new_version(
        question_attempt_step $oldstep,
        question_definition $otherversion
    ) {
        return $this->base->update_attempt_state_data_for_new_version($oldstep, $otherversion->base);
    }

    public function validate_can_regrade_with_other_version(question_definition $otherversion): ?string {
        return $this->base->validate_can_regrade_with_other_version($otherversion->base);
    }
}
