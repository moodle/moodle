<?php  // $Id$

require_once("$CFG->dirroot/question/format/qti2/qt_common.php");
////////////////////////////////////////////////////////////////////////////
/// IMS QTI 2.0 FORMAT
///
/// HISTORY: created 28.01.2005      brian@mediagonal.ch
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
define('CLOZE_TRAILING_TEXT_ID', 9999999);

class qformat_qti2 extends qformat_default {

    var $lang;

    function provide_export() {
       return true;
    }

    function indent_xhtml($source, $indenter = ' ') {
        // xml tidier-upper
        // (c) Ari Koivula http://ventionline.com

        // Remove all pre-existing formatting.
        // Remove all newlines.
        $source = str_replace("\n", '', $source);
        $source = str_replace("\r", '', $source);
        // Remove all tabs.
        $source = str_replace("\t", '', $source);
        // Remove all space after ">" and before "<".
        $source = ereg_replace(">( )*", ">", $source);
        $source = ereg_replace("( )*<", "<", $source);

        // Iterate through the source.
        $level = 0;
        $source_len = strlen($source);
        $pt = 0;
        while ($pt < $source_len) {
            if ($source{$pt} === '<') {
                // We have entered a tag.
                // Remember the point where the tag starts.
                $started_at = $pt;
                $tag_level = 1;
                // If the second letter of the tag is "/", assume its an ending tag.
                if ($source{$pt+1} === '/') {
                    $tag_level = -1;
                }
                // If the second letter of the tag is "!", assume its an "invisible" tag.
                if ($source{$pt+1} === '!') {
                    $tag_level = 0;
                }
                // Iterate throught the source until the end of tag.
                while ($source{$pt} !== '>') {
                    $pt++;
                }
                // If the second last letter is "/", assume its a self ending tag.
                if ($source{$pt-1} === '/') {
                    $tag_level = 0;
                }
                $tag_lenght = $pt+1-$started_at;

                // Decide the level of indention for this tag.
                // If this was an ending tag, decrease indent level for this tag..
                if ($tag_level === -1) {
                    $level--;
                }
                // Place the tag in an array with proper indention.
                $array[] = str_repeat($indenter, $level).substr($source, $started_at, $tag_lenght);
                // If this was a starting tag, increase the indent level after this tag.
                if ($tag_level === 1) {
                    $level++;
                }
                // if it was a self closing tag, dont do anything.
            }
            // Were out of the tag.
            // If next letter exists...
            if (($pt+1) < $source_len) {
                // ... and its not an "<".
                if ($source{$pt+1} !== '<') {
                    $started_at = $pt+1;
                    // Iterate through the source until the start of new tag or until we reach the end of file.
                    while ($source{$pt} !== '<' && $pt < $source_len) {
                        $pt++;
                    }
                    // If we found a "<" (we didnt find the end of file)
                    if ($source{$pt} === '<') {
                        $tag_lenght = $pt-$started_at;
                        // Place the stuff in an array with proper indention.
                        $array[] = str_repeat($indenter, $level).substr($source, $started_at, $tag_lenght);
                    }
                // If the next tag is "<", just advance pointer and let the tag indenter take care of it.
                } else {
                    $pt++;
                }
            // If the next letter doesnt exist... Were done... well, almost..
            } else {
                break;
            }
        }
        // Replace old source with the new one we just collected into our array.
        $source = implode($array, "\n");
        return $source;
    }

    function importpreprocess() {
        global $CFG;

        error("Sorry, importing this format is not yet implemented!",
            "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
    }

    function exportpreprocess() {
        global $CFG;

        require_once("{$CFG->libdir}/smarty/Smarty.class.php");

        // assign the language for the export: by parameter, SESSION, USER, or the default of 'en'
        $lang = current_language();
        $this->lang = $lang;

        return parent::exportpreprocess();
    }


    function export_file_extension() {
        // override default type so extension is .xml

        return ".zip";
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
            $name = 'multianswer';
            break;
        default:
            $name = 'Unknown';
        }
        return $name;
    }

