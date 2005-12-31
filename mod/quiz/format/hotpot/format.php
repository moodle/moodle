<?PHP // $Id$
////////////////////////////////////////////////////////////////////////////
/// Hotpotatoes 6.0 Format
///
/// This Moodle class provides all functions necessary to import (and export)
///
///
////////////////////////////////////////////////////////////////////////////

// Based on default.php, included by ../import.php

require_once ("$CFG->libdir/xmlize.php");

class quiz_format_hotpot extends quiz_default_format {

    function provide_import() {
        return true;
    }

    function readquestions ($lines) {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().

        // parse the xml source
        $source = implode($lines, " ");
        $xml = xmlize($source);

        // determine the quiz type
        $quiztype = '';
        $keys = array_keys($xml);
        foreach ($keys as $key) {
            if (preg_match('/(hotpot|textoys)-(\w+)-file/i', $key, $matches)) {
                $quiztype = strtolower($matches[2]);
                $xml = $xml[$matches[0]];
                break;
            }
        }

        $questions = array();
        switch ($quiztype) {
            case 'jcloze':
                process_jcloze($xml, $questions);
                break;
            case 'jcross':
                process_jcross($xml, $questions);
                break;
            case 'jmatch':
                process_jmatch($xml, $questions);
                break;
            case 'jmix':
                process_jmix($xml, $questions);
                break;
            case 'jquiz':
                process_jquiz($xml, $questions);
                break;
            default:
                notice("Unknown quiz type '$quiztype'");
                break;
        } // end switch

        return $questions;
    }
} // end class

function process_jcloze(&$xml, &$questions) {

	static $moodle_14;
	
	if (!isset($moodle_14)) {
		global $CFG, $db;
		$moodle_14 = false;
		if ($columns = $db->MetaColumns("{$CFG->prefix}quiz_multianswers")) {
			foreach ($columns as $column) {
				if ($column->name=='answers' || $column->name=='positionkey' || $column->name=='answertype' || $column->name=='norm') {
					$moodle_14 = true;
				}
			}
		}
	}

    $x = 0;
    while ($exercise = &$xml['#']['data'][0]['#']['gap-fill'][$x]['#']) {
        // there is usually only one exercise in a file

        $question = new stdClass();

        $question->qtype = MULTIANSWER;
        $question->defaultgrade = 1;
        $question->usecase = 0; // Ignore case
        $question->image = "";  // No images with this format

        $question->name = get_hotpotatoes_title($xml, $x);
        $question->questiontext = get_hotpotatoes_reading($xml);

		if ($moodle_14) {
			// Moodle 1.4 (or earlier)
			$question->answers = array();
		} else {
			// Moodle 1.5 (or greater)
			global $course; // set in mod/quiz/import.php
			$question->course = $course->id;
			$question->options = new stdClass();
			$question->options->questions = array(); // one for each gap
		}

        $q = 0;
        while ($question_record = &$exercise['question-record'][$q]['#']) {
			$positionkey = $q+1;
            if (isset($exercise[$q])) {
                $question->questiontext .= addslashes($exercise[$q]);
            }
            $question->questiontext .= '{#'.$positionkey.'}';
			
			if ($moodle_14) {
				// Moodle 1.4 (or earlier)
				$question->answers[$q]->positionkey = $positionkey;
				$question->answers[$q]->answertype = SHORTANSWER;
				$question->answers[$q]->norm = 1;
				$question->answers[$q]->alternatives = array();
			} else {
				// Moodle 1.5 (or greater)
				$wrapped = new stdClass();
				$wrapped->qtype = SHORTANSWER;
				$wrapped->usecase = 0;
				$wrapped->defaultgrade = 1;
				$wrapped->questiontextformat = 0;
				$wrapped->answer = array();
				$wrapped->fraction = array();
				$wrapped->feedback = array();
				$answers = array();
			}

            $a = 0;
            while ($answer = &$question_record['answer'][$a]['#']) {
				$text = addslashes($answer['text'][0]['#']);
				$fraction = empty($answer['correct'][0]['#']) ? 0 : 1;
				$feedback = addslashes($answer['feedback'][0]['#']);
				if ($moodle_14) {
					// Moodle 1.4 (or earlier)
					$question->answers[$q]->alternatives[$a] = new stdClass();
					$question->answers[$q]->alternatives[$a]->answer = $text;
					$question->answers[$q]->alternatives[$a]->fraction = $fraction;
					$question->answers[$q]->alternatives[$a]->feedback = $feedback;
				} else {
					// Moodle 1.5 (or greater)
					$wrapped->answer[] = $text;
					$wrapped->fraction[] = $fraction;
					$wrapped->feedback[] = $feedback;
					$answers[] = (empty($fraction) ? '' : '=').$text.(empty($feedback) ? '' : ('#'.$feedback));
				}
                $a++;
            }
			if ($moodle_14) {
				// Moodle 1.4 (or earlier)
			} else {
				// Moodle 1.5 (or greater)
				$wrapped->questiontext = '{'.$positionkey.':SHORTANSWER:'.implode('~', $answers).'}';
				$question->options->questions[] = $wrapped;
			}
            $q++;
        }
        if (isset($exercise[$q])) {
            $question->questiontext .= addslashes($exercise[$q]);
        }

        $questions[] = $question;
        $x++;
    }
}

