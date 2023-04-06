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


class qubits_adduser_form extends moodleform {

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

        // Deal with the name order sorting and required fields.
        $necessarynames = useredit_get_required_name_fields();
        foreach ($necessarynames as $necessaryname) {
            $mform->addElement('text', $necessaryname, get_string($necessaryname), 'maxlength="100" size="30"');
            $mform->addRule($necessaryname, $strrequired, 'required', null, 'client');
            $mform->setType($necessaryname, PARAM_NOTAGS);
        }

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
        $mform->addRule('email', $strrequired, 'required', null, 'client');
        $mform->setType('email', PARAM_EMAIL);

        $mform->addElement('static', 'blankline', '', '');
        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->addHelpButton('newpassword', 'newpassword');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->addElement('static', 'generatepassword', '',
                            get_string('leavepasswordemptytogenerate', 'local_qubitsuser'));

        $mform->addElement('advcheckbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
        $mform->addHelpButton('preference_auth_forcepasswordchange', 'forcepasswordchange');
        $mform->setDefault('preference_auth_forcepasswordchange', 1);

        $mform->addElement('hidden', 'sendnewpasswordemails', '1');

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

        // Validate email.
        if (empty($CFG->allowaccountssameemail) &&
            $DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
            $errors['email'] = get_string('emailexists');
        }

        if (!empty($usernew->newpassword)) {
            $errmsg = ''; // Prevent eclipse warning.
            if (!check_password_policy($usernew->newpassword, $errmsg)) {
                $errors['newpassword'] = $errmsg;
            }
        }

        return $errors;
    }

}