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
 * Version details
 *
 * @package    block_mediasearch
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/formslib.php');

class block_mediasearch_entry_edit_form extends moodleform {

    public function definition() {
        global $CFG, $DB;

        // Add the form elements.
        $mform =& $this->_form;

        $mform->addElement('header', 'entry_edit_header', get_string('entry_edit_header', 'block_mediasearch'));
        $mform->addElement('text', 'title', get_string('entry_title', 'block_mediasearch'));
        $mform->setType('title', PARAM_NOTAGS);
        $mform->addRule('title', get_string('required'), 'required');

        $mform->addElement('textarea', 'description', get_string('entry_description', 'block_mediasearch'));
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', get_string('required'), 'required');

        $mform->addElement('text', 'link', get_string('entry_link', 'block_mediasearch'));
        $mform->setType('link', PARAM_NOTAGS);
        $mform->addRule('link', get_string('required'), 'required');

        // Add the course select.
        $courses = $DB->get_records_menu('course', array(), null, 'id,fullname');
        $mform->addElement('select', 'courseid', get_string('entry_course', 'block_mediasearch'), $courses);

        $mform->addElement('textarea', 'keywords', get_string('entry_keywords', 'block_mediasearch'));
        $mform->setType('keywords', PARAM_NOTAGS);
        $mform->addRule('keywords', get_string('required'), 'required');
        
        // Add action buttons.
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                            get_string('createentry', 'block_mediasearch'));
        $buttonarray[] = &$mform->createElement('submit', 'submitandagainbutton',
                            get_string('createagain', 'block_mediasearch'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}
