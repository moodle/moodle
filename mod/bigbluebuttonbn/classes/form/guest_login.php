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

namespace mod_bigbluebuttonbn\form;
defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Guest login form.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class guest_login extends \moodleform {

    /**
     * Form definition
     */
    protected function definition() {
        global $USER;

        $mform = $this->_form;
        $mform->addElement('text', 'username',
            get_string('guestaccess_username', 'mod_bigbluebuttonbn'));
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username',
            get_string('required'), 'required', null, 'client');

        if (isloggedin() && !isguestuser()) {
            $mform->setConstant('username', fullname($USER));
            $mform->freeze('username');
        }

        $mform->addElement('password', 'password',
            get_string('guestaccess_password', 'mod_bigbluebuttonbn'));
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password',
            get_string('required'), 'required', null, 'client');
        $mform->addElement('hidden', 'uid', $this->_customdata['uid']);
        $mform->setType('uid', PARAM_ALPHANUMEXT);

        $this->add_action_buttons(false, get_string('guestaccess_join_meeting', 'mod_bigbluebuttonbn'));
    }

    /**
     * Validate form
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws \coding_exception
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $instance = $this->_customdata['instance'];
        if ($data['password'] != $instance->get_guest_access_password()) {
            $errors['password'] = get_string('guestaccess_meeting_invalid_password', 'mod_bigbluebuttonbn');
        }
        return $errors;
    }
}

