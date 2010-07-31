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
 * Various enrol UI forms
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_users_assign_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $user       = $this->_customdata['user'];
        $course     = $this->_customdata['course'];
        $context    = get_context_instance(CONTEXT_COURSE, $course->id);
        $assignable = $this->_customdata['assignable'];
        $assignable = array_reverse($assignable, true); // students first

        $ras = get_user_roles($context, $user->id, true);
        foreach ($ras as $ra) {
            unset($assignable[$ra->roleid]);
        }

        $mform->addElement('header','general', fullname($user));

        $mform->addElement('select', 'roleid', get_string('addrole', 'role'), $assignable);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'user');
        $mform->setType('user', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);

        $mform->addElement('hidden', 'ifilter');
        $mform->setType('ifilter', PARAM_ALPHA);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);

        $mform->addElement('hidden', 'perpage');
        $mform->setType('perpage', PARAM_INT);

        $mform->addElement('hidden', 'sort');
        $mform->setType('sort', PARAM_ALPHA);

        $mform->addElement('hidden', 'dir');
        $mform->setType('dir', PARAM_ALPHA);

        $this->add_action_buttons();

        $this->set_data(array('action'=>'assign', 'user'=>$user->id));
    }
}

class enrol_users_addmember_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $user     = $this->_customdata['user'];
        $course   = $this->_customdata['course'];
        $context  = get_context_instance(CONTEXT_COURSE, $course->id);
        $allgroups = $this->_customdata['allgroups'];
        $usergroups = groups_get_all_groups($course->id, $user->id, 0, 'g.id');

        $options = array();
        foreach ($allgroups as $group) {
            if (isset($usergroups[$group->id])) {
                continue;
            }
            $options[$group->id] = $group->name;
        }

        $mform->addElement('header','general', fullname($user));

        $mform->addElement('select', 'groupid', get_string('addgroup', 'group'), $options);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'user');
        $mform->setType('user', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);

        $mform->addElement('hidden', 'ifilter');
        $mform->setType('ifilter', PARAM_ALPHA);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);

        $mform->addElement('hidden', 'perpage');
        $mform->setType('perpage', PARAM_INT);

        $mform->addElement('hidden', 'sort');
        $mform->setType('sort', PARAM_ALPHA);

        $mform->addElement('hidden', 'dir');
        $mform->setType('dir', PARAM_ALPHA);

        $this->add_action_buttons();

        $this->set_data(array('action'=>'addmember', 'user'=>$user->id));
    }
}

class enrol_users_edit_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $user   = $this->_customdata['user'];
        $course = $this->_customdata['course'];
        $ue     = $this->_customdata['ue'];


        $mform->addElement('header','general', '');

        $options = array(ENROL_USER_ACTIVE    => get_string('participationactive', 'enrol'),
                         ENROL_USER_SUSPENDED => get_string('participationsuspended', 'enrol'));
        if (isset($options[$ue->status])) {
            $mform->addElement('select', 'status', get_string('participationstatus', 'enrol'), $options);
        }

        $mform->addElement('date_selector', 'timestart', get_string('enroltimestart', 'enrol'), array('optional' => true));

        $mform->addElement('date_selector', 'timeend', get_string('enroltimeend', 'enrol'), array('optional' => true));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'ue');
        $mform->setType('ue', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);

        $mform->addElement('hidden', 'ifilter');
        $mform->setType('ifilter', PARAM_ALPHA);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);

        $mform->addElement('hidden', 'perpage');
        $mform->setType('perpage', PARAM_INT);

        $mform->addElement('hidden', 'sort');
        $mform->setType('sort', PARAM_ALPHA);

        $mform->addElement('hidden', 'dir');
        $mform->setType('dir', PARAM_ALPHA);

        $this->add_action_buttons();

        $this->set_data(array('action'=>'edit', 'ue'=>$ue->id, 'status'=>$ue->status, 'timestart'=>$ue->timestart, 'timeend'=>$ue->timeend));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['timestart']) and !empty($data['timeend'])) {
            if ($data['timestart'] >= $data['timeend']) {
                $errors['timestart'] = get_string('error');
                $errors['timeend'] = get_string('error');
            }
        }

        return $errors;
    }
}
