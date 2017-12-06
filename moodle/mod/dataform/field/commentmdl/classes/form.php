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
 * @package dataformfield_commentmdl
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_commentmdl_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function definition_general() {
        parent::definition_general();

        $mform =& $this->_form;

        // Restrict name to alphanumeric.
        $mform->addRule('name', null, 'alphanumeric', null, 'client');
    }

    /**
     * The field settings fieldset. Overrides parent to omit.
     *
     * @return void
     */
    protected function definition_settings() {
    }

    /**
     * The field default content fieldset. Override parent to display no defaults.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

    /**
     *
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Name must be lower case.
        if (strtolower($data['name']) != $data['name']) {
            $errors['name'] = get_string('err_lowername', 'dataform');
        }

        return $errors;
    }

}
