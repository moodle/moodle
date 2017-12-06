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
 * @subpackage time
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_time_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform =& $this->_form;

        // Date.
        $mform->addElement('checkbox', 'param1', get_string('dateonly', 'dataformfield_time'));
        $mform->addHelpButton('param1', 'dateonly', 'dataformfield_time');

        // Masked.
        $mform->addElement('checkbox', 'param5', get_string('masked', 'dataformfield_time'));
        $mform->addHelpButton('param5', 'masked', 'dataformfield_time');

        // Start year.
        $mform->addElement('text', 'param2', get_string('startyear', 'dataformfield_time'));
        $mform->setType('param2', PARAM_INT);
        $mform->addRule('param2', null, 'numeric', null, 'client');
        $mform->addRule('param2', null, 'maxlength', 4, 'client');
        $mform->addHelpButton('param2', 'startyear', 'dataformfield_time');

        // End year.
        $mform->addElement('text', 'param3', get_string('stopyear', 'dataformfield_time'));
        $mform->setType('param3', PARAM_INT);
        $mform->addRule('param3', null, 'numeric', null, 'client');
        $mform->addRule('param3', null, 'maxlength', 4, 'client');
        $mform->addHelpButton('param3', 'stopyear', 'dataformfield_time');

        // Display format.
        $mform->addElement('text', 'param4', get_string('displayformat', 'dataformfield_time'));
        $mform->setType('param4', PARAM_TEXT);
        $mform->addHelpButton('param4', 'displayformat', 'dataformfield_time');
    }

    /**
     * The field default content fieldset. Override parent to display no defaults.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

}
