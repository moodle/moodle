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
 * Question type class for the embedded element in question text question types.
 *
 * @package    qtype_gapselect
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');


/**
 * The embedded element in question text question type class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_gapselect_base extends question_type {
    /**
     * Choices are stored in the question_answers table, and any options need to
     * be put into the feedback field somehow. This method is responsible for
     * converting all the options to a single string for this purpose. It is used
     * by {@link save_question_options()}.
     * @param array $choice the form data relating to this choice.
     * @return string ready to store in the database.
     */
    protected abstract function choice_options_to_feedback($choice);

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;

        // This question type needs the choices to be consecutively numbered, but
        // there is no reason why the question author should have done that,
        // so renumber if necessary.
        // Insert all the new answers.
        $nonblankchoices = [];
        $questiontext = $question->questiontext;
        $newkey = 0;
        foreach ($question->choices as $key => $choice) {
            if (trim($choice['answer']) == '') {
                continue;
            }

            $nonblankchoices[] = $choice;
            if ($newkey != $key) {
                // Safe to do this in this order, because we will always be replacing
                // a bigger number with a smaller number that is not present.
                // Numbers in the question text always one bigger than the array index.
                $questiontext = str_replace('[[' . ($key + 1) . ']]', '[[' . ($newkey + 1) . ']]',
                        $questiontext);
            }
            $newkey += 1;
        }
        $question->choices = $nonblankchoices;
        if ($questiontext !== $question->questiontext) {
            $DB->set_field('question', 'questiontext', $questiontext,
                    ['id' => $question->id]);
            $question->questiontext = $questiontext;
        }

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Insert all the new answers.
        foreach ($question->choices as $key => $choice) {

            // Answer guaranteed to be non-blank. See above.

            $feedback = $this->choice_options_to_feedback($choice);

            if ($answer = array_shift($oldanswers)) {
                $answer->answer = trim($choice['answer']);
                $answer->feedback = $feedback;
                $DB->update_record('question_answers', $answer);

            } else {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = $choice['answer'];
                $answer->answerformat = FORMAT_HTML;
                $answer->fraction = 0;
                $answer->feedback = $feedback;
                $answer->feedbackformat = 0;
                $DB->insert_record('question_answers', $answer);
            }
        }

        // Delete old answer records.
        foreach ($oldanswers as $oa) {
            $DB->delete_records('question_answers', array('id' => $oa->id));
        }

        $options = $DB->get_record('question_' . $this->name(),
                array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('question_' . $this->name(), $options);
        }

        $options->shuffleanswers = !empty($question->shuffleanswers);
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_' . $this->name(), $options);

        $this->save_hints($question, true);
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_'.$this->name(),
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_'.$this->name(), array('questionid' => $questionid));
        return parent::delete_question($questionid, $contextid);
    }

    /**
     * Used by {@link initialise_question_instance()} to set up the choice-specific data.
     * @param object $choicedata as loaded from the question_answers table.
     * @return object an appropriate object for representing the choice.
     */
    protected abstract function make_choice($choicedata);

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $question->shufflechoices = $questiondata->options->shuffleanswers;

        $this->initialise_combined_feedback($question, $questiondata, true);

        $question->choices = array();
        $choiceindexmap = array();

        // Store the choices in arrays by group.
        $i = 1;
        foreach ($questiondata->options->answers as $choicedata) {
            $choice = $this->make_choice($choicedata);

            if (array_key_exists($choice->choice_group(), $question->choices)) {
                $question->choices[$choice->choice_group()][] = $choice;
            } else {
                $question->choices[$choice->choice_group()][1] = $choice;
            }

            end($question->choices[$choice->choice_group()]);
            $choiceindexmap[$i] = array($choice->choice_group(),
                    key($question->choices[$choice->choice_group()]));
            $i += 1;
        }

        $question->places = array();
        $question->textfragments = array();
        $question->rightchoices = array();
        // Break up the question text, and store the fragments, places and right answers.

        $bits = preg_split('/\[\[(\d+)]]/', $question->questiontext,
                -1, PREG_SPLIT_DELIM_CAPTURE);
        $question->textfragments[0] = array_shift($bits);
        $i = 1;

        while (!empty($bits)) {
            $choice = array_shift($bits);

            list($group, $choiceindex) = $choiceindexmap[$choice];
            $question->places[$i] = $group;
            $question->rightchoices[$i] = $choiceindex;

            $question->textfragments[$i] = array_shift($bits);
            $i += 1;
        }
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    public function get_random_guess_score($questiondata) {
        $question = $this->make_question($questiondata);
        return $question->get_random_guess_score();
    }

    /**
     * This function should reverse {@link choice_options_to_feedback()}.
     * @param string $feedback the data loaded from the database.
     * @return array the choice options.
     */
    protected abstract function feedback_to_choice_options($feedback);

    /**
     * This method gets the choices (answers)
     * in a 2 dimentional array.
     *
     * @param object $question
     * @return array of groups
     */
    protected function get_array_of_choices($question) {
        $subquestions = $question->options->answers;
        $count = 0;
        foreach ($subquestions as $key => $subquestion) {
            $answers[$count]['id'] = $subquestion->id;
            $answers[$count]['answer'] = $subquestion->answer;
            $answers[$count]['fraction'] = $subquestion->fraction;
            $answers[$count] += $this->feedback_to_choice_options($subquestion->feedback);
            $answers[$count]['choice'] = $count + 1;
            ++$count;
        }
        return $answers;
    }

    /**
     * This method gets the choices (answers) and sort them by groups
     * in a 2 dimentional array.
     *
     * @param object $question
     * @param object $state Question state object
     * @return array of groups
     */
    protected function get_array_of_groups($question, $state) {
        $answers = $this->get_array_of_choices($question);
        $arr = array();
        for ($group = 1; $group < count($answers); $group++) {
            $players = $this->get_group_of_players($question, $state, $answers, $group);
            if ($players) {
                $arr[$group] = $players;
            }
        }
        return $arr;
    }

    /**
     * This method gets the correct answers in a 2 dimentional array.
     *
     * @param object $question
     * @return array of groups
     */
    protected function get_correct_answers($question) {
        $arrayofchoices = $this->get_array_of_choices($question);
        $arrayofplaceholdeers = $this->get_array_of_placeholders($question);

        $correctplayers = array();
        foreach ($arrayofplaceholdeers as $ph) {
            foreach ($arrayofchoices as $key => $choice) {
                if ($key + 1 == $ph) {
                    $correctplayers[] = $choice;
                }
            }
        }
        return $correctplayers;
    }

    /**
     * Return the list of groups used in a question.
     * @param stdClass $question the question data.
     * @return array the groups used, or false if an error occurs.
     */
    protected function get_array_of_placeholders($question) {
        $qtext = $question->questiontext;
        $error = '<b> ERROR</b>: Please check the form for this question. ';
        if (!$qtext) {
            echo $error . 'The question text is empty!';
            return false;
        }

        // Get the slots.
        $slots = $this->getEmbeddedTextArray($question);

        if (!$slots) {
            echo $error . 'The question text is not in the correct format!';
            return false;
        }

        $output = array();
        foreach ($slots as $slot) {
            $output[] = substr($slot, 2, strlen($slot) - 4); // 2 is for '[[' and 4 is for '[[]]'.
        }
        return $output;
    }

    protected function get_group_of_players($question, $state, $subquestions, $group) {
        $goupofanswers = array();
        foreach ($subquestions as $key => $subquestion) {
            if ($subquestion[$this->choice_group_key()] == $group) {
                $goupofanswers[] = $subquestion;
            }
        }

        // Shuffle answers within this group.
        if ($question->options->shuffleanswers == 1) {
            shuffle($goupofanswers);
        }
        return $goupofanswers;
    }

    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('shuffleanswers', $fromform->shuffleanswers ?? 0);
    }

    public function get_possible_responses($questiondata) {
        $question = $this->make_question($questiondata);

        $parts = array();
        foreach ($question->places as $place => $group) {
            $choices = array();

            foreach ($question->choices[$group] as $i => $choice) {
                $choices[$i] = new question_possible_response(
                        html_to_text($choice->text, 0, false),
                        ($question->rightchoices[$place] == $i) / count($question->places));
            }
            $choices[null] = question_possible_response::no_response();

            $parts[$place] = $choices;
        }

        return $parts;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}
