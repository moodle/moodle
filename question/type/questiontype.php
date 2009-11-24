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

require_once($CFG->libdir . '/questionlib.php');

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
class default_questiontype {

    /**
     * Name of the question type
     *
     * The name returned should coincide with the name of the directory
     * in which this questiontype is located
     *
     * @return string the name of this question type.
     */
    function name() {
        return 'default';
    }

    /**
     * The name this question should appear as in the create new question
     * dropdown.
     *
     * @return mixed the desired string, or false to hide this question type in the menu.
     */
    function menu_name() {
        $name = $this->name();
        $menu_name = get_string($name, 'qtype_' . $name);
        if ($menu_name[0] == '[') {
            // Legacy behavior, if the string was not in the proper qtype_name
            // language file, look it up in the quiz one.
            $menu_name = get_string($name, 'quiz');
        }
        return $menu_name;
    }

    /**
     * @return boolean true if this question type sometimes requires manual grading.
     */
    function is_manual_graded() {
        return false;
    }

    /**
     * @param object $question a question of this type.
     * @param string $otherquestionsinuse comma-separate list of other question ids in this attempt.
     * @return boolean true if a particular instance of this question requires manual grading.
     */
    function is_question_manual_graded($question, $otherquestionsinuse) {
        return $this->is_manual_graded();
    }

    /**
     * @return boolean true if this question type can be used by the random question type.
     */
    function is_usable_by_random() {
        return true;
    }

