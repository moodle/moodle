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

namespace core_user\form;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Contact site support form.
 *
 * @package core_user
 * @copyright 2022 Simey Lameze <simey@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contactsitesupport_form extends \moodleform {

    /**
     * Define the contact site support form.
     */
    public function definition(): void {
        global $CFG;

        $mform = $this->_form;
        $user = $this->_customdata;
        $strrequired = get_string('required');

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        // Email.
        $mform->addElement('text', 'email', get_string('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setType('email', PARAM_EMAIL);

        // Subject.
        $mform->addElement('text', 'subject', get_string('subject'));
        $mform->addRule('subject', $strrequired, 'required', null, 'client');
        $mform->setType('subject', PARAM_TEXT);

        // Message.
        $mform->addElement('textarea', 'message', get_string('message'));
        $mform->addRule('message', $strrequired, 'required', null, 'client');
        $mform->setType('message', PARAM_TEXT);

        // If the user is logged in set name and email fields to the current user info.
        if (isloggedin() && !isguestuser()) {
            $mform->setDefault('name', fullname($user));
            $mform->hardFreeze('name');

            $mform->setDefault('email', $user->email);
            $mform->hardFreeze('email');
        }

        if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        $this->add_action_buttons(true, get_string('submit'));
    }

    /**
     * Validate user supplied data on the contact site support form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (!validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');
        }
        if ($this->_form->elementExists('recaptcha_element')) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');

            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        return $errors;
    }

}
