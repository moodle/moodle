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
 * @package dataformfield_checkbox
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_checkbox_renderer extends dataformfield_selectmulti_renderer {

    /**
     *
     */
    protected function render(&$mform, $fieldname, $options, $selected, $required = false) {
        $field = $this->_field;

        $grp = array();
        foreach ($options as $i => $option) {
            $cb = &$mform->createElement('advcheckbox', $fieldname. '_'. $i, null, $option, array('group' => $fieldname), array(0, $i));
            $grp[] = $cb;
            if (in_array($i, $selected)) {
                $cb->setChecked(true);
            }
        }
        // Add checkbox controller.

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
            'name' => 'M.dataformfield_checkbox_required',
            'fullpath' => '/mod/dataform/field/checkbox/checkbox.js',
            'requires' => array('base', 'node')
        );

        $PAGE->requires->js_init_call('M.dataformfield_checkbox_required.init', array($options), false, $module);
    }

    /**
     *
     */
    public function validate_data($entryid, $tags, $data) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        $formfieldname = "field_{$fieldid}_{$entryid}_selected";

        // Only [[$fieldname]] is editable so check it if exists.
        if (in_array("[[*$fieldname]]", $tags)) {
            $emptyfield  = true;
            foreach ($field->options_menu() as $key => $unused) {
                $formelementname = "{$formfieldname}_$key";
                if (!empty($data->$formelementname)) {
                    $emptyfield = false;
                    break;
                }
            }
            if ($emptyfield) {
                return array($fieldname, get_string('fieldrequired', 'dataform'));
            }
        }
        return null;
    }

}
