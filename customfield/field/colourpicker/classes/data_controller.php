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
 * Customfields colourpicker field plugin
 *
 * @package   customfield_colourpicker
 * @copyright 2023 Qubits Dev Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_colourpicker;

defined('MOODLE_INTERNAL') || die;

use core_customfield\api;

/**
 * Class data
 *
 * @package customfield_colourpicker
 * @copyright 2023 Qubits Dev Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the name of the field where the information is stored
     * @return string
     */
    public function datafield() : string {
        return 'charvalue';
    }

    /**
     * Add fields for editing a text field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition(\MoodleQuickForm $mform) {
        global $PAGE;
        // Color Picker Code
        $field = $this->get_field();
        $config = $field->get('configdata');
        $type = $config['ispassword'] ? 'password' : 'text';
        $elementname = $this->get_form_element_name();
        $fldid = 'id_'.$elementname;

        $mform->addElement('html', '<div class="form-colourpicker defaultsnext row"><div class="col-md-3">&nbsp;</div>');
        $mform->addElement('html', '<div class="col-md-9"><div class="admin_colourpicker" id="acp_'.$fldid.'">');
        $mform->addElement('html', '<img class="icon loadingicon" alt="Loading" title="Loading" src="">');
        $mform->addElement('html', '</div></div></div>');
        
        $PAGE->requires->js('/customfield/field/colourpicker/script.js');
        $PAGE->requires->js_init_call('M.util.init_cstm_colour_picker', array($fldid, null));
        
        //$module = array('name'=>'form_url', 'fullpath'=>'/lib/form/url.js', 'requires'=>array('core_filepicker'));
        //$PAGE->requires->js_init_call('M.form_url.init', array($fldid, null), true, $module);

        
        $mform->addElement($type, $elementname, $this->get_field()->get_formatted_name(), 'id="'.$fldid.'" size=' . (int)$config['displaysize']);
        $mform->setType($elementname, PARAM_TEXT);
        if (!empty($config['defaultvalue'])) {
            $mform->setDefault($elementname, $config['defaultvalue']);
        }
        if ($field->get_configdata_property('required')) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }

    }

    /**
     * Validates data for this field.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function instance_form_validation(array $data, array $files) : array {

        $errors = parent::instance_form_validation($data, $files);
        $maxlength = $this->get_field()->get_configdata_property('maxlength');
        $elementname = $this->get_form_element_name();
        if (($maxlength > 0) && ($maxlength < \core_text::strlen($data[$elementname]))) {
            $errors[$elementname] = get_string('errormaxlength', 'customfield_colourpicker', $maxlength);
        }
        return $errors;
    }

    /**
     * Returns the default value as it would be stored in the database (not in human-readable format).
     *
     * @return mixed
     */
    public function get_default_value() {
        return $this->get_field()->get_configdata_property('defaultvalue');
    }

    /**
     * Returns value in a human-readable format
     *
     * @return mixed|null value or null if empty
     */
    public function export_value() {
        $value = parent::export_value();
        if ($value === null) {
            return null;
        }

        $link = $this->get_field()->get_configdata_property('link');
        if ($link) {
            $linktarget = $this->get_field()->get_configdata_property('linktarget');
            $url = str_replace('$$', urlencode($this->get_value()), $link);
            $attributes = $linktarget ? ['target' => $linktarget] : [];
            $value = \html_writer::link($url, $value, $attributes);
        }

        return $value;
    }
}
