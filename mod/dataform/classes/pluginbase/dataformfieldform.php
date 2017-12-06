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
 * @package dataformfield
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");

/**
 *
 */
class dataformfieldform extends \moodleform {
    protected $_field = null;

    /**
     *
     */
    public function __construct($field, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        $this->_field = $field;

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    /**
     *
     */
    public function definition() {
        $mform = &$this->_form;

        // Buttons.
        $this->add_action_buttons();
        // General.
        $this->definition_general();
        // Specific settings.
        $this->definition_settings();
        // Default content.
        $this->definition_defaults();
        // Buttons.
        $this->add_action_buttons();
    }

    /**
     *
     */
    protected function definition_general() {
        global $CFG;

        $mform = &$this->_form;
        $paramtext = !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN;

        // Header.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name.
        $mform->addElement('text', 'name', get_string('name'), array('size' => '32'));
        $mform->setType('name', $paramtext);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setDefault('name', $this->get_default_field_name());

        // Description.
        $mform->addElement('text', 'description', get_string('description'), array('size' => '64'));
        $mform->setType('description', $paramtext);

        // Visible.
        $options = array(
            dataformfield::VISIBLE_NONE => get_string('fieldvisiblenone', 'dataform'),
            dataformfield::VISIBLE_OWNER => get_string('fieldvisibleowner', 'dataform'),
            dataformfield::VISIBLE_ALL => get_string('fieldvisibleall', 'dataform'),
        );
        $mform->addElement('select', 'visible', get_string('visible'), $options);
        $mform->setDefault('visible', dataformfield::VISIBLE_ALL);

        // Editable.
        $options = array(-1 => get_string('yes'), 0 => get_string('no'));
        $mform->addElement('select', 'editable', get_string('fieldeditable', 'dataform'), $options);
        $mform->setDefault('editable', -1);

        // Template.
        $mform->addElement('textarea', 'label', get_string('fieldtemplate', 'dataform'), array('cols' => 60, 'rows' => 5));
        $mform->setType('label', $paramtext);
        $mform->addHelpButton('label', 'fieldtemplate', 'dataform');
    }

    /**
     * The field settings fieldset. Contains a header and calls the hook method
     * {@link dataformfieldform::field_definition()}.
     *
     * @return void
     */
    protected function definition_settings() {
        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'settingshdr', get_string('settings'));
        $mform->setExpanded('settingshdr');
        // Field settings.
        $this->field_definition();
    }

    /**
     * A hook method for field specific settings. Called from {@link dataformfieldform::definition_settings()}
     * so should not contain an opening header unless definition_settings has been overridden.
     *
     * @return void
     */
    protected function field_definition() {
    }

    /**
     * The field default content fieldset. Contains a header and calls the hook methods
     * {@link dataformfieldform::definition_default_settings()} and
     * {@link dataformfieldform::definition_default_content()}.
     *
     * @return void
     */
    protected function definition_defaults() {
        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'defaultcontenthdr', get_string('fielddefaultcontent', 'dataform'));
        $mform->setExpanded('defaultcontenthdr');
        // Settings.
        $this->definition_default_settings();
        // Content.
        $this->definition_default_content();
    }

    /**
     * A hook method for field default settings. Called from {@link dataformfieldform::definition_defaults()}
     * so should not contain an opening header unless definition_defaults has been overridden.
     *
     * @return void
     */
    protected function definition_default_settings() {
        $mform = &$this->_form;

        // Apply defaults.
        $options = array(
            // New entries only.
            dataformfield::DEFAULT_NEW => get_string('fielddefaultnew', 'dataform'),
            // Every empty content.
            dataformfield::DEFAULT_ANY => get_string('fielddefaultany', 'dataform'),
        );
        $mform->addElement('select', 'defaultcontentmode', get_string('fieldapplydefault', 'dataform'), $options);
    }

    /**
     * A hook method for field default content. Needs to be overridden in any field that displays
     * form element in entry editing mode. Called from {@link dataformfieldform::definition_defaults()}
     * so should not contain an opening header unless definition_defaults has been overridden.
     *
     * @return void
     */
    protected function definition_default_content() {
    }

    /**
     *
     */
    public function add_action_buttons($cancel = true, $submit = null) {
        $mform = &$this->_form;

        $buttonarray = array();
        // Save and display.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        // Save and continue.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savecont', 'dataform'));
        // Cancel.
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Adds elements for setting field size where applicable. The group of elements consists
     * of text input for the size number and a select element for units. Default units are px,
     * em and % in that order. It is possible to pass an array of alternate units via
     * $options['units']. The text element name is set with $name. The select element name is
     * $name. 'unit'. For example, width and widthunit. The content of the elements should be
     * set via {@link dataformfieldform::data_preprocessing()} and get via
     * {@link dataformfieldform::get_data()}.
     *
     * @param string $name The element name.
     * @param string $label The element label.
     * @param array $options Additional options (e.g. array of size units to override the default).
     * @return void
     */
    protected function add_field_size_elements($name, $label, array $options = null) {
        $mform = &$this->_form;

        if (empty($options['units'])) {
            $units = array(
                'px' => 'px',
                'em' => 'em',
                '%' => '%'
            );
        } else {
            $units = $options['units'];
        }

        $nameunit = $name. 'unit';

        $grp = array();
        $grp[] = &$mform->createElement('text', $name, null, array('size' => '8'));
        $grp[] = &$mform->createElement('select', $nameunit, null, $units);
        $mform->addGroup($grp, 'grp', $label, array(' '), false);
        $mform->setType($name, PARAM_TEXT);
        $mform->disabledIf($nameunit, $name, 'eq', '');
        $mform->setDefault($name, '');
    }

    /**
     * Returns a default field name for a new field.
     *
     * @return string
     */
    protected function get_default_field_name() {
        $field = $this->_field;
        $df = $field->df;
        $fieldname = $field->type;

        $i = 1;
        while ($df->name_exists('fields', $fieldname, $field->id)) {
            $fieldname = "$fieldname$i";
            $i++;
        }

        return $fieldname;
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     * A hook method for compiling field default content on saving field definition.
     * Needs to be overridden in any field which implements default content. Whatever
     * the field returns as default content is serialized and stored, and it is the field'
     * reponsibility to process it properly.
     * Called from {@link dataformfieldform::get_data()}.
     *
     * @param stdClass $data
     * @return mix|null
     */
    protected function get_data_default_content(\stdClass $data) {
        return null;
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            if ($content = $this->get_data_default_content($data)) {
                $data->defaultcontent = $content;
            } else {
                $data->defaultcontent = null;
            }
        }
        return $data;
    }

    /**
     * A hook method for validating field default content. Returns array of errors.
     * Should be overridden in any field whose default content depends on some settings.
     * Called from {@link dataformfieldform::validation()}.
     *
     * @param array The form data
     * @return array
     */
    protected function validation_default_content(array $data) {
        return array();
    }

    /**
     *
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $df = \mod_dataform_dataform::instance($this->_field->dataid);

        // Validate name.
        if ($df->name_exists('fields', $data['name'], $this->_field->id)) {
            $errors['name'] = get_string('invalidname', 'dataform', get_string('field', 'dataform'));
        }

        // Validate default content.
        if ($err = $this->validation_default_content($data)) {
            $errors = array_merge($errors, $err);
        }

        return $errors;
    }

}
