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
 * Blackboard 6.0 question importer.
 *
 * @package    qformat
 * @subpackage blackboard_six
 * @copyright  2005 Michael Penney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/xmlize.php');


/**
 * Blackboard 6.0 question importer.
 *
 * @copyright  2005 Michael Penney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard_six extends qformat_default {
    function provide_import() {
        return true;
    }

    public function can_import_file($file) {
        $mimetypes = array(
            mimeinfo('type', '.dat'),
            mimeinfo('type', '.zip')
        );
        return in_array($file->get_mimetype(), $mimetypes);
    }


    //Function to check and create the needed dir to unzip file to
    function check_and_create_import_dir($unique_code) {

        global $CFG;

        $status = $this->check_dir_exists($CFG->dataroot."/temp",true);
        if ($status) {
            $status = $this->check_dir_exists($CFG->dataroot."/temp/bbquiz_import",true);
        }
        if ($status) {
            $status = $this->check_dir_exists($CFG->dataroot."/temp/bbquiz_import/".$unique_code,true);
        }

        return $status;
    }

    function clean_temp_dir($dir='') {
        global $CFG;

        // for now we will just say everything happened okay note
        // that a mess may be piling up in $CFG->dataroot/temp/bbquiz_import
        // TODO return true at top of the function renders all the following code useless
        return true;

        if ($dir == '') {
            $dir = $this->temp_dir;
        }
        $slash = "/";

        // Create arrays to store files and directories
        $dir_files      = array();
        $dir_subdirs    = array();

        // Make sure we can delete it
        chmod($dir, $CFG->directorypermissions);

        if ((($handle = opendir($dir))) == FALSE) {
            // The directory could not be opened
            return false;
        }

        // Loop through all directory entries, and construct two temporary arrays containing files and sub directories
        while(false !== ($entry = readdir($handle))) {
            if (is_dir($dir. $slash .$entry) && $entry != ".." && $entry != ".") {
                $dir_subdirs[] = $dir. $slash .$entry;
            }
            else if ($entry != ".." && $entry != ".") {
                $dir_files[] = $dir. $slash .$entry;
            }
        }

        // Delete all files in the curent directory return false and halt if a file cannot be removed
        $countdir_files = count($dir_files);
        for($i=0; $i<$countdir_files; $i++) {
            chmod($dir_files[$i], $CFG->directorypermissions);
            if (((unlink($dir_files[$i]))) == FALSE) {
                return false;
            }
        }

        // Empty sub directories and then remove the directory
        $countdir_subdirs = count($dir_subdirs);
        for($i=0; $i<$countdir_subdirs; $i++) {
            chmod($dir_subdirs[$i], $CFG->directorypermissions);
            if ($this->clean_temp_dir($dir_subdirs[$i]) == FALSE) {
                return false;
            }
            else {
                if (rmdir($dir_subdirs[$i]) == FALSE) {
                return false;
                }
            }
        }

        // Close directory
        closedir($handle);
        if (rmdir($this->temp_dir) == FALSE) {
            return false;
        }
        // Success, every thing is gone return true
        return true;
    }

    //Function to check if a directory exists and, optionally, create it
    function check_dir_exists($dir,$create=false) {

        global $CFG;

        $status = true;
        if(!is_dir($dir)) {
            if (!$create) {
                $status = false;
            } else {
                umask(0000);
                $status = mkdir ($dir,$CFG->directorypermissions);
            }
        }
        return $status;
    }

    function importpostprocess() {
    /// Does any post-processing that may be desired
    /// Argument is a simple array of question ids that
    /// have just been added.

        // need to clean up temporary directory
        return $this->clean_temp_dir();
    }

    function copy_file_to_course($filename) {
        global $CFG, $COURSE;
        $filename = str_replace('\\','/',$filename);
        $fullpath = $this->temp_dir.'/res00001/'.$filename;
        $basename = basename($filename);

        $copy_to = $CFG->dataroot.'/'.$COURSE->id.'/bb_import';

        if ($this->check_dir_exists($copy_to,true)) {
            if(is_readable($fullpath)) {
                $copy_to.= '/'.$basename;
                if (!copy($fullpath, $copy_to)) {
                    return false;
                }
                else {
                    return $copy_to;
                }
            }
        }
        else {
            return false;
        }
    }

    function readdata($filename) {
    /// Returns complete file with an array, one item per line
        global $CFG;

        // if the extension is .dat we just return that,
        // if .zip we unzip the file and get the data
        $ext = substr($this->realfilename, strpos($this->realfilename,'.'), strlen($this->realfilename)-1);
        if ($ext=='.dat') {
            if (!is_readable($filename)) {
                print_error('filenotreadable', 'error');
            }
            return file($filename);
        }

        $unique_code = time();
        $temp_dir = $CFG->dataroot."/temp/bbquiz_import/".$unique_code;
        $this->temp_dir = $temp_dir;
        if ($this->check_and_create_import_dir($unique_code)) {
            if(is_readable($filename)) {
                if (!copy($filename, "$temp_dir/bboard.zip")) {
                    print_error('cannotcopybackup', 'question');
                }
                if(unzip_file("$temp_dir/bboard.zip", '', false)) {
                    // assuming that the information is in res0001.dat
                    // after looking at 6 examples this was always the case
                    $q_file = "$temp_dir/res00001.dat";
                    if (is_file($q_file)) {
                        if (is_readable($q_file)) {
                            $filearray = file($q_file);
                            /// Check for Macintosh OS line returns (ie file on one line), and fix
                            if (preg_match("~\r~", $filearray[0]) AND !preg_match("~\n~", $filearray[0])) {
                                return explode("\r", $filearray[0]);
                            } else {
                                return $filearray;
                            }
                        }
                    }
                    else {
                        print_error('cannotfindquestionfile', 'questioni');
                    }
                }
                else {
                    print "filename: $filename<br />tempdir: $temp_dir <br />";
                    print_error('cannotunzip', 'question');
                }
            }
            else {
                print_error('cannotreaduploadfile');
            }
        }
        else {
            print_error('cannotcreatetempdir');
        }
    }

    function save_question_options($question) {
        return true;
    }



  function readquestions ($lines) {
    /// Parses an array of lines into an array of questions,
    /// where each item is a question object as defined by
    /// readquestion().

    $text = implode($lines, " ");
    $xml = xmlize($text, 0);

    $raw_questions = $xml['questestinterop']['#']['assessment'][0]['#']['section'][0]['#']['item'];
    $questions = array();

    foreach($raw_questions as $quest) {
        $question = $this->create_raw_question($quest);

        switch($question->qtype) {
            case "Matching":
                $this->process_matching($question, $questions);
                break;
            case "Multiple Choice":
                $this->process_mc($question, $questions);
                break;
            case "Essay":
                $this->process_essay($question, $questions);
                break;
            case "Multiple Answer":
                $this->process_ma($question, $questions);
                break;
            case "True/False":
                $this->process_tf($question, $questions);
                break;
            case 'Fill in the Blank':
                $this->process_fblank($question, $questions);
                break;
            case 'Short Response':
                $this->process_essay($question, $questions);
                break;
            default:
                print "Unknown or unhandled question type: \"$question->qtype\"<br />";
                break;
        }

    }
    return $questions;
  }


// creates a cleaner object to deal with for processing into moodle
// the object created is NOT a moodle question object
function create_raw_question($quest) {

    $question = new stdClass();
    $question->qtype = $quest['#']['itemmetadata'][0]['#']['bbmd_questiontype'][0]['#'];
    $question->id = $quest['#']['itemmetadata'][0]['#']['bbmd_asi_object_id'][0]['#'];
    $presentation->blocks = $quest['#']['presentation'][0]['#']['flow'][0]['#']['flow'];

    foreach($presentation->blocks as $pblock) {

        $block = NULL;
        $block->type = $pblock['@']['class'];

        switch($block->type) {
            case 'QUESTION_BLOCK':
                $sub_blocks = $pblock['#']['flow'];
                foreach($sub_blocks as $sblock) {
                    //echo "Calling process_block from line 263<br>";
                    $this->process_block($sblock, $block);
                }
                break;

            case 'RESPONSE_BLOCK':
                $choices = NULL;
                switch($question->qtype) {
                    case 'Matching':
                        $bb_subquestions = $pblock['#']['flow'];
                        $sub_questions = array();
                        foreach($bb_subquestions as $bb_subquestion) {
                            $sub_question = NULL;
                            $sub_question->ident = $bb_subquestion['#']['response_lid'][0]['@']['ident'];
                            $this->process_block($bb_subquestion['#']['flow'][0], $sub_question);
                            $bb_choices = $bb_subquestion['#']['response_lid'][0]['#']['render_choice'][0]['#']['flow_label'][0]['#']['response_label'];
                            $choices = array();
                            $this->process_choices($bb_choices, $choices);
                            $sub_question->choices = $choices;
                            if (!isset($block->subquestions)) {
                                $block->subquestions = array();
                            }
                            $block->subquestions[] = $sub_question;
                        }
                        break;
                    case 'Multiple Answer':
                        $bb_choices = $pblock['#']['response_lid'][0]['#']['render_choice'][0]['#']['flow_label'];
                        $choices = array();
                        $this->process_choices($bb_choices, $choices);
                        $block->choices = $choices;
                        break;
                    case 'Essay':
                        // Doesn't apply since the user responds with text input
                        break;
                    case 'Multiple Choice':
                        $mc_choices = $pblock['#']['response_lid'][0]['#']['render_choice'][0]['#']['flow_label'];
                            foreach($mc_choices as $mc_choice) {
                            $choices = NULL;
                            $choices = $this->process_block($mc_choice, $choices);
                            $block->choices[] = $choices;
                        }
                        break;
                    case 'Short Response':
                        // do nothing?
                        break;
                    case 'Fill in the Blank':
                        // do nothing?
                        break;
                    default:
                        $bb_choices = $pblock['#']['response_lid'][0]['#']['render_choice'][0]['#']['flow_label'][0]['#']['response_label'];
                        $choices = array();
                        $this->process_choices($bb_choices, $choices);
                        $block->choices = $choices;
                }
                break;
            case 'RIGHT_MATCH_BLOCK':
                $matching_answerset = $pblock['#']['flow'];

                $answerset = array();
                foreach($matching_answerset as $answer) {
                    // $answerset[] = $this->process_block($answer, $bb_answer);
                    $bb_answer = null;
                    $bb_answer->text = $answer['#']['flow'][0]['#']['material'][0]['#']['mat_extension'][0]['#']['mat_formattedtext'][0]['#'];
                    $answerset[] = $bb_answer;
                }
                $block->matching_answerset = $answerset;
                break;
            default:
                print "UNHANDLED PRESENTATION BLOCK";
                break;
        }
        $question->{$block->type} = $block;
    }

    // determine response processing
    // there is a section called 'outcomes' that I don't know what to do with
    $resprocessing = $quest['#']['resprocessing'];
    $respconditions = $resprocessing[0]['#']['respcondition'];
    $reponses = array();
    if ($question->qtype == 'Matching') {
        $this->process_matching_responses($respconditions, $responses);
    }
    else {
        $this->process_responses($respconditions, $responses);
    }
    $question->responses = $responses;
    $feedbackset = $quest['#']['itemfeedback'];
    $feedbacks = array();
    $this->process_feedback($feedbackset, $feedbacks);
    $question->feedback = $feedbacks;
    return $question;
}

function process_block($cur_block, &$block) {
    global $COURSE, $CFG;

    $cur_type = $cur_block['@']['class'];
    switch($cur_type) {
        case 'FORMATTED_TEXT_BLOCK':
            $block->text = $this->strip_applet_tags_get_mathml($cur_block['#']['material'][0]['#']['mat_extension'][0]['#']['mat_formattedtext'][0]['#']);
            break;
        case 'FILE_BLOCK':
            //revisit this to make sure it is working correctly
            // Commented out ['matapplication']..., etc. because I
            // noticed that when I imported a new Blackboard 6 file
            // and printed out the block, the tree did not extend past ['material'][0]['#'] - CT 8/3/06
            $block->file = $cur_block['#']['material'][0]['#'];//['matapplication'][0]['@']['uri'];
            if ($block->file != '') {
                // if we have a file copy it to the course dir and adjust its name to be visible over the web.
                $block->file = $this->copy_file_to_course($block->file);
                $block->file = $CFG->wwwroot.'/file.php/'.$COURSE->id.'/bb_import/'.basename($block->file);
            }
            break;
        case 'Block':
            if (isset($cur_block['#']['material'][0]['#']['mattext'][0]['#'])) {
            $block->text = $cur_block['#']['material'][0]['#']['mattext'][0]['#'];
            }
            else if (isset($cur_block['#']['material'][0]['#']['mat_extension'][0]['#']['mat_formattedtext'][0]['#'])) {
                $block->text = $cur_block['#']['material'][0]['#']['mat_extension'][0]['#']['mat_formattedtext'][0]['#'];
            }
            else if (isset($cur_block['#']['response_label'])) {
                // this is a response label block
                $sub_blocks = $cur_block['#']['response_label'][0];
                if(!isset($block->ident)) {
                    if(isset($sub_blocks['@']['ident'])) {
                        $block->ident = $sub_blocks['@']['ident'];
                    }
                }
                foreach($sub_blocks['#']['flow_mat'] as $sub_block) {
                    $this->process_block($sub_block, $block);
                }
            }
            else {
                if (isset($cur_block['#']['flow_mat']) || isset($cur_block['#']['flow'])) {
                    if (isset($cur_block['#']['flow_mat'])) {
                        $sub_blocks = $cur_block['#']['flow_mat'];
                    }
                    elseif (isset($cur_block['#']['flow'])) {
                        $sub_blocks = $cur_block['#']['flow'];
                    }
                   foreach ($sub_blocks as $sblock) {
                        // this will recursively grab the sub blocks which should be of one of the other types
                        $this->process_block($sblock, $block);
                    }
                }
            }
            break;
        case 'LINK_BLOCK':
            // not sure how this should be included
            if (!empty($cur_block['#']['material'][0]['#']['mattext'][0]['@']['uri'])) {
                $block->link = $cur_block['#']['material'][0]['#']['mattext'][0]['@']['uri'];
            }
            else {
               $block->link = '';
            }
            break;
    }
    return $block;
}

function process_choices($bb_choices, &$choices) {
    foreach($bb_choices as $choice) {
            if (isset($choice['@']['ident'])) {
            $cur_choice = $choice['@']['ident'];
        }
        else { //for multiple answer
            $cur_choice = $choice['#']['response_label'][0];//['@']['ident'];
        }
        if (isset($choice['#']['flow_mat'][0])) { //for multiple answer
            $cur_block = $choice['#']['flow_mat'][0];
            // Reset $cur_choice to NULL because process_block is expecting an object
            // for the second argument and not a string, which is what is was set as
            // originally - CT 8/7/06
            $cur_choice = null;
            $this->process_block($cur_block, $cur_choice);
        }
        elseif (isset($choice['#']['response_label'])) {
            // Reset $cur_choice to NULL because process_block is expecting an object
            // for the second argument and not a string, which is what is was set as
            // originally - CT 8/7/06
            $cur_choice = null;
            $this->process_block($choice, $cur_choice);
        }
        $choices[] = $cur_choice;
    }
}

function process_matching_responses($bb_responses, &$responses) {
    foreach($bb_responses as $bb_response) {
        $response = NULL;
        if (isset($bb_response['#']['conditionvar'][0]['#']['varequal'])) {
            $response->correct = $bb_response['#']['conditionvar'][0]['#']['varequal'][0]['#'];
            $response->ident = $bb_response['#']['conditionvar'][0]['#']['varequal'][0]['@']['respident'];
        }
        else {
            $response->correct =  'Broken Question?';
            $response->ident = 'Broken Question?';
        }
        $response->feedback = $bb_response['#']['displayfeedback'][0]['@']['linkrefid'];
        $responses[] = $response;
    }
}

function process_responses($bb_responses, &$responses) {
    foreach($bb_responses as $bb_response) {
        //Added this line to instantiate $response.
        // Without instantiating the $response variable, the same object
        // gets added to the array
        $response = new stdClass();
        if (isset($bb_response['@']['title'])) {
                $response->title = $bb_response['@']['title'];
            }
            else {
                $reponse->title = $bb_response['#']['displayfeedback'][0]['@']['linkrefid'];
            }
            $reponse->ident = array();
            if (isset($bb_response['#']['conditionvar'][0]['#'])){//['varequal'][0]['#'])) {
                $response->ident[0] = $bb_response['#']['conditionvar'][0]['#'];//['varequal'][0]['#'];
            }
            else if (isset($bb_response['#']['conditionvar'][0]['#']['other'][0]['#'])) {
                $response->ident[0] = $bb_response['#']['conditionvar'][0]['#']['other'][0]['#'];
            }

            if (isset($bb_response['#']['conditionvar'][0]['#']['and'])){//[0]['#'])) {
                $responseset = $bb_response['#']['conditionvar'][0]['#']['and'];//[0]['#']['varequal'];
                foreach($responseset as $rs) {
                    $response->ident[] = $rs['#'];
                    if(!isset($response->feedback) and isset( $rs['@'] ) ) {
                        $response->feedback = $rs['@']['respident'];
                    }
                }
            }
            else {
                $response->feedback = $bb_response['#']['displayfeedback'][0]['@']['linkrefid'];
            }

            // determine what point value to give response
            if (isset($bb_response['#']['setvar'])) {
                switch ($bb_response['#']['setvar'][0]['#']) {
                    case "SCORE.max":
                        $response->fraction = 1;
                        break;
                    default:
                        // I have only seen this being 0 or unset
                        // there are probably fractional values of SCORE.max, but I'm not sure what they look like
                        $response->fraction = 0;
                        break;
                }
            }
            else {
               // just going to assume this is the case this is probably not correct.
               $response->fraction = 0;
            }

            $responses[] = $response;
        }
}

function process_feedback($feedbackset, &$feedbacks) {
    foreach($feedbackset as $bb_feedback) {
        // Added line $feedback=null so that $feedback does not get reused in the loop
        // and added the the $feedbacks[] array multiple times
        $feedback = null;
        $feedback->ident = $bb_feedback['@']['ident'];
        if (isset($bb_feedback['#']['flow_mat'][0])) {
            $this->process_block($bb_feedback['#']['flow_mat'][0], $feedback);
        }
        elseif (isset($bb_feedback['#']['solution'][0]['#']['solutionmaterial'][0]['#']['flow_mat'][0])) {
            $this->process_block($bb_feedback['#']['solution'][0]['#']['solutionmaterial'][0]['#']['flow_mat'][0], $feedback);
        }
        $feedbacks[] = $feedback;
    }
}

/**
 * Create common parts of question
 */
