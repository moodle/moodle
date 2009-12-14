<?PHP // $Id$
////////////////////////////////////////////////////////////////////////////
/// Hotpotatoes 5.0 and 6.0 Format
///
/// This Moodle class provides all functions necessary to import
///                                      (export is not implemented ... yet)
///
////////////////////////////////////////////////////////////////////////////

// Based on default.php, included by ../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
require_once($CFG->dirroot . '/mod/hotpot/lib.php');

class qformat_hotpot extends qformat_default {

    function provide_import() {
        return true;
    }

    function readquestions ($lines) {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().

        // set courseid and baseurl
        global $CFG, $COURSE, $course;
        switch (true) {
            case isset($this->course->id):
                // import to quiz module
                $courseid = $this->course->id;
                break;
            case isset($course->id):
                // import to lesson module
                $courseid = $course->id;
                break;
            case isset($COURSE->id):
                // last resort
                $courseid = $COURSE->id;
                break;
            default:
                // shouldn't happen !!
                $courseid = 0; 
        }
        require_once($CFG->libdir.'/filelib.php');
        $baseurl = get_file_url($courseid).'/';

        // get import file name
        global $params;
        if (! empty($this->realfilename)) {
            $filename = $this->realfilename;
        } else if (isset($params) && !empty($params->choosefile)) {
            // course file (Moodle >=1.6+)
            $filename = $params->choosefile;
        } else {
            // uploaded file (all Moodles)
            $filename = basename($_FILES['newfile']['tmp_name']);
        }

        // get hotpot file source
        $source = implode($lines, " ");
        $source = hotpot_convert_relative_urls($source, $baseurl, $filename);

        // create xml tree for this hotpot
        $xml = new hotpot_xml_tree($source);

        // determine the quiz type
        $xml->quiztype = '';
        $keys = array_keys($xml->xml);
        foreach ($keys as $key) {
            if (preg_match('/^(hotpot|textoys)-(\w+)-file$/i', $key, $matches)) {
                $xml->quiztype = strtolower($matches[2]);
                $xml->xml_root = "['$key']['#']";
                break;
            }
        }

        // convert xml to questions array
        $questions = array();
        switch ($xml->quiztype) {
            case 'jcloze':
                $this->process_jcloze($xml, $questions);
                break;
            case 'jcross':
                $this->process_jcross($xml, $questions);
                break;
            case 'jmatch':
                $this->process_jmatch($xml, $questions);
                break;
            case 'jmix':
                $this->process_jmix($xml, $questions);
                break;
            case 'jbc':
            case 'jquiz':
                $this->process_jquiz($xml, $questions);
                break;
            default:
                if (empty($xml->quiztype)) {
                    notice("Input file not recognized as a Hot Potatoes XML file");
                } else {
                    notice("Unknown quiz type '$xml->quiztype'");
                }
        } // end switch

        if (count($questions)) {
            return $questions;
        } else {
            if (method_exists($this, 'error')) { // Moodle >= 1.8
                $this->error(get_string('giftnovalidquestion', 'quiz'));
            }
            return false;
        }
    }