function process_jcross(&$xml, &$questions) {
    $x = 0;
    while ($item = &$xml['#']['data'][0]['#']['crossword'][0]['#']['clues'][0]['#']['item'][$x]['#']) {

        $text = $item['def'][0]['#'];
        $answer = $item['word'][0]['#'];

        if ($text && $answer) {
            $question = new stdClass();
            $question->qtype = SHORTANSWER;
            $question->usecase = 0; // Ignore case
            $question->image = "";  // No images with this format
            $question->name = get_hotpotatoes_title($xml, $x, true);

            $question->questiontext = addslashes($text);
            $question->answer = array(addslashes($answer));
            $question->fraction = array(1);
            $question->feedback = array('');

            $questions[] = $question;
        }
        $x++;
    }
}

function process_jmatch(&$xml, &$questions) {

    $x = 0;
    while ($exercise = &$xml['#']['data'][0]['#']['matching-exercise'][$x]['#']) {
        // there is usually only one exercise in a file

        $question = new stdClass();

        $question->qtype = MATCH;
        $question->defaultgrade = 1;
        $question->image = "";  // No images with this format
        $question->name = get_hotpotatoes_title($xml, $x);

        $question->questiontext = get_hotpotatoes_reading($xml);
        $question->questiontext .= get_hotpotatoes_instructions($xml, 'jmatch');

        $question->subquestions = array();
        $question->subanswers = array();
        $p = 0;
        while ($pair = &$exercise['pair'][$p]['#']) {
            $question->subquestions[$p] = addslashes($pair['left-item'][0]['#']['text'][0]['#']);
            $question->subanswers[$p] = addslashes($pair['right-item'][0]['#']['text'][0]['#']);
            $p++;
        }
        $questions[] = $question;
        $x++;
    }
}

