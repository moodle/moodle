<?php // $Id$
//
///////////////////////////////////////////////////////////////
// XML import/export
//
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php

require_once( "$CFG->libdir/xmlize.php" );

class qformat_xml extends qformat_default {

    function provide_import() {
        return true;
    }

    function provide_export() {
        return true;
    }

    // IMPORT FUNCTIONS START HERE

    /** 
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format( $name ) {
        $name = trim($name); 
 
        if ($name=='moodle_auto_format') {
            $id = 0;
        }
        elseif ($name=='html') {
            $id = 1;
        }
        elseif ($name=='plain_text') {
            $id = 2;
        }
        elseif ($name=='wiki_like') {
            $id = 3;
        }
        elseif ($name=='markdown') {
            $id = 4;
        }
        else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        $data = $text[0]['#'];
        $data = html_entity_decode( $data );
        return addslashes(trim( $data ));
    }

    /**
     * Process text from an element in the XML that may or not be there.
     * @param string $subelement the name of the element which is either present or missing.
     * @param array $question a bit of xml tree, this method looks for $question['#'][$subelement][0]['#']['text'].
     * @return string If $subelement is present, return the content of the text tag inside it.
     *      Otherwise returns an empty string.
     */
    function import_optional_text($subelement, $question) {
        if (array_key_exists($subelement, $question['#'])) {
            return $this->import_text($question['#'][$subelement][0]['#']['text']);
        } else {
            return '';
        }
    }

    /**
     * import parts of question common to all types
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_headers( $question ) {
        // this routine initialises the question object
        $qo = $this->defaultquestion();
        $name = $this->import_text( $question['#']['name'][0]['#']['text'] );
        $qtext = $this->import_text( $question['#']['questiontext'][0]['#']['text'] );
        $qformat = $question['#']['questiontext'][0]['@']['format'];
        $image = $question['#']['image'][0]['#'];
        if (!empty($question['#']['image_base64'][0]['#'])) {
            $image_base64 = stripslashes( trim( $question['#']['image_base64'][0]['#'] ) );
            $image = $this->importimagefile( $image, $image_base64 );
        }
        if (array_key_exists('generalfeedback', $question['#'])) {
            $generalfeedback = $this->import_text( $question['#']['generalfeedback'][0]['#']['text'] );
        } else {
            $generalfeedback = '';
        }
        if (!empty($question['#']['defaultgrade'][0]['#'])) {
            $qo->defaultgrade = $question['#']['defaultgrade'][0]['#'];
        }
        
        $penalty = $question['#']['penalty'][0]['#'];

        $qo->name = $name;
        $qo->questiontext = $qtext;
        $qo->questiontextformat = $this->trans_format( $qformat );
        $qo->image = ((!empty($image)) ?  $image : '');
        $qo->generalfeedback = $generalfeedback;
        $qo->penalty = $penalty;

        return $qo;
    }

    /**
     * import the common parts of a single answer
     * @param array answer xml tree for single answer
     * @return object answer object
     */   
    function import_answer( $answer ) {
        $fraction = $answer['@']['fraction'];
        $text = $this->import_text( $answer['#']['text']);
        $feedback = $this->import_text( $answer['#']['feedback'][0]['#']['text'] );

        $ans = null;
        $ans->answer = $text;
        $ans->fraction = $fraction / 100;
        $ans->feedback = $feedback;
  
        return $ans;
    }

