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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class upload_form extends moodleform {

    function definition() {
        
        $mform = $this->_form;
        $mform->addElement('hidden', 'idfile', true);
        $mform->setType('idfile', PARAM_TEXT);

        // File Manager.
        $mform->addElement('filemanager', 'pu_file', format_string('File Manager'), 
                null, $this->get_filemanager_options_array());

        // Buttons.
        $this->add_action_buttons();
    }

    /**Set here the options available for your file manager
     * https://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms
     * @return options for file manager
     */
    function get_filemanager_options_array () {
        return array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1,
                'accepted_types' => array('*'));
    }
}
