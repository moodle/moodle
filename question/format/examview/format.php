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
 * @package    qformat
 * @subpackage examview
 * @copyright  2005 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/xmlize.php');


/**
 * Examview question importer.
 *
 * @copyright  2005 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_examview extends qformat_default {

    public $qtypes = array(
        'tf' => TRUEFALSE,
        'mc' => MULTICHOICE,
        'yn' => TRUEFALSE,
        'co' => SHORTANSWER,
        'ma' => MATCH,
        'mtf' => 99,
        'nr' => NUMERICAL,
        'pr' => 99,
        'es' => 99,
        'ca' => 99,
        'ot' => 99,
        'sa' => ESSAY
        );

    public $matching_questions = array();

    function provide_import() {
        return true;
    }

    /**
     * unxmlise reconstructs part of the xml data structure in order
     * to identify the actual data therein
     * @param array $xml section of the xml data structure
     * @return string data with evrything else removed
     */
    function unxmlise( $xml ) {
        // if it's not an array then it's probably just data
        if (!is_array($xml)) {
            $text = s($xml);
        }
        else {
            // otherwise parse the array
            $text = '';
            foreach ($xml as $tag=>$data) {
                // if tag is '@' then it's attributes and we don't care
                if ($tag!=='@') {
                    $text = $text . $this->unxmlise( $data );
                }
            }
        }

        // currently we throw the tags we found
        $text = strip_tags($text);
        return $text;
    }

    function parse_matching_groups($matching_groups)
    {
        if (empty($matching_groups)) {
            return;
        }
        foreach($matching_groups as $match_group) {
            $newgroup = NULL;
            $groupname = trim($match_group['@']['name']);
            $questiontext = $this->unxmlise($match_group['#']['text'][0]['#']);
            $newgroup->questiontext = trim($questiontext);
            $newgroup->subchoices = array();
            $newgroup->subquestions = array();
            $newgroup->subanswers = array();
            $choices = $match_group['#']['choices']['0']['#'];
            foreach($choices as $key => $value) {
                if (strpos(trim($key),'choice-') !== FALSE) {
                    $key = strtoupper(trim(str_replace('choice-', '', $key)));
                    $newgroup->subchoices[$key] = trim($value['0']['#']);
                }
            }
            $this->matching_questions[$groupname] = $newgroup;
        }
    }

    function parse_ma($qrec, $groupname)
    {
        $match_group = $this->matching_questions[$groupname];
        $phrase = trim($this->unxmlise($qrec['text']['0']['#']));
        $answer = trim($this->unxmlise($qrec['answer']['0']['#']));
        $answer = strip_tags( $answer );
        $match_group->subquestions[] = $phrase;
        $match_group->subanswers[] = $match_group->subchoices[$answer];
        $this->matching_questions[$groupname] = $match_group;
        return NULL;
    }

    function process_matches(&$questions)
    {
        if (empty($this->matching_questions)) {
            return;
        }
        foreach($this->matching_questions as $match_group) {
            $question = $this->defaultquestion();
            $htmltext = s($match_group->questiontext);
            $question->questiontext = $htmltext;
            $question->name = $question->questiontext;
            $question->qtype = MATCH;
            $question->subquestions = array();
            $question->subanswers = array();
            foreach($match_group->subquestions as $key => $value) {
                $htmltext = s($value);
                $question->subquestions[] = $htmltext;

                $htmltext = s($match_group->subanswers[$key]);
                $question->subanswers[] = $htmltext;
            }
            $questions[] = $question;
        }
    }

    function cleanUnicode($text) {
        return str_replace('&#x2019;', "'", $text);
    }

    protected function readquestions($lines) {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().

        $questions = array();
        $currentquestion = array();

        $text = implode($lines, ' ');
        $text = $this->cleanUnicode($text);

        $xml = xmlize($text, 0);
        if (!empty($xml['examview']['#']['matching-group'])) {
            $this->parse_matching_groups($xml['examview']['#']['matching-group']);
        }

        $questionNode = $xml['examview']['#']['question'];
        foreach($questionNode as $currentquestion) {
            if ($question = $this->readquestion($currentquestion)) {
                $questions[] = $question;
            }
        }

        $this->process_matches($questions);
        return $questions;
    }
    // end readquestions

    function readquestion($qrec)
    {

        $type = trim($qrec['@']['type']);
        $question = $this->defaultquestion();
        if (array_key_exists($type, $this->qtypes)) {
            $question->qtype = $this->qtypes[$type];
        }
        else {
            $question->qtype = null;
        }
        $question->single = 1;
        // Only one answer is allowed
        $htmltext = $this->unxmlise($qrec['#']['text'][0]['#']);
        $question->questiontext = $htmltext;
        $question->name = shorten_text( $question->questiontext, 250 );

        switch ($question->qtype) {
        case MULTICHOICE:
            $question = $this->parse_mc($qrec['#'], $question);
            break;
        case MATCH:
            $groupname = trim($qrec['@']['group']);
            $question = $this->parse_ma($qrec['#'], $groupname);
            break;
        case TRUEFALSE:
            $question = $this->parse_tf_yn($qrec['#'], $question);
            break;
        case SHORTANSWER:
            $question = $this->parse_co($qrec['#'], $question);
            break;
        case ESSAY:
            $question = $this->parse_sa($qrec['#'], $question);
            break;
        case NUMERICAL:
            $question = $this->parse_nr($qrec['#'], $question);
            break;
            break;
            default:
            print("<p>Question type ".$type." import not supported for ".$question->questiontext."<p>");
            $question = NULL;
        }
        // end switch ($question->qtype)

        return $question;
    }
    // end readquestion

    function parse_tf_yn($qrec, $question)
    {
        $choices = array('T' => 1, 'Y' => 1, 'F' => 0, 'N' => 0 );
        $answer = trim($qrec['answer'][0]['#']);
        $question->answer = $choices[$answer];
        $question->correctanswer = $question->answer;
        if ($question->answer == 1) {
            $question->feedbacktrue = 'Correct';
            $question->feedbackfalse = 'Incorrect';
        } else {
            $question->feedbacktrue = 'Incorrect';
            $question->feedbackfalse = 'Correct';
        }
        return $question;
    }

    function parse_mc($qrec, $question)
    {
        $answer = 'choice-'.strtolower(trim($qrec['answer'][0]['#']));

        $choices = $qrec['choices'][0]['#'];
        foreach($choices as $key => $value) {
            if (strpos(trim($key),'choice-') !== FALSE) {

                $question->answer[$key] = s($this->unxmlise($value[0]['#']));
                if (strcmp($key, $answer) == 0) {
                    $question->fraction[$key] = 1;
                    $question->feedback[$key] = 'Correct';
                } else {
                    $question->fraction[$key] = 0;
                    $question->feedback[$key] = 'Incorrect';
                }
            }
        }
        return $question;
    }

    function parse_co($qrec, $question)
    {
        $question->usecase = 0;
        $answer = trim($this->unxmlise($qrec['answer'][0]['#']));
        $answer = strip_tags( $answer );
        $answers = explode("\n",$answer);

        foreach($answers as $key => $value) {
            $value = trim($value);
            if (strlen($value) > 0) {
                $question->answer[$key] = $value;
                $question->fraction[$key] = 1;
                $question->feedback[$key] = "Correct";
            }
        }
        return $question;
    }

    function parse_sa($qrec, $question) {
        $feedback = trim($this->unxmlise($qrec['answer'][0]['#']));
        $question->feedback = $feedback;
        $question->fraction = 0;
        return $question;
    }

    function parse_nr($qrec, $question)
    {
        $answer = trim($this->unxmlise($qrec['answer'][0]['#']));
        $answer = strip_tags( $answer );
        $answers = explode("\n",$answer);

        foreach($answers as $key => $value) {
            $value = trim($value);
            if (is_numeric($value)) {
                $errormargin = 0;
                $question->answer[$key] = $value;
                $question->fraction[$key] = 1;
                $question->feedback[$key] = "Correct";
                $question->min[$key] = $question->answer[$key] - $errormargin;
                $question->max[$key] = $question->answer[$key] + $errormargin;
            }
        }
        return $question;
    }

}
// end class


