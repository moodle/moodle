<?php  // $Id$ 
/**
 * Base class for question import and export formats.
 *
 * @author Martin Dougiamas, Howard Miller, and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage importexport
 */
class qformat_default {

    var $displayerrors = true;
    var $category = NULL;
    var $course = NULL;
    var $filename = '';
    var $matchgrades = 'error';
    var $catfromfile = 0;
    var $cattofile = 0;
    var $questionids = array();
    var $importerrors = 0;
    var $stoponerror = true;

// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods

    /**
     * set the category
     * @param object category the category object
     */
    function setCategory( $category ) {
        $this->category = $category;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set matchgrades
     * @param string matchgrades error or nearest for grades
     */
    function setMatchgrades( $matchgrades ) {
        $this->matchgrades = $matchgrades;
    }

    /**
     * set catfromfile
     * @param bool catfromfile allow categories embedded in import file
     */
    function setCatfromfile( $catfromfile ) {
        $this->catfromfile = $catfromfile;
    }
    
    /**
     * set cattofile
     * @param bool cattofile exports categories within export file
     */
    function setCattofile( $cattofile ) {
        $this->cattofile = $cattofile;
    } 

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }

/***********************
 * IMPORTING FUNCTIONS
 ***********************/

    /**
     * Handle parsing error
     */
    function error( $message, $text='', $questionname='' ) {
        $importerrorquestion = get_string('importerrorquestion','quiz');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$importerrorquestion $questionname</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<strong>$message</strong>\n";
        echo "</div>";

         $this->importerrors++;
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       // reset the timer in case file upload was slow
       @set_time_limit();

       // STAGE 1: Parse the file
       notify( get_string('parsingquestions','quiz') );
         
        if (! $lines = $this->readdata($this->filename)) {
            notify( get_string('cannotread','quiz') );
            return false;
        }

        if (! $questions = $this->readquestions($lines)) {   // Extract all the questions
            notify( get_string('noquestionsinfile','quiz') );
            return false;
        }

        // STAGE 2: Write data to database
        notify( get_string('importingquestions','quiz',count($questions)) );

        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }

        // get list of valid answer grades
        $grades = get_grade_options();
        $gradeoptionsfull = $grades->gradeoptionsfull;

        $count = 0;

        foreach ($questions as $question) {   // Process and store each question

            // reset the php timeout 
            @set_time_limit();

            // check for category modifiers
            if ($question->qtype=='category') {
                if ($this->catfromfile) {
                    // find/create category object
                    $catpath = $question->category;
                    $newcategory = create_category_path( $catpath, '/', $this->course->id );
                    if (!empty($newcategory)) {
                        $this->category = $newcategory;
                    }
                }
                continue; 
            }

            $count++;

            echo "<hr /><p><b>$count</b>. ".$this->format_question_text($question)."</p>";

            // check for answer grades validity (must match fixed list of grades)
            if (!empty($question->fraction) and (is_array($question->fraction))) {
                $fractions = $question->fraction;
                $answersvalid = true; // in case they are!
                foreach ($fractions as $key => $fraction) {
                    $newfraction = match_grade_options($gradeoptionsfull, $fraction, $this->matchgrades);
                    if ($newfraction===false) {
                        $answersvalid = false;
                    }
                    else {
                        $fractions[$key] = $newfraction;
                    }
                }
                if (!$answersvalid) {
                    notify(get_string('matcherror', 'quiz'));
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

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    function readdata($filename) {
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

    /**
     * Parses an array of lines into an array of questions, 
     * where each item is a question object as defined by 
     * readquestion().   Questions are defined as anything 
     * between blank lines.
     *
     * If your format does not use blank lines as a delimiter
     * then you will need to override this method. Even then
     * try to use readquestion for each question
     * @param array lines array of lines from readdata
     * @return array array of question objects
     */
    function readquestions($lines) {
     
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


    /**
     * return an "empty" question
     * Somewhere to specify question parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default question
     */ 
    function defaultquestion() {
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

        // this option in case the questiontypes class wants
        // to know where the data came from
        $question->export_process = true;
        $question->import_process = true;

        return $question;
    }

    /**
     * Given the data known to define a question in 
     * this format, this function converts it into a question 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit questions
     * (e.g. an XML format) you must override 'readquestions' too
     * @param $lines mixed data that represents question
     * @return object question object
     */
    function readquestion($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented','quiz' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        $newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        return $newfile;
    }

/*******************
 * EXPORT FUNCTIONS
 *******************/

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
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
        
        // track which category questions are in
        // if it changes we will record the category change in the output
        // file if selected. 0 means that it will get printed before the 1st question
        $trackcategory = 0;

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
            
            // check if we need to record category change
            if ($this->cattofile) {
                if ($question->category != $trackcategory) {
                    $trackcategory = $question->category;   
                    $categoryname = get_category_path( $trackcategory );
                    
                    // create 'dummy' question for category export
                    $dummyquestion = new object;
                    $dummyquestion->qtype = 'category';
                    $dummyquestion->category = $categoryname;
                    $dummyquestion->name = "switch category to $categoryname";
                    $dummyquestion->id = 0;
                    $dummyquestion->questiontextformat = '';
                    $expout .= $this->writequestion( $dummyquestion ) . "\n";
                }       
            }    

            // export the question displaying message
            $count++;
            echo "<hr /><p><b>$count</b>. ".$this->format_question_text($question)."</p>";
            $expout .= $this->writequestion( $question ) . "\n";
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
       
        // write file
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            error( get_string('cannotopen','quiz',$filepath) );
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            error( get_string('cannotwrite','quiz',$filepath) );
        }
        fclose($fh);
        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single question object into text output in the given
     * format.
     * This must be overriden
     * @param object question question object
     * @return mixed question export text or null if not implemented
     */
    function writequestion($question) {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented','quiz' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
    function question_get_export_dir() {
        $dirname = get_string("exportfilename","quiz");
        $path = $this->course->id.'/backupdata/'.$dirname; // backupdata is protected directory
        return $path;
    }

    /**
     * where question specifies a moodle (text) format this
     * performs the conversion.
     */
    function format_question_text($question) {
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        if (empty($question->questiontextformat)) {
            $format = FORMAT_MOODLE;
        } else {
            $format = $question->questiontextformat;
        }
        return format_text(stripslashes($question->questiontext), $format, $formatoptions);
    }
}

?>
