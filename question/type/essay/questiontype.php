<?php  // $Id$

//////////////////
///   ESSAY   ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class question_essay_qtype extends default_questiontype {
    var $usablebyrandom;

    function question_essay_qtype() {
        $this->usablebyrandom = get_config('qtype_random', 'selectmanual');
    }

    function name() {
        return 'essay';
    }

    function is_manual_graded() {
        return true;
    }

    function is_usable_by_random() {
        return $this->usablebyrandom;
    }

    function save_question_options($question) {
        $result = true;
        $update = true;
        $answer = get_record("question_answers", "question", $question->id);
        if (!$answer) {
            $answer = new stdClass;
            $answer->question = $question->id;
            $update = false;
        }
        $answer->answer   = $question->feedback;
        $answer->feedback = $question->feedback;
        $answer->fraction = $question->fraction;
        if ($update) {
            if (!update_record("question_answers", $answer)) {
                $result = new stdClass;
                $result->error = "Could not update quiz answer!";
            }
        } else {
            if (!$answer->id = insert_record("question_answers", $answer)) {
                $result = new stdClass;
                $result->error = "Could not insert quiz answer!";
            }
        }
        return $result;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
        static $htmleditorused = false;

        $answers       = &$question->options->answers;
        $readonly      = empty($options->readonly) ? '' : 'disabled="disabled"';

        // Only use the rich text editor for the first essay question on a page.
        $usehtmleditor = can_use_html_editor() && !$htmleditorused;

        $formatoptions          = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para    = false;

        $inputname = $question->name_prefix;
        $stranswer = get_string("answer", "quiz").': ';

        /// set question text and media
        $questiontext = format_text($question->questiontext,
                                   $question->questiontextformat,
                                   $formatoptions, $cmoptions->course);

        $image = get_question_image($question);

        // feedback handling
        $feedback = '';
        if ($options->feedback && !empty($answers)) {
            foreach ($answers as $answer) {
                $feedback = format_text($answer->feedback, '', $formatoptions, $cmoptions->course);
            }
        }

        // get response value
        if (isset($state->responses[''])) {
            $value = stripslashes_safe($state->responses['']);
        } else {
            $value = "";
        }

        // answer
        if (empty($options->readonly)) {
            // the student needs to type in their answer so print out a text editor
            $answer = print_textarea($usehtmleditor, 18, 80, 630, 400, $inputname, $value, $cmoptions->course, true);
        } else {
            // it is read only, so just format the students answer and output it
            $safeformatoptions = new stdClass;
            $safeformatoptions->para = false;
            $answer = format_text($value, FORMAT_MOODLE,
                                  $safeformatoptions, $cmoptions->course);
            $answer = '<div class="answerreview">' . $answer . '</div>';
        }

        include("$CFG->dirroot/question/type/essay/display.html");

        if ($usehtmleditor && empty($options->readonly)) {
            use_html_editor($inputname);
            $htmleditorused = true;
        }
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // All grading takes place in Manual Grading

        $state->responses[''] = clean_param($state->responses[''], PARAM_CLEAN);

        $state->raw_grade = 0;
        $state->penalty = 0;

        return true;
    }

    function response_summary($question, $state, $length = 80) {
        $responses = $this->get_actual_response($question, $state);
        $response = reset($responses);
        return shorten_text($response, $length);
    }

    /**
     * Backup the extra information specific to an essay question - over and above
     * what is in the mdl_question table.
     *
     * @param file $bf The backup file to write to.
     * @param object $preferences the blackup options controlling this backup.
     * @param $questionid the id of the question being backed up.
     * @param $level indent level in the backup file - so it can be formatted nicely.
     */
    function backup($bf, $preferences, $questionid, $level = 6) {
        return question_backup_answers($bf, $preferences, $questionid, $level);
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->questiontext = "What is the purpose of life?";
        $form->feedback = "feedback";
        $form->generalfeedback = "General feedback";
        $form->fraction = 0;
        $form->penalty = 0;

        if ($courseid) {
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }

    // Restore method not needed.
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_essay_qtype());
?>
