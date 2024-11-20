<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_jupyter configuration form.
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_jupyter_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('jupytername', 'mod_jupyter'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        // Setting rules for the input fields above.
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'jupytername', 'mod_jupyter');

        if ($this->_instance == '' || !$this->current->notebook_ready) {
            // Adding file manager for jupyter notebook file.
            $mform->addElement('filemanager', 'packagefile', get_string('package', 'mod_jupyter'), null, [
            'accepted_types' => '.ipynb',
            'maxbytes' => 0,
            'maxfiles' => 1,
            'subdirs' => 0,
            ]);
            $mform->addHelpButton('packagefile', 'package', 'mod_jupyter');
            $mform->addRule('packagefile', null, 'required');

            // Adding checkbox for whether the assignment should be auto-graded.
            $mform->addElement('advcheckbox', 'autograded', 'Auto-Grading', get_string('autograding', 'mod_jupyter'), '',
                array(0, 1));
            $mform->setDefault('autograded', 1);
        }

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons(true, null, false);
    }

    /**
     * Enforce defaults here.
     *
     * @param array $defaultvalues From defaults
     * @return void
     */
    public function data_preprocessing(&$defaultvalues) {
        // Load existing notebook file into file manager draft area.
        $draftitemid = file_get_submitted_draft_itemid('packagefile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_jupyter',
            'package', 0, ['subdirs' => 0, 'maxfiles' => 1]);
        $defaultvalues['packagefile'] = $draftitemid;
    }
}
