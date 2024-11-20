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
 * Instance settings form
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Class mod_realtimequiz_mod_form
 */
class mod_realtimequiz_mod_form extends moodleform_mod {

    /**
     * Form definition
     * @throws coding_exception
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('modulename', 'realtimequiz'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if ($CFG->branch < 29) {
            $this->add_intro_editor(true, get_string('realtimequizintro', 'realtimequiz'));
        } else {
            $this->standard_intro_elements(get_string('realtimequizintro', 'realtimequiz'));
        }

        $mform->addElement('header', 'realtimequizsettings', get_string('realtimequizsettings', 'realtimequiz'));

        $mform->addElement('text', 'questiontime', get_string('questiontime', 'realtimequiz'));
        $mform->addRule('questiontime', null, 'numeric', null, 'client');
        $mform->setDefault('questiontime', 30);
        $mform->setType('questiontime', PARAM_INT);
        $mform->addHelpButton('questiontime', 'questiontime', 'realtimequiz');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
