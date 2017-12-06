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
 * @subpackage radiobutton
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_radiobutton_renderer extends dataformfield_select_renderer {

    /**
     *
     */
    protected function render(&$mform, $fieldname, $options, $selected, $required = false) {
        $field = $this->_field;

        $grp = array();
        $separators = array();
        foreach ($options as $key => $option) {
            $grp[] = &$mform->createElement('radio', $fieldname, $field->separator, $option, $key);
        }
        if (!empty($selected)) {
            $mform->setDefault($fieldname, (int) $selected);
        }
        return array($grp, array($field->separator));
    }

    /**
     *
     */
    protected function set_required(&$mform, $fieldname, $selected) {
        global $PAGE;

        $mform->addRule($fieldname, null, 'required', null, 'client');
        // JS Error message.
        $options = array(
            'fieldname' => $fieldname,
            'message' => get_string('err_required', 'form'),
        );

        $module = array(
            'name' => 'M.dataformfield_radiobutton_required',
            'fullpath' => '/mod/dataform/field/radiobutton/radiobutton.js',
            'requires' => array('base', 'node')
        );

        $PAGE->requires->js_init_call('M.dataformfield_radiobutton_required.init', array($options), false, $module);
    }

}
