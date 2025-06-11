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
 * Defines the editing form for the essayautograde question type.
 *
 * @package    qtype
 * @subpackage essayautograde
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/question/type/essay/edit_essay_form.php');

/**
 * Essay question type editing form.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_edit_form extends qtype_essay_edit_form {

    /** Settings for adding repeated form elements */
    const NUM_ITEMS_DEFAULT = 0;
    const NUM_ITEMS_MIN     = 0;
    const NUM_ITEMS_ADD     = 1;

    /** Number of rows in TEXTAREA elements */
    const TEXTAREA_ROWS = 3;

    /**
     * qtype is plugin name without leading "qtype_"
     */
    public function qtype() {
        return substr($this->plugin_name(), 6);
    }

    /**
     * Plugin name is class name without trailing "_edit_form"
     */
    public function plugin_name() {
        return substr(get_class($this), 0, -10);
    }

    /**
     * Fetch a constant from the plugin class in "questiontype.php".
     */
    protected function plugin_constant($name) {
        $plugin = $this->plugin_name();
        return constant($plugin.'::'.$name);
    }

    protected function definition_inner($mform) {
        global $CFG, $PAGE;

        parent::definition_inner($mform);

        // cache the plugin name
        $plugin = 'qtype_essayautograde'; // $this->plugin_name();

        // add Javascript to expand/contract text input fields
        $params = array();
        $PAGE->requires->js_call_amd("$plugin/form", 'init', $params);

        // cache options for form elements to input text
        $short_text_options  = array('size' => 3,  'style' => 'width: auto');
        $medium_text_options = array('size' => 5,  'style' => 'width: auto');
        $long_text_options   = array('size' => 10, 'style' => 'width: auto');

        // Cache span element to act as linebreak in checkbox lists.
        $linebreak = \html_writer::tag('span', '', ['class' => 'w-100']);

        // cache options for show/hide elements
        $showhide_options = $this->get_showhide_options($plugin);

        // cache options for form elements to select a grade
        $grade_options = $this->get_grade_options($plugin);

        // cache options for form elements to select a grade
        $divisor_options = $this->get_divisor_options($plugin);

        /*////////////////////////////////////////
        // add main form elements
        ////////////////////////////////////////*/

        // AI is only available from Moodle 4.5.
        $providers = $this->get_ai_providers();
        if (is_array($providers)) {

            // Change the text on the "Grader Information" header to "AI Settings".
            $name = 'graderinfoheader';
            if ($mform->elementExists($name)) {
                $element = $mform->getElement($name);
                $element->setText(get_string('aisettings', $plugin));
            }

            // Change the label on "Information for graders" element to "AI prompt".
            $name = 'graderinfo';
            if ($mform->elementExists($name)) {
                $element = $mform->getElement($name);
                $element->setLabel(get_string('aiprompt', $plugin));
                $mform->addHelpButton($name, 'aiprompt', $plugin);
            }

            if (empty($providers)) {
                // No AI assistants are enabled for text generation.
                $name = 'aiassistant';
                $label = get_string($name, $plugin);
                $mform->addElement('hidden', $name, '');
                $mform->setType($name, PARAM_ALPHANUMEXT);
                // Show warning message.
                $text = get_string($name.'notenabled', $plugin);
                $mform->addElement('static', '', $label, $text);
            } else {
                $name = 'aiassistant';
                $label = get_string($name, $plugin);
                $providers = array_merge(['' => get_string('none')], $providers);
                $mform->addElement('select', $name, $label, $providers);
                $mform->addHelpButton($name, $name, $plugin);
                $mform->setType($name, PARAM_ALPHANUMEXT);
                $mform->setDefault($name, $this->get_my_default_value($name, 0));
                // Move the "aiassistant" element before the "graderinfo" (AI prompt) textarea.
                if ($mform->elementExists('graderinfo')) {
                    $mform->insertElementBefore($mform->removeElement($name, false), 'graderinfo');
                }

                $name = 'aipercent';
                $label = get_string($name, $plugin);
                $mform->addElement('select', $name, $label, $grade_options);
                $mform->addHelpButton($name, $name, $plugin);
                $mform->setType($name, PARAM_INT);
                $mform->setDefault($name, $this->get_my_default_value($name, 0));
            }
        }

        $name = 'autograding';
        $label = get_string($name, $plugin);
        $mform->addElement('header', $name, $label);
        $mform->setExpanded($name, true);

        $name = 'enableautograde';
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $name, $label);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 1));

        $name = 'itemtype';
        $label = get_string($name, $plugin);
        $options = $this->get_itemtype_options($plugin);
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, $this->plugin_constant('ITEM_TYPE_WORDS')));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $name = 'itemcount';
        $label = get_string($name, $plugin);
        $mform->addElement('text', $name, $label, $medium_text_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->disabledIf($name, 'itemtype', 'eq', $this->plugin_constant('ITEM_TYPE_NONE'));

        $name = 'showfeedback';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $showhide_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $name = 'showcalculation';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $showhide_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $name = 'showtextstats';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $showhide_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $name = 'textstatitems';
        $label = get_string($name, $plugin);
        $options = $this->get_textstatitems_options(true);
        $elements = array();
        foreach ($options as $value => $text) {
            $elements[] = $mform->createElement('checkbox', $name."[$value]",  '', $text);
        }
        $mform->addGroup($elements, $name, $label, $linebreak, false);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->disabledIf($name, 'showtextstats', 'eq', $this->plugin_constant('SHOW_NONE'));
        $mform->disabledIf($name.'[commonerrors]', 'errorcmid', 'eq', 0);

        // only use defaults on new record
        //$defaults = 'words,wordspersentence,uniquewords,longwords';
        //$defaults = $this->get_my_default_value($name, $defaults);
        //$defaults = explode(',', $defaults);
        //$defaults = array_filter($defaults);

        foreach ($options as $value => $text) {
            $mform->setType($name."[$value]", PARAM_INT);
            //$mform->setDefault($name."[$value]", in_array($value, $defaults));
        }

        /*////////////////////////////////////////
        // add grade bands
        ////////////////////////////////////////*/

        $name = 'gradebands';
        $label = get_string($name, $plugin);
        $mform->addElement('header', $name, $label);
        $mform->setExpanded($name, true);

        $name = 'showgradebands';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $showhide_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $name = 'addpartialgrades';
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $name, $label);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);

        $this->add_repeat_gradebands($mform, $plugin, $short_text_options, $long_text_options, $grade_options);

        /*////////////////////////////////////////
        // add target phrases
        ////////////////////////////////////////*/

        $name = 'targetphrases';
        $label = get_string($name, $plugin);
        $mform->addElement('header', $name, $label);
        $mform->setExpanded($name, true);

        $name = 'showtargetphrases';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $showhide_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->disabledIf($name, 'itemtype', 'eq', $this->plugin_constant('ITEM_TYPE_FILES'));

        $this->add_repeat_targetphrases($mform, $plugin, $short_text_options, $long_text_options, $grade_options, $divisor_options);

        /*////////////////////////////////////////
        // add common errors
        ////////////////////////////////////////*/

        $name = 'commonerrors';
        $label = get_string($name, $plugin);
        $mform->addElement('header', $name, $label);
        $mform->setExpanded($name, true);

        $name = 'errorcmid';
        $label = get_string($name, $plugin);
        $options = $this->get_errorcmid_options($PAGE->course->id);
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->disabledIf($name, 'itemtype', 'eq', $this->plugin_constant('ITEM_TYPE_FILES'));

        $name = 'errorpercent';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $grade_options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $this->get_my_default_value($name, 5));
        $mform->setType($name, PARAM_INT);
        $mform->disabledIf($name, 'errorcmid', 'eq', 0);
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->disabledIf($name, 'itemtype', 'eq', $this->plugin_constant('ITEM_TYPE_FILES'));

        // add group of error matching behaviors
        $elements = array();

        $name = 'errorfullmatch';
        $default = (empty($CFG->glossary_fullmatch) ? 0 : 1);
        $elements[] = $mform->createElement('select', $name, '', $this->get_fullmatch_options($plugin));
        $mform->setDefault($name, $this->get_my_default_value($name, $default));
        $mform->setType($name, PARAM_INT);

        $name = 'errorcasesensitive';
        $default = (empty($CFG->glossary_casesensitive) ? 0 : 1);
        $elements[] = $mform->createElement('select', $name, '', $this->get_casesensitive_options($plugin));
        $mform->setDefault($name, $this->get_my_default_value($name, $default));
        $mform->setType($name, PARAM_INT);

        $name = 'errorignorebreaks';
        $elements[] = $mform->createElement('select', $name, '', $this->get_ignorebreaks_options($plugin));
        $mform->setDefault($name, $this->get_my_default_value($name, 0));
        $mform->setType($name, PARAM_INT);

        $name = 'errorbehavior';
        $label = get_string($name, $plugin);
        $mform->addGroup($elements, $name, $label, ' ', false);
        $mform->disabledIf($name, 'enableautograde', 'eq', 0);
        $mform->addHelpButton($name, $name, $plugin);

        /*////////////////////////////////////////////////
        // Insert responsesample after responsetemplate.
        ////////////////////////////////////////////////*/

        $name = 'responsesample';
        $label = get_string($name, $plugin);
        $options = array_merge($this->editoroptions, array('maxfiles' => 0));
        $i = $mform->_elementIndex['responsetemplate'];
        $mform->insertElementBefore($mform->createElement(
            'editor', $name, $label, array('rows' => 10), $options
        ), array_search($i + 1, $mform->_elementIndex));
        $mform->addHelpButton($name, $name, $plugin);

        /*////////////////////////////////////////////////
        // Insert allowsimilarity after responsesample.
        ////////////////////////////////////////////////*/

        $name = 'allowsimilarity';
        $label = get_string($name, $plugin);
        $options = $this->get_allowsimilarity_options($plugin);
        $i = $mform->_elementIndex['responsesample'];
        $mform->insertElementBefore($mform->createElement(
            'select', $name, $label, $options
        ), array_search($i + 1, $mform->_elementIndex));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_my_default_value($name, 10));

        /*////////////////////////////////////////////////
        // Add feedback fields (= Combined feedback).
        // and interactive settings (= Multiple tries).
        // Move combined feedback after general feedback.
        ////////////////////////////////////////////////*/

        $this->add_combined_feedback_fields(false);
        $this->add_interactive_settings(false, false);

        $name = 'numhints';
        if ($mform->elementExists($name)) {
            $numhints = $mform->getElement($name);
            $numhints = $numhints->getValue();
        } else {
            $numhints = 0;
        }

        $previousname = 'responseoptions';
        $names = array('combinedfeedbackhdr',
                       'correctfeedback',
                       'partiallycorrectfeedback',
                       'incorrectfeedback');
        foreach ($names as $name) {
            if ($mform->elementExists($name)) {
                $mform->insertElementBefore($mform->removeElement($name, false), $previousname);
            }
        }

        /*////////////////////////////////////////
        // collapse certain form sections
        ////////////////////////////////////////*/

        $names = array('combinedfeedbackhdr' => false,
                       'responseoptions' => false,
                       'responsetemplateheader' => false,
                       'graderinfoheader' => empty($providers) ? false : true,
                       'multitriesheader' => false);
        foreach ($names as $name => $expanded) {
            if ($mform->elementExists($name)) {
                $mform->setExpanded($name, $expanded);
            }
        }

        /*////////////////////////////////////////
        // reduce vertical height of textareas
        ////////////////////////////////////////*/

        $names = array('questiontext',
                       'generalfeedback',
                       'correctfeedback',
                       'partiallycorrectfeedback',
                       'incorrectfeedback',
                       'responsetemplate',
                       'responsesample',
                       'graderinfo');
        for ($i=0; $i<$numhints; $i++) {
            $names[] = "hint[$i]";
        }
        foreach ($names as $name) {
            if ($mform->elementExists($name)) {
                $element = $mform->getElement($name);
                $attributes = $element->getAttributes();
                $attributes['rows'] = self::TEXTAREA_ROWS;
                $element->setAttributes($attributes);
            }
        }
    }

    protected function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);

        $question = $this->data_preprocessing_combined_feedback($question);
        $question = $this->data_preprocessing_hints($question, false, false);

        /*//////////////////////////////////////////////
        // add fields from qtype_essayautograde_options
        //////////////////////////////////////////////*/

        if (empty($question->options)) {
            return $question;
        }

        // Initialize numeric fields.
        $names = array('enableautograde', 'itemtype', 'itemcount',
                       'showfeedback', 'showcalculation', 'showtextstats',
                       'showgradebands', 'addpartialgrades', 'showtargetphrases',
                       'errorcmid', 'errorpercent', 'aipercent');
        foreach ($names as $name) {
            if (! isset($question->options->$name)) {
                $question->options->$name = 0;
            }
            $question->$name = $question->options->$name;
        }

        // Initialize HTML text fields.
        $names = array('responsesample');
        foreach ($names as $name) {
            if (! isset($question->options->$name)) {
                $question->options->$name = '';
            }
            if (! isset($question->options->{$name.'format'})) {
                $question->options->{$name.'format'} = FORMAT_HTML;
            }
        }
        $question->responsesample = array(
            'text' => $question->options->responsesample,
            'format' => $question->options->responsesampleformat,
        );

        // Initialize textstatitems (a comma delimited list)
        if (! isset($question->options->textstatitems)) {
            $question->options->textstatitems = '';
        }
        $question->textstatitems = $question->options->textstatitems;
        $question->textstatitems = explode(',', $question->textstatitems);
        $question->textstatitems = array_filter($question->textstatitems);
        $question->textstatitems = array_flip($question->textstatitems);
        foreach ($this->get_textstatitems_options(false) as $value) {
            $question->textstatitems[$value] = array_key_exists($value, $question->textstatitems);
        }

        /*////////////////////////////////////////
        // add fields from question_answers
        ////////////////////////////////////////*/

        $question->bandcount = array();
        $question->bandpercent = array();
        $question->phrasematch = array();
        $question->phrasepercent = array();
        $question->phrasedivisor = array();
        $question->phrasefullmatch = array();
        $question->phrasecasesensitive = array();
        $question->phraseignorebreaks = array();

        $question = parent::data_preprocessing_answers($question);

        if (empty($question->options->answers)) {
            return $question;
        }

        $ANSWER_TYPE = $this->plugin_constant('ANSWER_TYPE');
        $ANSWER_FULL_MATCH = $this->plugin_constant('ANSWER_FULL_MATCH');
        $ANSWER_CASE_SENSITIVE = $this->plugin_constant('ANSWER_CASE_SENSITIVE');
        $ANSWER_IGNORE_BREAKS = $this->plugin_constant('ANSWER_IGNORE_BREAKS');

        $ANSWER_TYPE_BAND = $this->plugin_constant('ANSWER_TYPE_BAND');
        $ANSWER_TYPE_PHRASE = $this->plugin_constant('ANSWER_TYPE_PHRASE');

        foreach ($question->options->answers as $id => $answer) {

            $fraction = sprintf('%.02f', $answer->fraction);
            list($fraction, $divisor) = explode('.', $fraction);
            $fraction = intval($fraction);

            $answer->type = ($fraction & $ANSWER_TYPE);
            $answer->phrasefullmatch = (($fraction & $ANSWER_FULL_MATCH) ? 1 : 0);
            $answer->phrasecasesensitive = (($fraction & $ANSWER_CASE_SENSITIVE) ? 1 : 0);
            $answer->phraseignorebreaks = (($fraction & $ANSWER_IGNORE_BREAKS) ? 1 : 0);
            $question->options->answers[$id] = $answer;

            switch ($answer->type) {
                case $ANSWER_TYPE_BAND:
                    $question->bandcount[] = $answer->answer;
                    $question->bandpercent[] = $answer->answerformat;
                    break;
                case $ANSWER_TYPE_PHRASE:
                    $question->phrasematch[] = $answer->feedback;
                    $question->phrasepercent[] = $answer->feedbackformat;
                    $question->phrasedivisor[] = intval($divisor);
                    $question->phrasefullmatch[] = $answer->phrasefullmatch;
                    $question->phrasecasesensitive[] = $answer->phrasecasesensitive;
                    $question->phraseignorebreaks[] = $answer->phraseignorebreaks;
                    break;
            }
        }

        return $question;
    }

    /**
     * Returns the number of repeated grade bands (type=0)
     * or target phrases (type=1) for this question.
     *
     * The "type" value is stored in the fraction field
     * of the "question_answers" records for this question.
     *
     * @param object  $question
     * @param integer $type 0=grade bands, 1=target phrases
     * @return int
     */
    protected function get_answer_repeats($question, $type) {
        if (isset($question->id)) {
            $repeats = 0;
            if (isset($question->options->answers)) {
                $ANSWER_TYPE = $this->plugin_constant('ANSWER_TYPE');
                foreach ($question->options->answers as $answer) {
                    $fraction = intval($answer->fraction);
                    if (($fraction & $ANSWER_TYPE)==$type) {
                        $repeats++;
                    }
                }
            }
        } else {
            $repeats = self::NUM_ITEMS_DEFAULT;
        }
        if ($repeats < self::NUM_ITEMS_MIN) {
            $repeats = self::NUM_ITEMS_MIN;
        }
        return $repeats;
    }

    /**
     * Returns default value for item
     *
     * @param string $name Item name
     * @param string|mixed|null $default Default value (optional, default = null)
     * @return string|mixed|null Default value for field with this $name
     */
    protected function get_my_default_value($name, $default) {
        if (method_exists($this, 'get_default_value')) {
            // Moodle >= 3.10
            return $this->get_default_value($name, $default);
        } else {
            // Moodle <= 3.9
            return get_user_preferences($this->plugin_name().'_'.$name, $default);
        }
    }

    /**
     * Get array of countable item types
     *
     * @param string $plugin name
     * @return array(type => description)
     */
    protected function get_itemtype_options($plugin) {
        return array($this->plugin_constant('ITEM_TYPE_NONE') => get_string('none'),
                     $this->plugin_constant('ITEM_TYPE_CHARS') => get_string('chars', $plugin),
                     $this->plugin_constant('ITEM_TYPE_WORDS') => get_string('words', $plugin),
                     $this->plugin_constant('ITEM_TYPE_SENTENCES') => get_string('sentences', $plugin),
                     $this->plugin_constant('ITEM_TYPE_PARAGRAPHS') => get_string('paragraphs', $plugin),
                     $this->plugin_constant('ITEM_TYPE_FILES') => get_string('files', $plugin));
    }

    /**
     * Get array of similarity threshold percentages
     *
     * @param string $plugin name
     * @return array(value => percent)
     */
    protected function get_allowsimilarity_options($plugin) {
        $options = array();
        $options[0] = get_string('no');
        for ($i=5; $i<=100; $i+=5) {
            $options[$i] = get_string('allowsimilaritypercent', $plugin, $i);
        }
        return $options;
    }

    /**
     * Get array of show/hide options
     *
     * @param string $plugin name
     * @return array(type => description)
     */
    protected function get_showhide_options($plugin) {
        return array($this->plugin_constant('SHOW_NONE') => get_string('no'),
                     $this->plugin_constant('SHOW_STUDENTS_ONLY') => get_string('showtostudentsonly', $plugin),
                     $this->plugin_constant('SHOW_TEACHERS_ONLY') => get_string('showtoteachersonly', $plugin),
                     $this->plugin_constant('SHOW_TEACHERS_AND_STUDENTS') => get_string('showtoteachersandstudents', $plugin));
    }

    /**
     * Get array of grade options
     *
     * @param string $plugin name
     * @return array(grade => description)
     */
    protected function get_grade_options($plugin) {
        $options = array();
        for ($i=0; $i<=100; $i++) {
            $options[$i] = "$i%";
        }
        return $options;
    }

    /**
     * Get array of grade options
     *
     * @param string $plugin name
     * @return array(grade => description)
     */
    protected function get_divisor_options($plugin) {
        $options = array();
        for ($i=1; $i<=20; $i++) {
            if ($i == 1) {
                $options[$i] = get_string('phrasepercentexactly', $plugin);
            } else {
                $options[$i] = get_string('phrasepercentdividedby', $plugin, $i);
            }
        }
        return $options;
    }

    /**
     * Retrieves available AI providers for the generate_text action.
     *
     * This method checks whether the Moodle core AI functionality exists.
     * If it does, it retrieves a list of AI providers that support the
     * `generate_text` action and returns them as an associative array
     * where the keys are provider names and the values are localized
     * provider names.
     *
     * In Moodle 4.5 the "get_providers_for_actions()" method was static,
     * but in 5.0, it was changed to non-static. To maintain compatibility
     * with both versions, PHP's ReflectionMethod is used to detect whether
     * the target method is static or not, and calls it appropriately.
     *
     * @return array|false An associative array of AI provider names with their localized names,
     *                     or false if the AI manager class or target method does not exist.
     */
    protected function get_ai_providers() {
        global $DB;

        // Define the PHP class and method that we will use.
        $managerclass = '\\core_ai\\manager';
        $actionclass = '\\core_ai\\aiactions\\generate_text';
        $method = 'get_providers_for_actions';
    
        // Moodle <= 4.5 had no core AI functionality.
        if (!class_exists($managerclass)) {
            return false;
        }
    
        // Check if the method exists - it should !!
        if (!method_exists($managerclass, $method)) {
            return false;
        }
    
        // Initialize the array of options.
        $options = [];

        // Extract action from class name.
        $action = trim($actionclass, '\\');
    
        $reflection = new \ReflectionMethod($managerclass, $method);
        if ($reflection->isStatic()) {
            // Moodle 4.5 and earlier
            $providers = $managerclass::$method([$action], true);
        } else {
            // Moodle 5.0 and later - needs an instance
            // and furthermore, that instance needs $DB.
            $manager = new $managerclass($DB);
            $providers = $manager->$method([$action], true);
        }
    
        foreach ($providers[$action] as $provider) {
            $name = $provider->get_name();
            $options[$name] = get_string('pluginname', $name);
        }
    
        return $options;
    }


    /**
     * Get array of full match options
     *
     * @param string $plugin name
     * @return array(value => description)
     */
    protected function get_fullmatch_options($plugin) {
        return array(0 => get_string('phrasefullmatchno', $plugin),
                     1 => get_string('phrasefullmatchyes', $plugin));
    }

    /**
     * Get array of case sensitivity options
     *
     * @param string $plugin name
     * @return array(value => description)
     */
    protected function get_casesensitive_options($plugin) {
        return array(0 => get_string('phrasecasesensitiveno', $plugin),
                     1 => get_string('phrasecasesensitiveyes', $plugin));
    }

    /**
     * Get array of options for ignoring breaks
     *
     * @param string $plugin name
     * @return array(value => description)
     */
    protected function get_ignorebreaks_options($plugin) {
        return array(0 => get_string('phraseignorebreaksno', $plugin),
                     1 => get_string('phraseignorebreaksyes', $plugin));
    }

    /**
     * Get array of glossary options
     *
     * @return array(glossaryid => name)
     */
    protected function get_errorcmid_options($courseid=0) {
        $options = array('0' => '');
        $modinfo = get_fast_modinfo($courseid);
        foreach ($modinfo->cms as $cmid => $cm) {
            if ($cm->modname=='glossary' && $cm->uservisible) {
                $options[$cm->id] = format_text($cm->name);
            }
        }
        return $options;
    }

    /**
     * Get array of countable item types
     *
     * @return array(type => description)
     */
    protected function get_textstatitems_options($returntext=true) {
        $options = array('chars', 'words',
                         'sentences', 'paragraphs',
                         'uniquewords', 'longwords',
                         'charspersentence', 'wordspersentence',
                         'longwordspersentence', 'sentencesperparagraph',
                         'lexicaldensity', 'fogindex',
                         'commonerrors', 'files');
        if ($returntext) {
            $plugin = 'qtype_essayautograde'; // $this->plugin_name();
            $options = array_flip($options);
            foreach (array_keys($options) as $option) {
                $options[$option] = get_string($option, $plugin);
            }
        }
        return $options;
    }

    /**
     * Get array of countable item types
     *
     * @return array(type => description)
     */
    protected function get_addcount_options($type, $max=10) {

        // cache plugin name
        $plugin = 'qtype_essayautograde'; // $this->plugin_name();

        // generate options
        $options = array();
        for ($i=1; $i<=$max; $i++) {
            if ($i==1) {
                $options[$i] = get_string('addsingle'.$type, $plugin);
            } else {
                $options[$i] = get_string('addmultiple'.$type.'s', $plugin, $i);
            }
        }
        return $options;
    }

    /**
     * Add grade bands as repeated elements
     *
     * @param object $mform the Moodle form object
     * @param string $plugin name
     * @param array $short_text_options
     * @param array $grade_options
     * @return void, but will update $mform
     */
    protected function add_repeat_gradebands($mform, $plugin, $short_text_options, $long_text_options, $grade_options) {

        $repeat_elements = array();
        $repeat_options = array();

        // Add group of grade bands
        $group_elements = array();

        $group_elements[] = $mform->createElement('html', get_string('bandtext1', $plugin));

        $name = 'bandcount';
        $group_elements[] = $mform->createElement('text', $name, '', $short_text_options);
        $repeat_options[$name] = array('type' => PARAM_INT);

        $group_elements[] = $mform->createElement('html', get_string('bandtext2', $plugin));

        $name = 'bandpercent';
        $group_elements[] = $mform->createElement('select', $name, '', $grade_options);
        $repeat_options[$name] = array('type' => PARAM_INT);

        $group_elements[] = $mform->createElement('html', get_string('bandtext3', $plugin));

        $name = 'gradeband';
        $repeat_elements[] = $mform->createElement('group', $name, '', $group_elements, ' ', false);
        $repeat_options[$name] = array('helpbutton' => array($name, $plugin),
                                       'disabledif' => array('enableautograde', 'eq', 0));
        $this->add_repeat_elements($mform, 'band', $repeat_elements, $repeat_options, $name);

        // disable gradebands if no itemtype is specified
        $i = 0;
        while ($mform->elementExists($name."[$i]")) {
            $mform->disabledIf($name."[$i]", 'itemtype', 'eq', 0);
            $i++;
        }
    }

    /**
     * Add grade bands as repeated elements
     *
     * @param object $mform the Moodle form object
     * @param string $plugin name
     * @param array $short_text_options
     * @param array $grade_options
     * @return void, but will update $mform
     */
    protected function add_repeat_targetphrases($mform, $plugin, $short_text_options, $long_text_options, $grade_options, $divisor_options) {

        $repeat_elements = array();
        $repeat_options = array();

        // add group of target phrases
        $group_elements = array();

        $group_elements[] = $mform->createElement('html', get_string('phrasetext1', $plugin));

        $name = 'phrasematch';
        $group_elements[] = $mform->createElement('text', $name, '', $long_text_options);
        $repeat_options[$name] = array('type' => PARAM_TEXT);

        $group_elements[] = $mform->createElement('html', get_string('phrasetext2', $plugin));

        $name = 'phrasepercent';
        $group_elements[] = $mform->createElement('select', $name, '', $grade_options);
        $repeat_options[$name] = array('type' => PARAM_INT);

        $group_elements[] = $mform->createElement('html', get_string('phrasetext3', $plugin));

        $name = 'phrasedivisor';
        $group_elements[] = $mform->createElement('select', $name, '', $divisor_options);
        $repeat_options[$name] = array('type' => PARAM_INT);

        $group_elements[] = $mform->createElement('html', get_string('phrasetext4', $plugin));

        $name = 'targetphrase';
        $label = get_string($name, $plugin);
        $repeat_elements[] = $mform->createElement('group', $name, $label, $group_elements, ' ', false);
        $repeat_options[$name] = array('helpbutton' => array($name, $plugin),
                                       'disabledif' => array('enableautograde', 'eq', 0));

        // add group of phrase matching behaviors
        $group_elements = array();

        $name = 'phrasefullmatch';
        $group_elements[] = $mform->createElement('select', $name, '', $this->get_fullmatch_options($plugin));
        $repeat_options[$name] = array('type' => PARAM_INT);

        $name = 'phrasecasesensitive';
        $group_elements[] = $mform->createElement('select', $name, '', $this->get_casesensitive_options($plugin));
        $repeat_options[$name] = array('type' => PARAM_INT);

        $name = 'phraseignorebreaks';
        $group_elements[] = $mform->createElement('select', $name, '', $this->get_ignorebreaks_options($plugin));
        $repeat_options[$name] = array('type' => PARAM_INT);

        $name = 'phrasebehavior';
        $label = get_string($name, $plugin);
        $repeat_elements[] = $mform->createElement('group', $name, $label, $group_elements, ' ', false);
        $repeat_options[$name] = array('helpbutton' => array($name, $plugin),
                                       'disabledif' => array('enableautograde', 'eq', 0));

        $this->add_repeat_elements($mform, 'phrase', $repeat_elements, $repeat_options, $name);
    }

    /**
     * Add repeated elements with a button allowing a selectable number of new elements
     *
     * @param object $mform the Moodle form object
     * @param string the $type of elements being added (e.g. "band" or "phrase")
     * @return void, but will update $mform
     */
    protected function add_repeat_elements($mform, $type, $elements, $options, $name) {
        $types = $type.'s';
        $plugin = 'qtype_essayautograde'; // $this->plugin_name();

        // cache element names
        $addtypes = 'add'.$types;
        $counttypes = 'count'.$types;
        $addtypescount = $addtypes.'count';
        $addtypesgroup = $addtypes.'group';

        $repeats = $this->plugin_constant('ANSWER_TYPE_'.strtoupper($type));
        $repeats = $this->get_answer_repeats($this->question, $repeats);

        $count = optional_param($addtypescount, self::NUM_ITEMS_ADD, PARAM_INT);

        $label = ($count==1 ? 'addsingle'.$type : 'addmultiple'.$types);
        $label = get_string($label, $plugin, $count);

        $this->repeat_elements($elements, $repeats, $options, $counttypes, $addtypes, $count, $label, true);

        // remove the original "Add" button
        $mform->removeElement($addtypes);

        // replace with button + select group
        $options = $this->get_addcount_options($type);
        $mform->addGroup(array(
            $mform->createElement('submit', $addtypes, get_string('add')),
            $mform->createElement('select', $addtypescount, '', $options)
        ), $addtypesgroup, '', ' ', false);

        // set default value and type of select element
        $mform->setDefault($addtypescount, $count);
        $mform->setType($addtypescount, PARAM_INT);
    }
}
