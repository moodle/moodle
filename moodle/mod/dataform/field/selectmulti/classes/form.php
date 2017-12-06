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
 * @package dataformfield_selectmulti
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_selectmulti_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform = &$this->_form;
        $field = $this->_field;

        // Options.
        $label = get_string('options', 'dataformfield_selectmulti');
        $mform->addElement('textarea', 'param1', $label, 'wrap="virtual" rows="10" cols="50"');

        // Options separator.
        $label = get_string('optionsseparator', 'dataformfield_selectmulti');
        $mform->addElement('select', 'param3', $label, array_map('current', $field->separator_types));

        // Allow add option.
        $label = get_string('allowaddoption', 'dataformfield_selectmulti');
        $mform->addElement('selectyesno', 'param4', $label);

    }

    /**
     *
     */
    public function definition_default_content() {
        $mform = &$this->_form;
        $field = &$this->_field;

        // Content elements.
        $label = get_string('fielddefaultvalue', 'dataform');
        $options = $field->options_menu();
        $select = $mform->addElement('select', 'contentdefault', $label, $options);
        $select->setMultiple(true);
        $mform->disabledIf('contentdefault', 'param1', 'eq', '');
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $field = &$this->_field;

        // Default content.
        $data->contentdefault = $field->default_content;
    }

    /**
     * Returns the default content data.
     *
     * @param stdClass $data
     * @return mix|null
     */
    protected function get_data_default_content(\stdClass $data) {
        if (!empty($data->contentdefault)) {
            return implode("\n", $data->contentdefault);
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
            $errmsg = get_string('invaliddefaultvalue', 'dataformfield_select');
            $options = !empty($data['param1']) ? explode("\n", $data['param1']) : null;
            // The default cannot be a valid option if there are no options.
            if (!$options) {
                $errors['contentdefault'] = $errmsg;
            }
            foreach ($data['contentdefault'] as $key) {
                // The default must be a valid option.
                if ($key > count($options)) {
                    $errors['contentdefault'] = get_string('invaliddefaultvalue', 'dataformfield_select');
                    break;
                }
            }
        }

        return $errors;
    }

}
