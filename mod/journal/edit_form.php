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
 * This page lists all the instances of journal in a particular course
 *
 * @package mod_journal
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mod_journal_entry_form class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_journal_entry_form extends moodleform {

    /**
     * Set up the form definition.
     */
    public function definition() {
        $this->_form->addElement('editor', 'text_editor', get_string('entry', 'mod_journal'),
                null, $this->_customdata['editoroptions']);
        $this->_form->setType('text_editor', PARAM_RAW);
        $this->_form->addRule('text_editor', null, 'required', null, 'client');
        $this->_form->addElement('hidden', 'id');
        $this->_form->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }
}
