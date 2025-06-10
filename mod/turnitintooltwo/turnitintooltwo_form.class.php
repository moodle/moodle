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
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir."/formslib.php");

class turnitintooltwo_form extends moodleform {

    /**
     * The form definition contains a customdata array. This contains all the elements as an array,
     * each element is passed with data in the following format:
     * array(type e.g. "text", id/name of element, label string, string identifier for help text,
     *          options (e.g. for select, advcheckbox or file upload), rule, rule error msg, param_type,
     *          array for disabled if rule (dependent field, condition, value)
     *
     * @global type $CFG
     */
    public function definition() {
        $mform =& $this->_form;

        foreach ($this->_customdata["elements"] as $element) {
            switch ($element[0]) {
                case "static":
                case "select":
                case "date_time_selector":
                case "date_selector":
                    $mform->addElement($element[0], $element[1], $element[2], $element[4]);
                    break;
                case "filemanager":
                    $mform->addElement($element[0], $element[1], $element[2], '', $element[4]);
                    break;
                case "html":
                    $mform->addElement($element[0], $element[1]);
                    break;
                case "advcheckbox":
                    $labelbefore = $element[2];
                    $labelafter = null;
                    if (!empty($this->_customdata["checkbox_label_after"])) {
                        $labelbefore = null;
                        $labelafter = $element[2];
                    }
                    $mform->addElement($element[0], $element[1], $labelbefore, $labelafter, null, $element[4]);
                    break;
                case "hidden":
                case "text":
                    $mform->addElement($element[0], $element[1], $element[2]);
                    $mform->setType($element[1], PARAM_RAW);
                    break;
                default:
                    $mform->addElement($element[0], $element[1], $element[2]);
                    break;
            }

            // Set form data. Only used for submission form.
            if (isset($_SESSION['form_data']->{$element[1]})) {
                $mform->setDefault($element[1], $_SESSION['form_data']->{$element[1]});
            }

            if (!empty($element[3])) {
                $mform->addHelpButton($element[1], $element[3], 'turnitintooltwo');
            }

            if (!empty($element[5])) {
                $mform->setType($element[1], PARAM_TEXT);
                $mform->addRule($element[1], $element[6], $element[5], null, 'client');
            }

            if (!empty($element[7])) {
                $disabledif = $element[7];
                $mform->disabledIf($element[1], $disabledif[0], $disabledif[1], $disabledif[2]);
            }
        }

        // Apply a class to the form if specified.
        if (isset($this->_customdata["class"])) {
            $mform->_formname = $this->_customdata["class"];
        }

        // Show the moodleform standard submit and cancel buttons.
        if (!isset($this->_customdata["hide_submit"])) {
            $submitlabel = null;
            if (isset($this->_customdata["submit_label"])) {
                $submitlabel = $this->_customdata["submit_label"];
            }
            if (!isset($this->_customdata["show_cancel"])) {
                $this->_customdata["show_cancel"] = "true";
            }

            $this->add_action_buttons($this->_customdata["show_cancel"], $submitlabel);
        }

        // Disable the form change checker - added in 2.3.2.
        if (is_callable(array($mform, 'disable_form_change_checker'))) {
            if (isset($this->_customdata["disable_form_change_checker"])) {
                $mform->disable_form_change_checker();
            }
        }

        // Show multiple submit buttons if needed.
        if (isset($this->_customdata["multi_submit_buttons"])) {
            $buttonarray = array();
            foreach ($this->_customdata["multi_submit_buttons"] as $btn) {
                $buttonarray[] = &$mform->createElement('submit', $btn[0], $btn[1]);
            }

            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        }
    }

    /**
     * Display the form, saving the contents of the output buffer overiding Moodle's
     * display function that prints to screen when called
     *
     * @return the form as an object to print to screen at our convenience
     */
    public function display() {
        ob_start();
        parent::display();
        $form = ob_get_contents();
        ob_end_clean();

        return $form;
    }
}

class turnitin_plagiarism_plugin_form extends moodleform {

    // Define the form.
    public function definition () {
        global $CFG;

        $mform =& $this->_form;

        require_once($CFG->dirroot.'/plagiarism/turnitin/turnitinplugin_view.class.php');

        $turnitinpluginview = new turnitinplugin_view();
        $turnitinpluginview->add_elements_to_settings_form($mform, array(), "defaults");

        $this->add_action_buttons(true);
    }
}