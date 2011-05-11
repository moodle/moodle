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
 * Contains the form used to edit manual enrolments for a user.
 *
 * @package    enrol
 * @subpackage manual
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_manual_user_enrolment_form extends moodleform {
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

        $mform->addElement('hidden', 'ue');
        $mform->setType('ue', PARAM_INT);

        $mform->addElement('hidden', 'ifilter');
        $mform->setType('ifilter', PARAM_ALPHA);

        $this->add_action_buttons();

        $this->set_data(array(
            'ue' => $ue->id,
            'status' => $ue->status,
            'timestart' => $ue->timestart,
            'timeend' => $ue->timeend
        ));
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