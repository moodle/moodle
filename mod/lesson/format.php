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
 * format.php  - Default format class for file imports/exports. Doesn't do
 * everything on it's own -- it needs to be extended.
 *
 * Included by import.ph
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**#@+
 * The core question types.
 *
 * These used to be in lib/questionlib.php, but are being deprecated. Copying them
 * here to keep this code working for now.
 */
if (!defined('SHORTANSWER')) {
    define("SHORTANSWER",   "shortanswer");
    define("TRUEFALSE",     "truefalse");
    define("MULTICHOICE",   "multichoice");
    define("RANDOM",        "random");
    define("MATCH",         "match");
    define("RANDOMSAMATCH", "randomsamatch");
    define("DESCRIPTION",   "description");
    define("NUMERICAL",     "numerical");
    define("MULTIANSWER",   "multianswer");
    define("CALCULATED",    "calculated");
    define("ESSAY",         "essay");
}
/**#@-*/

/**
 * Given some question info and some data about the the answers
 * this function parses, organises and saves the question
 *
 * This is only used when IMPORTING questions and is only called
 * from format.php
 * Lifted from mod/quiz/lib.php -
 *    1. all reference to oldanswers removed
 *    2. all reference to quiz_multichoice table removed
 *    3. In SHORTANSWER questions usecase is store in the qoption field
 *    4. In NUMERIC questions store the range as two answers
 *    5. TRUEFALSE options are ignored
 *    6. For MULTICHOICE questions with more than one answer the qoption field is true
 *
 * @param opject $question Contains question data like question, type and answers.
 * @return object Returns $result->error or $result->notice.
 **/
function lesson_save_question_options($question, $lesson) {
    global $DB;

    // These lines are required to ensure that all page types have
    // been loaded for the following switch
    if (!($lesson instanceof lesson)) {
        $lesson = new lesson($lesson);
    }
    $manager = lesson_page_type_manager::get($lesson);

    $timenow = time();
    $result = new stdClass();
    switch ($question->qtype) {
        case LESSON_PAGE_SHORTANSWER:

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    if ($question->fraction[$key] >=0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }


            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
            break;

        case LESSON_PAGE_NUMERICAL:   // Note similarities to SHORTANSWER

            $answers = array();
            $maxfraction = -1;


            // for each answer store the pair of min and max values even if they are the same
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->jumpto = LESSON_NEXTPAGE;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $min = $question->answer[$key] - $question->tolerance[$key];
                    $max = $question->answer[$key] + $question->tolerance[$key];
                    $answer->answer   = $min.":".$max;
                    // $answer->answer   = $question->min[$key].":".$question->max[$key]; original line for min/max
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);

                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;


        case LESSON_PAGE_TRUEFALSE:

            // the truth
            $answer = new stdClass();
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("true", "quiz");
            $answer->grade = $question->correctanswer * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbacktrue)) {
                $answer->response = $question->feedbacktrue['text'];
                $answer->responseformat = $question->feedbacktrue['format'];
            }
            $DB->insert_record("lesson_answers", $answer);

            // the lie
            $answer = new stdClass;
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("false", "quiz");
            $answer->grade = (1 - (int)$question->correctanswer) * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbackfalse)) {
                $answer->response = $question->feedbackfalse['text'];
                $answer->responseformat = $question->feedbackfalse['format'];
            }
            $DB->insert_record("lesson_answers", $answer);

          break;

        case LESSON_PAGE_MULTICHOICE:

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    // changed some defaults
                    /* Original Code
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    Replaced with:                    */
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
                    // end Replace
                    $answer->answer   = $dataanswer['text'];
                    $answer->answerformat   = $dataanswer['format'];
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    // for Sanity checks
                    if ($question->fraction[$key] > 0) {
                        $totalfraction += $question->fraction[$key];
                    }
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($question->single) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->notice = get_string("fractionsaddwrong", "quiz", $totalfraction);
                    return $result;
                }
            }
        break;

        case LESSON_PAGE_MATCHING:

            $subquestions = array();

            $defaultanswer = new stdClass;
            $defaultanswer->lessonid   = $question->lessonid;
            $defaultanswer->pageid   = $question->id;
            $defaultanswer->timecreated   = $timenow;
            $defaultanswer->grade = 0;

            // The first answer should always be the correct answer
            $correctanswer = clone($defaultanswer);
            $correctanswer->answer = get_string('thatsthecorrectanswer', 'lesson');
            $DB->insert_record("lesson_answers", $correctanswer);

            // The second answer should always be the wrong answer
            $wronganswer = clone($defaultanswer);
            $wronganswer->answer = get_string('thatsthewronganswer', 'lesson');
            $DB->insert_record("lesson_answers", $wronganswer);

            $i = 0;
            // Insert all the new question+answer pairs
            foreach ($question->subquestions as $key => $questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    $answer = clone($defaultanswer);
                    $answer->answer = $questiontext['text'];
                    $answer->answerformat   = $questiontext['format'];
                    $answer->response   = $answertext;
                    if ($i == 0) {
                        // first answer contains the correct answer jump
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $subquestions[] = $DB->insert_record("lesson_answers", $answer);
                    $i++;
                }
            }

            if (count($subquestions) < 3) {
                $result->notice = get_string("notenoughsubquestions", "quiz");
                return $result;
            }
            break;
        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
    }
    return true;
}


