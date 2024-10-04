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
 * This file contains the submission confirmation form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Assignment submission confirmation form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_confirm_submission_form extends moodleform {
    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        list($requiresubmissionstatement,
             $submissionstatement,
             $coursemoduleid,
             $data) = $this->_customdata;

        if ($requiresubmissionstatement) {
            $mform->addElement('checkbox', 'submissionstatement', '', $submissionstatement);
            $mform->addRule('submissionstatement', get_string('submissionstatementrequired', 'mod_assign'),
                'required', null, 'client');
        }

        $mform->addElement('static', 'confirmmessage', '', get_string('confirmsubmission', 'mod_assign'));
        $mform->addElement('hidden', 'id', $coursemoduleid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'confirmsubmit');
        $mform->setType('action', PARAM_ALPHA);
        $this->add_action_buttons(true, get_string('continue'));

        if ($data) {
            $this->set_data($data);
        }
    }

}
