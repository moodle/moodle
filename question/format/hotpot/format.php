<?PHP // $Id$
////////////////////////////////////////////////////////////////////////////
/// Hotpotatoes 5.0 and 6.0 Format
///
/// This Moodle class provides all functions necessary to import
///                                      (export is not implemented ... yet)
///
////////////////////////////////////////////////////////////////////////////

// Based on default.php, included by ../import.php

class qformat_hotpot extends qformat_default {

    function provide_import() {
        return true;
    }

    function readquestions ($lines) {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().

        // set baseurl
        global $CFG;
        if ($CFG->slasharguments) {
            $baseurl = "$CFG->wwwroot/file.php/{$this->course->id}/";
        } else {
            $baseurl = "$CFG->wwwroot/file.php?file=/{$this->course->id}/";
        }

        // get import file name
        global $params;
        if (isset($params) && !empty($params->choosefile)) {
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
        return $questions;
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
                global $course; // set in mod/quiz/import.php
                $question->course = $course->id;
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
                        if ($text) {
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

            // define total grade for this exercise
            $question->defaultgrade = $gap_count * $defaultgrade;

            $questions[] = $question;
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
                        $question->answer[] = $answertext;
                        $question->fraction[] = $fraction;
                        $question->feedback[] = $this->hotpot_prepare_str($xml->xml_value($tags, $answer."['feedback'][0]['#']"));
                    }
                    $a++;
                }
                $questions[] = $question;
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
        $str = preg_replace('/&#[x0-9A-F]+;/ie', "html_entity_decode('\\0',ENT_NOQUOTES,'UTF-8')", $str);
        return addslashes($str);
    }
} // end class

// get the standard XML parser supplied with Moodle
require_once("$CFG->libdir/xmlize.php");

class hotpot_xml_tree {
    function hotpot_xml_tree($str, $xml_root='') {
        if (empty($str)) {
            $this->xml =  array();
        } else {
            // encode htmlentities in JCloze
            $this->encode_cdata($str, 'gap-fill');
            // encode as utf8
            if (empty($CFG->unicodedb)) {
                $str = utf8_encode($str);
            }
            // xmlize (=convert xml to tree)
            $this->xml =  xmlize($str, 0);
        }
        $this->xml_root = $xml_root;
    }
    function xml_value($tags, $more_tags="[0]['#']") {

        $tags = empty($tags) ? '' : "['".str_replace(",", "'][0]['#']['", $tags)."']";
        eval('$value = &$this->xml'.$this->xml_root.$tags.$more_tags.';');

        if (is_string($value)) {
            if (empty($CFG->unicodedb)) {
                $value = utf8_decode($value);
            }

            // decode angle brackets and ampersands
            $value = strtr($value, array('&#x003C;'=>'<', '&#x003E;'=>'>', '&#x0026;'=>'&'));

            // remove white space between <table>, <ul|OL|DL> and <OBJECT|EMBED> parts 
            // (so it doesn't get converted to <br />)
            $htmltags = '('
            .    'TABLE|/?CAPTION|/?COL|/?COLGROUP|/?TBODY|/?TFOOT|/?THEAD|/?TD|/?TH|/?TR'
            .    '|OL|UL|/?LI'
            .    '|DL|/?DT|/?DD'
            .    '|EMBED|OBJECT|APPLET|/?PARAM'
            //.    '|SELECT|/?OPTION'
            //.    '|FIELDSET|/?LEGEND'
            //.    '|FRAMESET|/?FRAME'
            .    ')'
            ;
            $search = '#(<'.$htmltags.'[^>]*'.'>)\s+'.'(?='.'<'.')#is';
            $value = preg_replace($search, '\\1', $value);

            // replace remaining newlines with <br />
            $value = str_replace("\n", '<br />', $value);

            // encode unicode characters as HTML entities
            // (in particular, accented charaters that have not been encoded by HP)

            // multibyte unicode characters can be detected by checking the hex value of the first character
            //    00 - 7F : ascii char (roman alphabet + punctuation)
            //    80 - BF : byte 2, 3 or 4 of a unicode char
            //    C0 - DF : 1st byte of 2-byte char
            //    E0 - EF : 1st byte of 3-byte char
            //    F0 - FF : 1st byte of 4-byte char
            // if the string doesn't match the above, it might be
            //    80 - FF : single-byte, non-ascii char
            $search = '#('.'[\xc0-\xdf][\x80-\xbf]'.'|'.'[\xe0-\xef][\x80-\xbf]{2}'.'|'.'[\xf0-\xff][\x80-\xbf]{3}'.'|'.'[\x80-\xff]'.')#se';
            $value = preg_replace($search, "hotpot_utf8_to_html_entity('\\1')", $value);
        }
        return $value;
    }
    function encode_cdata(&$str, $tag) {

        // conversion tables
        static $HTML_ENTITIES = array(
            '&apos;' => "'",
            '&quot;' => '"',
            '&lt;'   => '<',
            '&gt;'   => '>',
            '&amp;'  => '&',
        );
        static $ILLEGAL_STRINGS = array(
            "\r"  => '',
            "\n"  => '&lt;br /&gt;',
            ']]>' => '&#93;&#93;&#62;',
        );

        // extract the $tag from the $str(ing), if possible
        $pattern = '|(^.*<'.$tag.'[^>]*)(>.*<)(/'.$tag.'>.*$)|is';
        if (preg_match($pattern, $str, $matches)) {

            // encode problematic CDATA chars and strings
            $matches[2] = strtr($matches[2], $ILLEGAL_STRINGS);


            // if there are any ampersands in "open text"
            // surround them by CDATA start and end markers
            // (and convert HTML entities to plain text)
            $search = '/>([^<]*&[^<]*)</e';
            $replace = '"><![CDATA[".strtr("$1", $HTML_ENTITIES)."]]><"';
            $matches[2] = preg_replace($search, $replace, $matches[2]);

            $str = $matches[1].$matches[2].$matches[3];
        }
    }
}

