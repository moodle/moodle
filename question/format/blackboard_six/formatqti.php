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
 * @copyright  2005 Michael Penney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/xmlize.php');

/**
 * Blackboard 6.0 question importer.
 *
 * @copyright  2005 Michael Penney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard_six_qti extends qformat_blackboard_six_base {
    /**
     * Parse the xml document into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
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

        // Treat the assessment title as a category title.
        $this->process_category($xml, $questions);

        // First step : we are only interested in the <item> tags.
        $rawquestions = $this->getpath($xml,
                array('questestinterop', '#', 'assessment', 0, '#', 'section', 0, '#', 'item'),
                array(), false);
        // Each <item> tag contains data related to a single question.
        foreach ($rawquestions as $quest) {
            // Second step : parse each question data into the intermediate
            // rawquestion structure array.
            // Warning : rawquestions are not Moodle questions.
            $question = $this->create_raw_question($quest);
            // Third step : convert a rawquestion into a Moodle question.
            switch($question->qtype) {
                case "Matching":
                    $this->process_matching($question, $questions);
                    break;
                case "Multiple Choice":
                    $this->process_mc($question, $questions);
                    break;
                case "Essay":
                    $this->process_essay($question, $questions);
                    break;
                case "Multiple Answer":
                    $this->process_ma($question, $questions);
                    break;
                case "True/False":
                    $this->process_tf($question, $questions);
                    break;
                case 'Fill in the Blank':
                    $this->process_fblank($question, $questions);
                    break;
                case 'Short Response':
                    $this->process_essay($question, $questions);
                    break;
                default:
                    $this->error(get_string('unknownorunhandledtype', 'question', $question->qtype));
                    break;
            }
        }
        return $questions;
    }

    /**
     * Creates a cleaner object to deal with for processing into Moodle.
     * The object returned is NOT a moodle question object.
     * @param array $quest XML <item> question  data
     * @return object rawquestion
     */
    public function create_raw_question($quest) {

        $rawquestion = new stdClass();
        $rawquestion->qtype = $this->getpath($quest,
                array('#', 'itemmetadata', 0, '#', 'bbmd_questiontype', 0, '#'),
                '', true);
        $rawquestion->id = $this->getpath($quest,
                array('#', 'itemmetadata', 0, '#', 'bbmd_asi_object_id', 0, '#'),
                '', true);
        $presentation = new stdClass();
        $presentation->blocks = $this->getpath($quest,
                array('#', 'presentation', 0, '#', 'flow', 0, '#', 'flow'),
                array(), false);

        foreach ($presentation->blocks as $pblock) {
            $block = new stdClass();
            $block->type = $this->getpath($pblock,
                    array('@', 'class'),
                    '', true);

            switch($block->type) {
                case 'QUESTION_BLOCK':
                    $subblocks = $this->getpath($pblock,
                            array('#', 'flow'),
                            array(), false);
                    foreach ($subblocks as $sblock) {
                        $this->process_block($sblock, $block);
                    }
                    break;

                case 'RESPONSE_BLOCK':
                    $choices = null;
                    switch($rawquestion->qtype) {
                        case 'Matching':
                            $bbsubquestions = $this->getpath($pblock,
                                    array('#', 'flow'),
                                    array(), false);
                            foreach ($bbsubquestions as $bbsubquestion) {
                                $subquestion = new stdClass();
                                $subquestion->ident = $this->getpath($bbsubquestion,
                                        array('#', 'response_lid', 0, '@', 'ident'),
                                        '', true);
                                $this->process_block($this->getpath($bbsubquestion,
                                        array('#', 'flow', 0),
                                        false, false), $subquestion);
                                $bbchoices = $this->getpath($bbsubquestion,
                                        array('#', 'response_lid', 0, '#', 'render_choice', 0,
                                        '#', 'flow_label', 0, '#', 'response_label'),
                                        array(), false);
                                $choices = array();
                                $this->process_choices($bbchoices, $choices);
                                $subquestion->choices = $choices;
                                if (!isset($block->subquestions)) {
                                    $block->subquestions = array();
                                }
                                $block->subquestions[] = $subquestion;
                            }
                            break;
                        case 'Multiple Answer':
                            $bbchoices = $this->getpath($pblock,
                                    array('#', 'response_lid', 0, '#', 'render_choice', 0, '#', 'flow_label'),
                                    array(), false);
                            $choices = array();
                            $this->process_choices($bbchoices, $choices);
                            $block->choices = $choices;
                            break;
                        case 'Essay':
                            // Doesn't apply since the user responds with text input.
                            break;
                        case 'Multiple Choice':
                            $mcchoices = $this->getpath($pblock,
                                    array('#', 'response_lid', 0, '#', 'render_choice', 0, '#', 'flow_label'),
                                    array(), false);
                            foreach ($mcchoices as $mcchoice) {
                                $choices = new stdClass();
                                $choices = $this->process_block($mcchoice, $choices);
                                $block->choices[] = $choices;
                            }
                            break;
                        case 'Short Response':
                            // Do nothing?
                            break;
                        case 'Fill in the Blank':
                            // Do nothing?
                            break;
                        default:
                            $bbchoices = $this->getpath($pblock,
                                    array('#', 'response_lid', 0, '#', 'render_choice', 0, '#',
                                    'flow_label', 0, '#', 'response_label'),
                                    array(), false);
                            $choices = array();
                            $this->process_choices($bbchoices, $choices);
                            $block->choices = $choices;
                    }
                    break;
                case 'RIGHT_MATCH_BLOCK':
                    $matchinganswerset = $this->getpath($pblock,
                            array('#', 'flow'),
                            false, false);

                    $answerset = array();
                    foreach ($matchinganswerset as $answer) {
                        $bbanswer = new stdClass;
                        $bbanswer->text = $this->getpath($answer,
                                array('#', 'flow', 0, '#', 'material', 0, '#', 'mat_extension',
                                0, '#', 'mat_formattedtext', 0, '#'),
                                false, false);
                        $answerset[] = $bbanswer;
                    }
                    $block->matchinganswerset = $answerset;
                    break;
                default:
                    $this->error(get_string('unhandledpresblock', 'qformat_blackboard_six'));
                    break;
            }
            $rawquestion->{$block->type} = $block;
        }

        // Determine response processing.
        // There is a section called 'outcomes' that I don't know what to do with.
        $resprocessing = $this->getpath($quest,
                array('#', 'resprocessing'),
                array(), false);

        $respconditions = $this->getpath($resprocessing[0],
                array('#', 'respcondition'),
                array(), false);
        $responses = array();
        if ($rawquestion->qtype == 'Matching') {
            $this->process_matching_responses($respconditions, $responses);
        } else {
            $this->process_responses($respconditions, $responses);
        }
        $rawquestion->responses = $responses;
        $feedbackset = $this->getpath($quest,
                array('#', 'itemfeedback'),
                array(), false);

        $feedbacks = array();
        $this->process_feedback($feedbackset, $feedbacks);
        $rawquestion->feedback = $feedbacks;
        return $rawquestion;
    }

    /**
     * Helper function to process an XML block into an object.
     * Can call himself recursively if necessary to parse this branch of the XML tree.
     * @param array $curblock XML block to parse
     * @param object $block block already parsed so far
     * @return object $block parsed
     */
    public function process_block($curblock, $block) {

        $curtype = $this->getpath($curblock,
                array('@', 'class'),
                '', true);

        switch($curtype) {
            case 'FORMATTED_TEXT_BLOCK':
                $text = $this->getpath($curblock,
                        array('#', 'material', 0, '#', 'mat_extension', 0, '#', 'mat_formattedtext', 0, '#'),
                        '', true);
                $block->text = $this->strip_applet_tags_get_mathml($text);
                break;
            case 'FILE_BLOCK':
                $block->filename = $this->getpath($curblock,
                        array('#', 'material', 0, '#'),
                        '', true);
                if ($block->filename != '') {
                    // TODO : determine what to do with the file's content.
                    $this->error(get_string('filenothandled', 'qformat_blackboard_six', $block->filename));
                }
                break;
            case 'Block':
                if ($this->getpath($curblock,
                        array('#', 'material', 0, '#', 'mattext'),
                        false, false)) {
                    $block->text = $this->getpath($curblock,
                            array('#', 'material', 0, '#', 'mattext', 0, '#'),
                            '', true);
                } else if ($this->getpath($curblock,
                        array('#', 'material', 0, '#', 'mat_extension', 0, '#', 'mat_formattedtext'),
                        false, false)) {
                    $block->text = $this->getpath($curblock,
                            array('#', 'material', 0, '#', 'mat_extension', 0, '#', 'mat_formattedtext', 0, '#'),
                            '', true);
                } else if ($this->getpath($curblock,
                        array('#', 'response_label'),
                        false, false)) {
                    // This is a response label block.
                    $subblocks = $this->getpath($curblock,
                            array('#', 'response_label', 0),
                            array(), false);
                    if (!isset($block->ident)) {

                        if ($this->getpath($subblocks,
                                array('@', 'ident'), '', true)) {
                            $block->ident = $this->getpath($subblocks,
                                array('@', 'ident'), '', true);
                        }
                    }
                    foreach ($this->getpath($subblocks,
                            array('#', 'flow_mat'), array(), false) as $subblock) {
                        $this->process_block($subblock, $block);
                    }
                } else {
                    if ($this->getpath($curblock,
                                array('#', 'flow_mat'), false, false)
                            || $this->getpath($curblock,
                                array('#', 'flow'), false, false)) {
                        if ($this->getpath($curblock,
                                array('#', 'flow_mat'), false, false)) {
                            $subblocks = $this->getpath($curblock,
                                    array('#', 'flow_mat'), array(), false);
                        } else if ($this->getpath($curblock,
                                array('#', 'flow'), false, false)) {
                            $subblocks = $this->getpath($curblock,
                                    array('#', 'flow'), array(), false);
                        }
                        foreach ($subblocks as $sblock) {
                            // This will recursively grab the sub blocks which should be of one of the other types.
                            $this->process_block($sblock, $block);
                        }
                    }
                }
                break;
            case 'LINK_BLOCK':
                // Not sure how this should be included?
                $link = $this->getpath($curblock,
                            array('#', 'material', 0, '#', 'mattext', 0, '@', 'uri'), '', true);
                if (!empty($link)) {
                    $block->link = $link;
                } else {
                    $block->link = '';
                }
                break;
        }
        return $block;
    }

    /**
     * Preprocess XML blocks containing data for questions' choices.
     * Called by {@link create_raw_question()}
     * for matching, multichoice and fill in the blank questions.
     * @param array $bbchoices XML block to parse
     * @param array $choices array of choices suitable for a rawquestion.
     */
    protected function process_choices($bbchoices, &$choices) {
        foreach ($bbchoices as $choice) {
            if ($this->getpath($choice,
                    array('@', 'ident'), '', true)) {
                $curchoice = $this->getpath($choice,
                        array('@', 'ident'), '', true);
            } else { // For multiple answers.
                $curchoice = $this->getpath($choice,
                         array('#', 'response_label', 0), array(), false);
            }
            if ($this->getpath($choice,
                    array('#', 'flow_mat', 0), false, false)) { // For multiple answers.
                $curblock = $this->getpath($choice,
                    array('#', 'flow_mat', 0), false, false);
                // Reset $curchoice to new stdClass because process_block is expecting an object
                // for the second argument and not a string,
                // which is what is was set as originally - CT 8/7/06.
                $curchoice = new stdClass();
                $this->process_block($curblock, $curchoice);
            } else if ($this->getpath($choice,
                    array('#', 'response_label'), false, false)) {
                // Reset $curchoice to new stdClass because process_block is expecting an object
                // for the second argument and not a string,
                // which is what is was set as originally - CT 8/7/06.
                $curchoice = new stdClass();
                $this->process_block($choice, $curchoice);
            }
            $choices[] = $curchoice;
        }
    }

    /**
     * Preprocess XML blocks containing data for subanswers
     * Called by {@link create_raw_question()}
     * for matching questions only.
     * @param array $bbresponses XML block to parse
     * @param array $responses array of responses suitable for a matching rawquestion.
     */
    protected function process_matching_responses($bbresponses, &$responses) {
        foreach ($bbresponses as $bbresponse) {
            $response = new stdClass;
            if ($this->getpath($bbresponse,
                    array('#', 'conditionvar', 0, '#', 'varequal'), false, false)) {
                $response->correct = $this->getpath($bbresponse,
                        array('#', 'conditionvar', 0, '#', 'varequal', 0, '#'), '', true);
                $response->ident = $this->getpath($bbresponse,
                        array('#', 'conditionvar', 0, '#', 'varequal', 0, '@', 'respident'), '', true);
            }
            // Suppressed an else block because if the above if condition is false,
            // the question is not necessary a broken one, most of the time it's an <other> tag.

            $response->feedback = $this->getpath($bbresponse,
                    array('#', 'displayfeedback', 0, '@', 'linkrefid'), '', true);
            $responses[] = $response;
        }
    }

    /**
     * Preprocess XML blocks containing data for responses processing.
     * Called by {@link create_raw_question()}
     * for all questions types.
     * @param array $bbresponses XML block to parse
     * @param array $responses array of responses suitable for a rawquestion.
     */
    protected function process_responses($bbresponses, &$responses) {
        foreach ($bbresponses as $bbresponse) {
            $response = new stdClass();
            if ($this->getpath($bbresponse,
                    array('@', 'title'), '', true)) {
                $response->title = $this->getpath($bbresponse,
                        array('@', 'title'), '', true);
            } else {
                $response->title = $this->getpath($bbresponse,
                        array('#', 'displayfeedback', 0, '@', 'linkrefid'), '', true);
            }
            $response->ident = array();
            if ($this->getpath($bbresponse,
                    array('#', 'conditionvar', 0, '#'), false, false)) {
                $response->ident[0] = $this->getpath($bbresponse,
                        array('#', 'conditionvar', 0, '#'), array(), false);
            } else if ($this->getpath($bbresponse,
                    array('#', 'conditionvar', 0, '#', 'other', 0, '#'), false, false)) {
                $response->ident[0] = $this->getpath($bbresponse,
                        array('#', 'conditionvar', 0, '#', 'other', 0, '#'), array(), false);
            }
            if ($this->getpath($bbresponse,
                    array('#', 'conditionvar', 0, '#', 'and'), false, false)) {
                $responseset = $this->getpath($bbresponse,
                    array('#', 'conditionvar', 0, '#', 'and'), array(), false);
                foreach ($responseset as $rs) {
                    $response->ident[] = $this->getpath($rs, array('#'), array(), false);
                    if (!isset($response->feedback) and $this->getpath($rs, array('@'), false, false)) {
                        $response->feedback = $this->getpath($rs,
                                array('@', 'respident'), '', true);
                    }
                }
            } else {
                $response->feedback = $this->getpath($bbresponse,
                        array('#', 'displayfeedback', 0, '@', 'linkrefid'), '', true);
            }

            // Determine what fraction to give response.
            if ($this->getpath($bbresponse,
                        array('#', 'setvar'), false, false)) {
                switch ($this->getpath($bbresponse,
                        array('#', 'setvar', 0, '#'), false, false)) {
                    case "SCORE.max":
                        $response->fraction = 1;
                        break;
                    default:
                        // I have only seen this being 0 or unset.
                        // There are probably fractional values of SCORE.max, but I'm not sure what they look like.
                        $response->fraction = 0;
                        break;
                }
            } else {
                // Just going to assume this is the case this is probably not correct.
                $response->fraction = 0;
            }

            $responses[] = $response;
        }
    }

    /**
     * Preprocess XML blocks containing data for responses feedbacks.
     * Called by {@link create_raw_question()}
     * for all questions types.
     * @param array $feedbackset XML block to parse
     * @param array $feedbacks array of feedbacks suitable for a rawquestion.
     */
    public function process_feedback($feedbackset, &$feedbacks) {
        foreach ($feedbackset as $bbfeedback) {
            $feedback = new stdClass();
            $feedback->ident = $this->getpath($bbfeedback,
                    array('@', 'ident'), '', true);
            $feedback->text = '';
            if ($this->getpath($bbfeedback,
                    array('#', 'flow_mat', 0), false, false)) {
                $this->process_block($this->getpath($bbfeedback,
                        array('#', 'flow_mat', 0), false, false), $feedback);
            } else if ($this->getpath($bbfeedback,
                    array('#', 'solution', 0, '#', 'solutionmaterial', 0, '#', 'flow_mat', 0), false, false)) {
                $this->process_block($this->getpath($bbfeedback,
                        array('#', 'solution', 0, '#', 'solutionmaterial', 0, '#', 'flow_mat', 0), false, false), $feedback);
            }

            $feedbacks[$feedback->ident] = $feedback;
        }
    }

    /**
     * Create common parts of question
     * @param object $quest rawquestion
     * @return object Moodle question.
     */
    public function process_common($quest) {
        $question = $this->defaultquestion();
        $text = $quest->QUESTION_BLOCK->text;
        $questiontext = $this->cleaned_text_field($text);
        $question->questiontext = $questiontext['text'];
        $question->questiontextformat = $questiontext['format']; // Needed because add_blank_combined_feedback uses it.
        if (isset($questiontext['itemid'])) {
            $question->questiontextitemid = $questiontext['itemid'];
        }
        $question->name = $this->create_default_question_name($question->questiontext,
                get_string('defaultname', 'qformat_blackboard_six' , $quest->id));
        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_HTML;
        $question->generalfeedbackfiles = array();

        return $question;
    }

    /**
     * Process True / False Questions
     * Parse a truefalse rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done
     */
    protected function process_tf($quest, &$questions) {
        $question = $this->process_common($quest);

        $question->qtype = 'truefalse';
        $question->single = 1; // Only one answer is allowed.
        $question->penalty = 1; // Penalty = 1 for truefalse questions.
        // 0th [response] is the correct answer.
        $responses = $quest->responses;
        $correctresponse = $this->getpath($responses[0]->ident[0],
                array('varequal', 0, '#'), '', true);
        if ($correctresponse != 'false') {
            $correct = true;
        } else {
            $correct = false;
        }
        $fback = new stdClass();

        foreach ($quest->feedback as $fb) {
            $fback->{$fb->ident} = $fb->text;
        }

        if ($correct) {  // True is correct.
            $question->answer = 1;
            $question->feedbacktrue = $this->cleaned_text_field($fback->correct);
            $question->feedbackfalse = $this->cleaned_text_field($fback->incorrect);
        } else {  // False is correct.
            $question->answer = 0;
            $question->feedbacktrue = $this->cleaned_text_field($fback->incorrect);
            $question->feedbackfalse = $this->cleaned_text_field($fback->correct);
        }
        $question->correctanswer = $question->answer;
        $questions[] = $question;
    }

    /**
     * Process Fill in the Blank Questions
     * Parse a fillintheblank rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done.
     */
    protected function process_fblank($quest, &$questions) {
        $question = $this->process_common($quest);
        $question->qtype = 'shortanswer';
        $question->usecase = 0; // Ignore case.

        $answers = array();
        $fractions = array();
        $feedbacks = array();

        // Extract the feedback.
        $feedback = array();
        foreach ($quest->feedback as $fback) {
            if (isset($fback->ident)) {
                if ($fback->ident == 'correct' || $fback->ident == 'incorrect') {
                    $feedback[$fback->ident] = $fback->text;
                }
            }
        }

        foreach ($quest->responses as $response) {
            if (isset($response->title)) {
                if ($this->getpath($response->ident[0],
                        array('varequal', 0, '#'), false, false)) {
                    // For BB Fill in the Blank, only interested in correct answers.
                    if ($response->feedback = 'correct') {
                        $answers[] = $this->getpath($response->ident[0],
                                array('varequal', 0, '#'), '', true);
                        $fractions[] = 1;
                        if (isset($feedback['correct'])) {
                            $feedbacks[] = $this->cleaned_text_field($feedback['correct']);
                        } else {
                            $feedbacks[] = $this->text_field('');
                        }
                    }
                }

            }
        }

        // Adding catchall to so that students can see feedback for incorrect answers when they enter something,
        // the instructor did not enter.
        $answers[] = '*';
        $fractions[] = 0;
        if (isset($feedback['incorrect'])) {
            $feedbacks[] = $this->cleaned_text_field($feedback['incorrect']);
        } else {
            $feedbacks[] = $this->text_field('');
        }

        $question->answer = $answers;
        $question->fraction = $fractions;
        $question->feedback = $feedbacks; // Changed to assign $feedbacks to $question->feedback instead of.

        if (!empty($question)) {
            $questions[] = $question;
        }

    }

    /**
     * Process Multichoice Questions
     * Parse a multichoice single answer rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done.
     */
    protected function process_mc($quest, &$questions) {
        $question = $this->process_common($quest);
        $question->qtype = 'multichoice';
        $question = $this->add_blank_combined_feedback($question);
        $question->single = 1;
        $feedback = array();
        foreach ($quest->feedback as $fback) {
            $feedback[$fback->ident] = $fback->text;
        }

        foreach ($quest->responses as $response) {
            if (isset($response->title)) {
                if ($response->title == 'correct') {
                    // Only one answer possible for this qtype so first index is correct answer.
                    $correct = $this->getpath($response->ident[0],
                            array('varequal', 0, '#'), '', true);
                }
            } else {
                // Fallback method for when the title is not set.
                if ($response->feedback == 'correct') {
                    // Only one answer possible for this qtype so first index is correct answer.
                    $correct = $this->getpath($response->ident[0],
                            array('varequal', 0, '#'), '', true);
                }
            }
        }

        $i = 0;
        foreach ($quest->RESPONSE_BLOCK->choices as $response) {
            $question->answer[$i] = $this->cleaned_text_field($response->text);
            if ($correct == $response->ident) {
                $question->fraction[$i] = 1;
                // This is a bit of a hack to catch the feedback... first we see if a  'specific'
                // feedback for this response exists, then if a 'correct' feedback exists.

                if (!empty($feedback[$response->ident]) ) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback[$response->ident]);
                } else if (!empty($feedback['correct'])) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback['correct']);
                } else if (!empty($feedback[$i])) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback[$i]);
                } else {
                    $question->feedback[$i] = $this->cleaned_text_field(get_string('correct', 'question'));
                }
            } else {
                $question->fraction[$i] = 0;
                if (!empty($feedback[$response->ident]) ) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback[$response->ident]);
                } else if (!empty($feedback['incorrect'])) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback['incorrect']);
                } else if (!empty($feedback[$i])) {
                    $question->feedback[$i] = $this->cleaned_text_field($feedback[$i]);
                } else {
                    $question->feedback[$i] = $this->cleaned_text_field(get_string('incorrect', 'question'));
                }
            }
            $i++;
        }

        if (!empty($question)) {
            $questions[] = $question;
        }
    }

    /**
     * Process Multiple Choice Questions With Multiple Answers.
     * Parse a multichoice multianswer rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done.
     */
    public function process_ma($quest, &$questions) {
        $question = $this->process_common($quest);
        $question->qtype = 'multichoice';
        $question = $this->add_blank_combined_feedback($question);
        $question->single = 0; // More than one answer allowed.

        $answers = $quest->responses;
        $correctanswers = array();
        foreach ($answers as $answer) {
            if ($answer->title == 'correct') {
                $answerset = $this->getpath($answer->ident[0],
                        array('and', 0, '#', 'varequal'), array(), false);
                foreach ($answerset as $ans) {
                    $correctanswers[] = $ans['#'];
                }
            }
        }
        $feedback = new stdClass();
        foreach ($quest->feedback as $fb) {
            $feedback->{$fb->ident} = trim($fb->text);
        }

        $correctanswercount = count($correctanswers);
        $fraction = 1 / $correctanswercount;
        $choiceset = $quest->RESPONSE_BLOCK->choices;
        $i = 0;
        foreach ($choiceset as $choice) {
            $question->answer[$i] = $this->cleaned_text_field(trim($choice->text));
            if (in_array($choice->ident, $correctanswers)) {
                // Correct answer.
                $question->fraction[$i] = $fraction;
                $question->feedback[$i] = $this->cleaned_text_field($feedback->correct);
            } else {
                // Wrong answer.
                $question->fraction[$i] = 0;
                $question->feedback[$i] = $this->cleaned_text_field($feedback->incorrect);
            }
            $i++;
        }

        $questions[] = $question;
    }

    /**
     * Process Essay Questions
     * Parse an essay rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done.
     */
    public function process_essay($quest, &$questions) {

        $question = $this->process_common($quest);
        $question->qtype = 'essay';

        $question->feedback = array();
        // Not sure where to get the correct answer from?
        foreach ($quest->feedback as $feedback) {
            // Added this code to put the possible solution that the
            // instructor gives as the Moodle answer for an essay question.
            if ($feedback->ident == 'solution') {
                $question->graderinfo = $this->cleaned_text_field($feedback->text);
            }
        }
        // Added because essay/questiontype.php:save_question_option is expecting a
        // fraction property - CT 8/10/06.
        $question->fraction[] = 1;
        $question->defaultmark = 1;
        $question->responseformat = 'editor';
        $question->responserequired = 1;
        $question->responsefieldlines = 15;
        $question->attachments = 0;
        $question->attachmentsrequired = 0;
        $question->responsetemplate = $this->text_field('');

        $questions[] = $question;
    }

    /**
     * Process Matching Questions
     * Parse a matching rawquestion and add the result
     * to the array of questions already parsed.
     * @param object $quest rawquestion
     * @param array $questions array of Moodle questions already done.
     */
    public function process_matching($quest, &$questions) {

        // Blackboard matching questions can't be imported in core Moodle without a loss in data,
        // as core match question don't allow HTML in subanswers. The contributed ddmatch
        // question type support HTML in subanswers.
        // The ddmatch question type is not part of core, so we need to check if it is defined.
        $ddmatchisinstalled = question_bank::is_qtype_installed('ddmatch');

        $question = $this->process_common($quest);
        $question = $this->add_blank_combined_feedback($question);
        $question->valid = true;
        if ($ddmatchisinstalled) {
            $question->qtype = 'ddmatch';
        } else {
            $question->qtype = 'match';
        }
        // Construction of the array holding mappings between subanswers and subquestions.
        foreach ($quest->RESPONSE_BLOCK->subquestions as $qid => $subq) {
            foreach ($quest->responses as $rid => $resp) {
                if (isset($resp->ident) && $resp->ident == $subq->ident) {
                    $correct = $resp->correct;
                }
            }

            foreach ($subq->choices as $cid => $choice) {
                if ($choice == $correct) {
                    $mappings[$subq->ident] = $cid;
                }
            }
        }

        foreach ($subq->choices as $choiceid => $choice) {
            $subanswertext = $quest->RIGHT_MATCH_BLOCK->matchinganswerset[$choiceid]->text;
            if ($ddmatchisinstalled) {
                $subanswer = $this->cleaned_text_field($subanswertext);
            } else {
                $subanswertext = html_to_text($this->cleaninput($subanswertext), 0);
                $subanswer = $subanswertext;
            }

            if ($subanswertext != '') { // Only import non empty subanswers.
                $subquestion = '';

                $fiber = moodle_array_keys_filter($mappings, $choiceid);
                foreach ($fiber as $correctanswerid) {
                    // We have found a correspondance for this subanswer so we need to take the associated subquestion.
                    foreach ($quest->RESPONSE_BLOCK->subquestions as $qid => $subq) {
                        $currentsubqid = $subq->ident;
                        if (strcmp ($currentsubqid, $correctanswerid) == 0) {
                            $subquestion = $subq->text;
                            break;
                        }
                    }
                    $question->subquestions[] = $this->cleaned_text_field($subquestion);
                    $question->subanswers[] = $subanswer;
                }

                if ($subquestion == '') { // Then in this case, $choice is a distractor.
                    $question->subquestions[] = $this->text_field('');
                    $question->subanswers[] = $subanswer;
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

    /**
     * Add a category question entry based on the assessment title
     * @param array $xml the xml tree
     * @param array $questions the questions already parsed
     */
    public function process_category($xml, &$questions) {
        $title = $this->getpath($xml, array('questestinterop', '#', 'assessment', 0, '@', 'title'), '', true);

        $dummyquestion = new stdClass();
        $dummyquestion->qtype = 'category';
        $dummyquestion->category = $this->cleaninput($this->clean_question_name($title));

        $questions[] = $dummyquestion;
    }

    /**
     * Strip the applet tag used by Blackboard to render mathml formulas,
     * keeping the mathml tag.
     * @param string $string
     * @return string
     */
    public function strip_applet_tags_get_mathml($string) {
        if (stristr($string, '</APPLET>') === false) {
            return $string;
        } else {
            // Strip all applet tags keeping stuff before/after and inbetween (if mathml) them.
            while (stristr($string, '</APPLET>') !== false) {
                preg_match("/(.*)\<applet.*value=\"(\<math\>.*\<\/math\>)\".*\<\/applet\>(.*)/i", $string, $mathmls);
                $string = $mathmls[1].$mathmls[2].$mathmls[3];
            }
            return $string;
        }
    }

}
