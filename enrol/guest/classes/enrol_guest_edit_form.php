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
 * Guest access plugin.
 *
 * Adds new instance of enrol_guest to specified course
 * or edits current instance.
 *
 * @package    enrol_guest
 * @copyright  2015 Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_guest;
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class enrol_guest_edit_form
 * @copyright  2015 Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_guest_edit_form extends moodleform {
    /**
     * Form definition
     */
    public function definition() {

        $mform = $this->_form;

        list($instance, $plugin) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_guest'));

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_guest'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_guest');
        $mform->setDefault('status', $plugin->get_config('status'));
        $mform->setAdvanced('status', $plugin->get_config('status_adv'));

        $mform->addElement('passwordunmask', 'password', get_string('password', 'enrol_guest'));
        $mform->addHelpButton('password', 'password', 'enrol_guest');

        if ($plugin->get_config('requirepassword')) {
            $mform->addRule('password', get_string('required'), 'required', null);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        list($instance, $plugin) = $this->_customdata;
        $checkpassword = false;

        if ($data['id']) {
            if ($data['status'] == ENROL_INSTANCE_ENABLED) {
                if ($instance->password !== $data['password']) {
                    $checkpassword = true;
                }
            }
        } else {
            if ($data['status'] == ENROL_INSTANCE_ENABLED) {
                $checkpassword = true;
            }
        }

        if ($checkpassword) {
            $policy  = $plugin->get_config('usepasswordpolicy');
            if ($policy) {
                $errmsg = '';
                if (!check_password_policy($data['password'], $errmsg)) {
                    $errors['password'] = $errmsg;
                }
            }
        }

        return $errors;
    }
}
