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
 * The default questiontype class.
 *
 * @package    moodlecore
 * @subpackage questiontypes
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/lib.php');


/**
 * This is the base class for Moodle question types.
 *
 * There are detailed comments on each method, explaining what the method is
 * for, and the circumstances under which you might need to override it.
 *
 * Note: the questiontype API should NOT be considered stable yet. Very few
 * question types have been produced yet, so we do not yet know all the places
 * where the current API is insufficient. I would rather learn from the
 * experiences of the first few question type implementors, and improve the
 * interface to meet their needs, rather the freeze the API prematurely and
 * condem everyone to working round a clunky interface for ever afterwards.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_type {
    protected $fileoptions = array(
        'subdirs' => false,
        'maxfiles' => -1,
        'maxbytes' => 0,
    );

    public function __construct() {
    }

    /**
     * @return string the name of this question type.
     */
    public function name() {
        return substr(get_class($this), 6);
    }

    /**
     * @return string the full frankenstyle name for this plugin.
     */
    public function plugin_name() {
        return get_class($this);
    }

    /**
     * @return string the name of this question type in the user's language.
     * You should not need to override this method, the default behaviour should be fine.
     */
    public function local_name() {
        return get_string('pluginname', $this->plugin_name());
    }

    /**
     * The name this question should appear as in the create new question
     * dropdown. Override this method to return false if you don't want your
     * question type to be createable, for example if it is an abstract base type,
     * otherwise, you should not need to override this method.
     *
     * @return mixed the desired string, or false to hide this question type in the menu.
     */
    public function menu_name() {
        return $this->local_name();
    }

    /**
     * @return bool override this to return false if this is not really a
     *      question type, for example the description question type is not
     *      really a question type.
     */
    public function is_real_question_type() {
        return true;
    }

    /**
     * @return bool true if this question type sometimes requires manual grading.
     */
    public function is_manual_graded() {
        return false;
    }

    /**
     * @param object $question a question of this type.
     * @param string $otherquestionsinuse comma-separate list of other question ids in this attempt.
     * @return bool true if a particular instance of this question requires manual grading.
     */
    public function is_question_manual_graded($question, $otherquestionsinuse) {
        return $this->is_manual_graded();
    }

    /**
     * @return bool true if this question type can be used by the random question type.
     */
    public function is_usable_by_random() {
        return true;
    }

    /**
     * Whether this question type can perform a frequency analysis of student
     * responses.
     *
     * If this method returns true, you must implement the get_possible_responses
     * method, and the question_definition class must implement the
     * classify_response method.
     *
     * @return bool whether this report can analyse all the student reponses
     * for things like the quiz statistics report.
     */
    public function can_analyse_responses() {
        // This works in most cases.
        return !$this->is_manual_graded();
    }

    /**
     * @return whether the question_answers.answer field needs to have
     * restore_decode_content_links_worker called on it.
     */
    public function has_html_answers() {
        return false;
    }

    /**
     * If your question type has a table that extends the question table, and
     * you want the base class to automatically save, backup and restore the extra fields,
     * override this method to return an array wherer the first element is the table name,
     * and the subsequent entries are the column names (apart from id and questionid).
     *
     * @return mixed array as above, or null to tell the base class to do nothing.
     */
    public function extra_question_fields() {
        return null;
    }

    /**
     * If you use extra_question_fields, overload this function to return question id field name
     *  in case you table use another name for this column
     */
    public function questionid_column_name() {
        return 'questionid';
    }

    /**
     * If your question type has a table that extends the question_answers table,
     * make this method return an array wherer the first element is the table name,
     * and the subsequent entries are the column names (apart from id and answerid).
     *
     * @return mixed array as above, or null to tell the base class to do nothing.
     */
    public function extra_answer_fields() {
        return null;
    }

    /**
     * If the quetsion type uses files in responses, then this method should
     * return an array of all the response variables that might have corresponding
     * files. For example, the essay qtype returns array('attachments', 'answers').
     *
     * @return array response variable names that may have associated files.
     */
    public function response_file_areas() {
        return array();
    }

    /**
     * Return an instance of the question editing form definition. This looks for a
     * class called edit_{$this->name()}_question_form in the file
     * question/type/{$this->name()}/edit_{$this->name()}_question_form.php
     * and if it exists returns an instance of it.
     *
     * @param string $submiturl passed on to the constructor call.
     * @return object an instance of the form definition, or null if one could not be found.
     */
    public function create_editing_form($submiturl, $question, $category,
            $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/edit_question_form.php');
        $definitionfile = $CFG->dirroot . '/question/type/' . $this->name() .
                '/edit_' . $this->name() . '_form.php';
        if (!is_readable($definitionfile) || !is_file($definitionfile)) {
            throw new coding_exception($this->plugin_name() .
                    ' is missing the definition of its editing formin file ' .
                    $definitionfile . '.');
        }
        require_once($definitionfile);
        $classname = $this->plugin_name() . '_edit_form';
        if (!class_exists($classname)) {
            throw new coding_exception($this->plugin_name() .
                    ' does not define the class ' . $this->plugin_name() .
                    '_edit_form.');
        }
        return new $classname($submiturl, $question, $category, $contexts, $formeditable);
    }

    /**
     * @return string the full path of the folder this plugin's files live in.
     */
    public function plugin_dir() {
        global $CFG;
        return $CFG->dirroot . '/question/type/' . $this->name();
    }

    /**
     * @return string the URL of the folder this plugin's files live in.
     */
    public function plugin_baseurl() {
        global $CFG;
        return $CFG->wwwroot . '/question/type/' . $this->name();
    }

    /**
     * This method should be overriden if you want to include a special heading or some other
     * html on a question editing page besides the question editing form.
     *
     * @param question_edit_form $mform a child of question_edit_form
     * @param object $question
     * @param string $wizardnow is '' for first page.
     */
    public function display_question_editing_page($mform, $question, $wizardnow) {
        global $OUTPUT;
        $heading = $this->get_heading(empty($question->id));

        echo $OUTPUT->heading_with_help($heading, 'pluginname', $this->plugin_name());

        $permissionstrs = array();
        if (!empty($question->id)) {
            if ($question->formoptions->canedit) {
                $permissionstrs[] = get_string('permissionedit', 'question');
            }
            if ($question->formoptions->canmove) {
                $permissionstrs[] = get_string('permissionmove', 'question');
            }
            if ($question->formoptions->cansaveasnew) {
                $permissionstrs[] = get_string('permissionsaveasnew', 'question');
            }
        }
        if (!$question->formoptions->movecontext  && count($permissionstrs)) {
            echo $OUTPUT->heading(get_string('permissionto', 'question'), 3);
            $html = '<ul>';
            foreach ($permissionstrs as $permissionstr) {
                $html .= '<li>'.$permissionstr.'</li>';
            }
            $html .= '</ul>';
            echo $OUTPUT->box($html, 'boxwidthnarrow boxaligncenter generalbox');
        }
        $mform->display();
    }

    /**
     * Method called by display_question_editing_page and by question.php to get
     * heading for breadcrumbs.
     *
     * @return string the heading
     */
    public function get_heading($adding = false) {
        if ($adding) {
            $string = 'pluginnameadding';
        } else {
            $string = 'pluginnameediting';
        }
        return get_string($string, $this->plugin_name());
    }

    /**
     * Set any missing settings for this question to the default values. This is
     * called before displaying the question editing form.
     *
     * @param object $questiondata the question data, loaded from the databsae,
     *      or more likely a newly created question object that is only partially
     *      initialised.
     */
    public function set_default_options($questiondata) {
    }

    /**
     * Saves (creates or updates) a question.
     *
     * Given some question info and some data about the answers
     * this function parses, organises and saves the question
     * It is used by {@link question.php} when saving new data from
     * a form, and also by {@link import.php} when importing questions
     * This function in turn calls {@link save_question_options}
     * to save question-type specific data.
     *
     * Whether we are saving a new question or updating an existing one can be
     * determined by testing !empty($question->id). If it is not empty, we are updating.
     *
     * The question will be saved in category $form->category.
     *
     * @param object $question the question object which should be updated. For a
     *      new question will be mostly empty.
     * @param object $form the object containing the information to save, as if
     *      from the question editing form.
     * @param object $course not really used any more.
     * @return object On success, return the new question object. On failure,
     *       return an object as follows. If the error object has an errors field,
     *       display that as an error message. Otherwise, the editing form will be
     *       redisplayed with validation errors, from validation_errors field, which
     *       is itself an object, shown next to the form fields. (I don't think this
     *       is accurate any more.)
     */
    public function save_question($question, $form) {
        global $USER, $DB, $OUTPUT;

        list($question->category) = explode(',', $form->category);
        $context = $this->get_context_by_category_id($question->category);

        // This default implementation is suitable for most
        // question types.

        // First, save the basic question itself
        $question->name = trim($form->name);
        $question->parent = isset($form->parent) ? $form->parent : 0;
        $question->length = $this->actual_number_of_questions($question);
        $question->penalty = isset($form->penalty) ? $form->penalty : 0;

        if (empty($form->questiontext['text'])) {
            $question->questiontext = '';
        } else {
            $question->questiontext = trim($form->questiontext['text']);;
        }
        $question->questiontextformat = !empty($form->questiontext['format']) ?
                $form->questiontext['format'] : 0;

        if (empty($form->generalfeedback['text'])) {
            $question->generalfeedback = '';
        } else {
            $question->generalfeedback = trim($form->generalfeedback['text']);
        }
        $question->generalfeedbackformat = !empty($form->generalfeedback['format']) ?
                $form->generalfeedback['format'] : 0;

        if (empty($question->name)) {
            $question->name = shorten_text(strip_tags($form->questiontext['text']), 15);
            if (empty($question->name)) {
                $question->name = '-';
            }
        }

        if ($question->penalty > 1 or $question->penalty < 0) {
            $question->errors['penalty'] = get_string('invalidpenalty', 'question');
        }

        if (isset($form->defaultmark)) {
            $question->defaultmark = $form->defaultmark;
        }

        // If the question is new, create it.
        if (empty($question->id)) {
            // Set the unique code
            $question->stamp = make_unique_id_code();
            $question->createdby = $USER->id;
            $question->timecreated = time();
            $question->id = $DB->insert_record('question', $question);
        }

        // Now, whether we are updating a existing question, or creating a new
        // one, we have to do the files processing and update the record.
        /// Question already exists, update.
        $question->modifiedby = $USER->id;
        $question->timemodified = time();

        if (!empty($question->questiontext) && !empty($form->questiontext['itemid'])) {
            $question->questiontext = file_save_draft_area_files($form->questiontext['itemid'],
                    $context->id, 'question', 'questiontext', (int)$question->id,
                    $this->fileoptions, $question->questiontext);
        }
        if (!empty($question->generalfeedback) && !empty($form->generalfeedback['itemid'])) {
            $question->generalfeedback = file_save_draft_area_files(
                    $form->generalfeedback['itemid'], $context->id,
                    'question', 'generalfeedback', (int)$question->id,
                    $this->fileoptions, $question->generalfeedback);
        }
        $DB->update_record('question', $question);

        // Now to save all the answers and type-specific options
        $form->id = $question->id;
        $form->qtype = $question->qtype;
        $form->category = $question->category;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = $question->questiontextformat;
        // current context
        $form->context = $context;

        $result = $this->save_question_options($form);

        if (!empty($result->error)) {
            print_error($result->error);
        }

        if (!empty($result->notice)) {
            notice($result->notice, "question.php?id=$question->id");
        }

        if (!empty($result->noticeyesno)) {
            throw new coding_exception(
                    '$result->noticeyesno no longer supported in save_question.');
        }

        // Give the question a unique version stamp determined by question_hash()
        $DB->set_field('question', 'version', question_hash($question),
                array('id' => $question->id));

        return $question;
    }

    /**
     * Saves question-type specific options
     *
     * This is called by {@link save_question()} to save the question-type specific data
     * @return object $result->error or $result->noticeyesno or $result->notice
     * @param object $question  This holds the information from the editing form,
     *      it is not a standard question object.
     */
    public function save_question_options($question) {
        global $DB;
        $extraquestionfields = $this->extra_question_fields();

        if (is_array($extraquestionfields)) {
            $question_extension_table = array_shift($extraquestionfields);

            $function = 'update_record';
            $questionidcolname = $this->questionid_column_name();
            $options = $DB->get_record($question_extension_table,
                    array($questionidcolname => $question->id));
            if (!$options) {
                $function = 'insert_record';
                $options = new stdClass();
                $options->$questionidcolname = $question->id;
            }
            foreach ($extraquestionfields as $field) {
                if (!isset($question->$field)) {
                    $result = new stdClass();
                    $result->error = "No data for field $field when saving " .
                            $this->name() . ' question id ' . $question->id;
                    return $result;
                }
                $options->$field = $question->$field;
            }

            if (!$DB->{$function}($question_extension_table, $options)) {
                $result = new stdClass();
                $result->error = 'Could not save question options for ' .
                        $this->name() . ' question id ' . $question->id;
                return $result;
            }
        }

        $extraanswerfields = $this->extra_answer_fields();
        // TODO save the answers, with any extra data.
    }

    public function save_hints($formdata, $withparts = false) {
        global $DB;
        $context = $formdata->context;

        $oldhints = $DB->get_records('question_hints',
                array('questionid' => $formdata->id), 'id ASC');

        if (!empty($formdata->hint)) {
            $numhints = max(array_keys($formdata->hint)) + 1;
        } else {
            $numhints = 0;
        }

        if ($withparts) {
            if (!empty($formdata->hintclearwrong)) {
                $numclears = max(array_keys($formdata->hintclearwrong)) + 1;
            } else {
                $numclears = 0;
            }
            if (!empty($formdata->hintshownumcorrect)) {
                $numshows = max(array_keys($formdata->hintshownumcorrect)) + 1;
            } else {
                $numshows = 0;
            }
            $numhints = max($numhints, $numclears, $numshows);
        }

        for ($i = 0; $i < $numhints; $i += 1) {
            if (html_is_blank($formdata->hint[$i]['text'])) {
                $formdata->hint[$i]['text'] = '';
            }

            if ($withparts) {
                $clearwrong = !empty($formdata->hintclearwrong[$i]);
                $shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
            }

            if (empty($formdata->hint[$i]['text']) && empty($clearwrong) &&
                    empty($shownumcorrect)) {
                continue;
            }

            // Update an existing hint if possible.
            $hint = array_shift($oldhints);
            if (!$hint) {
                $hint = new stdClass();
                $hint->questionid = $formdata->id;
                $hint->hint = '';
                $hint->id = $DB->insert_record('question_hints', $hint);
            }

            $hint->hint = $this->import_or_save_files($formdata->hint[$i],
                    $context, 'question', 'hint', $hint->id);
            $hint->hintformat = $formdata->hint[$i]['format'];
            if ($withparts) {
                $hint->clearwrong = $clearwrong;
                $hint->shownumcorrect = $shownumcorrect;
            }
            $DB->update_record('question_hints', $hint);
        }

        // Delete any remaining old hints.
        $fs = get_file_storage();
        foreach ($oldhints as $oldhint) {
            $fs->delete_area_files($context->id, 'question', 'hint', $oldhint->id);
            $DB->delete_records('question_hints', array('id' => $oldhint->id));
        }
    }

    /**
     * Can be used to {@link save_question_options()} to transfer the combined
     * feedback fields from $formdata to $options.
     * @param object $options the $question->options object being built.
     * @param object $formdata the data from the form.
     * @param object $context the context the quetsion is being saved into.
     * @param bool $withparts whether $options->shownumcorrect should be set.
     */
    protected function save_combined_feedback_helper($options, $formdata,
            $context, $withparts = false) {
        $options->correctfeedback = $this->import_or_save_files($formdata->correctfeedback,
                $context, 'question', 'correctfeedback', $formdata->id);
        $options->correctfeedbackformat = $formdata->correctfeedback['format'];

        $options->partiallycorrectfeedback = $this->import_or_save_files(
                $formdata->partiallycorrectfeedback,
                $context, 'question', 'partiallycorrectfeedback', $formdata->id);
        $options->partiallycorrectfeedbackformat = $formdata->partiallycorrectfeedback['format'];

        $options->incorrectfeedback = $this->import_or_save_files($formdata->incorrectfeedback,
                $context, 'question', 'incorrectfeedback', $formdata->id);
        $options->incorrectfeedbackformat = $formdata->incorrectfeedback['format'];

        if ($withparts) {
            $options->shownumcorrect = !empty($formdata->shownumcorrect);
        }

        return $options;
    }

    /**
     * Loads the question type specific options for the question.
     *
     * This function loads any question type specific options for the
     * question from the database into the question object. This information
     * is placed in the $question->options field. A question type is
     * free, however, to decide on a internal structure of the options field.
     * @return bool            Indicates success or failure.
     * @param object $question The question object for the question. This object
     *                         should be updated to include the question type
     *                         specific information (it is passed by reference).
     */
    public function get_question_options($question) {
        global $CFG, $DB, $OUTPUT;

        if (!isset($question->options)) {
            $question->options = new stdClass();
        }

        $extraquestionfields = $this->extra_question_fields();
        if (is_array($extraquestionfields)) {
            $question_extension_table = array_shift($extraquestionfields);
            $extra_data = $DB->get_record($question_extension_table,
                    array($this->questionid_column_name() => $question->id),
                    implode(', ', $extraquestionfields));
            if ($extra_data) {
                foreach ($extraquestionfields as $field) {
                    $question->options->$field = $extra_data->$field;
                }
            } else {
                echo $OUTPUT->notification('Failed to load question options from the table ' .
                        $question_extension_table . ' for questionid ' . $question->id);
                return false;
            }
        }

        $extraanswerfields = $this->extra_answer_fields();
        if (is_array($extraanswerfields)) {
            $answer_extension_table = array_shift($extraanswerfields);
            $question->options->answers = $DB->get_records_sql("
                    SELECT qa.*, qax." . implode(', qax.', $extraanswerfields) . "
                    FROM {question_answers} qa, {{$answer_extension_table}} qax
                    WHERE qa.question = ? AND qax.answerid = qa.id
                    ORDER BY qa.id", array($question->id));
            if (!$question->options->answers) {
                echo $OUTPUT->notification('Failed to load question answers from the table ' .
                        $answer_extension_table . 'for questionid ' . $question->id);
                return false;
            }
        } else {
            // Don't check for success or failure because some question types do
            // not use the answers table.
            $question->options->answers = $DB->get_records('question_answers',
                    array('question' => $question->id), 'id ASC');
        }

        $question->hints = $DB->get_records('question_hints',
                array('questionid' => $question->id), 'id ASC');

        return true;
    }

    /**
     * Create an appropriate question_definition for the question of this type
     * using data loaded from the database.
     * @param object $questiondata the question data loaded from the database.
     * @return question_definition the corresponding question_definition.
     */
    public function make_question($questiondata) {
        $question = $this->make_question_instance($questiondata);
        $this->initialise_question_instance($question, $questiondata);
        return $question;
    }

    /**
     * Create an appropriate question_definition for the question of this type
     * using data loaded from the database.
     * @param object $questiondata the question data loaded from the database.
     * @return question_definition an instance of the appropriate question_definition subclass.
     *      Still needs to be initialised.
     */
    protected function make_question_instance($questiondata) {
        question_bank::load_question_definition_classes($this->name());
        $class = 'qtype_' . $this->name() . '_question';
        return new $class();
    }

    /**
     * Initialise the common question_definition fields.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        $question->id = $questiondata->id;
        $question->category = $questiondata->category;
        $question->contextid = $questiondata->contextid;
        $question->parent = $questiondata->parent;
        $question->qtype = $this;
        $question->name = $questiondata->name;
        $question->questiontext = $questiondata->questiontext;
        $question->questiontextformat = $questiondata->questiontextformat;
        $question->generalfeedback = $questiondata->generalfeedback;
        $question->generalfeedbackformat = $questiondata->generalfeedbackformat;
        $question->defaultmark = $questiondata->defaultmark + 0;
        $question->length = $questiondata->length;
        $question->penalty = $questiondata->penalty;
        $question->stamp = $questiondata->stamp;
        $question->version = $questiondata->version;
        $question->hidden = $questiondata->hidden;
        $question->timecreated = $questiondata->timecreated;
        $question->timemodified = $questiondata->timemodified;
        $question->createdby = $questiondata->createdby;
        $question->modifiedby = $questiondata->modifiedby;

        //Fill extra question fields values
        $extraquestionfields = $this->extra_question_fields();
        if (is_array($extraquestionfields)) {
            //omit table name
            array_shift($extraquestionfields);
            foreach($extraquestionfields as $field) {
                $question->$field = $questiondata->options->$field;
            }
        }

        $this->initialise_question_hints($question, $questiondata);
    }

    /**
     * Initialise question_definition::hints field.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_hints(question_definition $question, $questiondata) {
        if (empty($questiondata->hints)) {
            return;
        }
        foreach ($questiondata->hints as $hint) {
            $question->hints[] = $this->make_hint($hint);
        }
    }

    /**
     * Create a question_hint, or an appropriate subclass for this question,
     * from a row loaded from the database.
     * @param object $hint the DB row from the question hints table.
     * @return question_hint
     */
    protected function make_hint($hint) {
        return question_hint::load_from_record($hint);
    }

    /**
     * Initialise the combined feedback fields.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     * @param bool $withparts whether to set the shownumcorrect field.
     */
    protected function initialise_combined_feedback(question_definition $question,
            $questiondata, $withparts = false) {
        $question->correctfeedback = $questiondata->options->correctfeedback;
        $question->correctfeedbackformat = $questiondata->options->correctfeedbackformat;
        $question->partiallycorrectfeedback = $questiondata->options->partiallycorrectfeedback;
        $question->partiallycorrectfeedbackformat =
                $questiondata->options->partiallycorrectfeedbackformat;
        $question->incorrectfeedback = $questiondata->options->incorrectfeedback;
        $question->incorrectfeedbackformat = $questiondata->options->incorrectfeedbackformat;
        if ($withparts) {
            $question->shownumcorrect = $questiondata->options->shownumcorrect;
        }
    }

    /**
     * Initialise question_definition::answers field.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     * @param bool $forceplaintextanswers most qtypes assume that answers are
     *      FORMAT_PLAIN, and dont use the answerformat DB column (it contains
     *      the default 0 = FORMAT_MOODLE). Therefore, by default this method
     *      ingores answerformat. Pass false here to use answerformat. For example
     *      multichoice does this.
     */
    protected function initialise_question_answers(question_definition $question,
            $questiondata, $forceplaintextanswers = true) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new question_answer($a->id, $a->answer,
                    $a->fraction, $a->feedback, $a->feedbackformat);
            if (!$forceplaintextanswers) {
                $question->answers[$a->id]->answerformat = $a->answerformat;
            }
        }
    }

    /**
     * Deletes the question-type specific data when a question is deleted.
     * @param int $question the question being deleted.
     * @param int $contextid the context this quesiotn belongs to.
     */
    public function delete_question($questionid, $contextid) {
        global $DB;

        $this->delete_files($questionid, $contextid);

        $extraquestionfields = $this->extra_question_fields();
        if (is_array($extraquestionfields)) {
            $question_extension_table = array_shift($extraquestionfields);
            $DB->delete_records($question_extension_table,
                    array($this->questionid_column_name() => $questionid));
        }

        $extraanswerfields = $this->extra_answer_fields();
        if (is_array($extraanswerfields)) {
            $answer_extension_table = array_shift($extraanswerfields);
            $DB->delete_records_select($answer_extension_table,
                    'answerid IN (SELECT qa.id FROM {question_answers} qa WHERE qa.question = ?)',
                    array($questionid));
        }

        $DB->delete_records('question_answers', array('question' => $questionid));

        $DB->delete_records('question_hints', array('questionid' => $questionid));
    }

    /**
     * Returns the number of question numbers which are used by the question
     *
     * This function returns the number of question numbers to be assigned
     * to the question. Most question types will have length one; they will be
     * assigned one number. The 'description' type, however does not use up a
     * number and so has a length of zero. Other question types may wish to
     * handle a bundle of questions and hence return a number greater than one.
     * @return int         The number of question numbers which should be
     *                         assigned to the question.
     * @param object $question The question whose length is to be determined.
     *                         Question type specific information is included.
     */
    public function actual_number_of_questions($question) {
        // By default, each question is given one number
        return 1;
    }

    /**
     * @param object $question
     * @return number|null either a fraction estimating what the student would
     * score by guessing, or null, if it is not possible to estimate.
     */
    public function get_random_guess_score($questiondata) {
        return 0;
    }

    /**
     * This method should return all the possible types of response that are
     * recognised for this question.
     *
     * The question is modelled as comprising one or more subparts. For each
     * subpart, there are one or more classes that that students response
     * might fall into, each of those classes earning a certain score.
     *
     * For example, in a shortanswer question, there is only one subpart, the
     * text entry field. The response the student gave will be classified according
     * to which of the possible $question->options->answers it matches.
     *
     * For the matching question type, there will be one subpart for each
     * question stem, and for each stem, each of the possible choices is a class
     * of student's response.
     *
     * A response is an object with two fields, ->responseclass is a string
     * presentation of that response, and ->fraction, the credit for a response
     * in that class.
     *
     * Array keys have no specific meaning, but must be unique, and must be
     * the same if this function is called repeatedly.
     *
     * @param object $question the question definition data.
     * @return array keys are subquestionid, values are arrays of possible
     *      responses to that subquestion.
     */
    public function get_possible_responses($questiondata) {
        return array();
    }

    /**
     * Utility method used by {@link qtype_renderer::head_code()}. It looks
     * for any of the files script.js or script.php that exist in the plugin
     * folder and ensures they get included.
     */
    public function find_standard_scripts() {
        global $PAGE;

        $plugindir = $this->plugin_dir();
        $plugindirrel = 'question/type/' . $this->name();

        if (file_exists($plugindir . '/script.js')) {
            $PAGE->requires->js('/' . $plugindirrel . '/script.js');
        }
        if (file_exists($plugindir . '/script.php')) {
            $PAGE->requires->js('/' . $plugindirrel . '/script.php');
        }
    }

    /**
     * Returns true if the editing wizard is finished, false otherwise.
     *
     * The default implementation returns true, which is suitable for all question-
     * types that only use one editing form. This function is used in
     * question.php to decide whether we can regrade any states of the edited
     * question and redirect to edit.php.
     *
     * The dataset dependent question-type, which is extended by the calculated
     * question-type, overwrites this method because it uses multiple pages (i.e.
     * a wizard) to set up the question and associated datasets.
     *
     * @param object $form  The data submitted by the previous page.
     *
     * @return bool      Whether the wizard's last page was submitted or not.
     */
    public function finished_edit_wizard($form) {
        //In the default case there is only one edit page.
        return true;
    }

    /// IMPORT/EXPORT FUNCTIONS /////////////////

    /*
     * Imports question from the Moodle XML format
     *
     * Imports question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $question_type = $data['@']['type'];
        if ($question_type != $this->name()) {
            return false;
        }

        $extraquestionfields = $this->extra_question_fields();
        if (!is_array($extraquestionfields)) {
            return false;
        }

        //omit table name
        array_shift($extraquestionfields);
        $qo = $format->import_headers($data);
        $qo->qtype = $question_type;

        foreach ($extraquestionfields as $field) {
            $qo->$field = $format->getpath($data, array('#', $field, 0, '#'), '');
        }

        // run through the answers
        $answers = $data['#']['answer'];
        $a_count = 0;
        $extraanswersfields = $this->extra_answer_fields();
        if (is_array($extraanswersfields)) {
            array_shift($extraanswersfields);
        }
        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer);
            if (!$this->has_html_answers()) {
                $qo->answer[$a_count] = $ans->answer['text'];
            } else {
                $qo->answer[$a_count] = $ans->answer;
            }
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            if (is_array($extraanswersfields)) {
                foreach ($extraanswersfields as $field) {
                    $qo->{$field}[$a_count] =
                        $format->getpath($answer, array('#', $field, 0, '#'), '');
                }
            }
            ++$a_count;
        }
        return $qo;
    }

    /*
     * Export question to the Moodle XML format
     *
     * Export question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $extraquestionfields = $this->extra_question_fields();
        if (!is_array($extraquestionfields)) {
            return false;
        }

        //omit table name
        array_shift($extraquestionfields);
        $expout='';
        foreach ($extraquestionfields as $field) {
            $exportedvalue = $format->xml_escape($question->options->$field);
            $expout .= "    <$field>{$exportedvalue}</$field>\n";
        }

        $extraanswersfields = $this->extra_answer_fields();
        if (is_array($extraanswersfields)) {
            array_shift($extraanswersfields);
        }
        foreach ($question->options->answers as $answer) {
            $extra = '';
            if (is_array($extraanswersfields)) {
                foreach ($extraanswersfields as $field) {
                    $exportedvalue = $format->xml_escape($answer->$field);
                    $extra .= "      <{$field}>{$exportedvalue}</{$field}>\n";
                }
            }

            $expout .= $format->write_answer($answer, $extra);
        }
        return $expout;
    }

    /**
     * Abstract function implemented by each question type. It runs all the code
     * required to set up and save a question of any type for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    public function generate_test($name, $courseid=null) {
        $form = new stdClass();
        $form->name = $name;
        $form->questiontextformat = 1;
        $form->questiontext = 'test question, generated by script';
        $form->defaultmark = 1;
        $form->penalty = 0.3333333;
        $form->generalfeedback = "Well done";

        $context = context_course::instance($courseid);
        $newcategory = question_make_default_categories(array($context));
        $form->category = $newcategory->id . ',1';

        $question = new stdClass();
        $question->courseid = $courseid;
        $question->qtype = $this->qtype;
        return array($form, $question);
    }

    /**
     * Get question context by category id
     * @param int $category
     * @return object $context
     */
    protected function get_context_by_category_id($category) {
        global $DB;
        $contextid = $DB->get_field('question_categories', 'contextid', array('id'=>$category));
        $context = context::instance_by_id($contextid, IGNORE_MISSING);
        return $context;
    }

    /**
     * Save the file belonging to one text field.
     *
     * @param array $field the data from the form (or from import). This will
     *      normally have come from the formslib editor element, so it will be an
     *      array with keys 'text', 'format' and 'itemid'. However, when we are
     *      importing, it will be an array with keys 'text', 'format' and 'files'
     * @param object $context the context the question is in.
     * @param string $component indentifies the file area question.
     * @param string $filearea indentifies the file area questiontext,
     *      generalfeedback, answerfeedback, etc.
     * @param int $itemid identifies the file area.
     *
     * @return string the text for this field, after files have been processed.
     */
    protected function import_or_save_files($field, $context, $component, $filearea, $itemid) {
        if (!empty($field['itemid'])) {
            // This is the normal case. We are safing the questions editing form.
            return file_save_draft_area_files($field['itemid'], $context->id, $component,
                    $filearea, $itemid, $this->fileoptions, trim($field['text']));

        } else if (!empty($field['files'])) {
            // This is the case when we are doing an import.
            foreach ($field['files'] as $file) {
                $this->import_file($context, $component,  $filearea, $itemid, $file);
            }
        }
        return trim($field['text']);
    }

    /**
     * Move all the files belonging to this question from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'questiontext', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'generalfeedback', $questionid);
    }

    /**
     * Move all the files belonging to this question's answers when the question
     * is moved from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     * @param bool $answerstoo whether there is an 'answer' question area,
     *      as well as an 'answerfeedback' one. Default false.
     */
    protected function move_files_in_answers($questionid, $oldcontextid,
            $newcontextid, $answerstoo = false) {
        global $DB;
        $fs = get_file_storage();

        $answerids = $DB->get_records_menu('question_answers',
                array('question' => $questionid), 'id', 'id,1');
        foreach ($answerids as $answerid => $notused) {
            if ($answerstoo) {
                $fs->move_area_files_to_new_context($oldcontextid,
                        $newcontextid, 'question', 'answer', $answerid);
            }
            $fs->move_area_files_to_new_context($oldcontextid,
                    $newcontextid, 'question', 'answerfeedback', $answerid);
        }
    }

    /**
     * Move all the files belonging to this question's hints when the question
     * is moved from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     * @param bool $answerstoo whether there is an 'answer' question area,
     *      as well as an 'answerfeedback' one. Default false.
     */
    protected function move_files_in_hints($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        $hintids = $DB->get_records_menu('question_hints',
                array('questionid' => $questionid), 'id', 'id,1');
        foreach ($hintids as $hintid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid,
                    $newcontextid, 'question', 'hint', $hintid);
        }
    }

    /**
     * Move all the files belonging to this question's answers when the question
     * is moved from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     * @param bool $answerstoo whether there is an 'answer' question area,
     *      as well as an 'answerfeedback' one. Default false.
     */
    protected function move_files_in_combined_feedback($questionid, $oldcontextid,
            $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'correctfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'partiallycorrectfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'incorrectfeedback', $questionid);
    }

    /**
     * Delete all the files belonging to this question.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'question', 'questiontext', $questionid);
        $fs->delete_area_files($contextid, 'question', 'generalfeedback', $questionid);
    }

    /**
     * Delete all the files belonging to this question's answers.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     * @param bool $answerstoo whether there is an 'answer' question area,
     *      as well as an 'answerfeedback' one. Default false.
     */
    protected function delete_files_in_answers($questionid, $contextid, $answerstoo = false) {
        global $DB;
        $fs = get_file_storage();

        $answerids = $DB->get_records_menu('question_answers',
                array('question' => $questionid), 'id', 'id,1');
        foreach ($answerids as $answerid => $notused) {
            if ($answerstoo) {
                $fs->delete_area_files($contextid, 'question', 'answer', $answerid);
            }
            $fs->delete_area_files($contextid, 'question', 'answerfeedback', $answerid);
        }
    }

    /**
     * Delete all the files belonging to this question's hints.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files_in_hints($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        $hintids = $DB->get_records_menu('question_hints',
                array('questionid' => $questionid), 'id', 'id,1');
        foreach ($hintids as $hintid => $notused) {
            $fs->delete_area_files($contextid, 'question', 'hint', $hintid);
        }
    }

    /**
     * Delete all the files belonging to this question's answers.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     * @param bool $answerstoo whether there is an 'answer' question area,
     *      as well as an 'answerfeedback' one. Default false.
     */
    protected function delete_files_in_combined_feedback($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        $fs->delete_area_files($contextid,
                'question', 'correctfeedback', $questionid);
        $fs->delete_area_files($contextid,
                'question', 'partiallycorrectfeedback', $questionid);
        $fs->delete_area_files($contextid,
                'question', 'incorrectfeedback', $questionid);
    }

    public function import_file($context, $component, $filearea, $itemid, $file) {
        $fs = get_file_storage();
        $record = new stdClass();
        if (is_object($context)) {
            $record->contextid = $context->id;
        } else {
            $record->contextid = $context;
        }
        $record->component = $component;
        $record->filearea  = $filearea;
        $record->itemid    = $itemid;
        $record->filename  = $file->name;
        $record->filepath  = '/';
        return $fs->create_file_from_string($record, $this->decode_file($file));
    }

    protected function decode_file($file) {
        switch ($file->encoding) {
            case 'base64':
            default:
                return base64_decode($file->content);
        }
    }
}


/**
 * This class is used in the return value from
 * {@link question_type::get_possible_responses()}.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_possible_response {
    /**
     * @var string the classification of this response the student gave to this
     * part of the question. Must match one of the responseclasses returned by
     * {@link question_type::get_possible_responses()}.
     */
    public $responseclass;

    /** @var string the (partial) credit awarded for this responses. */
    public $fraction;

    /**
     * Constructor, just an easy way to set the fields.
     * @param string $responseclassid see the field descriptions above.
     * @param string $response see the field descriptions above.
     * @param number $fraction see the field descriptions above.
     */
    public function __construct($responseclass, $fraction) {
        $this->responseclass = $responseclass;
        $this->fraction = $fraction;
    }

    public static function no_response() {
        return new question_possible_response(get_string('noresponse', 'question'), 0);
    }
}