class qformat_default {

    var $displayerrors = true;
    var $category = NULL;
    var $questionids = array();
    var $qtypeconvert = array(NUMERICAL   => LESSON_PAGE_NUMERICAL,
                              MULTICHOICE => LESSON_PAGE_MULTICHOICE,
                              TRUEFALSE   => LESSON_PAGE_TRUEFALSE,
                              SHORTANSWER => LESSON_PAGE_SHORTANSWER,
                              MATCH       => LESSON_PAGE_MATCHING
                              );

    // Importing functions
    function provide_import() {
        return false;
    }

    function importpreprocess() {
        // Does any pre-processing that may be desired
        return true;
    }

    function importprocess($filename, $lesson, $pageid) {
        global $DB, $OUTPUT;

    /// Processes a given file.  There's probably little need to change this
        $timenow = time();

        if (! $lines = $this->readdata($filename)) {
            echo $OUTPUT->notification("File could not be read, or was empty");
            return false;
        }

        if (! $questions = $this->readquestions($lines)) {   // Extract all the questions
            echo $OUTPUT->notification("There are no questions in this file!");
            return false;
        }

        //Avoid category as question type
        echo $OUTPUT->notification(get_string('importcount', 'lesson',
                $this->count_questions($questions)), 'notifysuccess');

        $count = 0;

        $unsupportedquestions = 0;

        foreach ($questions as $question) {   // Process and store each question
            switch ($question->qtype) {
                //TODO: Bad way to bypass category in data... Quickfix for MDL-27964
                case 'category':
                    break;
                // the good ones
                case SHORTANSWER :
                case NUMERICAL :
                case TRUEFALSE :
                case MULTICHOICE :
                case MATCH :
                    $count++;

                    //Show nice formated question in one line.
                    echo "<hr><p><b>$count</b>. ".$this->format_question_text($question)."</p>";

                    $newpage = new stdClass;
                    $newpage->lessonid = $lesson->id;
                    $newpage->qtype = $this->qtypeconvert[$question->qtype];
                    switch ($question->qtype) {
                        case SHORTANSWER :
                            if (isset($question->usecase)) {
                                $newpage->qoption = $question->usecase;
                            }
                            break;
                        case MULTICHOICE :
                            if (isset($question->single)) {
                                $newpage->qoption = !$question->single;
                            }
                            break;
                    }
                    $newpage->timecreated = $timenow;
                    if ($question->name != $question->questiontext) {
                        $newpage->title = $question->name;
                    } else {
                        $newpage->title = "Page $count";
                    }
                    $newpage->contents = $question->questiontext;

                    // set up page links
                    if ($pageid) {
                        // the new page follows on from this page
                        if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
                            print_error('invalidpageid', 'lesson');
                        }
                        $newpage->prevpageid = $pageid;
                        $newpage->nextpageid = $page->nextpageid;
                        // insert the page and reset $pageid
                        $newpageid = $DB->insert_record("lesson_pages", $newpage);
                        // update the linked list
                        $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $pageid));

                    } else {
                        // new page is the first page
                        // get the existing (first) page (if any)
                        $params = array ("lessonid" => $lesson->id, "prevpageid" => 0);
                        if (!$page = $DB->get_record_select("lesson_pages", "lessonid = :lessonid AND prevpageid = :prevpageid", $params)) {
                            // there are no existing pages
                            $newpage->prevpageid = 0; // this is a first page
                            $newpage->nextpageid = 0; // this is the only page
                            $newpageid = $DB->insert_record("lesson_pages", $newpage);
                        } else {
                            // there are existing pages put this at the start
                            $newpage->prevpageid = 0; // this is a first page
                            $newpage->nextpageid = $page->id;
                            $newpageid = $DB->insert_record("lesson_pages", $newpage);
                            // update the linked list
                            $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->id));
                        }
                    }
                    // reset $pageid and put the page ID in $question, used in save_question_option()
                    $pageid = $newpageid;
                    $question->id = $newpageid;

                    $this->questionids[] = $question->id;

                    // Now to save all the answers and type-specific options

                    $question->lessonid = $lesson->id; // needed for foreign key
                    $question->qtype = $this->qtypeconvert[$question->qtype];
                    $result = lesson_save_question_options($question, $lesson);

                    if (!empty($result->error)) {
                        echo $OUTPUT->notification($result->error);
                        return false;
                    }

                    if (!empty($result->notice)) {
                        echo $OUTPUT->notification($result->notice);
                        return true;
                    }
                    break;
            // the Bad ones
                default :
                    $unsupportedquestions++;
                    break;
            }

        }
        if ($unsupportedquestions) {
            echo $OUTPUT->notification(get_string('unknownqtypesnotimported', 'lesson', $unsupportedquestions));
        }
        return true;
    }

    /**
     * Count all non-category questions in the questions array.
     *
     * @param array questions An array of question objects.
     * @return int The count.
     *
     */
    protected function count_questions($questions) {
        $count = 0;
        if (!is_array($questions)) {
            return $count;
        }
        foreach ($questions as $question) {
            if (!is_object($question) || !isset($question->qtype) ||
                    ($question->qtype == 'category')) {
                continue;
            }
            $count++;
        }
        return $count;
    }

    function readdata($filename) {
    /// Returns complete file with an array, one item per line

        if (is_readable($filename)) {
            $filearray = file($filename);

            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (preg_match("/\r/", $filearray[0]) AND !preg_match("/\n/", $filearray[0])) {
                return explode("\r", $filearray[0]);
            } else {
                return $filearray;
            }
        }
        return false;
    }

    protected function readquestions($lines) {
    /// Parses an array of lines into an array of questions,
    /// where each item is a question object as defined by
    /// readquestion().   Questions are defined as anything
    /// between blank lines.

        $questions = array();
        $currentquestion = array();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                if (!empty($currentquestion)) {
                    if ($question = $this->readquestion($currentquestion)) {
                        $questions[] = $question;
                    }
                    $currentquestion = array();
                }
            } else {
                $currentquestion[] = $line;
            }
        }

        if (!empty($currentquestion)) {  // There may be a final question
            if ($question = $this->readquestion($currentquestion)) {
                $questions[] = $question;
            }
        }

        return $questions;
    }


    function readquestion($lines) {
    /// Given an array of lines known to define a question in
    /// this format, this function converts it into a question
    /// object suitable for processing and insertion into Moodle.

        echo "<p>This flash question format has not yet been completed!</p>";

        return NULL;
    }

    function defaultquestion() {
    // returns an "empty" question
    // Somewhere to specify question parameters that are not handled
    // by import but are required db fields.
    // This should not be overridden.
        global $CFG;

        $question = new stdClass();
        $question->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $question->defaultmark = 1;
        $question->image = "";
        $question->usecase = 0;
        $question->multiplier = array();
        $question->generalfeedback = '';
        $question->correctfeedback = '';
        $question->partiallycorrectfeedback = '';
        $question->incorrectfeedback = '';
        $question->answernumbering = 'abc';
        $question->penalty = 0.1;
        $question->length = 1;
        $question->qoption = 0;
        $question->layout = 1;

        // this option in case the questiontypes class wants
        // to know where the data came from
        $question->export_process = true;
        $question->import_process = true;

        return $question;
    }

    function importpostprocess() {
        /// Does any post-processing that may be desired
        /// Argument is a simple array of question ids that
        /// have just been added.
        return true;
    }

    /**
     * Convert the question text to plain text, so it can safely be displayed
     * during import to let the user see roughly what is going on.
     */
    protected function format_question_text($question) {
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        return html_to_text(format_text($question->questiontext,
                $question->questiontextformat, $formatoptions), 0, false);
    }
}


