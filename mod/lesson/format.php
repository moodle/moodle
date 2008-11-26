<?php  // $Id$ 
/**
 * format.php  - Default format class for file imports/exports. Doesn't do 
 * everything on it's own -- it needs to be extended.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

// Included by import.php

class qformat_default {

    var $displayerrors = true;
    var $category = NULL;
    var $questionids = array();
    var $qtypeconvert = array(NUMERICAL   => LESSON_NUMERICAL,
                              MULTICHOICE => LESSON_MULTICHOICE,
                              TRUEFALSE   => LESSON_TRUEFALSE,
                              SHORTANSWER => LESSON_SHORTANSWER,
                              MATCH       => LESSON_MATCHING
                              );

/// Importing functions

    function importpreprocess() {
    /// Does any pre-processing that may be desired

        return true;
    }

    function importprocess($filename, $lesson, $pageid) {
    /// Processes a given file.  There's probably little need to change this
        $timenow = time();

        if (! $lines = $this->readdata($filename)) {
            notify("File could not be read, or was empty");
            return false;
        }

        if (! $questions = $this->readquestions($lines)) {   // Extract all the questions
            notify("There are no questions in this file!");
            return false;
        }
        
        notify(get_string('importcount', 'lesson', sizeof($questions)));

        $count = 0;

        foreach ($questions as $question) {   // Process and store each question
            switch ($question->qtype) {
                // the good ones
                case SHORTANSWER :
                case NUMERICAL :
                case TRUEFALSE :
                case MULTICHOICE :
                case MATCH :
                    $count++;

                    echo "<hr><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";
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
                        if (!$page = get_record("lesson_pages", "id", $pageid)) {
                            error ("Format: Page $pageid not found");
                        }
                        $newpage->prevpageid = $pageid;
                        $newpage->nextpageid = $page->nextpageid;
                        // insert the page and reset $pageid
                        if (!$newpageid = insert_record("lesson_pages", $newpage)) {
                            error("Format: Could not insert new page!");
                        }
                        // update the linked list
                        if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
                            error("Format: unable to update link");
                        }

                    } else {
                        // new page is the first page
                        // get the existing (first) page (if any)
                        if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
                            // there are no existing pages
                            $newpage->prevpageid = 0; // this is a first page
                            $newpage->nextpageid = 0; // this is the only page
                            $newpageid = insert_record("lesson_pages", $newpage);
                            if (!$newpageid) {
                                error("Insert page: new first page not inserted");
                            }
                        } else {
                            // there are existing pages put this at the start
                            $newpage->prevpageid = 0; // this is a first page
                            $newpage->nextpageid = $page->id;
                            $newpageid = insert_record("lesson_pages", $newpage);
                            if (!$newpageid) {
                                error("Insert page: first page not inserted");
                            }
                            // update the linked list
                            if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->id)) {
                                error("Insert page: unable to update link");
                            }
                        }
                    }
                    // reset $pageid and put the page ID in $question, used in save_question_option()
                    $pageid = $newpageid;
                    $question->id = $newpageid;
                    
                    $this->questionids[] = $question->id;

                    // Now to save all the answers and type-specific options

                    $question->lessonid = $lesson->id; // needed for foreign key
                    $question->qtype = $this->qtypeconvert[$question->qtype];
                    $result = lesson_save_question_options($question);

                    if (!empty($result->error)) {
                        notify($result->error);
                        return false;
                    }

                    if (!empty($result->notice)) {
                        notify($result->notice);
                        return true;
                    }
                    break;
            // the Bad ones
                default :
                    notify(get_string('unsupportedqtype', 'lesson', $question->qtype));
            }
 
        }
        return true;
    }


    function readdata($filename) {
    /// Returns complete file with an array, one item per line

        if (is_readable($filename)) {
            $filearray = file($filename);

            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            } else {
                return $filearray;
            }
        }
        return false;
    }

    function readquestions($lines) {
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
        $question->shuffleanswers = $CFG->quiz_shuffleanswers;
        $question->defaultgrade = 1;
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
        
        return $question;
    }

    function importpostprocess() {
    /// Does any post-processing that may be desired
    /// Argument is a simple array of question ids that 
    /// have just been added.

        return true;
    }

}

?>
