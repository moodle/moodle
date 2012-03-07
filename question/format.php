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
 * Defines the base class for question import and export formats.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**#@+
 * The core question types.
 *
 * These used to be in lib/questionlib.php, but are being deprecated. Copying
 * them here to keep the import/export code working for now (there are 135
 * references to these constants which I don't want to try to fix at the moment.)
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
 * Base class for question import and export formats.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_default {

    public $displayerrors = true;
    public $category = null;
    public $questions = array();
    public $course = null;
    public $filename = '';
    public $realfilename = '';
    public $matchgrades = 'error';
    public $catfromfile = 0;
    public $contextfromfile = 0;
    public $cattofile = 0;
    public $contexttofile = 0;
    public $questionids = array();
    public $importerrors = 0;
    public $stoponerror = true;
    public $translator = null;
    public $canaccessbackupdata = true;

    protected $importcontext = null;

    // functions to indicate import/export functionality
    // override to return true if implemented

    /** @return bool whether this plugin provides import functionality. */
    public function provide_import() {
        return false;
    }

    /** @return bool whether this plugin provides export functionality. */
    public function provide_export() {
        return false;
    }

    /** The string mime-type of the files that this plugin reads or writes. */
    public function mime_type() {
        return mimeinfo('type', $this->export_file_extension());
    }

    /**
     * @return string the file extension (including .) that is normally used for
     * files handled by this plugin.
     */
    public function export_file_extension() {
        return '.txt';
    }

    /**
     * Check if the given file is capable of being imported by this plugin.
     *
     * Note that expensive or detailed integrity checks on the file should
     * not be performed by this method. Simple file type or magic-number tests
     * would be suitable.
     *
     * @param stored_file $file the file to check
     * @return bool whether this plugin can import the file
     */
    public function can_import_file($file) {
        return ($file->get_mimetype() == $this->mime_type());
    }

    // Accessor methods

    /**
     * set the category
     * @param object category the category object
     */
    public function setCategory($category) {
        if (count($this->questions)) {
            debugging('You shouldn\'t call setCategory after setQuestions');
        }
        $this->category = $category;
    }

    /**
     * Set the specific questions to export. Should not include questions with
     * parents (sub questions of cloze question type).
     * Only used for question export.
     * @param array of question objects
     */
    public function setQuestions($questions) {
        if ($this->category !== null) {
            debugging('You shouldn\'t call setQuestions after setCategory');
        }
        $this->questions = $questions;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    public function setCourse($course) {
        $this->course = $course;
    }

    /**
     * set an array of contexts.
     * @param array $contexts Moodle course variable
     */
    public function setContexts($contexts) {
        $this->contexts = $contexts;
        $this->translator = new context_to_string_translator($this->contexts);
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    public function setFilename($filename) {
        $this->filename = $filename;
    }

    /**
     * set the "real" filename
     * (this is what the user typed, regardless of wha happened next)
     * @param string realfilename name of file as typed by user
     */
    public function setRealfilename($realfilename) {
        $this->realfilename = $realfilename;
    }

    /**
     * set matchgrades
     * @param string matchgrades error or nearest for grades
     */
    public function setMatchgrades($matchgrades) {
        $this->matchgrades = $matchgrades;
    }

    /**
     * set catfromfile
     * @param bool catfromfile allow categories embedded in import file
     */
    public function setCatfromfile($catfromfile) {
        $this->catfromfile = $catfromfile;
    }

    /**
     * set contextfromfile
     * @param bool $contextfromfile allow contexts embedded in import file
     */
    public function setContextfromfile($contextfromfile) {
        $this->contextfromfile = $contextfromfile;
    }

    /**
     * set cattofile
     * @param bool cattofile exports categories within export file
     */
    public function setCattofile($cattofile) {
        $this->cattofile = $cattofile;
    }

    /**
     * set contexttofile
     * @param bool cattofile exports categories within export file
     */
    public function setContexttofile($contexttofile) {
        $this->contexttofile = $contexttofile;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    public function setStoponerror($stoponerror) {
        $this->stoponerror = $stoponerror;
    }

    /**
     * @param bool $canaccess Whether the current use can access the backup data folder. Determines
     * where export files are saved.
     */
    public function set_can_access_backupdata($canaccess) {
        $this->canaccessbackupdata = $canaccess;
    }

    /***********************
     * IMPORTING FUNCTIONS
     ***********************/

    /**
     * Handle parsing error
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

         $this->importerrors++;
    }

    /**
     * Import for questiontype plugins
     * Do not override.
     * @param data mixed The segment of data containing the question
     * @param question object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @param qtypehint hint about a question type from format
     * @return object question object suitable for save_options() or false if cannot handle
     */
    public function try_importing_using_qtypes($data, $question = null, $extra = null,
            $qtypehint = '') {

        // work out what format we are using
        $formatname = substr(get_class($this), strlen('qformat_'));
        $methodname = "import_from_$formatname";

        //first try importing using a hint from format
        if (!empty($qtypehint)) {
            $qtype = question_bank::get_qtype($qtypehint, false);
            if (is_object($qtype) && method_exists($qtype, $methodname)) {
                $question = $qtype->$methodname($data, $question, $this, $extra);
                if ($question) {
                    return $question;
                }
            }
        }

        // loop through installed questiontypes checking for
        // function to handle this question
        foreach (question_bank::get_all_qtypes() as $qtype) {
            if (method_exists($qtype, $methodname)) {
                if ($question = $qtype->$methodname($data, $question, $this, $extra)) {
                    return $question;
                }
            }
        }
        return false;
    }

    /**
     * Perform any required pre-processing
     * @return bool success
     */
    public function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @param object $category
     * @return bool success
     */
    public function importprocess($category) {
        global $USER, $CFG, $DB, $OUTPUT;

        $context = $category->context;
        $this->importcontext = $context;

        // reset the timer in case file upload was slow
        set_time_limit(0);

        // STAGE 1: Parse the file
        echo $OUTPUT->notification(get_string('parsingquestions', 'question'), 'notifysuccess');

        if (! $lines = $this->readdata($this->filename)) {
            echo $OUTPUT->notification(get_string('cannotread', 'question'));
            return false;
        }

        if (!$questions = $this->readquestions($lines, $context)) {   // Extract all the questions
            echo $OUTPUT->notification(get_string('noquestionsinfile', 'question'));
            return false;
        }

        // STAGE 2: Write data to database
        echo $OUTPUT->notification(get_string('importingquestions', 'question',
                $this->count_questions($questions)), 'notifysuccess');

        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            echo $OUTPUT->notification(get_string('importparseerror', 'question'));
            return true;
        }

        // get list of valid answer grades
        $gradeoptionsfull = question_bank::fraction_options_full();

        // check answer grades are valid
        // (now need to do this here because of 'stop on error': MDL-10689)
        $gradeerrors = 0;
        $goodquestions = array();
        foreach ($questions as $question) {
            if (!empty($question->fraction) and (is_array($question->fraction))) {
                $fractions = $question->fraction;
                $answersvalid = true; // in case they are!
                foreach ($fractions as $key => $fraction) {
                    $newfraction = match_grade_options($gradeoptionsfull, $fraction,
                            $this->matchgrades);
                    if ($newfraction === false) {
                        $answersvalid = false;
                    } else {
                        $fractions[$key] = $newfraction;
                    }
                }
                if (!$answersvalid) {
                    echo $OUTPUT->notification(get_string('invalidgrade', 'question'));
                    ++$gradeerrors;
                    continue;
                } else {
                    $question->fraction = $fractions;
                }
            }
            $goodquestions[] = $question;
        }
        $questions = $goodquestions;

        // check for errors before we continue
        if ($this->stoponerror && $gradeerrors > 0) {
            return false;
        }

        // count number of questions processed
        $count = 0;

        foreach ($questions as $question) {   // Process and store each question

            // reset the php timeout
            set_time_limit(0);

            // check for category modifiers
            if ($question->qtype == 'category') {
                if ($this->catfromfile) {
                    // find/create category object
                    $catpath = $question->category;
                    $newcategory = $this->create_category_path($catpath);
                    if (!empty($newcategory)) {
                        $this->category = $newcategory;
                    }
                }
                continue;
            }
            $question->context = $context;

            $count++;

            echo "<hr /><p><b>$count</b>. ".$this->format_question_text($question)."</p>";

            $question->category = $this->category->id;
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)

            $question->createdby = $USER->id;
            $question->timecreated = time();
            $question->modifiedby = $USER->id;
            $question->timemodified = time();

            $question->id = $DB->insert_record('question', $question);
            if (isset($question->questiontextfiles)) {
                foreach ($question->questiontextfiles as $file) {
                    question_bank::get_qtype($question->qtype)->import_file(
                            $context, 'question', 'questiontext', $question->id, $file);
                }
            }
            if (isset($question->generalfeedbackfiles)) {
                foreach ($question->generalfeedbackfiles as $file) {
                    question_bank::get_qtype($question->qtype)->import_file(
                            $context, 'question', 'generalfeedback', $question->id, $file);
                }
            }

            $this->questionids[] = $question->id;

            // Now to save all the answers and type-specific options

            $result = question_bank::get_qtype($question->qtype)->save_question_options($question);

            if (!empty($CFG->usetags) && isset($question->tags)) {
                require_once($CFG->dirroot . '/tag/lib.php');
                tag_set('question', $question->id, $question->tags);
            }

            if (!empty($result->error)) {
                echo $OUTPUT->notification($result->error);
                return false;
            }

            if (!empty($result->notice)) {
                echo $OUTPUT->notification($result->notice);
                return true;
            }

            // Give the question a unique version stamp determined by question_hash()
            $DB->set_field('question', 'version', question_hash($question),
                    array('id' => $question->id));
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

    /**
     * find and/or create the category described by a delimited list
     * e.g. $course$/tom/dick/harry or tom/dick/harry
     *
     * removes any context string no matter whether $getcontext is set
     * but if $getcontext is set then ignore the context and use selected category context.
     *
     * @param string catpath delimited category path
     * @param int courseid course to search for categories
     * @return mixed category object or null if fails
     */
    protected function create_category_path($catpath) {
        global $DB;
        $catnames = $this->split_category_path($catpath);
        $parent = 0;
        $category = null;

        // check for context id in path, it might not be there in pre 1.9 exports
        $matchcount = preg_match('/^\$([a-z]+)\$$/', $catnames[0], $matches);
        if ($matchcount == 1) {
            $contextid = $this->translator->string_to_context($matches[1]);
            array_shift($catnames);
        } else {
            $contextid = false;
        }

        if ($this->contextfromfile && $contextid !== false) {
            $context = get_context_instance_by_id($contextid);
            require_capability('moodle/question:add', $context);
        } else {
            $context = get_context_instance_by_id($this->category->contextid);
        }

        // Now create any categories that need to be created.
        foreach ($catnames as $catname) {
            if ($category = $DB->get_record('question_categories',
                    array('name' => $catname, 'contextid' => $context->id, 'parent' => $parent))) {
                $parent = $category->id;
            } else {
                require_capability('moodle/question:managecategory', $context);
                // create the new category
                $category = new stdClass();
                $category->contextid = $context->id;
                $category->name = $catname;
                $category->info = '';
                $category->parent = $parent;
                $category->sortorder = 999;
                $category->stamp = make_unique_id_code();
                $id = $DB->insert_record('question_categories', $category);
                $category->id = $id;
                $parent = $id;
            }
        }
        return $category;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    protected function readdata($filename) {
        if (is_readable($filename)) {
            $filearray = file($filename);

            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (preg_match("~\r~", $filearray[0]) AND !preg_match("~\n~", $filearray[0])) {
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
     * @param object $context
     * @return array array of question objects
     */
    protected function readquestions($lines, $context) {

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
            if ($question = $this->readquestion($currentquestion, $context)) {
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
    protected function readquestion($lines) {

        $formatnotimplemented = get_string('formatnotimplemented', 'question');
        echo "<p>$formatnotimplemented</p>";

        return null;
    }

    /**
     * Override if any post-processing is required
     * @return bool success
     */
    public function importpostprocess() {
        return true;
    }

    /*******************
     * EXPORT FUNCTIONS
     *******************/

    /**
     * Provide export functionality for plugin questiontypes
     * Do not override
     * @param name questiontype name
     * @param question object data to export
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    protected function try_exporting_using_qtypes($name, $question, $extra=null) {
        // work out the name of format in use
        $formatname = substr(get_class($this), strlen('qformat_'));
        $methodname = "export_to_$formatname";

        $qtype = question_bank::get_qtype($name, false);
        if (method_exists($qtype, $methodname)) {
            return $qtype->$methodname($question, $this, $extra);
        }
        return false;
    }

    /**
     * Do any pre-processing that may be required
     * @param bool success
     */
    public function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    protected function presave_process($content) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return stored_file
     */
    public function exportprocess() {
        global $CFG, $OUTPUT, $DB, $USER;

        // get the questions (from database) in this category
        // only get q's with no parents (no cloze subquestions specifically)
        if ($this->category) {
            $questions = get_questions_category($this->category, true);
        } else {
            $questions = $this->questions;
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
        foreach ($questions as $question) {
            // used by file api
            $contextid = $DB->get_field('question_categories', 'contextid',
                    array('id' => $question->category));
            $question->contextid = $contextid;

            // do not export hidden questions
            if (!empty($question->hidden)) {
                continue;
            }

            // do not export random questions
            if ($question->qtype == 'random') {
                continue;
            }

            // check if we need to record category change
            if ($this->cattofile) {
                if ($question->category != $trackcategory) {
                    $trackcategory = $question->category;
                    $categoryname = $this->get_category_path($trackcategory, $this->contexttofile);

                    // create 'dummy' question for category export
                    $dummyquestion = new stdClass();
                    $dummyquestion->qtype = 'category';
                    $dummyquestion->category = $categoryname;
                    $dummyquestion->name = 'Switch category to ' . $categoryname;
                    $dummyquestion->id = 0;
                    $dummyquestion->questiontextformat = '';
                    $dummyquestion->contextid = 0;
                    $expout .= $this->writequestion($dummyquestion) . "\n";
                }
            }

            // export the question displaying message
            $count++;

            if (question_has_capability_on($question, 'view', $question->category)) {
                $expout .= $this->writequestion($question, $contextid) . "\n";
            }
        }

        // continue path for following error checks
        $course = $this->course;
        $continuepath = "$CFG->wwwroot/question/export.php?courseid=$course->id";

        // did we actually process anything
        if ($count==0) {
            print_error('noquestions', 'question', $continuepath);
        }

        // final pre-process on exported data
        $expout = $this->presave_process($expout);
        return $expout;
    }

    /**
     * get the category as a path (e.g., tom/dick/harry)
     * @param int id the id of the most nested catgory
     * @return string the path
     */
    protected function get_category_path($id, $includecontext = true) {
        global $DB;

        if (!$category = $DB->get_record('question_categories', array('id' => $id))) {
            print_error('cannotfindcategory', 'error', '', $id);
        }
        $contextstring = $this->translator->context_to_string($category->contextid);

        $pathsections = array();
        do {
            $pathsections[] = $category->name;
            $id = $category->parent;
        } while ($category = $DB->get_record('question_categories', array('id' => $id)));

        if ($includecontext) {
            $pathsections[] = '$' . $contextstring . '$';
        }

        $path = $this->assemble_category_path(array_reverse($pathsections));

        return $path;
    }

    /**
     * Convert a list of category names, possibly preceeded by one of the
     * context tokens like $course$, into a string representation of the
     * category path.
     *
     * Names are separated by / delimiters. And /s in the name are replaced by //.
     *
     * To reverse the process and split the paths into names, use
     * {@link split_category_path()}.
     *
     * @param array $names
     * @return string
     */
    protected function assemble_category_path($names) {
        $escapednames = array();
        foreach ($names as $name) {
            $escapedname = str_replace('/', '//', $name);
            if (substr($escapedname, 0, 1) == '/') {
                $escapedname = ' ' . $escapedname;
            }
            if (substr($escapedname, -1) == '/') {
                $escapedname = $escapedname . ' ';
            }
            $escapednames[] = $escapedname;
        }
        return implode('/', $escapednames);
    }

    /**
     * Convert a string, as returned by {@link assemble_category_path()},
     * back into an array of category names.
     *
     * Each category name is cleaned by a call to clean_param(, PARAM_MULTILANG),
     * which matches the cleaning in question/category_form.php.
     *
     * @param string $path
     * @return array of category names.
     */
    protected function split_category_path($path) {
        $rawnames = preg_split('~(?<!/)/(?!/)~', $path);
        $names = array();
        foreach ($rawnames as $rawname) {
            $names[] = clean_param(trim(str_replace('//', '/', $rawname)), PARAM_MULTILANG);
        }
        return $names;
    }

    /**
     * Do an post-processing that may be required
     * @return bool success
     */
    protected function exportpostprocess() {
        return true;
    }

    /**
     * convert a single question object into text output in the given
     * format.
     * This must be overriden
     * @param object question question object
     * @return mixed question export text or null if not implemented
     */
    protected function writequestion($question) {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string('formatnotimplemented', 'question');
        echo "<p>$formatnotimplemented</p>";
        return null;
    }

    /**
     * Convert the question text to plain text, so it can safely be displayed
     * during import to let the user see roughly what is going on.
     */
    protected function format_question_text($question) {
        global $DB;
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        return html_to_text(format_text($question->questiontext,
                $question->questiontextformat, $formatoptions), 0, false);
    }
}
