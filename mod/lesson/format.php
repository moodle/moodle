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
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**
 * Import files embedded into answer or response
 *
 * @param string $field nfield name (answer or response)
 * @param array $data imported data
 * @param object $answer answer object
 * @param int $contextid
 **/
function lesson_import_question_files($field, $data, $answer, $contextid) {
    global $DB;
    if (!isset($data['itemid'])) {
        return;
    }
    $text = file_save_draft_area_files($data['itemid'],
            $contextid, 'mod_lesson', 'page_' . $field . 's', $answer->id,
            array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0),
            $answer->$field);

    $DB->set_field("lesson_answers", $field, $text, array("id" => $answer->id));
}

/**
 * Given some question info and some data about the the answers
 * this function parses, organises and saves the question
 *
 * This is only used when IMPORTING questions and is only called
 * from format.php
 * Lifted from mod/quiz/lib.php -
 *    1. all reference to oldanswers removed
 *    2. all reference to quiz_multichoice table removed
 *    3. In shortanswer questions usecase is store in the qoption field
 *    4. In numeric questions store the range as two answers
 *    5. truefalse options are ignored
 *    6. For multichoice questions with more than one answer the qoption field is true
 *
 * @param object $question Contains question data like question, type and answers.
 * @param object $lesson
 * @param int $contextid
 * @return object Returns $result->error or $result->notice.
 **/
