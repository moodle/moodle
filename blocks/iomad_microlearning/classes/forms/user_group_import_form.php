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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

use \moodleform;
use \csv_import_reader;
use \core_text;

/**
 * Script to let a user import departments to a particular company.
 */

class user_group_import_form extends moodleform {

    function definition() {
        global $CFG;

        // thing you have to do
        $mform =& $this->_form;

        // header for main bit
        $mform->addElement( 'header', 'general', get_string('importgroupsfromfile','block_iomad_microlearning'));

        // file picker
        $mform->addElement('filepicker', 'importfile', get_string('file'), null, array( 'accepted_types'=>'csv'));
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('hidden', 'fileimport');
        $mform->setType('fileimport', PARAM_BOOL);

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        // buttons
        $this->add_action_buttons();
    }
}

class completion_import_form2 extends moodleform {

    function definition() {
        global $CFG;

        // thing you have to do
        $mform =& $this->_form;

        // header for main bit
        $mform->addElement( 'header', 'general', get_string('importgroupsfromfile','block_iomad_microlearning'));

        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_BOOL);
        $mform->setType('fileimport', PARAM_BOOL);
        $mform->addElement('hidden', 'fileimport');

        // buttons
        $this->add_action_buttons();
    }
}