function process_common( $quest ) {
    $question = $this->defaultquestion();
    $question->questiontext = $quest->QUESTION_BLOCK->text;
    $question->name = shorten_text( $quest->id, 250 );

    return $question;
}

//----------------------------------------
// Process True / False Questions
//----------------------------------------
function process_tf($quest, &$questions) {
    $question = $this->process_common( $quest );

    $question->qtype = TRUEFALSE;
    $question->single = 1; // Only one answer is allowed
    // 0th [response] is the correct answer.
    $responses = $quest->responses;
    $correctresponse = $responses[0]->ident[0]['varequal'][0]['#'];
    if ($correctresponse != 'false') {
        $correct = true;
    }
    else {
        $correct = false;
    }

    foreach($quest->feedback as $fb) {
        $fback->{$fb->ident} = $fb->text;
    }

    if ($correct) {  // true is correct
        $question->answer = 1;
        $question->feedbacktrue = $fback->correct;
        $question->feedbackfalse = $fback->incorrect;
    } else {  // false is correct
        $question->answer = 0;
        $question->feedbacktrue = $fback->incorrect;
        $question->feedbackfalse = $fback->correct;
    }
    $question->correctanswer = $question->answer;
    $questions[] = $question;
}


//----------------------------------------
// Process Fill in the Blank
//----------------------------------------
function process_fblank($quest, &$questions) {
    $question = $this->process_common( $quest );
    $question->qtype = SHORTANSWER;
    $question->single = 1;

    $answers = array();
    $fractions = array();
    $feedbacks = array();

    // extract the feedback
    $feedback = array();
    foreach($quest->feedback as $fback) {
        if (isset($fback->ident)) {
            if ($fback->ident == 'correct' || $fback->ident == 'incorrect') {
                $feedback[$fback->ident] = $fback->text;
            }
        }
    }

    foreach($quest->responses as $response) {
        if(isset($response->title)) {
            if (isset($response->ident[0]['varequal'][0]['#'])) {
                //for BB Fill in the Blank, only interested in correct answers
                if ($response->feedback = 'correct') {
                    $answers[] = $response->ident[0]['varequal'][0]['#'];
                    $fractions[] = 1;
                    if (isset($feedback['correct'])) {
                        $feedbacks[] = $feedback['correct'];
                    }
                    else {
                        $feedbacks[] = '';
                    }
                }
            }

        }
    }

    //Adding catchall to so that students can see feedback for incorrect answers when they enter something the
    //instructor did not enter
    $answers[] = '*';
    $fractions[] = 0;
    if (isset($feedback['incorrect'])) {
        $feedbacks[] = $feedback['incorrect'];
    }
    else {
        $feedbacks[] = '';
    }

    $question->answer = $answers;
    $question->fraction = $fractions;
    $question->feedback = $feedbacks; // Changed to assign $feedbacks to $question->feedback instead of

    if (!empty($question)) {
        $questions[] = $question;
    }

}

