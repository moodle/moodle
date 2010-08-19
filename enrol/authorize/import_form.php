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
 * Adds new instance of enrol_authorize to specified course
 * or edits current instance.
 *
 * @package    enrol
 * @subpackage authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir.'/formslib.php');

class enrol_authorize_import_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;

        $mform->addElement('filepicker', 'csvfile', get_string('filetoimport', 'glossary'));
        $submit_string = get_string('submit');
        $this->add_action_buttons(false, $submit_string);
    }
}
