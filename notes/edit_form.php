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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

class note_edit_form extends moodleform {

    /**
     * Define the form for editing notes
     */
    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('note', 'notes'));

        $mform->addElement('textarea', 'content', get_string('content', 'notes'), array('rows' => 15, 'cols' => 40));
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('nocontent', 'notes'), 'required', null, 'client');

        $mform->addElement('select', 'publishstate', get_string('publishstate', 'notes'), note_get_state_names());
        $mform->setDefault('publishstate', NOTES_STATE_PUBLIC);
        $mform->setType('publishstate', PARAM_ALPHA);
        $mform->addHelpButton('publishstate', 'publishstate', 'notes');

        $this->add_action_buttons();

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
    }
}
