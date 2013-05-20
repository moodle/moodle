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

require_once($CFG->libdir.'/formslib.php');

class coursefiles_edit_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $contextid = $this->_customdata['contextid'];
        $options = array('subdirs'=>1, 'maxfiles'=>-1, 'accepted_types'=>'*');
        $mform->addElement('filemanager', 'files_filemanager', '', null, $options);
        $mform->addElement('hidden', 'contextid', $this->_customdata['contextid']);
        $mform->setType('contextid', PARAM_INT);
        $this->set_data($this->_customdata['data']);
        $this->add_action_buttons(true);
    }
}
