<?php // $Id$
//
///////////////////////////////////////////////////////////////
// imports all the questions from an XML question category file into a unique close/embedded question
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php

require_once( "$CFG->libdir/xmlize.php" );
    global $CFG, $langfile;
    $langfile = $CFG->dirroot.'/question/format/clozejr/lang/';

class qformat_clozejr extends qformat_default {
    function provide_import() {
        return false;
    }
    function provide_export() {
        return true;
    }

    // IMPORT FUNCTIONS START HERE

    function importprocess($lines) {
        $questions = $this->readquestions($lines);
        // Now process and store the unique cloze question
        $question = $questions[0];
        $question->category = $this->category->id;
        $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
        if (!$question->id = insert_record("question", $question)) {
            error( get_string('cannotinsert','quiz') );
        }
        $this->questionids[] = $question->id;
        // Now to save all the answers and type-specific options
        global $QTYPES;
		$question->course = '';
        $result = $QTYPES[$question->qtype]
                ->save_question_options($question);

        if (!empty($result->error)) {
            notify($result->error);
            return false;
        }

        if (!empty($result->notice)) {
            notify($result->notice);
            return true;
        }
        // Give the question a unique version stamp determined by question_hash()
        set_field('question', 'version', question_hash($question), 'id', $question->id);

        return true;
    }
    
    function import_text( $text ) {
        $data = $text[0]['#'];
        $data = html_entity_decode( $data );
        return trim( $data );
    }

    function import_answer( $answer ) {
    // import answer part of question

        $fraction = $answer['@']['fraction'];
        $text = $this->import_text( $answer['#']['text']);
        $feedback = $this->import_text( $answer['#']['feedback'][0]['#']['text'] );

        $ans = null;
        $ans->answer = $text;
        $ans->fraction = $fraction / 100;
        $ans->feedback = $feedback;
  
        return $ans;
    }
    
    function import_question( $question ) {
    // run through the answers
        $answers = $question['#']['answer'];
		$an = '';  
        foreach ($answers as $answer) {
            $ans = $this->import_answer( $answer );
            $fraction = $ans->fraction * 100;
            $an.="~%".$fraction."%".$ans->answer."#".$ans->feedback;
        }
        return $an;
    }
    
    function import_numerical( $question ) {
    // import numerical question
        // get answers array
        $answers = $question['#']['answer'];
        $an = '';
        foreach ($answers as $answer) {
            $fraction = trim($answer['#']['fraction'][0]['#']);
            $ans = trim($answer['#'][0]);
            $tolerance = trim($answer['#']['tolerance'][0]['#']);
            $feedback = addslashes(trim(( $answer['#']['feedback'][0]['#']['text'][0]['#'] )));
            $percent = $fraction * 100;
            $an.="~%$percent%".$ans.":".$tolerance."#".$feedback;
        }
        return $an;
    }

    function reverse_strrchr($haystack, $needle)
    {
       $pos = strrpos($haystack, $needle);
       if($pos === false) {
           return $haystack;
       }
       return substr($haystack, 0, $pos + 0).'<br />';
    }

