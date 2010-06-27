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
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class import_outcomes_form extends moodleform {

    public function definition() {
        global $PAGE, $USER;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'action', 'upload');
        $mform->setType('action', PARAM_ACTION);
        $mform->addElement('hidden', 'courseid', $PAGE->course->id);
        $mform->setType('id', PARAM_INT);

        $scope = array();
        if (($PAGE->course->id > 1) && has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->addElement('radio', 'scope', get_string('importcustom', 'grades'), null, 'custom');
            $mform->addElement('radio', 'scope', get_string('importstandard', 'grades'), null, 'global');
            $mform->setDefault('scope', 'custom');
        }

        $mform->addElement('filepicker', 'userfile', get_string('importoutcomes', 'grades'));
        $mform->addRule('userfile', get_string('required'), 'required', null, 'server');
        $mform->addHelpButton('userfile', 'importoutcomes', 'grades');

        $mform->addElement('submit', 'save', get_string('uploadthisfile'));

    }
}


