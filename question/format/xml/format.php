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
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath( $xml, $path, $default, $istext=false, $error='' ) {
        foreach ($path as $index) {
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                } else {
                    return $default;
                }
            }
            else $xml = $xml[$index];
        }
        if ($istext) {
            if (!is_string($xml)) {
                $this->error( get_string('invalidxml','qformat_xml') );
            }
            $xml = addslashes( trim( $xml ) );
        }

        return $xml;
    }


    /**
     * import parts of question common to all types
     * @param $question array question question array from xml tree
     * @return object question object
     */
    function import_headers( $question ) {
        // get some error strings
        $error_noname = get_string( 'xmlimportnoname','quiz' );
        $error_noquestion = get_string( 'xmlimportnoquestion','quiz' );

        // this routine initialises the question object
        $qo = $this->defaultquestion();

        // question name
        $qo->name = $this->getpath( $question, array('#','name',0,'#','text',0,'#'), '', true, $error_noname );
        $qo->questiontext = $this->getpath( $question, array('#','questiontext',0,'#','text',0,'#'), '', true );
        $qo->questiontextformat = $this->getpath( $question, array('#','questiontext',0,'@','format'), '' );
        $qo->image = $this->getpath( $question, array('#','image',0,'#'), $qo->image );
        $image_base64 = $this->getpath( $question, array('#','image_base64','0','#'),'' );
        if (!empty($image_base64)) {
            $qo->image = $this->importimagefile( $qo->image, stripslashes($image_base64) );
        }
        $qo->generalfeedback = $this->getpath( $question, array('#','generalfeedback',0,'#','text',0,'#'), $qo->generalfeedback, true );
        $qo->defaultgrade = $this->getpath( $question, array('#','defaultgrade',0,'#'), $qo->defaultgrade );
        $qo->penalty = $this->getpath( $question, array('#','penalty',0,'#'), $qo->penalty );

        return $qo;
    }

    /**
     * import the common parts of a single answer
     * @param array answer xml tree for single answer
     * @return object answer object
     */
    function import_answer( $answer ) {
        $fraction = $this->getpath( $answer, array('@','fraction'),0 );
        $text = $this->getpath( $answer, array('#','text',0,'#'), '', true );
        $feedback = $this->getpath( $answer, array('#','feedback',0,'#','text',0,'#'), '', true );

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
        $single = $this->getpath( $question, array('#','single',0,'#'), 'true' );
        $qo->single = $this->trans_single( $single );
        $shuffleanswers = $this->getpath( $question, array('#','shuffleanswers',0,'#'), 'false' );
        $qo->answernumbering = $this->getpath( $question, array('#','answernumbering',0,'#'), 'abc' );
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);
        $qo->correctfeedback = $this->getpath( $question, array('#','correctfeedback',0,'#','text',0,'#'), '', true );
        $qo->partiallycorrectfeedback = $this->getpath( $question, array('#','partiallycorrectfeedback',0,'#','text',0,'#'), '', true );
        $qo->incorrectfeedback = $this->getpath( $question, array('#','incorrectfeedback',0,'#','text',0,'#'), '', true );

        // There was a time on the 1.8 branch when it could output an empty answernumbering tag, so fix up any found.
        if (empty($qo->answernumbering)) {
            $qo->answernumbering = 'abc';
        }

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
        $questiontext = $questions['#']['questiontext'][0]['#']['text'];
        $qo = qtype_multianswer_extract_question($this->import_text($questiontext));

        // 'header' parts particular to multianswer
        $qo->qtype = MULTIANSWER;
        $qo->course = $this->course;
        $qo->generalfeedback = $this->getpath( $questions, array('#','generalfeedback',0,'#','text',0,'#'), '', true );

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
            $answertext = $this->getpath( $answer, array('#','text',0,'#'), '', true );
            $feedback = $this->getpath($answer, array('#','feedback',0,'#','text',0,'#'), '', true );
            if ($answertext != 'true' && $answertext != 'false') {
                $warning = true;
                $answertext = $first ? 'true' : 'false'; // Old style file, assume order is true/false.
            }
            if ($answertext == 'true') {
                $qo->answer = ($answer['@']['fraction'] == 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbacktrue = $feedback;
            } else {
                $qo->answer = ($answer['@']['fraction'] != 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbackfalse = $feedback;
            }
            $first = false;
        }

        if ($warning) {
            $a = new stdClass;
            $a->questiontext = $qo->questiontext;
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
        $qo->usecase = $this->getpath($question, array('#','usecase',0,'#'), $qo->usecase );

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
        $qo->defaultgrade = 0;
        $qo->length = 0;
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
            // answer outside of <text> is deprecated
            $answertext = trim( $this->getpath( $answer, array('#',0), '' ) );
            $qo->answer[] = $this->getpath( $answer, array('#','text',0,'#'), $answertext, true );
            if (empty($qo->answer)) {
                $qo->answer = '*';
            }
            $qo->feedback[] = $this->getpath( $answer, array('#','feedback',0,'#','text',0,'#'), '', true );
            $qo->tolerance[] = $this->getpath( $answer, array('#','tolerance',0,'#'), 0 );

            // fraction as a tag is deprecated
            $fraction = $this->getpath( $answer, array('@','fraction'), 0 ) / 100;
            $qo->fraction[] = $this->getpath( $answer, array('#','fraction',0,'#'), $fraction ); // deprecated
        }

        // get units array
        $qo->unit = array();
        $units = $this->getpath( $question, array('#','units',0,'#','unit'), array() );
        if (!empty($units)) {
            $qo->multiplier = array();
            foreach ($units as $unit) {
                $qo->multiplier[] = $this->getpath( $unit, array('#','multiplier',0,'#'), 1 );
                $qo->unit[] = $this->getpath( $unit, array('#','unit_name',0,'#'), '', true );
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
        $qo->shuffleanswers = $this->getpath( $question, array( '#','shuffleanswers',0,'#' ), 1 );

        // get subquestions
        $subquestions = $question['#']['subquestion'];
        $qo->subquestions = array();
        $qo->subanswers = array();

        // run through subquestions
        foreach ($subquestions as $subquestion) {
            $qo->subquestions[] = $this->getpath( $subquestion, array('#','text',0,'#'), '', true );
            $qo->subanswers[] = $this->getpath( $subquestion, array('#','answer',0,'#','text',0,'#'), '', true);
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
        $qo->feedback = $this->getpath( $question, array('#','answer',0,'#','feedback',0,'#','text',0,'#'), '', true );

        // get fraction - <fraction> tag is deprecated
        $qo->fraction = $this->getpath( $question, array('@','fraction'), 0 ) / 100;
        $q0->fraction = $this->getpath( $question, array('#','fraction',0,'#'), $qo->fraction );

        return $qo;
    }

    function import_calculated( $question ) {
    // import numerical question

        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to numerical
        $qo->qtype = CALCULATED ;//CALCULATED;

        // get answers array
       // echo "<pre> question";print_r($question);echo "</pre>";
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
     * this is not a real question type. It's a dummy type used
     * to specify the import category
     * format is:
     * <question type="category">
     *     <category>tom/dick/harry</category>
     * </question>
     */
    function import_category( $question ) {
        $qo = new stdClass;
        $qo->qtype = 'category';
        $qo->category = $this->import_text($question['#']['category'][0]['#']['text']);
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
            elseif ($question_type=='category') {
                $qo = $this->import_category( $question );
            }
            else {
                // try for plugin support
                // no default question, as the plugin can call
                // import_headers() itself if it wants to
                if (!$qo = $this->try_importing_using_qtypes( $question, null, null, $question_type)) {
                    $notsupported = get_string( 'xmltypeunsupported','quiz',$question_type );
                    $this->error( $notsupported );
                    $qo = null;
                }
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
            $name = false;
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

        // if required add CDATA tags
        if (!empty($raw) and (htmlspecialchars($raw)!=$raw)) {
            $raw = "<![CDATA[$raw]]>";
        }

        if ($short) {
            $xml = "$indent<text>$raw</text>\n";
        }
        else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    function xmltidy( $content ) {
        // can only do this if tidy is installed
        if (extension_loaded('tidy')) {
            $config = array( 'input-xml'=>true, 'output-xml'=>true, 'indent'=>true, 'wrap'=>0 );
            $tidy = new tidy;
            $tidy->parseString($content, $config, 'utf8');
            $tidy->cleanRepair();
            return $tidy->value;
        }
        else {
            return $content;
        }
    }


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<quiz>\n" .
                       $content . "\n" .
                       "</quiz>";

        // make the xml look nice
        $content = $this->xmltidy( $content );

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
    global $CFG,$QTYPES;
        // initial string;
        $expout = "";

        // add comment
        $expout .= "\n\n<!-- question: $question->id  -->\n";

        // check question type
        if (!$question_type = $this->get_qtype( $question->qtype )) {
            // must be a plugin then, so just accept the name supplied
            $question_type = $question->qtype;
        }

        // add opening tag
        // generates specific header for Cloze and category type question
        if ($question->qtype == 'category') {
            $categorypath = $this->writetext( $question->category );
            $expout .= "  <question type=\"category\">\n";
            $expout .= "    <category>\n";
            $expout .= "        $categorypath\n";
            $expout .= "    </category>\n";
            $expout .= "  </question>\n";
            return $expout;
        }
        elseif ($question->qtype != MULTIANSWER) {
            // for all question types except Close
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
            $name_text = $this->writetext( $question->name );
            $question_text = $this->writetext( $question->questiontext );
            $generalfeedback = $this->writetext( $question->generalfeedback );
            $expout .= "  <question type=\"$question_type\">\n";
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext>\n";
            $expout .= $question_text;
            $expout .= "    </questiontext>\n";
            $expout .= "    <generalfeedback>\n";
            $expout .= $generalfeedback;
            $expout .= "    </generalfeedback>\n";
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
            $expout .= "    <answernumbering>{$question->options->answernumbering}</answernumbering>\n";
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
        case NUMERICAL:
            foreach ($question->options->answers as $answer) {
                $tolerance = $answer->tolerance;
                $percent = 100 * $answer->fraction;
                $expout .= "<answer fraction=\"$percent\">\n";
                // <text> tags are an added feature, old filed won't have them
                $expout .= "    <text>{$answer->answer}</text>\n";
                $expout .= "    <tolerance>$tolerance</tolerance>\n";
                $expout .= "    <feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
                // fraction tag is deprecated
                // $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
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
            if (!empty($question->options->answers)) {
                foreach ($question->options->answers as $answer) {
                    $percent = 100 * $answer->fraction;
                    $expout .= "<answer fraction=\"$percent\">\n";
                    $expout .= "    <feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
                    // fraction tag is deprecated
                    // $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
                    $expout .= "</answer>\n";
                }
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
                $expout .= "    <correctanswerlength>$correctanswerlength</correctanswerlength>\n";
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
            // try support by optional plugin
            if (!$data = $this->try_exporting_using_qtypes( $question->qtype, $question )) {
                notify( get_string( 'unsupportedexport','qformat_xml',$QTYPES[$question->qtype]->menu_name() ) );
            }
            $expout .= $data;
        }

        // close the question tag
        $expout .= "</question>\n";

        return $expout;
    }
}

?>