    function process_jcloze(&$xml, &$questions) {
        // define default grade (per cloze gap)
        $defaultgrade = 1;
        $gap_count = 0;

        // detect old Moodles (1.4 and earlier)
        global $CFG, $db;
        $moodle_14 = false;
        if ($columns = $db->MetaColumns("{$CFG->prefix}question_multianswer")) {
            foreach ($columns as $column) {
                if ($column->name=='answers' || $column->name=='positionkey' || $column->name=='answertype' || $column->name=='norm') {
                    $moodle_14 = true;
                }
            }
        }

        // xml tags for the start of the gap-fill exercise
        $tags = 'data,gap-fill';

        $x = 0;
        while (($exercise = "[$x]['#']") && $xml->xml_value($tags, $exercise)) {
            // there is usually only one exercise in a file

            if (method_exists($this, 'defaultquestion')) {
                $question = $this->defaultquestion();
            } else {
                $question = new stdClass();
                $question->usecase = 0; // Ignore case
                $question->image = "";  // No images with this format
            }
            $question->qtype = MULTIANSWER;

            $question->name = $this->hotpot_get_title($xml, $x);
            $question->questiontext = $this->hotpot_get_reading($xml);

            // setup answer arrays
            if ($moodle_14) {
                $question->answers = array();
            } else {
                global $COURSE; // initialized in questions/import.php
                $question->course = $COURSE->id;
                $question->options = new stdClass();
                $question->options->questions = array(); // one for each gap
            }

            $q = 0;
            while ($text = $xml->xml_value($tags, $exercise."[$q]")) {
                // add next bit of text
                $question->questiontext .= $this->hotpot_prepare_str($text);

                // check for a gap
                $question_record = $exercise."['question-record'][$q]['#']";
                if ($xml->xml_value($tags, $question_record)) {

                    // add gap
                    $gap_count ++;
                    $positionkey = $q+1;
                    $question->questiontext .= '{#'.$positionkey.'}';
        
                    // initialize answer settings
                    if ($moodle_14) {
                        $question->answers[$q]->positionkey = $positionkey;
                        $question->answers[$q]->answertype = SHORTANSWER;
                        $question->answers[$q]->norm = $defaultgrade;
                        $question->answers[$q]->alternatives = array();
                    } else {
                        $wrapped = new stdClass();
                        $wrapped->qtype = SHORTANSWER;
                        $wrapped->usecase = 0;
                        $wrapped->defaultgrade = $defaultgrade;
                        $wrapped->questiontextformat = 0;
                        $wrapped->answer = array();
                        $wrapped->fraction = array();
                        $wrapped->feedback = array();
                        $answers = array();
                    }
        
                    // add answers
                    $a = 0;
                    while (($answer=$question_record."['answer'][$a]['#']") && $xml->xml_value($tags, $answer)) {
                        $text = $this->hotpot_prepare_str($xml->xml_value($tags,  $answer."['text'][0]['#']"));
                        $correct = $xml->xml_value($tags,  $answer."['correct'][0]['#']");
                        $feedback = $this->hotpot_prepare_str($xml->xml_value($tags,  $answer."['feedback'][0]['#']"));
                        if (strlen($text)) {
                            // set score (0=0%, 1=100%)
                            $fraction = empty($correct) ? 0 : 1;
                            // store answer
                            if ($moodle_14) {
                                $question->answers[$q]->alternatives[$a] = new stdClass();
                                $question->answers[$q]->alternatives[$a]->answer = $text;
                                $question->answers[$q]->alternatives[$a]->fraction = $fraction;
                                $question->answers[$q]->alternatives[$a]->feedback = $feedback;
                            } else {
                                $wrapped->answer[] = $text;
                                $wrapped->fraction[] = $fraction;
                                $wrapped->feedback[] = $feedback;
                                $answers[] = (empty($fraction) ? '' : '=').$text.(empty($feedback) ? '' : ('#'.$feedback));
                            }
                        }
                        $a++;
                    }
                    // compile answers into question text, if necessary
                    if ($moodle_14) {
                        // do nothing
                    } else {
                        $wrapped->questiontext = '{'.$defaultgrade.':SHORTANSWER:'.implode('~', $answers).'}';
                        $question->options->questions[] = $wrapped;
                    }
                } // end if gap
                $q++;
            } // end while $text

            if ($q) {
                // define total grade for this exercise
                $question->defaultgrade = $gap_count * $defaultgrade;

                // add this cloze as a single question object
                $questions[] = $question;
            } else {
                // no gaps found in this text so don't add this question
                // import will fail and error message will be displayed:
            }

            $x++;
        } // end while $exercise
    }

    function process_jcross(&$xml, &$questions) {
        // xml tags to the start of the crossword exercise clue items
        $tags = 'data,crossword,clues,item';

        $x = 0;
        while (($item = "[$x]['#']") && $xml->xml_value($tags, $item)) {

            $text = $xml->xml_value($tags, $item."['def'][0]['#']");
            $answer = $xml->xml_value($tags, $item."['word'][0]['#']");

            if ($text && $answer) {
                if (method_exists($this, 'defaultquestion')) {
                    $question = $this->defaultquestion();
                } else {
                    $question = new stdClass();
                    $question->usecase = 0; // Ignore case
                    $question->image = "";  // No images with this format
                }
                $question->qtype = SHORTANSWER;
                $question->name = $this->hotpot_get_title($xml, $x, true);

                $question->questiontext = $this->hotpot_prepare_str($text);
                $question->answer = array($this->hotpot_prepare_str($answer));
                $question->fraction = array(1);
                $question->feedback = array('');

                $questions[] = $question;
            }
            $x++;
        }
    }

