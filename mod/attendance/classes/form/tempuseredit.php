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
 * Form for editing temporary users.
 *
 * @package    mod_attendance
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_attendance\form;

defined('MOODLE_INTERNAL') || die();

/**
 * class for displaying tempedit form.
 *
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tempuseredit extends \moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'userid', 0);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'attheader', get_string('tempusersedit', 'attendance'));
        $mform->addElement('text', 'tname', get_string('tusername', 'attendance'));
        $mform->addRule('tname', 'Required', 'required', null, 'client');
        $mform->setType('tname', PARAM_TEXT);

        $mform->addElement('text', 'temail', get_string('tuseremail', 'attendance'));
        $mform->addRule('temail', 'Email', 'email', null, 'client');
        $mform->setType('temail', PARAM_EMAIL);

        $buttonarray = array(
            $mform->createElement('submit', 'submitbutton', get_string('edituser', 'attendance')),
            $mform->createElement('cancel'),
        );
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('submit');
    }

    /**
     * Apply filter to form
     *
     */
    public function definition_after_data() {
        $mform = $this->_form;
        $mform->applyFilter('tname', 'trim');
    }

    /**
     * Perform validation on the form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($err = \mod_attendance_structure::check_existing_email($data['temail'], $data['userid'])) {
            $errors['temail'] = $err;
        }
        return $errors;
    }
}
