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
 * Local plugin "QubitsCourse"
 *
 * @package   local_qubitscourse
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');


class qubits_assigncourses_form extends moodleform {
    
    /*
    ** Function Definition
    */
    function definition() {
        global $CFG, $PAGE;
        $mform    = $this->_form;
        $qubitssite_courses = $this->_customdata['qubitssite_courses']; // this contains the data of this form
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];
        $this->qubitssite_courses  = $qubitssite_courses;
        $this->context = $context;

        $mform->addElement('hidden', 'returnto', null);
        $mform->setType('returnto', PARAM_ALPHANUM);
        $mform->setConstant('returnto', $returnto);

        $mform->addElement('hidden', 'returnurl', null);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setConstant('returnurl', $returnurl);

        $mform->addElement('static','qubitssitename', get_string('qubitssitename', 'local_qubitssite'),'maxlength="254" size="50"');
        $mform->addHelpButton('qubitssitename', 'qubitssitename', 'local_qubitssite');

        $allcourses = core_course_category::get(0)->get_courses(array('recursive' => true));
                                                                  
        $acourses = array();                                                                                                       
        foreach ($allcourses as $onecourse) {
            $acourses[$onecourse->id] = $onecourse->fullname;
        }                                                                                                               
        $options = array(                                                                                                           
            'multiple' => true,                                                  
            'noselectionstring' => "Search Course",                                                                
        );
        $mform->addElement('autocomplete', 'course_id', "Search Course", $acourses, $options);

        $buttonarray = array();
        $classarray = array('class' => 'form-submit');
        if ($returnto !== 0) {
            $buttonarray[] = &$mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
        }
        $buttonarray[] = &$mform->createElement('submit', 'saveanddisplay', get_string('savechangesanddisplay'), $classarray);
        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'site_id', null);
        $mform->setType('site_id', PARAM_INT);

        $this->set_data($qubitssite_courses);
        
    }
}