    function readquestions($lines) {
        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        global $CFG, $langfile;
        $xml = xmlize( $lines, 0 ); 
        // set up array to hold all our questions
        $questions = array();
        $subquestions = array();
        $clozequestionname = $xml['QUESTION_CATEGORY']['#']['NAME'][0]['#'];
        notify(get_string( 'displayclozequestion','clozejr','',$langfile) );

        // iterate through questions
        $count = 0;
        $numberthequestions = false;
        foreach ($xml['QUESTION_CATEGORY']['#']['question'] as $question) {
            $count++;
            $questiontype = $question['@']['type'];
            $an = null;
            $defaultgrade = $question['#']['DEFAULTGRADE'][0]['#'];
            switch($questiontype) {
                CASE 'numerical' :
                    $an = $this->import_numerical( $question );
                    break;
                CASE 'description' :
                    break;
                CASE 'multichoice':
                CASE 'regexp':
                CASE 'shortanswer':
                    $an = addslashes( $this->import_question($question) ); 
                    break;
                CASE 'matching' :
                CASE 'cloze' :
                default:
                    $an = null;
            }
            // stick the result in the $questions array
			$isnewline = false;
            if ($an OR ($questiontype == 'description')) {
				//addslashes added JR 29 MAR 2007
                $qtext = addslashes($this->import_text( $question['#']['questiontext'][0]['#']['text'] ) );
                $qtext = ereg_replace("(</?p>|</?br ?/?>|\n)", "", $qtext);
                // remove all <p></p> tags and potential newline \n from question text ... but keeps existing <hr> tags if any
				$newline = '';
				$pos = strrpos($qtext, '<hr');
				if ($pos) {
					$qtext = substr($qtext, 0, $pos + 0);
					$newline = '<br />';
				}

                // if question text ends with horizontal rule, then replace <hr> with <br />

                $qtext_array = explode('***', $qtext,2);
                // replace "blank" *** sequences in original question text with question slot
				
                $name = $this->import_text( $question['#']['name'][0]['#']['text'] );
                if ($count === 1 and substr($name, 2, 1) == '-') {
                // only number questions if the very first one is something like '01-'
                    $numberthequestions = true;
                }
                $n = "00";
                $nb = substr($name, 0, 2);
                // get first 2 letters of question name - if 01, 02, ... 99, then change into 01- ... 99-
                if ($nb + 0 != 0) {
                        $n = $nb.'- ';
                    } else {
						$n = '';
				}
                if ($questiontype == 'description') {
                    $subquestions[] = $n.$qtext.$newline;
                } else {
                    $subquestions[] = $n.$qtext_array[0]."{".$defaultgrade.":".strtoupper($questiontype)
						.":".$an."}".$qtext_array[1].$newline;
                }
            }
        }
        sort($subquestions);
        if ($numberthequestions == false) {
            $count = 0;
            foreach ($subquestions as $question) {
                $subquestions[$count] = substr($question,3);
                $count++;
            }
        }
        $subquestions = implode(" ",$subquestions);
        $qo = qtype_multianswer_extract_question($subquestions);
        $qo->name = $clozequestionname;
        // the cloze question is named after the name of the question_category from which the questions were exported
        $questions[] = $qo;
        //now replace all {#} blanks in question text with correct answwer (if more than one, first met is selected)
        $qtext = $qo->questiontext;
        $countquestions = 1;
        foreach ($qo->options->questions as $question) {
            $count = 0;
            foreach ($question->answer as $answer) {
                if ($question->fraction[$count] == 1) {
                    $qtext = ereg_replace("\{\#".$countquestions."\}", "{".$answer."}", $qtext);
                    break;
                }
                $count++;
            }
            $countquestions++;
        }
        echo "<p>".stripslashes($qtext)."</p>";
        // print it here rather than in importprocess()
        return $questions;
    }

    // EXPORT FUNCTIONS START HERE