    /**
     * import multiple choice question 
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_multichoice( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // 'header' parts particular to multichoice
        $qo->qtype = MULTICHOICE;
        $single = $question['#']['single'][0]['#'];
        $qo->single = $this->trans_single( $single );
        if (array_key_exists('shuffleanswers', $question['#'])) {
            $shuffleanswers = $question['#']['shuffleanswers'][0]['#'];
        } else {
            $shuffleanswers = 'false';
        }
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);
        $qo->correctfeedback = $this->import_optional_text('correctfeedback', $question);
        $qo->partiallycorrectfeedback = $this->import_optional_text('partiallycorrectfeedback', $question);
        $qo->incorrectfeedback = $this->import_optional_text('incorrectfeedback', $question);
        
        // run through the answers
        $answers = $question['#']['answer'];  
        $a_count = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer( $answer );
            $qo->answer[$a_count] = $ans->answer;
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            ++$a_count;
        }

        return $qo;
    }

    /**
     * import cloze type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_multianswer( $questions ) {
        $qo = qtype_multianswer_extract_question($this->import_text( 
            $questions['#']['questiontext'][0]['#']['text'] ));

        // 'header' parts particular to multianswer
        $qo->qtype = MULTIANSWER;
        $qo->course = $this->course;

        if (!empty($questions)) {
            $qo->name = $this->import_text( $questions['#']['name'][0]['#']['text'] );
        }

        return $qo;
    }

    /**
     * import true/false type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_truefalse( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // 'header' parts particular to true/false
        $qo->qtype = TRUEFALSE;

        // get answer info
        //
        // In the past, it used to be assumed that the two answers were in the file
        // true first, then false. Howevever that was not always true. Now, we
        // try to match on the answer text, but in old exports, this will be a localised
        // string, so if we don't find true or false, we fall back to the old system.
        $first = true;
        $warning = false;
        foreach ($question['#']['answer'] as $answer) {
            $answertext = $this->import_text($answer['#']['text']);
            $feedback = $this->import_text($answer['#']['feedback'][0]['#']['text']);
            if ($answertext != 'true' && $answertext != 'false') {
                $warning = true;
                $answertext = $first ? 'true' : 'false'; // Old style file, assume order is true/false.
            } 
            if ($answertext == 'true') {
                $qo->answer = ($answer['@']['fraction'] == 100);
                $qo->feedbacktrue = $feedback;
            } else {
                $qo->answer = ($answer['@']['fraction'] != 100);
                $qo->feedbackfalse = $feedback;
            }
            $first = false;
        }

        if ($warning) {
            $a = new stdClass;
            $a->questiontext = stripslashes($qo->questiontext);
            $a->answer = get_string($qo->answer ? 'true' : 'false', 'quiz');
            notify(get_string('truefalseimporterror', 'quiz', $a));
        }

        return $qo;
    }

    /**
     * import short answer type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_shortanswer( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to shortanswer
        $qo->qtype = SHORTANSWER;

        // get usecase
        $qo->usecase = $question['#']['usecase'][0]['#'];

        // run through the answers
        $answers = $question['#']['answer'];  
        $a_count = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer( $answer );
            $qo->answer[$a_count] = $ans->answer;
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            ++$a_count;
        }

        return $qo;
    }
    
    /**
     * import regexp type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_regexp( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to shortanswer
        $qo->qtype = regexp;

        // get usecase
        $qo->usecase = $question['#']['usecase'][0]['#'];

        // run through the answers
        $answers = $question['#']['answer'];  
        $a_count = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer( $answer );
            $qo->answer[$a_count] = $ans->answer;
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            ++$a_count;
        }

        return $qo;
    }
    
    /**
     * import description type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_description( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );
        // header parts particular to shortanswer
        $qo->qtype = DESCRIPTION;
        return $qo;
    }

    /**
     * import numerical type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_numerical( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to numerical
        $qo->qtype = NUMERICAL;

        // get answers array
        $answers = $question['#']['answer'];
        $qo->answer = array();
        $qo->feedback = array();
        $qo->fraction = array();
        $qo->tolerance = array();
        foreach ($answers as $answer) {
            $answertext = trim($answer['#'][0]);
            if ($answertext == '') {
                $qo->answer[] = '*';
            } else {
                $qo->answer[] = $answertext;
            }
            $qo->feedback[] = $this->import_text( $answer['#']['feedback'][0]['#']['text'] );
            $qo->fraction[] = $answer['#']['fraction'][0]['#'];
            $qo->tolerance[] = $answer['#']['tolerance'][0]['#'];
        }

        // get units array
        $qo->unit = array();
        if (isset($question['#']['units'][0]['#']['unit'])) {
            $units = $question['#']['units'][0]['#']['unit'];
            $qo->multiplier = array();
            foreach ($units as $unit) {
                $qo->multiplier[] = $unit['#']['multiplier'][0]['#'];
                $qo->unit[] = $unit['#']['unit_name'][0]['#'];
            }
        }
        return $qo;
    }

    /**
     * import matching type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_matching( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to matching
        $qo->qtype = MATCH;
        if (!empty($question['#']['shuffleanswers'])) {
            $qo->shuffleanswers = $question['#']['shuffleanswers'][0]['#'];
        } else {
            $qo->shuffleanswers = false;
        }

        // get subquestions
        $subquestions = $question['#']['subquestion'];
        $qo->subquestions = array();
        $qo->subanswers = array();

        // run through subquestions
        foreach ($subquestions as $subquestion) {
            $qtext = $this->import_text( $subquestion['#']['text'] );
            $atext = $this->import_text( $subquestion['#']['answer'][0]['#']['text'] );
            $qo->subquestions[] = $qtext;
            $qo->subanswers[] = $atext;
        }
        return $qo;
    }

    /**
     * import  essay type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_essay( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to essay
        $qo->qtype = ESSAY;

        // get feedback
        $qo->feedback = $this->import_text( $question['#']['answer'][0]['#']['feedback'][0]['#']['text'] );        

        // get fraction
        $qo->fraction = $question['#']['answer'][0]['#']['fraction'][0]['#'];

        return $qo;
    }

    /**
     * this is not a real question type. It's a dummy type used
     * to specify the import category
     * format is:
     * <question type="category">
     *     <category>tom/dick/harry</category>
     * </question>
     */
    function import_category( $question ) {
        $qo->qtype = 'category';
        $qo->category = $question['#']['category'][0]['#'];
        return $qo;
    }

    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function readquestions($lines) {
        // we just need it as one big string
        $text = implode($lines, " ");
        unset( $lines );

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize( $text, 0 ); 

        // set up array to hold all our questions
        $questions = array();

        // iterate through questions
        foreach ($xml['quiz']['#']['question'] as $question) {
            $question_type = $question['@']['type'];
            $questiontype = get_string( 'questiontype','quiz',$question_type );

            if ($question_type=='multichoice') {
                $qo = $this->import_multichoice( $question );
            }  
            elseif ($question_type=='truefalse') {
                $qo = $this->import_truefalse( $question );
            }
            elseif ($question_type=='shortanswer') {
                $qo = $this->import_shortanswer( $question );
            }
            //elseif ($question_type=='regexp') {
            //    $qo = $this->import_regexp( $question );
            //}
            elseif ($question_type=='numerical') {
                $qo = $this->import_numerical( $question );
            }
            elseif ($question_type=='description') {
                $qo = $this->import_description( $question );
            }
            elseif ($question_type=='matching') {
                $qo = $this->import_matching( $question );
            }
            elseif ($question_type=='cloze') {
                $qo = $this->import_multianswer( $question );
            }
            elseif ($question_type=='essay') {
                $qo = $this->import_essay( $question );
            }
            elseif ($question_type=='category') {
                $qo = $this->import_category( $question );
            }
            else {
                $notsupported = get_string( 'xmltypeunsupported','quiz',$question_type );
                echo "<p>$notsupported</p>";
                $qo = null;
            }

            // stick the result in the $questions array
            if ($qo) {
                $questions[] = $qo;
            }
        }
        return $questions;
    }

