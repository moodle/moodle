<?php // $Id$
/*
**
** Examview 4.0 XML format into Moodle 1.4.3
** Author: Dan McCuaig ( dmccuaig@wvc.edu )
**
** @TODO:
**   Take care of odd unicode character mapping (ex: curly quotes)
**	 Image and table support
**   Formatting support
**   Support of rejoinders
**
** $Log$
** Revision 1.1  2005/05/16 08:12:40  thepurpleblob
** Added support for Examview import.
**
**
*/

// Based on default.php, included by ../import.php

require_once("$CFG->libdir/xmlize.php");
//require_once("xmlize.php");

/*
define("SHORTANSWER",   "1");
define("TRUEFALSE",     "2");
define("MULTICHOICE",   "3");
define("MATCH",         "5");
define("DESCRIPTION",   "7");
define("NUMERICAL",     "8");
define("MULTIANSWER",   "9");
define("CALCULATED",   "10");
*/

class quiz_format_examview extends quiz_default_format {
    
    var $qtypes = array('tf' => TRUEFALSE,
    'mc' => MULTICHOICE,
    'yn' => TRUEFALSE,
    'co' => SHORTANSWER,
    'ma' => MATCH,
    'mtf' => 99,
    'nr' => NUMERICAL,
    'pr' => 99,
    'es' => 99,
    'ca' => 99,
    'ot' => 99
    );
    
    var $matching_questions = array();

    function provide_import() {
        return true;
    }
    
    function print_matching_questions()
    {
        foreach($this->matching_questions as $key => $value) {
            print("$key => $value->questiontext<BR>");
            print("Questions:<UL>");
            foreach($value->subquestions as $qkey => $qvalue) {
                print("<LI>$qkey => $qvalue</LI>");
            }
            print("</UL>");
            print("Choices:<UL>");
            foreach($value->subchoices as $ckey => $cvalue) {
                print("<LI>$ckey => $cvalue</LI>");
            }
            print("</UL>");
            print("Answers:<UL>");
            foreach($value->subanswers as $akey => $avalue) {
                print("<LI>$akey => $avalue</LI>");
            }
            print("</UL>");
        }
    }
    
    function parse_matching_groups($matching_groups)
    {
        if (empty($matching_groups)) {
            return;
        }
        foreach($matching_groups as $match_group) {
            $newgroup = NULL;
            $groupname = trim($match_group['@']['name']);
            $questiontext = $this->ArrayTagToString($match_group['#']['text']['0']['#']);
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
        $phrase = trim($qrec['text']['0']['#']);
        $answer = trim($qrec['answer']['0']['#']);
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
            $question = NULL;
			$htmltext = $this->htmlPrepare($match_group->questiontext);
			$htmltext = addslashes($htmltext);
            $question->questiontext = $htmltext;
            $question->name = $question->questiontext;
            $question->qtype = MATCH;
            $question->defaultgrade = 1;
            $question->image = "";
            // No images with this format
            //		print($question->questiontext.' '.$question->id."<BR>");
            
            $question->subquestions = array();
            $question->subanswers = array();
            foreach($match_group->subquestions as $key => $value) {
			    $htmltext = $this->htmlPrepare($value);
				$htmltext = addslashes($htmltext);
                $question->subquestions[] = $htmltext;

                $htmltext = $this->htmlPrepare($match_group->subanswers[$key]);
				$htmltext = addslashes($htmltext);
                $question->subanswers[] = $htmltext;
            }
            $questions[] = $question;
        }
    }
    
	// cleans unicode characters from string
	// add to the array unicode_array as necessary
	function cleanUnicode($text) {
		//$unicode_array = array(	"&#2019;" => "'");
		//return strtr($text, $unicode_array);		
		return str_replace('&#x2019;', "'", $text);
	}
	
    function readquestions($lines)
    {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().
        
        $questions = array();
        $currentquestion = array();
        
        $text = implode($lines, ' ');
		$text = $this->cleanUnicode($text);

        $xml = xmlize($text, 0);
        $this->parse_matching_groups($xml['examview']['#']['matching-group']);
        
        $questionNode = $xml['examview']['#']['question'];
        foreach($questionNode as $currentquestion) {
            if ($question = $this->readquestion($currentquestion)) {
                $questions[] = $question;
            }
        }
        
        //    print('<hr>');
        //    $this->print_matching_questions();
        $this->process_matches($questions);
        //    print('<hr>');
        
        return $questions;
    }
    // end readquestions
    
    function htmlPrepare($htmltext)
    {
        $text = trim($text);
        $text = htmlentities($htmltext, ENT_QUOTES);
        //$htmltext = nl2br($text);
        return $text;
    }
    
    function ArrayTagToString($aTag)
    {
        if (!is_array($aTag)) {
            return $aTag;
        }
        $out = '';
        foreach($aTag as $key => $value) {
            if (is_array($value)) {
                $out = $out.$this->ArrayTagToString($value);
            } else {
                $out = $value;
            }
        }
        return $out;
    }
    
    
    function readquestion($qrec)
    {
        
        $type = trim($qrec['@']['type']);
        $question = NULL;
        $question->qtype = $this->qtypes[$type];
        $question->defaultgrade = 1;
        $question->single = 1;
        // Only one answer is allowed
        $question->image = "";
        // No images with this format
        $htmltext = $this->ArrayTagToString($qrec['#']['text'][0]['#']);
        $htmltext = $this->htmlPrepare($htmltext);
        $htmltext = addslashes($htmltext);
        $question->questiontext = $htmltext;
        $question->name = $question->questiontext;
        
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
                
                $question->answer[$key] = $this->htmlPrepare($value[0]['#']);
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
        $answer = trim($qrec['answer'][0]['#']);
        $answers = explode("\n",$answer);
        
        foreach($answers as $key => $value) {
            $value = trim($value);
            if (strlen($value) > 0) {
                $question->answer[$key] = addslashes($value);
                $question->fraction[$key] = 1;
                $question->feedback[$key] = "Correct";
            }
        }
        return $question;
    }
    
    function parse_nr($qrec, $question)
    {
        $answer = trim($qrec['answer'][0]['#']);
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

?>