//----------------------------------------
// Process Multiple Choice Questions
//----------------------------------------
function process_mc($quest, &$questions) {
    $question = $this->process_common( $quest );
    $question->qtype = MULTICHOICE;
    $question->single = 1;

    $feedback = array();
    foreach($quest->feedback as $fback) {
        $feedback[$fback->ident] = $fback->text;
    }

    foreach($quest->responses as $response) {
        if (isset($response->title)) {
            if ($response->title == 'correct') {
                // only one answer possible for this qtype so first index is correct answer
                $correct = $response->ident[0]['varequal'][0]['#'];
            }
        }
        else {
            // fallback method for when the title is not set
            if ($response->feedback == 'correct') {
               // only one answer possible for this qtype so first index is correct answer
               $correct = $response->ident[0]['varequal'][0]['#']; // added [0]['varequal'][0]['#'] to $response->ident - CT 8/9/06
            }
        }
    }

    $i = 0;
    foreach($quest->RESPONSE_BLOCK->choices as $response) {
        $question->answer[$i] = $response->text;
        if ($correct == $response->ident) {
            $question->fraction[$i] = 1;
            // this is a bit of a hack to catch the feedback... first we see if a 'correct' feedback exists
            // then specific feedback for this question (maybe this should be switched?, but from my example
            // question pools I have not seen response specific feedback, only correct or incorrect feedback
            if (!empty($feedback['correct'])) {
                $question->feedback[$i] = $feedback['correct'];
            }
            elseif (!empty($feedback[$i])) {
                $question->feedback[$i] = $feedback[$i];
            }
            else {
                // failsafe feedback (should be '' instead?)
                $question->feedback[$i] = "correct";
            }
        }
        else {
            $question->fraction[$i] = 0;
            if (!empty($feedback['incorrect'])) {
                $question->feedback[$i] = $feedback['incorrect'];
            }
            elseif (!empty($feedback[$i])) {
                $question->feedback[$i] = $feedback[$i];
            }
            else {
                // failsafe feedback (should be '' instead?)
                $question->feedback[$i] = 'incorrect';
            }
        }
        $i++;
    }

    if (!empty($question)) {
        $questions[] = $question;
    }
}

