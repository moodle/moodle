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
 * @package dataformfield_duration
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_duration_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform = &$this->_form;

        // Displayed units.
        $units = array(
            604800 => get_string('weeks'),
            86400 => get_string('days'),
            3600 => get_string('hours'),
            60 => get_string('minutes'),
            1 => get_string('seconds'),
        );
        $select = $mform->addElement('select', 'param4', get_string('limitunitsto', 'dataformfield_duration'), $units);
        $select->setMultiple(true);

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

    /**
     *
     */
    public function data_preprocessing(&$data) {
        if (!empty($data->param4)) {
            $data->param4 = explode(',', $data->param4);
        }
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     *
     */
    public function get_data() {
        $field = $this->_field;

        if ($data = parent::get_data()) {
            // Limit units to (param4).
            if (!empty($data->param4)) {
                $data->param4 = implode(',', $data->param4);
            } else {
                $data->param4 = null;
            }
        }
        return $data;
    }

}
