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
 * @package   enrol_license
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_license_enrol_form extends moodleform {
    protected $instance;

    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('license');

        if ($instance->password) {
            $heading = $plugin->get_instance_name($instance);
            $mform->addElement('header', 'licenseheader', $heading);
            $mform->addElement('passwordunmask', 'enrolpassword', get_string('password', 'enrol_license'));
        }

        $this->add_action_buttons(false, get_string('enrolme', 'enrol_license'));

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

        if ($instance->password) {
            if ($data['enrolpassword'] !== $instance->password) {
                if ($instance->customint1) {
                    $groups = $DB->get_records('groups', array('courseid' => $instance->courseid), 'id ASC', 'id, enrolmentkey');
                    $found = false;
                    foreach ($groups as $group) {
                        if (empty($group->enrolmentkey)) {
                            continue;
                        }
                        if ($group->enrolmentkey === $data['enrolpassword']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        // We can not hint because there are probably multiple passwords.
                        $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_license');
                    }

                } else {
                    $plugin = enrol_get_plugin('license');
                    if ($plugin->get_config('showhint')) {
                        $textlib = textlib_get_instance();
                        $hint = $textlib->substr($instance->password, 0, 1);
                        $errors['enrolpassword'] = get_string('passwordinvalidhint', 'enrol_license', $hint);
                    } else {
                        $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_license');
                    }
                }
            }
        }

        return $errors;
    }
}
