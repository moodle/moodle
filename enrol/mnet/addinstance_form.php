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
 * Form to add an instance of enrol_mnet plugin
 *
 * @package    enrol_mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_mnet_addinstance_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform   = $this->_form;
        $course  = $this->_customdata['course'];
        $enrol   = $this->_customdata['enrol'];
        $service = $this->_customdata['service'];
        $coursecontext = context_course::instance($course->id);

        $subscribers = $service->get_remote_subscribers();
        $hosts = array(0 => get_string('remotesubscribersall', 'enrol_mnet'));
        foreach ($subscribers as $hostid => $subscriber) {
            $hosts[$hostid] = $subscriber->appname.': '.$subscriber->hostname.' ('.$subscriber->hosturl.')';
        }
        $roles = get_assignable_roles($coursecontext);

        $mform->addElement('header','general', get_string('pluginname', 'enrol_mnet'));

        $mform->addElement('select', 'hostid', get_string('remotesubscriber', 'enrol_mnet'), $hosts);
        $mform->addHelpButton('hostid', 'remotesubscriber', 'enrol_mnet');
        $mform->addRule('hostid', get_string('required'), 'required', null, 'client');

        $mform->addElement('select', 'roleid', get_string('roleforremoteusers', 'enrol_mnet'), $roles);
        $mform->addHelpButton('roleid', 'roleforremoteusers', 'enrol_mnet');
        $mform->addRule('roleid', get_string('required'), 'required', null, 'client');
        $mform->setDefault('roleid', $enrol->get_config('roleid'));

        $mform->addElement('text', 'name', get_string('instancename', 'enrol_mnet'));
        $mform->addHelpButton('name', 'instancename', 'enrol_mnet');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data(array('id'=>$course->id));
    }

    /**
     * Do not allow multiple instances for single remote host
     *
     * @param array $data raw form data
     * @param array $files
     * @return array of errors
     */
    function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($DB->record_exists('enrol', array('enrol' => 'mnet', 'courseid' => $data['id'], 'customint1' => $data['hostid']))) {
            $errors['hostid'] = get_string('error_multiplehost', 'enrol_mnet');
        }

        return $errors;
    }
}
