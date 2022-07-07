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
 * Blackboard V5 and V6 question importer.
 *
 * @package    qformat_blackboard_six
 * @copyright  2003 Scott Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/xmlize.php');

/**
 * Blackboard pool question importer class.
 *
 * @package    qformat_blackboard_six
 * @copyright  2003 Scott Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard_six_pool extends qformat_blackboard_six_base {
    /**
     * @var bool Is the current question's question text escaped HTML
     * (true for most if not all Blackboard files).
     */
    public $ishtml = true;

    /**
     * Parse the xml document into an array of questions
     *
     * This *could* burn memory - but it won't happen that much
     * so fingers crossed!
     *
     * @param array $text array of lines from the input file.
     * @return array (of objects) questions objects.
     */
    protected function readquestions($text) {

        // This converts xml to big nasty data structure,
        // the 0 means keep white space as it is.
        try {
            $xml = xmlize($text, 0, 'UTF-8', true);
        } catch (xml_format_exception $e) {
            $this->error($e->getMessage(), '');
            return false;
        }

        $questions = array();

        $this->process_category($xml, $questions);

        $this->process_tf($xml, $questions);
        $this->process_mc($xml, $questions);
        $this->process_ma($xml, $questions);
        $this->process_fib($xml, $questions);
        $this->process_matching($xml, $questions);
        $this->process_essay($xml, $questions);

        return $questions;
    }

    /**
     * Do question import processing common to every qtype.
     *
     * @param array $questiondata the xml tree related to the current question
     * @return object initialized question object.
     */
    public function process_common($questiondata) {

        // This routine initialises the question object.
        $question = $this->defaultquestion();

        // Determine if the question is already escaped html.
        $this->ishtml = $this->getpath($questiondata,
                array('#', 'BODY', 0, '#', 'FLAGS', 0, '#', 'ISHTML', 0, '@', 'value'),
                false, false);

        // Put questiontext in question object.
        $text = $this->getpath($questiondata,
                array('#', 'BODY', 0, '#', 'TEXT', 0, '#'),
                '', true, get_string('importnotext', 'qformat_blackboard_six'));

        $questiontext = $this->cleaned_text_field($text);
        $question->questiontext = $questiontext['text'];
        $question->questiontextformat = $questiontext['format']; // Needed because add_blank_combined_feedback uses it.
        if (isset($questiontext['itemid'])) {
            $question->questiontextitemid = $questiontext['itemid'];
        }

        // Put name in question object. We must ensure it is not empty and it is less than 250 chars.
        $id = $this->getpath($questiondata, array('@', 'id'), '',  true);
        $question->name = $this->create_default_question_name($question->questiontext,
                get_string('defaultname', 'qformat_blackboard_six' , $id));

        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_HTML;
        $question->generalfeedbackfiles = array();

        // TODO : read the mark from the POOL TITLE QUESTIONLIST section.
        $question->defaultmark = 1;
        return $question;
    }

    /**
     * Add a category question entry based on the pool file title
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_category($xml, &$questions) {
        $title = $this->getpath($xml, array('POOL', '#', 'TITLE', 0, '@', 'value'), '', true);

        $dummyquestion = new stdClass();
        $dummyquestion->qtype = 'category';
        $dummyquestion->category = $this->cleaninput($this->clean_question_name($title));

        $questions[] = $dummyquestion;
    }

    /**
     * Process Essay Questions
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_essay($xml, &$questions) {

        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_ESSAY'), false, false)) {
            $essayquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_ESSAY'), false, false);
        } else {
            return;
        }

        foreach ($essayquestions as $thisquestion) {

            $question = $this->process_common($thisquestion);

            $question->qtype = 'essay';

            $question->answer = '';
            $answer = $this->getpath($thisquestion,
                    array('#', 'ANSWER', 0, '#', 'TEXT', 0, '#'), '', true);
            $question->graderinfo = $this->cleaned_text_field($answer);
            $question->responsetemplate = $this->text_field('');
            $question->feedback = '';
            $question->responseformat = 'editor';
            $question->responserequired = 1;
            $question->responsefieldlines = 15;
            $question->attachments = 0;
            $question->attachmentsrequired = 0;
            $question->fraction = 0;

            $questions[] = $question;
        }
    }

    /**
     * Process True / False Questions
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_tf($xml, &$questions) {

        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_TRUEFALSE'), false, false)) {
            $tfquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_TRUEFALSE'), false, false);
        } else {
            return;
        }

        foreach ($tfquestions as $thisquestion) {

            $question = $this->process_common($thisquestion);

            $question->qtype = 'truefalse';
            $question->single = 1; // Only one answer is allowed.

            $choices = $this->getpath($thisquestion, array('#', 'ANSWER'), array(), false);

            $correctanswer = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'CORRECTANSWER', 0, '@', 'answer_id'),
                    '', true);

            // First choice is true, second is false.
            $id = $this->getpath($choices[0], array('@', 'id'), '', true);
            $correctfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_CORRECT', 0, '#'),
                    '', true);
            $incorrectfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_INCORRECT', 0, '#'),
                    '', true);
            if (strcmp($id,  $correctanswer) == 0) {  // True is correct.
                $question->answer = 1;
                $question->feedbacktrue = $this->cleaned_text_field($correctfeedback);
                $question->feedbackfalse = $this->cleaned_text_field($incorrectfeedback);
            } else {  // False is correct.
                $question->answer = 0;
                $question->feedbacktrue = $this->cleaned_text_field($incorrectfeedback);
                $question->feedbackfalse = $this->cleaned_text_field($correctfeedback);
            }
            $question->correctanswer = $question->answer;
            $questions[] = $question;
        }
    }

    /**
     * Process Multiple Choice Questions with single answer
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_mc($xml, &$questions) {

        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_MULTIPLECHOICE'), false, false)) {
            $mcquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_MULTIPLECHOICE'), false, false);
        } else {
            return;
        }

        foreach ($mcquestions as $thisquestion) {

            $question = $this->process_common($thisquestion);

            $correctfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_CORRECT', 0, '#'),
                    '', true);
            $incorrectfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_INCORRECT', 0, '#'),
                    '', true);
            $question->correctfeedback = $this->cleaned_text_field($correctfeedback);
            $question->partiallycorrectfeedback = $this->text_field('');
            $question->incorrectfeedback = $this->cleaned_text_field($incorrectfeedback);

            $question->qtype = 'multichoice';
            $question->single = 1; // Only one answer is allowed.

            $choices = $this->getpath($thisquestion, array('#', 'ANSWER'), false, false);
            $correctanswerid = $this->getpath($thisquestion,
                        array('#', 'GRADABLE', 0, '#', 'CORRECTANSWER', 0, '@', 'answer_id'),
                        '', true);
            foreach ($choices as $choice) {
                $choicetext = $this->getpath($choice, array('#', 'TEXT', 0, '#'), '', true);
                // Put this choice in the question object.
                $question->answer[] = $this->cleaned_text_field($choicetext);

                $choiceid = $this->getpath($choice, array('@', 'id'), '', true);
                // If choice is the right answer, give 100% mark, otherwise give 0%.
                if (strcmp ($choiceid, $correctanswerid) == 0) {
                    $question->fraction[] = 1;
                } else {
                    $question->fraction[] = 0;
                }
                // There is never feedback specific to each choice.
                $question->feedback[] = $this->text_field('');
            }
            $questions[] = $question;
        }
    }

    /**
     * Process Multiple Choice Questions With Multiple Answers
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_ma($xml, &$questions) {
        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_MULTIPLEANSWER'), false, false)) {
            $maquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_MULTIPLEANSWER'), false, false);
        } else {
            return;
        }

        foreach ($maquestions as $thisquestion) {
            $question = $this->process_common($thisquestion);

            $correctfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_CORRECT', 0, '#'),
                    '', true);
            $incorrectfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_INCORRECT', 0, '#'),
                    '', true);
            $question->correctfeedback = $this->cleaned_text_field($correctfeedback);
            // As there is no partially correct feedback we use incorrect one.
            $question->partiallycorrectfeedback = $this->cleaned_text_field($incorrectfeedback);
            $question->incorrectfeedback = $this->cleaned_text_field($incorrectfeedback);

            $question->qtype = 'multichoice';
            $question->defaultmark = 1;
            $question->single = 0; // More than one answers allowed.

            $choices = $this->getpath($thisquestion, array('#', 'ANSWER'), false, false);
            $correctanswerids = array();
            foreach ($this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'CORRECTANSWER'), false, false) as $correctanswer) {
                if ($correctanswer) {
                    $correctanswerids[] = $this->getpath($correctanswer,
                            array('@', 'answer_id'),
                            '', true);
                }
            }
            $fraction = 1 / count($correctanswerids);

            foreach ($choices as $choice) {
                $choicetext = $this->getpath($choice, array('#', 'TEXT', 0, '#'), '', true);
                // Put this choice in the question object.
                $question->answer[] = $this->cleaned_text_field($choicetext);

                $choiceid = $this->getpath($choice, array('@', 'id'), '', true);

                $iscorrect = in_array($choiceid, $correctanswerids);

                if ($iscorrect) {
                    $question->fraction[] = $fraction;
                } else {
                    $question->fraction[] = 0;
                }
                // There is never feedback specific to each choice.
                $question->feedback[] = $this->text_field('');
            }
            $questions[] = $question;
        }
    }

    /**
     * Process Fill in the Blank Questions
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_fib($xml, &$questions) {
        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_FILLINBLANK'), false, false)) {
            $fibquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_FILLINBLANK'), false, false);
        } else {
            return;
        }

        foreach ($fibquestions as $thisquestion) {

            $question = $this->process_common($thisquestion);

            $question->qtype = 'shortanswer';
            $question->usecase = 0; // Ignore case.

            $correctfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_CORRECT', 0, '#'),
                    '', true);
            $incorrectfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_INCORRECT', 0, '#'),
                    '', true);
            $answers = $this->getpath($thisquestion, array('#', 'ANSWER'), false, false);
            foreach ($answers as $answer) {
                $question->answer[] = $this->getpath($answer,
                        array('#', 'TEXT', 0, '#'), '', true);
                $question->fraction[] = 1;
                $question->feedback[] = $this->cleaned_text_field($correctfeedback);
            }
            $question->answer[] = '*';
            $question->fraction[] = 0;
            $question->feedback[] = $this->cleaned_text_field($incorrectfeedback);

            $questions[] = $question;
        }
    }

    /**
     * Process Matching Questions
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_matching($xml, &$questions) {
        if ($this->getpath($xml, array('POOL', '#', 'QUESTION_MATCH'), false, false)) {
            $matchquestions = $this->getpath($xml,
                    array('POOL', '#', 'QUESTION_MATCH'), false, false);
        } else {
            return;
        }
        // Blackboard questions can't be imported in core Moodle without a loss in data,
        // as core match question don't allow HTML in subanswers. The contributed ddmatch
        // question type support HTML in subanswers.
        // The ddmatch question type is not part of core, so we need to check if it is defined.
        $ddmatchisinstalled = question_bank::is_qtype_installed('ddmatch');

        foreach ($matchquestions as $thisquestion) {

            $question = $this->process_common($thisquestion);
            if ($ddmatchisinstalled) {
                $question->qtype = 'ddmatch';
            } else {
                $question->qtype = 'match';
            }

            $correctfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_CORRECT', 0, '#'),
                    '', true);
            $incorrectfeedback = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'FEEDBACK_WHEN_INCORRECT', 0, '#'),
                    '', true);
            $question->correctfeedback = $this->cleaned_text_field($correctfeedback);
            // As there is no partially correct feedback we use incorrect one.
            $question->partiallycorrectfeedback = $this->cleaned_text_field($incorrectfeedback);
            $question->incorrectfeedback = $this->cleaned_text_field($incorrectfeedback);

            $choices = $this->getpath($thisquestion,
                    array('#', 'CHOICE'), false, false); // Blackboard "choices" are Moodle subanswers.
            $answers = $this->getpath($thisquestion,
                    array('#', 'ANSWER'), false, false); // Blackboard "answers" are Moodle subquestions.
            $correctanswers = $this->getpath($thisquestion,
                    array('#', 'GRADABLE', 0, '#', 'CORRECTANSWER'), false, false); // Mapping between choices and answers.
            $mappings = array();
            foreach ($correctanswers as $correctanswer) {
                if ($correctanswer) {
                    $correctchoiceid = $this->getpath($correctanswer,
                                array('@', 'choice_id'), '', true);
                    $correctanswerid = $this->getpath($correctanswer,
                            array('@', 'answer_id'),
                            '', true);
                    $mappings[$correctanswerid] = $correctchoiceid;
                }
            }

            foreach ($choices as $choice) {
                if ($ddmatchisinstalled) {
                    $choicetext = $this->cleaned_text_field($this->getpath($choice,
                            array('#', 'TEXT', 0, '#'), '', true));
                } else {
                    $choicetext = trim(strip_tags($this->getpath($choice,
                            array('#', 'TEXT', 0, '#'), '', true)));
                }

                if ($choicetext != '') { // Only import non empty subanswers.
                    $subquestion = '';
                    $choiceid = $this->getpath($choice,
                            array('@', 'id'), '', true);
                    $fiber = array_search($choiceid, $mappings);
                    $fiber = array_keys ($mappings, $choiceid);
                    foreach ($fiber as $correctanswerid) {
                        // We have found a correspondance for this choice so we need to take the associated answer.
                        foreach ($answers as $answer) {
                            $currentanswerid = $this->getpath($answer,
                                    array('@', 'id'), '', true);
                            if (strcmp ($currentanswerid, $correctanswerid) == 0) {
                                $subquestion = $this->getpath($answer,
                                        array('#', 'TEXT', 0, '#'), '', true);
                                break;
                            }
                        }
                        $question->subquestions[] = $this->cleaned_text_field($subquestion);
                        $question->subanswers[] = $choicetext;
                    }

                    if ($subquestion == '') { // Then in this case, $choice is a distractor.
                        $question->subquestions[] = $this->text_field('');
                        $question->subanswers[] = $choicetext;
                    }
                }
            }

            // Verify that this matching question has enough subquestions and subanswers.
            $subquestioncount = 0;
            $subanswercount = 0;
            $subanswers = $question->subanswers;
            foreach ($question->subquestions as $key => $subquestion) {
                $subquestion = $subquestion['text'];
                $subanswer = $subanswers[$key];
                if ($subquestion != '') {
                    $subquestioncount++;
                }
                $subanswercount++;
            }
            if ($subquestioncount < 2 || $subanswercount < 3) {
                    $this->error(get_string('notenoughtsubans', 'qformat_blackboard_six', $question->questiontext));
            } else {
                $questions[] = $question;
            }

        }
    }
}
