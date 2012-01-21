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
 * Guest access plugin implementation.
 *
 * @package    enrol
 * @subpackage guest
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_guest_enrol_form extends moodleform {
    protected $instance;

    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('guest');

        $heading = $plugin->get_instance_name($instance);
        $mform->addElement('header', 'guestheader', $heading);

        $mform->addElement('passwordunmask', 'guestpassword', get_string('password', 'enrol_guest'));

        $this->add_action_buttons(false, get_string('submit'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        $instance = $this->instance;

        if ($instance->password !== '') {
            if ($data['guestpassword'] !== $instance->password) {
                $plugin = enrol_get_plugin('guest');
                if ($plugin->get_config('showhint')) {
                    $hint = textlib::substr($instance->password, 0, 1);
                    $errors['guestpassword'] = get_string('passwordinvalidhint', 'enrol_guest', $hint);
                } else {
                    $errors['guestpassword'] = get_string('passwordinvalid', 'enrol_guest');
                }
            }
        }

        return $errors;
    }
}