   function exportprocess() {
    /// Exports a given category.  There's probably little need to change this
        global $CFG, $langfile;

        // create a directory for the exports (if not already existing)
        if (! $export_dir = make_upload_directory($this->question_get_export_dir())) {
              error( get_string('cannotcreatepath','quiz',$export_dir) );
        }
        $path = $CFG->dataroot.'/'.$this->question_get_export_dir();

        // get the questions (from database) in this category
        // only get q's with no parents (no cloze subquestions specifically)
        $questions = get_questions_category( $this->category, true );
        notify( get_string('displayquestions', 'clozejr', '', $langfile) );

        if (!count($questions)) {
            notify( get_string('noquestions','quiz') );
            return false;
        }
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";

        // iterate through questions
        foreach($questions as $question) {
            // do not export hidden questions
            if (!empty($question->hidden)) {
                continue;
            }

            // do not export questions of these types 
            $questiontype = $question->qtype; 
			$questiontypename = $this->get_qtype($questiontype);
            if ($questiontype==RANDOM or $questiontype==MULTIANSWER or $questiontype==MATCH or $questiontype==TRUEFALSE or $questiontype==ESSAY or $questiontype==RANDOMSAMATCH) {
                notify( get_string( 'clozetypeunsupported', 'clozejr', '', $langfile) );
                continue;
            }

        // export the question displaying message
        $count++;
        $qtext = stripslashes($question->questiontext);
        $qtext = ereg_replace("</?p>", "", $qtext);
        
        echo "<p><b>$count</b>. ".$qtext."</p>";
        $expout .= $this->writequestion( $question ) . "\n";
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
        $result = $this->importprocess ($expout);
        return true;
    }
	
	    function get_qtype( $type_id ) {
    // translates question type code number into actual name
   
        switch( $type_id ) {
        case TRUEFALSE:
            $name = 'truefalse';
            break;
        case MULTICHOICE:
            $name = 'multichoice';
            break;
        case SHORTANSWER:
            $name = 'shortanswer';
            break;
        case 'regexp':
            $name = 'regexp';
            break;
        case NUMERICAL:
            $name = 'numerical';
            break;
        case MATCH:
            $name = 'matching';
            break;
        case DESCRIPTION:
            $name = 'description';
            break;
        case ESSAY:
            $name = 'essay';
            break;
        case MULTIANSWER:
            $name = 'cloze';
            break;
        default:
            $name = 'unknown';
        }
        return $name;
    }


    function get_single( $id ) {
    // translate single value into something sensible

        switch( $id ) {
        case 0:
            $name = "false";
            break;
        case 1:
            $name = "true";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    function writetext( $raw, $ilev=0, $short=true) {
    // generates <text></text> tags, processing raw text therein 
    // $ilev is the current indent level
    // $short=true sticks it on one line
        $indent = str_repeat( "  ",$ilev );

        // encode the text to 'disguise' HTML content 
        $raw = htmlspecialchars( $raw );

        if ($short) {
            $xml = "$indent<text>$raw</text>\n";
        }
        else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers
        global $categoryname;
        $categoryname = '';
        $categoryid = optional_param('category',0, PARAM_INT);
        if (!$categories = get_records_select("question_categories", "id = '{$categoryid}'")) {
            error("Could not find any question categories!");
        }
        $categories = add_indented_names($categories);
        foreach ($categories as $key => $cat) {
            $categoryname = $cat->name;
        }
        notify ("categoryname = $categoryname");
        $categoryid = 1;
        $content = "<?xml version=\"1.0\"?>\n" .
                       "<QUESTION_CATEGORY>\n" .
                       "<ID>".$categoryid."</ID>\n" .
                       "<NAME>".$categoryname."</NAME>\n" .
                       $content . "\n" .
                       "</QUESTION_CATEGORY>";
        return $content;
    }

    function exportpostprocess() {
    /// Does any post-processing that may be desired
        global $langfile, $categoryname;
        notify( get_string('donotclick', 'clozejr', $categoryname, $langfile) );
        return true;
    }

    function writequestion( $question ) {
    // turns question into string
    // initial string;
        $expout = "";
        $questiontype = $question->qtype;
        switch( $questiontype ) {
            case MULTICHOICE:
                if ($this->get_single($question->options->single) == 'true') {
                    break;
                }
            case SHORTANSWER:
            case 'regexp':
            case DESCRIPTION:
            case NUMERICAL:
                break;
            case MATCH:
            case MULTIANSWER: //cloze/embedded
            case RANDOMSAMATCH:
            case TRUEFALSE:
            default:
                notify( get_string( 'clozetypeunsupported','clozejr',$questiontype) );
        }

        if ($questiontype == 'unsupported') {
            return null;
        }
        // add comment
        $expout .= "\n\n<!-- question: $question->id  -->\n";
        $defaultgrade = $question->defaultgrade;
        // add opening tag
        // generates specific header for Cloze type question

        $nametext = $this->writetext( $question->name );
        $qtformat = "";
        $questiontext = $this->writetext( $question->questiontext );
        $expout .= "  <question type=\"$questiontype\">\n";   
        $expout .= "    <name>$nametext</name>\n";
        $expout .= "    <DEFAULTGRADE>".$defaultgrade."</DEFAULTGRADE>\n";
        $expout .= "    <questiontext>\n".$questiontext;
        $expout .= "    </questiontext>\n";   
        // output depends on question type
        switch($question->qtype) {
        case MULTICHOICE:
        case SHORTANSWER:
        case 'regexp':
            foreach($question->options->answers as $answer) {
                $percent = $answer->fraction * 100;
                $expout .= "      <answer fraction=\"$percent\">\n";
                $expout .= $this->writetext( $answer->answer,4,false );
                $expout .= "      <feedback>\n";
                $expout .= $this->writetext( $answer->feedback,4,false );
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
                }
            break;
        case NUMERICAL:
            foreach ($question->options->answers as $answer) {
                $tolerance = $answer->tolerance;
                $expout .= "<answer>\n";
                $expout .= "    {$answer->answer}\n";
                $expout .= "    <tolerance>$tolerance</tolerance>\n";
                $expout .= "    <feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
                $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
                $expout .= "</answer>\n";
            }

            $units = $question->options->units;
            if (count($units)) {
                $expout .= "<units>\n";
                foreach ($units as $unit) {
                    $expout .= "  <unit>\n";
                    $expout .= "    <multiplier>{$unit->multiplier}</multiplier>\n";
                    $expout .= "    <unit_name>{$unit->unit}</unit_name>\n";
                    $expout .= "  </unit>\n";
                }
                $expout .= "</units>\n";
            }
            break;
        case DESCRIPTION:
            // nothing more to do for this type
            break;
        default:
            $expout .= "<!-- Question type is unknown or not supported (Type=$question->qtype) -->\n";
        }

        // close the question tag
        $expout .= "</question>\n";
        return $expout;
    }
}

?>