//----------------------------------------
// Process Multiple Choice Questions With Multiple Answers
//----------------------------------------
function process_ma($quest, &$questions) {
    $question = $this->process_common( $quest ); // copied this from process_mc
    $question->qtype = MULTICHOICE;
    $question->single = 0; // More than one answer allowed

    $answers = $quest->responses;
    $correct_answers = array();
    foreach($answers as $answer) {
        if($answer->title == 'correct') {
            $answerset = $answer->ident[0]['and'][0]['#']['varequal'];
            foreach($answerset as $ans) {
                $correct_answers[] = $ans['#'];
            }
        }
    }

    foreach ($quest->feedback as $fb) {
        $feedback->{$fb->ident} = trim($fb->text);
    }

    $correct_answer_count = count($correct_answers);
    $choiceset = $quest->RESPONSE_BLOCK->choices;
    $i = 0;
    foreach($choiceset as $choice) {
        $question->answer[$i] = trim($choice->text);
        if (in_array($choice->ident, $correct_answers)) {
            // correct answer
            $question->fraction[$i] = floor(100000/$correct_answer_count)/100000; // strange behavior if we have more than 5 decimal places
            $question->feedback[$i] = $feedback->correct;
        }
        else {
            // wrong answer
            $question->fraction[$i] = 0;
            $question->feedback[$i] = $feedback->incorrect;
        }
        $i++;
    }

    $questions[] = $question;
}

