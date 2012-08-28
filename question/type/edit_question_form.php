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
 * @package    moodlecore
 * @subpackage questiontypes
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');


abstract class question_wizard_form extends moodleform {
    /**
     * Add all the hidden form fields used by question/question.php.
     */
    protected function add_hidden_fields() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'inpopup');
        $mform->setType('inpopup', PARAM_INT);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);

        $mform->addElement('hidden', 'scrollpos');
        $mform->setType('scrollpos', PARAM_INT);

        $mform->addElement('hidden', 'appendqnumstring');
        $mform->setType('appendqnumstring', PARAM_ALPHA);
    }
}

/**
 * Form definition base class. This defines the common fields that
 * all question types need. Question types should define their own
 * class that inherits from this one, and implements the definition_inner()
 * method.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
abstract class question_edit_form extends question_wizard_form {
    const DEFAULT_NUM_HINTS = 2;

    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_data.
     * @var object
     */
    protected $question;

    protected $contexts;
    protected $category;
    protected $categorycontext;

    /** @var object current context */
    public $context;
    /** @var array html editor options */
    public $editoroptions;
    /** @var array options to preapre draft area */
    public $fileoptions;
    /** @var object instance of question type */
    public $instance;

    public function __construct($submiturl, $question, $category, $contexts, $formeditable = true) {
        global $DB;

        $this->question = $question;
        $this->contexts = $contexts;

        $record = $DB->get_record('question_categories',
                array('id' => $question->category), 'contextid');
        $this->context = context::instance_by_id($record->contextid);

        $this->editoroptions = array('subdirs' => 1, 'maxfiles' => EDITOR_UNLIMITED_FILES,
                'context' => $this->context);
        $this->fileoptions = array('subdirs' => 1, 'maxfiles' => -1, 'maxbytes' => -1);

        $this->category = $category;
        $this->categorycontext = context::instance_by_id($category->contextid);

        parent::__construct($submiturl, null, 'post', '', null, $formeditable);
    }

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the default question type supports.
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    protected function definition() {
        global $COURSE, $CFG, $DB;

        $qtype = $this->qtype();
        $langfile = "qtype_$qtype";

        $mform = $this->_form;

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        if (!isset($this->question->id)) {
            if (!empty($this->question->formoptions->mustbeusable)) {
                $contexts = $this->contexts->having_add_and_use();
            } else {
                $contexts = $this->contexts->having_cap('moodle/question:add');
            }

            // Adding question
            $mform->addElement('questioncategory', 'category', get_string('category', 'question'),
                    array('contexts' => $contexts));
        } else if (!($this->question->formoptions->canmove ||
                $this->question->formoptions->cansaveasnew)) {
            // Editing question with no permission to move from category.
            $mform->addElement('questioncategory', 'category', get_string('category', 'question'),
                    array('contexts' => array($this->categorycontext)));
        } else if ($this->question->formoptions->movecontext) {
            // Moving question to another context.
            $mform->addElement('questioncategory', 'categorymoveto',
                    get_string('category', 'question'),
                    array('contexts' => $this->contexts->having_cap('moodle/question:add')));

        } else {
            // Editing question with permission to move from category or save as new q
            $currentgrp = array();
            $currentgrp[0] = $mform->createElement('questioncategory', 'category',
                    get_string('categorycurrent', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit ||
                    $this->question->formoptions->cansaveasnew) {
                //not move only form
                $currentgrp[1] = $mform->createElement('checkbox', 'usecurrentcat', '',
                        get_string('categorycurrentuse', 'question'));
                $mform->setDefault('usecurrentcat', 1);
            }
            $currentgrp[0]->freeze();
            $currentgrp[0]->setPersistantFreeze(false);
            $mform->addGroup($currentgrp, 'currentgrp',
                    get_string('categorycurrent', 'question'), null, false);

            $mform->addElement('questioncategory', 'categorymoveto',
                    get_string('categorymoveto', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit ||
                    $this->question->formoptions->cansaveasnew) {
                //not move only form
                $mform->disabledIf('categorymoveto', 'usecurrentcat', 'checked');
            }
        }

        $mform->addElement('text', 'name', get_string('questionname', 'question'),
                array('size' => 50));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'),
                array('rows' => 15), $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);

        $mform->addElement('text', 'defaultmark', get_string('defaultmark', 'question'),
                array('size' => 7));
        $mform->setType('defaultmark', PARAM_FLOAT);
        $mform->setDefault('defaultmark', 1);
        $mform->addRule('defaultmark', null, 'required', null, 'client');

        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question'),
                array('rows' => 10), $this->editoroptions);
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');

        // Any questiontype specific fields.
        $this->definition_inner($mform);

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagsheader', get_string('tags'));
            $mform->addElement('tags', 'tags', get_string('tags'));
        }

        if (!empty($this->question->id)) {
            $mform->addElement('header', 'createdmodifiedheader',
                    get_string('createdmodifiedheader', 'question'));
            $a = new stdClass();
            if (!empty($this->question->createdby)) {
                $a->time = userdate($this->question->timecreated);
                $a->user = fullname($DB->get_record(
                        'user', array('id' => $this->question->createdby)));
            } else {
                $a->time = get_string('unknown', 'question');
                $a->user = get_string('unknown', 'question');
            }
            $mform->addElement('static', 'created', get_string('created', 'question'),
                     get_string('byandon', 'question', $a));
            if (!empty($this->question->modifiedby)) {
                $a = new stdClass();
                $a->time = userdate($this->question->timemodified);
                $a->user = fullname($DB->get_record(
                        'user', array('id' => $this->question->modifiedby)));
                $mform->addElement('static', 'modified', get_string('modified', 'question'),
                        get_string('byandon', 'question', $a));
            }
        }

        $this->add_hidden_fields();

        $mform->addElement('hidden', 'movecontext');
        $mform->setType('movecontext', PARAM_BOOL);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $buttonarray = array();
        if (!empty($this->question->id)) {
            // Editing / moving question
            if ($this->question->formoptions->movecontext) {
                $buttonarray[] = $mform->createElement('submit', 'submitbutton',
                        get_string('moveq', 'question'));
            } else if ($this->question->formoptions->canedit) {
                $buttonarray[] = $mform->createElement('submit', 'submitbutton',
                        get_string('savechanges'));
            }
            if ($this->question->formoptions->cansaveasnew) {
                $buttonarray[] = $mform->createElement('submit', 'makecopy',
                        get_string('makecopy', 'question'));
            }
            $buttonarray[] = $mform->createElement('cancel');
        } else {
            // Adding new question
            $buttonarray[] = $mform->createElement('submit', 'submitbutton',
                    get_string('savechanges'));
            $buttonarray[] = $mform->createElement('cancel');
        }
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        if ($this->question->formoptions->movecontext) {
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar'));
        } else if ((!empty($this->question->id)) && (!($this->question->formoptions->canedit ||
                $this->question->formoptions->cansaveasnew))) {
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar', 'currentgrp'));
        }
    }

    /**
     * Add any question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) {
        // By default, do nothing.
    }

    /**
     * Get the list of form elements to repeat, one for each answer.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options
     *      field holding an array of answers
     * @return array of form fields.
     */
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] = $mform->createElement('header', 'answerhdr', $label);
        $repeated[] = $mform->createElement('text', 'answer',
                get_string('answer', 'question'), array('size' => 80));
        $repeated[] = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), array('rows' => 5), $this->editoroptions);
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
     * @param $minoptions the minimum number of answer blanks to display.
     *      Default QUESTION_NUMANS_START.
     * @param $addoptions the number of answer blanks to add. Default QUESTION_NUMANS_ADD.
     */
    protected function add_per_answer_fields(&$mform, $label, $gradeoptions,
            $minoptions = QUESTION_NUMANS_START, $addoptions = QUESTION_NUMANS_ADD) {
        $answersoption = '';
        $repeatedoptions = array();
        $repeated = $this->get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);

        if (isset($this->question->options)) {
            $countanswers = count($this->question->options->$answersoption);
        } else {
            $countanswers = 0;
        }
        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = max($minoptions, $countanswers + $addoptions);
        } else {
            $repeatsatstart = $countanswers;
        }

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions,
                'noanswers', 'addanswers', $addoptions,
                get_string('addmorechoiceblanks', 'qtype_multichoice'));
    }

    protected function add_combined_feedback_fields($withshownumpartscorrect = false) {
        $mform = $this->_form;

        $mform->addElement('header', 'combinedfeedbackhdr',
                get_string('combinedfeedback', 'question'));

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        foreach ($fields as $feedbackname) {
            $mform->addElement('editor', $feedbackname, get_string($feedbackname, 'question'),
                                array('rows' => 5), $this->editoroptions);
            $mform->setType($feedbackname, PARAM_RAW);

            if ($withshownumpartscorrect && $feedbackname == 'partiallycorrectfeedback') {
                $mform->addElement('advcheckbox', 'shownumcorrect',
                        get_string('options', 'question'),
                        get_string('shownumpartscorrectwhenfinished', 'question'));
            }
        }
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        $mform = $this->_form;

        $repeated = array();
        $repeated[] = $mform->createElement('header', 'hinthdr', get_string('hintn', 'question'));
        $repeated[] = $mform->createElement('editor', 'hint', get_string('hinttext', 'question'),
                array('rows' => 5), $this->editoroptions);
        $repeatedoptions['hint']['type'] = PARAM_RAW;

        if ($withclearwrong) {
            $repeated[] = $mform->createElement('advcheckbox', 'hintclearwrong',
                    get_string('options', 'question'), get_string('clearwrongparts', 'question'));
        }
        if ($withshownumpartscorrect) {
            $repeated[] = $mform->createElement('advcheckbox', 'hintshownumcorrect', '',
                    get_string('shownumpartscorrect', 'question'));
        }

        return array($repeated, $repeatedoptions);
    }

    protected function add_interactive_settings($withclearwrong = false,
            $withshownumpartscorrect = false) {
        $mform = $this->_form;

        $mform->addElement('header', 'multitriesheader',
                get_string('settingsformultipletries', 'question'));

        $penalties = array(
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000
        );
        if (!empty($this->question->penalty) && !in_array($this->question->penalty, $penalties)) {
            $penalties[] = $this->question->penalty;
            sort($penalties);
        }
        $penaltyoptions = array();
        foreach ($penalties as $penalty) {
            $penaltyoptions["$penalty"] = (100 * $penalty) . '%';
        }
        $mform->addElement('select', 'penalty',
                get_string('penaltyforeachincorrecttry', 'question'), $penaltyoptions);
        $mform->addRule('penalty', null, 'required', null, 'client');
        $mform->addHelpButton('penalty', 'penaltyforeachincorrecttry', 'question');
        $mform->setDefault('penalty', 0.3333333);

        if (isset($this->question->hints)) {
            $counthints = count($this->question->hints);
        } else {
            $counthints = 0;
        }

        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = max(self::DEFAULT_NUM_HINTS, $counthints);
        } else {
            $repeatsatstart = $counthints;
        }

        list($repeated, $repeatedoptions) = $this->get_hint_fields(
                $withclearwrong, $withshownumpartscorrect);
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions,
                'numhints', 'addhint', 1, get_string('addanotherhint', 'question'));
    }

    public function set_data($question) {
        question_bank::get_qtype($question->qtype)->set_default_options($question);

        // prepare question text
        $draftid = file_get_submitted_draft_itemid('questiontext');

        if (!empty($question->questiontext)) {
            $questiontext = $question->questiontext;
        } else {
            $questiontext = $this->_form->getElement('questiontext')->getValue();
            $questiontext = $questiontext['text'];
        }
        $questiontext = file_prepare_draft_area($draftid, $this->context->id,
                'question', 'questiontext', empty($question->id) ? null : (int) $question->id,
                $this->fileoptions, $questiontext);

        $question->questiontext = array();
        $question->questiontext['text'] = $questiontext;
        $question->questiontext['format'] = empty($question->questiontextformat) ?
                editors_get_preferred_format() : $question->questiontextformat;
        $question->questiontext['itemid'] = $draftid;

        // prepare general feedback
        $draftid = file_get_submitted_draft_itemid('generalfeedback');

        if (empty($question->generalfeedback)) {
            $generalfeedback = $this->_form->getElement('generalfeedback')->getValue();
            $question->generalfeedback = $generalfeedback['text'];
        }

        $feedback = file_prepare_draft_area($draftid, $this->context->id,
                'question', 'generalfeedback', empty($question->id) ? null : (int) $question->id,
                $this->fileoptions, $question->generalfeedback);
        $question->generalfeedback = array();
        $question->generalfeedback['text'] = $feedback;
        $question->generalfeedback['format'] = empty($question->generalfeedbackformat) ?
                editors_get_preferred_format() : $question->generalfeedbackformat;
        $question->generalfeedback['itemid'] = $draftid;

        // Remove unnecessary trailing 0s form grade fields.
        if (isset($question->defaultgrade)) {
            $question->defaultgrade = 0 + $question->defaultgrade;
        }
        if (isset($question->penalty)) {
            $question->penalty = 0 + $question->penalty;
        }

        // Set any options.
        $extraquestionfields = question_bank::get_qtype($question->qtype)->extra_question_fields();
        if (is_array($extraquestionfields) && !empty($question->options)) {
            array_shift($extraquestionfields);
            foreach ($extraquestionfields as $field) {
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
     * Perform an preprocessing needed on the data passed to {@link set_data()}
     * before it is used to initialise the form.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing($question) {
        return $question;
    }

    /**
     * Perform the necessary preprocessing for the fields added by
     * {@link add_per_answer_fields()}.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_answers($question, $withanswerfiles = false) {
        if (empty($question->options->answers)) {
            return $question;
        }

        $key = 0;
        foreach ($question->options->answers as $answer) {
            if ($withanswerfiles) {
                // Prepare the feedback editor to display files in draft area
                $draftitemid = file_get_submitted_draft_itemid('answer['.$key.']');
                $question->answer[$key]['text'] = file_prepare_draft_area(
                    $draftitemid,          // draftid
                    $this->context->id,    // context
                    'question',            // component
                    'answer',              // filarea
                    !empty($answer->id) ? (int) $answer->id : null, // itemid
                    $this->fileoptions,    // options
                    $answer->answer        // text
                );
                $question->answer[$key]['itemid'] = $draftitemid;
                $question->answer[$key]['format'] = $answer->answerformat;
            } else {
                $question->answer[$key] = $answer->answer;
            }

            $question->fraction[$key] = 0 + $answer->fraction;
            $question->feedback[$key] = array();

            // Evil hack alert. Formslib can store defaults in two ways for
            // repeat elements:
            //   ->_defaultValues['fraction[0]'] and
            //   ->_defaultValues['fraction'][0].
            // The $repeatedoptions['fraction']['default'] = 0 bit above means
            // that ->_defaultValues['fraction[0]'] has already been set, but we
            // are using object notation here, so we will be setting
            // ->_defaultValues['fraction'][0]. That does not work, so we have
            // to unset ->_defaultValues['fraction[0]']
            unset($this->_form->_defaultValues["fraction[$key]"]);

            // Prepare the feedback editor to display files in draft area
            $draftitemid = file_get_submitted_draft_itemid('feedback['.$key.']');
            $question->feedback[$key]['text'] = file_prepare_draft_area(
                $draftitemid,          // draftid
                $this->context->id,    // context
                'question',            // component
                'answerfeedback',      // filarea
                !empty($answer->id) ? (int) $answer->id : null, // itemid
                $this->fileoptions,    // options
                $answer->feedback      // text
            );
            $question->feedback[$key]['itemid'] = $draftitemid;
            $question->feedback[$key]['format'] = $answer->feedbackformat;
            $key++;
        }
        return $question;
    }

    /**
     * Perform the necessary preprocessing for the fields added by
     * {@link add_combined_feedback_fields()}.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_combined_feedback($question,
            $withshownumcorrect = false) {
        if (empty($question->options)) {
            return $question;
        }

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        foreach ($fields as $feedbackname) {
            $draftid = file_get_submitted_draft_itemid($feedbackname);
            $feedback = array();
            $feedback['text'] = file_prepare_draft_area(
                $draftid,              // draftid
                $this->context->id,    // context
                'question',            // component
                $feedbackname,         // filarea
                !empty($question->id) ? (int) $question->id : null, // itemid
                $this->fileoptions,    // options
                $question->options->$feedbackname // text
            );
            $feedbackformat = $feedbackname . 'format';
            $feedback['format'] = $question->options->$feedbackformat;
            $feedback['itemid'] = $draftid;

            $question->$feedbackname = $feedback;
        }

        if ($withshownumcorrect) {
            $question->shownumcorrect = $question->options->shownumcorrect;
        }

        return $question;
    }

    /**
     * Perform the necessary preprocessing for the hint fields.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_hints($question, $withclearwrong = false,
            $withshownumpartscorrect = false) {
        if (empty($question->hints)) {
            return $question;
        }

        $key = 0;
        foreach ($question->hints as $hint) {
            $question->hint[$key] = array();

            // prepare feedback editor to display files in draft area
            $draftitemid = file_get_submitted_draft_itemid('hint['.$key.']');
            $question->hint[$key]['text'] = file_prepare_draft_area(
                $draftitemid,          // draftid
                $this->context->id,    // context
                'question',            // component
                'hint',                // filarea
                !empty($hint->id) ? (int) $hint->id : null, // itemid
                $this->fileoptions,    // options
                $hint->hint            // text
            );
            $question->hint[$key]['itemid'] = $draftitemid;
            $question->hint[$key]['format'] = $hint->hintformat;
            $key++;

            if ($withclearwrong) {
                $question->hintclearwrong[] = $hint->clearwrong;
            }
            if ($withshownumpartscorrect) {
                $question->hintshownumcorrect[] = $hint->shownumcorrect;
            }
        }

        return $question;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);
        if (empty($fromform['makecopy']) && isset($this->question->id)
                && ($this->question->formoptions->canedit ||
                        $this->question->formoptions->cansaveasnew)
                && empty($fromform['usecurrentcat']) && !$this->question->formoptions->canmove) {
            $errors['currentgrp'] = get_string('nopermissionmove', 'question');
        }
        return $errors;
    }

    /**
     * Override this in the subclass to question type name.
     * @return the question type name, should be the same as the name() method
     *      in the question type class.
     */
    public abstract function qtype();
}
