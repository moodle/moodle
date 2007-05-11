<?php // $Id$
//
///////////////////////////////////////////////////////////////
// XML import/export
//
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
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

    /*
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
     * @PARAM array text bit of xml tree after ['text']
     * @RETURN string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /*
     * import parts of question common to all types
     * @PARAM array question question array from xml tree
     * @RETURN object question object
     */
    function import_headers( $question ) {
        // this routine initialises the question object
        $name = $this->import_text( $question['#']['name'][0]['#']['text'] );
        $qtext = $this->import_text( $question['#']['questiontext'][0]['#']['text'] );
        $qformat = $question['#']['questiontext'][0]['@']['format'];
        $image = $question['#']['image'][0]['#'];
        if (!empty($question['#']['image_base64'][0]['#'])) {
            $image_base64 = stripslashes( trim( $question['#']['image_base64'][0]['#'] ) );
            $image = $this->importimagefile( $image, $image_base64 );
        }
        if (!empty($question['#']['defaultgrade'][0]['#'])) {
            $qo->defaultgrade = $question['#']['defaultgrade'][0]['#'];
        }
        $penalty = $question['#']['penalty'][0]['#'];

        $qo = $this->defaultquestion();
        $qo->name = $name;
        $qo->questiontext = $qtext;
        $qo->questiontextformat = $this->trans_format( $qformat );
        $qo->image = ((!empty($image)) ?  $image : '');
        $qo->penalty = $penalty;

        return $qo;
    }

    /*
     * import the common parts of a single answer
     * @PARAM array answer xml tree for single answer
     * @RETURN object answer object
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

    /*
     * import multiple choice question 
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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

    /*
     * import cloze type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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

    /*
     * import true/false type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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

    /*
     * import short answer type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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
    
    /*
     * import regexp type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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
    
    /*
     * import description type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
     */
    function import_description( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );
        // header parts particular to shortanswer
        $qo->qtype = DESCRIPTION;
        return $qo;
    }

    /*
     * import numerical type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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
            $qo->answer[] = $answer['#'][0];
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

    /*
     * import matching type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
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

    /*
     * import  essay type question
     * @PARAM array question question array from xml tree
     * @RETURN object question object
     */
    function import_essay( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to essay
        $qo->qtype = ESSAY;

        // get feedback
        $qo->feedback = $this->import_text( $question['#']['answer'][0]['#']['feedback'][0]['#']['text'] );        

        // handle answer
        $answer = $question['#']['answer'][0];

        // get fraction - <fraction> tag is deprecated
        if (!empty($answer['#']['fraction'][0]['#'])) {
            $qo->fraction = $answer['#']['fraction'][0]['#'];
        }
        else {
            $qo->fraction = $answer['@']['fraction'] / 100;
        }

        return $qo;
    }

    function import_calculated( $question ) {
    // import numerical question

        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to numerical
        $qo->qtype = CALCULATED ;//CALCULATED;
        // get answers array
        $answers = $question['#']['answer'];
        $qo->answers = array();
        $qo->feedback = array();
        $qo->fraction = array();
        $qo->tolerance = array();
        $qo->tolerancetype = array();
        $qo->correctanswerformat = array();
        $qo->correctanswerlength = array();
        $qo->feedback = array();
        foreach ($answers as $answer) {
            // answer outside of <text> is deprecated
            if (!empty( $answer['#']['text'] )) {
                $answertext = $this->import_text( $answer['#']['text'] );
            }
            else {
                $answertext = trim($answer['#'][0]);
            }
            if ($answertext == '') {
                $qo->answers[] = '*';
            } else {
                $qo->answers[] = $answertext;
            }
            $qo->feedback[] = $this->import_text( $answer['#']['feedback'][0]['#']['text'] );
            $qo->tolerance[] = $answer['#']['tolerance'][0]['#'];
            // fraction as a tag is deprecated
            if (!empty($answer['#']['fraction'][0]['#'])) {
                $qo->fraction[] = $answer['#']['fraction'][0]['#'];
            }
            else {
                $qo->fraction[] = $answer['@']['fraction'] / 100;
            }
            $qo->tolerancetype[] = $answer['#']['tolerancetype'][0]['#'];
            $qo->correctanswerformat[] = $answer['#']['correctanswerformat'][0]['#'];
            $qo->correctanswerlength[] = $answer['#']['correctanswerlength'][0]['#'];
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
                $datasets = $question['#']['dataset_definitions'][0]['#']['dataset_definition'];
                $qo->dataset = array();
                $qo->datasetindex= 0 ;
        foreach ($datasets as $dataset) {
            $qo->datasetindex++;
            $qo->dataset[$qo->datasetindex] = new stdClass();
            $qo->dataset[$qo->datasetindex]->status = $this->import_text( $dataset['#']['status'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->name = $this->import_text( $dataset['#']['name'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->type =  $dataset['#']['type'][0]['#'];
            $qo->dataset[$qo->datasetindex]->distribution = $this->import_text( $dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->max = $this->import_text( $dataset['#']['maximum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->min = $this->import_text( $dataset['#']['minimum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->length = $this->import_text( $dataset['#']['decimals'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->distribution = $this->import_text( $dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->itemcount = $dataset['#']['itemcount'][0]['#'];
            $qo->dataset[$qo->datasetindex]->datasetitem = array();
            $qo->dataset[$qo->datasetindex]->itemindex = 0;
            $qo->dataset[$qo->datasetindex]->number_of_items=$dataset['#']['number_of_items'][0]['#'];
            $datasetitems = $dataset['#']['dataset_items'][0]['#']['dataset_item'];
            foreach ($datasetitems as $datasetitem) {
                $qo->dataset[$qo->datasetindex]->itemindex++;
              $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex] = new stdClass();
              $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex]->itemnumber =  $datasetitem['#']['number'][0]['#']; //[0]['#']['number'][0]['#'] ; // [0]['numberitems'] ;//['#']['number'][0]['#'];// $datasetitems['#']['number'][0]['#'];
              $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex]->value = $datasetitem['#']['value'][0]['#'] ;//$datasetitem['#']['value'][0]['#'];
          } 
        }
                
                // echo "<pre>loaded qo";print_r($qo);echo "</pre>";

        return $qo;
    }

    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @PARAM array lines array of lines from the input file
     * @RETURN array (of objects) question objects
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
            elseif ($question_type=='calculated') {
                $qo = $this->import_calculated( $question );
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
     * @PARAM mixed type_id Internal code
     * @RETURN string question type string
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
        case CALCULATED:
            $name = 'calculated';
            break;
        default:
            $name = 'unknown';
        }
        return $name;
    }

    /*
     * Convert internal Moodle text format code into
     * human readable form
     * @PARAM int id internal code
     * @RETURN string format text
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

    /*
     * Convert internal single question code into 
     * human readable form
     * @PARAM int id single question code
     * @RETURN string single question string
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

    /*
     * generates <text></text> tags, processing raw text therein 
     * @PARAM int ilev the current indent level
     * @PARAM boolean short stick it on one line
     * @RETURN string formatted text
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

    /*
     * Include an image encoded in base 64
     * @PARAM string imagepath The location of the image file
     * @RETURN string xml code segment 
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

    /*
     * Turns question into an xml segment
     * @PARAM array question question array
     * @RETURN string xml segment
     */
    function writequestion( $question ) {
        // initial string;
        $expout = "";

        // add comment
        $expout .= "\n\n<!-- question: $question->id  -->\n";

        // add opening tag
        // generates specific header for Cloze type question
        if ($question->qtype != MULTIANSWER) {
            // for all question types except Close
            $question_type = $this->get_qtype( $question->qtype );
            $name_text = $this->writetext( $question->name );
            $qtformat = $this->get_format($question->questiontextformat);
            $question_text = $this->writetext( $question->questiontext );
            $expout .= "  <question type=\"$question_type\">\n";   
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext format=\"$qtformat\">\n";
            $expout .= $question_text;
            $expout .= "    </questiontext>\n";   
            $expout .= "    <image>{$question->image}</image>\n";
            $expout .= $this->writeimage($question->image);
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
        case CALCULATED:
            foreach ($question->options->answers as $answer) {
                $tolerance = $answer->tolerance;
                $tolerancetype = $answer->tolerancetype;
                $correctanswerlength= $answer->correctanswerlength ;
                $correctanswerformat= $answer->correctanswerformat;
                $percent = 100 * $answer->fraction;
                $expout .= "<answer fraction=\"$percent\">\n";
                // "<text/>" tags are an added feature, old files won't have them
                $expout .= "    <text>{$answer->answer}</text>\n";
                $expout .= "    <tolerance>$tolerance</tolerance>\n";
                $expout .= "    <tolerancetype>$tolerancetype</tolerancetype>\n";
                $expout .= "    <correctanswerformat>$correctanswerformat</correctanswerformat>\n";
                $expout .= "    <correctanswerlength>$correctanswerformat</correctanswerlength>\n";
                $expout .= "    <feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
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
        //echo "<pre> question calc";print_r($question);echo "</pre>"; 
        //First, we a new function to get all the   data itmes in the database
         //   $question_datasetdefs =$QTYPES['calculated']->get_datasets_for_export ($question);
        //    echo "<pre> question defs";print_r($question_datasetdefs);echo "</pre>";      
        //If there are question_datasets
            if( isset($question->options->datasets)&&count($question->options->datasets)){// there should be
                $expout .= "<dataset_definitions>\n";
                foreach ($question->options->datasets as $def) {
                    $expout .= "<dataset_definition>\n";
                    $expout .= "    <status>".$this->writetext($def->status)."</status>\n";
                    $expout .= "    <name>".$this->writetext($def->name)."</name>\n";
                    $expout .= "    <type>calculated</type>\n";
                    $expout .= "    <distribution>".$this->writetext($def->distribution)."</distribution>\n";
                    $expout .= "    <minimum>".$this->writetext($def->minimum)."</minimum>\n";
                    $expout .= "    <maximum>".$this->writetext($def->maximum)."</maximum>\n";
                    $expout .= "    <decimals>".$this->writetext($def->decimals)."</decimals>\n";               
                    $expout .= "    <itemcount>$def->itemcount</itemcount>\n";
                    if ($def->itemcount > 0 ) {
                        $expout .= "    <dataset_items>\n";
                        foreach ($def->items as $item ){
                              $expout .= "        <dataset_item>\n";
                              $expout .= "           <number>".$item->itemnumber."</number>\n";
                              $expout .= "           <value>".$item->value."</value>\n";
                              $expout .= "        </dataset_item>\n";
                        }        
                        $expout .= "    </dataset_items>\n";
                        $expout .= "    <number_of_items>".$def-> number_of_items."</number_of_items>\n";
                     }
                    $expout .= "</dataset_definition>\n";
                } 
                $expout .= "</dataset_definitions>\n";                                                                                
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
