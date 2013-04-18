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
 * Examview question importer.
 *
 * @package    qformat_examview
 * @copyright  2005 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/xmlize.php');


/**
 * Examview question importer.
 *
 * @copyright  2005 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_examview extends qformat_based_on_xml {

    public $qtypes = array(
        'tf' => 'truefalse',
        'mc' => 'multichoice',
        'yn' => 'truefalse',
        'co' => 'shortanswer',
        'ma' => 'match',
        'mtf' => 99,
        'nr' => 'numerical',
        'pr' => 99,
        'es' => 'essay',
        'ca' => 99,
        'ot' => 99,
        'sa' => 'shortanswer',
    );

    public $matching_questions = array();

    public function provide_import() {
        return true;
    }

    public function mime_type() {
        return 'application/xml';
    }

    /**
     * unxmlise reconstructs part of the xml data structure in order
     * to identify the actual data therein
     * @param array $xml section of the xml data structure
     * @return string data with evrything else removed
     */
    protected function unxmlise( $xml ) {
        // If it's not an array then it's probably just data.
        if (!is_array($xml)) {
            $text = s($xml);
        } else {
            // Otherwise parse the array.
            $text = '';
            foreach ($xml as $tag => $data) {
                // If tag is '@' then it's attributes and we don't care.
                if ($tag!=='@') {
                    $text = $text . $this->unxmlise( $data );
                }
            }
        }

        // Currently we throw the tags we found.
        $text = strip_tags($text);
        return $text;
    }

    public function parse_matching_groups($matching_groups) {
        if (empty($matching_groups)) {
            return;
        }
        foreach ($matching_groups as $match_group) {
            $newgroup = new stdClass();
            $groupname = trim($match_group['@']['name']);
            $questiontext = $this->unxmlise($match_group['#']['text'][0]['#']);
            $newgroup->questiontext = trim($questiontext);
            $newgroup->subchoices = array();
            $newgroup->subquestions = array();
            $newgroup->subanswers = array();
            $choices = $match_group['#']['choices']['0']['#'];
            foreach ($choices as $key => $value) {
                if (strpos(trim($key), 'choice-') !== false) {
                    $key = strtoupper(trim(str_replace('choice-', '', $key)));
                    $newgroup->subchoices[$key] = trim($value['0']['#']);
                }
            }
            $this->matching_questions[$groupname] = $newgroup;
        }
    }

    protected function parse_ma($qrec, $groupname) {
        $match_group = $this->matching_questions[$groupname];
        $phrase = trim($this->unxmlise($qrec['text']['0']['#']));
        $answer = trim($this->unxmlise($qrec['answer']['0']['#']));
        $answer = strip_tags( $answer );
        $match_group->mappings[$phrase] = $match_group->subchoices[$answer];
        $this->matching_questions[$groupname] = $match_group;
        return null;
    }

    protected function process_matches(&$questions) {
        if (empty($this->matching_questions)) {
            return;
        }

        foreach ($this->matching_questions as $match_group) {
            $question = $this->defaultquestion();
            $htmltext = s($match_group->questiontext);
            $question->questiontext = $htmltext;
            $question->questiontextformat = FORMAT_HTML;
            $question->questiontextfiles = array();
            $question->name = $this->create_default_question_name($question->questiontext, get_string('questionname', 'question'));
            $question->qtype = 'match';
            $question = $this->add_blank_combined_feedback($question);
            $question->subquestions = array();
            $question->subanswers = array();
            foreach ($match_group->subchoices as $subchoice) {
                $fiber = array_keys ($match_group->mappings, $subchoice);
                $subquestion = '';
                foreach ($fiber as $subquestion) {
                    $question->subquestions[] = $this->text_field($subquestion);
                    $question->subanswers[] = $subchoice;
                }
                if ($subquestion == '') { // Then in this case, $subchoice is a distractor.
                    $question->subquestions[] = $this->text_field('');
                    $question->subanswers[] = $subchoice;
                }
            }
            $questions[] = $question;
        }
    }

    protected function cleanunicode($text) {
        return str_replace('&#x2019;', "'", $text);
    }

    public function readquestions($lines) {
        // Parses an array of lines into an array of questions,
        // where each item is a question object as defined by
        // readquestion().

        $questions = array();
        $currentquestion = array();

        $text = implode($lines, ' ');
        $text = $this->cleanunicode($text);

        $xml = xmlize($text, 0);
        if (!empty($xml['examview']['#']['matching-group'])) {
            $this->parse_matching_groups($xml['examview']['#']['matching-group']);
        }

        $questionnode = $xml['examview']['#']['question'];
        foreach ($questionnode as $currentquestion) {
            if ($question = $this->readquestion($currentquestion)) {
                $questions[] = $question;
            }
        }

        $this->process_matches($questions);
        return $questions;
    }

    public function readquestion($qrec) {
        global $OUTPUT;

        $type = trim($qrec['@']['type']);
        $question = $this->defaultquestion();
        if (array_key_exists($type, $this->qtypes)) {
            $question->qtype = $this->qtypes[$type];
        } else {
            $question->qtype = null;
        }
        $question->single = 1;

        // Only one answer is allowed.
        $htmltext = $this->unxmlise($qrec['#']['text'][0]['#']);

        $question->questiontext = $this->cleaninput($htmltext);
        $question->questiontextformat = FORMAT_HTML;
        $question->questiontextfiles = array();
        $question->name = $this->create_default_question_name($question->questiontext, get_string('questionname', 'question'));

        switch ($question->qtype) {
            case 'multichoice':
                $question = $this->parse_mc($qrec['#'], $question);
                break;
            case 'match':
                $groupname = trim($qrec['@']['group']);
                $question = $this->parse_ma($qrec['#'], $groupname);
                break;
            case 'truefalse':
                $question = $this->parse_tf_yn($qrec['#'], $question);
                break;
            case 'shortanswer':
                $question = $this->parse_co($qrec['#'], $question);
                break;
            case 'essay':
                $question = $this->parse_es($qrec['#'], $question);
                break;
            case 'numerical':
                $question = $this->parse_nr($qrec['#'], $question);
                break;
                break;
            default:
                echo $OUTPUT->notification(get_string('unknownorunhandledtype', 'question', $type));
                $question = null;
        }

        return $question;
    }

    protected function parse_tf_yn($qrec, $question) {
        $choices = array('T' => 1, 'Y' => 1, 'F' => 0, 'N' => 0 );
        $answer = trim($qrec['answer'][0]['#']);
        $question->answer = $choices[$answer];
        $question->correctanswer = $question->answer;
        if ($question->answer == 1) {
            $question->feedbacktrue = $this->text_field(get_string('correct', 'question'));
            $question->feedbackfalse = $this->text_field(get_string('incorrect', 'question'));
        } else {
            $question->feedbacktrue = $this->text_field(get_string('incorrect', 'question'));
            $question->feedbackfalse = $this->text_field(get_string('correct', 'question'));
        }
        return $question;
    }

    protected function parse_mc($qrec, $question) {
        $question = $this->add_blank_combined_feedback($question);
        $answer = 'choice-'.strtolower(trim($qrec['answer'][0]['#']));

        $choices = $qrec['choices'][0]['#'];
        foreach ($choices as $key => $value) {
            if (strpos(trim($key), 'choice-') !== false) {

                $question->answer[] = $this->text_field(s($this->unxmlise($value[0]['#'])));
                if (strcmp($key, $answer) == 0) {
                    $question->fraction[] = 1;
                    $question->feedback[] = $this->text_field(get_string('correct', 'question'));
                } else {
                    $question->fraction[] = 0;
                    $question->feedback[] = $this->text_field(get_string('incorrect', 'question'));
                }
            }
        }
        return $question;
    }

    protected function parse_co($qrec, $question) {
        $question->usecase = 0;
        $answer = trim($this->unxmlise($qrec['answer'][0]['#']));
        $answer = strip_tags( $answer );
        $answers = explode("\n", $answer);

        foreach ($answers as $key => $value) {
            $value = trim($value);
            if (strlen($value) > 0) {
                $question->answer[] = $value;
                $question->fraction[] = 1;
                $question->feedback[] = $this->text_field(get_string('correct', 'question'));
            }
        }
        $question->answer[] = '*';
        $question->fraction[] = 0;
        $question->feedback[] = $this->text_field(get_string('incorrect', 'question'));

        return $question;
    }

    protected function parse_es($qrec, $question) {
        $feedback = trim($this->unxmlise($qrec['answer'][0]['#']));
        $question->graderinfo =  $this->text_field($feedback);
        $question->responsetemplate =  $this->text_field('');
        $question->feedback = $feedback;
        $question->responseformat = 'editor';
        $question->responsefieldlines = 15;
        $question->attachments = 0;
        $question->fraction = 0;
        return $question;
    }

    protected function parse_nr($qrec, $question) {
        $answer = trim($this->unxmlise($qrec['answer'][0]['#']));
        $answer = strip_tags( $answer );
        $answers = explode("\n", $answer);

        foreach ($answers as $key => $value) {
            $value = trim($value);
            if (is_numeric($value)) {
                $errormargin = 0;
                $question->answer[] = $value;
                $question->fraction[] = 1;
                $question->feedback[] = $this->text_field(get_string('correct', 'question'));
                $question->tolerance[] = $errormargin;
            }
        }
        return $question;
    }

}
// End class.


