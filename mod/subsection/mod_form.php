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
 * The main mod_subsection configuration form.
 *
 * @package     mod_subsection
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use mod_subsection\manager;

/**
 * Module instance settings form.
 *
 * @package     mod_subsection
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_subsection_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        // Showing edit form. Redirect to the edit section page.
        if (!empty($this->current->instance)) {
            $manager = manager::create_from_id($this->current->course, $this->current->id);
            $editurl = new moodle_url('/course/editsection.php', ['id' => $manager->get_delegated_section_info()->id]);
            redirect($editurl->out());
        } else {
            $mform = $this->_form;

            // Adding the "general" fieldset, where all the common settings are shown.
            $mform->addElement('header', 'general', get_string('general', 'form'));

            // Adding the standard "name" field.
            $mform->addElement('text', 'name', get_string('subsectionname', 'mod_subsection'), ['size' => '64']);

            if (!empty($CFG->formatstringstriptags)) {
                $mform->setType('name', PARAM_TEXT);
            } else {
                $mform->setType('name', PARAM_CLEANHTML);
            }

            $mform->addRule('name', null, 'required', null, 'client');
            $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

            // Add standard elements.
            $this->standard_coursemodule_elements();

            // Add standard buttons.
            $this->add_action_buttons();

            // Show only general and restrictions form sections.
            $mform->filter_shown_headers(['general', 'availabilityconditionsheader']);
        }
    }
}
