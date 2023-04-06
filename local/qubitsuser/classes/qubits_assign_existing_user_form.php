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
 * Local plugin "QubitsCourse"
 *
 * @package   local_qubitscourse
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');


class qubits_assign_existing_user_form extends moodleform {

    public function definition(){
        global $CFG, $DB;

        $mform    = $this->_form;
        $context = $this->_customdata['context']; // this contains the data of this form
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];
        $this->siteid = $this->_customdata['siteid'];

        $mform->addElement('hidden', 'siteid', $this->siteid);
        $mform->setType('siteid', PARAM_INT);

        $strrequired = get_string('required');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
        $mform->addRule('email', $strrequired, 'required', null, 'client');
        $mform->setType('email', PARAM_EMAIL);

        // add action buttons
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitandback',
                            get_string('submit'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    public function validation($usernew, $files) {
        global $CFG, $DB;
        $errors = parent::validation($usernew, $files);
        $usernew = (object)$usernew;
        $existuser = $DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id));
        // Validate email.
        if (empty($existuser)) {
            $errors['email'] = get_string('emailnotexists');
        }
        return $errors;
    }

}