function hotpot_utf8_to_html_entity($char) {
    // http://www.zend.com/codex.php?id=835&single=1

    // array used to figure what number to decrement from character order value 
    // according to number of characters used to map unicode to ascii by utf-8 
    static $HOTPOT_UTF8_DECREMENT = array(
        1=>0, 2=>192, 3=>224, 4=>240
    );

    // the number of bits to shift each character by 
    static $HOTPOT_UTF8_SHIFT = array(
        1=>array(0=>0),
        2=>array(0=>6,  1=>0),
        3=>array(0=>12, 1=>6,  2=>0),
        4=>array(0=>18, 1=>12, 2=>6, 3=>0)
    );
     
    $dec = 0; 
    $len = strlen($char);
    for ($pos=0; $pos<$len; $pos++) {
        $ord = ord ($char{$pos});
        $ord -= ($pos ? 128 : $HOTPOT_UTF8_DECREMENT[$len]); 
        $dec += ($ord << $HOTPOT_UTF8_SHIFT[$len][$pos]); 
    }
    return '&#x'.sprintf('%04X', $dec).';';
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

function hotpot_convert_relative_url($baseurl, $filename, $opentag, $url, $closetag, $stripslashes=true) {
    if ($stripslashes) {
        $opentag = stripslashes($opentag);
        $url = stripslashes($url);
        $closetag = stripslashes($closetag);
    }

    // catch <PARAM name="FlashVars" value="TheSound=soundfile.mp3">
    //    ampersands can appear as "&", "&amp;" or "&amp;#x0026;amp;"
    if (preg_match('|^'.'\w+=[^&]+'.'('.'&((amp;#x0026;)?amp;)?'.'\w+=[^&]+)*'.'$|', $url)) {
        $query = $url;
        $url = '';
        $fragment = '';

    // parse the $url into $matches
    //    [1] path
    //    [2] query string, if any
    //    [3] anchor fragment, if any
    } else if (preg_match('|^'.'([^?]*)'.'((?:\\?[^#]*)?)'.'((?:#.*)?)'.'$|', $url, $matches)) {
        $url = $matches[1];
        $query = $matches[2];
        $fragment = $matches[3];

    // there appears to be no query or fragment in this url
    } else {
        $query = '';
        $fragment = '';
    }

    if ($url) {
        $url = hotpot_convert_url($baseurl, $filename, $url, false);
    }

    if ($query) {
        $search = '#'.'(file|src|thesound)='."([^&]+)".'#ise';
        $replace = "'\\1='.hotpot_convert_url('".$baseurl."','".$filename."','\\2')";
        $query = preg_replace($search, $replace, $query);
    }

    $url = $opentag.$url.$query.$fragment.$closetag;

    return $url;
}

function hotpot_convert_url($baseurl, $filename, $url, $stripslashes=true) {
    // maintain a cache of converted urls
    static $HOTPOT_RELATIVE_URLS = array();

    if ($stripslashes) {
        $url = stripslashes($url);
    }

    // is this an absolute url? (or javascript pseudo url)
    if (preg_match('%^(http://|/|javascript:)%i', $url)) {
        // do nothing

    // has this relative url already been converted?
    } else if (isset($HOTPOT_RELATIVE_URLS[$url])) {
        $url = $HOTPOT_RELATIVE_URLS[$url];

    } else {
        $relativeurl = $url;

        // get the subdirectory, $dir, of the quiz $filename
        $dir = dirname($filename);

        // allow for leading "./" and "../"
        while (preg_match('|^(\.{1,2})/(.*)$|', $url, $matches)) {
            if ($matches[1]=='..') {
                $dir = dirname($dir);
            }
            $url = $matches[2];
        }

        // add subdirectory, $dir, to $baseurl, if necessary
        if ($dir && $dir<>'.') {
            $baseurl .= "$dir/";
        }

        // prefix $url with $baseurl
        $url = "$baseurl$url";

        // add url to cache
        $HOTPOT_RELATIVE_URLS[$relativeurl] = $url;
    }
    return $url;
}

// allow importing in Moodle v1.4 (and less)
// same core functions but different class name
if (!class_exists("quiz_file_format")) {
    class quiz_file_format extends qformat_default {
        function readquestions ($lines) {
            $format = new qformat_hotpot();
            return $format->readquestions($lines);
        }
    }
}

?>