function lesson_save_question_options($question, $lesson, $contextid) {
    global $DB;

    // These lines are required to ensure that all page types have
    // been loaded for the following switch
    if (!($lesson instanceof lesson)) {
        $lesson = new lesson($lesson);
    }
    $manager = lesson_page_type_manager::get($lesson);

    $timenow = time();
    $result = new stdClass();

    // Default answer to avoid code duplication.
    $defaultanswer = new stdClass();
    $defaultanswer->lessonid   = $question->lessonid;
    $defaultanswer->pageid = $question->id;
    $defaultanswer->timecreated   = $timenow;
    $defaultanswer->answerformat = FORMAT_HTML;
    $defaultanswer->jumpto = LESSON_THISPAGE;
    $defaultanswer->grade = 0;
    $defaultanswer->score = 0;

    switch ($question->qtype) {
        case LESSON_PAGE_SHORTANSWER:

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = clone($defaultanswer);
                    if ($question->fraction[$key] >=0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
                    $answer->grade = round($question->fraction[$key] * 100);
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedback[$key], $answer, $contextid);
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }


            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "lesson", $maxfraction);
                return $result;
            }
            break;

        case LESSON_PAGE_NUMERICAL:   // Note similarities to shortanswer.

            $answers = array();
            $maxfraction = -1;


            // for each answer store the pair of min and max values even if they are the same
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = clone($defaultanswer);
                    if ($question->fraction[$key] >= 0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
                    $answer->grade = round($question->fraction[$key] * 100);
                    $min = $question->answer[$key] - $question->tolerance[$key];
                    $max = $question->answer[$key] + $question->tolerance[$key];
                    $answer->answer   = $min.":".$max;
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedback[$key], $answer, $contextid);

                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "lesson", $maxfraction);
                return $result;
            }
        break;


        case LESSON_PAGE_TRUEFALSE:

            // In lesson the correct answer always come first, as it was the case
            // in question bank exports years ago.
            $answer = clone($defaultanswer);
            $answer->grade = 100;
            $answer->jumpto = LESSON_NEXTPAGE;
            $answer->score = 1;
            if ($question->correctanswer) {
                $answer->answer = get_string("true", "lesson");
                if (isset($question->feedbacktrue)) {
                    $answer->response = $question->feedbacktrue['text'];
                    $answer->responseformat = $question->feedbacktrue['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedbacktrue, $answer, $contextid);
                }
            } else {
                $answer->answer = get_string("false", "lesson");
                if (isset($question->feedbackfalse)) {
                    $answer->response = $question->feedbackfalse['text'];
                    $answer->responseformat = $question->feedbackfalse['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedbackfalse, $answer, $contextid);
                }
            }

            // Now the wrong answer.
            $answer = clone($defaultanswer);
            if ($question->correctanswer) {
                $answer->answer = get_string("false", "lesson");
                if (isset($question->feedbackfalse)) {
                    $answer->response = $question->feedbackfalse['text'];
                    $answer->responseformat = $question->feedbackfalse['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedbackfalse, $answer, $contextid);
                }
            } else {
                $answer->answer = get_string("true", "lesson");
                if (isset($question->feedbacktrue)) {
                    $answer->response = $question->feedbacktrue['text'];
                    $answer->responseformat = $question->feedbacktrue['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('response', $question->feedbacktrue, $answer, $contextid);
                }
            }

          break;

        case LESSON_PAGE_MULTICHOICE:

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = clone($defaultanswer);
                    $answer->grade = round($question->fraction[$key] * 100);

                    if ($question->single) {
                        if ($answer->grade > 50) {
                            $answer->jumpto = LESSON_NEXTPAGE;
                            $answer->score = 1;
                        }
                    } else {
                        // If multi answer allowed, any answer with fraction > 0 is considered correct.
                        if ($question->fraction[$key] > 0) {
                            $answer->jumpto = LESSON_NEXTPAGE;
                            $answer->score = 1;
                        }
                    }
                    $answer->answer   = $dataanswer['text'];
                    $answer->answerformat   = $dataanswer['format'];
                    $answer->response = $question->feedback[$key]['text'];
                    $answer->responseformat = $question->feedback[$key]['format'];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('answer', $dataanswer, $answer, $contextid);
                    lesson_import_question_files('response', $question->feedback[$key], $answer, $contextid);

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
                    $result->notice = get_string("fractionsnomax", "lesson", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->notice = get_string("fractionsaddwrong", "lesson", $totalfraction);
                    return $result;
                }
            }
        break;

        case LESSON_PAGE_MATCHING:

            $subquestions = array();

            // The first answer should always be the correct answer
            $correctanswer = clone($defaultanswer);
            $correctanswer->answer = get_string('thatsthecorrectanswer', 'lesson');
            $correctanswer->jumpto = LESSON_NEXTPAGE;
            $correctanswer->score = 1;
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
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    lesson_import_question_files('answer', $questiontext, $answer, $contextid);
                    $subquestions[] = $answer->id;
                    $i++;
                }
            }

            if (count($subquestions) < 3) {
                $result->notice = get_string("notenoughsubquestions", "lesson");
                return $result;
            }
            break;

        case LESSON_PAGE_ESSAY:
            $answer = new stdClass();
            $answer->lessonid = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated = $timenow;
            $answer->answer = null;
            $answer->answerformat = FORMAT_MOODLE;
            $answer->grade = 0;
            $answer->score = 1;
            $answer->jumpto = LESSON_NEXTPAGE;
            $answer->response = null;
            $answer->responseformat = FORMAT_MOODLE;
            $answer->id = $DB->insert_record("lesson_answers", $answer);
        break;
        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
    }
    return true;
}


class qformat_default {

    var $displayerrors = true;
    var $category = null;
    var $questionids = array();
    protected $importcontext = null;
    var $qtypeconvert = array('numerical'   => LESSON_PAGE_NUMERICAL,
                               'multichoice' => LESSON_PAGE_MULTICHOICE,
                               'truefalse'   => LESSON_PAGE_TRUEFALSE,
                               'shortanswer' => LESSON_PAGE_SHORTANSWER,
                               'match'       => LESSON_PAGE_MATCHING,
                               'essay'       => LESSON_PAGE_ESSAY
                              );

    // Importing functions
    function provide_import() {
        return false;
    }

    function set_importcontext($context) {
        $this->importcontext = $context;
    }

