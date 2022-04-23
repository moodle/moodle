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
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/local/iomad/lib/user.php');


class mod_trainingevent_mod_form extends moodleform_mod {

    public function definition() {
        global $USER, $SESSION, $DB;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $mform->addElement('date_time_selector', 'startdatetime', get_string('startdatetime', 'trainingevent'));
        $mform->addRule('startdatetime', get_string('missingstartdatetime', 'trainingevent'), 'required', null, 'client');

        $mform->addElement('date_time_selector', 'enddatetime', get_string('enddatetime', 'trainingevent'));
        $mform->addRule('enddatetime', get_string('missingenddatetime', 'trainingevent'), 'required', null, 'client');

        // Set the companyid to bypass the company select form if possible.
        $params = array();
        if (!empty($SESSION->currenteditingcompany)) {
            $params['companyid'] = $SESSION->currenteditingcompany;
        } else if (!empty($USER->company)) {
            $params['companyid'] = company_user::companyid();
        }

        $choices = array();
        if ($rooms = $DB->get_recordset('classroom', $params, 'name', '*')) {
            foreach ($rooms as $room) {
                $choices[$room->id] = $room->name;
            }
            $rooms->close();
        }

        $choices = array('' => get_string('selectaroom', 'trainingevent').'...') + $choices;
        $mform->addElement('select', 'classroomid', get_string('selectaroom', 'trainingevent'), $choices);
        $mform->addRule('classroomid', get_string('required'), 'required', null, 'client');

        $choices = array(get_string('none', 'trainingevent'),
                        get_string('manager', 'trainingevent'),
                        get_string('companymanager', 'trainingevent'),
                        get_string('both', 'trainingevent'),
                        get_string('enrolonly', 'trainingevent'));
        $mform->addElement('select', 'approvaltype', get_string('approvaltype', 'trainingevent'), $choices);

        $mform->addElement('checkbox', 'haswaitinglist', get_string('haswaitinglist', 'mod_trainingevent'));
        $mform->addHelpButton('haswaitinglist', 'haswaitinglist', 'mod_trainingevent');

        $mform->addElement('text', 'coursecapacity', get_string('maxsize', 'mod_trainingevent'));
        $mform->addHelpButton('coursecapacity', 'maxsize', 'mod_trainingevent');
        $mform->setType('coursecapacity', PARAM_INT);

        $mform->addElement('checkbox', 'emailteachers',  get_string('alertteachers', 'mod_trainingevent'));
        $mform->addHelpButton('emailteachers', 'alertteachers', 'mod_trainingevent');

        $mform->addElement('checkbox', 'isexclusive',  get_string('exclusive', 'mod_trainingevent'));
        $mform->addHelpButton('isexclusive', 'exclusive', 'mod_trainingevent');

        $remindergroup = [];
        $remindergroup[] =& $mform->createElement('text', 'sendreminder', '');
        $remindergroup[] =& $mform->createElement('checkbox', 'setreminder', get_string('enable'));
        $mform->setType('sendreminder', PARAM_INT);
        $mform->addGroup($remindergroup, 'remindergroup', get_string('sendreminder', 'mod_trainingevent'), ' ', false);
        $mform->disabledIf('remindergroup', 'setreminder');
        $mform->addHelpButton('remindergroup', 'sendreminder', 'mod_trainingevent');

        $lockedgroup = [];
        $lockedgroup[] =& $mform->createElement('text', 'lockdays', '');
        $lockedgroup[] =& $mform->createElement('checkbox', 'lockevent', get_string('enable'));
        $mform->setType('lockdays', PARAM_INT);
        $mform->addGroup($lockedgroup, 'lockedgroup', get_string('lockdays', 'mod_trainingevent'), ' ', false);
        $mform->disabledIf('lockedgroup', 'lockevent');
        $mform->addHelpButton('lockedgroup', 'lockdays', 'mod_trainingevent');

        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();

        // Add the buttons.
        $this->add_action_buttons(true, false, null);

    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();
        if (empty($data['classroomid']) || !$DB->get_record('classroom', array('id' => $data['classroomid']))) {
            $errors['classroomid'] = get_string('invalidclassroom', 'trainingevent');
            return $errors;
        }

        // Are we editing or adding?
        if (!empty($data['instance'])) {
            $mysql = " AND id != " . $data['instance'];
        } else {
            $mysql = "";
        }

        // Check the date against that room usage.
        $classroom = $DB->get_record('classroom', ['id' => $data['classroomid']]);
        if (empty($classroom->isvirtual)) {
            if ($roomclash = $DB->get_records_sql("SELECT * FROM {trainingevent}
                                                   WHERE classroomid = ".$data['classroomid']."$mysql
                                                   AND startdatetime < ".$data['startdatetime']."
                                                   AND enddatetime > ".$data['startdatetime'])) {
                $errors['classroomid'] = get_string('chosenclassroomunavailable', 'trainingevent');
            } else if ($roomclash = $DB->get_records_sql("SELECT * FROM {trainingevent}
                                                          WHERE classroomid = ".$data['classroomid']."$mysql
                                                          AND startdatetime > ".$data['startdatetime']."
                                                          AND startdatetime < ".$data['enddatetime'])) {
                $errors['classroomid'] = get_string('chosenclassroomunavailable', 'trainingevent');
            } else if ($roomclash = $DB->get_records_sql("SELECT * FROM {trainingevent}
                                                          WHERE classroomid = ".$data['classroomid']."$mysql
                                                          AND startdatetime > ".$data['startdatetime']."
                                                          AND enddatetime < ".$data['enddatetime'])) {
                $errors['classroomid'] = get_string('chosenclassroomunavailable', 'trainingevent');
            }
        }
        return $errors;
    }
}
