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
 * @package dataformfield_url
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_url_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform =& $this->_form;

        // Use url picker.
        $mform->addElement('selectyesno', 'param1', get_string('usepicker', 'dataformfield_url'));

        // Force link name.
        $mform->addElement('text', 'param2', get_string('forcename', 'dataformfield_url'), array('size' => '32'));
        $mform->setType('param2', PARAM_TEXT);
    }

    /**
     * The field default content fieldset. Override parent to display no defaults.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

}
