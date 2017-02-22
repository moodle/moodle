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
 * MNet enrolment plugin
 *
 * @package    enrol_mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * MNet enrolment plugin implementation for Moodle 2.x enrolment framework
 */
class enrol_mnet_plugin extends enrol_plugin {

    /**
     * Returns localised name of enrol instance
     *
     * @param object|null $instance enrol_mnet instance
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);

        } else if (empty($instance->name)) {
            $enrol = $this->get_name();
            if ($role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING));
            } else {
                $role = get_string('error');
            }
            if (empty($instance->customint1)) {
                $host = get_string('remotesubscribersall', 'enrol_mnet');
            } else {
                $host = $DB->get_field('mnet_host', 'name', array('id'=>$instance->customint1));
            }
            return get_string('pluginname', 'enrol_'.$enrol) . ' (' . format_string($host) . ' - ' . $role .')';

        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Returns true if a new instance can be added to this course.
     *
     * The link is returned only if there are some MNet peers that we publish enrolment service to.
     *
     * @param int $courseid id of the course to add the instance to
     * @return boolean
     */
    public function can_add_instance($courseid) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

        $service = mnetservice_enrol::get_instance();
        if (!$service->is_available()) {
            return false;
        }
        $coursecontext = context_course::instance($courseid);
        if (!has_capability('moodle/course:enrolconfig', $coursecontext)) {
            return false;
        }
        $subscribers = $service->get_remote_subscribers();
        if (empty($subscribers)) {
            return false;
        }

        return true;
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/mnet:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/mnet:config', $context);
    }

    /**
     * Return an array of valid options for the hosts property.
     *
     * @return array
     */
    protected function get_valid_hosts_options() {
        global $CFG;
        require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

        $service = mnetservice_enrol::get_instance();

        $subscribers = $service->get_remote_subscribers();
        $hosts = array(0 => get_string('remotesubscribersall', 'enrol_mnet'));
        foreach ($subscribers as $hostid => $subscriber) {
            $hosts[$hostid] = $subscriber->appname.': '.$subscriber->hostname.' ('.$subscriber->hosturl.')';
        }
        return $hosts;
    }

    /**
     * Return an array of valid options for the roles property.
     *
     * @param context $context
     * @return array
     */
    protected function get_valid_roles_options($context) {
        $roles = get_assignable_roles($context);
        return $roles;
    }

    /**
     * Add elements to the edit instance form.
     *
     * @param stdClass $instance
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return bool
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        global $CFG;

        $hosts = $this->get_valid_hosts_options();
        $mform->addElement('select', 'customint1', get_string('remotesubscriber', 'enrol_mnet'), $hosts);
        $mform->addHelpButton('customint1', 'remotesubscriber', 'enrol_mnet');
        $mform->addRule('customint1', get_string('required'), 'required', null, 'client');

        $roles = $this->get_valid_roles_options($context);
        $mform->addElement('select', 'roleid', get_string('roleforremoteusers', 'enrol_mnet'), $roles);
        $mform->addHelpButton('roleid', 'roleforremoteusers', 'enrol_mnet');
        $mform->addRule('roleid', get_string('required'), 'required', null, 'client');
        $mform->setDefault('roleid', $this->get_config('roleid'));

        $mform->addElement('text', 'name', get_string('instancename', 'enrol_mnet'));
        $mform->addHelpButton('name', 'instancename', 'enrol_mnet');
        $mform->setType('name', PARAM_TEXT);
    }

    /**
     * We are a good plugin and don't invent our own UI/validation code path.
     *
     * @return boolean
     */
    public function use_standard_editing_ui() {
        return true;
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        global $DB;
        $errors = array();

        $validroles = array_keys($this->get_valid_roles_options($context));
        $validhosts = array_keys($this->get_valid_hosts_options());

        $params = array('enrol' => 'mnet', 'courseid' => $instance->courseid, 'customint1' => $data['customint1']);
        if ($DB->record_exists('enrol', $params)) {
            $errors['customint1'] = get_string('error_multiplehost', 'enrol_mnet');
        }

        $tovalidate = array(
            'customint1' => $validhosts,
            'roleid' => $validroles,
            'name' => PARAM_TEXT
        );
        $typeerrors = $this->validate_param_types($data, $tovalidate);
        $errors = array_merge($errors, $typeerrors);

        return $errors;
    }
}