    // EXPORT FUNCTIONS START HERE

    function export_file_extension() {
    // override default type so extension is .xml
    
        return ".xml";
    }


    /**
     * Turn the internal question code into a human readable form
     * (The code used to be numeric, but this remains as some of
     * the names don't match the new internal format)
     * @param mixed type_id Internal code
     * @return string question type string
     */
    function get_qtype( $type_id ) {
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
        //case regexp:
        //    $name = 'regexp';
        //    break;
        case NUMERICAL:
            $name = 'numerical';
            break;
        case MATCH:
            $name = 'matching';
            break;
        case DESCRIPTION:
            $name = 'description';
            break;
        case MULTIANSWER:
            $name = 'cloze';
            break;
        case ESSAY:
            $name = 'essay';
            break;
        default:
            $name = 'unknown';
        }
        return $name;
    }

    /**
     * Convert internal Moodle text format code into
     * human readable form
     * @param int id internal code
     * @return string format text
     */
    function get_format( $id ) {
        switch( $id ) {
        case 0:
            $name = "moodle_auto_format";
            break;
        case 1:
            $name = "html";
            break;
        case 2:
            $name = "plain_text";
            break;
        case 3:
            $name = "wiki_like";
            break;
        case 4:
            $name = "markdown";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * Convert internal single question code into 
     * human readable form
     * @param int id single question code
     * @return string single question string
     */
    function get_single( $id ) {
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

    /**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */
    function writetext( $raw, $ilev=0, $short=true) {
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

        $content = "<?xml version=\"1.0\"?>\n" .
                       "<quiz>\n" .
                       $content . "\n" .
                       "</quiz>";

        return $content;
    }

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   
        if (empty($imagepath)) {
            return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
            return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

    /**
     * Turns question into an xml segment
     * @param array question question array
     * @return string xml segment
     */
    function writequestion( $question ) {
        // initial string;
        $expout = "";

        // add comment
        $expout .= "\n\n<!-- question: $question->id  -->\n";

        // add opening tag
        // generates specific header for Cloze and category type question
        if ($question->qtype == 'category') {
            $expout .= "  <question type=\"category\">\n";
            $expout .= "    <category>\n";
            $expout .= "        $question->category\n";
            $expout .= "    </category>\n";
            $expout .= "  </question>\n";
            return $expout;
        }    
        elseif ($question->qtype != MULTIANSWER) {
            // for all question types except Close
            $question_type = $this->get_qtype( $question->qtype );
            $name_text = $this->writetext( $question->name );
            $qtformat = $this->get_format($question->questiontextformat);
            $question_text = $this->writetext( $question->questiontext );
            $generalfeedback = $this->writetext( $question->generalfeedback );
            $expout .= "  <question type=\"$question_type\">\n";   
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext format=\"$qtformat\">\n";
            $expout .= $question_text;
            $expout .= "    </questiontext>\n";   
            $expout .= "    <image>{$question->image}</image>\n";
            $expout .= $this->writeimage($question->image);
            $expout .= "    <generalfeedback>\n";
            $expout .= $generalfeedback;
            $expout .= "    </generalfeedback>\n";
            $expout .= "    <defaultgrade>{$question->defaultgrade}</defaultgrade>\n";
            $expout .= "    <penalty>{$question->penalty}</penalty>\n";
            $expout .= "    <hidden>{$question->hidden}</hidden>\n";
        }
        else {
            // for Cloze type only
            $question_type = $this->get_qtype( $question->qtype );
            $name_text = $this->writetext( $question->name );
            $question_text = $this->writetext( $question->questiontext );
            $expout .= "  <question type=\"$question_type\">\n";
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext>\n";
            $expout .= $question_text;
            $expout .= "    </questiontext>\n";
        }

        if (!empty($question->options->shuffleanswers)) {
            $expout .= "    <shuffleanswers>{$question->options->shuffleanswers}</shuffleanswers>\n";
        }
        else {
            $expout .= "    <shuffleanswers>0</shuffleanswers>\n";
        }

        // output depends on question type
        switch($question->qtype) {
        case 'category':
            // not a qtype really - dummy used for category switching
            break;    
        case TRUEFALSE:
            foreach ($question->options->answers as $answer) {
                $fraction_pc = round( $answer->fraction * 100 );
                if ($answer->id == $question->options->trueanswer) {
                    $answertext = 'true';
                } else {
                    $answertext = 'false';
                } 
                $expout .= "    <answer fraction=\"$fraction_pc\">\n";
                $expout .= $this->writetext($answertext, 3) . "\n";
                $expout .= "      <feedback>\n";
                $expout .= $this->writetext( $answer->feedback,4,false );
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
            }
            break;
        case MULTICHOICE:
            $expout .= "    <single>".$this->get_single($question->options->single)."</single>\n";
            $expout .= "    <shuffleanswers>".$this->get_single($question->options->shuffleanswers)."</shuffleanswers>\n";
            $expout .= "    <correctfeedback>".$this->writetext($question->options->correctfeedback, 3)."</correctfeedback>\n";
            $expout .= "    <partiallycorrectfeedback>".$this->writetext($question->options->partiallycorrectfeedback, 3)."</partiallycorrectfeedback>\n";
            $expout .= "    <incorrectfeedback>".$this->writetext($question->options->incorrectfeedback, 3)."</incorrectfeedback>\n";
            foreach($question->options->answers as $answer) {
                $percent = $answer->fraction * 100;
                $expout .= "      <answer fraction=\"$percent\">\n";
                $expout .= $this->writetext( $answer->answer,4,false );
                $expout .= "      <feedback>\n";
                $expout .= $this->writetext( $answer->feedback,5,false );
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
                }
            break;
        case SHORTANSWER:
        $expout .= "    <usecase>{$question->options->usecase}</usecase>\n ";
            foreach($question->options->answers as $answer) {
                $percent = 100 * $answer->fraction;
                $expout .= "    <answer fraction=\"$percent\">\n";
                $expout .= $this->writetext( $answer->answer,3,false );
                $expout .= "      <feedback>\n";
                $expout .= $this->writetext( $answer->feedback,4,false );
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
            }
            break;
        //case regexp:
        //$expout .= "    <usecase>{$question->options->usecase}</usecase>\n ";
        //    foreach($question->options->answers as $answer) {
        //        $percent = 100 * $answer->fraction;
        //        $expout .= "    <answer fraction=\"$percent\">\n";
        //        $expout .= $this->writetext( $answer->answer,3,false );
        //        $expout .= "      <feedback>\n";
        //        $expout .= $this->writetext( $answer->feedback,4,false );
        //        $expout .= "      </feedback>\n";
        //        $expout .= "    </answer>\n";
        //    }
        //    break;
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
        case MATCH:
            foreach($question->options->subquestions as $subquestion) {
                $expout .= "<subquestion>\n";
                $expout .= $this->writetext( $subquestion->questiontext );
                $expout .= "<answer>".$this->writetext( $subquestion->answertext )."</answer>\n";
                $expout .= "</subquestion>\n";
            }
            break;
        case DESCRIPTION:
            // nothing more to do for this type
            break;
        case MULTIANSWER:
            $a_count=1;
            foreach($question->options->questions as $question) {
                $thispattern = addslashes("{#".$a_count."}");
                $thisreplace = $question->questiontext;
                $expout=ereg_replace($thispattern, $thisreplace, $expout );
                $a_count++;
            }
        break;
        case ESSAY:
            foreach ($question->options->answers as $answer) {
                $expout .= "<answer>\n";
                $expout .= "    <feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
                $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
                $expout .= "</answer>\n";
            }
            
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
