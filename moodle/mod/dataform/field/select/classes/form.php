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
 * @subpackage select
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_select_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {
        $mform =& $this->_form;

        // Options.
        $attrs = 'wrap="virtual" rows="5" cols="30"';
        $mform->addElement('textarea', 'param1', get_string('options', 'dataformfield_select'), $attrs);

        // Reserve param3 for options separator (e.g. radiobutton, image button).

        // Allow add option.
        $mform->addElement('selectyesno', 'param4', get_string('allowaddoption', 'dataformfield_select'));

    }
    /**
     *
     */
    public function definition_default_content() {
        $mform = &$this->_form;
        $field = &$this->_field;

        // Content elements.
        $label = get_string('fielddefaultvalue', 'dataform');
        $options = array('' => get_string('choosedots')) + $field->options_menu();
        $mform->addElement('select', 'contentdefault', $label, $options);
        $mform->disabledIf('contentdefault', 'param1', 'eq', '');
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $field = &$this->_field;

        // Default content.
        $data->contentdefault = $field->defaultcontent;
    }

    /**
     * Returns the default content data.
     *
     * @param stdClass $data
     * @return mix|null
     */
    protected function get_data_default_content(\stdClass $data) {
        if (!empty($data->contentdefault)) {
            return $data->contentdefault;
        }
        return null;
    }

    /**
     * A hook method for validating field default content. Returns list of errors.
     *
     * @param array The form data
     * @return void
     */
    protected function validation_default_content(array $data) {
        $errors = array();

        if (!empty($data['contentdefault'])) {
            $selected = $data['contentdefault'];
            // Get the options.
            if (!empty($data['param1'])) {
                $options = explode("\n", $data['param1']);
            } else {
                $options = null;
            }

            // The default must be a valid option.
            if (!$options or $selected > count($options)) {
                $errors['contentdefault'] = get_string('invaliddefaultvalue', 'dataformfield_select');
            }
        }

        return $errors;
    }
}
