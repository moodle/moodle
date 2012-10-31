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
 * minimalistic edit form
 *
 * @package   block_private_files
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class user_files_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        $data           = $this->_customdata['data'];
        $options        = $this->_customdata['options'];

        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
        $mform->addElement('hidden', 'returnurl', $data->returnurl);

        $this->add_action_buttons(true, get_string('savechanges'));

        $this->set_data($data);
    }
    function validation($data, $files) {
        global $CFG;

        $errors = array();
        $draftitemid = $data['files_filemanager'];
        if (file_is_draft_area_limit_reached($draftitemid, $this->_customdata['options']['areamaxbytes'])) {
            $errors['files_filemanager'] = get_string('userquotalimit', 'error');
        }

        return $errors;
    }
}