    function writetext( $raw ) {
        // generates <text></text> tags, processing raw text therein

        // for now, don't allow any additional tags in text
        // otherwise xml rules would probably get broken
        $raw = strip_tags( $raw );

        return "<text>$raw</text>\n";
    }


/**
 * flattens $object['media'], copies $object['media'] to $path, and sets $object['mediamimetype']
 *
 * @param array &$object containing a field 'media'
 * @param string $path the full path name to where the media files need to be copied
 * @param int $courseid
 * @return: mixed - true on success or in case of an empty media field, an error string if the file copy fails
 */
function copy_and_flatten(&$object, $path, $courseid) {
    global $CFG;
    if (!empty($object['media'])) {
        $location = $object['media'];
        $object['media'] = $this->flatten_image_name($location);
        if (!@copy("{$CFG->dataroot}/$courseid/$location", "$path/{$object['media']}")) {
            return "Failed to copy {$CFG->dataroot}/$courseid/$location to $path/{$object['media']}";
        }
        if (empty($object['mediamimetype'])) {
            $object['mediamimetype'] = mimeinfo('type', $object['media']);
        }
    }
    return true;
}
/**
 * copies all files needed by the questions to the given $path, and flattens the file names
 *
 * @param array $questions the question objects
 * @param string $path the full path name to where the media files need to be copied
 * @param int $courseid
 * @return mixed true on success, an array of error messages otherwise
 */
function handle_questions_media(&$questions, $path, $courseid) {
    global $CFG;
    $errors = array();
    foreach ($questions as $key=>$question) {

    // todo: handle in-line media (specified in the question text)
        if (!empty($question->image)) {
            $location = $questions[$key]->image;
            $questions[$key]->mediaurl = $this->flatten_image_name($location);
            if (!@copy("{$CFG->dataroot}/$courseid/$location", "$path/{$questions[$key]->mediaurl}")) {
                $errors[] = "Failed to copy {$CFG->dataroot}/$courseid/$location to $path/{$questions[$key]->mediaurl}";
            }
            if (empty($question->mediamimetype)) {
                $questions[$key]->mediamimetype = mimeinfo('type', $question->image);
            }
        }
    }

    return empty($errors) ? true : $errors;
}

/**
 * exports the questions in a question category to the given location
 *
 * The parent class method was overridden because the IMS export consists of multiple files
 *
 * @param string $filename the directory name which will hold the exported files
 * @return boolean - or errors out
 */
    function exportprocess() {

        global $CFG;
        $courseid = $this->course->id;

        // create a directory for the exports (if not already existing)
        if (!$export_dir = make_upload_directory($this->question_get_export_dir().'/'.$this->filename)) {
              error( get_string('cannotcreatepath','quiz',$export_dir) );
        }
        $path = $CFG->dataroot.'/'.$this->question_get_export_dir().'/'.$this->filename;

        // get the questions (from database) in this category
        // $questions = get_records("question","category",$this->category->id);
        $questions = get_questions_category( $this->category );

        notify("Exporting ".count($questions)." questions.");
        $count = 0;

        // create the imsmanifest file
        $smarty =& $this->init_smarty();
        $this->add_qti_info($questions);
        // copy files used by the main questions to the export directory
        $result = $this->handle_questions_media($questions, $path, $courseid);
        if ($result !== true) {
            notify(implode("<br />", $result));
        }

        $manifestquestions = $this->objects_to_array($questions);
        $manifestid = str_replace(array(':', '/'), array('-','_'), "question_category_{$this->category->id}---{$CFG->wwwroot}");
        $smarty->assign('externalfiles', 1);
        $smarty->assign('manifestidentifier', $manifestid);
        $smarty->assign('quiztitle', "question_category_{$this->category->id}");
        $smarty->assign('quizinfo', "All questions in category {$this->category->id}");
        $smarty->assign('questions', $manifestquestions);
        $smarty->assign('lang', $this->lang);
        $smarty->error_reporting = 99;
        $expout = $smarty->fetch('imsmanifest.tpl');
        $filepath = $path.'/imsmanifest.xml';
        if (empty($expout)) {
            error("Unkown error - empty imsmanifest.xml");
        }
        if (!$fh=fopen($filepath,"w")) {
            error("Cannot open for writing: $filepath");
        }
        if (!fwrite($fh, $expout)) {
            error("Cannot write exported questions to $filepath");
        }
        fclose($fh);

        // iterate through questions
        foreach($questions as $question) {

            // results are first written into string (and then to a file)
            $count++;
            echo "<hr /><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";
            $expout = $this->writequestion( $question , null, true, $path) . "\n";
            $expout = $this->presave_process( $expout );

            $filepath = $path.'/'.$this->get_assesment_item_id($question) . ".xml";
            if (!$fh=fopen($filepath,"w")) {
                error("Cannot open for writing: $filepath");
            }
            if (!fwrite($fh, $expout)) {
                error("Cannot write exported questions to $filepath");
            }
            fclose($fh);

        }

        // zip files into single export file
        zip_files( array($path), "$path.zip" );

        // remove the temporary directory
        remove_dir( $path );

        return true;
    }

/**
 * exports a quiz (as opposed to exporting a category of questions)
 *
 * The parent class method was overridden because the IMS export consists of multiple files
 *
 * @param object $quiz
 * @param array $questions - an array of question objects
 * @param object $result - if set, contains result of calling quiz_grade_responses()
 * @param string $redirect - a URL to redirect to in case of failure
 * @param string $submiturl - the URL for the qti player to send the results to (e.g. attempt.php)
 * @todo use $result in the ouput
 */
     function export_quiz($course, $quiz, $questions, $result, $redirect, $submiturl = null) {
        $this->xml_entitize($course);
        $this->xml_entitize($quiz);
        $this->xml_entitize($questions);
        $this->xml_entitize($result);
        $this->xml_entitize($submiturl);
        if (! $this->exportpreprocess(0, $course)) {   // Do anything before that we need to
            error("Error occurred during pre-processing!", $redirect);
        }
        if (! $this->exportprocess_quiz($quiz, $questions, $result, $submiturl, $course)) {         // Process the export data
            error("Error occurred during processing!", $redirect);
        }
        if (! $this->exportpostprocess()) {                    // In case anything needs to be done after
            error("Error occurred during post-processing!", $redirect);
        }

    }


/**
 * This function is called to export a quiz (as opposed to exporting a category of questions)
 *
 * @uses $USER
 * @param object $quiz
 * @param array $questions - an array of question objects
 * @param object $result - if set, contains result of calling quiz_grade_responses()
 * @todo use $result in the ouput
 */
    function exportprocess_quiz($quiz, $questions, $result, $submiturl, $course) {
        global $USER;
        global $CFG;

        $gradingmethod = array (1 => 'GRADEHIGHEST',
                                2 => 'GRADEAVERAGE',
                                3 => 'ATTEMPTFIRST' ,
                                4 => 'ATTEMPTLAST');

        $questions = $this->quiz_export_prepare_questions($questions, $quiz->id, $course->id, $quiz->shuffleanswers);

        $smarty =& $this->init_smarty();
        $smarty->assign('questions', $questions);

        // quiz level smarty variables
        $manifestid = str_replace(array(':', '/'), array('-','_'), "quiz{$quiz->id}-{$CFG->wwwroot}");
        $smarty->assign('manifestidentifier', $manifestid);
        $smarty->assign('submiturl', $submiturl);
        $smarty->assign('userid', $USER->id);
        $smarty->assign('username', htmlspecialchars($USER->username, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('quiz_level_export', 1);
        $smarty->assign('quiztitle', format_string($quiz->name,true)); //assigned specifically so as not to cause problems with category-level export
        $smarty->assign('quiztimeopen', date('Y-m-d\TH:i:s', $quiz->timeopen)); // ditto
        $smarty->assign('quiztimeclose', date('Y-m-d\TH:i:s', $quiz->timeclose)); // ditto
        $smarty->assign('grademethod', $gradingmethod[$quiz->grademethod]);
        $smarty->assign('quiz', $quiz);
        $smarty->assign('course', $course);
        $smarty->assign('lang', $this->lang);
        $expout = $smarty->fetch('imsmanifest.tpl');
        echo $expout;
        return true;
    }




/**
 * Prepares questions for quiz export
 *
 * The questions are changed as follows:
 *   - the question answers atached to the questions
 *   - image set to an http reference instead of a file path
 *   - qti specific info added
 *   - exporttext added, which contains an xml-formatted qti assesmentItem
 *
 * @param array $questions - an array of question objects
 * @param int $quizid
 * @return an array of question arrays
 */
    function quiz_export_prepare_questions($questions, $quizid, $courseid, $shuffleanswers = null) {
        global $CFG;
        // add the answers to the questions and format the image property
        foreach ($questions as $key=>$question) {
            $questions[$key] = get_question_data($question);
            $questions[$key]->courseid = $courseid;
            $questions[$key]->quizid = $quizid;

            if ($question->image) {

                if (empty($question->mediamimetype)) {
                  $questions[$key]->mediamimetype = mimeinfo('type',$question->image);
                }

                $localfile = (substr(strtolower($question->image), 0, 7) == 'http://') ? false : true;

                if ($localfile) {
                    // create the http url that the player will need to access the file
                    if ($CFG->slasharguments) {        // Use this method if possible for better caching
                        $questions[$key]->mediaurl = "$CFG->wwwroot/file.php/$question->image";
                    } else {
                        $questions[$key]->mediaurl = "$CFG->wwwroot/file.php?file=$question->image";
                    }
                } else {
                    $questions[$key]->mediaurl = $question->image;
                }
            }
        }

        $this->add_qti_info($questions);
        $questions = $this->questions_with_export_info($questions, $shuffleanswers);
        $questions = $this->objects_to_array($questions);
        return $questions;
    }

/**
 * calls htmlspecialchars for each string field, to convert, for example, & to &amp;
 *
 * collections are processed recursively
 *
 * @param array $collection - an array or object or string
 */
function xml_entitize(&$collection) {
    if (is_array($collection)) {
        foreach ($collection as $key=>$var) {
            if (is_string($var)) {
                $collection[$key]= htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
            } else if (is_array($var) || is_object($var)) {
                $this->xml_entitize($collection[$key]);
            }
        }
    } else if (is_object($collection)) {
        $vars = get_object_vars($collection);
        foreach ($vars as $key=>$var) {
            if (is_string($var)) {
                $collection->$key = htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
            } else if (is_array($var) || is_object($var)) {
                $this->xml_entitize($collection->$key);
            }
        }
    } else if (is_string($collection)) {
        $collection = htmlspecialchars($collection, ENT_COMPAT, 'UTF-8');
    }
}

/**
 * adds exporttext property to the questions
 *
 * Adds the qti export text to the questions
 *
 * @param array $questions - an array of question objects
 * @return an array of question objects
 */
    function questions_with_export_info($questions, $shuffleanswers = null) {
        $exportquestions = array();
        foreach($questions as $key=>$question) {
            $expout = $this->writequestion( $question , $shuffleanswers) . "\n";
            $expout = $this->presave_process( $expout );
            $key = $this->get_assesment_item_id($question);
            $exportquestions[$key] = $question;
            $exportquestions[$key]->exporttext = $expout;
        }
        return $exportquestions;
    }

/**
 * Creates the export text for a question
 *
 * @todo handle in-line media (specified in the question/subquestion/answer text) for course-level exports
 * @param object $question
 * @param boolean $shuffleanswers whether or not to shuffle the answers
 * @param boolean $courselevel whether or not this is a course-level export
 * @param string $path provide the path to copy question media files to, if $courselevel == true
 * @return string containing export text
 */
    function writequestion($question, $shuffleanswers = null, $courselevel = false, $path = '') {
        // turns question into string
        // question reflects database fields for general question and specific to type
        global $CFG;
        $expout = '';
        //need to unencode the html entities in the questiontext field.
        // the whole question object was earlier run throught htmlspecialchars in xml_entitize().
        $question->questiontext = html_entity_decode($question->questiontext, ENT_COMPAT);

        $hasimage = empty($question->image) ? 0 : 1;
        $hassize = empty($question->mediax) ? 0 : 1;

        $allowedtags = '<a><br><b><h1><h2><h3><h4><i><img><li><ol><strong><table><tr><td><th><u><ul><object>';  // all other tags will be stripped from question text
        $smarty =& $this->init_smarty();
        $assesmentitemid = $this->get_assesment_item_id($question);
        $question_type = $this->get_qtype( $question->qtype );
        $questionid = "question{$question->id}$question_type";
        $smarty->assign('question_has_image', $hasimage);
        $smarty->assign('hassize', $hassize);
        $smarty->assign('questionid', $questionid);
        $smarty->assign('assessmentitemidentifier', $assesmentitemid);
        $smarty->assign('assessmentitemtitle', $question->name);
        $smarty->assign('courselevelexport', $courselevel);

        if ($question->qtype == MULTIANSWER) {
            $question->questiontext = strip_tags($question->questiontext, $allowedtags . '<intro>');
            $smarty->assign('questionText',  $this->get_cloze_intro($question->questiontext));
        } else {
            $smarty->assign('questionText',  strip_tags($question->questiontext, $allowedtags));
        }

        $smarty->assign('question', $question);
        // the following two are left for compatibility; the templates should be changed, though, to make object tags for the questions
        //$smarty->assign('questionimage', $question->image);
        //$smarty->assign('questionimagealt', "image: $question->image");

        // output depends on question type
        switch($question->qtype) {
        case TRUEFALSE:
            $qanswers = $question->options->answers;
            $answers[0] = (array)$qanswers['true'];
            $answers[0]['answer'] = get_string("true", "quiz");
            $answers[1] = (array)$qanswers['false'];
            $answers[1]['answer'] = get_string("false", "quiz");

            if (!empty($shuffleanswers)) {
                $answers = $this->shuffle_things($answers);
            }

            if (isset($question->response)) {
              $correctresponseid = $question->response[$questionid];
              if ($answers[0]['id'] == $correctresponseid) {
                  $correctresponse = $answers[0];
              } else {
                  $correctresponse = $answers[1];
              }
            }
            else {
              $correctresponse = '';
            }

            $smarty->assign('correctresponse', $correctresponse);
            $smarty->assign('answers', $answers);
            $expout = $smarty->fetch('choice.tpl');
            break;
        case MULTICHOICE:
            $answers = $this->objects_to_array($question->options->answers);
            $correctresponses = $this->get_correct_answers($answers);
            $correctcount = count($correctresponses);
            $smarty->assign('responsedeclarationcardinality', $question->options->single ? 'single' : 'multiple');
            $smarty->assign('operator', $question->options->single ? 'match' : 'member');
            $smarty->assign('correctresponses', $correctresponses);
            $smarty->assign('answers', $answers);
            $smarty->assign('maxChoices', $question->options->single ? '1' : count($answers));
            $smarty->assign('maxChoices', $question->options->single ? '1' : count($answers));
            $smarty->assign('shuffle', empty($shuffleanswers) ? 'false' : 'true');
            $smarty->assign('generalfeedback', $question->generalfeedback);
            $smarty->assign('correctfeedback', $question->options->correctfeedback);
            $smarty->assign('partiallycorrectfeedback', $question->options->partiallycorrectfeedback);
            $smarty->assign('incorrectfeedback', $question->options->incorrectfeedback);
            $expout = $smarty->fetch('choiceMultiple.tpl');
            break;
        case SHORTANSWER:
            $answers = $this->objects_to_array($question->options->answers);
            if (!empty($shuffleanswers)) {
                $answers = $this->shuffle_things($answers);
            }

            $correctresponses = $this->get_correct_answers($answers);
            $correctcount = count($correctresponses);

            $smarty->assign('responsedeclarationcardinality', $correctcount > 1 ? 'multiple' : 'single');
            $smarty->assign('correctresponses', $correctresponses);
            $smarty->assign('answers', $answers);
            $expout = $smarty->fetch('textEntry.tpl');
            break;
        case NUMERICAL:
            $qanswer = array_pop( $question->options->answers );
            $smarty->assign('lowerbound', $qanswer->answer - $qanswer->tolerance);
            $smarty->assign('upperbound', $qanswer->answer + $qanswer->tolerance);
            $smarty->assign('answer', $qanswer->answer);
            $expout = $smarty->fetch('numerical.tpl');
            break;
        case MATCH:
            $this->xml_entitize($question->options->subquestions);
            $subquestions = $this->objects_to_array($question->options->subquestions);
            if (!empty($shuffleanswers)) {
                $subquestions = $this->shuffle_things($subquestions);
            }
            $setcount = count($subquestions);

            $smarty->assign('setcount', $setcount);
            $smarty->assign('matchsets', $subquestions);
            $expout = $smarty->fetch('match.tpl');
            break;
        case DESCRIPTION:
            $expout = $smarty->fetch('extendedText.tpl');
            break;
        // loss of get_answers() from quiz_embedded_close_qtype class during
        // Gustav's refactor breaks MULTIANSWER badly - one for another day!!
        /*
        case MULTIANSWER:
            $answers = $this->get_cloze_answers_array($question);
            $questions = $this->get_cloze_questions($question, $answers, $allowedtags);

            $smarty->assign('cloze_trailing_text_id', CLOZE_TRAILING_TEXT_ID);
            $smarty->assign('answers', $answers);
            $smarty->assign('questions', $questions);
            $expout = $smarty->fetch('composite.tpl');
            break; */
        default:
            $smarty->assign('questionText', "This question type (Unknown: type $question_type)  has not yet been implemented");
            $expout = $smarty->fetch('notimplemented.tpl');
        }

        // run through xml tidy function
        //$tidy_expout = $this->indent_xhtml( $expout, '    ' ) . "\n\n";
        //return $tidy_expout;
        return $expout;
    }

/**
 * Gets an id to use for a qti assesment item
 *
 * @param object $question
 * @return string containing a qti assesment item id
 */
    function get_assesment_item_id($question) {
        return "question{$question->id}";
    }

/**
 * gets the answers whose grade fraction > 0
 *
 * @param array $answers
 * @return array (0-indexed) containing the answers whose grade fraction > 0
 */
    function get_correct_answers($answers)
    {
        $correctanswers = array();
        foreach ($answers as $answer) {
            if ($answer['fraction'] > 0) {
                $correctanswers[] = $answer;
            }
        }
        return $correctanswers;
    }

/**
 * gets a new Smarty object, with the template and compile directories set
 *
 * @return object a smarty object
 */
    function & init_smarty() {
        global $CFG;

        // create smarty compile dir in dataroot
        $path = $CFG->dataroot."/smarty_c";
        if (!is_dir($path)) {
            if (!mkdir($path, $CFG->directorypermissions)) {
              error("Cannot create path: $path");
            }
        }
        $smarty = new Smarty;
        $smarty->template_dir = "{$CFG->dirroot}/question/format/qti2/templates";
        $smarty->compile_dir  = "$path";
        return $smarty;
    }

/**
 * converts an array of objects to an array of arrays (not recursively)
 *
 * @param array $objectarray
 * @return array - an array of answer arrays
 */
    function objects_to_array($objectarray)
    {
        $arrayarray = array();
        foreach ($objectarray as $object) {
            $arrayarray[] = (array)$object;
        }
        return $arrayarray;
    }

/**
 * gets a question's cloze answer objects as arrays containing only arrays and basic data types
 *
 * @param object $question
 * @return array - an array of answer arrays
 */
    function get_cloze_answers_array($question) {
        $answers = $this->get_answers($question);
        $this->xml_entitize($answers);
        foreach ($answers as $answerkey => $answer) {
            $answers[$answerkey]->subanswers = $this->objects_to_array($answer->subanswers);
        }
        return $this->objects_to_array($answers);
    }

/**
 * gets an array with text and question arrays for the given cloze question
 *
 * To make smarty processing easier, the returned text and question sub-arrays have an equal number of elements.
 * If it is necessary to add a dummy element to the question sub-array, the question will be given an id of CLOZE_TRAILING_TEXT_ID.
 *
 * @param object $question
 * @param array $answers - an array of arrays containing the question's answers
 * @param string $allowabletags - tags not to strip out of the question text (e.g. '<i><br>')
 * @return array with text and question arrays for the given cloze question
 */
     function get_cloze_questions($question, $answers, $allowabletags) {
        $questiontext = strip_tags($question->questiontext, $allowabletags);
        if (preg_match_all('/(.*){#([0-9]+)}/U', $questiontext, $matches)) {
            // matches[1] contains the text inbetween the question blanks
            // matches[2] contains the id of the question blanks (db: question_multianswer.positionkey)

            // find any trailing text after the last {#XX} and add it to the array
            if (preg_match('/.*{#[0-9]+}(.*)$/', $questiontext, $tail)) {
                $matches[1][] = $tail[1];
                $tailadded = true;
            }
            $questions['text'] = $matches[1];
            $questions['question'] = array();
            foreach ($matches[2] as $key => $questionid) {
                foreach ($answers as $answer) {
                    if ($answer['positionkey'] == $questionid) {
                        $questions['question'][$key] = $answer;
                        break;
                    }
                }
            }
            if ($tailadded) {
                // to have a matching number of question and text array entries:
                $questions['question'][] = array('id'=>CLOZE_TRAILING_TEXT_ID, 'answertype'=>SHORTANSWER);
            }

        } else {
            $questions['text'][0] = $question->questiontext;
            $questions['question'][0] = array('id'=>CLOZE_TRAILING_TEXT_ID, 'answertype'=>SHORTANSWER);
        }

        return $questions;
    }

/**
 * strips out the <intro>...</intro> section, if any, and returns the text
 *
 * changes the text object passed to it.
 *
 * @param string $&text
 * @return string the intro text, if there was an intro tag. '' otherwise.
 */
    function get_cloze_intro(&$text) {
        if (preg_match('/(.*)?\<intro>(.+)?\<\/intro>(.*)/s', $text, $matches)) {
            $text = $matches[1] . $matches[3];
            return $matches[2];
        }
        else {
            return '';
        }
    }


/**
 * adds qti metadata properties to the questions
 *
 * The passed array of questions is altered by this function
 *
 * @param &questions an array of question objects
 */
    function add_qti_info(&$questions)
    {
        foreach ($questions as $key=>$question) {
            $questions[$key]->qtiinteractiontype = $this->get_qti_interaction_type($question->qtype);
            $questions[$key]->qtiscoreable = $this->get_qti_scoreable($question);
            $questions[$key]->qtisolutionavailable = $this->get_qti_solution_available($question);
        }

    }

/**
 * returns whether or not a given question is scoreable
 *
 * @param object $question
 * @return boolean
 */
    function get_qti_scoreable($question) {
        switch ($question->qtype) {
            case DESCRIPTION:
                return 'false';
            default:
                return 'true';
        }
    }

/**
 * returns whether or not a solution is available for a given question
 *
 * The results are based on whether or not Moodle stores answers for the given question type
 *
 * @param object $question
 * @return boolean
 */
    function get_qti_solution_available($question) {
        switch($question->qtype) {
            case TRUEFALSE:
                return 'true';
            case MULTICHOICE:
                return 'true';
            case SHORTANSWER:
                return 'true';
            case NUMERICAL:
                return 'true';
            case MATCH:
                return 'true';
            case DESCRIPTION:
                return 'false';
            case MULTIANSWER:
                return 'true';
            default:
                return 'true';
        }

    }

/**
 * maps a moodle question type to a qti 2.0 question type
 *
 * @param int type_id - the moodle question type
 * @return string qti 2.0 question type
 */
    function get_qti_interaction_type($type_id) {
        switch( $type_id ) {
        case TRUEFALSE:
            $name = 'choiceInteraction';
            break;
        case MULTICHOICE:
            $name = 'choiceInteraction';
            break;
        case SHORTANSWER:
            $name = 'textInteraction';
            break;
        case NUMERICAL:
            $name = 'textInteraction';
            break;
        case MATCH:
            $name = 'matchInteraction';
            break;
        case DESCRIPTION:
            $name = 'extendedTextInteraction';
            break;
        case MULTIANSWER:
            $name = 'textInteraction';
            break;
        default:
            $name = 'textInteraction';
        }
        return $name;
    }

/**
 * returns the given array, shuffled
 *
 *
 * @param array $things
 * @return array
 */
    function shuffle_things($things) {
        $things = swapshuffle_assoc($things);
        $oldthings = $things;
        $things = array();
        foreach ($oldthings as $key=>$value) {
            $things[] = $value;      // This loses the index key, but doesn't matter
        }
        return $things;
    }

/**
 * returns a flattened image name - with all /, \ and : replaced with other characters
 *
 * used to convert a file or url to a qti-permissable identifier
 *
 * @param string name
 * @return string
 */
    function flatten_image_name($name) {
        return str_replace(array('/', '\\', ':'), array ('_','-','.'), $name);
    }

    function file_full_path($file, $courseid) {
        global $CFG;
        if (substr(strtolower($file), 0, 7) == 'http://') {
            $url = $file;
        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $url = "{$CFG->wwwroot}/file.php/$courseid/{$file}";
        } else {
            $url = "{$CFG->wwwroot}/file.php?file=/$courseid/{$file}";
        }
        return $url;
    }

}

?>