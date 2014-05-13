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
 * Form for grade history filters
 *
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class gradereport_history_filter_form extends moodleform {

    public function definition() {
        global $USER, $CFG;

        $mform    =& $this->_form;
        $course   = $this->_customdata['course'];
        $itemids  = $this->_customdata['itemids'];
        $graders  = $this->_customdata['graders'];
        $userbutton = $this->_customdata['userbutton'];
        $names = html_writer::span('', 'selectednames');

        $context = context_course::instance($course->id);

        $mform->addElement('static', 'userselect', get_string('selectuser', 'gradereport_history'), $userbutton);
        $mform->addElement('static', 'selectednames', get_string('selectedusers', 'gradereport_history'), $names);

        $mform->addElement('select', 'itemid', get_string('gradeitem', 'gradereport_history'), $itemids);
        $mform->setType('itemid', PARAM_INT);

        $mform->addElement('select', 'grader', get_string('grader', 'gradereport_history'), $graders);
        $mform->setType('grader', PARAM_INT);

        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'gradereport_history'), array('optional' => true));
        $mform->addElement('date_selector', 'datetill', get_string('datetill', 'gradereport_history'), array('optional' => true));

        $mform->addElement('checkbox', 'revisedonly', get_string('revisedonly', 'gradereport_history'));
        $mform->addHelpButton('revisedonly', 'revisedonly', 'gradereport_history');

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'userids');
        $mform->setType('userids', PARAM_SEQUENCE);

        $mform->addElement('hidden', 'userfullnames');
        $mform->setType('userfullnames', PARAM_TEXT);

        $submitlabel = get_string('submit');
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('submit', 'exportbutton', get_string('export', 'grades'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function definition_after_data() {
        $mform =& $this->_form;

        if ($userfullnames = $mform->getElementValue('userfullnames')) {
            $mform->getElement('selectednames')->setValue(html_writer::span($userfullnames, 'selectednames'));
        }
    }

    public function validation($data, $files) {
        return parent::validation($data, $files);
    }

}

