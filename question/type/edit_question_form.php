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
 * A base class for question editing forms.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * Form definition base class. This defines the common fields that
 * all question types need. Question types should define their own
 * class that inherits from this one, and implements the definition_inner()
 * method.
 *
 * @package questionbank
 * @subpackage questiontypes
 */
class question_edit_form extends moodleform {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_data.
     *
     * @var object
     */
    public $question;
    public $contexts;
    public $category;
    public $categorycontext;

    /** @var object current context */
    public $context;
    /** @var array html editor options */
    public $editoroptions;
    /** @var array options to preapre draft area */
    public $fileoptions;
    /** @var object instance of question type */
    public $instance;

    function question_edit_form($submiturl, $question, $category, $contexts, $formeditable = true){
        global $DB;

        $this->question = $question;

        $this->contexts = $contexts;

        $record = $DB->get_record('question_categories', array('id' => $question->category), 'contextid');
        $this->context = get_context_instance_by_id($record->contextid);

        $this->editoroptions = array('subdirs' => 1,'maxfiles' => EDITOR_UNLIMITED_FILES, 'context' => $this->context);
        $this->fileoptions = array('subdirs' => 1, 'maxfiles' => -1, 'maxbytes' => -1);

        $this->category = $category;
        $this->categorycontext = get_context_instance_by_id($category->contextid);

        if (!empty($question->id)) {
            $question->id = (int) $question->id;
        }

        parent::moodleform($submiturl, null, 'post', '', null, $formeditable);
    }

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the default question type supports.
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    function definition() {
        global $COURSE, $CFG, $DB;

        $qtype = $this->qtype();
        $langfile = "qtype_$qtype";

        $mform =& $this->_form;

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        if (!isset($this->question->id)){
            // Adding question
            $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                    array('contexts' => $this->contexts->having_cap('moodle/question:add')));
        } elseif (!($this->question->formoptions->canmove || $this->question->formoptions->cansaveasnew)){
            // Editing question with no permission to move from category.
            $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                    array('contexts' => array($this->categorycontext)));
        } elseif ($this->question->formoptions->movecontext){
            // Moving question to another context.
            $mform->addElement('questioncategory', 'categorymoveto', get_string('category', 'quiz'),
                    array('contexts' => $this->contexts->having_cap('moodle/question:add')));

        } else {
            // Editing question with permission to move from category or save as new q
            $currentgrp = array();
            $currentgrp[0] =& $mform->createElement('questioncategory', 'category', get_string('categorycurrent', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew){
                //not move only form
                $currentgrp[1] =& $mform->createElement('checkbox', 'usecurrentcat', '', get_string('categorycurrentuse', 'question'));
                $mform->setDefault('usecurrentcat', 1);
            }
            $currentgrp[0]->freeze();
            $currentgrp[0]->setPersistantFreeze(false);
            $mform->addGroup($currentgrp, 'currentgrp', get_string('categorycurrent', 'question'), null, false);

            $mform->addElement('questioncategory', 'categorymoveto', get_string('categorymoveto', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew){
                //not move only form
                $mform->disabledIf('categorymoveto', 'usecurrentcat', 'checked');
            }
        }

        $mform->addElement('text', 'name', get_string('questionname', 'quiz'), array('size' => 50));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'quiz'),
                array('rows' => 15), $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);

        $mform->addElement('text', 'defaultgrade', get_string('defaultgrade', 'quiz'),
                array('size' => 3));
        $mform->setType('defaultgrade', PARAM_INT);
        $mform->setDefault('defaultgrade', 1);
        $mform->addRule('defaultgrade', null, 'required', null, 'client');

        $mform->addElement('text', 'penalty', get_string('penaltyfactor', 'question'),
                array('size' => 3));
        $mform->setType('penalty', PARAM_NUMBER);
        $mform->addRule('penalty', null, 'required', null, 'client');
        $mform->addHelpButton('penalty', 'penaltyfactor', 'question');
        $mform->setDefault('penalty', 0.1);

        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'quiz'),
                array('rows' => 10), $this->editoroptions);
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'quiz');

        // Any questiontype specific fields.
        $this->definition_inner($mform);

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagsheader', get_string('tags'));
            $mform->addElement('tags', 'tags', get_string('tags'));
        }

        if (!empty($this->question->id)){
            $mform->addElement('header', 'createdmodifiedheader', get_string('createdmodifiedheader', 'question'));
            $a = new stdClass();
            if (!empty($this->question->createdby)){
                $a->time = userdate($this->question->timecreated);
                $a->user = fullname($DB->get_record('user', array('id' => $this->question->createdby)));
            } else {
                $a->time = get_string('unknown', 'question');
                $a->user = get_string('unknown', 'question');
            }
            $mform->addElement('static', 'created', get_string('created', 'question'), get_string('byandon', 'question', $a));
            if (!empty($this->question->modifiedby)){
                $a = new stdClass();
                $a->time = userdate($this->question->timemodified);
                $a->user = fullname($DB->get_record('user', array('id' => $this->question->modifiedby)));
                $mform->addElement('static', 'modified', get_string('modified', 'question'), get_string('byandon', 'question', $a));
            }
        }

        // Standard fields at the end of the form.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $mform->addElement('hidden', 'inpopup');
        $mform->setType('inpopup', PARAM_INT);

        $mform->addElement('hidden', 'versioning');
        $mform->setType('versioning', PARAM_BOOL);

        $mform->addElement('hidden', 'movecontext');
        $mform->setType('movecontext', PARAM_BOOL);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setDefault('returnurl', 0);

        $mform->addElement('hidden', 'appendqnumstring');
        $mform->setType('appendqnumstring', PARAM_ALPHA);
        $mform->setDefault('appendqnumstring', 0);

        $buttonarray = array();
        if (!empty($this->question->id)){
            //editing / moving question
            if ($this->question->formoptions->movecontext){
                $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('moveq', 'question'));
            } elseif ($this->question->formoptions->canedit || $this->question->formoptions->canmove ||$this->question->formoptions->movecontext){
                $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
            }
            if ($this->question->formoptions->cansaveasnew){
                $buttonarray[] = &$mform->createElement('submit', 'makecopy', get_string('makecopy', 'quiz'));
            }
            $buttonarray[] = &$mform->createElement('cancel');
        } else {
            // adding new question
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
            $buttonarray[] = &$mform->createElement('cancel');
        }
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        if ($this->question->formoptions->movecontext) {
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar'));
        } else if ((!empty($this->question->id)) && (!($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew))){
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar', 'currentgrp'));
        }
    }

    function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);
        if (empty($fromform->makecopy) && isset($this->question->id)
                && ($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew)
                && empty($fromform->usecurrentcat) && !$this->question->formoptions->canmove) {
            $errors['currentgrp'] = get_string('nopermissionmove', 'question');
        }
        return $errors;
    }

    /**
     * Add any question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        // By default, do nothing.
    }

    /**
     * Get the list of form elements to repeat, one for each answer.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options field holding an array of answers
     * @return array of form fields.
     */
    function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', $label);
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'), array('size' => 80));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('editor', 'feedback', get_string('feedback', 'quiz'),
                                array('rows' => 5), $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }

    /**
     * Add a set of form fields, obtained from get_per_answer_fields, to the form,
     * one for each existing answer, with some blanks for some new ones.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $minoptions the minimum number of answer blanks to display. Default QUESTION_NUMANS_START.
     * @param $addoptions the number of answer blanks to add. Default QUESTION_NUMANS_ADD.
     */
    function add_per_answer_fields(&$mform, $label, $gradeoptions, $minoptions = QUESTION_NUMANS_START, $addoptions = QUESTION_NUMANS_ADD) {
        $answersoption = '';
        $repeatedoptions = array();
        $repeated = $this->get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);

        if (isset($this->question->options)){
            $countanswers = count($this->question->options->$answersoption);
        } else {
            $countanswers = 0;
        }
        if ($this->question->formoptions->repeatelements){
            $repeatsatstart = max($minoptions, $countanswers + $addoptions);
        } else {
            $repeatsatstart = $countanswers;
        }

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', $addoptions, get_string('addmorechoiceblanks', 'qtype_multichoice'));
    }

    function set_data($question) {
        global $QTYPES;
        // prepare question text
        $draftid = file_get_submitted_draft_itemid('questiontext');

        if (!empty($question->questiontext)) {
            $questiontext = $question->questiontext;
        } else {
            $questiontext = '';
        }
        $questiontext = file_prepare_draft_area($draftid, $this->context->id, 'question', 'questiontext', empty($question->id)?null:(int)$question->id, $this->fileoptions, $questiontext);

        $question->questiontext = array();
        $question->questiontext['text'] = $questiontext;
        $question->questiontext['format'] = empty($question->questiontextformat) ? editors_get_preferred_format() : $question->questiontextformat;
        $question->questiontext['itemid'] = $draftid;

        // prepare general feedback
        $draftid = file_get_submitted_draft_itemid('generalfeedback');

        if (empty($question->generalfeedback)) {
            $question->generalfeedback = '';
        }

        $feedback = file_prepare_draft_area($draftid, $this->context->id, 'question', 'generalfeedback', empty($question->id)?null:(int)$question->id, $this->fileoptions, $question->generalfeedback);
        $question->generalfeedback = array();
        $question->generalfeedback['text'] = $feedback;
        $question->generalfeedback['format'] = empty($question->generalfeedbackformat) ? editors_get_preferred_format() : $question->generalfeedbackformat;
        $question->generalfeedback['itemid'] = $draftid;

        // Remove unnecessary trailing 0s form grade fields.
        if (isset($question->defaultgrade)) {
            $question->defaultgrade = 0 + $question->defaultgrade;
        }
        if (isset($question->penalty)) {
            $question->penalty = 0 + $question->penalty;
        }

        // Set any options.
        $extra_question_fields = $QTYPES[$question->qtype]->extra_question_fields();
        if (is_array($extra_question_fields) && !empty($question->options)) {
            array_shift($extra_question_fields);
            foreach ($extra_question_fields as $field) {
                if (isset($question->options->$field)) {
                    $question->$field = $question->options->$field;
                }
            }
        }
        // subclass adds data_preprocessing code here
        $question = $this->data_preprocessing($question);
        parent::set_data($question);
    }

    /**
     * Any preprocessing needed for the settings form for the question type
     *
     * @param array $question - array to fill in with the default values
     */
    function data_preprocessing($question) {
        return $question;
    }

    /**
     * Override this in the subclass to question type name.
     * @return the question type name, should be the same as the name() method in the question type class.
     */
    function qtype() {
        return '';
    }
}
