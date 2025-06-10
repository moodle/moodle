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

require_once $CFG->libdir.'/formslib.php';

class grade_import_form extends moodleform {
    function definition () {
        global $COURSE, $USER, $CFG, $DB;

        $mform =& $this->_form;

		//SC: ID is required in the form 	
        // course id needs to be passed for auth purposes
        
        //$mform->addElement('html', '<div class="qheader">');
                
        //$mform->addElement('header', 'general', get_string('importfile', 'grades'));
        $mform->addElement('hidden', 'id', optional_param('id', 0, PARAM_INT));
        $mform->setType('id', PARAM_INT);
	
		//TODO: User appropriate get_string
        //$this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
        $this->add_action_buttons(false, 'Sync MyLab & Mastering Grades');
    }

    function validation($data, $files) {
        $err = parent::validation($data, $files);
        if (empty($data['url']) and empty($data['userfile'])) {
            if (array_key_exists('url', $data)) {
                $err['url'] = get_string('required');
            }
            if (array_key_exists('userfile', $data)) {
                $err['userfile'] = get_string('required');
            }

        } else if (array_key_exists('url', $data) and $data['url'] != clean_param($data['url'], PARAM_URL)) {
            $err['url'] = get_string('error');
        }

        return $err;
    }
}