    /**
     * Handle parsing error
     *
     * @param string $message information about error
     * @param string $text imported text that triggered the error
     * @param string $questionname imported question name
     */
    protected function error($message, $text='', $questionname='') {
        $importerrorquestion = get_string('importerrorquestion', 'question');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$importerrorquestion $questionname</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<strong>$message</strong>\n";
        echo "</div>";
    }

    /**
     * Import for questiontype plugins
     * @param mixed $data The segment of data containing the question
     * @param object $question processed (so far) by standard import code if appropriate
     * @param object $extra mixed any additional format specific data that may be passed by the format
     * @param string $qtypehint hint about a question type from format
     * @return object question object suitable for save_options() or false if cannot handle
     */
    public function try_importing_using_qtypes($data, $question = null, $extra = null,
            $qtypehint = '') {

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
        $addquestionontop = false;
        if ($pageid == 0) {
            $addquestionontop = true;
            $updatelessonpage = $DB->get_record('lesson_pages', array('lessonid' => $lesson->id, 'prevpageid' => 0));
        } else {
            $updatelessonpage = $DB->get_record('lesson_pages', array('lessonid' => $lesson->id, 'id' => $pageid));
        }

        $unsupportedquestions = 0;

        foreach ($questions as $question) {   // Process and store each question
            switch ($question->qtype) {
                //TODO: Bad way to bypass category in data... Quickfix for MDL-27964
                case 'category':
                    break;
                // the good ones
                case 'shortanswer' :
                case 'numerical' :
                case 'truefalse' :
                case 'multichoice' :
                case 'match' :
                case 'essay' :
                    $count++;

                    //Show nice formated question in one line.
                    echo "<hr><p><b>$count</b>. ".$this->format_question_text($question)."</p>";

                    $newpage = new stdClass;
                    $newpage->lessonid = $lesson->id;
                    $newpage->qtype = $this->qtypeconvert[$question->qtype];
                    switch ($question->qtype) {
                        case 'shortanswer' :
                            if (isset($question->usecase)) {
                                $newpage->qoption = $question->usecase;
                            }
                            break;
                        case 'multichoice' :
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
                    $newpage->contentsformat = isset($question->questionformat) ? $question->questionformat : FORMAT_HTML;

                    // set up page links
                    if ($pageid) {
                        // the new page follows on from this page
                        if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
                            throw new \moodle_exception('invalidpageid', 'lesson');
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

                    // Import images in question text.
                    if (isset($question->questiontextitemid)) {
                        $questiontext = file_save_draft_area_files($question->questiontextitemid,
                                $this->importcontext->id, 'mod_lesson', 'page_contents', $newpageid,
                                null , $question->questiontext);
                        // Update content with recoded urls.
                        $DB->set_field("lesson_pages", "contents", $questiontext, array("id" => $newpageid));
                    }

                    // Now to save all the answers and type-specific options

                    $question->lessonid = $lesson->id; // needed for foreign key
                    $question->qtype = $this->qtypeconvert[$question->qtype];
                    $result = lesson_save_question_options($question, $lesson, $this->importcontext->id);

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
        // Update the prev links if there were existing pages.
        if (!empty($updatelessonpage)) {
            if ($addquestionontop) {
                $DB->set_field("lesson_pages", "prevpageid", $pageid, array("id" => $updatelessonpage->id));
            } else {
                $DB->set_field("lesson_pages", "prevpageid", $pageid, array("id" => $updatelessonpage->nextpageid));
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


    protected function readquestion($lines) {
    /// Given an array of lines known to define a question in
    /// this format, this function converts it into a question
    /// object suitable for processing and insertion into Moodle.

        // We should never get there unless the qformat plugin is broken.
        throw new coding_exception('Question format plugin is missing important code: readquestion.');

        return null;
    }

    /**
     * Construct a reasonable default question name, based on the start of the question text.
     * @param string $questiontext the question text.
     * @param string $default default question name to use if the constructed one comes out blank.
     * @return string a reasonable question name.
     */
    public function create_default_question_name($questiontext, $default) {
        $name = $this->clean_question_name(shorten_text($questiontext, 80));
        if ($name) {
            return $name;
        } else {
            return $default;
        }
    }

    /**
     * Ensure that a question name does not contain anything nasty, and will fit in the DB field.
     * @param string $name the raw question name.
     * @return string a safe question name.
     */
    public function clean_question_name($name) {
        $name = clean_param($name, PARAM_TEXT); // Matches what the question editing form does.
        $name = trim($name);
        $trimlength = 251;
        while (core_text::strlen($name) > 255 && $trimlength > 0) {
            $name = shorten_text($name, $trimlength);
            $trimlength -= 10;
        }
        return $name;
    }

    /**
     * return an "empty" question
     * Somewhere to specify question parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default question
     */
    protected function defaultquestion() {
        global $CFG;
        static $defaultshuffleanswers = null;
        if (is_null($defaultshuffleanswers)) {
            $defaultshuffleanswers = get_config('quiz', 'shuffleanswers');
        }

        $question = new stdClass();
        $question->shuffleanswers = $defaultshuffleanswers;
        $question->defaultmark = 1;
        $question->image = "";
        $question->usecase = 0;
        $question->multiplier = array();
        $question->questiontextformat = FORMAT_MOODLE;
        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_MOODLE;
        $question->correctfeedback = '';
        $question->partiallycorrectfeedback = '';
        $question->incorrectfeedback = '';
        $question->answernumbering = 'abc';
        $question->penalty = 0.3333333;
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
        // The html_to_text call strips out all URLs, but format_text complains
        // if it finds @@PLUGINFILE@@ tokens. So, we need to replace
        // @@PLUGINFILE@@ with a real URL, but it doesn't matter what.
        // We use http://example.com/.
        $text = str_replace('@@PLUGINFILE@@/', 'http://example.com/', $question->questiontext);
        return s(html_to_text(format_text($text,
                $question->questiontextformat, $formatoptions), 0, false));
    }

    /**
     * Since the lesson module tries to re-use the question bank import classes in
     * a crazy way, this is necessary to stop things breaking.
     */
    protected function add_blank_combined_feedback($question) {
        return $question;
    }
}


/**
 * Since the lesson module tries to re-use the question bank import classes in
 * a crazy way, this is necessary to stop things breaking. This should be exactly
 * the same as the class defined in question/format.php.
 */
class qformat_based_on_xml extends qformat_default {
    /**
     * A lot of imported files contain unwanted entities.
     * This method tries to clean up all known problems.
     * @param string str string to correct
     * @return string the corrected string
     */
    public function cleaninput($str) {

        $html_code_list = array(
            "&#039;" => "'",
            "&#8217;" => "'",
            "&#8220;" => "\"",
            "&#8221;" => "\"",
            "&#8211;" => "-",
            "&#8212;" => "-",
        );
        $str = strtr($str, $html_code_list);
        // Use core_text entities_to_utf8 function to convert only numerical entities.
        $str = core_text::entities_to_utf8($str, false);
        return $str;
    }

    /**
     * Return the array moodle is expecting
     * for an HTML text. No processing is done on $text.
     * qformat classes that want to process $text
     * for instance to import external images files
     * and recode urls in $text must overwrite this method.
     * @param array $text some HTML text string
     * @return array with keys text, format and files.
     */
    public function text_field($text) {
        return array(
            'text' => trim($text),
            'format' => FORMAT_HTML,
            'files' => array(),
        );
    }

    /**
     * Return the value of a node, given a path to the node
     * if it doesn't exist return the default value.
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    public function getpath($xml, $path, $default, $istext=false, $error='') {
        foreach ($path as $index) {
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error($error);
                    return false;
                } else {
                    return $default;
                }
            }

            $xml = $xml[$index];
        }

        if ($istext) {
            if (!is_string($xml)) {
                $this->error(get_string('invalidxml', 'qformat_xml'));
            }
            $xml = trim($xml);
        }

        return $xml;
    }
}