    /**
     * @return whether the question_answers.answer field needs to have
     * restore_decode_content_links_worker called on it.
     */
    function has_html_answers() {
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
    function extra_question_fields() {
        return null;
    }

    /**
        * If you use extra_question_fields, overload this function to return question id field name
        *  in case you table use another name for this column
        */
    function questionid_column_name() {
        return 'questionid';
    }

    /**
     * If your question type has a table that extends the question_answers table,
     * make this method return an array wherer the first element is the table name,
     * and the subsequent entries are the column names (apart from id and answerid).
     *
     * @return mixed array as above, or null to tell the base class to do nothing.
     */
     function extra_answer_fields() {
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
    function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
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
    function plugin_dir() {
        global $CFG;
        return $CFG->dirroot . '/question/type/' . $this->name();
    }

    /**
     * @return string the URL of the folder this plugin's files live in.
     */
    function plugin_baseurl() {
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
    function display_question_editing_page(&$mform, $question, $wizardnow){
        list($heading, $langmodule) = $this->get_heading(empty($question->id));
        print_heading_with_help($heading, $this->name(), $langmodule);
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
     * @return array a string heading and the langmodule in which it was found.
     */
    function get_heading($adding = false){
        $name = $this->name();
        $langmodule = 'qtype_' . $name;
        if (!$adding){
            $strtoget = 'editing' . $name;
        } else {
            $strtoget = 'adding' . $name;
        }
        $strheading = get_string($strtoget, $langmodule);
        if ($strheading[0] == '[') {
            // Legacy behavior, if the string was not in the proper qtype_name
            // language file, look it up in the quiz one.
            $langmodule = 'quiz';
            $strheading = get_string($strtoget, $langmodule);
        }
        return array($strheading, $langmodule);
    }

    /**
     *
     *
     * @param $question
     */
    function set_default_options(&$question) {
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
    function save_question($question, $form, $course) {
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

        if (isset($form->defaultgrade)) {
            $question->defaultgrade = $form->defaultgrade;
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
    *                          it is not a standard question object.
    */
    function save_question_options($question) {
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

    /**
    * Changes all states for the given attempts over to a new question
    *
    * This is used by the versioning code if the teacher requests that a question
    * gets replaced by the new version. In order for the attempts to be regraded
    * properly all data in the states referring to the old question need to be
    * changed to refer to the new version instead. In particular for question types
    * that use the answers table the answers belonging to the old question have to
    * be changed to those belonging to the new version.
    *
    * @param integer $oldquestionid  The id of the old question
    * @param object $newquestion    The new question
    * @param array  $attempts       An array of all attempt objects in whose states
    *                               replacement should take place
    */
    function replace_question_in_attempts($oldquestionid, $newquestion, $attemtps) {
        echo 'Not yet implemented';
        return;
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
    function get_question_options(&$question) {
        global $CFG;

        if (!isset($question->options)) {
            $question->options = new object;
        }

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

        return true;
    }

    /**
    * Deletes states from the question-type specific tables
    *
    * @param string $stateslist  Comma separated list of state ids to be deleted
    */
    function delete_states($stateslist) {
        /// The default question type does not have any tables of its own
        // therefore there is nothing to delete

        return true;
    }

    /**
    * Deletes a question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
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
    function actual_number_of_questions($question) {
        // By default, each question is given one number
        return 1;
    }

    /**
    * Creates empty session and response information for the question
    *
    * This function is called to start a question session. Empty question type
    * specific session data (if any) and empty response data will be added to the
    * state object. Session data is any data which must persist throughout the
    * attempt possibly with updates as the user interacts with the
    * question. This function does NOT create new entries in the database for
    * the session; a call to the {@link save_session_and_responses} member will
    * occur to do this.
    * @return bool            Indicates success or failure.
    * @param object $question The question for which the session is to be
    *                         created. Question type specific information is
    *                         included.
    * @param object $state    The state to create the session for. Note that
    *                         this will not have been saved in the database so
    *                         there will be no id. This object will be updated
    *                         to include the question type specific information
    *                         (it is passed by reference). In particular, empty
    *                         responses will be created in the ->responses
    *                         field.
    * @param object $cmoptions
    * @param object $attempt  The attempt for which the session is to be
    *                         started. Questions may wish to initialize the
    *                         session in different ways depending on the user id
    *                         or time available for the attempt.
    */
    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // The default implementation should work for the legacy question types.
        // Most question types with only a single form field for the student's response
        // will use the empty string '' as the index for that one response. This will
        // automatically be stored in and restored from the answer field in the
        // question_states table.
        $state->responses = array(
                '' => '',
        );
        return true;
    }

    /**
    * Restores the session data and most recent responses for the given state
    *
    * This function loads any session data associated with the question
    * session in the given state from the database into the state object.
    * In particular it loads the responses that have been saved for the given
    * state into the ->responses member of the state object.
    *
    * Question types with only a single form field for the student's response
    * will not need not restore the responses; the value of the answer
    * field in the question_states table is restored to ->responses['']
    * before this function is called. Question types with more response fields
    * should override this method and set the ->responses field to an
    * associative array of responses.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including any
    *                         question type specific information.
    * @param object $state    The saved state to load the session for. This
    *                         object should be updated to include the question
    *                         type specific session information and responses
    *                         (it is passed by reference).
    */
    function restore_session_and_responses(&$question, &$state) {
        // The default implementation does nothing (successfully)
        return true;
    }

    /**
    * Saves the session data and responses for the given question and state
    *
    * This function saves the question type specific session data from the
    * state object to the database. In particular for most question types it saves the
    * responses from the ->responses member of the state object. The question type
    * non-specific data for the state has already been saved in the question_states
    * table and the state object contains the corresponding id and
    * sequence number which may be used to index a question type specific table.
    *
    * Question types with only a single form field for the student's response
    * which is contained in ->responses[''] will not have to save this response,
    * it will already have been saved to the answer field of the question_states table.
    * Question types with more response fields should override this method to convert
    * the data the ->responses array into a single string field, and save it in the
    * database. The implementation in the multichoice question type is a good model to follow.
    * http://cvs.moodle.org/contrib/plugins/question/type/opaque/questiontype.php?view=markup
    * has a solution that is probably quite generally applicable.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including
    *                         the question type specific information.
    * @param object $state    The state for which the question type specific
    *                         data and responses should be saved.
    */
    function save_session_and_responses(&$question, &$state) {
        // The default implementation does nothing (successfully)
        return true;
    }

    /**
    * Returns an array of values which will give full marks if graded as
    * the $state->responses field
    *
    * The correct answer to the question in the given state, or an example of
    * a correct answer if there are many, is returned. This is used by some question
    * types in the {@link grade_responses()} function but it is also used by the
    * question preview screen to fill in correct responses.
    * @return mixed           A response array giving the responses corresponding
    *                         to the (or a) correct answer to the question. If there is
    *                         no correct answer that scores 100% then null is returned.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state of the question, for which a correct answer is
    *                         needed. Question type specific information is included.
    */
    function get_correct_responses(&$question, &$state) {
        /* The default implementation returns the response for the first answer
        that gives full marks. */
        if ($question->options->answers) {
            foreach ($question->options->answers as $answer) {
                if (((int) $answer->fraction) === 1) {
                    return array('' => addslashes($answer->answer));
                }
            }
        }
        return null;
    }

    /**
    * Return an array of values with the texts for all possible responses stored
    * for the question
    *
    * All answers are found and their text values isolated
    * @return object          A mixed object
    *             ->id        question id. Needed to manage random questions:
    *                         it's the id of the actual question presented to user in a given attempt
    *             ->responses An array of values giving the responses corresponding
    *                         to all answers to the question. Answer ids are used as keys.
    *                         The text and partial credit are the object components
    * @param object $question The question for which the answers are to
    *                         be retrieved. Question type specific information is
    *                         available.
    */
    // ULPGC ecastro
    function get_all_responses(&$question, &$state) {
        if (isset($question->options->answers) && is_array($question->options->answers)) {
            $answers = array();
            foreach ($question->options->answers as $aid=>$answer) {
                $r = new stdClass;
                $r->answer = $answer->answer;
                $r->credit = $answer->fraction;
                $answers[$aid] = $r;
            }
            $result = new stdClass;
            $result->id = $question->id;
            $result->responses = $answers;
            return $result;
        } else {
            return null;
        }
    }

    /**
    * Return the actual response to the question in a given state
    * for the question.
    *
    * @return mixed           An array containing the response or reponses (multiple answer, match)
    *                         given by the user in a particular attempt.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed. Question
    *                         type specific information is included.
    */
    // ULPGC ecastro
    function get_actual_response($question, $state) {
       if (!empty($state->responses)) {
           $responses[] = stripslashes($state->responses['']);
       } else {
           $responses[] = '';
       }
       return $responses;
    }

    // ULPGC ecastro
    function get_fractional_grade(&$question, &$state) {
        $maxgrade = $question->maxgrade;
        $grade = $state->grade;
        if ($maxgrade) {
            return (float)($grade/$maxgrade);
        } else {
            return (float)$grade;
        }
    }


    /**
    * Checks if the response given is correct and returns the id
    *
    * @return int             The ide number for the stored answer that matches the response
    *                         given by the user in a particular attempt.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed. Question
    *                         type specific information is included.
    */
    // ULPGC ecastro
    function check_response(&$question, &$state){
        return false;
    }

    // Used by the following function, so that it only returns results once per quiz page.
    var $htmlheadalreadydone = false; // no private in 1.9 yet!
    /**
     * If this question type requires extra CSS or JavaScript to function,
     * then this method will return an array of <link ...> tags that reference
     * those stylesheets. This function will also call require_js()
     * from ajaxlib.php, to get any necessary JavaScript linked in too.
     *
     * Remember that there may be more than one question of this type on a page.
     * try to avoid including JS and CSS more than once.
     *
     * The two parameters match the first two parameters of print_question.
     *
     * @param object $question The question object.
     * @param object $state    The state object.
     *
     * @return an array of bits of HTML to add to the head of pages where
     * this question is print_question-ed in the body. The array should use
     * integer array keys, which have no significance.
     */
    function get_html_head_contributions(&$question, &$state) {
        // We only do this once for this question type, no matter how often this
        // method is called on one page.
        if ($this->htmlheadalreadydone) {
            return array();
        }
        $this->htmlheadalreadydone = true;

        // By default, we link to any of the files styles.css, styles.php,
        // script.js or script.php that exist in the plugin folder.
        // Core question types should not use this mechanism. Their styles
        // should be included in the standard theme.
        return $this->find_standard_scripts_and_css();
    }

    /**
     * Like @see{get_html_head_contributions}, but this method is for CSS and
     * JavaScript required on the question editing page question/question.php.
     *
     * @return an array of bits of HTML to add to the head of pages where
     * this question is print_question-ed in the body. The array should use
     * integer array keys, which have no significance.
     */
    function get_editing_head_contributions() {
        // By default, we link to any of the files styles.css, styles.php,
        // script.js or script.php that exist in the plugin folder.
        // Core question types should not use this mechanism. Their styles
        // should be included in the standard theme.
        return $this->find_standard_scripts_and_css();
    }

    /**
     * Utility method used by @see{get_html_head_contributions} and
     * @see{get_editing_head_contributions}. This looks for any of the files
     * styles.css, styles.php, script.js or script.php that exist in the plugin
     * folder and ensures they get included.
     *
     * @return array as required by get_html_head_contributions or get_editing_head_contributions.
     */
    function find_standard_scripts_and_css() {
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
     * Prints the question including the number, grading details, content,
     * feedback and interactions
     *
     * This function prints the question including the question number,
     * grading details, content for the question, any feedback for the previously
     * submitted responses and the interactions. The default implementation calls
     * various other methods to print each of these parts and most question types
     * will just override those methods.
     * @param object $question The question to be rendered. Question type
     *                         specific information is included. The
     *                         maximum possible grade is in ->maxgrade. The name
     *                         prefix for any named elements is in ->name_prefix.
     * @param object $state    The state to render the question in. The grading
     *                         information is in ->grade, ->raw_grade and
     *                         ->penalty. The current responses are in
     *                         ->responses. This is an associative array (or the
     *                         empty string or null in the case of no responses
     *                         submitted). The last graded state is in
     *                         ->last_graded (hence the most recently graded
     *                         responses are in ->last_graded->responses). The
     *                         question type specific information is also
     *                         included.
     * @param integer $number  The number for this question.
     * @param object $cmoptions
     * @param object $options  An object describing the rendering options.
     */
    function print_question(&$question, &$state, $number, $cmoptions, $options) {
        /* The default implementation should work for most question types
        provided the member functions it calls are overridden where required.
        The layout is determined by the template question.html */

        global $CFG;
        $isgraded = question_state_is_graded($state->last_graded);

        // For editing teachers print a link to an editing popup window
        $editlink = $this->get_question_edit_link($question, $cmoptions, $options);

        $generalfeedback = '';
        if ($isgraded && $options->generalfeedback) {
            $generalfeedback = $this->format_text($question->generalfeedback,
                    $question->questiontextformat, $cmoptions);
        }

        $grade = '';
        if ($question->maxgrade and $options->scores) {
            if ($cmoptions->optionflags & QUESTION_ADAPTIVE) {
                $grade = !$isgraded ? '--/' : round($state->last_graded->grade, $cmoptions->decimalpoints).'/';
            }
            $grade .= $question->maxgrade;
        }

        $formatoptions = new stdClass;
        $formatoptions->para = false;
        $comment = format_text(stripslashes($state->manualcomment), FORMAT_HTML,
                $formatoptions, $cmoptions->course);
        $commentlink = '';

        if (!empty($options->questioncommentlink)) {
            $strcomment = get_string('commentorgrade', 'quiz');
            $question_to_comment = isset($question->randomquestionid) ? $question->randomquestionid : $question->id;
            $commentlink = link_to_popup_window($options->questioncommentlink .
                    '?attempt=' . $state->attempt . '&amp;question=' . $question_to_comment,
                    'commentquestion', $strcomment, 470, 740, $strcomment, 'none', true);
            $commentlink = '<div class="commentlink">'. $commentlink .'</div>';
        }

        $history = $this->history($question, $state, $number, $cmoptions, $options);

        include "$CFG->dirroot/question/type/question.html";
    }

    /**
     * Get a link to an edit icon for this question, if the current user is allowed
     * to edit it.
     *
     * @param object $question the question object.
     * @param object $cmoptions the options from the module. If $cmoptions->thispageurl is set
     *      then the link will be to edit the question in this browser window, then return to
     *      $cmoptions->thispageurl. Otherwise the link will be to edit in a popup. $cmoptions->cmid should also be set.
     * @return string the HTML of the link, or nothing it the currenty user is not allowed to edit.
     */
    function get_question_edit_link($question, $cmoptions, $options) {
        global $CFG;

    /// Is this user allowed to edit this question?
        if (!empty($options->noeditlink) || !question_has_capability_on($question, 'edit')) {
            return '';
        }

    /// Work out the right URL.
        $linkurl = '/question/question.php?id=' . $question->id;
        if (!empty($cmoptions->cmid)) {
            $linkurl .= '&amp;cmid=' . $cmoptions->cmid;
        } else if (!empty($cmoptions->course)) {
            $linkurl .= '&amp;courseid=' . $cmoptions->course;
        } else {
            error('Need to provide courseid or cmid to get_question_edit_link.');
        }

    /// Work out the contents of the link.
        $stredit = get_string('edit');
        $linktext = '<img src="' . $CFG->pixpath . '/t/edit.gif" alt="' . $stredit . '" />';

        if (!empty($cmoptions->thispageurl)) {
        /// The module allow editing in the same window, print an ordinary link.
            return '<a href="' . $CFG->wwwroot . $linkurl . '&amp;returnurl=' .
                    urlencode($cmoptions->thispageurl . '#q' . $question->id) .
                    '" title="' . $stredit . '">' . $linktext . '</a>';
        } else {
        /// We have to edit in a pop-up.
            return link_to_popup_window($linkurl . '&amp;inpopup=1', 'editquestion',
                    $linktext, false, false, $stredit, '', true);
        }
    }

    /*
     * Print history of responses
     *
     * Used by print_question()
     */
    function history($question, $state, $number, $cmoptions, $options) {
        if (empty($options->history)) {
            return '';
        }

        if (isset($question->randomquestionid)) {
            $qid = $question->randomquestionid;
            $randomprefix = 'random' . $question->id . '-';
        } else {
            $qid = $question->id;
            $randomprefix = '';
        }
        if ($options->history == 'all') {
            $eventtest = 'event > 0';
        } else {
            $eventtest = 'event IN (' . QUESTION_EVENTS_GRADED . ')';
        }
        $states = get_records_select('question_states',
                'attempt = ' . $state->attempt . ' AND question = ' . $qid .
                ' AND ' . $eventtest, 'seq_number ASC');
        if (count($states) <= 1) {
            return '';
        }

        $strreviewquestion = get_string('reviewresponse', 'quiz');
        $table = new stdClass;
        $table->width = '100%';
        $table->head  = array (
            get_string('numberabbr', 'quiz'),
            get_string('action', 'quiz'),
            get_string('response', 'quiz'),
            get_string('time'),
        );
        if ($options->scores) {
            $table->head[] = get_string('score', 'quiz');
            $table->head[] = get_string('grade', 'quiz');
        }

        foreach ($states as $st) {
            if ($randomprefix && strpos($st->answer, $randomprefix) === 0) {
                $st->answer = substr($st->answer, strlen($randomprefix));
            }
            $st->responses[''] = $st->answer;
            $this->restore_session_and_responses($question, $st);

            if ($state->id == $st->id) {
                $link = '<b>' . $st->seq_number . '</b>';
            } else if (isset($options->questionreviewlink)) {
                $link = link_to_popup_window($options->questionreviewlink.'?state='.$st->id.'&amp;number='.$number,
                        'reviewquestion', $st->seq_number, 450, 650, $strreviewquestion, 'none', true);
            } else {
                $link = $st->seq_number;
            }

            if ($state->id == $st->id) {
                $b = '<b>';
                $be = '</b>';
            } else {
                $b = '';
                $be = '';
            }

            $data = array (
                $link,
                $b.get_string('event'.$st->event, 'quiz').$be,
                $b.$this->response_summary($question, $st).$be,
                $b.userdate($st->timestamp, get_string('timestr', 'quiz')).$be,
            );
            if ($options->scores) {
                $data[] = $b.round($st->raw_grade, $cmoptions->decimalpoints).$be;
                $data[] = $b.round($st->grade, $cmoptions->decimalpoints).$be;
            }
            $table->data[] = $data;
        }
        return print_table($table, true);
    }


    /**
    * Prints the score obtained and maximum score available plus any penalty
    * information
    *
    * This function prints a summary of the scoring in the most recently
    * graded state (the question may not have been submitted for marking at
    * the current state). The default implementation should be suitable for most
    * question types.
    * @param object $question The question for which the grading details are
    *                         to be rendered. Question type specific information
    *                         is included. The maximum possible grade is in
    *                         ->maxgrade.
    * @param object $state    The state. In particular the grading information
    *                          is in ->grade, ->raw_grade and ->penalty.
    * @param object $cmoptions
    * @param object $options  An object describing the rendering options.
    */
    function print_question_grading_details(&$question, &$state, $cmoptions, $options) {
        /* The default implementation prints the number of marks if no attempt
        has been made. Otherwise it displays the grade obtained out of the
        maximum grade available and a warning if a penalty was applied for the
        attempt and displays the overall grade obtained counting all previous
        responses (and penalties) */

        if (QUESTION_EVENTDUPLICATE == $state->event) {
            echo ' ';
            print_string('duplicateresponse', 'quiz');
        }
        if (!empty($question->maxgrade) && $options->scores) {
            if (question_state_is_graded($state->last_graded)) {
                // Display the grading details from the last graded state
                $grade = new stdClass;
                $grade->cur = round($state->last_graded->grade, $cmoptions->decimalpoints);
                $grade->max = $question->maxgrade;
                $grade->raw = round($state->last_graded->raw_grade, $cmoptions->decimalpoints);

                // let student know wether the answer was correct
                echo '<div class="correctness ';
                if ($state->last_graded->raw_grade >= $question->maxgrade/1.01) { // We divide by 1.01 so that rounding errors dont matter.
                    echo ' correct">';
                    print_string('correct', 'quiz');
                } else if ($state->last_graded->raw_grade > 0) {
                    echo ' partiallycorrect">';
                    print_string('partiallycorrect', 'quiz');
                } else {
                    echo ' incorrect">';
                    print_string('incorrect', 'quiz');
                }
                echo '</div>';

                echo '<div class="gradingdetails">';
                // print grade for this submission
                print_string('gradingdetails', 'quiz', $grade);
                if ($cmoptions->penaltyscheme) {
                    // print details of grade adjustment due to penalties
                    if ($state->last_graded->raw_grade > $state->last_graded->grade){
                        echo ' ';
                        print_string('gradingdetailsadjustment', 'quiz', $grade);
                    }
                    // print info about new penalty
                    // penalty is relevant only if the answer is not correct and further attempts are possible
                    if (($state->last_graded->raw_grade < $question->maxgrade / 1.01)
                                and (QUESTION_EVENTCLOSEANDGRADE != $state->event)) {

                        if ('' !== $state->last_graded->penalty && ((float)$state->last_graded->penalty) > 0.0) {
                            // A penalty was applied so display it
                            echo ' ';
                            print_string('gradingdetailspenalty', 'quiz', $state->last_graded->penalty);
                        } else {
                            /* No penalty was applied even though the answer was
                            not correct (eg. a syntax error) so tell the student
                            that they were not penalised for the attempt */
                            echo ' ';
                            print_string('gradingdetailszeropenalty', 'quiz');
                        }
                    }
                }
                echo '</div>';
            }
        }
    }

    /**
    * Prints the main content of the question including any interactions
    *
    * This function prints the main content of the question including the
    * interactions for the question in the state given. The last graded responses
    * are printed or indicated and the current responses are selected or filled in.
    * Any names (eg. for any form elements) are prefixed with $question->name_prefix.
    * This method is called from the print_question method.
    * @param object $question The question to be rendered. Question type
    *                         specific information is included. The name
    *                         prefix for any named elements is in ->name_prefix.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The current responses are in
    *                         ->responses. This is an associative array (or the
    *                         empty string or null in the case of no responses
    *                         submitted). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included.
    *                         The state is passed by reference because some adaptive
    *                         questions may want to update it during rendering
    * @param object $cmoptions
    * @param object $options  An object describing the rendering options.
    */
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        /* This default implementation prints an error and must be overridden
        by all question type implementations, unless the default implementation
        of print_question has been overridden. */

        notify('Error: Question formulation and input controls has not'
               .'  been implemented for question type '.$this->name());
    }

    /**
    * Prints the submit button(s) for the question in the given state
    *
    * This function prints the submit button(s) for the question in the
    * given state. The name of any button created will be prefixed with the
    * unique prefix for the question in $question->name_prefix. The suffix
    * 'submit' is reserved for the single question submit button and the suffix
    * 'validate' is reserved for the single question validate button (for
    * question types which support it). Other suffixes will result in a response
    * of that name in $state->responses which the printing and grading methods
    * can then use.
    * @param object $question The question for which the submit button(s) are to
    *                         be rendered. Question type specific information is
    *                         included. The name prefix for any
    *                         named elements is in ->name_prefix.
    * @param object $state    The state to render the buttons for. The
    *                         question type specific information is also
    *                         included.
    * @param object $cmoptions
    * @param object $options  An object describing the rendering options.
    */
    function print_question_submit_buttons(&$question, &$state, $cmoptions, $options) {
        /* The default implementation should be suitable for most question
        types. It prints a mark button in the case where individual marking is
        allowed. */

        if (($cmoptions->optionflags & QUESTION_ADAPTIVE) and !$options->readonly) {
            echo '<input type="submit" name="', $question->name_prefix, 'submit" value="',
                    get_string('mark', 'quiz'), '" class="submit btn" onclick="',
                    "form.action = form.action + '#q", $question->id, "'; return true;", '" />';
        }
    }

    /**
    * Return a summary of the student response
    *
    * This function returns a short string of no more than a given length that
    * summarizes the student's response in the given $state. This is used for
    * example in the response history table. This string should already be,
    * for output.
    * @return string         The summary of the student response
    * @param object $question
    * @param object $state   The state whose responses are to be summarized
    * @param int $length     The maximum length of the returned string
    */
    function response_summary($question, $state, $length=80) {
        // This should almost certainly be overridden
        $responses = $this->get_actual_response($question, $state);
        if (empty($responses) || !is_array($responses)) {
            $responses = array();
        }
        if (is_array($responses)) {
            $responses = implode(',', array_map('s', $responses));
        }
        return shorten_text($responses, $length);
    }

    /**
    * Renders the question for printing and returns the LaTeX source produced
    *
    * This function should render the question suitable for a printed problem
    * or solution sheet in LaTeX and return the rendered output.
    * @return string          The LaTeX output.
    * @param object $question The question to be rendered. Question type
    *                         specific information is included.
    * @param object $state    The state to render the question in. The
    *                         question type specific information is also
    *                         included.
    * @param object $cmoptions
    * @param string $type     Indicates if the question or the solution is to be
    *                         rendered with the values 'question' and
    *                         'solution'.
    */
    function get_texsource(&$question, &$state, $cmoptions, $type) {
        // The default implementation simply returns a string stating that
        // the question is only available online.

        return get_string('onlineonly', 'texsheet');
    }

    /**
    * Compares two question states for equivalence of the student's responses
    *
    * The responses for the two states must be examined to see if they represent
    * equivalent answers to the question by the student. This method will be
    * invoked for each of the previous states of the question before grading
    * occurs. If the student is found to have already attempted the question
    * with equivalent responses then the attempt at the question is ignored;
    * grading does not occur and the state does not change. Thus they are not
    * penalized for this case.
    * @return boolean
    * @param object $question  The question for which the states are to be
    *                          compared. Question type specific information is
    *                          included.
    * @param object $state     The state of the question. The responses are in
    *                          ->responses. This is the only field of $state
    *                          that it is safe to use.
    * @param object $teststate The state whose responses are to be
    *                          compared. The state will be of the same age or
    *                          older than $state. If possible, the method should
    *                          only use the field $teststate->responses, however
    *                          any field that is set up by restore_session_and_responses
    *                          can be used.
    */
    function compare_responses(&$question, $state, $teststate) {
        // The default implementation performs a comparison of the response
        // arrays. The ordering of the arrays does not matter.
        // Question types may wish to override this (eg. to ignore trailing
        // white space or to make "7.0" and "7" compare equal).

        // In php neither == nor === compare arrays the way you want. The following
        // ensures that the arrays have the same keys, with the same values.
        $result = false;
        $diff1 = array_diff_assoc($state->responses, $teststate->responses);
        if (empty($diff1)) {
            $diff2 = array_diff_assoc($teststate->responses, $state->responses);
            $result =  empty($diff2);
        }

        return $result;
    }

    /**
    * Checks whether a response matches a given answer
    *
    * This method only applies to questions that use teacher-defined answers
    *
    * @return boolean
    */
    function test_response(&$question, &$state, $answer) {
        $response = isset($state->responses['']) ? $state->responses[''] : '';
        return ($response == $answer->answer);
    }

    /**
    * Performs response processing and grading
    *
    * This function performs response processing and grading and updates
    * the state accordingly.
    * @return boolean         Indicates success or failure.
    * @param object $question The question to be graded. Question type
    *                         specific information is included.
    * @param object $state    The state of the question to grade. The current
    *                         responses are in ->responses. The last graded state
    *                         is in ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included. The ->raw_grade and ->penalty fields
    *                         must be updated. The method is able to
    *                         close the question session (preventing any further
    *                         attempts at this question) by setting
    *                         $state->event to QUESTION_EVENTCLOSEANDGRADE
    * @param object $cmoptions
    */
    function grade_responses(&$question, &$state, $cmoptions) {
        // The default implementation uses the test_response method to
        // compare what the student entered against each of the possible
        // answers stored in the question, and uses the grade from the
        // first one that matches. It also sets the marks and penalty.
        // This should be good enought for most simple question types.

        $state->raw_grade = 0;
        foreach($question->options->answers as $answer) {
            if($this->test_response($question, $state, $answer)) {
                $state->raw_grade = $answer->fraction;
                break;
            }
        }

        // Make sure we don't assign negative or too high marks.
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Update the penalty.
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }


    /**
    * Includes configuration settings for the question type on the quiz admin
    * page
    *
    * TODO: It makes no sense any longer to do the admin for question types
    * from the quiz admin page. This should be changed.
    * Returns an array of objects describing the options for the question type
    * to be included on the quiz module admin page.
    * Configuration options can be included by setting the following fields in
    * the object:
    * ->name           The name of the option within this question type.
    *                  The full option name will be constructed as
    *                  "quiz_{$this->name()}_$name", the human readable name
    *                  will be displayed with get_string($name, 'quiz').
    * ->code           The code to display the form element, help button, etc.
    *                  i.e. the content for the central table cell. Be sure
    *                  to name the element "quiz_{$this->name()}_$name" and
    *                  set the value to $CFG->{"quiz_{$this->name()}_$name"}.
    * ->help           Name of the string from the quiz module language file
    *                  to be used for the help message in the third column of
    *                  the table. An empty string (or the field not set)
    *                  means to leave the box empty.
    * Links to custom settings pages can be included by setting the following
    * fields in the object:
    * ->name           The name of the link text string.
    *                  get_string($name, 'quiz') will be called.
    * ->link           The filename part of the URL for the link. The full URL
    *                  is contructed as
    *                  "$CFG->wwwroot/question/type/{$this->name()}/$link?sesskey=$sesskey"
    *                  [but with the relavant calls to the s and rawurlencode
    *                  functions] where $sesskey is the sesskey for the user.
    * @return array    Array of objects describing the configuration options to
    *                  be included on the quiz module admin page.
    */
    function get_config_options() {
        // No options by default

        return false;
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
    function finished_edit_wizard(&$form) {
        //In the default case there is only one edit page.
        return true;
    }

    /**
    * Prints a table of course modules in which the question is used
    *
    * TODO: This should be made quiz-independent
    *
    * This function is used near the end of the question edit forms in all question types
    * It prints the table of quizzes in which the question is used
    * containing checkboxes to allow the teacher to replace the old question version
    *
    * @param object $question
    * @param object $course
    * @param integer $cmid optional The id of the course module currently being edited
    */
    function print_replacement_options($question, $course, $cmid='0') {

        // Disable until the versioning code has been fixed
        if (true) {
            return;
        }

        // no need to display replacement options if the question is new
        if(empty($question->id)) {
            return true;
        }

        // get quizzes using the question (using the question_instances table)
        $quizlist = array();
        if(!$instances = get_records('quiz_question_instances', 'question', $question->id)) {
            $instances = array();
        }
        foreach($instances as $instance) {
            $quizlist[$instance->quiz] = $instance->quiz;
        }
        $quizlist = implode(',', $quizlist);
        if(empty($quizlist) or !$quizzes = get_records_list('quiz', 'id', $quizlist)) {
            $quizzes = array();
        }

        // do the printing
        if(count($quizzes) > 0) {
            // print the table
            $strquizname  = get_string('modulename', 'quiz');
            $strdoreplace = get_string('replace', 'quiz');
            $straffectedstudents = get_string('affectedstudents', 'quiz', $course->students);
            echo "<tr valign=\"top\">\n";
            echo "<td align=\"right\"><b>".get_string("replacementoptions", "quiz").":</b></td>\n";
            echo "<td align=\"left\">\n";
            echo "<table cellpadding=\"5\" align=\"left\" class=\"generalbox\" width=\"100%\">\n";
            echo "<tr>\n";
            echo "<th align=\"left\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\" scope=\"col\">$strquizname</th>\n";
            echo "<th align=\"center\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\" scope=\"col\">$strdoreplace</th>\n";
            echo "<th align=\"left\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\" scope=\"col\">$straffectedstudents</th>\n";
            echo "</tr>\n";
            foreach($quizzes as $quiz) {
                // work out whethere it should be checked by default
                $checked = '';
                if((int)$cmid === (int)$quiz->id
                    or empty($quiz->usercount)) {
                    $checked = "checked=\"checked\"";
                }

                // find how many different students have already attempted this quiz
                $students = array();
                if($attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'")) {
                    foreach($attempts as $attempt) {
                        if (record_exists('question_states', 'attempt', $attempt->uniqueid, 'question', $question->id, 'originalquestion', 0)) {
                            $students[$attempt->userid] = 1;
                        }
                    }
                }
                $studentcount = count($students);

                $strstudents = $studentcount === 1 ? $course->student : $course->students;
                echo "<tr>\n";
                echo "<td align=\"left\" class=\"generaltablecell c0\">".format_string($quiz->name)."</td>\n";
                echo "<td align=\"center\" class=\"generaltablecell c0\"><input name=\"q{$quiz->id}replace\" type=\"checkbox\" ".$checked." /></td>\n";
                echo "<td align=\"left\" class=\"generaltablecell c0\">".(($studentcount) ? $studentcount.' '.$strstudents : '-')."</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</td></tr>\n";
    }

    /**
     * Call format_text from weblib.php with the options appropriate to question types.
     *
     * @param string $text the text to format.
     * @param integer $text the type of text. Normally $question->questiontextformat.
     * @param object $cmoptions the context the string is being displayed in. Only $cmoptions->course is used.
     * @return string the formatted text.
     */
    function format_text($text, $textformat, $cmoptions = NULL) {
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        return format_text($text, $textformat, $formatoptions, $cmoptions === NULL ? NULL : $cmoptions->course);
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
    function find_file_links($question, $courseid){
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
    function replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination){
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
    /**
     * @return the best link to pass to print_error.
     * @param $cmoptions as passed in from outside.
     */
    function error_link($cmoptions) {
        global $CFG;
        $cm = get_coursemodule_from_instance('quiz', $cmoptions->id);
        if (!empty($cm->id)) {
            return $CFG->wwwroot . '/mod/quiz/view.php?id=' . $cm->id;
        } else if (!empty($cm->course)) {
            return $CFG->wwwroot . '/course/view.php?id=' . $cm->course;
        } else {
            return '';
        }
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

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
    function restore($old_question_id,$new_question_id,$info,$restore) {

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

    function restore_map($old_question_id,$new_question_id,$info,$restore) {
        // There is nothing to decode
        return true;
    }

    function restore_recode_answer($state, $restore) {
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
    function import_from_xml($data, $question, $format, $extra=null) {
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
            $qo->$field = $format->getpath($data, array('#',$field,0,'#'), $qo->$field);
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
    function export_to_xml($question, $format, $extra=null) {
        $extraquestionfields = $this->extra_question_fields();
        if (!is_array($extraquestionfields)) {
            return false;
        }

        //omit table name
        array_shift($extraquestionfields);
        $expout='';
        foreach ($extraquestionfields as $field) {
            $expout .= "    <$field>{$question->options->$field}</$field>\n";
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
    function generate_test($name, $courseid=null) {
        $form = new stdClass();
        $form->name = $name;
        $form->questiontextformat = 1;
        $form->questiontext = 'test question, generated by script';
        $form->defaultgrade = 1;
        $form->penalty = 0.1;
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
?>
