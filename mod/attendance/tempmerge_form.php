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
 * Temp merge form class.
 *
 * @package    mod_attendance
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Temp merge form class.
 *
 * @package    mod_attendance
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tempmerge_form extends moodleform {
    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $COURSE;

        $context = context_course::instance($COURSE->id);
        $namefields = get_all_user_name_fields(true, 'u');
        $students = get_enrolled_users($context, 'mod/attendance:canbelisted', 0, 'u.id,'.$namefields.',u.email',
                                       'u.lastname, u.firstname', 0, 0, true);
        $partarray = array();
        foreach ($students as $student) {
            $partarray[$student->id] = fullname($student).' ('.$student->email.')';
        }

        $mform = $this->_form;
        $description = $this->_customdata['description'];

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'userid', 0);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('header', 'attheader', get_string('tempusermerge', 'attendance'));
        $mform->addElement('static', 'description', get_string('tempuser', 'attendance'), $description);

        $mform->addElement('select', 'participant', get_string('participant', 'attendance'), $partarray);

        $mform->addElement('static', 'requiredentries', '', get_string('requiredentries', 'attendance'));
        $mform->addHelpButton('requiredentries', 'requiredentry', 'attendance');

        $this->add_action_buttons(true, get_string('mergeuser', 'attendance'));
    }
}