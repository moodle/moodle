<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////
/// format.php  - Default format class for file imports/exports.  //
///                                                               //
/// Doesn't do everything on it's own -- it needs to be extended. //
////////////////////////////////////////////////////////////////////

// Included by import.php

class quiz_default_format {

    var $displayerrors = true;
    var $category = NULL;
    var $questionids = array();

/// Importing functions

    function importpreprocess($category) {
    /// Does any pre-processing that may be desired

        $this->category = $category;  // Important

        return true;
    }

    function importprocess($filename) {
    /// Processes a given file.  There's probably little need to change this

        if (! $lines = $this->readdata($filename)) {
            notify("File could not be read, or was empty");
            return false;
        }

        if (! $questions = $this->readquestions($lines)) {   // Extract all the questions
            notify("There are no questions in this file!");
            return false;
        }

        notify("Importing ".count($questions)." questions");

        $count = 0;

        foreach ($questions as $question) {   // Process and store each question
            $count++;

            echo "<hr><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";

            $question->category = $this->category->id;
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
            $question->version = 1;                    // Original version of this question

            if (!$question->id = insert_record("quiz_questions", $question)) {
                error("Could not insert new question!");
            }

            $this->questionids[] = $question->id;

            // Now to save all the answers and type-specific options

            $result = quiz_save_question_options($question);

            if (!empty($result->error)) {
                notify($result->error);
                return false;
            }

            if (!empty($result->notice)) {
                notify($result->notice);
                return true;
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

        echo "<p>This quiz format has not yet been completed!</p>";

        return NULL;
    }


    function importpostprocess() {
    /// Does any post-processing that may be desired
    /// Argument is a simple array of question ids that 
    /// have just been added.

        return true;
    }

}

?>
