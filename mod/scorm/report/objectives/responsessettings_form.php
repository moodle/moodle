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
 * Defines the version of scorm_objectives
 * @package   scormreport_objectives
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 2013 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
/**
 * A form that displays the objective report settings
 *
 * @copyright  2013 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_scorm_report_objectives_settings extends moodleform {
    /**
     * Definition of the setting form elements
     */
    protected function definition() {
        global $COURSE;
        $mform    =& $this->_form;

        $mform->addElement('header', 'preferencespage', get_string('preferencespage', 'scorm'));

        $options = array();
        if ($COURSE->id != SITEID) {
            $options[SCORM_REPORT_ATTEMPTS_ALL_STUDENTS] = get_string('optallstudents', 'scorm');
            $options[SCORM_REPORT_ATTEMPTS_STUDENTS_WITH] = get_string('optattemptsonly', 'scorm');
            $options[SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO] = get_string('optnoattemptsonly', 'scorm');
        }
        $mform->addElement('select', 'attemptsmode', get_string('show', 'scorm'), $options);
        $mform->addElement('advcheckbox', 'objectivescore', '', get_string('objectivescore', 'scormreport_objectives'));

        $mform->addElement('header', 'preferencesuser', get_string('preferencesuser', 'scorm'));

        $mform->addElement('text', 'pagesize', get_string('pagesize', 'scorm'));
        $mform->setType('pagesize', PARAM_INT);

        $this->add_action_buttons(false, get_string('savepreferences'));
    }
}
