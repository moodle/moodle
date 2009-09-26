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


require_once($CFG->dirroot.'/lib/formslib.php');

class import_outcomes_form extends moodleform {

    function definition() {
        global $COURSE, $USER;

        $mform =& $this->_form;
        //$this->set_upload_manager(new upload_manager('importfile', false, false, null, false, 0, true, true, false));

        $mform->addElement('hidden', 'action', 'upload');
        $mform->setType('action', PARAM_ACTION);
        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

        $scope = array();
        if (($COURSE->id > 1) && has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->addElement('radio', 'scope', get_string('importcustom', 'grades'), null, 'custom');
            $mform->addElement('radio', 'scope', get_string('importstandard', 'grades'), null, 'global');
            $mform->setDefault('scope', 'custom');
        }

        $mform->addElement('file', 'userfile', get_string('importoutcomes', 'grades'));
        $mform->setHelpButton('userfile', array('importoutcomes', get_string('importoutcomes', 'grades'), 'grade'));

        $mform->addElement('submit', 'save', get_string('uploadthisfile'));

    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>
