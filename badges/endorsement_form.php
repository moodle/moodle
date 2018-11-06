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
 * Form endorsement for editing.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');
/**
 * Form to edit endorsement.
 *
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai
 */
class endorsement_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        $mform = $this->_form;
        $badge = $this->_customdata['badge'];
        $mform->addElement('header', 'endorsement', get_string('issuerdetails', 'badges'));
        $mform->addElement('text', 'issuername', get_string('issuername_endorsement', 'badges'), array('size' => '70'));
        $mform->setType('issuername', PARAM_TEXT);
        $mform->addRule('issuername', null, 'required');
        $mform->addHelpButton('issuername', 'issuername_endorsement', 'badges');
        $mform->addElement('text', 'issueremail', get_string('issueremail', 'badges'), array('size' => '70'));
        $mform->addRule('issueremail', null, 'required');
        $mform->setType('issueremail', PARAM_RAW);
        $mform->addHelpButton('issueremail', 'issueremail', 'badges');
        $mform->addElement('text', 'issuerurl', get_string('issuerurl', 'badges'), array('size' => '70'));
        $mform->setType('issuerurl', PARAM_URL);
        $mform->addRule('issuerurl', null, 'required');
        $mform->addHelpButton('issuerurl', 'issuerurl', 'badges');
        $mform->addElement('date_time_selector', 'dateissued',
            get_string('dateawarded', 'badges'));
        $mform->addElement('header', 'claim', get_string('claim', 'badges'));
        $mform->addElement('text', 'claimid', get_string('claimid', 'badges'), array('size' => '70'));
        $mform->setType('claimid', PARAM_URL);
        $mform->addRule('claimid', null, 'required');
        $mform->addElement('textarea', 'claimcomment', get_string('claimcomment', 'badges'), 'wrap="virtual" rows="8" cols="70"');
        $mform->setType('claimcomment', PARAM_NOTAGS);
        $endorsement = new stdClass();
        $endorsement = $badge->get_endorsement();
        if ($endorsement) {
            $mform->setDefault('dateissued', $endorsement->dateissued);
            $this->set_data($endorsement);
        }
        $this->add_action_buttons();
        // Freeze all elements if badge is active or locked.
        if ($badge->is_active() || $badge->is_locked()) {
            $mform->hardFreezeAllVisibleExcept(array());
        }
    }

    /**
     * Validates form data.
     *
     * @param array $data submitted data.
     * @param array $files submitted files.
     * @return array $errors An array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['issueremail'] && !validate_email($data['issueremail'])) {
            $errors['issueremail'] = get_string('invalidemail');
        }
        return $errors;
    }
}