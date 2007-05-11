<?php  // $Id$ 

////////////////////////////////////////////////////////////////////
/// format.php  - Default format class for file imports/exports.  //
///                                                               //
/// Doesn't do everything on it's own -- it needs to be extended. //
////////////////////////////////////////////////////////////////////

// Included by import.php and export.php

class qformat_default {

    var $displayerrors = true;
    var $category = NULL;
    var $course = NULL;
    var $questionids = array();

// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

/// Importing functions

    function importpreprocess($category, $course=NULL ) {
    /// Does any pre-processing that may be desired

        $this->category = $category;  // Important
        $this->course = $course;

        return true;
    }

    /**
     *
     * @PARAM $matchgrades string 'error' or 'nearest', mismatched grades handling
     */
    function importprocess($filename, $matchgrades='error') {
    /// Processes a given file.  There's probably little need to change this

        if (! $lines = $this->readdata($filename)) {
            notify( get_string('cannotread','quiz') );
            return false;
        }

        if (! $questions = $this->readquestions($lines)) {   // Extract all the questions
            notify( get_string('noquestionsinfile','quiz') );
            return false;
        }

        notify( get_string('importingquestions','quiz',count($questions)) );

        // get list of valid answer grades
        $grades = get_grade_options();
        $gradeoptionsfull = $grades->gradeoptionsfull;

        $count = 0;

        foreach ($questions as $question) {   // Process and store each question
            $count++;

            echo "<hr /><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";

            // check for answer grades validity (must match fixed list of grades)
            if (!empty($question->fraction)) {
                $fractions = $question->fraction;
                $answersvalid = true; // in case they are!
                foreach ($fractions as $key => $fraction) {
                    $newfraction = match_grade_options($gradeoptionsfull, $fraction, $matchgrades);
                    if ($newfraction===false) {
                        $answersvalid = false;
                    }
                    else {
                        $fractions[$key] = $newfraction;
                    }
                }
                if (!$answersvalid) {
                    notify( get_string('matcherror','quiz') );
                    continue;
                }
                else {
                    $question->fraction = $fractions;
                }
            }

            $question->category = $this->category->id;
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)

            if (!$question->id = insert_record("question", $question)) {
                error( get_string('cannotinsert','quiz') );
            }

            $this->questionids[] = $question->id;

            // Now to save all the answers and type-specific options

            global $QTYPES;
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

        // this option in case the questiontypes class wants
        // to know where the data came from
        $question->export_process = true;
        $question->import_process = true;

        return $question;
    }

    function readquestion($lines) {
    /// Given an array of lines known to define a question in 
    /// this format, this function converts it into a question 
    /// object suitable for processing and insertion into Moodle.

        $formatnotimplemented = get_string( 'formatnotimplemented','quiz' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }


    function importpostprocess() {
    /// Does any post-processing that may be desired
    /// Argument is a simple array of question ids that 
    /// have just been added.

        return true;
    }

    function importimagefile( $path, $base64 ) {
    /// imports an image file encoded in base64 format
    /// This should not be overridden.
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return false;
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return false;
        }
        if (!fwrite( $fh, $content )) {
            return false;
        }
        fclose( $fh );

        // return the (possibly) new filename
        return $newfile;
    }

//=================
// Export functions
//=================

    function export_file_extension() {
    /// return the files extension appropriate for this type
    /// override if you don't want .txt
  
        return ".txt";
    }

    function exportpreprocess($category, $course) {
    /// Does any pre-processing that may be desired

        $this->category = $category;  // Important
        $this->course = $course; // As is this!

        return true;
    }

    function presave_process( $content ) {
    /// enables any processing to be done on the content
    /// just prior to the file being saved
    /// default is to do nothing
 
        return $content;
    }

    function exportprocess($filename) {
    /// Exports a given category.  There's probably little need to change this

        global $CFG;

        // create a directory for the exports (if not already existing)
        if (! $export_dir = make_upload_directory($this->question_get_export_dir())) {
              error( get_string('cannotcreatepath','quiz',$export_dir) );
        }
        $path = $CFG->dataroot.'/'.$this->question_get_export_dir();

        // get the questions (from database) in this category
        // only get q's with no parents (no cloze subquestions specifically)
        $questions = get_questions_category( $this->category, true );

        notify( get_string('exportingquestions','quiz') );
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

            // do not export random questions
            if ($question->qtype==RANDOM) {
                continue;
            }

        // export the question displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";
        $expout .= $this->writequestion( $question ) . "\n";
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );

        // write file
        $filepath = $path."/".$filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            error( get_string('cannotopen','quiz',$filepath) );
        }
        if (!fwrite($fh, $expout)) {
            error( get_string('cannotwrite','quiz',$filepath) );
        }
        fclose($fh);

        return true;
    }

    function exportpostprocess() {
    /// Does any post-processing that may be desired

        return true;
    }

    function writequestion($question) {
    /// Turns a question object into textual output in the given format 
    /// must be overidden

        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented','quiz' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    function question_get_export_dir() {
        $dirname = get_string("exportfilename","quiz");
        $path = $this->course->id.'/backupdata/'.$dirname; // backupdata is protected directory
        return $path;
    }

}

?>