    function process_jmatch(&$xml, &$questions) {
        // define default grade (per matched pair)
        $defaultgrade = 1;
        $match_count = 0;

        // xml tags to the start of the matching exercise
        $tags = 'data,matching-exercise';

        $x = 0;
        while (($exercise = "[$x]['#']") && $xml->xml_value($tags, $exercise)) {
            // there is usually only one exercise in a file

            if (method_exists($this, 'defaultquestion')) {
                $question = $this->defaultquestion();
            } else {
                $question = new stdClass();
                $question->usecase = 0; // Ignore case
                $question->image = "";  // No images with this format
            }
            $question->qtype = MATCH;
            $question->name = $this->hotpot_get_title($xml, $x);

            $question->questiontext = $this->hotpot_get_reading($xml);
            $question->questiontext .= $this->hotpot_get_instructions($xml);

            $question->subquestions = array();
            $question->subanswers = array();
            $p = 0;
            while (($pair = $exercise."['pair'][$p]['#']") && $xml->xml_value($tags, $pair)) {
                $left = $xml->xml_value($tags, $pair."['left-item'][0]['#']['text'][0]['#']");
                $right = $xml->xml_value($tags, $pair."['right-item'][0]['#']['text'][0]['#']");
                if ($left && $right) {
                    $match_count++;
                    $question->subquestions[$p] = $this->hotpot_prepare_str($left);
                    $question->subanswers[$p] = $this->hotpot_prepare_str($right);
                }
                $p++;
            }
            $question->defaultgrade = $match_count * $defaultgrade;
            $questions[] = $question;
            $x++;
        }
    }

