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
 * Defines the editing form for the ordering question type.
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this script.
defined('MOODLE_INTERNAL') || die();

// Include required files.
require_once($CFG->dirroot.'/question/type/ordering/question.php');

/**
 * Ordering editing form definition
 *
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_edit_form extends question_edit_form {

    /** Rows count in answer field */
    const TEXTFIELD_ROWS = 2;

    /** Cols count in answer field */
    const TEXTFIELD_COLS = 60;

    /** Number of answers in question by default */
    const NUM_ITEMS_DEFAULT = 6;

    /** Minimum number of answers to show */
    const NUM_ITEMS_MIN = 3;

    /** Number of answers to add on demand */
    const NUM_ITEMS_ADD = 1;

    /**
     * qtype is plugin name without leading "qtype_"
     */
    public function qtype() {
        return 'ordering';
    }

    /**
     * Plugin name is class name without trailing "_edit_form"
     */
    public function plugin_name() {
        return 'qtype_ordering';
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    public function definition_inner($mform) {

        // Cache this plugins name.
        $plugin = 'qtype_ordering';

        // Field for layouttype.
        $name = 'layouttype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_layout_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $this->get_default_value($name, qtype_ordering_question::LAYOUT_VERTICAL));

        // Field for selecttype.
        $name = 'selecttype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_select_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $this->get_default_value($name, qtype_ordering_question::SELECT_ALL));

        // Field for selectcount.
        $name = 'selectcount';
        $label = get_string($name, $plugin);
        $options = array(0 => get_string('all'));
        for ($i = 3; $i <= 20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', $name, $label, $options);
        $mform->disabledIf($name, 'selecttype', 'eq', 0);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 6);

        // Field for gradingtype.
        $name = 'gradingtype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_grading_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $this->get_default_value($name, qtype_ordering_question::GRADING_ABSOLUTE_POSITION));

        // Field for showgrading.
        $name = 'showgrading';
        $label = get_string($name, $plugin);
        $options = array(0 => get_string('hide'),
                         1 => get_string('show'));
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $this->get_default_value($name, 1));

        $elements = array();
        $options = array();

        $name = 'answerheader';
        $label = get_string($name, $plugin);
        $elements[] = $mform->createElement('header', $name, $label);
        $options[$name] = array('expanded' => true);

        $name = 'answer';
        $elements[] = $mform->createElement('editor', $name, $label, $this->get_editor_attributes(), $this->get_editor_options());
        $elements[] = $mform->createElement('submit', $name . 'removeeditor', get_string('removeeditor', $plugin),
                array('onclick' => 'skipClientValidation = true;'));
        $options[$name] = array('type' => PARAM_RAW);

        $this->add_repeat_elements($mform, $name, $elements, $options);

        // Adjust HTML editor and removal buttons.
        $this->adjust_html_editors($mform, $name);

        // Adding feedback fields (=Combined feedback).
        $this->add_combined_feedback_fields(false);
        
        // Adding interactive settings (=Multiple tries).
        $this->add_interactive_settings(false, false);
    }

    /**
     * Returns answer repeats count
     *
     * @param object $question
     * @return int
     */
    protected function get_answer_repeats($question) {
        if (isset($question->id)) {
            $repeats = count($question->options->answers);
        } else {
            $repeats = self::NUM_ITEMS_DEFAULT;
        }
        if ($repeats < self::NUM_ITEMS_MIN) {
            $repeats = self::NUM_ITEMS_MIN;
        }
        return $repeats;
    }

    /**
     * Returns editor attributes
     *
     * @return array
     */
    protected function get_editor_attributes() {
        return array(
            'rows'  => self::TEXTFIELD_ROWS,
            'cols'  => self::TEXTFIELD_COLS
        );
    }

    /**
     * Returns editor options
     *
     * @return array
     */
    protected function get_editor_options() {
        return array(
            'context'  => $this->context,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean'  => true
        );
    }

    /**
     * Resets editor format to specified
     *
     * @param object $editor
     * @param int $format
     * @return int
     */
    protected function reset_editor_format($editor, $format=FORMAT_MOODLE) {
        $value = $editor->getValue();
        $value['format'] = $format;
        $value = $editor->setValue($value);
        return $format;
    }

    /**
     * Adjust HTML editor and removal buttons.
     *
     * @param object $mform
     * @param string $name
     * @param int $repeats
     */
    protected function adjust_html_editors($mform, $name) {

        // Cache the number of formats supported
        // by the preferred editor for each format.
        $count = array();

        if (isset($this->question->options->answers)) {
            $ids = array_keys($this->question->options->answers);
        } else {
            $ids = array();
        }

        $defaultanswerformat = get_config('qtype_ordering', 'defaultanswerformat');

        $repeats = 'count'.$name.'s'; // e.g. countanswers
        if ($mform->elementExists($repeats)) {
            // Use mform element to get number of repeats
            $repeats = $mform->getElement($repeats)->getValue();
        } else {
            // Determine number of repeats by object sniffing
            $repeats = 0;
            while ($mform->elementExists($name."[$repeats]")) {
                $repeats++;
            }
        }

        for ($i=0; $i < $repeats; $i++) {
            $editor = $mform->getElement($name."[$i]");

            if (isset($ids[$i])) {
                $id = $ids[$i];
            } else {
                $id = 0;
            }

            // The old/new name of the button to remove the HTML editor
            // old : the name of the button when added by repeat_elements
            // new : the simplified name of the button to satisfy "no_submit_button_pressed()" in lib/formslib.php.
            $oldname = $name.'removeeditor['.$i.']';
            $newname = $name.'removeeditor_'.$i;

            // Remove HTML editor, if necessary.
            if (optional_param($newname, 0, PARAM_RAW)) {
                $format = $this->reset_editor_format($editor, FORMAT_MOODLE);
                $_POST['answer'][$i]['format'] = $format; // Overwrite incoming data.
            } else if ($id) {
                $format = $this->question->options->answers[$id]->answerformat;
            } else {
                $format = $this->reset_editor_format($editor, $defaultanswerformat);
            }

            // Check we have a submit button - it should always be there !!
            if ($mform->elementExists($oldname)) {
                if (! isset($count[$format])) {
                    $editor = editors_get_preferred_editor($format);
                    $count[$format] = $editor->get_supported_formats();
                    $count[$format] = count($count[$format]);
                }
                if ($count[$format] > 1) {
                    $mform->removeElement($oldname);
                } else {
                    $submit = $mform->getElement($oldname);
                    $submit->setName($newname);
                }
                $mform->registerNoSubmitButton($newname);
            }
        }
    }

    /**
     * Perform an preprocessing needed on the data passed to {@link set_data()}
     * before it is used to initialise the form.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    public function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question, true);

        // Preprocess feedback.
        $question = $this->data_preprocessing_combined_feedback($question);
        
        $question = $this->data_preprocessing_hints($question, false, false);
        
        // Preprocess answers and fractions.
        $question->answer     = array();
        $question->fraction   = array();

        if (empty($question->options->answers)) {
            $answerids = array();
        } else {
            $answerids = array_keys($question->options->answers);
        }

        $defaultanswerformat = get_config('qtype_ordering', 'defaultanswerformat');
        $repeats = $this->get_answer_repeats($question);
        for ($i = 0; $i < $repeats; $i++) {

            if ($answerid = array_shift($answerids)) {
                $answer = $question->options->answers[$answerid];
            } else {
                $answer = (object)array('answer' => '',
                                        'answerformat' => $defaultanswerformat);
                $answerid = 0;
            }

            if (empty($question->id)) {
                $question->answer[$i] = $answer->answer;
            } else {
                $itemid = file_get_submitted_draft_itemid("answer[$i]");
                $format = $answer->answerformat;
                $text = file_prepare_draft_area($itemid, $this->context->id, 'question', 'answer',
                                                $answerid, $this->editoroptions, $answer->answer);
                $question->answer[$i] = array('text' => $text,
                                              'format' => $format,
                                              'itemid' => $itemid);
            }
            $question->fraction[$i] = ($i + 1);
        }

        // Defining default values.
        $names = array(
            'layouttype'  => qtype_ordering_question::LAYOUT_VERTICAL,
            'selecttype'  => qtype_ordering_question::SELECT_ALL,
            'selectcount' => 0, // 0 means ALL.
            'gradingtype' => qtype_ordering_question::GRADING_ABSOLUTE_POSITION,
            'showgrading' => 1  // 1 means SHOW.
        );
        foreach ($names as $name => $default) {
            if (isset($question->options->$name)) {
                $question->$name = $question->options->$name;
            } else {
                $question->$name = $this->get_default_value($name, $default);
            }
        }

        return $question;
    }

    /**
     * Form validation
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = array();
        $plugin = 'qtype_ordering';

        $answercount = 0;
        foreach ($data['answer'] as $answer) {
            if (is_array($answer)) {
                $answer = $answer['text'];
            }
            if (trim($answer) == '') {
                continue; // Skip empty answer.
            }
            $answercount++;
        }

        switch ($answercount) {
            case 0: $errors['answer[0]'] = get_string('notenoughanswers', $plugin, 2);
            case 1: $errors['answer[1]'] = get_string('notenoughanswers', $plugin, 2);
        }

        // If adding a new ordering question, update defaults.
        if (empty($errors) && empty($data['id'])) {
            $fields = array('layouttype', 'selecttype', 'selectcount', 'gradingtype', 'showgrading');
            foreach ($fields as $field) {
                if (array_key_exists($field, $data)) {
                    $this->set_default_value($field, $data[$field]);
                }
            }
        }

        return $errors;
    }

    /**
     * Returns default value for item
     *
     * @param string $name Item name
     * @param string|mixed|null $default Default value (optional, default = null)
     * @return string|mixed|null Default value for field with this $name
     */
    protected function get_default_value($name, $default=null) {
        return get_user_preferences("qtype_ordering_$name", $default);
    }

    /**
     * Saves default value for item
     *
     * @param string $name Item name
     * @param string|mixed|null $value
     * @return bool Always true or exception
     */
    protected function set_default_value($name, $value) {
        return set_user_preferences(array("qtype_ordering_$name" => $value));
    }


    /**
     * Get array of countable item types
     *
     * @return array(type => description)
     */
    protected function get_addcount_options($type, $max=10) {

        // cache plugin name
        $plugin = $this->plugin_name();

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
     * Add repeated elements with a button allowing a selectable number of new elements
     *
     * @param object $mform the Moodle form object
     * @return voide, but will update $mform
     */
    protected function add_repeat_elements($mform, $type, $elements, $options) {

        // cache plugin name
        $plugin = $this->plugin_name();

        // cache element names
        $types = $type.'s';
        $addtypes = 'add'.$types;
        $counttypes = 'count'.$types;
        $addtypescount = $addtypes.'count';
        $addtypesgroup = $addtypes.'group';

        $repeats = $this->get_answer_repeats($this->question);

        $count = optional_param($addtypescount, self::NUM_ITEMS_ADD, PARAM_INT);

        $label = ($count==1 ? 'addsingle'.$type : 'addmultiple'.$types);
        $label = get_string($label, $plugin, $count);

        $this->repeat_elements($elements, $repeats, $options, $counttypes, $addtypes, $count, $label, true);

        // remove the original "Add xxx" button ...
        $mform->removeElement($addtypes);

        // ... and replace it with "Add" button + select group
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
