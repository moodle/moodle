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
 * Change password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once('lib.php');

class login_change_password_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;
        $mform->setDisableShortforms(true);

        $mform->addElement('header', 'changepassword', get_string('changepassword'), '');

        // visible elements
        $mform->addElement('static', 'username', get_string('username'), $USER->username);

        $policies = array();
        if (!empty($CFG->passwordpolicy)) {
            $policies[] = print_password_policy();
        }
        if (!empty($CFG->passwordreuselimit) and $CFG->passwordreuselimit > 0) {
            $policies[] = get_string('informminpasswordreuselimit', 'auth', $CFG->passwordreuselimit);
        }
        if ($policies) {
            $mform->addElement('static', 'passwordpolicyinfo', '', implode('<br />', $policies));
        }
        $purpose = user_edit_map_field_purpose($USER->id, 'password');
        $mform->addElement('password', 'password', get_string('oldpassword'), $purpose);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');
        $mform->setType('password', PARAM_RAW);

        $mform->addElement('password', 'newpassword1', get_string('newpassword'),
            ['autocomplete' => 'new-password', 'maxlength' => MAX_PASSWORD_CHARACTERS]);
        $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
        $mform->addRule('password', get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength', MAX_PASSWORD_CHARACTERS, 'client');
        $mform->setType('newpassword1', PARAM_RAW);

        $mform->addElement('password', 'newpassword2', get_string('newpassword').' ('.get_String('again').')',
            ['autocomplete' => 'new-password', 'maxlength' => MAX_PASSWORD_CHARACTERS]);
        $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword2', PARAM_RAW);

        $mform->addElement('checkbox', 'logoutothersessions', get_string('logoutothersessions', 'report_usersessions'));
        $mform->addHelpButton('logoutothersessions', 'logoutothersessions', 'report_usersessions');
        $mform->setDefault('logoutothersessions', 1);
        if (!empty($CFG->passwordchangelogout)) {
            $mform->getElement('logoutothersessions')->freeze();
        }

        if (!empty(webservice::get_active_tokens($USER->id))) {
            $mform->addElement('advcheckbox', 'signoutofotherservices', get_string('signoutofotherservices'));
            $mform->addHelpButton('signoutofotherservices', 'signoutofotherservices');
            $mform->setDefault('signoutofotherservices', 1);
            if (!empty($CFG->passwordchangetokendeletion)) {
                $mform->getElement('signoutofotherservices')->freeze();
            }
        }

        // hidden optional params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Hook for plugins to extend form definition.
        core_login_extend_change_password_form($mform, $USER);

        // buttons
        if (get_user_preferences('auth_forcepasswordchange')) {
            $this->add_action_buttons(false);
        } else {
            $this->add_action_buttons(true);
        }
    }

/// perform extra password change validation
    function validation($data, $files) {
        global $USER;
        $errors = parent::validation($data, $files);
        $reason = null;

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, core_login_validate_extend_change_password_form($data, $USER));

        // ignore submitted username
        if (!$user = authenticate_user_login($USER->username, $data['password'], true, $reason, false)) {
            $errors['password'] = get_string('invalidlogin');
            return $errors;
        }

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
            return $errors;
        }

        if ($data['password'] == $data['newpassword1']){
            $errors['newpassword1'] = get_string('mustchangepassword');
            $errors['newpassword2'] = get_string('mustchangepassword');
            return $errors;
        }

        if (user_is_previously_used_password($USER->id, $data['newpassword1'])) {
            $errors['newpassword1'] = get_string('errorpasswordreused', 'core_auth');
            $errors['newpassword2'] = get_string('errorpasswordreused', 'core_auth');
        }

        $errmsg = '';//prevents eclipse warnings
        if (!check_password_policy($data['newpassword1'], $errmsg, $USER)) {
            $errors['newpassword1'] = $errmsg;
            $errors['newpassword2'] = $errmsg;
            return $errors;
        }

        return $errors;
    }
}
