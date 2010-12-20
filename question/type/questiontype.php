<?php  // $Id$
/**
 * The default questiontype class.
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

require_once($CFG->dirroot . '/question/engine/lib.php');

// DONOTCOMMIT
class default_questiontype {
    function plugin_dir() {
        return '';
    }
}
/**
 * This is the base class for Moodle question types.
 *
 * There are detailed comments on each method, explaining what the method is
 * for, and the circumstances under which you might need to override it.
 *
 * Note: the questiontype API should NOT be considered stable yet. Very few
 * question tyeps have been produced yet, so we do not yet know all the places
 * where the current API is insufficient. I would rather learn from the
 * experiences of the first few question type implementors, and improve the
 * interface to meet their needs, rather the freeze the API prematurely and
 * condem everyone to working round a clunky interface for ever afterwards.
 *
 * @package questionbank
 * @subpackage questiontypes
 */
class question_type {

    public function __construct() {
    }

    /**
     * @return string the name of this question type.
     */
    public function name() {
        return substr(get_class($this), 6);
    }

    /**
     * The name this question should appear as in the create new question
     * dropdown.
     *
     * @return mixed the desired string, or false to hide this question type in the menu.
     */
    public function menu_name() {
        $name = $this->name();
        return get_string($name, 'qtype_' . $name);
    }

    /**
     * @return boolean true if this question type sometimes requires manual grading.
     */
    public function is_manual_graded() {
        return false;
    }

    /**
     * @param object $question a question of this type.
     * @param string $otherquestionsinuse comma-separate list of other question ids in this attempt.
     * @return boolean true if a particular instance of this question requires manual grading.
     */
    public function is_question_manual_graded($question, $otherquestionsinuse) {
        return $this->is_manual_graded();
    }

    /**
     * @return boolean true if this question type can be used by the random question type.
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
     * @return boolean whether this report can analyse all the student reponses
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
    protected function questionid_column_name() {
        return 'questionid';
    }

    /**
     * If your question type has a table that extends the question_answers table,
     * make this method return an array wherer the first element is the table name,
     * and the subsequent entries are the column names (apart from id and answerid).
     *
     * @return mixed array as above, or null to tell the base class to do nothing.
     */
     protected function extra_answer_fields() {
         return null;
     }

