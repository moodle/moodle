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
        $this->importcontext = context::instance_by_id($this->category->contextid);
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
        echo "<strong>{$importerrorquestion} {$questionname}</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>{$text}</blockquote>\n";
        }
        echo "<strong>{$message}</strong>\n";
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
        $methodname = "import_from_{$formatname}";

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

        // Raise time and memory, as importing can be quite intensive.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);

        // STAGE 1: Parse the file
        echo $OUTPUT->notification(get_string('parsingquestions', 'question'), 'notifysuccess');

        if (! $lines = $this->readdata($this->filename)) {
            echo $OUTPUT->notification(get_string('cannotread', 'question'));
            return false;
        }

        if (!$questions = $this->readquestions($lines)) {   // Extract all the questions
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
                $invalidfractions = array();
                foreach ($fractions as $key => $fraction) {
                    $newfraction = match_grade_options($gradeoptionsfull, $fraction,
                            $this->matchgrades);
                    if ($newfraction === false) {
                        $invalidfractions[] = $fraction;
                    } else {
                        $fractions[$key] = $newfraction;
                    }
                }
                if ($invalidfractions) {
                    echo $OUTPUT->notification(get_string('invalidgrade', 'question',
                            implode(', ', $invalidfractions)));
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
            $transaction = $DB->start_delegated_transaction();

            // reset the php timeout
            core_php_time_limit::raise();

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
                $transaction->allow_commit();
                continue;
            }
            $question->context = $this->importcontext;

            $count++;

            echo "<hr /><p><b>{$count}</b>. ".$this->format_question_text($question)."</p>";

            $question->category = $this->category->id;
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)

            $question->createdby = $USER->id;
            $question->timecreated = time();
            $question->modifiedby = $USER->id;
            $question->timemodified = time();
            $fileoptions = array(
                    'subdirs' => true,
                    'maxfiles' => -1,
                    'maxbytes' => 0,
                );

            $question->id = $DB->insert_record('question', $question);

            if (isset($question->questiontextitemid)) {
                $question->questiontext = file_save_draft_area_files($question->questiontextitemid,
                        $this->importcontext->id, 'question', 'questiontext', $question->id,
                        $fileoptions, $question->questiontext);
            } else if (isset($question->questiontextfiles)) {
                foreach ($question->questiontextfiles as $file) {
                    question_bank::get_qtype($question->qtype)->import_file(
                            $this->importcontext, 'question', 'questiontext', $question->id, $file);
                }
            }
            if (isset($question->generalfeedbackitemid)) {
                $question->generalfeedback = file_save_draft_area_files($question->generalfeedbackitemid,
                        $this->importcontext->id, 'question', 'generalfeedback', $question->id,
                        $fileoptions, $question->generalfeedback);
            } else if (isset($question->generalfeedbackfiles)) {
                foreach ($question->generalfeedbackfiles as $file) {
                    question_bank::get_qtype($question->qtype)->import_file(
                            $this->importcontext, 'question', 'generalfeedback', $question->id, $file);
                }
            }
            $DB->update_record('question', $question);

            $this->questionids[] = $question->id;

            // Now to save all the answers and type-specific options

            $result = question_bank::get_qtype($question->qtype)->save_question_options($question);

            if (isset($question->tags)) {
                core_tag_tag::set_item_tags('core_question', 'question', $question->context, $question->id, $question->tags);
            }

            if (!empty($result->error)) {
                echo $OUTPUT->notification($result->error);
                // Can't use $transaction->rollback(); since it requires an exception,
                // and I don't want to rewrite this code to change the error handling now.
                $DB->force_transaction_rollback();
                return false;
            }

            $transaction->allow_commit();

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
            $context = context::instance_by_id($contextid);
            require_capability('moodle/question:add', $context);
        } else {
            $context = context::instance_by_id($this->category->contextid);
        }
        $this->importcontext = $context;

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

            // If the first line of the file starts with a UTF-8 BOM, remove it.
            $filearray[0] = core_text::trim_utf8_bom($filearray[0]);

            // Check for Macintosh OS line returns (ie file on one line), and fix.
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
     * NOTE this method used to take $context as a second argument. However, at
     * the point where this method was called, it was impossible to know what
     * context the quetsions were going to be saved into, so the value could be
     * wrong. Also, none of the standard question formats were using this argument,
     * so it was removed. See MDL-32220.
     *
     * If your format does not use blank lines as a delimiter
     * then you will need to override this method. Even then
     * try to use readquestion for each question
     * @param array lines array of lines from readdata
     * @return array array of question objects
     */
    protected function readquestions($lines) {

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
     * Add a blank combined feedback to a question object.
     * @param object question
     * @return object question
     */
    protected function add_blank_combined_feedback($question) {
        $question->correctfeedback['text'] = '';
        $question->correctfeedback['format'] = $question->questiontextformat;
        $question->correctfeedback['files'] = array();
        $question->partiallycorrectfeedback['text'] = '';
        $question->partiallycorrectfeedback['format'] = $question->questiontextformat;
        $question->partiallycorrectfeedback['files'] = array();
        $question->incorrectfeedback['text'] = '';
        $question->incorrectfeedback['format'] = $question->questiontextformat;
        $question->incorrectfeedback['files'] = array();
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
        // We should never get there unless the qformat plugin is broken.
        throw new coding_exception('Question format plugin is missing important code: readquestion.');

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
        $methodname = "export_to_{$formatname}";

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
        $continuepath = "{$CFG->wwwroot}/question/export.php?courseid={$course->id}";

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
     * Each category name is cleaned by a call to clean_param(, PARAM_TEXT),
     * which matches the cleaning in question/category_form.php.
     *
     * @param string $path
     * @return array of category names.
     */
    protected function split_category_path($path) {
        $rawnames = preg_split('~(?<!/)/(?!/)~', $path);
        $names = array();
        foreach ($rawnames as $rawname) {
            $names[] = clean_param(trim(str_replace('//', '/', $rawname)), PARAM_TEXT);
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
        throw new coding_exception('Question format plugin is missing important code: writequestion.');
        return null;
    }

    /**
     * Convert the question text to plain text, so it can safely be displayed
     * during import to let the user see roughly what is going on.
     */
    protected function format_question_text($question) {
        return question_utils::to_plain_text($question->questiontext,
                $question->questiontextformat);
    }
}

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