    function process_jmix(&$xml, &$questions) {
        // define default grade (per segment)
        $defaultgrade = 1;
        $segment_count = 0;

        // xml tags to the start of the jumbled order exercise
        $tags = 'data,jumbled-order-exercise';

        $x = 0;
        while (($exercise = "[$x]['#']") && $xml->xml_value($tags, $exercise)) {
            // there is usually only one exercise in a file

            if (method_exists($this, 'defaultquestion')) {
                $question = $this->defaultquestion();
            } else {
                $question = new stdClass();
                $question->usecase = 0; // Ignore case
                $question->image = "";  // No images with this format
            }
            $question->qtype = SHORTANSWER;
            $question->name = $this->hotpot_get_title($xml, $x);

            $question->answer = array();
            $question->fraction = array();
            $question->feedback = array();

            $i = 0;
            $segments = array();
            while ($segment = $xml->xml_value($tags, $exercise."['main-order'][0]['#']['segment'][$i]['#']")) {
                $segments[] = $this->hotpot_prepare_str($segment);
                $segment_count++;
                $i++;
            }
            $answer = implode(' ', $segments);

            $this->hotpot_seed_RNG();
            shuffle($segments);

            $question->questiontext = $this->hotpot_get_reading($xml);
            $question->questiontext .= $this->hotpot_get_instructions($xml);
            $question->questiontext .= ' &nbsp; <NOBR><B>[ &nbsp; '.implode(' &nbsp; ', $segments).' &nbsp; ]</B></NOBR>';

            $a = 0;
            while (!empty($answer)) {
                $question->answer[$a] = $answer;
                $question->fraction[$a] = 1;
                $question->feedback[$a] = '';
                $answer = $this->hotpot_prepare_str($xml->xml_value($tags, $exercise."['alternate'][$a]['#']"));
                $a++;
            }
            $question->defaultgrade = $segment_count * $defaultgrade;
            $questions[] = $question;
            $x++;
        }
    }
    function process_jquiz(&$xml, &$questions) {
        // define default grade (per question)
        $defaultgrade = 1;

        // xml tags to the start of the questions
        $tags = 'data,questions';

        $x = 0;
        while (($exercise = "[$x]['#']") && $xml->xml_value($tags, $exercise)) {
            // there is usually only one 'questions' object in a single exercise

            $q = 0;
            while (($question_record = $exercise."['question-record'][$q]['#']") && $xml->xml_value($tags, $question_record)) {

                if (method_exists($this, 'defaultquestion')) {
                    $question = $this->defaultquestion();
                } else {
                    $question = new stdClass();
                    $question->usecase = 0; // Ignore case
                    $question->image = "";  // No images with this format
                }
                $question->defaultgrade = $defaultgrade;
                $question->name = $this->hotpot_get_title($xml, $q, true);

                $text = $xml->xml_value($tags, $question_record."['question'][0]['#']");
                $question->questiontext = $this->hotpot_prepare_str($text);

                if ($xml->xml_value($tags, $question_record."['answers']")) {
                    // HP6 JQuiz
                    $answers = $question_record."['answers'][0]['#']";
                } else {
                    // HP5 JBC or JQuiz
                    $answers = $question_record;
                }
                if($xml->xml_value($tags, $question_record."['question-type']")) {
                    // HP6 JQuiz
                    $type = $xml->xml_value($tags, $question_record."['question-type'][0]['#']");
                    //  1 : multiple choice
                    //  2 : short-answer
                    //  3 : hybrid
                    //  4 : multiple select
                } else {
                    // HP5
                    switch ($xml->quiztype) {
                        case 'jbc':
                            $must_select_all = $xml->xml_value($tags, $question_record."['must-select-all'][0]['#']");
                            if (empty($must_select_all)) {
                                $type = 1; // multichoice
                            } else {
                                $type = 4; // multiselect
                            }
                            break;
                        case 'jquiz':
                            $type = 2; // shortanswer
                            break;
                        default:
                            $type = 0; // unknown
                    }
                }
                $question->qtype = ($type==2 ? SHORTANSWER : MULTICHOICE);
                $question->single = ($type==4 ? 0 : 1);

                // workaround required to calculate scores for multiple select answers
                $no_of_correct_answers = 0;
                if ($type==4) {
                    $a = 0;
                    while (($answer = $answers."['answer'][$a]['#']") && $xml->xml_value($tags, $answer)) {
                        $correct = $xml->xml_value($tags, $answer."['correct'][0]['#']");
                        if (empty($correct)) {
                            // do nothing
                        } else {
                            $no_of_correct_answers++;
                        }
                        $a++;
                    }
                }
                $a = 0;
                $question->answer = array();
                $question->fraction = array();
                $question->feedback = array();
                $aa = 0;
                $correct_answers = array();
                $correct_answers_all_zero = true;
                while (($answer = $answers."['answer'][$a]['#']") && $xml->xml_value($tags, $answer)) {
                    $correct = $xml->xml_value($tags, $answer."['correct'][0]['#']");
                    if (empty($correct)) {
                        $fraction = 0;
                    } else if ($type==4) { // multiple select
                        // strange behavior if the $fraction isn't exact to 5 decimal places
                        $fraction = round(1/$no_of_correct_answers, 5);
                    } else {
                        if ($xml->xml_value($tags, $answer."['percent-correct']")) {
                            // HP6 JQuiz
                            $percent = $xml->xml_value($tags, $answer."['percent-correct'][0]['#']");
                            $fraction = $percent/100;
                        } else {
                            // HP5 JBC or JQuiz
                            $fraction = 1;
                        }
                    }
                    $answertext = $this->hotpot_prepare_str($xml->xml_value($tags, $answer."['text'][0]['#']"));
                    if ($answertext!='') {
                        $question->answer[$aa] = $answertext;
                        $question->fraction[$aa] = $fraction;
                        $question->feedback[$aa] = $this->hotpot_prepare_str($xml->xml_value($tags, $answer."['feedback'][0]['#']"));
                        if ($correct) {
                            if ($fraction) {
                                $correct_answers_all_zero = false;
                            }
                            $correct_answers[] = $aa;
                        }
                        $aa++;
                    }
                    $a++;
                }
                if ($correct_answers_all_zero) {
                    // correct answers all have score of 0%, 
                    // so reset score for correct answers 100%
                    foreach ($correct_answers as $aa) {
                        $question->fraction[$aa] = 1;
                    }
                }
                // add a sanity check for empty questions, see MDL-17779
                if (!empty($question->questiontext)) {
                    $questions[] = $question;
                }
                $q++;
            }
            $x++;
        }
    }