    /**
     * Return an instance of the question editing form definition. This looks for a
     * class called edit_{$this->name()}_question_form in the file
     * {$CFG->docroot}/question/type/{$this->name()}/edit_{$this->name()}_question_form.php
     * and if it exists returns an instance of it.
     *
     * @param string $submiturl passed on to the constructor call.
     * @return object an instance of the form definition, or null if one could not be found.
     */
    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once("{$CFG->dirroot}/question/type/edit_question_form.php");
        $definition_file = $CFG->dirroot.'/question/type/'.$this->name().'/edit_'.$this->name().'_form.php';
        if (!(is_readable($definition_file) && is_file($definition_file))) {
            return null;
        }
        require_once($definition_file);
        $classname = 'question_edit_'.$this->name().'_form';
        if (!class_exists($classname)) {
            return null;
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
    public function display_question_editing_page(&$mform, $question, $wizardnow) {
        $name = $this->name();
        print_heading_with_help($this->get_heading(empty($question->id)), $name, 'qtype_' . $name);
        $permissionstrs = array();
        if (!empty($question->id)){
            if ($question->formoptions->canedit){
                $permissionstrs[] = get_string('permissionedit', 'question');
            }
            if ($question->formoptions->canmove){
                $permissionstrs[] = get_string('permissionmove', 'question');
            }
            if ($question->formoptions->cansaveasnew){
                $permissionstrs[] = get_string('permissionsaveasnew', 'question');
            }
        }
        if (!$question->formoptions->movecontext  && count($permissionstrs)){
            print_heading(get_string('permissionto', 'question'), 'center', 3);
            $html = '<ul>';
            foreach ($permissionstrs as $permissionstr){
                $html .= '<li>'.$permissionstr.'</li>';
            }
            $html .= '</ul>';
            print_box($html, 'boxwidthnarrow boxaligncenter generalbox');
        }
        $mform->display();
    }

    /**
     * Method called by display_question_editing_page and by question.php to get heading for breadcrumbs.
     *
     * @return string the heading
     */
    public function get_heading($adding = false){
        $name = $this->name();
        if ($adding){
            $action = 'adding';
        } else {
            $action = 'editing';
        }
        return get_string($action . $name, 'qtype_' . $name);
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
    * @param object $question the question object which should be updated. For a new question will be mostly empty.
    * @param object $form the object containing the information to save, as if from the question editing form.
    * @param object $course not really used any more.
    * @return object On success, return the new question object. On failure,
    *       return an object as follows. If the error object has an errors field,
    *       display that as an error message. Otherwise, the editing form will be
    *       redisplayed with validation errors, from validation_errors field, which
    *       is itself an object, shown next to the form fields. (I don't think this is accurate any more.)
    */
    public function save_question($question, $form, $course) {
        global $USER;

        // This default implementation is suitable for most
        // question types.

        // First, save the basic question itself
        $question->name = trim($form->name);
        $question->questiontext = trim($form->questiontext);
        $question->questiontextformat = $form->questiontextformat;
        $question->parent = isset($form->parent) ? $form->parent : 0;
        $question->length = $this->actual_number_of_questions($question);
        $question->penalty = isset($form->penalty) ? $form->penalty : 0;

        if (empty($form->image)) {
            $question->image = '';
        } else {
            $question->image = $form->image;
        }

        if (empty($form->generalfeedback)) {
            $question->generalfeedback = '';
        } else {
            $question->generalfeedback = trim($form->generalfeedback);
        }

        if (empty($question->name)) {
            $question->name = shorten_text(strip_tags($question->questiontext), 15);
            if (empty($question->name)) {
                $question->name = '-';
            }
        }

        if ($question->penalty > 1 or $question->penalty < 0) {
            $question->errors['penalty'] = get_string('invalidpenalty', 'quiz');
        }

        if (isset($form->defaultmark)) {
            $question->defaultmark = $form->defaultmark;
        }

        list($question->category) = explode(',', $form->category);

        if (!empty($question->id)) {
        /// Question already exists, update.
            $question->modifiedby = $USER->id;
            $question->timemodified = time();
            if (!update_record('question', $question)) {
                error('Could not update question!');
            }

        } else {
        /// New question.
            // Set the unique code
            $question->stamp = make_unique_id_code();
            $question->createdby = $USER->id;
            $question->modifiedby = $USER->id;
            $question->timecreated = time();
            $question->timemodified = time();
            if (!$question->id = insert_record('question', $question)) {
                error('Could not insert new question!');
            }
        }

        // Now to save all the answers and type-specific options
        $form->id = $question->id;
        $form->qtype = $question->qtype;
        $form->category = $question->category;
        $form->questiontext = $question->questiontext;

        $result = $this->save_question_options($form);

        if (!empty($result->error)) {
            error($result->error);
        }

        if (!empty($result->notice)) {
            notice($result->notice, "question.php?id=$question->id");
        }

        if (!empty($result->noticeyesno)) {
            notice_yesno($result->noticeyesno, "question.php?id=$question->id&amp;courseid={$course->id}",
                "edit.php?courseid={$course->id}");
            print_footer($course);
            exit;
        }

        // Give the question a unique version stamp determined by question_hash()
        if (!set_field('question', 'version', question_hash($question), 'id', $question->id)) {
            error('Could not update question version field');
        }

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
        $extra_question_fields = $this->extra_question_fields();

        if (is_array($extra_question_fields)) {
            $question_extension_table = array_shift($extra_question_fields);

            $function = 'update_record';
            $questionidcolname = $this->questionid_column_name();
            $options = get_record($question_extension_table,  $questionidcolname, $question->id);
            if (!$options) {
                $function = 'insert_record';
                $options = new stdClass;
                $options->$questionidcolname = $question->id;
            }
            foreach ($extra_question_fields as $field) {
                if (!isset($question->$field)) {
                    $result = new stdClass;
                    $result->error = "No data for field $field when saving " .
                            $this->name() . ' question id ' . $question->id;
                    return $result;
                }
                $options->$field = $question->$field;
            }

            if (!$function($question_extension_table, $options)) {
                $result = new stdClass;
                $result->error = 'Could not save question options for ' .
                        $this->name() . ' question id ' . $question->id;
                return $result;
            }
        }

        $extra_answer_fields = $this->extra_answer_fields();
        // TODO save the answers, with any extra data.

        return null;
    }

    public function save_hints($formdata, $withparts = false) {
        delete_records('question_hints', 'questionid', $formdata->id);

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
            $hint = new stdClass;
            $hint->hint = $formdata->hint[$i];
            $hint->questionid = $formdata->id;

            if (html_is_blank($hint->hint)) {
                $hint->hint = '';
            }

            if ($withparts) {
                $hint->clearwrong = !empty($formdata->hintclearwrong[$i]);
                $hint->shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
            }

            if (empty($hint->hint) && empty($hint->clearwrong) && empty($hint->shownumcorrect)) {
                continue;
            }

            insert_record('question_hints', $hint);
        }
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
        global $CFG;

        $extra_question_fields = $this->extra_question_fields();
        if (is_array($extra_question_fields)) {
            $question_extension_table = array_shift($extra_question_fields);
            $extra_data = get_record($question_extension_table, $this->questionid_column_name(), $question->id, '', '', '', '', implode(', ', $extra_question_fields));
            if ($extra_data) {
                foreach ($extra_question_fields as $field) {
                    $question->options->$field = $extra_data->$field;
                }
            } else {
                notify("Failed to load question options from the table $question_extension_table for questionid " .
                        $question->id);
                return false;
            }
        }

        $extra_answer_fields = $this->extra_answer_fields();
        if (is_array($extra_answer_fields)) {
            $answer_extension_table = array_shift($extra_answer_fields);
            $question->options->answers = get_records_sql('
                    SELECT qa.*, qax.' . implode(', qax.', $extra_answer_fields) . '
                    FROM ' . $CFG->prefix . 'question_answers qa, ' . $CFG->prefix . '$answer_extension_table qax
                    WHERE qa.questionid = ' . $question->id . ' AND qax.answerid = qa.id');
            if (!$question->options->answers) {
                notify("Failed to load question answers from the table $answer_extension_table for questionid " .
                        $question->id);
                return false;
            }
        } else {
            // Don't check for success or failure because some question types do not use the answers table.
            $question->options->answers = get_records('question_answers', 'question', $question->id, 'id ASC');
        }

        $question->hints = get_records('question_hints', 'questionid', $question->id, 'id ASC');

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
        $question->parent = $questiondata->parent;
        $question->qtype = $this;
        $question->name = $questiondata->name;
        $question->questiontext = $questiondata->questiontext;
        $question->questiontextformat = $questiondata->questiontextformat;
        $question->generalfeedback = $questiondata->generalfeedback;
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
     * Initialise question_definition::answers field.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_answers(question_definition $question, $questiondata) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new question_answer($a->answer, $a->fraction, $a->feedback);
        }
    }

    /**
    * Deletes a question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    public function delete_question($questionid) {
        global $CFG;
        $success = true;

        $extra_question_fields = $this->extra_question_fields();
        if (is_array($extra_question_fields)) {
            $question_extension_table = array_shift($extra_question_fields);
            $success = $success && delete_records($question_extension_table,
                    $this->questionid_column_name(), $questionid);
        }

        $extra_answer_fields = $this->extra_answer_fields();
        if (is_array($extra_answer_fields)) {
            $answer_extension_table = array_shift($extra_answer_fields);
            $success = $success && delete_records_select($answer_extension_table,
                    "answerid IN (SELECT qa.id FROM {$CFG->prefix}question_answers qa WHERE qa.question = $questionid)");
        }

        $success = $success && delete_records('question_answers', 'question', $questionid);

        $success = $success && delete_records('question_hints', 'questionid', $questionid);

        return $success;
    }

    /**
    * Returns the number of question numbers which are used by the question
    *
    * This function returns the number of question numbers to be assigned
    * to the question. Most question types will have length one; they will be
    * assigned one number. The 'description' type, however does not use up a
    * number and so has a length of zero. Other question types may wish to
    * handle a bundle of questions and hence return a number greater than one.
    * @return integer         The number of question numbers which should be
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
    function get_random_guess_score($questiondata) {
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
    function get_possible_responses($questiondata) {
        return array();
    }

    /**
     * Return any CSS JavaScript required on the head of the question editing
     * page question/question.php.
     *
     * @return an array of bits of HTML to add to the head of pages where
     * this question is displayed in the body. The array should use
     * integer array keys, which have no significance.
     */
    public function get_editing_head_contributions() {
        // By default, we link to any of the files styles.css, styles.php,
        // script.js or script.php that exist in the plugin folder.
        // Core question types should not use this mechanism. Their styles
        // should be included in the standard theme.
        return $this->find_standard_scripts_and_css();
    }

    /**
     * Utility method used by @see{get_editing_head_contributions} and
     * @see{get_editing_head_contributions}. This looks for any of the files
     * styles.css, styles.php, script.js or script.php that exist in the plugin
     * folder and ensures they get included.
     *
     * @return array as required by get_editing_head_contributions.
     */
    public function find_standard_scripts_and_css() {
        $plugindir = $this->plugin_dir();
        $baseurl = $this->plugin_baseurl();

        if (file_exists($plugindir . '/script.js')) {
            require_js($baseurl . '/script.js');
        }
        if (file_exists($plugindir . '/script.php')) {
            require_js($baseurl . '/script.php');
        }

        $stylesheets = array();
        if (file_exists($plugindir . '/styles.css')) {
            $stylesheets[] = 'styles.css';
        }
        if (file_exists($plugindir . '/styles.php')) {
            $stylesheets[] = 'styles.php';
        }
        $contributions = array();
        foreach ($stylesheets as $stylesheet) {
            $contributions[] = '<link rel="stylesheet" type="text/css" href="' .
                    $baseurl . '/' . $stylesheet . '" />';
        }
        return $contributions;
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
    * @return boolean      Whether the wizard's last page was submitted or not.
    */
    public function finished_edit_wizard(&$form) {
        //In the default case there is only one edit page.
        return true;
    }

    /*
     * Find all course / site files linked from a question.
     *
     * Need to check for links to files in question_answers.answer and feedback
     * and in question table in generalfeedback and questiontext fields. Methods
     * on child classes will also check extra question specific fields.
     *
     * Needs to be overriden for child classes that have extra fields containing
     * html.
     *
     * @param string html the html to search
     * @param int courseid search for files for courseid course or set to siteid for
     *              finding site files.
     * @return array of url, relative url is key and array with one item = question id as value
     *                  relative url is relative to course/site files directory root.
     */
    public function find_file_links($question, $courseid){
        $urls = array();

    /// Question image
        if ($question->image != ''){
            if (substr(strtolower($question->image), 0, 7) == 'http://') {
                $matches = array();

                //support for older questions where we have a complete url in image field
                if (preg_match('!^'.question_file_links_base_url($courseid).'(.*)!i', $question->image, $matches)){
                    if ($cleanedurl = question_url_check($urls[$matches[2]])){
                        $urls[$cleanedurl] = null;
                    }
                }
            } else {
                if ($question->image != ''){
                    if ($cleanedurl = question_url_check($question->image)){
                        $urls[$cleanedurl] = null;//will be set later
                    }
                }

            }

        }

    /// Questiontext and general feedback.
        $urls += question_find_file_links_from_html($question->questiontext, $courseid);
        $urls += question_find_file_links_from_html($question->generalfeedback, $courseid);

    /// Answers, if this question uses them.
        if (isset($question->options->answers)){
            foreach ($question->options->answers as $answerkey => $answer){
            /// URLs in the answers themselves, if appropriate.
                if ($this->has_html_answers()) {
                    $urls += question_find_file_links_from_html($answer->answer, $courseid);
                }
            /// URLs in the answer feedback.
                $urls += question_find_file_links_from_html($answer->feedback, $courseid);
            }
        }

    /// Set all the values of the array to the question object
        if ($urls){
            $urls = array_combine(array_keys($urls), array_fill(0, count($urls), array($question->id)));
        }
        return $urls;
    }

    /*
     * Find all course / site files linked from a question.
     *
     * Need to check for links to files in question_answers.answer and feedback
     * and in question table in generalfeedback and questiontext fields. Methods
     * on child classes will also check extra question specific fields.
     *
     * Needs to be overriden for child classes that have extra fields containing
     * html.
     *
     * @param string html the html to search
     * @param int course search for files for courseid course or set to siteid for
     *              finding site files.
     * @return array of files, file name is key and array with one item = question id as value
     */
    public function replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination){
        global $CFG;
        $updateqrec = false;

    /// Question image
        if (!empty($question->image)){
            //support for older questions where we have a complete url in image field
            if (substr(strtolower($question->image), 0, 7) == 'http://') {
                $questionimage = preg_replace('!^'.question_file_links_base_url($fromcourseid).preg_quote($url, '!').'$!i', $destination, $question->image, 1);
            } else {
                $questionimage = preg_replace('!^'.preg_quote($url, '!').'$!i', $destination, $question->image, 1);
            }
            if ($questionimage != $question->image){
                $question->image = $questionimage;
                $updateqrec = true;
            }
        }

    /// Questiontext and general feedback.
        $question->questiontext = question_replace_file_links_in_html($question->questiontext, $fromcourseid, $tocourseid, $url, $destination, $updateqrec);
        $question->generalfeedback = question_replace_file_links_in_html($question->generalfeedback, $fromcourseid, $tocourseid, $url, $destination, $updateqrec);

    /// If anything has changed, update it in the database.
        if ($updateqrec){
            if (!update_record('question', addslashes_recursive($question))){
                error ('Couldn\'t update question '.$question->name);
            }
        }


    /// Answers, if this question uses them.
        if (isset($question->options->answers)){
            //answers that do not need updating have been unset
            foreach ($question->options->answers as $answer){
                $answerchanged = false;
            /// URLs in the answers themselves, if appropriate.
                if ($this->has_html_answers()) {
                    $answer->answer = question_replace_file_links_in_html($answer->answer, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
                }
            /// URLs in the answer feedback.
                $answer->feedback = question_replace_file_links_in_html($answer->feedback, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
            /// If anything has changed, update it in the database.
                if ($answerchanged){
                    if (!update_record('question_answers', addslashes_recursive($answer))){
                        error ('Couldn\'t update question ('.$question->name.') answer '.$answer->id);
                    }
                }
            }
        }
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    public function backup($bf,$preferences,$question,$level=6) {

        $status = true;
        $extraquestionfields = $this->extra_question_fields();

        if (is_array($extraquestionfields)) {
            $questionextensiontable = array_shift($extraquestionfields);
            $record = get_record($questionextensiontable, $this->questionid_column_name(), $question);
            if ($record) {
                $tagname = strtoupper($this->name());
                $status = $status && fwrite($bf, start_tag($tagname, $level, true));
                foreach ($extraquestionfields as $field) {
                    if (!isset($record->$field)) {
                        echo "No data for field $field when backuping " .
                                $this->name() . ' question id ' . $question;
                        return false;
                    }
                    fwrite($bf, full_tag(strtoupper($field), $level + 1, false, $record->$field));
                }
                $status = $status && fwrite($bf, end_tag($tagname, $level, true));
            }
        }

        $extraasnwersfields = $this->extra_answer_fields();
        if (is_array($extraasnwersfields)) {
            //TODO backup the answers, with any extra data.
        } else {
            $status = $status && question_backup_answers($bf, $preferences, $question);
        }
        return $status;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    public function restore($old_question_id,$new_question_id,$info,$restore) {

        $status = true;
        $extraquestionfields = $this->extra_question_fields();

        if (is_array($extraquestionfields)) {
            $questionextensiontable = array_shift($extraquestionfields);
            $tagname = strtoupper($this->name());
            $recordinfo = $info['#'][$tagname][0];

            $record = new stdClass;
            $qidcolname = $this->questionid_column_name();
            $record->$qidcolname = $new_question_id;
            foreach ($extraquestionfields as $field) {
                $record->$field = backup_todb($recordinfo['#'][strtoupper($field)]['0']['#']);
            }
            if (!insert_record($questionextensiontable, $record)) {
                echo "Can't insert record in $questionextensiontable when restoring " .
                                $this->name() . ' question id ' . $question;
                $status = false;
            }
        }
        //TODO restore extra data in answers
        return $status;
    }

    public function restore_map($old_question_id,$new_question_id,$info,$restore) {
        // There is nothing to decode
        return true;
    }

    public function restore_recode_answer($state, $restore) {
        // There is nothing to decode
        return $state->answer;
    }

/// IMPORT/EXPORT FUNCTIONS /////////////////

    /*
     * Imports question from the Moodle XML format
     *
     * Imports question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     */
    public function import_from_xml($data, $question, $format, $extra=null) {
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
            $qo->$field = addslashes($format->getpath($data, array('#',$field,0,'#'), $qo->$field));
        }

        // run through the answers
        $answers = $data['#']['answer'];
        $a_count = 0;
        $extraasnwersfields = $this->extra_answer_fields();
        if (is_array($extraasnwersfields)) {
            //TODO import the answers, with any extra data.
        } else {
            foreach ($answers as $answer) {
                $ans = $format->import_answer($answer);
                $qo->answer[$a_count] = $ans->answer;
                $qo->fraction[$a_count] = $ans->fraction;
                $qo->feedback[$a_count] = $ans->feedback;
                ++$a_count;
            }
        }
        return $qo;
    }

    /*
     * Export question to the Moodle XML format
     *
     * Export question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     */
    public function export_to_xml($question, $format, $extra=null) {
        $extraquestionfields = $this->extra_question_fields();
        if (!is_array($extraquestionfields)) {
            return false;
        }

        //omit table name
        array_shift($extraquestionfields);
        $expout='';
        foreach ($extraquestionfields as $field) {
            $exportedvalue = $question->options->$field;
            if (!empty($exportedvalue) && htmlspecialchars($exportedvalue) != $exportedvalue) {
                $exportedvalue = '<![CDATA[' . $exportedvalue . ']]>';
            }
            $expout .= "    <$field>{$exportedvalue}</$field>\n";
        }

        $extraasnwersfields = $this->extra_answer_fields();
        if (is_array($extraasnwersfields)) {
            //TODO export answers with any extra data
        } else {
            foreach ($question->options->answers as $answer) {
                $percent = 100 * $answer->fraction;
                $expout .= "    <answer fraction=\"$percent\">\n";
                $expout .= $format->writetext($answer->answer, 3, false);
                $expout .= "      <feedback>\n";
                $expout .= $format->writetext($answer->feedback, 4, false);
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
            }
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

        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $newcategory = question_make_default_categories(array($context));
        $form->category = $newcategory->id . ',1';

        $question = new stdClass();
        $question->courseid = $courseid;
        $question->qtype = $this->qtype;
        return array($form, $question);
    }
}


/**
 * This class is used in the return value from
 * {@link question_type::get_possible_responses()}.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_possible_response {
    /**
     * @var string the classification of this response the student gave to this
     * part of the question. Must match one of the responseclasses returned by
     * {@link question_type::get_possible_responses()}.
     */
    public $responseclass;
    /** @var string the actual response the student gave to this part. */
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
