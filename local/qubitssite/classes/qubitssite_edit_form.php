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
 * Local plugin "QubitsSite"
 *
 * @package   local_qubitssite
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class qubitssite_edit_form extends moodleform {

    /**
     * Form definition.
     */
    function definition() {
        global $CFG, $PAGE;
        $mform    = $this->_form;
        $qubitssite = $this->_customdata['qubitssite']; // this contains the data of this form
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];
        $this->qubitssite  = $qubitssite;
        $this->context = $context;

        $mform->addElement('hidden', 'returnto', null);
        $mform->setType('returnto', PARAM_ALPHANUM);
        $mform->setConstant('returnto', $returnto);

        $mform->addElement('hidden', 'returnurl', null);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setConstant('returnurl', $returnurl);

        $mform->addElement('text','name', get_string('qubitssitename', 'local_qubitssite'),'maxlength="254" size="50"');
        $mform->addHelpButton('name', 'qubitssitename', 'local_qubitssite');
        $mform->addRule('name', get_string('missingqubitssitename', 'local_qubitssite'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text','hostname', get_string('qubitssitehostname', 'local_qubitssite'),'maxlength="254" size="50"');
        $mform->addHelpButton('hostname', 'qubitssitehostname', 'local_qubitssite');
        $mform->addRule('hostname', get_string('missingqubitssitehostname', 'local_qubitssite'), 'required', null, 'client');
        $mform->setType('hostname', PARAM_TEXT);

        $choices = array();
        $choices['0'] = get_string('hide');
        $choices['1'] = get_string('show');
        $mform->addElement('select', 'status', get_string('coursevisibility'), $choices);
        $mform->addHelpButton('status', 'sitevisibility', 'local_qubitssite');
        $mform->setDefault('status', 1);

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

        $mform->addElement('hidden', 'cohortid', null);
        $mform->setType('cohortid', PARAM_INT);

        $this->set_data($qubitssite);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate sitename.
        if ($qubitssite = $DB->get_record('local_qubits_sites', array('name' => $data['name']), '*', IGNORE_MULTIPLE)) {
            if (empty($data['id']) || $qubitssite->id != $data['id']) {
                $errors['name'] = get_string('qubitssitenametaken', 'local_qubitssite', $qubitssite->name);
            }
        }

        if ($qubitssite = $DB->get_record('local_qubits_sites', array('hostname' => $data['hostname']), '*', IGNORE_MULTIPLE)) {
            if (empty($data['id']) || $qubitssite->id != $data['id']) {
                $errors['hostname'] = get_string('qubitssitehostnametaken', 'local_qubitssite', $qubitssite->hostname);
            }
        }

        if($data['hostname'] === SITE_MAIN_DOMAIN){
            $errors['hostname'] = get_string('qubitssitehostnamemaintaken', 'local_qubitssite', SITE_MAIN_DOMAIN);
        }

        return $errors;

    }


}