//----------------------------------------
// Process Essay Questions
//----------------------------------------
function process_essay($quest, &$questions) {
// this should be rewritten to accomodate moodle 1.6 essay question type eventually

    if (defined("ESSAY")) {
        // treat as short answer
        $question = $this->process_common( $quest ); // copied this from process_mc
        $question->qtype = ESSAY;

        $question->feedback = array();
        // not sure where to get the correct answer from
        foreach($quest->feedback as $feedback) {
            // Added this code to put the possible solution that the
            // instructor gives as the Moodle answer for an essay question
            if ($feedback->ident == 'solution') {
                $question->feedback = $feedback->text;
            }
        }
        //Added because essay/questiontype.php:save_question_option is expecting a
        //fraction property - CT 8/10/06
        $question->fraction[] = 1;
        if (!empty($question)) {
            $questions[]=$question;
        }
    }
    else {
        print "Essay question types are not handled because the quiz question type 'Essay' does not exist in this installation of Moodle<br/>";
        print "&nbsp;&nbsp;&nbsp;&nbsp;Omitted Question: ".$quest->QUESTION_BLOCK->text.'<br/><br/>';
    }
}

//----------------------------------------
// Process Matching Questions
//----------------------------------------
function process_matching($quest, &$questions) {
    // renderedmatch is an optional plugin, so we need to check if it is defined
    if (question_bank::is_qtype_installed('renderedmatch')) {
        $question = $this->process_common($quest);
        $question->valid = true;
        $question->qtype = 'renderedmatch';

        foreach($quest->RESPONSE_BLOCK->subquestions as $qid => $subq) {
            foreach($quest->responses as $rid => $resp) {
                if ($resp->ident == $subq->ident) {
                    $correct = $resp->correct;
                    $feedback = $resp->feedback;
                }
            }

            foreach($subq->choices as $cid => $choice) {
                if ($choice == $correct) {
                    $question->subquestions[] = $subq->text;
                    $question->subanswers[] = $quest->RIGHT_MATCH_BLOCK->matching_answerset[$cid]->text;
                }
            }
        }

        // check format
        $status = true;
        if ( count($quest->RESPONSE_BLOCK->subquestions) > count($quest->RIGHT_MATCH_BLOCK->matching_answerset) || count($question->subquestions) < 2) {
            $status = false;
        }
        else {
            // need to redo to make sure that no two questions have the same answer (rudimentary now)
            foreach($question->subanswers as $qstn) {
                if(isset($previous)) {
                    if ($qstn == $previous) {
                        $status = false;
                    }
                }
                $previous = $qstn;
                if ($qstn == '') {
                    $status = false;
                }
            }
        }

        if ($status) {
            $questions[] = $question;
        }
        else {
            global $COURSE, $CFG;
            print '<table class="boxaligncenter" border="1">';
            print '<tr><td colspan="2" style="background-color:#FF8888;">This matching question is malformed. Please ensure there are no blank answers, no two questions have the same answer, and/or there are correct answers for each question. There must be at least as many subanswers as subquestions, and at least one subquestion.</td></tr>';

            print "<tr><td>Question:</td><td>".$quest->QUESTION_BLOCK->text;
            if (isset($quest->QUESTION_BLOCK->file)) {
                print '<br/><font color="red">There is a subfile contained in the zipfile that has been copied to course files: bb_import/'.basename($quest->QUESTION_BLOCK->file).'</font>';
                if (preg_match('/(gif|jpg|jpeg|png)$/i', $quest->QUESTION_BLOCK->file)) {
                    print '<img src="'.$CFG->wwwroot.'/file.php/'.$COURSE->id.'/bb_import/'.basename($quest->QUESTION_BLOCK->file).'" />';
                }
            }
            print "</td></tr>";
            print "<tr><td>Subquestions:</td><td><ul>";
            foreach($quest->responses as $rs) {
                $correct_responses->{$rs->ident} = $rs->correct;
            }
            foreach($quest->RESPONSE_BLOCK->subquestions as $subq) {
                print '<li>'.$subq->text.'<ul>';
                foreach($subq->choices as $id=>$choice) {
                    print '<li>';
                    if ($choice == $correct_responses->{$subq->ident}) {
                        print '<font color="green">';
                    }
                    else {
                        print '<font color="red">';
                    }
                    print $quest->RIGHT_MATCH_BLOCK->matching_answerset[$id]->text.'</font></li>';
                }
                print '</ul>';
            }
            print '</ul></td></tr>';

            print '<tr><td>Feedback:</td><td><ul>';
            foreach($quest->feedback as $fb) {
                print '<li>'.$fb->ident.': '.$fb->text.'</li>';
            }
            print '</ul></td></tr></table>';
        }
    }
    else {
        print "Matching question types are not handled because the quiz question type 'Rendered Matching' does not exist in this installation of Moodle<br/>";
        print "&nbsp;&nbsp;&nbsp;&nbsp;Omitted Question: ".$quest->QUESTION_BLOCK->text.'<br/><br/>';
    }
}


function strip_applet_tags_get_mathml($string) {
    if(stristr($string, '</APPLET>') === FALSE) {
        return $string;
    }
    else {
        // strip all applet tags keeping stuff before/after and inbetween (if mathml) them
        while (stristr($string, '</APPLET>') !== FALSE) {
            preg_match("/(.*)\<applet.*value=\"(\<math\>.*\<\/math\>)\".*\<\/applet\>(.*)/i",$string, $mathmls);
            $string = $mathmls[1].$mathmls[2].$mathmls[3];
        }
        return $string;
    }
}

} // close object

