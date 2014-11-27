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
 * Adds instance form
 *
 * @package    enrol_cohort
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_cohort_edit_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform  = $this->_form;

        list($instance, $plugin, $course) = $this->_customdata;
        $coursecontext = context_course::instance($course->id);

        $enrol = enrol_get_plugin('cohort');


        $groups = array(0 => get_string('none'));
        foreach (groups_get_all_groups($course->id) as $group) {
            $groups[$group->id] = format_string($group->name, true, array('context'=>$coursecontext));
        }

        $mform->addElement('header','general', get_string('pluginname', 'enrol_cohort'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_cohort'), $options);

        if ($instance->id) {
            if ($cohort = $DB->get_record('cohort', array('id'=>$instance->customint1))) {
                $cohorts = array($instance->customint1=>format_string($cohort->name, true, array('context'=>context::instance_by_id($cohort->contextid))));
            } else {
                $cohorts = array($instance->customint1=>get_string('error'));
            }
            $mform->addElement('select', 'customint1', get_string('cohort', 'cohort'), $cohorts);
            $mform->setConstant('customint1', $instance->customint1);
            $mform->hardFreeze('customint1', $instance->customint1);

        } else {
            $cohorts = array('' => get_string('choosedots'));
            $allcohorts = cohort_get_available_cohorts($coursecontext, 0, 0, 0);
            foreach ($allcohorts as $c) {
                $cohorts[$c->id] = format_string($c->name);
            }
            $mform->addElement('select', 'customint1', get_string('cohort', 'cohort'), $cohorts);
            $mform->addRule('customint1', get_string('required'), 'required', null, 'client');
        }

        $roles = get_assignable_roles($coursecontext);
        $roles[0] = get_string('none');
        $roles = array_reverse($roles, true); // Descending default sortorder.
        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_cohort'), $roles);
        $mform->setDefault('roleid', $enrol->get_config('roleid'));
        if ($instance->id and !isset($roles[$instance->roleid])) {
            if ($role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $roles = role_fix_names($roles, $coursecontext, ROLENAME_ALIAS, true);
                $roles[$instance->roleid] = role_get_name($role, $coursecontext);
            } else {
                $roles[$instance->roleid] = get_string('error');
            }
        }
        $mform->addElement('select', 'customint2', get_string('addgroup', 'enrol_cohort'), $groups);

        $mform->addElement('hidden', 'courseid', null);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        if ($instance->id) {
            $this->add_action_buttons(true);
        } else {
            $this->add_action_buttons(true, get_string('addinstance', 'enrol'));
        }

        $this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $params = array('roleid'=>$data['roleid'], 'customint1'=>$data['customint1'], 'courseid'=>$data['courseid'], 'id'=>$data['id']);
        if ($DB->record_exists_select('enrol', "roleid = :roleid AND customint1 = :customint1 AND courseid = :courseid AND enrol = 'cohort' AND id <> :id", $params)) {
            $errors['roleid'] = get_string('instanceexists', 'enrol_cohort');
        }

        return $errors;
    }
}
