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
 * Set password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Set forgotten password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @copyright  2013 Peter Bulmer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class login_set_password_form extends moodleform {

    /**
     * Define the set password form.
     */
    public function definition() {
        global $USER, $CFG;
        // Prepare a string showing whether the site wants login password autocompletion to be available to user.
        if (empty($CFG->loginpasswordautocomplete)) {
            $autocomplete = 'autocomplete="on"';
        } else {
            $autocomplete = '';
        }

        $mform = $this->_form;
        $mform->setDisableShortforms(true);
        $mform->addElement('header', 'setpassword', get_string('setpassword'), '');

        // Include the username in the form so browsers will recognise that a password is being set.
        $mform->addElement('text', 'username', '', 'style="display: none;" ' . $autocomplete);
        $mform->setType('username', PARAM_RAW);
        // Token gives authority to change password.
        $mform->addElement('hidden', 'token', '');
        $mform->setType('token', PARAM_ALPHANUM);

        // Visible elements.
        $mform->addElement('static', 'username2', get_string('username'));

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('newpassword'), $autocomplete);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');
        $mform->setType('password', PARAM_RAW);

        $strpasswordagain = get_string('newpassword') . ' (' . get_string('again') . ')';
        $mform->addElement('password', 'password2', $strpasswordagain, $autocomplete);
        $mform->addRule('password2', get_string('required'), 'required', null, 'client');
        $mform->setType('password2', PARAM_RAW);

        $this->add_action_buttons(true);
    }

    /**
     * Perform extra password change validation.
     * @param array $data submitted form fields.
     * @param array $files submitted with the form.
     * @return array errors occuring during validation.
     */
    public function validation($data, $files) {
        global $USER;
        $errors = parent::validation($data, $files);

        // Ignore submitted username.
        if ($data['password'] !== $data['password2']) {
            $errors['password'] = get_string('passwordsdiffer');
            $errors['password2'] = get_string('passwordsdiffer');
            return $errors;
        }

        $errmsg = ''; // Prevents eclipse warnings.
        if (!check_password_policy($data['password'], $errmsg)) {
            $errors['password'] = $errmsg;
            $errors['password2'] = $errmsg;
            return $errors;
        }

        return $errors;
    }
}
