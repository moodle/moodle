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
 * Student form class.
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_attendance\form;

/**
 * Class studentattendance
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class studentattendance extends \moodleform {
    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $USER, $DB;

        $mform  =& $this->_form;

        $attforsession = $this->_customdata['session'];
        $attblock = $this->_customdata['attendance'];
        $password = $this->_customdata['password'];
        $existingstatus = null;

        [$statuses, $disabledduetotime] = $attblock->get_student_statuses($attforsession);

        $mform->addElement('hidden', 'sessid', null);
        $mform->setType('sessid', PARAM_INT);
        $mform->setConstant('sessid', $attforsession->id);

        $mform->addElement('hidden', 'sesskey', null);
        $mform->setType('sesskey', PARAM_INT);
        $mform->setConstant('sesskey', sesskey());

        // Set a title as the date and time of the session.
        $sesstiontitle = userdate($attforsession->sessdate, get_string('strftimedate')).' '
                .attendance_strftimehm($attforsession->sessdate);

        $mform->addElement('header', 'session', $sesstiontitle);

        // If a session description is set display it.
        if (!empty($attforsession->description)) {
            $mform->addElement('html', $attforsession->description);
        }
        if (!empty($attforsession->studentpassword) &&
            !(attendance_is_status_availablebeforesession($attforsession->id) && !attendance_session_open_for_students($attforsession))) {
            $mform->addElement('text', 'studentpassword', get_string('password', 'attendance'));
            $mform->setType('studentpassword', PARAM_TEXT);
            $mform->addRule('studentpassword', get_string('passwordrequired', 'attendance'), 'required');
            $mform->setDefault('studentpassword', $password);
        }

        // Display current status:
        if (attendance_check_allow_update($attforsession->id)) {
            // Check if an existing status is set, and show it.
            $existingstatusid = $DB->get_field('attendance_log', 'statusid',
                ['sessionid' => $attforsession->id, 'studentid' => $USER->id]);
            if (!empty($existingstatusid)) {
                $existingstatus = $attblock->get_statuses(false)[$existingstatusid];
                if (!empty($existingstatus)) {
                    $mform->addElement('static', '', '', get_string("userexistingstatus", 'mod_attendance', $existingstatus->description));
                }
            }
        }

        // Create radio buttons for setting the attendance status.
        $radioarray = array();
        foreach ($statuses as $status) {
            $name = \html_writer::span($status->description, 'statusdesc');
            $radioarray[] =& $mform->createElement('radio', 'status', '', $name, $status->id, array());
        }
        if ($disabledduetotime) {
            $warning = \html_writer::span(get_string('somedisabledstatus', 'attendance'), 'somedisabledstatus');
            $radioarray[] =& $mform->createElement('static', '', '', $warning);
        }
        // Add the radio buttons as a control with the user's name in front.
        $radiogroup = $mform->addGroup($radioarray, 'statusarray', fullname($USER).':', array(''), false);
        $radiogroup->setAttributes(array('class' => 'statusgroup'));
        $mform->addRule('statusarray', get_string('attendancenotset', 'attendance'), 'required', '', 'client', false, false);
        if (!empty($existingstatus) && !empty($statuses[$existingstatus->id])) {
            $mform->setDefault('status', $existingstatus->id);
        }

        $this->add_action_buttons();
    }

    /**
     * Validate Form.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = array();
        if (!($this->_customdata['session']->autoassignstatus)) {
            // Check if this status is allowed to be set.
            if (empty($data['status'])) {
                $errors['statusarray'] = get_string('invalidstatus', 'attendance');
            }
        }

        return $errors;
    }
}
