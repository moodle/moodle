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
 * @package dataformfield_number
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_number_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform =& $this->_form;

        // Decimals.
        $options = array('' => 0) + array_combine(range(1, 10), range(1, 10));
        $mform->addElement('select', 'param1', get_string('decimals', 'dataformfield_number'), $options);

        // Field width.
        $fieldwidthgrp = array();
        $fieldwidthgrp[] = &$mform->createElement('text', 'param2', null, array('size' => '8'));
        $fieldwidthgrp[] = &$mform->createElement('select', 'param3', null, array('px' => 'px', 'em' => 'em', '%' => '%'));
        $mform->addGroup($fieldwidthgrp, 'fieldwidthgrp', get_string('fieldwidth', 'dataform'), array(' '), false);
        $mform->setType('param2', PARAM_INT);
        $mform->addGroupRule('fieldwidthgrp', array('param2' => array(array(null, 'numeric', null, 'client'))));
        $mform->disabledIf('param3', 'param2', 'eq', '');
        $mform->setDefault('param2', '');
        $mform->setDefault('param3', 'px');
    }

    /**
     * The field default content fieldset. Override parent to display no defaults.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

}
