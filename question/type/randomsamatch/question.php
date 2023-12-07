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
 * Matching question definition class.
 *
 * @package   qtype_randomsamatch
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/match/question.php');

/**
 * Represents a randomsamatch question.
 *
 * @copyright 22013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_question extends qtype_match_question {
    /** @var qtype_randomsamatch_question_loader helper for loading the shortanswer questions. */
    public $questionsloader;

    /** @var int the number of subquestions to randomly create. */
    public $choose;

    /** @var bool whether to include questions from subcategories when making the random selection. */
    public $subcats;

    public function start_attempt(question_attempt_step $step, $variant) {
        $saquestions = $this->questionsloader->load_questions();
        foreach ($saquestions as $wrappedquestion) {
            // Store and save stem text and format.
            $this->stems[$wrappedquestion->id] = $wrappedquestion->questiontext;
            $this->stemformat[$wrappedquestion->id] = $wrappedquestion->questiontextformat;
            $step->set_qt_var('_stem_' . $wrappedquestion->id, $this->stems[$wrappedquestion->id]);
            $step->set_qt_var('_stemformat_' . $wrappedquestion->id, $this->stemformat[$wrappedquestion->id]);

            // Find, store and save right choice id.
            $key = $this->find_right_answer($wrappedquestion);
            $this->right[$wrappedquestion->id] = $key;
            $step->set_qt_var('_right_' . $wrappedquestion->id, $key);
            // No need to save saquestions, it will be saved by parent class in _stemorder.
        }

        // Save all the choices.
        foreach ($this->choices as $key => $answer) {
            $step->set_qt_var('_choice_' . $key, $answer);
        }

        parent::start_attempt($step, $variant);
    }

    /**
     * Find the corresponding choice id of the first correct answer of a shortanswer question.
     * choice is added to the randomsamatch question if it doesn't already exist.
     * @param object $wrappedquestion short answer question.
     * @return int correct choice id.
     */
    public function find_right_answer($wrappedquestion) {
        // We only take into account *one* (the first) correct answer.
        while ($answer = array_shift($wrappedquestion->answers)) {
            if (!question_state::graded_state_for_fraction(
                    $answer->fraction)->is_incorrect()) {
                // Store this answer as a choice, only if this is a new one.
                $key = array_search($answer->answer, $this->choices);
                if ($key === false) {
                    $key = $answer->id;
                    $this->choices[$key] = $answer->answer;
                }
                return $key;
            }
        }
        // We should never get there.
        throw new coding_exception('shortanswerquestionwithoutrightanswer', $wrappedquestion->id);

    }

    public function apply_attempt_state(question_attempt_step $step) {
        $saquestions = explode(',', $step->get_qt_var('_stemorder'));
        foreach ($saquestions as $questionid) {
            $this->stems[$questionid] = $step->get_qt_var('_stem_' . $questionid);
            $this->stemformat[$questionid] = $step->get_qt_var('_stemformat_' . $questionid);
            $key = $step->get_qt_var('_right_' . $questionid);
            $this->right[$questionid] = $key;
            $this->choices[$key] = $step->get_qt_var('_choice_' . $key);
        }
        parent::apply_attempt_state($step);
    }
}

/**
 * This class is responsible for loading the questions that a question needs from the database.
 *
 * @copyright  2013 Jean-Michel vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_question_loader {
    /** @var array hold available shortanswers questionid to choose from. */
    protected $availablequestions;
    /** @var int how many questions to load. */
    protected $choose;

    /**
     * Constructor
     * @param array $availablequestions array of available question ids.
     * @param int $choose how many questions to load.
     */
    public function __construct($availablequestions, $choose) {
        $this->availablequestions = $availablequestions;
        $this->choose = $choose;
    }

    /**
     * Choose and load the desired number of questions.
     * @return array of short answer questions.
     */
    public function load_questions() {
        if ($this->choose > count($this->availablequestions)) {
            throw new coding_exception('notenoughtshortanswerquestions');
        }

        $questionids = draw_rand_array($this->availablequestions, $this->choose);
        $questions = array();
        foreach ($questionids as $questionid) {
            $questions[] = question_bank::load_question($questionid);
        }
        return $questions;
    }
}
