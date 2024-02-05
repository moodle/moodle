<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_message
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

require_once("$CFG->libdir/formslib.php");

class edit extends moodleform {
    // Add elements to form.
    public function definition() {
        //global $CFG;
        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!

        // Add elements to your form.
        $mform->addElement('text', 'messagetext', get_string('message_text', 'loca l_message'));

        // Set type of element.
        $mform->setType('messagetext', PARAM_NOTAGS);

        // Default value.
        $mform->setDefault('messagetext', get_string('enter_message', 'local_message'));

        // Init choices
        $choices =  array();
        $choices['0'] = \core\output\notification::NOTIFY_WARNING;
        $choices['1'] = \core\output\notification::NOTIFY_SUCCESS;
        $choices['2'] = \core\output\notification::NOTIFY_ERROR;
        $choices['3'] = \core\output\notification::NOTIFY_INFO;

        $mform->addElement('select', 'messagetype', get_string('message_type', 'local_message'), $choices);
        $mform->setDefault('messagetype', '3');

        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}