function process_jmix(&$xml, &$questions) {
    $x = 0;
    while ($exercise = &$xml['#']['data'][0]['#']['jumbled-order-exercise'][$x]['#']) {
        // there is usually only one exercise in a file

        $question = new stdClass();
        $question->qtype = SHORTANSWER;
        $question->usecase = 0; // Ignore case
        $question->image = "";  // No images with this format
        $question->name = get_hotpotatoes_title($xml, $x);

        $question->answer = array();
        $question->fraction = array();
        $question->feedback = array();

        $i = 0;
        $segments = array();
        while ($segment = &$exercise['main-order'][0]['#']['segment'][$i]['#']) {
            $segments[] = addslashes($segment);
            $i++;
        }
        $answer = implode(' ', $segments);

        seed_hotpotatoes_RNG();
        shuffle($segments);

        $question->questiontext = get_hotpotatoes_reading($xml);
        $question->questiontext .= get_hotpotatoes_instructions($xml, 'jmix');
        $question->questiontext .= ' &nbsp; <NOBR><B>[ &nbsp; '.implode(' &nbsp; ', $segments).' &nbsp; ]</B></NOBR>';

        $a = 0;
        while (!empty($answer)) {
            $question->answer[$a] = $answer;
            $question->fraction[$a] = 1;
            $question->feedback[$a] = '';
            $answer = addslashes($exercise['alternate'][$a++]['#']);
        }
        $questions[] = $question;
        $x++;
    }
}
function process_jquiz(&$xml, &$questions) {
    $x = 0;
    while ($exercise = &$xml['#']['data'][0]['#']['questions'][$x]['#']) {
        // there is usually only one 'questions' object in a single exercise

        $q = 0;
        while ($question_record = &$exercise['question-record'][$q]['#']) {

            $question= new stdClass();
            $question->defaultgrade = 1;
            $question->usecase = 0; // Ignore case
            $question->image = "";  // No images with this format
            $question->name = get_hotpotatoes_title($xml, $q, true);

            $question->questiontext = addslashes($question_record['question'][0]['#']);

            $type = $question_record['question-type'][0]['#'];
            //  1 : multiple choice
            //  2 : short-answer
            //  3 : hybrid
            //  4 : multiple select
            $question->qtype = ($type==2 ? SHORTANSWER : MULTICHOICE);
            $question->single = ($type==4 ? 0 : 1);

            // workaround required to calculate scores for multiple select answers
            $no_of_correct_answers = 0;
            if ($type==4) {
                $a = 0;
                while ($answer = &$question_record['answers'][0]['#']['answer'][$a]['#']) {
                    if (!empty($answer['correct'][0]['#'])) {
                        $no_of_correct_answers++;
                    }
                    $a++;
                }
            }
            $a = 0;
            $question->answer = array();
            $question->fraction = array();
            $question->feedback = array();
            while ($answer = &$question_record['answers'][0]['#']['answer'][$a]['#']) {
                if (empty($answer['correct'][0]['#'])) {
                    $fraction = 0;
                } else if ($type==4) { // multiple select
                    // strange behavior if the $fraction isn't exact to 5 decimal places
                    $fraction = round(1/$no_of_correct_answers, 5);
                } else {
                    $fraction = $answer['percent-correct'][0]['#']/100;
                }
                $question->feedback[] = addslashes($answer['feedback'][0]['#']);
                $question->fraction[] = $fraction;
                $question->answer[] = addslashes($answer['text'][0]['#']);
                $a++;
            }
            $questions[] = $question;
            $q++;
        }
        $x++;
    }
}

function seed_hotpotatoes_RNG() {
    static $seeded_hotpotatoes_RNG = FALSE;
    if (!$seeded_hotpotatoes_RNG) {
        srand((double) microtime() * 1000000);
        $seeded_hotpotatoes_RNG = TRUE;
    }
}
function get_hotpotatoes_title(&$xml, $x, $flag=false) {
    $title = $xml['#']['data'][0]['#']['title'][0]['#'];
    if ($x || $flag) {
        $title .= ' ('.($x+1).')';
    }
    return addslashes($title);
}
function get_hotpotatoes_instructions(&$xml, $quiztype) {
    $text = $xml['#']['hotpot-config-file'][0]['#'][$quiztype][0]['#']['instructions'][0]['#'];
    if (empty($text)) {
        $text = "Hot Potatoes $quiztype";
    }
    return addslashes($text);
}
function get_hotpotatoes_reading(&$xml) {
    $str = '';
    $obj = &$xml['#']['data'][0]['#']['reading'][0]['#'];
    if ($obj['include-reading'][0]['#']) {
        if ($title = $obj['reading-title'][0]['#']) {
            $str .= "<H3>$title</H3>";
        }
        if ($text = $obj['reading-text'][0]['#']) {
            $str .= "<P>$text</P>";
        }
    }
    return addslashes($str);
}

?>
