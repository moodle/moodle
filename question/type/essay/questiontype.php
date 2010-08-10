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

//////////////////
///   ESSAY   ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class question_essay_qtype extends default_questiontype {

    function name() {
        return 'essay';
    }

    function is_manual_graded() {
        return true;
    }

    function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = true;
        $update = true;
        $answer = $DB->get_record("question_answers", array("question" => $question->id));
        if (!$answer) {
            $answer = new stdClass;
            $answer->question = $question->id;
            $update = false;
        }
        $answer->feedbackformat = $question->feedback['format'];
        $answer->answerformat = $question->feedback['format'];
        $answer->fraction = $question->fraction;
        if ($update) {
            $answer->feedback = file_save_draft_area_files($question->feedback['itemid'], $context->id, 'question', 'answerfeedback', $answer->id, self::$fileoptions, trim($question->feedback['text']));
            $answer->answer = $answer->feedback;
            $DB->update_record("question_answers", $answer);
        } else {
            $answer->feedback = $question->feedback['text'];
            $answer->answer = $answer->feedback;
            $answer->id = $DB->insert_record('question_answers', $answer);
            $answer->feedback = file_save_draft_area_files($question->feedback['itemid'], $context->id, 'question', 'answerfeedback', $answer->id, self::$fileoptions, trim($question->feedback['text']));
            $answer->answer = $answer->feedback;
            $DB->update_record('question_answers', $answer);
        }
        return $result;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $context = $this->get_context_by_category_id($question->category);

        $answers  = &$question->options->answers;
        $readonly = empty($options->readonly) ? '' : 'disabled="disabled"';

        // Only use the rich text editor for the first essay question on a page.

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para    = false;

        $inputname = $question->name_prefix;
        $stranswer = get_string("answer", "quiz").': ';

        /// set question text and media
        $questiontext = format_text($question->questiontext,
                                   $question->questiontextformat,
                                   $formatoptions, $cmoptions->course);

        // feedback handling
        $feedback = '';
        if ($options->feedback && !empty($answers)) {
            foreach ($answers as $answer) {
                $feedback = quiz_rewrite_question_urls($answer->feedback, 'pluginfile.php', $context->id, 'qtype_essay', 'feedback', array($state->attempt, $state->question), $answer->id);
                $feedback = format_text($feedback, $answer->feedbackformat, $formatoptions, $cmoptions->course);
            }
        }

        // get response value
        if (isset($state->responses[''])) {
            $value = $state->responses[''];
        } else {
            $value = "";
        }

        // answer
        if (empty($options->readonly)) {
            // the student needs to type in their answer so print out a text editor
            $answer = print_textarea(can_use_html_editor(), 18, 80, 630, 400,
                    $inputname, $value, $cmoptions->course, true);
        } else {
            // it is read only, so just format the students answer and output it
            $safeformatoptions = new stdClass;
            $safeformatoptions->para = false;
            $answer = format_text($value, FORMAT_MOODLE,
                                  $safeformatoptions, $cmoptions->course);
            $answer = '<div class="answerreview">' . $answer . '</div>';
        }

        include("$CFG->dirroot/question/type/essay/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // All grading takes place in Manual Grading

        $state->responses[''] = clean_param($state->responses[''], PARAM_CLEAN);

        $state->raw_grade = 0;
        $state->penalty = 0;

        return true;
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
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->questiontext = "What is the purpose of life?";
        $form->feedback = "feedback";
        $form->generalfeedback = "General feedback";
        $form->fraction = 0;
        $form->penalty = 0;

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form, $course);
    }

    /**
     * When move the category of questions, the belonging files should be moved as well
     * @param object $question, question information
     * @param object $newcategory, target category information
     */
    function move_files($question, $newcategory) {
        global $DB;
        parent::move_files($question, $newcategory);

        $fs = get_file_storage();
        // process files in answer
        if (!$oldanswers = $DB->get_records('question_answers', array('question' =>  $question->id), 'id ASC')) {
            $oldanswers = array();
        }
        $component = 'question';
        $filearea = 'answerfeedback';
        foreach ($oldanswers as $answer) {
            $files = $fs->get_area_files($question->contextid, $component, $filearea, $answer->id);
            foreach ($files as $storedfile) {
                if (!$storedfile->is_directory()) {
                    $newfile = new object();
                    $newfile->contextid = (int)$newcategory->contextid;
                    $fs->create_file_from_storedfile($newfile, $storedfile);
                    $storedfile->delete();
                }
            }
        }
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        if ($component == 'question' && $filearea == 'answerfeedback') {

            $answerid = reset($args); // itemid is answer id.
            $answers = &$question->options->answers;
            if (isset($state->responses[''])) {
                $response = $state->responses[''];
            } else {
                $response = '';
            }

            return $options->feedback && !empty($response);

        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }
    // Restore method not needed.
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_essay_qtype());