    function hotpot_seed_RNG() {
        // seed the random number generator
        static $HOTPOT_SEEDED_RNG = FALSE;
        if (!$HOTPOT_SEEDED_RNG) {
            srand((double) microtime() * 1000000);
            $HOTPOT_SEEDED_RNG = TRUE;
        }
    }
    function hotpot_get_title(&$xml, $x, $flag=false) {
        $title = $xml->xml_value('data,title');
        if ($x || $flag) {
            $title .= ' ('.($x+1).')';
        }
        return $this->hotpot_prepare_str($title);
    }
    function hotpot_get_instructions(&$xml) {
        $text = $xml->xml_value('hotpot-config-file,instructions');
        if (empty($text)) {
            $text = "Hot Potatoes $xml->quiztype";
        }
        return $this->hotpot_prepare_str($text);
    }
    function hotpot_get_reading(&$xml) {
        $str = '';
        $tags = 'data,reading';
        if ($xml->xml_value("$tags,include-reading")) {
            if ($title = $xml->xml_value("$tags,reading-title")) {
                $str .= "<H3>$title</H3>";
            }
            if ($text = $xml->xml_value("$tags,reading-text")) {
                $str .= "<P>$text</P>";
            }
        }
        return $this->hotpot_prepare_str($str);
    }
    function hotpot_prepare_str($str) {
        // convert html entities to unicode and add slashes
        $str = preg_replace('/&#x([0-9a-f]+);/ie', "hotpot_charcode_to_utf8(hexdec('\\1'))", $str);
        $str = preg_replace('/&#([0-9]+);/e', "hotpot_charcode_to_utf8(\\1)", $str);
        return addslashes($str);
    }
} // end class

function hotpot_charcode_to_utf8($charcode) {
    // thanks to Miguel Perez: http://jp2.php.net/chr (19-Sep-2007)
    if ($charcode <= 0x7F) {
        // ascii char (roman alphabet + punctuation)
        return chr($charcode);
    }
    if ($charcode <= 0x7FF) {
        // 2-byte char
        return chr(($charcode >> 0x06) + 0xC0).chr(($charcode & 0x3F) + 0x80);
    }
    if ($charcode <= 0xFFFF) {
        // 3-byte char
        return chr(($charcode >> 0x0C) + 0xE0).chr((($charcode >> 0x06) & 0x3F) + 0x80).chr(($charcode & 0x3F) + 0x80);
    }
    if ($charcode <= 0x1FFFFF) {
        // 4-byte char
        return chr(($charcode >> 0x12) + 0xF0).chr((($charcode >> 0x0C) & 0x3F) + 0x80).chr((($charcode >> 0x06) & 0x3F) + 0x80).chr(($charcode & 0x3F) + 0x80);
    }
    // unidentified char code !!
    return ' '; 
}

function hotpot_convert_relative_urls($str, $baseurl, $filename) {
    $tagopen = '(?:(<)|(&lt;)|(&amp;#x003C;))'; // left angle bracket
    $tagclose = '(?(2)>|(?(3)&gt;|(?(4)&amp;#x003E;)))'; //  right angle bracket (to match left angle bracket)

    $space = '\s+'; // at least one space
    $anychar = '(?:[^>]*?)'; // any character

    $quoteopen = '("|&quot;|&amp;quot;)'; // open quote
    $quoteclose = '\\5'; //  close quote (to match open quote)

    $replace = "hotpot_convert_relative_url('".$baseurl."', '".$filename."', '\\1', '\\6', '\\7')";

    $tags = array('script'=>'src', 'link'=>'href', 'a'=>'href','img'=>'src','param'=>'value', 'object'=>'data', 'embed'=>'src');
    foreach ($tags as $tag=>$attribute) {
        if ($tag=='param') {
            $url = '\S+?\.\S+?'; // must include a filename and have no spaces
        } else {
            $url = '.*?';
        }
        $search = "%($tagopen$tag$space$anychar$attribute=$quoteopen)($url)($quoteclose$anychar$tagclose)%ise";
        $str = preg_replace($search, $replace, $str);
    }

    return $str;
}
