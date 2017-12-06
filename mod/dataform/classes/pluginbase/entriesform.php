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
 * @package dataformview
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

require_once("$CFG->libdir/formslib.php");

/**
 *
 */
class entriesform extends \moodleform {

    public function definition() {

        // Entries.
        $this->definition_entries();

        // Buttons.
        $this->add_action_buttons();
    }

    /**
     * Adds elements for the given entries.
     *
     * @return void
     */
    protected function definition_entries() {
        $view = $this->_customdata['view'];
        $elements = !empty($this->_customdata['elements']) ? $this->_customdata['elements'] : array();
        $mform =& $this->_form;

        // Header.
        $mform->addElement('header', 'entrieshdr', null);

        $htmlcontent = '';
        foreach ($elements as $element) {
            if (!empty($element)) {
                if (!is_array($element)) {
                    // Collect consecutive html to reduce number of elements.
                    $htmlcontent .= $element;
                } else {
                    // Add html element for html content and reset the var.
                    if ($htmlcontent) {
                        $mform->addElement('html', $htmlcontent);
                        $htmlcontent = '';
                    }
                    // If the element is an array, it contains a function.
                    list($func, $params) = $element;
					call_user_func_array($func, array_merge(array(&$mform), $params));
                }
            }
        }
        if ($htmlcontent) {
            $mform->addElement('html', $htmlcontent);
        }
    }

    /**
     *
     */
    public function add_action_buttons($cancel = true, $submit = null) {
        $view = $this->_customdata['view'];
        $mform = &$this->_form;

        static $i = 0;
        $i++;

        $buttons = $view->submission_buttons;
        $settings = $view->submission_settings;

        $arr = array();
        foreach ($buttons as $name) {
            if (!$settings or !is_array($settings) or !array_key_exists($name, $settings)) {
                continue;
            }

            $type = ($name == 'cancel' ? 'cancel' : 'submit');
            $elemname = "submitbutton_$name";
            $label = !empty($settings[$name]) ? $settings[$name] : get_string("{$name}button", 'dataform');
            $arr[] = &$mform->createElement($type, $elemname, $label);
        }
        if ($arr) {
            $mform->addGroup($arr, 'buttonarr', null, ' ', false);
            $mform->closeHeaderBefore('buttonar');
        }
    }

    /**
     *
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Field validations.
        $view = $this->_customdata['view'];
        $patterns = $view->get_pattern_set('field');
        $fields = $view->get_fields();
        $entryids = explode(',', $this->_customdata['update']);

        foreach ($entryids as $eid) {
            // Validate all fields for this entry.
            foreach ($fields as $fid => $field) {
                // Captcha check.
                if ($field->type == 'captcha') {
                    if ($err = $field->verify($eid, $mform)) {
                        $errors = array_merge($errors, $err);
                    }
                } else if ($err = $field->validate($eid, $patterns[$fid], (object) $data)) {
                    $errors = array_merge($errors, $err);
                }
            }
        }

        return $errors;
    }

}
